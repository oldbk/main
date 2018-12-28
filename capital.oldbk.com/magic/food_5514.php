<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='intel';
$addvalue=10;
$addtxt='Интеллект';
include "food_castles.php"
?>
