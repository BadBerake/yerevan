<?php view('header', ['title' => $community['name']]); ?>

<div class="chat-container">
    <!-- Chat Sidebar -->
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <img src="<?= $community['image_url'] ?: '/public/img/placeholder.jpg' ?>" class="community-avatar">
            <div class="community-info">
                <h3><?= htmlspecialchars($community['name']) ?></h3>
                <p><?= count($members) ?> Members</p>
            </div>
        </div>
        
        <div class="sidebar-section">
            <h4>Online Members</h4>
            <div class="member-list">
                <?php foreach ($members as $member): ?>
                    <div class="member-item">
                        <span class="status-indicator"></span>
                        <?= htmlspecialchars($member['username']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="sidebar-footer">
            <form action="/communities/leave" method="POST" onsubmit="return confirm('Leave this community?')">
                <input type="hidden" name="community_id" value="<?= $community['id'] ?>">
                <button type="submit" class="leave-btn">Leave Community</button>
            </form>
        </div>
    </div>

    <!-- Chat Main Area -->
    <div class="chat-main">
        <div class="chat-header">
            <div class="title-group">
                <h3>Chat Room</h3>
                <span>Activity: Recent</span>
            </div>
            <a href="/communities" class="back-link">Discovery</a>
        </div>
        
        <div class="message-area" id="messageArea">
            <?php if (empty($messages)): ?>
                <div class="empty-chat">
                    <span>👋</span>
                    <p>No messages yet. Be the first to say hello!</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message-wrapper <?= $msg['user_id'] == $user['id'] ? 'own-message' : '' ?>" data-id="<?= $msg['id'] ?>">
                        <div class="message-meta"><?= htmlspecialchars($msg['username']) ?> • <?= date('H:i', strtotime($msg['created_at'])) ?></div>
                        <div class="message-bubble">
                            <?php if ($msg['image_url']): ?>
                                <img src="<?= $msg['image_url'] ?>" class="message-image" onclick="window.open(this.src)">
                            <?php endif; ?>
                            <?php if ($msg['message_text']): ?>
                                <p><?= nl2br(htmlspecialchars($msg['message_text'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="chat-input-area">
            <form id="chatForm" enctype="multipart/form-data">
                <input type="hidden" name="community_id" value="<?= $community['id'] ?>">
                
                <div class="input-actions">
                    <label class="attach-btn">
                        📷
                        <input type="file" name="image" id="imageInput" accept="image/*, image/webp" style="display: none;" onchange="previewImage(this)">
                    </label>
                </div>
                
                <div class="input-wrapper">
                    <div id="imagePreview" style="display: none;">
                        <span id="previewName"></span>
                        <button type="button" onclick="clearPreview()">✕</button>
                    </div>
                    <textarea name="message_text" id="messageInput" placeholder="Type a message..." rows="1"></textarea>
                </div>
                
                <button type="submit" class="send-btn" id="sendBtn">
                    <span id="btnText">Send</span>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
/* Chat Layout */
.chat-container {
    display: flex;
    height: calc(100vh - 80px);
    background: #f8fafc;
    overflow: hidden;
}

.chat-sidebar {
    width: 300px;
    background: white;
    border-right: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid #f1f5f9;
}

.community-avatar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    object-fit: cover;
}

.community-info h3 { margin: 0; font-size: 1.1rem; }
.community-info p { margin: 0; font-size: 0.8rem; color: #64748b; }

.sidebar-section { padding: 1.5rem; flex: 1; overflow-y: auto; }
.sidebar-section h4 { text-transform: uppercase; font-size: 0.75rem; color: #94a3b8; letter-spacing: 1px; margin-bottom: 1rem; }

.member-list { display: flex; flex-direction: column; gap: 10px; }
.member-item { display: flex; align-items: center; gap: 10px; font-size: 0.95rem; color: #475569; }
.status-indicator { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; }

.sidebar-footer { padding: 1.5rem; border-top: 1px solid #f1f5f9; }
.leave-btn { width: 100%; padding: 10px; background: #fff1f2; color: #e11d48; border: none; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; }

/* Main Area */
.chat-main { flex: 1; display: flex; flex-direction: column; background: white; }

.chat-header {
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
    background: white;
}

.chat-header h3 { margin: 0; font-size: 1.2rem; }
.chat-header .title-group span { font-size: 0.75rem; color: #22c55e; font-weight: 600; }
.back-link { text-decoration: none; color: #64748b; font-size: 0.9rem; font-weight: 500; }

.message-area {
    flex: 1;
    padding: 2rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    background: #fbfcfe;
}

.empty-chat { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; }
.empty-chat span { font-size: 3rem; margin-bottom: 1rem; }

/* Message Styles */
.message-wrapper { max-width: 70%; display: flex; flex-direction: column; gap: 4px; }
.own-message { align-self: flex-end; align-items: flex-end; }

.message-meta { font-size: 0.75rem; color: #94a3b8; padding: 0 4px; }
.message-bubble { padding: 12px 16px; border-radius: 18px; position: relative; font-size: 0.95rem; line-height: 1.5; }

.message-wrapper:not(.own-message) .message-bubble { 
    background: white; 
    border: 1px solid #e2e8f0; 
    color: #1e293b; 
    border-bottom-left-radius: 4px; 
}
.own-message .message-bubble { 
    background: #3b82f6; 
    color: white; 
    border-bottom-right-radius: 4px; 
}

.message-image { max-width: 100%; border-radius: 12px; margin-bottom: 8px; cursor: pointer; }
.message-bubble p { margin: 0; }

/* Input Area */
.chat-input-area { padding: 1.5rem; border-top: 1px solid #f1f5f9; background: white; }
#chatForm { display: flex; align-items: flex-end; gap: 12px; background: #f8fafc; padding: 8px; border-radius: 16px; border: 1px solid #e2e8f0; }

.input-actions { padding: 8px; }
.attach-btn { cursor: pointer; font-size: 1.4rem; color: #64748b; transition: color 0.2s; }
.attach-btn:hover { color: #3b82f6; }

.input-wrapper { flex: 1; display: flex; flex-direction: column; }
#messageInput { border: none; background: transparent; padding: 10px; outline: none; resize: none; font-size: 1rem; font-family: inherit; }

#imagePreview { background: #eff6ff; padding: 4px 10px; border-radius: 8px; font-size: 0.8rem; margin-bottom: 5px; display: flex; align-items: center; gap: 10px; color: #3b82f6; }
#imagePreview button { border: none; background: transparent; color: #3b82f6; cursor: pointer; font-weight: 800; }

.send-btn { background: #3b82f6; color: white; border: none; padding: 10px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: transform 0.2s, background 0.2s; }
.send-btn:hover { background: #2563eb; transform: scale(1.02); }
.send-btn:disabled { background: #94a3b8; transform: none; cursor: default; }

@media (max-width: 900px) {
    .chat-sidebar { display: none; }
}
</style>

<script>
/** CHAT LOGIC **/
const messageArea = document.getElementById('messageArea');
const chatForm = document.getElementById('chatForm');
const messageInput = document.getElementById('messageInput');
const imageInput = document.getElementById('imageInput');
const sendBtn = document.getElementById('sendBtn');
const btnText = document.getElementById('btnText');

let lastMessageId = <?= !empty($messages) ? end($messages)['id'] : 0 ?>;
const communityId = <?= $community['id'] ?>;
const userId = <?= $user['id'] ?>;

// Scroll to bottom
function scrollToBottom() {
    messageArea.scrollTop = messageArea.scrollHeight;
}
scrollToBottom();

// Auto-expand textarea
messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// Image Preview
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const name = document.getElementById('previewName');
    if (input.files && input.files[0]) {
        preview.style.display = 'flex';
        name.innerText = input.files[0].name;
    }
}

function clearPreview() {
    imageInput.value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

// Send Message
chatForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (sendBtn.disabled) return;
    
    const formData = new FormData(chatForm);
    if (!formData.get('message_text').trim() && !formData.get('image').name) return;
    
    sendBtn.disabled = true;
    btnText.innerText = '...';
    
    try {
        const response = await fetch('/messages/send', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            messageInput.value = '';
            messageInput.style.height = 'auto';
            clearPreview();
            fetchMessages(); // Immediately check for new messages
        }
    } catch (err) {
        console.error('Send Error:', err);
    } finally {
        sendBtn.disabled = false;
        btnText.innerText = 'Send';
    }
});

// Fetch Messages
async function fetchMessages() {
    try {
        const res = await fetch(`/messages/fetch?community_id=${communityId}&last_id=${lastMessageId}`);
        const newMessages = await res.json();
        
        if (newMessages.length > 0) {
            // Remove empty chat placeholder if exists
            const empty = messageArea.querySelector('.empty-chat');
            if (empty) empty.remove();
            
            newMessages.forEach(msg => {
                const isOwn = msg.user_id == userId;
                const date = new Date(msg.created_at);
                const time = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
                
                const wrapper = document.createElement('div');
                wrapper.className = `message-wrapper ${isOwn ? 'own-message' : ''}`;
                wrapper.innerHTML = `
                    <div class="message-meta">${msg.username} • ${time}</div>
                    <div class="message-bubble">
                        ${msg.image_url ? `<img src="${msg.image_url}" class="message-image" onclick="window.open(this.src)">` : ''}
                        ${msg.message_text ? `<p>${msg.message_text.replace(/\n/g, '<br>')}</p>` : ''}
                    </div>
                `;
                messageArea.appendChild(wrapper);
                lastMessageId = msg.id;
            });
            scrollToBottom();
        }
    } catch (err) {
        console.error('Fetch Error:', err);
    }
}

// Polling interval (every 3 seconds)
setInterval(fetchMessages, 3000);

// Enter to send (Shift+Enter for newline)
messageInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        chatForm.dispatchEvent(new Event('submit'));
    }
});
</script>
