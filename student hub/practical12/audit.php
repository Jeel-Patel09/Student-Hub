<?php
require 'config.php';
require 'functions.php';
require_admin();

$logs = $conn->query("SELECT * FROM audit_log ORDER BY id DESC LIMIT 100");

require 'header.php';
?>
<h3 class="mb-3">Audit Log</h3>
<div class="table-responsive">
<table class="table table-bordered table-striped bg-white">
    <thead class="table-dark">
        <tr><th>#</th><th>Time</th><th>Admin</th><th>Event ID</th><th>Action</th><th>Details</th></tr>
    </thead>
    <tbody>
    <?php if ($logs->num_rows === 0): ?>
        <tr><td colspan="6" class="text-center">No activity yet.</td></tr>
    <?php endif; ?>
    <?php while ($l = $logs->fetch_assoc()): ?>
        <tr>
            <td><?= (int)$l['id'] ?></td>
            <td><?= e($l['created_at']) ?></td>
            <td><?= e($l['admin_username']) ?></td>
            <td><?= e($l['event_id']) ?></td>
            <td>
                <span class="badge <?= ['CREATE' => 'bg-success', 'UPDATE' => 'bg-primary', 'DELETE' => 'bg-danger'][$l['action']] ?? 'bg-secondary' ?>">
                    <?= e($l['action']) ?>
                </span>
            </td>
            <td><?= e($l['details']) ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>
<?php require 'footer.php'; ?>