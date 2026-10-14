<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Home';
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <p class="status">Student portal</p>
  <h1>Everything students need, in one hub.</h1>
  <p>StudentHub brings student profiles, campus events, registration, feedback and administration into one responsive web application.</p>
  <div class="actions">
    <?php if (current_user()): ?>
      <a class="button" href="dashboard.php">Open dashboard</a>
    <?php else: ?>
      <a class="button" href="register.php">Create account</a>
      <a class="button secondary" href="login.php">Sign in</a>
    <?php endif; ?>
    <a class="button secondary" href="events.php">Explore events</a>
  </div>
</section>
<h2 class="section-heading">Portal features</h2>
<section class="grid">
  <article class="card"><h3>Student dashboard</h3><p>Access your profile and event registrations after signing in.</p></article>
  <article class="card"><h3>Campus events</h3><p>Browse upcoming activities and register for eligible events.</p></article>
  <article class="card"><h3>Secure access</h3><p>Role-based access, password hashing, prepared SQL statements and CSRF checks for state-changing forms.</p></article>
  <article class="card"><h3>Administration</h3><p>Administrators can manage accounts, review registrations and view audit history.</p></article>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
