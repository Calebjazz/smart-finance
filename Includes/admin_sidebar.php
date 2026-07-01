<?php
/** @var string $active_page */
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
                <?php
                $adminNav = [
                    'dashboard' => ['admindashboard.php', 'fa-tachometer-alt', 'Dashboard', ''],
                    'users' => ['users.php', 'fa-users', 'Users', 'text-blue-400'],
                    'categories' => ['categories.php', 'fa-tags', 'Categories', 'text-green-400'],
                    'ai_logs' => ['Ai_logs.php', 'fa-robot', 'AI Logs', 'text-purple-400'],
                    'automation' => ['Automation_logs.php', 'fa-cogs', 'Automation Logs', 'text-yellow-400'],
                ];
                foreach ($adminNav as $key => $item):
                    $cls = ($active_page === $key) ? 'sidebar-item active flex items-center gap-3 px-6 py-3 text-white' : 'sidebar-item flex items-center gap-3 px-6 py-3 text-gray-300 hover:text-white';
                ?>
                <li>
                    <a href="<?php echo $item[0]; ?>" class="<?php echo $cls; ?>">
                        <i class="fas <?php echo $item[1]; ?> w-5 <?php echo $item[3]; ?>"></i>
                        <span><?php echo $item[2]; ?></span>
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
