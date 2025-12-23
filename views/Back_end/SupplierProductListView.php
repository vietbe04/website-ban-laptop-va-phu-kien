<?php
$list = $data['products'] ?? [];
$suppliers = $data['suppliers'] ?? [];
$categories = $data['categories'] ?? [];
$supplierId = $data['supplierId'] ?? null;
$search = $data['search'] ?? '';
$category = $data['category'] ?? null;
$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$total = $data['total'] ?? 0;
?>

<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-box-seam"></i> Quản lý hàng hóa cung cấp</h2>
    <div>
      <a href="<?= APP_URL ?>/Supplier/index" class="btn btn-secondary me-2">
        <i class="bi bi-building"></i> NCC
      </a>
      <a href="<?= APP_URL ?>/Supplier/contracts" class="btn btn-secondary me-2">
        <i class="bi bi-file-earmark-text"></i> Hợp đồng
      </a>
      <a href="<?= APP_URL ?>/Supplier/createProduct" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm hàng hóa
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
        <div class="col-md-3">
          <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                 class="form-control" placeholder="Tìm mã, tên sản phẩm...">
        </div>
        <div class="col-md-3">
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
          <select name="category" class="form-select">
            <option value="">Tất cả danh mục</option>
            <?php foreach($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat) ?>
            </option>
            <?php endforeach; ?>
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
          <th>Mã SP NCC</th>
          <th>Nhà cung cấp</th>
          <th>Tên sản phẩm</th>
          <th>Danh mục</th>
          <th>Đơn giá</th>
          <th>Đơn vị</th>
          <th>SL tối thiểu</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($list)): ?>
        <tr><td colspan="9" class="text-center">Không có dữ liệu</td></tr>
        <?php else: foreach($list as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['product_code'] ?? '-') ?></td>
          <td><small><?= htmlspecialchars($item['supplier_name']) ?></small></td>
          <td><?= htmlspecialchars($item['product_name']) ?></td>
          <td><span class="badge bg-info"><?= htmlspecialchars($item['category'] ?? '-') ?></span></td>
          <td><strong><?= number_format($item['unit_price'], 0, ',', '.') ?></strong> <?= $item['currency'] ?></td>
          <td><?= htmlspecialchars($item['unit'] ?? '-') ?></td>
          <td><?= $item['min_order_quantity'] ?></td>
          <td>
            <?php if ($item['status'] == 1): ?>
              <span class="badge bg-success">Còn cung cấp</span>
            <?php else: ?>
              <span class="badge bg-secondary">Ngừng</span>
            <?php endif; ?>
          </td>
          <td>
            <a href="<?= APP_URL ?>/Supplier/editProduct/<?= $item['id'] ?>" class="btn btn-sm btn-warning">
              <i class="bi bi-pencil"></i>
            </a>
            <a href="<?= APP_URL ?>/Supplier/deleteProduct/<?= $item['id'] ?>" 
               class="btn btn-sm btn-danger" onclick="return confirm('Xóa hàng hóa này?')">
              <i class="bi bi-trash"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
