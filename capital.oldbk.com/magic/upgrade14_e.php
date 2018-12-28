<?
if (!($_SESSION['uid'] > 0)) header("Location: index.php");

$upgrade = array(
	"level" => 14,
	"hp" => 35,
	"bron" => 1,
	"stat" => 1,
	"mf" => 27,
	"udar" => 2,
	"nparam" => 1,
	"duration" => 15,
	"destiny" => false,
	"nintel" => 0
	);

include "upgrade.php";
?>