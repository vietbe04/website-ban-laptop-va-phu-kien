<?php
/**
 * Controller khuyến mãi cũ (giữ endpoint) – điều hướng sang trang Discount hợp nhất
 */
class khuyenmai extends Controller
{
    /**
     * Điều hướng sang Discount#promo
     */
    public function show()
    {
        // Giữ endpoint cũ nhưng điều hướng sang trang quản lý giảm giá hợp nhất
        $this->requireRole(['admin','staff'], 'promotion');
        header('Location: ' . APP_URL . '/Discount/show#promo');
        exit();
    }

    /**
     * Xóa khuyến mãi theo id
     * @param int|string $km_id
     */
    public function delete($km_id)
    {
        $this->requireRole(['admin','staff'], 'promotion');
        $obj = $this->model("AdKhuyenMai");
        $obj->deleteKm($km_id);
        header("Location: " . APP_URL . "/khuyenmai/show");
    }
    /**
     * Thêm khuyến mãi (áp cho loại hoặc sản phẩm)
     */
    public function add()
    {
        $this->requireRole(['admin','staff'], 'promotion');
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $maLoaiSP = $_POST["maLoaiSP"] ?? "";
            $masp = $_POST["masp"] ?? "";
            $phantram = $_POST["phantram"];
            $ngaybatdau = $_POST["ngaybatdau"];
            $ngayketthuc = $_POST["ngayketthuc"];

            $obj = $this->model("AdKhuyenMai");

            try {
                if (empty($masp)) {
                    // Nếu không chọn sản phẩm cụ thể => áp dụng cho cả loại
                    $obj->insertForCategory($maLoaiSP, $phantram, $ngaybatdau, $ngayketthuc);
                } else {
                    // Ngược lại => áp dụng cho 1 sản phẩm
                    $obj->insert($maLoaiSP, $masp, $phantram, $ngaybatdau, $ngayketthuc);
                }
                // success flash
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['flash_promo'] = ['type' => 'success', 'message' => 'Thêm khuyến mãi thành công.'];
            } catch (Exception $e) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['flash_promo'] = ['type' => 'danger', 'message' => $e->getMessage()];
            }
        }

        header("Location: " . APP_URL . "/Discount/show#promo");
    }

    /**
     * Sửa khuyến mãi theo id (GET form/POST lưu)
     * @param int|string|null $km_id
     */
    public function edit($km_id = null)
    {
        $this->requireRole(['admin','staff'], 'promotion');
        $obj = $this->model("AdKhuyenMai");
        $obj2 = $this->model("AdProducModel");
        $products = $obj2->all("tblsanpham");
        $dataView = $obj->getView();

        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $km_id = $_POST['km_id'] ?? null;
            $maLoaiSP = $_POST['maLoaiSP'] ?? '';
            $masp = $_POST['masp'] ?? '';
            $phantram = $_POST['phantram'] ?? 0;
            $ngaybatdau = $_POST['ngaybatdau'] ?? null;
            $ngayketthuc = $_POST['ngayketthuc'] ?? null;

            try {
                if ($km_id) {
                    $obj->updateKm($km_id, $maLoaiSP, $masp, $phantram, $ngaybatdau, $ngayketthuc);
                }
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['flash_promo'] = ['type' => 'success', 'message' => 'Cập nhật khuyến mãi thành công.'];
            } catch (Exception $e) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['flash_promo'] = ['type' => 'danger', 'message' => $e->getMessage()];
            }

            header("Location: " . APP_URL . "/Discount/show#promo");
            exit();
        }

        // GET: show edit form
        $item = $obj->findById($km_id);

        $this->view('adminPage', [
            'page' => 'khuyenmaiEditView',
            'products' => $products,
            'dataView' => $dataView,
            'item' => $item
        ]);
    }
}
