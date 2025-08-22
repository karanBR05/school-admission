<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'school_admission');
define('DB_USER', 'root');
define('DB_PASS', 'Karan@1903');

// File upload paths
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/');
define('PHOTO_PATH', UPLOAD_PATH . 'photos/');
define('RESIDENCE_PATH', UPLOAD_PATH . 'residence/');
define('FATHER_AADHAR_PATH', UPLOAD_PATH . 'father_aadhar/');
define('MOTHER_AADHAR_PATH', UPLOAD_PATH . 'mother_aadhar/');

// Create directories if they don't exist
if (!file_exists(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0777, true);
if (!file_exists(PHOTO_PATH)) mkdir(PHOTO_PATH, 0777, true);
if (!file_exists(RESIDENCE_PATH)) mkdir(RESIDENCE_PATH, 0777, true);
if (!file_exists(FATHER_AADHAR_PATH)) mkdir(FATHER_AADHAR_PATH, 0777, true);
if (!file_exists(MOTHER_AADHAR_PATH)) mkdir(MOTHER_AADHAR_PATH, 0777, true);

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_email@gmail.com');
define('SMTP_PASS', 'your_app_password');
define('FROM_EMAIL', 'noreply@school.com');
define('FROM_NAME', 'School Admission System');

// School information
define('SCHOOL_NAME', 'ABC Public School');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>