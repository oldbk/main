<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='mudra';
$addvalue=1;
$addtxt='Мудрость';
include "food_base.php"
?>
