<?php
require_once __DIR__ . '/../includes/init.php';
require_login();
$conn = $GLOBALS['conn'] ?? null; // ensure $conn is defined to avoid undefined variable notices

$user_id = (int) $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['full_name'] ?? 'User');
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        $category_id = (int) ($_POST['category_id'] ?? 0);
        $amount = (float) ($_POST['amount'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $income_date = $_POST['income_date'] ?? date('Y-m-d');
        if ($title && $category_id && $amount > 0) {
            $stmt = mysqli_prepare($conn, "INSERT INTO incomes (user_id, category_id, amount, description, title, income_date) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'iidsss', $user_id, $category_id, $amount, $description, $title, $income_date);
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Income added successfully.';
                create_notification($conn, $user_id, 'Income recorded', format_tsh($amount) . ' from ' . $title);
            } else {
                $error = 'Failed to add income.';
            }
        } else {
            $error = 'Please fill all required fields.';
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, "DELETE FROM incomes WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $id, $user_id);
        mysqli_stmt_execute($stmt);
        $message = 'Income deleted.';
    }
}

$incomes = get_user_incomes($conn, $user_id);
$categories = get_income_categories($conn);
$total_income = get_total_income($conn, $user_id);
$month_income = get_month_income($conn, $user_id);
$active_sources = count(array_unique(array_column($incomes, 'title')));

$page_title = 'Income';
$active_page = 'income';
$dash_path = '';
$user_path = '../user/';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/user_sidebar.php';
include __DIR__ . '/../includes/user_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">Income Management</h1>
        <p class="card-text">Track and manage all your income sources</p>
    </div>

    <?php if ($message): ?><div class="alert-success rounded-xl p-4 mb-4"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert-error rounded-xl p-4 mb-4"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="mb-6">
        <button type="button" onclick="document.getElementById('addIncomeModal').classList.remove('hidden')" class="gradient-blue text-white px-6 py-3 rounded-xl font-medium hover:opacity-90 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Add Income
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card rounded-2xl p-6">
            <p class="text-sm card-text">Total Income</p>
            <p class="text-2xl font-bold card-title"><?php echo format_tsh($total_income); ?></p>
        </div>
        <div class="card rounded-2xl p-6">
            <p class="text-sm card-text">Active Sources</p>
            <p class="text-2xl font-bold card-title"><?php echo $active_sources; ?></p>
        </div>
        <div class="card rounded-2xl p-6">
            <p class="text-sm card-text">This Month</p>
            <p class="text-2xl font-bold card-title"><?php echo format_tsh($month_income); ?></p>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-4 card-title">Income Records</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm border-b table-head">
                        <th class="pb-3">Title</th><th class="pb-3">Category</th><th class="pb-3">Amount</th><th class="pb-3">Date</th><th class="pb-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($incomes)): ?>
                    <tr><td colspan="5" class="py-6 text-center card-text">No income records yet.</td></tr>
                    <?php else: foreach ($incomes as $row): ?>
                    <tr class="border-b table-row">
                        <td class="py-4 card-title"><?php echo htmlspecialchars($row['title']); ?></td>
                        <td class="py-4"><span class="bg-blue-500/20 text-blue-600 dark:text-blue-300 px-3 py-1 rounded-full text-sm"><?php echo htmlspecialchars($row['category_name']); ?></span></td>
                        <td class="py-4 font-medium text-green-500"><?php echo format_tsh((float)$row['amount']); ?></td>
                        <td class="py-4 card-text"><?php echo date('M j, Y', strtotime($row['income_date'])); ?></td>
                        <td class="py-4">
                            <form method="POST" class="inline" onsubmit="return confirm('Delete this income?');">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
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

<div id="addIncomeModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="modal-panel rounded-2xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold card-title">Add Income</h3>
            <button type="button" onclick="document.getElementById('addIncomeModal').classList.add('hidden')" class="card-text hover:opacity-70"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add">
            <div>
                <label class="block text-sm mb-2 card-text">Title *</label>
                <input type="text" name="title" required class="form-input w-full rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="e.g. Salary">
            </div>
            <div>
                <label class="block text-sm mb-2 card-text">Category *</label>
                <select name="category_id" required class="form-select w-full rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo (int)$cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2 card-text">Amount (Tsh) *</label>
                <input type="number" name="amount" step="0.01" min="0.01" required class="form-input w-full rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="0.00">
            </div>
            <div>
                <label class="block text-sm mb-2 card-text">Date *</label>
                <input type="date" name="income_date" required value="<?php echo date('Y-m-d'); ?>" class="form-input w-full rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm mb-2 card-text">Description</label>
                <textarea name="description" rows="2" class="form-textarea w-full rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Optional notes"></textarea>
            </div>
            <button type="submit" class="w-full gradient-green text-white py-3 rounded-xl font-medium">Save Income</button>
        </form>
    </div>
</div>


