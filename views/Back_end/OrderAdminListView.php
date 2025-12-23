<?php
/** @var array $data */
if (!isset($data) || !is_array($data)) { $data = []; }
$filters = isset($data['filters']) && is_array($data['filters']) ? $data['filters'] : ['code'=>'','email'=>'','status'=>''];
$orders  = isset($data['orders']) && is_array($data['orders']) ? $data['orders'] : [];
?>
<div class="container mt-4">
	<h2 class="mb-3">Quản lý đơn hàng</h2>
	<form class="row g-2 mb-3" method="get" action="<?= APP_URL ?>/AdminOrder/index">
		<div class="col-md-3"><input name="code" class="form-control" placeholder="Mã đơn" value="<?= htmlspecialchars($filters['code'] ?? '') ?>"></div>
		<div class="col-md-3"><input name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($filters['email'] ?? '') ?>"></div>
		<div class="col-md-3">
			<select name="status" class="form-select">
				<option value="">-- Trạng thái --</option>
				<?php $statusList=['dathantoan'=>'Đã thanh toán','chothanhtoan'=>'Chờ thanh toán','chuathanhtoan'=>'Chưa thanh toán','huy'=>'Hủy'];
				foreach($statusList as $k=>$label): ?>
					<option value="<?= $k ?>" <?= (($filters['status'] ?? '')===$k)?'selected':'' ?>><?= $label ?></option>
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
					<th>STT</th><th>Mã</th><th>Email</th><th>Người nhận</th><th>Tổng tiền</th><th>Ngày tạo</th><th>Trạng thái</th><th>Hình thức giao hàng</th><th>Hành động</th>
				</tr>
			</thead>
			<tbody>
				<?php if(empty($orders)): ?>
					<tr><td colspan="9" class="text-center text-muted">Không có đơn phù hợp.</td></tr>
				<?php else: $i = 1; foreach($orders as $o): 
					$info = $o['transaction_info'] ?? ''; $statusTok = explode('|',$info)[0];
					// Parse shipping method
					$shippingMethod = '';
					if (!empty($info)) {
						$parts = explode('|', $info);
						array_shift($parts);
						foreach ($parts as $tok) {
							$tok = trim($tok);
							if (stripos($tok, 'shipping:') === 0) {
								if (preg_match('/^shipping:(.*?)\(/i', $tok, $m)) {
									$rawMethod = trim($m[1]);
									if (stripos($rawMethod, 'nhanh') !== false) {
										$shippingMethod = 'Giao hàng nhanh';
									} elseif (stripos($rawMethod, 'tiêu chuẩn') !== false) {
										$shippingMethod = 'Giao hàng tiêu chuẩn';
									} else {
										$shippingMethod = $rawMethod;
									}
								}
							} elseif (stripos($tok, 'pickup:') === 0) {
								$shippingMethod = 'Nhận tại cửa hàng';
							}
						}
					}
					if ($shippingMethod == '') {
						$norm = strtolower(preg_replace('/[\s_-]+/','', $statusTok));
						if ($norm == 'chonhantaicuahang') {
							$shippingMethod = 'Nhận tại cửa hàng';
						} else {
							$shippingMethod = 'Giao hàng tiêu chuẩn';
						}
					}
					?>
					<tr>
						<td><?= $i + ($data['offset'] ?? 0) ?></td>
						<td><a href="<?= APP_URL ?>/AdminOrder/detail/<?= urlencode($o['order_code']) ?>" class="text-decoration-none"><?= htmlspecialchars($o['order_code']) ?></a></td>
						<td><?= htmlspecialchars($o['user_email']) ?></td>
						<td><?= htmlspecialchars($o['receiver']) ?></td>
						<td><?= number_format((float)$o['total_amount'],0,',','.') ?> ₫</td>
						<td><?= htmlspecialchars($o['created_at']) ?></td>
						<td><span class="badge bg-info text-dark text-uppercase"><?= htmlspecialchars($statusTok) ?></span></td>
						<td><?= htmlspecialchars($shippingMethod) ?></td>
						<td><a class="btn btn-sm btn-outline-primary" href="<?= APP_URL ?>/AdminOrder/detail/<?= urlencode($o['order_code']) ?>">Chi tiết</a></td>
					</tr>
				<?php $i++; endforeach; endif; ?>
			</tbody>
		</table>
	</div>
	
	<!-- Pagination -->
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