<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$admin_name = htmlspecialchars($_SESSION['full_name'] ?? 'Admin');
$logs = mysqli_fetch_all(mysqli_query($conn, "
    SELECT al.*, u.full_name FROM automation_logs al
    LEFT JOIN users u ON u.id = al.user_id
    ORDER BY al.created_at DESC LIMIT 100
"), MYSQLI_ASSOC);

$page_title = 'Automation Logs';
$active_page = 'automation';
$asset_path = '../assets';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/admin_sidebar.php';
include __DIR__ . '/../includes/admin_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">Automation Logs</h1>
        <p class="card-text">n8n and system automation events</p>
    </div>

    <div class="card rounded-2xl p-6">
        <div class="space-y-3">
            <?php if (empty($logs)): ?>
            <p class="card-text">No automation logs yet. Connect n8n to <code class="text-sm">api/n8n_webhook.php</code></p>
            <?php else: foreach ($logs as $log): ?>
            <div class="flex items-start gap-4 p-4 sub-card rounded-xl">
                <div class="w-10 h-10 rounded-full flex items-center justify-center <?php echo $log['status'] === 'success' ? 'bg-green-500/20' : 'bg-red-500/20'; ?>">
                    <i class="fas fa-cog <?php echo $log['status'] === 'success' ? 'text-green-500' : 'text-red-500'; ?>"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium card-title"><?php echo htmlspecialchars($log['event_type']); ?></p>
                    <p class="text-sm card-text"><?php echo htmlspecialchars($log['message']); ?></p>
                    <p class="text-xs card-text mt-1"><?php echo htmlspecialchars($log['full_name'] ?? 'System'); ?> · <?php echo time_ago($log['created_at']); ?></p>
                </div>
                <span class="text-xs px-2 py-1 rounded <?php echo $log['status'] === 'success' ? 'bg-green-500/20 text-green-600' : 'bg-red-500/20 text-red-600'; ?>"><?php echo $log['status']; ?></span>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/layout_end.php'; ?>
