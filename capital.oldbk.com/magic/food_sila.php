<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='sila';
$addvalue=1;
$addtxt='Сила';
include "food_base.php"
?>
