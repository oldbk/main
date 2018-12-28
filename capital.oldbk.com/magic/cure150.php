<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$cure_value = 150;
if ($user[in_tower]==0) { $self_only = ture; } else { $self_only = false; }
 if  ($_POST['target']=='') { $self_only = ture;  }

include "cure_base.php"
?>