<?php
session_start();

// 1. Security Check
if (!isset($_SESSION["student_name"])) {
  header("location:../index.php");
  exit();
}

// 2. Database Connection
$con = mysqli_connect("localhost", "root", "", "qr_ats");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$rollno = $_SESSION['rollno'];

// 3. Fetch History
$query = "SELECT * FROM attendance WHERE rollno='$rollno' ORDER BY date DESC";
$result = mysqli_query($con, $query);

// 4. Calculate Stats
$total_present = mysqli_num_rows($result);
$last_present = "N/A";

// Fetch the first row to get the "Last Active" date, then reset the pointer
if($total_present > 0) {
    $first_row = mysqli_fetch_assoc($result);
    $dateObj = date_create($first_row["date"]);
    $last_present = date_format($dateObj, "M d");
    mysqli_data_seek($result, 0); // Reset so the table loop below works
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>My History</title>
  <link rel="stylesheet" href="student.css" />
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <style>
      /* --- Page Specific Styles --- */
      
      /* Stats Grid */
      .stats-container {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
          gap: 20px;
          margin-bottom: 30px;
          width: 100%;
      }

      .stat-card {
          background: white;
          padding: 20px;
          border-radius: var(--radius);
          box-shadow: var(--shadow);
          display: flex;
          align-items: center;
          gap: 20px;
          border-left: 5px solid var(--primary-color);
          transition: transform 0.2s;
      }
      
      .stat-card:hover {
          transform: translateY(-5px);
      }

      .stat-icon {
          width: 50px;
          height: 50px;
          background: #ffe5e5;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 1.5rem;
          color: var(--primary-color);
      }

      .stat-info h3 {
          font-size: 2rem;
          margin: 0;
          color: var(--text-color);
      }

      .stat-info p {
          color: #666;
          font-size: 0.9rem;
          margin: 0;
      }

      /* Search Bar */
      .search-box {
          position: relative;
          width: 100%;
          max-width: 400px;
          margin-bottom: 20px;
      }
      
      .search-box input {
          width: 100%;
          padding: 15px 15px 15px 45px;
          border-radius: 30px;
          border: 1px solid #ddd;
          outline: none;
          transition: all 0.3s;
          box-shadow: var(--shadow);
      }

      .search-box input:focus {
          border-color: var(--primary-color);
          box-shadow: 0 0 0 3px rgba(255, 75, 75, 0.1);
      }

      .search-icon {
          position: absolute;
          left: 15px;
          top: 50%;
          transform: translateY(-50%);
          opacity: 0.5;
      }

      /* Table Enhancements */
      tr {
          transition: background 0.2s;
      }
      
      tr:hover td {
          background-color: #fef2f2; /* Light red hover */
      }
      
      /* Status Badges */
      .badge {
          padding: 5px 12px;
          border-radius: 20px;
          font-weight: 600;
          font-size: 0.85rem;
          display: inline-flex;
          align-items: center;
          gap: 5px;
          color: white;
      }
      .badge.success { background-color: #2ecc71; }
      .badge.warning { background-color: #f1c40f; }
      .badge.danger { background-color: #e74c3c; }

      /* Animation for rows */
      @keyframes fadeIn {
          from { opacity: 0; transform: translateY(10px); }
          to { opacity: 1; transform: translateY(0); }
      }

      tbody tr {
          animation: fadeIn 0.3s ease-in-out;
      }
  </style>
</head>

<body>
    <?php $title = 'Attendance Log';
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
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <h3><?php echo $total_present; ?></h3>
                        <p>Total Records</p>
                    </div>
                </div>
                <div class="stat-card" style="border-left-color: #2ecc71;">
                    <div class="stat-icon" style="background: #e8f5e9; color: #2ecc71;">🕒</div>
                    <div class="stat-info">
                        <h3><?php echo $last_present; ?></h3>
                        <p>Last Activity</p>
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
                            if($total_present > 0){
                                while($row = mysqli_fetch_assoc($result)){
                                    // DATE FORMATTING
                                    $dateObj = date_create($row["date"]);
                                    $formattedDate = date_format($dateObj, "M d, Y h:i A");
                                    
                                    // STATUS LOGIC (Inside the loop!)
                                    // Check if status exists in DB, otherwise default to Present
                                    $status = isset($row['status']) ? $row['status'] : 'Present';
                                    
                                    $badgeClass = 'success'; // Green
                                    $icon = '●';
                                    
                                    if($status == 'Late') { 
                                        $badgeClass = 'warning'; // Orange
                                        $icon = '⏱';
                                    } elseif($status == 'Absent') { 
                                        $badgeClass = 'danger'; // Red
                                        $icon = '✖';
                                    }

                                    echo "<tr>
                                        <td style='font-weight:600; color: var(--text-color);'>".htmlspecialchars($row["subject"])."</td>
                                        <td style='color: #666;'>".$formattedDate."</td>
                                        <td>".htmlspecialchars($row["section"])."</td>
                                        <td>
                                            <span class='badge $badgeClass'>
                                                <span>$icon</span> $status
                                            </span>
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
    // Toggle Profile Dropdown
    function showBox() {
      document.getElementById('box').classList.toggle('active');
    }
    
    document.addEventListener('click', function(event) {
        const profile = document.querySelector('.profile');
        const box = document.getElementById('box');
        if (profile && !profile.contains(event.target) && !box.contains(event.target)) {
            box.classList.remove('active');
        }
    });

    // INTERACTIVE: Search Filter Logic
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