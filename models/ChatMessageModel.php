<?php

class ChatMessageModel extends DB {
	private $table = 'chat_messages';

	public function addMessage($conversationId, $sender, $senderType, $message) {
		$sql = "INSERT INTO {$this->table} (conversation_id, sender, sender_type, message, is_read)
				VALUES (:conversation_id, :sender, :sender_type, :message, 0)";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':conversation_id', $conversationId, PDO::PARAM_INT);
		$stmt->bindParam(':sender', $sender);
		$stmt->bindParam(':sender_type', $senderType);
		$stmt->bindParam(':message', $message);
		$stmt->execute();
		return (int)$this->db->lastInsertId();
	}

	public function getMessages($conversationId, $afterId = 0) {
		$sql = "SELECT id, conversation_id, sender, sender_type, message, is_read, created_at
				FROM {$this->table}
				WHERE conversation_id = :conversation_id";
		if ($afterId > 0) {
			$sql .= " AND id > :after_id";
		}
		$sql .= " ORDER BY id ASC";

		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':conversation_id', $conversationId, PDO::PARAM_INT);
		if ($afterId > 0) {
			$stmt->bindParam(':after_id', $afterId, PDO::PARAM_INT);
		}
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function markReadExceptSenderType($conversationId, $readerType) {
		$sql = "UPDATE {$this->table}
				SET is_read = 1
				WHERE conversation_id = :conversation_id AND sender_type != :reader_type";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':conversation_id', $conversationId, PDO::PARAM_INT);
		$stmt->bindParam(':reader_type', $readerType);
		return $stmt->execute();
	}
}
