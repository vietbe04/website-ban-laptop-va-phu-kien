<?php
$product = $data['product'] ?? null;
$suppliers = $data['suppliers'] ?? [];
$categories = $data['categories'] ?? [];
$isEdit = !empty($product);
$title = $isEdit ? 'Sửa hàng hóa' : 'Thêm hàng hóa';
?>

<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-md-10 offset-md-1">
      <div class="card">
        <div class="card-header">
          <h4><?= $title ?></h4>
        </div>
        <div class="card-body">
          <?php if (isset($_SESSION['flash_message'])): ?>
          <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'danger' ?> alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['flash_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); endif; ?>

          <form method="post" action="<?= $isEdit ? APP_URL.'/Supplier/updateProduct/'.$product['id'] : APP_URL.'/Supplier/storeProduct' ?>" class="needs-validation" novalidate>
            
            <!-- Thông tin nhà cung cấp -->
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                <select name="supplier_id" class="form-select" required <?= $isEdit ? 'disabled' : '' ?>>
                  <option value="">-- Chọn nhà cung cấp --</option>
                  <?php foreach($suppliers as $s): ?>
                  <option value="<?= $s['id'] ?>" <?= isset($product) && $product['supplier_id'] == $s['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['code'] . ' - ' . $s['name']) ?>
                  </option>
                  <?php endforeach; ?>
                </select>
                <?php if ($isEdit): ?>
                  <input type="hidden" name="supplier_id" value="<?= $product['supplier_id'] ?>">
                <?php endif; ?>
                <div class="invalid-feedback">Vui lòng chọn nhà cung cấp</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Mã SP NCC <span class="text-danger">*</span></label>
                <input type="text" name="product_code" class="form-control" 
                       value="<?= htmlspecialchars($product['product_code'] ?? '') ?>" 
                       placeholder="VD: SP001" required maxlength="50">
                <div class="invalid-feedback">Vui lòng nhập mã sản phẩm</div>
              </div>
            </div>

            <!-- Thông tin sản phẩm -->
            <div class="row mb-3">
              <div class="col-md-8">
                <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                <input type="text" name="product_name" class="form-control" 
                       value="<?= htmlspecialchars($product['product_name'] ?? '') ?>" 
                       placeholder="Nhập tên sản phẩm" required maxlength="200">
                <div class="invalid-feedback">Vui lòng nhập tên sản phẩm</div>
              </div>

              <div class="col-md-4">
                <label class="form-label">Danh mục</label>
                <select name="category" class="form-select">
                  <option value="">-- Chọn danh mục --</option>
                  <?php foreach($categories as $cat): ?>
                  <option value="<?= htmlspecialchars($cat) ?>" <?= isset($product) && $product['category'] == $cat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Đơn giá & đơn vị -->
            <div class="row mb-3">
              <div class="col-md-4">
                <label class="form-label">Đơn giá <span class="text-danger">*</span></label>
                <input type="number" name="unit_price" class="form-control" 
                       value="<?= $product['unit_price'] ?? '' ?>" 
                       placeholder="0" required min="0" step="0.01">
                <div class="invalid-feedback">Vui lòng nhập đơn giá</div>
              </div>

              <div class="col-md-2">
                <label class="form-label">Tiền tệ</label>
                <select name="currency" class="form-select">
                  <option value="VND" <?= isset($product) && $product['currency'] == 'VND' ? 'selected' : '' ?>>VND</option>
                  <option value="USD" <?= isset($product) && $product['currency'] == 'USD' ? 'selected' : '' ?>>USD</option>
                  <option value="EUR" <?= isset($product) && $product['currency'] == 'EUR' ? 'selected' : '' ?>>EUR</option>
                </select>
              </div>

              <div class="col-md-3">
                <label class="form-label">Đơn vị</label>
                <input type="text" name="unit" class="form-control" 
                       value="<?= htmlspecialchars($product['unit'] ?? '') ?>" 
                       placeholder="VD: cái, kg, hộp" maxlength="50">
              </div>

              <div class="col-md-3">
                <label class="form-label">SL tối thiểu</label>
                <input type="number" name="min_order_quantity" class="form-control" 
                       value="<?= $product['min_order_quantity'] ?? 1 ?>" 
                       placeholder="1" min="1">
              </div>
            </div>

            <!-- Thời gian & bảo hành -->
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Thời gian giao hàng (ngày)</label>
                <input type="number" name="lead_time_days" class="form-control" 
                       value="<?= $product['lead_time_days'] ?? '' ?>" 
                       placeholder="VD: 7" min="0">
              </div>

              <div class="col-md-6">
                <label class="form-label">Bảo hành</label>
                <input type="text" name="warranty_period" class="form-control" 
                       value="<?= htmlspecialchars($product['warranty_period'] ?? '') ?>" 
                       placeholder="VD: 12 tháng" maxlength="100">
              </div>
            </div>

            <!-- Trạng thái & Ghi chú -->
            <div class="row mb-3">
              <div class="col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                  <option value="1" <?= isset($product) && $product['status'] == 1 ? 'selected' : '' ?>>Còn cung cấp</option>
                  <option value="0" <?= isset($product) && $product['status'] == 0 ? 'selected' : '' ?>>Ngừng cung cấp</option>
                </select>
              </div>

              <div class="col-md-9">
                <label class="form-label">Ghi chú</label>
                <textarea name="notes" class="form-control" rows="2" 
                          placeholder="Ghi chú về sản phẩm..."><?= htmlspecialchars($product['notes'] ?? '') ?></textarea>
              </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between">
              <a href="<?= APP_URL ?>/Supplier/products" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> <?= $isEdit ? 'Cập nhật' : 'Thêm mới' ?>
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Bootstrap form validation
(function() {
  'use strict';
  var forms = document.querySelectorAll('.needs-validation');
  Array.prototype.slice.call(forms).forEach(function(form) {
    form.addEventListener('submit', function(event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>
