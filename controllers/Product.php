<?php
/**
 * Controller quản trị sản phẩm
 * - Danh sách, tạo mới, chỉnh sửa, xóa
 */
class Product extends Controller{
    /**
     * Danh sách sản phẩm (admin) với phân trang
     */
    public function show(){
        $this->requireRole(['admin','staff'], 'product');
        
        // Lấy tham số trang từ URL, mặc định là trang 1
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 15; // Số sản phẩm mỗi trang
        $offset = ($page - 1) * $limit;
        
        $obj = $this->model("AdProducModel");
        // load product types for filter dropdown
        $typeModel = $this->model("AdProductTypeModel");
        $producttype = $typeModel->all("tblloaisp");

        // read optional filters from GET
        $filterCategory = isset($_GET['maLoaiSP']) && $_GET['maLoaiSP'] !== '' ? trim($_GET['maLoaiSP']) : '';
        $q = isset($_GET['q']) && $_GET['q'] !== '' ? trim($_GET['q']) : '';

        if ($q !== '' || $filterCategory !== '') {
            // build filters array for searchWithPagination
            $filters = [];
            if ($q !== '') { $filters['tensp'] = $q; }
            if ($filterCategory !== '') { $filters['maLoaiSP'] = $filterCategory; }

            $totalProducts = $obj->countProducts($filters);
            $totalPages = ceil($totalProducts / $limit);
            $data = $obj->searchWithPagination($filters, $limit, $offset);
        } else {
            // Lấy tổng số sản phẩm để tính tổng số trang
            $totalProducts = $obj->countAllProducts();
            $totalPages = ceil($totalProducts / $limit);
            // Lấy danh sách sản phẩm theo phân trang
            $data = $obj->getProductsWithPagination($limit, $offset);
        }
        
        // Bổ sung phần trăm khuyến mãi hiệu lực (ưu tiên từ bảng khuyenmai nếu đang trong thời gian) cho từng sản phẩm
        try {
            $promoModel = $this->model('AdKhuyenMai');
            foreach ($data as &$p) {
                $row = $promoModel->findWithDiscount($p['masp']);
                // phantram trong row đã là COALESCE(k.phantram, s.khuyenmai,0) nếu còn hiệu lực
                $p['effective_discount'] = isset($row['phantram']) ? (int)$row['phantram'] : 0;
            }
            unset($p);
        } catch (Exception $e) {
            // Nếu lỗi model khuyến mãi, fallback dùng giá trị khuyenmai mặc định
            foreach ($data as &$p) { $p['effective_discount'] = 0; }
            unset($p);
        }
        
        $this->view("adminPage",[
            "page"=>"ProductListView",
            "productList"=>$data,
            "currentPage"=>$page,
            "totalPages"=>$totalPages,
            "totalProducts"=>$totalProducts,
            "producttype" => $producttype,
            "currentFilter" => $filterCategory,
            "currentQuery" => $q
        ]);
    }
    /**
     * Xóa sản phẩm theo mã
     * @param string $id
     */
    public function delete($id){
        $this->requireRole(['admin','staff'], 'product');
        $obj=$this->model("AdProducModel");
        // load product to find associated images/variants
        $product = $obj->find('tblsanpham', $id);
        // delete gallery images and files
        $imgModel = $this->model('ProductImageModel');
        $images = $imgModel->listByProduct($id);
        foreach ($images as $img) {
            @unlink('./public/images/' . ($img['filename'] ?? ''));
            $imgModel->deleteById($img['id']);
        }
        // delete main image file
        if ($product && !empty($product['hinhanh'])) {
            @unlink('./public/images/' . $product['hinhanh']);
        }
        // delete variants
        $variantModel = $this->model('ProductVariantModel');
        $variants = $variantModel->getByProduct($id);
        foreach ($variants as $v) {
            $variantModel->deleteVariant($v['id']);
        }
        // finally delete product row
        $obj->delete("tblsanpham",$id);
        header("Location:".APP_URL."/index.php?url=Product");    
        exit();
    }
    /**
     * Tạo mới sản phẩm
     * - Nhận form POST và lưu ảnh nếu có
     */
    public function create(){
        $this->requireRole(['admin','staff'], 'product');
        $obj = $this->model("AdProducModel");
        $obj2 = $this->model("AdProductTypeModel");
        $producttype = $obj2->all("tblloaisp");
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $masp_goc = $_POST["txt_masp"];
            $masp = preg_replace('/\s+/', '', $masp_goc);
            $tensp = $_POST["txt_tensp"];
            $maloaisp = $_POST["txt_maloaisp"];
            $soluong = $_POST["txt_soluong"];
            $gianhap = $_POST["txt_gianhap"];
            $giaxuat = $_POST["txt_giaxuat"];
            // Các khuyến mãi theo sản phẩm được quản lý riêng trong bảng khuyenmai.
            // Tại đây không đọc input trực tiếp.
            $mota = $_POST["txt_mota"];
            $ngaytao = $_POST["create_date"];
            $hinhanh = "";
            if (!empty($_FILES["uploadfile"]["name"])) {
                $hinhanh = $_FILES["uploadfile"]["name"];
                $file_tmp = $_FILES["uploadfile"]["tmp_name"];
                move_uploaded_file($file_tmp, "./public/images/" . $hinhanh);
            }

            $obj->insert($maloaisp,$masp, $tensp, $hinhanh, $soluong, $gianhap, $giaxuat, $mota, $ngaytao);
            // If additional images uploaded during create, save them to product images table
            $firstSavedExtra = '';
            if (!empty($_FILES['extra_images']) && is_array($_FILES['extra_images']['name'])) {
                $imgModel = $this->model('ProductImageModel');
                $filesCount = count($_FILES['extra_images']['name']);
                for ($i = 0; $i < $filesCount; $i++) {
                    $name = $_FILES['extra_images']['name'][$i];
                    $tmp = $_FILES['extra_images']['tmp_name'][$i] ?? null;
                    if (!$name || !$tmp) continue;
                    // sanitize and avoid collisions
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $base = pathinfo($name, PATHINFO_FILENAME);
                    $safeBase = preg_replace('/[^A-Za-z0-9_-]/', '_', $base);
                    $safe = $safeBase . '_' . time() . '_' . mt_rand(1000,9999) . ($ext ? '.' . $ext : '');
                    if (move_uploaded_file($tmp, './public/images/' . $safe)) {
                        // add to gallery, default is_main = 0
                        $imgModel->add($masp, $safe, 0);
                        if ($firstSavedExtra === '') $firstSavedExtra = $safe;
                    }
                }
            }
            // If no main image provided but extra images were uploaded, set the first extra as main
            if (empty($hinhanh) && $firstSavedExtra !== '') {
                $obj->update($maloaisp,$masp,$tensp,$firstSavedExtra,$soluong,$gianhap,$giaxuat,$mota,$ngaytao);
            }
            header("Location: " . APP_URL . "/index.php?url=Product");
            exit();
        }
        $this->view("adminPage", ["page" => "ProductView", "producttype" => $producttype]);
    }
       /**
        * Chỉnh sửa sản phẩm theo mã
        * @param string $masp
        */
       public function edit($masp){
        $this->requireRole(['admin','staff'], 'product');
        $obj = $this->model("AdProducModel");
        $obj2 = $this->model("AdProductTypeModel");
        $producttype = $obj2->all("tblloaisp");
        $product = $obj->find("tblsanpham", $masp);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $masp = $_POST["txt_masp"];
            $tensp = $_POST["txt_tensp"];
            $maloaisp = $_POST["txt_maloaisp"];
            $soluong = $_POST["txt_soluong"];
            $gianhap = $_POST["txt_gianhap"];
            $giaxuat = $_POST["txt_giaxuat"];
            // Khi chỉnh sửa, không chỉnh sửa khuyến mãi sản phẩm tại đây (quản lý qua Khuyến mãi)
            $mota = $_POST["txt_mota"];
            $ngaytao = $_POST["create_date"];
            $hinhanh = $product['hinhanh'];
            if (!empty($_FILES["uploadfile"]["name"])) {
                $hinhanh = $_FILES["uploadfile"]["name"];
                $file_tmp = $_FILES["uploadfile"]["tmp_name"];
                move_uploaded_file($file_tmp, "./public/images/" . $hinhanh);
            }
            // Pass original product code as last parameter to ensure the UPDATE WHERE matches the original row
            $obj->update($maloaisp,$masp, $tensp,$hinhanh, $soluong, $gianhap, $giaxuat, $mota, $ngaytao, $product['masp']);
            header("Location: " . APP_URL . "/index.php?url=Product");
            exit();
        }
        // Load extra images
        $imgModel = $this->model('ProductImageModel');
        $images = $imgModel->listByProduct($masp);
        $this->view("adminPage", [
            "page" => "ProductView", //ProductView
            "producttype" => $producttype,
            "editItem" => $product,
            "images" => $images
        ]);

    }

    /** Add extra image for product */
    public function imageAdd(){
        $this->requireRole(['admin','staff'], 'product');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/Product/'); exit(); }
        $masp = trim($_POST['masp'] ?? '');
        $isMain = isset($_POST['is_main']) ? 1 : 0;
        if (!$masp) { header('Location: '.APP_URL.'/Product/'); exit(); }
        $imgModel = $this->model('ProductImageModel');
        $firstSaved = '';
        // Support both single file and multiple files (input name image[])
        if (!empty($_FILES['image'])) {
            // If multiple
            if (is_array($_FILES['image']['name'])) {
                $count = count($_FILES['image']['name']);
                for ($i = 0; $i < $count; $i++) {
                    $name = $_FILES['image']['name'][$i];
                    $tmp = $_FILES['image']['tmp_name'][$i] ?? null;
                    if (!$name || !$tmp) continue;
                    $safe = preg_replace('/[^A-Za-z0-9._-]/','_', basename($name));
                    // make filename unique
                    $ext = pathinfo($safe, PATHINFO_EXTENSION);
                    $base = pathinfo($safe, PATHINFO_FILENAME);
                    $safe = $base . '_' . time() . '_' . mt_rand(1000,9999) . ($ext ? '.' . $ext : '');
                    if (move_uploaded_file($tmp, './public/images/'.$safe)) {
                        $imgModel->add($masp, $safe, 0);
                        if ($firstSaved === '') $firstSaved = $safe;
                    }
                }
            } else {
                // single file fallback
                $name = $_FILES['image']['name'];
                $tmp = $_FILES['image']['tmp_name'];
                if ($name && $tmp) {
                    $safe = preg_replace('/[^A-Za-z0-9._-]/','_', basename($name));
                    $ext = pathinfo($safe, PATHINFO_EXTENSION);
                    $base = pathinfo($safe, PATHINFO_FILENAME);
                    $safe = $base . '_' . time() . '_' . mt_rand(1000,9999) . ($ext ? '.' . $ext : '');
                    if (move_uploaded_file($tmp, './public/images/'.$safe)) {
                        $imgModel->add($masp, $safe, $isMain ? 1 : 0);
                        $firstSaved = $safe;
                    }
                }
            }
        }

        // If is_main requested and we saved at least one image, set the first saved as main on product
        if ($isMain && $firstSaved !== '') {
            $obj = $this->model('AdProducModel');
            $p = $obj->find('tblsanpham', $masp);
            if ($p) {
                $obj->update($p['maLoaiSP'],$masp,$p['tensp'],$firstSaved,$p['soluong'],$p['giaNhap'],$p['giaXuat'],$p['mota'],$p['createDate']);
            }
            // Also, mark the corresponding gallery row as main (if model supports it)
            // Attempt to set main by filename if model has method; otherwise already set in product row above
            if (method_exists($imgModel, 'setMainByFilename')) {
                $imgModel->setMainByFilename($masp, $firstSaved);
            }

        }
        header('Location: '.APP_URL.'/index.php?url=Product/edit/'.urlencode($masp)); exit();
    }

    /** Delete extra image */
    public function imageDelete(){
        $this->requireRole(['admin','staff'], 'product');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/Product/'); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        $masp = trim($_POST['masp'] ?? '');
        $imgModel = $this->model('ProductImageModel');
        $row = $imgModel->getById($id);
        if($row){
            @unlink('./public/images/'.$row['filename']);
            $imgModel->deleteById($id);
        }
        header('Location: '.APP_URL.'/index.php?url=Product/edit/'.urlencode($masp)); exit();
    }

    /** Set main image from gallery */
    public function imageSetMain(){
        $this->requireRole(['admin','staff'], 'product');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('Location: '.APP_URL.'/Product/'); exit(); }
        $id = (int)($_POST['id'] ?? 0);
        $masp = trim($_POST['masp'] ?? '');
        $imgModel = $this->model('ProductImageModel');
        $imgModel->setMain($id);
        // also update main image field on tblsanpham
            $row = $imgModel->getById($id);
        if($row){
            $obj = $this->model('AdProducModel');
            // load product and update hinhanh field
            $p = $obj->find('tblsanpham', $masp);
            if($p){
                $obj->update($p['maLoaiSP'],$masp,$p['tensp'],$row['filename'],$p['soluong'],$p['giaNhap'],$p['giaXuat'],$p['mota'],$p['createDate']);
            }
        }
        header('Location: '.APP_URL.'/index.php?url=Product/edit/'.urlencode($masp)); exit();
    }
    
}