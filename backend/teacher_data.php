<?php
// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$con = mysqli_connect("localhost", "root", "", "qr_ats");

$status = "";
$message = "";
$action_button = "";

$name = mysqli_real_escape_string($con, $_POST['name']);
$email = mysqli_real_escape_string($con, $_POST['email']);
$subject = mysqli_real_escape_string($con, $_POST['subject']);
$password = mysqli_real_escape_string($con, $_POST['password']);

try {
    $query = "INSERT INTO teacher(name, email, subject, password) VALUES ('$name', '$email', '$subject', '$password')";
    
    if (mysqli_query($con, $query)) {
        $status = "success";
        $message = "Teacher registered successfully!";
        // Redirect to Admin Panel since admin usually adds teachers
        $action_button = '<a href="../admin/teacher.php" class="button_submit" style="text-decoration:none; display:inline-block; width:auto; padding: 10px 30px;">Return to Dashboard</a>';
    }

} catch (mysqli_sql_exception $e) {
    $status = "error";
    if ($e->getCode() == 1062) { 
        $error_msg = $e->getMessage();
        if (strpos($error_msg, 'email') !== false) {
            $message = "This Email Address is already registered.";
        } elseif (strpos($error_msg, 'subject') !== false) {
            $message = "This Subject is already assigned to another teacher.";
        } else {
            $message = "Teacher account already exists.";
        }
    } else {
        $message = "Database error: " . $e->getMessage();
    }
    $action_button = '<button onclick="history.back()" class="button_submit" style="width:auto; padding: 10px 30px; background-color: #666;">Try Again</button>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .status-card {
            background: var(--white);
            padding: 40px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
            max-width: 400px;
            width: 100%;
            border-top: 5px solid #ccc;
        }
        .status-card.success { border-color: #2ecc71; }
        .status-card.error { border-color: #e74c3c; }
        
        .icon { font-size: 4rem; margin-bottom: 20px; display: block; }
        h2 { margin-bottom: 15px; color: var(--text-color); }
        p { color: #666; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="status-card <?php echo $status; ?>">
        <?php if($status == 'success'): ?>
            <span class="icon">✅</span>
            <h2 style="color: #2ecc71;">Registered!</h2>
        <?php else: ?>
            <span class="icon">⛔</span>
            <h2 style="color: #e74c3c;">Error</h2>
        <?php endif; ?>

        <p><?php echo $message; ?></p>
        
        <?php echo $action_button; ?>
    </div>
</body>
</html>