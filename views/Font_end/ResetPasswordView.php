<?php
/**
 * View: Reset password form
 * Expects $data['token'] to be provided by controller
 */
$token = $data['token'] ?? '';
?>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-3">Đặt lại mật khẩu</h4>
          <p class="text-muted">Vui lòng nhập mật khẩu mới cho tài khoản của bạn.</p>
          <form method="POST" action="<?= APP_URL ?>/AuthController/doResetPassword">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />
            <div class="mb-3">
              <label for="password" class="form-label">Mật khẩu mới</label>
              <input id="password" name="password" type="password" class="form-control" required minlength="6" autocomplete="new-password" />
            </div>
            <div class="mb-3">
              <label for="password_confirm" class="form-label">Xác nhận mật khẩu</label>
              <input id="password_confirm" name="password_confirm" type="password" class="form-control" required minlength="6" autocomplete="new-password" />
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
