<?php
/**
 * Danh sách bài viết (Admin).
 * - Hiển thị bảng bài viết với ảnh, trạng thái, ngày tạo.
 * - Hành động: tạo mới, sửa, xoá.
 * - Phân trang đơn giản, bảo toàn trang hiện tại.
 */
$articles = $data['articles'] ?? [];
$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$total = $data['total'] ?? 0;
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý bài viết</h2>
    <a href="<?= APP_URL ?>/Article/create" class="btn btn-primary">+ Thêm bài viết</a>
  </div>
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th><th>Tiêu đề</th><th>Hình</th><th>Trạng thái</th><th>Ngày tạo</th><th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($articles)): ?>
          <tr><td colspan="6" class="text-center text-muted">Chưa có bài viết.</td></tr>
        <?php else: foreach($articles as $a): ?>
          <tr>
            <td><?= (int)$a['id'] ?></td>
            <td><?= htmlspecialchars($a['title']) ?></td>
            <td>
              <?php $img = $a['image'] ?? ''; $url = APP_URL.'/public/images/'.rawurlencode($img); ?>
              <?php if($img): ?><img src="<?= $url ?>" alt="" width="80" onerror="this.style.display='none'"><?php endif; ?>
            </td>
            <td><?= (int)$a['status']===1?'<span class="badge bg-success">Hiển thị</span>':'<span class="badge bg-secondary">Ẩn</span>' ?></td>
            <td><?= htmlspecialchars($a['created_at']) ?></td>
            <td class="d-flex gap-2">
              <a href="<?= APP_URL ?>/Article/edit/<?= (int)$a['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
              <form method="post" action="<?= APP_URL ?>/Article/delete" onsubmit="return confirm('Xóa bài viết này?')">
                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                <button class="btn btn-sm btn-danger" type="submit">Xóa</button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  
  <!-- Pagination -->
  <?php if($totalPages > 1): ?>
  <nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
      <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= APP_URL ?>/Article/admin?page=<?= $currentPage - 1 ?>">Trước</a>
      </li>
      
      <?php
      $start = max(1, $currentPage - 2);
      $end = min($totalPages, $currentPage + 2);
      for($i = $start; $i <= $end; $i++): 
      ?>
        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
          <a class="page-link" href="<?= APP_URL ?>/Article/admin?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      
      <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= APP_URL ?>/Article/admin?page=<?= $currentPage + 1 ?>">Sau</a>
      </li>
    </ul>
    <div class="text-center text-muted">Trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng: <?= $total ?> bài viết)</div>
  </nav>
  <?php endif; ?>
</div>