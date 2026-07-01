<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mark_read') {
    $user_id = (int) $_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "UPDATE notifications SET status = 'sent' WHERE user_id = ? AND notification_type = 'system' AND status = 'pending'");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
}

$redirect = $_SERVER['HTTP_REFERER'] ?? '../Dashboard/Home.php';
header('Location: ' . $redirect);
exit;
