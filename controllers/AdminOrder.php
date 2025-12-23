<?php
/**
 * Controller quản trị đơn hàng
 * - Danh sách + lọc, chi tiết, cập nhật trạng thái
 */
class AdminOrder extends Controller {
    /**
     * Danh sách đơn hàng + bộ lọc và phân trang
     */
    public function index(){
        $this->requireRole(['admin', 'staff'], 'admin-orders');
        $orderModel = $this->model('OrderModel');
        // Lọc theo mã đơn, email, trạng thái thô (transaction_info)
        $code = trim($_GET['code'] ?? '');
        $email = trim($_GET['email'] ?? '');
        $status = trim($_GET['status'] ?? '');
        
        // Phân trang
        $itemsPerPage = 15;
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($currentPage - 1) * $itemsPerPage;
        
        // Build WHERE conditions
        $whereSql = "WHERE 1=1";
        $params = [];
        if($code !== ''){ $whereSql .= " AND order_code LIKE ?"; $params[] = "%$code%"; }
        if($email !== ''){ $whereSql .= " AND user_email LIKE ?"; $params[] = "%$email%"; }
        if($status !== ''){ $whereSql .= " AND transaction_info LIKE ?"; $params[] = "$status%"; }
        
        $db = $orderModel->getDb();
        
        // Lấy danh sách đơn hàng có phân trang
        $sql = "SELECT id, order_code, user_email, receiver, phone, address, total_amount, created_at, transaction_info FROM orders $whereSql ORDER BY created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $db->prepare($sql);
        // Bind các tham số WHERE trước
        foreach($params as $index => $param){
            $stmt->bindValue($index + 1, $param);
        }
        // Bind LIMIT và OFFSET riêng với kiểu INTEGER
        $stmt->bindValue(count($params) + 1, $itemsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Đếm tổng số đơn hàng
        $countSql = "SELECT COUNT(*) FROM orders $whereSql";
        $countStmt = $db->prepare($countSql);
        foreach($params as $index => $param){
            $countStmt->bindValue($index + 1, $param);
        }
        $countStmt->execute();
        $totalItems = $countStmt->fetchColumn();
        $totalPages = ceil($totalItems / $itemsPerPage);
        
        $this->view('adminPage', [
            'page'=>'OrderAdminListViewNew',
            'orders'=>$orders,
            'filters'=>['code'=>$code,'email'=>$email,'status'=>$status],
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'total' => $totalItems,
            'offset' => $offset
        ]);
    }
    /**
     * Chi tiết một đơn hàng theo mã
     * @param string $code
     */
    public function detail($code){
        $this->requireRole(['admin', 'staff'], 'admin-orders');
        $orderModel = $this->model('OrderModel');
        $detailModel = $this->model('OrderDetailModel');
        $order = $orderModel->getOrderByCode($code);
        $items = [];
        if($order){ $items = $detailModel->getOrderDetailsByOrderId($order['id']); }
        $this->view('adminPage', ['page'=>'OrderAdminDetailView','order'=>$order,'items'=>$items]);
    }
    /**
     * Cập nhật trạng thái đơn hàng bằng cách ghi đè token đầu của transaction_info
     */
    public function updateStatus(){
        $this->requireRole(['admin', 'staff'], 'admin-orders');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/AdminOrder/index'); exit(); }
        $code = trim($_POST['order_code'] ?? '');
        $newStatus = trim($_POST['status'] ?? '');
        if($code === '' || $newStatus === ''){ header('Location: '.APP_URL.'/AdminOrder/index'); exit(); }
        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderByCode($code);
        if($order){
            $info = $order['transaction_info'] ?? '';
            // Tách phần còn lại sau dấu | nếu có
            $parts = explode('|', $info);
            $rest = '';
            if(count($parts) > 1){ $rest = implode('|', array_slice($parts,1)); }
            $newInfo = $newStatus . ($rest !== '' ? ('|' . $rest) : '');
            $sql = "UPDATE orders SET transaction_info = :ti WHERE order_code = :code";
            $stmt = $orderModel->getDb()->prepare($sql);
            $stmt->execute([':ti'=>$newInfo, ':code'=>$code]);
        }
        header('Location: '.APP_URL.'/AdminOrder/detail/'.$code); exit();
    }
    
    /**
     * In đơn hàng cho khách
     * @param string $code
     */
    public function print($code){
        $this->requireRole(['admin', 'staff'], 'admin-orders');
        $orderModel = $this->model('OrderModel');
        $detailModel = $this->model('OrderDetailModel');
        $order = $orderModel->getOrderByCode($code);
        $items = [];
        if($order){ $items = $detailModel->getOrderDetailsByOrderId($order['id']); }
        $this->view('OrderPrintView', ['order'=>$order,'items'=>$items]);
    }
}