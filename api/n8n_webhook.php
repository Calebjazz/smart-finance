<?php
/**
 * n8n Webhook endpoint for Smart Finance automations
 * POST JSON: { "event_type": "...", "message": "...", "user_id": 1, "status": "success" }
 * Optional: creates system notification when notify=true
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$event_type = trim($input['event_type'] ?? 'n8n_event');
$message = trim($input['message'] ?? '');
$user_id = isset($input['user_id']) ? (int) $input['user_id'] : null;
$status = in_array($input['status'] ?? '', ['success', 'failure']) ? $input['status'] : 'success';
$notify = !empty($input['notify']);
$title = trim($input['title'] ?? $event_type);

if ($message === '') {
    http_response_code(400);
    echo json_encode(['error' => 'message is required']);
    exit;
}

$logged = log_automation($conn, $user_id, $event_type, $message, $status);

if ($notify && $user_id) {
    create_notification($conn, $user_id, $title, $message);
}

echo json_encode([
    'success' => $logged,
    'event_type' => $event_type,
    'logged_at' => date('c'),
]);
