<?php
/**
 * View so sánh sản phẩm - Thiết kế hiện đại, gradient theme
 */
$products = $data['products'] ?? [];
$compareCount = count($products);
?>

<link rel="stylesheet" href="<?= APP_URL ?>/public/css/compare.css">

<!-- Modern Compare Section -->
<section class="compare-section-modern">
    <div class="container">
        <!-- Header Section -->
        <div class="compare-header-modern">
            <div class="header-content">
                <div class="header-icon">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div class="header-text">
                    <h1 class="header-title">So sánh sản phẩm</h1>
                    <p class="header-subtitle">
                        <span class="compare-count-highlight"><?= $compareCount ?></span>
                        sản phẩm đang được so sánh
                    </p>
                </div>
                <?php if (!empty($products)): ?>
                <div class="header-actions">
                    <a href="<?= APP_URL ?>/Home/show" class="btn btn-add-more">
                        <i class="bi bi-plus-lg"></i> Thêm sản phẩm
                    </a>
                    <a href="<?= APP_URL ?>/Wishlist/clearCompare" class="btn btn-clear-all"
                       onclick="return confirm('Xóa tất cả sản phẩm khỏi so sánh?')">
                        <i class="bi bi-trash"></i> Xóa tất cả
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Empty State -->
        <?php if ($compareCount === 0): ?>
        <div class="empty-compare-modern">
            <div class="empty-icon-wrapper">
                <div class="empty-icon-bg">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
            </div>
            <h3 class="empty-title">Chưa có sản phẩm so sánh</h3>
            <p class="empty-description">
                Hãy thêm sản phẩm vào danh sách so sánh để dễ dàng đối chiếu tính năng và giá.
            </p>
            <div class="empty-actions">
                <a href="<?= APP_URL ?>/Home/show" class="btn btn-explore">
                    <i class="bi bi-shop"></i> Khám phá sản phẩm
                </a>
            </div>
        </div>
        <?php elseif ($compareCount < 2): ?>
        <div class="empty-compare-modern">
            <div class="empty-icon-wrapper">
                <div class="empty-icon-bg">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
            </div>
            <h3 class="empty-title">Chưa đủ sản phẩm để so sánh</h3>
            <p class="empty-description">
                Vui lòng thêm ít nhất 2 sản phẩm để bắt đầu so sánh và tìm ra lựa chọn phù hợp nhất.
            </p>
            <div class="empty-actions">
                <a href="<?= APP_URL ?>/Home/show" class="btn btn-explore">
                    <i class="bi bi-shop"></i> Khám phá sản phẩm
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Compare Table Modern -->
        <?php if ($compareCount >= 2): ?>
        <div class="compare-table-modern">
            <div class="table-responsive">
                <table class="table compare-table">
                    <thead class="table-header-modern">
                        <tr>
                            <th class="feature-header">Tính năng</th>
                            <?php foreach ($products as $product): ?>
                                <th class="product-header-modern">
                                    <div class="product-card-header">
                                        <div class="product-image-wrapper">
                                            <img src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($product['hinhanh']) ?>"
                                                 alt="<?= htmlspecialchars($product['tensp']) ?>"
                                                 class="product-image">
                                            <div class="product-image-overlay">
                                                <a href="<?= APP_URL ?>/Home/detail/<?= htmlspecialchars($product['masp']) ?>"
                                                   class="view-detail-btn">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                            <?php if (($product['phantram'] ?? 0) > 0): ?>
                                            <div class="discount-badge">
                                                <span>-<?= (int)($product['phantram'] ?? 0) ?>%</span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-info-header">
                                            <h5 class="product-name"><?= htmlspecialchars($product['tensp']) ?></h5>
                                            <button class="remove-btn"
                                                    onclick="removeFromCompare('<?= htmlspecialchars($product['masp']) ?>')"
                                                    title="Xóa khỏi so sánh">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Giá -->
                        <tr class="compare-row">
                            <td class="feature-label">
                                <i class="bi bi-tag"></i> Giá bán
                            </td>
                            <?php foreach ($products as $product):
                                $phantram = $product['phantram'] ?? 0;
                                $giaGoc = (float)$product['giaXuat'];
                                $giaSauKM = $giaGoc * (1 - $phantram / 100);
                            ?>
                                <td class="feature-value price-cell">
                                    <?php if ($phantram > 0): ?>
                                        <div class="price-current">
                                            <?= number_format($giaSauKM, 0, ',', '.') ?> ₫
                                        </div>
                                        <div class="price-original">
                                            <?= number_format($giaGoc, 0, ',', '.') ?> ₫
                                        </div>
                                        <span class="discount-badge-small">
                                            Tiết kiệm <?= number_format($giaGoc - $giaSauKM, 0, ',', '.') ?> ₫
                                        </span>
                                    <?php else: ?>
                                        <div class="price-current">
                                            <?= number_format($giaGoc, 0, ',', '.') ?> ₫
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                        <!-- Đánh giá -->
                        <tr class="compare-row">
                            <td class="feature-label">
                                <i class="bi bi-star"></i> Đánh giá
                            </td>
                            <?php foreach ($products as $product):
                                $avgRating = $product['avg_rating'] ?? 0;
                                $ratingCount = $product['rating_count'] ?? 0;
                                $fullStars = floor($avgRating);
                                $hasHalfStar = ($avgRating - $fullStars) >= 0.5;
                            ?>
                                <td class="feature-value rating-cell">
                                    <div class="rating-stars">
                                        <?php for ($i = 0; $i < $fullStars; $i++): ?>
                                            <i class="bi bi-star-fill"></i>
                                        <?php endfor; ?>
                                        
                                        <?php if ($hasHalfStar): ?>
                                            <i class="bi bi-star-half"></i>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = ceil($avgRating); $i < 5; $i++): ?>
                                            <i class="bi bi-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="rating-info">
                                        <span class="rating-score"><?= number_format($avgRating, 1) ?>/5</span>
                                        <span class="rating-count">(<?= $ratingCount ?> đánh giá)</span>
                                    </div>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                        <!-- Mô tả -->
                        <tr class="compare-row">
                            <td class="feature-label">
                                <i class="bi bi-file-text"></i> Mô tả
                            </td>
                            <?php foreach ($products as $product): ?>
                                <td class="feature-value description-cell">
                                    <div class="description-content">
                                        <?= nl2br(htmlspecialchars($product['mota'] ?? 'Chưa có mô tả')) ?>
                                    </div>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                        <!-- Hành động -->
                        <tr class="compare-row action-row">
                            <td class="feature-label">
                                <i class="bi bi-cart"></i> Thao tác
                            </td>
                            <?php foreach ($products as $product): ?>
                                <td class="feature-value action-cell">
                                    <div class="action-buttons">
                                        <button class="btn-add-to-cart"
                                                onclick="addToCart('<?= htmlspecialchars($product['masp']) ?>')">
                                            <i class="bi bi-cart-plus"></i>
                                            <span>Thêm giỏ hàng</span>
                                        </button>
                                        <button class="btn-add-wishlist"
                                                onclick="addToWishlist('<?= htmlspecialchars($product['masp']) ?>')">
                                            <i class="bi bi-heart"></i>
                                            <span>Yêu thích</span>
                                        </button>
                                    </div>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Modern Compare Functions
function removeFromCompare(productId) {
    if (!confirm('Xóa sản phẩm này khỏi danh sách so sánh?')) return;
    
    fetch('<?= APP_URL ?>/Wishlist/removeFromCompare', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra', 'error');
    });
}

function addToWishlist(productId) {
    fetch('<?= APP_URL ?>/Wishlist/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        showToast(data.message, data.success ? 'success' : 'warning');
        if (data.success) {
            updateWishlistCount(data.count);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra', 'error');
    });
}

function addToCart(productId) {
    fetch('<?= APP_URL ?>/Cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
        showToast(data.message, data.success ? 'success' : 'warning');
        if (data.success) {
            updateCartCount(data.cart_count || 0);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
    });
}

// Toast Notification System
function showToast(message, type = 'info') {
    // Remove existing toast
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }
    
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    
    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-exclamation-triangle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };
    
    const colors = {
        success: '#48bb78',
        error: '#f56565',
        warning: '#ed8936',
        info: '#4299e1'
    };
    
    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="bi ${icons[type]}"></i>
            <span>${message}</span>
        </div>
    `;
    
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        color: ${colors[type]};
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        font-weight: 500;
        border-left: 4px solid ${colors[type]};
        animation: slideInRight 0.3s ease-out;
        max-width: 400px;
        font-size: 0.95rem;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Update count functions
function updateWishlistCount(count) {
    const wishlistCount = document.querySelector('.wishlist-count');
    if (wishlistCount) {
        wishlistCount.textContent = count;
    }
}

function updateCartCount(count) {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = count;
    }
}

// Add CSS for toast animations
const toastStyle = document.createElement('style');
toastStyle.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(toastStyle);

// Add loading state to buttons
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-add-to-cart, .btn-add-wishlist')) {
        const button = e.target.closest('.btn-add-to-cart, .btn-add-wishlist');
        const originalContent = button.innerHTML;
        
        button.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang xử lý...';
        button.disabled = true;
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        }, 2000);
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to compare rows
    const compareRows = document.querySelectorAll('.compare-row');
    compareRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Add pulse animation to discount badges
    const discountBadges = document.querySelectorAll('.discount-badge, .discount-badge-small');
    discountBadges.forEach(badge => {
        setInterval(() => {
            badge.style.transform = 'scale(1.05)';
            setTimeout(() => {
                badge.style.transform = 'scale(1)';
            }, 200);
        }, 3000);
    });
});
</script>