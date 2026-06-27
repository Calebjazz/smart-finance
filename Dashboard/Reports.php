<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../components/auth/login.php');
    exit();
}

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['full_name'] ?? 'User');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Smart Finance</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-card: #334155;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --border-color: #475569;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
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
        }

        .gradient-cyan {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        }
    </style>
</head>

<body class="min-h-screen">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed left-0 top-0 h-full w-64 z-50 transform translate-x-0">
        <div class="flex flex-col h-full">
            <div class="p-6 border-b border-slate-700">
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-wallet text-indigo-400"></i>
                    Smart<span class="text-indigo-400">Finance</span>
                </h1>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="Home.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
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
                        <a href="Reports.php" class="sidebar-item active flex items-center gap-3 px-6 py-3 text-white">
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

            <div class="p-4 border-t border-slate-700">
                <a href="../components/auth/logout.php" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-lg transition">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64">
        
        <!-- Top Navbar -->
        <nav class="sticky top-0 z-40 bg-slate-900/80 backdrop-blur-md border-b border-slate-700">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button id="sidebar-toggle" class="text-gray-400 hover:text-white transition">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-wallet text-indigo-400 text-lg"></i>
                            <span class="text-white font-semibold">Smart Finance</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-gray-400 hover:text-white transition">
                            <i class="fas fa-moon"></i>
                        </button>
                        <button class="relative w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-gray-400 hover:text-white transition">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                        </button>
                        <div class="flex items-center gap-3 pl-4 border-l border-slate-700">
                            <div class="text-right hidden md:block">
                                <p class="text-white font-medium text-sm"><?php echo $user_name; ?></p>
                                <p class="text-gray-400 text-xs">User</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Reports Content -->
        <div class="p-6">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Financial Reports</h1>
                <p class="text-gray-400">Analytics and insights for your financial health</p>
            </div>

            <!-- Date Range Filter -->
            <div class="card rounded-2xl p-4 mb-8">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2">
                        <label class="text-gray-400 text-sm">From:</label>
                        <input type="date" class="bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-gray-400 text-sm">To:</label>
                        <input type="date" class="bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <button class="gradient-cyan text-white px-6 py-2 rounded-xl font-medium hover:opacity-90 transition">
                        <i class="fas fa-filter mr-2"></i>Apply Filter
                    </button>
                    <button class="bg-slate-700 text-white px-6 py-2 rounded-xl font-medium hover:bg-slate-600 transition">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-arrow-up text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Net Income</p>
                            <p class="text-2xl font-bold text-white">$5,170</p>
                        </div>
                    </div>
                    <p class="text-green-400 text-sm">+8.2% vs last period</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-arrow-down text-red-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Expenses</p>
                            <p class="text-2xl font-bold text-white">$3,280</p>
                        </div>
                    </div>
                    <p class="text-red-400 text-sm">-3.1% vs last period</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-piggy-bank text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Savings Rate</p>
                            <p class="text-2xl font-bold text-white">61.2%</p>
                        </div>
                    </div>
                    <p class="text-green-400 text-sm">+5.4% vs last period</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Avg Daily Spend</p>
                            <p class="text-2xl font-bold text-white">$109</p>
                        </div>
                    </div>
                    <p class="text-green-400 text-sm">-12.5% vs last period</p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Income vs Expenses Trend -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Income vs Expenses Trend</h3>
                    <canvas id="incomeExpenseChart" height="250"></canvas>
                </div>

                <!-- Expense Breakdown -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Expense Breakdown</h3>
                    <canvas id="expenseBreakdownChart" height="250"></canvas>
                </div>
            </div>

            <!-- Monthly Comparison -->
            <div class="card rounded-2xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-4">Monthly Comparison</h3>
                <canvas id="monthlyComparisonChart" height="150"></canvas>
            </div>

            <!-- AI Insights -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-white">AI-Powered Insights</h3>
                    <span class="bg-cyan-500/20 text-cyan-400 px-3 py-1 rounded-full text-sm flex items-center gap-2">
                        <i class="fas fa-robot"></i>
                        Smart Analysis
                    </span>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-green-400"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-medium mb-1">Great Savings Progress</h4>
                                <p class="text-gray-400 text-sm">Your savings rate of 61.2% is excellent. You're on track to reach your emergency fund goal by December 2026.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-exclamation text-yellow-400"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-medium mb-1">Vacation Fund Behind Schedule</h4>
                                <p class="text-gray-400 text-sm">Consider increasing your monthly contribution to $300 to reach your vacation goal by August 2026.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-lightbulb text-blue-400"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-medium mb-1">Spending Pattern Detected</h4>
                                <p class="text-gray-400 text-sm">Your food expenses have decreased by 15% this month. Keep up the good work on meal planning!</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-500/10 border border-purple-500/30 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-chart-line text-purple-400"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-medium mb-1">Prediction</h4>
                                <p class="text-gray-400 text-sm">Based on your current spending patterns, you're projected to save $6,200 by the end of the year.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    // Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('main');
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

    // Income vs Expenses Chart
    const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
    new Chart(incomeExpenseCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Income',
                data: [7500, 8200, 7800, 8500, 8100, 8450],
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

    // Expense Breakdown Chart
    const expenseBreakdownCtx = document.getElementById('expenseBreakdownChart').getContext('2d');
    new Chart(expenseBreakdownCtx, {
        type: 'doughnut',
        data: {
            labels: ['Housing', 'Food', 'Transport', 'Utilities', 'Entertainment', 'Other'],
            datasets: [{
                data: [1200, 450, 300, 200, 150, 180],
                backgroundColor: ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ec4899', '#06b6d4']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#94a3b8' }
                }
            }
        }
    });

    // Monthly Comparison Chart
    const monthlyComparisonCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
    new Chart(monthlyComparisonCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Savings',
                data: [4300, 4700, 4700, 4700, 4700, 5170],
                backgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
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
</script>

</body>
</html>
