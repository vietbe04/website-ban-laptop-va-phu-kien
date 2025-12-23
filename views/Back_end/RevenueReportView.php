<?php
/**
 * View: Báo cáo doanh thu (Admin)
 * - Hiển thị bộ lọc theo ngày/tháng/năm, tổng quan doanh thu, bảng dữ liệu và biểu đồ.
 * - Có chức năng xuất Excel (phụ thuộc thư viện XLSX được include từ layout).
 *
 * Dữ liệu đầu vào ($data):
 * - single: array|null, bản ghi doanh thu của khoảng thời gian được chọn (period, revenue)
 * - list: array, danh sách các mốc (period) và doanh thu (revenue)
 * - type: string, "day"|"month"|"year"
 * - date, month, year: giá trị lọc hiện tại
 * - totalAllTime: tổng doanh thu toàn thời gian
 * - error: thông báo lỗi (nếu có)
 */
$single = $data['single'] ?? null;
$list   = $data['list'] ?? [];
$type   = htmlspecialchars($data['type'] ?? 'day');
$error  = $data['error'] ?? null;
?>
<div class="container py-4">
    <h1 class="mb-3">Báo cáo doanh thu</h1>
        <form method="get" class="filter-form row g-2 align-items-end" action="index.php">
        <input type="hidden" name="url" value="Report/Show" />
        <div class="col-md-2">
            <label class="form-label">Loại</label>
            <select name="type" class="form-control">
                <option value="day"   <?= $type==='day'?'selected':''; ?>>Ngày</option>
                <option value="month" <?= $type==='month'?'selected':''; ?>>Tháng</option>
                <option value="year"  <?= $type==='year'?'selected':''; ?>>Năm</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Ngày</label>
            <input type="date" name="date" value="<?= htmlspecialchars($data['date']); ?>" class="form-control" />
        </div>
        <div class="col-md-2">
            <label class="form-label">Năm</label>
            <input type="number" name="year" value="<?= htmlspecialchars($data['year']); ?>" class="form-control" />
        </div>
        <div class="col-md-2">
            <label class="form-label">Tháng</label>
            <input type="number" name="month" min="1" max="12" value="<?= htmlspecialchars($data['month']); ?>" class="form-control" />
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">Xem báo cáo</button>
        </div>
    </form>
    <?php if($error): ?>
        <div class="alert alert-danger mt-3">Lỗi: <?= htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div class="summary-box mt-3 p-3 border rounded bg-light">
        <h4 class="h6">Tổng quan</h4>
        <?php $tot = $data['totalAllTime'] ?? ['revenue'=>0,'profit'=>0]; ?>
        <p class="mb-1">Tổng doanh thu toàn thời gian: <strong><?= number_format($tot['revenue'] ?? 0,0,',','.') ?> VND</strong></p>
        <p class="mb-1">Tổng doanh thu thực tế (sau trừ giá nhập): <strong><?= number_format($tot['profit'] ?? 0,0,',','.') ?> VND</strong></p>
        <?php if($single): ?>
            <p class="mb-0">Doanh thu (<?= htmlspecialchars($single['period']); ?>): <strong><?= number_format($single['revenue'] ?? 0,0,',','.') ?> VND</strong></p>
            <p class="mb-0">Doanh thu thực tế (<?= htmlspecialchars($single['period']); ?>): <strong><?= number_format($single['profit'] ?? 0,0,',','.') ?> VND</strong></p>
        <?php endif; ?>
    </div>
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Khoảng</th>
                    <th>Doanh thu (VND)</th>
                    <th>Doanh thu thực tế (VND)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($list)): ?>
                    <tr><td colspan="3" class="text-center">Không có dữ liệu</td></tr>
                <?php else: foreach($list as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['period']); ?></td>
                        <td><?= number_format($row['revenue'] ?? 0,0,',','.') ?></td>
                        <td><?= number_format($row['profit'] ?? 0,0,',','.') ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        <canvas id="revChart" height="120"></canvas>
    </div>
    
    <div class="mt-3 text-center">
        <button type="button" class="btn btn-success" onclick="exportToExcel()">
            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
        </button>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    (function(){
        const ctx = document.getElementById('revChart');
        const data = {
            labels: <?= json_encode(array_column($list,'period')); ?>,
            datasets: [{
                label: 'Doanh thu',
                data: <?= json_encode(array_map(function($v){return (float)($v['revenue'] ?? 0);}, $list)); ?>,
                backgroundColor: 'rgba(54,162,235,0.25)',
                borderColor: 'rgba(54,162,235,1)',
                borderWidth: 1,
                tension: .2,
                fill: true
            },{
                label: 'Doanh thu thực tế',
                data: <?= json_encode(array_map(function($v){return (float)($v['profit'] ?? 0);}, $list)); ?>,
                backgroundColor: 'rgba(75,192,192,0.15)',
                borderColor: 'rgba(75,192,192,1)',
                borderWidth: 1,
                tension: .2,
                fill: true
            }]
        };
        new Chart(ctx, {type:'line', data, options:{responsive:true, plugins:{legend:{display:true}}, scales:{y:{beginAtZero:true}}}});
    })();
    
    function exportToExcel() {
        const table = document.querySelector('table');
        const rows = table.querySelectorAll('tbody tr');
        
        if (rows.length === 0) {
            alert('Không có dữ liệu để xuất');
            return;
        }
        
        const data = [];
        const headers = ['Khoảng thời gian', 'Doanh thu (VND)', 'Doanh thu thực tế (VND)'];
        data.push(headers);
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 2) {
                const period = cells[0].textContent.trim();
                const revenue = (cells[1] ? cells[1].textContent.trim().replace(/\./g, '').replace(' VND', '') : '0');
                const profit = (cells[2] ? cells[2].textContent.trim().replace(/\./g, '').replace(' VND', '') : '0');
                data.push([period, revenue, profit]);
            }
        });
        
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'BaoCaoDoanhThu');
        
        // Đặt độ rộng cột cho file Excel
        ws['!cols'] = [
            { wch: 20 },
            { wch: 15 }
        ];
        
        // Định dạng cột doanh thu là kiểu số
        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let row = 1; row <= range.e.r; row++) {
            const cell = ws[XLSX.utils.encode_cell({ r: row, c: 1 })];
            if (cell && cell.v) {
                cell.v = parseFloat(cell.v) || 0;
                cell.t = 'n';
            }
        }
        
        const date = new Date().toLocaleDateString('vi-VN').replace(/\//g, '-');
        XLSX.writeFile(wb, `BaoCaoDoanhThu_${date}.xlsx`);
    }
    </script>
</div>