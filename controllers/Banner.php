<?php
/**
 * Controller quản lý banner
 */
class Banner extends Controller {
    
    /**
     * Danh sách banner
     */
    public function index() {
        $this->requireRole(['admin'], 'banner-management');
        
        $bannerDir = dirname(__DIR__) . '/public/images/banners/';
        $banners = [];
        if (is_dir($bannerDir)) {
            $files = scandir($bannerDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                    $filePath = $bannerDir . $file;
                    $banners[] = [
                        'name' => $file,
                        'size' => filesize($filePath),
                        'modified' => filemtime($filePath),
                        'url' => APP_URL . '/public/images/banners/' . $file
                    ];
                }
            }
            // Sắp xếp theo thời gian sửa đổi mới nhất
            usort($banners, function($a, $b) {
                return $b['modified'] - $a['modified'];
            });
        }
        
        $this->view('adminPage', [
            'page' => 'BannerListView',
            'banners' => $banners,
            'total' => count($banners)
        ]);
    }
    
    /**
     * Upload nhiều banner cùng lúc
     */
    public function upload() {
        $this->requireRole(['admin'], 'banner-management');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/Banner/index');
            exit();
        }
        
        if (!isset($_FILES['banners']) || empty($_FILES['banners']['name'][0])) {
            $_SESSION['flash_message'] = 'Vui lòng chọn ít nhất một ảnh!';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/Banner/index');
            exit();
        }
        
        $bannerDir = dirname(__DIR__) . '/public/images/banners/';
        
        // Kiểm tra và tạo thư mục nếu chưa tồn tại
        if (!is_dir($bannerDir)) {
            mkdir($bannerDir, 0755, true);
        }
        
        $uploadedCount = 0;
        $errors = [];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        $fileCount = count($_FILES['banners']['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['banners']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            
            $fileName = $_FILES['banners']['name'][$i];
            $fileTmp = $_FILES['banners']['tmp_name'][$i];
            $fileSize = $_FILES['banners']['size'][$i];
            $fileType = $_FILES['banners']['type'][$i];
            
            // Kiểm tra loại file
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "$fileName: Chỉ chấp nhận file ảnh (jpg, png, gif, webp)";
                continue;
            }
            
            // Kiểm tra kích thước
            if ($fileSize > $maxSize) {
                $errors[] = "$fileName: Kích thước vượt quá 5MB";
                continue;
            }
            
            // Tạo tên file unique
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = date('d_M') . md5(uniqid() . $fileName) . '.' . $ext;
            $destination = $bannerDir . $newFileName;
            
            // Upload file
            if (move_uploaded_file($fileTmp, $destination)) {
                // Resize ảnh nếu cần (giữ tỷ lệ, max width 1920px)
                $this->resizeImage($destination, 1920);
                $uploadedCount++;
            } else {
                $errors[] = "$fileName: Không thể upload";
            }
        }
        
        // Thông báo kết quả
        if ($uploadedCount > 0) {
            $_SESSION['flash_message'] = "Đã upload thành công $uploadedCount banner!";
            $_SESSION['flash_type'] = 'success';
        }
        
        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
        }
        
        header('Location: ' . APP_URL . '/Banner/index');
        exit();
    }
    
    /**
     * Xóa một banner
     */
    public function delete($fileName) {
        $this->requireRole(['admin'], 'banner-management');
        
        if (empty($fileName)) {
            $_SESSION['flash_message'] = 'Tên file không hợp lệ!';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/Banner/index');
            exit();
        }
        
        $bannerDir = dirname(__DIR__) . '/public/images/banners/';
        
        // Decode tên file (nếu có ký tự đặc biệt)
        $fileName = urldecode($fileName);
        $filePath = $bannerDir . $fileName;
        
        if (file_exists($filePath) && is_file($filePath)) {
            if (unlink($filePath)) {
                $_SESSION['flash_message'] = 'Đã xóa banner thành công!';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Không thể xóa banner!';
                $_SESSION['flash_type'] = 'danger';
            }
        } else {
            $_SESSION['flash_message'] = 'Banner không tồn tại!';
            $_SESSION['flash_type'] = 'warning';
        }
        
        header('Location: ' . APP_URL . '/Banner/index');
        exit();
    }
    
    /**
     * Xóa nhiều banner cùng lúc
     */
    public function deleteMultiple() {
        $this->requireRole(['admin'], 'banner-management');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/Banner/index');
            exit();
        }
        
        $fileNames = $_POST['selected_banners'] ?? [];
        
        if (empty($fileNames)) {
            $_SESSION['flash_message'] = 'Vui lòng chọn ít nhất một banner để xóa!';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/Banner/index');
            exit();
        }
        
        $bannerDir = dirname(__DIR__) . '/public/images/banners/';
        $deletedCount = 0;
        foreach ($fileNames as $fileName) {
            $filePath = $bannerDir . $fileName;
            if (file_exists($filePath) && is_file($filePath)) {
                if (unlink($filePath)) {
                    $deletedCount++;
                }
            }
        }
        
        $_SESSION['flash_message'] = "Đã xóa $deletedCount banner!";
        $_SESSION['flash_type'] = 'success';
        
        header('Location: ' . APP_URL . '/Banner/index');
        exit();
    }
    
    /**
     * Resize ảnh để tối ưu
     */
    private function resizeImage($filePath, $maxWidth = 1920) {
        $info = getimagesize($filePath);
        if (!$info) return false;
        
        list($width, $height) = $info;
        
        // Không cần resize nếu ảnh đã nhỏ hơn maxWidth
        if ($width <= $maxWidth) return true;
        
        $ratio = $maxWidth / $width;
        $newWidth = $maxWidth;
        $newHeight = (int)($height * $ratio);
        
        // Tạo ảnh từ file gốc
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filePath);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($filePath);
                break;
            default:
                return false;
        }
        
        // Tạo ảnh mới với kích thước đã resize
        $destination = imagecreatetruecolor($newWidth, $newHeight);
        
        // Giữ trong suốt cho PNG và GIF
        if ($info[2] == IMAGETYPE_PNG || $info[2] == IMAGETYPE_GIF) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Copy và resize
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Lưu lại file
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                imagejpeg($destination, $filePath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($destination, $filePath, 8);
                break;
            case IMAGETYPE_GIF:
                imagegif($destination, $filePath);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($destination, $filePath, 90);
                break;
        }
        
        // Giải phóng bộ nhớ
        imagedestroy($source);
        imagedestroy($destination);
        
        return true;
    }
}
