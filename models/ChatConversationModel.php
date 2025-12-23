<?php

class ChatConversationModel extends DB {
	private $table = 'chat_conversations';

	public function findById($id) {
		$sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function findActiveBySessionOrEmail($sessionId, $userEmail) {
		$sql = "SELECT * FROM {$this->table}
				WHERE status IN ('waiting','assigned')
				  AND (session_id = :session_id OR user_email = :user_email)
				ORDER BY updated_at DESC LIMIT 1";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':session_id', $sessionId);
		$stmt->bindParam(':user_email', $userEmail);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function createConversation($sessionId, $userEmail, $userName, $topic = null) {
		$sql = "INSERT INTO {$this->table} (session_id, user_email, user_name, topic, status)
				VALUES (:session_id, :user_email, :user_name, :topic, 'waiting')";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':session_id', $sessionId);
		$stmt->bindParam(':user_email', $userEmail);
		$stmt->bindParam(':user_name', $userName);
		$stmt->bindParam(':topic', $topic);
		$stmt->execute();
		return (int)$this->db->lastInsertId();
	}

	public function assignAdmin($conversationId, $adminEmail) {
		$sql = "UPDATE {$this->table}
				SET admin_email = :admin_email, status = 'assigned', updated_at = NOW()
				WHERE id = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':admin_email', $adminEmail);
		$stmt->bindParam(':id', $conversationId, PDO::PARAM_INT);
		return $stmt->execute();
	}

	public function close($conversationId) {
		$sql = "UPDATE {$this->table}
				SET status = 'closed', updated_at = NOW()
				WHERE id = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':id', $conversationId, PDO::PARAM_INT);
		return $stmt->execute();
	}

	public function touch($conversationId) {
		$sql = "UPDATE {$this->table} SET updated_at = NOW() WHERE id = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':id', $conversationId, PDO::PARAM_INT);
		return $stmt->execute();
	}

	public function listActiveWithMeta($limit = 30) {
		$sql = "SELECT c.id, c.user_name, c.user_email, c.status, c.created_at, c.updated_at, c.admin_email,
					   (SELECT message FROM chat_messages m WHERE m.conversation_id = c.id ORDER BY m.id DESC LIMIT 1) AS last_message,
					   (SELECT sender_type FROM chat_messages m WHERE m.conversation_id = c.id ORDER BY m.id DESC LIMIT 1) AS last_sender_type,
					   (SELECT MAX(id) FROM chat_messages m WHERE m.conversation_id = c.id) AS last_message_id,
					   (SELECT COUNT(*) FROM chat_messages m WHERE m.conversation_id = c.id AND m.sender_type = 'user' AND m.is_read = 0) AS unread_from_user
				FROM {$this->table} c
				WHERE c.status IN ('waiting','assigned')
				ORDER BY c.updated_at DESC
				LIMIT :limit";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
