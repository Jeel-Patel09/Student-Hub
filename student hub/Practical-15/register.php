<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
if (current_user()) { header('Location: dashboard.php'); exit; }
$pageTitle = 'Register';
$errors = [];
$name = trim($_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$course = trim($_POST['course'] ?? 'Computer Engineering');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf'] ?? null);
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if ($name === '' || mb_strlen($name) > 100) $errors[] = 'Enter a name up to 100 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) $errors[] = 'Enter a valid email address.';
    if (mb_strlen($password) < 8) $errors[] = 'Password must contain at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
    if (!in_array($course, ['Computer Engineering','Information Technology','Computer Science','Other'], true)) $errors[] = 'Select a valid course.';
    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash,role,status) VALUES (?,?,?,'student','active')");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $userId = (int)$pdo->lastInsertId();
            $studentCode = 'SH' . strtoupper(bin2hex(random_bytes(4)));
            $stmt = $pdo->prepare('INSERT INTO students (user_id, student_code, name, email, course) VALUES (?,?,?,?,?)');
            $stmt->execute([$userId, $studentCode, $name, $email, $course]);
            $pdo->commit();
            audit($pdo, $userId, 'REGISTER', 'Student account created');
            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'student';
            flash('success', 'Registration completed. Welcome to StudentHub!');
            header('Location: dashboard.php'); exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('Registration failed: ' . $e->getMessage());
            $errors[] = 'That email may already be registered. Please try another email.';
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="form-card">
  <h1>Create your StudentHub account</h1>
  <p class="muted">Fields marked required must be completed.</p>
  <?php if ($errors): ?><div class="notice error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post" action="register.php">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label for="name">Full name *</label><input id="name" name="name" required maxlength="100" autocomplete="name" value="<?= e($name) ?>">
    <label for="email">Email *</label><input id="email" name="email" type="email" required maxlength="150" autocomplete="email" value="<?= e($email) ?>">
    <label for="course">Course *</label>
    <select id="course" name="course" required>
      <?php foreach (['Computer Engineering','Information Technology','Computer Science','Other'] as $c): ?>
        <option value="<?= e($c) ?>" <?= $course === $c ? 'selected' : '' ?>><?= e($c) ?></option>
      <?php endforeach; ?>
    </select>
    <label for="password">Password (minimum 8 characters) *</label><input id="password" name="password" type="password" minlength="8" required autocomplete="new-password">
    <label for="confirm_password">Confirm password *</label><input id="confirm_password" name="confirm_password" type="password" minlength="8" required autocomplete="new-password">
    <p><button type="submit">Register</button></p>
  </form>
  <p>Already registered? <a href="login.php">Sign in</a>.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
