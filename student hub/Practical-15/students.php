<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
require_admin();
$pageTitle = 'Student management';
$students = $pdo->query('SELECT id,student_code,name,email,course,created_at FROM students ORDER BY id DESC')->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<h1>Student management</h1>
<p class="muted">This page uses the StudentHub AJAX API for database CRUD operations. Only administrators can access it.</p>
<div class="panel">
  <h2>Add student</h2>
  <form id="student-form">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="form-row">
      <div><label for="student-name">Name</label><input id="student-name" name="name" required maxlength="100"></div>
      <div><label for="student-email">Email</label><input id="student-email" name="email" type="email" required maxlength="150"></div>
      <div><label for="student-course">Course</label><input id="student-course" name="course" required maxlength="100"></div>
    </div>
    <p><button type="submit">Add student</button></p>
    <p id="api-message" role="status" aria-live="polite"></p>
  </form>
</div>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Student code</th><th>Name</th><th>Email</th><th>Course</th><th>Action</th></tr></thead><tbody id="students-body">
<?php foreach($students as $s): ?><tr data-id="<?= (int)$s['id'] ?>"><td><?= (int)$s['id'] ?></td><td><?= e($s['student_code']) ?></td><td><?= e($s['name']) ?></td><td><?= e($s['email']) ?></td><td><?= e($s['course']) ?></td><td><button type="button" class="delete-student" data-id="<?= (int)$s['id'] ?>">Delete</button></td></tr><?php endforeach; ?>
</tbody></table></div>
<script>
const studentCsrf = <?= json_encode(csrf_token()) ?>;
const msg = document.getElementById('api-message');
async function apiRequest(method, body={}) {
  const response = await fetch('api/students.php', {
    method, headers: {'Content-Type':'application/json', 'X-CSRF-Token': studentCsrf},
    body: method === 'GET' ? undefined : JSON.stringify({...body, csrf_token: studentCsrf})
  });
  const data = await response.json();
  if (!response.ok || !data.success) throw new Error(data.message || 'Request failed');
  return data;
}
function safeCell(value) { const td=document.createElement('td'); td.textContent=value ?? ''; return td; }
function addRow(s) {
  const tr=document.createElement('tr'); tr.dataset.id=s.id;
  [s.id,s.student_code,s.name,s.email,s.course].forEach(v=>tr.appendChild(safeCell(v)));
  const td=document.createElement('td'); const b=document.createElement('button');
  b.type='button'; b.className='delete-student'; b.dataset.id=s.id; b.textContent='Delete'; td.appendChild(b); tr.appendChild(td);
  document.getElementById('students-body').prepend(tr);
}
document.getElementById('student-form').addEventListener('submit', async e=>{
  e.preventDefault(); msg.textContent='Saving…';
  const form=new FormData(e.currentTarget); const body=Object.fromEntries(form.entries());
  try { const data=await apiRequest('POST',body); addRow(data.student); e.currentTarget.reset(); msg.textContent=data.message; }
  catch(err){msg.textContent=err.message;}
});
document.getElementById('students-body').addEventListener('click', async e=>{
  const b=e.target.closest('.delete-student'); if(!b) return;
  if(!confirm('Delete this student record?')) return;
  try { const data=await apiRequest('DELETE',{id:Number(b.dataset.id)}); b.closest('tr').remove(); msg.textContent=data.message; }
  catch(err){msg.textContent=err.message;}
});
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
