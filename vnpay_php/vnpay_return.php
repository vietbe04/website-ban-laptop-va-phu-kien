<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kết quả thanh toán VNPAY">
    <meta name="author" content="VNPAY">
    <title>Kết quả thanh toán - VNPAY</title>
    
    <!-- Modern CSS -->
    <link href="assets/vnpay-modern.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/jquery-1.11.3.min.js"></script>
    
    <style>
        .result-card {
            text-align: center;
            padding: 40px 30px;
        }
        
        .result-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            display: inline-block;
            animation: bounceIn 0.6s ease-out;
        }
        
        .result-success {
            color: var(--success-color);
        }
        
        .result-error {
            color: var(--error-color);
        }
        
        .result-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-dark);
        }
        
        .result-message {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 30px;
        }
        
        .info-table {
            background: var(--bg-light);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .info-value {
            color: var(--text-light);
            font-weight: 500;
        }
        
        .amount-value {
            color: var(--primary-color);
            font-size: 1.2rem;
            font-weight: 700;
        }
        
        .success-value {
            color: var(--success-color);
            font-weight: 600;
        }
        
        .error-value {
            color: var(--error-color);
            font-weight: 600;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        
        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @media (max-width: 768px) {
            .result-card {
                padding: 30px 20px;
            }
            
            .result-icon {
                font-size: 3rem;
            }
            
            .result-title {
                font-size: 1.5rem;
            }
            
            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>

<body>
    <?php
    require_once("./config.php");
    // Khởi động session để lấy user/email và thao tác giỏ hàng
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once '../models/OrderModel.php';
    require_once "../app/DB.php";
    $orderModel = new OrderModel();
    // Load app config to get APP_URL if available
    if (file_exists(__DIR__ . '/../app/config.php')) {
        require_once __DIR__ . '/../app/config.php';
    }

    $vnp_SecureHash = $_GET['vnp_SecureHash'];
    $inputData = array();
    foreach ($_GET as $key => $value) {
        if (substr($key, 0, 4) == "vnp_") {
            $inputData[$key] = $value;
        }
    }

    unset($inputData['vnp_SecureHash']);
    ksort($inputData);
    $i = 0;
    $hashData = "";
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
    }

    $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    
    // Debug hiển thị khi thêm ?__debug=1
    $isDebug = isset($_GET['__debug']) && $_GET['__debug'] == '1';
    // Cờ để biết giao dịch thành công (dùng cho auto redirect)
    $paymentSuccess = false;
    
    // Xác định kết quả giao dịch
    $isValidHash = ($secureHash == $vnp_SecureHash);
    $isSuccess = ($isValidHash && $_GET['vnp_ResponseCode'] == '00');
    if ($isSuccess) {
        $paymentSuccess = true;
    }
    ?>

    <!--Begin display -->
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-credit-card"></i> Kết quả thanh toán</h1>
            <p>Thông tin giao dịch từ VNPAY</p>
        </div>

        <!-- Result Card -->
        <div class="card result-card">
            <?php if ($isSuccess): ?>
                <div class="result-icon result-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="result-title">Thanh toán thành công!</h2>
                <p class="result-message">Giao dịch của bạn đã được xử lý thành công. Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi.</p>
            <?php elseif (!$isValidHash): ?>
                <div class="result-icon result-error">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="result-title">Lỗi xác thực!</h2>
                <p class="result-message">Chữ ký không hợp lệ. Vui lòng liên hệ bộ phận hỗ trợ nếu bạn nghi ngờ có vấn đề.</p>
            <?php else: ?>
                <div class="result-icon result-error">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h2 class="result-title">Thanh toán thất bại!</h2>
                <p class="result-message">Giao dịch không thành công. Vui lòng thử lại hoặc chọn phương thức thanh toán khác.</p>
            <?php endif; ?>

            <!-- Transaction Details -->
            <?php 
                // Tính số tiền hiển thị: ưu tiên từ VNPAY, fallback từ session tổng tiền
                $amountFromGateway = isset($_GET['vnp_Amount']) ? ((float)$_GET['vnp_Amount'] / 100) : null;
                $amountFromSession = isset($_SESSION['totalAmount']) ? (float)$_SESSION['totalAmount'] : null;
                $displayAmount = $amountFromGateway !== null && $amountFromGateway > 0 
                    ? $amountFromGateway 
                    : ($amountFromSession !== null ? $amountFromSession : null);
            ?>
            <div class="info-table">
                <div class="info-row">
                    <span class="info-label">Mã đơn hàng:</span>
                    <span class="info-value"><?php echo htmlspecialchars($_GET['vnp_TxnRef']); ?><?php if($isDebug){ echo ' (debug)'; } ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Số tiền:</span>
                    <span class="info-value amount-value">
                        <?php 
                            if ($displayAmount !== null) {
                                echo number_format($displayAmount, 0, ',', '.'); 
                                echo ' ₫';
                            } else {
                                echo '<span class="text-muted">Không có dữ liệu số tiền</span>';
                            }
                        ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nội dung:</span>
                    <span class="info-value"><?php echo htmlspecialchars($_GET['vnp_OrderInfo']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mã giao dịch VNPAY:</span>
                    <span class="info-value"><?php echo htmlspecialchars($_GET['vnp_TransactionNo']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ngân hàng:</span>
                    <span class="info-value"><?php echo htmlspecialchars($_GET['vnp_BankCode']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Thời gian:</span>
                    <span class="info-value"><?php echo htmlspecialchars($_GET['vnp_PayDate']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Trạng thái:</span>
                    <span class="info-value <?php echo $isSuccess ? 'success-value' : 'error-value'; ?>">
                        <?php echo $isSuccess ? 'Thành công' : ($isValidHash ? 'Thất bại' : 'Lỗi xác thực'); ?>
                    </span>
                </div>
                <?php if($isDebug): ?>
                <div class="info-row">
                    <span class="info-label">Debug Mode:</span>
                    <span class="info-value">Bật</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <?php
                // Determine home URL: prefer APP_URL, otherwise compute from host
                if (defined('APP_URL') && APP_URL) {
                    $homeUrl = rtrim(APP_URL,'/').'/Home/index';
                } else {
                    $homeUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
                    $homeUrl = rtrim($homeUrl,'/').'/DQV/Home/index';
                }
                ?>
                <a href="<?php echo htmlspecialchars($homeUrl); ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i> Quay về trang chủ
                </a>
                
                <?php if ($isSuccess): ?>
                <a href="<?php echo htmlspecialchars(rtrim($homeUrl, '/Home/index') . '/Home/orderHistory'); ?>" class="btn btn-secondary">
                    <i class="fas fa-history"></i> Xem lịch sử đơn hàng
                </a>
                <?php endif; ?>
            </div>

            <!-- Bỏ chuyển hướng tự động sau khi thanh toán thành công theo yêu cầu -->
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; VNPAY <?php echo date('Y') ?> - Cổng thanh toán điện tử hàng đầu Việt Nam</p>
        </footer>
    </div>

    <?php
    // Xử lý logic sau khi hiển thị giao diện
    if ($isSuccess) {
        // Instrumentation: log session id & pre-clear cart keys for debug
        @error_log('[VNPAY_RETURN] session_id=' . session_id() . ' pre_cart_keys=' . json_encode(array_keys($_SESSION['cart'] ?? [])));
        
        $orderCode = $_GET['vnp_TxnRef'];
        try {
            $orderRow = $orderModel->getOrderByCode($orderCode);
            if ($orderRow) {
                $existingInfo = $orderRow['transaction_info'] ?? '';
                $firstToken = $existingInfo === '' ? '' : explode('|', $existingInfo)[0];
                $norm = strtolower(preg_replace('/[\s_-]+/', '', $firstToken));
                $alreadyPaid = in_array($norm, ['dathanhtoan', 'dathantoan', 'completed']);
                
                require_once '../models/AdProducModel.php';
                require_once '../models/CartModel.php';
                $productModel = new AdProducModel();
                $cartModel = new CartModel();
                
                @error_log('[VNPAY_RETURN] orderCode=' . $orderCode . ' email_db=' . ($orderRow['user_email'] ?? 'NULL') . ' alreadyPaid=' . ($alreadyPaid ? 'yes' : 'no'));
                
                // Đánh dấu đã thanh toán nếu chưa
                if (!$alreadyPaid) {
                    $orderModel->markPaidPreserveInfo($orderCode);
                }
                
                // Trừ tồn kho (idempotent)
                $details = $orderModel->getOrderDetailsByCode($orderCode);
                foreach ($details as $d) {
                    $masp = $d['product_id'] ?? null;
                    $qty = (int)($d['quantity'] ?? 0);
                    if ($masp && $qty > 0) {
                        $productModel->decrementStock($masp, $qty);
                    }
                }
                
                // Xóa giỏ hàng
                $email = $_SESSION['user']['email'] ?? ($orderRow['user_email'] ?? null);
                if ($email) {
                    try {
                        @error_log('[VNPAY_RETURN] clearing cart DB for email=' . $email);
                        $cartModel->clearCartByEmail(trim($email));
                    } catch (Exception $ex) {
                        @error_log('[VNPAY_RETURN] clearCart DB error ' . $ex->getMessage());
                    }
                }
                
                // Dọn giỏ session
                if (isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                
                @error_log('[VNPAY_RETURN] session cart cleared for orderCode=' . $orderCode);
                $_SESSION['cart_force_cleared'] = 1;
                
                // Gửi email xác nhận đơn hàng (cho đơn đã tồn tại)
                if (isset($_SESSION['user']['email']) && $orderRow) {
                    error_log("[VNPAY_RETURN] Sending email for existing order: " . $orderCode);
                    
                    $snap = $_SESSION['shipping_snapshot'] ?? [];
                    $cartSnap = $_SESSION['cart_snapshot'] ?? [];
                    
                    // Nếu không có trong session, lấy từ database
                    if (empty($cartSnap)) {
                        if (!class_exists('OrderDetailModel')) {
                            require_once '../models/OrderDetailModel.php';
                        }
                        $detailModel = new OrderDetailModel();
                        $orderDetails = $detailModel->getOrderDetailsByOrderId($orderRow['id']);
                        
                        foreach ($orderDetails as $detail) {
                            $cartSnap[] = [
                                'tensp' => $detail['product_name'] ?? '',
                                'qty' => $detail['quantity'] ?? 1,
                                'giaxuat' => $detail['price'] ?? 0,
                                'phantram' => 0,
                                'hinhanh' => $detail['product_image'] ?? ''
                            ];
                        }
                    }
                    
                    // Lấy thông tin giao hàng từ order nếu không có trong session
                    if (empty($snap)) {
                        $snap = [
                            'receiver' => $orderRow['receiver_name'] ?? '',
                            'phone' => $orderRow['receiver_phone'] ?? '',
                            'address' => $orderRow['receiver_address'] ?? '',
                            'shipping_fee' => 30000,
                            'shipping_speed' => 'standard'
                        ];
                    }
                    
                    $shippingSpeedText = 'Giao hàng tiêu chuẩn';
                    if (isset($snap['shipping_speed'])) {
                        if ($snap['shipping_speed'] === 'fast') {
                            $shippingSpeedText = 'Giao hàng nhanh';
                        } elseif ($snap['shipping_speed'] === 'store_pickup') {
                            $shippingSpeedText = 'Nhận tại cửa hàng';
                        }
                    }
                    
                    if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
                        require_once '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
                        require_once '../vendor/phpmailer/phpmailer/src/SMTP.php';
                        require_once '../vendor/phpmailer/phpmailer/src/Exception.php';
                    }
                    
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                    
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'nttv9604@gmail.com';
                        $mail->Password = 'ryae yfan rkle pelu';
                        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        $mail->CharSet = 'UTF-8';
                        
                        $mail->setFrom('nttv9604@gmail.com', 'Cửa hàng DQV');
                        $mail->addAddress($_SESSION['user']['email'], $snap['receiver'] ?? '');
                        
                        $mail->isHTML(true);
                        $mail->Subject = 'Xác nhận đơn hàng #' . $orderCode;
                        
                        // Tạo bảng sản phẩm
                        $productRows = '';
                        $subtotal = 0;
                        foreach ($cartSnap as $item) {
                            $phantram = isset($item['phantram']) ? (float)$item['phantram'] : 0;
                            $giaSauKM = $item['giaxuat'] * (1 - $phantram / 100);
                            $thanhtien = $giaSauKM * $item['qty'];
                            $subtotal += $thanhtien;
                            
                            $productRows .= '<tr>';
                            $productRows .= '<td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($item['tensp']) . '</td>';
                            $productRows .= '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . $item['qty'] . '</td>';
                            $productRows .= '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">' . number_format($giaSauKM, 0, ',', '.') . ' ₫</td>';
                            $productRows .= '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">' . number_format($thanhtien, 0, ',', '.') . ' ₫</td>';
                            $productRows .= '</tr>';
                        }
                        
                        $totalAmount = $orderRow['total_amount'] ?? 0;
                        $shippingFee = $snap['shipping_fee'] ?? 30000;
                        
                        $mail->Body = '
                        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5;">
                            <div style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                <h2 style="color: #0d6efd; margin-bottom: 20px; border-bottom: 2px solid #0d6efd; padding-bottom: 10px;">
                                    Xác nhận đơn hàng
                                </h2>
                                
                                <p style="color: #333; font-size: 16px;">Xin chào <strong>' . htmlspecialchars($snap['receiver'] ?? '') . '</strong>,</p>
                                <p style="color: #666;">Cảm ơn bạn đã đặt hàng tại cửa hàng DQV. Đơn hàng của bạn đã được thanh toán thành công qua VNPAY.</p>
                                
                                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                                    <h3 style="color: #0d6efd; margin-top: 0;">Thông tin đơn hàng</h3>
                                    <p style="margin: 5px 0;"><strong>Mã đơn hàng:</strong> ' . htmlspecialchars($orderCode) . '</p>
                                    <p style="margin: 5px 0;"><strong>Ngày đặt:</strong> ' . date('d/m/Y H:i') . '</p>
                                    <p style="margin: 5px 0;"><strong>Phương thức thanh toán:</strong> VNPAY (Đã thanh toán)</p>
                                </div>
                                
                                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                                    <h3 style="color: #0d6efd; margin-top: 0;">Thông tin giao hàng</h3>
                                    <p style="margin: 5px 0;"><strong>Người nhận:</strong> ' . htmlspecialchars($snap['receiver'] ?? '') . '</p>
                                    <p style="margin: 5px 0;"><strong>Số điện thoại:</strong> ' . htmlspecialchars($snap['phone'] ?? '') . '</p>
                                    <p style="margin: 5px 0;"><strong>Địa chỉ:</strong> ' . htmlspecialchars($snap['address'] ?? '') . '</p>
                                    <p style="margin: 5px 0;"><strong>Hình thức giao hàng:</strong> ' . htmlspecialchars($shippingSpeedText) . ' (' . number_format($shippingFee, 0, ',', '.') . ' ₫)</p>
                                </div>
                                
                                <h3 style="color: #0d6efd;">Chi tiết đơn hàng</h3>
                                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                                    <thead>
                                        <tr style="background-color: #0d6efd; color: white;">
                                            <th style="padding: 10px; text-align: left;">Sản phẩm</th>
                                            <th style="padding: 10px; text-align: center;">SL</th>
                                            <th style="padding: 10px; text-align: right;">Đơn giá</th>
                                            <th style="padding: 10px; text-align: right;">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ' . $productRows . '
                                    </tbody>
                                </table>
                                
                                <div style="text-align: right; margin-top: 20px; padding-top: 15px; border-top: 2px solid #ddd;">
                                    <p style="margin: 5px 0; font-size: 16px;"><strong>Tạm tính:</strong> ' . number_format($subtotal, 0, ',', '.') . ' ₫</p>
                                    <p style="margin: 5px 0; font-size: 16px;"><strong>Phí vận chuyển:</strong> ' . number_format($shippingFee, 0, ',', '.') . ' ₫</p>
                                    <p style="margin: 10px 0; font-size: 20px; color: #dc3545;"><strong>Tổng cộng:</strong> ' . number_format($totalAmount, 0, ',', '.') . ' ₫</p>
                                </div>
                                
                                <div style="margin-top: 30px; padding: 15px; background-color: #d4edda; border-left: 4px solid #28a745; border-radius: 5px;">
                                    <p style="margin: 0; color: #155724;">
                                        <strong>✓ Đã thanh toán:</strong> Đơn hàng của bạn đã được thanh toán thành công qua VNPAY. 
                                        Chúng tôi sẽ xử lý và giao hàng trong thời gian sớm nhất.
                                    </p>
                                </div>
                                
                                <p style="margin-top: 30px; color: #666;">
                                    Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi qua email hoặc hotline.
                                </p>
                                
                                <p style="color: #999; font-size: 14px; margin-top: 20px;">
                                    Trân trọng,<br>
                                    <strong>Đội ngũ DQV</strong>
                                </p>
                            </div>
                        </div>';
                        
                        $mail->send();
                        error_log("[VNPAY_RETURN] Email sent successfully to: " . $_SESSION['user']['email']);
                    } catch (Exception $e) {
                        error_log("[VNPAY_RETURN] Email sending failed: " . $e->getMessage());
                    }
                }
            } else {
                // Nếu chưa có đơn, tạo đơn mới từ snapshot trong session
                require_once '../models/OrderDetailModel.php';
                $detailModel = new OrderDetailModel();
                $userEmail = $_SESSION['user']['email'] ?? null;
                $snap = $_SESSION['shipping_snapshot'] ?? [];
                $cartSnap = $_SESSION['cart_snapshot'] ?? [];
                $created_at = $snap['created_at'] ?? date('Y-m-d H:i:s');
                $transaction_info = trim(($snap['transaction_info'] ?? ''));
                // annotate paid via vnpay
                $transaction_info = ($transaction_info === '' ? '' : ($transaction_info . ' | ')) . 'dathanhtoan';
                $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
                $orderId = $orderModel->createOrderWithShipping(
                    $orderCode,
                    (int)($_SESSION['totalAmount'] ?? 0),
                    $userId,
                    ($userEmail ?? ''),
                    ($snap['receiver'] ?? ''),
                    ($snap['phone'] ?? ''),
                    ($snap['address'] ?? ''),
                    $created_at,
                    $transaction_info
                );
                foreach ($cartSnap as $item) {
                    $phantram = isset($item['phantram']) ? (float)$item['phantram'] : 0;
                    $giaSauKM = $item['giaxuat'] * (1 - $phantram / 100);
                    $thanhtien = $giaSauKM * $item['qty'];
                    $detailModel->addOrderDetail(
                        $orderId,
                        $item['masp'],
                        ($item['capacity_variant_id'] ?? null),
                        ($item['capacity_variant_name'] ?? null),
                        ($item['color_variant_id'] ?? null),
                        ($item['color_variant_name'] ?? null),
                        $item['qty'],
                        $item['giaxuat'],
                        $giaSauKM,
                        $thanhtien,
                        ($item['hinhanh'] ?? ''),
                        ($item['tensp'] ?? '')
                    );
                }
                $orderRow = $orderModel->getOrderByCode($orderCode);
                // Sau khi tạo chi tiết đơn, trừ tồn kho theo snapshot (idempotent vì decrementStock clamp/ghi log)
                require_once '../models/AdProducModel.php';
                $productModel = new AdProducModel();
                foreach ($cartSnap as $item) {
                    $masp = $item['masp'] ?? null;
                    $qty = (int)($item['qty'] ?? 0);
                    if ($masp && $qty > 0) {
                        $ok = $productModel->decrementStock($masp, $qty);
                        @error_log('[VNPAY_RETURN] decremented (created order) ' . $masp . ' qty=' . $qty . ' orderCode=' . $orderCode . ' ok=' . ($ok?1:0));
                    }
                }
                // Sau khi tạo đơn mới: xóa giỏ hàng DB và session
                require_once '../models/CartModel.php';
                $cartModel = new CartModel();
                $emailToClear = $_SESSION['user']['email'] ?? ($userEmail ?? null);
                if ($emailToClear) {
                    try {
                        @error_log('[VNPAY_RETURN] clearing cart DB (created order) for email=' . $emailToClear);
                        $cartModel->clearCartByEmail(trim($emailToClear));
                    } catch (Exception $ex) {
                        @error_log('[VNPAY_RETURN] clearCart DB error (created order) ' . $ex->getMessage());
                    }
                }
                if (isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
            }
            // Đánh dấu đã thanh toán (idempotent)
            $orderModel->markPaidPreserveInfo($orderCode);
            
            // Gửi email xác nhận đơn hàng
            if ($orderRow && isset($_SESSION['user']['email'])) {
                error_log("[VNPAY_RETURN] Attempting to send email confirmation for order: " . $orderCode);
                
                // Lấy thông tin từ order
                $snap = $_SESSION['shipping_snapshot'] ?? [];
                $cartSnap = $_SESSION['cart_snapshot'] ?? [];
                
                // Nếu không có trong session, lấy từ database
                if (empty($cartSnap) && $orderRow) {
                    if (!class_exists('OrderDetailModel')) {
                        require_once '../models/OrderDetailModel.php';
                    }
                    $detailModel = new OrderDetailModel();
                    $orderDetails = $detailModel->getOrderDetailsByOrderId($orderRow['id']);
                    
                    foreach ($orderDetails as $detail) {
                        $cartSnap[] = [
                            'tensp' => $detail['product_name'] ?? '',
                            'qty' => $detail['quantity'] ?? 1,
                            'giaxuat' => $detail['price'] ?? 0,
                            'phantram' => 0,
                            'hinhanh' => $detail['product_image'] ?? ''
                        ];
                    }
                }
                
                $shippingSpeedText = 'Giao hàng tiêu chuẩn';
                if (isset($snap['shipping_speed'])) {
                    if ($snap['shipping_speed'] === 'fast') {
                        $shippingSpeedText = 'Giao hàng nhanh';
                    } elseif ($snap['shipping_speed'] === 'store_pickup') {
                        $shippingSpeedText = 'Nhận tại cửa hàng';
                    }
                }
                
                // Gọi phương thức private bằng reflection hoặc tạo public wrapper
                // Hoặc đơn giản: Gửi mail trực tiếp tại đây
                require_once '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
                require_once '../vendor/phpmailer/phpmailer/src/SMTP.php';
                require_once '../vendor/phpmailer/phpmailer/src/Exception.php';
                
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'nttv9604@gmail.com';
                    $mail->Password = 'ryae yfan rkle pelu';
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';
                    
                    $mail->setFrom('nttv9604@gmail.com', 'Cửa hàng DQV');
                    $mail->addAddress($_SESSION['user']['email'], $snap['receiver'] ?? '');
                    
                    $mail->isHTML(true);
                    $mail->Subject = 'Xác nhận đơn hàng #' . $orderCode;
                    
                    // Tạo bảng sản phẩm
                    $productRows = '';
                    $subtotal = 0;
                    foreach ($cartSnap as $item) {
                        $phantram = isset($item['phantram']) ? (float)$item['phantram'] : 0;
                        $giaSauKM = $item['giaxuat'] * (1 - $phantram / 100);
                        $thanhtien = $giaSauKM * $item['qty'];
                        $subtotal += $thanhtien;
                        
                        $productRows .= '<tr>';
                        $productRows .= '<td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($item['tensp']) . '</td>';
                        $productRows .= '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . $item['qty'] . '</td>';
                        $productRows .= '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">' . number_format($giaSauKM, 0, ',', '.') . ' ₫</td>';
                        $productRows .= '<td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">' . number_format($thanhtien, 0, ',', '.') . ' ₫</td>';
                        $productRows .= '</tr>';
                    }
                    
                    $totalAmount = $orderRow['total_amount'] ?? ($_SESSION['totalAmount'] ?? 0);
                    $shippingFee = $snap['shipping_fee'] ?? 30000;
                    
                    $mail->Body = '
                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5;">
                        <div style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <h2 style="color: #0d6efd; margin-bottom: 20px; border-bottom: 2px solid #0d6efd; padding-bottom: 10px;">
                                Xác nhận đơn hàng
                            </h2>
                            
                            <p style="color: #333; font-size: 16px;">Xin chào <strong>' . htmlspecialchars($snap['receiver'] ?? '') . '</strong>,</p>
                            <p style="color: #666;">Cảm ơn bạn đã đặt hàng tại cửa hàng DQV. Đơn hàng của bạn đã được thanh toán thành công qua VNPAY.</p>
                            
                            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                                <h3 style="color: #0d6efd; margin-top: 0;">Thông tin đơn hàng</h3>
                                <p style="margin: 5px 0;"><strong>Mã đơn hàng:</strong> ' . htmlspecialchars($orderCode) . '</p>
                                <p style="margin: 5px 0;"><strong>Ngày đặt:</strong> ' . date('d/m/Y H:i') . '</p>
                                <p style="margin: 5px 0;"><strong>Phương thức thanh toán:</strong> VNPAY (Đã thanh toán)</p>
                            </div>
                            
                            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                                <h3 style="color: #0d6efd; margin-top: 0;">Thông tin giao hàng</h3>
                                <p style="margin: 5px 0;"><strong>Người nhận:</strong> ' . htmlspecialchars($snap['receiver'] ?? '') . '</p>
                                <p style="margin: 5px 0;"><strong>Số điện thoại:</strong> ' . htmlspecialchars($snap['phone'] ?? '') . '</p>
                                <p style="margin: 5px 0;"><strong>Địa chỉ:</strong> ' . htmlspecialchars($snap['address'] ?? '') . '</p>
                                <p style="margin: 5px 0;"><strong>Hình thức giao hàng:</strong> ' . htmlspecialchars($shippingSpeedText) . ' (' . number_format($shippingFee, 0, ',', '.') . ' ₫)</p>
                            </div>
                            
                            <h3 style="color: #0d6efd;">Chi tiết đơn hàng</h3>
                            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                                <thead>
                                    <tr style="background-color: #0d6efd; color: white;">
                                        <th style="padding: 10px; text-align: left;">Sản phẩm</th>
                                        <th style="padding: 10px; text-align: center;">SL</th>
                                        <th style="padding: 10px; text-align: right;">Đơn giá</th>
                                        <th style="padding: 10px; text-align: right;">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ' . $productRows . '
                                </tbody>
                            </table>
                            
                            <div style="text-align: right; margin-top: 20px; padding-top: 15px; border-top: 2px solid #ddd;">
                                <p style="margin: 5px 0; font-size: 16px;"><strong>Tạm tính:</strong> ' . number_format($subtotal, 0, ',', '.') . ' ₫</p>
                                <p style="margin: 5px 0; font-size: 16px;"><strong>Phí vận chuyển:</strong> ' . number_format($shippingFee, 0, ',', '.') . ' ₫</p>
                                <p style="margin: 10px 0; font-size: 20px; color: #dc3545;"><strong>Tổng cộng:</strong> ' . number_format($totalAmount, 0, ',', '.') . ' ₫</p>
                            </div>
                            
                            <div style="margin-top: 30px; padding: 15px; background-color: #d4edda; border-left: 4px solid #28a745; border-radius: 5px;">
                                <p style="margin: 0; color: #155724;">
                                    <strong>✓ Đã thanh toán:</strong> Đơn hàng của bạn đã được thanh toán thành công qua VNPAY. 
                                    Chúng tôi sẽ xử lý và giao hàng trong thời gian sớm nhất.
                                </p>
                            </div>
                            
                            <p style="margin-top: 30px; color: #666;">
                                Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi qua email hoặc hotline.
                            </p>
                            
                            <p style="color: #999; font-size: 14px; margin-top: 20px;">
                                Trân trọng,<br>
                                <strong>Đội ngũ DQV</strong>
                            </p>
                        </div>
                    </div>';
                    
                    $mail->send();
                    error_log("[VNPAY_RETURN] Email sent successfully to: " . $_SESSION['user']['email']);
                } catch (Exception $e) {
                    error_log("[VNPAY_RETURN] Email sending failed: " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            @error_log('[VNPAY_RETURN] exception ' . $e->getMessage());
        }
    }
    ?>
</body>

</html>