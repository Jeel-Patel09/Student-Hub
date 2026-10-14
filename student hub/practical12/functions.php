<?php
session_start();

const CATEGORIES      = ['Technical', 'Cultural', 'Sports', 'Workshop', 'Seminar'];
const STATUSES        = ['open', 'closed'];
const MAX_POSTER_SIZE = 2 * 1024 * 1024;                  // 2 MB
const ALLOWED_EXT     = ['jpg', 'jpeg', 'png', 'webp'];
const ALLOWED_MIME    = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
define('UPLOAD_DIR', __DIR__ . '/uploads/');

function e($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function show_flash(): void {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $cls = $f['type'] === 'success' ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $cls . '">' . e($f['msg']) . '</div>';
    }
}

/* ---------- Access control ---------- */

function require_admin(): void {
    if (empty($_SESSION['admin_id'])) {
        flash('error', 'Please log in as admin to continue.');
        redirect('login.php');
    }
}

/* ---------- CSRF ---------- */

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function check_csrf(): void {
    if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        http_response_code(403);
        die('Invalid request (CSRF check failed).');
    }
}

/* ---------- Validation ---------- */

function validate_event(array $d): array {
    $errors = [];
    if (mb_strlen($d['title']) < 3 || mb_strlen($d['title']) > 150) {
        $errors[] = 'Title must be 3-150 characters.';
    }
    if ($d['description'] === '' || mb_strlen($d['description']) > 2000) {
        $errors[] = 'Description is required (max 2000 characters).';
    }
    $dt = DateTime::createFromFormat('Y-m-d', $d['event_date']);
    if (!$dt || $dt->format('Y-m-d') !== $d['event_date']) {
        $errors[] = 'Enter a valid event date.';
    }
    if (mb_strlen($d['venue']) < 2 || mb_strlen($d['venue']) > 150) {
        $errors[] = 'Venue must be 2-150 characters.';
    }
    if (!in_array($d['category'], CATEGORIES, true)) {
        $errors[] = 'Select a valid category.';
    }
    if (!in_array($d['status'], STATUSES, true)) {
        $errors[] = 'Select a valid status.';
    }
    return $errors;
}

/* ---------- Poster upload (validation + renaming) ---------- */

// Returns the new stored filename, or null (no file uploaded OR error added to $errors)
function save_poster(?array $file, array &$errors): ?string {
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;                                   // poster is optional
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
            ? 'Poster is too large. Maximum allowed size is 2 MB.'
            : 'Poster upload failed (error code ' . $file['error'] . ').';
        return null;
    }

    // 1) Size check
    if ($file['size'] > MAX_POSTER_SIZE) {
        $errors[] = 'Poster must be 2 MB or smaller.';
        return null;
    }

    // 2) Extension check
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXT, true)) {
        $errors[] = 'Only JPG, PNG or WEBP files are allowed.';
        return null;
    }

    // 3) Real MIME type check (do NOT trust $_FILES['type'])
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset(ALLOWED_MIME[$mime]) || @getimagesize($file['tmp_name']) === false) {
        $errors[] = 'The uploaded file is not a valid image.';
        return null;
    }

    // 4) Rename to a random name (original name is never used)
    $newName = bin2hex(random_bytes(16)) . '.' . ALLOWED_MIME[$mime];

    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $newName)) {
        $errors[] = 'Could not save the poster on the server.';
        return null;
    }
    return $newName;
}

function delete_poster_file(?string $name): void {
    if ($name) {
        $path = UPLOAD_DIR . basename($name);
        if (is_file($path)) {
            unlink($path);
        }
    }
}

/* ---------- Audit log ---------- */

function audit(mysqli $conn, ?int $eventId, string $action, string $details): void {
    $user = $_SESSION['admin_username'] ?? 'unknown';
    $details = mb_substr($details, 0, 500);
    $stmt = $conn->prepare(
        "INSERT INTO audit_log (admin_username, event_id, action, details) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('siss', $user, $eventId, $action, $details);
    $stmt->execute();
}