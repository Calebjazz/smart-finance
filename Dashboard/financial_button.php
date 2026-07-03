<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) return;
?>

<!-- Floating Financial Advisor Button -->
<button
    id="advisorBtn"
    class="fixed bottom-6 right-6 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full w-16 h-16 shadow-2xl z-50 flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95 group"
    aria-label="Toggle Financial Advisor">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 transition-transform duration-300 group-hover:rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
    </svg>
</button>

<div id="advisorWindow" class="hidden fixed bottom-24 right-6 w-96 max-w-[calc(100vw-2rem)] h-[500px] bg-white dark:bg-slate-800 rounded-2xl shadow-2xl z-50 flex flex-col overflow-hidden border border-gray-200 dark:border-slate-600">
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white p-4 flex items-center justify-between">
        <h2 class="font-bold text-base">Financial Consultation Agent</h2>
        <button id="closeAdvisor" type="button" class="text-emerald-200 hover:text-white">✕</button>
    </div>`
    <div id="chatMessages" class="flex-1 overflow-y-auto p-4 bg-slate-50 dark:bg-gray-300 space-y-3"></div>
    <div class="p-3 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-600 flex gap-2">
        <input id="advisorMessage" type="text" class="form-input flex-1 rounded-xl px-4 py-2 text-sm" placeholder="Ask your financial question...">
        <button id="advisorSend" type="button" class="bg-emerald-600 hover:bg-emerald-500 text-white w-10 h-10 rounded-xl flex items-center justify-center">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
(function() {
    const btn = document.getElementById('advisorBtn');
    const win = document.getElementById('advisorWindow');
    const closeBtn = document.getElementById('closeAdvisor');
    const sendBtn = document.getElementById('advisorSend');
    const inputField = document.getElementById('advisorMessage');
    const chatMessages = document.getElementById('chatMessages');
    if (!btn || !win) return;

    chatMessages.innerHTML = '<div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border text-sm card-text">Hello! I am your financial assistant. Ask about budgets, expenses, savings, or overspending.</div>';

    btn.addEventListener('click', () => win.classList.toggle('hidden'));
    closeBtn.addEventListener('click', () => win.classList.add('hidden'));

    async function sendMessage() {
        const userText = inputField.value.trim();
        if (!userText) return;
        appendMessage(userText, 'user');
        inputField.value = '';
        const loadingId = appendMessage('Thinking...', 'ai-loading');
        try {
            const response = await fetch('http://localhost:5678/webhook/financial-chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    message: userText,
                    userId: <?php echo (int)$_SESSION['user_id']; ?>,
                    phpSessionId: '<?php echo session_id(); ?>'
                })
            });
            const data = await response.json();
            document.getElementById(loadingId)?.remove();
            appendMessage(data.output || data.message || 'No response from agent.', 'ai');
            fetch('../api/ai_log.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ question: userText, response: data.output || data.message || '' })
            }).catch(() => {});
        } catch (err) {
            document.getElementById(loadingId)?.remove();
            appendMessage('Could not connect to n8n agent. Ensure n8n is running on localhost:5678.', 'error');
        }
    }

    function appendMessage(text, sender) {
        const msgDiv = document.createElement('div');
        const id = 'msg-' + Date.now();
        msgDiv.id = id;
        if (sender === 'user') msgDiv.className = 'bg-blue-100 dark:bg-blue-900/40 text-blue-900 dark:text-blue-100 rounded-lg p-3 ml-8 text-right text-sm';
        else if (sender === 'ai-loading') msgDiv.className = 'bg-gray-300 dark:bg-slate-700 text-white rounded-lg p-3 mr-8 italic text-sm animate-pulse';
        else if (sender === 'error') msgDiv.className = 'bg-red-100 dark:bg-red-900/30 text-red-700 rounded-lg p-3 text-sm text-center';
        else msgDiv.className = 'bg-white dark:bg-slate-800 rounded-lg p-3 mr-8 text-sm border-l-4 border-emerald-600 card-text';
        msgDiv.innerText = text;
        chatMessages.appendChild(msgDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return id;
    }

    sendBtn.addEventListener('click', sendMessage);
    inputField.addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });
})();
</script>
