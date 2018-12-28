<?
if (!($_SESSION['uid'] > 0)) header("Location: index.php");

$upgrade = array(
	"level" => 12,
	"hp" => 20,
	"bron" => 1,
	"stat" => 1,
	"mf" => 17,
	"udar" => 1,
	"nparam" => 1,
	"duration" => 15,
	"destiny" => true,
	"nintel" => 0
	);

include "upgrade.php";
?>