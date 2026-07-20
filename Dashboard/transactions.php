<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

global $conn;
if (!isset($conn)) {
    die('Database connection failed.');
}

$user_id = (int) $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['full_name'] ?? 'User');
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $source = $_POST['source'] ?? '';
        if ($source === 'income') {
            $stmt = mysqli_prepare($conn, "DELETE FROM incomes WHERE id = ? AND user_id = ?");
        } elseif ($source === 'expense') {
            $stmt = mysqli_prepare($conn, "DELETE FROM expenses WHERE id = ? AND user_id = ?");
        } else {
            $error = 'Invalid transaction source.';
        }
        if (isset($stmt)) {
            mysqli_stmt_bind_param($stmt, 'ii', $id, $user_id);
            mysqli_stmt_execute($stmt);
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $message = 'Transaction deleted.';
            } else {
                $error = 'Transaction not found or already deleted.';
            }
        }
    }
}

$all_tx = get_recent_transactions($conn, $user_id, 1000);
$total_deposits = get_total_income($conn, $user_id);
$total_withdrawals = get_total_expenses($conn, $user_id);

$page_title = 'Transactions';
$active_page = 'transactions';
$dash_path = '';
$user_path = '../user/';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/user_sidebar.php';
include __DIR__ . '/../includes/user_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">Transactions</h1>
        <p class="card-text">View all income, expense, and savings transactions</p>
    </div>

    <?php if ($message): ?><div class="alert-success rounded-xl p-4 mb-4"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert-error rounded-xl p-4 mb-4"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Deposits</p><p class="text-2xl font-bold text-green-500"><?php echo format_tsh($total_deposits); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Withdrawals</p><p class="text-2xl font-bold text-red-500"><?php echo format_tsh($total_withdrawals); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">All Activity</p><p class="text-2xl font-bold card-title"><?php echo count($all_tx); ?></p></div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-4 card-title">All Transactions</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="text-left text-sm border-b table-head"><th class="pb-3">Description</th><th class="pb-3">Type</th><th class="pb-3">Category</th><th class="pb-3">Amount</th><th class="pb-3">Date</th><th class="pb-3">Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($all_tx)): ?>
                    <tr><td colspan="6" class="py-6 text-center card-text">No transactions yet.</td></tr>
                    <?php else: foreach ($all_tx as $tx):
                        $isCredit = str_contains($tx['type'], 'income') || str_contains($tx['type'], 'deposit');
                        $source = str_contains($tx['type'], 'income') ? 'income' : 'expense';
                    ?>
                    <tr class="border-b table-row">
                        <td class="py-4 card-title"><?php echo htmlspecialchars($tx['label']); ?></td>
                        <td class="py-4"><span class="px-2 py-1 rounded text-xs <?php echo $isCredit ? 'bg-green-500/20 text-green-600' : 'bg-red-500/20 text-red-600'; ?>"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $tx['type']))); ?></span></td>
                        <td class="py-4 card-text"><?php echo htmlspecialchars($tx['category_name'] ?? '-'); ?></td>
                        <td class="py-4 font-medium <?php echo $isCredit ? 'text-green-500' : 'text-red-500'; ?>"><?php echo ($isCredit ? '+' : '-') . format_tsh((float)$tx['amount']); ?></td>
                        <td class="py-4 card-text"><?php echo date('M j, Y', strtotime($tx['tx_date'])); ?></td>
                        <td class="py-4">
                            <form method="POST" class="inline" onsubmit="return confirm('Delete this transaction?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo (int)$tx['source_id']; ?>">
                                <input type="hidden" name="source" value="<?php echo $source; ?>">
                                <button type="submit" class="text-red-500 hover:text-red-400"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


