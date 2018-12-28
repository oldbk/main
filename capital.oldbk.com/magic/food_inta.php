<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$addstat='inta';
$addvalue=1;
$addtxt='Интуиция';
include "food_base.php"
?>
