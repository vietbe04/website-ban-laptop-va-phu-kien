<?php
/**
 * Quản lý mã giảm giá (Admin):
 * - Thêm mã: loại %, cố định, giá trị, thời gian, giới hạn, trạng thái.
 * - Danh sách mã kèm trạng thái, số lần sử dụng, thao tác sửa/xoá.
 */
?>
<div class="container mt-4">
    <h2 class="mb-4">Quản lý mã giảm giá (Coupon)</h2>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-bold">Thêm mã giảm giá mới</div>
        <div class="card-body">
            <form method="post" action="<?= APP_URL ?>/coupon/show">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Mã</label>
                        <input name="code" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>Loại</label>
                        <select name="type" class="form-select">
                            <option value="percent">Phần trăm (%)</option>
                            <option value="fixed">Cố định (VNĐ)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Giá trị</label>
                        <input name="value" type="number" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>Trạng thái</label>
                        <div class="form-check">
                            <input type="checkbox" name="status" class="form-check-input" checked>
                            <label class="form-check-label">Kích hoạt</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Ngày kết thúc</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Đơn tối thiểu (VNĐ)</label>
                        <input type="number" name="min_total" class="form-control" placeholder="(không bắt buộc)">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Giới hạn sử dụng</label>
                        <input type="number" name="usage_limit" class="form-control" placeholder="(không bắt buộc)">
                    </div>
                </div>

                <button class="btn btn-success">Lưu mã</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white fw-bold">Danh sách mã</div>
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Mã</th>
                        <th>Loại</th>
                        <th>Giá trị</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Giới hạn</th>
                        <th>Sử dụng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=1; foreach ($data['coupons'] as $c): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($c['code']) ?></td>
                            <td><?= htmlspecialchars($c['type']) ?></td>
                            <td><?= htmlspecialchars($c['value']) ?><?= $c['type']==='percent' ? '%' : ' ₫' ?></td>
                            <td><?= htmlspecialchars($c['start_date']) ?> - <?= htmlspecialchars($c['end_date']) ?></td>
                            <td><?= $c['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
                            <td><?= htmlspecialchars($c['usage_limit'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($c['used_count']) ?></td>
                            <td>
                                <a href="<?= APP_URL ?>/coupon/edit/<?= $c['id'] ?>" class="btn btn-primary btn-sm me-1">Sửa</a>
                                <a href="<?= APP_URL ?>/coupon/delete/<?= $c['id'] ?>" onclick="return confirm('Xóa mã này?')" class="btn btn-danger btn-sm">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data['coupons'])): ?>
                        <tr><td colspan="9" class="text-center text-muted">Chưa có mã nào</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
