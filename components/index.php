<?php

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Finance</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
        .animate-pulse {
            animation: blink 1s infinite;
        }
       .typing-container{
    display:inline-block;
    min-width:12ch;
    min-height:1.1em;
}
    </style>
</head>

<body class="bg-linear-to-br from-slate-950 via-blue-950 to-slate-900">

<!-- navbar -->
<nav class="w-full fixed top-0 left-0 z-50 bg-slate-950/80 backdrop-blur-sm border-b border-slate-800">

    <div class="max-w-7xl mx-auto px-6 py-3">

        <div class="flex items-center justify-between">

            <!-- Logo -->
            <div>
                <h1 class="text-2xl font-bold text-white flex items-center justify-cente">
                    <i class="fas fa-wallet p-1 text-green-500"></i>
                    Smart<span class="text-blue-400">Finance</span>
                </h1>
            </div>

            <!-- Login/Register Buttons -->
            <div class="flex items-center gap-4">
                <a href="auth/login.php"
                    class="text-white hover:text-blue-400 transition font-medium">
                    Login
                </a>
                <a href="auth/register.php"
                    class="bg-blue-500 hover:bg-blue-600 transition px-6 py-3 rounded-full text-white font-medium">
                    Register
                </a>
            </div>

        </div>

    </div>

</nav>

<<!-- hero section -->
<section
    class="relative min-h-screen pt-24 flex items-center bg-cover bg-center bg-no-repeat"
    style="
        background-image: 
        linear-gradient(rgba(2,6,23,0.75), rgba(15,23,42,0.75)),
        url('../assets/images/one.jpg');
    ">

    <!-- Optional Dark Overlay -->
    <div class="absolute inset-0 bg-black/20"></div>

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-10 items-center">

            <!-- LEFT -->

            <div>

                <h1 class="text-white text-2xl md:text-7xl font-bold leading-tight mt-8">
                    Smart Finance, Where Smart <br>
                    <span id="typing-text" class="text-blue-400 typing-container"></span><span class="animate-pulse">|</span>
                </h1>

                <p class="text-gray-300 mt-8 max-w-lg text-lg">
                    Track your income, monitor expenses, automated budgeting,
                 , and get AI-powered financial consultations
                    - all in one powerful platform.
                </p>

                <div class="flex items-center gap-5 mt-10">
                    <a href="auth/register.php"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-4 rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        Start Free Trial
                    </a>
                </div>

                <!-- Stats -->
                <div class="flex items-center gap-12 mt-16">

                    <div>
                        <div class="text-yellow-400 text-xl">★★★★★</div>
                        <h2 class="text-white text-5xl font-bold mt-2">4.9</h2>
                        <p class="text-gray-400">User Rating</p>
                    </div>

                    <div>
                        <p class="text-white font-semibold">Active Users</p>
                        <h3 class="text-blue-400 text-3xl font-bold mt-2">500+</h3>
                        <p class="text-gray-400">Currently</p>
                    </div>

                    <div>
                        <p class="text-white font-semibold">Money Managed</p>
                        <h3 class="text-green-400 text-3xl font-bold mt-2">TSH</h3>
                        <p class="text-gray-400">Currency</p>
                    </div>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="relative self-start pt-14">

                <!-- Keep your existing dashboard card here -->

            </div>

        </div>

    </div>

</section>
<!-- features section -->
<section class="py-24 bg-slate-900">

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                Our Powerful Financial Management Features
            </h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Everything you need to take control of your finances and build wealth smarter
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Income Tracking -->
            <div class="bg-slate-800/40 backdrop-blur-md rounded-2xl p-8 border border-slate-800 hover:border-emerald-500/50 hover:shadow-[0_0_30px_-5px_rgba(16,185,129,0.2)] transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-full">
                <div>
                    <div class="w-14 h-14 bg-emerald-500/10 text-blue-400 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Income Tracking</h3>
                    <p class="text-gray-400 mb-6">
                        Monitor all your income sources in one place. Track salary, investments, side hustles, and more with automatic categorization.
                    </p>
                </div>
                <ul class="text-gray-300 space-y-2 border-t border-slate-700/50 pt-4">
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">✓</span> Multiple income sources
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">✓</span> Automatic categorization
                    </li>
                </ul>
            </div>

            <!-- Expense Tracking -->
            <div class="bg-slate-800/40 backdrop-blur-md rounded-2xl p-8 border border-slate-800 hover:border-rose-500/50 hover:shadow-[0_0_30px_-5px_rgba(239,68,68,0.2)] transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-full">
                <div>
                    <div class="w-14 h-14 bg-rose-500/10 text-blue-400 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-credit-card text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Expense Tracking</h3>
                    <p class="text-gray-400 mb-6">
                        Track every expense with ease. Categorize spending, set limits, and get alerts when you're approaching budget limits.
                    </p>
                </div>
                <ul class="text-gray-300 space-y-2 border-t border-slate-700/50 pt-4">
                    <li class="flex items-center gap-2">
                        <span class="text-rose-400 font-bold">✓</span> Smart categorization
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-rose-400 font-bold">✓</span> Spending alerts
                    </li>
                </ul>
            </div>

            <!-- Automated Budgeting -->
            <div class="bg-slate-800/40 backdrop-blur-md rounded-2xl p-8 border border-slate-800 hover:border-blue-500/50 hover:shadow-[0_0_30px_-5px_rgba(59,130,246,0.2)] transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-full">
                <div>
                    <div class="w-14 h-14 bg-blue-500/10 text-blue-400 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-pie text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Automated Budgeting</h3>
                    <p class="text-gray-400 mb-6">
                        Let AI create smart budgets based on your income and spending patterns. Adjust automatically as your life changes.
                    </p>
                </div>
                <ul class="text-gray-300 space-y-2 border-t border-slate-700/50 pt-4">
                    <li class="flex items-center gap-2">
                        <span class="text-blue-400 font-bold">✓</span> AI-powered suggestions
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-blue-400 font-bold">✓</span> Goal-based budgets
                    </li>
                </ul>
            </div>

            <!-- AI Financial Consultation -->
            <div class="bg-slate-800/40 backdrop-blur-md rounded-2xl p-8 border border-slate-800 hover:border-cyan-500/50 hover:shadow-[0_0_30px_-5px_rgba(6,182,212,0.2)] transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-full">
                <div>
                    <div class="w-14 h-14 bg-cyan-500/10 text-blue-400 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-robot text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">AI Consultation</h3>
                    <p class="text-gray-400 mb-6">
                        Get personalized financial advice powered by AI. Integrated with n8n for automated workflows and smart recommendations.
                    </p>
                </div>
                <ul class="text-gray-300 space-y-2 border-t border-slate-700/50 pt-4">
                    <li class="flex items-center gap-2">
                        <span class="text-cyan-400 font-bold">✓</span> 24/7 AI assistant
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text--400 fobluent-bold">✓</span> Smart workflows
                    </li>
                </ul>
            </div>

            <!-- Reports & Analytics -->
            <div class="bg-slate-800/40 backdrop-blur-md rounded-2xl p-8 border border-slate-800 hover:border-amber-500/50 hover:shadow-[0_0_30px_-5px_rgba(245,158,11,0.2)] transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-full">
                <div>
                    <div class="w-14 h-14 bg-amber-500/10 text-blue-400 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Reports & Analytics</h3>
                    <p class="text-gray-400 mb-6">
                        Generate detailed financial reports and analytics. Visualize your financial health with charts and insights.
                    </p>
                </div>
                <ul class="text-gray-300 space-y-2 border-t border-slate-700/50 pt-4">
                    <li class="flex items-center gap-2">
                        <span class="text-amber-400 font-bold">✓</span> Custom reports
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-amber-400 font-bold">✓</span> Export options
                    </li>
                </ul>
            </div>

            <!-- Savings & Goals Tracking -->
            <div class="bg-slate-800/40 backdrop-blur-md rounded-2xl p-8 border border-slate-800 hover:border-violet-500/50 hover:shadow-[0_0_30px_-5px_rgba(139,92,246,0.2)] transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-full">
                <div>
                    <div class="w-14 h-14 bg-violet-500/10 text-blue-400 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-piggy-bank text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Savings</h3>
                    <p class="text-gray-400 mb-6">
                    Saving goals. Track your deposits and monitor your milestones dynamically.
                    </p>
                </div>
                <ul class="text-gray-300 space-y-2 border-t border-slate-700/50 pt-4">
                    <li class="flex items-center gap-2">
                        <span class="text-violet-400 font-bold">✓</span> Goal milestones
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-violet-400 font-bold">✓</span> Visual progress tracking
                    </li>
                </ul>
            </div>

        </div>

    </div>

</section>

<!-- How It Works -->
<section class="py-24 bg-linear-to-b from-slate-900 to-slate-950">

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                How Smart Finance Works
            </h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Get started in minutes and transform your financial life
            </p>
        </div>

        <div class="grid md:grid-cols-4 gap-8">

            <div class="text-center">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">1</div>
                <h3 class="text-xl font-bold text-white mb-3">Create Account</h3>
                <p class="text-gray-400">Sign up in seconds with your email or phone number</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">2</div>
                <h3 class="text-xl font-bold text-white mb-3">Add Your Income</h3>
                <p class="text-gray-400">Add your income sources and let AI budget it for tou</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">3</div>
                <h3 class="text-xl font-bold text-white mb-3">Define Budgets</h3>
                <p class="text-gray-400">Let AI create it for you</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">4</div>
                <h3 class="text-xl font-bold text-white mb-3">Track & Grow</h3>
                <p class="text-gray-400">Monitor progress and get AI-powered financial advice 24/7</p>
            </div>

        </div>

    </div>

</section>

<!-- User Testimonies -->
<section class="py-24 bg-slate-950">

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                Trusted by Thousands
            </h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                See what our users say about Smart Finance
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">

            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-700">
                <div class="flex items-center gap-1 mb-4">
                    <span class="text-yellow-400">★★★★★</span>
                </div>
                <p class="text-gray-300 mb-6">
                    "Smart Finance completely changed how I manage my money. The AI recommendations are incredibly accurate and have helped me save 30% more each month."
                </p>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold">
                        <img src="../assets/images/Viola_Curry.jpg" alt="Viola_Curry" class="w-full h-full rounded-full object-cover">
                    </div>
                    <div>
                        <p class="text-white font-medium">Viola Curry</p>
                        <p class="text-gray-400 text-sm">Entrepreneur</p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-700">
                <div class="flex items-center gap-1 mb-4">
                    <span class="text-yellow-400">★★★★★</span>
                </div>
                <p class="text-gray-300 mb-6">
                    "The automated budgeting feature is a game-changer. I no longer have to manually Budget my money. The AI does it all for me!"
                </p>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold">
                        <img src="../assets/images/Johnny_Tenfingers.jpg" alt="Johnny_Tenfingers" class="w-full h-full rounded-full object-cover">
                    </div>
                    <div>
                        <p class="text-white font-medium">Johnny Tenfingers</p>
                        <p class="text-gray-400 text-sm">Marketing Manager</p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-700">
                <div class="flex items-center gap-1 mb-4">
                    <span class="text-yellow-400">★★★★★</span>
                </div>
                <p class="text-gray-300 mb-6">
                    "The AI financial consultation feature is amazing. It's like having a personal financial advisor available 24/7. Highly recommended!"
                </p>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold">
                        <img src="../assets/images/Sasha_Whiteman.jpg" alt="Sasha_Whiteman" class="w-full h-full rounded-full object-cover">
                    </div>
                    <div>
                        <p class="text-white font-medium">Sasha Whiteman</p>
                        <p class="text-gray-400 text-sm">Software Engineer</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>

<!-- CTA Section -->
<section  class="relative min-h-screen pt-24 flex items-center bg-cover bg-center bg-no-repeat"
    style="
        background-image: 
        linear-gradient(rgba(2,6,23,0.75), rgba(15,23,42,0.75)),
        url('../assets/images/three.jpg');
    ">

    <div class="max-w-4xl mx-auto px-6 text-center">

        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
            Ready to Take Control of Your Finances?
        </h2>
        <p class="text-blue-100 text-lg mb-8 max-w-2xl mx-auto">
            Join thousands of users who are already building wealth smarter with Smart Finance. Start your free trial today.
        </p>
        <div class="flex items-center justify-center gap-4">
            <a href="auth/register.php"
                class="bg-white text-blue-600 px-8 py-4 rounded-full font-bold hover:bg-gray-100 transition shadow-lg transform hover:scale-105 transition-all duration-200">
                Get Started
            </a>
            <a href="#"
                class="border-2 border-white text-white px-8 py-4 rounded-full font-bold hover:bg-white/10 transition">
                Learn More
            </a>
        </div>

    </div>

</section>

<!-- Footer -->
<footer class="bg-slate-950 text-white py-12">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-2xl font-bold mb-4"><i class="fas fa-wallet p-1 text-green-500"></i>Smart<span class="text-blue-400">Finance</span></h3>
                <p class="text-gray-400">Your AI-powered financial management solution.</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">Quick Links</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="./auth/login.php" class="hover:text-blue-400 transition">Home</a></li>
                    <li class="hover:text-blue-400">About Us</li>
                    <li class="hover:text-blue-400">Services</li>
                    <li class="hover:text-blue-400">Contact</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Features</h4>
                <ul class="space-y-2 text-gray-400">
                    <li>Automated Budgeting</li>
                    <li>Financial Reports</li>
                    <li>AI Consultant</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Contact</h4>
                <ul class="space-y-2 text-gray-400">
                    <li>hello@smartfinance.com</li>
                    <li>+255629895212</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2026 Smart Finance. All rights reserved.</p>
        </div>
    </div>
</footer>

<script>
    const typingText = document.getElementById('typing-text');
    const text = 'Financial Decisions Create Success.';
    let index = 0;
    
    function type() {
        if (index < text.length) {
            typingText.textContent += text.charAt(index);
            index++;
            setTimeout(type, 80);
        }
    }
    
    setTimeout(type, 500);
</script>

</body>
</html>