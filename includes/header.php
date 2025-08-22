<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Admission ERP System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="bg-primary text-white py-3 shadow">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <?php echo SCHOOL_NAME; ?> Admission Portal
                </h1>
                <div>
                    <?php if (is_logged_in()): ?>
                        <span class="me-3">Welcome, <?php echo $_SESSION['username']; ?></span>
                        <a href="../logout.php" class="btn btn-sm btn-outline-light" onclick="return confirmLogout()">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-sm btn-outline-light">
                            <i class="fas fa-sign-in-alt me-1"></i> Admin Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>