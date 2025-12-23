<?php
/**
 * Form tạo/sửa sản phẩm (Admin).
 * - Khi sửa: hiển thị ảnh hiện tại, mã sản phẩm readonly.
 * - Nhập thông tin cơ bản: loại, mã, tên, số lượng, giá, khuyến mại, ngày tạo, mô tả, ảnh.
 * Bảo mật: dữ liệu đổ ra form dùng htmlspecialchars; số lượng/giá là số.
 */
?>
<?php
// Ensure form posts to explicit front-controller URL so it works without rewrite rules
$formAction = isset($data['editItem'])
    ? APP_URL . '/index.php?url=Product/edit/' . urlencode($data['editItem']['masp'])
    : APP_URL . '/index.php?url=Product/create';
?>
<form action="<?= $formAction ?>" method="post" enctype="multipart/form-data" class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><?php echo isset($data['editItem']) ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới'; ?></h5>
        </div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <?php 
                if (isset($data['editItem']) && $data['editItem']['hinhanh']) {
                    echo "<img src='" . APP_URL . "/public/images/" . $data['editItem']['hinhanh'] . "' 
                          class='img-thumbnail mb-2' style='height: 10rem; width: auto;'>";
                }
                else {?>
                    <img src="<?php echo APP_URL?>/public/images/defaut.png" >
              <?php  }
                ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Mã loại sản phẩm</label>
                <select name="txt_maloaisp" class="form-select">
                    <?php
                    foreach ($data["producttype"] as $k => $v) {
                        $selected = (isset($data['editItem']) && $data['editItem']['maLoaiSP'] == $v["maLoaiSP"]) ? "selected" : "";
                        echo "<option value='{$v["maLoaiSP"]}' $selected>{$v["maLoaiSP"]}</option>";
                    }
                    ?>
                </select>
                <br>
                <label class="form-label">Mã sản phẩm</label>
                <input type="text" name="txt_masp" class="form-control"
                       value="<?php echo isset($data['editItem']) ? $data['editItem']['masp'] : ''; ?>"
                       <?php echo isset($data['editItem']) ? 'readonly' : ''; ?>>
            </div>

            <div class="col-md-6">
                <label class="form-label">Hình ảnh</label><br>
                <input type="file" name="uploadfile" class="form-control">
                <?php if(!isset($data['editItem'])): ?>
                <div class="form-text mt-2">Bạn có thể thêm nhiều ảnh phụ cho sản phẩm ngay khi tạo.</div>
                <label class="form-label mt-2">Ảnh phụ (nhiều ảnh)</label>
                <input type="file" name="extra_images[]" class="form-control" multiple accept="image/*">
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label class="form-label">Tên sản phẩm</label>
                <input type="text" name="txt_tensp" class="form-control"
                       value="<?php echo isset($data['editItem']) ? $data['editItem']['tensp'] : ''; ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Số lượng</label>
                <input type="number" name="txt_soluong" class="form-control"
                       value="<?php echo isset($data['editItem']) ? $data['editItem']['soluong'] : ''; ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Giá nhập</label>
                <input type="number" name="txt_gianhap" class="form-control"
                       value="<?php echo isset($data['editItem']) ? $data['editItem']['giaNhap'] : ''; ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Giá xuất</label>
                <input type="number" name="txt_giaxuat" class="form-control"
                       value="<?php echo isset($data['editItem']) ? $data['editItem']['giaXuat'] : ''; ?>">
            </div>

            

            <div class="col-md-6">
                <label class="form-label">Ngày tạo</label>
                <input type="date" name="create_date" class="form-control"
                       value="<?php echo isset($data['editItem']) ? $data['editItem']['createDate'] : ''; ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Mô tả</label>
                <textarea name="txt_mota" rows="1" class="form-control"><?php echo isset($data['editItem']) ? $data['editItem']['mota'] : ''; ?></textarea>
            </div>
        </div>

        <div class="card-footer text-end">
            <input type="submit" name="btn_submit"
                   class="btn btn-<?php echo isset($data['editItem']) ? 'warning' : 'success'; ?>"
                   value="<?php echo isset($data['editItem']) ? 'Cập nhật' : 'Lưu'; ?>">
        </div>
    </div>
</form>

<?php if (isset($data['editItem'])): $masp = $data['editItem']['masp']; $images = $data['images'] ?? []; ?>
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">Ảnh phụ của sản phẩm</div>
        <div class="card-body">
            <form method="post" action="<?= APP_URL ?>/Product/imageAdd" enctype="multipart/form-data" class="row g-2 align-items-end">
                <input type="hidden" name="masp" value="<?= htmlspecialchars($masp) ?>">
                <div class="col-md-6">
                    <label class="form-label">Chọn ảnh</label>
                    <input type="file" name="image[]" class="form-control" multiple accept="image/*">
                    <div class="form-text">Bạn có thể chọn nhiều ảnh cùng lúc. Nếu chọn "Đặt làm ảnh chính", ảnh đầu tiên sẽ được đặt làm ảnh chính.</div>
                </div>
                <div class="col-md-3 form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_main" id="isMain">
                    <label class="form-check-label" for="isMain">Đặt làm ảnh chính</label>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary">Tải lên</button>
                </div>
            </form>
            <hr>
            <div class="row g-3">
                <?php if(empty($images)): ?>
                    <div class="text-muted">Chưa có ảnh phụ.</div>
                <?php else: foreach($images as $img): ?>
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 text-center">
                            <img src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($img['filename']) ?>" class="img-fluid" style="max-height:140px" alt="">
                            <div class="small mt-2">
                                <?php if((int)$img['is_main']===1): ?>
                                    <span class="badge bg-success">Ảnh chính</span>
                                <?php else: ?>
                                    <form method="post" action="<?= APP_URL ?>/Product/imageSetMain" class="d-inline">
                                        <input type="hidden" name="id" value="<?= (int)$img['id'] ?>">
                                        <input type="hidden" name="masp" value="<?= htmlspecialchars($masp) ?>">
                                        <button class="btn btn-sm btn-outline-primary">Đặt làm chính</button>
                                    </form>
                                <?php endif; ?>
                                <form method="post" action="<?= APP_URL ?>/Product/imageDelete" class="d-inline" onsubmit="return confirm('Xóa ảnh này?')">
                                    <input type="hidden" name="id" value="<?= (int)$img['id'] ?>">
                                    <input type="hidden" name="masp" value="<?= htmlspecialchars($masp) ?>">
                                    <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>