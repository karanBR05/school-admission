<?php
// Start session and include config
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Initialize variables
$errors = [];
$success = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and process the form data
    $student_name = clean_input($_POST['student_name']);
    $father_name = clean_input($_POST['father_name']);
    $mother_name = clean_input($_POST['mother_name']);
    $dob = clean_input($_POST['dob']);
    $parent_mobile = clean_input($_POST['parent_mobile']);
    $parent_email = clean_input($_POST['parent_email']);
    
    // Validate required fields
    if (empty($student_name)) $errors[] = "Student name is required";
    if (empty($father_name)) $errors[] = "Father's name is required";
    if (empty($mother_name)) $errors[] = "Mother's name is required";
    if (empty($dob)) $errors[] = "Date of birth is required";
    if (empty($parent_mobile)) $errors[] = "Parent mobile number is required";
    if (empty($parent_email)) $errors[] = "Parent email is required";
    
    // Validate mobile number
    if (!empty($parent_mobile) && !preg_match('/^[0-9]{10}$/', $parent_mobile)) {
        $errors[] = "Mobile number must be 10 digits";
    }
    
    // Validate email
    if (!empty($parent_email) && !filter_var($parent_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate terms and conditions
    if (!isset($_POST['terms'])) {
        $errors[] = "You must accept the terms and conditions";
    }
    
    // Process file uploads if no errors
    if (empty($errors)) {
        $upload_errors = [];
        $file_paths = [];
        
        // Student photo upload
        if (!empty($_FILES['student_photo']['name'])) {
            $photo_result = handle_file_upload('student_photo', 'photos', ['jpg', 'jpeg'], 50);
            if ($photo_result['success']) {
                $file_paths['student_photo'] = $photo_result['file_path'];
            } else {
                $upload_errors[] = $photo_result['error'];
            }
        } else {
            $upload_errors[] = "Student photo is required";
        }
        
        // Residence certificate upload
        if (!empty($_FILES['residence_certificate']['name'])) {
            $residence_result = handle_file_upload('residence_certificate', 'residence', ['jpg', 'jpeg', 'pdf'], 200);
            if ($residence_result['success']) {
                $file_paths['residence_certificate'] = $residence_result['file_path'];
            } else {
                $upload_errors[] = $residence_result['error'];
            }
        } else {
            $upload_errors[] = "Residence certificate is required";
        }
        
        // Father's Aadhaar front
        if (!empty($_FILES['father_aadhar_front']['name'])) {
            $father_front_result = handle_file_upload('father_aadhar_front', 'father_aadhar', ['jpg', 'jpeg'], 200);
            if ($father_front_result['success']) {
                $file_paths['father_aadhar_front'] = $father_front_result['file_path'];
            } else {
                $upload_errors[] = $father_front_result['error'];
            }
        } else {
            $upload_errors[] = "Father's Aadhaar front is required";
        }
        
        // Father's Aadhaar back
        if (!empty($_FILES['father_aadhar_back']['name'])) {
            $father_back_result = handle_file_upload('father_aadhar_back', 'father_aadhar', ['jpg', 'jpeg'], 200);
            if ($father_back_result['success']) {
                $file_paths['father_aadhar_back'] = $father_back_result['file_path'];
            } else {
                $upload_errors[] = $father_back_result['error'];
            }
        } else {
            $upload_errors[] = "Father's Aadhaar back is required";
        }
        
        // Mother's Aadhaar front
        if (!empty($_FILES['mother_aadhar_front']['name'])) {
            $mother_front_result = handle_file_upload('mother_aadhar_front', 'mother_aadhar', ['jpg', 'jpeg'], 200);
            if ($mother_front_result['success']) {
                $file_paths['mother_aadhar_front'] = $mother_front_result['file_path'];
            } else {
                $upload_errors[] = $mother_front_result['error'];
            }
        } else {
            $upload_errors[] = "Mother's Aadhaar front is required";
        }
        
        // Mother's Aadhaar back
        if (!empty($_FILES['mother_aadhar_back']['name'])) {
            $mother_back_result = handle_file_upload('mother_aadhar_back', 'mother_aadhar', ['jpg', 'jpeg'], 200);
            if ($mother_back_result['success']) {
                $file_paths['mother_aadhar_back'] = $mother_back_result['file_path'];
            } else {
                $upload_errors[] = $mother_back_result['error'];
            }
        } else {
            $upload_errors[] = "Mother's Aadhaar back is required";
        }
        
        // If no upload errors, save to database
        if (empty($upload_errors)) {
            // Generate serial number
            $serial_no = generate_serial_number();
            
            // Insert into database
            $sql = "INSERT INTO admissions (serial_no, student_name, father_name, mother_name, dob, parent_mobile, parent_email, student_photo, residence_certificate, father_aadhar_front, father_aadhar_back, mother_aadhar_front, mother_aadhar_back, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                $serial_no, 
                $student_name, 
                $father_name, 
                $mother_name, 
                $dob, 
                $parent_mobile, 
                $parent_email, 
                $file_paths['student_photo'], 
                $file_paths['residence_certificate'], 
                $file_paths['father_aadhar_front'], 
                $file_paths['father_aadhar_back'], 
                $file_paths['mother_aadhar_front'], 
                $file_paths['mother_aadhar_back']
            ]);
            
            if ($success) {
                // Send confirmation email
                send_email($parent_email, "Admission Application Received", "admission_success.php", [
                    'student_name' => $student_name,
                    'serial_no' => $serial_no
                ]);
                
                // Clear form data
                $_POST = [];
                
                // Set success message
                $_SESSION['success_message'] = "Admission form submitted successfully. Your serial number is: $serial_no";
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Failed to save application. Please try again.";
            }
        } else {
            $errors = array_merge($errors, $upload_errors);
        }
    }
}

// Include header
include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center">School Admission Form</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" id="admissionForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_name">Student Full Name *</label>
                                    <input type="text" class="form-control" id="student_name" name="student_name" value="<?php echo isset($_POST['student_name']) ? htmlspecialchars($_POST['student_name']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="father_name">Father's Name *</label>
                                    <input type="text" class="form-control" id="father_name" name="father_name" value="<?php echo isset($_POST['father_name']) ? htmlspecialchars($_POST['father_name']) : ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mother_name">Mother's Name *</label>
                                    <input type="text" class="form-control" id="mother_name" name="mother_name" value="<?php echo isset($_POST['mother_name']) ? htmlspecialchars($_POST['mother_name']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dob">Date of Birth *</label>
                                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent_mobile">Parent's Mobile Number *</label>
                                    <input type="tel" class="form-control" id="parent_mobile" name="parent_mobile" pattern="[0-9]{10}" value="<?php echo isset($_POST['parent_mobile']) ? htmlspecialchars($_POST['parent_mobile']) : ''; ?>" required>
                                    <small class="form-text text-muted">10 digits only</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent_email">Parent's Email *</label>
                                    <input type="email" class="form-control" id="parent_email" name="parent_email" value="<?php echo isset($_POST['parent_email']) ? htmlspecialchars($_POST['parent_email']) : ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_photo">Student Photo (JPG, max 50KB) *</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="student_photo" name="student_photo" accept=".jpg,.jpeg" required>
                                        <label class="custom-file-label" for="student_photo">Choose file</label>
                                    </div>
                                    <small class="form-text text-muted">JPG format only, maximum 50KB</small>
                                    <div class="camera-container mt-2 d-none">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="openCamera">Use Camera</button>
                                        <video id="cameraView" class="d-none mt-2" width="200" height="150" autoplay></video>
                                        <button type="button" class="btn btn-sm btn-success mt-2 d-none" id="capturePhoto">Capture</button>
                                        <canvas id="photoCanvas" class="d-none"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="residence_certificate">Residence Certificate (PDF/JPG, max 200KB) *</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="residence_certificate" name="residence_certificate" accept=".jpg,.jpeg,.pdf" required>
                                        <label class="custom-file-label" for="residence_certificate">Choose file</label>
                                    </div>
                                    <small class="form-text text-muted">PDF or JPG format, maximum 200KB</small>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5>Father's Aadhaar Card</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="father_aadhar_front">Front Side (JPG, max 200KB) *</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="father_aadhar_front" name="father_aadhar_front" accept=".jpg,.jpeg" required>
                                        <label class="custom-file-label" for="father_aadhar_front">Choose file</label>
                                    </div>
                                    <small class="form-text text-muted">JPG format only, maximum 200KB</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="father_aadhar_back">Back Side (JPG, max 200KB) *</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="father_aadhar_back" name="father_aadhar_back" accept=".jpg,.jpeg" required>
                                        <label class="custom-file-label" for="father_aadhar_back">Choose file</label>
                                    </div>
                                    <small class="form-text text-muted">JPG format only, maximum 200KB</small>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mt-4">Mother's Aadhaar Card</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mother_aadhar_front">Front Side (JPG, max 200KB) *</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="mother_aadhar_front" name="mother_aadhar_front" accept=".jpg,.jpeg" required>
                                        <label class="custom-file-label" for="mother_aadhar_front">Choose file</label>
                                    </div>
                                    <small class="form-text text-muted">JPG format only, maximum 200KB</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mother_aadhar_back">Back Side (JPG, max 200KB) *</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="mother_aadhar_back" name="mother_aadhar_back" accept=".jpg,.jpeg" required>
                                        <label class="custom-file-label" for="mother_aadhar_back">Choose file</label>
                                    </div>
                                    <small class="form-text text-muted">JPG format only, maximum 200KB</small>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" <?php echo isset($_POST['terms']) ? 'checked' : ''; ?> required>
                            <label class="form-check-label" for="terms">I agree to the terms and conditions *</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Submit Application</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>