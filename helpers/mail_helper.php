<?php

/**
 * Mail Helper - Gửi email qua SMTP bằng PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function sendMail($toEmail, $toName, $subject, $body)
{
    // Tự động phát hiện cơ chế nạp PHPMailer
    if (file_exists('./vendor/autoload.php')) {
        require './vendor/autoload.php';
    } else {
        // Nhúng thủ công nếu người dùng tải PHPMailer thủ công đặt trong helpers
        $exceptionPath = './helpers/PHPMailer/src/Exception.php';
        $phpmailerPath = './helpers/PHPMailer/src/PHPMailer.php';
        $smtpPath      = './helpers/PHPMailer/src/SMTP.php';

        if (file_exists($exceptionPath) && file_exists($phpmailerPath) && file_exists($smtpPath)) {
            require $exceptionPath;
            require $phpmailerPath;
            require $smtpPath;
        } else {
            return [
                'success' => false,
                'message' => 'Không tìm thấy thư viện PHPMailer. Hãy chạy "composer require phpmailer/phpmailer" hoặc tải PHPMailer đặt vào thư mục "helpers/PHPMailer/"'
            ];
        }
    }

    // Nạp cấu hình email
    require './config/mail.php';

    $mail = new PHPMailer(true);

    try {
        // Thiết lập cấu hình SMTP
        $mail->isSMTP();
        $mail->Host       = $mailConfig['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailConfig['username'];
        $mail->Password   = $mailConfig['password'];
        $mail->SMTPSecure = ($mailConfig['encryption'] === 'ssl') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $mailConfig['port'];
        $mail->CharSet    = 'UTF-8';

        // Bỏ qua lỗi xác thực SSL trên localhost nếu cần thiết
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            )
        );

        // Thiết lập thông tin người gửi & người nhận
        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $mail->addAddress($toEmail, $toName);

        // Thiết lập nội dung Email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();

        return [
            'success' => true,
            'message' => 'Gửi email thành công'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Lỗi gửi mail: ' . $mail->ErrorInfo
        ];
    }
}

/**
 * Gửi email thông báo thanh toán thành công cho đơn hàng SePay.
 */
function sendPaymentSuccessEmail($orderId)
{
    $pdo = getConnection();
    
    // 1. Lấy thông tin đơn hàng
    $stmt = $pdo->prepare("SELECT * FROM DONHANG WHERE MaDonHang = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) return false;
    
    // 2. Lấy thông tin chi tiết các sản phẩm
    $stmtItems = $pdo->prepare("
        SELECT ct.*, sp.AnhChinh as AnhChinh 
        FROM CHITIETDONHANG ct 
        LEFT JOIN SANPHAM sp ON ct.MaSP = sp.MaSP 
        WHERE ct.MaDonHang = ?
    ");
    $stmtItems->execute([$orderId]);
    $cartItems = $stmtItems->fetchAll();
    
    // 3. Tính toán base URL tuyệt đối cho hình ảnh
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if (substr($baseDir, -1) !== '/') {
        $baseDir .= '/';
    }
    $absoluteBaseUrl = $protocol . '://' . $host . $baseDir;

    // 4. Tạo danh sách sản phẩm dạng HTML table row
    $productItemsRows = '';
    foreach ($cartItems as $item) {
        $imgUrl = $item['AnhChinh'];
        if (strpos($imgUrl, 'http://') !== 0 && strpos($imgUrl, 'https://') !== 0) {
            $imgUrl = $absoluteBaseUrl . ltrim($imgUrl, './');
        }
        
        $productItemsRows .= '
        <tr>
            <td width="60" style="padding-bottom: 15px; vertical-align: top;">
                <img src="' . htmlspecialchars($imgUrl) . '" width="60" height="60" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" alt="' . htmlspecialchars($item['TenSP']) . '">
            </td>
            <td style="padding-left: 15px; padding-bottom: 15px; vertical-align: top;">
                <div style="font-size: 14px; font-weight: 600; color: #ffffff; margin-bottom: 4px;">' . htmlspecialchars($item['TenSP']) . '</div>';
        if (!empty($item['KichCo'])) {
            $productItemsRows .= '
                <div style="font-size: 12px; color: #aaaaaa;">Size: ' . htmlspecialchars($item['KichCo']) . '</div>';
        }
        $productItemsRows .= '
            </td>
            <td align="right" style="padding-bottom: 15px; vertical-align: top; font-size: 14px; color: #ffffff; white-space: nowrap;">
                ' . number_format($item['DonGia'], 0, ',', '.') . 'đ<br>
                <small style="color: #aaaaaa; font-size: 12px;">x ' . $item['SoLuongSP'] . '</small>
            </td>
        </tr>';
    }

    // 5. Định dạng ngày đặt hàng và các chi phí
    $orderDate = date('d/m/Y H:i', strtotime($order['NgayTao']));
    $discount = (int)($order['SoTienGiam'] ?? 0);
    $discountRowEmail = '';
    if ($discount > 0) {
        $discountRowEmail = '
                <tr>
                    <td style="padding-bottom: 8px;">Mã giảm giá (' . htmlspecialchars($order['MaGiamGia']) . ')</td>
                    <td align="right" style="padding-bottom: 8px; color: #ffc107;">-' . number_format($discount, 0, ',', '.') . 'đ</td>
                </tr>';
    }
    
    // Tính tổng phụ = tổng tiền + số tiền giảm
    $subtotal = (int)$order['TongTien'] + $discount;
    $subtotalFormatted = number_format($subtotal, 0, ',', '.');
    $totalFormatted = number_format($order['TongTien'], 0, ',', '.');
    $orderDetailUrl = $absoluteBaseUrl . 'index.php?page=order-detail&id=' . $orderId;
    $customerEmail = trim($order['email'] ?? '');
    $customerName = $order['HoTenKH'] ?? 'Khách hàng';

    if (empty($customerEmail)) return false;

    // 6. Mẫu HTML email thanh toán thành công
    $emailBody = '
<div style="background-color:#121212;padding:30px 15px;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Arial,sans-serif;color:#ffffff;max-width:600px;margin:0 auto;border-radius:12px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:25px;border-bottom:1px solid #2a2a2a;padding-bottom:15px;">
        <tr><td align="center">
            <h2 style="margin:0;color:#f25c19;font-size:24px;font-weight:800;letter-spacing:1px;">VAB SHOP</h2>
            <span style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:2px;">Thời Trang Thể Thao Cao Cấp</span>
        </td></tr>
    </table>
    <div style="text-align:center;margin-bottom:25px;">
        <div style="width:70px;height:70px;background:rgba(16,185,129,0.15);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;border:2px solid #10b981;font-size:32px;line-height:70px;margin:0 auto;">✅</div>
        <h3 style="margin:15px 0 6px;font-size:20px;font-weight:700;color:#10b981;">Thanh toán thành công!</h3>
        <p style="margin:0;color:#aaa;font-size:14px;">Chúng tôi đã nhận được thanh toán và đang xử lý đơn hàng của bạn.</p>
    </div>
    
    <!-- Product List -->
    <div style="background-color: #1a1a1a; padding: 15px 15px 0 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #2a2a2a;">
        <table width="100%" cellpadding="0" cellspacing="0">
            ' . $productItemsRows . '
        </table>
    </div>

    <div style="background:#1a1a1a;padding:20px;border-radius:10px;margin-bottom:20px;border:1px solid #2a2a2a;">
        <h4 style="margin:0 0 15px;font-size:15px;font-weight:600;color:#fff;border-bottom:1px solid #2a2a2a;padding-bottom:10px;">Thông tin đơn hàng</h4>
        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;color:#aaa;">
            <tr>
                <td style="padding-bottom:10px;">Mã đơn hàng</td>
                <td align="right" style="padding-bottom:10px;color:#fff;font-weight:700;">#' . $orderId . '</td>
            </tr>
            <tr>
                <td style="padding-bottom:10px;">Ngày đặt</td>
                <td align="right" style="padding-bottom:10px;color:#fff;">' . $orderDate . '</td>
            </tr>
            <tr>
                <td style="padding-bottom:10px;">Phương thức</td>
                <td align="right" style="padding-bottom:10px;color:#10b981;font-weight:600;">Chuyển khoản SePay (Đã thanh toán)</td>
            </tr>
            <tr>
                <td style="padding-bottom:10px;">Tổng phụ</td>
                <td align="right" style="padding-bottom:10px;color:#fff;">' . $subtotalFormatted . 'đ</td>
            </tr>
            ' . $discountRowEmail . '
            <tr style="border-top:1px solid #2a2a2a;">
                <td style="padding-top:10px;">Tổng tiền đơn hàng</td>
                <td align="right" style="padding-top:10px;color:#f25c19;font-weight:700;font-size:16px;">' . $totalFormatted . 'đ</td>
            </tr>
        </table>
    </div>
    <div style="background:#1a1a1a;padding:20px;border-radius:10px;margin-bottom:25px;border:1px solid #2a2a2a;">
        <h4 style="margin:0 0 12px;font-size:15px;font-weight:600;color:#fff;border-bottom:1px solid #2a2a2a;padding-bottom:10px;">Địa chỉ giao hàng</h4>
        <p style="margin:0 0 5px;color:#fff;font-weight:600;">' . htmlspecialchars($customerName) . '</p>
        <p style="margin:0 0 5px;color:#aaa;font-size:13px;">' . htmlspecialchars($order['SDT'] ?? '') . '</p>
        <p style="margin:0;color:#aaa;font-size:13px;">' . htmlspecialchars($order['DiaChi'] ?? '') . '</p>
    </div>
    
    <!-- Action Button -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
        <tr>
            <td align="center">
                <a href="' . htmlspecialchars($orderDetailUrl) . '" style="display: inline-block; background-color: #df3852; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 50px; font-size: 14px; font-weight: 600;">Xem chi tiết đơn hàng</a>
            </td>
        </tr>
    </table>

    <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.3);border-radius:10px;padding:15px;margin-bottom:25px;text-align:center;">
        <p style="margin:0;color:#10b981;font-size:14px;font-weight:600;">🚀 Đơn hàng của bạn đang được xử lý và sẽ sớm được vận chuyển!</p>
    </div>
    <table width="100%" cellpadding="0" cellspacing="0" style="font-size:11px;color:#555;border-top:1px solid #2a2a2a;padding-top:15px;">
        <tr><td align="center">
            <p style="margin:0 0 5px;">Email này được gửi tự động từ hệ thống VAB SHOP.</p>
            <p style="margin:0;">&copy; 2026 VAB SHOP. All rights reserved.</p>
        </td></tr>
    </table>
</div>';

    $subject = "✅ Thanh toán thành công - Đơn hàng #" . $orderId . " | VAB SHOP";
    return sendMail($customerEmail, $customerName, $subject, $emailBody);
}

