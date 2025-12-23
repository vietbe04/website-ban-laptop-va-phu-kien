<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="VNPAY - Cổng thanh toán điện tử hàng đầu Việt Nam">
    <meta name="author" content="VNPAY">
    <title>VNPAY - Cổng thanh toán điện tử</title>
    
    <!-- Modern CSS -->
    <link href="assets/vnpay-modern.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/jquery-1.11.3.min.js"></script>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-credit-card"></i> VNPAY</h1>
            <p>Cổng thanh toán điện tử hàng đầu Việt Nam</p>
        </div>

        <!-- Action Cards -->
        <div class="action-buttons">
            <div class="action-card" onclick="pay()">
                <div class="action-icon">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <div class="action-title">Thanh toán</div>
                <div class="action-desc">Thực hiện giao dịch thanh toán qua VNPAY</div>
            </div>
            
            <div class="action-card" onclick="querydr()">
                <div class="action-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="action-title">Tra cứu</div>
                <div class="action-desc">Kiểm tra kết quả giao dịch thanh toán</div>
            </div>
            
            <div class="action-card" onclick="refund()">
                <div class="action-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <div class="action-title">Hoàn tiền</div>
                <div class="action-desc">Xử lý hoàn tiền cho giao dịch</div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; VNPAY <?php echo date('Y')?> - Bản quyền thuộc về Công ty Cổ phần Giải pháp Thanh toán Việt Nam</p>
        </footer>
    </div>

    <script>
        function pay() {
            window.location.href = "vnpay_pay.php";
        }
        
        function querydr() {
            window.location.href = "vnpay_querydr.php";
        }
        
        function refund() {
            window.location.href = "vnpay_refund.php";
        }

        // Add loading effect
        $('.action-card').on('click', function() {
            $(this).addClass('loading');
        });
    </script>
</body>
</html>