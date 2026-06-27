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
$user_email = htmlspecialchars($_SESSION['email'] ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Smart Finance</title>
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

        .gradient-gray {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
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
                        <a href="../Dashboard/Home.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-home w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="../Dashboard/Income.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-arrow-trend-up w-5 text-green-400"></i>
                            <span>Income</span>
                        </a>
                    </li>
                    <li>
                        <a href="../Dashboard/Expenses.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-arrow-trend-down w-5 text-red-400"></i>
                            <span>Expenses</span>
                        </a>
                    </li>
                    <li>
                        <a href="../Dashboard/Budget.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-piggy-bank w-5 text-yellow-400"></i>
                            <span>Budget</span>
                        </a>
                    </li>
                    <li>
                        <a href="../Dashboard/Saving&Goals.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-bullseye w-5 text-purple-400"></i>
                            <span>Savings & Goals</span>
                        </a>
                    </li>
                    <li>
                        <a href="../Dashboard/transactions.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-exchange-alt w-5 text-blue-400"></i>
                            <span>Transactions</span>
                        </a>
                    </li>
                    <li>
                        <a href="../Dashboard/Reports.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-chart-pie w-5 text-cyan-400"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li>
                        <a href="profile.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-user w-5 text-pink-400"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="settings.php" class="sidebar-item active flex items-center gap-3 px-6 py-3 text-white">
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

        <!-- Settings Content -->
        <div class="p-6">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Settings</h1>
                <p class="text-gray-400">Manage your account settings and preferences</p>
            </div>

            <!-- Account Settings -->
            <div class="card rounded-2xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-6">Account Settings</h3>
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">Current Password</label>
                            <input type="password" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-gray-500" placeholder="••••••••">
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">New Password</label>
                            <input type="password" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-gray-500" placeholder="••••••••">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-300 text-sm mb-2">Confirm New Password</label>
                            <input type="password" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-gray-500" placeholder="••••••••">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="gradient-gray text-white px-8 py-3 rounded-xl font-medium hover:opacity-90 transition">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notification Settings -->
            <div class="card rounded-2xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-6">Notification Settings</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-slate-700">
                        <div>
                            <p class="text-white font-medium">Email Notifications</p>
                            <p class="text-gray-400 text-sm">Receive updates via email</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-gray-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-500"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-slate-700">
                        <div>
                            <p class="text-white font-medium">Budget Alerts</p>
                            <p class="text-gray-400 text-sm">Alert when budget limit is reached</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-gray-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-500"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-slate-700">
                        <div>
                            <p class="text-white font-medium">Transaction Alerts</p>
                            <p class="text-gray-400 text-sm">Notify on new transactions</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-gray-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-500"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-white font-medium">Goal Reminders</p>
                            <p class="text-gray-400 text-sm">Reminders for savings goals</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-gray-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Currency & Language -->
            <div class="card rounded-2xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-6">Preferences</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">Currency</label>
                        <select class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <option>Tanzanian Shilling (TZS)</option>
                            <option>US Dollar (USD)</option>
                            <option>Euro (EUR)</option>
                            <option>Kenyan Shilling (KES)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">Language</label>
                        <select class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <option>English</option>
                            <option>Swahili</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">Timezone</label>
                        <select class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <option>East Africa Time (EAT)</option>
                            <option>UTC</option>
                            <option>GMT</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">Date Format</label>
                        <select class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <option>DD/MM/YYYY</option>
                            <option>MM/DD/YYYY</option>
                            <option>YYYY-MM-DD</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="card rounded-2xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-6">Security</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-slate-700">
                        <div>
                            <p class="text-white font-medium">Two-Factor Authentication</p>
                            <p class="text-gray-400 text-sm">Add an extra layer of security</p>
                        </div>
                        <button class="bg-slate-700 text-white px-4 py-2 rounded-xl hover:bg-slate-600 transition text-sm">
                            Enable
                        </button>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-slate-700">
                        <div>
                            <p class="text-white font-medium">Login History</p>
                            <p class="text-gray-400 text-sm">View recent login activity</p>
                        </div>
                        <button class="bg-slate-700 text-white px-4 py-2 rounded-xl hover:bg-slate-600 transition text-sm">
                            View
                        </button>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-white font-medium">Connected Devices</p>
                            <p class="text-gray-400 text-sm">Manage devices with access</p>
                        </div>
                        <button class="bg-slate-700 text-white px-4 py-2 rounded-xl hover:bg-slate-600 transition text-sm">
                            Manage
                        </button>
                    </div>
                </div>
            </div>

            <!-- Data & Privacy -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-6">Data & Privacy</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-slate-700">
                        <div>
                            <p class="text-white font-medium">Export Data</p>
                            <p class="text-gray-400 text-sm">Download all your financial data</p>
                        </div>
                        <button class="bg-slate-700 text-white px-4 py-2 rounded-xl hover:bg-slate-600 transition text-sm">
                            Export
                        </button>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-slate-700">
                        <div>
                            <p class="text-white font-medium">Delete Account</p>
                            <p class="text-gray-400 text-sm">Permanently delete your account</p>
                        </div>
                        <button class="bg-red-500/20 text-red-400 px-4 py-2 rounded-xl hover:bg-red-500/30 transition text-sm">
                            Delete
                        </button>
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
</script>

</body>
</html>