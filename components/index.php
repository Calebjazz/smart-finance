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
</head>

<body class="bg-gray-500">

<!-- ========================= -->
<!-- NAVBAR -->
<!-- ========================= -->

<nav class="w-full absolute top-0 left-0 z-50">

    <div class="max-w-7xl mx-auto px-6 py-6">

        <div class="flex items-center justify-between">

            <!-- Logo -->
            <div>
                <h1 class="text-4xl font-bold text-white">
                    Smart<span class="text-blue-400">Finance</span>
                </h1>
            </div>

            <!-- Menu -->
            <div
                class="hidden md:flex items-center gap-10 bg-white/10 backdrop-blur-md px-10 py-4 rounded-full text-white">

                <a href="#" class="hover:text-blue-400 transition">Home</a>
                <a href="#" class="hover:text-blue-400 transition">About Us</a>
                <a href="#" class="hover:text-blue-400 transition">Services</a>
                <a href="#" class="hover:text-blue-400 transition">Pages</a>
                <a href="#" class="hover:text-blue-400 transition">Contact</a>

            </div>

            <!-- Button -->
            <a href="auth/register.php"
                class="bg-blue-500 hover:bg-blue-600 transition px-6 py-3 rounded-full text-white font-medium">

                Get Started

            </a>

        </div>

    </div>

</nav>

<!-- ========================= -->
<!-- HERO SECTION -->
<!-- ========================= -->

<section
    class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 flex items-center">

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-10 items-center">

            <!-- LEFT -->

            <div>

                <span
                    class="inline-block bg-blue-500/20 text-blue-300 px-5 py-2 rounded-full text-sm">

                    Welcome To Smart Finance

                </span>

                <h1
                    class="text-white text-6xl md:text-7xl font-bold leading-tight mt-8">

                    Where Smart <br>
                    Financial Decisions <br>
                    Create Success

                </h1>

                <p
                    class="text-gray-300 mt-8 max-w-lg text-lg">

                    Manage your income, expenses,
                    budgets and savings goals with
                    AI-powered financial insights.

                </p>

                <div class="flex items-center gap-5 mt-10">

                    <a href="auth/register.php"
                        class="bg-blue-500 hover:bg-blue-600 transition text-white px-8 py-4 rounded-full">

                        Let's Get Started

                    </a>

                    <button
                        class="w-14 h-14 rounded-full bg-white text-blue-600 text-xl shadow-lg">

                        ▶

                    </button>

                </div>

                <!-- Rating -->

                <div class="flex items-center gap-12 mt-16">

                    <div>

                        <div class="text-yellow-400 text-xl">
                            ★★★★★
                        </div>

                        <h2 class="text-white text-5xl font-bold mt-2">
                            4.8
                        </h2>

                        <p class="text-gray-400">
                            User Satisfaction
                        </p>

                    </div>

                    <div>

                        <p class="text-white font-semibold">
                            Trusted By
                        </p>

                        <h3
                            class="text-blue-400 text-3xl font-bold mt-2">

                            5,000+

                        </h3>

                        <p class="text-gray-400">
                            Finance Users
                        </p>

                    </div>

                </div>

            </div>

            <!-- RIGHT -->

            <div class="relative">

                <!-- Main Image -->

                <img
                    src="assets/images/phone.png"
                    alt=""
                    class="w-full max-w-xl mx-auto">

                <!-- Floating Card -->

                <div
                    class="absolute bottom-10 right-0 bg-white rounded-3xl px-8 py-6 shadow-2xl">

                    <h2
                        class="text-5xl font-bold text-blue-600">

                        25+

                    </h2>

                    <p class="text-gray-600">
                        Smart Features
                    </p>

                </div>

                <!-- Floating Info -->

                <div
                    class="absolute top-20 left-0 bg-white p-5 rounded-2xl shadow-xl">

                    <p class="text-sm text-gray-500">
                        Monthly Savings
                    </p>

                    <h3
                        class="text-2xl font-bold text-blue-600">

                        $15,430

                    </h3>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- ========================= -->
<!-- CARDS SECTION -->
<!-- ========================= -->

<section
    class="-mt-20 relative z-20 pb-24">

    <div class="max-w-7xl mx-auto px-6">

        <div
            class="grid lg:grid-cols-4 gap-6">

            <!-- CARD 1 -->

            <div
                class="bg-white rounded-3xl overflow-hidden shadow-xl">

                <img
                    src="assets/images/card1.jpg"
                    alt=""
                    class="w-full h-56 object-cover">

                <div class="p-6">

                    <h3
                        class="text-2xl font-bold">

                        How Does It Work?

                    </h3>

                    <a
                        href="#"
                        class="text-blue-600 mt-3 inline-block">

                        Learn More →

                    </a>

                </div>

            </div>

            <!-- CARD 2 -->

            <div
                class="bg-gradient-to-br from-blue-600 to-blue-500 rounded-3xl p-8 text-white">

                <div
                    class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-3xl">

                    💼

                </div>

                <h3
                    class="text-3xl font-bold mt-8">

                    Budget Planning

                </h3>

                <p class="mt-4 text-blue-100">

                    Create and manage monthly budgets
                    intelligently.

                </p>

            </div>

            <!-- CARD 3 -->

            <div
                class="bg-gradient-to-br from-blue-700 to-blue-600 rounded-3xl p-8 text-white">

                <div
                    class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-3xl">

                    📈

                </div>

                <h3
                    class="text-3xl font-bold mt-8">

                    Financial Reports

                </h3>

                <p class="mt-4 text-blue-100">

                    Analyze your financial performance
                    using reports.

                </p>

            </div>

            <!-- CARD 4 -->

            <div
                class="bg-gradient-to-br from-blue-800 to-blue-700 rounded-3xl p-8 text-white">

                <div
                    class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-3xl">

                    🤖

                </div>

                <h3
                    class="text-3xl font-bold mt-8">

                    AI Consultant

                </h3>

                <p class="mt-4 text-blue-100">

                    Receive smart financial guidance
                    powered by AI and n8n automation.

                </p>

            </div>

        </div>

    </div>

</section>

</body>
</html>