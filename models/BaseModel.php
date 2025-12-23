<?php

require_once __DIR__ . '/../app/DB.php';

/**
 * BaseModel: lớp tiện ích cho các model sử dụng PDO
 * - Cung cấp CRUD cơ bản, thực thi query/select, và ánh xạ khóa chính theo bảng
 */
class BaseModel extends DB{
       // Danh sách bảng và cột khóa chính tương ứng
    protected    $primaryKeys = [
            'tblsanpham'       => 'masp',
            'tblloaisp'        => 'maLoaiSP',
            'product_variants' => 'id' // hỗ trợ quản lý biến thể sản phẩm
            // thêm các bảng khác nếu cần
        ];
    /**
     * Lấy toàn bộ bản ghi của bảng
     * @param string $table
     * @return array
     */
    public function all($table) {
        $sql = "SELECT * FROM $table";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();       
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Tìm một bản ghi theo khóa chính được cấu hình
     * @param string $table
     * @param mixed $id
     * @return array|null
     * @throws Exception
     */
    public  function find($table, $id) {
        // Kiểm tra bảng có trong danh sách không
        if (!array_key_exists($table, $this->primaryKeys)) {
            throw new Exception("Bảng không hợp lệ hoặc chưa được định nghĩa.");
        }
        $column = $this->primaryKeys[$table];
        $sql = "SELECT * FROM $table WHERE $column = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Kiểm tra sự tồn tại theo cột bất kỳ
     * @param string $table
     * @param string $column
     * @param mixed $id
     * @return int số lượng khớp
     */
    public function check($table, $column, $id) {
        $sql = "SELECT COUNT(*) FROM $table WHERE $column = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    /**
     * Xóa một bản ghi theo khóa chính đã cấu hình
     * @param string $table
     * @param mixed $id
     * @return bool
     * @throws Exception
     */
    public  function delete($table,$id){
        if (!array_key_exists($table, $this->primaryKeys)) {
            throw new Exception("Bảng không hợp lệ hoặc chưa được định nghĩa.");
        }
        $column = $this->primaryKeys[$table];
        if($this->check($table, $column, $id)>0){
            $sql="DELETE FROM $table WHERE $column=:id"; 
            $stmt=$this->db->prepare($sql);
            $stmt->bindParam(":id",$id);
            return $stmt->execute();   
        }
        else{
            return false;
        }
        
    }   
    /**
     * Thực thi câu lệnh INSERT/UPDATE/DELETE, trả về PDOStatement
     * @param string $sql
     * @param array $params
     */
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Thực thi SELECT và trả về mảng kết quả
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function select($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy ID vừa insert từ PDO
     */
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }
    /**
     * Lấy PDO để truy vấn tùy biến (cần thận trọng)
     */
    public function getDb() {
        return $this->db;
    }
}