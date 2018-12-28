<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$cure_value = 360;
if ($user[in_tower]==0) { $self_only = true; } else { $self_only = false; }
 if  ($_POST['target']=='') { $self_only = true;  }

include "cure_base.php"
?>