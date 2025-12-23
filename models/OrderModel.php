<?php
require_once 'BaseModel.php';
/**
 * Model đơn hàng: truy vấn/ghi đơn, trạng thái, và chi tiết liên quan
 */
class OrderModel extends BaseModel {
    /**
     * Lấy danh sách dòng chi tiết theo `order_id`
     */
    public function getOrderDetailsByOrderId($orderId) {
        $sql = "SELECT * FROM order_details WHERE order_id = ?";
        return $this->select($sql, [$orderId]);
    }

    protected $table = 'orders';
    /**
     * Lấy danh sách đơn theo `user_id` (mới nhất trước)
     */
    public function getOrdersByUser($userId) {
        $sql = "SELECT * FROM $this->table WHERE user_id = ? ORDER BY created_at DESC";
        return $this->select($sql, [$userId]);
    }
    
    /**
     * Lấy đơn hàng theo mã đơn (order_code)
     */
    public function getOrderByCode($orderCode) {
        $sql = "SELECT * FROM $this->table WHERE order_code = :order_code LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_code' => $orderCode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy đơn hàng theo id (primary key)
     */
    public function getOrderById($id) {
        $sql = "SELECT * FROM $this->table WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết (order_details) theo order id
     */
    public function getOrderItems($orderId) {
        $sql = "SELECT * FROM order_details WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gắn/ghi thêm thông tin vào trường transaction_info (append)
     */
    public function appendTransactionInfo($orderCode, $text) {
        $order = $this->getOrderByCode($orderCode);
        if (!$order) return false;
        $current = $order['transaction_info'] ?? '';
        if ($current === '') $new = $text; else $new = $current . '|' . $text;
        $sql = "UPDATE $this->table SET transaction_info = :tinfo WHERE order_code = :order_code";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':tinfo' => $new, ':order_code' => $orderCode]);
    }
    /**
     * Cập nhật trạng thái/nhật ký giao dịch đơn theo `order_code`
     */
    public function updateOrderStatus($orderCode, $status) {
        $sql = "UPDATE orders SET transaction_info = :transaction_info WHERE order_code = :orderCode";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':transaction_info' => $status,
            ':orderCode' => $orderCode
        ]);
    }
    /**
     * Đánh dấu đơn đã thanh toán (thay thế token đầu tiên bằng 'dathanhtoan') và giữ nguyên thông tin sau.
     * Idempotent nếu đã có trạng thái thanh toán.
     */
    public function markPaidPreserveInfo($orderCode) {
        $order = $this->getOrderByCode($orderCode);
        if (!$order) return false;
        $ti = $order['transaction_info'] ?? '';
        $parts = $ti === '' ? [] : explode('|', $ti);
        $first = $parts[0] ?? '';
        $norm = strtolower(preg_replace('/[\s_-]+/','', $first));
        if (in_array($norm, ['dathantoan','dathanhtoan','completed'])) {
            return true; // đã thanh toán
        }
        if (empty($parts)) {
            $parts = ['dathanhtoan'];
        } else {
            $parts[0] = 'dathanhtoan';
        }
        $newInfo = implode('|', $parts);
        return $this->updateOrderStatus($orderCode, $newInfo);
    }
    /**
     * Tạo đơn hàng kèm thông tin giao/nhận (receiver, phone, address)
     * Trả về ID đơn vừa tạo
     */
    public function createOrderWithShipping($orderCode, $totalAmount, $userId, $userEmail, $receiver, $phone, $address, $created_at, $transaction_info) {
        $sql = "INSERT INTO $this->table (user_id, order_code, total_amount, user_email, receiver, phone, address, created_at, transaction_info) VALUES ("
              . ":user_id, :order_code, :total_amount, :user_email, :receiver, :phone, :address, :created_at, :transaction_info)";
        $stm = $this->db->prepare($sql);
        $stm->execute([
            'user_id' => $userId,
            'order_code' => $orderCode,
            'total_amount' => $totalAmount,
            'user_email' => $userEmail,
            'receiver' => $receiver,
            'phone' => $phone,
            'address' => $address,
            'created_at' => $created_at,
            'transaction_info' => $transaction_info
        ]);
        return $this->getLastInsertId();
    }

    /**
     * Lấy các dòng chi tiết đơn hàng (order_details) theo mã đơn (order_code)
     */
    public function getOrderDetailsByCode($orderCode) {
        $order = $this->getOrderByCode($orderCode);
        if (!$order) return [];
        return $this->getOrderItems($order['id']);
    }

    /**
     * Kiểm tra người dùng đã mua và thanh toán product_id này chưa
     */
    public function userPurchasedProductPaid($email, $productId){
        $sql = "SELECT o.transaction_info FROM orders o JOIN order_details d ON o.id = d.order_id WHERE o.user_email = :email AND d.product_id = :pid ORDER BY o.created_at DESC LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email'=>$email, ':pid'=>$productId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!$rows) return false;
        foreach($rows as $r){
            $ti = $r['transaction_info'] ?? '';
            $status = trim(explode('|',$ti)[0] ?? '');
            $norm = strtolower(preg_replace('/[\s_-]+/','',$status));
            if(in_array($norm, ['dathantoan','dathanhtoan','completed'])) return true;
        }
        return false;
    }

    /**
     * Lấy lịch sử đơn hàng theo email (mới nhất trước)
     */
    public function getOrdersByEmail($email) {
        $sql = "SELECT * FROM $this->table WHERE user_email = ? ORDER BY created_at DESC";
        return $this->select($sql, [$email]);
    }

    /**
     * Lấy lịch sử đơn hàng theo email có phân trang
     */
    public function getOrdersByEmailWithPagination($email, $limit, $offset) {
        // Tránh lỗi LIMIT/OFFSET khi bind tham số (MariaDB/MySQL đôi khi không chấp nhận dạng chuỗi)
        $limit  = (int)$limit;
        $offset = (int)$offset;
        if ($limit <= 0) { $limit = 15; }
        if ($offset < 0) { $offset = 0; }
        $sql = "SELECT * FROM $this->table WHERE user_email = :email ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    




}