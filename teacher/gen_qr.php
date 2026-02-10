<?php
session_start();

if (!isset($_SESSION["teacher_name"])) {
    header("location:../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Teacher Dashboard</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="teacher.css" />
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>

<body>
  <?php 
    $title = 'Teacher Dashboard';
    $username = $_SESSION['teacher_name'];
    include "../componets/header.php"; 
  ?>
  
  <div id="box">
      <a href="../logout.php">
          <img src="../resources/icons/Logout.svg" alt="" style="width:20px;"> Logout
      </a>
  </div>

  <main>
    <div class="container" style="text-align: center;">
      
      <div class="scanner-card" style="max-width: 600px; margin: 0 auto;">
        <h1>Class Attendance</h1>
        <p style="color: #666; margin-bottom: 25px;">
            Subject: <strong><?php echo htmlspecialchars($_SESSION['subject']); ?></strong>
        </p>

        <div id="qr-container" style="margin-bottom: 30px; min-height: 260px; display: flex; align-items: center; justify-content: center; background: #f9f9f9; border-radius: 12px; border: 2px dashed #eee;">
            <div id="placeholder-text" style="color: #aaa;">
                QR Code will appear here
            </div>
            <div id="qrcode"></div>
        </div>

        <button class="button_submit" id="btn1" onclick="changeQR()">
            Generate New QR Code
        </button>
        
        <a href="show_attendance.php" class="button_submit" style="background-color: transparent; border: 1px solid var(--primary-color); color: var(--primary-color); margin-top: 15px; display: block; text-decoration: none;">
            View Attendance List
        </a>
      </div>

    </div>
  </main>

  <script src="../js/qrcode.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // --- LOGIN SUCCESS POPUP LOGIC ---
    <?php if(isset($_SESSION['login_success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Welcome Back!',
            text: 'Login Successful, <?php echo $_SESSION['teacher_name']; ?>',
            timer: 2000,
            showConfirmButton: false,
            background: '#fff',
            iconColor: '#2ecc71'
        });
        <?php unset($_SESSION['login_success']); ?>
    <?php endif; ?>
    // ---------------------------------

    // Dropdown Logic
    function showBox() {
      document.getElementById('box').classList.toggle('active');
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const profile = document.querySelector('.profile');
        const box = document.getElementById('box');
        if (profile && !profile.contains(event.target) && !box.contains(event.target)) {
            box.classList.remove('active');
        }
    });

    // QR Generation Logic
    function changeQR() {
      // Clear previous QR if any
      document.getElementById("qrcode").innerHTML = "";
      document.getElementById("placeholder-text").style.display = "none";
      
      // Generate QR
      var qrcode = new QRCode("qrcode", {
          text: '{"subject":"<?php date_default_timezone_set("Asia/Manila"); echo $_SESSION['subject'];?>","date":"<?php $date=date_create(); echo date_format($date,"Y/m/d H:i:s");?>"}',
          width: 250,
          height: 250,
          colorDark : "#000000",
          colorLight : "#ffffff",
          correctLevel : QRCode.CorrectLevel.H
      });

      // Update button text to indicate refresh capability
      const btn = document.getElementById("btn1");
      btn.innerText = "Refresh QR Code";
      btn.style.backgroundColor = "#2ecc71"; // Change to green
    }
  </script>
</body>
</html>