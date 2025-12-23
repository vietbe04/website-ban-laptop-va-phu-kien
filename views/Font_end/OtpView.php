<?php
/**
 * Form xác thực OTP.
 * Người dùng nhập mã OTP đã gửi về email để hoàn tất xác thực.
 * Bảo mật: chỉ một trường nhập; giá trị xử lý server side.
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực OTP - Thời trang DQV</title>
    <link rel="stylesheet" href="/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/otp.css">
</head>
<body>
    <div class="otp-container">
        <div class="otp-card">
            <div class="card-header">
                <div class="icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2>Xác thực OTP</h2>
                <p>Chúng tôi đã gửi mã xác thực 6 chữ số đến email của bạn. Vui lòng nhập mã để tiếp tục.</p>
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
            
            <div class="security-note">
                <i class="fas fa-info-circle"></i>
                <strong>Lưu ý:</strong> Mã OTP sẽ hết hạn sau 5 phút. Vui lòng không chia sẻ mã này với bất kỳ ai.
            </div>
            
            <form id="otpForm" action="<?= APP_URL ?>/AuthController/verifyOtp" method="POST">
                <div class="otp-input-group">
                    <input type="text" class="otp-input" maxlength="1" data-index="0" required>
                    <input type="text" class="otp-input" maxlength="1" data-index="1" required>
                    <input type="text" class="otp-input" maxlength="1" data-index="2" required>
                    <input type="text" class="otp-input" maxlength="1" data-index="3" required>
                    <input type="text" class="otp-input" maxlength="1" data-index="4" required>
                    <input type="text" class="otp-input" maxlength="1" data-index="5" required>
                </div>
                
                <input type="hidden" id="otp" name="otp" value="">
                
                <button type="submit" class="btn btn-verify" id="verifyBtn">
                    <i class="fas fa-check me-2"></i>
                    Xác thực
                </button>
                
                <div class="loading-spinner" id="loadingSpinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang xử lý...</span>
                    </div>
                    <p class="mt-2 mb-0">Đang xác thực...</p>
                </div>
            </form>
            
            <div class="timer-section">
                <div class="timer-text">Mã OTP sẽ hết hạn sau:</div>
                <div class="timer-display" id="timer">05:00</div>
            </div>
            
            <button type="button" class="btn btn-resend" id="resendBtn" disabled>
                <i class="fas fa-redo me-2"></i>
                Gửi lại mã OTP (<span id="countdown">300</span>)
            </button>
            
            <div class="back-link">
                <a href="<?= APP_URL ?>/AuthController/ShowLogin">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại đăng nhập
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        // OTP Input Handling
        const otpInputs = document.querySelectorAll('.otp-input');
        const otpHidden = document.getElementById('otp');
        const verifyBtn = document.getElementById('verifyBtn');
        
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const value = e.target.value;
                
                // Chỉ cho phép nhập số
                if (!/^\d$/.test(value)) {
                    e.target.value = '';
                    return;
                }
                
                // Tự động chuyển sang ô tiếp theo
                if (value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                
                // Cập nhật mã OTP ẩn
                updateOtpValue();
                
                // Kiểm tra xem đã nhập đủ 6 số chưa
                checkOtpComplete();
            });
            
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                    otpInputs[index - 1].value = '';
                    updateOtpValue();
                }
            });
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text');
                const digits = pasteData.replace(/\D/g, '').slice(0, 6);
                
                digits.split('').forEach((digit, i) => {
                    if (i < otpInputs.length) {
                        otpInputs[i].value = digit;
                    }
                });
                
                updateOtpValue();
                checkOtpComplete();
                
                // Focus vào ô cuối cùng hoặc ô trống đầu tiên
                const nextEmpty = otpInputs.findIndex(input => !input.value);
                if (nextEmpty !== -1) {
                    otpInputs[nextEmpty].focus();
                } else {
                    otpInputs[otpInputs.length - 1].focus();
                }
            });
        });
        
        function updateOtpValue() {
            const otpValue = Array.from(otpInputs).map(input => input.value).join('');
            otpHidden.value = otpValue;
            
            // Thêm hiệu ứng cho ô đã nhập
            otpInputs.forEach(input => {
                if (input.value) {
                    input.classList.add('filled');
                } else {
                    input.classList.remove('filled');
                }
            });
        }
        
        function checkOtpComplete() {
            const otpValue = otpHidden.value;
            if (otpValue.length === 6 && /^\d{6}$/.test(otpValue)) {
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="fas fa-check me-2"></i>Xác thực';
            } else {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Nhập đủ 6 số';
            }
        }
        
        // Timer functionality
        let timeLeft = 300; // 5 phút
        let timerInterval;
        let countdownInterval;
        
        function startTimer() {
            timerInterval = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('timer').textContent = '00:00';
                    document.getElementById('resendBtn').disabled = false;
                    document.getElementById('resendBtn').innerHTML = '<i class="fas fa-redo me-2"></i>Gửi lại mã OTP';
                    return;
                }
                
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                document.getElementById('timer').textContent = 
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                timeLeft--;
            }, 1000);
        }
        
        function startCountdown() {
            let countdown = 300;
            countdownInterval = setInterval(() => {
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    return;
                }
                
                document.getElementById('countdown').textContent = countdown;
                countdown--;
            }, 1000);
        }
        
        // Form submission
        document.getElementById('otpForm').addEventListener('submit', function(e) {
            const otpValue = otpHidden.value;
            
            if (otpValue.length !== 6 || !/^\d{6}$/.test(otpValue)) {
                e.preventDefault();
                alert('Vui lòng nhập đầy đủ 6 chữ số OTP!');
                return;
            }
            
            // Disable button and show loading
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xác thực...';
            document.getElementById('loadingSpinner').style.display = 'block';
        });
        
        // Resend OTP
        document.getElementById('resendBtn').addEventListener('click', function() {
            if (this.disabled) return;
            
            // Gửi yêu cầu resend OTP
            fetch('<?= APP_URL ?>/AuthController/resendOtp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: '<?= $_SESSION['reset_email'] ?? '' ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset timer
                    clearInterval(timerInterval);
                    clearInterval(countdownInterval);
                    timeLeft = 300;
                    
                    // Clear OTP inputs
                    otpInputs.forEach(input => {
                        input.value = '';
                        input.classList.remove('filled');
                    });
                    otpHidden.value = '';
                    
                    // Restart timer
                    startTimer();
                    startCountdown();
                    
                    // Disable resend button
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-redo me-2"></i>Gửi lại mã OTP (<span id="countdown">300</span>)';
                    
                    alert('Mã OTP mới đã được gửi!');
                } else {
                    alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            });
        });
        
        // Initialize
        startTimer();
        startCountdown();
        checkOtpComplete();
    </script>
</body>
</html>