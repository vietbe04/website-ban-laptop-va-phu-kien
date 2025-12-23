<?php
/**
 * Chi tiết đơn hàng cho người dùng - Phiên bản cải tiến 2024
 * - Hiển thị thông tin đơn: mã, ngày đặt, trạng thái, ưu đãi, người nhận.
 * - Bảng sản phẩm với giá gốc / giá giảm nếu có.
 * - Tính toán các khoản giảm: sản phẩm, mã giảm giá, ngưỡng.
 * - Thêm timeline trạng thái đơn hàng
 * - Giao diện responsive hiện đại
 * Ghi chú: hàm normalize_status_vn chuyển trạng thái có dấu sang dạng so sánh.
 */
?>
<div class="order-detail-container">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/views/OrderDetailView.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <?php if (!empty($data['order'])): 
        if (!function_exists('normalize_status_vn')) {
            function normalize_status_vn($s) {
                $s = trim((string)$s);
                $mapVN = [
                    'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
                    'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
                    'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
                    'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o','ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
                    'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
                    'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y','đ'=>'d',
                    'À'=>'a','Á'=>'a','Ạ'=>'a','Ả'=>'a','Ã'=>'a','Â'=>'a','Ầ'=>'a','Ấ'=>'a','Ậ'=>'a','Ẩ'=>'a','Ẫ'=>'a','Ă'=>'a','Ằ'=>'a','Ắ'=>'a','Ặ'=>'a','Ẳ'=>'a','Ẵ'=>'a',
                    'È'=>'e','É'=>'e','Ẹ'=>'e','Ẻ'=>'e','Ẽ'=>'e','Ê'=>'e','Ề'=>'e','Ế'=>'e','Ệ'=>'e','Ể'=>'e','Ễ'=>'e',
                    'Ì'=>'i','Í'=>'i','Ị'=>'i','Ỉ'=>'i','Ĩ'=>'i',
                    'Ò'=>'o','Ó'=>'o','Ọ'=>'o','Ỏ'=>'o','Õ'=>'o','Ô'=>'o','Ồ'=>'o','Ố'=>'o','Ộ'=>'o','Ổ'=>'o','Ỗ'=>'o','Ơ'=>'o','Ờ'=>'o','Ớ'=>'o','Ợ'=>'o','Ở'=>'o','Ỡ'=>'o',
                    'Ù'=>'u','Ú'=>'u','Ụ'=>'u','Ủ'=>'u','Ũ'=>'u','Ư'=>'u','Ừ'=>'u','Ứ'=>'u','Ự'=>'u','Ử'=>'u','Ữ'=>'u',
                    'Ỳ'=>'y','Ý'=>'y','Ỵ'=>'y','Ỷ'=>'y','Ỹ'=>'y','Đ'=>'d',
                ];
                $s = strtr($s, $mapVN);
                $s = strtolower($s);
                $s = preg_replace('/[\s_\-]+/','', $s);
                return $s;
            }
        }
        $order = $data['order'];
        $orderItems = $data['orderItems'];
        $statusValue = $order['status'] ?? '';
        $txn = $order['transaction_info'] ?? '';
        // Parse shipping method
        $shippingMethod = '';
        $shippingFee = 0;
        if (!empty($txn)) {
            $parts = explode('|', $txn);
            array_shift($parts);
            foreach ($parts as $tok) {
                $tok = trim($tok);
                if (stripos($tok, 'shipping:') === 0) {
                    if (preg_match('/^shipping:(.*?)\(/i', $tok, $m)) {
                        $rawMethod = trim($m[1]);
                        if (stripos($rawMethod, 'nhanh') !== false) {
                            $shippingMethod = 'Giao hàng nhanh';
                            $shippingFee = 50000;
                        } elseif (stripos($rawMethod, 'tiêu chuẩn') !== false) {
                            $shippingMethod = 'Giao hàng tiêu chuẩn';
                            $shippingFee = 30000;
                        } else {
                            $shippingMethod = $rawMethod;
                            $shippingFee = 30000; // default
                        }
                    }
                } elseif (stripos($tok, 'pickup:') === 0) {
                    $shippingMethod = 'Nhận tại cửa hàng';
                    $shippingFee = 0;
                }
            }
        }
        if ($shippingMethod == '') {
            // Fallback based on status
            if ($norm == 'chonhantaicuahang') {
                $shippingMethod = 'Nhận tại cửa hàng';
                $shippingFee = 0;
            } else {
                $shippingMethod = 'Giao hàng tiêu chuẩn';
                $shippingFee = 30000;
            }
        }
        if ($statusValue === '' && $txn !== '') {
            $parts = explode('|', $txn);
            $statusValue = trim($parts[0] ?? '');
        }
        $norm = normalize_status_vn($statusValue);
        
        // Tính toán tổng tiền gốc và sau khuyến mãi theo từng dòng
        $rawSubtotal = 0; $saleSubtotal = 0;
        foreach ($orderItems as $calcItem) {
            $orig = isset($calcItem['price']) ? (float)$calcItem['price'] : 0;
            $sale = isset($calcItem['sale_price']) && $calcItem['sale_price'] !== null ? (float)$calcItem['sale_price'] : $orig;
            $qty = (int)($calcItem['quantity'] ?? 0);
            $rawSubtotal += $orig * $qty;
            $saleSubtotal += $sale * $qty;
        }
        // Parse các ưu đãi từ transaction_info để lấy thêm giảm giá coupon / threshold nếu có
        $couponDiscount = 0; $thresholdDiscount = 0;
        // Tính threshold từ subtotal
        try {
            $tierModel = $this->model('ThresholdDiscountModel');
            $tiers = $tierModel->getActiveTiers();
            $best = ['percent' => 0, 'min' => 0];
            foreach ($tiers as $tier) {
                $minVal = isset($tier['min_total']) ? (int)$tier['min_total'] : (int)$tier['min'];
                $percentVal = (int)$tier['percent'];
                if ($rawSubtotal >= $minVal && $percentVal >= $best['percent']) {
                    $best = ['percent' => $percentVal, 'min' => $minVal];
                }
            }
            if ($best['percent'] > 0) {
                $thresholdDiscount = (int)round($rawSubtotal * $best['percent'] / 100, 0);
            }
        } catch (Exception $e) {
            // Không tính nếu lỗi
        }
        if (!empty($txn)) {
            $partsDiscount = explode('|', $txn);
            array_shift($partsDiscount); // bỏ status
            foreach ($partsDiscount as $tok) {
                $tok = trim($tok);
                if (stripos($tok, 'coupon:') === 0) {
                    if (preg_match('/\(([-+]?\d+)\)/', $tok, $m)) { $couponDiscount += abs((int)$m[1]); }
                } elseif (stripos($tok, 'threshold:') === 0) {
                    // Sử dụng calculated thresholdDiscount
                }
            }
        }
        $productLevelDiscount = max(0, $rawSubtotal - $saleSubtotal); // giảm do khuyến mãi sản phẩm
        $finalTotal = (float)$order['total_amount']; // tổng thanh toán cuối cùng đã lưu DB (sau coupon + threshold)
    ?>
    
    <!-- Order Header Card -->
    <div class="row">
        <div class="col-12">
            <div class="card order-header-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h1 class="order-title">
                                <i class="bi bi-receipt me-3"></i>Chi tiết đơn hàng
                            </h1>
                            <div class="order-meta">
                                <span class="me-4">
                                    <i class="bi bi-hash me-2"></i>
                                    <strong>#<?= htmlspecialchars($order['order_code']) ?></strong>
                                </span>
                                <span>
                                    <i class="bi bi-calendar3 me-2"></i>
                                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <?php
                                $statusConfig = [
                                    'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Đang xử lý'],
                                    'shipping' => ['class' => 'info', 'icon' => 'truck', 'text' => 'Đang giao'],
                                    'completed' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Hoàn thành'],
                                    'cancelled' => ['class' => 'danger', 'icon' => 'x-circle', 'text' => 'Đã hủy'],
                                    'huy' => ['class' => 'danger', 'icon' => 'x-circle', 'text' => 'Hủy'],
                                    'dathantoan' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Đã thanh toán'],
                                    'dathanhtoan' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Đã thanh toán'],
                                    'chothanhtoan' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Chờ thanh toán'],
                                    'chuathanhtoan' => ['class' => 'secondary', 'icon' => 'clock', 'text' => 'Chưa thanh toán'],
                                    'chonhantaicuahang' => ['class' => 'info', 'icon' => 'shop', 'text' => 'Nhận tại cửa hàng'],
                                ];
                                
                                $config = $statusConfig[$norm] ?? ['class' => 'secondary', 'icon' => 'question-circle', 'text' => 'Không xác định'];
                                $statusLabel = '<span class="status-badge bg-' . $config['class'] . ' ' . $norm . '">
                                    <i class="bi bi-' . $config['icon'] . ' me-2"></i>' . $config['text'] . '
                                </span>';
                                echo $statusLabel;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Information Cards -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card info-card h-100">
                <div class="card-header">
                    <i class="bi bi-person-circle me-2"></i>Thông tin người nhận
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-icon bg-primary text-white">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Người nhận</div>
                            <div class="info-value"><?= htmlspecialchars($order['receiver']) ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon bg-success text-white">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Số điện thoại</div>
                            <div class="info-value"><?= htmlspecialchars($order['phone']) ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon bg-info text-white">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Địa chỉ giao hàng</div>
                            <div class="info-value"><?= htmlspecialchars($order['address']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card info-card h-100">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>Thông tin đơn hàng
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-icon bg-warning text-white">
                            <i class="bi bi-hash"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Mã đơn hàng</div>
                            <div class="info-value text-primary fw-bold">#<?= htmlspecialchars($order['order_code']) ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon bg-secondary text-white">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Ngày đặt hàng</div>
                            <div class="info-value"><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon bg-dark text-white">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Giờ đặt hàng</div>
                            <div class="info-value"><?= date('H:i', strtotime($order['created_at'])) ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon bg-<?= $config['class'] ?> text-white">
                            <i class="bi bi-<?= $config['icon'] ?>"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Trạng thái đơn hàng</div>
                            <div class="info-value"><?= $config['text'] ?></div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon bg-info text-white">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Hình thức giao hàng</div>
                            <div class="info-value"><?= htmlspecialchars($shippingMethod) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="row">
        <div class="col-12">
            <div class="card products-card">
                <div class="card-header">
                    <i class="bi bi-box-seam me-2"></i>Danh sách sản phẩm
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table product-table mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="py-3">Hình ảnh</th>
                                    <th class="py-3">Sản phẩm</th>
                                    <th class="py-3">Số lượng</th>
                                    <th class="py-3">Đơn giá</th>
                                    <th class="py-3">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <?php
                                        $img = $item['image'] ?? '';
                                        $base = dirname(__DIR__, 2);
                                        $imgUrl = APP_URL . '/public/images/' . rawurlencode($img);
                                        $path1 = $base . '/public/images/' . $img;
                                        $path2 = $base . '/public/uploads/' . $img;
                                        if (!empty($img)) {
                                            if (file_exists($path1)) {
                                                $imgUrl = APP_URL . '/public/images/' . rawurlencode($img);
                                            } elseif (file_exists($path2)) {
                                                $imgUrl = APP_URL . '/public/uploads/' . rawurlencode($img);
                                            }
                                        }
                                    ?>
                                    <tr class="align-middle">
                                        <td class="text-center" data-label="Hình ảnh">
                                            <img src="<?= $imgUrl ?>" alt="Sản phẩm" class="product-image" onerror="this.src='<?= APP_URL ?>/public/images/placeholder.png'">
                                        </td>
                                        <td data-label="Sản phẩm">
                                            <div class="product-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                            <?php 
                                                $hasCapacity = !empty($item['variant_name']);
                                                $hasColor = !empty($item['color_variant_name']);
                                                if ($hasCapacity) {
                                                    echo '<div class="product-variant"><i class="bi bi-tag me-1"></i>Dung lượng: ' . htmlspecialchars($item['variant_name']) . '</div>';
                                                }
                                                if ($hasColor) {
                                                    echo '<div class="product-variant"><i class="bi bi-palette me-1"></i>Màu sắc: ' . htmlspecialchars($item['color_variant_name']) . '</div>';
                                                }
                                            ?>
                                        </td>
                                        <td class="text-center" data-label="Số lượng">
                                            <span class="badge bg-primary quantity-badge"><?= (int)$item['quantity'] ?></span>
                                        </td>
                                        <td class="text-center" data-label="Đơn giá">
                                            <?php
                                                $original = isset($item['price']) ? (float)$item['price'] : 0;
                                                $sale = isset($item['sale_price']) && $item['sale_price'] !== null ? (float)$item['sale_price'] : $original;
                                                if ($sale < $original) {
                                                    echo '<div class="price-original">' . number_format($original,0,',','.') . ' ₫</div>';
                                                    echo '<div class="price-sale">' . number_format($sale,0,',','.') . ' ₫</div>';
                                                } else {
                                                    echo '<div class="price-normal">' . number_format($original,0,',','.') . ' ₫</div>';
                                                }
                                            ?>
                                        </td>
                                        <td class="text-center" data-label="Thành tiền">
                                            <?php
                                                $lineOriginal = $original * (int)$item['quantity'];
                                                $lineSale = $sale * (int)$item['quantity'];
                                                if ($sale < $original) {
                                                    echo '<div class="price-original">' . number_format($lineOriginal,0,',','.') . ' ₫</div>';
                                                    echo '<div class="price-sale">' . number_format($lineSale,0,',','.') . ' ₫</div>';
                                                } else {
                                                    echo '<div class="price-normal fw-bold">' . number_format($lineOriginal,0,',','.') . ' ₫</div>';
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Tổng gốc:</th>
                                    <th class="text-center"><?= number_format($rawSubtotal, 0, ',', '.') ?> ₫</th>
                                </tr>
                                <?php if ($productLevelDiscount > 0): ?>
                                <tr>
                                    <th colspan="4" class="text-end">Giảm giá sản phẩm:</th>
                                    <th class="text-center text-success">-<?= number_format($productLevelDiscount, 0, ',', '.') ?> ₫</th>
                                </tr>
                                <?php endif; ?>
                                <?php if ($couponDiscount > 0): ?>
                                <tr>
                                    <th colspan="4" class="text-end">Giảm mã giảm giá:</th>
                                    <th class="text-center text-success">-<?= number_format($couponDiscount, 0, ',', '.') ?> ₫</th>
                                </tr>
                                <?php endif; ?>
                                <?php if ($thresholdDiscount > 0): ?>
                                <tr>
                                    <th colspan="4" class="text-end">Giảm theo ngưỡng:</th>
                                    <th class="text-center text-success">-<?= number_format($thresholdDiscount, 0, ',', '.') ?> ₫</th>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th colspan="4" class="text-end">Phí vận chuyển:</th>
                                    <th class="text-center">
                                        <?php if ($shippingMethod === 'Nhận tại cửa hàng'): ?>
                                            Miễn phí
                                        <?php else: ?>
                                            <?= number_format($shippingFee, 0, ',', '.') ?> ₫
                                        <?php endif; ?>
                                    </th>
                                </tr>
                                <tr class="table-secondary">
                                    <th colspan="4" class="text-end">Tổng thanh toán:</th>
                                    <th class="text-center text-danger fw-bold fs-5"><?= number_format($finalTotal, 0, ',', '.') ?> ₫</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($txn)): ?>
        <?php
            $infos = explode('|', $txn);
            array_shift($infos); // remove status token
            $out = [];
            foreach ($infos as $tok) {
                $tok = trim($tok);
                if (stripos($tok, 'coupon:') === 0) {
                    if (preg_match('/^coupon:(\S+)\s*\(([-+]?\d+)\)/i', $tok, $m)) {
                        $code = $m[1];
                        $amt = (int)$m[2];
                        $out[] = 'Mã giảm giá: <strong>' . htmlspecialchars($code) . '</strong> (-' . number_format(abs($amt),0,',','.') . ' ₫)';
                    } else {
                        $out[] = 'Mã giảm giá: ' . htmlspecialchars(substr($tok,7));
                    }
                } elseif (stripos($tok, 'threshold:') === 0) {
                    if (preg_match('/^threshold:(\S+)\s*\(([-+]?\d+)\)/i', $tok, $m)) {
                        $percent = $m[1];
                        $amt = (int)$m[2];
                        $out[] = 'Giảm theo ngưỡng: <strong>' . htmlspecialchars($percent) . '</strong> (-' . number_format(abs($amt),0,',','.') . ' ₫)';
                    } else {
                        $out[] = 'Giảm theo ngưỡng: ' . htmlspecialchars(substr($tok,10)).'%';
                    }
                } elseif (stripos($tok, 'shipping:') === 0 || stripos($tok, 'pickup:') === 0) {
                    $label = stripos($tok, 'shipping:') === 0 ? 'Phí vận chuyển' : 'Nhận tại cửa hàng';
                    if (preg_match('/^([^:]+):(.*?)\(([-+]?\d+)\)/i', $tok, $m)) {
                        $amt = (int)$m[3];
                        $out[] = htmlspecialchars($m[2] ?: $label) . ': ' . ($amt >= 0 ? '+' : '') . number_format($amt,0,',','.') . ' ₫';
                    } elseif (preg_match('/^([^:]+):\(([-+]?\d+)\)/i', $tok, $m)) {
                        $amt = (int)$m[2];
                        $out[] = htmlspecialchars($label) . ': ' . ($amt >= 0 ? '+' : '') . number_format($amt,0,',','.') . ' ₫';
                    }
                }
            }
        ?>
        <?php if (!empty($out)): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card discount-card">
                        <div class="card-header">
                            <i class="bi bi-gift me-2"></i>Ưu đãi đã áp dụng
                        </div>
                        <div class="card-body">
                            <div class="alert discount-alert mb-0">
                                <i class="bi bi-tag-fill me-2"></i>
                                <?= implode(' • ', $out) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="action-buttons text-center mb-4">
                <a href="<?= APP_URL ?>/Home/orderHistory" class="btn btn-outline-secondary btn-lg me-3">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại lịch sử
                </a>
                <a href="<?= APP_URL ?>/Home" class="btn btn-primary btn-lg">
                    <i class="bi bi-shop me-2"></i>Tiếp tục mua sắm
                </a>
            </div>
        </div>
    </div>

    <?php else: ?>
        <!-- Not Found Section -->
        <div class="row">
            <div class="col-12">
                <div class="not-found-card text-center">
                    <div class="not-found-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h3 class="not-found-title">Không tìm thấy đơn hàng</h3>
                    <p class="not-found-text">Đơn hàng bạn tìm kiếm không tồn tại hoặc đã bị xóa.</p>
                    <a href="<?= APP_URL ?>/Home/orderHistory" class="btn btn-primary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Quay lại lịch sử đơn hàng
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>