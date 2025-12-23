<?php
/**
 * Quản lý bài viết: tạo mới / chỉnh sửa.
 * - Nhận $data['article'] khi sửa, hiển thị form với giá trị sẵn có.
 * - Upload ảnh minh hoạ, chọn trạng thái hiển thị.
 * Bảo mật: dữ liệu hiển thị dùng htmlspecialchars; ID ép kiểu số.
 */
$isEdit = !empty($data['article']);
$article = $data['article'] ?? ['title'=>'','content'=>'','image'=>'','status'=>1,'id'=>null];
$action = $isEdit ? APP_URL.'/Article/update' : APP_URL.'/Article/store';
?>
<div class="container mt-4">
  <h2 class="mb-3"><?= $isEdit ? 'Sửa bài viết' : 'Thêm bài viết' ?></h2>
  <form method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?php if($isEdit): ?>
      <input type="hidden" name="id" value="<?= (int)$article['id'] ?>">
      <input type="hidden" name="old_image" value="<?= htmlspecialchars($article['image'] ?? '') ?>">
    <?php endif; ?>
    <div class="mb-3">
      <label class="form-label">Tiêu đề</label>
      <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($article['title']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung</label>
      <textarea name="content" rows="8" class="form-control" required><?= htmlspecialchars($article['content']) ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Ảnh (tải lên từ máy)</label>
      <input type="file" name="image_file" accept="image/*" class="form-control">
      <div class="form-text">Định dạng hỗ trợ: JPG, PNG, GIF. Tối đa ~2MB.</div>
      <?php if($isEdit && !empty($article['image'])): ?>
        <div class="mt-2">
          <div class="text-muted small mb-1">Ảnh hiện tại:</div>
          <img src="<?= APP_URL.'/public/images/'.rawurlencode($article['image']) ?>" alt="current image" style="max-height:120px" onerror="this.style.display='none'">
        </div>
      <?php endif; ?>
    </div>
    <div class="mb-3">
      <label class="form-label">Trạng thái</label>
      <select name="status" class="form-select">
        <option value="1" <?= (int)$article['status']===1?'selected':'' ?>>Hiển thị</option>
        <option value="0" <?= (int)$article['status']===0?'selected':'' ?>>Ẩn</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Lưu</button>
    <a href="<?= APP_URL ?>/Article/admin" class="btn btn-secondary">Hủy</a>
  </form>
</div>
