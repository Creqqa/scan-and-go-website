<?php
session_start();

// 1. Security & Connection
if (!isset($_SESSION["teacher_name"])) {
  header("location:../index.php");
  exit();
}

$con = mysqli_connect("localhost", "root", "", "qr_ats");
$subject = $_SESSION['subject'];

// 2. Fetch Data (Removed 'GROUP BY' so we see individual daily records)
$query = "SELECT * FROM attendance WHERE subject='$subject' ORDER BY date DESC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Attendance List</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="teacher.css" />
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>

<body>
    <?php 
    $title = 'Attendance List';
    $username = $_SESSION['teacher_name'];
    include "../componets/header.php"; 
    ?>
    
    <div id="box">
      <a href="../logout.php">
          <img src="../resources/icons/Logout.svg" alt="" style="width:20px;"> Logout
      </a>
    </div>

    <main>
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: var(--text-color);">Attendance Records</h2>
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
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if(mysqli_num_rows($result) > 0){
                                while($row = mysqli_fetch_assoc($result)){
                                    // Handle missing status (backward compatibility)
                                    $status = isset($row['status']) ? $row['status'] : 'Present';
                                    
                                    // Color Coding
                                    $color = '#2ecc71'; // Green
                                    if($status == 'Late') { $color = '#f39c12'; } // Orange
                                    if($status == 'Absent') { $color = '#e74c3c'; } // Red

                                    // Date Formatting
                                    $dateDisplay = $row['date'];
                                    try {
                                        $d = date_create($row['date']);
                                        $dateDisplay = date_format($d, "M d, h:i A");
                                    } catch(Exception $e) {}

                                    echo "<tr>
                                        <td>".htmlspecialchars($row["rollno"])."</td>
                                        <td>".htmlspecialchars($row["s_name"])."</td>
                                        <td>".htmlspecialchars($row["section"])."</td>
                                        <td style='color: #666;'>".$dateDisplay."</td>
                                        <td>
                                            <span style='background: $color; color: white; padding: 5px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;'>
                                                $status
                                            </span>
                                        </td>
                                        <td><a href='details.php?sub=".urlencode($row['subject'])."&roll=".urlencode($row['rollno'])."' style='color: var(--primary-color);'>History</a></td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align:center; padding: 20px;'>No records found.</td></tr>";
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
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const profile = document.querySelector('.profile');
        const box = document.getElementById('box');
        if (profile && !profile.contains(event.target) && !box.contains(event.target)) {
            box.classList.remove('active');
        }
    });
  </script>
</body>
</html>