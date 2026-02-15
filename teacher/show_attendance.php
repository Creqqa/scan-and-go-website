<?php
session_start();

// 1. Security & Connection
if (!isset($_SESSION["teacher_name"])) {
  header("location:../index.php");
  exit();
}

$con = mysqli_connect("localhost", "root", "", "qr_ats");
if (mysqli_connect_errno()) { die("Failed to connect: " . mysqli_connect_error()); }

$subject = $_SESSION['subject'];

// CHECK: Are we viewing a specific section?
$selected_section = isset($_GET['section']) ? $_GET['section'] : null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Attendance Records</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="teacher.css" />
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  
  <style>
      /* --- SECTION FOLDER STYLES --- */
      .section-grid {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
          gap: 20px;
          margin-top: 20px;
      }
      .section-card {
          background: white;
          padding: 25px;
          border-radius: 12px;
          box-shadow: 0 2px 5px rgba(0,0,0,0.05);
          text-align: center;
          cursor: pointer;
          border: 1px solid #eee;
          text-decoration: none;
          color: inherit;
          display: block;
          transition: background 0.2s, border-color 0.2s;
      }
      .section-card:hover {
          border-color: var(--primary-color);
          background-color: #fafafa;
      }
      .folder-icon {
          font-size: 2.5rem;
          margin-bottom: 10px;
          display: block;
      }
      .section-name {
          font-weight: 700;
          font-size: 1.1rem;
          color: #333;
      }
      .student-count {
          color: #888;
          font-size: 0.85rem;
          margin-top: 5px;
      }

      /* --- STATS BADGES --- */
      .badge { 
          padding: 6px 12px; 
          border-radius: 6px; 
          color: white; 
          font-weight: 600; 
          font-size: 0.85rem; 
          min-width: 35px; 
          display: inline-block; 
          text-align: center;
      }
      .bg-present { background-color: #2ecc71; }
      .bg-late { background-color: #f1c40f; }
      .bg-absent { background-color: #e74c3c; }

      /* Stop Table from Moving on Hover */
      .card:hover {
          transform: none !important;
          box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
      }
  </style>
</head>

<body>
    <?php 
    $title = 'Scan-and-Go';
    $username = $_SESSION['teacher_name'];
    include "../componets/header.php"; 
    ?>
    
    <div id="box">
        <a href="../logout.php"><img src="../resources/icons/Logout.svg" alt="" style="width:20px;"> Logout</a>
    </div>

    <main>
        <div class="container" style="margin-left: 0; width: 100%; max-width: 1200px; margin: 80px auto 0 auto;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h2 style="color: var(--text-color); margin: 0;">
                        <?php echo $selected_section ? "" . htmlspecialchars($selected_section) : "Select Section"; ?>
                    </h2>
                    <p style="color: #666; font-size: 0.9rem; margin-top: 5px;">
                        Subject: <strong><?php echo htmlspecialchars($subject); ?></strong>
                    </p>
                </div>

                <div>
                    <?php if($selected_section): ?>
                        <a href="show_attendance.php" class="button_submit" style="background: #7f8c8d; width: auto; padding: 10px 20px; margin: 0; text-decoration: none;">
                            &larr; Back to Sections
                        </a>
                    <?php else: ?>
                        <a href="gen_qr.php" class="button_submit" style="width: auto; padding: 10px 20px; margin: 0; text-decoration: none;">
                            &larr; Back to Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>


            <?php if (!$selected_section): ?>
                
                <div class="section-grid">
                    <?php
                        // Get all unique sections
                        $sec_query = "SELECT DISTINCT section FROM student ORDER BY section ASC";
                        $sec_result = mysqli_query($con, $sec_query);

                        if(mysqli_num_rows($sec_result) > 0){
                            while($row = mysqli_fetch_assoc($sec_result)){
                                $sec = $row['section'];
                                
                                // Count ALL students in this section from the Master Table
                                $count_sql = "SELECT COUNT(*) as cnt FROM student WHERE section='$sec'";
                                $count_res = mysqli_fetch_assoc(mysqli_query($con, $count_sql));
                                $s_count = $count_res['cnt'];

                                echo "
                                <a href='show_attendance.php?section=".urlencode($sec)."' class='section-card'>
                                    <span class='folder-icon'>📂</span>
                                    <div class='section-name'> $sec</div>
                                    <div class='student-count'>$s_count Students</div>
                                </a>";
                            }
                        } else {
                            echo "<p style='color:#666; grid-column: 1/-1; text-align:center;'>No sections found.</p>";
                        }
                    ?>
                </div>

            <?php else: ?>
                
                <?php
                    // FIX: Select from STUDENT table first, then JOIN with ATTENDANCE
                    // This ensures all students appear, even if they have no attendance records.
                    $query = "SELECT s.roll_no, s.name, s.section,
                              COALESCE(SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END), 0) as count_present,
                              COALESCE(SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END), 0) as count_late,
                              COALESCE(SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END), 0) as count_absent
                              FROM student s
                              LEFT JOIN attendance a ON s.roll_no = a.rollno AND a.subject = '$subject'
                              WHERE s.section = '$selected_section'
                              GROUP BY s.roll_no 
                              ORDER BY s.name ASC";
                    
                    $result = mysqli_query($con, $query);
                ?>

                <div class="card" style="width: 100%; margin: 0; padding: 0; overflow: hidden; transform: none !important;">
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>LRN</th>
                                    <th>Name</th>
                                    <th style="text-align: center;">Present</th>
                                    <th style="text-align: center;">Late</th>
                                    <th style="text-align: center;">Absent</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                if(mysqli_num_rows($result) > 0){
                                    while($row = mysqli_fetch_assoc($result)){
                                        echo "<tr>
                                            <td style='color: #555; font-size: 0.9rem;'>".htmlspecialchars($row["roll_no"])."</td>
                                            <td style='font-weight: 600; font-size: 1rem;'>".htmlspecialchars($row["name"])."</td>
                                            
                                            <td style='text-align: center;'>
                                                <span class='badge bg-present'>".$row["count_present"]."</span>
                                            </td>

                                            <td style='text-align: center;'>
                                                <span class='badge bg-late'>".$row["count_late"]."</span>
                                            </td>

                                            <td style='text-align: center;'>
                                                <span class='badge bg-absent'>".$row["count_absent"]."</span>
                                            </td>

                                            <td style='text-align: right;'>
                                                <a href='details.php?sub=".urlencode($subject)."&roll=".urlencode($row['roll_no'])."' style='color: var(--primary-color); font-weight:600; text-decoration: none; font-size: 0.9rem;'>
                                                    View Record &rarr;
                                                </a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' style='text-align:center; padding: 40px; color: #888;'>
                                        No students found in Section $selected_section.
                                    </td></tr>";
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </main>

  <script>
    function showBox() {
      const box = document.getElementById('box');
      if(box) box.classList.toggle('active');
    }
    document.addEventListener('click', function(event) {
        const profile = document.querySelector('.profile');
        const box = document.getElementById('box');
        if (profile && box && !profile.contains(event.target) && !box.contains(event.target)) {
            box.classList.remove('active');
        }
    });
  </script>
</body>
</html>