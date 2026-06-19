<?php

/**
 * SePayWebhookController - Tiếp nhận và xử lý Webhook tự động từ SePay
 * Hỗ trợ xác thực HMAC, tìm đơn hàng linh hoạt và gửi email xác nhận.
 */
class SePayWebhookController
{
    public function xuly()
    {
        // Tắt hiển thị lỗi HTML ra output để tránh hỏng JSON response
        error_reporting(0);
        ini_set('display_errors', 0);

        header('Content-Type: application/json');
        header('ngrok-skip-browser-warning: true'); // Bỏ qua cảnh báo nếu dùng ngrok test

        // 1. Lấy headers và raw body
        $signatureHeader = $this->getHeader('X-SePay-Signature');
        $timestampHeader = $this->getHeader('X-SePay-Timestamp');
        $rawPayload = file_get_contents('php://input');

        // Ghi log debug đầy đủ ban đầu
        $logLine = date('[Y-m-d H:i:s] ')
            . "METHOD: {$_SERVER['REQUEST_METHOD']} | "
            . "Sig: {$signatureHeader} | "
            . "Time: {$timestampHeader} | "
            . "Body: {$rawPayload}\n";
        file_put_contents('./sepay_webhook_log.txt', $logLine, FILE_APPEND);

        if (empty($rawPayload)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Empty body']);
            exit;
        }

        // 2. Nạp cấu hình SePay
        $sepayConfig = require './config/sepay.php';
        $secretKey = $sepayConfig['webhook_secret'];

        // 3. Kiểm tra chữ ký HMAC-SHA256
        if (!empty($signatureHeader) && !empty($timestampHeader)) {
            $signature = str_replace('sha256=', '', $signatureHeader);
            $dataToVerify = $timestampHeader . '.' . $rawPayload;
            $calculatedSignature = hash_hmac('sha256', $dataToVerify, $secretKey);

            file_put_contents('./sepay_webhook_log.txt',
                date('[Y-m-d H:i:s] ') . "DEBUG: secret_len=" . strlen($secretKey)
                . " | data_len=" . strlen($dataToVerify)
                . " | calculated=$calculatedSignature"
                . " | received=$signature"
                . " | MATCH=" . ($calculatedSignature === $signature ? 'YES' : 'NO') . "\n",
                FILE_APPEND);

            if (!hash_equals($calculatedSignature, $signature)) {
                // Thử lại với các biến thể body khác nhau
                $calcTrimmed = hash_hmac('sha256', $timestampHeader . '.' . trim($rawPayload), $secretKey);
                $calcUnescaped = hash_hmac('sha256', $timestampHeader . '.' . str_replace('\/', '/', $rawPayload), $secretKey);

                if (hash_equals($calcTrimmed, $signature)) {
                    file_put_contents('./sepay_webhook_log.txt', date('[Y-m-d H:i:s] ') . "SIGNATURE OK (trimmed body)\n", FILE_APPEND);
                } elseif (hash_equals($calcUnescaped, $signature)) {
                    file_put_contents('./sepay_webhook_log.txt', date('[Y-m-d H:i:s] ') . "SIGNATURE OK (unescaped slash body)\n", FILE_APPEND);
                } else {
                    file_put_contents('./sepay_webhook_log.txt', date('[Y-m-d H:i:s] ') . "SIGNATURE FAILED ALL VARIANTS.\n", FILE_APPEND);
                    http_response_code(401);
                    echo json_encode(['success' => false, 'message' => 'Invalid signature']);
                    exit;
                }
            }

            // Kiểm tra Replay Attack (5 phút)
            $diff = abs(time() - (int)$timestampHeader);
            if ($diff > 300) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Request expired']);
                exit;
            }
        }

        // 4. Phân tích dữ liệu JSON
        $data = json_decode($rawPayload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
            exit;
        }

        // Chỉ xử lý giao dịch tiền vào (nhận tiền)
        $transferType = strtolower($data['transferType'] ?? 'in');
        if ($transferType !== 'in') {
            echo json_encode(['success' => true, 'message' => 'Ignored outgoing transaction']);
            exit;
        }

        $content        = $data['content']     ?? '';
        $description    = $data['description'] ?? '';
        $sepayCode      = $data['code']        ?? '';
        $transferAmount = (int)($data['transferAmount'] ?? 0);

        // 5. Nạp Model
        if (!class_exists('OrderModel')) {
            require './models/OrderModel.php';
        }
        $orderModel = new OrderModel();

        $orderId = null;

        // === CHIẾN LƯỢC 1: Tìm mã VABORD trong content hoặc description ===
        foreach ([$content, $description, $sepayCode] as $textSource) {
            if (preg_match('/VABORD(\d+)/i', $textSource, $matches)) {
                $orderId = (int)$matches[1];
                file_put_contents('./sepay_webhook_log.txt',
                    date('[Y-m-d H:i:s] ') . "Order found via VABORD code: #{$orderId} in '{$textSource}'\n",
                    FILE_APPEND);
                break;
            }
        }

        // === CHIẾN LƯỢC 2: Tìm đơn hàng pending có cùng số tiền nếu không có mã khớp ===
        if ($orderId === null && $transferAmount > 0) {
            $pendingOrder = $orderModel->findPendingByAmount($transferAmount);
            if ($pendingOrder) {
                $orderId = (int)$pendingOrder['MaDonHang'];
                file_put_contents('./sepay_webhook_log.txt',
                    date('[Y-m-d H:i:s] ') . "Order found via amount matching: #{$orderId} (amount={$transferAmount})\n",
                    FILE_APPEND);
            }
        }

        // Không tìm thấy đơn hàng -> vẫn trả 200 để SePay dừng gửi lại request (retry)
        if ($orderId === null) {
            file_put_contents('./sepay_webhook_log.txt',
                date('[Y-m-d H:i:s] ') . "⚠️ No matching order found. Content: '{$content}' | Amount: {$transferAmount}\n",
                FILE_APPEND);
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Transaction received but no matching order']);
            exit;
        }

        // 6. Truy vấn và xử lý cập nhật Đơn hàng
        $order = $orderModel->getById($orderId);
        if (!$order) {
            file_put_contents('./sepay_webhook_log.txt', date('[Y-m-d H:i:s] ') . "Order #{$orderId} not found.\n", FILE_APPEND);
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }

        $orderTotal = (int)($order['TongTien'] ?? 0);

        // Xác thực số tiền (cho phép lệch nhỏ ±1000đ do làm tròn)
        if ($transferAmount < ($orderTotal - 1000)) {
            file_put_contents('./sepay_webhook_log.txt',
                date('[Y-m-d H:i:s] ') . "Underpayment for Order #{$orderId}: Received {$transferAmount}, Expected {$orderTotal}\n",
                FILE_APPEND);
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Underpayment']);
            exit;
        }

        $currentStatus = strtolower(trim($order['TrangThaiDH'] ?? ''));
        if ($currentStatus === 'pending') {
            $orderModel->updateStatus($orderId, 'confirmed');
            file_put_contents('./sepay_webhook_log.txt',
                date('[Y-m-d H:i:s] ') . "✅ Order #{$orderId} CONFIRMED after payment. Amount: {$transferAmount}đ\n",
                FILE_APPEND);

            // ====== GỬI EMAIL XÁC NHẬN THANH TOÁN THÀNH CÔNG ======
            if (!function_exists('sendPaymentSuccessEmail')) {
                require_once './helpers/mail_helper.php';
            }
            $sent = sendPaymentSuccessEmail($orderId);
            if ($sent) {
                file_put_contents('./sepay_webhook_log.txt',
                    date('[Y-m-d H:i:s] ') . "📧 Email xác nhận thanh toán đã gửi tới: " . ($order['email'] ?? '') . "\n",
                    FILE_APPEND);
            } else {
                file_put_contents('./sepay_webhook_log.txt',
                    date('[Y-m-d H:i:s] ') . "⚠️ Không gửi được email (lỗi SMTP hoặc email trống) cho Order #{$orderId}\n",
                    FILE_APPEND);
            }
        } else {
            file_put_contents('./sepay_webhook_log.txt',
                date('[Y-m-d H:i:s] ') . "Order #{$orderId} already has status: {$currentStatus} - skipped update\n",
                FILE_APPEND);
        }

        echo json_encode(['success' => true]);
        exit;
    }

    private function getHeader($name)
    {
        $nameUpper = str_replace('-', '_', strtoupper($name));
        if (isset($_SERVER['HTTP_' . $nameUpper])) {
            return $_SERVER['HTTP_' . $nameUpper];
        }
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $key => $value) {
                if (strcasecmp($key, $name) === 0) {
                    return $value;
                }
            }
        }
        return '';
    }
}
