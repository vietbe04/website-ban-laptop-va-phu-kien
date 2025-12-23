<?php
require_once 'BaseModel.php';
/**
 * Model đánh giá sản phẩm: thêm, duyệt, tìm kiếm và thống kê điểm
 */
class ReviewModel extends BaseModel {
    protected $table = 'product_reviews';

    /**
     * Thêm đánh giá mới ở trạng thái chưa duyệt (approved = 0)
     */
    public function addReview($email, $fullname, $productId, $rating, $comment, $images = null){
        $sql = "INSERT INTO {$this->table} (email, fullname, product_id, rating, comment, images, approved, created_at) VALUES (:email,:fullname,:pid,:rating,:comment,:images,0,NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':email'=>$email, ':fullname'=>$fullname, ':pid'=>$productId,
            ':rating'=>$rating, ':comment'=>$comment, ':images'=>$images
        ]);
    }

    /**
     * Kiểm tra người dùng đã từng đánh giá sản phẩm này chưa
     */
    public function hasUserReviewed($email, $productId){
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql); $stmt->execute([$email,$productId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Lấy danh sách đánh giá đã duyệt theo sản phẩm (mới nhất trước)
     */
    public function getApprovedByProduct($productId){
        $sql = "SELECT fullname, email, rating, comment, images, created_at FROM {$this->table} WHERE product_id = ? AND approved = 1 ORDER BY created_at DESC";
        return $this->select($sql, [$productId]);
    }

    /**
     * Tính điểm trung bình và số lượt đánh giá đã duyệt của sản phẩm
     */
    public function getAverageRating($productId){
        $sql = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS cnt FROM {$this->table} WHERE product_id = ? AND approved = 1";
        $stmt = $this->db->prepare($sql); $stmt->execute([$productId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row) return ['avg'=>0,'count'=>0];
        return ['avg'=>round((float)($row['avg_rating'] ?? 0),1), 'count'=>(int)($row['cnt'] ?? 0)];
    }

    /**
     * Tìm kiếm đánh giá trong admin theo bộ lọc và phân trang (tùy chọn)
     */
    public function adminSearch($filters, $limit = null, $offset = null){
        $sql = "SELECT id, email, fullname, product_id, rating, comment, images, approved, created_at FROM {$this->table} WHERE 1=1";
        $params = [];
        if(!empty($filters['product_id'])){ $sql .= " AND product_id LIKE ?"; $params[] = '%'.$filters['product_id'].'%'; }
        if(!empty($filters['rating'])){ $sql .= " AND rating = ?"; $params[] = (int)$filters['rating']; }
        if($filters['approved'] !== '' && $filters['approved'] !== null){ $sql .= " AND approved = ?"; $params[] = (int)$filters['approved']; }
        $sql .= " ORDER BY created_at DESC";
        
        // Bind WHERE parameters first, then LIMIT/OFFSET
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            
            // Bind WHERE parameters
            foreach ($params as $i => $value) {
                $stmt->bindValue($i + 1, $value);
            }
            // Bind LIMIT and OFFSET with PDO::PARAM_INT
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số bản ghi trong adminSearch để phục vụ phân trang
     */
    public function countAdminSearch($filters){
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE 1=1";
        $params = [];
        if(!empty($filters['product_id'])){ $sql .= " AND product_id LIKE ?"; $params[] = '%'.$filters['product_id'].'%'; }
        if(!empty($filters['rating'])){ $sql .= " AND rating = ?"; $params[] = (int)$filters['rating']; }
        if($filters['approved'] !== '' && $filters['approved'] !== null){ $sql .= " AND approved = ?"; $params[] = (int)$filters['approved']; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Cập nhật trạng thái duyệt của một đánh giá
     */
    public function setApproved($id, $approved){
        $stmt = $this->db->prepare("UPDATE {$this->table} SET approved = :a WHERE id = :id");
        return $stmt->execute([':a'=>(int)$approved, ':id'=>$id]);
    }

    /**
     * Xóa một đánh giá theo id
     */
    public function deleteById($id){
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id'=>$id]);
    }
}