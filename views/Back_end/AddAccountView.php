<?php
/** @var array $data */
?>

<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="admin-card">
        <div class="card-header bg-primary bg-opacity-10">
          <h4 class="mb-0">
            <i class="bi bi-person-plus me-2"></i>Thêm tài khoản mới
          </h4>
        </div>

<script>
// Form validation for add account form
document.addEventListener('DOMContentLoaded', function() {
  'use strict';
  
  var form = document.querySelector('.needs-validation');
  if (form) {
    form.addEventListener('submit', function(event) {
      if (form.checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  }

  // Password toggle
  var togglePassword = document.getElementById('togglePassword');
  if (togglePassword) {
    togglePassword.addEventListener('click', function() {
      const password = document.getElementById('password');
      const toggleBtn = this.querySelector('i');
      if (password.type === 'password') {
        password.type = 'text';
        toggleBtn.classList.remove('bi-eye');
        toggleBtn.classList.add('bi-eye-slash');
      } else {
        password.type = 'password';
        toggleBtn.classList.remove('bi-eye-slash');
        toggleBtn.classList.add('bi-eye');
      }
    });
  }
});
</script>
        <div class="card-body">
          <?php if (isset($data['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle me-2"></i>
              <?= htmlspecialchars($data['error']) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if (!isset($data['show_otp']) || !$data['show_otp']): ?>
          <form method="post" action="<?= APP_URL ?>/Admin/addAccount" class="needs-validation" novalidate>
            <div class="row g-3">
              <div class="col-12">
                <label for="fullname" class="form-label fw-semibold">
                  <i class="bi bi-person me-1"></i>Họ tên đầy đủ
                </label>
                <input type="text" class="form-control" id="fullname" name="fullname" 
                       value="<?= htmlspecialchars($_POST['fullname'] ?? $data['form_data']['fullname'] ?? '') ?>" required />
                <div class="invalid-feedback">Vui lòng nhập họ tên đầy đủ</div>
              </div>

              <div class="col-12">
                <label for="email" class="form-label fw-semibold">
                  <i class="bi bi-envelope me-1"></i>Email
                </label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($_POST['email'] ?? $data['form_data']['email'] ?? '') ?>" required />
                <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
              </div>

              <div class="col-12">
                <label for="password" class="form-label fw-semibold">
                  <i class="bi bi-lock me-1"></i>Mật khẩu
                </label>
                <div class="input-group">
                  <input type="password" class="form-control" id="password" name="password" required />
                  <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <div class="invalid-feedback">Vui lòng nhập mật khẩu</div>
                <small class="text-muted">Mật khẩu phải có ít nhất 6 ký tự</small>
              </div>

              <div class="col-12">
                <label for="role" class="form-label fw-semibold">
                  <i class="bi bi-person-badge me-1"></i>Phân quyền
                </label>
                <select class="form-select" id="role" name="role" required>
                  <option value="user" <?= (($_POST['role'] ?? $data['form_data']['role'] ?? '') === 'user') ? 'selected' : '' ?>>
                    Khách hàng
                  </option>
                  <option value="staff" <?= (($_POST['role'] ?? $data['form_data']['role'] ?? '') === 'staff') ? 'selected' : '' ?>>
                    Nhân viên
                  </option>
                  <option value="admin" <?= (($_POST['role'] ?? $data['form_data']['role'] ?? '') === 'admin') ? 'selected' : '' ?>>
                    Quản trị viên
                  </option>
                </select>
                <div class="invalid-feedback">Vui lòng chọn phân quyền</div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-12">
                <button type="submit" class="btn btn-success me-2">
                  <i class="bi bi-person-plus me-1"></i>Thêm tài khoản
                </button>
                <a href="<?= APP_URL ?>/Admin/customers" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
                </a>
              </div>
            </div>
          </form>
          <?php else: ?>
          <!-- OTP Verification Form -->
          <form method="post" action="<?= APP_URL ?>/Admin/verifyOtp" class="needs-validation" novalidate>
            <div class="row g-3">
              <div class="col-12">
                <div class="alert alert-info">
                  <i class="bi bi-info-circle me-2"></i>
                  Mã OTP đã được gửi đến email: <strong><?= htmlspecialchars($data['email'] ?? '') ?></strong>
                </div>
              </div>
              
              <div class="col-12">
                <label for="otp" class="form-label fw-semibold">
                  <i class="bi bi-shield-check me-1"></i>Nhập mã OTP
                </label>
                <input type="text" class="form-control" id="otp" name="otp" 
                       pattern="[0-9]{6}" maxlength="6" required 
                       placeholder="Nhập 6 chữ số" />
                <div class="invalid-feedback">Vui lòng nhập mã OTP gồm 6 chữ số</div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-12">
                <button type="submit" class="btn btn-success me-2">
                  <i class="bi bi-check-circle me-1"></i>Xác nhận OTP
                </button>
                <a href="<?= APP_URL ?>/Admin/addAccount" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-left me-1"></i>Quay lại
                </a>
              </div>
            </div>
          </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>