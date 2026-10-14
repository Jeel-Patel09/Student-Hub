-- StudentHub Practical 15: final integrated database
-- Import this file in phpMyAdmin using the SQL tab.
-- It creates a dedicated database and does not drop or overwrite prior practical databases.

CREATE DATABASE IF NOT EXISTS studenthub_final_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studenthub_final_db;

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
  CONSTRAINT fk_final_students_user FOREIGN KEY (user_id) REFERENCES users(id)
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
  UNIQUE KEY uq_final_student_event (student_id, event_id),
  CONSTRAINT fk_final_registration_student FOREIGN KEY (student_id) REFERENCES students(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_final_registration_event FOREIGN KEY (event_id) REFERENCES events(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(100) NOT NULL,
  details VARCHAR(255) NOT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_final_audit_created (created_at),
  CONSTRAINT fk_final_audit_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_messages (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_final_contact_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS feedback (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  rating TINYINT NOT NULL,
  comments VARCHAR(2000) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_final_feedback_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- Local demonstration account: admin@example.com / password
-- Password hash is generated for the literal password "password".
INSERT IGNORE INTO users (id, name, email, password_hash, role, status)
VALUES (1, 'StudentHub Administrator', 'admin@example.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

INSERT IGNORE INTO students (student_code, name, email, course)
VALUES
  ('DEMO001', 'Aarav Patel', 'aarav.demo@example.com', 'Computer Engineering'),
  ('DEMO002', 'Diya Shah', 'diya.demo@example.com', 'Information Technology'),
  ('DEMO003', 'Kabir Desai', 'kabir.demo@example.com', 'Computer Engineering');

INSERT IGNORE INTO events (id, title, event_date, status)
VALUES
  (1, 'Web Development Workshop', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'upcoming'),
  (2, 'Coding Challenge', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'upcoming'),
  (3, 'Tech Seminar', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'completed');
