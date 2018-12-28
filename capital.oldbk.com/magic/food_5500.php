<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='sila';
$addvalue=2;
$addtxt='Сила';
include "food_castles.php"
?>
