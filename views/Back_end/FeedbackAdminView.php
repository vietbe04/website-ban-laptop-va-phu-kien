<?php
$filters = $data['filters'] ?? ['q'=>'','status'=>''];
$feedbacks = $data['feedbacks'] ?? [];
$currentPage = (int)($data['currentPage'] ?? 1);
$totalPages = (int)($data['totalPages'] ?? 1);
$total = (int)($data['total'] ?? 0);
?>
<div class="container mt-4">
	<h2 class="mb-3">Quản lý góp ý người dùng</h2>
	<form class="row g-2 mb-3" method="get" action="<?= APP_URL ?>/Feedback/adminIndex">
		<div class="col-md-6"><input name="q" class="form-control" placeholder="Tìm theo email, tiêu đề, nội dung" value="<?= htmlspecialchars($filters['q']) ?>"></div>
		<div class="col-md-3">
			<select name="status" class="form-select">
				<option value="">-- Trạng thái --</option>
				<option value="0" <?= $filters['status']==='0'?'selected':'' ?>>Chờ xử lý</option>
				<option value="1" <?= $filters['status']==='1'?'selected':'' ?>>Đã trả lời</option>
				<option value="2" <?= $filters['status']==='2'?'selected':'' ?>>Đã đóng</option>
			</select>
		</div>
		<div class="col-md-3 d-flex">
			<button class="btn btn-primary me-2" type="submit">Lọc</button>
			<a class="btn btn-outline-secondary" href="<?= APP_URL ?>/Feedback/adminIndex">Reset</a>
		</div>
	</form>
	<div class="table-responsive">
		<table class="table table-bordered table-hover align-middle">
			<thead class="table-light">
				<tr>
					<th>ID</th><th>Người dùng</th><th>Tiêu đề</th><th>Nội dung</th><th>Trạng thái</th><th>Phản hồi</th><th>Ngày</th><th>Hành động</th>
				</tr>
			</thead>
			<tbody>
				<?php if(empty($feedbacks)): ?>
					<tr><td colspan="8" class="text-center text-muted">Không có góp ý phù hợp.</td></tr>
				<?php else: foreach($feedbacks as $f): ?>
					<tr>
						<td><?= (int)$f['id'] ?></td>
						<td>
							<div class="small text-muted"><?= htmlspecialchars($f['user_email'] ?? '') ?></div>
							<div><?= htmlspecialchars($f['fullname'] ?? '') ?></div>
						</td>
						<td><?= htmlspecialchars($f['subject'] ?? '') ?></td>
						<td style="max-width:360px; white-space:pre-wrap;"><?= htmlspecialchars($f['content'] ?? '') ?></td>
						<td>
							<?php $st = (int)($f['status'] ?? 0);
								echo $st===1 ? '<span class="badge bg-success">Đã trả lời</span>' : ($st===2 ? '<span class="badge bg-secondary">Đã đóng</span>' : '<span class="badge bg-warning text-dark">Chờ xử lý</span>');
							?>
						</td>
						<td style="max-width:360px; white-space:pre-wrap;"><?= htmlspecialchars($f['admin_reply'] ?? '') ?></td>
						<td><?= htmlspecialchars($f['created_at'] ?? '') ?></td>
						<td class="d-flex gap-2">
							<form method="post" action="<?= APP_URL ?>/Feedback/reply" class="d-inline w-100">
								<input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
								<div class="input-group">
									<input name="reply" class="form-control form-control-sm" placeholder="Nhập phản hồi">
									<select name="status" class="form-select form-select-sm" style="max-width:140px">
										<option value="1" selected>Đã trả lời</option>
										<option value="2">Đã đóng</option>
									</select>
									<div class="input-group-text ps-2 pe-2" style="white-space:nowrap">
										<label class="mb-0">
											<input type="checkbox" name="notify" value="1" class="form-check-input me-1"> Gửi email
										</label>
									</div>
									<button class="btn btn-sm btn-success" type="submit">Gửi</button>
								</div>
							</form>
							<form method="post" action="<?= APP_URL ?>/Feedback/delete" class="d-inline" onsubmit="return confirm('Xóa góp ý này?')">
								<input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
								<button class="btn btn-sm btn-danger" type="submit">Xóa</button>
							</form>
						</td>
					</tr>
				<?php endforeach; endif; ?>
			</tbody>
		</table>
	</div>
	<?php if($totalPages > 1): ?>
	<nav aria-label="Page navigation" class="mt-3">
		<ul class="pagination justify-content-center">
			<li class="page-item <?= $currentPage<=1?'disabled':'' ?>">
				<a class="page-link" href="<?= APP_URL ?>/Feedback/adminIndex?<?= http_build_query(array_merge($filters, ['page' => max(1, $currentPage - 1)])) ?>">Trước</a>
			</li>
			<li class="page-item <?= $currentPage>=$totalPages?'disabled':'' ?>">
				<a class="page-link" href="<?= APP_URL ?>/Feedback/adminIndex?<?= http_build_query(array_merge($filters, ['page' => min($totalPages, $currentPage + 1)])) ?>">Sau</a>
			</li>
		</ul>
		<div class="text-center text-muted">Trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng: <?= $total ?> góp ý)</div>
	</nav>
	<?php endif; ?>
</div>
