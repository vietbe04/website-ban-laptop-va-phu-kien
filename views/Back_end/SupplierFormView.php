<?php
$action = $data['action'] ?? 'create';
$supplier = $data['supplier'] ?? null;
$isEdit = $action === 'edit' && $supplier;
?>

<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>
      <i class="bi bi-building"></i> 
      <?= $isEdit ? 'Sửa nhà cung cấp' : 'Thêm nhà cung cấp mới' ?>
    </h2>
    <a href="<?= APP_URL ?>/Supplier/index" class="btn btn-secondary">
      <i class="bi bi-arrow-left"></i> Quay lại
    </a>
  </div>

  <?php if (isset($_SESSION['flash_message'])): ?>
  <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
    <?= htmlspecialchars($_SESSION['flash_message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="post" action="<?= $isEdit ? APP_URL . '/Supplier/update/' . $supplier['id'] : APP_URL . '/Supplier/store' ?>" class="needs-validation" novalidate>
        
        <div class="row g-3">
          <!-- Thông tin cơ bản -->
          <div class="col-12">
            <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-info-circle"></i> Thông tin cơ bản</h5>
          </div>

          <div class="col-md-3">
            <label class="form-label">Mã nhà cung cấp <span class="text-danger">*</span></label>
            <input type="text" name="code" class="form-control" 
                   value="<?= $isEdit ? htmlspecialchars($supplier['code']) : '' ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Tên nhà cung cấp <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" 
                   value="<?= $isEdit ? htmlspecialchars($supplier['name']) : '' ?>" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
              <option value="1" <?= $isEdit && $supplier['status'] == 1 ? 'selected' : '' ?>>Hoạt động</option>
              <option value="0" <?= $isEdit && $supplier['status'] == 0 ? 'selected' : '' ?>>Ngừng hợp tác</option>
            </select>
          </div>

          <!-- Thông tin liên hệ -->
          <div class="col-12 mt-4">
            <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-person-lines-fill"></i> Thông tin liên hệ</h5>
          </div>

          <div class="col-md-4">
            <label class="form-label">Người liên hệ</label>
            <input type="text" name="contact_person" class="form-control" 
                   value="<?= $isEdit ? htmlspecialchars($supplier['contact_person']) : '' ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Điện thoại</label>
            <input type="text" name="phone" class="form-control" 
                   value="<?= $isEdit ? htmlspecialchars($supplier['phone']) : '' ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" 
                   value="<?= $isEdit ? htmlspecialchars($supplier['email']) : '' ?>">
          </div>

          <div class="col-12">
            <label class="form-label">Địa chỉ</label>
            <textarea name="address" class="form-control" rows="2"><?= $isEdit ? htmlspecialchars($supplier['address']) : '' ?></textarea>
          </div>

          <!-- Thông tin pháp lý và ngân hàng -->
          <div class="col-12 mt-4">
            <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-bank"></i> Thông tin pháp lý & ngân hàng</h5>
          </div>

          <div class="col-md-4">
            <label class="form-label">Mã số thuế</label>
            <input type="text" name="tax_code" class="form-control" 
                   value="<?= $isEdit ? htmlspecialchars($supplier['tax_code']) : '' ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Số tài khoản</label>
            <input type="text" name="bank_account" class="form-control" 
                   value="<?= $isEdit ? htmlspecialchars($supplier['bank_account']) : '' ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Ngân hàng</label>
            <input type="text" name="bank_name" class="form-control" 
                   value="<?= $isEdit ? htmlspecialchars($supplier['bank_name']) : '' ?>">
          </div>

          <!-- Ghi chú -->
          <div class="col-12 mt-3">
            <label class="form-label">Ghi chú</label>
            <textarea name="notes" class="form-control" rows="3"><?= $isEdit ? htmlspecialchars($supplier['notes']) : '' ?></textarea>
          </div>

          <div class="col-12 mt-4">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-circle"></i> <?= $isEdit ? 'Cập nhật' : 'Tạo mới' ?>
            </button>
            <a href="<?= APP_URL ?>/Supplier/index" class="btn btn-secondary">
              <i class="bi bi-x-circle"></i> Hủy
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Form validation
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
