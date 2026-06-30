<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../components/auth/login.php');
    exit();
}

require_once '../config/database.php';
require_once '../Dashboard/financial_button.php';

// Fetch user financial data
$user_id = $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['full_name'] ?? 'User');

// Get financial summary
$total_balance = 0;
$total_income = 0;
$total_expenses = 0;
$total_savings = 0;

// Sample data - replace with actual database queries
$total_balance = 15420.50;
$total_income = 8450.00;
$total_expenses = 3280.00;
$total_savings = 5170.00;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Finance Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --sidebar-bg: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }

        body {
            background-color: #f1f5f9;
            color: #1e293b;
            transition: background-color 0.3s, color 0.3s;
        }

        body.dark-mode {
            background-color: #0f172a;
            color: #f8fafc;
        }

        .sidebar {
            background: var(--sidebar-bg);
            transition: transform 0.3s ease, width 0.3s ease;
        }

        .sidebar-item {
            transition: all 0.2s ease;
        }

        .sidebar-item:hover {
            background: rgba(99, 102, 241, 0.1);
            border-left: 3px solid #6366f1;
        }

        .sidebar-item.active {
            background: rgba(99, 102, 241, 0.15);
            border-left: 3px solid #6366f1;
        }

        .card {
            background: white;
            border: 1px solid #e2e8f0;
            transition: background-color 0.3s, border-color 0.3s;
        }

        body.dark-mode .card {
            background: #334155;
            border-color: #475569;
        }

        .card-title {
            color: #1e293b;
        }

        body.dark-mode .card-title {
            color: #f8fafc;
        }

        .card-text {
            color: #64748b;
        }

        body.dark-mode .card-text {
            color: #94a3b8;
        }

        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            transition: transform 0.3s ease, width 0.3s ease;
        }

        .sidebar-item {
            transition: all 0.2s ease;
        }

        .sidebar-item:hover {
            background: rgba(99, 102, 241, 0.1);
            border-left: 3px solid #6366f1;
        }

        .sidebar-item.active {
            background: rgba(99, 102, 241, 0.15);
            border-left: 3px solid #6366f1;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-secondary) 100%);
        }

        .gradient-blue {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }

        .gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .gradient-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .gradient-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }

        .notification-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #e2e8f0;
        }

        body.dark-mode ::-webkit-scrollbar-track {
            background: #1e293b;
        }

        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #6366f1;
        }
    </style>
</head>

<body class="min-h-screen">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed left-0 top-0 h-full w-64 z-50 transform translate-x-0">
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="p-6 border-b border-slate-700">
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-wallet text-green-400"></i>
                    Smart<span class="text-blue-400">Finance</span>
                </h1>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="Home.php" class="sidebar-item active flex items-center gap-3 px-6 py-3 text-white">
                            <i class="fas fa-home w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="Income.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-arrow-trend-up w-5 text-green-400"></i>
                            <span>Income</span>
                        </a>
                    </li>
                    <li>
                        <a href="Expenses.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-arrow-trend-down w-5 text-red-400"></i>
                            <span>Expenses</span>
                        </a>
                    </li>
                    <li>
                        <a href="Budget.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-piggy-bank w-5 text-yellow-400"></i>
                            <span>Budget</span>
                        </a>
                    </li>
                    <li>
                        <a href="Saving&Goals.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-bullseye w-5 text-purple-400"></i>
                            <span>Savings & Goals</span>
                        </a>
                    </li>
                    <li>
                        <a href="transactions.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-exchange-alt w-5 text-blue-400"></i>
                            <span>Transactions</span>
                        </a>
                    </li>
                    <li>
                        <a href="Reports.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-chart-pie w-5 text-cyan-400"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li>
                        <a href="../user/profile.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-user w-5 text-pink-400"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="../user/settings.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-cog w-5 text-gray-400"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Logout Button -->
            <div class="p-4 border-t border-slate-700">
                <a href="../components/auth/logout.php" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-lg transition">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 transition-all duration-300" id="main-content">
        
        <!-- Top Navbar -->
        <nav class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-gray-200 transition-colors duration-300">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    
                    <!-- Left: Toggle & Logo -->
                    <div class="flex items-center gap-4">
                        <button id="sidebar-toggle" class="text-gray-600 hover:text-gray-900 transition">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="hidden md:flex items-center gap-2">
                            <i class="fas fa-wallet text-green-500 text-lg"></i>
                            <span class="text-gray-900 font-semibold">Smart <span class="text-blue-500">Finance</span></span>
                        </div>
                    </div>

                    <!-- Right: Actions -->
                    <div class="flex items-center gap-4">
                        
                        <!-- Dark/Light Mode Toggle -->
                        <button id="theme-toggle" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:text-gray-900 transition">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                        
                        <!-- Notifications -->
                        <button class="relative w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:text-gray-900 transition">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                        </button>

                        <!-- User Profile -->
                        <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                            <div class="text-right hidden md:block">
                                <p class="text-gray-900 font-medium text-sm"><?php echo $user_name; ?></p>
                                <p class="text-gray-500 text-xs">User</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="p-6">
            
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2 card-title">
                    Hi, <?php echo $user_name; ?> 
                </h1>
                <p class="text-gray-600 card-text">Welcome to your Smart Finance Dashboard! Here's your financial overview.</p>
            </div>

            <!-- Financial Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <!-- Total Balance -->
                <div class="stat-card card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 gradient-blue rounded-xl flex items-center justify-center">
                            <i class="fas fa-wallet text-white text-xl"></i>
                        </div>
                        <span class="text-green-500 text-sm font-medium">+12.5%</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-1 card-text">Total Balance</p>
                    <p class="text-2xl font-bold text-gray-900 card-title">$<?php echo number_format($total_balance, 2); ?></p>
                </div>

                <!-- Total Income -->
                <div class="stat-card card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 gradient-green rounded-xl flex items-center justify-center">
                            <i class="fas fa-arrow-trend-up text-white text-xl"></i>
                        </div>
                        <span class="text-green-500 text-sm font-medium">+8.2%</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-1 card-text">Total Income</p>
                    <p class="text-2xl font-bold text-gray-900 card-title">$<?php echo number_format($total_income, 2); ?></p>
                </div>

                <!-- Total Expenses -->
                <div class="stat-card card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 gradient-red rounded-xl flex items-center justify-center">
                            <i class="fas fa-arrow-trend-down text-white text-xl"></i>
                        </div>
                        <span class="text-red-500 text-sm font-medium">-3.1%</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-1 card-text">Total Expenses</p>
                    <p class="text-2xl font-bold text-gray-900 card-title">$<?php echo number_format($total_expenses, 2); ?></p>
                </div>

                <!-- Total Savings -->
                <div class="stat-card card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 gradient-purple rounded-xl flex items-center justify-center">
                            <i class="fas fa-piggy-bank text-white text-xl"></i>
                        </div>
                        <span class="text-green-500 text-sm font-medium">+15.3%</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-1 card-text">Total Savings</p>
                    <p class="text-2xl font-bold text-gray-900 card-title">$<?php echo number_format($total_savings, 2); ?></p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                
                <!-- Income vs Expenses Line Chart -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 card-title">Income vs Expenses</h3>
                    <div class="chart-container">
                        <canvas id="incomeExpenseChart"></canvas>
                    </div>
                </div>

                <!-- Expenses by Category Pie Chart -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 card-title">Expenses by Category</h3>
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Spending Bar Chart & Recent Transactions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                <!-- Monthly Spending Bar Chart -->
                <div class="card rounded-2xl p-6 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 card-title">Monthly Spending</h3>
                    <div class="chart-container">
                        <canvas id="monthlySpendingChart"></canvas>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 card-title">Recent Transactions</h3>
                        <a href="transactions.php" class="text-indigo-400 text-sm hover:text-indigo-300">View All</a>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                <i class="fas fa-arrow-down text-green-500"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 text-sm font-medium card-title">Salary Deposit</p>
                                <p class="text-gray-500 text-xs card-text">Today</p>
                            </div>
                            <p class="text-green-500 font-medium">+$5,000</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-red-500"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 text-sm font-medium card-title">Grocery Shopping</p>
                                <p class="text-gray-500 text-xs card-text">Yesterday</p>
                            </div>
                            <p class="text-red-500 font-medium">-$150</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">
                                <i class="fas fa-bolt text-red-500"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 text-sm font-medium card-title">Electricity Bill</p>
                                <p class="text-gray-500 text-xs card-text">2 days ago</p>
                            </div>
                            <p class="text-red-500 font-medium">-$85</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                <i class="fas fa-hand-holding-usd text-green-500"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 text-sm font-medium card-title">Freelance Payment</p>
                                <p class="text-gray-500 text-xs card-text">3 days ago</p>
                            </div>
                            <p class="text-green-500 font-medium">+$1,200</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Overview (50/30/20 Rule) -->
            <div class="card rounded-2xl p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 card-title">Budget Overview (50/30/20 Rule)</h3>
                    <a href="Budget.php" class="text-indigo-500 text-sm hover:text-indigo-600">Manage Budget</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-100 rounded-xl p-4 dark:bg-slate-800/50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm card-text dark:text-gray-400">Needs (50%)</span>
                            <span class="text-gray-900 font-medium card-title dark:text-white">$4,225</span>
                        </div>
                        <div class="w-full bg-gray-300 rounded-full h-2 dark:bg-slate-700">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 50%"></div>
                        </div>
                    </div>
                    <div class="bg-gray-100 rounded-xl p-4 dark:bg-slate-800/50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm card-text dark:text-gray-400">Wants (30%)</span>
                            <span class="text-gray-900 font-medium card-title dark:text-white">$2,535</span>
                        </div>
                        <div class="w-full bg-gray-300 rounded-full h-2 dark:bg-slate-700">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: 30%"></div>
                        </div>
                    </div>
                    <div class="bg-gray-100 rounded-xl p-4 dark:bg-slate-800/50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm card-text dark:text-gray-400">Savings (20%)</span>
                            <span class="text-gray-900 font-medium card-title dark:text-white">$1,690</span>
                        </div>
                        <div class="w-full bg-gray-300 rounded-full h-2 dark:bg-slate-700">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 20%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Payment Integration Preview -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 card-title">Mobile Payment Integration</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-100 rounded-xl p-4 text-center hover:bg-gray-200 transition cursor-pointer dark:bg-slate-800/50 dark:hover:bg-slate-700/50">
                        <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-mobile-alt text-green-500 text-xl dark:text-green-400"></i>
                        </div>
                        <p class="text-gray-900 text-sm font-medium card-title dark:text-white">Vodacom M-Pesa</p>
                    </div>
                    <div class="bg-gray-100 rounded-xl p-4 text-center hover:bg-gray-200 transition cursor-pointer dark:bg-slate-800/50 dark:hover:bg-slate-700/50">
                        <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-mobile-alt text-blue-500 text-xl dark:text-blue-400"></i>
                        </div>
                        <p class="text-gray-900 text-sm font-medium card-title dark:text-white">Airtel Money</p>
                    </div>
                    <div class="bg-gray-100 rounded-xl p-4 text-center hover:bg-gray-200 transition cursor-pointer dark:bg-slate-800/50 dark:hover:bg-slate-700/50">
                        <div class="w-12 h-12 rounded-full bg-yellow-500/20 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-mobile-alt text-yellow-500 text-xl dark:text-yellow-400"></i>
                        </div>
                        <p class="text-gray-900 text-sm font-medium card-title dark:text-white">Halotel</p>
                    </div>
                    <div class="bg-gray-100 rounded-xl p-4 text-center hover:bg-gray-200 transition cursor-pointer dark:bg-slate-800/50 dark:hover:bg-slate-700/50">
                        <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-mobile-alt text-purple-500 text-xl dark:text-purple-400"></i>
                        </div>
                        <p class="text-gray-900 text-sm font-medium card-title dark:text-white">Yas</p>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    // Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    let sidebarOpen = true;

    sidebarToggle.addEventListener('click', () => {
        sidebarOpen = !sidebarOpen;
        if (sidebarOpen) {
            sidebar.style.transform = 'translateX(0)';
            mainContent.style.marginLeft = '16rem';
        } else {
            sidebar.style.transform = 'translateX(-100%)';
            mainContent.style.marginLeft = '0';
        }
    });

    // Theme Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;
    
    themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        if (body.classList.contains('dark-mode')) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }
    });

    // Income vs Expenses Chart
    const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
    new Chart(incomeExpenseCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Income',
                data: [6500, 7200, 6800, 7500, 8000, 8450],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Expenses',
                data: [3200, 3500, 3100, 3800, 3400, 3280],
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: '#94a3b8' }
                }
            },
            scales: {
                y: {
                    ticks: { color: '#94a3b8' },
                    grid: { color: '#334155' }
                },
                x: {
                    ticks: { color: '#94a3b8' },
                    grid: { color: '#334155' }
                }
            }
        }
    });

    // Expenses by Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Housing', 'Food', 'Transport', 'Utilities', 'Entertainment', 'Other'],
            datasets: [{
                data: [1200, 450, 300, 200, 350, 780],
                backgroundColor: [
                    '#6366f1',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6',
                    '#3b82f6'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#94a3b8' }
                }
            }
        }
    });

    // Monthly Spending Chart
    const monthlySpendingCtx = document.getElementById('monthlySpendingChart').getContext('2d');
    new Chart(monthlySpendingCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Spending',
                data: [3200, 3500, 3100, 3800, 3400, 3280],
                backgroundColor: '#6366f1',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: '#94a3b8' }
                }
            },
            scales: {
                y: {
                    ticks: { color: '#94a3b8' },
                    grid: { color: '#334155' }
                },
                x: {
                    ticks: { color: '#94a3b8' },
                    grid: { display: false }
                }
            }
        }
    });
</script>

</body>
</html>
