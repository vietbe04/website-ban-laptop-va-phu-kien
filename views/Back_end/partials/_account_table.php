<?php if (!isset($tableData)) { $tableData = []; } ?>
<div class="table-responsive">
  <table class="table table-bordered table-hover align-middle">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Họ tên</th>
        <th>Email</th>
        <th>Role</th>
        <th>Trạng thái</th>
        <th>Số đơn hàng</th>
        <th>Tổng chi tiêu</th>
        <th>Ngày tạo</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($tableData)): ?>
        <tr><td colspan="9" class="text-center text-muted">Không có dữ liệu</td></tr>
      <?php else: foreach($tableData as $u): 
        $isLocked = isset($u['is_locked']) && (int)$u['is_locked'] === 1;
      ?>
        <tr class="<?= $isLocked ? 'table-secondary' : '' ?>">
          <td><?= (int)$u['user_id'] ?></td>
          <td><?= htmlspecialchars($u['fullname']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($u['role'] ?? 'user') ?></span></td>
          <td>
            <?php if ($isLocked): ?>
              <span class="badge bg-danger">
                <i class="bi bi-lock-fill"></i> Đã khóa
              </span>
            <?php else: ?>
              <span class="badge bg-success">
                <i class="bi bi-unlock-fill"></i> Hoạt động
              </span>
            <?php endif; ?>
          </td>
          <td>
            <span class="badge bg-info text-dark"><?= (int)$u['orders_count'] ?> đơn</span>
          </td>
          <td>
            <strong class="text-primary"><?= number_format((float)$u['total_spent'],0,',','.') ?> VND</strong>
          </td>
          <td><small><?= htmlspecialchars($u['created_at']) ?></small></td>
          <td>
            <div class="btn-group-vertical btn-group-sm" role="group">
              <a href="<?= APP_URL ?>/Admin/customerEdit/<?= (int)$u['user_id'] ?><?= ($q??'')!==''? ('?q='.urlencode($q)) : '' ?>" 
                 class="btn btn-warning btn-sm" title="Sửa thông tin">
                <i class="bi bi-pencil"></i> Sửa
              </a>
              
              <?php if ($isLocked): ?>
                <a href="<?= APP_URL ?>/Admin/unlockCustomer/<?= (int)$u['user_id'] ?>" 
                   class="btn btn-success btn-sm" 
                   onclick="return confirm('Mở khóa tài khoản này?');" 
                   title="Mở khóa tài khoản">
                  <i class="bi bi-unlock"></i> Mở khóa
                </a>
              <?php else: ?>
                <a href="<?= APP_URL ?>/Admin/lockCustomer/<?= (int)$u['user_id'] ?>" 
                   class="btn btn-warning btn-sm" 
                   onclick="return confirm('Khóa tài khoản này? Người dùng sẽ không thể đăng nhập.');" 
                   title="Khóa tài khoản">
                  <i class="bi bi-lock"></i> Khóa
                </a>
              <?php endif; ?>
              
              <a href="<?= APP_URL ?>/Admin/deleteCustomer/<?= (int)$u['user_id'] ?>" 
                 class="btn btn-danger btn-sm" 
                 onclick="return confirm('XÓA VĨNH VIỄN tài khoản này? Hành động này không thể hoàn tác!');" 
                 title="Xóa tài khoản">
                <i class="bi bi-trash"></i> Xóa
              </a>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
