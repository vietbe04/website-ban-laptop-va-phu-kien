<?php
/**
 * Quản lý giảm giá tổng hợp (Admin):
 * - Tab Khuyến mãi sản phẩm: tạo KM theo loại/SP, danh sách KM.
 * - Tab Coupon: thêm/sửa/xoá mã giảm giá.
 * - Tab Ngưỡng giảm giá: cấu hình giảm theo tổng đơn.
 * Bảo mật: escape dữ liệu hiển thị; kiểm tra vai trò hiển thị tab.
 */
?>
<div class="container mt-4">
  <h2 class="mb-4">Quản lý giảm giá</h2>
  
  <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } $role = $_SESSION['user']['role'] ?? 'user'; ?>
  <?php if (!empty($_SESSION['flash_promo'])): $f = $_SESSION['flash_promo']; ?>
    <div class="alert alert-<?= htmlspecialchars($f['type'] ?? 'info') ?>"><?= htmlspecialchars($f['message'] ?? '') ?></div>
    <?php unset($_SESSION['flash_promo']); endif; ?>
  <ul class="nav nav-tabs" id="discountTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="promo-tab" data-bs-toggle="tab" data-bs-target="#promo" type="button" role="tab">Khuyến mãi sản phẩm</button>
    </li>
    <?php if (in_array($role,['admin','staff'])): ?>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="coupon-tab" data-bs-toggle="tab" data-bs-target="#coupon" type="button" role="tab">Mã giảm giá (Coupon)</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="threshold-tab" data-bs-toggle="tab" data-bs-target="#threshold" type="button" role="tab">Ngưỡng giảm giá</button>
    </li>
    <?php endif; ?>
  </ul>
  <div class="tab-content py-4">
    
    <div class="tab-pane fade show active" id="promo" role="tabpanel" aria-labelledby="promo-tab">
      <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">Thêm khuyến mãi mới</div>
        <div class="card-body">
          <form method="post" action="<?= APP_URL ?>/khuyenmai/add">
            <div class="row mb-3">
              <div class="col-md-4">
                <label>Loại sản phẩm</label>
                <select name="maLoaiSP" id="maLoaiSP" class="form-select">
                  <option value="">-- Chọn loại sản phẩm --</option>
                  <?php foreach ($data['promoViewTypes'] as $type): ?>
                    <option value="<?= htmlspecialchars($type['maLoaiSP']) ?>"><?= htmlspecialchars($type['maLoaiSP']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4">
                <label>Sản phẩm</label>
                <select name="masp" id="masp" class="form-select">
                  <option value="">-- Áp dụng cho tất cả sản phẩm của loại --</option>
                  <?php foreach ($data['products'] as $p): ?>
                    <option value="<?= htmlspecialchars($p['masp']) ?>" data-loai="<?= htmlspecialchars($p['maLoaiSP']) ?>">
                      <?= htmlspecialchars($p['tensp']) ?> (<?= htmlspecialchars($p['masp']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <script>
                document.addEventListener('DOMContentLoaded', function(){
                  const maLoaiSP = document.getElementById('maLoaiSP');
                  const masp = document.getElementById('masp');
                  if(maLoaiSP){
                    maLoaiSP.addEventListener('change', function(){
                      const val = this.value;
                      Array.from(masp.options).forEach(o=>{ if(!o.value) return; o.hidden = val && o.getAttribute('data-loai') !== val; });
                      masp.value='';
                    });
                  }
                });
              </script>
              <div class="col-md-4">
                <label>Phần trăm khuyến mãi (%)</label>
                <input type="number" name="phantram" class="form-control" min="1" max="100" required>
              </div>
            </div>
              <script>
                // Activate tab based on URL fragment (keeps admin on same tab after redirects)
                (function(){
                  document.addEventListener('DOMContentLoaded', function(){
                    try{
                      var hash = window.location.hash;
                      if(hash){
                        // Normalize (only keep #promo, #coupon, #threshold)
                        if(['#promo','#coupon','#threshold'].indexOf(hash) !== -1){
                          var btn = document.querySelector('#discountTab button[data-bs-target="'+hash+'"]');
                          if(btn){
                            // Use Bootstrap tab API to show the tab
                            if(window.bootstrap && bootstrap.Tab){
                              var t = new bootstrap.Tab(btn);
                              t.show();
                            } else {
                              // Fallback: simulate click
                              btn.click();
                            }
                            // ensure focus scrolled to top of tab area
                            setTimeout(function(){ if(document.getElementById(hash.replace('#',''))) document.getElementById(hash.replace('#','')).scrollIntoView({behavior:'smooth', block:'start'}); }, 100);
                          }
                        }
                      }

                      // Keep URL hash in sync when switching tabs (so future redirects preserve state)
                      var tabButtons = document.querySelectorAll('#discountTab button[data-bs-target]');
                      tabButtons.forEach(function(b){
                        b.addEventListener('shown.bs.tab', function(e){
                          try{ var target = e.target.getAttribute('data-bs-target') || ''; if(target) window.location.hash = target; }catch(ex){}
                        });
                      });
                    }catch(e){ console.error('tab-hash:', e); }
                  });
                })();
              </script>
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
            <button class="btn btn-success">+ Lưu khuyến mãi</button>
          </form>
        </div>
      </div>
      <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white fw-bold">Danh sách khuyến mãi</div>
        <div class="card-body">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>STT</th>
                <th>Loại</th>
                <th>Sản phẩm</th>
                <th>Giá gốc</th>
                <th>%</th>
                <th>Từ</th>
                <th>Đến</th>
                <th>Hành động</th>
              </tr>
            </thead>
            <tbody>
              <?php $stt=1; foreach($data['promoList'] as $row): ?>
                <tr>
                  <td><?= $stt++ ?></td>
                  <td><?= htmlspecialchars($row['maLoaiSP']) ?></td>
                  <td><?= htmlspecialchars($row['tensp']) ?: '<span class="text-muted">Tất cả</span>' ?></td>
                  <td><?= empty($row['tensp']) ? '<span class="text-muted">Nhiều SP</span>' : number_format($row['giaXuat'],0,',','.') . ' ₫' ?></td>
                  <td><?= htmlspecialchars($row['phantram']) ?>%</td>
                  <td><?= htmlspecialchars($row['ngaybatdau']) ?></td>
                  <td><?= htmlspecialchars($row['ngayketthuc']) ?></td>
                  <td>
                    <a href="<?= APP_URL ?>/khuyenmai/edit/<?= urlencode($row['km_id']) ?>" class="btn btn-primary btn-sm me-1">Sửa</a>
                    <a href="<?= APP_URL ?>/khuyenmai/delete/<?= urlencode($row['km_id']) ?>" onclick="return confirm('Xóa khuyến mãi này?')" class="btn btn-danger btn-sm">Xóa</a>
                  </td>
                </tr>
              <?php endforeach; if(empty($data['promoList'])): ?>
                <tr><td colspan="8" class="text-center text-muted">Chưa có khuyến mãi</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php if (in_array($role,['admin','staff'])): ?>
    
    <div class="tab-pane fade" id="coupon" role="tabpanel" aria-labelledby="coupon-tab">
      <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-bold">Thêm mã giảm giá mới</div>
        <div class="card-body">
          <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } if (!empty($_SESSION['flash_coupon'])): $cf = $_SESSION['flash_coupon']; ?>
            <div class="alert alert-<?= htmlspecialchars($cf['type'] ?? 'info') ?>"><?= htmlspecialchars($cf['message'] ?? '') ?></div>
            <?php unset($_SESSION['flash_coupon']); endif; ?>
          <form method="post" action="<?= APP_URL ?>/coupon/show">
            <div class="row mb-3">
              <div class="col-md-3"><label>Mã</label><input name="code" class="form-control" required></div>
              <div class="col-md-3"><label>Loại</label><select name="type" class="form-select"><option value="percent">%</option><option value="fixed">VNĐ</option></select></div>
              <div class="col-md-3"><label>Giá trị</label><input name="value" type="number" class="form-control" required></div>
              <div class="col-md-3"><label>Trạng thái</label><div class="form-check"><input type="checkbox" name="status" class="form-check-input" checked><label class="form-check-label">Kích hoạt</label></div></div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><label>Ngày bắt đầu</label><input type="date" name="start_date" class="form-control"></div>
              <div class="col-md-4"><label>Ngày kết thúc</label><input type="date" name="end_date" class="form-control"></div>
              <div class="col-md-4"><label>Đơn tối thiểu (VNĐ)</label><input type="number" name="min_total" class="form-control" placeholder="(tùy chọn)"></div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><label>Giới hạn sử dụng</label><input type="number" name="usage_limit" class="form-control" placeholder="(tùy chọn)"></div>
            </div>
            <button class="btn btn-success">Lưu mã</button>
          </form>
        </div>
      </div>
      <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white fw-bold">Danh sách mã</div>
        <div class="card-body">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light"><tr><th>#</th><th>Mã</th><th>Loại</th><th>Giá trị</th><th>Thời gian</th><th>Trạng thái</th><th>Giới hạn</th><th>Sử dụng</th><th>Hành động</th></tr></thead>
            <tbody>
              <?php $i=1; foreach($data['coupons'] as $c): ?>
                <tr>
                  <td><?= $i++ ?></td>
                  <td><?= htmlspecialchars($c['code']) ?></td>
                  <td><?= htmlspecialchars($c['type']) ?></td>
                  <td><?= htmlspecialchars($c['value']) ?><?= $c['type']==='percent' ? '%' : ' ₫' ?></td>
                  <td><?= htmlspecialchars($c['start_date']) ?> - <?= htmlspecialchars($c['end_date']) ?></td>
                  <td><?= $c['status']?'<span class="badge bg-success">Active</span>':'<span class="badge bg-secondary">Inactive</span>' ?></td>
                  <td><?= htmlspecialchars($c['usage_limit'] ?: '-') ?></td>
                  <td><?= htmlspecialchars($c['used_count']) ?></td>
                  <td>
                    <a href="<?= APP_URL ?>/coupon/edit/<?= $c['id'] ?>" class="btn btn-primary btn-sm me-1">Sửa</a>
                    <a href="<?= APP_URL ?>/coupon/delete/<?= $c['id'] ?>" onclick="return confirm('Xóa mã này?')" class="btn btn-danger btn-sm">Xóa</a>
                  </td>
                </tr>
              <?php endforeach; if(empty($data['coupons'])): ?>
                <tr><td colspan="9" class="text-center text-muted">Chưa có mã</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <div class="tab-pane fade" id="threshold" role="tabpanel" aria-labelledby="threshold-tab">
      <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-bold">Thêm ngưỡng giảm giá</div>
        <div class="card-body">
          <form class="row g-3" method="POST" action="<?= APP_URL ?>/Admin/thresholdCreate">
            <div class="col-md-3"><label class="form-label">Tổng tối thiểu (₫)</label><input type="number" name="min_total" class="form-control" min="1000" step="1000" required></div>
            <div class="col-md-2"><label class="form-label">Phần trăm (%)</label><input type="number" name="percent" class="form-control" min="1" max="100" required></div>
            <div class="col-md-2 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="status" id="status" checked><label class="form-check-label" for="status">Kích hoạt</label></div></div>
            <div class="col-md-2 d-flex align-items-end"><button class="btn btn-success" type="submit">Thêm</button></div>
          </form>
        </div>
      </div>
      <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white fw-bold">Danh sách ngưỡng</div>
        <div class="card-body">
          <table class="table table-bordered table-sm align-middle">
            <thead class="table-light"><tr><th>ID</th><th>Tổng tối thiểu (₫)</th><th>% giảm</th><th>Trạng thái</th><th>Ngày tạo</th><th>Hành động</th></tr></thead>
            <tbody>
              <?php if(!empty($data['tiers'])): foreach($data['tiers'] as $tier): ?>
                <tr>
                  <td><?= (int)$tier['id'] ?></td>
                  <td><?= number_format($tier['min_total'],0,',','.') ?></td>
                  <td><?= (int)$tier['percent'] ?>%</td>
                  <td><?= $tier['status']?'<span class="badge bg-success">Bật</span>':'<span class="badge bg-secondary">Tắt</span>' ?></td>
                  <td><?= htmlspecialchars($tier['created_at']) ?></td>
                  <td>
                    <a class="btn btn-sm btn-outline-warning" href="<?= APP_URL ?>/Admin/thresholdToggle/<?= (int)$tier['id'] ?>">Toggle</a>
                    <a class="btn btn-sm btn-outline-danger" href="<?= APP_URL ?>/Admin/thresholdDelete/<?= (int)$tier['id'] ?>" onclick="return confirm('Xóa ngưỡng này?');">Xóa</a>
                  </td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="6" class="text-center text-muted">Chưa có ngưỡng.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
          <p class="small text-muted">Hệ thống chọn mức % cao nhất thỏa điều kiện tổng ≥ ngưỡng và trạng thái bật.</p>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
