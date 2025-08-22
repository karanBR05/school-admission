<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_admin();

// Get statistics
$stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM admissions GROUP BY status");
$stmt->execute();
$status_counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Get recent verified applications
$stmt = $pdo->prepare("SELECT * FROM admissions WHERE status = 'verified' ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$recent_applications = $stmt->fetchAll();

$title = "Admin Dashboard";
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4">Admin Dashboard</h1>
                
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $status_counts['pending'] ?? 0; ?></h5>
                                <p class="card-text">Pending Applications</p>
                                <i class="fas fa-clock fa-2x opacity-50 float-end"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $status_counts['verified'] ?? 0; ?></h5>
                                <p class="card-text">Verified Applications</p>
                                <i class="fas fa-check-circle fa-2x opacity-50 float-end"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $status_counts['approved'] ?? 0; ?></h5>
                                <p class="card-text">Approved Applications</p>
                                <i class="fas fa-thumbs-up fa-2x opacity-50 float-end"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $status_counts['rejected'] ?? 0; ?></h5>
                                <p class="card-text">Rejected Applications</p>
                                <i class="fas fa-times-circle fa-2x opacity-50 float-end"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Verified Applications -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">Recent Verified Applications</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($recent_applications) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Serial No</th>
                                            <th>Student Name</th>
                                            <th>Parent Mobile</th>
                                            <th>Applied On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_applications as $application): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($application['serial_no']); ?></td>
                                                <td><?php echo htmlspecialchars($application['student_name']); ?></td>
                                                <td><?php echo htmlspecialchars($application['parent_mobile']); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($application['created_at'])); ?></td>
                                                <td>
                                                    <a href="view_verified.php?id=<?php echo $application['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                                    <a href="approve.php?id=<?php echo $application['id']; ?>" class="btn btn-sm btn-success" onclick="return confirmAction('approve')">Approve</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="view_verified.php" class="btn btn-info">View All Verified Applications</a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">No verified applications found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>