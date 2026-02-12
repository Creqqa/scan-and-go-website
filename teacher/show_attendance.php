<?php
session_start();

// 1. Security & Connection
if (!isset($_SESSION["teacher_name"])) {
  header("location:../index.php");
  exit();
}

$con = mysqli_connect("localhost", "root", "", "qr_ats");




$subject = $_SESSION['subject'];

// 2. Query
$query = "SELECT rollno, s_name, section, subject, COUNT(*) as total_attendance 
          FROM attendance 
          WHERE subject='$subject' 
          GROUP BY rollno 
          ORDER BY s_name ASC";

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Attendance</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="teacher.css" />
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>

<body>
    <?php 
    $title = 'Attendance Record';
    $username = $_SESSION['teacher_name'];
    include "../componets/header.php"; 
    ?>
    
    <main>
        <div class="container" style="margin-left: 0; width: 100%; max-width: 1200px; margin: 80px auto 0 auto;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: var(--text-color);">Student List</h2>
                <a href="gen_qr.php" class="button_submit" style="width: auto; padding: 10px 20px; margin: 0; text-decoration: none;">Back to Dashboard</a>
            </div>

            <div class="card" style="width: 100%; margin: 0; padding: 0; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>LRN</th>
                                <th>Name</th>
                                <th>Section</th>
                                <th style="text-align: center;">Total Present</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if(mysqli_num_rows($result) > 0){
                                while($row = mysqli_fetch_assoc($result)){
                                    
                                    $countColor = '#2ecc71'; 
                                    if($row['total_attendance'] < 3) { $countColor = '#e74c3c'; } 

                                    echo "<tr>
                                        <td>".htmlspecialchars($row["rollno"])."</td>
                                        <td>".htmlspecialchars($row["s_name"])."</td>
                                        <td>".htmlspecialchars($row["section"])."</td>
                                        <td style='text-align: center;'>
                                            <span style='background: $countColor; color: white; padding: 5px 15px; border-radius: 15px; font-weight: bold;'>
                                                ".$row["total_attendance"]."
                                            </span>
                                        </td>
                                        <td>
                                            <a href='details.php?sub=".urlencode($row['subject'])."&roll=".urlencode($row['rollno'])."' style='color: var(--primary-color); font-weight:600; text-decoration: none;'>
                                                View Attendance
                                            </a>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center; padding: 30px; color: #888;'>No students have scanned yet.</td></tr>";
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
      const box = document.querySelector('.profile-dropdown');
      if(box) box.classList.toggle('active');
    }
  </script>
</body>
</html>