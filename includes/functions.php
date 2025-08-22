<?php
// Input cleaning function
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// File upload handling function
function handle_file_upload($field_name, $folder, $allowed_types, $max_size_kb) {
    $file = $_FILES[$field_name];
    $result = ['success' => false, 'file_path' => '', 'error' => ''];
    
    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = "File upload error: " . $file['error'];
        return $result;
    }
    
    // Check file size
    $max_size = $max_size_kb * 1024; // Convert to bytes
    if ($file['size'] > $max_size) {
        $result['error'] = "File size exceeds maximum allowed size of " . $max_size_kb . "KB";
        return $result;
    }
    
    // Check file type
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        $result['error'] = "Only " . implode(', ', $allowed_types) . " files are allowed";
        return $result;
    }
    
    // Generate unique filename
    $unique_name = uniqid() . '_' . time() . '.' . $file_ext;
    
    // Determine upload path based on folder
    switch ($folder) {
        case 'photos':
            $upload_path = PHOTO_PATH . $unique_name;
            $web_path = 'uploads/photos/' . $unique_name;
            break;
        case 'residence':
            $upload_path = RESIDENCE_PATH . $unique_name;
            $web_path = 'uploads/residence/' . $unique_name;
            break;
        case 'father_aadhar':
            $upload_path = FATHER_AADHAR_PATH . $unique_name;
            $web_path = 'uploads/father_aadhar/' . $unique_name;
            break;
        case 'mother_aadhar':
            $upload_path = MOTHER_AADHAR_PATH . $unique_name;
            $web_path = 'uploads/mother_aadhar/' . $unique_name;
            break;
        default:
            $upload_path = UPLOAD_PATH . $unique_name;
            $web_path = 'uploads/' . $unique_name;
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $result['success'] = true;
        $result['file_path'] = $web_path;
    } else {
        $result['error'] = "Failed to move uploaded file";
    }
    
    return $result;
}

// Generate serial number
function generate_serial_number() {
    global $pdo;
    
    // Get current year
    $year = date('Y');
    
    // Get count of admissions this year
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admissions WHERE YEAR(created_at) = ?");
    $stmt->execute([$year]);
    $count = $stmt->fetchColumn();
    
    // Generate serial number
    $serial_no = 'SCH/' . $year . '/' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    
    return $serial_no;
}

// Email sending function
function send_email($to, $subject, $template, $data = []) {
    // Extract variables from data array
    extract($data);
    
    // Start output buffering to capture template content
    ob_start();
    include dirname(__DIR__) . '/emails/' . $template;
    $message = ob_get_clean();
    
    // For now, we'll just log the email (in a real application, you would use PHPMailer or similar)
    $log_file = dirname(__DIR__) . '/email_log.txt';
    $log_content = "To: $to\nSubject: $subject\nMessage:\n$message\n\n";
    file_put_contents($log_file, $log_content, FILE_APPEND);
    
    // In a real implementation, you would use PHPMailer or mail() function here
    // For this example, we'll just return true
    return true;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Check if user has specific role
function has_role($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

// Redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header("Location: ../login.php");
        exit();
    }
}

// Redirect if not admin
function require_admin() {
    require_login();
    if (!has_role('admin')) {
        header("Location: ../login.php");
        exit();
    }
}

// Redirect if not verifier
function require_verifier() {
    require_login();
    if (!has_role('verifier')) {
        header("Location: ../login.php");
        exit();
    }
}
?>