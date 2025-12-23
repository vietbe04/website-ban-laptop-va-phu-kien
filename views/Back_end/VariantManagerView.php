<?php
/**
 * Quản lý biến thể sản phẩm (Admin).
 * - Thêm nhanh biến thể màu sắc/dung lượng trực tiếp trên bảng.
 * - Sửa/xoá từng biến thể; dung lượng có giá riêng.
 * - Phân trang nếu danh sách dài.
 */
$product = $data['product'] ?? null;
$variants = $data['variants'] ?? [];
?>
<div class="container py-4">
    <h3 class="mb-3">Biến thể sản phẩm: <span class="text-primary"><?= htmlspecialchars($product['tensp'] ?? '') ?></span></h3>
    <a href="<?= APP_URL ?>/Product" class="btn btn-light btn-sm mb-3">← Quay lại sản phẩm</a>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <strong>Danh sách & Thêm/Sửa nhanh</strong>
            <small class="text-warning">Thao tác trực tiếp trên bảng</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle text-center" id="variantTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">#</th>
                            <th style="width:120px">Loại</th>
                            <th>Giá trị</th>
                            <th style="width:150px">Giá riêng (dung lượng)</th>
                            <th style="width:90px">Trạng thái</th>
                            <th style="width:180px">Thao tác</th>
                        </tr>
                        
                        <tr class="table-success">
                            <form method="post" action="<?= APP_URL ?>/ProductVariant/create/<?= htmlspecialchars($product['masp']) ?>" class="d-contents" onsubmit="return validateAddVariant(this);">
                                <td>+</td>
                                <td>
                                    <select name="variant_type" class="form-select form-select-sm" onchange="toggleAddPrice(this)" required>
                                        <option value="color">Màu sắc</option>
                                        <option value="capacity">Dung lượng</option>
                                    </select>
                                </td>
                                <td><input name="variant_value" class="form-control form-control-sm" placeholder="VD: Đỏ / 256GB" required /></td>
                                <td><input name="variant_price" class="form-control form-control-sm" placeholder="Giá" style="display:none" type="number" min="0" /></td>
                                <td><span class="badge bg-success">Mới</span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" type="submit">Thêm</button>
                                    <button class="btn btn-sm btn-secondary" type="reset">Xóa nhập</button>
                                </td>
                            </form>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($variants)): $i=1; foreach ($variants as $v): ?>
                        <tr data-id="<?= $v['id'] ?>">
                            <form method="post" action="<?= APP_URL ?>/ProductVariant/update/<?= $v['id'] ?>" class="d-contents" onsubmit="return validateEditVariant(this, '<?= $v['variant_type'] ?>');">
                                <td><?= $i + ($data['offset'] ?? 0) ?></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($v['variant_type']) ?>" disabled />
                                </td>
                                <td>
                                    <input name="variant_value" class="form-control form-control-sm" value="<?= htmlspecialchars($v['name']) ?>" />
                                </td>
                                <td>
                                    <?php if ($v['variant_type']==='capacity'): ?>
                                        <input name="variant_price" type="number" min="0" class="form-control form-control-sm" value="<?= htmlspecialchars($v['price_per_kg']) ?>" />
                                    <?php else: ?>
                                        <input name="variant_price" class="form-control form-control-sm" style="display:none" />
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" name="active" <?= $v['active'] ? 'checked' : '' ?> />
                                    </div>
                                </td>
                                <td class="d-flex gap-1 justify-content-center">
                                    <button type="submit" class="btn btn-sm btn-warning">Lưu</button>
                                    <a href="<?= APP_URL ?>/ProductVariant/delete/<?= $v['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa biến thể này?');">Xóa</a>
                                </td>
                            </form>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="6" class="text-muted py-3">Chưa có biến thể.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <?php if(($data['totalPages'] ?? 1) > 1): ?>
        <div class="card-footer bg-light">
            <nav aria-label="Page navigation">
              <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= ($data['currentPage'] ?? 1) <= 1 ? 'disabled' : '' ?>">
                  <a class="page-link" href="<?= APP_URL ?>/ProductVariant/manage/<?= htmlspecialchars($product['masp']) ?>?page=<?= ($data['currentPage'] ?? 1) - 1 ?>">Trước</a>
                </li>
                
                <?php
                $currentPage = $data['currentPage'] ?? 1;
                $totalPages = $data['totalPages'] ?? 1;
                $start = max(1, $currentPage - 2);
                $end = min($totalPages, $currentPage + 2);
                for($i = $start; $i <= $end; $i++): 
                ?>
                  <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="<?= APP_URL ?>/ProductVariant/manage/<?= htmlspecialchars($product['masp']) ?>?page=<?= $i ?>"><?= $i ?></a>
                  </li>
                <?php endfor; ?>
                
                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                  <a class="page-link" href="<?= APP_URL ?>/ProductVariant/manage/<?= htmlspecialchars($product['masp']) ?>?page=<?= $currentPage + 1 ?>">Sau</a>
                </li>
              </ul>
              <div class="text-center text-muted mt-2">Trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng: <?= $data['total'] ?? 0 ?> biến thể)</div>
            </nav>
        </div>
        <?php endif; ?>
        
    </div>
</div>
<script>
function toggleAddPrice(sel){
    const priceInput = sel.closest('tr').querySelector('input[name="variant_price"]');
    if(sel.value==='capacity'){ priceInput.style.display='block'; priceInput.required=true; } else { priceInput.style.display='none'; priceInput.required=false; priceInput.value=''; }
}
function validateAddVariant(form){
    const type = form.querySelector('select[name="variant_type"]').value;
    const val = form.querySelector('input[name="variant_value"]').value.trim();
    if(val===''){ alert('Giá trị biến thể không được rỗng'); return false; }
    if(type==='capacity'){
        const p = form.querySelector('input[name="variant_price"]').value;
        if(p===''|| Number(p)<0){ alert('Giá biến thể dung lượng phải >= 0'); return false; }
    }
    return true;
}
function validateEditVariant(form, type){
    const val = form.querySelector('input[name="variant_value"]').value.trim();
    if(val===''){ alert('Giá trị không được rỗng'); return false; }
    if(type==='capacity'){
        const p = form.querySelector('input[name="variant_price"]').value;
        if(p===''|| Number(p)<0){ alert('Giá dung lượng phải >= 0'); return false; }
    }
    return true;
}
</script>