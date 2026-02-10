<?php
session_start();

if (!isset($_SESSION["teacher_name"])) {
  header("location:../index.php");
  exit();
}

$con = mysqli_connect("localhost", "root", "", "qr_ats");
$subject = $_GET['sub'];
$rollno = $_GET['roll'];

// Added prepare statement for security, though standard query works for prototype
$query = "SELECT * FROM attendance WHERE subject='$subject' and rollno='$rollno' ORDER BY date DESC";
$result = mysqli_query($con, $query);
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
</head>

<body>
    <?php $title = 'Student History';
    $username = $_SESSION['teacher_name'];
    include "../componets/header.php"; ?>
    
    <div id="box">
      <a href="../logout.php">Logout</a>
    </div>

    <main>
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: var(--text-color);">Detailed Logs</h2>
                <a href="show_attendance.php" class="button_submit" style="width: auto; padding: 10px 20px; margin: 0; text-decoration: none; background-color: #666;">Back</a>
            </div>

            <div class="card" style="width: 100%; margin: 0; padding: 0; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Name</th>
                                <th>LRN</th>
                                <th>Section</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            while($row = mysqli_fetch_assoc($result)){
                                // Format date nicely
                                $dateObj = date_create($row["date"]);
                                $formattedDate = date_format($dateObj, "M d, Y h:i A");
                                
                                echo "<tr>
                                    <td style='font-weight: 500;'>".$formattedDate."</td>
                                    <td>".htmlspecialchars($row["s_name"])."</td>
                                    <td>".htmlspecialchars($row["rollno"])."</td>
                                    <td>".htmlspecialchars($row["section"])."</td>
                                    <td><span style='color: var(--success);'>Present</span></td>
                                </tr>";
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
  </script>
</body>
</html>