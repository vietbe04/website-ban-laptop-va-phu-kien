<?php
/**
 * Quản lý sản phẩm (Admin).
 * - Thống kê nhanh: tổng sp, loại, đang KM, sắp hết hàng.
 * - Bảng sản phẩm với thao tác: sửa, quản lý biến thể, xoá.
 * - Xuất Excel danh sách hiển thị (yêu cầu thư viện XLSX đã nạp từ layout).
 */
?>
 <div class="container-fluid py-4">
    <div class="admin-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="mb-1 text-primary">
                    <i class="bi bi-box-seam me-2"></i>Quản lý sản phẩm
                </h2>
                <p class="text-muted mb-0">Quản lý danh mục sản phẩm trong hệ thống</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="<?= APP_URL ?>/index.php?url=Product/create" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Thêm sản phẩm mới
                </a>
            </div>
        </div>
    </div>

    <!-- Filter: category -->
    <div class="mb-3">
        <form method="get" action="<?= APP_URL ?>/index.php" class="row g-2 align-items-center">
            <input type="hidden" name="url" value="Product/show">
            <div class="col-auto">
                <label class="form-label">Lọc theo loại</label>
            </div>
            <div class="col-auto">
                <select name="maLoaiSP" class="form-select">
                    <option value="">-- Tất cả loại --</option>
                    <?php if (!empty($data['producttype'])): foreach ($data['producttype'] as $pt): ?>
                        <option value="<?= htmlspecialchars($pt['maLoaiSP']) ?>" <?= (isset($data['currentFilter']) && $data['currentFilter'] == $pt['maLoaiSP']) ? 'selected' : '' ?>><?= htmlspecialchars($pt['maLoaiSP']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label">Tìm theo tên</label>
            </div>
            <div class="col-auto">
                <input type="text" name="q" class="form-control" placeholder="Nhập tên sản phẩm" value="<?= htmlspecialchars($data['currentQuery'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Áp dụng</button>
                <a href="<?= APP_URL ?>/index.php?url=Product/show" class="btn btn-secondary">Xóa</a>
            </div>
        </form>
    </div>

    
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-primary">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stats-content">
                    <h3><?= $data['totalProducts'] ?? count($data['productList'] ?? []) ?></h3>
                    <p>Tổng sản phẩm</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-success">
                    <i class="bi bi-tags"></i>
                </div>
                <div class="stats-content">
                    <h3><?= count(array_unique(array_column($data['productList'] ?? [], 'maLoaiSP'))) ?></h3>
                    <p>Loại sản phẩm</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-warning">
                    <i class="bi bi-percent"></i>
                </div>
                <div class="stats-content">
                    <h3><?= count(array_filter($data['productList'] ?? [], fn($p) => ($p['effective_discount'] ?? 0) > 0)) ?></h3>
                    <p>Đang được giảm</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon bg-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stats-content">
                    <h3><?= count(array_filter($data['productList'] ?? [], fn($p) => $p['soluong'] < 10)) ?></h3>
                    <p>Sắp hết hàng</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="admin-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>Danh sách sản phẩm
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> In
                </button>
                <button class="btn btn-outline-success btn-sm" onclick="exportToExcel()">
                    <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th width="80">Ảnh</th>
                        <th>Mã SP</th>
                        <th>Tên sản phẩm</th>
                        <th>Loại</th>
                        <th width="100">Số lượng</th>
                        <th width="120">Giá nhập</th>
                        <th width="120">Giá xuất</th>
                        <!-- Cột khuyến mãi đã được chuyển sang quản lý riêng (Khuyến mãi). Không hiển thị tại danh sách sản phẩm. -->
                        <th width="150">Mô tả</th>
                        <th width="120">Ngày tạo</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                     <?php
                        if (!empty($data['productList'])) {
                            $i = 1;
                            foreach ($data['productList'] as  $k => $v) {
                        ?>
                        <tr>
                            <td class="text-center fw-semibold"><?= $i++ ?></td>
                            <td>
                                <img src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($v['hinhanh']) ?>" 
                                     class="product-image-sm" alt="<?= htmlspecialchars($v['tensp']) ?>"/>
                            </td>
                            <td>
                                <span class="text-primary fw-semibold"><?= htmlspecialchars($v["masp"]) ?></span>
                            </td>
                            <td>
                                <div class="product-name">
                                    <?= htmlspecialchars($v["tensp"]) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($v["maLoaiSP"]) ?></span>
                            </td>
                            <td class="text-center">
                                <?php 
                                $quantity = (int)($v["soluong"] ?? 0);
                                $quantityClass = $quantity < 10 ? 'text-danger fw-bold' : ($quantity < 50 ? 'text-warning' : 'text-success');
                                ?>
                                <span class="<?= $quantityClass ?>">
                                    <?= number_format($quantity) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="text-muted text-decoration-line-through small">
                                    <?= number_format($v["giaNhap"] ?? 0) ?>
                                </span>
                            </td>
                            <td class="text-end fw-semibold text-primary">
                                <?= number_format($v["giaXuat"] ?? 0) ?>
                            </td>
                            <!-- Cột khuyến mãi không hiển thị tại danh sách sản phẩm theo yêu cầu -->
                            <td>
                                <div class="product-description">
                                    <?= htmlspecialchars(mb_substr($v["mota"] ?? '', 0, 50, 'UTF-8')) ?>
                                    <?= strlen($v["mota"] ?? '') > 50 ? '...' : '' ?>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m/Y', strtotime($v["createDate"] ?? '')) ?>
                                </small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                                <a href="<?= APP_URL ?>/index.php?url=Product/edit/<?= urlencode($v["masp"]) ?>" 
                                       class="btn btn-admin-warning btn-sm" 
                                       title="Chỉnh sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                                <a href="<?= APP_URL ?>/index.php?url=ProductVariant/manage/<?= urlencode($v['masp']) ?>" 
                                       class="btn btn-admin-secondary btn-sm" 
                                       title="Quản lý biến thể">
                                        <i class="bi bi-gear"></i>
                                    </a>
                                    <a href="<?= APP_URL ?>/index.php?url=Product/delete/<?= urlencode($v["masp"]) ?>" 
                                       class="btn btn-admin-danger btn-sm" 
                                       onclick="return confirm('Bạn có chắc muốn xoá sản phẩm này?');" 
                                       title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } 
                        } else {
                        ?>
                        <tr>
                            <td colspan="11" class="text-center">
                                <div class="empty-state">
                                    <i class="bi bi-inbox display-1 text-muted"></i>
                                    <h5 class="text-muted mt-3">Chưa có sản phẩm nào</h5>
                                    <p class="text-muted">Bắt đầu bằng cách thêm sản phẩm mới vào hệ thống</p>
                                    <a href="<?= APP_URL ?>/Product/create" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i> Thêm sản phẩm đầu tiên
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                </table>
            </div>
        </div>
        
        
        <?php if (!empty($data['productList']) && $data['totalPages'] > 1): ?>
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị <?= count($data['productList']) ?> / <?= $data['totalProducts'] ?? 0 ?> sản phẩm
                </div>
                <nav aria-label="Product pagination">
                    <ul class="pagination pagination-sm mb-0">
                        <?php
                        $currentPage = $data['currentPage'] ?? 1;
                        $totalPages = $data['totalPages'] ?? 1;
                        $baseUrl = APP_URL . '/index.php?url=Product/show';
                        $filterParam = isset($data['currentFilter']) && $data['currentFilter'] !== '' ? '&maLoaiSP=' . urlencode($data['currentFilter']) : '';
                        $qParam = isset($data['currentQuery']) && $data['currentQuery'] !== '' ? '&q=' . urlencode($data['currentQuery']) : '';
                        $combinedParams = $filterParam . $qParam;
                        
                        // Nút Trước
                        if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage - 1 ?><?= $combinedParams ?>">Trước</a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Trước</a>
                            </li>
                        <?php endif;
                        
                        // Các trang
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $baseUrl ?>&page=1<?= $combinedParams ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif;
                        endif;
                        
                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?><?= $combinedParams ?>"><?= $i ?></a>
                            </li>
                        <?php endfor;
                        
                        if ($endPage < $totalPages): 
                            if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $totalPages ?><?= $combinedParams ?>"><?= $totalPages ?></a>
                            </li>
                        <?php endif;
                        
                        // Nút Sau
                        if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $currentPage + 1 ?><?= $combinedParams ?>">Sau</a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Sau</a>
                            </li>
                        <?php endif; ?>
                        
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Xuất Excel danh sách hiển thị trong bảng (yêu cầu XLSX có sẵn)
function exportToExcel() {
    const table = document.querySelector('table');
    const rows = table.querySelectorAll('tbody tr');
    const wb = XLSX.utils.book_new();
    const data = [];
    const headers = ['STT','Mã SP','Tên sản phẩm','Loại','Số lượng','Giá nhập','Giá xuất','Mô tả','Ngày tạo'];
    data.push(headers);
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 1) {
            data.push([
                cells[0].textContent.trim(),
                cells[2].textContent.trim(),
                cells[3].textContent.trim(),
                cells[4].textContent.trim(),
                cells[5].textContent.trim(),
                cells[6].textContent.trim(),
                cells[7].textContent.trim(),
                cells[8].textContent.trim() || '',
                cells[9].textContent.trim()
            ]);
        }
    });
    const ws = XLSX.utils.aoa_to_sheet(data);
    XLSX.utils.book_append_sheet(wb, ws, 'Danh sách sản phẩm');
    ws['!cols'] = [{wch:5},{wch:10},{wch:25},{wch:10},{wch:8},{wch:12},{wch:12},{wch:8},{wch:10}];
    XLSX.writeFile(wb, 'DanhSachSanPham_' + new Date().toISOString().slice(0,10) + '.xlsx');
}
</script>