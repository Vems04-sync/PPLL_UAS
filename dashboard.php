<?php
require_once "functions/auth.php";
checkLogin();
?>

<h1>Selamat datang, <?php echo $_SESSION['name']; ?></h1>