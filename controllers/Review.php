<?php
/**
 * Controller đánh giá sản phẩm
 * - Gửi đánh giá (user đã mua), duyệt/xóa (admin)
 */
class Review extends Controller {
    /**
     * Gửi đánh giá sản phẩm của người dùng đã thanh toán
     */
    public function submit(){
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['user'])){ header('Location: '.APP_URL.'/AuthController/ShowLogin'); exit(); }
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/Home'); exit(); }
        $email = $_SESSION['user']['email'];
        $fullname = $_SESSION['user']['fullname'] ?? ($email);
        $productId = trim($_POST['product_id'] ?? '');
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        if($productId === '' || $rating < 1 || $rating > 5){ $_SESSION['flash_message']='Dữ liệu đánh giá không hợp lệ.'; header('Location: '.APP_URL.'/Home/detail/'.$productId); exit(); }
        $orderModel = $this->model('OrderModel');
        if(!$orderModel->userPurchasedProductPaid($email, $productId)){
            $_SESSION['flash_message'] = 'Bạn chỉ có thể đánh giá sau khi đã thanh toán thành công cho sản phẩm này.';
            header('Location: '.APP_URL.'/Home/detail/'.$productId); exit();
        }
        $reviewModel = $this->model('ReviewModel');
        if($reviewModel->hasUserReviewed($email,$productId)){
            $_SESSION['flash_message'] = 'Bạn đã gửi đánh giá cho sản phẩm này.';
            header('Location: '.APP_URL.'/Home/detail/'.$productId); exit();
        }
        
        // Xử lý upload ảnh
        $uploadedImages = [];
        if (isset($_FILES['review_images']) && !empty($_FILES['review_images']['name'][0])) {
            $uploadDir = 'public/images/reviews/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $maxImages = 5;
            $maxFileSize = 5 * 1024 * 1024; // 5MB
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            
            $fileCount = min(count($_FILES['review_images']['name']), $maxImages);
            
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['review_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $fileSize = $_FILES['review_images']['size'][$i];
                    $fileType = $_FILES['review_images']['type'][$i];
                    $fileTmpName = $_FILES['review_images']['tmp_name'][$i];
                    $fileName = $_FILES['review_images']['name'][$i];
                    
                    // Validate file
                    if ($fileSize > $maxFileSize) {
                        continue; // Skip file quá lớn
                    }
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        continue; // Skip file không đúng định dạng
                    }
                    
                    // Tạo tên file unique
                    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                    $newFileName = 'review_' . $productId . '_' . time() . '_' . $i . '.' . $fileExt;
                    $targetPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($fileTmpName, $targetPath)) {
                        $uploadedImages[] = $newFileName;
                    }
                }
            }
        }
        
        // Lưu đánh giá với ảnh (JSON encode)
        $imagesJson = !empty($uploadedImages) ? json_encode($uploadedImages) : null;
        $reviewModel->addReview($email,$fullname,$productId,$rating,$comment,$imagesJson);
        $_SESSION['flash_message'] = 'Đã gửi đánh giá. Vui lòng đợi duyệt.';
        header('Location: '.APP_URL.'/Home/detail/'.$productId); exit();
    }

    /**
     * Admin: danh sách đánh giá có lọc + phân trang
     */
    public function adminIndex(){
        $this->requireRole(['admin'], 'admin-reviews');
        $filters = [
            'product_id' => trim($_GET['product_id'] ?? ''),
            'rating' => trim($_GET['rating'] ?? ''),
            'approved' => trim($_GET['approved'] ?? '')
        ];
        
        // Phân trang
        $itemsPerPage = 15;
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($currentPage - 1) * $itemsPerPage;
        
        $reviewModel = $this->model('ReviewModel');
        $total = $reviewModel->countAdminSearch($filters);
        $reviews = $reviewModel->adminSearch($filters, $itemsPerPage, $offset);
        $totalPages = max(1, ceil($total / $itemsPerPage));
        
        $this->view('adminPage', [
            'page'=>'ReviewAdminView',
            'reviews'=>$reviews,
            'filters'=>$filters,
            'currentPage'=>$currentPage,
            'totalPages'=>$totalPages,
            'total'=>$total
        ]);
    }

    /**
     * Admin: duyệt hoặc bỏ duyệt một đánh giá
     */
    public function approve(){
        $this->requireRole(['admin'], 'admin-reviews');
        if($_SERVER['REQUEST_METHOD']!=='POST'){ header('Location: '.APP_URL.'/Review/adminIndex'); exit(); }
        $id = (int)($_POST['id'] ?? 0); $approved = (int)($_POST['approved'] ?? 0);
        $m = $this->model('ReviewModel'); $m->setApproved($id,$approved);
        header('Location: '.APP_URL.'/Review/adminIndex'); exit();
    }

    /**
     * Admin: xóa một đánh giá theo id
     */
    public function delete(){
        $this->requireRole(['admin'], 'admin-reviews');
        if($_SERVER['REQUEST_METHOD']!=='POST'){ header('Location: '.APP_URL.'/Review/adminIndex'); exit(); }
        $id = (int)($_POST['id'] ?? 0); $m = $this->model('ReviewModel'); $m->deleteById($id);
        header('Location: '.APP_URL.'/Review/adminIndex'); exit();
    }
}