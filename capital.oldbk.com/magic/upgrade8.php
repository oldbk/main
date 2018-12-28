<?
if (!($_SESSION['uid'] > 0)) header("Location: index.php");

$upgrade = array(
	"level" => 8,
	"hp" => 8,
	"bron" => 1,
	"stat" => 1,
	"mf" => 7,
	"udar" => 2,
	"nparam" => 1,
	"duration" => 5,
	"destiny" => false,
	"nintel" => 50
	);
if($_SERVER['PHP_SELF'] != '/repair.php')
{
	include "upgrade.php";
}
?>