<?php
session_start();
if (!isset($_SESSION["student_name"])) {
    header("location:../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Scan QR</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="student.css" />
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>

<body>
  <?php 
    $title = 'Scan-and-Go';
    $username = $_SESSION['student_name'];
    include "../componets/header.php"; 
  ?>
  
  <div id="box">
      <a href="../logout.php">
          <img src="../resources/icons/Logout.svg" alt="" style="width:20px;"> Logout
      </a>
  </div>

  <main>
    <div class="container">
      <div class="scanner-card">
        <h1>Scan Attendance QR</h1>
        <p style="margin-bottom: 20px; color: #666;">Scan the specific subject QR code.</p>
        
        <div id="my-qr-reader"></div>
        
        <a href="history.php" class="button_submit" style="background-color: transparent; border: 1px solid var(--primary-color); color: var(--primary-color); margin-top: 20px; display: block; text-decoration: none;">
            View My Attendance Record
        </a>
      </div>
    </div>
  </main>

  <script src="https://unpkg.com/html5-qrcode"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    <?php if(isset($_SESSION['login_success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Welcome!',
            text: 'Login Successful, <?php echo $_SESSION['student_name']; ?>',
            timer: 2000,
            showConfirmButton: false,
            background: '#fff',
            iconColor: '#2ecc71'
        });
        <?php unset($_SESSION['login_success']); ?>
    <?php endif; ?>

    function showBox() {
      let box = document.getElementById('box');
      box.classList.toggle('active');
    }

    function domReady(fn) {
      if (document.readyState === "complete" || document.readyState === "interactive") {
        setTimeout(fn, 1000);
      } else {
        document.addEventListener("DOMContentLoaded", fn);
      }
    }

    domReady(function () {
      function onScanSuccess(decodeText, decodeResult) { 
        try {
            // 1. Parse QR Data
            let qr_info = JSON.parse(decodeText);
            
            // 2. Check for SESSION ID (The new format)
            if(qr_info.session_id) {
                // Redirect to backend with Session ID
                // We don't calculate time here anymore. The SERVER does it for security.
                window.location.href = "scan_qr.php?session_id=" + qr_info.session_id;
            } else {
                 Swal.fire({
                    icon: 'error',
                    title: 'Old QR Code',
                    text: 'This QR code format is outdated. Please ask teacher to regenerate.',
                });
            }
        } catch (e) {
            alert("Invalid QR Code format.");
        }
      }

      let htmlscanner = new Html5QrcodeScanner("my-qr-reader", { fps: 10, qrbos: 250 });
      htmlscanner.render(onScanSuccess);
    });
  </script>
</body>
</html>