<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['user_role'] === 'admin' ? 'admin/dashboard.php' : 'verifier/dashboard.php'));
    exit();
}

$error = '';
$success = '';

// Check if token is valid
if (!isset($_GET['token'])) {
    header("Location: login.php");
    exit();
}

$token = $_GET['token'];

// Validate token
$stmt = $pdo->prepare("SELECT pr.*, u.username FROM password_resets pr JOIN users u ON pr.user_id = u.id WHERE pr.token = ? AND pr.expires_at > NOW()");
$stmt->execute([$token]);
$reset_request = $stmt->fetch();

if (!$reset_request) {
    $error = "Invalid or expired reset token.";
}

// Process password reset form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $reset_request) {
    $password = clean_input($_POST['password']);
    $confirm_password = clean_input($_POST['confirm_password']);
    
    if (empty($password)) {
        $error = "Please enter a new password.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Update password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $success = $stmt->execute([$hashed_password, $reset_request['user_id']]);
        
        if ($success) {
            // Delete reset token
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $stmt->execute([$reset_request['user_id']]);
            
            $success = "Password reset successfully. You can now <a href='login.php'>login</a> with your new password.";
        } else {
            $error = "Failed to reset password. Please try again.";
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-key me-2"></i>Reset Password</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($reset_request): ?>
                        <p class="text-center">Resetting password for: <strong><?php echo htmlspecialchars($reset_request['username']); ?></strong></p>
                        
                        <form method="POST" action="">
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                            </div>
                        </form>
                    <?php endif; ?>
                    
                    <div class="text-center mt-3">
                        <a href="login.php">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>