<?php
/**
 * Lịch sử đơn hàng của người dùng.
 * - Liệt kê các đơn với trạng thái, tổng tiền, thông tin nhận hàng.
 * - Link tới chi tiết từng đơn.
 * Trạng thái chuẩn hoá bằng normalize_status_vn để đồng nhất.
 */
?>
<div class="container mt-5">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/views/OrderHistoryView.css" />
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0 fw-bold">
                            <i class="bi bi-clock-history me-2"></i>Lịch sử đơn hàng của bạn
                        </h2>
                        <span class="badge bg-light text-primary fs-6">
                            Tổng: <?= number_format($data['pagination']['totalOrders'] ?? 0, 0, ',', '.') ?> đơn hàng
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($data['orders'])): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light border-0">
                                    <tr class="text-center">
                                        <th class="border-0 py-3 fw-semibold text-muted">Mã hóa đơn</th>
                                        <th class="border-0 py-3 fw-semibold text-muted">Ngày đặt</th>
                                        <th class="border-0 py-3 fw-semibold text-muted">Trạng thái</th>
                                        <th class="border-0 py-3 fw-semibold text-muted">Tổng tiền</th>
                                        <th class="border-0 py-3 fw-semibold text-muted">Người nhận</th>
                                        <th class="border-0 py-3 fw-semibold text-muted">Địa chỉ</th>
                                        <th class="border-0 py-3 fw-semibold text-muted">SĐT</th>
                                        <th class="border-0 py-3 fw-semibold text-muted">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
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
                                    foreach ($data['orders'] as $order): 
                                        $statusValue = $order['status'] ?? '';
                                        $txn = $order['transaction_info'] ?? '';
                                        if ($statusValue === '' && $txn !== '') {
                                            $parts = explode('|', $txn);
                                            $statusValue = trim($parts[0] ?? '');
                                        }
                                        $norm = normalize_status_vn($statusValue);
                                        
                                        // Xác định màu sắc và biểu tượng cho trạng thái
                                        $statusConfig = [
                                            'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Đang xử lý'],
                                            'shipping' => ['class' => 'info', 'icon' => 'truck', 'text' => 'Đang giao'],
                                            'dangvanchuyen' => ['class' => 'info', 'icon' => 'truck', 'text' => 'Đang vận chuyển'],
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
                                        $statusLabel = '<span class="badge bg-' . $config['class'] . ' fs-6">
                                            <i class="bi bi-' . $config['icon'] . ' me-1"></i>' . $config['text'] . '
                                        </span>';
                                        $finalStatuses = ['dathantoan','dathanhtoan','completed','cancelled','huy','dahuy','thatbai','failed'];
                                        $canConfirmReceived = !in_array($norm, $finalStatuses, true);
                                    ?>
                                        <tr class="align-middle">
                                            <td class="text-center">
                                                <span class="fw-bold text-primary fs-6">
                                                    #<?= htmlspecialchars($order['order_code']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                                                    <br>
                                                    <i class="bi bi-clock me-1"></i>
                                                    <?= date('H:i', strtotime($order['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td class="text-center"><?= $statusLabel ?></td>
                                            <td class="text-center">
                                                <span class="fw-bold text-danger fs-6">
                                                    <?= number_format($order['total_amount'], 0, ',', '.') ?> ₫
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">
                                                    <?= htmlspecialchars($order['receiver']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted d-block" style="max-width: 200px;">
                                                    <?= htmlspecialchars($order['address']) ?>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark fs-6">
                                                    <?= htmlspecialchars($order['phone']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?php echo APP_URL; ?>/Home/orderDetail/<?= $order['id'] ?>" 
                                                   class="btn btn-primary btn-sm rounded-pill px-3">
                                                    <i class="bi bi-eye me-1"></i>Xem chi tiết
                                                </a>
                                                <?php if ($canConfirmReceived): ?>
                                                    <form method="post" action="<?= APP_URL ?>/Home/updateOrderStatus" style="display:inline-block;">
                                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                        <input type="hidden" name="new_status" value="dathantoan">
                                                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 mt-1" onclick="return confirm('Xác nhận đã nhận hàng và thanh toán?');">
                                                            <i class="bi bi-check2-circle me-1"></i>Đã nhận hàng
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Phân trang -->
                        <?php if (!empty($data['pagination']) && $data['pagination']['totalPages'] > 1): ?>
                            <nav aria-label="Phân trang đơn hàng" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php
                                    $currentPage = $data['pagination']['currentPage'];
                                    $totalPages = $data['pagination']['totalPages'];
                                    $baseUrl = APP_URL . '/Home/orderHistory';
                                    
                                    // Nút Trước
                                    if ($currentPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= $baseUrl ?>?page=<?= $currentPage - 1 ?>">
                                                <i class="bi bi-chevron-left"></i> Trước
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <span class="page-link"><i class="bi bi-chevron-left"></i> Trước</span>
                                        </li>
                                    <?php endif;
                                    
                                    // Các trang
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($totalPages, $currentPage + 2);
                                    
                                    if ($startPage > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= $baseUrl ?>?page=1">1</a>
                                        </li>
                                        <?php if ($startPage > 2): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif;
                                    endif;
                                    
                                    for ($i = $startPage; $i <= $endPage; $i++): ?>
                                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                            <a class="page-link" href="<?= $baseUrl ?>?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor;
                                    
                                    if ($endPage < $totalPages): ?>
                                        <?php if ($endPage < $totalPages - 1): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= $baseUrl ?>?page=<?= $totalPages ?>"><?= $totalPages ?></a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= $baseUrl ?>?page=<?= $currentPage + 1 ?>">
                                                Sau <i class="bi bi-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">Sau <i class="bi bi-chevron-right"></i></span>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            <div class="text-center text-muted mt-2">
                                <small>
                                    Hiển thị <?= ($currentPage - 1) * $data['pagination']['itemsPerPage'] + 1 ?> - 
                                    <?= min($currentPage * $data['pagination']['itemsPerPage'], $data['pagination']['totalOrders']) ?> 
                                    trong tổng số <?= number_format($data['pagination']['totalOrders']) ?> đơn hàng
                                </small>
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                            </div>
                            <h4 class="text-muted mb-2">Chưa có đơn hàng nào</h4>
                            <p class="text-muted mb-4">Bạn chưa thực hiện bất kỳ đơn hàng nào.</p>
                            <a href="<?= APP_URL ?>/Home" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-shop me-2"></i>Tiếp tục mua sắm
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>