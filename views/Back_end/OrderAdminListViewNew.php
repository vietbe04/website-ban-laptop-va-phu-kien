<?php
$filters = $data['filters'] ?? ['code'=>'','email'=>'','status'=>''];
$orders = $data['orders'] ?? [];
?>
<div class="container mt-4">
  <h2 class="mb-3">Quản lý đơn hàng</h2>
  <form class="row g-2 mb-3" method="get" action="<?= APP_URL ?>/AdminOrder/index">
    <div class="col-md-3"><input name="code" class="form-control" placeholder="Mã đơn" value="<?= htmlspecialchars($filters['code']) ?>"></div>
    <div class="col-md-3"><input name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($filters['email']) ?>"></div>
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="">-- Trạng thái --</option>
        <?php $statusList=['dathantoan'=>'Đã thanh toán','chothanhtoan'=>'Chờ thanh toán','chuathanhtoan'=>'Chưa thanh toán','chonhantaicuahang'=>'Nhận tại cửa hàng','huy'=>'Hủy'];
        foreach($statusList as $k=>$label): ?>
          <option value="<?= $k ?>" <?= $filters['status']===$k?'selected':'' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3 d-flex">
      <button class="btn btn-primary me-2" type="submit">Lọc</button>
      <a href="<?= APP_URL ?>/AdminOrder/index" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>STT</th><th>Mã</th><th>Email</th><th>Người nhận</th><th>Tổng tiền</th><th>Ngày tạo</th><th>Trạng thái</th><th>Cập nhật</th><th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($orders)): ?>
          <tr><td colspan="9" class="text-center text-muted">Không có đơn phù hợp.</td></tr>
        <?php else: foreach($orders as $index => $o):
          $stt = ($data['offset'] ?? 0) + $index + 1; 
          $info = $o['transaction_info'] ?? ''; $statusTok = trim(explode('|',$info)[0] ?? '');
          // Chuẩn hóa để hiển thị VN
          $norm = strtolower(preg_replace('/[\s_-]+/','',$statusTok));
          $map = [
            'dathantoan' => '<span class="badge bg-success">Đã thanh toán</span>',
            'dathanhtoan' => '<span class="badge bg-success">Đã thanh toán</span>',
            'chothanhtoan' => '<span class="badge bg-warning text-dark">Chờ thanh toán</span>',
            'chuathanhtoan' => '<span class="badge bg-secondary">Chưa thanh toán</span>',
            'chonhantaicuahang' => '<span class="badge bg-info">Nhận tại cửa hàng</span>',
            'huy' => '<span class="badge bg-danger">Hủy</span>',
            'pending' => '<span class="badge bg-warning">Đang xử lý</span>',
            'shipping' => '<span class="badge bg-info">Đang giao</span>',
            'completed' => '<span class="badge bg-success">Hoàn thành</span>',
            'cancelled' => '<span class="badge bg-danger">Đã hủy</span>'
          ];
          $statusBadge = $map[$norm] ?? '<span class="badge bg-secondary">Không xác định</span>';
          ?>
          <tr>
            <td><?= $stt ?></td>
            <td><a href="<?= APP_URL ?>/AdminOrder/detail/<?= urlencode($o['order_code']) ?>" class="text-decoration-none"><?= htmlspecialchars($o['order_code']) ?></a></td>
            <td><?= htmlspecialchars($o['user_email']) ?></td>
            <td><?= htmlspecialchars($o['receiver']) ?></td>
            <td><?= number_format((float)$o['total_amount'],0,',','.') ?> ₫</td>
            <td><?= htmlspecialchars($o['created_at']) ?></td>
            <td><?= $statusBadge ?></td>
            <td>
              <?php if (!in_array($norm, ['dathantoan','dathanhtoan'])): ?>
                <form method="post" action="<?= APP_URL ?>/AdminOrder/updateStatus" class="d-flex align-items-center gap-2">
                  <input type="hidden" name="order_code" value="<?= htmlspecialchars($o['order_code']) ?>">
                  <select name="status" class="form-select form-select-sm" style="width: 170px" onchange="this.form.submit()">
                    <?php foreach($statusList as $k=>$label): ?>
                      <option value="<?= $k ?>" <?= $norm===$k?'selected':'' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                  </select>
                  <noscript><button class="btn btn-sm btn-primary" type="submit">Lưu</button></noscript>
                </form>
              <?php else: ?>
                <span class="text-muted">Không thể sửa</span>
              <?php endif; ?>
            </td>
            <td><a class="btn btn-sm btn-outline-primary" href="<?= APP_URL ?>/AdminOrder/detail/<?= urlencode($o['order_code']) ?>">Chi tiết</a></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  
  <!-- Thanh phân trang -->
  <?php if(($data['totalPages'] ?? 1) > 1): ?>
  <nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
      <li class="page-item <?= ($data['currentPage'] ?? 1) <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= APP_URL ?>/AdminOrder/index?<?= http_build_query(array_merge($filters, ['page' => ($data['currentPage'] ?? 1) - 1])) ?>">Trước</a>
      </li>
      
      <?php
      $currentPage = $data['currentPage'] ?? 1;
      $totalPages = $data['totalPages'] ?? 1;
      $start = max(1, $currentPage - 2);
      $end = min($totalPages, $currentPage + 2);
      for($i = $start; $i <= $end; $i++): 
      ?>
        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
          <a class="page-link" href="<?= APP_URL ?>/AdminOrder/index?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      
      <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= APP_URL ?>/AdminOrder/index?<?= http_build_query(array_merge($filters, ['page' => $currentPage + 1])) ?>">Sau</a>
      </li>
    </ul>
    <div class="text-center text-muted">Trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng: <?= $data['total'] ?? 0 ?> đơn hàng)</div>
  </nav>
  <?php endif; ?>
  
</div>