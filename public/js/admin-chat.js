// Floating admin chat inbox
(() => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminChatWidget);
    } else {
        initAdminChatWidget();
    }

    let activeConversationId = null;
    let lastMessageId = 0;
    let listTimer = null;
    let messageTimer = null;

    function initAdminChatWidget() {
        // Avoid duplicate init
        if (document.getElementById('admin-chat-widget')) return;

        const widgetHTML = `
            <div id="admin-chat-widget" class="admin-chat-widget">
                <button id="admin-chat-toggle" class="admin-chat-toggle" title="Hộp thư chat khách hàng">
                    <span class="chat-balloon">💬</span>
                    <span class="chat-dot" id="admin-chat-dot" style="display:none"></span>
                </button>
                <div id="admin-chat-window" class="admin-chat-window" style="display:none;">
                    <div class="admin-chat-header">
                        <div>
                            <div class="title">Chat khách hàng</div>
                            <div class="subtitle">Trả lời nhanh các cuộc chat trực tiếp</div>
                        </div>
                        <button class="btn-close" id="admin-chat-close">✕</button>
                    </div>
                    <div class="admin-chat-body">
                        <div class="conversation-list" id="admin-conversation-list"></div>
                        <div class="conversation-panel">
                            <div class="panel-header" id="admin-conversation-title">Chọn khách để trả lời</div>
                            <div class="messages" id="admin-conversation-messages"></div>
                            <form id="admin-chat-form" class="admin-chat-form">
                                <input type="text" id="admin-chat-input" placeholder="Nhập trả lời..." autocomplete="off" />
                                <button type="submit">Gửi</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                .admin-chat-widget { position: fixed; bottom: 18px; right: 18px; z-index: 9999; font-family: 'Segoe UI', Tahoma, sans-serif; }
                .admin-chat-toggle { width: 58px; height: 58px; border-radius: 50%; border: none; background: linear-gradient(135deg, #0d6efd 0%, #5a67d8 100%); color: white; box-shadow: 0 10px 25px rgba(13,110,253,.35); cursor: pointer; position: relative; }
                .chat-dot { position: absolute; top: 6px; right: 8px; width: 12px; height: 12px; border-radius: 50%; background: #dc3545; box-shadow: 0 0 0 6px rgba(220,53,69,.15); }
                
                .admin-chat-window { 
                    position: fixed;
                    bottom: 90px;
                    right: 18px;
                    width: 800px; 
                    max-width: calc(100vw - 36px); 
                    height: 550px;
                    background: #fff; 
                    border-radius: 14px; 
                    box-shadow: 0 14px 38px rgba(0,0,0,.18); 
                    display: flex; 
                    flex-direction: column;
                    overflow: hidden;
                }
                
                .admin-chat-header { 
                    padding: 14px 16px; 
                    background: linear-gradient(135deg, #0d6efd 0%, #5a67d8 100%); 
                    color: #fff; 
                    display: flex; 
                    align-items: center; 
                    justify-content: space-between;
                    flex-shrink: 0;
                }
                .admin-chat-header .title { font-weight: 700; font-size: 16px; }
                .admin-chat-header .subtitle { font-size: 12px; opacity: .9; }
                .admin-chat-header .btn-close { border: none; background: transparent; color: #fff; font-size: 18px; cursor: pointer; }
                
                .admin-chat-body { 
                    display: grid; 
                    grid-template-columns: 280px 1fr; 
                    flex: 1;
                    min-height: 0;
                    overflow: hidden;
                }
                
                .conversation-list { 
                    border-right: 1px solid #e5e7eb; 
                    overflow-y: auto; 
                    background: #f8fafc;
                    min-height: 0;
                }
                .conversation-item { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; cursor: pointer; transition: background .2s; }
                .conversation-item:hover { background: #eef2ff; }
                .conversation-item.active { background: #e0e7ff; }
                .conversation-item .name { font-weight: 600; color: #111827; font-size: 14px; }
                .conversation-item .meta { font-size: 12px; color: #6b7280; display: flex; justify-content: space-between; margin-top: 4px; }
                .badge { display: inline-flex; align-items: center; justify-content: center; min-width: 22px; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 600; }
                .badge-danger { background: #dc3545; color: #fff; }
                .badge-secondary { background: #e5e7eb; color: #374151; }
                
                .conversation-panel { 
                    display: flex; 
                    flex-direction: column; 
                    min-height: 0;
                    overflow: hidden;
                }
                .panel-header { 
                    padding: 12px 16px; 
                    border-bottom: 1px solid #e5e7eb; 
                    font-weight: 600; 
                    color: #111827;
                    flex-shrink: 0;
                    font-size: 14px;
                }
                .messages { 
                    flex: 1;
                    min-height: 0;
                    padding: 16px; 
                    overflow-y: auto; 
                    overflow-x: hidden; 
                    background: #f9fafb; 
                    display: flex; 
                    flex-direction: column; 
                    gap: 12px;
                }
                .message-row { 
                    max-width: 78%; 
                    padding: 10px 14px; 
                    border-radius: 12px; 
                    line-height: 1.4; 
                    font-size: 14px; 
                    word-wrap: break-word;
                    flex-shrink: 0;
                }
                .message-row.user { 
                    align-self: flex-start; 
                    background: #ffffff; 
                    border: 1px solid #e5e7eb; 
                    color: #111827;
                }
                .message-row.admin { 
                    align-self: flex-end; 
                    background: linear-gradient(135deg, #0d6efd 0%, #5a67d8 100%); 
                    color: #fff; 
                }
                .message-time { 
                    display: block; 
                    margin-top: 4px; 
                    font-size: 12px; 
                    opacity: .7; 
                }
                
                .admin-chat-form { 
                    display: flex; 
                    gap: 10px; 
                    padding: 12px 16px; 
                    border-top: 1px solid #e5e7eb; 
                    background: #fff;
                    flex-shrink: 0;
                }
                .admin-chat-form input { 
                    flex: 1; 
                    padding: 10px 14px; 
                    border: 1px solid #d1d5db; 
                    border-radius: 8px;
                    font-size: 14px;
                    outline: none;
                }
                .admin-chat-form input:focus { border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13,110,253,.1); }
                .admin-chat-form button { 
                    padding: 10px 20px; 
                    background: #0d6efd; 
                    color: #fff; 
                    border: none; 
                    border-radius: 8px; 
                    cursor: pointer;
                    font-weight: 600;
                    white-space: nowrap;
                }
                .admin-chat-form button:hover { background: #0b5ed7; }
                
                @media (max-width: 900px) { 
                    .admin-chat-window { width: calc(100vw - 36px); }
                    .admin-chat-body { grid-template-columns: 1fr; } 
                    .conversation-list { max-height: 150px; border-right: none; border-bottom: 1px solid #e5e7eb; }
                }
            </style>
        `;

        document.body.insertAdjacentHTML('beforeend', widgetHTML);

        const toggleBtn = document.getElementById('admin-chat-toggle');
        const closeBtn = document.getElementById('admin-chat-close');
        const windowEl = document.getElementById('admin-chat-window');
        const listEl = document.getElementById('admin-conversation-list');
        const msgEl = document.getElementById('admin-conversation-messages');
        const titleEl = document.getElementById('admin-conversation-title');
        const formEl = document.getElementById('admin-chat-form');
        const inputEl = document.getElementById('admin-chat-input');

        toggleBtn.addEventListener('click', () => {
            const isHidden = windowEl.style.display === 'none';
            windowEl.style.display = isHidden ? 'flex' : 'none';
            if (isHidden) {
                refreshConversationList();
                startListPolling();
            } else {
                stopPolling();
            }
        });

        closeBtn.addEventListener('click', () => {
            windowEl.style.display = 'none';
            stopPolling();
        });

        formEl.addEventListener('submit', (e) => {
            e.preventDefault();
            const text = inputEl.value.trim();
            if (!text || !activeConversationId) return;
            sendAdminMessage(activeConversationId, text, msgEl);
            inputEl.value = '';
        });

        listEl.addEventListener('click', (ev) => {
            const item = ev.target.closest('.conversation-item');
            if (!item) return;
            const convId = parseInt(item.dataset.id, 10);
            if (!convId) return;
            activeConversationId = convId;
            lastMessageId = 0;
            document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
            item.classList.add('active');
            titleEl.textContent = item.dataset.name || 'Khách hàng';
            msgEl.innerHTML = '';
            loadMessages(convId, msgEl);
            startMessagePolling();
        });
    }

    function sendAdminMessage(conversationId, text, msgEl) {
        const body = `conversation_id=${encodeURIComponent(conversationId)}&message=${encodeURIComponent(text)}`;
        fetch(getBaseUrl() + '/AdminChat/send', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.message_id) {
                appendMessage({ sender_type: 'admin', message: text, created_at: new Date().toISOString() }, msgEl);
                lastMessageId = Math.max(lastMessageId, data.message_id);
            }
        })
        .catch(() => {
            appendMessage({ sender_type: 'admin', message: 'Không gửi được tin nhắn.', created_at: new Date().toISOString() }, msgEl);
        });
    }

    function refreshConversationList() {
        fetch(getBaseUrl() + '/AdminChat/list')
            .then(res => res.json())
            .then(data => renderConversationList(data.conversations || []));
    }

    function renderConversationList(conversations) {
        const listEl = document.getElementById('admin-conversation-list');
        const dot = document.getElementById('admin-chat-dot');
        if (!listEl) return;
        listEl.innerHTML = '';
        let hasUnread = false;
        conversations.forEach(conv => {
            const unread = parseInt(conv.unread_from_user || 0, 10) || 0;
            if (unread > 0) hasUnread = true;
            const item = document.createElement('div');
            item.className = 'conversation-item' + (activeConversationId === conv.id ? ' active' : '');
            item.dataset.id = conv.id;
            item.dataset.name = conv.user_name || conv.user_email || 'Khách hàng';
            item.innerHTML = `
                <div class="name">${escapeHtml(conv.user_name || conv.user_email || 'Khách')}</div>
                <div class="meta">
                    <span>${escapeHtml(conv.last_message || 'Chưa có tin nhắn')}</span>
                    ${unread > 0 ? `<span class="badge badge-danger">${unread}</span>` : '<span class="badge badge-secondary">0</span>'}
                </div>
            `;
            listEl.appendChild(item);
        });
        dot.style.display = hasUnread ? 'block' : 'none';
    }

    function loadMessages(conversationId, msgEl) {
        fetch(getBaseUrl() + `/AdminChat/messages?conversation_id=${conversationId}&since_id=${lastMessageId || 0}`)
            .then(res => res.json())
            .then(data => {
                (data.messages || []).forEach(msg => {
                    appendMessage(msg, msgEl);
                    lastMessageId = Math.max(lastMessageId, parseInt(msg.id || 0, 10) || 0);
                });
            });
    }

    function appendMessage(msg, msgEl) {
        if (!msgEl || !msg) return;
        const wrap = document.createElement('div');
        wrap.className = 'message-row ' + (msg.sender_type === 'admin' ? 'admin' : 'user');
        wrap.innerHTML = `${escapeHtml(msg.message || '')}<span class="message-time">${formatTime(msg.created_at)}</span>`;
        msgEl.appendChild(wrap);
        msgEl.scrollTop = msgEl.scrollHeight;
    }

    function startListPolling() {
        stopPolling();
        listTimer = setInterval(refreshConversationList, 8000);
    }

    function startMessagePolling() {
        if (messageTimer) clearInterval(messageTimer);
        messageTimer = setInterval(() => {
            if (activeConversationId) {
                loadMessages(activeConversationId, document.getElementById('admin-conversation-messages'));
            }
        }, 3500);
    }

    function stopPolling() {
        if (listTimer) clearInterval(listTimer);
        if (messageTimer) clearInterval(messageTimer);
        listTimer = null;
        messageTimer = null;
    }

    function getBaseUrl() {
        const scripts = document.querySelectorAll('script');
        for (let script of scripts) {
            if (script.src && script.src.includes('admin-chat.js')) {
                const url = new URL(script.src);
                const parts = url.pathname.split('/');
                const publicIndex = parts.indexOf('public');
                if (publicIndex > 0) {
                    return url.origin + '/' + parts.slice(1, publicIndex).join('/');
                }
            }
        }
        return window.location.origin;
    }

    function formatTime(val) {
        const d = val ? new Date(val) : new Date();
        return d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
})();
