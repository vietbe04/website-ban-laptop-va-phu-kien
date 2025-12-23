<?php
$banners = $data['banners'] ?? [];
$total = $data['total'] ?? 0;
?>

<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-image"></i> Quản lý Banner</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
      <i class="bi bi-cloud-upload"></i> Upload Banner
    </button>
  </div>

  <?php if (isset($_SESSION['flash_message'])): ?>
  <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
    <?= htmlspecialchars($_SESSION['flash_message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); endif; ?>

  <?php if (isset($_SESSION['flash_errors'])): ?>
  <div class="alert alert-warning alert-dismissible fade show">
    <strong>Một số lỗi xảy ra:</strong>
    <ul class="mb-0 mt-2">
      <?php foreach ($_SESSION['flash_errors'] as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash_errors']); endif; ?>

  <!-- Statistics -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <h5><i class="bi bi-images"></i> Tổng Banner</h5>
          <h2><?= $total ?></h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Actions Bar -->
  <div class="card mb-3">
    <div class="card-body">
      <form method="post" action="<?= APP_URL ?>/Banner/deleteMultiple" onsubmit="return confirmDelete()">
        <div class="d-flex gap-2">
          <button type="button" id="selectAllBtn" class="btn btn-outline-secondary" onclick="toggleSelectAll()">
            <i class="bi bi-check-square"></i> Chọn tất cả
          </button>
          <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash"></i> Xóa đã chọn
          </button>
        </div>
        
        <!-- Banner Grid -->
        <div class="row g-3 mt-3">
          <?php if (empty($banners)): ?>
            <div class="col-12">
              <div class="alert alert-info text-center">
                <i class="bi bi-info-circle fs-1"></i>
                <p class="mt-2">Chưa có banner nào. Hãy upload banner mới!</p>
              </div>
            </div>
          <?php else: foreach($banners as $banner): ?>
            <div class="col-md-4 col-lg-3">
              <div class="card h-100 banner-card">
                <div class="position-relative">
                  <input type="checkbox" name="selected_banners[]" value="<?= htmlspecialchars($banner['name']) ?>" 
                         class="position-absolute top-0 start-0 m-2 form-check-input banner-checkbox" 
                         style="z-index: 10; width: 25px; height: 25px;">
                  <img src="<?= $banner['url'] ?>" class="card-img-top" alt="<?= htmlspecialchars($banner['name']) ?>"
                       style="height: 200px; object-fit: cover; cursor: pointer;"
                       onclick="previewImage('<?= $banner['url'] ?>')">
                </div>
                <div class="card-body">
                  <h6 class="card-title text-truncate" title="<?= htmlspecialchars($banner['name']) ?>">
                    <?= htmlspecialchars($banner['name']) ?>
                  </h6>
                  <p class="card-text small text-muted mb-2">
                    <i class="bi bi-file-earmark"></i> <?= number_format($banner['size'] / 1024, 2) ?> KB<br>
                    <i class="bi bi-clock"></i> <?= date('d/m/Y H:i', $banner['modified']) ?>
                  </p>
                  <div class="d-flex gap-2">
                    <a href="<?= $banner['url'] ?>" target="_blank" class="btn btn-sm btn-info flex-fill" title="Xem ảnh">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="<?= APP_URL ?>/Banner/delete/<?= urlencode($banner['name']) ?>" 
                       class="btn btn-sm btn-danger flex-fill"
                       onclick="return confirm('Xóa banner này?')" title="Xóa">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-cloud-upload"></i> Upload Banner</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="<?= APP_URL ?>/Banner/upload" enctype="multipart/form-data" id="uploadForm">
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> 
            <strong>Lưu ý:</strong> Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP). Kích thước tối đa: 5MB/file. 
            Bạn có thể chọn nhiều ảnh cùng lúc.
          </div>
          
          <div class="mb-3">
            <label class="form-label">Chọn ảnh banner <span class="text-danger">*</span></label>
            <input type="file" name="banners[]" class="form-control" id="bannerInput" 
                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" 
                   multiple required>
            <div class="form-text">Có thể chọn nhiều file cùng lúc (Ctrl/Cmd + Click)</div>
          </div>

          <!-- Preview area -->
          <div id="previewArea" class="row g-2"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-primary" id="uploadBtn">
            <i class="bi bi-upload"></i> Upload
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Xem trước Banner</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="previewImage" src="" class="img-fluid" style="max-height: 80vh;">
      </div>
    </div>
  </div>
</div>

<style>
.banner-card {
  transition: transform 0.2s, box-shadow 0.2s;
}
.banner-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.banner-checkbox {
  cursor: pointer;
  background: white;
  border: 2px solid #333;
}
.preview-item {
  position: relative;
}
.preview-item img {
  width: 100%;
  height: 150px;
  object-fit: cover;
  border-radius: 5px;
}
.preview-item .remove-preview {
  position: absolute;
  top: 5px;
  right: 5px;
  background: rgba(220, 53, 69, 0.9);
  color: white;
  border: none;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>

<script>
// Preview images before upload
document.getElementById('bannerInput').addEventListener('change', function(e) {
  const previewArea = document.getElementById('previewArea');
  previewArea.innerHTML = '';
  
  const files = Array.from(e.target.files);
  
  files.forEach((file, index) => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const col = document.createElement('div');
        col.className = 'col-md-3';
        col.innerHTML = `
          <div class="preview-item">
            <img src="${e.target.result}" alt="Preview">
            <div class="small text-center mt-1 text-truncate">${file.name}</div>
            <div class="small text-muted text-center">${(file.size / 1024).toFixed(2)} KB</div>
          </div>
        `;
        previewArea.appendChild(col);
      };
      reader.readAsDataURL(file);
    }
  });
  
  if (files.length > 0) {
    document.getElementById('uploadBtn').innerHTML = 
      `<i class="bi bi-upload"></i> Upload ${files.length} ảnh`;
  }
});

// Select all checkboxes
let allSelected = false;
function toggleSelectAll() {
  allSelected = !allSelected;
  const checkboxes = document.querySelectorAll('.banner-checkbox');
  checkboxes.forEach(cb => cb.checked = allSelected);
  document.getElementById('selectAllBtn').innerHTML = allSelected 
    ? '<i class="bi bi-square"></i> Bỏ chọn tất cả'
    : '<i class="bi bi-check-square"></i> Chọn tất cả';
}

// Confirm delete
function confirmDelete() {
  const checked = document.querySelectorAll('.banner-checkbox:checked');
  if (checked.length === 0) {
    alert('Vui lòng chọn ít nhất một banner để xóa!');
    return false;
  }
  return confirm(`Bạn có chắc muốn xóa ${checked.length} banner đã chọn?`);
}

// Preview image in modal
function previewImage(url) {
  document.getElementById('previewImage').src = url;
  new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
}

// Reset form when modal closes
document.getElementById('uploadModal').addEventListener('hidden.bs.modal', function() {
  document.getElementById('uploadForm').reset();
  document.getElementById('previewArea').innerHTML = '';
  document.getElementById('uploadBtn').innerHTML = '<i class="bi bi-upload"></i> Upload';
});
</script>
