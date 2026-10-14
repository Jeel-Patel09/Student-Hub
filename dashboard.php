<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
$user = require_login();
$pageTitle = 'Dashboard';
$stmt = $pdo->prepare('SELECT id,student_code,course FROM students WHERE user_id = ? LIMIT 1');
$stmt->execute([$user['id']]);
$student = $stmt->fetch();
$registrationCount = 0;
if ($student) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM registrations WHERE student_id = ?');
    $stmt->execute([(int)$student['id']]);
    $registrationCount = (int)$stmt->fetchColumn();
}
$eventCount = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE status = 'upcoming'")->fetchColumn();
require __DIR__ . '/includes/header.php';
?>
<h1>Welcome, <?= e($user['name']) ?>!</h1>
<p class="muted">Here is your StudentHub overview.</p>
<section class="grid">
  <article class="card"><h3>Account role</h3><p class="status"><?= e(ucfirst($user['role'])) ?></p></article>
  <article class="card"><h3>Upcoming events</h3><p><?= $eventCount ?></p><a href="events.php">Browse events</a></article>
  <article class="card"><h3>Your registrations</h3><p><?= $registrationCount ?></p><a href="events.php">Manage event registrations</a></article>
</section>
<section class="panel">
  <h2>Student information</h2>
  <p><strong>Name:</strong> <?= e($user['name']) ?></p>
  <p><strong>Email:</strong> <?= e($user['email']) ?></p>
  <p><strong>Student code:</strong> <?= e($student['student_code'] ?? 'Not assigned') ?></p>
  <p><strong>Course:</strong> <?= e($student['course'] ?? 'Not applicable') ?></p>
  <a class="button" href="profile.php">Manage profile</a>
  <?php if ($user['role'] === 'admin'): ?> <a class="button secondary" href="admin/index.php">Open admin dashboard</a><?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
