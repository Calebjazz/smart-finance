<?php

function format_usd(float $amount): string
{
    return '$' . number_format($amount, 2);
}

function get_user_initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    if (count($parts) >= 2) {
        return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
    }
    return strtoupper(substr($name, 0, 1));
}

function get_avatar_path(int $user_id): ?string
{
    $dir = __DIR__ . '/../uploads/avatars';
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
        $file = "$dir/{$user_id}.{$ext}";
        if (file_exists($file)) {
            return "../uploads/avatars/{$user_id}.{$ext}";
        }
    }
    return null;
}

function get_total_income(mysqli $conn, int $user_id, ?string $from = null, ?string $to = null): float
{
    $sql = "SELECT COALESCE(SUM(amount), 0) AS total FROM incomes WHERE user_id = ?";
    $types = 'i';
    $params = [$user_id];

    if ($from) {
        $sql .= " AND income_date >= ?";
        $types .= 's';
        $params[] = $from;
    }
    if ($to) {
        $sql .= " AND income_date <= ?";
        $types .= 's';
        $params[] = $to;
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return (float) ($row['total'] ?? 0);
}

function get_total_expenses(mysqli $conn, int $user_id, ?string $from = null, ?string $to = null): float
{
    $sql = "SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE user_id = ?";
    $types = 'i';
    $params = [$user_id];

    if ($from) {
        $sql .= " AND expense_date >= ?";
        $types .= 's';
        $params[] = $from;
    }
    if ($to) {
        $sql .= " AND expense_date <= ?";
        $types .= 's';
        $params[] = $to;
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return (float) ($row['total'] ?? 0);
}

function get_total_savings(mysqli $conn, int $user_id): float
{
    $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(current_amount), 0) AS total FROM savings_goals WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return (float) ($row['total'] ?? 0);
}

function get_balance(mysqli $conn, int $user_id): float
{
    return get_total_income($conn, $user_id) - get_total_expenses($conn, $user_id);
}

function get_notification_count(mysqli $conn, int $user_id): int
{
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND notification_type = 'system' AND status = 'pending'");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return (int) ($row['cnt'] ?? 0);
}

function get_notifications(mysqli $conn, int $user_id, int $limit = 10): array
{
    $stmt = mysqli_prepare($conn, "SELECT id, title, message, status, created_at FROM notifications WHERE user_id = ? AND notification_type = 'system' ORDER BY created_at DESC LIMIT ?");
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $limit);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

function create_notification(mysqli $conn, int $user_id, string $title, string $message, string $type = 'system'): bool
{
    $stmt = mysqli_prepare($conn, "INSERT INTO notifications (user_id, title, message, notification_type, status) VALUES (?, ?, ?, ?, 'pending')");
    mysqli_stmt_bind_param($stmt, 'isss', $user_id, $title, $message, $type);
    return mysqli_stmt_execute($stmt);
}

function log_automation(mysqli $conn, ?int $user_id, string $event_type, string $message, string $status = 'success'): bool
{
    if ($user_id) {
        $stmt = mysqli_prepare($conn, "INSERT INTO automation_logs (user_id, event_type, message, status) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'isss', $user_id, $event_type, $message, $status);
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO automation_logs (user_id, event_type, message, status) VALUES (NULL, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sss', $event_type, $message, $status);
    }
    return mysqli_stmt_execute($stmt);
}

function get_income_categories(mysqli $conn): array
{
    $result = mysqli_query($conn, "SELECT id, category_name FROM income_categories ORDER BY category_name");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function get_expense_categories(mysqli $conn): array
{
    $result = mysqli_query($conn, "SELECT id, category_name FROM expense_categories ORDER BY category_name");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function get_monthly_income_expenses(mysqli $conn, int $user_id, int $months = 6): array
{
    $labels = [];
    $income = [];
    $expenses = [];

    for ($i = $months - 1; $i >= 0; $i--) {
        $date = new DateTime("first day of -{$i} months");
        $year = $date->format('Y');
        $month = $date->format('m');
        $labels[] = $date->format('M');

        $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) AS t FROM incomes WHERE user_id=? AND YEAR(income_date)=? AND MONTH(income_date)=?");
        mysqli_stmt_bind_param($stmt, 'iii', $user_id, $year, $month);
        mysqli_stmt_execute($stmt);
        $income[] = (float) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['t'];

        $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) AS t FROM expenses WHERE user_id=? AND YEAR(expense_date)=? AND MONTH(expense_date)=?");
        mysqli_stmt_bind_param($stmt, 'iii', $user_id, $year, $month);
        mysqli_stmt_execute($stmt);
        $expenses[] = (float) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['t'];
    }

    return compact('labels', 'income', 'expenses');
}

function get_expenses_by_category(mysqli $conn, int $user_id): array
{
    $stmt = mysqli_prepare($conn, "
        SELECT ec.category_name, COALESCE(SUM(e.amount), 0) AS total
        FROM expense_categories ec
        LEFT JOIN expenses e ON e.category_id = ec.id AND e.user_id = ?
        GROUP BY ec.id, ec.category_name
        HAVING total > 0
        ORDER BY total DESC
    ");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

function get_recent_transactions(mysqli $conn, int $user_id, int $limit = 8): array
{
    $stmt = mysqli_prepare($conn, "
        (SELECT 'income' AS type, i.title AS label, i.amount, i.income_date AS tx_date, ic.category_name
         FROM incomes i JOIN income_categories ic ON ic.id = i.category_id WHERE i.user_id = ?)
        UNION ALL
        (SELECT 'expense' AS type, COALESCE(e.description, ec.category_name) AS label, e.amount, e.expense_date AS tx_date, ec.category_name
         FROM expenses e JOIN expense_categories ec ON ec.id = e.category_id WHERE e.user_id = ?)
        UNION ALL
        (SELECT CONCAT('savings_', st.type) AS type, st.REFERENCE AS label, st.amount, st.transaction_date AS tx_date, st.type AS category_name
         FROM savings_transactions st WHERE st.user_id = ?)
        ORDER BY tx_date DESC, label ASC
        LIMIT ?
    ");
    mysqli_stmt_bind_param($stmt, 'iiii', $user_id, $user_id, $user_id, $limit);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

function get_user_incomes(mysqli $conn, int $user_id): array
{
    $stmt = mysqli_prepare($conn, "
        SELECT i.*, ic.category_name FROM incomes i
        JOIN income_categories ic ON ic.id = i.category_id
        WHERE i.user_id = ? ORDER BY i.income_date DESC
    ");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

function get_user_expenses(mysqli $conn, int $user_id): array
{
    $stmt = mysqli_prepare($conn, "
        SELECT e.*, ec.category_name FROM expenses e
        JOIN expense_categories ec ON ec.id = e.category_id
        WHERE e.user_id = ? ORDER BY e.expense_date DESC
    ");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

function get_user_budgets(mysqli $conn, int $user_id): array
{
    $stmt = mysqli_prepare($conn, "
        SELECT b.*, ec.category_name FROM budgets b
        JOIN expense_categories ec ON ec.id = b.category_id
        WHERE b.user_id = ? ORDER BY b.year DESC, b.month DESC
    ");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

function get_user_savings_goals(mysqli $conn, int $user_id): array
{
    $stmt = mysqli_prepare($conn, "SELECT * FROM savings_goals WHERE user_id = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

function get_savings_transactions(mysqli $conn, int $user_id): array
{
    $stmt = mysqli_prepare($conn, "SELECT * FROM savings_transactions WHERE user_id = ? ORDER BY transaction_date DESC");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

function get_month_income(mysqli $conn, int $user_id): float
{
    $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) AS t FROM incomes WHERE user_id=? AND YEAR(income_date)=YEAR(CURDATE()) AND MONTH(income_date)=MONTH(CURDATE())");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    return (float) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['t'];
}

function get_month_expenses(mysqli $conn, int $user_id): float
{
    $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) AS t FROM expenses WHERE user_id=? AND YEAR(expense_date)=YEAR(CURDATE()) AND MONTH(expense_date)=MONTH(CURDATE())");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    return (float) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['t'];
}

function get_savings_rate(mysqli $conn, int $user_id): float
{
    $income = get_total_income($conn, $user_id);
    if ($income <= 0) return 0;
    $balance = get_balance($conn, $user_id);
    return round(($balance / $income) * 100, 1);
}

function get_admin_stats(mysqli $conn): array
{
    $stats = [];
    $stats['users'] = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM users WHERE role='user'"))['c'];
    $stats['transactions'] = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM savings_transactions"))['c']
        + (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM incomes"))['c']
        + (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM expenses"))['c'];
    $stats['ai_requests'] = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM ai_consultations"))['c'];
    $stats['automations'] = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM automation_logs"))['c'];
    return $stats;
}

function get_admin_recent_activity(mysqli $conn, int $limit = 8): array
{
    $activities = [];

    $users = mysqli_query($conn, "SELECT full_name, created_at FROM users ORDER BY created_at DESC LIMIT 3");
    while ($u = mysqli_fetch_assoc($users)) {
        $activities[] = ['type' => 'user', 'title' => 'New user registered', 'message' => $u['full_name'] . ' joined', 'time' => $u['created_at']];
    }

    $ai = mysqli_query($conn, "SELECT user_question, created_at FROM ai_consultations ORDER BY created_at DESC LIMIT 3");
    while ($a = mysqli_fetch_assoc($ai)) {
        $activities[] = ['type' => 'ai', 'title' => 'AI consultation', 'message' => mb_substr($a['user_question'], 0, 60), 'time' => $a['created_at']];
    }

    $auto = mysqli_query($conn, "SELECT event_type, message, created_at FROM automation_logs ORDER BY created_at DESC LIMIT 3");
    while ($l = mysqli_fetch_assoc($auto)) {
        $activities[] = ['type' => 'automation', 'title' => $l['event_type'], 'message' => $l['message'], 'time' => $l['created_at']];
    }

    usort($activities, fn($a, $b) => strtotime($b['time']) <=> strtotime($a['time']));
    return array_slice($activities, 0, $limit);
}

function time_ago(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
    return floor($diff / 86400) . ' days ago';
}

function save_ai_consultation(mysqli $conn, int $user_id, string $question, string $response): bool
{
    $stmt = mysqli_prepare($conn, "INSERT INTO ai_consultations (user_id, user_question, ai_response) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'iss', $user_id, $question, $response);
    return mysqli_stmt_execute($stmt);
}

function save_report_record(mysqli $conn, int $user_id, string $report_type): bool
{
    $stmt = mysqli_prepare($conn, "INSERT INTO reports (user_id, report_type) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, 'is', $user_id, $report_type);
    return mysqli_stmt_execute($stmt);
}
