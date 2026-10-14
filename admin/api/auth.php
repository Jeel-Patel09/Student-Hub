<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function requireAdmin(): void {
    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ../login.php');
        exit;
    }
}
