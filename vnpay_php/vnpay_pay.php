<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Tạo mới đơn hàng - VNPAY">
    <meta name="author" content="VNPAY">
    <title>Tạo mới đơn hàng - VNPAY</title>
    
    <!-- Modern CSS -->
    <link href="assets/vnpay-modern.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/jquery-1.11.3.min.js"></script>
</head>

<body>
    <?php 
    require_once("./config.php"); 
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    // Fallback: compute from session cart if totalAmount not set
    $amount = isset($_SESSION['totalAmount']) ? (float)$_SESSION['totalAmount'] : null;
    if ($amount === null || $amount <= 0) {
        $amount = 0;
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $phantram = isset($item['phantram']) ? (float)$item['phantram'] : 0;
                $giaSauKM = (float)$item['giaxuat'] * (1 - $phantram / 100);
                $amount += $giaSauKM * (int)($item['qty'] ?? 1);
            }
        }
    }
    if ($amount <= 0) { $amount = 10000; } // minimal fallback
    ?>            
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-shopping-cart"></i> Tạo mới đơn hàng</h1>
            <p>Vui lòng chọn phương thức thanh toán phù hợp</p>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Thông tin thanh toán</h2>
            </div>
            
            <form action="vnpay_create_payment.php" id="frmCreateOrder" method="post">
                <!-- Amount Section -->
                <div class="form-group">
                    <label class="form-label" for="amount">
                        <i class="fas fa-money-bill-wave"></i> Số tiền thanh toán
                    </label>
                    <div style="position: relative;">
                           <input class="form-control" id="amount" max="100000000" min="1" name="amount" type="number" readonly
                               value="<?php echo (int)round($amount,0);?>" style="padding-left: 40px;" />
                        <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-light); font-weight: bold;">₫</span>
                    </div>
                    <small style="color: var(--text-light); margin-top: 5px; display: block;">
                        Số tiền đã được điền tự động từ giỏ hàng của bạn
                    </small>
                </div>

                <!-- Payment Methods -->
                <div class="payment-methods">
                    <div class="method-section">
                        <div class="method-title">
                            <i class="fas fa-credit-card"></i> Cách 1: Chuyển hướng sang Cổng VNPAY
                        </div>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" Checked="True" id="bankCode_default" name="bankCode" value="">
                                <label for="bankCode_default">
                                    <strong>Cổng thanh toán VNPAYQR</strong><br>
                                    <small style="color: var(--text-light);">Tự động chọn phương thức tối ưu</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="method-section">
                        <div class="method-title">
                            <i class="fas fa-list-ul"></i> Cách 2: Chọn phương thức tại đây
                        </div>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" id="bankCode_vnpayqr" name="bankCode" value="VNPAYQR">
                                <label for="bankCode_vnpayqr">
                                    <strong><i class="fas fa-qrcode"></i> VNPAYQR</strong><br>
                                    <small style="color: var(--text-light);">Quét mã QR để thanh toán nhanh chóng</small>
                                </label>
                            </div>
                            
                            <div class="radio-item">
                                <input type="radio" id="bankCode_atm" name="bankCode" value="VNBANK">
                                <label for="bankCode_atm">
                                    <strong><i class="fas fa-university"></i> Thẻ ATM/Tài khoản nội địa</strong><br>
                                    <small style="color: var(--text-light);">Thanh toán bằng thẻ ngân hàng trong nước</small>
                                </label>
                            </div>
                            
                            <div class="radio-item">
                                <input type="radio" id="bankCode_intcard" name="bankCode" value="INTCARD">
                                <label for="bankCode_intcard">
                                    <strong><i class="fas fa-globe"></i> Thẻ quốc tế</strong><br>
                                    <small style="color: var(--text-light);">Visa, Mastercard, JCB, UnionPay</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Language Selection -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-language"></i> Ngôn ngữ giao diện thanh toán
                    </label>
                    <div class="language-selector">
                        <div class="radio-item language-option">
                            <input type="radio" Checked="True" id="language_vn" name="language" value="vn">
                            <label for="language_vn">
                                <strong><i class="fas fa-flag"></i> Tiếng Việt</strong><br>
                                <small style="color: var(--text-light);">Giao diện tiếng Việt</small>
                            </label>
                        </div>
                        
                        <div class="radio-item language-option">
                            <input type="radio" id="language_en" name="language" value="en">
                            <label for="language_en">
                                <strong><i class="fas fa-flag-usa"></i> Tiếng Anh</strong><br>
                                <small style="color: var(--text-light);">English interface</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        <i class="fas fa-lock"></i> Thanh toán an toàn
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Notice -->
        <div style="text-align: center; margin: 20px 0; color: white; opacity: 0.8;">
            <small><i class="fas fa-shield-alt"></i> Giao dịch được bảo vệ bởi VNPAY với công nghệ mã hóa SSL 256-bit</small>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; VNPAY <?php echo date('Y')?> - Bản quyền thuộc về Công ty Cổ phần Giải pháp Thanh toán Việt Nam</p>
        </footer>
    </div>

    <script>
        $(document).ready(function() {
            // Add loading effect to submit button
            $('#frmCreateOrder').on('submit', function() {
                $('#submitBtn').addClass('loading').html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
            });

            // Auto-check payment method when clicking on radio item
            $('.radio-item').on('click', function() {
                $(this).find('input[type="radio"]').prop('checked', true);
            });

            // Format currency display
            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount);
            }

            // Keep numeric amount; do not reformat input value
        });
    </script>
</body>
</html>