<?php
$order = $data['order'] ?? null;
$items = $data['items'] ?? [];
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Chi tiết đơn hàng</h2>
    <div>
      <a href="<?= APP_URL ?>/AdminOrder/print/<?= htmlspecialchars($order['order_code'] ?? '') ?>" 
         class="btn btn-success me-2" target="_blank">
        <i class="bi bi-printer"></i> In đơn hàng
      </a>
      <a href="<?= APP_URL ?>/AdminOrder/index" class="btn btn-outline-secondary">← Quay lại quản lý đơn hàng</a>
    </div>
  </div>
  <?php if(!$order): ?>
    <div class="alert alert-warning">Không tìm thấy đơn hàng.</div>
  <?php else: ?>
    <?php
      $info = $order['transaction_info'] ?? '';
      $statusTok = trim(explode('|',$info)[0] ?? '');
      $norm = strtolower(preg_replace('/[\s_-]+/','',$statusTok));
      $badgeMap = [
        'dathantoan' => '<span class="badge bg-success">Đã thanh toán</span>',
        'dathanhtoan' => '<span class="badge bg-success">Đã thanh toán</span>',
        'chothanhtoan' => '<span class="badge bg-warning text-dark">Chờ thanh toán</span>',
        'chuathanhtoan' => '<span class="badge bg-secondary">Chưa thanh toán</span>',
        'chonhantaicuahang' => '<span class="badge bg-info">Nhận tại cửa hàng</span>',
        'huy' => '<span class="badge bg-danger">Hủy</span>',
        'pending' => '<span class="badge bg-warning">Đang xử lý</span>',
        'shipping' => '<span class="badge bg-info">Đang giao</span>',
        'completed' => '<span class="badge bg-success">Hoàn thành</span>',
        'cancelled' => '<span class="badge bg-danger">Đã hủy</span>'
      ];
      $statusBadge = $badgeMap[$norm] ?? '<span class="badge bg-secondary">Không xác định</span>';
      $statusList = [
        'dathantoan'=>'Đã thanh toán',
        'chothanhtoan'=>'Chờ thanh toán',
        'chuathanhtoan'=>'Chưa thanh toán',
        'chonhantaicuahang'=>'Nhận tại cửa hàng',
        'huy'=>'Hủy'
      ];
      // Parse shipping method
      $shippingMethod = '';
      $shippingFee = 0;
      if (!empty($info)) {
        $parts = explode('|', $info);
        array_shift($parts);
        foreach ($parts as $tok) {
          $tok = trim($tok);
          if (stripos($tok, 'shipping:') === 0) {
            if (preg_match('/^shipping:(.*?)\(/i', $tok, $m)) {
              $rawMethod = trim($m[1]);
              if (stripos($rawMethod, 'nhanh') !== false) {
                $shippingMethod = 'Giao hàng nhanh';
                $shippingFee = 50000;
              } elseif (stripos($rawMethod, 'tiêu chuẩn') !== false) {
                $shippingMethod = 'Giao hàng tiêu chuẩn';
                $shippingFee = 30000;
              } else {
                $shippingMethod = $rawMethod;
                $shippingFee = 30000; // default
              }
            }
          } elseif (stripos($tok, 'pickup:') === 0) {
            $shippingMethod = 'Nhận tại cửa hàng';
            $shippingFee = 0;
          }
        }
      }
      if ($shippingMethod == '') {
        if ($norm == 'chonhantaicuahang') {
          $shippingMethod = 'Nhận tại cửa hàng';
          $shippingFee = 0;
        } else {
          $shippingMethod = 'Giao hàng tiêu chuẩn';
          $shippingFee = 30000;
        }
      }
    ?>
    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <strong>Mã đơn:</strong><br><?= htmlspecialchars($order['order_code']) ?>
          </div>
          <div class="col-md-4">
            <strong>Email:</strong><br><?= htmlspecialchars($order['user_email']) ?>
          </div>
          <div class="col-md-4">
            <strong>Ngày tạo:</strong><br><?= htmlspecialchars($order['created_at']) ?>
          </div>
          <div class="col-md-4">
            <strong>Người nhận:</strong><br><?= htmlspecialchars($order['receiver']) ?>
          </div>
          <div class="col-md-4">
            <strong>SĐT:</strong><br><?= htmlspecialchars($order['phone']) ?>
          </div>
          <div class="col-md-4">
            <strong>Địa chỉ:</strong><br><?= htmlspecialchars($order['address']) ?>
          </div>
          <div class="col-md-4">
            <strong>Tổng tiền:</strong><br><span class="fw-bold text-danger"><?= number_format((float)$order['total_amount'],0,',','.') ?> ₫</span>
          </div>
          <div class="col-md-4">
            <strong>Trạng thái hiện tại:</strong><br>
            <?= $statusBadge ?>
          </div>
          <div class="col-md-4">
            <strong>Hình thức giao hàng:</strong><br><?= htmlspecialchars($shippingMethod) ?>
          </div>
        </div>
      </div>
    </div>
    <div class="card mb-4">
      <div class="card-header">Cập nhật trạng thái</div>
      <div class="card-body">
        <form method="post" action="<?= APP_URL ?>/AdminOrder/updateStatus">
          <input type="hidden" name="order_code" value="<?= htmlspecialchars($order['order_code']) ?>">
          <div class="row g-2 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Trạng thái mới</label>
              <select name="status" class="form-select" required>
                <option value="">-- Chọn --</option>
                <?php foreach($statusList as $k=>$label): ?>
                  <option value="<?= $k ?>" <?= $statusTok===$k?'selected':'' ?>><?= $label ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <button class="btn btn-primary" type="submit">Cập nhật</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="card">
      <div class="card-header">Sản phẩm trong đơn</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <?php
            // Tính các tổng cho admin
            $rawSubtotal = 0; $saleSubtotal = 0;
            foreach ($items as $r) {
              $orig = isset($r['price']) ? (float)$r['price'] : 0;
              $sale = isset($r['sale_price']) && $r['sale_price'] !== null ? (float)$r['sale_price'] : $orig;
              $qty  = (int)($r['quantity'] ?? 0);
              $rawSubtotal += $orig * $qty;
              $saleSubtotal += $sale * $qty;
            }
            $productLevelDiscount = max(0, $rawSubtotal - $saleSubtotal);
            $couponDiscount = 0; $thresholdDiscount = 0;
            if (!empty($info)) {
              $partsDiscount = explode('|', $info);
              // phần đầu statusTok đã lấy ở trên, bỏ nó đi
              if (!empty($partsDiscount)) { array_shift($partsDiscount); }
              foreach ($partsDiscount as $tok) {
                $tok = trim($tok);
                if (stripos($tok, 'coupon:') === 0) {
                  if (preg_match('/\(([-+]?\d+)\)/', $tok, $m)) { $couponDiscount += abs((int)$m[1]); }
                } elseif (stripos($tok, 'threshold:') === 0) {
                  if (preg_match('/\(([-+]?\d+)\)/', $tok, $m)) { $thresholdDiscount += abs((int)$m[1]); }
                }
              }
            }
            $finalTotal = isset($order['total_amount']) ? (float)$order['total_amount'] : ($saleSubtotal - $couponDiscount - $thresholdDiscount);
          ?>
          <table class="table table-striped mb-0">
            <thead>
              <tr>
                <th>Hình ảnh</th>
                <th>Sản phẩm & Biến thể</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
              </tr>
            </thead>
            <tbody>
              <?php if(empty($items)): ?>
                <tr><td colspan="5" class="text-center text-muted">Không có dữ liệu.</td></tr>
              <?php else: foreach($items as $it): ?>
                <?php
                  $original = isset($it['price']) ? (float)$it['price'] : 0;
                  $sale = isset($it['sale_price']) && $it['sale_price'] !== null ? (float)$it['sale_price'] : $original;
                  $qty = (int)($it['quantity'] ?? 0);
                  $img = $it['image'] ?? '';
                  $imgUrl = APP_URL . '/public/images/' . rawurlencode($img);
                  $base = dirname(__DIR__, 2);
                  $path1 = $base . '/public/images/' . $img;
                  $path2 = $base . '/public/uploads/' . $img;
                  if (!empty($img)) {
                    if (file_exists($path1)) {
                      $imgUrl = APP_URL . '/public/images/' . rawurlencode($img);
                    } elseif (file_exists($path2)) {
                      $imgUrl = APP_URL . '/public/uploads/' . rawurlencode($img);
                    }
                  }
                ?>
                <tr>
                  <td><img src="<?= $imgUrl ?>" alt="SP" width="60" height="60" class="rounded" onerror="this.src='<?= APP_URL ?>/public/images/placeholder.png'" /></td>
                  <td>
                    <strong><?= htmlspecialchars($it['product_name'] ?? ($it['product_id'] ?? 'N/A')) ?></strong>
                    <?php
                      if (!empty($it['variant_name'])) {
                        echo '<br><small class="text-primary">Dung lượng: ' . htmlspecialchars($it['variant_name']) . '</small>';
                      }
                      if (!empty($it['color_variant_name'])) {
                        echo '<br><small class="text-primary">Màu sắc: ' . htmlspecialchars($it['color_variant_name']) . '</small>';
                      }
                    ?>
                  </td>
                  <td><?= $qty ?></td>
                  <td>
                    <?php if ($sale < $original): ?>
                      <span class="text-muted text-decoration-line-through"><?= number_format($original,0,',','.') ?> ₫</span><br>
                      <span class="text-danger fw-semibold"><?= number_format($sale,0,',','.') ?> ₫</span>
                    <?php else: ?>
                      <?= number_format($original,0,',','.') ?> ₫
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php
                      $lineOriginal = $original * $qty;
                      $lineSale = $sale * $qty;
                      if ($sale < $original) {
                        echo '<span class="text-muted text-decoration-line-through">' . number_format($lineOriginal,0,',','.') . ' ₫</span><br>';
                        echo '<span class="text-danger fw-semibold">' . number_format($lineSale,0,',','.') . ' ₫</span>';
                      } else {
                        echo number_format($lineOriginal,0,',','.') . ' ₫';
                      }
                    ?>
                  </td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
            <tfoot class="table-light">
              <tr>
                <th colspan="4" class="text-end">Tổng gốc:</th>
                <th class="text-center"><?= number_format($rawSubtotal, 0, ',', '.') ?> ₫</th>
              </tr>
              <?php if ($productLevelDiscount > 0): ?>
              <tr>
                <th colspan="4" class="text-end">Giảm giá sản phẩm:</th>
                <th class="text-center text-success">-<?= number_format($productLevelDiscount, 0, ',', '.') ?> ₫</th>
              </tr>
              <?php endif; ?>
              <?php if ($couponDiscount > 0): ?>
              <tr>
                <th colspan="4" class="text-end">Giảm mã giảm giá:</th>
                <th class="text-center text-success">-<?= number_format($couponDiscount, 0, ',', '.') ?> ₫</th>
              </tr>
              <?php endif; ?>
              <?php if ($thresholdDiscount > 0): ?>
              <tr>
                <th colspan="4" class="text-end">Giảm theo ngưỡng:</th>
                <th class="text-center text-success">-<?= number_format($thresholdDiscount, 0, ',', '.') ?> ₫</th>
              </tr>
              <?php endif; ?>
              <tr>
                <th colspan="4" class="text-end">Phí vận chuyển:</th>
                <th class="text-center">
                  <?php if ($shippingMethod === 'Nhận tại cửa hàng'): ?>
                    Miễn phí
                  <?php else: ?>
                    <?= number_format($shippingFee, 0, ',', '.') ?> ₫
                  <?php endif; ?>
                </th>
              </tr>
              <tr class="table-secondary">
                <th colspan="4" class="text-end">Tổng thanh toán:</th>
                <th class="text-center text-danger fw-bold fs-5"><?= number_format($finalTotal, 0, ',', '.') ?> ₫</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>
    <div class="mt-3">
      <a href="<?= APP_URL ?>/AdminOrder/index" class="btn btn-secondary">← Quay lại quản lý đơn hàng</a>
    </div>
</div>
