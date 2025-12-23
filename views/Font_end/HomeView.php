<?php
/**
 * Trang chủ (HomeView) - Phiên bản cải tiến với thiết kế modern.
 * Dữ liệu nhận từ Controller qua $data:
 *  - featuredProducts: sản phẩm nổi bật.
 *  - latestArticles: bài viết mới.
 *  - activePromotions: danh sách khuyến mãi đang chạy.
 * Chức năng:
 *  - Tự động lấy danh sách banner từ thư mục /public/images/banners.
 *  - Hiển thị banner (carousel) với hiệu ứng modern.
 *  - Hiển thị khuyến mãi, sản phẩm nổi bật, tin tức với thiết kế card đẹp.
 * Bảo mật:
 *  - Dùng htmlspecialchars cho các trường văn bản người dùng/DB.
 */
$featured = $data['featuredProducts'] ?? [];
$articles = $data['latestArticles'] ?? [];
$promos = $data['activePromotions'] ?? [];
// Removed best-selling feature

// Lấy danh sách banner từ thư mục banners
$bannerDir = dirname(__DIR__, 2) . '/public/images/banners';
$bannerFiles = [];
if (is_dir($bannerDir)) {
    $files = scandir($bannerDir);
    foreach ($files as $file) {
        if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
            $bannerFiles[] = $file;
        }
    }
    shuffle($bannerFiles); // Trộn ngẫu nhiên để hiển thị đa dạng
}
?>

<!-- Header CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- App styles (load after vendors for highest priority) -->
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/public.css?v=<?= @filemtime(dirname(__DIR__, 2) . '/public/css/public.css') ?>">
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/home.css?v=<?= @filemtime(dirname(__DIR__, 2) . '/public/css/home.css') ?>">

<!-- Wishlist & Compare Script -->
<script src="<?= APP_URL ?>/public/js/wishlist-compare.js"></script>

<!-- Styles moved to public/css/home.css -->

<!-- Modern Hero Section with Banner Carousel -->
<section class="modern-hero-section">
    <div class="hero-background">
        <div class="hero-gradient"></div>
        <div class="hero-particles"></div>
    </div>
    
    <!-- Banner Carousel -->
    <?php if (!empty($bannerFiles)): ?>
    <div id="bannerCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <?php foreach ($bannerFiles as $index => $banner): ?>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="<?= $index ?>" <?= $index === 0 ? 'class="active"' : '' ?>></button>
            <?php endforeach; ?>
        </div>
        
        <div class="carousel-inner">
            <?php foreach ($bannerFiles as $index => $banner): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <div class="carousel-image-wrapper">
                    <img src="<?= APP_URL ?>/public/images/banners/<?= htmlspecialchars($banner) ?>" 
                         class="d-block w-100 carousel-image" alt="Banner <?= $index + 1 ?>">
                    <div class="carousel-overlay"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Trước</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sau</span>
        </button>
    </div>
    <?php else: ?>
    <!-- Fallback background pattern when no banners -->
    <div class="hero-pattern-bg"></div>
    <?php endif; ?>
       
      <!-- Hero content removed for minimal design -->
      <div class="hero-content-placeholder"></div>
</section>

<!-- Hero and carousel styles moved to public/css/home.css -->

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="feature-card hover-lift animate-fadeInUp" style="animation-delay: 0.1s;">
                    <div class="feature-icon animate-pulse">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h4 class="feature-title">Giao hàng nhanh</h4>
                    <p class="feature-desc">Miễn phí giao hàng toàn quốc cho đơn hàng từ 500k</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-card hover-lift animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="feature-icon animate-pulse">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4 class="feature-title">Bảo hành uy tín</h4>
                    <p class="feature-desc">Bảo hành chính hãng lên đến 24 tháng</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-card hover-lift animate-fadeInUp" style="animation-delay: 0.3s;">
                    <div class="feature-icon animate-pulse">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <h4 class="feature-title">Đổi trả dễ dàng</h4>
                    <p class="feature-desc">Đổi trả trong 7 ngày nếu sản phẩm lỗi</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-card hover-lift animate-fadeInUp" style="animation-delay: 0.4s;">
                    <div class="feature-icon animate-pulse">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h4 class="feature-title">Hỗ trợ 24/7</h4>
                    <p class="feature-desc">Tư vấn và hỗ trợ khách hàng mọi lúc</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Promo section removed as requested -->

<!-- Best-selling section removed per request -->

<!-- Enhanced Interactive Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Counter animation for statistics
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                element.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(start).toLocaleString();
            }
        }, 16);
    }

    // Animate statistics numbers when they come into view
    const statNumbers = document.querySelectorAll('.stat-number');
    const statObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const text = entry.target.textContent;
                const number = parseInt(text.replace(/[^\d]/g, ''));
                if (number > 0) {
                    animateCounter(entry.target, number, 2000);
                }
                statObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    statNumbers.forEach(stat => statObserver.observe(stat));

    // Enhanced hover effects for all cards
    document.querySelectorAll('.feature-card, .modern-product-card, .modern-news-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-15px) scale(1.02)';
            this.style.boxShadow = '0 25px 50px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 15px 35px rgba(0,0,0,0.08)';
        });
    });

    // Ripple effect for buttons
    document.querySelectorAll('.btn-add-cart, .btn-view-all, .read-more-link').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255,255,255,0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Countdown Timer Functionality
    function updateCountdowns() {
        document.querySelectorAll('.countdown-timer').forEach(timer => {
            const endDate = timer.dataset.endDate;
            if (!endDate) return;
            
            const endTime = new Date(endDate).getTime();
            const now = new Date().getTime();
            const distance = endTime - now;
            
            if (distance < 0) {
                timer.innerHTML = 'Đã kết thúc';
                timer.parentElement.style.background = 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            let timeString = '';
            if (days > 0) timeString += `${days}d `;
            if (hours > 0 || days > 0) timeString += `${hours}h `;
            if (minutes > 0 || hours > 0 || days > 0) timeString += `${minutes}m `;
            timeString += `${seconds}s`;
            
            timer.innerHTML = `Kết thúc sau: ${timeString}`;
        });
    }
    
    // Update countdowns every second
    updateCountdowns();
    setInterval(updateCountdowns, 1000);

    // Loading animation for images
    document.querySelectorAll('img').forEach(img => {
        img.addEventListener('load', function() {
            this.style.opacity = '1';
            this.style.transform = 'scale(1)';
        });
        
        img.addEventListener('error', function() {
            this.style.opacity = '0.5';
            this.style.filter = 'grayscale(100%)';
        });
    });

    // Add loading class to images initially
    document.querySelectorAll('img').forEach(img => {
        if (!img.complete) {
            img.style.opacity = '0';
            img.style.transform = 'scale(0.95)';
            img.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        }
    });

    // Add ripple animation to CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
        
        /* Enhanced carousel animations */
        .carousel-fade .carousel-item {
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        
        .carousel-fade .carousel-item.active {
            opacity: 1;
        }
        
        .carousel-image {
            animation: zoomIn 10s ease-in-out infinite;
        }
        
        @keyframes zoomIn {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    `;
    document.head.appendChild(style);
    
    // Enhanced carousel functionality
    const carousel = document.querySelector('#bannerCarousel');
    if (carousel) {
        // Pause carousel on hover
        carousel.addEventListener('mouseenter', function() {
            const bsCarousel = bootstrap.Carousel.getInstance(this);
            if (bsCarousel) bsCarousel.pause();
        });
        
        carousel.addEventListener('mouseleave', function() {
            const bsCarousel = bootstrap.Carousel.getInstance(this);
            if (bsCarousel) bsCarousel.cycle();
        });
        
        // Add touch swipe support
        let touchStartX = 0;
        let touchEndX = 0;
        
        carousel.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        carousel.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
        
        function handleSwipe() {
            const bsCarousel = bootstrap.Carousel.getInstance(carousel);
            if (!bsCarousel) return;
            
            if (touchEndX < touchStartX - 50) {
                bsCarousel.next();
            }
            if (touchEndX > touchStartX + 50) {
                bsCarousel.prev();
            }
        }
    }

    // Parallax effect for hero section
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const heroSection = document.querySelector('.modern-hero-section');
        if (heroSection) {
            heroSection.style.transform = `translateY(${scrolled * 0.1}px)`;
        }
    });

    // Floating animation for cards
    function addFloatingAnimation() {
        const cards = document.querySelectorAll('.feature-card, .modern-product-card, .modern-news-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-fadeInUp');
        });
    }

    addFloatingAnimation();
});

// Premium Featured Products JavaScript
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const productCards = document.querySelectorAll('.featured-products-grid .col-12');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active tab
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Filter products
            filterProducts(filter);
        });
    });
    
    function filterProducts(filter) {
        productCards.forEach(card => {
            const categories = card.dataset.category.split(' ');
            
            if (filter === 'all' || categories.includes(filter)) {
                card.classList.remove('hidden');
                setTimeout(() => {
                    card.style.display = 'block';
                }, 10);
            } else {
                card.classList.add('hidden');
                setTimeout(() => {
                    card.style.display = 'none';
                }, 300);
            }
        });
    }
});

// Enhanced quantity selector
document.addEventListener('DOMContentLoaded', function() {
    const qtySelectors = document.querySelectorAll('.modern-qty');
    
    qtySelectors.forEach(selector => {
        const minusBtn = selector.querySelector('.minus');
        const plusBtn = selector.querySelector('.plus');
        const input = selector.querySelector('.qty-input');
        
        minusBtn.addEventListener('click', function() {
            const currentValue = parseInt(input.value);
            const minValue = parseInt(input.min);
            if (currentValue > minValue) {
                input.value = currentValue - 1;
                animateButton(this);
            }
        });
        
        plusBtn.addEventListener('click', function() {
            const currentValue = parseInt(input.value);
            const maxValue = parseInt(input.max);
            if (currentValue < maxValue) {
                input.value = currentValue + 1;
                animateButton(this);
            }
        });
        
        input.addEventListener('change', function() {
            const value = parseInt(this.value);
            const minValue = parseInt(this.min);
            const maxValue = parseInt(this.max);
            
            if (value < minValue) this.value = minValue;
            if (value > maxValue) this.value = maxValue;
        });
    });
    
    function animateButton(button) {
        button.style.transform = 'scale(0.9)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 100);
    }
});

// Wishlist functionality
function addToWishlist(productId) {
    const wishlistBtn = event.target.closest('.wishlist-btn');
    const icon = wishlistBtn.querySelector('i');
    
    // Toggle wishlist state
    if (icon.classList.contains('bi-heart')) {
        icon.classList.remove('bi-heart');
        icon.classList.add('bi-heart-fill');
        wishlistBtn.style.color = '#e53e3e';
        showNotification('Đã thêm vào yêu thích!', 'success');
    } else {
        icon.classList.remove('bi-heart-fill');
        icon.classList.add('bi-heart');
        wishlistBtn.style.color = '#4a5568';
        showNotification('Đã xóa khỏi yêu thích!', 'info');
    }
    
    // Add animation
    wishlistBtn.style.transform = 'scale(1.2)';
    setTimeout(() => {
        wishlistBtn.style.transform = 'scale(1)';
    }, 200);
}

// Compare functionality
function addToCompare(productId) {
    const compareBtn = event.target.closest('.compare-btn');
    const icon = compareBtn.querySelector('i');
    
    // Toggle compare state
    if (icon.classList.contains('bi-arrow-left-right')) {
        icon.classList.remove('bi-arrow-left-right');
        icon.classList.add('bi-check-circle-fill');
        compareBtn.style.color = '#38a169';
        showNotification('Đã thêm vào so sánh!', 'success');
    } else {
        icon.classList.remove('bi-check-circle-fill');
        icon.classList.add('bi-arrow-left-right');
        compareBtn.style.color = '#4a5568';
        showNotification('Đã xóa khỏi so sánh!', 'info');
    }
    
    // Add animation
    compareBtn.style.transform = 'scale(1.2)';
    setTimeout(() => {
        compareBtn.style.transform = 'scale(1)';
    }, 200);
}

// Notify when available
function notifyWhenAvailable(productId) {
    const notifyBtn = event.target.closest('.btn-notify-me');
    const originalText = notifyBtn.innerHTML;
    
    notifyBtn.innerHTML = '<i class="bi bi-check-circle"></i> Đã đăng ký!';
    notifyBtn.style.background = 'linear-gradient(135deg, #38a169 0%, #2f855a 100%)';
    
    showNotification('Chúng tôi sẽ thông báo khi có hàng!', 'success');
}

// News card animations
function initNewsAnimations() {
    const newsCards = document.querySelectorAll('.modern-news-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    newsCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
}

// Initialize news animations when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initNewsAnimations();
});

// Add hover effects for news cards
document.addEventListener('DOMContentLoaded', function() {
    const newsCards = document.querySelectorAll('.modern-news-card');
    
    newsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-15px) scale(1.02)';
            
            // Add shimmer effect
            const shimmer = document.createElement('div');
            shimmer.className = 'card-shimmer';
            shimmer.style.cssText = `
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.6s ease;
                z-index: 1;
            `;
            this.appendChild(shimmer);
            
            setTimeout(() => {
                shimmer.style.left = '100%';
            }, 100);
            
            setTimeout(() => {
                shimmer.remove();
            }, 700);
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
    
    setTimeout(() => {
        notifyBtn.innerHTML = originalText;
        notifyBtn.style.background = 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)';
    }, 3000);


// Load more feature removed

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'info-circle-fill'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Add notification styles
const notificationStyles = `
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    transform: translateX(400px);
    transition: transform 0.3s ease;
    z-index: 1000;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.notification.show {
    transform: translateX(0);
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
}

.notification-success {
    background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
    color: white;
}

.notification-info {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    color: white;
}

.notification i {
    font-size: 1.2rem;
}

@media (max-width: 768px) {
    .notification {
        top: auto;
        bottom: 20px;
        right: 20px;
        left: 20px;
        transform: translateY(100px);
    }
    
    .notification.show {
        transform: translateY(0);
    }
}
`;

// Add styles to head
const styleSheet = document.createElement('style');
styleSheet.textContent = notificationStyles;
document.head.appendChild(styleSheet);
</script>

<script>
// Update quantity function for product cards
function updateQuantity(button, change) {
    const input = button.parentElement.querySelector('input[type="number"]');
    const currentValue = parseInt(input.value);
    const maxValue = parseInt(input.max);
    const minValue = parseInt(input.min);
    
    let newValue = currentValue + change;
    if (newValue < minValue) newValue = minValue;
    if (newValue > maxValue) newValue = maxValue;
    
    input.value = newValue;
}
</script>

<?php if (!empty($featured)): ?>
<section class="featured-section premium-section">
    <div class="container">
        <div class="section-header modern-header">
            <h2 class="section-title modern-title">
                <i class="bi bi-star-fill text-warning"></i> 
                Sản phẩm nổi bật
            </h2>
            <p class="section-subtitle modern-subtitle">Những sản phẩm được yêu thích và đánh giá cao nhất</p>
        </div>
        
        <!-- Filter tabs for featured products -->
        <div class="filter-tabs">
            <button class="filter-tab active" data-filter="all">Tất cả</button>
            <button class="filter-tab" data-filter="discount">Đang giảm giá</button>
            <button class="filter-tab" data-filter="bestseller">Bán chạy</button>
            <button class="filter-tab" data-filter="new">Mới nhất</button>
        </div>
        
        <div class="row g-4 featured-products-grid" id="featuredProducts">
            <?php foreach ($featured as $index => $fp):
                $phantram = (float)($fp['phantram'] ?? 0);
                $giaGoc = (float)($fp['giaXuat'] ?? 0);
                $giaSale = $phantram>0 ? $giaGoc*(1-$phantram/100) : $giaGoc;
                $soLuong = (int)($fp['soluong'] ?? 0);
                $daBan = (int)($fp['daban'] ?? 0);
                $avgRating = $fp['avg_rating'] ?? 0;
                $ratingCount = $fp['rating_count'] ?? 0;
                $soldCount = $fp['sold_count'] ?? 0;
                $isNew = (time() - strtotime($fp['created_at'] ?? 'now')) < (30 * 24 * 60 * 60); // 30 days
                
                // Determine product category for filtering
                $productCategory = [];
                if ($phantram > 0) $productCategory[] = 'discount';
                // Gắn nhóm bán chạy nếu được controller đánh dấu hoặc có bán >0
                if (!empty($fp['is_bestseller']) || $daBan > 0) $productCategory[] = 'bestseller';
                if ($isNew) $productCategory[] = 'new';
                $productCategory[] = 'all';
            ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3" data-category="<?= implode(' ', $productCategory) ?>">
                <div class="card h-100 product-card border-0">
                    <div class="product-image-container">
                        <a href="<?= APP_URL ?>/Home/detail/<?= urlencode($fp['masp']) ?>" class="d-block">
                            <img src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($fp['hinhanh']) ?>" 
                                 alt="<?= htmlspecialchars($fp['tensp']) ?>" 
                                 class="img-fluid" 
                                 style="height: 220px; width: 100%; object-fit: contain;">
                        </a>
                        <?php if($phantram>0): ?>
                            <span class="discount-badge">
                                <i class="bi bi-lightning-fill"></i> -<?= (int)$phantram ?>%
                            </span>
                        <?php endif; ?>
                        
                        <!-- Quick View Overlay -->
                        <div class="product-overlay">
                            <a href="<?= APP_URL ?>/Home/detail/<?= urlencode($fp['masp']) ?>" class="quick-view">
                                <i class="bi bi-eye-fill"></i> Xem chi tiết
                            </a>
                        </div>
                        
                        <!-- Wishlist & Compare Buttons -->
                        <div class="product-actions-top" style="position: absolute; top: 10px; right: 10px; z-index: 10; display: flex; gap: 5px;">
                            <button onclick="toggleWishlist('<?= htmlspecialchars($fp['masp']) ?>', this)" 
                                    class="btn btn-sm btn-light rounded-circle" 
                                    style="width: 35px; height: 35px; padding: 0;"
                                    title="Thêm vào yêu thích">
                                <i class="bi bi-heart"></i>
                            </button>
                            <button onclick="toggleCompare('<?= htmlspecialchars($fp['masp']) ?>', this)" 
                                    class="btn btn-sm btn-light rounded-circle" 
                                    style="width: 35px; height: 35px; padding: 0;"
                                    title="So sánh">
                                <i class="bi bi-arrow-left-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="product-title" title="<?= htmlspecialchars($fp['tensp']) ?>">
                            <?= htmlspecialchars($fp['tensp']) ?>
                        </h6>
                        
                        <!-- Đánh giá sao và lượt bán -->
                        <div class="product-stats mb-2">
                            <div class="rating-display">
                                <span class="stars-small">
                                    <?php
                                        $fullStars = floor($avgRating);
                                        $hasHalfStar = ($avgRating - $fullStars) >= 0.5;
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $fullStars) {
                                                echo '<i class="bi bi-star-fill text-warning"></i>';
                                            } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                                                echo '<i class="bi bi-star-half text-warning"></i>';
                                            } else {
                                                echo '<i class="bi bi-star text-muted"></i>';
                                            }
                                        }
                                    ?>
                                </span>
                                <span class="rating-text"><?= number_format($avgRating, 1) ?></span>
                                <span class="rating-count text-muted">(<?= $ratingCount ?>)</span>
                            </div>
                            <div class="sold-count">
                                <i class="bi bi-bag-check-fill text-success"></i>
                                <span>Đã bán: <strong><?= number_format($soldCount) ?></strong></span>
                            </div>
                        </div>
                        
                        <div class="price-container">
                            <?php if($phantram>0): ?>
                                <div class="original-price">
                                    <i class="bi bi-currency-exchange"></i> <?= number_format($giaGoc, 0, ',', '.') ?> ₫
                                </div>
                                <div class="discount-price">
                                    <i class="bi bi-tag-fill"></i> <?= number_format($giaSale,0,',','.') ?> ₫
                                </div>
                            <?php else: ?>
                                <div class="normal-price">
                                    <i class="bi bi-tag-fill"></i> <?= number_format($giaGoc,0,',','.') ?> ₫
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="stock-info">
                            <i class="bi bi-box-seam"></i> Còn: <?= $soLuong ?> sản phẩm
                        </div>
                        
                        <form method="post" action="<?= APP_URL ?>/Home/addtocard/<?= urlencode($fp['masp']) ?>" class="mt-auto">
                            <div class="quantity-input-group">
                                <label for="qty_<?= htmlspecialchars($fp['masp']) ?>" class="mb-0">
                                    <i class="bi bi-calculator"></i> Số lượng:
                                </label>
                                <input id="qty_<?= htmlspecialchars($fp['masp']) ?>" 
                                       type="number" 
                                       name="qty" 
                                       class="form-control form-control-sm" 
                                       value="1" 
                                       min="1" 
                                       max="<?= $soLuong ?>" 
                                       required>
                            </div>
                            <button class="btn btn-primary add-to-cart-btn w-100" type="submit">
                                <i class="bi bi-cart-plus-fill"></i> Thêm vào giỏ hàng
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- View all products -->
        <div class="text-center mt-3">
            <a href="<?= APP_URL ?>/Home/show" class="btn-view-all-products">
                <i class="bi bi-grid-3x3-gap"></i> Tất cả sản phẩm
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if(!empty($articles)): ?>
<section class="news-section-modern">
    <div class="container">
        <div class="section-header modern-header">
            <h2 class="section-title modern-title">
                <i class="bi bi-newspaper"></i> 
                Tin tức mới nhất
            </h2>
            <p class="section-subtitle modern-subtitle">Cập nhật thông tin và tin tức công nghệ mới nhất</p>
        </div>
        <div class="row g-4">
            <?php foreach($articles as $index => $a): 
                $createdDate = date('d/m/Y', strtotime($a['created_at'] ?? 'now'));
                $contentExcerpt = htmlspecialchars(mb_substr(strip_tags($a['content'] ?? ''), 0, 150)) . '...';
                $title = htmlspecialchars($a['title'] ?? 'Không có tiêu đề');
                $imageUrl = !empty($a['image']) ? APP_URL . '/public/images/' . htmlspecialchars($a['image']) : '';
                $detailUrl = APP_URL . '/Article/detail/' . ($a['id'] ?? 0);
            ?>
            <div class="col-md-6 col-lg-4">
                <article class="modern-news-card hover-lift animate-fadeInUp" style="animation-delay: <?= $index * 0.1 ?>s;">
                    <div class="news-image-wrapper">
                        <?php if(!empty($a['image'])): ?>
                            <img src="<?= $imageUrl ?>" 
                                 alt="<?= $title ?>" 
                                 class="news-image" 
                                 loading="lazy">
                        <?php else: ?>
                            <div class="news-placeholder">
                                <i class="bi bi-newspaper"></i>
                                <span>Tin tức</span>
                            </div>
                        <?php endif; ?>
                        <div class="news-date-tag animate-pulse">
                            <i class="bi bi-calendar3"></i> 
                            <?= $createdDate ?>
                        </div>
                    </div>
                    <div class="news-content">
                        <h3 class="news-title"><?= $title ?></h3>
                        <p class="news-excerpt"><?= $contentExcerpt ?></p>
                        <div class="news-footer">
                            <div class="news-meta">
                                <span class="meta-item">
                                    <i class="bi bi-calendar3"></i>
                                    <?= $createdDate ?>
                                </span>
                                <span class="meta-item">
                                    <i class="bi bi-person"></i>
                                    <?= htmlspecialchars($a['author'] ?? 'Admin') ?>
                                </span>
                                <?php if(!empty($a['view_count'])): ?>
                                <span class="meta-item">
                                    <i class="bi bi-eye"></i>
                                    <?= number_format($a['view_count']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= $detailUrl ?>" class="read-more-link hover-glow">
                                Đọc tiếp
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?= APP_URL ?>/Article/list" class="btn-view-all">
                <i class="bi bi-arrow-right"></i> Xem tất cả tin tức
            </a>
        </div>
    </div>
</section>
<?php endif; ?>