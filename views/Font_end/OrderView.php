<?php
/**
 * Trang giỏ hàng (OrderView).
 * Hiển thị:
 *  - Danh sách sản phẩm trong giỏ với giá, khuyến mãi, biến thể.
 *  - Tính tạm tính và áp dụng giảm giá ngưỡng + mã giảm giá (preview).
 *  - Form cập nhật số lượng / xóa sản phẩm, chuyển sang checkout.
 * Bảo mật: tất cả dữ liệu đầu ra dùng htmlspecialchars.
 */
?>
<?php if (!empty($data['success'])): ?>
    <div class="alert alert-success text-center mt-3">
        <?= htmlspecialchars($data['success']) ?>
    </div>
<?php endif; ?>

<form action="<?= APP_URL ?>/Home/update" method="post">
<div class="container my-5">
    <h2 class="mb-4 text-center fw-bold text-primary">🛒 Giỏ Hàng Của Bạn</h2>

    <?php if (empty($data["listProductOrder"])): ?>
        <div class="alert alert-info text-center">
            Giỏ hàng của bạn đang trống 😢
            <a href="<?= APP_URL ?>/Home" class="alert-link">Mua sắm ngay!</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="list-group shadow-sm rounded-3">
                    <?php
                    $tongtien = 0;
                    foreach ($data["listProductOrder"] as $k => $v):
                        $phantram = 0;
                        if (!empty($v["phantram"])) {
                            $phantram = (float)$v["phantram"];
                        } elseif (!empty($v["khuyenmai"])) {
                            $phantram = (float)$v["khuyenmai"];
                        }
                        $giaGoc = (float)$v["giaxuat"];
                        $giaSauKM = ($phantram > 0) ? $giaGoc * (1 - $phantram / 100) : $giaGoc;
                        $thanhtien = $giaSauKM * (int)$v["qty"];
                        $tongtien += $thanhtien;
                        $checked = !empty($v["note_cart"]) && $v["note_cart"] == 1 ? 'checked' : '';
                    ?>
                    <div class="list-group-item p-3">
                        <div class="d-flex gap-3 align-items-center">
                            <div style="width:110px; flex: 0 0 110px;">
                                <img src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($v['hinhanh']) ?>" alt="<?= htmlspecialchars($v['tensp']) ?>" class="img-fluid rounded" style="height:90px; object-fit:contain;">
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 fw-bold mb-0"><?= htmlspecialchars($v["tensp"]) ?></h6>
                                        <small class="text-muted d-block">Mã: <?= htmlspecialchars($v["masp"]) ?></small>
                                        <?php
                                            // Hiển thị biến thể dung lượng & màu sắc (ưu tiên mới), fallback legacy
                                            $hasCapacity = !empty($v['capacity_variant_name']);
                                            $hasColor = !empty($v['color_variant_name']);
                                            if ($hasCapacity || $hasColor):
                                                if ($hasCapacity): ?>
                                                    <small class="text-primary d-block">Dung lượng: <?= htmlspecialchars($v['capacity_variant_name']) ?></small>
                                                <?php endif; if ($hasColor): ?>
                                                    <small class="text-primary d-block">Màu sắc: <?= htmlspecialchars($v['color_variant_name']) ?></small>
                                                <?php endif;
                                            elseif (!empty($v['variant_id'])): ?>
                                                <small class="text-primary d-block">Biến thể: <?= htmlspecialchars($v['variant_name']) ?></small>
                                            <?php endif; ?>
                                    </div>
                                    <div class="text-end">
                                        <?php if ($phantram > 0): ?>
                                            <div class="text-muted small text-decoration-line-through"><?= number_format($giaGoc,0,',','.') ?> ₫</div>
                                            <div class="fw-bold text-danger"><?= number_format($giaSauKM,0,',','.') ?> ₫</div>
                                            <div class="badge bg-warning text-dark small">-<?= $phantram ?>%</div>
                                        <?php else: ?>
                                            <div class="fw-bold text-danger"><?= number_format($giaSauKM,0,',','.') ?> ₫</div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="text-secondary small mb-0">Số lượng</label>
                                        <input type="number" name="qty[<?= htmlspecialchars($k) ?>]" value="<?= (int)$v["qty"] ?>" min="1" class="form-control form-control-sm text-center" style="width:70px;">
                                    </div>

                                    <div class="text-end">
                                        <div class="small text-secondary">Thành tiền</div>
                                        <div class="fw-bold text-success"><?= number_format($thanhtien,0,',','.') ?> ₫</div>
                                        <div class="mt-2">
                                            <a href="<?= APP_URL ?>/Home/delete/<?= urlencode($k) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xoá sản phẩm này?');">🗑️ Xóa</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-12 col-lg-4 mt-3 mt-lg-0">
                <div class="card shadow-sm rounded-3 p-3">
                    <h5 class="fw-bold">Tóm tắt đơn hàng</h5>
                    <hr>
                    <?php
                        // Tính giảm giá ngưỡng (threshold) ngay trên trang giỏ
                        $thresholdPercent = 0; $thresholdDiscount = 0; $afterThreshold = $tongtien; $thresholdMin = 0;
                        try {
                            require_once __DIR__ . '/../../models/ThresholdDiscountModel.php';
                            $tModel = new ThresholdDiscountModel();
                            $tiers = $tModel->getActiveTiers();
                            foreach ($tiers as $t) {
                                $minVal = isset($t['min_total']) ? (int)$t['min_total'] : (int)$t['min'];
                                $pVal = (int)$t['percent'];
                                // choose the highest percent applicable; prefer larger min when equal percent
                                if ($tongtien >= $minVal && $pVal >= $thresholdPercent) {
                                    $thresholdPercent = $pVal;
                                    $thresholdMin = $minVal;
                                }
                            }
                            if ($thresholdPercent > 0) {
                                $thresholdDiscount = (int)round($tongtien * $thresholdPercent / 100, 0);
                                $afterThreshold = max(0, $tongtien - $thresholdDiscount);
                            }
                        } catch (Exception $e) { /* ignore, giữ giá gốc */ }
                        // Nếu có coupon đã áp (trong session) hiển thị tạm nhưng tổng cuối vẫn tính ở trang checkout
                        // Do not apply coupon value to the cart preview total here.
                        // Keep threshold discount applied but keep coupon only applied during checkout.
                        $couponDiscount = 0;
                        if (!empty($_SESSION['coupon']['discount'])) {
                            $couponDiscount = (int)$_SESSION['coupon']['discount'];
                        }
                        $finalPreviewTotal = max(0, $afterThreshold);
                    ?>
                    <div class="d-flex justify-content-between">
                        <div>Tạm tính</div>
                        <div class="fw-bold"><?= number_format($tongtien,0,',','.') ?> ₫</div>
                    </div>
                    <?php if ($thresholdDiscount > 0): ?>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="me-2 flex-grow-1" style="min-width:0;">
                            <span class="d-block text-truncate" style="max-width:100%;" title="Đơn hàng &gt; <?= number_format($thresholdMin,0,',','.') ?> ₫">Đơn hàng &gt; <?= number_format($thresholdMin,0,',','.') ?> ₫</span>
                        </div>
                        <div class="text-success flex-shrink-0" title="Giảm <?= $thresholdPercent ?>%">-<?= number_format($thresholdDiscount,0,',','.') ?> ₫ <small class="text-muted">(<?= $thresholdPercent ?>%)</small></div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Mã giảm giá -->
                    <div class="mt-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-ticket-perforated me-1"></i>Mã giảm giá
                        </label>
                        <div class="input-group">
                            <input type="text" id="cart_coupon_code" class="form-control" 
                                   value="<?= htmlspecialchars($_SESSION['coupon']['code'] ?? '') ?>" 
                                   placeholder="Nhập mã giảm giá">
                            <button class="btn btn-outline-primary" type="button" id="apply-cart-coupon-btn">
                                <i class="bi bi-tag me-1"></i>Áp dụng
                            </button>
                        </div>
                        <div id="cart-coupon-result" class="mt-2"></div>
                    </div>
                    
                    <?php if ($couponDiscount > 0): ?>
                    <div class="d-flex justify-content-between mt-2">
                        <div>Mã giảm giá</div>
                        <div class="text-success">-<?= number_format($couponDiscount,0,',','.') ?> ₫</div>
                    </div>
                    <?php 
                        $finalPreviewTotal = max(0, $afterThreshold - $couponDiscount);
                    endif; ?>
                    
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <div class="h5 mb-0">Tổng cộng (tạm tính)</div>
                        <div class="h5 mb-0 text-danger fw-bold" id="cart-final-total"><?= number_format($finalPreviewTotal,0,',','.') ?> ₫</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">🔄 Cập nhật giỏ hàng</button>
                        <a href="<?= APP_URL ?>/Home/checkout" class="btn btn-success">🛒 Tiến hành đặt hàng</a>
                        <a href="<?= APP_URL ?>/Home" class="btn btn-link text-decoration-none">Tiếp tục mua sắm</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sản phẩm gợi ý -->
        <?php 
        $recommendedProducts = $data['recommendedProducts'] ?? [];
        if (!empty($recommendedProducts)): 
        ?>
        <div class="row mt-5">
            <div class="col-12">
                <div class="mb-4">
                    <h3 class="fw-bold text-primary mb-2">
                        <i class="fas fa-star text-warning me-2"></i>
                        Sản phẩm đề xuất cho bạn
                    </h3>
                    <p class="text-muted">Các sản phẩm tương tự có thể bạn quan tâm</p>
                </div>
            </div>

            <?php foreach ($recommendedProducts as $product): 
                $phantram = !empty($product['phantram']) ? (float)$product['phantram'] : 0;
                $giaGoc = (float)$product['giaXuat'];
                $giaSauKM = $phantram > 0 ? $giaGoc * (1 - $phantram/100) : $giaGoc;
            ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100 product-card border-0">
                    <div class="product-image-container">
                        <a href="<?= APP_URL ?>/Home/detail/<?= urlencode($product['masp']) ?>" class="d-block">
                            <img src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($product['hinhanh']) ?>" 
                                 alt="<?= htmlspecialchars($product['tensp']) ?>" 
                                 class="img-fluid" 
                                 style="height: 220px; width: 100%; object-fit: contain;">
                        </a>
                        <?php if($phantram > 0): ?>
                            <span class="discount-badge">
                                <i class="bi bi-lightning-fill"></i> -<?= (int)$phantram ?>%
                            </span>
                        <?php endif; ?>
                        
                        <div class="product-overlay">
                            <a href="<?= APP_URL ?>/Home/detail/<?= urlencode($product['masp']) ?>" class="quick-view">
                                <i class="bi bi-eye-fill"></i> Xem chi tiết
                            </a>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="product-title" title="<?= htmlspecialchars($product['tensp']) ?>">
                            <?= htmlspecialchars($product['tensp']) ?>
                        </h6>
                        
                        <div class="product-stats mb-2">
                            <?php 
                                $avgRating = $product['avg_rating'] ?? 0;
                                $ratingCount = $product['rating_count'] ?? 0;
                                $soldCount = $product['sold_count'] ?? 0;
                            ?>
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
                            <?php if($phantram > 0): ?>
                                <div class="original-price">
                                    <i class="bi bi-currency-exchange"></i> <?= number_format($giaGoc, 0, ',', '.') ?> ₫
                                </div>
                                <div class="discount-price">
                                    <i class="bi bi-tag-fill"></i> <?= number_format($giaSauKM, 0, ',', '.') ?> ₫
                                </div>
                            <?php else: ?>
                                <div class="normal-price">
                                    <i class="bi bi-tag-fill"></i> <?= number_format($giaGoc, 0, ',', '.') ?> ₫
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="stock-info">
                            <i class="bi bi-box-seam"></i> Còn: <?= (int)$product['soluong'] ?> sản phẩm
                        </div>
                        
                        <button class="btn btn-primary add-to-cart-btn w-100 mt-auto btn-add-to-cart" 
                                data-masp="<?= $product['masp'] ?>"
                                type="button">
                            <i class="bi bi-cart-plus-fill"></i> Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</form>

<style>
/* Recommended products styling - matching ProductListView */
.product-card {
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-radius: 12px;
    overflow: hidden;
}
.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}
.product-image-container {
    position: relative;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 15px;
}
.discount-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.4);
    z-index: 2;
}
.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.product-card:hover .product-overlay {
    opacity: 1;
}
.quick-view {
    color: white;
    text-decoration: none;
    font-weight: 600;
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 25px;
    border: 2px solid white;
    transition: all 0.3s ease;
}
.quick-view:hover {
    background: white;
    color: #0d6efd;
    transform: scale(1.1);
}
.product-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2c3e50;
    min-height: 45px;
    line-height: 1.4;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    margin-bottom: 0.75rem;
}
.product-stats {
    font-size: 0.85rem;
    padding: 8px 0;
    border-top: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
}
.rating-display {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 6px;
}
.stars-small i {
    font-size: 0.875rem;
}
.rating-text {
    font-weight: 600;
    color: #f39c12;
    margin-left: 4px;
}
.rating-count {
    font-size: 0.8rem;
}
.sold-count {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.85rem;
    color: #6c757d;
}
.price-container {
    margin: 10px 0;
}
.original-price {
    font-size: 0.85rem;
    color: #999;
    text-decoration: line-through;
    margin-bottom: 4px;
}
.discount-price {
    font-size: 1.1rem;
    color: #e74c3c;
    font-weight: 700;
}
.normal-price {
    font-size: 1.1rem;
    color: #2c3e50;
    font-weight: 700;
}
.stock-info {
    font-size: 0.85rem;
    color: #27ae60;
    margin: 8px 0;
    font-weight: 500;
}
.add-to-cart-btn {
    padding: 10px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.add-to-cart-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
}
</style>

<script>
// Quick add to cart from recommended products
document.addEventListener('DOMContentLoaded', function() {
    // Apply coupon from cart page
    const applyCouponBtn = document.getElementById('apply-cart-coupon-btn');
    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', async function() {
            const code = document.getElementById('cart_coupon_code').value.trim();
            const resultEl = document.getElementById('cart-coupon-result');
            
            if (!code) {
                resultEl.innerHTML = '<small class="text-warning"><i class="bi bi-exclamation-circle me-1"></i>Vui lòng nhập mã giảm giá</small>';
                return;
            }
            
            try {
                const resp = await fetch('<?= APP_URL ?>/Home/applyCoupon', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ coupon_code: code }).toString()
                });
                
                const data = await resp.json();
                
                if (data.valid) {
                    resultEl.innerHTML = '<small class="text-success"><i class="bi bi-check-circle me-1"></i>Áp dụng thành công! Giảm ' + new Intl.NumberFormat('vi-VN').format(data.discount) + ' ₫</small>';
                    
                    // Update total display
                    const finalTotal = document.getElementById('cart-final-total');
                    if (finalTotal) {
                        finalTotal.textContent = new Intl.NumberFormat('vi-VN').format(data.discountedTotal) + ' ₫';
                    }
                    
                    // Reload page after 1 second to update cart summary
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    resultEl.innerHTML = '<small class="text-danger"><i class="bi bi-x-circle me-1"></i>' + (data.message || 'Mã không hợp lệ') + '</small>';
                }
            } catch (err) {
                console.error(err);
                resultEl.innerHTML = '<small class="text-danger"><i class="bi bi-x-circle me-1"></i>Có lỗi xảy ra</small>';
            }
        });
    }
    
    // Add to cart from recommended products
    const addButtons = document.querySelectorAll('.btn-add-to-cart');
    
    addButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const masp = this.dataset.masp;
            const originalText = this.innerHTML;
            
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-spinner-border spinner-border-sm me-1"></i>Đang thêm...';
            
            const formData = new FormData();
            formData.append('qty', 1);
            
            fetch('<?= APP_URL ?>/Home/addtocard/' + masp, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Đã thêm!';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-success');
                    
                    // Reload page to update cart
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert('✗ ' + (data.message || 'Có lỗi xảy ra'));
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('✗ Có lỗi xảy ra khi thêm vào giỏ hàng');
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });
});
</script>
