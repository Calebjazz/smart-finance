<?php
require_once __DIR__ . '/../includes/init.php';
require_admin();

$admin_name = htmlspecialchars($_SESSION['full_name'] ?? 'Admin');
$message = '';
$error = '';

// Ensure $conn is available (init.php may set it). Fall back to a local default
if (!isset($conn) || !$conn) {
    $conn = $GLOBALS['conn'] ?? mysqli_connect('localhost', 'root', '', 'smart_finance');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'toggle_status') {
        $id = (int) ($_POST['id'] ?? 0);
        $new_status = $_POST['status'] === 'active' ? 'blocked' : 'active';
        $stmt = mysqli_prepare($conn, "UPDATE users SET status = ? WHERE id = ? AND role != 'admin'");
        mysqli_stmt_bind_param($stmt, 'si', $new_status, $id);
        mysqli_stmt_execute($stmt);
        $message = 'User status updated.';
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ? AND role != 'admin'");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $message = 'User deleted.';
    }
}

$search = trim($_GET['q'] ?? '');
$sql = "SELECT id, full_name, email, phone, role, status, created_at FROM users WHERE role = 'user'";
if ($search) {
    $like = '%' . mysqli_real_escape_string($conn, $search) . '%';
    $sql .= " AND (full_name LIKE '$like' OR email LIKE '$like' OR phone LIKE '$like')";
}
$sql .= " ORDER BY created_at DESC";
$users = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);

$total = count($users);
$active = count(array_filter($users, fn($u) => $u['status'] === 'active'));
$blocked = $total - $active;

$page_title = 'Users Management';
$active_page = 'users';
$asset_path = '../assets';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/admin_sidebar.php';
include __DIR__ . '/../includes/admin_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">Users Management</h1>
        <p class="card-text">Manage registered users</p>
    </div>

    <?php if ($message): ?><div class="alert-success rounded-xl p-4 mb-4"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Users</p><p class="text-2xl font-bold card-title"><?php echo $total; ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Active</p><p class="text-2xl font-bold card-title"><?php echo $active; ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Blocked</p><p class="text-2xl font-bold card-title"><?php echo $blocked; ?></p></div>
    </div>

    <div class="card rounded-2xl p-4 mb-6">
        <form method="GET" class="flex gap-3">
            <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, email, phone..." class="form-input flex-1 rounded-xl px-4 py-2">
            <button type="submit" class="gradient-blue text-white px-6 py-2 rounded-xl">Search</button>
        </form>
    </div>

    <div class="card rounded-2xl p-6 overflow-x-auto">
        <table class="w-full">
            <thead><tr class="text-left text-sm border-b table-head"><th class="pb-3">User</th><th class="pb-3">Email</th><th class="pb-3">Phone</th><th class="pb-3">Status</th><th class="pb-3">Joined</th><th class="pb-3">Actions</th></tr></thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr><td colspan="6" class="py-6 text-center card-text">No users found.</td></tr>
                <?php else: foreach ($users as $u): ?>
                <tr class="border-b table-row">
                    <td class="py-4"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full avatar-fallback flex items-center justify-center text-white text-sm font-bold"><?php echo get_user_initials($u['full_name']); ?></div><span class="card-title"><?php echo htmlspecialchars($u['full_name']); ?></span></div></td>
                    <td class="py-4 card-text"><?php echo htmlspecialchars($u['email']); ?></td>
                    <td class="py-4 card-text"><?php echo htmlspecialchars($u['phone']); ?></td>
                    <td class="py-4"><span class="px-3 py-1 rounded-full text-sm <?php echo $u['status'] === 'active' ? 'bg-green-500/20 text-green-600' : 'bg-red-500/20 text-red-600'; ?>"><?php echo ucfirst($u['status']); ?></span></td>
                    <td class="py-4 card-text"><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                    <td class="py-4 flex gap-2">
                        <form method="POST"><input type="hidden" name="action" value="toggle_status"><input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>"><input type="hidden" name="status" value="<?php echo $u['status']; ?>"><button type="submit" class="text-blue-500 text-sm hover:underline"><?php echo $u['status'] === 'active' ? 'Block' : 'Activate'; ?></button></form>
                        <form method="POST" onsubmit="return confirm('Delete user?');"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>"><button type="submit" class="text-red-500"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/layout_end.php'; ?>
