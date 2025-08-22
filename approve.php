<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_admin();

if (!isset($_GET['id'])) {
    header("Location: view_verified.php");
    exit();
}

$id = $_GET['id'];

// Get application details
$stmt = $pdo->prepare("SELECT * FROM admissions WHERE id = ? AND status = 'verified'");
$stmt->execute([$id]);
$application = $stmt->fetch();

if (!$application) {
    header("Location: view_verified.php");
    exit();
}

// Approve application
$stmt = $pdo->prepare("UPDATE admissions SET status = 'approved', approved_at = NOW() WHERE id = ?");
$success = $stmt->execute([$id]);

if ($success) {
    // Send approval email
    send_email($application['parent_email'], "Admission Approved", "admission_approved.php", [
        'student_name' => $application['student_name'],
        'serial_no' => $application['serial_no']
    ]);
    
    $_SESSION['success_message'] = "Application approved successfully.";
} else {
    $_SESSION['error_message'] = "Failed to approve application.";
}

header("Location: view_verified.php");
exit();
?>