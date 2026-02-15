<?php
session_start();
date_default_timezone_set("Asia/Manila");

// 1. Security Check
if (!isset($_SESSION["teacher_name"]) || !isset($_SESSION['teacher_id'])) {
    header("location:../index.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "qr_ats");
if (mysqli_connect_errno()) { die("Failed to connect: " . mysqli_connect_error()); }

$teacher_id = $_SESSION['teacher_id'];
$subject = $_SESSION['subject'];
$msg = "";
$msg_type = "";

// --- CHECK FOR ACTIVE SESSION ON LOAD ---
$check_active = "SELECT * FROM class_sessions WHERE teacher_id='$teacher_id' AND status='active' LIMIT 1";
$active_res = mysqli_query($con, $check_active);
$active_session_data = mysqli_fetch_assoc($active_res);
$has_active_qr = ($active_session_data != null);


// --- HANDLE BUTTON CLICKS ---

// A. START CLASS (Create Session in DB)
if (isset($_POST['start_class_btn']) && !$has_active_qr) {
    $target_section = $_POST['section_selector'];
    $late_mins = intval($_POST['late_minutes']);
    $absent_mins = intval($_POST['absent_minutes']);

    $start_time = date("Y-m-d H:i:s");
    $late_boundary = date("Y-m-d H:i:s", strtotime("+$late_mins minutes"));
    $end_boundary = date("Y-m-d H:i:s", strtotime("+$absent_mins minutes"));

    if($absent_mins <= $late_mins){
         $msg = "Error: Absent time must be greater than late time.";
         $msg_type = "danger";
    } else {
        $insert_sql = "INSERT INTO class_sessions (teacher_id, subject, section, start_time, late_time_boundary, end_time_boundary, status) 
                       VALUES ('$teacher_id', '$subject', '$target_section', '$start_time', '$late_boundary', '$end_boundary', 'active')";
        
        if(mysqli_query($con, $insert_sql)){
            header("Refresh:0");
            exit();
        } else {
             $msg = "Database Error: " . mysqli_error($con);
             $msg_type = "danger";
        }
    }
}

// B. END CLASS (Finalize and Auto-Mark Absent) -- THIS IS THE CODE YOU ASKED WHERE TO PUT
if (isset($_POST['end_class_btn']) && $has_active_qr) {
    $session_id = $active_session_data['id'];
    $session_start = $active_session_data['start_time']; // Get Start Time
    $now_time = date("Y-m-d H:i:s");
    $current_subject = $_SESSION['subject']; 

    // 1. Close the session
    mysqli_query($con, "UPDATE class_sessions SET status='closed' WHERE id='$session_id'");

    // 2. UPDATED LOGIC: Find students who registered but DID NOT SCAN *FOR THIS SESSION*
    // We check for attendance records that happened AFTER the session started.
    
    $absent_query = "SELECT e.student_id, e.student_name, e.roll_no, e.section 
                     FROM subject_enrollment e
                     WHERE e.subject = '$current_subject'
                     AND e.roll_no NOT IN (
                        -- Find students who HAVE an attendance record since class started
                        SELECT a.rollno FROM attendance a 
                        WHERE a.subject = '$current_subject' 
                        AND a.date >= '$session_start'
                     )";
    
    $absent_res = mysqli_query($con, $absent_query);
    $absent_count = 0;

    // 3. Insert Absent records
    if(mysqli_num_rows($absent_res) > 0){
        while($stud = mysqli_fetch_assoc($absent_res)){
            $sid = $stud['student_id']; 
            $sname = $stud['student_name']; 
            $ssec = $stud['section']; 
            $roll = $stud['roll_no'];

            $ins = "INSERT INTO attendance(s_id, s_name, subject, section, rollno, date, status) 
                    VALUES('$sid', '$sname', '$current_subject', '$ssec', '$roll', '$now_time', 'Absent')";
            mysqli_query($con, $ins);
            $absent_count++;
        }
    }

    $msg = "Class ended. $absent_count enrolled student(s) were marked Absent.";
    $msg_type = "success";
    
    $has_active_qr = false;
    $active_session_data = null;
}
// ------------------------------------------------------------------

// Fetch Sections for Dropdown
$sec_query = "SELECT DISTINCT section FROM student ORDER BY section ASC";
$sec_result = mysqli_query($con, $sec_query);
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
  
  <style>
      /* PRINT STYLES */
      @media print {
          body * { visibility: hidden; }
          .header, #box, .button_submit, .sidebar, #settings-form, #control-buttons, a, form, .alert { display: none !important; }
          #print-area, #print-area * { visibility: visible; }
          #print-area {
              position: absolute; left: 50%; top: 40%;
              transform: translate(-50%, -50%); width: 100%;
              text-align: center; border: 2px solid #333;
              padding: 40px; border-radius: 20px;
          }
          #qrcode img { width: 400px !important; height: 400px !important; margin: 0 auto; }
      }

      /* Form Styles */
      .time-settings {
          display: flex; gap: 10px; margin-bottom: 15px; text-align: left;
      }
      .form-group { flex: 1; }
      .form-group label { display: block; font-size: 0.85rem; color: #666; margin-bottom: 5px; font-weight: 600;}
      .form-control {
          width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; outline: none;
      }
      .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
      .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
      .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
      .session-badge { display: inline-block; background: #e8f6f3; color: #16a085; padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: bold; margin-bottom: 10px;}
  </style>
</head>

<body>
  <?php 
    $title = 'Teacher Dashboard';
    $username = $_SESSION['teacher_name'];
    include "../componets/header.php"; 
  ?>
  
  <div id="box">
      <a href="../logout.php"><img src="../resources/icons/Logout.svg" alt="" style="width:20px;"> Logout</a>
  </div>

  <main>
    <div class="container" style="text-align: center;">
      
      <div class="scanner-card" style="max-width: 600px; margin: 0 auto;">
        
        <?php if($msg != ""): ?>
            <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

        <div id="print-area">
            <h1>Class Attendance</h1>
            <p style="color: #666; margin-bottom: 15px;">Subject: <strong><?php echo htmlspecialchars($subject); ?></strong></p>
            
            <?php if ($has_active_qr): ?>
                <div class="session-badge">
                    Session Active for Section: <?php echo $active_session_data['section']; ?>
                </div>
            <?php endif; ?>

            <div id="qr-container" style="margin-bottom: 20px; min-height: 260px; display: flex; align-items: center; justify-content: center; background: #f9f9f9; border-radius: 12px; border: 2px dashed #eee;">
                <div id="placeholder-text" style="color: #aaa; <?php echo $has_active_qr ? 'display:none;' : ''; ?>">
                    Configure time settings and click "Start Class"
                </div>
                <div id="qrcode"></div>
            </div>
            
            <p id="qr-note" style="<?php echo $has_active_qr ? 'display:block;' : 'display:none;'; ?> color: #666; font-size: 0.9rem;">
                Scan to mark attendance before it expires.<br>
                <small>Expires at: <?php echo date("h:i A", strtotime($active_session_data['end_time_boundary'])); ?></small>
            </p>
        </div>

        <?php if (!$has_active_qr): ?>
            <form method="post" id="settings-form">
                <div style="text-align: left; margin-bottom: 15px;">
                    <label style="font-weight: 600; color: #333;">Select Section for this Class:</label>
                    <select name="section_selector" required class="form-control" style="border: 2px solid var(--primary-color);">
                        <option value="" disabled selected>-- Choose Section --</option>
                        <?php 
                            if(mysqli_num_rows($sec_result) > 0) {
                                while($row = mysqli_fetch_assoc($sec_result)) {
                                    echo "<option value='".$row['section']."'>Section ".$row['section']."</option>";
                                }
                            }
                        ?>
                    </select>
                </div>

                <div class="time-settings">
                    <div class="form-group">
                        <label>Late after (mins):</label>
                        <input type="number" name="late_minutes" class="form-control" value="15" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Absent/Expire after(mins):</label>
                        <input type="number" name="absent_minutes" class="form-control" value="60" min="5" required>
                    </div>
                </div>
                
                <button type="submit" name="start_class_btn" class="button_submit" style="background-color: #2ecc71;">
                    Start Class & Generate QR
                </button>
            </form>

        <?php else: ?>
            <div id="control-buttons">
                <button type="button" onclick="window.print()" class="button_submit" style="background-color: #34495e; margin-bottom: 10px;">
                    Print QR Code
                </button>

                <form method="post" onsubmit="return confirm('End Class? Students who have not scanned will be marked ABSENT automatically.');">
                    <button type="submit" name="end_class_btn" class="button_submit" style="background-color: #e74c3c;">
                        End Class
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <a href="show_attendance.php" class="button_submit" style="background-color: transparent; border: 1px solid var(--primary-color); color: var(--primary-color); margin-top: 15px; display: block; text-decoration: none;">
            View Attendance List
        </a>

      </div>
    </div>
  </main>

  <script src="../js/qrcode.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    <?php if(isset($_SESSION['login_success'])): ?>
        Swal.fire({ icon: 'success', title: 'Welcome Back!', text: 'Login Successful', timer: 1500, showConfirmButton: false });
        <?php unset($_SESSION['login_success']); ?>
    <?php endif; ?>

    function showBox() { document.getElementById('box').classList.toggle('active'); }
    
    
    <?php if ($has_active_qr): ?>
        var qrData = JSON.stringify({ 
            session_id: "<?php echo $active_session_data['id']; ?>",
            subject: "<?php echo $subject; ?>"
        });
        
        new QRCode("qrcode", {
            text: qrData, width: 250, height: 250,
            colorDark : "#000000", colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    <?php endif; ?>
  </script>
</body>
</html>