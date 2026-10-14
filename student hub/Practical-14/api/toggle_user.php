<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Administrator access required.']);
    exit;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/audit.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON request.']);
    exit;
}

if (!hash_equals($_SESSION['csrf_token'] ?? '', (string)($data['csrf_token'] ?? ''))) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token. Refresh the page and try again.']);
    exit;
}

$userId = filter_var($data['user_id'] ?? null, FILTER_VALIDATE_INT);
$status = $data['status'] ?? '';

if (!$userId || !in_array($status, ['active', 'inactive'], true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid user ID or status.']);
    exit;
}

if ((int)$userId === (int)$_SESSION['user_id']) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'You cannot change your own status.']);
    exit;
}

$stmt = $pdo->prepare('SELECT id, name, status FROM users WHERE id = ?');
$stmt->execute([(int)$userId]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

$update = $pdo->prepare('UPDATE users SET status = ? WHERE id = ?');
$update->execute([$status, (int)$userId]);
writeAudit($pdo, (int)$_SESSION['user_id'], 'USER_STATUS_CHANGED',
    'Changed user ' . $user['email'] . ' status from ' . $user['status'] . ' to ' . $status);

echo json_encode(['success' => true, 'message' => 'User status updated successfully.']);
