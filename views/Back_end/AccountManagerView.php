<?php
$list = $data['customerList'] ?? [];
$q    = $data['q'] ?? '';
$edit = $data['editItem'] ?? null;
$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$total = $data['total'] ?? 0;

// Phân nhóm theo role và tính thống kê
$userAccounts = [];
$adminAccounts = [];
$totalOrders = 0;
$totalRevenue = 0;
$lockedCount = 0;

foreach ($list as $u) {
    $role = $u['role'] ?? 'user';
    $isLocked = isset($u['is_locked']) && (int)$u['is_locked'] === 1;
    
    // Tính tổng thống kê
    $totalOrders += (int)$u['orders_count'];
    $totalRevenue += (float)$u['total_spent'];
    if ($isLocked) $lockedCount++;
    
    if ($role === 'admin' || $role === 'staff') {
        $adminAccounts[] = $u;
    } else {
        $userAccounts[]  = $u;
    }
}
?>
<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-people-fill"></i> Quản lý tài khoản</h2>
    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_SESSION['flash_message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); endif; ?>
  </div>

  <!-- Thống kê tổng quan -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-center bg-primary text-white">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-people"></i> Tổng tài khoản</h5>
          <h2><?= number_format($total) ?></h2>
          <small>Người dùng: <?= count($userAccounts) ?> | Admin/Staff: <?= count($adminAccounts) ?></small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center bg-success text-white">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-cart-check"></i> Tổng đơn hàng</h5>
          <h2><?= number_format($totalOrders) ?></h2>
          <small>Từ tất cả khách hàng</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center bg-info text-white">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-cash-stack"></i> Tổng doanh thu</h5>
          <h2><?= number_format($totalRevenue, 0, ',', '.') ?></h2>
          <small>VND</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center bg-warning text-dark">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-lock-fill"></i> Tài khoản khóa</h5>
          <h2><?= $lockedCount ?></h2>
          <small>Đang bị khóa</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Form tìm kiếm -->
  <form class="row g-2 mb-3" method="get" action="index.php">
    <input type="hidden" name="url" value="Admin/customers" />
    <div class="col-md-5">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" 
               class="form-control" placeholder="Tìm theo email, họ tên, số điện thoại..." />
      </div>
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100" type="submit">
        <i class="bi bi-search"></i> Tìm kiếm
      </button>
    </div>
    <?php if ($q !== ''): ?>
    <div class="col-md-2">
      <a class="btn btn-secondary w-100" href="<?= APP_URL ?>/Admin/customers">
        <i class="bi bi-x-circle"></i> Xóa bộ lọc
      </a>
    </div>
    <?php endif; ?>
  </form>

  <?php if ($edit): ?>
  <div class="card mb-3 border-warning">
    <div class="card-header bg-warning">
      <i class="bi bi-pencil-square"></i> Sửa tài khoản (ID: <?= (int)$edit['user_id'] ?>)
    </div>
    <div class="card-body">
      <form method="post" action="<?= APP_URL ?>/Admin/updateCustomer">
        <input type="hidden" name="user_id" value="<?= (int)$edit['user_id'] ?>" />
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Họ tên</label>
            <input class="form-control" value="<?= htmlspecialchars($edit['fullname']) ?>" readonly />
          </div>
          <div class="col-md-3">
            <label class="form-label">Email</label>
            <input class="form-control" value="<?= htmlspecialchars($edit['email']) ?>" readonly />
          </div>
          <div class="col-md-2">
            <label class="form-label">Số đơn hàng</label>
            <input class="form-control" value="<?= (int)$edit['orders_count'] ?>" readonly />
          </div>
          <div class="col-md-2">
            <label class="form-label">Tổng chi tiêu</label>
            <input class="form-control" value="<?= number_format((float)$edit['total_spent'], 0, ',', '.') ?> VND" readonly />
          </div>
          <div class="col-md-2">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
              <?php $currentRole = $edit['role'] ?? 'user'; ?>
              <option value="user" <?= $currentRole==='user'?'selected':'' ?>>user</option>
              <option value="staff" <?= $currentRole==='staff'?'selected':'' ?>>staff</option>
              <option value="admin" <?= $currentRole==='admin'?'selected':'' ?>>admin</option>
            </select>
          </div>
          <div class="col-12">
            <button class="btn btn-success" type="submit">
              <i class="bi bi-check-circle"></i> Lưu thay đổi
            </button>
            <a class="btn btn-secondary" href="<?= APP_URL ?>/Admin/customers">
              <i class="bi bi-x-circle"></i> Hủy
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <ul class="nav nav-tabs" id="acctTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
        <i class="bi bi-person"></i> Người dùng (<?= count($userAccounts) ?>)
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#admins" type="button" role="tab">
        <i class="bi bi-shield-check"></i> Quản trị / Staff (<?= count($adminAccounts) ?>)
      </button>
    </li>
  </ul>
  <div class="tab-content pt-3">
    <div class="tab-pane fade show active" id="users" role="tabpanel">
      <?php $tableData = $userAccounts; include __DIR__ . '/partials/_account_table.php'; ?>
    </div>
    <div class="tab-pane fade" id="admins" role="tabpanel">
      <?php $tableData=$adminAccounts; include __DIR__ . '/partials/_account_table.php'; ?>
    </div>
  </div>
  
  <!-- Phân trang -->
  <?php if ($totalPages > 1): ?>
  <nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
      <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= APP_URL ?>/Admin/customers?q=<?= urlencode($q) ?>&page=<?= max(1, $currentPage - 1) ?>">
          <i class="bi bi-chevron-left"></i> Trước
        </a>
      </li>
      
      <?php 
      $startPage = max(1, $currentPage - 2);
      $endPage = min($totalPages, $currentPage + 2);
      
      if ($startPage > 1): ?>
        <li class="page-item">
          <a class="page-link" href="<?= APP_URL ?>/Admin/customers?q=<?= urlencode($q) ?>&page=1">1</a>
        </li>
        <?php if ($startPage > 2): ?>
          <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php endif; ?>
      <?php endif; ?>
      
      <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
      <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
        <a class="page-link" href="<?= APP_URL ?>/Admin/customers?q=<?= urlencode($q) ?>&page=<?= $i ?>"><?= $i ?></a>
      </li>
      <?php endfor; ?>
      
      <?php if ($endPage < $totalPages): ?>
        <?php if ($endPage < $totalPages - 1): ?>
          <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php endif; ?>
        <li class="page-item">
          <a class="page-link" href="<?= APP_URL ?>/Admin/customers?q=<?= urlencode($q) ?>&page=<?= $totalPages ?>"><?= $totalPages ?></a>
        </li>
      <?php endif; ?>
      
      <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= APP_URL ?>/Admin/customers?q=<?= urlencode($q) ?>&page=<?= min($totalPages, $currentPage + 1) ?>">
          Sau <i class="bi bi-chevron-right"></i>
        </a>
      </li>
    </ul>
    <div class="text-center text-muted small mt-2">
      <i class="bi bi-info-circle"></i> Trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng: <?= $total ?> tài khoản)
    </div>
  </nav>
  <?php endif; ?>
</div>

<style>
.card {
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}
.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
.btn-group-vertical .btn {
  margin-bottom: 2px;
}
.table-secondary {
  opacity: 0.7;
}
</style>