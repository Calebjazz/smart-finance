<?php
/** @var string $admin_name */
/** @var int $user_id - admin id optional */
$initials = get_user_initials($admin_name);
?>
<main class="flex-1 ml-64 transition-all duration-300" id="main-content">
    <nav class="sticky top-0 z-40 top-navbar">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button id="sidebar-toggle" type="button" class="nav-btn w-10 h-10 rounded-full flex items-center justify-center transition">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-shield-alt logo-pocket text-lg"></i>
                        <span class="font-semibold card-title">Smart <span class="logo-finance">Finance</span> Admin</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button id="theme-toggle" type="button" class="nav-btn w-10 h-10 rounded-full flex items-center justify-center transition">
                        <i class="fas fa-moon" id="theme-icon"></i>
                    </button>
                    <div class="flex items-center gap-3 pl-3 border-l nav-divider">
                        <div class="text-right hidden md:block">
                            <p class="font-medium text-sm card-title"><?php echo htmlspecialchars($admin_name); ?></p>
                            <p class="text-xs card-text">Administrator</p>
                        </div>
                        <div class="w-10 h-10 rounded-full avatar-fallback flex items-center justify-center text-white font-bold text-sm"><?php echo htmlspecialchars($initials); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
