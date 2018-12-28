<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='maxhp';
$addvalue=30;
$addtxt='Уровень жизни';
include "food_base.php"
?>
