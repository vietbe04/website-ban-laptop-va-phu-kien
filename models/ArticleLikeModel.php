<?php
require_once 'BaseModel.php';
class ArticleLikeModel extends BaseModel {
    protected $table = 'article_likes';

    public function hasLiked(int $articleId, int $userId): bool {
        $sql = "SELECT 1 FROM {$this->table} WHERE article_id = :aid AND user_id = :uid LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':aid'=>$articleId, ':uid'=>$userId]);
        return (bool)$stmt->fetchColumn();
    }

    public function countLikes(int $articleId): int {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE article_id = :aid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':aid'=>$articleId]);
        return (int)$stmt->fetchColumn();
    }

    // Returns ['liked'=>true|false]
    public function toggleLike(int $articleId, int $userId): array {
        if ($this->hasLiked($articleId, $userId)) {
            $sql = "DELETE FROM {$this->table} WHERE article_id = :aid AND user_id = :uid";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':aid'=>$articleId, ':uid'=>$userId]);
            return ['liked'=>false];
        } else {
            $sql = "INSERT INTO {$this->table} (article_id,user_id,created_at) VALUES (:aid,:uid,NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':aid'=>$articleId, ':uid'=>$userId]);
            return ['liked'=>true];
        }
    }
}
