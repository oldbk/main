<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='lovk';
$addvalue=1;
$addtxt='Ловкость';
include "food_base.php"
?>