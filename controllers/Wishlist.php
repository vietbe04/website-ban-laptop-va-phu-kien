<?php
/**
 * Controller xử lý Wishlist và So sánh sản phẩm
 */
class Wishlist extends Controller {
    
    /**
     * Thêm sản phẩm vào wishlist
     */
    public function add() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập', 'count' => 0]);
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = trim($_POST['product_id'] ?? '');
            $action = trim($_POST['action'] ?? '');
            
            $wishlistModel = $this->model('WishlistModel');
            $userEmail = $_SESSION['user']['email'];
            
            // Nếu chỉ lấy số lượng
            if ($action === 'count') {
                $count = $wishlistModel->countWishlist($userEmail);
                echo json_encode(['success' => true, 'count' => $count]);
                exit();
            }
            
            if (empty($productId)) {
                echo json_encode(['success' => false, 'message' => 'Mã sản phẩm không hợp lệ']);
                exit();
            }
            
            if ($wishlistModel->addToWishlist($userEmail, $productId)) {
                $count = $wishlistModel->countWishlist($userEmail);
                echo json_encode(['success' => true, 'message' => 'Đã thêm vào yêu thích', 'count' => $count]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể thêm vào yêu thích']);
            }
        }
        exit();
    }
    
    /**
     * Xóa sản phẩm khỏi wishlist
     */
    public function remove() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = trim($_POST['product_id'] ?? '');
            
            $wishlistModel = $this->model('WishlistModel');
            $userEmail = $_SESSION['user']['email'];
            
            if ($wishlistModel->removeFromWishlist($userEmail, $productId)) {
                $count = $wishlistModel->countWishlist($userEmail);
                echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi yêu thích', 'count' => $count]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa']);
            }
        }
        exit();
    }

    /**
     * Xóa toàn bộ wishlist hiện tại của người dùng
     */
    public function clear() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $wishlistModel = $this->model('WishlistModel');
            $userEmail = $_SESSION['user']['email'];

            if ($wishlistModel->clearWishlist($userEmail)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã xóa toàn bộ danh sách yêu thích',
                    'count' => 0
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa danh sách yêu thích']);
            }
        }
        exit();
    }
    
    /**
     * Hiển thị trang wishlist
     */
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['user'])) {
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }
        
        $wishlistModel = $this->model('WishlistModel');
        $promoModel = $this->model('AdKhuyenMai');
        $reviewModel = $this->model('ReviewModel');
        $userEmail = $_SESSION['user']['email'];
        
        $wishlist = $wishlistModel->getUserWishlist($userEmail);
        
        // Thêm thông tin khuyến mãi và đánh giá
        foreach ($wishlist as &$item) {
            $promo = $promoModel->findWithDiscount($item['product_id']);
            $item['phantram'] = $promo['phantram'] ?? 0;
            
            $rating = $reviewModel->getAverageRating($item['product_id']);
            $item['avg_rating'] = $rating['avg'] ?? 0;
            $item['rating_count'] = $rating['count'] ?? 0;
        }
        
        $this->view('homePage', [
            'page' => 'WishlistView',
            'wishlist' => $wishlist
        ]);
    }
    
    /**
     * Thêm sản phẩm vào danh sách so sánh (lưu trong session)
     */
    public function addToCompare() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = trim($_POST['product_id'] ?? '');
            $action = trim($_POST['action'] ?? '');
            
            if (!isset($_SESSION['compare'])) {
                $_SESSION['compare'] = [];
            }
            
            // Nếu chỉ lấy số lượng
            if ($action === 'count') {
                echo json_encode(['success' => true, 'count' => count($_SESSION['compare'])]);
                exit();
            }
            
            if (empty($productId)) {
                echo json_encode(['success' => false, 'message' => 'Mã sản phẩm không hợp lệ']);
                exit();
            }
            
            // Giới hạn tối đa 4 sản phẩm để so sánh
            if (count($_SESSION['compare']) >= 4) {
                echo json_encode(['success' => false, 'message' => 'Chỉ có thể so sánh tối đa 4 sản phẩm']);
                exit();
            }
            
            if (in_array($productId, $_SESSION['compare'])) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm đã có trong danh sách so sánh']);
                exit();
            }
            
            $_SESSION['compare'][] = $productId;
            echo json_encode(['success' => true, 'message' => 'Đã thêm vào so sánh', 'count' => count($_SESSION['compare'])]);
        }
        exit();
    }
    
    /**
     * Xóa sản phẩm khỏi danh sách so sánh
     */
    public function removeFromCompare() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = trim($_POST['product_id'] ?? '');
            
            if (isset($_SESSION['compare'])) {
                $_SESSION['compare'] = array_values(array_filter($_SESSION['compare'], function($id) use ($productId) {
                    return $id !== $productId;
                }));
                
                echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi so sánh', 'count' => count($_SESSION['compare'])]);
            }
        }
        exit();
    }
    
    /**
     * Hiển thị trang so sánh sản phẩm
     */
    public function compare() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $compareIds = $_SESSION['compare'] ?? [];
        
        if (empty($compareIds)) {
            $this->view('homePage', [
                'page' => 'CompareView',
                'products' => []
            ]);
            return;
        }
        
        $promoModel = $this->model('AdKhuyenMai');
        $reviewModel = $this->model('ReviewModel');
        
        $products = [];
        foreach ($compareIds as $productId) {
            // Sử dụng findWithDiscount để lấy thông tin sản phẩm và khuyến mãi
            $product = $promoModel->findWithDiscount($productId);
            if ($product) {
                $rating = $reviewModel->getAverageRating($productId);
                $product['avg_rating'] = $rating['avg'] ?? 0;
                $product['rating_count'] = $rating['count'] ?? 0;
                
                $products[] = $product;
            }
        }
        
        $this->view('homePage', [
            'page' => 'CompareView',
            'products' => $products
        ]);
    }
    
    /**
     * Xóa toàn bộ danh sách so sánh
     */
    public function clearCompare() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['compare'] = [];
        header('Location: ' . APP_URL . '/Wishlist/compare');
        exit();
    }
    
    /**
     * Lấy danh sách wishlist của user (trả về JSON)
     */
    public function getUserWishlist() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        header('Content-Type: application/json');
        
        if (empty($_SESSION['user'])) {
            echo json_encode(['success' => false, 'wishlist' => []]);
            exit();
        }
        
        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['user']['email'];
        $wishlist = $wishlistModel->getUserWishlist($userEmail);
        
        // Chỉ trả về array các product_id
        $productIds = array_column($wishlist, 'product_id');
        
        echo json_encode(['success' => true, 'wishlist' => $productIds]);
        exit();
    }
    
    /**
     * Lấy danh sách compare (trả về JSON)
     */
    public function getCompareList() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        header('Content-Type: application/json');
        
        $compareList = $_SESSION['compare'] ?? [];
        
        echo json_encode(['success' => true, 'compare' => $compareList]);
        exit();
    }
}
