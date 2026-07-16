<?php
require_once __DIR__ . '/../includes/init.php';
/** @var mysqli $conn */
if (!isset($conn)) {
    throw new RuntimeException('Database connection is not initialized.');
}

require_login();

$user_id = (int) $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['full_name'] ?? 'User');
$user_email = htmlspecialchars($_SESSION['email'] ?? '');
$user_phone = htmlspecialchars($_SESSION['phone'] ?? '');
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        if ($full_name && $email && $phone) {
            $stmt = mysqli_prepare($conn, "UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'sssi', $full_name, $email, $phone, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                refresh_user_session($conn, $user_id);
                $user_name = htmlspecialchars($_SESSION['full_name']);
                $user_email = htmlspecialchars($_SESSION['email']);
                $user_phone = htmlspecialchars($_SESSION['phone']);
                $message = 'Profile updated successfully.';
            } else {
                $error = 'Failed to update profile.';
            }
        } else {
            $error = 'All fields are required.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $dir = __DIR__ . '/../uploads/avatars';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    foreach (['jpg','jpeg','png','webp'] as $ext) {
        @unlink("$dir/{$user_id}.{$ext}");
    }
    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION) ?: 'jpg';
    $ext = strtolower(preg_replace('/[^a-z]/', '', $ext)) ?: 'jpg';
    $dest = "$dir/{$user_id}.{$ext}";
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
        $message = 'Profile photo updated.';
    } else {
        $error = 'Failed to upload photo.';
    }
}

if (isset($_GET['remove_avatar'])) {
    $dir = __DIR__ . '/../uploads/avatars';
    foreach (['jpg','jpeg','png','webp'] as $ext) {
        @unlink("$dir/{$user_id}.{$ext}");
    }
    header('Location: profile.php');
    exit;
}

$avatar = get_avatar_path($user_id);
$initials = get_user_initials($user_name);
$member_since = '';
$stmt = mysqli_prepare($conn, "SELECT created_at FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if ($row) $member_since = date('M Y', strtotime($row['created_at']));

$tx_count = count(get_recent_transactions($conn, $user_id, 1000));
// $goals_done = count(array_filter(get_user_savings_goals($conn, $user_id), fn($g) => (float)$g['current_amount'] >= (float)$g['target_amount']));

$page_title = 'Profile';
$active_page = 'profile';
$dash_path = '../Dashboard/';
$user_path = '';

include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/user_sidebar.php';
include __DIR__ . '/../includes/user_navbar.php';
?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2 card-title">My Profile</h1>
        <p class="card-text">Manage your personal information</p>
    </div>

    <?php if ($message): ?><div class="alert-success rounded-xl p-4 mb-4"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert-error rounded-xl p-4 mb-4"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="card rounded-2xl p-8 mb-8">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="relative">
                <?php if ($avatar): ?>
                <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="w-32 h-32 rounded-full object-cover border-4 border-green-500/30" id="profileAvatar">
                <?php else: ?>
                <div class="w-32 h-32 rounded-full avatar-fallback flex items-center justify-center text-white text-4xl font-bold" id="profileAvatar"><?php echo htmlspecialchars($initials); ?></div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data" class="absolute bottom-0 right-0">
                    <label class="w-10 h-10 gradient-pink rounded-full flex items-center justify-center text-white cursor-pointer hover:opacity-90">
                        <i class="fas fa-camera"></i>
                        <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                    </label>
                </form>
            </div>
            <div class="text-center md:text-left">
                <h2 class="text-2xl font-bold card-title mb-2"><?php echo $user_name; ?></h2>
                <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                    <span class="bg-green-500/20 text-green-600 dark:text-green-400 px-3 py-1 rounded-full text-sm">Active</span>
                    <?php if ($avatar): ?><a href="?remove_avatar=1" class="text-sm text-red-500 hover:underline">Remove photo</a><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card rounded-2xl p-6 mb-8">
        <h3 class="text-lg font-semibold mb-6 card-title">Personal Information</h3>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="update_profile" value="1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm mb-2 card-text">Full Name *</label><input type="text" name="full_name" required value="<?php echo $user_name; ?>" class="form-input w-full rounded-xl px-4 py-3"></div>
                <div><label class="block text-sm mb-2 card-text">Email *</label><input type="email" name="email" required value="<?php echo $user_email; ?>" class="form-input w-full rounded-xl px-4 py-3"></div>
                <div><label class="block text-sm mb-2 card-text">Phone *</label><input type="tel" name="phone" required value="<?php echo $user_phone; ?>" class="form-input w-full rounded-xl px-4 py-3"></div>
            </div>
            <div class="flex justify-end"><button type="submit" class="gradient-blue text-white px-8 py-3 rounded-xl font-medium">Save Changes</button></div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Member Since</p><p class="text-xl font-bold card-title"><?php echo $member_since ?: '-'; ?></p></div>
        <div class="card rounded-2xl p-6"><p class="text-sm card-text">Total Transactions</p><p class="text-xl font-bold card-title"><?php echo $tx_count; ?></p></div>
       
</div>

