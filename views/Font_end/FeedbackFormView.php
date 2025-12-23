<?php
// User feedback submission form - Modern Design
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$email = htmlspecialchars($_SESSION['user']['email'] ?? '');
$fullname = htmlspecialchars($_SESSION['user']['fullname'] ?? $email);

// Lấy thông báo nếu có
$flash_message = $_SESSION['flash_message'] ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message']);
unset($_SESSION['flash_type']);
?>

<link rel="stylesheet" href="<?= APP_URL ?>/public/css/feedback.css">

<!-- Modern Feedback Section -->
<section class="feedback-section-modern">
    <div class="container">
        <!-- Header Section -->
        <div class="feedback-header-modern">
            <div class="header-content">
                <div class="header-icon">
                    <i class="bi bi-chat-dots"></i>
                </div>
                <div class="header-text">
                    <h1 class="header-title">Góp ý cho hệ thống</h1>
                    <p class="header-subtitle">
                        Chúng tôi luôn lắng nghe ý kiến của bạn để cải thiện dịch vụ
                    </p>
                </div>
            </div>
        </div>

        <!-- Flash Message -->
        <?php if ($flash_message): ?>
        <div class="alert alert-<?= $flash_type ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-<?= $flash_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
            <?= htmlspecialchars($flash_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Feedback Form -->
        <div class="feedback-form-container">
            <div class="form-card-modern">
                <div class="form-header">
                    <div class="form-icon">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <h3 class="form-title">Gửi góp ý của bạn</h3>
                    <p class="form-description">
                        Mọi ý kiến đóng góp của bạn đều quý giá với chúng tôi
                    </p>
                </div>

                <form method="post" action="<?= APP_URL ?>/Feedback/store" class="feedback-form" id="feedbackForm">
                    <div class="form-body">
                        <!-- User Info -->
                        <div class="user-info-card">
                            <div class="user-avatar">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <div class="user-details">
                                <div class="user-name"><?= $fullname ?></div>
                                <div class="user-email"><?= $email ?></div>
                            </div>
                        </div>

                        <!-- Subject Field -->
                        <!-- Subject Field - Added back as optional -->
                        <div class="form-group-modern">
                            <label for="subject" class="form-label-modern">
                                <i class="bi bi-chat-left-text"></i>
                                Tiêu đề góp ý
                                <span class="label-optional">(Tùy chọn)</span>
                            </label>
                            <input type="text" 
                                   id="subject" 
                                   name="subject" 
                                   class="form-control-modern" 
                                   maxlength="255" 
                                   placeholder="Ví dụ: Góp ý về giao diện website, chất lượng sản phẩm...">
                            <div class="input-icon">
                                <i class="bi bi-hash"></i>
                            </div>
                        </div>

                        <!-- Content Field -->
                        <div class="form-group-modern">
                            <label for="content" class="form-label-modern">
                                <i class="bi bi-body-text"></i>
                                Nội dung góp ý
                                <span class="label-required">*</span>
                            </label>
                            <textarea id="content" 
                                      name="content" 
                                      class="form-control-modern form-textarea" 
                                      rows="8" 
                                      required 
                                      placeholder="Vui lòng mô tả chi tiết góp ý của bạn... 

Ví dụ:
- Vấn đề bạn gặp phải
- Gợi ý cải thiện
- Khen ngợi (nếu có)"></textarea>
                            <div class="textarea-icon">
                                <i class="bi bi-chat-square-text"></i>
                            </div>
                            <div class="char-counter">
                                <span id="charCount">0</span> / 1000 ký tự
                            </div>
                        </div>


                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions-modern">
                        <button type="submit" class="btn-submit-modern" id="submitBtn">
                            <span class="btn-icon">
                                <i class="bi bi-send"></i>
                            </span>
                            <span class="btn-text">Gửi góp ý</span>
                            <span class="btn-loader" style="display: none;">
                                <i class="bi bi-hourglass-split"></i>
                            </span>
                        </button>
                        
                        <a href="<?= APP_URL ?>/Feedback/my" class="btn-outline-modern">
                            <span class="btn-icon">
                                <i class="bi bi-clock-history"></i>
                            </span>
                            <span class="btn-text">Xem góp ý của tôi</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Benefits Section -->
        <div class="benefits-section">
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="bi bi-lightning"></i>
                    </div>
                    <h4 class="benefit-title">Xử lý nhanh chóng</h4>
                    <p class="benefit-description">Góp ý của bạn sẽ được xử lý trong thời gian sớm nhất</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4 class="benefit-title">Bảo mật thông tin</h4>
                    <p class="benefit-description">Thông tin cá nhân của bạn được bảo mật tuyệt đối</p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h4 class="benefit-title">Cải thiện dịch vụ</h4>
                    <p class="benefit-description">Góp ý của bạn giúp chúng tôi phát triển tốt hơn</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced JavaScript for modern functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced Character counter with progress bar
    const textarea = document.getElementById('content');
    const charCount = document.getElementById('charCount');
    const submitBtn = document.getElementById('submitBtn');
    
    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            // Change color when approaching limit
            if (length > 800) {
                charCount.style.color = '#e74c3c';
            } else if (length > 600) {
                charCount.style.color = '#f39c12';
            } else {
                charCount.style.color = '#999';
            }
            
            // Limit characters
            if (length > 1000) {
                this.value = this.value.substring(0, 1000);
                charCount.textContent = 1000;
            }
            
            // Auto resize textarea
            this.style.height = 'auto';
            this.style.height = Math.max(150, this.scrollHeight) + 'px';
        });
    }
    
    // Enhanced form submission with loading state and ripple effect
    const form = document.getElementById('feedbackForm');
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Enhanced validation
            const content = textarea.value.trim();
            const subject = document.getElementById('subject').value.trim();
            
            if (!content) {
                e.preventDefault();
                textarea.focus();
                textarea.classList.add('error');
                
                // Create enhanced error message
                showNotification('Vui lòng nhập nội dung góp ý', 'error');
                
                setTimeout(() => {
                    textarea.classList.remove('error');
                }, 3000);
                
                return false;
            }
            
            // Show enhanced loading state
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoader = submitBtn.querySelector('.btn-loader');
            const btnIcon = submitBtn.querySelector('.btn-icon');
            
            if (btnText && btnLoader && btnIcon) {
                btnText.style.display = 'none';
                btnLoader.style.display = 'inline-block';
                btnIcon.style.display = 'none';
                submitBtn.disabled = true;
                submitBtn.classList.add('loading');
            }
        });
    }
    
    // Enhanced ripple effect for buttons
    const buttons = document.querySelectorAll('.btn-submit-modern, .btn-outline-modern');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.classList.contains('loading')) return;
            
            const ripple = this.querySelector('.btn-ripple');
            if (ripple) {
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('animate');
                
                setTimeout(() => {
                    ripple.classList.remove('animate');
                }, 600);
            }
        });
    });
    
    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-icon">
                <i class="bi bi-${type === 'error' ? 'exclamation-triangle-fill' : 'info-circle-fill'}"></i>
            </div>
            <div class="notification-content">${message}</div>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
    
    // Add smooth scroll behavior
    const header = document.querySelector('.feedback-header-modern');
    if (header) {
        setTimeout(() => {
            header.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start',
                inline: 'nearest'
            });
        }, 100);
    }
    
    // Initialize AOS animations if available
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true
        });
    }
});
</script>