<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='lovk';
$addvalue=15;
$addtxt='Ловкость';
include "food_castles.php"
?>
