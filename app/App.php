<?php
class App{
    protected $controller="Home";
    protected $action="Show"; //phương thức
    protected $param;
 public function __construct(){
        $arr=array();
         //xử lý url
        if(isset($_GET["url"])){
            $arr=$this->urlprocess();
        } else {
            // Fallback: if no 'url' param but common search GET params are present,
            // route to Home::search so links like index.php?masp=... work.
            if (!empty($_GET['masp']) || !empty($_GET['tensp']) || isset($_GET['price_min']) || isset($_GET['price_max']) || isset($_GET['__debug'])) {
                $arr = ['Home','search'];
            }
        }
         //kiểm tra tra file trong thư mục controller có tồn tại ko
        if(isset($arr[0])&& file_exists("./controllers/".$arr[0].".php")){
            $this->controller=$arr[0];
            unset($arr[0]);
        }
        require_once "./controllers/".$this->controller.".php";
        //khởi tạo controller
        $this->controller= new $this->controller;
            //kiểm tra phương thức tồn tại trong controller
        if(isset($arr[1]) && method_exists($this->controller,$arr[1])){
            $this->action=$arr[1];
        }
        unset($arr[1]);
        //lấy tham số
        $this->param =$arr?array_values($arr):array();
        // var_dump($this->param);
        call_user_func_array(array($this->controller,$this->action),$this->param);
    }
   public function urlprocess(){
        if(isset($_GET["url"])){
           return explode('/', filter_var(trim($_GET["url"],'/')));
        }
        return array();
    }      
}