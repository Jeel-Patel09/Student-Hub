PRACTICAL 13: PHP JSON API AND FETCH-BASED CRUD

FILES
- index.php
- api/db.php
- api/students.php
- practical13_setup.sql

INSTALLATION
1. Copy the entire practical13_php_json_crud folder into C:\xampp\htdocs\
2. Start Apache and MySQL from XAMPP Control Panel.
3. Open http://localhost/phpmyadmin/
4. Open the SQL tab and run practical13_setup.sql.
5. If your MySQL credentials are not root with an empty password, edit api/db.php.
6. Open http://localhost/practical13_php_json_crud/

API
GET    api/students.php       List students
POST   api/students.php       Add student (JSON body: name, email, course)
PUT    api/students.php       Update student (JSON body: id, name, email, course)
DELETE api/students.php       Delete student (JSON body: id)

NOTES
- This project uses the separate database practical13_db.
- It does not use or delete student_api or student_db.
- Run the SQL script only if you want to create/seed this practical's database.
- The SQL script uses INSERT IGNORE to avoid duplicate sample emails on reruns.
