<?php
/**
 * Danh sách sản phẩm (ProductListView).
 * Chức năng:
 *  - Nhận $data['productList'] để hiển thị dạng lưới.
 *  - Bộ lọc giá (slider + số + preset) + từ khoá tìm kiếm.
 *  - Tính tiêu đề động dựa trên bộ lọc.
 *  - Phân trang thân thiện (hiển thị trang gần nhất).
 * Bảo mật: dùng htmlspecialchars cho các trường văn bản.
 */
?>
<script src="<?= APP_URL ?>/public/js/wishlist-compare.js"></script>
<?php $products = $data['productList'] ?? []; ?>

<div class="container py-5 product-list-modern">
    <?php $filtersHeading = $data['searchFilters'] ?? ['q'=>($_GET['q'] ?? ''),'price_min'=>($_GET['price_min'] ?? ''),'price_max'=>($_GET['price_max'] ?? '')];
    $qHeading = trim($filtersHeading['q'] ?? '');
    $priceMinH = trim($filtersHeading['price_min'] ?? '');
    $priceMaxH = trim($filtersHeading['price_max'] ?? '');
    $hasPriceMin = ($priceMinH !== '' && is_numeric($priceMinH));
    $hasPriceMax = ($priceMaxH !== '' && is_numeric($priceMaxH));
    $headingText = 'Tất cả sản phẩm';
    $fmt = function($v){ return number_format((float)$v,0,',','.').' ₫'; };
    if($qHeading !== '' && ($hasPriceMin || $hasPriceMax)) {
        if($hasPriceMin && $hasPriceMax){ $headingText = 'Tìm kiếm "'.htmlspecialchars($qHeading).'" (giá '.$fmt($priceMinH).' - '.$fmt($priceMaxH).')'; }
        elseif($hasPriceMin){ $headingText = 'Tìm kiếm "'.htmlspecialchars($qHeading).'" (giá từ '.$fmt($priceMinH).'+)'; }
        else { $headingText = 'Tìm kiếm "'.htmlspecialchars($qHeading).'" (giá đến '.$fmt($priceMaxH).')'; }
    } elseif($qHeading !== '') { $headingText = 'Tìm kiếm "'.htmlspecialchars($qHeading).'"'; }
    elseif($hasPriceMin || $hasPriceMax){
        if($hasPriceMin && $hasPriceMax){ $headingText = 'Sản phẩm có giá '.$fmt($priceMinH).' - '.$fmt($priceMaxH); }
        elseif($hasPriceMin){ $headingText = 'Sản phẩm giá từ '.$fmt($priceMinH).'+'; }
        else { $headingText = 'Sản phẩm giá đến '.$fmt($priceMaxH); }
    }
    // Nếu đang xem theo danh mục và chưa có tiêu đề tìm kiếm/giá, ghi đè bằng tên danh mục
    if (!empty($data['currentCategoryName']) && $headingText === 'Tất cả sản phẩm') {
        $headingText = 'Danh mục: ' . htmlspecialchars($data['currentCategoryName']);
    }
    ?>
    <div class="page-heading d-flex justify-content-between align-items-center">
        <h1 class="heading-title"><?= $headingText ?></h1>
        <div class="action-buttons">
            <button class="btn-filter" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <i class="bi bi-funnel"></i> Bộ lọc & Sắp xếp
            </button>
            <a href="<?= APP_URL ?>/Home/show" class="btn-reset">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
        </div>
    </div>
    <?php 
        $filters = $data['searchFilters'] ?? ['price_min'=>'','price_max'=>'','q'=>'','color'=>'','capacity'=>'','in_stock'=>'','maLoaiSP'=>'']; 
        $sortBy = $data['sortBy'] ?? 'newest';
        $categories = $data['categories'] ?? [];
        $allColors = $data['allColors'] ?? [];
        $allCapacities = $data['allCapacities'] ?? [];
    ?>
    <?php $maxPrice = 0; if(!empty($products)){ foreach($products as $p){ $val = (int)($p['giaXuat'] ?? 0); if($val > $maxPrice) $maxPrice = $val; } } if($maxPrice < 100000000){ $maxPrice = 100000000; } $currentMin = is_numeric($filters['price_min'])?(int)$filters['price_min']:0; $currentMax = is_numeric($filters['price_max'])?(int)$filters['price_max']:$maxPrice; if($currentMax > $maxPrice){ $currentMax = $maxPrice; } ?>
    <div class="collapse" id="filterCollapse">
        <form class="filter-section advanced-filter" method="get" action="<?= APP_URL ?>/Home/search" id="filterForm" aria-label="Bộ lọc nâng cao">
            <!-- Tìm kiếm từ khóa -->
            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <div class="form-floating">
                        <input type="text" name="q" id="search_q" class="form-control" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($filters['q']) ?>">
                        <label for="search_q"><i class="bi bi-search"></i> Tìm kiếm sản phẩm (tên, mã, mô tả)</label>
                    </div>
                </div>
            </div>
            
            <!-- Bộ lọc -->
            <div class="row g-3 mb-3">
                <!-- Danh mục -->
                <div class="col-md-3">
                    <label class="filter-subtitle" style="color: #000000 !important;"><i class="bi bi-grid-3x3-gap" style="color: #000000 !important;"></i> Danh mục</label>
                    <select name="category" class="form-select" id="category_select">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['maLoaiSP']) ?>" <?= ($filters['maLoaiSP'] === $cat['maLoaiSP']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['tenLoaiSP']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Màu sắc -->
                <div class="col-md-3">
                    <label class="filter-subtitle" style="color: #000000 !important;"><i class="bi bi-palette" style="color: #000000 !important;"></i> Màu sắc</label>
                    <select name="color" class="form-select" id="color_select">
                        <option value="">Tất cả màu</option>
                        <?php foreach ($allColors as $color): ?>
                            <option value="<?= htmlspecialchars($color) ?>" <?= ($filters['color'] === $color) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($color) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Dung lượng/Size -->
                <div class="col-md-3">
                    <label class="filter-subtitle" style="color: #000000 !important;"><i class="bi bi-hdd" style="color: #000000 !important;"></i> Dung lượng/Size</label>
                    <select name="capacity" class="form-select" id="capacity_select">
                        <option value="">Tất cả</option>
                        <?php foreach ($allCapacities as $capacity): ?>
                            <option value="<?= htmlspecialchars($capacity) ?>" <?= ($filters['capacity'] === $capacity) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($capacity) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Tình trạng -->
                <div class="col-md-3">
                    <label class="filter-subtitle" style="color: #000000 !important;"><i class="bi bi-box-seam" style="color: #000000 !important;"></i> Tình trạng</label>
                    <select name="in_stock" class="form-select" id="stock_select">
                        <option value="">Tất cả</option>
                        <option value="1" <?= ($filters['in_stock'] === '1') ? 'selected' : '' ?>>Còn hàng</option>
                    </select>
                </div>
            </div>
            
            <!-- Lọc giá -->
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="filter-title">
                        <i class="bi bi-lightning-fill"></i> Khoảng giá nhanh
                    </label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn preset-btn" data-min="0" data-max="5000000">
                            <i class="bi bi-currency-exchange"></i> Dưới 5M
                        </button>
                        <button type="button" class="btn preset-btn" data-min="5000000" data-max="10000000">
                            <i class="bi bi-currency-exchange"></i> 5M - 10M
                        </button>
                        <button type="button" class="btn preset-btn" data-min="10000000" data-max="20000000">
                            <i class="bi bi-currency-exchange"></i> 10M - 20M
                        </button>
                        <button type="button" class="btn preset-btn" data-min="20000000" data-max="30000000">
                            <i class="bi bi-currency-exchange"></i> 20M - 30M
                        </button>
                        <button type="button" class="btn preset-btn" data-min="30000000" data-max="100000000">
                            <i class="bi bi-currency-exchange"></i> 30M+
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="range_min" class="form-label small text-muted mb-2">
                        <i class="bi bi-arrow-down-circle"></i> Giá tối thiểu
                    </label>
                    <input type="range" id="range_min" min="0" max="<?= $maxPrice ?>" step="500000" value="<?= $currentMin ?>" class="form-range range-slider">
                </div>
                <div class="col-md-6">
                    <label for="range_max" class="form-label small text-muted mb-2">
                        <i class="bi bi-arrow-up-circle"></i> Giá tối đa
                    </label>
                    <input type="range" id="range_max" min="0" max="<?= $maxPrice ?>" step="500000" value="<?= $currentMax ?>" class="form-range range-slider">
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" name="price_min" id="price_min" class="form-control" placeholder="Giá từ" value="<?= $currentMin ?>" min="0">
                        <label for="price_min">Giá từ (₫)</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" name="price_max" id="price_max" class="form-control" placeholder="Giá đến" value="<?= $currentMax ?>" min="0">
                        <label for="price_max">Giá đến (₫)</label>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="price-range-display w-100">
                        <small class="text-secondary d-block mb-2" id="rangeDisplay">
                            <i class="bi bi-tag-fill"></i> Khoảng: <?= number_format($currentMin,0,',','.') ?> ₫ - <?= number_format($currentMax,0,',','.') ?> ₫
                        </small>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-gradient" role="progressbar" 
                                 style="width: <?= $maxPrice > 0 ? ($currentMax / $maxPrice * 100) : 0 ?>%"
                                 aria-valuenow="<?= $currentMax ?>" aria-valuemin="0" aria-valuemax="<?= $maxPrice ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sắp xếp -->
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="filter-subtitle"><i class="bi bi-sort-down"></i> Sắp xếp theo</label>
                    <select name="sort" class="form-select" id="sort_select">
                        <option value="newest" <?= ($sortBy === 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="price_asc" <?= ($sortBy === 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="price_desc" <?= ($sortBy === 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
                        <option value="popularity" <?= ($sortBy === 'popularity') ? 'selected' : '' ?>>Bán chạy nhất</option>
                        <option value="rating" <?= ($sortBy === 'rating') ? 'selected' : '' ?>>Đánh giá cao nhất</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary me-2 px-4" type="submit">
                        <i class="bi bi-funnel-fill"></i> Áp dụng
                    </button>
                    <a href="<?= APP_URL ?>/Home/show" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-state-icon">🔍</div>
                    <h4 class="empty-state-title">Không tìm thấy sản phẩm</h4>
                    <p class="empty-state-text">Rất tiếc! Không có sản phẩm nào phù hợp với tiêu chí lọc của bạn.</p>
                    <div class="empty-state-actions mt-4">
                        <a href="<?= APP_URL ?>/Home/show" class="btn btn-primary">
                            <i class="bi bi-arrow-clockwise"></i> Xem tất cả sản phẩm
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php foreach ($products as $v): 
            $phantram = !empty($v['phantram']) ? (float)$v['phantram'] : 0; 
            $giaGoc = (float)$v['giaXuat']; 
            $giaSauKM = $phantram > 0 ? $giaGoc * (1 - $phantram/100) : $giaGoc; 
        ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card h-100 product-card border-0">
                <div class="product-image-container">
                    <a href="<?= APP_URL ?>/Home/detail/<?= urlencode($v['masp']) ?>" class="d-block">
                        <img src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($v['hinhanh']) ?>" 
                             alt="<?= htmlspecialchars($v['tensp']) ?>" 
                             class="img-fluid" 
                             style="height: 220px; width: 100%; object-fit: contain;">
                    </a>
                    <?php if($phantram > 0): ?>
                        <span class="discount-badge">
                            <i class="bi bi-lightning-fill"></i> -<?= (int)$phantram ?>%
                        </span>
                    <?php endif; ?>
                    
                    <!-- Quick View Overlay -->
                    <div class="product-overlay">
                        <a href="<?= APP_URL ?>/Home/detail/<?= urlencode($v['masp']) ?>" class="quick-view">
                            <i class="bi bi-eye-fill"></i> Xem chi tiết
                        </a>
                    </div>
                    
                    <!-- Wishlist & Compare Buttons -->
                    <div class="product-actions-top" style="position: absolute; top: 10px; right: 10px; z-index: 10; display: flex; gap: 5px;">
                        <button onclick="toggleWishlist('<?= htmlspecialchars($v['masp']) ?>', this)" 
                                class="btn btn-sm btn-light rounded-circle" 
                                style="width: 35px; height: 35px; padding: 0;"
                                title="Thêm vào yêu thích">
                            <i class="bi bi-heart"></i>
                        </button>
                        <button onclick="toggleCompare('<?= htmlspecialchars($v['masp']) ?>', this)" 
                                class="btn btn-sm btn-light rounded-circle" 
                                style="width: 35px; height: 35px; padding: 0;"
                                title="So sánh">
                            <i class="bi bi-arrow-left-right"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h6 class="product-title" title="<?= htmlspecialchars($v['tensp']) ?>">
                        <?= htmlspecialchars($v['tensp']) ?>
                    </h6>
                    
                    <!-- Đánh giá sao và lượt bán -->
                    <div class="product-stats mb-2">
                        <?php 
                            $avgRating = $v['avg_rating'] ?? 0;
                            $ratingCount = $v['rating_count'] ?? 0;
                            $soldCount = $v['sold_count'] ?? 0;
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
                        <i class="bi bi-box-seam"></i> Còn: <?= (int)$v['soluong'] ?> sản phẩm
                    </div>
                    
                    <form method="post" action="<?= APP_URL ?>/Home/addtocard/<?= urlencode($v['masp']) ?>" class="mt-auto">
                        <div class="quantity-input-group">
                            <label for="qty_<?= htmlspecialchars($v['masp']) ?>" class="mb-0">
                                <i class="bi bi-calculator"></i> Số lượng:
                            </label>
                            <input id="qty_<?= htmlspecialchars($v['masp']) ?>" 
                                   type="number" 
                                   name="qty" 
                                   class="form-control form-control-sm" 
                                   value="1" 
                                   min="1" 
                                   max="<?= (int)$v['soluong'] ?>" 
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
    
    <!-- Phân trang -->
    <?php if (isset($data['pagination']) && $data['pagination']['totalPages'] > 1): ?>
    <?php 
        $pagination = $data['pagination'];
        $currentPage = $pagination['currentPage'];
        $totalPages = $pagination['totalPages'];
        
        // Xây dựng URL với các tham số hiện tại
        $currentUrl = $_SERVER['REQUEST_URI'];
        $parsedUrl = parse_url($currentUrl);
        $baseUrl = $parsedUrl['path'];
        
        // Parse query string hiện tại
        parse_str($parsedUrl['query'] ?? '', $queryParams);
        // Loại bỏ tham số page để thêm lại sau
        unset($queryParams['page']);
    ?>
    <nav aria-label="Phân trang sản phẩm" class="pagination-modern">
        <ul class="pagination justify-content-center">
            <!-- Nút trước -->
            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                <?php 
                    $queryParams['page'] = $currentPage - 1;
                    $prevUrl = $baseUrl . '?' . http_build_query($queryParams);
                ?>
                <a class="page-link" href="<?= $currentPage <= 1 ? '#' : $prevUrl ?>" aria-label="Trang trước">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
            
            <!-- Hiển thị 5 trang gần nhất -->
            <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                
                // Điều chỉnh nếu ở đầu hoặc cuối
                if ($currentPage <= 3) {
                    $endPage = min(5, $totalPages);
                } elseif ($currentPage >= $totalPages - 2) {
                    $startPage = max(1, $totalPages - 4);
                }
                
                for ($i = $startPage; $i <= $endPage; $i++):
                    $queryParams['page'] = $i;
                    $pageUrl = $baseUrl . '?' . http_build_query($queryParams);
            ?>
                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $pageUrl ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            
            <!-- Nút sau -->
            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                <?php 
                    $queryParams['page'] = $currentPage + 1;
                    $nextUrl = $baseUrl . '?' . http_build_query($queryParams);
                ?>
                <a class="page-link" href="<?= $currentPage >= $totalPages ? '#' : $nextUrl ?>" aria-label="Trang sau">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
        
        <!-- Thông tin phân trang -->
        <div class="pagination-info">
            Hiển thị <?= (($currentPage - 1) * $pagination['limit']) + 1 ?> - <?= min($currentPage * $pagination['limit'], $pagination['totalProducts']) ?> 
            trong <?= number_format($pagination['totalProducts']) ?> sản phẩm
        </div>
    </nav>
    <?php endif; ?>
</div>
<script>
// Đồng bộ slider và ô nhập giá
const rangeMin=document.getElementById('range_min');const rangeMax=document.getElementById('range_max');const priceMin=document.getElementById('price_min');const priceMax=document.getElementById('price_max');const rangeDisplay=document.getElementById('rangeDisplay');function fmt(v){return (v||0).toLocaleString('vi-VN');}
function syncRange(){if(!rangeMin||!rangeMax)return;let minV=parseInt(rangeMin.value,10);let maxV=parseInt(rangeMax.value,10);if(minV>maxV){[minV,maxV]=[maxV,minV];rangeMin.value=minV;rangeMax.value=maxV;}priceMin.value=minV;priceMax.value=maxV;if(rangeDisplay)rangeDisplay.textContent=`Khoảng: ${fmt(minV)} ₫ - ${fmt(maxV)} ₫`;}
function syncInput(){if(!priceMin||!priceMax)return;let minV=parseInt(priceMin.value,10)||0;let maxV=parseInt(priceMax.value,10)||0;const maxAttr=parseInt(rangeMax.getAttribute('max'),10)||0;if(minV<0)minV=0;if(maxV<minV)maxV=minV;if(maxV>maxAttr)maxV=maxAttr;rangeMin.value=minV;rangeMax.value=maxV;if(rangeDisplay)rangeDisplay.textContent=`Khoảng: ${fmt(minV)} ₫ - ${fmt(maxV)} ₫`;}
if(rangeMin)rangeMin.addEventListener('input',syncRange);if(rangeMax)rangeMax.addEventListener('input',syncRange);if(priceMin)priceMin.addEventListener('change',syncInput);if(priceMax)priceMax.addEventListener('change',syncInput);
document.querySelectorAll('.preset-btn').forEach(b=>b.addEventListener('click',()=>{const minV=parseInt(b.getAttribute('data-min'),10)||0;const maxV=parseInt(b.getAttribute('data-max'),10)||0;rangeMin.value=minV;rangeMax.value=maxV;priceMin.value=minV;priceMax.value=maxV;syncRange();}));
</script>