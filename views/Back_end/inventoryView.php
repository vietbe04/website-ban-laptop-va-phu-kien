<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary mb-0">Quản lý tồn kho</h3>
        <a href="<?= APP_URL ?>/Product/" class="btn btn-secondary">Quản lý sản phẩm</a>
    </div>

    <!-- Filters -->
    <div class="mb-3">
        <form method="get" action="<?= APP_URL ?>/index.php" class="row g-2 align-items-center">
            <input type="hidden" name="url" value="Inventory/show">
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
                <a href="<?= APP_URL ?>/index.php?url=Inventory/show" class="btn btn-secondary">Xóa</a>
            </div>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Danh sách tồn kho</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Ảnh</th>
                            <th>Mã SP</th>
                            <th>Tên SP</th>
                            <th>Số lượng hiện tại</th>
                            <th>Điều chỉnh</th>
                        </tr>
                    </thead>
                    <?php if (!empty($data['productList'])) { $i = 1; foreach ($data['productList'] as $v) { ?>
                    <tr>
                        <td><?= $i + ($data['offset'] ?? 0) ?></td>
                        <td>
                            <img src="<?php echo APP_URL;?>/public/images/<?= htmlspecialchars($v['hinhanh']) ?>" style="height:6rem;"/>
                        </td>
                        <td><?= htmlspecialchars($v['masp']) ?></td>
                        <td class="text-start"><?= htmlspecialchars($v['tensp']) ?></td>
                        <td>
                            <?php $qty = intval($v['soluong']);
                                if ($qty <= 5) {
                                    echo '<span class="badge bg-danger">' . $qty . ' (ít)</span>';
                                } else {
                                    echo '<span class="badge bg-success">' . $qty . '</span>';
                                }
                            ?>
                        </td>
                        <td>
                            <form method="post" action="<?= APP_URL ?>/Inventory/updateStock/<?= htmlspecialchars($v['masp']) ?>" class="d-flex justify-content-center">
                                <input type="number" name="soluong" class="form-control form-control-sm me-2" style="width:6rem;" value="<?= htmlspecialchars($v['soluong']) ?>" min="0" />
                                <button class="btn btn-primary btn-sm">Cập nhật</button>
                            </form>
                        </td>
                    </tr>
                    <?php } } else { ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Không có sản phẩm.</td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        
        <!-- Export Buttons -->
        <div class="card-footer bg-light text-center">
            <button type="button" class="btn btn-success" onclick="exportToExcel()">
                <i class="bi bi-file-earmark-excel"></i> Xuất Excel
            </button>
        </div>
        
                <!-- Pagination -->
                <?php if(($data['totalPages'] ?? 1) > 1): ?>
                <div class="card-footer bg-light">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mb-0">
                                <?php
                                $currentPage = $data['currentPage'] ?? 1;
                                $totalPages = $data['totalPages'] ?? 1;
                                $filterParam = isset($data['currentFilter']) && $data['currentFilter'] !== '' ? '&maLoaiSP=' . urlencode($data['currentFilter']) : '';
                                $qParam = isset($data['currentQuery']) && $data['currentQuery'] !== '' ? '&q=' . urlencode($data['currentQuery']) : '';
                                $combined = $filterParam . $qParam;
                                ?>
                                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= APP_URL ?>/index.php?url=Inventory/show&page=<?= max(1, $currentPage - 1) ?><?= $combined ?>">Trước</a>
                                </li>
                
                                <?php
                                $start = max(1, $currentPage - 2);
                                $end = min($totalPages, $currentPage + 2);
                                for($i = $start; $i <= $end; $i++): 
                                ?>
                                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= APP_URL ?>/index.php?url=Inventory/show&page=<?= $i ?><?= $combined ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                
                                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= APP_URL ?>/index.php?url=Inventory/show&page=<?= min($totalPages, $currentPage + 1) ?><?= $combined ?>">Sau</a>
                                </li>
                            </ul>
                            <div class="text-center text-muted mt-2">Trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng: <?= $data['total'] ?? 0 ?> sản phẩm)</div>
                        </nav>
                </div>
                <?php endif; ?>
        
    </div>
</div>

<script>
function exportToExcel() {
    const table = document.querySelector('table');
    const rows = table.querySelectorAll('tbody tr');
    
    if (rows.length === 0) {
        alert('Không có dữ liệu để xuất');
        return;
    }
    
    const data = [];
    const headers = ['STT', 'Mã SP', 'Tên SP', 'Số lượng hiện tại'];
    data.push(headers);
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 5) {
            const stt = cells[0].textContent.trim();
            const masp = cells[2].textContent.trim();
            const tensp = cells[3].textContent.trim();
            const soluong = cells[4].textContent.trim().replace(/\D/g, '');
            data.push([stt, masp, tensp, soluong]);
        }
    });
    
    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'TonKho');
    
    // Set column widths
    ws['!cols'] = [
        { wch: 8 },   // STT
        { wch: 15 },  // Mã SP
        { wch: 40 },  // Tên SP
        { wch: 12 }   // Số lượng
    ];
    
    // Format quantity column as number
    const range = XLSX.utils.decode_range(ws['!ref']);
    for (let row = 1; row <= range.e.r; row++) {
        const cell = ws[XLSX.utils.encode_cell({ r: row, c: 3 })];
        if (cell && cell.v) {
            cell.v = parseInt(cell.v) || 0;
            cell.t = 'n';
        }
    }
    
    const date = new Date().toLocaleDateString('vi-VN').replace(/\//g, '-');
    XLSX.writeFile(wb, `BaoCaoTonKho_${date}.xlsx`);
}
</script>