<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

$user_id = (int) $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['full_name'] ?? 'User');
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (strlen($new) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if ($row && password_verify($current, $row['password'])) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt2 = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt2, 'si', $hash, $user_id);
            mysqli_stmt_execute($stmt2);
            $message = 'Password updated successfully.';
        } else {
            $error = 'Current password is incorrect.';
        }
    }
}

$page_title = 'Settings';
$active_page = 'settings';
$dash_path = '../Dashboard/';
$user_path = '';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/user_sidebar.php';
include __DIR__ . '/../includes/user_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">Settings</h1>
        <p class="card-text">Manage your account settings</p>
    </div>

    <?php if ($message): ?><div class="alert-success rounded-xl p-4 mb-4"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert-error rounded-xl p-4 mb-4"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="card rounded-2xl p-6 mb-8">
        <h3 class="text-lg font-semibold mb-6 card-title">Change Password</h3>
        <form method="POST" class="space-y-4 max-w-lg">
            <input type="hidden" name="change_password" value="1">
            <div><label class="block text-sm mb-2 card-text">Current Password</label><input type="password" name="current_password" required class="form-input w-full rounded-xl px-4 py-3"></div>
            <div><label class="block text-sm mb-2 card-text">New Password</label><input type="password" name="new_password" required minlength="6" class="form-input w-full rounded-xl px-4 py-3"></div>
            <div><label class="block text-sm mb-2 card-text">Confirm Password</label><input type="password" name="confirm_password" required class="form-input w-full rounded-xl px-4 py-3"></div>
            <button type="submit" class="gradient-green text-white px-8 py-3 rounded-xl font-medium">Update Password</button>
        </form>
    </div>

    <div class="card rounded-2xl p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4 card-title">Preferences</h3>
        <p class="card-text text-sm mb-4">Currency is set to US Dollars (USD) across the system.</p>
        <p class="card-text text-sm">Use the theme toggle in the top bar to switch between light and dark mode. Your preference is saved automatically.</p>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold mb-4 card-title">Export Data</h3>
        <p class="card-text text-sm mb-4">Download your financial reports from the Reports page.</p>
        <a href="../Dashboard/Reports.php" class="inline-flex items-center gap-2 nav-btn px-4 py-2 rounded-xl text-sm"><i class="fas fa-chart-pie"></i> Go to Reports</a>
    </div>
</div>

<?php include __DIR__ . '/../includes/layout_end.php'; ?>
