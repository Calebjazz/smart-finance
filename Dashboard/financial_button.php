<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Floating Button -->
<button
    id="advisorBtn"
    onclick="this.classList.toggle('animate-bounce')"
    class="fixed bottom-6 right-6 bg-emerald-600 hover:bg-emerald-400 text-white rounded-full animate-bounce w-16 h-16 shadow-2xl z-50 flex items-center justify-center transition-all duration-300 hover:scale-110"
    >

<svg xmlns="http://w3.org" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <!-- The Box Shape with the Chat Tail -->
  <path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
</svg>





</button>

<!-- Advisor Window -->
<div
id="advisorWindow"
class="hidden fixed bottom-24 right-6 w-96 bg-white rounded-2xl shadow-2xl z-50 overflow-hidden">

    <div class="bg-slate-500 text-white p-4">

        <h2 class="font-bold text-lg">

            SmartFinance Advisor

        </h2>

        <p class="text-sm">

            Welcome <?php echo htmlspecialchars($_SESSION['full_name']); ?>

        </p>

    </div>

    <div
        id="chatMessages"
        class="h-80 overflow-y-auto p-4 bg-gray-100">

        <div class="bg-white rounded-lg p-3 shadow">

            👋 Hello!

            <br><br>

            I'm your Financial Consultation Assistant.

            Ask me about:

            <ul class="ml-5 mt-2">

                <li>💼Budgets</li>

                <li>📊Expenses</li>

                <li>💰Savings<li>

                <li>💵Financial goals</li>

            </ul>

        </div>

    </div>

    <div class="p-3 border-t flex">

        <input
            id="advisorMessage"
            type="text"
            class="flex-1 border rounded-lg px-3 py-2"
            placeholder="Ask your financial question...">

        <button
            id="advisorSend"
            class="ml-2 bg-blue-600 text-white px-4 rounded">

            Send

        </button>

    </div>

</div>

<script>

const btn=document.getElementById("advisorBtn");

const win=document.getElementById("advisorWindow");

btn.addEventListener("click",function(){

    win.classList.toggle("hidden");

});

</script>