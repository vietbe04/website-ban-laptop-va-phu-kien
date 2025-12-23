<?php
require_once __DIR__ . '/BaseModel.php';
class ProductImageModel extends BaseModel {
    protected $table = 'product_images';

    public function listByProduct($masp){
        $sql = "SELECT * FROM {$this->table} WHERE masp = ? ORDER BY is_main DESC, created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$masp]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($masp, $filename, $isMain = 0){
        if ($isMain) { $this->unsetMain($masp); }
        $sql = "INSERT INTO {$this->table} (masp, filename, is_main) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$masp, $filename, (int)$isMain]);
    }

    public function setMain($id){
        $row = $this->getById($id);
        if(!$row) return false;
        $this->unsetMain($row['masp']);
        $sql = "UPDATE {$this->table} SET is_main = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function unsetMain($masp){
        $sql = "UPDATE {$this->table} SET is_main = 0 WHERE masp = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$masp]);
    }

    public function deleteById($id){
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getById($id){
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
?>