<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admission Approved</title>
</head>
<body>
    <h2>Congratulations! Admission Approved</h2>
    <p>Dear Parent,</p>
    <p>We are pleased to inform you that your son/daughter's admission has been approved.</p>
    <p><strong>Application Details:</strong></p>
    <ul>
        <li>Student Name: <?php echo $student_name; ?></li>
        <li>Serial Number: <?php echo $serial_no; ?></li>
    </ul>
    <p>Please visit the school office to complete the admission process and pay the required fees.</p>
    <p>Thank you,<br><?php echo SCHOOL_NAME; ?> Team</p>
</body>
</html>