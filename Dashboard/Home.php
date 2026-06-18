<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../components/auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .glassmorphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="bg-slate-950 min-h-screen">

//navbar
<nav class="w-full bg-slate-900/80 backdrop-blur-md border-b border-slate-800 sticky top-0 z-50">

    <div class="max-w-7xl mx-auto px-6 py-4">

        <div class="flex items-center justify-between">

            <!-- Logo -->
            <div class="flex items-center gap-8">
                <h1 class="text-2xl font-bold text-white">
                    Smart<span class="text-blue-400">Finance</span>
                </h1>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="dashboard.php" class="text-white font-medium">Dashboard</a>
                    <a href="Income.php" class="text-gray-400 hover:text-white transition">Income</a>
                    <a href="Expenses.php" class="text-gray-400 hover:text-white transition">Expenses</a>
                    <a href="Budget.php" class="text-gray-400 hover:text-white transition">Budget</a>
                    <a href="Saving&Goals.php" class="text-gray-400 hover:text-white transition">Savings</a>
                </div>
            </div>

            <!-- User Menu -->
            <div class="flex items-center gap-4">
                <span class="text-gray-300 hidden md:block">Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></span>
                <a href="../components/auth/logout.php" class="text-red-400 hover:text-red-300 transition">Logout</a>
            </div>

        </div>

    </div>

</nav>
