<?php
/** @var array $data */
$list = $data['customerList'] ?? [];
$q    = $data['q'] ?? '';
$edit = $data['editItem'] ?? null;
?>
<div class="container-fluid py-4">
  
  <!-- Header Section -->
  <div class="admin-card mb-4">
    <div class="row align-items-center">
      <div class="col-md-6">
        <h2 class="mb-1 text-primary">
          <i class="bi bi-people me-2"></i>Quản lý khách hàng
        </h2>
        <p class="text-muted mb-0">Danh sách và quản lý thông tin khách hàng trong hệ thống</p>
      </div>
      <div class="col-md-6 text-md-end">
        <a href="<?= APP_URL ?>/AuthController/register" class="btn btn-success">
          <i class="bi bi-person-plus me-1"></i> Thêm khách hàng mới
        </a>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="stats-card">
        <div class="stats-icon bg-primary">
          <i class="bi bi-people"></i>
        </div>
        <div class="stats-content">
          <h3><?= count($list) ?></h3>
          <p>Tổng khách hàng</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="stats-card">
        <div class="stats-icon bg-success">
          <i class="bi bi-cart-check"></i>
        </div>
        <div class="stats-content">
          <h3><?= count(array_filter($list, fn($u) => $u['orders_count'] > 0)) ?></h3>
          <p>Đã từng mua hàng</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="stats-card">
        <div class="stats-icon bg-warning">
          <i class="bi bi-currency-dollar"></i>
        </div>
        <div class="stats-content">
          <h3><?= number_format(array_sum(array_column($list, 'total_spent')), 0, ',', '.') ?></h3>
          <p>Tổng doanh thu</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="stats-card">
        <div class="stats-icon bg-info">
          <i class="bi bi-calendar-plus"></i>
        </div>
        <div class="stats-content">
          <h3><?= count(array_filter($list, fn($u) => date('Y-m-d', strtotime($u['created_at'])) === date('Y-m-d'))) ?></h3>
          <p>Khách hàng mới hôm nay</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Search Form -->
  <div class="admin-card mb-4">
    <form method="get" action="index.php" class="row g-3">
      <input type="hidden" name="url" value="Admin/customers" />
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-text">
            <i class="bi bi-search"></i>
          </span>
          <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" class="form-control" placeholder="Tìm theo email, số điện thoại hoặc tên..." />
        </div>
      </div>
      <div class="col-md-3">
        <button class="btn btn-primary w-100" type="submit">
          <i class="bi bi-search me-1"></i> Tìm kiếm
        </button>
      </div>
      <?php if ($q !== ''): ?>
      <div class="col-md-2">
        <a class="btn btn-outline-secondary w-100" href="<?= APP_URL ?>/Admin/customers">
          <i class="bi bi-x-circle me-1"></i> Xóa lọc
        </a>
      </div>
      <?php endif; ?>
    </form>
  </div>

  <?php if ($edit): ?>
  <div class="admin-card mb-4">
    <div class="card-header bg-warning bg-opacity-10">
      <h5 class="mb-0">
        <i class="bi bi-pencil-square me-2"></i>Sửa thông tin khách hàng
        <span class="badge bg-warning text-dark ms-2">ID: <?= (int)$edit['user_id'] ?></span>
      </h5>
    </div>
    <div class="card-body">
      <form method="post" action="<?= APP_URL ?>/Admin/updateCustomer">
        <input type="hidden" name="user_id" value="<?= (int)$edit['user_id'] ?>" />
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Họ tên</label>
            <input class="form-control" name="fullname" value="<?= htmlspecialchars($edit['fullname']) ?>" required />
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Email</label>
            <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($edit['email']) ?>" required />
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-success me-2" type="submit">
              <i class="bi bi-save me-1"></i>Lưu thay đổi
            </button>
            <a class="btn btn-outline-secondary" href="<?= APP_URL ?>/Admin/customers">
              <i class="bi bi-x-circle me-1"></i>Hủy
            </a>
          </div>
        </div>
        <div class="mt-3 alert alert-info">
          <i class="bi bi-info-circle me-1"></i>
          <small>Lưu ý: SĐT/Địa chỉ sẽ được cập nhật theo đơn hàng gần đây (không sửa tại đây).</small>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <div class="admin-card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">
        <i class="bi bi-people me-2"></i>Danh sách khách hàng
      </h5>
      <span class="badge bg-primary"><?= count($list) ?> khách hàng</span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th class="text-center" style="width: 60px;">ID</th>
              <th>Họ tên</th>
              <th>Email</th>
              <th>Số ĐT (gần đây)</th>
              <th>Địa chỉ (gần đây)</th>
              <th class="text-center">Số đơn</th>
              <th class="text-end">Tổng chi tiêu</th>
              <th class="text-center">Ngày tạo</th>
              <th class="text-center" style="width: 140px;">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php if(empty($list)): ?>
              <tr>
                <td colspan="9">
                  <div class="empty-state">
                    <div class="empty-state-icon">
                      <i class="bi bi-person-x display-1 text-muted"></i>
                    </div>
                    <h5 class="text-muted">Không có dữ liệu</h5>
                    <p class="text-muted">Bắt đầu bằng cách thêm khách hàng mới hoặc chờ đơn hàng đầu tiên.</p>
                  </div>
                </td>
              </tr>
            <?php else: foreach($list as $u): ?>
              <tr>
                <td class="text-center fw-bold"><?= (int)$u['user_id'] ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="customer-avatar me-2">
                      <?= strtoupper(substr($u['fullname'], 0, 1)) ?>
                    </div>
                    <div>
                      <div class="fw-semibold"><?= htmlspecialchars($u['fullname']) ?></div>
                      <small class="text-muted"><?= htmlspecialchars($u['email']) ?></small>
                    </div>
                  </div>
                </td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
                <td><?= htmlspecialchars($u['address'] ?? '—') ?></td>
                <td class="text-center">
                  <span class="badge bg-info"><?= (int)$u['orders_count'] ?></span>
                </td>
                <td class="text-end fw-semibold text-success">
                  <?= number_format((float)$u['total_spent'],0,',','.') ?> VND
                </td>
                <td class="text-center">
                  <small><?= date('d/m/Y', strtotime($u['created_at'])) ?></small>
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm">
                    <a class="btn btn-outline-warning" href="<?= APP_URL ?>/Admin/customerEdit/<?= (int)$u['user_id'] ?><?= $q!==''? ('?q='.urlencode($q)) : '' ?>"
                       title="Sửa thông tin">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a class="btn btn-outline-danger" onclick="return confirm('Xóa khách hàng sẽ không thể hoàn tác. Tiếp tục?')" 
                       href="<?= APP_URL ?>/Admin/deleteCustomer/<?= (int)$u['user_id'] ?>"
                       title="Xóa khách hàng">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>