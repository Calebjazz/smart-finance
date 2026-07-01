<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$admin_name = htmlspecialchars($_SESSION['full_name'] ?? 'Admin');
$logs = mysqli_fetch_all(mysqli_query($conn, "
    SELECT ac.*, u.full_name FROM ai_consultations ac
    LEFT JOIN users u ON u.id = ac.user_id
    ORDER BY ac.created_at DESC LIMIT 100
"), MYSQLI_ASSOC);

$page_title = 'AI Logs';
$active_page = 'ai_logs';
$asset_path = '../assets';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/admin_sidebar.php';
include __DIR__ . '/../includes/admin_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">AI Consultation Logs</h1>
        <p class="card-text"><?php echo count($logs); ?> consultation records</p>
    </div>

    <div class="card rounded-2xl p-6 overflow-x-auto">
        <table class="w-full">
            <thead><tr class="text-left text-sm border-b table-head"><th class="pb-3">User</th><th class="pb-3">Question</th><th class="pb-3">Response</th><th class="pb-3">Date</th></tr></thead>
            <tbody>
                <?php if (empty($logs)): ?>
                <tr><td colspan="4" class="py-6 text-center card-text">No AI logs yet.</td></tr>
                <?php else: foreach ($logs as $log): ?>
                <tr class="border-b table-row">
                    <td class="py-4 card-title"><?php echo htmlspecialchars($log['full_name'] ?? 'Unknown'); ?></td>
                    <td class="py-4 card-text max-w-xs truncate"><?php echo htmlspecialchars($log['user_question']); ?></td>
                    <td class="py-4 card-text max-w-md truncate"><?php echo htmlspecialchars(mb_substr($log['ai_response'] ?? '-', 0, 120)); ?></td>
                    <td class="py-4 card-text"><?php echo date('M j, Y H:i', strtotime($log['created_at'])); ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/layout_end.php'; ?>
