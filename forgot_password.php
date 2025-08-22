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

// Process forgot password form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email']);
    
    if (!empty($email)) {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete any existing tokens
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            
            // Insert new token
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
            $success = $stmt->execute([$user['id'], $token, $expires]);
            
            if ($success) {
                // Send reset email
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
                
                $email_body = "Hello " . $user['username'] . ",\n\n";
                $email_body .= "You requested a password reset. Click the link below to reset your password:\n";
                $email_body .= $reset_link . "\n\n";
                $email_body .= "This link will expire in 1 hour.\n\n";
                $email_body .= "If you didn't request this reset, please ignore this email.\n";
                
                // In a real application, use PHPMailer or similar
                mail($email, "Password Reset Request", $email_body);
                
                $success = "Password reset instructions have been sent to your email.";
            } else {
                $error = "Failed to process reset request. Please try again.";
            }
        } else {
            $error = "No account found with that email address.";
        }
    } else {
        $error = "Please enter your email address.";
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-key me-2"></i>Forgot Password</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="login.php">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>