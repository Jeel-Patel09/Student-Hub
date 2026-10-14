PRACTICAL 14: ADMIN DASHBOARD WITH RBAC, DYNAMIC MENU AND AUDIT LOG

FILES
- index.php: dashboard statistics, registrations, user management and audit log
- login.php / logout.php: session login/logout
- api/db.php: PDO database connection
- api/auth.php: administrator access check
- api/audit.php: audit logging helper
- api/toggle_user.php: protected active/inactive status endpoint
- practical14_setup.sql: database, tables and sample records

INSTALL
1. Extract the folder practical14_admin_dashboard into C:\xampp\htdocs\
2. Start Apache and MySQL from XAMPP.
3. Open http://localhost/phpmyadmin/
4. Select the SQL tab at server level and execute practical14_setup.sql.
5. If your MySQL username/password differ from root/empty, edit api/db.php.
6. Open http://localhost/practical14_admin_dashboard/login.php

DEMO ADMIN LOGIN
Email: admin@example.com
Password: password

IMPORTANT
- This is a local educational demo. Change the demo password before any real deployment.
- The SQL uses practical14_db and does not modify your other practical databases.
- Re-running the setup script preserves existing demo rows through INSERT IGNORE.
- Dashboard access is restricted to active users whose role is admin.
- User status changes are protected by an admin session and CSRF token and are written to audit_logs.
