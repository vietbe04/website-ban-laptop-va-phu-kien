<?php

class Chat extends Controller {
    private $conversationModel;
    private $messageModel;

    public function __construct() {
        // Load models
        require_once __DIR__ . '/../models/ChatConversationModel.php';
        require_once __DIR__ . '/../models/ChatMessageModel.php';
        
        $this->conversationModel = new ChatConversationModel();
        $this->messageModel = new ChatMessageModel();
    }

    public function getCurrentUser() {
        $this->ensureSession();
        $user = $_SESSION['user'] ?? [];
        error_log('[getCurrentUser] user=' . json_encode($user));
        return $this->json([
            'email' => $user['email'] ?? '',
            'fullname' => $user['fullname'] ?? '',
            'isLoggedIn' => !empty($user)
        ]);
    }

    public function startAdmin() {
        $this->ensureSession();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $topic = isset($_POST['topic']) ? trim($_POST['topic']) : null;
        $clientUserName = isset($_POST['user_name']) ? trim($_POST['user_name']) : null;
        $sessionId = session_id();
        $user = $_SESSION['user'] ?? [];
        $userEmail = $user['email'] ?? null;
        
        error_log('[startAdmin] clientUserName=' . $clientUserName . ', server fullname=' . ($user['fullname'] ?? 'EMPTY'));
        
        // Nếu client gửi 'Khách' (default fallback), lấy từ server session
        // Nếu server có fullname, dùng server; nếu không thì dùng client value
        if ($clientUserName === 'Khách' && !empty($user['fullname'])) {
            $userName = $user['fullname'];
        } else {
            $userName = $clientUserName ?? $user['fullname'] ?? 'Khách';
        }
        
        error_log('[startAdmin] final userName=' . $userName);
        
        $conversation = $this->conversationModel->findActiveBySessionOrEmail($sessionId, $userEmail);
        if (!$conversation) {
            $conversationId = $this->conversationModel->createConversation($sessionId, $userEmail, $userName, $topic);
            $conversation = $this->conversationModel->findById($conversationId);
        }

        $_SESSION['chat_conversation_id'] = $conversation['id'];
        $messages = $this->messageModel->getMessages($conversation['id'], 0);
        $lastId = 0;
        foreach ($messages as $m) {
            $lastId = max($lastId, (int)$m['id']);
        }

        return $this->json([
            'conversation_id' => (int)$conversation['id'],
            'status' => $conversation['status'],
            'messages' => $messages,
            'last_message_id' => $lastId
        ]);
	}

	public function sendMessage() {
		$this->ensureSession();
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return $this->json(['error' => 'Method not allowed'], 405);
		}

		$conversationId = isset($_POST['conversation_id']) ? (int)$_POST['conversation_id'] : 0;
		$message = isset($_POST['message']) ? trim($_POST['message']) : '';

		if ($conversationId <= 0 || $message === '') {
			return $this->json(['error' => 'Thiếu thông tin cuộc trò chuyện hoặc nội dung trống.'], 400);
		}

		$conversation = $this->conversationModel->findById($conversationId);
		if (!$this->conversationBelongsToCurrentUser($conversation)) {
			return $this->json(['error' => 'Cuộc trò chuyện không hợp lệ.'], 403);
		}

		$user = $_SESSION['user'] ?? [];
		$senderName = $user['fullname'] ?? 'Khách';
		$messageId = $this->messageModel->addMessage($conversationId, $senderName, 'user', $message);
		$this->conversationModel->touch($conversationId);

		return $this->json([
			'success' => true,
			'message_id' => $messageId
		]);
	}

	public function fetchMessages() {
		$this->ensureSession();
		$conversationId = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : 0;
		$afterId = isset($_GET['since_id']) ? (int)$_GET['since_id'] : 0;

		if ($conversationId <= 0) {
			return $this->json(['error' => 'Thiếu mã cuộc trò chuyện.'], 400);
		}

		$conversation = $this->conversationModel->findById($conversationId);
		if (!$this->conversationBelongsToCurrentUser($conversation)) {
			return $this->json(['error' => 'Cuộc trò chuyện không hợp lệ.'], 403);
		}

		$messages = $this->messageModel->getMessages($conversationId, $afterId);
		$this->messageModel->markReadExceptSenderType($conversationId, 'user');

		$lastId = $afterId;
		foreach ($messages as $m) {
			$lastId = max($lastId, (int)$m['id']);
		}

		return $this->json([
			'messages' => $messages,
			'last_message_id' => $lastId
		]);
	}

	public function handshake() {
		$this->ensureSession();
		$sessionId = session_id();
		$user = $_SESSION['user'] ?? [];
		$userEmail = $user['email'] ?? null;
		$existingId = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : 0;

		$conversation = null;
		if ($existingId > 0) {
			$candidate = $this->conversationModel->findById($existingId);
			if ($this->conversationBelongsToCurrentUser($candidate)) {
				$conversation = $candidate;
			}
		}

		if (!$conversation) {
			$conversation = $this->conversationModel->findActiveBySessionOrEmail($sessionId, $userEmail);
		}

		if (!$conversation) {
			return $this->json(['conversation_id' => null, 'messages' => []]);
		}

		$_SESSION['chat_conversation_id'] = $conversation['id'];
		$messages = $this->messageModel->getMessages($conversation['id'], 0);
		$lastId = 0;
		foreach ($messages as $m) {
			$lastId = max($lastId, (int)$m['id']);
		}

		return $this->json([
			'conversation_id' => (int)$conversation['id'],
			'messages' => $messages,
			'status' => $conversation['status'],
			'last_message_id' => $lastId
		]);
	}

	private function ensureSession() {
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
	}

	private function conversationBelongsToCurrentUser($conversation) {
		if (!$conversation) {
			return false;
		}
		$sessionId = session_id();
		$user = $_SESSION['user'] ?? [];
		$userEmail = $user['email'] ?? null;

		if (!empty($conversation['session_id']) && $conversation['session_id'] === $sessionId) {
			return true;
		}
		if ($userEmail && $conversation['user_email'] === $userEmail) {
			return true;
		}
		return false;
	}

	private function json($payload, $status = 200) {
		http_response_code($status);
		header('Content-Type: application/json');
		echo json_encode($payload);
		exit;
	}
}
