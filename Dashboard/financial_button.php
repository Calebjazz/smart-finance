<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>






</button>
<!-- Floating Button -->
<button
    id="advisorBtn"
    class="fixed bottom-6 right-6 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full w-16 h-16 shadow-2xl z-50 flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95 group"
    aria-label="Toggle Financial Advisor">
    <!-- Clean modern chatting icon -->
    <svg xmlns="http://w3.org" class="h-7 w-7 transition-transform duration-300 group-hover:rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
    </svg>
</button>

<!-- Advisor Window Container -->
<div
    id="advisorWindow"
    class="hidden fixed bottom-24 right-6 w-96 max-w-[calc(100vw-2rem)] h-[500px] bg-white rounded-2xl shadow-2xl z-50 flex flex-col overflow-hidden border border-gray-100 transition-all duration-300 transform scale-95 origin-bottom-right">

    <!-- Header Block -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white p-4 shadow-md flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-ping"></div>
            <div>
                <h2 class="font-bold text-base tracking-wide">SmartFinance Agent</h2>
                <p class="text-xs text-emerald-100 font-light">Online Financial Consultation</p>
            </div>
        </div>
        <button id="closeAdvisor" class="text-emerald-200 hover:text-white transition-colors text-sm font-semibold">✕</button>
    </div>

    <!-- Scrollable Messages Container Box -->
    <div
        id="chatMessages"
        class="flex-1 overflow-y-auto p-4 bg-slate-50 space-y-3 scrollbar-thin scrollbar-thumb-gray-200">
        
        <!-- Welcome System Card Message -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 max-w-[85%] mr-auto">
            <div class="flex items-center space-x-2 text-emerald-700 font-semibold text-sm mb-2">
                <span>👋 Hello, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>!</span>
            </div>
            <p class="text-gray-600 text-sm leading-relaxed">
                I'm your digital personal finance assistant. I'm trained on your tracking logs. Ask me anything about:
            </p>
            <ul class="mt-2 space-y-1.5 text-xs text-gray-500 font-medium pl-1">
                <li class="flex items-center"><span class="mr-2">💼</span> Automatic Budgets (50/30/20)</li>
                <li class="flex items-center"><span class="mr-2">📊</span> Real-time Expense Audits</li>
                <li class="flex items-center"><span class="mr-2">💰</span> Savings & Debt Allocations</li>
                <li class="flex items-center"><span class="mr-2">💵</span> Overspending Warning Systems</li>
            </ul>
        </div>
    </div>

    <!-- Input Bar Wrapper Section -->
    <div class="p-3 bg-white border-t border-slate-100 flex items-center space-x-2 shadow-inner">
        <input
            id="advisorMessage"
            type="text"
            class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all duration-200 placeholder-gray-400"
            placeholder="Ask your financial question...">

        <!-- Envelope Shaped Send Button -->
        <button
            id="advisorSend"
            class="bg-emerald-600 hover:bg-emerald-500 text-white w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-200 active:scale-90 shadow-md shadow-emerald-100 hover:shadow-emerald-200"
            aria-label="Send Message">
            <svg xmlns="http://w3.org" class="h-5 w-5 transform rotate-45 -translate-x-0.5 translate-y-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <!-- Clean Envelope/Paper Airplane Path -->
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
        </button>
    </div>
</div>
<script>
const btn = document.getElementById("advisorBtn");
const win = document.getElementById("advisorWindow");
const sendBtn = document.getElementById("advisorSend");
const inputField = document.getElementById("advisorMessage");
const chatMessages = document.getElementById("chatMessages");

// Toggle window visibility
btn.addEventListener("click", function() {
    win.classList.toggle("hidden");
});

// Send message logic
async function sendMessage() {
    const userText = inputField.value.trim();
    if (!userText) return;

    //Append User Message to UI
    appendMessage(userText, 'user');
    inputField.value = '';

    //Append Loading Placeholder
    const loadingId = appendMessage('Agent is thinking...', 'ai-loading');

    try {
        //POST Request to Docker n8n Webhook
        const response = await fetch('http://localhost:5678/webhook/financial-chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: userText,
    phpSessionId: '<?php echo session_id(); ?>' })
        });
        
        const data = await response.json();
        
        // Remove loading state
        document.getElementById(loadingId).remove();

        // Append AI Agent Response to UI
        // n8n returns the output text inside the first item's output property
        console.log(data);
     appendMessage(JSON.stringify(data), "ai");

    } catch (error) {
        document.getElementById(loadingId).remove();
        appendMessage('Error connecting to financial agent server.', 'error');
        console.error(error);
    }
}

// UI Helper to insert chat blocks dynamically
function appendMessage(text, sender) {
    const msgDiv = document.createElement("div");
    const uniqueId = "msg-" + Date.now();
    msgDiv.id = uniqueId;
    
    if (sender === 'user') {
        msgDiv.className = "bg-blue-100 text-blue-900 rounded-lg p-3 shadow my-2 ml-8 text-right";
    } else if (sender === 'ai-loading') {
        msgDiv.className = "bg-gray-200 text-gray-500 rounded-lg p-3 shadow my-2 mr-8 italic animate-pulse";
    } else if (sender === 'error') {
        msgDiv.className = "bg-red-100 text-red-700 rounded-lg p-3 shadow my-2 text-center text-sm";
    } else {
        msgDiv.className = "bg-white rounded-lg p-3 shadow my-2 mr-8 text-left border-l-4 border-emerald-600";
    }
    
    msgDiv.innerText = text;
    chatMessages.appendChild(msgDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight; // Auto-scroll to bottom
    return uniqueId;
}

// Bind Events
sendBtn.addEventListener("click", sendMessage);
inputField.addEventListener("keypress", function(e) {
    if (e.key === 'Enter') sendMessage();
});
</script>
