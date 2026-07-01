<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

$user_id = (int) $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['full_name'] ?? 'User');
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_goal') {
        $goal_name = trim($_POST['goal_name'] ?? '');
        $target_amount = (float) ($_POST['target_amount'] ?? 0);
        $target_date = $_POST['target_date'] ?: null;
        if ($goal_name && $target_amount > 0) {
            if ($target_date) {
                $stmt = mysqli_prepare($conn, "INSERT INTO savings_goals (user_id, goal_name, target_amount, target_date) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, 'isds', $user_id, $goal_name, $target_amount, $target_date);
            } else {
                $stmt = mysqli_prepare($conn, "INSERT INTO savings_goals (user_id, goal_name, target_amount) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, 'isd', $user_id, $goal_name, $target_amount);
            }
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Savings goal created.';
            } else {
                $error = 'Failed to create goal.';
            }
        } else {
            $error = 'Please fill required fields.';
        }
    } elseif ($action === 'add_deposit') {
        $goal_id = (int) ($_POST['goal_id'] ?? 0);
        $amount = (float) ($_POST['amount'] ?? 0);
        $reference = trim($_POST['reference'] ?? 'Manual deposit');
        if ($goal_id && $amount > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE savings_goals SET current_amount = current_amount + ? WHERE id = ? AND user_id = ?");
            mysqli_stmt_bind_param($stmt, 'dii', $amount, $goal_id, $user_id);
            mysqli_stmt_execute($stmt);
            $stmt2 = mysqli_prepare($conn, "INSERT INTO savings_transactions (user_id, amount, status, type, REFERENCE, transaction_date) VALUES (?, ?, 'active', 'deposit', ?, CURDATE())");
            mysqli_stmt_bind_param($stmt2, 'ids', $user_id, $amount, $reference);
            mysqli_stmt_execute($stmt2);
            $message = 'Deposit added to goal.';
        }
    } elseif ($action === 'delete_goal') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, "DELETE FROM savings_goals WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $id, $user_id);
        mysqli_stmt_execute($stmt);
        $message = 'Goal deleted.';
    }
}

$goals = get_user_savings_goals($conn, $user_id);
$total_saved = get_total_savings($conn, $user_id);
$total_target = array_sum(array_column($goals, 'target_amount'));
$completed = count(array_filter($goals, fn($g) => $g['status'] === 'completed' || (float)$g['current_amount'] >= (float)$g['target_amount']));

$page_title = 'Savings & Goals';
$active_page = 'savings';
$dash_path = '';
$user_path = '../user/';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/user_sidebar.php';
include __DIR__ . '/../includes/user_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">Savings & Goals</h1>
        <p class="card-text">Set goals and track your savings progress</p>
    </div>

    <?php if ($message): ?><div class="alert-success rounded-xl p-4 mb-4"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert-error rounded-xl p-4 mb-4"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="flex flex-wrap gap-3 mb-6">
        <button type="button" onclick="document.getElementById('addGoalModal').classList.remove('hidden')" class="gradient-purple text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2"><i class="fas fa-plus"></i> New Goal</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Saved</p><p class="text-2xl font-bold card-title"><?php echo format_usd($total_saved); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Target Total</p><p class="text-2xl font-bold card-title"><?php echo format_usd($total_target); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Goals Completed</p><p class="text-2xl font-bold card-title"><?php echo $completed; ?></p></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php if (empty($goals)): ?>
        <div class="card rounded-2xl p-8 col-span-2 text-center card-text">No savings goals yet. Create your first goal!</div>
        <?php else: foreach ($goals as $goal):
            $pct = (float)$goal['target_amount'] > 0 ? min(100, round(((float)$goal['current_amount'] / (float)$goal['target_amount']) * 100)) : 0;
        ?>
        <div class="card rounded-2xl p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold card-title"><?php echo htmlspecialchars($goal['goal_name']); ?></h3>
                <form method="POST" onsubmit="return confirm('Delete this goal?');"><input type="hidden" name="action" value="delete_goal"><input type="hidden" name="id" value="<?php echo (int)$goal['id']; ?>"><button type="submit" class="text-red-500 text-sm"><i class="fas fa-trash"></i></button></form>
            </div>
            <p class="text-sm card-text mb-2"><?php echo format_usd((float)$goal['current_amount']); ?> of <?php echo format_usd((float)$goal['target_amount']); ?></p>
            <div class="w-full bg-gray-300 rounded-full h-3 mb-4"><div class="bg-purple-500 h-3 rounded-full" style="width:<?php echo $pct; ?>%"></div></div>
            <?php if ($goal['target_date']): ?><p class="text-xs card-text mb-4">Target: <?php echo date('M j, Y', strtotime($goal['target_date'])); ?></p><?php endif; ?>
            <form method="POST" class="flex gap-2">
                <input type="hidden" name="action" value="add_deposit"><input type="hidden" name="goal_id" value="<?php echo (int)$goal['id']; ?>">
                <input type="number" name="amount" step="0.01" min="0.01" placeholder="Amount" required class="form-input flex-1 rounded-lg px-3 py-2 text-sm">
                <input type="hidden" name="reference" value="Goal deposit">
                <button type="submit" class="gradient-green text-white px-4 py-2 rounded-lg text-sm">Add</button>
            </form>
        </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<div id="addGoalModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-panel rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-6 card-title">New Savings Goal</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add_goal">
            <div><label class="block text-sm mb-2 card-text">Goal Name *</label><input type="text" name="goal_name" required class="form-input w-full rounded-xl px-4 py-3" placeholder="Emergency Fund"></div>
            <div><label class="block text-sm mb-2 card-text">Target Amount (USD) *</label><input type="number" name="target_amount" step="0.01" min="0.01" required class="form-input w-full rounded-xl px-4 py-3"></div>
            <div><label class="block text-sm mb-2 card-text">Target Date</label><input type="date" name="target_date" class="form-input w-full rounded-xl px-4 py-3"></div>
            <button type="submit" class="w-full gradient-purple text-white py-3 rounded-xl font-medium">Create Goal</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/layout_end.php'; ?>
