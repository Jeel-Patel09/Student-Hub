<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
if (current_user()) {
    header('Location: ' . ((($_SESSION['role'] ?? '') === 'admin') ? 'admin/index.php' : 'dashboard.php')); exit;
}
$pageTitle = 'Login';
$error = '';
$email = strtolower(trim($_POST['email'] ?? ''));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf'] ?? null);
    $password = $_POST['password'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email address and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id,name,email,password_hash,role,status FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $account = $stmt->fetch();
        if (!$account || !password_verify($password, $account['password_hash'])) {
            $error = 'Invalid email or password.';
        } elseif ($account['status'] !== 'active') {
            $error = 'This account is inactive. Contact an administrator.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$account['id'];
            $_SESSION['name'] = $account['name'];
            $_SESSION['email'] = $account['email'];
            $_SESSION['role'] = $account['role'];
            audit($pdo, (int)$account['id'], 'LOGIN', 'User signed in');
            header('Location: ' . ($account['role'] === 'admin' ? 'admin/index.php' : 'dashboard.php'));
            exit;
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="form-card">
  <h1>Sign in to StudentHub</h1>
  <?php if ($error): ?><div class="notice error" role="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post" action="login.php">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label for="email">Email address</label><input id="email" name="email" type="email" required autocomplete="username" value="<?= e($email) ?>">
    <label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="current-password">
    <p><button type="submit">Login</button></p>
  </form>
  <p>New here? <a href="register.php">Create an account</a>.</p>
  <p class="muted small">Admin demo account: admin@example.com / password. Change this password before any real deployment.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
