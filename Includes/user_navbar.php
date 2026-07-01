<?php
/** @var string $user_name */
/** @var int $user_id */
/** @var mysqli $conn */
/** @var string $asset_path */
/** @var string $role_label */
$notif_count = get_notification_count($conn, $user_id);
$notifications = get_notifications($conn, $user_id, 8);
$avatar = get_avatar_path($user_id);
$initials = get_user_initials($user_name);
$role_label = $role_label ?? 'User';
?>
<main class="flex-1 ml-64 transition-all duration-300" id="main-content">
    <nav class="sticky top-0 z-40 top-navbar">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button id="sidebar-toggle" type="button" class="nav-btn w-10 h-10 rounded-full flex items-center justify-center transition">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="hidden md:flex items-center gap-2">
                        <i class="fas fa-wallet logo-pocket text-lg"></i>
                        <span class="font-semibold logo-smart-text"><span class="logo-smart">Smart</span> <span class="logo-finance">Finance</span></span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button id="theme-toggle" type="button" class="nav-btn w-10 h-10 rounded-full flex items-center justify-center transition" aria-label="Toggle theme">
                        <i class="fas fa-moon" id="theme-icon"></i>
                    </button>
                    <div class="relative">
                        <button id="notif-toggle" type="button" class="nav-btn relative w-10 h-10 rounded-full flex items-center justify-center transition">
                            <i class="fas fa-bell"></i>
                            <?php if ($notif_count > 0): ?>
                            <span class="absolute -top-1 -right-1 min-w-5 h-5 px-1 bg-red-500 rounded-full text-xs text-white flex items-center justify-center"><?php echo $notif_count > 9 ? '9+' : $notif_count; ?></span>
                            <?php endif; ?>
                        </button>
                        <div id="notif-panel" class="notification-dropdown hidden absolute right-0 mt-2 w-80 rounded-xl shadow-xl z-50 overflow-hidden">
                            <div class="p-3 border-b font-semibold card-title" style="border-color:var(--sf-card-border)">Notifications</div>
                            <div class="max-h-72 overflow-y-auto">
                                <?php if (empty($notifications)): ?>
                                <p class="p-4 text-sm card-text">No notifications yet.</p>
                                <?php else: foreach ($notifications as $n): ?>
                                <div class="p-3 border-b text-sm" style="border-color:var(--sf-table-border)">
                                    <p class="font-medium card-title"><?php echo htmlspecialchars($n['title']); ?></p>
                                    <p class="card-text text-xs mt-1"><?php echo htmlspecialchars($n['message']); ?></p>
                                    <p class="text-xs mt-1 opacity-60"><?php echo time_ago($n['created_at']); ?></p>
                                </div>
                                <?php endforeach; endif; ?>
                            </div>
                            <?php if ($notif_count > 0): ?>
                            <form action="../api/notifications.php" method="POST" class="p-2 border-t" style="border-color:var(--sf-card-border)">
                                <input type="hidden" name="action" value="mark_read">
                                <button type="submit" class="w-full text-sm text-center py-2 text-blue-500 hover:underline">Mark all as read</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pl-3 border-l nav-divider">
                        <div class="text-right hidden md:block">
                            <p class="font-medium text-sm card-title"><?php echo htmlspecialchars($user_name); ?></p>
                            <p class="text-xs card-text"><?php echo htmlspecialchars($role_label); ?></p>
                        </div>
                        <?php if ($avatar): ?>
                        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-green-500/50">
                        <?php else: ?>
                        <div class="w-10 h-10 rounded-full avatar-fallback flex items-center justify-center text-white font-bold text-sm"><?php echo htmlspecialchars($initials); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
