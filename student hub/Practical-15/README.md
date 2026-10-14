# StudentHub — Practical 15 Final Integration

StudentHub is a local full-stack student portal assembled for Practical 15. The integrated application uses HTML, CSS, JavaScript, PHP, MySQL, sessions, prepared SQL statements, CSRF checks, role-based access, an AJAX student-management API and an administrator dashboard.

## Included work

- **Integrated root portal:** Home, About, Register, Login, Dashboard, Events, Profile, Contact, Feedback and FAQ.
- **Student management:** Administrator-only page with AJAX create/delete operations.
- **Admin dashboard:** User status management, latest event registrations, event statistics and audit history.
- **Earlier practicals:** `practicals/Practical-3` through `practicals/Practical-14` are preserved as individual learning/practical modules. `legacy/static-site` keeps the earlier static Student-Hub site files for reference.
- **Database:** One dedicated database for the final integrated portal. Earlier practical folders may still use their own database names when run independently.

## Requirements

- XAMPP for Windows with Apache, MySQL/MariaDB and PHP 8.x
- A web browser
- Git (optional, for GitHub submission)

## Local installation (XAMPP)

1. Extract the `StudentHub_Final` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin/`.
4. Select the **SQL** tab and import/run `StudentHub_Final/database/studenthub_final_setup.sql`.
5. Check `StudentHub_Final/config.php`. The defaults are `localhost`, database `studenthub_final_db`, username `root`, and an empty password. Change these only if your local MySQL account uses different credentials.
6. Open `http://localhost/StudentHub_Final/`.

If you use a different folder name, change the URL to match that folder name.

## Demo administrator account

- Email: `admin@example.com`
- Password: `password`

This is only a local demonstration account. Change it before sharing the application or deploying it to any public server. Do not reuse the demo password.

## Main routes

- `index.php` — portal home
- `register.php` / `login.php` / `logout.php` — account access
- `dashboard.php` — signed-in dashboard
- `events.php` — event listing and registration
- `profile.php` — profile update
- `students.php` — administrator-only AJAX student management
- `admin/index.php` — administrator dashboard
- `api/students.php` — JSON API, administrator-only
- `contact.php` / `feedback.php` / `faq.php` — contact, feedback and help

## Security implemented in the integrated portal

- Passwords are stored with PHP `password_hash()` and checked using `password_verify()`.
- PDO prepared statements are used for database queries with user input.
- Output is escaped with `htmlspecialchars()`.
- Login regenerates the session ID.
- CSRF tokens protect state-changing forms and AJAX requests.
- Administrator-only pages and APIs check the session role.
- Account status is checked during login.
- Audit entries are stored for key account and data actions.
- The AJAX student API validates input and returns JSON errors without exposing raw database errors.

This is a student project, not a security-certified production service. For public hosting, use HTTPS, strong administrator credentials, secure cookie settings, production error logging, backups and server-side configuration outside the web root.

## Virtual-host option (optional)

For local Apache virtual-host deployment, enable `httpd-vhosts.conf` in XAMPP's Apache configuration and add a host similar to this (adjust the path to your installation):

```apache
<VirtualHost *:80>
    ServerName studenthub.local
    DocumentRoot "C:/xampp/htdocs/StudentHub_Final"
    <Directory "C:/xampp/htdocs/StudentHub_Final">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add this line to the Windows hosts file as administrator:

```text
127.0.0.1 studenthub.local
```

Restart Apache and visit `http://studenthub.local/`. This is optional; `http://localhost/StudentHub_Final/` is enough for the practical.

## GitHub submission

1. Review the project and test it locally first.
2. Do not commit real passwords, private credentials, server logs or uploaded user data.
3. From the `StudentHub_Final` folder, run:

```bash
git init
git add .
git commit -m "Complete StudentHub practical 15 integration"
git branch -M main
git remote add origin https://github.com/YOUR-USERNAME/YOUR-REPOSITORY.git
git push -u origin main
```

Replace the example repository URL with your own GitHub repository URL. If Git is already initialized, do not run `git init` again; inspect `git status` first.

## Practical 15 test report

Use `docs/TEST_REPORT.md` to record the actual results on your XAMPP computer. The code was checked for PHP syntax in this build environment, but the MySQL-backed flows still need to be run on your own XAMPP setup; do not mark a test as passed until you observe it working.

## Screenshots to capture for submission

1. Home page
2. Registration success
3. Successful login and dashboard
4. Events list and successful event registration
5. Profile update
6. Admin dashboard and user management
7. AJAX student add/delete
8. phpMyAdmin showing the final database tables
9. GitHub repository page and final commit

## Practical folders

The `practicals` directory preserves Practicals 3–14 for reference. Some individual practicals are intentionally standalone exercises and use their own database names/configuration; the integrated root portal uses `studenthub_final_db`. Do not run every historical SQL script against the final database.
