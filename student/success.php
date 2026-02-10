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
  <title>Success</title>
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
      <a href="../logout.php">Logout</a>
  </div>

  <main>
    <div class="container">
      <div class="status-card success">
        <span class="status-icon">🎉</span>
        <h1 class="msg-success">Attendance Registered!</h1>
        <p>Your attendance has been successfully recorded.</p>
        <a href="sc_qr.php" class="button_submit" style="display:inline-block; width: auto; padding: 10px 30px; margin-top: 20px; text-decoration: none;">Scan Another</a>
      </div>
    </div>
  </main>
  
  <script>
    function showBox() {
      document.getElementById('box').classList.toggle('active');
    }
  </script>
</body>
</html>