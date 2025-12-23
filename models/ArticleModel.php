<?php
require_once 'BaseModel.php';
/**
 * Model bài viết: CRUD và liệt kê (admin/front)
 */
class ArticleModel extends BaseModel {
    protected $table = 'articles';

    /**
     * Lấy danh sách bài viết
     * @param bool $onlyPublished Chỉ bài đã xuất bản (front)
     * @param int|null $limit Giới hạn
     * @param int|null $offset Bắt đầu
     */
    public function all($onlyPublished=false, $limit = null, $offset = null){
        if ($onlyPublished) {
            // Frontend list needs content for teaser
            $sql = "SELECT id, title, image, status, created_at, content FROM {$this->table} WHERE status = 1 ORDER BY created_at DESC";
        } else {
            // Admin list can omit heavy content field
            $sql = "SELECT id, title, image, status, created_at FROM {$this->table} ORDER BY created_at DESC";
        }
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->select($sql);
    }

    /**
     * Đếm tổng bài viết (có thể lọc chỉ bài xuất bản)
     */
    public function countAll($onlyPublished=false){
        if ($onlyPublished) {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = 1";
        } else {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Tìm bài viết theo id
     */
    public function findById($id){
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy các bài viết liên quan (đơn giản: các bài khác đã xuất bản, trừ chính bài hiện tại)
     * @param int $articleId
     * @param int $limit
     * @return array
     */
    public function related(int $articleId, int $limit = 3): array {
        $sql = "SELECT id, title, image, created_at
                FROM {$this->table}
                WHERE status = 1 AND id <> :id
                ORDER BY created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $articleId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        /**
         * Bài viết trước (cũ hơn) theo created_at
         */
        public function prevOf(int $articleId): ?array {
                $sql = "SELECT a.id, a.title, a.created_at
                                FROM {$this->table} a
                                WHERE a.status = 1
                                    AND a.created_at < (SELECT created_at FROM {$this->table} WHERE id = :id)
                                ORDER BY a.created_at DESC, a.id DESC
                                LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':id', $articleId, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ?: null;
        }

        /**
         * Bài viết sau (mới hơn) theo created_at
         */
        public function nextOf(int $articleId): ?array {
                $sql = "SELECT a.id, a.title, a.created_at
                                FROM {$this->table} a
                                WHERE a.status = 1
                                    AND a.created_at > (SELECT created_at FROM {$this->table} WHERE id = :id)
                                ORDER BY a.created_at ASC, a.id ASC
                                LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':id', $articleId, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ?: null;
        }

    /**
     * Tạo bài viết mới
     */
    public function create($title,$content,$image,$status){
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (title, content, image, status, created_at) VALUES (:t,:c,:i,:s,NOW())");
        return $stmt->execute([':t'=>$title, ':c'=>$content, ':i'=>$image, ':s'=>(int)$status]);
    }

    /**
     * Cập nhật bài viết theo id
     */
    public function updateById($id,$title,$content,$image,$status){
        $stmt = $this->db->prepare("UPDATE {$this->table} SET title=:t, content=:c, image=:i, status=:s WHERE id=:id");
        return $stmt->execute([':t'=>$title, ':c'=>$content, ':i'=>$image, ':s'=>(int)$status, ':id'=>$id]);
    }

    /**
     * Xóa bài viết theo id
     */
    public function deleteById($id){
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }
}