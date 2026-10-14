<?php
$pageTitle = $pageTitle ?? 'StudentHub';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="StudentHub student portal for events, profiles and academic activities.">
  <title><?= e($pageTitle) ?> | StudentHub</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
  <a class="brand" href="index.php">StudentHub</a>
  <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav">Menu</button>
  <nav id="main-nav" class="main-nav" aria-label="Main navigation">
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="events.php">Events</a>
    <?php if ($user): ?>
      <a href="dashboard.php">Dashboard</a>
      <a href="profile.php">Profile</a>
      <?php if ($user['role'] === 'admin'): ?>
        <a href="students.php">Students</a>
        <a href="admin/index.php">Admin</a>
      <?php endif; ?>
      <a href="contact.php">Contact</a>
      <a href="feedback.php">Feedback</a>
      <a class="nav-cta" href="logout.php">Logout</a>
    <?php else: ?>
      <a href="contact.php">Contact</a>
      <a href="faq.php">FAQ</a>
      <a href="login.php">Login</a>
      <a class="nav-cta" href="register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
<main class="page-shell">
<?php show_flash(); ?>
