<?php
require_once __DIR__ . '/BaseModel.php';
class FeedbackModel extends BaseModel {
	protected $table = 'tblfeedback';

	public function create($data){
		$sql = "INSERT INTO {$this->table} (user_email, fullname, subject, content, status, created_at) VALUES (?, ?, ?, ?, 0, NOW())";
		return $this->execute($sql, [
			$data['user_email'] ?? '',
			$data['fullname'] ?? null,
			$data['subject'] ?? null,
			$data['content'] ?? ''
		]);
	}

	public function listByEmail($email, $limit = 50, $offset = 0){
		$limit = (int)$limit; $offset = (int)$offset;
		$sql = "SELECT * FROM {$this->table} WHERE user_email = ? ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
		return $this->queryAll($sql, [$email]);
	}

	public function countByEmail($email){
		$sql = "SELECT COUNT(*) AS c FROM {$this->table} WHERE user_email = ?";
		$row = $this->queryOne($sql, [$email]);
		return (int)($row['c'] ?? 0);
	}

	public function adminSearch($filters = [], $limit = 15, $offset = 0){
		$conds = [];
		$params = [];
		if (!empty($filters['q'])) { $conds[] = "(subject LIKE ? OR content LIKE ? OR user_email LIKE ?)"; $params[] = '%'.$filters['q'].'%'; $params[] = '%'.$filters['q'].'%'; $params[] = '%'.$filters['q'].'%'; }
		if ($filters['status'] !== '' && $filters['status'] !== null) { $conds[] = "status = ?"; $params[] = (int)$filters['status']; }
		$where = $conds ? ('WHERE '.implode(' AND ', $conds)) : '';
		$limit = (int)$limit; $offset = (int)$offset;
		$sql = "SELECT * FROM {$this->table} $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
		return $this->queryAll($sql, $params);
	}

	public function countAdminSearch($filters = []){
		$conds = [];
		$params = [];
		if (!empty($filters['q'])) { $conds[] = "(subject LIKE ? OR content LIKE ? OR user_email LIKE ?)"; $params[] = '%'.$filters['q'].'%'; $params[] = '%'.$filters['q'].'%'; $params[] = '%'.$filters['q'].'%'; }
		if ($filters['status'] !== '' && $filters['status'] !== null) { $conds[] = "status = ?"; $params[] = (int)$filters['status']; }
		$where = $conds ? ('WHERE '.implode(' AND ', $conds)) : '';
		$sql = "SELECT COUNT(*) AS c FROM {$this->table} $where";
		$row = $this->queryOne($sql, $params);
		return (int)($row['c'] ?? 0);
	}

	public function reply($id, $reply, $status = 1){
		$sql = "UPDATE {$this->table} SET admin_reply = ?, status = ?, answered_at = NOW(), updated_at = NOW() WHERE id = ?";
		return $this->execute($sql, [$reply, (int)$status, (int)$id]);
	}

	public function findById($id){
		$sql = "SELECT * FROM {$this->table} WHERE id = ?";
		return $this->queryOne($sql, [(int)$id]);
	}

	public function remove($id){
		$sql = "DELETE FROM {$this->table} WHERE id = ?";
		return $this->execute($sql, [(int)$id]);
	}

	// Local helpers to align with BaseModel's PDO
	private function getPdo(){ return $this->db; }
	private function execute($sql, $params = []){
		$stmt = $this->getPdo()->prepare($sql);
		return $stmt->execute($params);
	}
	private function queryAll($sql, $params = []){
		$stmt = $this->getPdo()->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	private function queryOne($sql, $params = []){
		$stmt = $this->getPdo()->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
	}
}
?>
