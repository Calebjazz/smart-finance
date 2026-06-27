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
    <title>Budget - Smart Finance</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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

        .gradient-yellow {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
                        <a href="Budget.php" class="sidebar-item active flex items-center gap-3 px-6 py-3 text-white">
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

        <!-- Budget Content -->
        <div class="p-6">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Budget Management</h1>
                <p class="text-gray-400">Manage your budget using the 50/30/20 rule with AI-powered automation</p>
            </div>

            <!-- 50/30/20 Rule Overview -->
            <div class="card rounded-2xl p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-white">50/30/20 Budget Rule</h3>
                    <button class="gradient-yellow text-white px-4 py-2 rounded-xl font-medium hover:opacity-90 transition text-sm">
                        <i class="fas fa-robot mr-2"></i>AI Auto-Budget
                    </button>
                </div>
                <p class="text-gray-400 mb-6">This rule allocates 50% to needs, 30% to wants, and 20% to savings. AI will automatically adjust based on your spending patterns via n8n integration.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Needs (50%) -->
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-home text-blue-400 text-xl"></i>
                            </div>
                            <span class="text-blue-400 font-bold text-2xl">50%</span>
                        </div>
                        <h4 class="text-white font-semibold mb-2">Needs</h4>
                        <p class="text-gray-400 text-sm mb-4">Essential expenses: rent, utilities, groceries, transport</p>
                        <div class="flex items-center justify-between">
                            <span class="text-white font-medium">$4,225</span>
                            <span class="text-gray-400 text-sm">of $8,450</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2 mt-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 50%"></div>
                        </div>
                    </div>

                    <!-- Wants (30%) -->
                    <div class="bg-purple-500/10 border border-purple-500/30 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-heart text-purple-400 text-xl"></i>
                            </div>
                            <span class="text-purple-400 font-bold text-2xl">30%</span>
                        </div>
                        <h4 class="text-white font-semibold mb-2">Wants</h4>
                        <p class="text-gray-400 text-sm mb-4">Discretionary spending: entertainment, dining out, hobbies</p>
                        <div class="flex items-center justify-between">
                            <span class="text-white font-medium">$2,535</span>
                            <span class="text-gray-400 text-sm">of $8,450</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2 mt-2">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: 30%"></div>
                        </div>
                    </div>

                    <!-- Savings (20%) -->
                    <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-piggy-bank text-green-400 text-xl"></i>
                            </div>
                            <span class="text-green-400 font-bold text-2xl">20%</span>
                        </div>
                        <h4 class="text-white font-semibold mb-2">Savings</h4>
                        <p class="text-gray-400 text-sm mb-4">Financial goals: emergency fund, investments, debt repayment</p>
                        <div class="flex items-center justify-between">
                            <span class="text-white font-medium">$1,690</span>
                            <span class="text-gray-400 text-sm">of $8,450</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2 mt-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 20%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Categories -->
            <div class="card rounded-2xl p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-white">Budget Categories</h3>
                    <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')" class="text-indigo-400 hover:text-indigo-300 font-medium text-sm">
                        <i class="fas fa-plus mr-1"></i> Add Category
                    </button>
                </div>
                
                <div class="space-y-4">
                    <!-- Housing -->
                    <div class="bg-slate-800/50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                                    <i class="fas fa-home text-blue-400"></i>
                                </div>
                                <div>
                                    <p class="text-white font-medium">Housing</p>
                                    <p class="text-gray-400 text-xs">Rent, mortgage, insurance</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-white font-medium">$1,200 / $1,500</p>
                                <p class="text-green-400 text-xs">80% used</p>
                            </div>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 80%"></div>
                        </div>
                    </div>

                    <!-- Food -->
                    <div class="bg-slate-800/50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                    <i class="fas fa-utensils text-green-400"></i>
                                </div>
                                <div>
                                    <p class="text-white font-medium">Food & Groceries</p>
                                    <p class="text-gray-400 text-xs">Groceries, dining out</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-white font-medium">$450 / $600</p>
                                <p class="text-green-400 text-xs">75% used</p>
                            </div>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>

                    <!-- Transport -->
                    <div class="bg-slate-800/50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                                    <i class="fas fa-car text-purple-400"></i>
                                </div>
                                <div>
                                    <p class="text-white font-medium">Transportation</p>
                                    <p class="text-gray-400 text-xs">Fuel, public transport, maintenance</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-white font-medium">$300 / $400</p>
                                <p class="text-yellow-400 text-xs">75% used</p>
                            </div>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>

                    <!-- Entertainment -->
                    <div class="bg-slate-800/50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-pink-500/20 flex items-center justify-center">
                                    <i class="fas fa-film text-pink-400"></i>
                                </div>
                                <div>
                                    <p class="text-white font-medium">Entertainment</p>
                                    <p class="text-gray-400 text-xs">Movies, subscriptions, hobbies</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-white font-medium">$200 / $300</p>
                                <p class="text-green-400 text-xs">67% used</p>
                            </div>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-pink-500 h-2 rounded-full" style="width: 67%"></div>
                        </div>
                    </div>

                    <!-- Savings -->
                    <div class="bg-slate-800/50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                    <i class="fas fa-piggy-bank text-yellow-400"></i>
                                </div>
                                <div>
                                    <p class="text-white font-medium">Savings</p>
                                    <p class="text-gray-400 text-xs">Emergency fund, investments</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-white font-medium">$1,690 / $1,690</p>
                                <p class="text-green-400 text-xs">100% saved</p>
                            </div>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- n8n Integration Status -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">n8n Automation Integration</h3>
                    <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        Active
                    </span>
                </div>
                <p class="text-gray-400 mb-4">Your budget is automatically synchronized with n8n workflows for AI-powered budget adjustments and alerts.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-slate-800/50 rounded-xl p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas fa-sync text-indigo-400"></i>
                            <span class="text-white font-medium">Auto-Sync</span>
                        </div>
                        <p class="text-gray-400 text-sm">Every 6 hours</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-xl p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas fa-bell text-yellow-400"></i>
                            <span class="text-white font-medium">Smart Alerts</span>
                        </div>
                        <p class="text-gray-400 text-sm">Budget limit warnings</p>
                    </div>
                    <div class="bg-slate-800/50 rounded-xl p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas fa-chart-line text-green-400"></i>
                            <span class="text-white font-medium">AI Insights</span>
                        </div>
                        <p class="text-gray-400 text-sm">Spending predictions</p>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-slate-800 rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white">Add Budget Category</h3>
            <button onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">Category Name</label>
                <input type="text" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500" placeholder="e.g., Healthcare, Education">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Budget Type</label>
                <select class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option>Need (50%)</option>
                    <option>Want (30%)</option>
                    <option>Savings (20%)</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Monthly Budget</label>
                <input type="number" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500" placeholder="0.00">
            </div>
            <button type="submit" class="w-full gradient-yellow text-white py-3 rounded-xl font-medium hover:opacity-90 transition">
                Add Category
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
</script>

</body>
</html>