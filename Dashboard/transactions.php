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
    <title>Transactions - Smart Finance</title>
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

        .gradient-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
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
                        <a href="transactions.php" class="sidebar-item active flex items-center gap-3 px-6 py-3 text-white">
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

        <!-- Transactions Content -->
        <div class="p-6">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Transactions</h1>
                <p class="text-gray-400">View and manage all your financial transactions</p>
            </div>

            <!-- Filters -->
            <div class="card rounded-2xl p-4 mb-8">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2">
                        <label class="text-gray-400 text-sm">Type:</label>
                        <select class="bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>All</option>
                            <option>Income</option>
                            <option>Expense</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-gray-400 text-sm">Category:</label>
                        <select class="bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>All Categories</option>
                            <option>Housing</option>
                            <option>Food</option>
                            <option>Transport</option>
                            <option>Utilities</option>
                            <option>Entertainment</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-gray-400 text-sm">From:</label>
                        <input type="date" class="bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-gray-400 text-sm">To:</label>
                        <input type="date" class="bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button class="gradient-blue text-white px-6 py-2 rounded-xl font-medium hover:opacity-90 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <button class="bg-slate-700 text-white px-6 py-2 rounded-xl font-medium hover:bg-slate-600 transition">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-arrow-down text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Income</p>
                            <p class="text-2xl font-bold text-white">$8,450</p>
                        </div>
                    </div>
                    <p class="text-green-400 text-sm">This month</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-arrow-up text-red-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Expenses</p>
                            <p class="text-2xl font-bold text-white">$3,280</p>
                        </div>
                    </div>
                    <p class="text-red-400 text-sm">This month</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-receipt text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Transactions</p>
                            <p class="text-2xl font-bold text-white">45</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm">This month</p>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-white">All Transactions</h3>
                    <button onclick="document.getElementById('addTransactionModal').classList.remove('hidden')" class="gradient-blue text-white px-4 py-2 rounded-xl font-medium hover:opacity-90 transition text-sm">
                        <i class="fas fa-plus mr-2"></i>Add Transaction
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-400 text-sm border-b border-slate-700">
                                <th class="pb-3">Date</th>
                                <th class="pb-3">Description</th>
                                <th class="pb-3">Category</th>
                                <th class="pb-3">Type</th>
                                <th class="pb-3">Amount</th>
                                <th class="pb-3">Payment Method</th>
                                <th class="pb-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-white">
                            <tr class="border-b border-slate-700/50">
                                <td class="py-4 text-gray-400">Jun 18, 2026</td>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                            <i class="fas fa-building text-green-400"></i>
                                        </div>
                                        <span>Salary Deposit</span>
                                    </div>
                                </td>
                                <td class="py-4"><span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-sm">Employment</span></td>
                                <td class="py-4"><span class="text-green-400 font-medium">Income</span></td>
                                <td class="py-4 font-medium text-green-400">+$5,000.00</td>
                                <td class="py-4"><span class="bg-slate-600 text-gray-300 px-3 py-1 rounded-full text-sm">Bank Transfer</span></td>
                                <td class="py-4">
                                    <button class="text-gray-400 hover:text-white mr-2"><i class="fas fa-eye"></i></button>
                                    <button class="text-gray-400 hover:text-red-400"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr class="border-b border-slate-700/50">
                                <td class="py-4 text-gray-400">Jun 18, 2026</td>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                                            <i class="fas fa-shopping-cart text-blue-400"></i>
                                        </div>
                                        <span>Grocery Shopping</span>
                                    </div>
                                </td>
                                <td class="py-4"><span class="bg-green-500/20 text-green-300 px-3 py-1 rounded-full text-sm">Food</span></td>
                                <td class="py-4"><span class="text-red-400 font-medium">Expense</span></td>
                                <td class="py-4 font-medium text-red-400">-$150.00</td>
                                <td class="py-4"><span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded-full text-sm">M-Pesa</span></td>
                                <td class="py-4">
                                    <button class="text-gray-400 hover:text-white mr-2"><i class="fas fa-eye"></i></button>
                                    <button class="text-gray-400 hover:text-red-400"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr class="border-b border-slate-700/50">
                                <td class="py-4 text-gray-400">Jun 17, 2026</td>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                            <i class="fas fa-bolt text-yellow-400"></i>
                                        </div>
                                        <span>Electricity Bill</span>
                                    </div>
                                </td>
                                <td class="py-4"><span class="bg-yellow-500/20 text-yellow-300 px-3 py-1 rounded-full text-sm">Utilities</span></td>
                                <td class="py-4"><span class="text-red-400 font-medium">Expense</span></td>
                                <td class="py-4 font-medium text-red-400">-$85.00</td>
                                <td class="py-4"><span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-sm">Bank</span></td>
                                <td class="py-4">
                                    <button class="text-gray-400 hover:text-white mr-2"><i class="fas fa-eye"></i></button>
                                    <button class="text-gray-400 hover:text-red-400"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr class="border-b border-slate-700/50">
                                <td class="py-4 text-gray-400">Jun 16, 2026</td>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                                            <i class="fas fa-laptop text-purple-400"></i>
                                        </div>
                                        <span>Freelance Payment</span>
                                    </div>
                                </td>
                                <td class="py-4"><span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded-full text-sm">Self-Employment</span></td>
                                <td class="py-4"><span class="text-green-400 font-medium">Income</span></td>
                                <td class="py-4 font-medium text-green-400">+$800.00</td>
                                <td class="py-4"><span class="bg-cyan-500/20 text-cyan-300 px-3 py-1 rounded-full text-sm">Airtel</span></td>
                                <td class="py-4">
                                    <button class="text-gray-400 hover:text-white mr-2"><i class="fas fa-eye"></i></button>
                                    <button class="text-gray-400 hover:text-red-400"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr class="border-b border-slate-700/50">
                                <td class="py-4 text-gray-400">Jun 16, 2026</td>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                                            <i class="fas fa-car text-purple-400"></i>
                                        </div>
                                        <span>Fuel</span>
                                    </div>
                                </td>
                                <td class="py-4"><span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded-full text-sm">Transport</span></td>
                                <td class="py-4"><span class="text-red-400 font-medium">Expense</span></td>
                                <td class="py-4 font-medium text-red-400">-$60.00</td>
                                <td class="py-4"><span class="bg-cyan-500/20 text-cyan-300 px-3 py-1 rounded-full text-sm">Airtel</span></td>
                                <td class="py-4">
                                    <button class="text-gray-400 hover:text-white mr-2"><i class="fas fa-eye"></i></button>
                                    <button class="text-gray-400 hover:text-red-400"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr class="border-b border-slate-700/50">
                                <td class="py-4 text-gray-400">Jun 15, 2026</td>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-pink-500/20 flex items-center justify-center">
                                            <i class="fas fa-film text-pink-400"></i>
                                        </div>
                                        <span>Netflix Subscription</span>
                                    </div>
                                </td>
                                <td class="py-4"><span class="bg-pink-500/20 text-pink-300 px-3 py-1 rounded-full text-sm">Entertainment</span></td>
                                <td class="py-4"><span class="text-red-400 font-medium">Expense</span></td>
                                <td class="py-4 font-medium text-red-400">-$15.00</td>
                                <td class="py-4"><span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-sm">Credit Card</span></td>
                                <td class="py-4">
                                    <button class="text-gray-400 hover:text-white mr-2"><i class="fas fa-eye"></i></button>
                                    <button class="text-gray-400 hover:text-red-400"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 text-gray-400">Jun 15, 2026</td>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                            <i class="fas fa-chart-line text-yellow-400"></i>
                                        </div>
                                        <span>Investment Return</span>
                                    </div>
                                </td>
                                <td class="py-4"><span class="bg-yellow-500/20 text-yellow-300 px-3 py-1 rounded-full text-sm">Investment</span></td>
                                <td class="py-4"><span class="text-green-400 font-medium">Income</span></td>
                                <td class="py-4 font-medium text-green-400">+$250.00</td>
                                <td class="py-4"><span class="bg-green-500/20 text-green-300 px-3 py-1 rounded-full text-sm">Bank</span></td>
                                <td class="py-4">
                                    <button class="text-gray-400 hover:text-white mr-2"><i class="fas fa-eye"></i></button>
                                    <button class="text-gray-400 hover:text-red-400"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between mt-6">
                    <p class="text-gray-400 text-sm">Showing 1-7 of 45 transactions</p>
                    <div class="flex items-center gap-2">
                        <button class="w-10 h-10 rounded-lg bg-slate-700 text-gray-400 hover:text-white hover:bg-slate-600 transition">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="w-10 h-10 rounded-lg bg-blue-500 text-white">1</button>
                        <button class="w-10 h-10 rounded-lg bg-slate-700 text-gray-400 hover:text-white hover:bg-slate-600 transition">2</button>
                        <button class="w-10 h-10 rounded-lg bg-slate-700 text-gray-400 hover:text-white hover:bg-slate-600 transition">3</button>
                        <button class="w-10 h-10 rounded-lg bg-slate-700 text-gray-400 hover:text-white hover:bg-slate-600 transition">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Add Transaction Modal -->
<div id="addTransactionModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-slate-800 rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white">Add Transaction</h3>
            <button onclick="document.getElementById('addTransactionModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">Type</label>
                <select class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>Income</option>
                    <option>Expense</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Description</label>
                <input type="text" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Grocery, Salary">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Category</label>
                <select class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>Housing</option>
                    <option>Food</option>
                    <option>Transport</option>
                    <option>Utilities</option>
                    <option>Entertainment</option>
                    <option>Employment</option>
                    <option>Investment</option>
                    <option>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Amount</label>
                <input type="number" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Payment Method</label>
                <select class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>Cash</option>
                    <option>Bank Transfer</option>
                    <option>M-Pesa</option>
                    <option>Airtel Money</option>
                    <option>Halotel</option>
                    <option>Yas</option>
                    <option>Credit Card</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Date</label>
                <input type="date" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="w-full gradient-blue text-white py-3 rounded-xl font-medium hover:opacity-90 transition">
                Add Transaction
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