<?php
$list = $data['contracts'] ?? [];
$suppliers = $data['suppliers'] ?? [];
$supplierId = $data['supplierId'] ?? null;
$status = $data['status'] ?? null;
$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$total = $data['total'] ?? 0;
?>

<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-file-earmark-text"></i> Quản lý hợp đồng</h2>
    <div>
      <a href="<?= APP_URL ?>/Supplier/index" class="btn btn-secondary me-2">
        <i class="bi bi-building"></i> NCC
      </a>
      <a href="<?= APP_URL ?>/Supplier/products" class="btn btn-secondary me-2">
        <i class="bi bi-box-seam"></i> Hàng hóa
      </a>
      <a href="<?= APP_URL ?>/Supplier/createContract" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm hợp đồng
      </a>
    </div>
  </div>

  <?php if (isset($_SESSION['flash_message'])): ?>
  <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
    <?= htmlspecialchars($_SESSION['flash_message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); endif; ?>

  <!-- Form lọc -->
  <div class="card mb-3">
    <div class="card-body">
      <form method="get" class="row g-3">
        <div class="col-md-4">
          <select name="supplier_id" class="form-select">
            <option value="">Tất cả nhà cung cấp</option>
            <?php foreach($suppliers as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $supplierId == $s['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['code'] . ' - ' . $s['name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <select name="status" class="form-select">
            <option value="">Tất cả trạng thái</option>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Hiệu lực</option>
            <option value="expired" <?= $status === 'expired' ? 'selected' : '' ?>>Hết hạn</option>
            <option value="terminated" <?= $status === 'terminated' ? 'selected' : '' ?>>Chấm dứt</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">Lọc</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Danh sách -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>Số HĐ</th>
          <th>Nhà cung cấp</th>
          <th>Tên hợp đồng</th>
          <th>Ngày bắt đầu</th>
          <th>Ngày kết thúc</th>
          <th>Giá trị</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" class="text-center">Không có dữ liệu</td></tr>
        <?php else: foreach($list as $item): ?>
        <tr>
          <td><strong><?= htmlspecialchars($item['contract_number']) ?></strong></td>
          <td><?= htmlspecialchars($item['supplier_name']) ?></td>
          <td><?= htmlspecialchars($item['contract_name']) ?></td>
          <td><?= date('d/m/Y', strtotime($item['start_date'])) ?></td>
          <td><?= $item['end_date'] ? date('d/m/Y', strtotime($item['end_date'])) : '-' ?></td>
          <td><?= number_format($item['contract_value'], 0, ',', '.') ?> VND</td>
          <td>
            <?php
            $badges = ['active' => 'success', 'expired' => 'danger', 'terminated' => 'secondary'];
            $labels = ['active' => 'Hiệu lực', 'expired' => 'Hết hạn', 'terminated' => 'Chấm dứt'];
            ?>
            <span class="badge bg-<?= $badges[$item['status']] ?? 'secondary' ?>">
              <?= $labels[$item['status']] ?? $item['status'] ?>
            </span>
          </td>
          <td>
            <a href="<?= APP_URL ?>/Supplier/editContract/<?= $item['id'] ?>" class="btn btn-sm btn-warning">
              <i class="bi bi-pencil"></i>
            </a>
            <a href="<?= APP_URL ?>/Supplier/deleteContract/<?= $item['id'] ?>" 
               class="btn btn-sm btn-danger" onclick="return confirm('Xóa hợp đồng này?')">
              <i class="bi bi-trash"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
