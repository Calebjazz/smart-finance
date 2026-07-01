<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$admin_name = htmlspecialchars($_SESSION['full_name'] ?? 'Admin');
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $type = $_POST['type'] ?? 'expense';
    $table = $type === 'income' ? 'income_categories' : 'expense_categories';

    if ($action === 'add') {
        $name = trim($_POST['category_name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($name) {
            $stmt = mysqli_prepare($conn, "INSERT INTO $table (category_name, description) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, 'ss', $name, $desc);
            mysqli_stmt_execute($stmt);
            $message = 'Category added.';
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        mysqli_query($conn, "DELETE FROM $table WHERE id = $id");
        $message = 'Category deleted.';
    }
}

$income_cats = get_income_categories($conn);
$expense_cats = get_expense_categories($conn);

$page_title = 'Categories';
$active_page = 'categories';
$asset_path = '../assets';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/admin_sidebar.php';
include __DIR__ . '/../includes/admin_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">Categories Management</h1>
        <p class="card-text">Manage income and expense categories</p>
    </div>

    <?php if ($message): ?><div class="alert-success rounded-xl p-4 mb-4"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold mb-4 card-title">Income Categories (<?php echo count($income_cats); ?>)</h3>
            <form method="POST" class="flex gap-2 mb-4">
                <input type="hidden" name="action" value="add"><input type="hidden" name="type" value="income">
                <input type="text" name="category_name" required placeholder="Category name" class="form-input flex-1 rounded-lg px-3 py-2 text-sm">
                <button type="submit" class="gradient-green text-white px-4 py-2 rounded-lg text-sm">Add</button>
            </form>
            <ul class="space-y-2">
                <?php foreach ($income_cats as $c): ?>
                <li class="flex justify-between items-center sub-card rounded-lg px-3 py-2">
                    <span class="card-title"><?php echo htmlspecialchars($c['category_name']); ?></span>
                    <form method="POST" onsubmit="return confirm('Delete?');"><input type="hidden" name="action" value="delete"><input type="hidden" name="type" value="income"><input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>"><button type="submit" class="text-red-500 text-sm"><i class="fas fa-trash"></i></button></form>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold mb-4 card-title">Expense Categories (<?php echo count($expense_cats); ?>)</h3>
            <form method="POST" class="flex gap-2 mb-4">
                <input type="hidden" name="action" value="add"><input type="hidden" name="type" value="expense">
                <input type="text" name="category_name" required placeholder="Category name" class="form-input flex-1 rounded-lg px-3 py-2 text-sm">
                <button type="submit" class="gradient-green text-white px-4 py-2 rounded-lg text-sm">Add</button>
            </form>
            <ul class="space-y-2">
                <?php foreach ($expense_cats as $c): ?>
                <li class="flex justify-between items-center sub-card rounded-lg px-3 py-2">
                    <span class="card-title"><?php echo htmlspecialchars($c['category_name']); ?></span>
                    <form method="POST" onsubmit="return confirm('Delete?');"><input type="hidden" name="action" value="delete"><input type="hidden" name="type" value="expense"><input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>"><button type="submit" class="text-red-500 text-sm"><i class="fas fa-trash"></i></button></form>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/layout_end.php'; ?>
