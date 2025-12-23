<?php
/**
 * Controller mã giảm giá (coupon)
 * - Duy trì endpoint cũ nhưng điều hướng sang trang Discount hợp nhất
 */
class coupon extends Controller
{
    /**
     * Trang coupon cũ: điều hướng sang Discount#coupon hoặc hiển thị nếu cần
     */
    public function show()
    {
        // Giữ endpoint cũ: chuyển hướng sang trang quản lý giảm giá hợp nhất
        $this->requireRole(['admin','staff'], 'coupon');
        header('Location: ' . APP_URL . '/Discount/show#coupon');
        // Nếu muốn giữ logic hiển thị riêng, comment 2 dòng trên và bỏ return
        // return;
        $obj = $this->model('CouponModel');
        $coupons = $obj->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = $_POST['code'] ?? '';
            $type = $_POST['type'] ?? 'percent';
            $value = $_POST['value'] ?? 0;
            $start_date = $_POST['start_date'] ?? null;
            $end_date = $_POST['end_date'] ?? null;
            $status = isset($_POST['status']) ? 1 : 0;
            $min_total = $_POST['min_total'] ?: null;
            $usage_limit = $_POST['usage_limit'] ?: null;

            try {
                $obj->create($code, $type, $value, $start_date, $end_date, $status, $min_total, $usage_limit);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['flash_coupon'] = ['type' => 'success', 'message' => 'Lưu mã giảm giá thành công.'];
            } catch (Exception $e) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['flash_coupon'] = ['type' => 'danger', 'message' => $e->getMessage()];
            }
            header('Location: ' . APP_URL . '/Discount/show#coupon');
            exit();
        }

        $this->view('adminPage', [
            'page' => 'couponView',
            'coupons' => $coupons
        ]);
    }

    /**
     * Sửa một coupon theo id hoặc code
     * @param int|string|null $id
     */
    public function edit($id = null)
    {
        $this->requireRole(['admin','staff'], 'coupon');
        $obj = $this->model('CouponModel');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $code = $_POST['code'] ?? '';
            $type = $_POST['type'] ?? 'percent';
            $value = $_POST['value'] ?? 0;
            $start_date = $_POST['start_date'] ?? null;
            $end_date = $_POST['end_date'] ?? null;
            $status = isset($_POST['status']) ? 1 : 0;
            $min_total = $_POST['min_total'] ?: null;
            $usage_limit = $_POST['usage_limit'] ?: null;

            if ($id) {
                $obj->update($id, $code, $type, $value, $start_date, $end_date, $status, $min_total, $usage_limit);
            }
            header('Location: ' . APP_URL . '/Discount/show#coupon');
            exit();
        }

        $item = $obj->findByCode($id) ?: $obj->findByCode(strtoupper($id));
        // if id is numeric fetch by id (we didn't implement findById), so just query all and filter
        if (!$item && $id) {
            $all = $obj->getAll();
            foreach ($all as $c) {
                if ($c['id'] == $id) { $item = $c; break; }
            }
        }

        $this->view('adminPage', [
            'page' => 'couponEditView',
            'item' => $item
        ]);
    }

    /**
     * Xóa một coupon theo id
     * @param int|string $id
     */
    public function delete($id)
    {
        $this->requireRole(['admin','staff'], 'coupon');
        $obj = $this->model('CouponModel');
        $obj->deleteById($id);
        header('Location: ' . APP_URL . '/Discount/show#coupon');
    }
}
