<?php
session_start();
require_once __DIR__ . '/api/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email address and password.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id, name, email, password_hash, role, status
             FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Invalid email or password.';
        } elseif ($user['status'] !== 'active') {
            $error = 'This account is inactive. Contact an administrator.';
        } elseif ($user['role'] !== 'admin') {
            $error = 'Access denied. This dashboard is for administrators only.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            require_once __DIR__ . '/api/audit.php';
            writeAudit($pdo, (int)$user['id'], 'LOGIN', 'Administrator signed in');

            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - Practical 14</title>
<style>
*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;background:#eef2ff;font-family:Arial,sans-serif;color:#172033;padding:20px}
.card{background:white;padding:30px;border-radius:16px;width:100%;max-width:420px;box-shadow:0 15px 45px #1e293b18}
h1{margin-top:0;color:#1d4ed8}label{display:block;font-weight:bold;margin:16px 0 7px}
input{width:100%;padding:12px;border:1px solid #cbd5e1;border-radius:8px}
button{margin-top:20px;width:100%;padding:12px;background:#2563eb;color:white;border:0;border-radius:8px;font-weight:bold;cursor:pointer}
.error{background:#fee2e2;color:#991b1b;padding:10px;border-radius:7px}
.hint{color:#64748b;font-size:13px;line-height:1.5}
</style>
</head>
<body>
<div class="card">
  <h1>Admin Dashboard</h1>
  <p>Sign in with an active administrator account.</p>
  <?php if ($error): ?><p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  <form method="post">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" required autocomplete="username"
      value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <label for="password">Password</label>
    <input id="password" name="password" type="password" required autocomplete="current-password">
    <button type="submit">Sign In</button>
  </form>
  <p class="hint">Demo login: admin@example.com / password</p>
</div>
</body>
</html>
