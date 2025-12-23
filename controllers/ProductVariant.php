<?php
/**
 * Controller quản lý biến thể sản phẩm (màu sắc/dung lượng)
 */
class ProductVariant extends Controller {
    /**
     * Quản lý danh sách biến thể của một sản phẩm (phân trang)
     * @param string $masp
     */
    public function manage($masp) {
        $this->requireRole(['admin','staff'], 'product');
        $variantModel = $this->model('ProductVariantModel');
        $productModel = $this->model('AdProducModel');
        $product = $productModel->find('tblsanpham', $masp);
        
        // Phân trang
        $itemsPerPage = 15;
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($currentPage - 1) * $itemsPerPage;
        
        // Lấy biến thể có phân trang
        $sql = "SELECT * FROM product_variants WHERE masp = :masp ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $variantModel->getDb()->prepare($sql);
        $stmt->bindValue(':masp', $masp);
        $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $variants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Đếm tổng số biến thể
        $countSql = "SELECT COUNT(*) FROM product_variants WHERE masp = :masp";
        $countStmt = $variantModel->getDb()->prepare($countSql);
        $countStmt->execute([':masp' => $masp]);
        $totalItems = $countStmt->fetchColumn();
        $totalPages = ceil($totalItems / $itemsPerPage);
        
        $this->view('adminPage', [
            'page' => 'VariantManagerView',
            'product' => $product,
            'variants' => $variants,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'total' => $totalItems,
            'offset' => $offset
        ]);
    }

    /**
     * Tạo biến thể mới cho sản phẩm (POST)
     * @param string $masp
     */
    public function create($masp) {
        $this->requireRole(['admin','staff'], 'product');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['variant_type'] ?? 'color';
            $value = trim($_POST['variant_value'] ?? '');
            $price = null;
            if ($type === 'capacity') {
                $priceInput = $_POST['variant_price'] ?? '';
                $price = $priceInput !== '' ? (float)$priceInput : null;
            }
            if ($value === '') {
                $_SESSION['flash_message'] = 'Giá trị biến thể không được rỗng.';
                header('Location: ' . APP_URL . '/ProductVariant/manage/' . $masp);
                exit();
            }
            $variantModel = $this->model('ProductVariantModel');
            $variantModel->add($masp, $type, $value, $price, 1);
            header('Location: ' . APP_URL . '/ProductVariant/manage/' . $masp);
            exit();
        }
        header('Location: ' . APP_URL . '/ProductVariant/manage/' . $masp);
    }

    /**
     * Cập nhật thông tin biến thể theo id
     * @param int|string $id
     */
    public function update($id) {
        $this->requireRole(['admin','staff'], 'product');
        $variantModel = $this->model('ProductVariantModel');
        $variant = $variantModel->findById($id);
        if (!$variant) {
            $_SESSION['flash_message'] = 'Biến thể không tồn tại.';
            header('Location: ' . APP_URL . '/Product');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $value = trim($_POST['variant_value'] ?? '');
            $active = isset($_POST['active']) ? 1 : 0;
            $price = null;
            if ($variant['variant_type'] === 'capacity') {
                $priceInput = $_POST['variant_price'] ?? '';
                $price = $priceInput !== '' ? (float)$priceInput : null;
            }
            $variantModel->updateVariant($id, $value, $price, $active);
            header('Location: ' . APP_URL . '/ProductVariant/manage/' . $variant['masp']);
            exit();
        }
        // Hiển thị form sửa (tái sử dụng trang manage với dữ liệu chỉnh sửa)
        $productModel = $this->model('AdProducModel');
        $product = $productModel->find('tblsanpham', $variant['masp']);
        $variants = $variantModel->getByProduct($variant['masp']);
        $this->view('adminPage', [
            'page' => 'VariantManagerView',
            'product' => $product,
            'variants' => $variants,
            'editVariant' => $variant
        ]);
    }

    /**
     * Xóa biến thể theo id
     * @param int|string $id
     */
    public function delete($id) {
        $this->requireRole(['admin','staff'], 'product');
        $variantModel = $this->model('ProductVariantModel');
        $variant = $variantModel->findById($id);
        if ($variant) {
            $variantModel->deleteVariant($id);
            header('Location: ' . APP_URL . '/ProductVariant/manage/' . $variant['masp']);
            exit();
        }
        header('Location: ' . APP_URL . '/Product');
    }
}