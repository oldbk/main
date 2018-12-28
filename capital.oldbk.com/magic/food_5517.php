<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='mudra';
$addvalue=5;
$addtxt='Мудрость';
include "food_castles.php"
?>
