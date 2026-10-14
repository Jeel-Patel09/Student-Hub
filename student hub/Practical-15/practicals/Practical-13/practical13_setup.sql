CREATE DATABASE IF NOT EXISTS practical13_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE practical13_db;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    course VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO students (name, email, course)
VALUES
('Aarav Patel', 'aarav@example.com', 'Computer Engineering'),
('Diya Shah', 'diya@example.com', 'Information Technology');
