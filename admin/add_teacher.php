<?php
    session_start();
    if(!isset($_SESSION["admin_name"])){
      header("location:index.php");
      exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Add Teacher</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body>
    <div style="position: absolute; top: 20px; left: 20px;">
        <a href="teacher.php" class="button_submit" style="text-decoration: none; width: auto; padding: 10px 20px;">← Back</a>
    </div>

    <main>
        <div class="landing-wrapper">
             <section class="left">
                <div class="logo">
                    <h2>Admin</h2>
                </div>
                <img src="../resources/img/img1.gif" alt="">
            </section>

            <section class="right">
                <form action="../backend/teacher_data.php" method="post" id="form" onsubmit="return validateForm()">
                    <h2>Add New Teacher</h2>
                    <div class="input_area">
                        <img src="../resources/icons/Profile.svg" alt="" style="width: 20px; opacity: 0.6;">
                        <input type="text" placeholder="Full Name" name="name" required style="padding-left: 45px;">
                    </div>
                    <div class="input_area">
                        <img src="../resources/img/mail.png" alt="">
                        <input type="email" placeholder="Email Address" name="email" required>
                    </div>
                    <div class="input_area">
                        <input type="text" placeholder="Subject Name" name="subject" required style="padding-left: 15px;">
                    </div>
                    <div class="input_area">
                         <img src="../resources/img/padlock.png" alt="">
                        <input type="password" placeholder="Set Password" name="password" id="pass" required>
                    </div>
                    <div class="input_area">
                         <img src="../resources/img/padlock.png" alt="">
                        <input type="password" placeholder="Confirm Password" id="cpass" required>
                    </div>
                    <button class="button_submit" name="register">Register Teacher</button>
                </form>
            </section>
        </div>
    </main>
    <script>
        function validateForm() {
            let pass = document.getElementById("pass").value;
            let cpass = document.getElementById("cpass").value;
            if(pass !== cpass){
                alert("Passwords do not match");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>