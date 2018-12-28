<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='inta';
$addvalue=2;
$addtxt='Интуиция';
include "food_castles.php"
?>
