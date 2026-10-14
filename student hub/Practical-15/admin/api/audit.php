<?php
function writeAudit(PDO $pdo, ?int $userId, string $action, string $details): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $stmt = $pdo->prepare(
        'INSERT INTO audit_logs (user_id, action, details, ip_address)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $action, mb_substr($details, 0, 255), $ip]);
}
