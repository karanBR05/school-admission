<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    header("Location: " . ($_SESSION['user_role'] === 'admin' ? 'admin/dashboard.php' : 'verifier/dashboard.php'));
    exit();
}

$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        // Check user credentials
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            
            // Redirect based on role
            header("Location: " . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'verifier/dashboard.php'));
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Please enter both username and password";
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-lock me-2"></i>Admin Login</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="forgot_password.php">Forgot Password?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>