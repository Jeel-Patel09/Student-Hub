<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
$user = current_user();
$pageTitle = 'Contact';
$errors = [];
$name = trim($_POST['name'] ?? ($user['name'] ?? ''));
$email = strtolower(trim($_POST['email'] ?? ($user['email'] ?? '')));
$message = trim($_POST['message'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf'] ?? null);
    if ($name === '' || mb_strlen($name) > 100) $errors[] = 'Enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if ($message === '' || mb_strlen($message) > 3000) $errors[] = 'Enter a message (maximum 3000 characters).';
    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO contact_messages (user_id,name,email,message) VALUES (?,?,?,?)');
        $stmt->execute([$user['id'] ?? null, $name, $email, $message]);
        audit($pdo, $user['id'] ?? null, 'CONTACT_MESSAGE', 'Contact form submitted');
        flash('success', 'Your message has been saved. Thank you for contacting StudentHub.');
        header('Location: contact.php'); exit;
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="form-card"><h1>Contact StudentHub</h1>
<?php if ($errors): ?><div class="notice error" role="alert"><ul><?php foreach($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<label for="name">Name</label><input id="name" name="name" required maxlength="100" value="<?= e($name) ?>">
<label for="email">Email</label><input id="email" name="email" type="email" required value="<?= e($email) ?>">
<label for="message">Message</label><textarea id="message" name="message" required maxlength="3000"><?= e($message) ?></textarea>
<p><button type="submit">Send message</button></p></form></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
