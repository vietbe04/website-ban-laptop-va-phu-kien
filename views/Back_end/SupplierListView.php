<?php
$list = $data['suppliers'] ?? [];
$search = $data['search'] ?? '';
$status = $data['status'] ?? null;
$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$total = $data['total'] ?? 0;
?>

<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-building"></i> Quản lý nhà cung cấp</h2>
    <a href="<?= APP_URL ?>/Supplier/create" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Thêm nhà cung cấp
    </a>
  </div>

  <?php if (isset($_SESSION['flash_message'])): ?>
  <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
    <?= htmlspecialchars($_SESSION['flash_message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); endif; ?>

  <!-- Form tìm kiếm và lọc -->
  <div class="card mb-3">
    <div class="card-body">
      <form method="get" action="<?= APP_URL ?>/Supplier/index" class="row g-3">
        <div class="col-md-5">
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                   class="form-control" placeholder="Tìm mã, tên, người liên hệ, SĐT...">
          </div>
        </div>
        <div class="col-md-2">
          <select name="status" class="form-select">
            <option value="">Tất cả trạng thái</option>
            <option value="1" <?= $status === 1 ? 'selected' : '' ?>>Hoạt động</option>
            <option value="0" <?= $status === 0 ? 'selected' : '' ?>>Ngừng hợp tác</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-funnel"></i> Lọc
          </button>
        </div>
        <?php if ($search || $status !== null): ?>
        <div class="col-md-2">
          <a href="<?= APP_URL ?>/Supplier/index" class="btn btn-secondary w-100">
            <i class="bi bi-x-circle"></i> Xóa bộ lọc
          </a>
        </div>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <!-- Danh sách nhà cung cấp -->
  <div class="card">
    <div class="card-header bg-light">
      <strong>Danh sách nhà cung cấp (<?= $total ?>)</strong>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width: 80px;">ID</th>
              <th style="width: 120px;">Mã NCC</th>
              <th>Tên nhà cung cấp</th>
              <th>Người liên hệ</th>
              <th>Điện thoại</th>
              <th>Email</th>
              <th style="width: 100px;">Trạng thái</th>
              <th style="width: 200px;">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($list)): ?>
            <tr>
              <td colspan="8" class="text-center text-muted py-4">Không có dữ liệu</td>
            </tr>
            <?php else: foreach($list as $item): ?>
            <tr>
              <td><?= $item['id'] ?></td>
              <td><strong><?= htmlspecialchars($item['code']) ?></strong></td>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td><?= htmlspecialchars($item['contact_person'] ?? '-') ?></td>
              <td><?= htmlspecialchars($item['phone'] ?? '-') ?></td>
              <td><?= htmlspecialchars($item['email'] ?? '-') ?></td>
              <td>
                <?php if ($item['status'] == 1): ?>
                  <span class="badge bg-success">Hoạt động</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Ngừng</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="<?= APP_URL ?>/Supplier/edit/<?= $item['id'] ?>" 
                   class="btn btn-sm btn-warning" title="Sửa">
                  <i class="bi bi-pencil"></i>
                </a>
                <a href="<?= APP_URL ?>/Supplier/delete/<?= $item['id'] ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('Xóa nhà cung cấp này?')" title="Xóa">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Phân trang -->
  <?php if ($totalPages > 1): ?>
  <nav class="mt-3">
    <ul class="pagination justify-content-center">
      <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="?search=<?= urlencode($search) ?>&status=<?= $status ?>&page=<?= $currentPage - 1 ?>">Trước</a>
      </li>
      <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
      <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
        <a class="page-link" href="?search=<?= urlencode($search) ?>&status=<?= $status ?>&page=<?= $i ?>"><?= $i ?></a>
      </li>
      <?php endfor; ?>
      <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link" href="?search=<?= urlencode($search) ?>&status=<?= $status ?>&page=<?= $currentPage + 1 ?>">Sau</a>
      </li>
    </ul>
  </nav>
  <?php endif; ?>
</div>
