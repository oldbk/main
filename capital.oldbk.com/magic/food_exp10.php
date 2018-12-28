<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='expbonus';
$addvalue=0.10; //10%
$addtxt='Опыт';
include "food_base.php"
?>
