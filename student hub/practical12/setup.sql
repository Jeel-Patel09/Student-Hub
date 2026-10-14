CREATE DATABASE IF NOT EXISTS event_db;
USE event_db;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(150) NOT NULL,
    category ENUM('Technical','Cultural','Sports','Workshop','Seminar') NOT NULL,
    status ENUM('open','closed') NOT NULL DEFAULT 'open',
    poster VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_username VARCHAR(50) NOT NULL,
    event_id INT NULL,
    action VARCHAR(20) NOT NULL,
    details VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO events (title, description, event_date, venue, category, status) VALUES
('Hackathon 2026',       '24-hour coding hackathon open to all departments.',  '2026-11-15', 'Main Auditorium',  'Technical', 'open'),
('Annual Cultural Fest', 'Music, dance and drama performances by students.',   '2026-12-05', 'Open Air Theatre', 'Cultural',  'open'),
('Inter-College Cricket','Knockout cricket tournament between colleges.',       '2026-11-22', 'College Ground',   'Sports',    'open'),
('PHP Workshop',         'Hands-on workshop on PHP and MySQL for beginners.',   '2026-10-20', 'Computer Lab 3',   'Workshop',  'closed'),
('AI Awareness Seminar', 'Expert talk on the impact of AI in education.',       '2026-10-12', 'Seminar Hall',     'Seminar',   'closed'),
('Web Design Contest',   'Design and build a responsive web page in 3 hours.',  '2026-11-30', 'Computer Lab 1',   'Technical', 'open');