<?php
/**
 * Form quên mật khẩu: người dùng nhập email để nhận hướng dẫn đặt lại.
 * Bảo mật: chỉ thu thập email, không tự động điền dữ liệu nhạy cảm.
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Thời trang DQV</title>
    <link rel="stylesheet" href="/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/forgot-password.css">
</head>
<body>
    <div class="forgot-password-container">
        <div class="forgot-password-card">
            <div class="card-header">
                <div class="icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h2>Quên mật khẩu?</h2>
                <p>Nhập email của bạn để nhận hướng dẫn đặt lại mật khẩu</p>
            </div>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <form id="forgotPasswordForm" action="<?= APP_URL ?>/AuthController/resetPassword" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-2"></i>Email
                    </label>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           placeholder="Nhập email của bạn"
                           required>
                    <div class="invalid-feedback">
                        Vui lòng nhập email hợp lệ
                    </div>
                </div>
                
                <button type="submit" class="btn btn-submit" id="submitBtn">
                    <i class="fas fa-paper-plane me-2"></i>
                    Gửi yêu cầu đặt lại mật khẩu
                </button>
                
                <div class="loading-spinner" id="loadingSpinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang xử lý...</span>
                    </div>
                    <p class="mt-2 mb-0">Đang gửi email...</p>
                </div>
            </form>
            
            <div class="back-to-login">
                <a href="<?= APP_URL ?>/AuthController/ShowLogin">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại đăng nhập
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';
            loadingSpinner.style.display = 'block';
        });
        
        // Form validation
        const emailInput = document.getElementById('email');
        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>