<?php
/**
 * Form đăng ký tài khoản (RegisterView).
 * Thu thập: họ tên, email, mật khẩu.
 * Bảo mật: autocomplete tắt, mật khẩu không hiển thị lại.
 */
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h4 mb-4 text-center">Đăng ký thành viên</h2>
                    <form action="<?= APP_URL; ?>/AuthController/register" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Họ tên</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required placeholder="Nguyễn Văn A">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="name@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Tạo tài khoản</button>
                        </div>
                    </form>
                    <hr class="my-4">
                    <div class="text-center small">Đã có tài khoản? <a href="<?= APP_URL; ?>/AuthController/ShowLogin">Đăng nhập</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
