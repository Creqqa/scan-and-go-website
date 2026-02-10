<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "qr_ats");
if(!isset($_SESSION["admin_name"])){
  header("location:index.php");
  exit();
}

$id = $_GET['id'];
$section = $_GET['section'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit Student</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
</head>
<body>
    <div style="position: absolute; top: 20px; left: 20px;">
        <a href="student.php" class="button_submit" style="text-decoration: none; width: auto; padding: 10px 20px;">← Back</a>
    </div>

    <main>
        <div class="landing-wrapper">
             <section class="left">
                <div class="logo"><h2>Edit Record</h2></div>
                <img src="../resources/img/img1.gif" alt="">
            </section>
            
            <section class="right">
                <form action="#" method="post" id="form">
                    <h2>Edit Student</h2>
                    <div class="input_area">
                         <input type="text" placeholder="Name" name="name" value="<?php echo htmlspecialchars($_GET['name']);?>" required style="padding-left: 15px;">
                    </div>
                    <div class="input_area">
                        <img src="../resources/img/mail.png" alt="">
                        <input type="email" placeholder="Email" name="email" value="<?php echo htmlspecialchars($_GET['email']);?>" required>
                    </div>
                    <div class="input_area">
                         <input type="text" placeholder="LRN" name="roll_no" value="<?php echo htmlspecialchars($_GET['roll_no']);?>" required style="padding-left: 15px;">
                    </div>
                    <div class="input_area">
                        <select name="section" id="section" required style="padding-left: 15px;">
                            <option value="<?php echo htmlspecialchars($section);?>" selected><?php echo htmlspecialchars($section);?></option>
                            <option value="ICT-1201">ICT-1201</option>
                            <option value="ICT-1202">ICT-1202</option>
                            <option value="ICT-1203">ICT-1203</option>
                        </select>
                    </div>
                    <button class="button_submit" name="update">Save Changes</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>

<?php
if(isset($_POST['update'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $roll_no = $_POST['roll_no'];
    $section_post = $_POST['section'];

    $query = "update student set name='$name',email='$email',roll_no='$roll_no',section='$section_post' where id='$id'";
    $result = mysqli_query($con,$query);

    if($result){
        echo "<script>window.location.href='student.php';</script>";
    }
}
?>