<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="resources/img/Attendance System.png" type="image/x-icon">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body>
    <main>
        <div class="landing-wrapper">
            <section class="left">
                <div class="logo">
                    <h2>Scan-and-Go</h2>
                </div>
                <img src="resources/img/img1.gif" alt="Attendance Illustration">
            </section>
            
            <section class="right">
                <form id="form" method="post">
                    <h2>Welcome Back</h2>
                    
                    <?php if(isset($_GET['msg'])): ?>
                        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                            <?php echo htmlspecialchars($_GET['msg']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="role-selection">
    <div class="role-option">
        <input type="radio" name="role" id="teacher_radio" onchange="checkRadio()" value="teacher" required>
        <label for="teacher_radio" class="role-card">
            <div class="icon-box">
                <img src="resources/icons/Profile.svg" alt="Teacher">
            </div>
            <span>Teacher</span>
        </label>
    </div>

    <div class="role-option">
        <input type="radio" name="role" id="student_radio" onchange="checkRadio()" value="student" required>
        <label for="student_radio" class="role-card">
            <div class="icon-box">
                <img src="resources/icons/2 User.svg" alt="Student">
            </div>
            <span>Student</span>
        </label>
    </div>
</div>

                    <div class="input_area">
                        <img src="resources/img/mail.png" alt="email icon">
                        <input type="email" placeholder="Email Address" name="email" required>
                    </div>
                    <div class="input_area">
                        <img src="resources/img/padlock.png" alt="password icon">
                        <input type="password" placeholder="Password" name="password" required>
                    </div>

                    <button class="button_submit">Login</button>
                    
                    <div class="msg">
                        New Student? <a href="register.php">Register here</a>
                    </div>
                    <div class="msg" style="margin-top: 10px; font-size: 0.8rem;">
                         <a href="admin/index.php">Admin Login</a>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <script>
        function checkRadio(){
            let form = document.getElementById("form");
            if(document.getElementById("teacher_radio").checked){
                form.setAttribute("action", "teacher/index.php");
            }
            if(document.getElementById("student_radio").checked){
                form.setAttribute("action", "student/index.php");
            }
        }
    </script>
</body>
</html>