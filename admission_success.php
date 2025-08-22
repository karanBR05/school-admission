<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admission Application Received</title>
</head>
<body>
    <h2>Admission Application Received</h2>
    <p>Dear Parent,</p>
    <p>You have successfully applied for admission at <?php echo SCHOOL_NAME; ?>.</p>
    <p><strong>Application Details:</strong></p>
    <ul>
        <li>Student Name: <?php echo $student_name; ?></li>
        <li>Serial Number: <?php echo $serial_no; ?></li>
    </ul>
    <p>Kindly visit the school with original documents for verification.</p>
    <p>Thank you,<br><?php echo SCHOOL_NAME; ?> Team</p>
</body>
</html>