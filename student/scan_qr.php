<?php
session_start();
date_default_timezone_set("Asia/Manila");

if (!isset($_SESSION["student_name"])) {
  header("location:../index.php");
  exit();
}

$con = mysqli_connect("localhost", "root", "", "qr_ats");

// Check for Session ID
if(!isset($_GET['session_id'])){
    header("location:sc_qr.php"); 
    exit();
}

$session_id = $_GET['session_id'];

// 1. GET SESSION DETAILS
$query = "SELECT * FROM class_sessions WHERE id='$session_id'";
$result = mysqli_query($con, $query);

if(mysqli_num_rows($result) > 0){
    $session = mysqli_fetch_assoc($result);
    
    // Check Status
    if($session['status'] == 'closed'){
        echo "<script>alert('Class Ended. Attendance Closed.'); window.location.href='sc_qr.php';</script>";
        exit();
    }

    // 2. CHECK TIME BOUNDARIES
    $now = date("Y-m-d H:i:s");
    $start_time = $session['start_time']; // Get when THIS class started
    $late_limit = $session['late_time_boundary'];
    $end_limit = $session['end_time_boundary'];
    
    if($now > $end_limit){
        $status = "Absent";
        $msg = "QR Code Expired. Marked Absent.";
    } elseif ($now > $late_limit){
        $status = "Late";
        $msg = "Marked Late";
    } else {
        $status = "Present";
        $msg = "Marked Present";
    }

    // Student Info
    $s_id = $_SESSION['id'];
    $s_name = $_SESSION['student_name'];
    $roll_no = $_SESSION['rollno'];
    $section = $_SESSION['section'];
    $subject = $session['subject']; 

    // --- 3. AUTO-ENROLLMENT LOGIC ---
    $check_enroll = "SELECT * FROM subject_enrollment WHERE student_id='$s_id' AND subject='$subject'";
    if(mysqli_num_rows(mysqli_query($con, $check_enroll)) == 0){
        $enroll_sql = "INSERT INTO subject_enrollment(student_id, student_name, roll_no, subject, section) 
                       VALUES('$s_id', '$s_name', '$roll_no', '$subject', '$section')";
        mysqli_query($con, $enroll_sql);
    }
    // ---------------------------------------------

    // 4. FIX: CHECK "ALREADY REGISTERED" FOR *THIS SESSION*
    // We check if an attendance record exists for this student, this subject, 
    // AND the time is GREATER THAN OR EQUAL to the Start Time of this session.
    $check_duplicate = "SELECT * FROM attendance 
                        WHERE rollno='$roll_no' 
                        AND subject='$subject' 
                        AND date >= '$start_time'";
                        
    if(mysqli_num_rows(mysqli_query($con, $check_duplicate)) > 0){
         header("location:already.php"); // Already scanned for THIS session
         exit();
    }

    // 5. INSERT RECORD
    $insert = "INSERT INTO attendance(s_id, s_name, subject, section, rollno, date, status) 
               VALUES('$s_id','$s_name','$subject','$section','$roll_no','$now', '$status')";

    if(mysqli_query($con, $insert)){
         echo "<script>alert('$msg'); window.location.href='success.php';</script>";
    }

} else {
    echo "<script>alert('Invalid Session ID'); window.location.href='sc_qr.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Processing...</title>
</head>
<body>
  Processing Attendance...
</body>
</html>