<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About';
require __DIR__ . '/includes/header.php';
?>
<section class="panel">
  <h1>About StudentHub</h1>
  <p>StudentHub is a college portal prototype designed to bring common student services into one place. It combines a responsive interface with PHP sessions and a MySQL database.</p>
  <h2>Project objectives</h2>
  <ul class="clean">
    <li>Provide clear navigation and a responsive interface.</li>
    <li>Support account registration, login and profile management.</li>
    <li>Display events and store event registrations.</li>
    <li>Provide administrator tools, protected actions and audit records.</li>
    <li>Document setup, testing and local deployment.</li>
  </ul>
  <p class="muted small">This is an educational project. Review the security and hosting notes in README.md before exposing it publicly.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
