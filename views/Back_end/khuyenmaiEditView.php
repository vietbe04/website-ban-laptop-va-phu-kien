<?php
/**
 * Sửa khuyến mãi (Admin):
 * - Chọn loại/SP, phần trăm, thời gian; lưu thay đổi.
 * - Lọc danh sách sản phẩm theo loại bằng JS khi thay đổi.
 */
?>
<div class="container mt-4">
    <h2 class="mb-4">Sửa khuyến mãi</h2>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">Sửa khuyến mãi</div>
        <div class="card-body">
            <?php if (empty($data['item'])): ?>
                <div class="alert alert-warning">Không tìm thấy khuyến mãi cần sửa.</div>
            <?php else: ?>
                <form method="post" action="<?= APP_URL ?>/khuyenmai/edit">
                    <input type="hidden" name="km_id" value="<?= htmlspecialchars($data['item']['km_id']) ?>">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Loại sản phẩm</label>
                            <select name="maLoaiSP" id="maLoaiSP_edit" class="form-select">
                                <option value="">-- Chọn loại sản phẩm --</option>
                                <?php foreach ($data['dataView'] as $type): ?>
                                    <option value="<?= htmlspecialchars($type['maLoaiSP']) ?>" <?= $data['item']['maLoaiSP'] == $type['maLoaiSP'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['maLoaiSP']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Sản phẩm</label>
                            <select name="masp" id="masp_edit" class="form-select">
                                <option value="">-- Áp dụng cho tất cả sản phẩm của loại --</option>
                                <?php foreach ($data['products'] as $p): ?>
                                    <option value="<?= htmlspecialchars($p['masp']) ?>" data-loai="<?= htmlspecialchars($p['maLoaiSP']) ?>" <?= $data['item']['masp'] == $p['masp'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['tensp']) ?> (<?= htmlspecialchars($p['masp']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Phần trăm khuyến mãi (%)</label>
                            <input type="number" name="phantram" class="form-control" min="1" max="100" required value="<?= htmlspecialchars($data['item']['phantram']) ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Ngày bắt đầu</label>
                            <input type="date" name="ngaybatdau" class="form-control" required value="<?= htmlspecialchars($data['item']['ngaybatdau']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label>Ngày kết thúc</label>
                            <input type="date" name="ngayketthuc" class="form-control" required value="<?= htmlspecialchars($data['item']['ngayketthuc']) ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                    <a href="<?= APP_URL ?>/khuyenmai/show" class="btn btn-secondary ms-2">Huỷ</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Filter products by category when editing
    document.getElementById('maLoaiSP_edit')?.addEventListener('change', function() {
        const selectedLoai = this.value;
        const maspSelect = document.getElementById('masp_edit');
        Array.from(maspSelect.options).forEach(opt => {
            if (opt.value === "") return;
            opt.hidden = selectedLoai && opt.getAttribute('data-loai') !== selectedLoai;
        });
        // keep previous selection if matches
    });
</script>
