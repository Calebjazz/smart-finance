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
    <title>Savings & Goals - Smart Finance</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
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

        .gradient-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
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
                        <a href="Saving&Goals.php" class="sidebar-item active flex items-center gap-3 px-6 py-3 text-white">
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
        <nav class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-gray-200 transition-colors duration-300">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button id="sidebar-toggle" class="text-gray-600 hover:text-gray-900 transition">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-wallet text-indigo-500 text-lg"></i>
                            <span class="text-gray-900 font-semibold">Smart Finance</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button id="theme-toggle" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:text-gray-900 transition">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                        <button class="relative w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:text-gray-900 transition">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                        </button>
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

        <!-- Savings & Goals Content -->
        <div class="p-6">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2 card-title">Savings & Goals</h1>
                <p class="text-gray-500 card-text">Track your savings progress and achieve your financial goals</p>
            </div>

            <!-- Add Goal Button -->
            <div class="mb-6">
                <button onclick="document.getElementById('addGoalModal').classList.remove('hidden')" class="gradient-purple text-white px-6 py-3 rounded-xl font-medium hover:opacity-90 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add New Goal
                </button>
            </div>

            <!-- Savings Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 gradient-purple rounded-xl flex items-center justify-center">
                            <i class="fas fa-piggy-bank text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Savings</p>
                            <p class="text-2xl font-bold text-white">$5,170.00</p>
                        </div>
                    </div>
                    <p class="text-green-400 text-sm">+15.3% from last month</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-bullseye text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Active Goals</p>
                            <p class="text-2xl font-bold text-white">4</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm">Goals in progress</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Completed</p>
                            <p class="text-2xl font-bold text-white">2</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm">Goals achieved</p>
                </div>
            </div>

            <!-- Savings Goals -->
            <div class="card rounded-2xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-6">Your Savings Goals</h3>
                
                <div class="space-y-6">
                    <!-- Emergency Fund -->
                    <div class="bg-slate-800/50 rounded-xl p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-red-500/20 flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-red-400 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold text-lg">Emergency Fund</h4>
                                    <p class="text-gray-400 text-sm">6 months of expenses</p>
                                </div>
                            </div>
                            <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">On Track</span>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-white font-medium">$8,500 / $10,000</span>
                                <span class="text-gray-400 text-sm">85%</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-red-500 to-orange-500 h-3 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Target: Dec 2026</span>
                            <span class="text-green-400">$1,500 remaining</span>
                        </div>
                    </div>

                    <!-- Vacation -->
                    <div class="bg-slate-800/50 rounded-xl p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-blue-500/20 flex items-center justify-center">
                                    <i class="fas fa-plane text-blue-400 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold text-lg">Vacation Fund</h4>
                                    <p class="text-gray-400 text-sm">Summer trip to Zanzibar</p>
                                </div>
                            </div>
                            <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-sm">Behind</span>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-white font-medium">$1,200 / $3,000</span>
                                <span class="text-gray-400 text-sm">40%</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-3 rounded-full" style="width: 40%"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Target: Aug 2026</span>
                            <span class="text-yellow-400">$1,800 remaining</span>
                        </div>
                    </div>

                    <!-- New Car -->
                    <div class="bg-slate-800/50 rounded-xl p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-purple-500/20 flex items-center justify-center">
                                    <i class="fas fa-car text-purple-400 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold text-lg">New Car</h4>
                                    <p class="text-gray-400 text-sm">Down payment for vehicle</p>
                                </div>
                            </div>
                            <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">On Track</span>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-white font-medium">$4,500 / $15,000</span>
                                <span class="text-gray-400 text-sm">30%</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-3 rounded-full" style="width: 30%"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Target: Jun 2027</span>
                            <span class="text-green-400">$10,500 remaining</span>
                        </div>
                    </div>

                    <!-- Investment -->
                    <div class="bg-slate-800/50 rounded-xl p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-green-500/20 flex items-center justify-center">
                                    <i class="fas fa-chart-line text-green-400 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold text-lg">Investment Portfolio</h4>
                                    <p class="text-gray-400 text-sm">Stock market investments</p>
                                </div>
                            </div>
                            <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">On Track</span>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-white font-medium">$2,000 / $5,000</span>
                                <span class="text-gray-400 text-sm">40%</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-3 rounded-full" style="width: 40%"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Target: Dec 2026</span>
                            <span class="text-green-400">$3,000 remaining</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auto-Deposit Rules -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-white">Auto-Deposit Rules</h3>
                    <button onclick="document.getElementById('addRuleModal').classList.remove('hidden')" class="text-indigo-400 hover:text-indigo-300 font-medium text-sm">
                        <i class="fas fa-plus mr-1"></i> Add Rule
                    </button>
                </div>
                <p class="text-gray-400 mb-4">Automatically transfer money to your savings goals based on your rules.</p>
                
                <div class="space-y-3">
                    <div class="bg-slate-800/50 rounded-xl p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                <i class="fas fa-sync text-green-400"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">Emergency Fund</p>
                                <p class="text-gray-400 text-sm">$500 monthly on payday</p>
                            </div>
                        </div>
                        <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">Active</span>
                    </div>
                    <div class="bg-slate-800/50 rounded-xl p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                                <i class="fas fa-sync text-blue-400"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">Vacation Fund</p>
                                <p class="text-gray-400 text-sm">$200 monthly on 1st</p>
                            </div>
                        </div>
                        <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">Active</span>
                    </div>
                    <div class="bg-slate-800/50 rounded-xl p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                                <i class="fas fa-sync text-purple-400"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">Investment</p>
                                <p class="text-gray-400 text-sm">10% of income monthly</p>
                            </div>
                        </div>
                        <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">Active</span>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Add Goal Modal -->
<div id="addGoalModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-slate-800 rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white">Add Savings Goal</h3>
            <button onclick="document.getElementById('addGoalModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">Goal Name</label>
                <input type="text" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="e.g., Emergency Fund, Vacation">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Target Amount</label>
                <input type="number" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="0.00">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Target Date</label>
                <input type="date" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Category</label>
                <select class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option>Emergency Fund</option>
                    <option>Vacation</option>
                    <option>Vehicle</option>
                    <option>Home</option>
                    <option>Education</option>
                    <option>Investment</option>
                    <option>Other</option>
                </select>
            </div>
            <button type="submit" class="w-full gradient-purple text-white py-3 rounded-xl font-medium hover:opacity-90 transition">
                Create Goal
            </button>
        </form>
    </div>
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
</script>

</body>
</html>