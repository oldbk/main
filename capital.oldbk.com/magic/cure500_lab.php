<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$cure_value = 500;
$self_only = true;
include "cure_base.php"
?>