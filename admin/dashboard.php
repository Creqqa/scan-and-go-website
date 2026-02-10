<?php
session_start();
if(!isset($_SESSION["admin_name"])){
  header("location:index.php");
  exit();
}
$con = mysqli_connect("localhost", "root", "", "qr_ats");
$student_query = "select * from student";
$student_result = mysqli_query($con,$student_query);

$teacher_query = "select * from teacher";
$teacher_result = mysqli_query($con,$teacher_query);

$total_teacher = mysqli_num_rows($teacher_result);
$total_student = mysqli_num_rows($student_result);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
  </head>
  <body>
    <?php $title = 'Admin Dashboard'; $username=$_SESSION['admin_name']; include "../componets/header.php" ?>
    <?php include "../componets/sidebar.php" ?>
    
    <div id="box">
        <a href="logout.php">
            <img src="../resources/icons/Logout.svg" alt="" style="width:20px;"> Logout
        </a>
    </div>

    <main>
      <div class="container">
        <div class="card" style="background: linear-gradient(135deg, #ff4b4b 0%, #d43f3f 100%); color: white; width: 100%; max-width: 300px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                 <img src="../resources/icons/sun.png" style="width: 50px; height: 50px; filter: brightness(0) invert(1);"/>
                 <div style="text-align: right;">
                     <h2 id="time" style="font-size: 1.8rem; margin: 0;"></h2>
                     <h3 id="date" style="font-size: 0.9rem; font-weight: 400; margin: 0; opacity: 0.9;"></h3>
                 </div>
            </div>
        </div>

        <a href="teacher.php" style="text-decoration: none; color: inherit;">
            <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
              <div>
                  <h1 style="color: var(--primary-color); font-size: 2.5rem;"><?php echo $total_teacher; ?></h1>
                  <h4 style="color: #666;">Total Teachers</h4>
              </div>
              <img src="../resources/icons/Profile.svg" alt="" style="width: 50px; opacity: 0.5;" />
            </div>
        </a>

        <a href="student.php" style="text-decoration: none; color: inherit;">
            <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
              <div>
                  <h1 style="color: var(--primary-color); font-size: 2.5rem;"><?php echo $total_student; ?></h1>
                  <h4 style="color: #666;">Total Students</h4>
              </div>
              <img src="../resources/icons/2 User.svg" alt="" style="width: 50px; opacity: 0.5;" />
            </div>
        </a>
      </div>
    </main>
    
    <script>
        // Clock Script
        const timeDisplay = document.getElementById("time");
        const dateDisplay = document.getElementById("date");
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        setInterval(() => {
          const d = new Date();
          let h = d.getHours();
          const m = String(d.getMinutes()).padStart(2, '0');
          const ampm = h >= 12 ? 'PM' : 'AM';
          h = h % 12;
          h = h ? h : 12; 

          timeDisplay.innerHTML = `${h}:${m} ${ampm}`;
          dateDisplay.innerHTML = `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
        }, 1000);

        // Dropdown Script
        function showBox() {
            document.getElementById('box').classList.toggle('active');
        }
        document.addEventListener('click', function(e) {
            const profile = document.querySelector('.profile');
            const box = document.getElementById('box');
            if(profile && !profile.contains(e.target) && !box.contains(e.target)) {
                box.classList.remove('active');
            }
        });
    </script>
  </body>
</html>