<?php
/**
 * Cấu hình ngưỡng giảm giá theo phần trăm (Admin).
 * - Thêm ngưỡng: tổng tối thiểu, % giảm, trạng thái.
 * - Danh sách ngưỡng với thao tác bật/tắt, xoá.
 */
?>
<div class="container mt-4">
  <h3>Ngưỡng giảm giá theo %</h3>
  <form class="row g-3" method="POST" action="<?php echo APP_URL; ?>/Admin/thresholdCreate">
    <div class="col-md-3">
      <label class="form-label">Tổng tối thiểu (₫)</label>
      <input type="number" name="min_total" class="form-control" min="1000" step="1000" required>
    </div>
    <div class="col-md-2">
      <label class="form-label">Phần trăm (%)</label>
      <input type="number" name="percent" class="form-control" min="1" max="100" required>
    </div>
    <div class="col-md-2 d-flex align-items-end">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="status" id="status" checked>
        <label class="form-check-label" for="status">Kích hoạt</label>
      </div>
    </div>
    <div class="col-md-2 d-flex align-items-end">
      <button class="btn btn-primary" type="submit">Thêm ngưỡng</button>
    </div>
  </form>
  <hr>
  <table class="table table-bordered table-sm align-middle">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Tổng tối thiểu (₫)</th>
        <th>% giảm</th>
        <th>Trạng thái</th>
        <th>Ngày tạo</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
    <?php if (!empty($data['tiers'])): foreach($data['tiers'] as $tier): ?>
      <tr>
        <td><?php echo (int)$tier['id']; ?></td>
        <td><?php echo number_format($tier['min_total'],0,',','.'); ?></td>
        <td><?php echo (int)$tier['percent']; ?>%</td>
        <td><?php echo $tier['status']?'<span class="badge bg-success">Bật</span>':'<span class="badge bg-secondary">Tắt</span>'; ?></td>
        <td><?php echo htmlspecialchars($tier['created_at']); ?></td>
        <td>
          <a class="btn btn-sm btn-outline-warning" href="<?php echo APP_URL; ?>/Admin/thresholdToggle/<?php echo (int)$tier['id']; ?>">Toggle</a>
          <a class="btn btn-sm btn-outline-danger" href="<?php echo APP_URL; ?>/Admin/thresholdDelete/<?php echo (int)$tier['id']; ?>" onclick="return confirm('Xóa ngưỡng này?');">Xóa</a>
        </td>
      </tr>
    <?php endforeach; else: ?>
      <tr><td colspan="6" class="text-center text-muted">Chưa có ngưỡng nào.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  <p class="small text-muted">Hệ thống sẽ chọn mức % cao nhất thỏa điều kiện tổng tiền &ge; ngưỡng và trạng thái đang bật.</p>
</div>
