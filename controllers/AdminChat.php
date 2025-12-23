<?php

class AdminChat extends Controller {
    private $conversationModel;
    private $messageModel;

    public function __construct() {
        // Load models
        require_once __DIR__ . '/../models/ChatConversationModel.php';
        require_once __DIR__ . '/../models/ChatMessageModel.php';
        
        $this->conversationModel = new ChatConversationModel();
        $this->messageModel = new ChatMessageModel();
    }

	public function list() {
		$this->ensureAdmin();
		$conversations = $this->conversationModel->listActiveWithMeta();
		return $this->json(['conversations' => $conversations]);
	}

	public function messages() {
		$this->ensureAdmin();
		$conversationId = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : 0;
		$afterId = isset($_GET['since_id']) ? (int)$_GET['since_id'] : 0;
		if ($conversationId <= 0) {
			return $this->json(['error' => 'Thiếu mã cuộc trò chuyện.'], 400);
		}

		$conversation = $this->conversationModel->findById($conversationId);
		if (!$conversation) {
			return $this->json(['error' => 'Cuộc trò chuyện không tồn tại.'], 404);
		}

		$messages = $this->messageModel->getMessages($conversationId, $afterId);
		$this->messageModel->markReadExceptSenderType($conversationId, 'admin');

		$lastId = $afterId;
		foreach ($messages as $m) {
			$lastId = max($lastId, (int)$m['id']);
		}

		return $this->json([
			'messages' => $messages,
			'last_message_id' => $lastId
		]);
	}

	public function send() {
		$this->ensureAdmin();
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return $this->json(['error' => 'Method not allowed'], 405);
		}

		$conversationId = isset($_POST['conversation_id']) ? (int)$_POST['conversation_id'] : 0;
		$message = isset($_POST['message']) ? trim($_POST['message']) : '';

		if ($conversationId <= 0 || $message === '') {
			return $this->json(['error' => 'Thiếu thông tin hoặc nội dung trống.'], 400);
		}

		$conversation = $this->conversationModel->findById($conversationId);
		if (!$conversation) {
			return $this->json(['error' => 'Cuộc trò chuyện không tồn tại.'], 404);
		}

		$admin = $_SESSION['user'] ?? [];
		$adminEmail = $admin['email'] ?? '';
		$adminName = $admin['fullname'] ?? 'Admin';

		// Gán admin nếu chưa có
		if (empty($conversation['admin_email'])) {
			$this->conversationModel->assignAdmin($conversationId, $adminEmail);
		} else {
			$this->conversationModel->touch($conversationId);
		}

		$messageId = $this->messageModel->addMessage($conversationId, $adminName, 'admin', $message);

		return $this->json([
			'success' => true,
			'message_id' => $messageId
		]);
	}

	private function ensureAdmin() {
		$this->requireRole(['admin', 'staff']);
	}

	private function json($payload, $status = 200) {
		http_response_code($status);
		header('Content-Type: application/json');
		echo json_encode($payload);
		exit;
	}
}
