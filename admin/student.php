<?php
error_reporting(0);
session_start();
if(!isset($_SESSION["admin_name"])){
  header("location:index.php");
  exit();
}

$con = mysqli_connect("localhost", "root", "", "qr_ats");
$query = "select * from student";
$result = mysqli_query($con,$query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Students</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body>
    <?php $title = 'Manage Students'; include "../componets/header.php"  ?>
    <?php include "../componets/sidebar.php" ?>
    
    <div id="box">
        <a href="logout.php">
            <img src="../resources/icons/Logout.svg" alt="" style="width:20px;"> Logout
        </a>
    </div>

    <main>
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <h2 style="color: var(--text-color);">Student List</h2>
                <a href="../register.php" class="button_submit" style="width: auto; padding: 10px 20px; margin: 0; text-decoration: none;">+ Add Student</a>
            </div>

            <div class="card" style="width: 100%; margin: 0; padding: 0; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>LRN</th>
                                <th>Section</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if(mysqli_num_rows($result) <= 0){
                                echo "<tr><td colspan='6' style='text-align:center; padding: 20px;'>No students registered yet.</td></tr>";
                            }
                            else{
                                $srno = 0;
                                while($row = mysqli_fetch_assoc($result)) {
                                    $srno++;
                                    echo "<tr>
                                        <td>".$srno."</td>
                                        <td>".htmlspecialchars($row["name"])."</td>
                                        <td>".htmlspecialchars($row["email"])."</td>
                                        <td>".htmlspecialchars($row["roll_no"])."</td>
                                        <td>".htmlspecialchars($row["section"])."</td>
                                        <td>
                                            <a href='edit_student.php?id=$row[id]&name=$row[name]&email=$row[email]&roll_no=$row[roll_no]&section=$row[section]' title='Edit' style='margin-right: 10px;'><img src='../resources/icons/Edit.svg' style='width: 20px;'></a>
                                            <a href='delete_student.php?id=$row[id]' title='Delete' onclick='return confirm(\"Are you sure you want to delete this student?\")'><img src='../resources/icons/Delete.svg' style='width: 20px;'></a>
                                        </td>
                                    </tr>";
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <script>
        function showBox() { document.getElementById('box').classList.toggle('active'); }
    </script>
</body>
</html>