<?php
require_once "BaseModel.php";
/**
 * Model loại sản phẩm: CRUD và phân trang danh sách
 */
class AdProductTypeModel extends BaseModel{
    private $table="tblloaisp";
    /**
     * Thêm loại sản phẩm mới
     */
    public function insert($maLoaiSP, $tenLoaiSP, $moTaLoaiSP) {
        // Kiểm tra bảng có trong danh sách không
        if (!array_key_exists($this->table, $this->primaryKeys)) {
            throw new Exception("Bảng không hợp lệ hoặc chưa được định nghĩa.");
        }
        // Kiểm tra xem mã loại sản phẩm đã tồn tại chưa
        $column = $this->primaryKeys[$this->table];
        if($this->check($this->table, $column, $maLoaiSP)){
            echo "Mã loại sản phẩm đã tồn tại. Vui lòng chọn mã khác.";
            return;
        }
        else{
            // Chuẩn bị câu lệnh INSERT
            $sql = "INSERT INTO tblloaisp (maLoaiSP, tenLoaiSP, moTaLoaiSP) 
                    VALUES (:maLoaiSP, :tenLoaiSP, :moTaLoaiSP)";
            try {
                $stmt = $this->db->prepare($sql);
                // Gán giá trị cho các tham số
                $stmt->bindParam(':maLoaiSP', $maLoaiSP);
                $stmt->bindParam(':tenLoaiSP', $tenLoaiSP);
                $stmt->bindParam(':moTaLoaiSP', $moTaLoaiSP);
                // Thực thi câu lệnh
                $stmt->execute();
                echo "Thêm loại sản phẩm thành công.";
            } catch (PDOException $e) {
                echo "Thất bại" . $e->getMessage();
            } 
        }    
    }
    
    /**
     * Cập nhật loại sản phẩm theo mã
     */
    public function update($maLoaiSP, $tenLoaiSP, $moTaLoaiSP) {
        // Chuẩn bị câu lệnh UPDATE
        $sql = "UPDATE tblloaisp SET 
                tenLoaiSP = :tenLoaiSP, 
                moTaLoaiSP = :moTaLoaiSP
                WHERE maLoaiSP = :maLoaiSP";
        try {
            $stmt = $this->db->prepare($sql); 
            // Gán giá trị cho các tham số
            $stmt->bindParam(':maLoaiSP', $maLoaiSP);
            $stmt->bindParam(':tenLoaiSP', $tenLoaiSP);
            $stmt->bindParam(':moTaLoaiSP', $moTaLoaiSP);

            // Thực thi câu lệnh
            $stmt->execute();
            echo "Cập nhật loại sản phẩm thành công.";
        } catch (PDOException $e) {
            echo "Cập nhật không thành công: " . $e->getMessage();
        }
    }
    
    /**
     * Lấy danh sách loại có phân trang
     */
    public function getListPaginated($limit, $offset) {
        $sql = "SELECT * FROM {$this->table} ORDER BY maLoaiSP DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm kiếm loại sản phẩm với phân trang (hỗ trợ q: mã hoặc tên)
     */
    public function searchWithPagination(array $filters = [], $limit, $offset) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        if (!empty($filters['q'])) {
            $sql .= " AND (maLoaiSP = :q_exact OR tenLoaiSP LIKE :q_like)";
            $params[':q_exact'] = $filters['q'];
            $params[':q_like'] = '%' . $filters['q'] . '%';
        }
        $sql .= " ORDER BY maLoaiSP DESC LIMIT :limit OFFSET :offset";
        try {
            $stmt = $this->db->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('AdProductTypeModel searchWithPagination error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm loại sản phẩm theo bộ lọc (hỗ trợ q)
     */
    public function countWithFilter(array $filters = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        if (!empty($filters['q'])) {
            $sql .= " AND (maLoaiSP = :q_exact OR tenLoaiSP LIKE :q_like)";
            $params[':q_exact'] = $filters['q'];
            $params[':q_like'] = '%' . $filters['q'] . '%';
        }
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row['total'] ?? 0);
        } catch (PDOException $e) {
            error_log('AdProductTypeModel countWithFilter error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Đếm tổng số loại sản phẩm
     */
    public function countAll() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    
}