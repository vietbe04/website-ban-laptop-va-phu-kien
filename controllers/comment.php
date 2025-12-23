<?php
/**
 * Controller ví dụ cho bình luận (chưa triển khai đầy đủ)
 */
class comment extends Controller{
    /**
     * Demo: hiển thị danh sách sản phẩm vào ProductListView
     */
    public function show(){
        $obj=$this->model("commentModel");
        $data=$obj->all("tblsanpham");
        $this->view("HomePage",["page"=>"ProductListView","productList"=>$data]);
    }

    /**
     * Placeholder: tạo bình luận (chưa cài đặt)
     */
    public function create(){
        
    }

}