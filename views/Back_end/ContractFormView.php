<?php
$action = $data['action'] ?? 'create';
$contract = $data['contract'] ?? null;
$suppliers = $data['suppliers'] ?? [];
$isEdit = $action === 'edit' && $contract;
?>

<div class="container-fluid mt-4">
  <h2><?= $isEdit ? 'Sửa hợp đồng' : 'Thêm hợp đồng mới' ?></h2>

  <div class="card mt-3">
    <div class="card-body">
      <form method="post" action="<?= $isEdit ? APP_URL . '/Supplier/updateContract/' . $contract['id'] : APP_URL . '/Supplier/storeContract' ?>">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nhà cung cấp *</label>
            <select name="supplier_id" class="form-select" required>
              <option value="">-- Chọn nhà cung cấp --</option>
              <?php foreach($suppliers as $s): ?>
              <option value="<?= $s['id'] ?>" <?= $isEdit && $contract['supplier_id'] == $s['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['code'] . ' - ' . $s['name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Số hợp đồng *</label>
            <input type="text" name="contract_number" class="form-control" 
                   value="<?= $isEdit ? htmlspecialchars($contract['contract_number']) : '' ?>" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
              <option value="active" <?= $isEdit && $contract['status'] === 'active' ? 'selected' : '' ?>>Hiệu lực</option>
              <option value="expired" <?= $isEdit && $contract['status'] === 'expired' ? 'selected' : '' ?>>Hết hạn</option>
              <option value="terminated" <?= $isEdit && $contract['status'] === 'terminated' ? 'selected' : '' ?>>Chấm dứt</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Tên hợp đồng *</label>
            <input type="text" name="contract_name" class="form-control" 
                   value="<?= $isEdit ? htmlspecialchars($contract['contract_name']) : '' ?>" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Ngày bắt đầu *</label>
            <input type="date" name="start_date" class="form-control" 
                   value="<?= $isEdit ? $contract['start_date'] : '' ?>" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Ngày kết thúc</label>
            <input type="date" name="end_date" class="form-control" 
                   value="<?= $isEdit ? $contract['end_date'] : '' ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Giá trị hợp đồng (VND)</label>
            <input type="number" name="contract_value" class="form-control" step="0.01"
                   value="<?= $isEdit ? $contract['contract_value'] : '0' ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Điều khoản thanh toán</label>
            <textarea name="payment_terms" class="form-control" rows="2"><?= $isEdit ? htmlspecialchars($contract['payment_terms']) : '' ?></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Điều khoản giao hàng</label>
            <textarea name="delivery_terms" class="form-control" rows="2"><?= $isEdit ? htmlspecialchars($contract['delivery_terms']) : '' ?></textarea>
          </div>

          <div class="col-12">
            <label class="form-label">Ghi chú</label>
            <textarea name="notes" class="form-control" rows="2"><?= $isEdit ? htmlspecialchars($contract['notes']) : '' ?></textarea>
          </div>

          <div class="col-12 mt-4">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Cập nhật' : 'Tạo mới' ?></button>
            <a href="<?= APP_URL ?>/Supplier/contracts" class="btn btn-secondary">Hủy</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
