<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
$user = current_user();
$pageTitle = 'Feedback';
$errors = [];
$rating = (int)($_POST['rating'] ?? 5);
$comments = trim($_POST['comments'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf'] ?? null);
    if ($rating < 1 || $rating > 5) $errors[] = 'Choose a rating from 1 to 5.';
    if ($comments === '' || mb_strlen($comments) > 2000) $errors[] = 'Enter feedback (maximum 2000 characters).';
    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO feedback (user_id,rating,comments) VALUES (?,?,?)');
        $stmt->execute([$user['id'] ?? null, $rating, $comments]);
        audit($pdo, $user['id'] ?? null, 'FEEDBACK_SUBMIT', 'Feedback submitted');
        flash('success', 'Thank you. Your feedback has been saved.');
        header('Location: feedback.php'); exit;
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="form-card"><h1>Share your feedback</h1>
<?php if ($errors): ?><div class="notice error" role="alert"><ul><?php foreach($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<label for="rating">Rating</label><select id="rating" name="rating"><?php for($i=5;$i>=1;$i--): ?><option value="<?= $i ?>" <?= $rating===$i?'selected':'' ?>><?= $i ?> / 5</option><?php endfor; ?></select>
<label for="comments">Comments</label><textarea id="comments" name="comments" maxlength="2000" required><?= e($comments) ?></textarea>
<p><button type="submit">Submit feedback</button></p></form></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
