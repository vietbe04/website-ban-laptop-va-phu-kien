<?php
require_once 'BaseModel.php';
class ArticleCommentModel extends BaseModel {
    protected $table = 'article_comments';

    public function listByArticle(int $articleId): array {
        $sql = "SELECT c.id, c.article_id, c.user_id, c.fullname, c.content, c.created_at
                FROM {$this->table} c
                WHERE c.article_id = :aid AND c.status = 1
                ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':aid'=>$articleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addComment(int $articleId, int $userId, string $fullname, string $content): bool {
        $sql = "INSERT INTO {$this->table} (article_id, user_id, fullname, content, status, created_at)
                VALUES (:aid, :uid, :fullname, :content, 1, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':aid'=>$articleId,
            ':uid'=>$userId,
            ':fullname'=>$fullname,
            ':content'=>$content
        ]);
    }

    public function countComments(int $articleId): int {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE article_id = :aid AND status = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':aid'=>$articleId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Admin: đếm tổng số bình luận theo bộ lọc
     * filters: ['q' => string, 'status' => '0'|'1'|'' ]
     */
    public function countAdminSearch(array $filters = []): int {
        $where = [];
        $params = [];
        if (($filters['q'] ?? '') !== '') {
            $where[] = "(a.title LIKE :kw OR c.fullname LIKE :kw OR c.content LIKE :kw)";
            $params[':kw'] = '%'.trim($filters['q']).'%';
        }
        if (($filters['status'] ?? '') !== '' && in_array($filters['status'], ['0','1'], true)) {
            $where[] = "c.status = :status";
            $params[':status'] = (int)$filters['status'];
        }
        $whereSql = $where ? ('WHERE '.implode(' AND ', $where)) : '';
        $sql = "SELECT COUNT(*)
                FROM {$this->table} c
                JOIN articles a ON a.id = c.article_id
                $whereSql";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Admin: tìm kiếm bình luận theo bộ lọc + phân trang
     * Trả về: id, article_id, article_title, fullname, content, status, created_at
     */
    public function adminSearch(array $filters = [], int $limit = 15, int $offset = 0): array {
        $where = [];
        $params = [];
        if (($filters['q'] ?? '') !== '') {
            $where[] = "(a.title LIKE :kw OR c.fullname LIKE :kw OR c.content LIKE :kw)";
            $params[':kw'] = '%'.trim($filters['q']).'%';
        }
        if (($filters['status'] ?? '') !== '' && in_array($filters['status'], ['0','1'], true)) {
            $where[] = "c.status = :status";
            $params[':status'] = (int)$filters['status'];
        }
        $whereSql = $where ? ('WHERE '.implode(' AND ', $where)) : '';
        $sql = "SELECT c.id, c.article_id, a.title AS article_title, c.fullname, c.content, c.status, c.created_at
                FROM {$this->table} c
                JOIN articles a ON a.id = c.article_id
                $whereSql
                ORDER BY c.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Toggle/Set trạng thái hiển thị bình luận */
    public function setStatus(int $id, int $status): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status=:s WHERE id=:id");
        return $stmt->execute([':s'=>$status?1:0, ':id'=>$id]);
    }

    /** Xóa bình luận theo id */
    public function deleteById(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }
}
