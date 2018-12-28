<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='lovk';
$addvalue=5;
$addtxt='Ловкость';
include "food_castles.php"
?>
