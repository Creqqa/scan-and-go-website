<?php
session_start();
date_default_timezone_set("Asia/Manila"); // Set your timezone

if (!isset($_SESSION["student_name"])) {
  header("location:../index.php");
  exit();
}

$con = mysqli_connect("localhost", "root", "", "qr_ats");

// Get Data from URL
$s_id = $_GET['s_id'];
$s_name = $_GET['s_name'];
$subject = $_GET['subject'];
$section = $_GET['section'];
$roll_no = $_GET['rollno'];
$qr_date_str = $_GET['date']; // The time the teacher generated the QR

// --- LOGIC: Calculate Status ---
$qr_time = strtotime($qr_date_str);
$scan_time = time(); 
$diff_minutes = ($scan_time - $qr_time) / 60; // Convert seconds to minutes

$status = "Present"; 

if ($diff_minutes > 60) {
    $status = "Absent"; // Scanned after 1 hour
} elseif ($diff_minutes > 30) {
    $status = "Late";   // Scanned after 30 mins
} else {
    $status = "Present";
}

// Save to Database with Status
$query = "INSERT INTO attendance(s_id, s_name, subject, section, rollno, date, status) 
          VALUES('$s_id','$s_name','$subject','$section','$roll_no','$qr_date_str', '$status')";

try{
  $result = mysqli_query($con, $query);
  header("location:success.php");
}
catch(mysqli_sql_exception $e){
  // Duplicate entry (Student already scanned for this specific class time)
  header("location:already.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Dashboard</title>
  <link rel="stylesheet" href="student.css" />
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <style>
    a {
      text-decoration: none;
      color: black;
    }
    .msg{
      color: green;
    }
  </style>
</head>

<body>
  <main>
    <?php $title = 'Scan-and-Go';
    $username = $_SESSION['student_name'];
    include "../componets/header.php" ?>
    <div class="container">
      <h1 class="msg">Attendance Registered</h1>
    </div>
  </main>
  <script>
    var show = 0;
    function showBox() {
      box = document.getElementById('box');
      if (show == 0) {
        box.style.height = "100px";
        show = 1;
      } else {
        box.style.height = "0px";
        show = 0;
      }
    }
  </script>
</body>

</html>