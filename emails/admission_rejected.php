<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admission Application Status</title>
</head>
<body>
    <h2>Admission Application Update</h2>
    <p>Dear Parent,</p>
    <p>We regret to inform you that your admission application has been rejected.</p>
    <p><strong>Application Details:</strong></p>
    <ul>
        <li>Student Name: <?php echo $student_name; ?></li>
        <li>Serial Number: <?php echo $serial_no; ?></li>
    </ul>
    <p>For more information, please contact the school office.</p>
    <p>Thank you,<br><?php echo SCHOOL_NAME; ?> Team</p>
</body>
</html>