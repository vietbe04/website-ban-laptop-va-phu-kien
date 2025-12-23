<?php
/**
 * Quản lý loại sản phẩm (Admin).
 * - Form thêm/sửa nhanh trên cùng trang.
 * - Danh sách loại với phân trang.
 * Bảo mật: dữ liệu hiển thị qua htmlspecialchars; mã loại readonly khi sửa.
 */
?>
<div class="container mt-5">
    <h2 class="mb-4">📦 Quản lý danh mục loại sản phẩm</h2>
    <?php
        // Form thêm / sửa luôn hiển thị để quản trị có thể thêm loại mới bất kể danh sách có rỗng hay không
        $isEdit = isset($data['editItem']); $edit = $isEdit ? $data['editItem'] : null;
    ?>

    <div class="mb-3">
        <form
            action="<?= $isEdit ? APP_URL . '/index.php?url=ProductType/update/' . $edit["maLoaiSP"] : APP_URL . '/index.php?url=ProductType/create' ?>"
            method="post"
            class="bg-light p-3 rounded shadow-sm">
            <div class="row align-items-end gx-3 gy-2">
                <div class="col-md-3">
                    <label for="txt_maloaisp" class="form-label">Mã loại SP</label>
                    <input type="text" name="txt_maloaisp" id="txt_maloaisp" class="form-control"
                        required value="<?= $isEdit ? htmlspecialchars($edit["maLoaiSP"]) : '' ?>"
                        <?= $isEdit ? 'readonly' : '' ?> />
                </div>

                <div class="col-md-3">
                    <label for="txt_tenloaisp" class="form-label">Tên loại SP</label>
                    <input type="text"
                        name="txt_tenloaisp"
                        id="txt_tenloaisp"
                        class="form-control"
                        value="<?= $isEdit ? htmlspecialchars($edit["tenLoaiSP"]) : '' ?>" />
                </div>

                <div class="col-md-3">
                    <label for="txt_motaloaisp" class="form-label">Mô tả</label>
                    <input type="text"
                        name="txt_motaloaisp"
                        id="txt_motaloaisp"
                        class="form-control"
                        value="<?= $isEdit ? htmlspecialchars($edit["moTaLoaiSP"]) : '' ?>" />
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-<?= $isEdit ? 'warning' : 'primary' ?>">
                            💾 <?= $isEdit ? "Cập nhật" : "Thêm mới" ?>
                        </button>
                        <?php if ($isEdit): ?>
                            <a href="<?= APP_URL ?>/ProductType" class="btn btn-secondary">
                                🔁 Huỷ
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Search/filter -->
    <div class="mb-3">
        <form method="get" action="<?= APP_URL ?>/index.php" class="row g-2 align-items-center">
            <input type="hidden" name="url" value="ProductType/show">
            <div class="col-auto">
                <label class="form-label">Tìm mã/tên loại</label>
            </div>
            <div class="col-auto">
                <input type="text" name="q" class="form-control" placeholder="Nhập mã hoặc tên loại" value="<?= htmlspecialchars($data['currentQuery'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Tìm</button>
                <a href="<?= APP_URL ?>/index.php?url=ProductType/show" class="btn btn-secondary">Xóa</a>
            </div>
        </form>
    </div>

    <?php if (!empty($data["productList"])): ?>
        <table class="table table-bordered table-hover">
            <tr>
                <th>STT</th>
                <th>Mã loại SP</th>
                <th>Tên loại SP</th>
                <th>Mô tả</th>
                <th>Hành động</th>
            </tr>
            <?php
            $offset = $data['offset'] ?? 0;
            foreach ($data["productList"] as $k => $v): ?>
                <tr>
                    <td><?= $offset + $k + 1 ?></td>
                    <td><?= htmlspecialchars($v["maLoaiSP"]) ?></td>
                    <td><?= htmlspecialchars($v["tenLoaiSP"]) ?> </td>
                    <td><?= htmlspecialchars($v["moTaLoaiSP"]) ?></td>
                    <td>
                        <a href="<?= APP_URL ?>/index.php?url=ProductType/edit/<?= $v["maLoaiSP"] ?>" class="btn btn-warning btn-sm">✏️ Sửa</a>
                        <a href="<?= APP_URL ?>/index.php?url=ProductType/delete/<?= $v["maLoaiSP"] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Bạn có chắc muốn xoá sản phẩm này?');">🗑️ Xoá</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

                <?php if(($data['totalPages'] ?? 1) > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                            <?php
                            $currentPage = $data['currentPage'] ?? 1;
                            $totalPages = $data['totalPages'] ?? 1;
                            $qParam = isset($data['currentQuery']) && $data['currentQuery'] !== '' ? '&q=' . urlencode($data['currentQuery']) : '';
                            ?>
                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= APP_URL ?>/index.php?url=ProductType/show&page=<?= max(1, $currentPage - 1) ?><?= $qParam ?>">Trước</a>
                            </li>
                            <?php
                            $start = max(1, $currentPage - 2);
                            $end = min($totalPages, $currentPage + 2);
                            for($i = $start; $i <= $end; $i++): 
                            ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= APP_URL ?>/index.php?url=ProductType/show&page=<?= $i ?><?= $qParam ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= APP_URL ?>/index.php?url=ProductType/show&page=<?= min($totalPages, $currentPage + 1) ?><?= $qParam ?>">Sau</a>
                            </li>
                    </ul>
                    <div class="text-center text-muted">Trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng: <?= $data['total'] ?? 0 ?> loại sản phẩm)</div>
                </nav>
                <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-info">Chưa có sản phẩm nào. Hãy sử dụng form phía trên để thêm loại sản phẩm mới.</div>
    <?php endif; ?>
</div>