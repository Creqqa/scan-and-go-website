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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Role Switcher Logic
        function checkRadio(){
            let form = document.getElementById("form");
            if(document.getElementById("teacher_radio").checked){
                form.setAttribute("action", "teacher/index.php");
            }
            if(document.getElementById("student_radio").checked){
                form.setAttribute("action", "student/index.php");
            }
        }

        // --- UPDATED ANIMATION LOGIC ---
        <?php if(isset($_GET['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: 'Incorrect Email or Password!',
                confirmButtonColor: '#ff4b4b',
                background: '#fff',
                // Updated: No Shake, Faster Animation (500ms)
                showClass: {
                    popup: 'animate__animated animate__fadeIn animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOut animate__faster'
                }
            }).then(() => {
                // Clear URL parameters
                window.history.replaceState(null, null, window.location.pathname);
            });
        <?php endif; ?>

        // Success Message Logic (Also sped up)
        <?php if(isset($_GET['msg'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?php echo htmlspecialchars($_GET['msg']); ?>',
                confirmButtonColor: '#2ecc71',
                showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' },
                hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' }
            });
        <?php endif; ?>
    </script>
</body>
</html>