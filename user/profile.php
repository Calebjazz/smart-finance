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
    <title>Profile - Smart Finance</title>
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

        .gradient-pink {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
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
                        <a href="profile.php" class="sidebar-item active flex items-center gap-3 px-6 py-3 text-white">
                            <i class="fas fa-user w-5 text-pink-400"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="settings.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white">
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

        <!-- Profile Content -->
        <div class="p-6">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">My Profile</h1>
                <p class="text-gray-400">Manage your personal information and preferences</p>
            </div>

            <!-- Profile Header -->
            <div class="card rounded-2xl p-8 mb-8">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="relative">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-4xl font-bold">
                            <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                        </div>
                        <button onclick="document.getElementById('profilePictureInput').click()" class="absolute bottom-0 right-0 w-10 h-10 bg-pink-500 rounded-full flex items-center justify-center text-white hover:bg-pink-600 transition">
                            <i class="fas fa-camera"></i>
                        </button>
                        <input type="file" id="profilePictureInput" class="hidden" accept="image/*">
                    </div>
                    <div class="text-center md:text-left">
                        <h2 class="text-2xl font-bold text-white mb-2"><?php echo $user_name; ?></h2>
                        <p class="text-gray-400 mb-4"><?php echo $user_email; ?></p>
                        <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                            <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">Active Member</span>
                            <span class="bg-indigo-500/20 text-indigo-400 px-3 py-1 rounded-full text-sm">Since 2024</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Information Form -->
            <div class="card rounded-2xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-6">Personal Information</h3>
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">Full Name</label>
                            <input type="text" value="<?php echo $user_name; ?>" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">Email Address</label>
                            <input type="email" value="<?php echo $user_email; ?>" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">Phone Number</label>
                            <input type="tel" placeholder="+255 XXX XXX XXX" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">Date of Birth</label>
                            <input type="date" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-300 text-sm mb-2">Address</label>
                            <input type="text" placeholder="Enter your address" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">City</label>
                            <input type="text" placeholder="City" class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">Country</label>
                            <select class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-pink-500">
                                <option>Tanzania</option>
                                <option>Kenya</option>
                                <option>Uganda</option>
                                <option>Rwanda</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="gradient-pink text-white px-8 py-3 rounded-xl font-medium hover:opacity-90 transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Account Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-check text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Member Since</p>
                            <p class="text-xl font-bold text-white">Jan 2024</p>
                        </div>
                    </div>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Transactions</p>
                            <p class="text-xl font-bold text-white">248</p>
                        </div>
                    </div>
                </div>

                <div class="card rounded-2xl p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-trophy text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Goals Achieved</p>
                            <p class="text-xl font-bold text-white">2</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Linked Accounts -->
            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-6">Linked Mobile Payment Accounts</h3>
                <div class="space-y-4">
                    <div class="bg-slate-800/50 rounded-xl p-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center">
                                <i class="fas fa-mobile-alt text-green-400 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">M-Pesa</p>
                                <p class="text-gray-400 text-sm">+255 712 345 678</p>
                            </div>
                        </div>
                        <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">Connected</span>
                    </div>
                    <div class="bg-slate-800/50 rounded-xl p-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-red-500/20 flex items-center justify-center">
                                <i class="fas fa-mobile-alt text-red-400 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-white font-medium">Airtel Money</p>
                                <p class="text-gray-400 text-sm">+255 756 789 012</p>
                            </div>
                        </div>
                        <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">Connected</span>
                    </div>
                    <button class="w-full border-2 border-dashed border-slate-600 rounded-xl p-4 text-gray-400 hover:text-white hover:border-slate-500 transition flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span>Link New Account</span>
                    </button>
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

    // Profile Picture Upload
    const profilePictureInput = document.getElementById('profilePictureInput');
    profilePictureInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const avatar = document.querySelector('.w-32.h-32');
                avatar.style.backgroundImage = `url(${event.target.result})`;
                avatar.style.backgroundSize = 'cover';
                avatar.style.backgroundPosition = 'center';
                avatar.textContent = '';
            };
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>