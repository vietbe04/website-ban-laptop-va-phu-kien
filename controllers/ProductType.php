<?php
/**
 * Controller quản trị loại sản phẩm
 */
class ProductType extends Controller{
    /**
     * Danh sách loại sản phẩm có phân trang
     */
    public function show(){
        $this->requireRole(['admin','staff'], 'product-type');
        $obj=$this->model("AdProductTypeModel");
        
        // Phân trang
        $itemsPerPage = 15;
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($currentPage - 1) * $itemsPerPage;
        // read optional search query
        $q = isset($_GET['q']) && $_GET['q'] !== '' ? trim($_GET['q']) : '';

        if ($q !== '') {
            $filters = ['q' => $q];
            $total = $obj->countWithFilter($filters);
            $data = $obj->searchWithPagination($filters, $itemsPerPage, $offset);
        } else {
            $total = $obj->countAll();
            $data = $obj->getListPaginated($itemsPerPage, $offset);
        }
        $totalPages = max(1, ceil($total / $itemsPerPage));
        
        $this->view("adminPage",[
            "page"=>"ProductTypeView",
            "productList"=>$data,
            "currentPage"=>$currentPage,
            "totalPages"=>$totalPages,
            "total"=>$total,
            "offset"=>$offset,
            "currentQuery" => $q
        ]);
    }
    /**
     * Xóa loại sản phẩm theo mã
     * @param string $id
     */
    public function delete($id){
        $this->requireRole(['admin','staff'], 'product-type');
        $obj=$this->model("AdProductTypeModel");
        $obj->delete("tblloaisp",$id);
        header("Location:".APP_URL."/ProductType/");    
        exit();
    }
    /**
     * Tạo loại sản phẩm mới từ form POST
     */
    public function create(){
        $this->requireRole(['admin','staff'], 'product-type');
        $txt_maloaisp =isset($_POST["txt_maloaisp"])?$_POST["txt_maloaisp"]:"";
        $txt_tenloaisp =isset($_POST["txt_tenloaisp"])?$_POST["txt_tenloaisp"]:"";
        $txt_motaloaisp =isset($_POST["txt_motaloaisp"])?$_POST["txt_motaloaisp"]:"";
        $obj=$this->model("AdProductTypeModel");
        $obj->insert($txt_maloaisp, $txt_tenloaisp, $txt_motaloaisp);
        header("Location:".APP_URL."/ProductType/");    
        exit();
    }
    /**
     * Hiển thị form chỉnh sửa loại sản phẩm
     * @param string $maLoaiSP
     */
    public function edit($maLoaiSP)
    {
        $this->requireRole(['admin','staff'], 'product-type');
        $obj=$this->model("AdProductTypeModel");
        $product = $obj->find("tblloaisp",$maLoaiSP);
        $productList = $obj->all("tblloaisp"); // Lấy lại toàn bộ danh sách
        $this->view("adminPage",["page"=>"ProductTypeView",
                            'productList' => $productList,
                            'editItem' => $product]);
    }
    /**
     * Cập nhật loại sản phẩm theo mã
     * @param string $maLoaiSP
     */
    public function update($maLoaiSP)
    {
        $this->requireRole(['admin','staff'], 'product-type');
        $tenLoaiSP = $_POST['txt_tenloaisp'];
        $moTaLoaiSP = $_POST['txt_motaloaisp'];
        $obj=$this->model("AdProductTypeModel");
        $obj->update($maLoaiSP,$tenLoaiSP,$moTaLoaiSP);
        header("Location:".APP_URL."/ProductType/");    
        exit();
        // Quay lại trang danh sách
    }

}