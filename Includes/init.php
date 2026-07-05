<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

/** @var mysqli $conn — make sure the global db handle is in scope */
if (!isset($conn) || !$conn instanceof mysqli) {
    global $conn;
}

function require_login(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../components/auth/login.php');
        exit();
    }
}

function require_admin(): void
{
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../components/auth/login.php');
        exit();
    }
}

function refresh_user_session(mysqli $conn, int $user_id): void
{
    $stmt = mysqli_prepare($conn, "SELECT full_name, email, phone, role FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($user) {
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['role'] = $user['role'];
    }
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}
