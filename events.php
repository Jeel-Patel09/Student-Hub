<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
$user = current_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = require_login();
    verify_csrf($_POST['csrf'] ?? null);
    $eventId = filter_var($_POST['event_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$eventId || $user['role'] === 'admin') {
        flash('error', 'This account cannot register for that event.');
        header('Location: events.php'); exit;
    }
    $stmt = $pdo->prepare("SELECT id FROM events WHERE id = ? AND status = 'upcoming'");
    $stmt->execute([$eventId]);
    if (!$stmt->fetch()) {
        flash('error', 'That event is not available for registration.');
        header('Location: events.php'); exit;
    }
    $stmt = $pdo->prepare('SELECT id FROM students WHERE user_id = ? LIMIT 1');
    $stmt->execute([$user['id']]);
    $student = $stmt->fetch();
    if (!$student) {
        flash('error', 'Your student profile was not found. Contact the administrator.');
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO registrations (student_id,event_id) VALUES (?,?)');
            $stmt->execute([(int)$student['id'], $eventId]);
            audit($pdo, $user['id'], 'EVENT_REGISTER', 'Registered for event ID ' . $eventId);
            flash('success', 'You have registered for the event.');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') flash('error', 'You are already registered for this event.');
            else { error_log($e->getMessage()); flash('error', 'Could not complete registration.'); }
        }
    }
    header('Location: events.php'); exit;
}
$pageTitle = 'Events';
$events = $pdo->query('SELECT id,title,event_date,status FROM events ORDER BY event_date ASC')->fetchAll();
$studentId = null;
if ($user && $user['role'] !== 'admin') {
    $stmt = $pdo->prepare('SELECT id FROM students WHERE user_id = ?');
    $stmt->execute([$user['id']]); $studentId = $stmt->fetchColumn();
}
$registered = [];
if ($studentId) {
    $stmt = $pdo->prepare('SELECT event_id FROM registrations WHERE student_id = ?');
    $stmt->execute([(int)$studentId]);
    $registered = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}
require __DIR__ . '/includes/header.php';
?>
<h1>Campus events</h1>
<p class="muted">Explore events and register with your StudentHub account.</p>
<?php if (!$events): ?><div class="panel">No events have been added yet.</div><?php else: ?>
<section class="grid">
<?php foreach ($events as $event): ?>
  <article class="card">
    <p class="status"><?= e(ucfirst($event['status'])) ?></p>
    <h2><?= e($event['title']) ?></h2>
    <p><strong>Date:</strong> <?= e($event['event_date']) ?></p>
    <?php if ($user && $user['role'] !== 'admin' && $event['status'] === 'upcoming'): ?>
      <?php if (in_array((int)$event['id'], $registered, true)): ?>
        <p class="muted">You are registered.</p>
      <?php else: ?>
        <form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="event_id" value="<?= (int)$event['id'] ?>"><button type="submit">Register for event</button></form>
      <?php endif; ?>
    <?php elseif (!$user && $event['status'] === 'upcoming'): ?>
      <a class="button" href="login.php">Login to register</a>
    <?php endif; ?>
  </article>
<?php endforeach; ?>
</section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
