<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$cure_value = 15;

if ($user[in_tower]==0) { $self_only = ture; } else { $self_only = false; }

include "cure_base.php"
?>
