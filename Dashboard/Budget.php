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
        $category_id = (int) ($_POST['category_id'] ?? 0);
        $budget_amount = (float) ($_POST['budget_amount'] ?? 0);
        $month = trim($_POST['month'] ?? date('F'));
        $year = (int) ($_POST['year'] ?? date('Y'));
        if ($category_id && $budget_amount > 0) {
            $stmt = mysqli_prepare($conn, "INSERT INTO budgets (user_id, category_id, budget_amount, month, year) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'iidsi', $user_id, $category_id, $budget_amount, $month, $year);
            if (mysqli_stmt_execute($stmt)) {
                $budget_id = mysqli_insert_id($conn);
                $stmt2 = mysqli_prepare($conn, "INSERT INTO budget_items (budget_id, category_id, allocated_amount, spent_amount) VALUES (?, ?, ?, 0)");
                mysqli_stmt_bind_param($stmt2, 'iid', $budget_id, $category_id, $budget_amount);
                mysqli_stmt_execute($stmt2);
                $message = 'Budget created successfully.';
            } else {
                $error = 'Failed to create budget.';
            }
        } else {
            $error = 'Please fill all required fields.';
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        mysqli_query($conn, "DELETE bi FROM budget_items bi JOIN budgets b ON b.id = bi.budget_id WHERE b.id = $id AND b.user_id = $user_id");
        $stmt = mysqli_prepare($conn, "DELETE FROM budgets WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $id, $user_id);
        mysqli_stmt_execute($stmt);
        $message = 'Budget deleted.';
    }
}

$budgets = get_user_budgets($conn, $user_id);
$categories = get_expense_categories($conn);
$total_budget = array_sum(array_column($budgets, 'budget_amount'));
$month_expenses = get_month_expenses($conn, $user_id);

$page_title = 'Budget';
$active_page = 'budget';
$dash_path = '';
$user_path = '../user/';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/user_sidebar.php';
include __DIR__ . '/../includes/user_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">Budget Management</h1>
        <p class="card-text">Plan and track your monthly spending limits</p>
    </div>

    <?php if ($message): ?><div class="alert-success rounded-xl p-4 mb-4"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert-error rounded-xl p-4 mb-4"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="mb-6">
        <button type="button" onclick="document.getElementById('addBudgetModal').classList.remove('hidden')" class="gradient-blue
         text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2">
            <i class="fas fa-plus"></i> Create Budget
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Budget</p><p class="text-2xl font-bold card-title"><?php echo format_usd($total_budget); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Spent This Month</p><p class="text-2xl font-bold card-title"><?php echo format_usd($month_expenses); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Remaining</p><p class="text-2xl font-bold card-title"><?php echo format_usd(max(0, $total_budget - $month_expenses)); ?></p></div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-4 card-title">Your Budgets</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="text-left text-sm border-b table-head"><th class="pb-3">Category</th><th class="pb-3">Amount</th><th class="pb-3">Month</th><th class="pb-3">Year</th><th class="pb-3">Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($budgets)): ?>
                    <tr><td colspan="5" class="py-6 text-center card-text">No budgets yet. Create one to start tracking.</td></tr>
                    <?php else: foreach ($budgets as $b): ?>
                    <tr class="border-b table-row">
                        <td class="py-4 card-title"><?php echo htmlspecialchars($b['category_name']); ?></td>
                        <td class="py-4 font-medium"><?php echo format_usd((float)$b['budget_amount']); ?></td>
                        <td class="py-4 card-text"><?php echo htmlspecialchars($b['month']); ?></td>
                        <td class="py-4 card-text"><?php echo htmlspecialchars($b['year']); ?></td>
                        <td class="py-4">
                            <form method="POST" class="inline" onsubmit="return confirm('Delete this budget?');">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>">
                                <button type="submit" class="text-red-500"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addBudgetModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="modal-panel rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-6 card-title">Create Budget</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add">
            <div><label class="block text-sm mb-2 card-text">Category *</label>
                <select name="category_id" required class="form-select w-full rounded-xl px-4 py-3">
                    <?php foreach ($categories as $cat): ?><option value="<?php echo (int)$cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div><label class="block text-sm mb-2 card-text">Budget Amount (USD) *</label><input type="number" name="budget_amount" step="0.01" min="0.01" required class="form-input w-full rounded-xl px-4 py-3"></div>
            <div><label class="block text-sm mb-2 card-text">Month *</label><input type="text" name="month" required value="<?php echo date('F'); ?>" class="form-input w-full rounded-xl px-4 py-3"></div>
            <div><label class="block text-sm mb-2 card-text">Year *</label><input type="number" name="year" required value="<?php echo date('Y'); ?>" class="form-input w-full rounded-xl px-4 py-3"></div>
            <button type="submit" class="w-full gradient-yellow text-white py-3 rounded-xl font-medium">Save Budget</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/layout_end.php'; ?>
