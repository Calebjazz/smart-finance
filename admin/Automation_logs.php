<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../components/auth/login.php');
    exit();
}

require_once '../config/database.php';

$admin_name = htmlspecialchars($_SESSION['full_name'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automation Logs - Smart Finance Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
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
                    <i class="fas fa-shield-alt text-indigo-400"></i>
                    Admin<span class="text-indigo-400">Panel</span>
                </h1>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="admindashboard.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-tachometer-alt w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="users.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-users w-5 text-blue-400"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="categories.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-tags w-5 text-green-400"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li>
                        <a href="Ai_logs.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
                            <i class="fas fa-robot w-5 text-purple-400"></i>
                            <span>AI Logs</span>
                        </a>
                    </li>
                    <li>
                        <a href="Automation_logs.php" class="sidebar-item active flex items-center gap-3 px-6 py-3 text-white">
                            <i class="fas fa-cogs w-5 text-yellow-400"></i>
                            <span>Automation Logs</span>
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
                            <i class="fas fa-shield-alt text-indigo-500 text-lg"></i>
                            <span class="text-gray-900 font-semibold">Smart Finance Admin</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button id="theme-toggle" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:text-gray-900 transition">
                            <i class="fas fa-moon"></i>
                        </button>
                        <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                            <div class="text-right hidden md:block">
                                <p class="text-gray-900 font-medium text-sm"><?php echo $admin_name; ?></p>
                                <p class="text-gray-500 text-xs">Administrator</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                A
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Automation Logs Content -->
        <div class="p-6">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2 card-title">Automation Logs</h1>
                <p class="text-gray-500 card-text">View all automated tasks and n8n integrations</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-cogs text-yellow-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm card-text">Total Automations</p>
                            <p class="text-2xl font-bold text-gray-900 card-title">156</p>
                        </div>
                    </div>
                    <p class="text-green-500 text-sm">+5% this month</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-play-circle text-green-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm card-text">Executed Today</p>
                            <p class="text-2xl font-bold text-gray-900 card-title">42</p>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm">Running smoothly</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-blue-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm card-text">Scheduled</p>
                            <p class="text-2xl font-bold text-gray-900 card-title">18</p>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm">Pending execution</p>
                </div>
            </div>

            <!-- Filter -->
            <div class="card rounded-2xl p-4 mb-8">
                <div class="flex flex-wrap items-center gap-4">
                    <select class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors duration-300">
                        <option>All Types</option>
                        <option>Savings Transfer</option>
                        <option>Budget Alert</option>
                        <option>Goal Reminder</option>
                        <option>Report Generation</option>
                    </select>
                    <select class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors duration-300">
                        <option>All Status</option>
                        <option>Completed</option>
                        <option>Running</option>
                        <option>Failed</option>
                    </select>
                    <input type="date" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors duration-300">
                </div>
            </div>

            <!-- Automation Logs Table -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 card-title">Recent Automations</h3>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-xl p-4 transition-colors duration-300">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                    <i class="fas fa-check text-green-500"></i>
                                </div>
                                <div>
                                    <p class="text-gray-900 font-medium card-title">Monthly Savings Transfer</p>
                                    <p class="text-gray-500 text-sm card-text">User ID: 1248</p>
                                </div>
                            </div>
                            <span class="text-gray-400 text-sm">2 min ago</span>
                        </div>
                        <div class="pl-13">
                            <p class="text-gray-600 text-sm card-text">Transferred TZS 50,000 to savings goal (Vacation Fund)</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 transition-colors duration-300">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                    <i class="fas fa-check text-green-500"></i>
                                </div>
                                <div>
                                    <p class="text-gray-900 font-medium card-title">Budget Alert</p>
                                    <p class="text-gray-500 text-sm card-text">User ID: 1245</p>
                                </div>
                            </div>
                            <span class="text-gray-400 text-sm">15 min ago</span>
                        </div>
                        <div class="pl-13">
                            <p class="text-gray-600 text-sm card-text">Alert sent: Food budget at 85% of monthly limit</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 transition-colors duration-300">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                                    <i class="fas fa-spinner fa-spin text-blue-500"></i>
                                </div>
                                <div>
                                    <p class="text-gray-900 font-medium card-title">Report Generation</p>
                                    <p class="text-gray-500 text-sm card-text">User ID: 1239</p>
                                </div>
                            </div>
                            <span class="text-gray-400 text-sm">Running</span>
                        </div>
                        <div class="pl-13">
                            <p class="text-gray-600 text-sm card-text">Generating monthly financial report...</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 transition-colors duration-300">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                    <i class="fas fa-check text-green-500"></i>
                                </div>
                                <div>
                                    <p class="text-gray-900 font-medium card-title">Goal Reminder</p>
                                    <p class="text-gray-500 text-sm card-text">User ID: 1235</p>
                                </div>
                            </div>
                            <span class="text-gray-400 text-sm">1 hour ago</span>
                        </div>
                        <div class="pl-13">
                            <p class="text-gray-600 text-sm card-text">Reminder sent: Emergency Fund goal 75% complete</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 transition-colors duration-300">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">
                                    <i class="fas fa-times text-red-500"></i>
                                </div>
                                <div>
                                    <p class="text-gray-900 font-medium card-title">Savings Transfer</p>
                                    <p class="text-gray-500 text-sm card-text">User ID: 1230</p>
                                </div>
                            </div>
                            <span class="text-gray-400 text-sm">2 hours ago</span>
                        </div>
                        <div class="pl-13">
                            <p class="text-gray-600 text-sm card-text">Failed: Insufficient funds for scheduled transfer</p>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between mt-6">
                    <p class="text-gray-500 text-sm card-text">Showing 1-5 of 156 logs</p>
                    <div class="flex items-center gap-2">
                        <button class="w-10 h-10 rounded-lg bg-gray-100 text-gray-600 hover:text-gray-900 hover:bg-gray-200 transition">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="w-10 h-10 rounded-lg bg-yellow-500 text-white">1</button>
                        <button class="w-10 h-10 rounded-lg bg-gray-100 text-gray-600 hover:text-gray-900 hover:bg-gray-200 transition">2</button>
                        <button class="w-10 h-10 rounded-lg bg-gray-100 text-gray-600 hover:text-gray-900 hover:bg-gray-200 transition">3</button>
                        <button class="w-10 h-10 rounded-lg bg-gray-100 text-gray-600 hover:text-gray-900 hover:bg-gray-200 transition">
                            <i class="fas fa-chevron-right"></i>
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

    // Theme Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    
    themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        const icon = themeToggle.querySelector('i');
        if (body.classList.contains('dark-mode')) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        }
    });
</script>

</body>
</html>