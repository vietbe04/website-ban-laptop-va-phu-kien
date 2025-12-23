// Trạng thái chat hiện tại
let chatMode = 'bot'; // 'bot' | 'admin'
let chatConversationId = null;
let chatLastMessageId = 0;
let adminPollTimer = null;
let isConnectingAdmin = false;

// Khởi tạo chatbox khi DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Reset về bot mode khi load trang
    chatMode = 'bot';
    chatConversationId = null;
    chatLastMessageId = 0;
    if (adminPollTimer) {
        clearInterval(adminPollTimer);
        adminPollTimer = null;
    }
    try { 
        localStorage.removeItem('chat_conversation_id'); 
    } catch (e) {}
    
    initChatbox();
    initChatEvents();
});

// Khởi tạo HTML chatbox
function initChatbox() {
    const chatHTML = `
        <div id="chat-widget" class="chat-widget">
            <button id="chat-toggle-btn" class="chat-toggle-btn" title="Hỗ trợ trực tuyến">
                <span class="chat-icon">💬</span>
                <span class="chat-badge" id="chat-badge" style="display: none;">0</span>
            </button>
            
            <div id="chat-window" class="chat-window" style="display: none;">
                <div class="chat-header">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span class="chat-icon">💬</span>
                        <div>
                            <div class="chat-title">Hỗ trợ khách hàng</div>
                            <div class="chat-subtitle">Trực tuyến 8:00-17:00</div>
                        </div>
                    </div>
                    <button id="chat-close-btn" class="chat-close-btn">✕</button>
                </div>
                
                <div class="chat-body" id="chat-body">
                    <div class="chat-message bot-message">
                        <div class="message-avatar">
                            <i class="bi bi-robot"></i>
                        </div>
                        <div class="message-content">
                            <p>Xin chào! 👋 Chúng tôi có thể giúp gì cho bạn?</p>
                            <div class="quick-replies mt-2">
                                <button class="quick-reply-btn" data-message="Tư vấn sản phẩm">🛍️ Tư vấn sản phẩm</button>
                                <button class="quick-reply-btn" data-message="Kiểm tra đơn hàng">📦 Kiểm tra đơn hàng</button>
                                <button class="quick-reply-btn" data-message="Chính sách bảo hành">🔧 Bảo hành</button>
                                <button class="quick-reply-btn" data-message="Chat với admin">👨‍💼 Chat với admin</button>
                            </div>
                            <small class="message-time">22:00</small>
                        </div>
                    </div>
                </div>
                
                <div class="chat-footer">
                    <form id="chat-form" class="d-flex gap-2">
                        <input 
                            type="text" 
                            id="chat-input" 
                            class="form-control" 
                            placeholder="Nhập tin nhắn..." 
                            autocomplete="off"
                        />
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 50%; width: 42px; height: 42px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <style>
            #chat-widget {
                position: fixed;
                bottom: 20px;
                right: 20px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                z-index: 9999;
            }
            
            .chat-toggle-btn {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                color: white;
                font-size: 28px;
                cursor: pointer;
                box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
                transition: all 0.3s ease;
                position: relative;
            }
            
            .chat-toggle-btn:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 30px rgba(102, 126, 234, 0.6);
            }
            
            .chat-toggle-btn:active {
                transform: scale(0.95);
            }
            
            .chat-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                background: #dc3545;
                color: white;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: bold;
                animation: pulse 2s infinite;
            }
            
            @keyframes pulse {
                0%, 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
                50% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
            }
            
            .chat-window {
                position: fixed;
                bottom: 100px;
                right: 20px;
                width: 380px;
                height: 500px;
                background: white;
                border-radius: 16px;
                box-shadow: 0 8px 40px rgba(0, 0, 0, 0.2);
                display: flex;
                flex-direction: column;
                overflow: hidden;
                animation: slideUp 0.3s ease;
            }
            
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .chat-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .chat-title {
                font-weight: 600;
                font-size: 15px;
            }
            
            .chat-subtitle {
                font-size: 12px;
                opacity: 0.9;
            }
            
            .chat-close-btn {
                background: transparent;
                border: none;
                color: white;
                font-size: 20px;
                cursor: pointer;
                padding: 5px;
                transition: all 0.3s ease;
            }
            
            .chat-close-btn:hover {
                transform: rotate(90deg);
            }
            
            .chat-body {
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 16px;
                background: #f8f9fa;
                display: flex;
                flex-direction: column;
                min-height: 0;
                scroll-behavior: smooth;
                justify-content: flex-start;
                gap: 0;
                height: calc(100% - 110px);
            }
            
            /* Scrollbar styling */
            .chat-body::-webkit-scrollbar {
                width: 8px;
            }
            
            .chat-body::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }
            
            .chat-body::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 10px;
                transition: all 0.3s ease;
            }
            
            .chat-body::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, #5568d3 0%, #653a8f 100%);
            }
            
            .chat-message {
                display: flex;
                gap: 10px;
                margin-bottom: 16px;
                animation: fadeIn 0.3s ease;
                flex-shrink: 0;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .bot-message .message-avatar {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .admin-reply .message-avatar {
                background: #0d6efd;
            }

            .admin-label {
                font-size: 11px;
                color: #6c757d;
                margin-bottom: 2px;
                display: block;
            }
            
            .user-message {
                flex-direction: row-reverse;
            }
            
            .user-message .message-avatar {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: #6c757d;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            
            .message-content {
                max-width: 70%;
            }
            
            .bot-message .message-content p {
                background: white;
                padding: 10px 14px;
                border-radius: 12px 12px 12px 4px;
                margin: 0;
                word-wrap: break-word;
                line-height: 1.4;
            }
            
            .user-message .message-content p {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 10px 14px;
                border-radius: 12px 12px 4px 12px;
                margin: 0;
                word-wrap: break-word;
                line-height: 1.4;
            }
            
            .message-time {
                font-size: 11px;
                color: #6c757d;
                margin-top: 4px;
                display: block;
            }
            
            .user-message .message-time {
                text-align: right;
            }
            
            .quick-replies {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }
            
            .quick-reply-btn {
                background: #e7f1ff;
                border: 1px solid #667eea;
                color: #667eea;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 13px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .quick-reply-btn:hover {
                background: #667eea;
                color: white;
            }
            
            .chat-footer {
                padding: 16px;
                background: white;
                border-top: 1px solid #dee2e6;
                flex-shrink: 0;
            }
            
            .chat-footer input {
                border-radius: 25px;
                border: 1px solid #dee2e6;
                padding: 10px 16px;
            }
            
            .chat-footer input:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
                outline: none;
            }
            
            .typing-indicator {
                display: flex;
                gap: 6px;
                padding: 12px 16px;
                background: white;
                border-radius: 12px;
            }
            
            .typing-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #667eea;
                animation: typing 1.4s infinite;
            }
            
            .typing-dot:nth-child(2) {
                animation-delay: 0.2s;
            }
            
            .typing-dot:nth-child(3) {
                animation-delay: 0.4s;
            }
            
            @keyframes typing {
                0%, 60%, 100% {
                    opacity: 0.5;
                    transform: translateY(0);
                }
                30% {
                    opacity: 1;
                    transform: translateY(-10px);
                }
            }
            
            @media (max-width: 768px) {
                .chat-window {
                    width: calc(100vw - 40px);
                    height: 70vh;
                    right: 20px;
                    left: 20px;
                }
                
                .message-content {
                    max-width: 85% !important;
                }
            }
            
            .mt-2 { margin-top: 8px; }
            .d-flex { display: flex; }
            .gap-2 { gap: 8px; }
            .form-control {
                width: 100%;
                padding: 8px 12px;
                border: 1px solid #dee2e6;
                border-radius: 20px;
                font-size: 14px;
            }
            .btn {
                padding: 8px 12px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 14px;
                transition: all 0.3s;
            }
        </style>
    `;
    
    // Inject HTML vào page
    document.body.insertAdjacentHTML('beforeend', chatHTML);
}

// Khởi tạo event listeners
function initChatEvents() {
    const chatToggleBtn = document.getElementById('chat-toggle-btn');
    const chatCloseBtn = document.getElementById('chat-close-btn');
    const chatWindow = document.getElementById('chat-window');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    
    // Toggle chatbox
    chatToggleBtn.addEventListener('click', () => {
        chatWindow.style.display = chatWindow.style.display === 'none' ? 'flex' : 'none';
    });
    
    // Close chatbox
    chatCloseBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        chatWindow.style.display = 'none';
    });
    
    // Submit form
    chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (message) {
            if (shouldStartAdminChat(message) && chatMode !== 'admin') {
                startAdminChat(message);
                chatInput.value = '';
                return;
            }
            sendMessage(message);
            chatInput.value = '';
        }
    });
    
    // Quick replies
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('quick-reply-btn')) {
            const message = e.target.getAttribute('data-message');
            if (shouldStartAdminChat(message) && chatMode !== 'admin') {
                startAdminChat(message);
                document.getElementById('chat-input').value = '';
                return;
            }
            if (chatMode !== 'admin') {
                sendMessage(message);
                document.getElementById('chat-input').value = '';
            }
        }
    });

    resumeAdminConversation();
}

// Gửi tin nhắn
function sendMessage(message) {
    const chatBody = document.getElementById('chat-body');
    const now = new Date();
    const time = now.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});
    
    // Thêm tin nhắn của user
    const userMessage = `
        <div class="chat-message user-message">
            <div class="message-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="message-content">
                <p>${escapeHtml(message)}</p>
                <small class="message-time">${time}</small>
            </div>
        </div>
    `;
    chatBody.insertAdjacentHTML('beforeend', userMessage);
    chatBody.scrollTop = chatBody.scrollHeight;

    if (chatMode === 'admin') {
        sendMessageToAdmin(message);
        return;
    }

    // Hiển thị typing indicator
    showTypingIndicator();

    // Gửi tin nhắn đến server
    setTimeout(() => {
        getBotResponse(message);
    }, 800);
}

// Hiển thị typing indicator
function showTypingIndicator() {
    const chatBody = document.getElementById('chat-body');
    const typingHTML = `
        <div class="chat-message bot-message" id="typing-indicator">
            <div class="message-avatar">
                <i class="bi bi-robot"></i>
            </div>
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>
    `;
    chatBody.insertAdjacentHTML('beforeend', typingHTML);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Lấy phản hồi từ bot
function getBotResponse(userMessage) {
    const baseUrl = getChatBaseUrl();
    
    // Xóa typing indicator
    const typingIndicator = document.getElementById('typing-indicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
    
    // Gửi request đến server
    fetch(`${baseUrl}/Support/chat`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'message=' + encodeURIComponent(userMessage)
    })
    .then(response => response.json())
    .then(data => {
        addBotMessage(data.response, data.quickReplies || []);
    })
    .catch(error => {
        console.error('Chat error:', error);
        addBotMessage('Xin lỗi, hệ thống đang bận. Vui lòng thử lại sau.', []);
    });
}

// Thêm tin nhắn bot
function addBotMessage(message, quickReplies = []) {
    const chatBody = document.getElementById('chat-body');
    const now = new Date();
    const time = now.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});

    if (chatMode === 'admin') {
        quickReplies = [];
    }
    
    let quickRepliesHTML = '';
    if (quickReplies.length > 0) {
        quickRepliesHTML = '<div class="quick-replies mt-2">';
        quickReplies.forEach(reply => {
            quickRepliesHTML += `<button class="quick-reply-btn" data-message="${escapeHtml(reply)}">${escapeHtml(reply)}</button>`;
        });
        quickRepliesHTML += '</div>';
    }
    
    const botMessage = `
        <div class="chat-message bot-message">
            <div class="message-avatar">
                <i class="bi bi-robot"></i>
            </div>
            <div class="message-content">
                <p>${escapeHtml(message)}</p>
                ${quickRepliesHTML}
                <small class="message-time">${time}</small>
            </div>
        </div>
    `;
    chatBody.insertAdjacentHTML('beforeend', botMessage);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Lấy base URL cho chat
function getChatBaseUrl() {
    const scripts = document.querySelectorAll('script');
    for (let script of scripts) {
        if (script.src && script.src.includes('chatbox.js')) {
            const url = new URL(script.src);
            const parts = url.pathname.split('/');
            // Tìm index của 'public' và lấy phần tổng quát
            const publicIndex = parts.indexOf('public');
            if (publicIndex > 0) {
                return url.origin + '/' + parts.slice(1, publicIndex).join('/');
            }
        }
    }
    // Fallback
    return window.location.origin;
}

// Kiểm tra người dùng muốn chat trực tiếp với admin
function shouldStartAdminChat(text) {
    if (!text) return false;
    const t = text.toLowerCase();
    return t.includes('chat với admin') || t.includes('chat admin') || t.includes('nhân viên');
}

// Xóa nhanh các quick reply (tránh lẫn với chat admin)
function clearQuickReplies() {
    document.querySelectorAll('.quick-replies').forEach(el => el.remove());
}

function startAdminChat(initialMessage = null) {
    if (isConnectingAdmin) return;
    isConnectingAdmin = true;
    chatMode = 'admin';
    clearQuickReplies();

    addBotMessage('Đang kết nối nhân viên hỗ trợ...', []);

    const baseUrl = getChatBaseUrl();
    
    // Lấy user info từ server để chắc chắn có tên đúng
    fetch(`${baseUrl}/Chat/getCurrentUser`)
        .then(res => res.json())
        .then(userInfo => {
            let userName = userInfo.fullname || userInfo.email || 'Khách';
            if (!userName || userName.trim() === '') {
                userName = 'Khách';
            }
            
            console.log('[Chat] Got user from server:', userInfo);
            console.log('[Chat] Starting admin chat with user:', userName);
            
            return fetch(`${baseUrl}/Chat/startAdmin`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'topic=' + encodeURIComponent('Hỗ trợ trực tiếp') + '&user_name=' + encodeURIComponent(userName)
            });
        })
        .then(res => res.json())
        .then(data => {
            console.log('[Chat] startAdmin response:', data);
            isConnectingAdmin = false;
            if (!data || !data.conversation_id) {
                chatMode = 'bot';
                addBotMessage('Không thể kết nối nhân viên, vui lòng thử lại sau.', []);
                return;
            }

            chatConversationId = data.conversation_id;
            chatLastMessageId = 0;
            try { localStorage.setItem('chat_conversation_id', chatConversationId); } catch (e) {}

            if (Array.isArray(data.messages)) {
                let maxId = 0;
                data.messages.forEach(msg => {
                    renderConversationMessage(msg, true);
                    const mid = parseInt(msg.id || 0, 10) || 0;
                    if (mid > maxId) maxId = mid;
                });
                chatLastMessageId = maxId;
            }

            if (!chatLastMessageId) {
                chatLastMessageId = parseInt(data.last_message_id || 0, 10) || 0;
            }

            addBotMessage('Bạn đang trò chuyện trực tiếp với nhân viên. Hãy mô tả vấn đề bạn gặp phải.', []);
            startAdminPolling();

            if (initialMessage) {
                sendMessage(initialMessage);
            }
        })
        .catch(err => {
            console.error('[Chat] startAdminChat error:', err);
            isConnectingAdmin = false;
            chatMode = 'bot';
            addBotMessage('Không thể kết nối nhân viên, vui lòng thử lại sau.', []);
        });
}

function sendMessageToAdmin(message) {
    const baseUrl = getChatBaseUrl();
    if (!chatConversationId) {
        startAdminChat(message);
        return;
    }

    fetch(`${baseUrl}/Chat/sendMessage`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'conversation_id=' + encodeURIComponent(chatConversationId) + '&message=' + encodeURIComponent(message)
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.message_id) {
            chatLastMessageId = Math.max(chatLastMessageId, data.message_id);
        }
    })
    .catch(() => {
        addBotMessage('Không gửi được tin nhắn, vui lòng kiểm tra kết nối.', []);
    });
}

function startAdminPolling() {
    if (adminPollTimer) {
        clearInterval(adminPollTimer);
    }
    adminPollTimer = setInterval(fetchAdminMessages, 3500);
    fetchAdminMessages();
}

function fetchAdminMessages() {
    if (!chatConversationId) return;
    const baseUrl = getChatBaseUrl();
    fetch(`${baseUrl}/Chat/fetchMessages?conversation_id=${chatConversationId}&since_id=${chatLastMessageId || 0}`)
        .then(res => res.json())
        .then(data => {
            if (!data || !Array.isArray(data.messages)) return;
            data.messages.forEach(msg => {
                renderConversationMessage(msg);
                chatLastMessageId = Math.max(chatLastMessageId, msg.id);
            });
        })
        .catch(err => console.error('fetchAdminMessages error', err));
}

function resumeAdminConversation() {
    const savedId = (() => {
        try { return localStorage.getItem('chat_conversation_id'); } catch (e) { return null; }
    })();
    if (!savedId) return;

    const baseUrl = getChatBaseUrl();
    fetch(`${baseUrl}/Chat/handshake?conversation_id=${encodeURIComponent(savedId)}`)
        .then(res => res.json())
        .then(data => {
            if (!data || !data.conversation_id) {
                try { localStorage.removeItem('chat_conversation_id'); } catch (e) {}
                return;
            }

            chatMode = 'admin';
            chatConversationId = data.conversation_id;
            chatLastMessageId = 0;
            clearQuickReplies();

            if (Array.isArray(data.messages)) {
                let maxId = 0;
                data.messages.forEach(msg => {
                    renderConversationMessage(msg, true);
                    const mid = parseInt(msg.id || 0, 10) || 0;
                    if (mid > maxId) maxId = mid;
                });
                chatLastMessageId = maxId;
            }
            if (!chatLastMessageId) {
                chatLastMessageId = parseInt(data.last_message_id || 0, 10) || 0;
            }
            addBotMessage('Tiếp tục cuộc trò chuyện với nhân viên hỗ trợ.', []);
            startAdminPolling();
        })
        .catch(err => console.error('resumeAdminConversation error', err));
}

function renderConversationMessage(msg, force = false) {
    if (!msg || !msg.message) return;
    const chatBody = document.getElementById('chat-body');
    if (!chatBody) return;
    const messageId = parseInt(msg.id || 0, 10) || 0;
    if (!force && messageId && chatLastMessageId && messageId <= chatLastMessageId) {
        return;
    }
    const time = formatTimeLabel(msg.created_at);

    if (msg.sender_type === 'user') {
        const userMessage = `
            <div class="chat-message user-message">
                <div class="message-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="message-content">
                    <p>${escapeHtml(msg.message)}</p>
                    <small class="message-time">${time}</small>
                </div>
            </div>
        `;
        chatBody.insertAdjacentHTML('beforeend', userMessage);
    } else {
        const adminMessage = `
            <div class="chat-message bot-message admin-reply">
                <div class="message-avatar">
                    <i class="bi bi-headset"></i>
                </div>
                <div class="message-content">
                    <span class="admin-label">${escapeHtml(msg.sender || 'Admin')}</span>
                    <p>${escapeHtml(msg.message)}</p>
                    <small class="message-time">${time}</small>
                </div>
            </div>
        `;
        chatBody.insertAdjacentHTML('beforeend', adminMessage);
    }

    chatBody.scrollTop = chatBody.scrollHeight;
}

function formatTimeLabel(source) {
    try {
        const d = source ? new Date(source) : new Date();
        return d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    } catch (e) {
        return new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    }
}
