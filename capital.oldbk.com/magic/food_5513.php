<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='intel';
$addvalue=5;
$addtxt='Интеллект';
include "food_castles.php"
?>
