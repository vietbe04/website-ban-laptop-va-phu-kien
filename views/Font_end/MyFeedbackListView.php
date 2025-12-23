<?php
$feedbacks = $data['feedbacks'] ?? [];
$currentPage = (int)($data['currentPage'] ?? 1);
$totalPages = (int)($data['totalPages'] ?? 1);
$total = (int)($data['total'] ?? 0);

// Hàm format thời gian
function formatTimeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Vừa xong';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' phút trước';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' giờ trước';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' ngày trước';
    } else {
        return date('d/m/Y', $time);
    }
}

// Hàm lấy màu sắc theo trạng thái
function getStatusColor($status) {
    switch ($status) {
        case 1: return ['success', 'Đã phản hồi', 'bi-check-circle-fill'];
        case 2: return ['secondary', 'Đã đóng', 'bi-x-circle-fill'];
        default: return ['warning', 'Chờ xử lý', 'bi-clock-fill'];
    }
}
?>

<!-- Modern Feedback List Section -->
<section class="feedback-list-section">
    <div class="container">
        <!-- Enhanced Header -->
        <div class="feedback-list-header">
            <div class="header-content">
                <div class="header-icon-wrapper">
                    <div class="header-icon">
                        <i class="bi bi-chat-left-text"></i>
                    </div>
                    <div class="header-pulse"></div>
                </div>
                <div class="header-text">
                    <h1 class="header-title">Góp ý của tôi</h1>
                    <p class="header-subtitle">
                        Theo dõi trạng thái và phản hồi từ đội ngũ hỗ trợ
                    </p>
                    <div class="header-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?= $total ?></span>
                            <span class="stat-label">Tổng góp ý</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?= count(array_filter($feedbacks, fn($f) => $f['status'] == 1)) ?></span>
                            <span class="stat-label">Đã phản hồi</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?= count(array_filter($feedbacks, fn($f) => $f['status'] == 0)) ?></span>
                            <span class="stat-label">Chờ xử lý</span>
                        </div>
                    </div>
                </div>
            </div>
            <a href="<?= APP_URL ?>/Feedback/create" class="btn-submit-modern">
                <i class="bi bi-plus-circle"></i>
                Gửi góp ý mới
            </a>
        </div>

        <!-- Feedback Cards -->
        <div class="feedback-cards-container">
            <?php if (empty($feedbacks)): ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h3 class="empty-title">Chưa có góp ý nào</h3>
                    <p class="empty-description">
                        Bạn chưa gửi bất kỳ góp ý nào cho hệ thống. <br>
                        Hãy chia sẻ ý kiến của bạn để chúng tôi cải thiện dịch vụ.
                    </p>
                    <a href="<?= APP_URL ?>/Feedback/create" class="btn-outline-modern">
                        <i class="bi bi-pencil-square"></i>
                        Gửi góp ý đầu tiên
                    </a>
                </div>
            <?php else: ?>
                <?php foreach($feedbacks as $index => $f): 
                    [$statusColor, $statusText, $statusIcon] = getStatusColor($f['status']);
                ?>
                    <div class="feedback-card" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="feedback-card-header">
                            <div class="feedback-info">
                                <div class="feedback-id">#<?= (int)$f['id'] ?></div>
                                <div class="feedback-time">
                                    <i class="bi bi-clock"></i>
                                    <?= formatTimeAgo($f['created_at']) ?>
                                </div>
                            </div>
                            <div class="feedback-status status-<?= $statusColor ?>">
                                <i class="bi <?= $statusIcon ?>"></i>
                                <?= $statusText ?>
                            </div>
                        </div>

                        <div class="feedback-card-body">
                            <?php if (!empty($f['subject'])): ?>
                                <h4 class="feedback-subject"><?= htmlspecialchars($f['subject']) ?></h4>
                            <?php endif; ?>
                            <div class="feedback-content">
                                <?= nl2br(htmlspecialchars($f['content'])) ?>
                            </div>
                        </div>

                        <?php if (!empty($f['admin_reply'])): ?>
                            <div class="feedback-card-reply">
                                <div class="reply-header">
                                    <div class="reply-avatar">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                    <div class="reply-info">
                                        <div class="reply-author">Quản trị viên</div>
                                        <div class="reply-time">
                                            <?php if (!empty($f['replied_at'])): ?>
                                                <i class="bi bi-clock"></i>
                                                <?= formatTimeAgo($f['replied_at']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="reply-content">
                                    <?= nl2br(htmlspecialchars($f['admin_reply'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
        <div class="pagination-container">
            <nav aria-label="Page navigation">
                <ul class="pagination-modern">
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= APP_URL ?>/Feedback/my?page=<?= max(1, $currentPage - 1) ?>">
                            <i class="bi bi-chevron-left"></i>
                            Trước
                        </a>
                    </li>
                    
                    <?php 
                    // Hiển thị các trang
                    $start = max(1, $currentPage - 2);
                    $end = min($totalPages, $currentPage + 2);
                    
                    if ($start > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= APP_URL ?>/Feedback/my?page=1">1</a>
                        </li>
                        <?php if ($start > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= APP_URL ?>/Feedback/my?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= APP_URL ?>/Feedback/my?page=<?= $totalPages ?>"><?= $totalPages ?></a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= APP_URL ?>/Feedback/my?page=<?= min($totalPages, $currentPage + 1) ?>">
                            Sau
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="pagination-info">
                Hiển thị trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng: <?= $total ?> góp ý)
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Custom CSS for Feedback List -->
<style>
.feedback-list-section {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 60px 0;
    position: relative;
    overflow: hidden;
}

.feedback-list-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><pattern id='grain' width='100' height='100' patternUnits='userSpaceOnUse'><circle cx='25' cy='25' r='1' fill='rgba(255,255,255,0.1)'/><circle cx='75' cy='75' r='1' fill='rgba(255,255,255,0.1)'/><circle cx='50' cy='10' r='0.5' fill='rgba(255,255,255,0.05)'/><circle cx='10' cy='60' r='0.5' fill='rgba(255,255,255,0.05)'/><circle cx='90' cy='40' r='0.5' fill='rgba(255,255,255,0.05)'/></pattern></defs><rect width='100' height='100' fill='url(%23grain)'/></svg>");
    opacity: 0.3;
}

.feedback-list-header {
    text-align: center;
    margin-bottom: 50px;
    position: relative;
    z-index: 2;
}

.header-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
}

.header-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    animation: pulse 2s infinite;
}

.header-icon i {
    font-size: 36px;
    color: white;
}

.header-text {
    color: #333;
}

.header-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.header-subtitle {
    font-size: 1.2rem;
    color: #666;
    font-weight: 300;
}

.header-stats {
    display: flex;
    gap: 30px;
    justify-content: center;
    margin-top: 20px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.8rem;
    font-weight: 700;
    color: #667eea;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
}

.feedback-cards-container {
    max-width: 1000px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.feedback-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    margin-bottom: 25px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.feedback-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.feedback-card-header {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 20px 25px;
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.feedback-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.feedback-id {
    font-weight: 700;
    color: #667eea;
    font-size: 1.1rem;
}

.feedback-time {
    color: #666;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.feedback-status {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.status-success {
    background: linear-gradient(135deg, #4CAF50, #45a049);
    color: white;
}

.status-warning {
    background: linear-gradient(135deg, #FF9800, #F57C00);
    color: white;
}

.status-secondary {
    background: linear-gradient(135deg, #757575, #616161);
    color: white;
}

.feedback-card-body {
    padding: 25px;
}

.feedback-subject {
    font-size: 1.3rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
    line-height: 1.4;
}

.feedback-content {
    color: #555;
    line-height: 1.7;
    font-size: 1rem;
    white-space: pre-wrap;
}

.feedback-card-reply {
    margin: 0 25px 25px;
    padding: 20px;
    background: linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%);
    border-radius: 15px;
    border-left: 4px solid #667eea;
}

.reply-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.reply-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.reply-avatar i {
    font-size: 20px;
    color: white;
}

.reply-author {
    font-weight: 600;
    color: #333;
    font-size: 1rem;
}

.reply-time {
    color: #666;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

.reply-content {
    color: #444;
    line-height: 1.6;
    white-space: pre-wrap;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    backdrop-filter: blur(10px);
}

.empty-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
}

.empty-icon i {
    font-size: 40px;
    color: #999;
}

.empty-title {
    font-size: 1.8rem;
    color: #333;
    margin-bottom: 10px;
    font-weight: 600;
}

.empty-description {
    color: #666;
    margin-bottom: 30px;
    line-height: 1.6;
}

.btn-submit-modern,
.btn-outline-modern {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 30px;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    min-width: 180px;
    justify-content: center;
}

.btn-submit-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-submit-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-outline-modern {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
}

.btn-outline-modern:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

.pagination-container {
    margin-top: 40px;
    text-align: center;
    position: relative;
    z-index: 2;
}

.pagination-modern {
    display: inline-flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 8px;
}

.page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.page-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    text-decoration: none;
    color: #666;
    background: white;
    transition: all 0.3s ease;
    font-weight: 500;
}

.page-link:hover {
    background: #f8f9ff;
    color: #667eea;
    border-color: #667eea;
}

.pagination-info {
    margin-top: 15px;
    color: #666;
    font-size: 0.9rem;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-title {
        font-size: 2rem;
    }
    
    .header-stats {
        flex-direction: column;
        gap: 15px;
    }
    
    .feedback-card-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .feedback-info {
        order: 2;
    }
    
    .feedback-status {
        align-self: flex-end;
    }
    
    .pagination-modern {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .feedback-list-header {
        flex-direction: column;
        gap: 20px;
    }
}

@media (max-width: 480px) {
    .feedback-list-section {
        padding: 40px 0;
    }
    
    .feedback-card-body,
    .feedback-card-header {
        padding: 20px;
    }
    
    .feedback-subject {
        font-size: 1.1rem;
    }
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.feedback-card {
    animation: fadeInUp 0.6s ease-out;
}

.empty-state {
    animation: fadeInUp 0.8s ease-out;
}
</style>

<!-- Add AOS Animation CSS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS animations
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true
        });
    }
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.feedback-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Smooth scroll for pagination
    const paginationLinks = document.querySelectorAll('.page-link');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.parentElement.classList.contains('disabled')) {
                // Scroll to top of the section
                document.querySelector('.feedback-list-section').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>