<?php
class Controller {
    public function model($model){
       require_once "./models/".$model.".php";
       return new $model;
    }
    public function view($view,$data=array()){
        require_once "./views/".$view.".php";
    }
    /**
     * Kiểm tra quyền truy cập: truyền mảng các role hợp lệ, ví dụ ['admin','staff']
     * Nếu chưa đăng nhập => redirect tới trang login với next
     * Nếu không có quyền => chuyển về trang chủ (có thể thay bằng trang 403)
     */
    public function requireRole(array $allowedRoles, $next = null) {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['user'])) {
            $q = $next ? '?next=' . urlencode($next) : '';
            header('Location: ' . APP_URL . '/AuthController/ShowLogin' . $q);
            exit();
        }
        $role = $_SESSION['user']['role'] ?? 'user';
        if (!in_array($role, $allowedRoles)) {
            // Không có quyền
            $_SESSION['flash_message'] = 'Bạn không có quyền truy cập vào trang này.';
            header('Location: ' . APP_URL . '/Home');
            exit();
        }
    }
}