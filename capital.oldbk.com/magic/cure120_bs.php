<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$cure_value = 120;
$self_only = false;

include "cure_base.php"
?>