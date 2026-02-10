<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link rel="shortcut icon" href="../resources/img/Attendance System.png" type="image/x-icon">
</head>
<body class="login-page">
    <main>
        <div class="landing-wrapper">
            <section class="left">
                <div class="logo">
                    <h2>Scan-and-Go</h2>
                </div>
                <img src="../resources/img/img1.gif" alt="Admin Illustration">
            </section>
            
            <section class="right">
                <form id="form" method="post" action="#">
                    <h2>Admin</h2>
                    
                    <div class="input_area">
                        <img src="../resources/img/mail.png" alt="email">
                        <input type="email" placeholder="Admin Email" name="email" required>
                    </div>
                    <div class="input_area">
                        <img src="../resources/img/padlock.png" alt="password">
                        <input type="password" placeholder="Password" name="pass" required>
                    </div>
                    
                    <button class="button_submit" name="login">Login</button>
                    
                    <div class="msg" style="margin-top: 20px;">
                        <a href="../index.php">← Back to Main Site</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>

<?php
if(isset($_POST["login"]))
{
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $con = mysqli_connect("localhost", "root", "", "qr_ats");
    $query = "select * from admin where email='$email' and pass='$pass'";
    $result = mysqli_query($con,$query);

    if(mysqli_num_rows($result) <= 0){
        echo "<script>alert('Invalid Admin Credentials');</script>";
    }
    else{
        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin_name'] = $row['name'];
        $_SESSION['admin_email'] = $row['email'];
        header("location:dashboard.php");
    }
}
?>