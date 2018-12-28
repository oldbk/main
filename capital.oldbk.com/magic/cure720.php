<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$cure_value = 720;
$self_only = ture;  

include "cure_base.php"
?>