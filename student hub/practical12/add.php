<?php
require 'config.php';
require 'functions.php';
require_admin();

$errors = [];
$data = ['title' => '', 'description' => '', 'event_date' => '', 'venue' => '',
         'category' => '', 'status' => 'open', 'poster' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $data = [
        'title'       => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'event_date'  => trim($_POST['event_date'] ?? ''),
        'venue'       => trim($_POST['venue'] ?? ''),
        'category'    => trim($_POST['category'] ?? ''),
        'status'      => trim($_POST['status'] ?? ''),
        'poster'      => null,
    ];
    $errors = validate_event($data);

    $poster = null;
    if (!$errors) {
        $poster = save_poster($_FILES['poster'] ?? null, $errors);   // may add upload errors
    }

    if (!$errors) {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO events (title, description, event_date, venue, category, status, poster)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('sssssss', $data['title'], $data['description'], $data['event_date'],
                              $data['venue'], $data['category'], $data['status'], $poster);
            $stmt->execute();
            $newId = $conn->insert_id;

            audit($conn, $newId, 'CREATE',
                  "Created '{$data['title']}'" . ($poster ? ' with poster ' . $poster : ' without poster'));

            flash('success', 'Event added successfully.');
            redirect('index.php');
        } catch (mysqli_sql_exception $ex) {
            delete_poster_file($poster);          // do not leave an orphan file behind
            $errors[] = 'Database error: could not add the event.';
        }
    }
}

$action = 'add.php';
$button = 'Add Event';
require 'header.php';
echo '<h3 class="mb-3">Add Event</h3>';
require 'form.php';
require 'footer.php';