<?php
/**
 * Sửa mã giảm giá (Admin):
 * - Chỉnh mã, loại, giá trị, thời gian hiệu lực, đơn tối thiểu, giới hạn, trạng thái.
 */
?>
<div class="container mt-4">
    <h2 class="mb-4">Sửa mã giảm giá</h2>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-bold">Sửa mã</div>
        <div class="card-body">
            <?php if (empty($data['item'])): ?>
                <div class="alert alert-warning">Không tìm thấy mã cần sửa.</div>
            <?php else: ?>
                <form method="post" action="<?= APP_URL ?>/coupon/edit">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($data['item']['id']) ?>">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Mã</label>
                            <input name="code" class="form-control" required value="<?= htmlspecialchars($data['item']['code']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label>Loại</label>
                            <select name="type" class="form-select">
                                <option value="percent" <?= $data['item']['type']==='percent' ? 'selected' : '' ?>>Phần trăm (%)</option>
                                <option value="fixed" <?= $data['item']['type']==='fixed' ? 'selected' : '' ?>>Cố định (VNĐ)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Giá trị</label>
                            <input name="value" type="number" class="form-control" required value="<?= htmlspecialchars($data['item']['value']) ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Ngày bắt đầu</label>
                            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($data['item']['start_date']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label>Ngày kết thúc</label>
                            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($data['item']['end_date']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label>Đơn tối thiểu (VNĐ)</label>
                            <input type="number" name="min_total" class="form-control" value="<?= htmlspecialchars($data['item']['min_total']) ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Giới hạn sử dụng</label>
                            <input type="number" name="usage_limit" class="form-control" value="<?= htmlspecialchars($data['item']['usage_limit']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label>Trạng thái</label>
                            <div class="form-check">
                                <input type="checkbox" name="status" class="form-check-input" <?= $data['item']['status'] ? 'checked' : '' ?>>
                                <label class="form-check-label">Kích hoạt</label>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-success">Lưu</button>
                    <a href="<?= APP_URL ?>/coupon/show" class="btn btn-secondary ms-2">Huỷ</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
