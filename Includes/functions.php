<?php

// Currency helpers
function format_tsh(float $amount): string
{
    // Tanzanian Shillings - format as: Tsh 1,234.56
    return 'Tsh ' . number_format($amount, 2);
}

// Note: removed duplicate/deprecated alias for format_tsh to avoid redeclaration.

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

/**
 * Total deposits shown on the transactions page.
 * Source of truth is the `incomes` table so this value is guaranteed
 * to match `get_total_income()`.
 */
function get_total_deposits(mysqli $conn, int $user_id, ?string $from = null, ?string $to = null): float
{
    return get_total_income($conn, $user_id, $from, $to);
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

/**
 * Total withdraws shown on the transactions page.
 * Source of truth is the `expenses` table so this value is guaranteed
 * to match `get_total_expenses()`.
 */
function get_total_withdraws(mysqli $conn, int $user_id, ?string $from = null, ?string $to = null): float
{
    return get_total_expenses($conn, $user_id, $from, $to);
}

function get_total_savings(mysqli $conn, int $user_id): float
{
    $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(current_amount), 0) AS total FROM savings_goals WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return (float) ($row['total'] ?? 0);
}

/**
 * Current user balance.
 * Single source of truth: balance = total_income - total_expenses.
 * No persisted balance column is used; this is recomputed on every read
 * so adding an income or an expense is immediately reflected everywhere.
 */
function get_balance(mysqli $conn, int $user_id, ?string $from = null, ?string $to = null): float
{
    return get_total_income($conn, $user_id, $from, $to)
        - get_total_expenses($conn, $user_id, $from, $to);
}

/**
 * Unified transactions list shown on the Transactions page.
 * Combines income (as deposits) and expenses (as withdraws) into a single,
 * chronologically ordered feed so all views stay in sync.
 *
 * Returned rows are normalized to the shape:
 *   [
 *     'id'           => int,
 *     'user_id'      => int,
 *     'tx_type'      => 'deposit' | 'withdraw',
 *     'amount'       => float,
 *     'description'  => string,
 *     'category'     => string|null,
 *     'tx_date'      => 'Y-m-d',
 *     'created_at'   => 'Y-m-d H:i:s',
 *     'source'       => 'income' | 'expense',
 *     'source_id'    => int,
 *   ]
 */
function get_unified_transactions(mysqli $conn, int $user_id, int $limit = 200): array
{
    $sql = "
        SELECT
            i.id            AS id,
            i.user_id       AS user_id,
            'deposit'       AS tx_type,
            i.amount        AS amount,
            i.source        AS description,
            NULL            AS category,
            i.income_date   AS tx_date,
            i.created_at    AS created_at,
            'income'        AS source,
            i.id            AS source_id
        FROM incomes i
        WHERE i.user_id = ?

        UNION ALL

        SELECT
            e.id            AS id,
            e.user_id       AS user_id,
            'withdraw'      AS tx_type,
            e.amount        AS amount,
            e.description   AS description,
            ec.category_name AS category,
            e.expense_date  AS tx_date,
            e.created_at    AS created_at,
            'expense'       AS source,
            e.id            AS source_id
        FROM expenses e
        LEFT JOIN expense_categories ec ON ec.id = e.category_id
        WHERE e.user_id = ?

        ORDER BY tx_date DESC, created_at DESC
        LIMIT ?
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'iii', $user_id, $user_id, $limit);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
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

/**
 * Chart data helpers — used by the api/chart_data.php endpoint AND by
 * any page that wants to re-render its charts after a POST/redirect
 * without making an extra HTTP request. The numbers returned here are
 * the single source of truth for every chart on the dashboard, reports
 * and transactions pages, so all graphs and stat cards stay in sync
 * with the moment a user adds or removes an income / expense / budget.
 */

function get_chart_monthly(mysqli $conn, int $user_id): array
{
    $data = get_monthly_income_expenses($conn, $user_id);
    $savings = [];
    foreach ($data['income'] as $i => $v) {
        $savings[] = round(((float) $v) - ((float) $data['expenses'][$i]), 2);
    }
    return [
        'labels'   => $data['labels'],
        'income'   => $data['income'],
        'expenses' => $data['expenses'],
        'savings'  => $savings,
    ];
}

function get_chart_category(mysqli $conn, int $user_id): array
{
    $rows = get_expenses_by_category($conn, $user_id);
    $labels = array_map(fn($r) => $r['category_name'], $rows);
    $values = array_map(fn($r) => (float) $r['total'], $rows);
    return ['labels' => $labels, 'values' => $values];
}

function get_chart_income_expense(mysqli $conn, int $user_id, ?string $from = null, ?string $to = null): array
{
    return [
        'total_income'   => get_total_income($conn, $user_id, $from, $to),
        'total_expenses' => get_total_expenses($conn, $user_id, $from, $to),
        'balance'        => get_balance($conn, $user_id, $from, $to),
    ];
}

function get_chart_flow(mysqli $conn, int $user_id, int $days = 14): array
{
    $days = max(1, $days);
    $labels = [];
    $values = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $labels[] = date('M j', strtotime($d));
        $stmt = mysqli_prepare($conn, "
            SELECT
              (SELECT COALESCE(SUM(amount),0) FROM incomes
                 WHERE user_id = ? AND income_date <= ?) -
              (SELECT COALESCE(SUM(amount),0) FROM expenses
                 WHERE user_id = ? AND expense_date <= ?) AS bal
        ");
        mysqli_stmt_bind_param($stmt, 'isss', $user_id, $d, $user_id, $d);
        mysqli_stmt_execute($stmt);
        $values[] = (float) (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['bal'] ?? 0);
    }
    return ['labels' => $labels, 'values' => $values];
}

function get_budget_remaining(mysqli $conn, int $user_id, ?int $budget_id = null): array
{
    // Per-row budget remaining (budget_amount - spent so far) so the
    // dashboard, reports, and budget pages all see the same value.
    $sql = "
        SELECT
            b.id            AS id,
            b.budget_amount AS budget_amount,
            b.month         AS month,
            b.year          AS year,
            b.category_id   AS category_id,
            ec.category_name AS category_name,
            COALESCE((
                SELECT SUM(e.amount) FROM expenses e
                WHERE e.user_id = b.user_id
                  AND e.category_id = b.category_id
                  AND MONTH(e.expense_date) =
                      CASE LOWER(b.month)
                          WHEN 'january' THEN 1 WHEN 'february' THEN 2
                          WHEN 'march' THEN 3 WHEN 'april' THEN 4
                          WHEN 'may' THEN 5 WHEN 'june' THEN 6
                          WHEN 'july' THEN 7 WHEN 'august' THEN 8
                          WHEN 'september' THEN 9 WHEN 'october' THEN 10
                          WHEN 'november' THEN 11 WHEN 'december' THEN 12
                          ELSE MONTH(b.month)
                      END
                  AND YEAR(e.expense_date) = b.year
            ), 0) AS spent
        FROM budgets b
        JOIN expense_categories ec ON ec.id = b.category_id
        WHERE b.user_id = ?
    ";
    $types = 'i';
    $params = [$user_id];
    if ($budget_id !== null) {
        $sql .= " AND b.id = ?";
        $types .= 'i';
        $params[] = $budget_id;
    }
    $sql .= " ORDER BY b.year DESC, b.month DESC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    foreach ($rows as &$r) {
        $r['remaining'] = round(((float) $r['budget_amount']) - ((float) $r['spent']), 2);
    }
    return $rows;
}
