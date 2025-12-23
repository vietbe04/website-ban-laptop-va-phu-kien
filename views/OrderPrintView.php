<?php
$order = $data['order'] ?? null;
$items = $data['items'] ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>In đơn hàng - <?= htmlspecialchars($order['order_code'] ?? '') ?></title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Arial', sans-serif;
      font-size: 13px;
      line-height: 1.4;
      color: #000;
      background: #fff;
      padding: 20px;
    }
    .invoice-container {
      max-width: 800px;
      margin: 0 auto;
      background: #fff;
      padding: 30px;
      border: 2px solid #333;
    }
    .header {
      text-align: center;
      border-bottom: 3px double #333;
      padding-bottom: 15px;
      margin-bottom: 20px;
    }
    .header h1 {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 5px;
      text-transform: uppercase;
      color: #333;
    }
    .header p {
      margin: 3px 0;
      font-size: 12px;
    }
    .invoice-info {
      display: flex;
      justify-content: space-between;
      margin-bottom: 25px;
    }
    .invoice-info-left, .invoice-info-right {
      width: 48%;
    }
    .info-section {
      margin-bottom: 20px;
    }
    .info-section h3 {
      font-size: 14px;
      font-weight: bold;
      margin-bottom: 8px;
      text-transform: uppercase;
      border-bottom: 1px solid #ddd;
      padding-bottom: 3px;
    }
    .info-row {
      margin: 5px 0;
    }
    .info-row strong {
      display: inline-block;
      min-width: 100px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }
    table th {
      background: #f5f5f5;
      font-weight: bold;
      text-align: left;
      padding: 10px 8px;
      border: 1px solid #333;
      font-size: 13px;
    }
    table td {
      padding: 8px;
      border: 1px solid #ddd;
      vertical-align: top;
    }
    table .text-right {
      text-align: right;
    }
    table .text-center {
      text-align: center;
    }
    .summary {
      margin-top: 20px;
      text-align: right;
    }
    .summary-row {
      display: flex;
      justify-content: flex-end;
      margin: 8px 0;
      font-size: 13px;
    }
    .summary-row label {
      min-width: 200px;
      font-weight: bold;
      text-align: right;
      padding-right: 15px;
    }
    .summary-row span {
      min-width: 150px;
      text-align: right;
    }
    .total-row {
      border-top: 2px solid #333;
      padding-top: 10px;
      margin-top: 10px;
      font-size: 16px;
      font-weight: bold;
    }
    .footer {
      margin-top: 40px;
      padding-top: 20px;
      border-top: 1px solid #ddd;
      text-align: center;
      font-size: 12px;
    }
    .signature-section {
      display: flex;
      justify-content: space-around;
      margin-top: 40px;
      text-align: center;
    }
    .signature-box {
      width: 45%;
    }
    .signature-box p {
      margin: 5px 0;
      font-weight: bold;
    }
    .signature-line {
      margin-top: 60px;
      font-style: italic;
      font-size: 12px;
    }
    .badge {
      display: inline-block;
      padding: 3px 8px;
      font-size: 11px;
      font-weight: bold;
      border-radius: 3px;
      color: #fff;
    }
    .badge-success { background: #28a745; }
    .badge-warning { background: #ffc107; color: #000; }
    .badge-secondary { background: #6c757d; }
    .badge-info { background: #17a2b8; }
    .badge-danger { background: #dc3545; }
    
    @media print {
      body {
        padding: 0;
      }
      .invoice-container {
        border: none;
        padding: 20px;
      }
      .no-print {
        display: none;
      }
    }
    .print-button-container {
      text-align: center;
      margin-bottom: 20px;
    }
    .print-button {
      background: #007bff;
      color: #fff;
      border: none;
      padding: 10px 30px;
      font-size: 16px;
      cursor: pointer;
      border-radius: 5px;
    }
    .print-button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <div class="print-button-container no-print">
    <button class="print-button" onclick="window.print()">🖨️ In đơn hàng</button>
    <button class="print-button" style="background: #6c757d; margin-left: 10px;" onclick="window.close()">✖ Đóng</button>
  </div>

  <?php if(!$order): ?>
    <div style="text-align: center; padding: 50px;">
      <h2>Không tìm thấy đơn hàng</h2>
    </div>
  <?php else: ?>
    <?php
      $info = $order['transaction_info'] ?? '';
      $statusTok = trim(explode('|',$info)[0] ?? '');
      $norm = strtolower(preg_replace('/[\s_-]+/','',$statusTok));
      $statusLabel = [
        'dathantoan' => 'Đã thanh toán',
        'dathanhtoan' => 'Đã thanh toán',
        'chothanhtoan' => 'Chờ thanh toán',
        'chuathanhtoan' => 'Chưa thanh toán',
        'chonhantaicuahang' => 'Nhận tại cửa hàng',
        'huy' => 'Hủy',
        'pending' => 'Đang xử lý',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy'
      ];
      $statusText = $statusLabel[$norm] ?? 'Không xác định';
      
      // Tính toán các giá trị
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
      $finalTotal = (float)$order['total_amount'];
    ?>

    <div class="invoice-container">
      <!-- Header -->
      <div class="header">
        <h1>HÓA ĐƠN BÁN HÀNG</h1>
        <p><strong>Cửa hàng điện tử DQV</strong></p>
        <p>Địa chỉ: 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
        <p>Điện thoại: 0123-456-789 | Email: support@dqv.vn</p>
        <p>Website: www.dqv.vn</p>
      </div>

      <!-- Invoice Info -->
      <div class="invoice-info">
        <div class="invoice-info-left">
          <div class="info-section">
            <h3>Thông tin đơn hàng</h3>
            <div class="info-row">
              <strong>Mã đơn:</strong> <?= htmlspecialchars($order['order_code']) ?>
            </div>
            <div class="info-row">
              <strong>Ngày tạo:</strong> <?= htmlspecialchars($order['created_at']) ?>
            </div>
            <div class="info-row">
              <strong>Trạng thái:</strong> <?= htmlspecialchars($statusText) ?>
            </div>
          </div>
        </div>
        <div class="invoice-info-right">
          <div class="info-section">
            <h3>Thông tin người nhận</h3>
            <div class="info-row">
              <strong>Họ tên:</strong> <?= htmlspecialchars($order['receiver']) ?>
            </div>
            <div class="info-row">
              <strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?>
            </div>
            <div class="info-row">
              <strong>Email:</strong> <?= htmlspecialchars($order['user_email']) ?>
            </div>
            <div class="info-row">
              <strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Table -->
      <table>
        <thead>
          <tr>
            <th style="width: 5%;">STT</th>
            <th style="width: 40%;">Tên sản phẩm</th>
            <th style="width: 10%;" class="text-center">Số lượng</th>
            <th style="width: 15%;" class="text-right">Đơn giá</th>
            <th style="width: 15%;" class="text-right">Giảm giá</th>
            <th style="width: 15%;" class="text-right">Thành tiền</th>
          </tr>
        </thead>
        <tbody>
          <?php $stt = 1; foreach($items as $item): ?>
            <?php
              $orig = (float)($item['price'] ?? 0);
              $sale = isset($item['sale_price']) && $item['sale_price'] !== null ? (float)$item['sale_price'] : $orig;
              $qty = (int)($item['quantity'] ?? 0);
              $discount = max(0, $orig - $sale);
              $lineTotal = $sale * $qty;
            ?>
            <tr>
              <td class="text-center"><?= $stt++ ?></td>
              <td><?= htmlspecialchars($item['product_name'] ?? 'N/A') ?></td>
              <td class="text-center"><?= $qty ?></td>
              <td class="text-right"><?= number_format($orig, 0, ',', '.') ?> ₫</td>
              <td class="text-right">
                <?php if($discount > 0): ?>
                  -<?= number_format($discount, 0, ',', '.') ?> ₫
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td class="text-right"><strong><?= number_format($lineTotal, 0, ',', '.') ?> ₫</strong></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Summary -->
      <div class="summary">
        <div class="summary-row">
          <label>Tổng tiền hàng:</label>
          <span><?= number_format($rawSubtotal, 0, ',', '.') ?> ₫</span>
        </div>
        <?php if($productLevelDiscount > 0): ?>
        <div class="summary-row">
          <label>Giảm giá sản phẩm:</label>
          <span>-<?= number_format($productLevelDiscount, 0, ',', '.') ?> ₫</span>
        </div>
        <?php endif; ?>
        <?php if($couponDiscount > 0): ?>
        <div class="summary-row">
          <label>Giảm giá Coupon:</label>
          <span>-<?= number_format($couponDiscount, 0, ',', '.') ?> ₫</span>
        </div>
        <?php endif; ?>
        <?php if($thresholdDiscount > 0): ?>
        <div class="summary-row">
          <label>Giảm giá theo ngưỡng:</label>
          <span>-<?= number_format($thresholdDiscount, 0, ',', '.') ?> ₫</span>
        </div>
        <?php endif; ?>
        <div class="summary-row total-row">
          <label>TỔNG THANH TOÁN:</label>
          <span style="color: #dc3545;"><?= number_format($finalTotal, 0, ',', '.') ?> ₫</span>
        </div>
      </div>

      <!-- Signature Section -->
      <div class="signature-section">
        <div class="signature-box">
          <p>NGƯỜI BÁN HÀNG</p>
          <div class="signature-line">(Ký, ghi rõ họ tên)</div>
        </div>
        <div class="signature-box">
          <p>NGƯỜI MUA HÀNG</p>
          <div class="signature-line">(Ký, ghi rõ họ tên)</div>
        </div>
      </div>

      <!-- Footer -->
      <div class="footer">
        <p><strong>Cảm ơn quý khách đã mua hàng tại DQV!</strong></p>
        <p>Vui lòng kiểm tra kỹ hàng hóa khi nhận. Đổi trả trong vòng 7 ngày nếu có lỗi từ nhà sản xuất.</p>
        <p style="margin-top: 10px; font-style: italic; font-size: 11px;">
          Hóa đơn được in lúc: <?= date('d/m/Y H:i:s') ?>
        </p>
      </div>
    </div>
  <?php endif; ?>

  <script>
    // Auto focus for printing
    window.onload = function() {
      // Uncomment next line to auto-print when page loads
      // window.print();
    }
  </script>
</body>
</html>
