<?php
// Account info page for logged-in users
$user = $data['user'] ?? [];
$error = $data['error'] ?? '';
$success = $data['success'] ?? '';
?>

<link rel="stylesheet" href="<?= APP_URL ?>/public/css/account-info.css">

<div class="account-container">
  <div class="container">
    <div class="account-card">
      <div class="account-header">
        <div class="user-avatar">
          <?= strtoupper(substr($user['fullname'] ?? 'U', 0, 1)) ?>
        </div>
        <h1>Thông tin tài khoản</h1>
        <p>Quản lý thông tin cá nhân và bảo mật của bạn</p>
      </div>
      
      <div class="account-body">
        <?php if ($error): ?>
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
          <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>

        <div class="row">
          <div class="col-lg-6">
            <div class="form-section">
              <h3 class="section-title">Thông tin cá nhân</h3>
              <form method="post" action="<?= APP_URL ?>/AuthController/updateAccount">
                <div class="mb-3">
                  <label class="form-label">
                    <i class="fas fa-user"></i> Họ và tên
                  </label>
                  <input type="text" name="fullname" class="form-control" 
                         value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required 
                         placeholder="Nhập họ và tên của bạn">
                </div>
                <div class="mb-4">
                  <label class="form-label">
                    <i class="fas fa-envelope"></i> Email
                  </label>
                  <input type="email" name="email" class="form-control" 
                         value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly aria-readonly="true"
                         placeholder="Email không thể thay đổi">
                  <div class="form-text" style="color:#6b7280; margin-top:6px;"></div>
                </div>
                <button class="btn btn-update w-100" type="submit">
                  <i class="fas fa-save"></i> Cập nhật thông tin
                </button>
              </form>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="form-section">
              <h3 class="section-title">Bảo mật tài khoản</h3>
              <form method="post" action="<?= APP_URL ?>/AuthController/changePasswordAuthenticated">
                <div class="mb-3">
                  <label class="form-label">
                    <i class="fas fa-lock"></i> Mật khẩu hiện tại
                  </label>
                  <input type="password" name="current_password" class="form-control" required 
                         placeholder="Nhập mật khẩu hiện tại">
                </div>
                <div class="mb-3">
                  <label class="form-label">
                    <i class="fas fa-key"></i> Mật khẩu mới
                  </label>
                  <input type="password" name="new_password" class="form-control" required 
                         placeholder="Nhập mật khẩu mới">
                </div>
                <div class="mb-4">
                  <label class="form-label">
                    <i class="fas fa-check-double"></i> Xác nhận mật khẩu mới
                  </label>
                  <input type="password" name="new_password_confirm" class="form-control" required 
                         placeholder="Nhập lại mật khẩu mới">
                </div>
                <button class="btn btn-password w-100" type="submit">
                  <i class="fas fa-shield-alt"></i> Đổi mật khẩu
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">