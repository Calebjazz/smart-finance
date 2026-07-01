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
        $description = trim($_POST['description'] ?? '');
        $category_id = (int) ($_POST['category_id'] ?? 0);
        $amount = (float) ($_POST['amount'] ?? 0);
        $expense_date = $_POST['expense_date'] ?? date('Y-m-d');
        if ($description && $category_id && $amount > 0) {
            $stmt = mysqli_prepare($conn, "INSERT INTO expenses (user_id, category_id, amount, description, expense_date) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'iidss', $user_id, $category_id, $amount, $description, $expense_date);
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Expense added successfully.';
                create_notification($conn, $user_id, 'Expense recorded', format_usd($amount) . ' - ' . $description);
            } else {
                $error = 'Failed to add expense.';
            }
        } else {
            $error = 'Please fill all required fields.';
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, "DELETE FROM expenses WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $id, $user_id);
        mysqli_stmt_execute($stmt);
        $message = 'Expense deleted.';
    }
}

$expenses = get_user_expenses($conn, $user_id);
$categories = get_expense_categories($conn);
$total_expenses = get_total_expenses($conn, $user_id);
$month_expenses = get_month_expenses($conn, $user_id);
$by_category = get_expenses_by_category($conn, $user_id);

$page_title = 'Expenses';
$active_page = 'expenses';
$dash_path = '';
$user_path = '../user/';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/user_sidebar.php';
include __DIR__ . '/../includes/user_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">Expense Management</h1>
        <p class="card-text">Track and categorize all your expenses</p>
    </div>

    <?php if ($message): ?><div class="alert-success rounded-xl p-4 mb-4"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert-error rounded-xl p-4 mb-4"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="mb-6">
        <button type="button" onclick="document.getElementById('addExpenseModal').classList.remove('hidden')" class="gradient-red text-white px-6 py-3 rounded-xl font-medium hover:opacity-90 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Add Expense
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Expenses</p><p class="text-2xl font-bold card-title"><?php echo format_usd($total_expenses); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Categories Used</p><p class="text-2xl font-bold card-title"><?php echo count($by_category); ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">This Month</p><p class="text-2xl font-bold card-title"><?php echo format_usd($month_expenses); ?></p></div>
    </div>

    <?php if (!empty($by_category)): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <?php foreach (array_slice($by_category, 0, 4) as $cat):
            $pct = $total_expenses > 0 ? round(((float)$cat['total'] / $total_expenses) * 100) : 0;
        ?>
        <div class="card rounded-xl p-4">
            <p class="font-medium card-title"><?php echo htmlspecialchars($cat['category_name']); ?></p>
            <p class="text-xl font-bold card-title"><?php echo format_usd((float)$cat['total']); ?></p>
            <div class="w-full bg-gray-300 rounded-full h-2 mt-2"><div class="bg-red-500 h-2 rounded-full" style="width:<?php echo $pct; ?>%"></div></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-4 card-title">Recent Expenses</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="text-left text-sm border-b table-head"><th class="pb-3">Description</th><th class="pb-3">Category</th><th class="pb-3">Amount</th><th class="pb-3">Date</th><th class="pb-3">Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($expenses)): ?>
                    <tr><td colspan="5" class="py-6 text-center card-text">No expenses yet.</td></tr>
                    <?php else: foreach ($expenses as $row): ?>
                    <tr class="border-b table-row">
                        <td class="py-4 card-title"><?php echo htmlspecialchars($row['description']); ?></td>
                        <td class="py-4"><span class="bg-red-500/20 text-red-600 dark:text-red-300 px-3 py-1 rounded-full text-sm"><?php echo htmlspecialchars($row['category_name']); ?></span></td>
                        <td class="py-4 font-medium text-red-500"><?php echo format_usd((float)$row['amount']); ?></td>
                        <td class="py-4 card-text"><?php echo date('M j, Y', strtotime($row['expense_date'])); ?></td>
                        <td class="py-4">
                            <form method="POST" class="inline" onsubmit="return confirm('Delete this expense?');">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
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

<div id="addExpenseModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="modal-panel rounded-2xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold card-title">Add Expense</h3>
            <button type="button" onclick="document.getElementById('addExpenseModal').classList.add('hidden')" class="card-text"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add">
            <div><label class="block text-sm mb-2 card-text">Description *</label><input type="text" name="description" required class="form-input w-full rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-500" placeholder="What did you spend on?"></div>
            <div><label class="block text-sm mb-2 card-text">Category *</label>
                <select name="category_id" required class="form-select w-full rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-500">
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?><option value="<?php echo (int)$cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div><label class="block text-sm mb-2 card-text">Amount (USD) *</label><input type="number" name="amount" step="0.01" min="0.01" required class="form-input w-full rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-500"></div>
            <div><label class="block text-sm mb-2 card-text">Date *</label><input type="date" name="expense_date" required value="<?php echo date('Y-m-d'); ?>" class="form-input w-full rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-500"></div>
            <button type="submit" class="w-full gradient-red text-white py-3 rounded-xl font-medium">Save Expense</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/layout_end.php'; ?>
