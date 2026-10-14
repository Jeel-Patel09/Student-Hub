<?php
declare(strict_types=1);

/*
 * StudentHub final integration configuration.
 * Default credentials match a standard local XAMPP installation.
 * Change these values if your MySQL account uses a password.
 */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

const DB_HOST = 'localhost';
const DB_NAME = 'studenthub_final_db';
const DB_USER = 'root';
const DB_PASS = '';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log('StudentHub database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Database connection failed. Start MySQL in XAMPP and import database/studenthub_final_setup.sql.');
}
