<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_verifier();

if (!isset($_GET['id'])) {
    header("Location: view_pending.php");
    exit();
}

$id = $_GET['id'];

// Get application details
$stmt = $pdo->prepare("SELECT * FROM admissions WHERE id = ? AND status = 'pending'");
$stmt->execute([$id]);
$application = $stmt->fetch();

if (!$application) {
    header("Location: view_pending.php");
    exit();
}

// Verify application
$stmt = $pdo->prepare("UPDATE admissions SET status = 'verified', verified_at = NOW() WHERE id = ?");
$success = $stmt->execute([$id]);

if ($success) {
    $_SESSION['success_message'] = "Application verified successfully.";
} else {
    $_SESSION['error_message'] = "Failed to verify application.";
}

header("Location: view_pending.php");
exit();
?>