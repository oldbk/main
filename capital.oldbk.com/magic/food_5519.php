<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='mudra';
$addvalue=15;
$addtxt='Мудрость';
include "food_castles.php"
?>
