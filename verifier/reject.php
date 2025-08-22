<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: " . ($_SESSION['user_role'] === 'admin' ? 'admin/view_verified.php' : 'verifier/view_pending.php'));
    exit();
}

$id = $_GET['id'];
$role = $_SESSION['user_role'];

// Get application details
if ($role === 'admin') {
    $stmt = $pdo->prepare("SELECT * FROM admissions WHERE id = ? AND status = 'verified'");
} else {
    $stmt = $pdo->prepare("SELECT * FROM admissions WHERE id = ? AND status = 'pending'");
}

$stmt->execute([$id]);
$application = $stmt->fetch();

if (!$application) {
    header("Location: " . ($role === 'admin' ? 'admin/view_verified.php' : 'verifier/view_pending.php'));
    exit();
}

// Reject application
$stmt = $pdo->prepare("UPDATE admissions SET status = 'rejected' WHERE id = ?");
$success = $stmt->execute([$id]);

if ($success) {
    // Send rejection email
    send_email($application['parent_email'], "Admission Rejected", "admission_rejected.php", [
        'student_name' => $application['student_name'],
        'serial_no' => $application['serial_no']
    ]);
    
    $_SESSION['success_message'] = "Application rejected successfully.";
} else {
    $_SESSION['error_message'] = "Failed to reject application.";
}

header("Location: " . ($role === 'admin' ? 'admin/view_verified.php' : 'verifier/view_pending.php'));
exit();
?>