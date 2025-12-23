<?php
require_once 'BaseModel.php';

/**
 * Model quản lý Wishlist (danh sách yêu thích)
 */
class WishlistModel extends BaseModel {
    protected $table = 'wishlist';

    /**
     * Thêm sản phẩm vào wishlist
     */
    public function addToWishlist($userEmail, $productId) {
        $sql = "INSERT IGNORE INTO {$this->table} (user_email, product_id, created_at) 
                VALUES (:email, :product_id, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':email' => $userEmail,
            ':product_id' => $productId
        ]);
    }

    /**
     * Xóa sản phẩm khỏi wishlist
     */
    public function removeFromWishlist($userEmail, $productId) {
        $sql = "DELETE FROM {$this->table} WHERE user_email = :email AND product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':email' => $userEmail,
            ':product_id' => $productId
        ]);
    }

    /**
     * Kiểm tra sản phẩm có trong wishlist không
     */
    public function isInWishlist($userEmail, $productId) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_email = :email AND product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email' => $userEmail,
            ':product_id' => $productId
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Lấy danh sách wishlist của user
     */
    public function getUserWishlist($userEmail) {
        $sql = "SELECT w.*, p.tensp, p.giaxuat, p.hinhanh, p.mota 
                FROM {$this->table} w
                LEFT JOIN tblsanpham p ON w.product_id = p.masp
                WHERE w.user_email = :email
                ORDER BY w.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $userEmail]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm số sản phẩm trong wishlist
     */
    public function countWishlist($userEmail) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $userEmail]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Xóa toàn bộ wishlist của user
     */
    public function clearWishlist($userEmail) {
        $sql = "DELETE FROM {$this->table} WHERE user_email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':email' => $userEmail]);
    }
}
