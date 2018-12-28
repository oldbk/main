<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$cure_value = 360;
$self_only = true;
$FIX=1;
include "cure_base.php"
?>