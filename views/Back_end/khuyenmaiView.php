<?php
/**
 * Quản lý khuyến mãi (Admin):
 * - Thêm khuyến mãi theo loại hoặc sản phẩm cụ thể.
 * - Danh sách khuyến mãi hiện hành, sửa/xoá.
 */
?>
<div class="container mt-4">
    <h2 class="mb-4">Quản lý khuyến mãi</h2>


    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">
            Thêm khuyến mãi mới
        </div>
        <div class="card-body">
            <form method="post" action="<?= APP_URL ?>/khuyenmai/show">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Loại sản phẩm</label>
                        <select name="maLoaiSP" id="maLoaiSP" class="form-select">
                            <option value="">-- Chọn loại sản phẩm --</option>
                            <?php foreach ($data["dataView"] as $type): ?>
                                <option value="<?= htmlspecialchars($type["maLoaiSP"]) ?>">
                                    <?= htmlspecialchars($type["maLoaiSP"]) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Sản phẩm</label>
                        <select name="masp" id="masp" class="form-select">
                            <option value="">-- Áp dụng cho tất cả sản phẩm của loại --</option>
                            <?php foreach ($data["products"] as $p): ?>
                                <option value="<?= htmlspecialchars($p["masp"]) ?>" data-loai="<?= htmlspecialchars($p["maLoaiSP"]) ?>">
                                    <?= htmlspecialchars($p["tensp"]) ?> (<?= htmlspecialchars($p["masp"]) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <script>
                        document.getElementById('maLoaiSP').addEventListener('change', function() {
                            const selectedLoai = this.value;
                            const maspSelect = document.getElementById('masp');
                            Array.from(maspSelect.options).forEach(opt => {
                                if (opt.value === "") return; // giữ dòng đầu tiên
                                opt.hidden = selectedLoai && opt.getAttribute('data-loai') !== selectedLoai;
                            });
                            maspSelect.value = "";
                        });
                    </script>

                    <div class="col-md-4">
                        <label>Phần trăm khuyến mãi (%)</label>
                        <input type="number" name="phantram" class="form-control" min="1" max="100" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Ngày bắt đầu</label>
                        <input type="date" name="ngaybatdau" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Ngày kết thúc</label>
                        <input type="date" name="ngayketthuc" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">+ Lưu khuyến mãi</button>
            </form>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white fw-bold">
            Danh sách khuyến mãi hiện tại
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Loại sản phẩm</th>

                        <th>Tên sản phẩm</th>
                        <th>Giá gốc</th>
                        <th>Phần trăm (%)</th>
                        <th>Từ ngày</th>
                        <th>Đến ngày</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stt = 1;
                    foreach ($data["productList"] as $row): ?>
                        <tr>
                            <td><?= $stt++ ?></td>
                            <td><?= htmlspecialchars($row["maLoaiSP"]) ?></td>
                            <td><?= htmlspecialchars($row["tensp"]) ?: '<span class="text-muted">Tất cả sản phẩm</span>' ?></td>
                            <td>
                                <?php if (empty($row["tensp"])): ?>
                                    <span class="text-muted">Nhiều sản phẩm</span>
                                <?php else: ?>
                                    <?= number_format($row["giaXuat"], 0, ',', '.') ?> ₫
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($row["phantram"]) ?>%</td>
                            <td><?= htmlspecialchars($row["ngaybatdau"]) ?></td>
                            <td><?= htmlspecialchars($row["ngayketthuc"]) ?></td>
                            <td>
                                <a href="<?= APP_URL ?>/khuyenmai/edit/<?= urlencode($row["km_id"]) ?>" class="btn btn-primary btn-sm me-1">Sửa</a>
                                <a href="<?= APP_URL ?>/khuyenmai/delete/<?= urlencode($row["km_id"]) ?>"
                                    onclick="return confirm('Xóa khuyến mãi này?')"
                                    class="btn btn-danger btn-sm">Xoá</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data["productList"])): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Chưa có khuyến mãi nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>