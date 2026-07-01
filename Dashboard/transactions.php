<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

$user_id = (int) $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['full_name'] ?? 'User');
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $amount = (float) ($_POST['amount'] ?? 0);
        $type = $_POST['type'] ?? 'deposit';
        $reference = trim($_POST['reference'] ?? '');
        $transaction_date = $_POST['transaction_date'] ?? date('Y-m-d');
        $status = $_POST['status'] ?? 'active';
        if ($amount > 0 && $reference && in_array($type, ['deposit', 'withdrawal'])) {
            $stmt = mysqli_prepare($conn, "INSERT INTO savings_transactions (user_id, amount, status, type, REFERENCE, transaction_date) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'idssss', $user_id, $amount, $status, $type, $reference, $transaction_date);
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Transaction recorded.';
            } else {
                $error = 'Failed to save transaction.';
            }
        } else {
            $error = 'Please fill all required fields.';
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, "DELETE FROM savings_transactions WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $id, $user_id);
        mysqli_stmt_execute($stmt);
        $message = 'Transaction deleted.';
    }
}

$transactions = get_savings_transactions($conn, $user_id);
$all_tx = get_recent_transactions($conn, $user_id, 50);
$total_deposits = 0;
$total_withdrawals = 0;
foreach ($transactions as $t) {
    if ($t['type'] === 'deposit') $total_deposits += (float)$t['amount'];
    else $total_withdrawals += (float)$t['amount'];
}

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

    <div class="mb-6">
        <button type="button" onclick="document.getElementById('addTxModal').classList.remove('hidden')" class="gradient-blue text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2"><i class="fas fa-plus"></i> Add Savings Transaction</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Deposits</p><p class="text-2xl font-bold text-green-500"><?php echo format_usd($total_deposits); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Withdrawals</p><p class="text-2xl font-bold text-red-500"><?php echo format_usd($total_withdrawals); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">All Activity</p><p class="text-2xl font-bold card-title"><?php echo count($all_tx); ?></p></div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-4 card-title">All Transactions</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="text-left text-sm border-b table-head"><th class="pb-3">Description</th><th class="pb-3">Type</th><th class="pb-3">Category</th><th class="pb-3">Amount</th><th class="pb-3">Date</th></tr></thead>
                <tbody>
                    <?php if (empty($all_tx)): ?>
                    <tr><td colspan="5" class="py-6 text-center card-text">No transactions yet.</td></tr>
                    <?php else: foreach ($all_tx as $tx):
                        $isCredit = str_contains($tx['type'], 'income') || str_contains($tx['type'], 'deposit');
                    ?>
                    <tr class="border-b table-row">
                        <td class="py-4 card-title"><?php echo htmlspecialchars($tx['label']); ?></td>
                        <td class="py-4"><span class="px-2 py-1 rounded text-xs <?php echo $isCredit ? 'bg-green-500/20 text-green-600' : 'bg-red-500/20 text-red-600'; ?>"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $tx['type']))); ?></span></td>
                        <td class="py-4 card-text"><?php echo htmlspecialchars($tx['category_name'] ?? '-'); ?></td>
                        <td class="py-4 font-medium <?php echo $isCredit ? 'text-green-500' : 'text-red-500'; ?>"><?php echo ($isCredit ? '+' : '-') . format_usd((float)$tx['amount']); ?></td>
                        <td class="py-4 card-text"><?php echo date('M j, Y', strtotime($tx['tx_date'])); ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addTxModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-panel rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-6 card-title">Add Savings Transaction</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add">
            <div><label class="block text-sm mb-2 card-text">Type *</label>
                <select name="type" class="form-select w-full rounded-xl px-4 py-3"><option value="deposit">Deposit</option><option value="withdrawal">Withdrawal</option></select>
            </div>
            <div><label class="block text-sm mb-2 card-text">Reference *</label><input type="text" name="reference" required class="form-input w-full rounded-xl px-4 py-3" placeholder="Transaction reference"></div>
            <div><label class="block text-sm mb-2 card-text">Amount (USD) *</label><input type="number" name="amount" step="0.01" min="0.01" required class="form-input w-full rounded-xl px-4 py-3"></div>
            <div><label class="block text-sm mb-2 card-text">Status *</label>
                <select name="status" class="form-select w-full rounded-xl px-4 py-3"><option value="active">Active</option><option value="completed">Completed</option></select>
            </div>
            <div><label class="block text-sm mb-2 card-text">Date *</label><input type="date" name="transaction_date" required value="<?php echo date('Y-m-d'); ?>" class="form-input w-full rounded-xl px-4 py-3"></div>
            <button type="submit" class="w-full gradient-blue text-white py-3 rounded-xl font-medium">Save Transaction</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/layout_end.php'; ?>
