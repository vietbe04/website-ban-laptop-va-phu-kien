<?php
/* Payment Notify
 * IPN URL: Ghi nhận kết quả thanh toán từ VNPAY
 * Các bước thực hiện:
 * Kiểm tra checksum 
 * Tìm giao dịch trong database
 * Kiểm tra số tiền giữa hai hệ thống
 * Kiểm tra tình trạng của giao dịch trước khi cập nhật
 * Cập nhật kết quả vào Database
 * Trả kết quả ghi nhận lại cho VNPAY
 */

require_once("./config.php");
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../models/OrderModel.php';
require_once '../models/AdProducModel.php';
require_once '../models/CartModel.php';
require_once '../app/DB.php';

$inputData = [];
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) === 'vnp_') { $inputData[$key] = $value; }
}
$returnData = [];

if (!isset($inputData['vnp_SecureHash'])) {
    echo json_encode(['RspCode'=>'98','Message'=>'Missing secure hash']);
    exit();
}

$vnp_SecureHash = $inputData['vnp_SecureHash'];
unset($inputData['vnp_SecureHash']);
ksort($inputData);
$hashData = '';
foreach ($inputData as $key => $value) {
    $hashData .= $key . '=' . $value . '&';
}
$hashData = rtrim($hashData,'&');
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

$orderCode = $inputData['vnp_TxnRef'] ?? '';
$responseCode = $inputData['vnp_ResponseCode'] ?? '';
$txnStatus = $inputData['vnp_TransactionStatus'] ?? '';
$amount = isset($inputData['vnp_Amount']) ? ($inputData['vnp_Amount'] / 100) : 0;

try {
    if ($secureHash !== $vnp_SecureHash) {
        $returnData = ['RspCode'=>'97','Message'=>'Invalid signature'];
    } else {
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderByCode($orderCode);
        if (!$order) {
            $returnData = ['RspCode'=>'01','Message'=>'Order not found'];
        } else {
            // Kiểm tra số tiền khớp (nếu muốn chặt chẽ hơn so sánh với order['total_amount'])
            $expectedAmount = (float)($order['total_amount'] ?? 0);
            if ($expectedAmount > 0 && abs($expectedAmount - $amount) > 0.01) {
                $returnData = ['RspCode'=>'04','Message'=>'Invalid amount'];
            } else {
                // Idempotent check trạng thái
                $ti = $order['transaction_info'] ?? '';
                $firstToken = $ti === '' ? '' : explode('|',$ti)[0];
                $norm = strtolower(preg_replace('/[\s_-]+/','', $firstToken));
                $alreadyPaid = in_array($norm,['dathanhtoan','dathantoan','completed']);
                if ($responseCode === '00' && $txnStatus === '00') {
                    // Thành công (luôn cố gắng finalize, idempotent bên trong)
                    try {
                        @error_log('[VNPAY_IPN] orderCode=' . $orderCode . ' email_db=' . ($order['user_email'] ?? 'NULL') . ' alreadyPaid=' . ($alreadyPaid?'yes':'no'));
                        if (!$alreadyPaid) { $orderModel->markPaidPreserveInfo($orderCode); @error_log('[VNPAY_IPN] markPaid executed for ' . $orderCode); }
                        $details = $orderModel->getOrderDetailsByCode($orderCode);
                        $productModel = new AdProducModel();
                        foreach ($details as $d) {
                            $masp = $d['product_id'] ?? null; $qty = (int)($d['quantity'] ?? 0);
                            if ($masp && $qty > 0) { $productModel->decrementStock($masp, $qty); }
                        }
                        $cartModel = new CartModel();
                        $email = $_SESSION['user']['email'] ?? ($order['user_email'] ?? null);
                        if ($email) { @error_log('[VNPAY_IPN] clearing cart DB for email=' . $email); $cartModel->clearCartByEmail(trim($email)); }
                        if (isset($_SESSION['cart'])) { $_SESSION['cart'] = []; @error_log('[VNPAY_IPN] session cart cleared for orderCode=' . $orderCode); }
                    } catch (Exception $fe) { /* ignore finalize errors */ }
                    $returnData = ['RspCode'=>'00','Message'=>'Confirm Success'];
                } else {
                    // Thất bại
                    $returnData = ['RspCode'=>'00','Message'=>'Confirm Failed']; // vẫn trả 00 để VNPAY không retry
                }
            }
        }
    }
} catch (Exception $e) {
    $returnData = ['RspCode'=>'99','Message'=>'Unknown error'];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($returnData);
