<?php
/**
 * View hiển thị danh sách sản phẩm yêu thích (Wishlist) - Thiết kế hiện đại
 */
$wishlist = $data['wishlist'] ?? [];
?>

<!-- Modern Wishlist Section -->
<section class="wishlist-section-modern">
    <div class="container">
        <!-- Header Section -->
        <div class="wishlist-header-modern">
            <div class="container">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <div class="header-text">
                        <h1 class="header-title">Danh sách yêu thích</h1>
                        <p class="header-subtitle">
                            <span class="wishlist-count-highlight"><?= count($wishlist) ?></span> 
                            sản phẩm bạn yêu thích
                        </p>
                    </div>
                    <?php if (!empty($wishlist)): ?>
                    <button class="btn btn-clear-all" onclick="clearAllWishlist()">
                        <i class="bi bi-trash3"></i> Xóa tất cả
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <?php if (empty($wishlist)): ?>
        <div class="empty-wishlist-modern">
            <div class="empty-icon-wrapper">
                <div class="empty-icon-bg">
                    <i class="bi bi-heart"></i>
                </div>
            </div>
            <h3 class="empty-title">Chưa có sản phẩm yêu thích</h3>
            <p class="empty-description">
                Hãy khám phá và thêm những sản phẩm bạn yêu thích vào đây để không bỏ lỡ ưu đãi!
            </p>
            <a href="<?= APP_URL ?>/Home/show" class="btn btn-explore">
                <i class="bi bi-shop"></i> Khám phá ngay
            </a>
        </div>

        <!-- Product Grid -->
        <?php else: ?>
        <div class="wishlist-grid-modern">
            <?php foreach ($wishlist as $item): 
                $phantram = $item['phantram'] ?? 0;
                $giaGoc = (float)$item['giaxuat'];
                $giaSauKM = $giaGoc * (1 - $phantram / 100);
                $avgRating = $item['avg_rating'] ?? 0;
                $ratingCount = (int)($item['rating_count'] ?? 0);
            ?>
            
            <div class="wishlist-item-modern" data-product-id="<?= htmlspecialchars($item['product_id']) ?>">
                <div class="product-card-modern">
                    <!-- Product Image -->
                    <div class="product-image-wrapper-modern">
                        <a href="<?= APP_URL ?>/Home/detail/<?= htmlspecialchars($item['product_id']) ?>" 
                           class="product-image-link">
                            <img src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($item['hinhanh']) ?>" 
                                 alt="<?= htmlspecialchars($item['tensp']) ?>"
                                 class="product-image">
                            <div class="product-image-overlay">
                                <span class="view-detail-text">
                                    <i class="bi bi-eye"></i> Xem chi tiết
                                </span>
                            </div>
                        </a>
                        
                        <!-- Discount Badge -->
                        <?php if ($phantram > 0): ?>
                        <div class="discount-badge-modern">
                            <span class="discount-text">-<?= (int)$phantram ?>%</span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Remove Button -->
                        <button class="remove-btn-modern" 
                                onclick="removeFromWishlist('<?= htmlspecialchars($item['product_id']) ?>', this)"
                                title="Xóa khỏi yêu thích">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        
                        <!-- Quick Actions -->
                        <div class="quick-actions-modern">
                            <button class="action-btn quick-view-btn" 
                                    onclick="window.location.href='<?= APP_URL ?>/Home/detail/<?= htmlspecialchars($item['product_id']) ?>'"
                                    title="Xem nhanh">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="action-btn compare-btn" 
                                    onclick="addToCompare('<?= htmlspecialchars($item['product_id']) ?>')"
                                    title="So sánh">
                                <i class="bi bi-arrow-left-right"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="product-info-modern">
                        <h3 class="product-name-modern">
                            <a href="<?= APP_URL ?>/Home/detail/<?= htmlspecialchars($item['product_id']) ?>">
                                <?= htmlspecialchars($item['tensp']) ?>
                            </a>
                        </h3>
                        
                        <!-- Rating -->
                        <div class="rating-modern">
                            <div class="stars">
                                <?php 
                                $fullStars = floor($avgRating);
                                $hasHalfStar = ($avgRating - $fullStars) >= 0.5;
                                
                                for ($i = 0; $i < $fullStars; $i++): ?>
                                    <i class="bi bi-star-fill"></i>
                                <?php endfor;
                                
                                if ($hasHalfStar): ?>
                                    <i class="bi bi-star-half"></i>
                                <?php endif;
                                
                                for ($i = ceil($avgRating); $i < 5; $i++): ?>
                                    <i class="bi bi-star"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-count">(<?= $ratingCount ?>)</span>
                        </div>
                        
                        <!-- Price -->
                        <div class="price-section-modern">
                            <?php if ($phantram > 0): ?>
                                <div class="price-current">
                                    <?= number_format($giaSauKM, 0, ',', '.') ?> ₫
                                </div>
                                <div class="price-original">
                                    <?= number_format($giaGoc, 0, ',', '.') ?> ₫
                                </div>
                            <?php else: ?>
                                <div class="price-current">
                                    <?= number_format($giaGoc, 0, ',', '.') ?> ₫
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Description -->
                        <p class="product-description-modern">
                            <?= htmlspecialchars(substr($item['mota'] ?? '', 0, 100)) ?><?= strlen($item['mota'] ?? '') > 100 ? '...' : '' ?>
                        </p>
                    </div>
                    
                    <!-- Product Actions -->
                    <div class="product-actions-modern">
                        <button class="btn-add-to-cart" 
                                onclick="addToCart('<?= htmlspecialchars($item['product_id']) ?>')">
                            <i class="bi bi-cart-plus"></i>
                            <span>Thêm vào giỏ</span>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modern Wishlist Styles -->
<style>
/* Wishlist Section Modern */
.wishlist-section-modern {
    padding: 80px 0;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: 70vh;
}

/* Header Modern */
.wishlist-header-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem;
    border-radius: 24px;
    margin-bottom: 3rem;
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 2rem;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.header-icon {
    width: 70px;
    height: 70px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    backdrop-filter: blur(10px);
}

.header-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    background: linear-gradient(45deg, #ffffff, #f0f8ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.header-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0;
}

.wishlist-count-highlight {
    font-weight: 700;
    color: #ffd700;
    font-size: 1.4rem;
}

.btn-clear-all {
    background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-clear-all:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
    background: linear-gradient(135deg, #ff5252, #ff7b7b);
}

.btn-clear-all:active {
    transform: translateY(0);
}

.btn-clear-all i {
    font-size: 1.1rem;
}

/* Empty State Modern */
.empty-wishlist-modern {
    text-align: center;
    padding: 5rem 2rem;
    max-width: 600px;
    margin: 0 auto;
}

.empty-icon-wrapper {
    margin-bottom: 2rem;
}

.empty-icon-bg {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    animation: pulse 2s infinite;
}

.empty-icon-bg i {
    font-size: 3rem;
    color: white;
}

.empty-title {
    font-size: 2rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1rem;
}

.empty-description {
    font-size: 1.1rem;
    color: #718096;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.btn-explore {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-explore:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
    color: white;
}

/* Wishlist Grid Modern */
.wishlist-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

/* Product Card Modern */
.wishlist-item-modern {
    transition: all 0.3s ease;
}

.product-card-modern {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card-modern:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

/* Product Image */
.product-image-wrapper-modern {
    position: relative;
    overflow: hidden;
    height: 280px;
}

.product-image-link {
    display: block;
    height: 100%;
    position: relative;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.product-image-link:hover .product-image {
    transform: scale(1.1);
}

.product-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-image-link:hover .product-image-overlay {
    opacity: 1;
}

.view-detail-text {
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Discount Badge Modern */
.discount-badge-modern {
    position: absolute;
    top: 15px;
    left: 15px;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 700;
    font-size: 0.9rem;
    box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
    z-index: 2;
}

/* Remove Button Modern */
.remove-btn-modern {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #dc3545;
    font-size: 1.2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    z-index: 3;
}

.remove-btn-modern:hover {
    background: #dc3545;
    color: white;
    transform: rotate(90deg) scale(1.1);
}

/* Quick Actions Modern */
.quick-actions-modern {
    position: absolute;
    bottom: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.3s ease;
    z-index: 3;
}

.product-card-modern:hover .quick-actions-modern {
    opacity: 1;
    transform: translateY(0);
}

.action-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.95);
    color: #4a5568;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    font-size: 1.1rem;
}

.action-btn:hover {
    background: #667eea;
    color: white;
    transform: scale(1.1);
}

/* Product Info Modern */
.product-info-modern {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-name-modern {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.product-name-modern a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.product-name-modern a:hover {
    color: #667eea;
}

/* Rating Modern */
.rating-modern {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.stars {
    color: #ffd700;
    font-size: 0.9rem;
}

.rating-count {
    color: #718096;
    font-size: 0.9rem;
}

/* Price Section Modern */
.price-section-modern {
    margin-bottom: 1rem;
}

.price-current {
    font-size: 1.5rem;
    font-weight: 700;
    color: #e53e3e;
    margin-bottom: 0.25rem;
}

.price-original {
    font-size: 1.1rem;
    color: #a0aec0;
    text-decoration: line-through;
}

/* Product Description Modern */
.product-description-modern {
    color: #718096;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 1.5rem;
    flex: 1;
}

/* Product Actions Modern */
.product-actions-modern {
    padding: 0 1.5rem 1.5rem;
}

.btn-add-to-cart {
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-add-to-cart:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

/* Animations */
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .wishlist-section-modern {
        padding: 40px 0;
    }
    
    .wishlist-header-modern {
        padding: 2rem;
        flex-direction: column;
        text-align: center;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .header-title {
        font-size: 2rem;
    }
    
    .wishlist-grid-modern {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .product-image-wrapper-modern {
        height: 250px;
    }
    
    .empty-wishlist-modern {
        padding: 3rem 1rem;
    }
}

@media (max-width: 480px) {
    .wishlist-header-modern {
        padding: 1.5rem;
    }
    
    .header-title {
        font-size: 1.8rem;
    }
    
    .product-image-wrapper-modern {
        height: 200px;
    }
    
    .product-info-modern {
        padding: 1rem;
    }
    
    .product-name-modern {
        font-size: 1.1rem;
    }
    
    .price-current {
        font-size: 1.3rem;
    }
}
</style>

<script>
function removeFromWishlist(productId, btn) {
    if (!confirm('Xóa sản phẩm này khỏi danh sách yêu thích?')) return;
    
    // Thêm hiệu ứng loading
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    btn.disabled = true;

    const params = new URLSearchParams();
    params.append('product_id', productId);

    fetch('<?= APP_URL ?>/Wishlist/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: params.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hiệu ứng fade out cho card
            const card = btn.closest('.wishlist-item-modern');
            card.style.transition = 'all 0.5s ease';
            card.style.transform = 'scale(0.8)';
            card.style.opacity = '0';
            
            setTimeout(() => {
                card.remove();
                
                updateWishlistCount(data.count ?? 0);

                // Nếu không còn sản phẩm nào, reload trang
                if (data.count === 0) {
                    location.reload();
                }
                
                // Hiển thị thông báo thành công
                showNotification('Đã xóa khỏi danh sách yêu thích', 'success');
            }, 500);
        } else {
            alert(data.message);
            btn.innerHTML = '<i class="bi bi-x-lg"></i>';
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra');
        btn.innerHTML = '<i class="bi bi-x-lg"></i>';
        btn.disabled = false;
    });
}

function addToCompare(productId) {
    const params = new URLSearchParams();
    params.append('product_id', productId);

    fetch('<?= APP_URL ?>/Wishlist/addToCompare', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: params.toString()
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'error');
        if (data.success) {
            // Cập nhật số lượng compare trong header
            const compareCount = document.querySelector('.compare-count');
            if (compareCount) {
                compareCount.textContent = data.count;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra', 'error');
    });
}

function addToCart(productId) {
    const params = new URLSearchParams();
    params.append('qty', '1');

    fetch('<?= APP_URL ?>/Home/addtocard/' + encodeURIComponent(productId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: params.toString()
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message || 'Không thể thêm vào giỏ hàng', data.success ? 'success' : 'error');
        if (data.success) {
            const totalCount = data.cartCountQty ?? data.cartCount ?? 0;
            updateCartBadge(totalCount);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra', 'error');
    });
}

function clearAllWishlist() {
    if (!confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm khỏi danh sách yêu thích?')) return;

    fetch('<?= APP_URL ?>/Wishlist/clear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hiệu ứng fade out cho toàn bộ grid
            const grid = document.querySelector('.wishlist-grid-modern');
            if (grid) {
                grid.style.transition = 'all 0.5s ease';
                grid.style.opacity = '0';
            }

            updateWishlistCount(0);
            
            setTimeout(() => {
                location.reload();
            }, 500);
            
            showNotification('Đã xóa tất cả sản phẩm khỏi danh sách yêu thích', 'success');
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra', 'error');
    });
}

function updateWishlistCount(count) {
    const headerCount = document.querySelector('.wishlist-count-highlight');
    if (headerCount) {
        headerCount.textContent = count;
    }

    const navBadge = document.querySelector('.wishlist-nav-btn .wishlist-count');
    if (navBadge) {
        navBadge.textContent = count;
        navBadge.style.display = count > 0 ? 'inline-block' : 'none';
    }
}

function updateCartBadge(count) {
    const cartButton = document.querySelector('.cart-btn');
    if (!cartButton) {
        return;
    }

    let badge = cartButton.querySelector('.cart-badge');
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'cart-badge';
        cartButton.appendChild(badge);
    }

    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'flex';
    } else {
        badge.textContent = '0';
        badge.style.display = 'none';
    }
}

// Hiển thị thông báo đẹp
function showNotification(message, type = 'info') {
    // Tạo notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : type === 'error' ? 'exclamation-triangle-fill' : 'info-circle-fill'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Thêm vào body
    document.body.appendChild(notification);
    
    // Trigger animation
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

// CSS cho notification
const notificationStyle = document.createElement('style');
notificationStyle.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        z-index: 9999;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        min-width: 300px;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .notification-success {
        border-left: 4px solid #28a745;
        color: #28a745;
    }
    
    .notification-error {
        border-left: 4px solid #dc3545;
        color: #dc3545;
    }
    
    .notification-info {
        border-left: 4px solid #17a2b8;
        color: #17a2b8;
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
            min-width: auto;
        }
        
        .notification.show {
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(notificationStyle);
</script>