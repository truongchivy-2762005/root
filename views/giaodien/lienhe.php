<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Liên hệ - VAB';
    $trangHienTai = 'contact';
    include 'dautrang.php';
}
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section"><div class="container"><h1>LIÊN HỆ</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item active">Liên hệ</li></ol></nav>
</div></section>

<section class="pb-5"><div class="container">
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="contact-info-card">
                <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h5>Địa chỉ</h5>
                <p>123 Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="contact-info-card">
                <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                <h5>Số điện thoại</h5>
                <p>1900 1234<br>028.3829.1234</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="contact-info-card">
                <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                <h5>Email</h5>
                <p>info@vab.com<br>support@vab.com</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="admin-table-wrapper p-4">
                <h5 class="fw-bold mb-4">Gửi liên hệ</h5>
                <form>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="fw-bold" style="font-size:14px;">Họ tên</label><input type="text" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="fw-bold" style="font-size:14px;">Email</label><input type="email" class="form-control" required></div>
                    </div>
                    <div class="mb-3"><label class="fw-bold" style="font-size:14px;">Số điện thoại</label><input type="tel" class="form-control" required></div>
                    <div class="mb-3"><label class="fw-bold" style="font-size:14px;">Nội dung</label><textarea class="form-control" rows="4" required></textarea></div>
                    <button type="submit" class="btn btn-secondary rounded-pill px-5 py-2" style="background:var(--color-secondary);border:none;">Gửi liên hệ</button>
                </form>
            </div>
            <div class="mt-3">
                <h5 class="fw-bold mb-3">Mạng xã hội</h5>
                <div class="d-flex gap-3">
                    <a href="#" style="width:50px;height:50px;border-radius:50%;background:#0a1628;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;transition:all 0.3s;" onmouseover="this.style.background='#f25c19'" onmouseout="this.style.background='#0a1628'"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" style="width:50px;height:50px;border-radius:50%;background:#0a1628;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;transition:all 0.3s;" onmouseover="this.style.background='#f25c19'" onmouseout="this.style.background='#0a1628'"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="width:50px;height:50px;border-radius:50%;background:#0a1628;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;transition:all 0.3s;" onmouseover="this.style.background='#f25c19'" onmouseout="this.style.background='#0a1628'"><i class="fab fa-tiktok"></i></a>
                    <a href="#" style="width:50px;height:50px;border-radius:50%;background:#0a1628;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;transition:all 0.3s;" onmouseover="this.style.background='#f25c19'" onmouseout="this.style.background='#0a1628'"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="contact-map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.424!2d106.703!3d10.775!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTDCsDQ2JzMwLjAiTiAxMDbCsDQyJzEwLjgiRQ!5e0!3m2!1svi!2s!4v1" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div></section>
<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>