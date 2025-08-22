<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_admin();

if (!isset($_GET['id'])) {
    header("Location: view_verified.php");
    exit();
}

$id = $_GET['id'];

// Get application details
$stmt = $pdo->prepare("SELECT * FROM admissions WHERE id = ? AND status = 'approved'");
$stmt->execute([$id]);
$application = $stmt->fetch();

if (!$application) {
    header("Location: view_verified.php");
    exit();
}

// Generate PDF content
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admission Form - <?php echo $application['serial_no']; ?></title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            line-height: 1.6;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px;
            border-bottom: 2px solid #3b5998;
            padding-bottom: 20px;
        }
        .header h1 { 
            color: #3b5998; 
            margin-bottom: 5px;
        }
        .header h2 {
            color: #555;
            margin-top: 0;
            font-size: 1.5em;
        }
        .section { 
            margin-bottom: 20px; 
        }
        .section h3 { 
            background-color: #f5f5f5; 
            padding: 8px 12px;
            border-left: 4px solid #3b5998;
            margin-bottom: 15px;
            font-size: 1.2em;
        }
        .info-table { 
            width: 100%; 
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td { 
            padding: 10px; 
            border-bottom: 1px solid #ddd; 
            vertical-align: top;
        }
        .info-table .label { 
            font-weight: bold; 
            width: 30%; 
            color: #555;
        }
        .signature { 
            margin-top: 60px; 
            border-top: 1px solid #000;
            padding-top: 20px;
            width: 50%;
        }
        .footer { 
            margin-top: 80px; 
            text-align: center; 
            font-size: 12px; 
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .school-stamp {
            position: absolute;
            right: 50px;
            bottom: 150px;
            text-align: center;
            width: 150px;
            border: 2px solid #3b5998;
            padding: 10px;
            font-style: italic;
        }
        .document-id {
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 4px;
            font-family: monospace;
            display: inline-block;
            margin-bottom: 10px;
        }
        .photo-placeholder {
            width: 120px;
            height: 150px;
            border: 1px dashed #ccc;
            text-align: center;
            padding: 10px;
            margin: 10px 0;
            display: inline-block;
            vertical-align: top;
        }
        @page {
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo SCHOOL_NAME; ?></h1>
        <h2>Admission Confirmation</h2>
        <div class="document-id">Serial No: <strong><?php echo $application['serial_no']; ?></strong></div>
    </div>
    
    <div style="float: right; margin-left: 20px; margin-bottom: 20px;">
        <div class="photo-placeholder">
            Student Photo<br>
            <small>(Attach here)</small>
        </div>
    </div>
    
    <div class="section">
        <h3>Student Information</h3>
        <table class="info-table">
            <tr>
                <td class="label">Student Name:</td>
                <td><?php echo htmlspecialchars($application['student_name']); ?></td>
            </tr>
            <tr>
                <td class="label">Date of Birth:</td>
                <td><?php echo date('F j, Y', strtotime($application['dob'])); ?></td>
            </tr>
            <tr>
                <td class="label">Age:</td>
                <td><?php 
                    $dob = new DateTime($application['dob']);
                    $today = new DateTime();
                    $age = $today->diff($dob);
                    echo $age->y . ' years, ' . $age->m . ' months';
                ?></td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <h3>Parent Information</h3>
        <table class="info-table">
            <tr>
                <td class="label">Father's Name:</td>
                <td><?php echo htmlspecialchars($application['father_name']); ?></td>
            </tr>
            <tr>
                <td class="label">Mother's Name:</td>
                <td><?php echo htmlspecialchars($application['mother_name']); ?></td>
            </tr>
            <tr>
                <td class="label">Mobile Number:</td>
                <td><?php echo htmlspecialchars($application['parent_mobile']); ?></td>
            </tr>
            <tr>
                <td class="label">Email:</td>
                <td><?php echo htmlspecialchars($application['parent_email']); ?></td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <h3>Application Timeline</h3>
        <table class="info-table">
            <tr>
                <td class="label">Applied On:</td>
                <td><?php echo date('F j, Y \a\t H:i', strtotime($application['created_at'])); ?></td>
            </tr>
            <tr>
                <td class="label">Verified On:</td>
                <td><?php echo $application['verified_at'] ? date('F j, Y \a\t H:i', strtotime($application['verified_at'])) : 'Not verified'; ?></td>
            </tr>
            <tr>
                <td class="label">Approved On:</td>
                <td><?php echo $application['approved_at'] ? date('F j, Y \a\t H:i', strtotime($application['approved_at'])) : 'Not approved'; ?></td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <h3>Required Documents Checklist</h3>
        <table class="info-table">
            <tr>
                <td style="width: 30px;">□</td>
                <td>Birth Certificate</td>
                <td style="width: 30px;">□</td>
                <td>Transfer Certificate (if applicable)</td>
            </tr>
            <tr>
                <td>□</td>
                <td>Aadhaar Card (Student)</td>
                <td>□</td>
                <td>Previous Year Marksheet</td>
            </tr>
            <tr>
                <td>□</td>
                <td>Aadhaar Card (Father)</td>
                <td>□</td>
                <td>Passport Size Photos (4 copies)</td>
            </tr>
            <tr>
                <td>□</td>
                <td>Aadhaar Card (Mother)</td>
                <td>□</td>
                <td>Residence Proof</td>
            </tr>
        </table>
    </div>
    
    <div class="signature">
        <p>Parent's Signature: _________________________</p>
        <p>Date: _________________________</p>
    </div>
    
    <div class="school-stamp">
        OFFICIAL STAMP<br>
        <?php echo SCHOOL_NAME; ?><br>
        Date: _________________
    </div>
    
    <div class="footer">
        <p><strong><?php echo SCHOOL_NAME; ?></strong> - Providing quality education since 1990</p>
        <p>Address: 123 Education Street, Knowledge City, 560001 | Phone: +91-80-12345678 | Email: info@school.com</p>
        <p>Document generated on: <?php echo date('F j, Y \a\t H:i'); ?></p>
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

try {
    // Check if Dompdf is available via Composer
    $dompdfPath = __DIR__ . '/../vendor/autoload.php';
    
    if (file_exists($dompdfPath)) {
        require_once $dompdfPath;
        
        // Create PDF using Dompdf
        $options = new Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        
        $dompdf = new Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output PDF
        $dompdf->stream('admission_form_' . $application['serial_no'] . '.pdf', [
            'Attachment' => 1,
            'compress' => true
        ]);
        
        exit();
    } else {
        throw new Exception("Dompdf library not found. Please install via Composer: composer require dompdf/dompdf");
    }
} catch (Exception $e) {
    // Fallback: Output HTML as plain text
    header('Content-Type: text/html; charset=utf-8');
    echo "<h2>PDF Generation Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Showing HTML version instead:</p>";
    echo "<hr>";
    echo $html;
    exit();
}
?>