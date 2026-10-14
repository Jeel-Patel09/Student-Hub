<?php
require 'config.php';
require 'functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$old = $stmt->get_result()->fetch_assoc();

if (!$old) {
    flash('error', 'Event not found.');
    redirect('index.php');
}

$data   = $old;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $data = [
        'title'       => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'event_date'  => trim($_POST['event_date'] ?? ''),
        'venue'       => trim($_POST['venue'] ?? ''),
        'category'    => trim($_POST['category'] ?? ''),
        'status'      => trim($_POST['status'] ?? ''),
        'poster'      => $old['poster'],
    ];
    $errors = validate_event($data);

    $newPoster = null;
    if (!$errors) {
        $newPoster = save_poster($_FILES['poster'] ?? null, $errors);
    }

    if (!$errors) {
        $finalPoster = $old['poster'];
        if ($newPoster) {
            $finalPoster = $newPoster;
        } elseif (!empty($_POST['remove_poster'])) {
            $finalPoster = null;
        }

        try {
            $stmt = $conn->prepare(
                "UPDATE events SET title = ?, description = ?, event_date = ?, venue = ?,
                                   category = ?, status = ?, poster = ? WHERE id = ?"
            );
            $stmt->bind_param('sssssssi', $data['title'], $data['description'], $data['event_date'],
                              $data['venue'], $data['category'], $data['status'], $finalPoster, $id);
            $stmt->execute();

            // Remove the old file only after the DB update succeeded
            if ($finalPoster !== $old['poster']) {
                delete_poster_file($old['poster']);
            }

            // Build audit details: what exactly changed
            $changes = [];
            foreach (['title', 'description', 'event_date', 'venue', 'category', 'status'] as $f) {
                if ((string)$old[$f] !== (string)$data[$f]) {
                    $changes[] = $f . ": '" . mb_strimwidth($old[$f], 0, 40, '...') . "' -> '"
                               . mb_strimwidth($data[$f], 0, 40, '...') . "'";
                }
            }
            if ($newPoster)                 { $changes[] = 'poster replaced'; }
            elseif ($finalPoster === null && $old['poster']) { $changes[] = 'poster removed'; }

            audit($conn, $id, 'UPDATE', $changes ? implode('; ', $changes) : 'No field changes');

            flash('success', 'Event updated successfully.');
            redirect('index.php');
        } catch (mysqli_sql_exception $ex) {
            delete_poster_file($newPoster);
            $errors[] = 'Database error: could not update the event.';
        }
    }
}

$action = 'edit.php?id=' . $id;
$button = 'Update Event';
require 'header.php';
echo '<h3 class="mb-3">Edit Event</h3>';
require 'form.php';
require 'footer.php';