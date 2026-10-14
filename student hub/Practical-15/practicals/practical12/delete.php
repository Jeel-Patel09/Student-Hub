<?php
require 'config.php';
require 'functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
check_csrf();

$id = (int)($_POST['id'] ?? 0);

try {
    $stmt = $conn->prepare("SELECT title, poster FROM events WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();

    if (!$event) {
        flash('error', 'Event not found.');
        redirect('index.php');
    }

    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    delete_poster_file($event['poster']);
    audit($conn, $id, 'DELETE', "Deleted '{$event['title']}'");

    flash('success', 'Event deleted successfully.');
} catch (mysqli_sql_exception $ex) {
    flash('error', 'Database error: could not delete the event.');
}

redirect('index.php');