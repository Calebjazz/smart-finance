<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$question = trim($input['question'] ?? '');
$response = trim($input['response'] ?? '');
$user_id = (int) $_SESSION['user_id'];

if ($question === '') {
    http_response_code(400);
    echo json_encode(['error' => 'question required']);
    exit;
}

$ok = save_ai_consultation($conn, $user_id, $question, $response);
echo json_encode(['success' => $ok]);
