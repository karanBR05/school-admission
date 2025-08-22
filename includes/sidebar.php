<?php
// Sidebar navigation based on user role
?>
<div class="sidebar">
    <nav class="nav flex-column">
        <?php if ($user_role === 'admin'): ?>
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i>
                <span>Dashboard</span>
            </a>
            <a class="nav-link" href="view_verified.php">
                <i class="fas fa-check-circle me-2"></i>
                <span>Verified Applications</span>
            </a>
            <a class="nav-link" href="manage_verifiers.php">
                <i class="fas fa-users me-2"></i>
                <span>Manage Verifiers</span>
            </a>
        <?php elseif ($user_role === 'verifier'): ?>
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i>
                <span>Dashboard</span>
            </a>
            <a class="nav-link" href="view_pending.php">
                <i class="fas fa-clock me-2"></i>
                <span>Pending Applications</span>
            </a>
        <?php endif; ?>
        <a class="nav-link" href="../logout.php" onclick="return confirmLogout()">
            <i class="fas fa-sign-out-alt me-2"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>