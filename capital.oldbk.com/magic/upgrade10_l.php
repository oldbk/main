<?
if (!($_SESSION['uid'] > 0)) header("Location: index.php");

$upgrade = array(
	"level" => 10,
	"hp" => 12,
	"bron" => 1,
	"stat" => 1,
	"mf" => 12,
	"udar" => 4,
	"nparam" => 1,
	"duration" => 10,
	"destiny" => true,
	"nintel" => 0
	);

include "upgrade.php";
?>