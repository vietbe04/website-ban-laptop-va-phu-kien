<?php
/**
 * Thông tin giao hàng & thanh toán - Thiết kế lại với giao diện hiện đại
 * - Form chính: receiver, email, phone, address, mã giảm giá, chọn phương thức thanh toán.
 * - Nếu chọn VNPAY: JS chặn submit, chuyển dữ liệu sang form ẩn vnpayForm.
 * - Áp dụng mã giảm giá: hiển thị kết quả từ $data['couponResult'].
 * Bảo mật: htmlspecialchars với dữ liệu từ session & mã coupon.
 */
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-truck fs-3 me-3"></i>
                        <div>
                            <h3 class="mb-0 fw-bold">Thông tin giao hàng</h3>
                            <p class="mb-0 opacity-75">Vui lòng điền đầy đủ thông tin để nhận hàng</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form id="shippingForm" action="<?= APP_URL ?>/Home/checkoutSave" method="POST">
                        <!-- Thông tin người nhận -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-person-circle me-2"></i>Thông tin người nhận
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="receiver" class="form-label fw-semibold">
                                        <i class="bi bi-person me-1 text-primary"></i>Tên người nhận
                                    </label>
                                    <input type="text" class="form-control form-control-lg" id="receiver" name="receiver" 
                                           value="<?php echo isset($_SESSION['user']['fullname']) ? htmlspecialchars($_SESSION['user']['fullname']) : ''; ?>" 
                                           placeholder="Nhập họ tên người nhận" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold">
                                        <i class="bi bi-envelope me-1 text-primary"></i>Email
                                    </label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                           value="<?php echo isset($_SESSION['user']['email']) ? htmlspecialchars($_SESSION['user']['email']) : ''; ?>" 
                                           readonly>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold">
                                        <i class="bi bi-telephone me-1 text-primary"></i>Số điện thoại
                                    </label>
                                    <input type="tel" class="form-control form-control-lg" id="phone" name="phone" 
                                           value="<?php echo isset($_SESSION['user']['phone']) ? htmlspecialchars($_SESSION['user']['phone']) : ''; ?>"
                                           placeholder="Nhập số điện thoại" required>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label for="address" class="form-label fw-semibold">
                                        <i class="bi bi-geo-alt me-1 text-primary"></i>Địa chỉ giao hàng
                                    </label>
                                    <textarea class="form-control form-control-lg" id="address" name="address" 
                                              rows="2" placeholder="Nhập địa chỉ chi tiết (số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố)" 
                                              required><?php echo isset($_SESSION['user']['address']) ? htmlspecialchars($_SESSION['user']['address']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Hình thức giao hàng -->
                        <div class="mb-4" id="shipping-section">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-box-seam me-2"></i>Hình thức giao hàng
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check custom-radio-card">
                                        <input class="form-check-input shipping-option" type="radio" name="shipping_speed" id="ship_slow" value="slow" data-fee="30000" checked>
                                        <label class="form-check-label d-flex align-items-center" for="ship_slow">
                                            <i class="bi bi-truck fs-4 me-3 text-info"></i>
                                            <div>
                                                <div class="fw-semibold">Giao hàng tiêu chuẩn</div>
                                                <small class="text-muted">3-5 ngày - Phí: 30.000₫</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-check custom-radio-card">
                                        <input class="form-check-input shipping-option" type="radio" name="shipping_speed" id="ship_fast" value="fast" data-fee="50000">
                                        <label class="form-check-label d-flex align-items-center" for="ship_fast">
                                            <i class="bi bi-lightning-charge fs-4 me-3 text-warning"></i>
                                            <div>
                                                <div class="fw-semibold">Giao hàng nhanh</div>
                                                <small class="text-muted">1-2 ngày - Phí: 50.000₫</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Phương thức thanh toán -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-credit-card me-2"></i>Phương thức thanh toán
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-check custom-radio-card">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="cod" checked>
                                        <label class="form-check-label d-flex align-items-center" for="pay_cod">
                                            <i class="bi bi-cash-coin fs-4 me-3 text-success"></i>
                                            <div>
                                                <div class="fw-semibold">Tiền mặt (COD)</div>
                                                <small class="text-muted">Thanh toán khi nhận hàng</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div class="form-check custom-radio-card">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pay_vnpay" value="vnpay">
                                        <label class="form-check-label d-flex align-items-center" for="pay_vnpay">
                                            <i class="bi bi-credit-card-2-front fs-4 me-3 text-primary"></i>
                                            <div>
                                                <div class="fw-semibold">VNPAY</div>
                                                <small class="text-muted">Thanh toán online qua thẻ</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div class="form-check custom-radio-card">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pay_store" value="store">
                                        <label class="form-check-label d-flex align-items-center" for="pay_store">
                                            <i class="bi bi-shop fs-4 me-3 text-danger"></i>
                                            <div>
                                                <div class="fw-semibold">Tại cửa hàng</div>
                                                <small class="text-muted">Thanh toán khi nhận tại cửa hàng</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Nút xác nhận -->
                        <div class="d-grid">
                            <button type="submit" id="submitBtn" class="btn btn-success btn-lg" name="place_order">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Xác nhận đặt hàng</strong>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Luồng VNPAY sẽ được xử lý trong Home::checkoutSave (redirect tới vnpay_php/vnpay_pay.php) -->
                 </div>
             </div>
         </div>
     </div>
 </div>
 
 <script>
// Submit trực tiếp tới Home/checkoutSave; controller sẽ xử lý redirect VNPAY

// Handle payment method change - disable shipping when store pickup selected
const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
paymentRadios.forEach(radio => {
    radio.addEventListener('change', function() {
        const shippingSection = document.getElementById('shipping-section');
        const shippingOptions = document.querySelectorAll('.shipping-option');
        
        if (this.value === 'store') {
            // Disable shipping options when store pickup selected
            shippingSection.style.opacity = '0.5';
            shippingSection.style.pointerEvents = 'none';
            shippingOptions.forEach(opt => {
                opt.disabled = true;
            });
            
            // Set shipping fee to 0 for store pickup
            const feeDisplay = document.getElementById('shipping-fee-display');
            const totalDisplay = document.getElementById('final-total-display');
            
            if (feeDisplay) {
                feeDisplay.textContent = '0 ₫';
            }
            
            if (totalDisplay) {
                const currentTotal = parseInt(totalDisplay.textContent.replace(/[^\d]/g, ''));
                const currentShippingFee = feeDisplay ? parseInt(feeDisplay.textContent.replace(/[^\d]/g, '')) : 0;
                const newTotal = currentTotal - currentShippingFee;
                totalDisplay.textContent = new Intl.NumberFormat('vi-VN').format(newTotal) + ' ₫';
            }
        } else {
            // Enable shipping options for other payment methods
            shippingSection.style.opacity = '1';
            shippingSection.style.pointerEvents = 'auto';
            shippingOptions.forEach(opt => {
                opt.disabled = false;
            });
            
            // Restore shipping fee
            updateShippingFee();
        }
    });
});

// Handle shipping speed change
const shippingRadios = document.querySelectorAll('input[name="shipping_speed"]');
shippingRadios.forEach(radio => {
    radio.addEventListener('change', function() {
        updateShippingFee();
    });
});

function updateShippingFee() {
    const selectedShipping = document.querySelector('input[name="shipping_speed"]:checked');
    const shippingFee = selectedShipping ? parseInt(selectedShipping.dataset.fee) : 30000;
    
    // Update display in coupon result if exists
    const feeDisplay = document.getElementById('shipping-fee-display');
    const totalDisplay = document.getElementById('final-total-display');
    
    if (feeDisplay) {
        feeDisplay.textContent = new Intl.NumberFormat('vi-VN').format(shippingFee) + ' ₫';
    }
    
    // Recalculate total if coupon result is shown
    if (totalDisplay) {
        const currentTotal = parseInt(totalDisplay.textContent.replace(/[^\d]/g, ''));
        const oldShippingFee = feeDisplay ? parseInt(feeDisplay.textContent.replace(/[^\d]/g, '')) : 30000;
        const newTotal = currentTotal - oldShippingFee + shippingFee;
        totalDisplay.textContent = new Intl.NumberFormat('vi-VN').format(newTotal) + ' ₫';
    }
}

// (Removed duplicate handler and alerts)
</script>

<!-- View-specific styles moved to external CSS -->
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/views/CheckoutInfoView.css" />