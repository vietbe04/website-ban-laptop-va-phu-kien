<?php
/**
 * Trang chi tiết sản phẩm (DetailView) - Thiết kế hiện đại
 * Dữ liệu chính: $data['product'] cùng các biến thể màu sắc & dung lượng.
 * Hiển thị: thông tin, giá (có/không khuyến mãi), biến thể, mô tả, đánh giá.
 * Bảo mật: tất cả chuỗi từ DB/ người dùng được htmlspecialchars khi đưa ra HTML.
 */
?>
<!-- Detail page styles moved to /public/css/detail.css -->
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/detail.css?v=<?= @filemtime(dirname(__DIR__, 2) . '/public/css/detail.css') ?>">
<!-- Wishlist & Compare Script -->
<script src="<?= APP_URL ?>/public/js/wishlist-compare.js"></script>
<div class="container mt-4">    
    <?php
    $p = $data['product'];
    if (!empty($p)) :
        $coKhuyenMai = !empty($p['phantram']) && $p['phantram'] > 0;
        $giaGoc = (float)$p['giaXuat'];
        $giaSauKM = $coKhuyenMai ? $giaGoc * (1 - $p['phantram'] / 100) : $giaGoc;
    ?>

        <!-- 🕒 ĐỒNG HỒ ĐẾM NGƯỢC (chỉ hiện khi có khuyến mãi) -->
        <?php if ($coKhuyenMai): ?>
            <div id="countdown-box" class="countdown-banner mb-4">
                <div class="countdown-content">
                    <div class="countdown-icon">🔥</div>
                    <div class="countdown-text">
                        <strong>Khuyến mãi kết thúc sau:</strong>
                        <span id="countdown" class="countdown-timer"></span>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const endTime = new Date("<?= date('Y-m-d H:i:s', strtotime($p['ngayketthuc'])) ?>").getTime();
                    const countdownEl = document.getElementById('countdown');
                    const box = document.getElementById('countdown-box');
                    const promoDateText = document.getElementById('promo-date');

                    if (!countdownEl || !box) return;

                    function updateCountdown() {
                        const now = new Date().getTime();
                        const distance = endTime - now;

                        if (distance <= 0) {
                            box.style.display = "none";
                            if (promoDateText) promoDateText.style.display = "none";
                            clearInterval(timer);
                            return;
                        }

                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        countdownEl.innerHTML = `
                            <span class="time-unit">${days}<small>d</small></span>
                            <span class="time-unit">${hours}<small>h</small></span>
                            <span class="time-unit">${minutes}<small>m</small></span>
                            <span class="time-unit">${seconds}<small>s</small></span>
                        `;
                    }

                    updateCountdown();
                    const timer = setInterval(updateCountdown, 1000);
                });
            </script>
        <?php endif; ?>

        <div class="row">
            <!-- Hình ảnh sản phẩm + Gallery -->
            <div class="col-lg-6 mb-4">
                <?php
                    // Lấy danh sách ảnh phụ
                    $images = $data['images'] ?? null;
                    if ($images === null) {
                        $modelPath = dirname(__DIR__, 2) . '/models/ProductImageModel.php';
                        if (file_exists($modelPath)) {
                            require_once $modelPath;
                            if (class_exists('ProductImageModel')) {
                                $imgModel = new ProductImageModel();
                                $images = $imgModel->listByProduct($p['masp']);
                            }
                        }
                    }
                    // Đặt ảnh chính: ưu tiên ảnh có is_main, nếu không dùng $p['hinhanh']
                    $mainImage = $p['hinhanh'];
                    if (!empty($images)) {
                        foreach ($images as $im) { if ((int)$im['is_main'] === 1) { $mainImage = $im['filename']; break; } }
                    }
                    // Tập thumbnail: thêm cả ảnh bìa bên ngoài vào đầu danh sách
                    $thumbs = [];
                    $thumbs[] = [ 'filename' => $p['hinhanh'], 'is_main' => ($mainImage === $p['hinhanh']) ? 1 : 0 ];
                    if (!empty($images)) {
                        foreach ($images as $im) {
                            // Tránh trùng tên file với ảnh bìa
                            if ($im['filename'] === $p['hinhanh']) continue;
                            $thumbs[] = [ 'filename' => $im['filename'], 'is_main' => (int)$im['is_main'] ];
                        }
                    }
                ?>
                <div class="product-image-container">
                    <img id="product-main-image"
                         src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($mainImage) ?>"
                         alt="<?= htmlspecialchars($p['tensp']) ?>"
                         class="product-main-image">
                    <div class="product-image-overlay">
                        <button type="button" class="btn btn-light btn-sm" id="open-lightbox">
                            <i class="bi bi-zoom-in"></i> Xem ảnh lớn
                        </button>
                    </div>
                </div>
                <!-- Lightbox Overlay -->
                <div id="image-lightbox" class="image-lightbox" aria-hidden="true">
                    <button type="button" class="lightbox-close" id="lightbox-close" aria-label="Đóng">&times;</button>
                    <div class="lightbox-content">
                        <img id="lightbox-img" src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($p['tensp']) ?>">
                        <div class="lightbox-tools">
                            <button type="button" class="lightbox-zoom-btn" data-action="in" aria-label="Phóng to">+</button>
                            <button type="button" class="lightbox-zoom-btn" data-action="out" aria-label="Thu nhỏ">-</button>
                            <button type="button" class="lightbox-zoom-btn" data-action="reset" aria-label="Reset">↺</button>
                        </div>
                    </div>
                </div>

                <?php if (!empty($thumbs) && count($thumbs) > 1): ?>
                <div class="product-thumbnails mt-3">
                    <?php foreach ($thumbs as $im): ?>
                        <img class="thumb-item <?= (int)$im['is_main'] === 1 ? 'active' : '' ?>"
                             src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($im['filename']) ?>"
                             alt="Thumb"
                             data-src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($im['filename']) ?>">
                    <?php endforeach; ?>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const mainImg = document.getElementById('product-main-image');
                        const thumbs = document.querySelectorAll('.thumb-item');
                        const lightboxImg = document.getElementById('lightbox-img');
                        thumbs.forEach(t => {
                            t.addEventListener('click', function() {
                                thumbs.forEach(x => x.classList.remove('active'));
                                this.classList.add('active');
                                const src = this.getAttribute('data-src');
                                if (src) { 
                                    mainImg.src = src; 
                                    lightboxImg.src = src; 
                                }
                            });
                        });
                        // Lightbox handlers
                        const openBtn = document.getElementById('open-lightbox');
                        const lightbox = document.getElementById('image-lightbox');
                        const closeBtn = document.getElementById('lightbox-close');
                        const zoomButtons = document.querySelectorAll('.lightbox-zoom-btn');
                        let scale = 1;
                        function applyScale(){
                            lightboxImg.style.transform = 'scale(' + scale + ')';
                        }
                        function openLightbox(){
                            lightbox.setAttribute('aria-hidden','false');
                            lightbox.classList.add('active');
                            lightboxImg.src = mainImg.src;
                            scale = 1; applyScale();
                        }
                        function closeLightbox(){
                            lightbox.setAttribute('aria-hidden','true');
                            lightbox.classList.remove('active');
                        }
                        openBtn.addEventListener('click', openLightbox);
                        mainImg.addEventListener('click', openLightbox);
                        closeBtn.addEventListener('click', closeLightbox);
                        lightbox.addEventListener('click', (e)=>{
                            if(e.target === lightbox) closeLightbox();
                        });
                        zoomButtons.forEach(btn=>{
                            btn.addEventListener('click',()=>{
                                const action = btn.getAttribute('data-action');
                                if(action === 'in'){ scale = Math.min(scale + 0.2, 5); }
                                else if(action === 'out'){ scale = Math.max(scale - 0.2, 0.2); }
                                else if(action === 'reset'){ scale = 1; }
                                applyScale();
                            });
                        });
                        // Wheel zoom
                        lightboxImg.addEventListener('wheel', (e)=>{
                            e.preventDefault();
                            const delta = e.deltaY;
                            scale += (delta < 0 ? 0.1 : -0.1);
                            scale = Math.min(Math.max(scale, 0.2), 5);
                            applyScale();
                        }, { passive: false });
                    });
                </script>
                <?php endif; ?>
            </div>

            <!-- Thông tin sản phẩm -->
            <div class="col-lg-6">
                <div class="product-info-card">
                    <div class="product-header">
                        <h1 class="product-title"><?= htmlspecialchars($p['tensp']) ?></h1>
                        <div class="product-meta">
                            <span class="product-sku">Mã: <?= htmlspecialchars($p['masp']) ?></span>
                            <span class="product-category"><?= htmlspecialchars($p['maLoaiSP']) ?></span>
                        </div>
                    </div>

                    <!-- Đánh giá sao -->
                    <?php 
                        $avg = $data['avgRating'] ?? ['avg'=>0,'count'=>0];
                        $stars = function($v){
                            $v = (float)$v; $full = floor($v); $half = ($v - $full) >= 0.5 ? 1 : 0;
                            $out = '';
                            for ($i = 0; $i < $full; $i++) $out .= '★';
                            if ($half) $out .= '☆';
                            while (strlen($out) < 5) $out .= '☆';
                            return $out;
                        };
                    ?>
                    <div class="product-rating">
                        <div class="rating-stars">
                            <span class="stars"><?= $stars($avg['avg'] ?? 0) ?></span>
                            <span class="rating-text"><?= number_format((float)($avg['avg'] ?? 0),1) ?>/5</span>
                        </div>
                        <span class="rating-count">(<?= (int)($avg['count'] ?? 0) ?> đánh giá)</span>
                    </div>

                    <!-- Giá sản phẩm -->
                    <div class="product-price-section">
                        <?php if ($coKhuyenMai): ?>
                            <div class="price-wrapper">
                                <div class="original-price">
                                    <span class="price-label">Giá gốc:</span>
                                    <span class="price-value" id="base-price-value" data-base-price="<?= (int)$giaGoc ?>">
                                        <?= number_format($giaGoc, 0, ',', '.') ?>₫
                                    </span>
                                </div>
                                <div class="sale-price">
                                    <span class="price-label">Giá khuyến mãi:</span>
                                    <span class="price-value text-danger" id="sale-price-value" data-sale-price="<?= (int)$giaSauKM ?>">
                                        <?= number_format($giaSauKM, 0, ',', '.') ?>₫
                                    </span>
                                </div>
                                <div class="discount-badge">
                                    <span class="badge-discount">-<?= htmlspecialchars($p['phantram']) ?>%</span>
                                    <small id="promo-date" class="promo-date">
                                        Áp dụng: <?= date('d/m/Y', strtotime($p['ngaybatdau'])) ?> - <?= date('d/m/Y', strtotime($p['ngayketthuc'])) ?>
                                    </small>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="price-wrapper">
                                <div class="regular-price">
                                    <span class="price-label">Giá bán:</span>
                                    <span class="price-value text-danger" id="regular-price-value" data-base-price="<?= (int)$giaGoc ?>">
                                        <?= number_format($giaGoc, 0, ',', '.') ?>₫
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tình trạng kho -->
                    <div class="product-stock">
                        <span class="stock-status in-stock">
                            <i class="bi bi-check-circle"></i> Còn hàng (<?= (int)$p['soluong'] ?> sản phẩm)
                        </span>
                    </div>

                    <!-- Chọn biến thể -->
                    <?php 
                        $colorVariants = $data['colorVariants'] ?? [];
                        $capacityVariants = $data['capacityVariants'] ?? [];
                    ?>
                    <?php if (!empty($colorVariants) || !empty($capacityVariants)): ?>
                    <div class="product-variants">
                        <h5 class="variants-title">Chọn biến thể</h5>
                        
                        <?php if (!empty($colorVariants)): ?>
                            <div class="variant-group">
                                <label class="variant-label">Màu sắc:</label>
                                <div class="color-variants" id="color-choices">
                                    <?php foreach ($colorVariants as $cv): ?>
                                        <button type="button" class="color-variant-btn" 
                                                data-variant-id="<?= $cv['id'] ?>" 
                                                data-variant-name="<?= htmlspecialchars($cv['name']) ?>">
                                            <?= htmlspecialchars($cv['name']) ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($capacityVariants)): ?>
                            <div class="variant-group">
                                <label class="variant-label">Dung lượng:</label>
                                <select class="capacity-select" id="capacity-select">
                                    <option value="" data-price="">-- Chọn dung lượng --</option>
                                    <?php foreach ($capacityVariants as $cap): 
                                        $vPrice = (float)$cap['price_per_kg'];
                                        $discountPercent = $coKhuyenMai ? (float)$p['phantram'] : 0;
                                        $priceAfterDiscount = $discountPercent > 0 ? $vPrice * (1 - $discountPercent/100) : $vPrice;
                                    ?>
                                        <option value="<?= $cap['id'] ?>" 
                                                data-price="<?= (int)$vPrice ?>" 
                                                data-sale="<?= (int)$priceAfterDiscount ?>" 
                                                data-name="<?= htmlspecialchars($cap['name']) ?>">
                                            <?= htmlspecialchars($cap['name']) ?> - <?= number_format($vPrice,0,',','.') ?>₫
                                            <?php if($discountPercent>0): ?>
                                                <span class="text-danger">(<?= number_format($priceAfterDiscount,0,',','.') ?>₫)</span>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        
                        <div class="current-selection" id="current-selection">
                            <i class="bi bi-info-circle"></i> Chưa chọn biến thể
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Form thêm vào giỏ hàng -->
                    <form class="add-to-cart-form" method="post" action="<?= APP_URL ?>/Home/addtocard/<?= urlencode($p['masp']) ?>" id="add-to-cart-form">
                        <!-- Hidden inputs for variants -->
                        <input type="hidden" name="variant_id" id="variant_id" />
                        <input type="hidden" name="variant_name" id="variant_name" />
                        <input type="hidden" name="variant_type" id="variant_type" />
                        <input type="hidden" name="variant_price_override" id="variant_price_override" />
                        <input type="hidden" name="color_variant_id" id="color_variant_id" />
                        <input type="hidden" name="color_variant_name" id="color_variant_name" />
                        <input type="hidden" name="capacity_variant_id" id="capacity_variant_id" />
                        <input type="hidden" name="capacity_variant_name" id="capacity_variant_name" />
                        <input type="hidden" name="capacity_variant_price" id="capacity_variant_price" />
                        
                        <div class="quantity-section">
                            <label class="quantity-label">Số lượng:</label>
                            <div class="quantity-control">
                                <button type="button" class="quantity-btn" onclick="updateQuantity(-1)">-</button>
                                <input type="number" name="qty" id="qty" value="1" min="1" max="<?= (int)$p['soluong'] ?>" required>
                                <button type="button" class="quantity-btn" onclick="updateQuantity(1)">+</button>
                            </div>
                            <small class="stock-info">Tồn kho: <?= (int)$p['soluong'] ?></small>
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" class="btn-add-cart">
                                <i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng
                            </button>
                            <button type="button" onclick="toggleWishlist('<?= htmlspecialchars($p['masp']) ?>', this)" class="btn-wishlist" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 15px 25px; border-radius: 12px; font-weight: 600; transition: all 0.3s ease;" title="Thêm vào yêu thích">
                                <i class="bi bi-heart"></i> Yêu thích
                            </button>
                            <button type="button" onclick="toggleCompare('<?= htmlspecialchars($p['masp']) ?>', this)" class="btn-compare" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; padding: 15px 25px; border-radius: 12px; font-weight: 600; transition: all 0.3s ease;" title="So sánh">
                                <i class="bi bi-arrow-left-right"></i> So sánh
                            </button>
                            <a href="<?= APP_URL ?>/Home" class="btn-back">
                                <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mô tả sản phẩm -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="product-description-card">
                    <h3 class="description-title">Mô tả sản phẩm</h3>
                    <div class="description-content">
                        <?= nl2br(htmlspecialchars($p['mota'])) ?>
                    </div>
                    <div class="product-meta-info">
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> Ngày đăng: <?= htmlspecialchars($p['createDate']) ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đánh giá sản phẩm -->
        <?php 
            $reviews = $data['reviews'] ?? [];
            $canReview = $data['canReview'] ?? false;
            $alreadyReviewed = $data['alreadyReviewed'] ?? false;
        ?>
        <div class="row mt-5">
            <div class="col-12">
                <div class="reviews-section">
                    <h3 class="reviews-title">
                        <i class="bi bi-star-fill"></i> Đánh giá sản phẩm
                    </h3>
                    
                    <div class="rating-summary">
                        <div class="rating-overview">
                            <div class="rating-score">
                                <span class="score-number"><?= number_format((float)($avg['avg'] ?? 0),1) ?></span>
                                <span class="score-max">/5</span>
                            </div>
                            <div class="rating-stars-large">
                                <?= $stars($avg['avg'] ?? 0) ?>
                            </div>
                            <div class="rating-count-text">
                                Dựa trên <?= (int)($avg['count'] ?? 0) ?> đánh giá
                            </div>
                        </div>
                    </div>

                    <?php if($canReview): ?>
                    <div class="review-form-card">
                        <h5 class="form-title">Gửi đánh giá của bạn</h5>
                        <form method="post" action="<?= APP_URL ?>/Review/submit" class="review-form" enctype="multipart/form-data">
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($p['masp']) ?>">
                            <div class="form-group">
                                <label class="form-label">Số sao đánh giá:</label>
                                <div class="star-rating-input">
                                    <?php for ($i=5; $i>=1; $i--): ?>
                                        <label class="star-option">
                                            <input type="radio" name="rating" value="<?= $i ?>" required>
                                            <span class="star-display"><?= str_repeat('★', $i) ?></span>
                                            <span class="star-text"><?= $i ?> sao</span>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nội dung đánh giá:</label>
                                <textarea name="comment" class="form-textarea" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Hình ảnh sản phẩm thực tế (tối đa 5 ảnh):</label>
                                <div class="image-upload-container">
                                    <input type="file" name="review_images[]" id="reviewImages" class="file-input" accept="image/*" multiple>
                                    <label for="reviewImages" class="file-label">
                                        <i class="bi bi-camera"></i>
                                        <span>Chọn ảnh</span>
                                    </label>
                                    <div class="image-preview" id="imagePreview"></div>
                                </div>
                                <small class="form-text text-muted">Bạn có thể tải lên tối đa 5 hình ảnh (JPG, PNG, max 5MB/ảnh)</small>
                            </div>
                            <button type="submit" class="btn-submit-review">
                                <i class="bi bi-send"></i> Gửi đánh giá
                            </button>
                        </form>
                    </div>
                    <?php elseif($alreadyReviewed): ?>
                        <div class="review-status">
                            <div class="status-icon">✅</div>
                            <div class="status-text">
                                <h5>Bạn đã gửi đánh giá cho sản phẩm này</h5>
                                <p>Cảm ơn bạn đã chia sẻ trải nghiệm của mình!</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="review-status">
                            <div class="status-icon">ℹ️</div>
                            <div class="status-text">
                                <h5>Chưa thể đánh giá</h5>
                                <p>Bạn chỉ có thể đánh giá sau khi đã mua và thanh toán thành công cho sản phẩm này.</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Danh sách đánh giá -->
                    <?php if (!empty($reviews)): ?>
                    <div class="reviews-list">
                        <?php foreach ($reviews as $rv): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <?= strtoupper(substr($rv['fullname'], 0, 1)) ?>
                                    </div>
                                    <div class="reviewer-details">
                                        <h6 class="reviewer-name"><?= htmlspecialchars($rv['fullname']) ?></h6>
                                        <div class="review-rating"><?= $stars((int)$rv['rating']) ?></div>
                                    </div>
                                </div>
                                <div class="review-date">
                                    <?= htmlspecialchars(date('d/m/Y', strtotime($rv['created_at']))) ?>
                                </div>
                            </div>
                            <?php if(!empty($rv['comment'])): ?>
                            <div class="review-content">
                                <?= nl2br(htmlspecialchars($rv['comment'])) ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php 
                            // Hiển thị ảnh đánh giá
                            if (!empty($rv['images'])) {
                                $images = json_decode($rv['images'], true);
                                if (is_array($images) && count($images) > 0):
                            ?>
                            <div class="review-images">
                                <?php foreach ($images as $img): ?>
                                    <div class="review-image-item">
                                        <img src="<?= APP_URL ?>/public/images/reviews/<?= htmlspecialchars($img) ?>" 
                                             alt="Review image" 
                                             onclick="openImageModal(this.src)">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php 
                                endif;
                            }
                            ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="no-reviews">
                        <div class="no-reviews-icon">📝</div>
                        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                        <small>Hãy là người đầu tiên đánh giá sản phẩm này!</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- CSS moved to /public/css/detail.css -->

        <!-- JavaScript for quantity control -->
        <script>
        function updateQuantity(change) {
            const qtyInput = document.getElementById('qty');
            const max = parseInt(qtyInput.getAttribute('max'), 10);
            let currentValue = parseInt(qtyInput.value, 10) || 1;
            
            let newValue = currentValue + change;
            if (newValue < 1) newValue = 1;
            if (newValue > max) newValue = max;
            
            qtyInput.value = newValue;
        }

        // Enhanced variant selection script
        (function(){
            const qtyInput = document.getElementById('qty');
            const max = parseInt(qtyInput.getAttribute('max'),10);
            qtyInput.addEventListener('input',()=>{
                let v = parseInt(qtyInput.value,10);
                if(isNaN(v) || v < 1) v = 1;
                if(v > max) v = max;
                qtyInput.value = v;
            });
            
            const capacitySelect = document.getElementById('capacity-select');
            const basePriceLine = document.getElementById('base-price-line');
            const salePriceLine = document.getElementById('sale-price-line');
            const regularPriceLine = document.getElementById('regular-price-line');
            const basePriceValue = document.getElementById('base-price-value');
            const salePriceValue = document.getElementById('sale-price-value');
            const regularPriceValue = document.getElementById('regular-price-value');
            const variantIdInput = document.getElementById('variant_id');
            const variantNameInput = document.getElementById('variant_name');
            const variantTypeInput = document.getElementById('variant_type');
            const variantPriceOverrideInput = document.getElementById('variant_price_override');
            const colorVariantIdInput = document.getElementById('color_variant_id');
            const colorVariantNameInput = document.getElementById('color_variant_name');
            const capacityVariantIdInput = document.getElementById('capacity_variant_id');
            const capacityVariantNameInput = document.getElementById('capacity_variant_name');
            const capacityVariantPriceInput = document.getElementById('capacity_variant_price');
            const currentSel = document.getElementById('current-selection');
            const colorButtons = document.querySelectorAll('.color-variant-btn');
            
            function fmt(n){ return new Intl.NumberFormat('vi-VN').format(n); }
            
            let selectedColorId = null;
            let selectedColorName = null;
            let selectedCapacityId = null;
            let selectedCapacityName = null;
            let selectedCapacityRawPrice = null;
            
            colorButtons.forEach(btn=>{
                btn.addEventListener('click',()=>{
                    colorButtons.forEach(b=>b.classList.remove('active'));
                    btn.classList.add('active');
                    selectedColorId = btn.dataset.variantId;
                    selectedColorName = btn.dataset.variantName;
                    
                    if(!selectedCapacityId){
                        variantIdInput.value = selectedColorId;
                        variantNameInput.value = selectedColorName;
                        variantTypeInput.value = 'color';
                        variantPriceOverrideInput.value = '';
                    }
                    
                    colorVariantIdInput.value = selectedColorId;
                    colorVariantNameInput.value = selectedColorName;
                    
                    let parts = [];
                    if(selectedColorName) parts.push('Màu ' + selectedColorName);
                    if(selectedCapacityName) parts.push(selectedCapacityName);
                    currentSel.innerHTML = '<i class="bi bi-check-circle"></i> Đã chọn: ' + parts.join(' + ');
                });
            });
            
            if(capacitySelect){
                capacitySelect.addEventListener('change',()=>{
                    const opt = capacitySelect.selectedOptions[0];
                    const id = opt.value;
                    if(!id){
                        if(basePriceValue){ basePriceValue.textContent = fmt(basePriceValue.dataset.basePrice); }
                        if(salePriceValue){ salePriceValue.textContent = fmt(salePriceValue.dataset.salePrice); }
                        if(regularPriceValue){ regularPriceValue.textContent = fmt(regularPriceValue.dataset.basePrice); }
                        selectedCapacityId = null;
                        selectedCapacityName = null;
                        selectedCapacityRawPrice = null;
                        capacityVariantIdInput.value='';
                        capacityVariantNameInput.value='';
                        capacityVariantPriceInput.value='';
                        if(!selectedColorId){
                            variantIdInput.value=''; variantNameInput.value=''; variantTypeInput.value=''; variantPriceOverrideInput.value='';
                            currentSel.innerHTML = '<i class="bi bi-info-circle"></i> Chưa chọn biến thể';
                        } else {
                            variantIdInput.value = selectedColorId;
                            variantNameInput.value = selectedColorName;
                            variantTypeInput.value = 'color';
                            variantPriceOverrideInput.value = '';
                            currentSel.innerHTML = '<i class="bi bi-check-circle"></i> Đã chọn: Màu ' + selectedColorName;
                        }
                        return;
                    }
                    const raw = parseInt(opt.dataset.price,10);
                    const sale = parseInt(opt.dataset.sale,10);
                    selectedCapacityId = id;
                    selectedCapacityName = opt.dataset.name;
                    selectedCapacityRawPrice = raw;
                    
                    variantIdInput.value = id;
                    variantNameInput.value = selectedCapacityName;
                    variantTypeInput.value = 'capacity';
                    variantPriceOverrideInput.value = raw;
                    
                    capacityVariantIdInput.value = id;
                    capacityVariantNameInput.value = selectedCapacityName;
                    capacityVariantPriceInput.value = raw;
                    
                    if(basePriceValue){ basePriceValue.textContent = fmt(raw); basePriceValue.dataset.basePrice = raw; }
                    if(salePriceValue){ salePriceValue.textContent = fmt(sale); salePriceValue.dataset.salePrice = sale; }
                    if(regularPriceValue){ regularPriceValue.textContent = fmt(raw); regularPriceValue.dataset.basePrice = raw; }
                    
                    let parts = [];
                    if(selectedColorName) parts.push('Màu ' + selectedColorName);
                    if(selectedCapacityName) parts.push(selectedCapacityName + ' (' + fmt(raw) + '₫)');
                    currentSel.innerHTML = '<i class="bi bi-check-circle"></i> Đã chọn: ' + parts.join(' + ');
                });
            }
        })();
        </script>
        
        <!-- Review Images Styles -->
        <style>
        /* Image Upload Container */
        .image-upload-container {
            margin-top: 10px;
        }
        
        .file-input {
            display: none;
        }
        
        .file-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }
        
        .file-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .file-label i {
            font-size: 18px;
        }
        
        /* Image Preview */
        .image-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 12px;
            margin-top: 15px;
        }
        
        .preview-item {
            position: relative;
            width: 100%;
            padding-bottom: 100%;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .preview-item img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .remove-preview {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 10;
        }
        
        .remove-preview:hover {
            background: #dc3545;
            transform: scale(1.1);
        }
        
        /* Review Images Display */
        .review-images {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        
        .review-image-item {
            position: relative;
            width: 100%;
            padding-bottom: 100%;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .review-image-item:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .review-image-item img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Image Modal */
        .image-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .image-modal.active {
            opacity: 1;
        }
        
        .modal-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(5px);
        }
        
        .image-modal .modal-content {
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
            z-index: 10;
            animation: zoomIn 0.3s ease;
        }
        
        .image-modal .modal-content img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 8px;
        }
        
        .modal-close {
            position: absolute;
            top: -40px;
            right: 0;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #333;
            font-size: 18px;
        }
        
        .modal-close:hover {
            background: white;
            transform: rotate(90deg);
        }
        
        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        @media (max-width: 768px) {
            .image-preview,
            .review-images {
                grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
                gap: 8px;
            }
            
            .modal-close {
                top: 10px;
                right: 10px;
            }
        }
        </style>
        
        <!-- Image Preview & Modal Script -->
        <script>
        // Preview ảnh trước khi upload
        document.getElementById('reviewImages')?.addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const files = e.target.files;
            
            if (!preview) return;
            
            preview.innerHTML = '';
            
            if (files.length > 5) {
                alert('Bạn chỉ có thể tải lên tối đa 5 ảnh');
                e.target.value = '';
                return;
            }
            
            Array.from(files).forEach((file, index) => {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ảnh ' + file.name + ' quá lớn (max 5MB)');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'preview-item';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-preview" onclick="removePreviewImage(this, ${index})">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
        
        function removePreviewImage(btn, index) {
            btn.parentElement.remove();
            const input = document.getElementById('reviewImages');
            if (input) {
                const dt = new DataTransfer();
                const files = input.files;
                for (let i = 0; i < files.length; i++) {
                    if (i !== index) dt.items.add(files[i]);
                }
                input.files = dt.files;
            }
        }
        
        // Modal xem ảnh đánh giá
        function openImageModal(src) {
            const modal = document.createElement('div');
            modal.className = 'image-modal';
            modal.innerHTML = `
                <div class="modal-backdrop" onclick="this.parentElement.remove()"></div>
                <div class="modal-content">
                    <button class="modal-close" onclick="this.closest('.image-modal').remove()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <img src="${src}" alt="Review image">
                </div>
            `;
            document.body.appendChild(modal);
            setTimeout(() => modal.classList.add('active'), 10);
        }
        </script>
                                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-danger">Không tìm thấy sản phẩm!</div>
    <?php endif; ?>
</div>