<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Event Manager</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="index.php">Events</a>
            <a class="nav-link" href="add.php">+ Add Event</a>
            <a class="nav-link" href="audit.php">Audit Log</a>
        </div>
        <span class="navbar-text me-3">Admin: <?= e($_SESSION['admin_username'] ?? '') ?></span>
        <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
    </div>
</nav>
<div class="container pb-5">
    <?php show_flash(); ?>