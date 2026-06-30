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
    <title>Categories Management - Smart Finance Admin</title>
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
                        <a href="categories.php" class="sidebar-item active flex items-center gap-3 px-6 py-3 text-white">
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

        <!-- Categories Content -->
        <div class="p-6">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2 card-title">Categories Management</h1>
                <p class="text-gray-500 card-text">Manage income and expense categories</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-arrow-down text-green-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm card-text">Income Categories</p>
                            <p class="text-2xl font-bold text-gray-900 card-title">8</p>
                        </div>
                    </div>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-arrow-up text-red-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm card-text">Expense Categories</p>
                            <p class="text-2xl font-bold text-gray-900 card-title">12</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Category Button -->
            <div class="mb-8">
                <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')" class="gradient-green text-white px-6 py-3 rounded-xl font-medium hover:opacity-90 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add New Category
                </button>
            </div>

            <!-- Income Categories -->
            <div class="card rounded-2xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 card-title">Income Categories</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3 transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                            <i class="fas fa-building text-green-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium card-title">Salary</p>
                            <p class="text-gray-500 text-xs card-text">Employment</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3 transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                            <i class="fas fa-laptop text-blue-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium card-title">Freelance</p>
                            <p class="text-gray-500 text-xs card-text">Self-Employment</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3 transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium card-title">Investments</p>
                            <p class="text-gray-500 text-xs card-text">Returns</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3 transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                            <i class="fas fa-gift text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium card-title">Gifts</p>
                            <p class="text-gray-500 text-xs card-text">Received</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                    </div>
                </div>
            </div>

            <!-- Expense Categories -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 card-title">Expense Categories</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3 transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">
                            <i class="fas fa-home text-red-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium card-title">Housing</p>
                            <p class="text-gray-500 text-xs card-text">Rent/Mortgage</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3 transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-green-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium card-title">Food</p>
                            <p class="text-gray-500 text-xs card-text">Groceries</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3 transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                            <i class="fas fa-car text-blue-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium card-title">Transport</p>
                            <p class="text-gray-500 text-xs card-text">Fuel/Travel</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3 transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                            <i class="fas fa-bolt text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium card-title">Utilities</p>
                            <p class="text-gray-500 text-xs card-text">Electricity/Water</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3 transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-pink-500/20 flex items-center justify-center">
                            <i class="fas fa-film text-pink-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium card-title">Entertainment</p>
                            <p class="text-gray-500 text-xs card-text">Movies/Games</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3 transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                            <i class="fas fa-heartbeat text-purple-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium card-title">Healthcare</p>
                            <p class="text-gray-500 text-xs card-text">Medical</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3 transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-cyan-500/20 flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-cyan-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium card-title">Education</p>
                            <p class="text-gray-500 text-xs card-text">Learning</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3 transition-colors duration-300">
                        <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-orange-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium card-title">Shopping</p>
                            <p class="text-gray-500 text-xs card-text">Clothing/Items</p>
                        </div>
                        <button class="ml-auto text-gray-400 hover:text-blue-500"><i class="fas fa-edit"></i></button>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 transition-colors duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 card-title">Add Category</h3>
            <button onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-gray-700 text-sm mb-2">Category Name</label>
                <input type="text" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="e.g., Dining Out">
            </div>
            <div>
                <label class="block text-gray-700 text-sm mb-2">Type</label>
                <select class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option>Income</option>
                    <option>Expense</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 text-sm mb-2">Icon</label>
                <select class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option>fa-home</option>
                    <option>fa-car</option>
                    <option>fa-shopping-cart</option>
                    <option>fa-bolt</option>
                    <option>fa-film</option>
                    <option>fa-heartbeat</option>
                </select>
            </div>
            <button type="submit" class="w-full gradient-green text-white py-3 rounded-xl font-medium hover:opacity-90 transition">
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