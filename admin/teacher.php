<?php
error_reporting(0);
session_start();
if(!isset($_SESSION["admin_name"])){
  header("location:index.php");
  exit();
}

$con = mysqli_connect("localhost", "root", "", "qr_ats");
$query = "select * from teacher";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>

<body>
    <?php $title = 'Manage Teachers'; include "../componets/header.php" ?>
    <?php include "../componets/sidebar.php" ?>
    
    <div id="box">
        <a href="logout.php">
            <img src="../resources/icons/Logout.svg" alt="" style="width:20px;"> Logout
        </a>
    </div>

    <main>
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <h2 style="color: var(--text-color);">Teacher List</h2>
                <a href="add_teacher.php" class="button_submit" style="width: auto; padding: 10px 20px; margin: 0; text-decoration: none;">+ Add Teacher</a>
            </div>

            <div class="card" style="width: 100%; margin: 0; padding: 0; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if (mysqli_num_rows($result) <= 0) {
                                echo "<tr><td colspan='5' style='text-align:center; padding: 20px;'>No teachers registered yet.</td></tr>";
                            } else {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                    <td>" . $row["id"] . "</td>
                                    <td>" . htmlspecialchars($row["name"]) . "</td>
                                    <td>" . htmlspecialchars($row["email"]) . "</td>
                                    <td>" . htmlspecialchars($row["subject"]) . "</td>
                                    <td>
                                        <a href='edit_teacher.php?id=$row[id]&name=$row[name]&email=$row[email]&subject=$row[subject]' style='margin-right: 10px;'><img src='../resources/icons/Edit.svg' style='width: 20px;'></a>
                                        <a href='delete_teacher.php?id=$row[id]' onclick='return confirm(\"Are you sure you want to delete this teacher?\")'><img src='../resources/icons/Delete.svg' style='width: 20px;'></a>
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