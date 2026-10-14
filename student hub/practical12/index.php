<?php
require 'config.php';
require 'functions.php';
require_admin();

$q        = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? '';
$status   = $_GET['status'] ?? '';

$where = []; $types = ''; $params = [];

if ($q !== '') {
    $where[] = '(title LIKE ? OR venue LIKE ?)';
    $like = "%$q%";
    $types .= 'ss';
    array_push($params, $like, $like);
}
if (in_array($category, CATEGORIES, true)) {
    $where[] = 'category = ?';
    $types .= 's';
    $params[] = $category;
}
if (in_array($status, STATUSES, true)) {
    $where[] = 'status = ?';
    $types .= 's';
    $params[] = $status;
}

$sql = 'SELECT * FROM events' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY event_date DESC';
$stmt = $conn->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$result = $stmt->get_result();
$total  = $result->num_rows;

require 'header.php';
?>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Search title or venue" value="<?= e($q) ?>">
    </div>
    <div class="col-md-3">
        <select name="category" class="form-select">
            <option value="">All Categories</option>
            <?php foreach (CATEGORIES as $c): ?>
                <option value="<?= e($c) ?>" <?= $category === $c ? 'selected' : '' ?>><?= e($c) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Open + Closed</option>
            <option value="open"   <?= $status === 'open'   ? 'selected' : '' ?>>Open</option>
            <option value="closed" <?= $status === 'closed' ? 'selected' : '' ?>>Closed</option>
        </select>
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary">Filter</button>
        <a href="index.php" class="btn btn-secondary">Reset</a>
    </div>
</form>

<p><b><?= $total ?></b> event(s) found.</p>

<div class="table-responsive">
<table class="table table-bordered table-striped align-middle bg-white">
    <thead class="table-dark">
        <tr>
            <th>#</th><th>Poster</th><th>Title</th><th>Date</th><th>Venue</th>
            <th>Category</th><th>Status</th><th style="width:150px">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($total === 0): ?>
        <tr><td colspan="8" class="text-center">No events found.</td></tr>
    <?php endif; ?>

    <?php while ($r = $result->fetch_assoc()): ?>
        <tr>
            <td><?= (int)$r['id'] ?></td>
            <td>
                <?php if ($r['poster']): ?>
                    <img src="uploads/<?= e($r['poster']) ?>" alt="Poster" style="height:55px" class="rounded border">
                <?php else: ?>
                    <span class="text-muted small">No poster</span>
                <?php endif; ?>
            </td>
            <td><?= e($r['title']) ?></td>
            <td><?= e($r['event_date']) ?></td>
            <td><?= e($r['venue']) ?></td>
            <td><?= e($r['category']) ?></td>
            <td>
                <span class="badge <?= $r['status'] === 'open' ? 'bg-success' : 'bg-secondary' ?>">
                    <?= e(ucfirst($r['status'])) ?>
                </span>
            </td>
            <td>
                <a href="edit.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                <form method="POST" action="delete.php" class="d-inline"
                      onsubmit="return confirm('Delete this event permanently?');">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>

<?php require 'footer.php'; ?>