<?php

/**
 * Chart data endpoint.
 * Returns JSON describing the user's financial activity for the
 * dashboard, reports, and transactions pages so charts can refresh
 * on page load and after every add/delete action.
 *
 * Query params:
 *   type = monthly | category | income_expense | flow   (default: monthly)
 *   from = YYYY-MM-DD                                   (optional)
 *   to   = YYYY-MM-DD                                   (optional)
 *   days = integer (flow only, default 14)
 */

require_once __DIR__ . '/../includes/init.php';

global $conn;

header('Content-Type: application/json; charset=utf-8');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$type    = $_GET['type'] ?? 'monthly';
$from    = $_GET['from'] ?? null;
$to      = $_GET['to'] ?? null;

function chart_json(array $payload): void
{
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

switch ($type) {
    case 'monthly':
        chart_json(get_chart_monthly($conn, $user_id));

    case 'category':
        chart_json(get_chart_category($conn, $user_id));

    case 'income_expense':
        chart_json(get_chart_income_expense($conn, $user_id, $from, $to));

    case 'flow':
        $days = max(1, (int) ($_GET['days'] ?? 14));
        chart_json(get_chart_flow($conn, $user_id, $days));

    case 'budget':
        chart_json(get_budget_remaining($conn, $user_id));
}

chart_json(['error' => 'Unknown chart type']);
