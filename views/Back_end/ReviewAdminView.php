<?php
/**
 * Quản lý đánh giá sản phẩm (Admin):
 * - Lọc theo mã SP, số sao, trạng thái duyệt.
 * - Duyệt/ẩn, xoá đánh giá.
 * - Phân trang giữ tham số lọc.
 */
$filters = $data['filters'] ?? ['product_id'=>'','rating'=>'','approved'=>''];
$reviews = $data['reviews'] ?? [];
$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$total = $data['total'] ?? 0;
?>
<div class="container mt-4">
  <h2 class="mb-3">Quản lý đánh giá sản phẩm</h2>
  <form class="row g-2 mb-3" method="get" action="<?= APP_URL ?>/Review/adminIndex">
    <div class="col-md-4"><input name="product_id" class="form-control" placeholder="Mã sản phẩm" value="<?= htmlspecialchars($filters['product_id']) ?>"></div>
    <div class="col-md-3">
      <select name="rating" class="form-select">
        <option value="">-- Số sao --</option>
        <?php for($i=1;$i<=5;$i++): ?>
          <option value="<?= $i ?>" <?= (string)$filters['rating']===(string)$i?'selected':'' ?>><?= $i ?> sao</option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="approved" class="form-select">
        <option value="">-- Trạng thái --</option>
        <option value="1" <?= $filters['approved']==='1'?'selected':'' ?>>Hiển thị</option>
        <option value="0" <?= $filters['approved']==='0'?'selected':'' ?>>Ẩn</option>
      </select>
    </div>
    <div class="col-md-2 d-flex">
      <button class="btn btn-primary me-2" type="submit">Lọc</button>
      <a class="btn btn-outline-secondary" href="<?= APP_URL ?>/Review/adminIndex">Reset</a>
    </div>
  </form>
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th><th>Sản phẩm</th><th>Người dùng</th><th>Số sao</th><th>Nội dung</th><th>Hình ảnh</th><th>Ngày</th><th>Trạng thái</th><th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($reviews)): ?>
          <tr><td colspan="9" class="text-center text-muted">Không có đánh giá phù hợp.</td></tr>
        <?php else: foreach($reviews as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['product_id']) ?></td>
            <td><strong><?= htmlspecialchars($r['fullname']) ?></strong><br><small><?= htmlspecialchars($r['email']) ?></small></td>
            <td>
              <div class="text-warning">
                <?php for($i=0; $i<(int)$r['rating']; $i++): ?>
                  <i class="bi bi-star-fill"></i>
                <?php endfor; ?>
                <?php for($i=(int)$r['rating']; $i<5; $i++): ?>
                  <i class="bi bi-star"></i>
                <?php endfor; ?>
              </div>
              <small class="text-muted"><?= (int)$r['rating'] ?>/5</small>
            </td>
            <td style="max-width:320px; white-space:pre-wrap;"><?= htmlspecialchars($r['comment']) ?></td>
            <td>
              <?php 
              if (!empty($r['images'])) {
                $images = json_decode($r['images'], true);
                if (is_array($images) && count($images) > 0):
              ?>
                <div class="review-images-admin">
                  <?php foreach ($images as $index => $img): ?>
                    <?php if ($index < 3): ?>
                      <div class="review-image-thumb">
                        <img src="<?= APP_URL ?>/public/images/reviews/<?= htmlspecialchars($img) ?>" 
                             alt="Review" 
                             onclick="openImageModal('<?= APP_URL ?>/public/images/reviews/<?= htmlspecialchars($img) ?>')">
                      </div>
                    <?php endif; ?>
                  <?php endforeach; ?>
                  <?php if (count($images) > 3): ?>
                    <div class="more-images" onclick="showAllImages(<?= htmlspecialchars(json_encode($images)) ?>, '<?= APP_URL ?>')">
                      +<?= count($images) - 3 ?>
                    </div>
                  <?php endif; ?>
                </div>
              <?php 
                endif;
              } else {
                echo '<span class="text-muted"><i class="bi bi-image"></i> Không có</span>';
              }
              ?>
            </td>
            <td><small><?= htmlspecialchars(date('d/m/Y H:i', strtotime($r['created_at']))) ?></small></td>
            <td>
              <?php if((int)$r['approved']===1): ?>
                <span class="badge bg-success">Hiển thị</span>
              <?php else: ?>
                <span class="badge bg-secondary">Ẩn</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="d-flex gap-1">
                <form method="post" action="<?= APP_URL ?>/Review/approve" class="d-inline">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <input type="hidden" name="approved" value="<?= (int)$r['approved']===1?0:1 ?>">
                  <button type="submit" class="btn btn-sm <?= (int)$r['approved']===1?'btn-warning':'btn-success' ?>" title="<?= (int)$r['approved']===1?'Ẩn đánh giá':'Duyệt đánh giá' ?>">
                    <i class="bi bi-<?= (int)$r['approved']===1?'eye-slash':'check-circle' ?>"></i>
                  </button>
                </form>
                <form method="post" action="<?= APP_URL ?>/Review/delete" onsubmit="return confirm('Xóa đánh giá này?')" class="d-inline">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger" title="Xóa đánh giá">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
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
        <a class="page-link" href="<?= APP_URL ?>/Review/adminIndex?<?= http_build_query(array_merge($filters, ['page' => $currentPage - 1])) ?>">Trước</a>
      </li>
      
      <?php
      $start = max(1, $currentPage - 2);
      $end = min($totalPages, $currentPage + 2);
      for($i = $start; $i <= $end; $i++): 
      ?>
        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
          <a class="page-link" href="<?= APP_URL ?>/Review/adminIndex?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      
      <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= APP_URL ?>/Review/adminIndex?<?= http_build_query(array_merge($filters, ['page' => $currentPage + 1])) ?>">Sau</a>
      </li>
    </ul>
    <div class="text-center text-muted">Trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng: <?= $total ?> đánh giá)</div>
  </nav>
  <?php endif; ?>
</div>

<!-- Modal xem ảnh -->
<div id="imageModal" class="image-modal-admin" style="display: none;">
  <div class="modal-backdrop-admin" onclick="closeImageModal()"></div>
  <div class="modal-content-admin">
    <button class="modal-close-admin" onclick="closeImageModal()">
      <i class="bi bi-x-lg"></i>
    </button>
    <img id="modalImage" src="" alt="Review image">
  </div>
</div>

<!-- Modal xem tất cả ảnh -->
<div id="allImagesModal" class="image-modal-admin" style="display: none;">
  <div class="modal-backdrop-admin" onclick="closeAllImagesModal()"></div>
  <div class="modal-content-admin all-images-view">
    <button class="modal-close-admin" onclick="closeAllImagesModal()">
      <i class="bi bi-x-lg"></i>
    </button>
    <h5 style="color: white; margin-bottom: 20px; text-align: center;">Tất cả hình ảnh đánh giá</h5>
    <div id="allImagesGrid" class="all-images-grid"></div>
  </div>
</div>

<style>
/* Review Images Thumbnails */
.review-images-admin {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  align-items: center;
}

.review-image-thumb {
  width: 60px;
  height: 60px;
  border-radius: 6px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  position: relative;
}

.review-image-thumb:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  z-index: 10;
}

.review-image-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.more-images {
  width: 60px;
  height: 60px;
  border-radius: 6px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 14px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  cursor: pointer;
  transition: all 0.3s ease;
}

.more-images:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
}

/* Image Modal */
.image-modal-admin {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-backdrop-admin {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.85);
  backdrop-filter: blur(5px);
}

.modal-content-admin {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
  z-index: 10;
  animation: zoomIn 0.3s ease;
}

.modal-content-admin img {
  width: 100%;
  height: 100%;
  max-height: 90vh;
  object-fit: contain;
  border-radius: 8px;
}

.modal-close-admin {
  position: absolute;
  top: -40px;
  right: 0;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #333;
  font-size: 18px;
}

.modal-close-admin:hover {
  background: white;
  transform: rotate(90deg);
}

@keyframes zoomIn {
  from {
    transform: scale(0.8);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

@media (max-width: 768px) {
  .modal-close-admin {
    top: 10px;
    right: 10px;
  }
}

/* All Images Grid Modal */
.all-images-view {
  max-width: 1000px !important;
  max-height: 80vh;
  overflow-y: auto;
  padding: 20px;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(10px);
  border-radius: 12px;
}

.all-images-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 15px;
  margin-top: 20px;
}

.all-images-grid .grid-image-item {
  position: relative;
  width: 100%;
  padding-bottom: 100%;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.all-images-grid .grid-image-item:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 12px rgba(255,255,255,0.3);
}

.all-images-grid .grid-image-item img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

</style>

<script>
function openImageModal(src) {
  const modal = document.getElementById('imageModal');
  const modalImage = document.getElementById('modalImage');
  modalImage.src = src;
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeImageModal() {
  const modal = document.getElementById('imageModal');
  modal.style.display = 'none';
  document.body.style.overflow = 'auto';
}

function showAllImages(images, appUrl) {
  const modal = document.getElementById('allImagesModal');
  const grid = document.getElementById('allImagesGrid');
  
  grid.innerHTML = '';
  
  images.forEach(img => {
    const div = document.createElement('div');
    div.className = 'grid-image-item';
    div.innerHTML = `<img src="${appUrl}/public/images/reviews/${img}" alt="Review image">`;
    div.onclick = () => {
      openImageModal(`${appUrl}/public/images/reviews/${img}`);
      closeAllImagesModal();
    };
    grid.appendChild(div);
  });
  
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeAllImagesModal() {
  const modal = document.getElementById('allImagesModal');
  modal.style.display = 'none';
  document.body.style.overflow = 'auto';
}

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeImageModal();
    closeAllImagesModal();
  }
});
</script>