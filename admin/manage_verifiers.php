<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_admin();

// Add new verifier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_verifier'])) {
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = clean_input($_POST['password']);
    $confirm_password = clean_input($_POST['confirm_password']);
    
    $errors = [];
    
    if (empty($username)) $errors[] = "Username is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($password)) $errors[] = "Password is required";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    
    if (empty($errors)) {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = "Username already exists";
        } else {
            // Insert new verifier
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, email) VALUES (?, ?, 'verifier', ?)");
            $success = $stmt->execute([$username, $hashed_password, $email]);
            
            if ($success) {
                $_SESSION['success_message'] = "Verifier added successfully.";
            } else {
                $errors[] = "Failed to add verifier.";
            }
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
    }
}

// Delete verifier
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    
    // Prevent deleting own account
    if ($delete_id != $user_id) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'verifier'");
        $success = $stmt->execute([$delete_id]);
        
        if ($success) {
            $_SESSION['success_message'] = "Verifier deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to delete verifier.";
        }
    } else {
        $_SESSION['error_message'] = "You cannot delete your own account.";
    }
    
    header("Location: manage_verifiers.php");
    exit();
}

// Get all verifiers
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'verifier' ORDER BY created_at DESC");
$stmt->execute();
$verifiers = $stmt->fetchAll();

$title = "Manage Verifiers";
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4">Manage Verifiers</h1>
                
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-5 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">Add New Verifier</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <button type="submit" name="add_verifier" class="btn btn-primary">Add Verifier</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">Existing Verifiers</h5>
                            </div>
                            <div class="card-body">
                                <?php if (count($verifiers) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Username</th>
                                                    <th>Email</th>
                                                    <th>Created On</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($verifiers as $verifier): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($verifier['username']); ?></td>
                                                        <td><?php echo htmlspecialchars($verifier['email']); ?></td>
                                                        <td><?php echo date('M j, Y', strtotime($verifier['created_at'])); ?></td>
                                                        <td>
                                                            <a href="manage_verifiers.php?delete=<?php echo $verifier['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete()">Delete</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info mb-0">No verifiers found.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>