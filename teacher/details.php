<?php
session_start();

if (!isset($_SESSION["teacher_name"])) {
  header("location:../index.php");
  exit();
}

$con = mysqli_connect("localhost", "root", "", "qr_ats");
if (mysqli_connect_errno()) { die("Failed to connect: " . mysqli_connect_error()); }

$subject = isset($_GET['sub']) ? $_GET['sub'] : '';
$rollno = isset($_GET['roll']) ? $_GET['roll'] : '';

// 1. Fetch Student Name & SECTION
$student_name = "Student";
$student_section = "Unknown";

$name_q = "SELECT name, section FROM student WHERE roll_no='$rollno' LIMIT 1";
$res_name = mysqli_query($con, $name_q);
if($r = mysqli_fetch_assoc($res_name)) { 
    $student_name = $r['name'];
    $student_section = $r['section'];
}

// 2. Fetch Logs
$query = "SELECT * FROM attendance WHERE subject='$subject' AND rollno='$rollno' ORDER BY date DESC";
$result = mysqli_query($con, $query);

// 3. Calculate Stats
$logs = [];
$p = 0; $l = 0; $a = 0;

while($row = mysqli_fetch_assoc($result)){
    $logs[] = $row;
    $st = ucfirst(strtolower($row['status']));
    if($st == 'Present') $p++;
    elseif($st == 'Late') $l++;
    elseif($st == 'Absent') $a++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Student Details</title>
  <link rel="stylesheet" href="teacher.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

  <style>
      /* --- BOXED STATS --- */
      .stats-box-container {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
          gap: 15px;
          margin-bottom: 25px;
      }
      .stat-box {
          background: white;
          border: 1px solid #e0e0e0;
          border-radius: 10px;
          padding: 15px;
          text-align: center;
          border-top: 4px solid #ccc; 
      }
      .stat-box.present { border-top-color: #2ecc71; }
      .stat-box.late { border-top-color: #f1c40f; }
      .stat-box.absent { border-top-color: #e74c3c; }

      .stat-number {
          font-size: 1.8rem;
          font-weight: 700;
          color: #333;
          margin-bottom: 5px;
      }
      .stat-label {
          font-size: 0.9rem;
          color: #777;
          text-transform: uppercase;
          letter-spacing: 0.5px;
          font-weight: 600;
      }

      /* --- STOP TABLE UPSIES (Fix Hover Movement) --- */
      .card:hover {
          transform: none !important;
          box-shadow: 0 4px 10px rgba(0,0,0,0.05) !important; /* Keep static shadow */
      }
  </style>
</head>

<body>
    <?php 
    // UPDATED TITLE TO INCLUDE BRANDING
    $title = 'Scan-and-Go';
    $username = $_SESSION['teacher_name'];
    include "../componets/header.php"; 
    ?>
    
    <div id="box">
      <a href="../logout.php"><img src="../resources/icons/Logout.svg" alt="" style="width:20px;"> Logout</a>
    </div>

    <main>
        <div class="container" style="max-width: 1000px;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h2 style="color: var(--text-color); margin: 0;">Attendance Record</h2>
                    <p style="color: #666; margin-top: 5px;">
                        Student: <strong><?php echo htmlspecialchars($student_name); ?></strong><br>
                        Section: <strong><?php echo htmlspecialchars($student_section); ?></strong> &nbsp;|&nbsp; LRN: <?php echo htmlspecialchars($rollno); ?>
                    </p>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button onclick="generatePDF()" class="button_submit" style="width: auto; padding: 10px 20px; margin: 0; background-color: #e74c3c;">
                        📄 Download PDF Report
                    </button>
                    <a href="show_attendance.php?section=<?php echo urlencode($student_section); ?>" class="button_submit" style="width: auto; padding: 10px 20px; margin: 0; text-decoration: none; background-color: #7f8c8d;">
                        Back
                    </a>
                </div>
            </div>

            <div class="stats-box-container">
                <div class="stat-box present">
                    <div class="stat-number"><?php echo $p; ?></div>
                    <div class="stat-label">Present</div>
                </div>
                <div class="stat-box late">
                    <div class="stat-number"><?php echo $l; ?></div>
                    <div class="stat-label">Late</div>
                </div>
                <div class="stat-box absent">
                    <div class="stat-number"><?php echo $a; ?></div>
                    <div class="stat-label">Absent</div>
                </div>
            </div>

            <div class="card" style="width: 100%; margin: 0; padding: 0; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Name</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if(count($logs) > 0){
                                foreach($logs as $row){
                                    $dateObj = date_create($row["date"]);
                                    $dateStr = date_format($dateObj, "M d, Y");
                                    $timeStr = date_format($dateObj, "h:i A");
                                    
                                    $status = ucfirst(strtolower($row['status']));
                                    $color = '#2ecc71'; 
                                    if($status == 'Late') $color = '#f1c40f'; 
                                    if($status == 'Absent') $color = '#e74c3c'; 

                                    echo "<tr>
                                        <td style='font-weight: 500;'>".$dateStr."</td>
                                        <td style='color: #666;'>".$timeStr."</td>
                                        <td>".htmlspecialchars($row["s_name"])."</td>
                                        <td style='text-align: center;'>
                                            <span style='color: $color; font-weight: 700;'>$status</span>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center; padding: 20px; color: #999;'>No records found.</td></tr>";
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

  <script>
    function showBox() {
      document.getElementById('box').classList.toggle('active');
    }
    document.addEventListener('click', function(event) {
        const profile = document.querySelector('.profile');
        const box = document.getElementById('box');
        if (profile && box && !profile.contains(event.target) && !box.contains(event.target)) {
            box.classList.remove('active');
        }
    });
    
    // --- PDF GENERATOR ---
    function generatePDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // 1. Header
        doc.setFontSize(18);
        doc.setTextColor(44, 62, 80);
        doc.text("Scan-and-Go Attendance Report", 14, 20);

        // 2. Student Info
        doc.setFontSize(10);
        doc.setTextColor(100);
        doc.text("Student Name: <?php echo $student_name; ?>", 14, 30);
        doc.text("Section: <?php echo $student_section; ?>", 14, 35);
        doc.text("LRN: <?php echo $rollno; ?>", 14, 40);
        doc.text("Subject: <?php echo $subject; ?>", 14, 45);
        
        // 3. Stats Summary
        doc.setTextColor(0);
        doc.text("Total Present: <?php echo $p; ?>", 14, 55);
        doc.text("Total Late: <?php echo $l; ?>", 60, 55);
        doc.text("Total Absent: <?php echo $a; ?>", 110, 55);

        // 4. Table Data
        var tableBody = [
            <?php 
                foreach($logs as $row) {
                    $d = date_create($row["date"]);
                    $dStr = date_format($d, "M d, Y");
                    $tStr = date_format($d, "h:i A");
                    $st = ucfirst(strtolower($row['status']));
                    echo "['$dStr', '$tStr', '$st'],";
                }
            ?>
        ];

        // 5. Render Table
        doc.autoTable({
            startY: 60,
            head: [['Date', 'Time', 'Status']],
            body: tableBody,
            theme: 'striped',
            headStyles: { fillColor: [52, 73, 94] },
            didParseCell: function(data) {
                if (data.section === 'body' && data.column.index === 2) {
                    var text = data.cell.raw;
                    if (text === 'Present') data.cell.styles.textColor = [46, 204, 113];
                    if (text === 'Late') data.cell.styles.textColor = [243, 156, 18];
                    if (text === 'Absent') data.cell.styles.textColor = [231, 76, 60];
                }
            }
        });

        doc.save("<?php echo $student_name; ?>_Attendance_Report.pdf");
    }
  </script>
</body>
</html>