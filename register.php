<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body>
    <main>
        <div class="landing-wrapper">
            <section class="left">
                <div class="logo">
                    <h2>Scan-and-Go</h2>
                </div>
                <img src="resources/img/img1.gif" alt="Registration Illustration">
            </section>

            <section class="right">
                <form action="backend/student_data.php" method="post" id="form" onsubmit="return validateForm()">
                    <h2>Student Registration</h2>
                    
                    <div class="input_area">
                        <img src="resources/icons/Profile.svg" alt="" style="width: 20px; opacity: 0.6;">
                        <img src="resources/icons/Profile.svg" alt="" style="display:none"> <input type="text" placeholder="Full Name" name="name" required style="padding-left: 45px;">
                    </div>
                    
                    <div class="input_area">
                        <img src="resources/img/mail.png" alt="">
                        <input type="email" placeholder="Email Address" name="email" required>
                    </div>
                    
                    <div class="input_area">
                         <input type="text" placeholder="LRN Number" name="roll_no" required style="padding-left: 15px;">
                    </div>
                    
                    <div class="input_area">
                        <select name="section" id="section" required style="padding-left: 15px;">
                            <option value="" disabled selected>Select Section</option>
                            <option value="ICT-1201">ICT-1201</option>
                            <option value="ICT-1202">ICT-1202</option>
                            <option value="ICT-1203">ICT-1203</option>
                        </select>
                    </div>
                    
                    <div class="input_area">
                        <img src="resources/img/padlock.png" alt="">
                        <input type="password" placeholder="Password" name="password" id="pass" required>
                    </div>
                    
                    <div class="input_area">
                        <img src="resources/img/padlock.png" alt="">
                        <input type="password" placeholder="Confirm Password" id="cpass" required>
                    </div>

                    <button class="button_submit" name="register">Create Account</button>
                    
                    <div class="msg">
                        Already have an account? <a href="index.php">Login here</a>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <script>
        function validateForm() {
            let pass = document.getElementById("pass").value;
            let cpass = document.getElementById("cpass").value;

            if(pass === cpass){
                return true;
            } else {
                alert("Passwords do not match!");
                return false;
            }
        }
    </script>
</body>
</html>