<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
$user = require_login();
$pageTitle = 'Profile';
$stmt = $pdo->prepare('SELECT id,name,email,course,student_code FROM students WHERE user_id = ? LIMIT 1');
$stmt->execute([$user['id']]); $student = $stmt->fetch();
$errors = [];
$name = $student['name'] ?? $user['name'];
$course = $student['course'] ?? 'Computer Engineering';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf'] ?? null);
    $name = trim($_POST['name'] ?? '');
    $course = trim($_POST['course'] ?? '');
    if ($name === '' || mb_strlen($name) > 100) $errors[] = 'Enter a name up to 100 characters.';
    if (!in_array($course, ['Computer Engineering','Information Technology','Computer Science','Other'], true)) $errors[] = 'Select a valid course.';
    if (!$errors) {
        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE users SET name = ? WHERE id = ?')->execute([$name, $user['id']]);
            if ($student) $pdo->prepare('UPDATE students SET name = ?, course = ? WHERE user_id = ?')->execute([$name, $course, $user['id']]);
            $pdo->commit();
            $_SESSION['name'] = $name;
            audit($pdo, $user['id'], 'PROFILE_UPDATE', 'Profile details updated');
            flash('success', 'Profile updated successfully.');
            header('Location: profile.php'); exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log($e->getMessage()); $errors[] = 'Profile update failed. Please try again.';
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="form-card">
<h1>Manage profile</h1>
<p><strong>Email:</strong> <?= e($user['email']) ?></p>
<p><strong>Student code:</strong> <?= e($student['student_code'] ?? 'Not assigned') ?></p>
<?php if ($errors): ?><div class="notice error" role="alert"><ul><?php foreach($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<label for="name">Full name</label><input id="name" name="name" maxlength="100" required value="<?= e($name) ?>">
<label for="course">Course</label><select id="course" name="course"><?php foreach(['Computer Engineering','Information Technology','Computer Science','Other'] as $c): ?><option <?= $course===$c?'selected':'' ?> value="<?= e($c) ?>"><?= e($c) ?></option><?php endforeach; ?></select>
<p><button type="submit">Save changes</button></p>
</form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
