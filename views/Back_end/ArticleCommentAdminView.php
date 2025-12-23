<?php
/**
 * Quản lý bình luận bài viết (Admin)
 * - Tìm kiếm theo tiêu đề bài viết, họ tên, nội dung
 * - Lọc trạng thái (Hiển thị/Ẩn)
 * - Ẩn/Hiện (status), Xóa bình luận
 * - Phân trang
 */
$filters = $data['filters'] ?? ['q'=>'', 'status'=>''];
$comments = $data['comments'] ?? [];
$currentPage = (int)($data['currentPage'] ?? 1);
$totalPages = (int)($data['totalPages'] ?? 1);
$total = (int)($data['total'] ?? 0);
?>
<div class="container mt-4">
  <h2 class="mb-3">Quản lý bình luận bài viết</h2>
  <form class="row g-2 mb-3" method="get" action="<?= APP_URL ?>/ArticleComment/adminIndex">
    <div class="col-md-6">
      <input name="q" class="form-control" placeholder="Tìm theo tiêu đề bài, họ tên, nội dung" value="<?= htmlspecialchars($filters['q']) ?>">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="">-- Trạng thái --</option>
        <option value="1" <?= $filters['status']==='1'?'selected':'' ?>>Hiển thị</option>
        <option value="0" <?= $filters['status']==='0'?'selected':'' ?>>Ẩn</option>
      </select>
    </div>
    <div class="col-md-3 d-flex">
      <button class="btn btn-primary me-2" type="submit">Lọc</button>
      <a class="btn btn-outline-secondary" href="<?= APP_URL ?>/ArticleComment/adminIndex">Reset</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Bài viết</th>
          <th>Người dùng</th>
          <th>Nội dung</th>
          <th>Ngày</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($comments)): ?>
          <tr><td colspan="7" class="text-center text-muted">Không có bình luận phù hợp.</td></tr>
        <?php else: foreach($comments as $c): ?>
          <tr>
            <td><?= (int)$c['id'] ?></td>
            <td>
              <div><strong>#<?= (int)$c['article_id'] ?></strong> - <?= htmlspecialchars($c['article_title'] ?? '') ?></div>
              <a class="small" target="_blank" href="<?= APP_URL ?>/Article/detail/<?= (int)$c['article_id'] ?>">Xem bài</a>
            </td>
            <td><?= htmlspecialchars($c['fullname'] ?? '') ?></td>
            <td style="max-width:420px; white-space:pre-wrap;"><?= htmlspecialchars($c['content'] ?? '') ?></td>
            <td><?= htmlspecialchars($c['created_at'] ?? '') ?></td>
            <td>
              <?php if ((int)($c['status'] ?? 0) === 1): ?>
                <span class="badge bg-success">Hiển thị</span>
              <?php else: ?>
                <span class="badge bg-secondary">Ẩn</span>
              <?php endif; ?>
            </td>
            <td class="d-flex gap-2">
              <form method="post" action="<?= APP_URL ?>/ArticleComment/approve" class="d-inline">
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <input type="hidden" name="status" value="<?= (int)($c['status'] ?? 0)===1?0:1 ?>">
                <button type="submit" class="btn btn-sm <?= (int)($c['status'] ?? 0)===1?'btn-warning':'btn-success' ?>">
                  <?= (int)($c['status'] ?? 0)===1?'Ẩn':'Hiện' ?>
                </button>
              </form>
              <form method="post" action="<?= APP_URL ?>/ArticleComment/delete" onsubmit="return confirm('Xóa bình luận này?')" class="d-inline">
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <?php if($totalPages > 1): ?>
  <nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
      <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= APP_URL ?>/ArticleComment/adminIndex?<?= http_build_query(array_merge($filters, ['page' => $currentPage - 1])) ?>">Trước</a>
      </li>
      <?php
      $start = max(1, $currentPage - 2);
      $end = min($totalPages, $currentPage + 2);
      for($i=$start; $i<=$end; $i++):
      ?>
        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
          <a class="page-link" href="<?= APP_URL ?>/ArticleComment/adminIndex?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= APP_URL ?>/ArticleComment/adminIndex?<?= http_build_query(array_merge($filters, ['page' => $currentPage + 1])) ?>">Sau</a>
      </li>
    </ul>
    <div class="text-center text-muted">Trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng: <?= $total ?> bình luận)</div>
  </nav>
  <?php endif; ?>
</div>
