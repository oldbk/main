<?
if (!($_SESSION['uid'] > 0)) header("Location: index.php");

$upgrade = array(
	"level" => 13,
	"hp" => 27,
	"bron" => 1,
	"stat" => 1,
	"mf" => 22,
	"udar" => 2,
	"nparam" => 1,
	"duration" => 15,
	"destiny" => false,
	"nintel" => 0
	);

include "upgrade.php";
?>