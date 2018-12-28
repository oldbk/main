<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='intel';
$addvalue=1;
$addtxt='Интеллект';
include "food_base.php"
?>
