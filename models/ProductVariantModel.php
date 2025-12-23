<?php
require_once "BaseModel.php";
/**
 * Model biến thể sản phẩm: quản lý màu sắc/dung lượng và giá theo biến thể
 */
class ProductVariantModel extends BaseModel {
    private $table = 'product_variants';

    /**
     * Lấy tất cả biến thể theo mã sản phẩm
     */
    public function getByProduct($masp) {
        $sql = "SELECT * FROM {$this->table} WHERE masp = :masp ORDER BY created_at DESC";
        return $this->select($sql, [':masp' => $masp]);
    }

    /**
     * Thêm biến thể mới
     * $type: color | capacity
     * $value: tên màu hoặc dung lượng (VD: 'Đỏ', '256GB')
     * $price: giá riêng cho biến thể dung lượng; null cho màu sắc
     */
    public function add($masp, $type, $value, $price = null, $active = 1) {
        $sql = "INSERT INTO {$this->table} (masp, variant_type, name, price_per_kg, active) VALUES (:masp, :type, :name, :price, :active)";
        $params = [
            ':masp' => $masp,
            ':type' => $type,
            ':name' => $value,
            ':price' => $price,
            ':active' => (int)$active
        ];
        $this->query($sql, $params);
        return $this->getLastInsertId();
    }

    /**
     * Cập nhật thông tin biến thể (tên/giá/kích hoạt)
     */
    public function updateVariant($id, $value, $price = null, $active = 1) {
        $sql = "UPDATE {$this->table} SET name = :name, price_per_kg = :price, active = :active WHERE id = :id";
        $this->query($sql, [
            ':name' => $value,
            ':price' => $price,
            ':active' => (int)$active,
            ':id' => $id
        ]);
    }

    /**
     * Xóa một biến thể theo id
     */
    public function deleteVariant($id) {
        return $this->delete($this->table, $id);
    }

    /**
     * Lấy thông tin biến thể theo id
     */
    public function findById($id) {
        return $this->find($this->table, $id);
    }
}
