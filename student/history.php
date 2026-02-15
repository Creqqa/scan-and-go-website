<?php
session_start();


if (!isset($_SESSION["student_name"])) {
  header("location:../index.php");
  exit();
}

$con = mysqli_connect("localhost", "root", "", "qr_ats");

if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

$rollno = $_SESSION['rollno'];


$query = "SELECT * FROM attendance WHERE rollno='$rollno' ORDER BY date DESC";
$result = mysqli_query($con, $query);

$count_present = 0;
$count_late = 0;
$count_absent = 0;
$total_records = mysqli_num_rows($result);


while($row = mysqli_fetch_assoc($result)) {
    
    $status = isset($row['status']) && $row['status'] != '' ? $row['status'] : 'Present';
    
    if($status == 'Present') {
        $count_present++;
    } elseif($status == 'Late') {
        $count_late++;
    } elseif($status == 'Absent') {
        $count_absent++;
    }
}


mysqli_data_seek($result, 0); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Attendance Record</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="student.css" />
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <style>
      .stats-container {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
          gap: 15px;
          margin-bottom: 30px;
          width: 100%;
      }

      .stat-card {
          background: white;
          padding: 15px;
          border-radius: var(--radius);
          box-shadow: var(--shadow);
          display: flex;
          align-items: center;
          gap: 10px;
          border-left: 5px solid #ccc;
          transition: transform 0.2s;
      }
      
      .stat-card:hover { transform: translateY(-3px); }

      /* Specific Colors for Cards */
      .stat-card.present { border-left-color: #2ecc71; }
      .stat-card.late { border-left-color: #f1c40f; }
      .stat-card.absent { border-left-color: #e74c3c; }

      .stat-icon {
          width: 35px; height: 35px;
          border-radius: 50%;
          display: flex; align-items: center; justify-content: center;
          font-size: 1rem;
          font-weight: bold;
      }

      .stat-info h3 { font-size: 1.2rem; margin: 0; color: var(--text-color); }
      .stat-info p { color: #666; font-size: 0.8rem; margin: 0; }

      /* Search & Table */
      .search-box {
          position: relative; width: 100%; max-width: 400px; margin-bottom: 20px;
      }
      .search-box input {
          width: 100%; padding: 12px 12px 12px 40px;
          border-radius: 30px; border: 1px solid #ddd; outline: none;
      }
      .search-icon {
          position: absolute; left: 15px; top: 50%; transform: translateY(-50%); opacity: 0.5;
      }
      
      .badge {
          padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem; color: white;
      }
      .badge.success { background-color: #2ecc71; }
      .badge.warning { background-color: #f1c40f; }
      .badge.danger { background-color: #e74c3c; }
  </style>
</head>

<body>
    <?php $title = 'Scan-and-Go';
    $username = $_SESSION['student_name'];
    include "../componets/header.php"; ?>
    
    <div id="box">
      <a href="../logout.php">
          <img src="../resources/icons/Logout.svg" alt="" style="width:20px;"> Logout
      </a>
    </div>

    <main>
        <div class="container">
            
            <div class="stats-container">
                <div class="stat-card present">
                    <div class="stat-icon" style="background: #e8f5e9; color: #2ecc71;">P</div>
                    <div class="stat-info">
                        <h3><?php echo $count_present; ?></h3>
                        <p>Present</p>
                    </div>
                </div>

                <div class="stat-card late">
                    <div class="stat-icon" style="background: #fef9e7; color: #f1c40f;">L</div>
                    <div class="stat-info">
                        <h3><?php echo $count_late; ?></h3>
                        <p>Late</p>
                    </div>
                </div>

                <div class="stat-card absent">
                    <div class="stat-icon" style="background: #fceceb; color: #e74c3c;">A</div>
                    <div class="stat-info">
                        <h3><?php echo $count_absent; ?></h3>
                        <p>Absent</p>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search Subject or Date...">
                </div>
                <a href="sc_qr.php" class="button_submit" style="width: auto; padding: 10px 25px; margin: 0; text-decoration: none; border-radius: 30px;">
                    &larr; Back to Scanner
                </a>
            </div>

            <div class="card" style="width: 100%; margin: 0; padding: 0; overflow: hidden; min-height: 400px;">
                <div style="overflow-x: auto;">
                    <table id="attendanceTable">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Date & Time</th>
                                <th>Section</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if($total_records > 0){
                                while($row = mysqli_fetch_assoc($result)){
                                    // DATE FORMATTING
                                    $dateObj = date_create($row["date"]);
                                    $formattedDate = date_format($dateObj, "M d, Y h:i A");
                                    
                                    // STATUS LOGIC
                                    $status = isset($row['status']) && $row['status'] != '' ? $row['status'] : 'Present';
                                    
                                    $badgeClass = 'success'; // Green
                                    
                                    if($status == 'Late') { 
                                        $badgeClass = 'warning'; // Orange
                                    } elseif($status == 'Absent') { 
                                        $badgeClass = 'danger'; // Red
                                    }

                                    echo "<tr>
                                        <td style='font-weight:600; color: var(--text-color);'>".htmlspecialchars($row["subject"])."</td>
                                        <td style='color: #666;'>".$formattedDate."</td>
                                        <td>".htmlspecialchars($row["section"])."</td>
                                        <td>
                                            <span class='badge $badgeClass'>$status</span>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center; padding: 50px; color: #999;'>
                                    <div style='font-size: 3rem; margin-bottom: 10px;'>📂</div>
                                    No attendance records found yet.
                                </td></tr>";
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
      let box = document.getElementById('box');
      box.classList.toggle('active');
    }

    // Search Filter Logic
    function filterTable() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("searchInput");
      filter = input.value.toUpperCase();
      table = document.getElementById("attendanceTable");
      tr = table.getElementsByTagName("tr");

      for (i = 0; i < tr.length; i++) {
        // Search in Subject (col 0) and Date (col 1)
        tdSubject = tr[i].getElementsByTagName("td")[0];
        tdDate = tr[i].getElementsByTagName("td")[1];
        
        if (tdSubject || tdDate) {
          txtValueSubject = tdSubject.textContent || tdSubject.innerText;
          txtValueDate = tdDate.textContent || tdDate.innerText;
          
          if (txtValueSubject.toUpperCase().indexOf(filter) > -1 || txtValueDate.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    }
  </script>
</body>
</html>