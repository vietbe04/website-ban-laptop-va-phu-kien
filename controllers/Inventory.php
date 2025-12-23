<?php
/**
 * Controller quản lý tồn kho
 */
class Inventory extends Controller{
    /**
     * Danh sách tồn kho có phân trang
     */
    public function show(){
        // Chỉ admin được xem tồn kho
        $this->requireRole(['admin'], 'inventory');
        
        // Phân trang
        $itemsPerPage = 15;
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($currentPage - 1) * $itemsPerPage;
        
        $obj = $this->model("AdProducModel");
        $typeModel = $this->model("AdProductTypeModel");
        $producttype = $typeModel->all("tblloaisp");

        // read filters
        $filterCategory = isset($_GET['maLoaiSP']) && $_GET['maLoaiSP'] !== '' ? trim($_GET['maLoaiSP']) : '';
        $q = isset($_GET['q']) && $_GET['q'] !== '' ? trim($_GET['q']) : '';

        if ($q !== '' || $filterCategory !== '') {
            $filters = [];
            if ($q !== '') { $filters['tensp'] = $q; }
            if ($filterCategory !== '') { $filters['maLoaiSP'] = $filterCategory; }

            $totalItems = $obj->countProducts($filters);
            $totalPages = ceil($totalItems / $itemsPerPage);
            $productList = $obj->searchWithPagination($filters, $itemsPerPage, $offset);
        } else {
            // Lấy danh sách có phân trang
            $productList = $obj->getProductsWithPagination($itemsPerPage, $offset);
            // Đếm tổng số sản phẩm
            $totalItems = $obj->countAllProducts();
            $totalPages = ceil($totalItems / $itemsPerPage);
        }
        
        $this->view("adminPage", [
            "page" => "inventoryView", 
            "productList" => $productList,
            "currentPage" => $currentPage,
            "totalPages" => $totalPages,
            "total" => $totalItems,
            "offset" => $offset,
            "producttype" => $producttype,
            "currentFilter" => $filterCategory,
            "currentQuery" => $q
        ]);
    }

    /**
     * Cập nhật số lượng tồn kho (POST)
     * @param string $masp
     */
    public function updateStock($masp){
        $this->requireRole(['admin'], 'inventory');
        if ($_SERVER["REQUEST_METHOD"] === "POST"){
            $soluong = isset($_POST['soluong']) ? intval($_POST['soluong']) : 0;
            $obj = $this->model("AdProducModel");
            $obj->setStock($masp, $soluong);
            header("Location:".APP_URL."/Inventory/");
            exit();
        }
        // If not POST, redirect to list
        header("Location:".APP_URL."/Inventory/");
        exit();
    }
}