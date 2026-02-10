<?php
session_start();
session_destroy();
header("location:index.php?msg=You+have+been+logged+out");
exit();
?>