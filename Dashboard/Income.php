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
    <title>Income - Smart Finance</title>
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

        .gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
                        <a href="Income.php" class="sidebar-item active flex items-center gap-3 px-6 py-3 text-white">
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
                        <button class="relative w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-gray-400 hover:text-white transition">
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

        <!-- Income Content -->
        <div class="p-6">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Income Management</h1>
                <p class="text-gray-400">Track and manage all your income sources</p>
            </div>

            <!-- Add Income Button -->
            <div class="mb-6">
                <button onclick="document.getElementById('addIncomeModal').classList.remove('hidden')" class="gradient-green text-white px-6 py-3 rounded-xl font-medium hover:opacity-90 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add Income
                </button>
            </div>

            <!-- Income Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 gradient-green rounded-xl flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Income</p>
                            <p class="text-2xl font-bold text-white">$8,450.00</p>
                        </div>
                    </div>
                    <p class="text-green-400 text-sm">+12.5% from last month</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-briefcase text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Active Sources</p>
                            <p class="text-2xl font-bold text-white">4</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm">Income streams active</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">This Month</p>
                            <p class="text-2xl font-bold text-white">$5,000.00</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm">Monthly total</p>
                </div>
            </div>

            <!-- Income Sources Table -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Income Sources</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-400 text-sm border-b border-slate-700">
                                <th class="pb-3">Source</th>
                                <th class="pb-3">Category</th>
                                <th class="pb-3">Amount</th>
                                <th class="pb-3">Frequency</th>
                                <th class="pb-3">Last Received</th>
                                <th class="pb-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-white">
                            <tr class="border-b border-slate-700/50">
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                            <i class="fas fa-building text-green-400"></i>
                                        </div>
                                        <span>Salary</span>
                                    </div>
                                </td>
                                <td class="py-4"><span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-sm">Employment</span></td>
                                <td class="py-4 font-medium">$5,000.00</td>
                                <td class="py-4">Monthly</td>
                                <td class="py-4 text-gray-400">Jun 1, 2026</td>
                                <td class="py-4">
                                    <button class="text-gray-400 hover:text-white mr-2"><i class="fas fa-edit"></i></button>
                                    <button class="text-gray-400 hover:text-red-400"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr class="border-b border-slate-700/50">
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                                            <i class="fas fa-laptop text-purple-400"></i>
                                        </div>
                                        <span>Freelance</span>
                                    </div>
                                </td>
                                <td class="py-4"><span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded-full text-sm">Self-Employment</span></td>
                                <td class="py-4 font-medium">$2,450.00</td>
                                <td class="py-4">Variable</td>
                                <td class="py-4 text-gray-400">Jun 15, 2026</td>
                                <td class="py-4">
                                    <button class="text-gray-400 hover:text-white mr-2"><i class="fas fa-edit"></i></button>
                                    <button class="text-gray-400 hover:text-red-400"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr class="border-b border-slate-700/50">
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                            <i class="fas fa-chart-line text-yellow-400"></i>
                                        </div>
                                        <span>Investments</span>
                                    </div>
                                </td>
                                <td class="py-4"><span class="bg-yellow-500/20 text-yellow-300 px-3 py-1 rounded-full text-sm">Investment</span></td>
                                <td class="py-4 font-medium">$750.00</td>
                                <td class="py-4">Quarterly</td>
                                <td class="py-4 text-gray-400">May 30, 2026</td>
                                <td class="py-4">
                                    <button class="text-gray-400 hover:text-white mr-2"><i class="fas fa-edit"></i></button>
                                    <button class="text-gray-400 hover:text-red-400"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-cyan-500/20 flex items-center justify-center">
                                            <i class="fas fa-gift text-cyan-400"></i>
                                        </div>
                                        <span>Side Hustle</span>
                                    </div>
                                </td>
                                <td class="py-4"><span class="bg-cyan-500/20 text-cyan-300 px-3 py-1 rounded-full text-sm">Other</span></td>
                                <td class="py-4 font-medium">$250.00</td>
                                <td class="py-4">Variable</td>
                                <td class="py-4 text-gray-400">Jun 10, 2026</td>
                                <td class="py-4">
                                    <button class="text-gray-400 hover:text-white mr-2"><i class="fas fa-edit"></i></button>
                                    <button class="text-gray-400 hover:text-red-400"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Add Income Modal -->
<div id="addIncomeModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-slate-800 rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white">Add Income Source</h3>
            <button onclick="document.getElementById('addIncomeModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">Source Name</label>
                <input type="text" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="e.g., Salary, Freelance">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Category</label>
                <select class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option>Employment</option>
                    <option>Self-Employment</option>
                    <option>Investment</option>
                    <option>Rental</option>
                    <option>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Amount</label>
                <input type="number" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="0.00">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">Frequency</label>
                <select class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option>One-time</option>
                    <option>Weekly</option>
                    <option>Bi-weekly</option>
                    <option>Monthly</option>
                    <option>Quarterly</option>
                    <option>Yearly</option>
                </select>
            </div>
            <button type="submit" class="w-full gradient-green text-white py-3 rounded-xl font-medium hover:opacity-90 transition">
                Add Income Source
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