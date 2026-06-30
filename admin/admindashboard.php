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
    <title>Admin Dashboard - Smart Finance</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <i class="fas fa-shield-alt text-indigo-400"></i>
                    Admin<span class="text-indigo-400">Panel</span>
                </h1>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="admindashboard.php" class="sidebar-item active flex items-center gap-3 px-6 py-3 text-white">
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
                        <a href="Automation_logs.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
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

        <!-- Admin Dashboard Content -->
        <div class="p-6">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2 card-title">Admin Dashboard</h1>
                <p class="text-gray-500 card-text">Overview of system statistics and management</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-users text-blue-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm card-text">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900 card-title">1,248</p>
                        </div>
                    </div>
                    <p class="text-green-500 text-sm">+12% from last month</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-green-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm card-text">Total Transactions</p>
                            <p class="text-2xl font-bold text-gray-900 card-title">8,542</p>
                        </div>
                    </div>
                    <p class="text-green-500 text-sm">+8% from last month</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-robot text-purple-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm card-text">AI Requests</p>
                            <p class="text-2xl font-bold text-gray-900 card-title">3,291</p>
                        </div>
                    </div>
                    <p class="text-green-500 text-sm">+24% from last month</p>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-cogs text-yellow-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm card-text">Automations</p>
                            <p class="text-2xl font-bold text-gray-900 card-title">156</p>
                        </div>
                    </div>
                    <p class="text-green-500 text-sm">+5% from last month</p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- User Growth Chart -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 card-title">User Growth</h3>
                    <canvas id="userGrowthChart" height="250"></canvas>
                </div>

                <!-- Transaction Volume Chart -->
                <div class="card rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 card-title">Transaction Volume</h3>
                    <canvas id="transactionChart" height="250"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 card-title">Recent Activity</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                            <i class="fas fa-user-plus text-blue-500"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-900 font-medium card-title">New user registered</p>
                            <p class="text-gray-500 text-sm card-text">John Doe joined the platform</p>
                        </div>
                        <span class="text-gray-400 text-sm">2 min ago</span>
                    </div>
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                            <i class="fas fa-robot text-green-500"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-900 font-medium card-title">AI Budget Analysis</p>
                            <p class="text-gray-500 text-sm card-text">Budget recommendations generated</p>
                        </div>
                        <span class="text-gray-400 text-sm">15 min ago</span>
                    </div>
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                            <i class="fas fa-cog text-yellow-500"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-900 font-medium card-title">Automation Triggered</p>
                            <p class="text-gray-500 text-sm card-text">Monthly savings transfer completed</p>
                        </div>
                        <span class="text-gray-400 text-sm">1 hour ago</span>
                    </div>
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                            <i class="fas fa-tag text-purple-500"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-900 font-medium card-title">Category Updated</p>
                            <p class="text-gray-500 text-sm card-text">New expense category added</p>
                        </div>
                        <span class="text-gray-400 text-sm">3 hours ago</span>
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

    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'New Users',
                data: [120, 190, 300, 500, 800, 1248],
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: { color: '#64748b' }
                }
            },
            scales: {
                y: {
                    ticks: { color: '#64748b' },
                    grid: { color: '#e2e8f0' }
                },
                x: {
                    ticks: { color: '#64748b' },
                    grid: { color: '#e2e8f0' }
                }
            }
        }
    });

    // Transaction Volume Chart
    const transactionCtx = document.getElementById('transactionChart').getContext('2d');
    new Chart(transactionCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Transactions',
                data: [1200, 1900, 3000, 5000, 7500, 8542],
                backgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: { color: '#64748b' }
                }
            },
            scales: {
                y: {
                    ticks: { color: '#64748b' },
                    grid: { color: '#e2e8f0' }
                },
                x: {
                    ticks: { color: '#64748b' },
                    grid: { color: '#e2e8f0' }
                }
            }
        }
    });
</script>

</body>
</html>
