CREATE DATABASE IF NOT EXISTS practical14_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE practical14_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','staff','student') NOT NULL DEFAULT 'student',
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  student_code VARCHAR(30) NULL UNIQUE,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  course VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  event_date DATE NOT NULL,
  status ENUM('upcoming','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS registrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  event_id INT NOT NULL,
  registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_student_event (student_id, event_id),
  CONSTRAINT fk_reg_student FOREIGN KEY (student_id) REFERENCES students(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_reg_event FOREIGN KEY (event_id) REFERENCES events(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(100) NOT NULL,
  details VARCHAR(255) NOT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_audit_created (created_at),
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- Demo login account: admin@example.com / password
-- Replace this hash if you choose a different password.
INSERT IGNORE INTO users (name, email, password_hash, role, status)
VALUES (
  'Practical Admin',
  'admin@example.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'admin',
  'active'
);

INSERT IGNORE INTO students (student_code, name, email, course)
VALUES
('STU001', 'Aarav Patel', 'aarav.student@example.com', 'Computer Engineering'),
('STU002', 'Diya Shah', 'diya.student@example.com', 'Information Technology'),
('STU003', 'Kabir Desai', 'kabir.student@example.com', 'Computer Engineering');

INSERT IGNORE INTO events (id, title, event_date, status)
VALUES
(1, 'Web Development Workshop', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'upcoming'),
(2, 'Coding Challenge', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'upcoming'),
(3, 'Tech Seminar', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'completed');

INSERT IGNORE INTO registrations (student_id, event_id)
VALUES (1,1), (2,1), (3,2), (1,2);
