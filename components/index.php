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
                <h1 class="text-2xl font-bold text-white">
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

<!-- hero section -->
<section
    class="min-h-screen pt-24 bg-linear-to-br from-slate-950 via-blue-950 to-slate-900 flex items-center">

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-10 items-center">

            <!-- LEFT -->

            <div>


                <h1
                    class="text-white text-2xl md:text-7xl font-bold leading-tight mt-8">

                      Smart finance, Where Smart <br>
                    <span id="typing-text" class="text-blue-400 typing-container"></span><span class="animate-pulse">|</span>

                </h1>

                <p
                    class="text-gray-300 mt-8 max-w-lg text-lg">

                    Track your income, monitor expenses, automated budgeting, 
                    set savings goals, and get AI-powered financial consultations 
                    - all in one powerful platform.

                </p>

                <div class="flex items-center gap-5 mt-10">

                    <a href="auth/register.php"
                        class="bg-blue-500 hover:bg-blue-600 transition text-white px-8 py-4 rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">

                        Start Free Trial

                    </a>
               </div>

               
                <!-- Stats -->

                <div class="flex items-center gap-12 mt-16">

                    <div>

                        <div class="text-yellow-400 text-xl">
                            ★★★★★
                        </div>

                        <h2 class="text-white text-5xl font-bold mt-2">
                            4.9
                        </h2>

                        <p class="text-gray-400">
                            User Rating
                        </p>

                    </div>

                    <div>

                        <p class="text-white font-semibold">
                            Active Users
                        </p>

                        <h3
                            class="text-blue-400 text-3xl font-bold mt-2">

                            50,000+

                        </h3>

                        <p class="text-gray-400">
                            Worldwide
                        </p>

                    </div>

                    <div>

                        <p class="text-white font-semibold">
                            Money Managed
                        </p>

                        <h3
                            class="text-green-400 text-3xl font-bold mt-2">

                            $2.5B+

                        </h3>

                        <p class="text-gray-400">
                            Tracked
                        </p>

                    </div>

                </div>

            </div>

            <!-- RIGHT - Dashboard Preview -->

            <div class="relative self-start pt-14">

                <div class="bg-linear-to-br from-blue-600/20 to-purple-600/20 backdrop-blur-lg rounded-3xl p-8 border border-white/10 shadow-2xl">
                    
                    <div class="bg-slate-900/80 rounded-2xl p-6 border border-white/10">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-white font-bold text-lg">Dashboard Overview</h3>
                            <span class="text-green-400 text-sm">+12.5%</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-blue-500/20 rounded-xl p-4 border border-blue-500/30">
                                <p class="text-blue-300 text-sm">Total Income</p>
                                <p class="text-white text-2xl font-bold">$8,450</p>
                            </div>
                            <div class="bg-red-500/20 rounded-xl p-4 border border-red-500/30">
                                <p class="text-red-300 text-sm">Total Expenses</p>
                                <p class="text-white text-2xl font-bold">$3,280</p>
                            </div>
                        </div>
                        
                        <div class="bg-green-500/20 rounded-xl p-4 border border-green-500/30 mb-4">
                            <p class="text-green-300 text-sm">Monthly Savings</p>
                            <p class="text-white text-2xl font-bold">$5,170</p>
                        </div>
                        
                        <button class="w-full bg-linear-to-r from-blue-500 to-green-500 text-white py-3 rounded-xl font-medium hover:opacity-90 transition">
                            🤖 Get AI Financial Advice
                        </button>
                    </div>
                    
                </div>

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
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-700 hover:border-blue-500/50 transition-all duration-300 hover:transform hover:scale-105">
                <div class="w-14 h-14 bg-green-500/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">Income Tracking</h3>
                <p class="text-gray-400 mb-4">
                    Monitor all your income sources in one place. Track salary, investments, side hustles, and more with automatic categorization.
                </p>
                <ul class="text-gray-300 space-y-2">
                    <li class="flex items-center gap-2">
                        <span class="text-green-400">✓</span> Multiple income sources
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-green-400">✓</span> Automatic categorization
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-green-400">✓</span> Income trend analysis
                    </li>
                </ul>
            </div>

            <!-- Expense Tracking -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-700 hover:border-red-500/50 transition-all duration-300 hover:transform hover:scale-105">
                <div class="w-14 h-14 bg-red-500/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">Expense Tracking</h3>
                <p class="text-gray-400 mb-4">
                    Track every expense with ease. Categorize spending, set limits, and get alerts when you're approaching budget limits.
                </p>
                <ul class="text-gray-300 space-y-2">
                    <li class="flex items-center gap-2">
                        <span class="text-red-400">✓</span> Smart categorization
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-red-400">✓</span> Receipt scanning
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-red-400">✓</span> Spending alerts
                    </li>
                </ul>
            </div>

            <!-- Automated Budgeting -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-700 hover:border-blue-500/50 transition-all duration-300 hover:transform hover:scale-105">
                <div class="w-14 h-14 bg-blue-500/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">Automated Budgeting</h3>
                <p class="text-gray-400 mb-4">
                    Let AI create smart budgets based on your income and spending patterns. Adjust automatically as your life changes.
                </p>
                <ul class="text-gray-300 space-y-2">
                    <li class="flex items-center gap-2">
                        <span class="text-blue-400">✓</span> AI-powered suggestions
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-blue-400">✓</span> Auto-adjustment
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-blue-400">✓</span> Goal-based budgets
                    </li>
                </ul>
            </div>

            <!-- Savings & Goals -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-700 hover:border-purple-500/50 transition-all duration-300 hover:transform hover:scale-105">
                <div class="w-14 h-14 bg-purple-500/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">Savings & Goals</h3>
                <p class="text-gray-400 mb-4">
                    Set financial goals and track your progress. Create multiple savings goals for vacations, emergencies, or investments.
                </p>
                <ul class="text-gray-300 space-y-2">
                    <li class="flex items-center gap-2">
                        <span class="text-purple-400">✓</span> Multiple goals
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-purple-400">✓</span> Progress tracking
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-purple-400">✓</span> Auto-deposit rules
                    </li>
                </ul>
            </div>

            <!-- AI Financial Consultation -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-700 hover:border-cyan-500/50 transition-all duration-300 hover:transform hover:scale-105">
                <div class="w-14 h-14 bg-cyan-500/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">AI Financial Consultation</h3>
                <p class="text-gray-400 mb-4">
                    Get personalized financial advice powered by AI. Integrated with n8n for automated workflows and smart recommendations.
                </p>
                <ul class="text-gray-300 space-y-2">
                    <li class="flex items-center gap-2">
                        <span class="text-cyan-400">✓</span> 24/7 AI assistant
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-cyan-400">✓</span> n8n integration
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-cyan-400">✓</span> Smart workflows
                    </li>
                </ul>
            </div>

            <!-- Reports & Analytics -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-700 hover:border-yellow-500/50 transition-all duration-300 hover:transform hover:scale-105">
                <div class="w-14 h-14 bg-yellow-500/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-3">Reports & Analytics</h3>
                <p class="text-gray-400 mb-4">
                    Generate detailed financial reports and analytics. Visualize your financial health with charts and insights.
                </p>
                <ul class="text-gray-300 space-y-2">
                    <li class="flex items-center gap-2">
                        <span class="text-yellow-400">✓</span> Custom reports
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-yellow-400">✓</span> Visual analytics
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-yellow-400">✓</span> Export options
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
                <h3 class="text-xl font-bold text-white mb-3">Connect Accounts</h3>
                <p class="text-gray-400">Link your Mobile account and income sources securely</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">3</div>
                <h3 class="text-xl font-bold text-white mb-3">Set Goals</h3>
                <p class="text-gray-400">Define your financial goals and let AI create your budget</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">4</div>
                <h3 class="text-xl font-bold text-white mb-3">Track & Grow</h3>
                <p class="text-gray-400">Monitor progress and get AI-powered financial advice</p>
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
                        <img src="assets/images/Viola_Curry.jpg" alt="Viola_Curry" class="w-full h-full rounded-full object-cover">
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
                        <img src="assets/images/Johnny_Tenfingers.jpg" alt="Johnny_Tenfingers" class="w-full h-full rounded-full object-cover">
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
                        <img src="assets/images/Sasha_Whiteman.jpg" alt="Sasha_Whiteman" class="w-full h-full rounded-full object-cover">
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
<section class="py-24 bg-linear-to-r from-blue-600 to-green-500">

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
                Get Started Free
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
                <h3 class="text-2xl font-bold mb-4">Smart<span class="text-blue-400">Finance</span></h3>
                <p class="text-gray-400">Your AI-powered financial management solution.</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">Quick Links</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-blue-400 transition">Home</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition">About Us</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition">Services</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Features</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-blue-400 transition"> Automated Budgeting</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition">Financial Reports</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition">AI Consultant</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition">Savings Goals</a></li>
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