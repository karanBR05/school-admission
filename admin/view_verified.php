<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_admin();

// Get all verified applications
$stmt = $pdo->prepare("SELECT * FROM admissions WHERE status = 'verified' ORDER BY created_at DESC");
$stmt->execute();
$applications = $stmt->fetchAll();

$title = "Verified Applications";
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Verified Applications</h1>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <?php if (count($applications) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="applicationsTable">
                                    <thead>
                                        <tr>
                                            <th>Serial No</th>
                                            <th>Student Name</th>
                                            <th>Father's Name</th>
                                            <th>Parent Mobile</th>
                                            <th>Parent Email</th>
                                            <th>Applied On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applications as $application): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($application['serial_no']); ?></td>
                                                <td><?php echo htmlspecialchars($application['student_name']); ?></td>
                                                <td><?php echo htmlspecialchars($application['father_name']); ?></td>
                                                <td><?php echo htmlspecialchars($application['parent_mobile']); ?></td>
                                                <td><?php echo htmlspecialchars($application['parent_email']); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($application['created_at'])); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $application['id']; ?>">
                                                        View
                                                    </button>
                                                    <a href="approve.php?id=<?php echo $application['id']; ?>" class="btn btn-sm btn-success" onclick="return confirmAction('approve')">Approve</a>
                                                    <a href="reject.php?id=<?php echo $application['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirmAction('reject')">Reject</a>
                                                </td>
                                            </tr>
                                            
                                            <!-- View Modal -->
                                            <div class="modal fade" id="viewModal<?php echo $application['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Application Details - <?php echo htmlspecialchars($application['serial_no']); ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <p><strong>Student Name:</strong> <?php echo htmlspecialchars($application['student_name']); ?></p>
                                                                    <p><strong>Father's Name:</strong> <?php echo htmlspecialchars($application['father_name']); ?></p>
                                                                    <p><strong>Mother's Name:</strong> <?php echo htmlspecialchars($application['mother_name']); ?></p>
                                                                    <p><strong>Date of Birth:</strong> <?php echo date('M j, Y', strtotime($application['dob'])); ?></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p><strong>Parent Mobile:</strong> <?php echo htmlspecialchars($application['parent_mobile']); ?></p>
                                                                    <p><strong>Parent Email:</strong> <?php echo htmlspecialchars($application['parent_email']); ?></p>
                                                                    <p><strong>Applied On:</strong> <?php echo date('M j, Y H:i', strtotime($application['created_at'])); ?></p>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row mt-3">
                                                                <div class="col-12">
                                                                    <h6>Documents:</h6>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        <a href="../<?php echo $application['student_photo']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">Student Photo</a>
                                                                        <a href="../<?php echo $application['residence_certificate']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">Residence Certificate</a>
                                                                        <a href="../<?php echo $application['father_aadhar_front']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">Father Aadhaar Front</a>
                                                                        <a href="../<?php echo $application['father_aadhar_back']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">Father Aadhaar Back</a>
                                                                        <a href="../<?php echo $application['mother_aadhar_front']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">Mother Aadhaar Front</a>
                                                                        <a href="../<?php echo $application['mother_aadhar_back']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">Mother Aadhaar Back</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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