# Deployment notes

- Local target: XAMPP Apache + MySQL/MariaDB.
- Final database: `studenthub_final_db` (created by `database/studenthub_final_setup.sql`).
- Default local configuration: edit `config.php`.
- Optional virtual-host example and hosts-file entry are in `README.md`.
- The `practicals/` folders preserve individual exercises. Some intentionally use their own databases, so only import a historical practical SQL file when running that standalone exercise.
- For a real hosted server, remove the demo credentials and apply HTTPS, secure cookie settings, environment-based secrets, backups and production error handling.
