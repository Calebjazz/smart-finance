<?php
/** @var string $active_page */
/** @var string $dash_path */
/** @var string $user_path */
$nav = [
    'home' => ['file' => 'Home.php', 'icon' => 'fa-home', 'label' => 'Dashboard', 'color' => ''],
    'income' => ['file' => 'Income.php', 'icon' => 'fa-arrow-trend-up', 'label' => 'Income', 'color' => 'text-green-400'],
    'expenses' => ['file' => 'Expenses.php', 'icon' => 'fa-arrow-trend-down', 'label' => 'Expenses', 'color' => 'text-red-400'],
    'budget' => ['file' => 'Budget.php', 'icon' => 'fa-piggy-bank', 'label' => 'Budget', 'color' => 'text-yellow-400'],
    'transactions' => ['file' => 'transactions.php', 'icon' => 'fa-exchange-alt', 'label' => 'Transactions', 'color' => 'text-blue-400'],
    'reports' => ['file' => 'Reports.php', 'icon' => 'fa-chart-pie', 'label' => 'Reports', 'color' => 'text-cyan-400'],
    'profile' => ['file' => 'profile.php', 'icon' => 'fa-user', 'label' => 'Profile', 'color' => 'text-pink-400', 'path' => $user_path],
    'settings' => ['file' => 'settings.php', 'icon' => 'fa-cog', 'label' => 'Settings', 'color' => 'text-gray-400', 'path' => $user_path],
];
?>
<aside id="sidebar" class="sidebar fixed left-0 top-0 h-full w-64 z-50">
    <div class="flex flex-col h-full">
        <div class="p-6 border-b border-slate-700">
            <h1 class="text-xl font-bold flex items-center gap-2">
                <i class="fas fa-wallet logo-pocket"></i>
                <span class="logo-smart">Smart</span><span class="logo-finance">Finance</span>
            </h1>
        </div>
        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1">
                <?php foreach ($nav as $key => $item):
                    $href = ($item['path'] ?? $dash_path) . $item['file'];
                    $isActive = ($active_page === $key);
                    $linkClass = 'sidebar-item flex items-center gap-3 px-6 py-3 ' . ($isActive ? 'active text-white' : 'text-gray-300 hover:text-white');
                ?>
                <li>
                    <a href="<?php echo htmlspecialchars($href); ?>" class="<?php echo $linkClass; ?>">
                        <i class="fas <?php echo $item['icon']; ?> w-5 <?php echo $item['color']; ?>"></i>
                        <span><?php echo $item['label']; ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
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
