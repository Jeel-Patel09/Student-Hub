<?php
session_start();
require_once __DIR__ . '/api/db.php';
require_once __DIR__ . '/api/audit.php';

if (!empty($_SESSION['user_id'])) {
    try {
        writeAudit($pdo, (int)$_SESSION['user_id'], 'LOGOUT', 'Administrator signed out');
    } catch (Throwable $e) {
        error_log($e->getMessage());
    }
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
header('Location: login.php');
exit;
