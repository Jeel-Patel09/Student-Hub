<?php
require_once __DIR__ . '/api/auth.php';
requireAdmin();
require_once __DIR__ . '/api/db.php';
require_once __DIR__ . '/api/audit.php';

$userId = (int)$_SESSION['user_id'];
$csrf = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf;

$stats = [
    'users' => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'students' => (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn(),
    'events' => (int)$pdo->query('SELECT COUNT(*) FROM events')->fetchColumn(),
    'registrations' => (int)$pdo->query('SELECT COUNT(*) FROM registrations')->fetchColumn()
];

$latest = $pdo->query(
    'SELECT r.id, s.name AS student_name, e.title AS event_title, r.registered_at
     FROM registrations r
     JOIN students s ON s.id = r.student_id
     JOIN events e ON e.id = r.event_id
     ORDER BY r.registered_at DESC LIMIT 8'
)->fetchAll();

$users = $pdo->query(
    'SELECT id, name, email, role, status, created_at FROM users ORDER BY id DESC LIMIT 50'
)->fetchAll();

$logs = $pdo->query(
    'SELECT a.id, a.action, a.details, a.ip_address, a.created_at, u.name AS user_name
     FROM audit_logs a LEFT JOIN users u ON u.id = a.user_id
     ORDER BY a.created_at DESC LIMIT 12'
)->fetchAll();

$eventStats = $pdo->query(
    'SELECT e.title, COUNT(r.id) AS registrations
     FROM events e LEFT JOIN registrations r ON r.event_id = e.id
     GROUP BY e.id, e.title ORDER BY registrations DESC, e.title LIMIT 6'
)->fetchAll();

$menu = [
    ['id' => 'overview', 'label' => 'Overview', 'roles' => ['admin']],
    ['id' => 'registrations', 'label' => 'Latest Registrations', 'roles' => ['admin']],
    ['id' => 'users', 'label' => 'User Management', 'roles' => ['admin']],
    ['id' => 'audit', 'label' => 'Audit Log', 'roles' => ['admin']]
];
$visibleMenu = array_values(array_filter($menu, fn($item) => in_array($_SESSION['role'], $item['roles'], true)));

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Practical 14 - Admin Dashboard</title>
<style>
*{box-sizing:border-box}
body{margin:0;font-family:Arial,sans-serif;background:#f4f7fb;color:#172033}
.layout{display:flex;min-height:100vh}
.sidebar{width:240px;background:#111827;color:#e5e7eb;padding:24px 16px;flex-shrink:0}
.brand{font-size:20px;font-weight:bold;margin:0 8px 30px;color:white}
.menu a{display:block;color:#cbd5e1;text-decoration:none;padding:12px;border-radius:8px;margin:5px 0}
.menu a:hover,.menu a.active{background:#2563eb;color:white}
main{flex:1;min-width:0}
.topbar{background:white;padding:18px 28px;display:flex;align-items:center;justify-content:space-between;gap:15px;border-bottom:1px solid #e2e8f0}
.content{padding:26px;max-width:1400px;margin:auto}
.muted{color:#64748b}
.cards{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin:20px 0}
.card,.panel{background:white;border:1px solid #e5eaf1;border-radius:14px;padding:20px;box-shadow:0 3px 12px #0f172a08}
.card span{color:#64748b;font-size:14px}.card strong{display:block;font-size:32px;margin-top:10px;color:#1d4ed8}
.grid{display:grid;grid-template-columns:1.2fr .8fr;gap:18px}
h1{font-size:26px;margin:0}h2{font-size:18px;margin-top:0}
.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:560px}
th,td{text-align:left;padding:12px;border-bottom:1px solid #e5eaf1;font-size:14px}
th{background:#f8fafc;color:#475569}
button{border:0;border-radius:7px;padding:8px 11px;cursor:pointer;font-weight:bold}
.toggle{background:#dbeafe;color:#1d4ed8}.danger{background:#fee2e2;color:#991b1b}
.pill{display:inline-block;border-radius:20px;padding:4px 8px;font-size:12px;background:#dcfce7;color:#166534}
.pill.inactive{background:#fee2e2;color:#991b1b}
.bar-row{margin:14px 0}.bar-label{display:flex;justify-content:space-between;gap:10px;font-size:13px;margin-bottom:6px}
.bar{height:9px;background:#e2e8f0;border-radius:9px;overflow:hidden}.bar span{display:block;height:100%;background:#3b82f6}
section{scroll-margin-top:20px;margin-bottom:20px}
.notice{padding:10px 12px;border-radius:8px;margin:12px 0;background:#eff6ff;color:#1d4ed8}
@media(max-width:900px){.cards{grid-template-columns:repeat(2,minmax(0,1fr))}.grid{grid-template-columns:1fr}.sidebar{width:200px}}
@media(max-width:620px){.layout{display:block}.sidebar{width:100%;padding:14px}.brand{margin:0 8px 12px}.menu{display:flex;overflow-x:auto}.menu a{white-space:nowrap}.topbar{padding:16px;align-items:flex-start;flex-direction:column}.content{padding:16px}.cards{gap:10px}.card{padding:14px}.card strong{font-size:26px}}
</style>
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="brand">Admin Console</div>
    <nav class="menu" aria-label="Admin menu">
      <?php foreach ($visibleMenu as $item): ?>
        <a href="#<?= h($item['id']) ?>" class="<?= $item['id'] === 'overview' ? 'active' : '' ?>"><?= h($item['label']) ?></a>
      <?php endforeach; ?>
    </nav>
  </aside>
  <main>
    <header class="topbar">
      <div><h1>Dashboard Overview</h1><span class="muted">Live summary from the database</span></div>
      <div>Welcome, <strong><?= h($_SESSION['name']) ?></strong> · <span class="pill">ADMIN</span> <a href="logout.php">Logout</a></div>
    </header>
    <div class="content">
      <section id="overview">
        <h2>System Statistics</h2>
        <div class="cards">
          <div class="card"><span>Total Users</span><strong><?= $stats['users'] ?></strong></div>
          <div class="card"><span>Total Students</span><strong><?= $stats['students'] ?></strong></div>
          <div class="card"><span>Total Events</span><strong><?= $stats['events'] ?></strong></div>
          <div class="card"><span>Event Registrations</span><strong><?= $stats['registrations'] ?></strong></div>
        </div>
        <div class="grid">
          <div class="panel">
            <h2>Registrations by Event</h2>
            <?php $maxCount = max(1, ...array_map(fn($e) => (int)$e['registrations'], $eventStats)); ?>
            <?php if (!$eventStats): ?><p class="muted">No event data available.</p><?php endif; ?>
            <?php foreach ($eventStats as $event): ?>
              <div class="bar-row">
                <div class="bar-label"><span><?= h($event['title']) ?></span><strong><?= (int)$event['registrations'] ?></strong></div>
                <div class="bar"><span style="width:<?= min(100, (int)$event['registrations'] / $maxCount * 100) ?>%"></span></div>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="panel">
            <h2>Dashboard checks</h2>
            <p>✓ Statistics loaded from MySQL</p>
            <p>✓ Admin-only session access</p>
            <p>✓ Role-based menu items</p>
            <p>✓ Audit log records available</p>
            <p class="muted">Menu options are filtered by the logged-in user's role.</p>
          </div>
        </div>
      </section>

      <section id="registrations" class="panel">
        <h2>Latest Registrations</h2>
        <div class="table-wrap"><table>
          <thead><tr><th>ID</th><th>Student</th><th>Event</th><th>Registered At</th></tr></thead>
          <tbody>
          <?php foreach ($latest as $row): ?>
            <tr><td><?= (int)$row['id'] ?></td><td><?= h($row['student_name']) ?></td><td><?= h($row['event_title']) ?></td><td><?= h($row['registered_at']) ?></td></tr>
          <?php endforeach; ?>
          <?php if (!$latest): ?><tr><td colspan="4">No registrations found.</td></tr><?php endif; ?>
          </tbody>
        </table></div>
      </section>

      <section id="users" class="panel">
        <h2>User Management · Active / Inactive Toggle</h2>
        <p class="muted">You cannot deactivate your own account. Inactive users cannot sign in.</p>
        <div id="userMessage" role="status" class="notice" hidden></div>
        <div class="table-wrap"><table>
          <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
          <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= h($user['name']) ?></td><td><?= h($user['email']) ?></td><td><?= h($user['role']) ?></td>
              <td><span class="pill <?= $user['status'] === 'inactive' ? 'inactive' : '' ?>"><?= h($user['status']) ?></span></td>
              <td>
                <?php if ((int)$user['id'] === $userId): ?>
                  <span class="muted">Current account</span>
                <?php else: ?>
                  <button class="toggle" type="button" data-user-id="<?= (int)$user['id'] ?>" data-next-status="<?= $user['status'] === 'active' ? 'inactive' : 'active' ?>">
                    Set <?= $user['status'] === 'active' ? 'Inactive' : 'Active' ?>
                  </button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table></div>
      </section>

      <section id="audit" class="panel">
        <h2>Recent Audit Log</h2>
        <div class="table-wrap"><table>
          <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Details</th><th>IP Address</th></tr></thead>
          <tbody>
          <?php foreach ($logs as $log): ?>
            <tr><td><?= h($log['created_at']) ?></td><td><?= h($log['user_name'] ?? 'Deleted user') ?></td><td><?= h($log['action']) ?></td><td><?= h($log['details']) ?></td><td><?= h($log['ip_address'] ?? '-') ?></td></tr>
          <?php endforeach; ?>
          <?php if (!$logs): ?><tr><td colspan="5">No audit log entries yet.</td></tr><?php endif; ?>
          </tbody>
        </table></div>
      </section>
      <p class="muted">Practical 14 · PHP, MySQL, RBAC, dynamic menu and audit logging</p>
    </div>
  </main>
</div>
<script>
const csrfToken = <?= json_encode($csrf) ?>;
document.querySelectorAll("[data-user-id]").forEach(button => {
  button.addEventListener("click", async () => {
    const nextStatus = button.dataset.nextStatus;
    if (!confirm(`Set this user to ${nextStatus}?`)) return;

    button.disabled = true;
    const message = document.getElementById("userMessage");
    try {
      const response = await fetch("api/toggle_user.php", {
        method: "POST",
        headers: {"Content-Type": "application/json", "Accept": "application/json"},
        body: JSON.stringify({
          csrf_token: csrfToken,
          user_id: Number(button.dataset.userId),
          status: nextStatus
        })
      });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.message || "Request failed.");
      message.hidden = false;
      message.textContent = result.message;
      location.reload();
    } catch (error) {
      message.hidden = false;
      message.textContent = error.message;
      button.disabled = false;
    }
  });
});
</script>
</body>
</html>
