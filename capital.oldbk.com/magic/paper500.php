
<?php
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$num_w=500;
include 'paper_add.php';
?>

