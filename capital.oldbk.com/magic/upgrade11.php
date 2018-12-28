<?
if (!($_SESSION['uid'] > 0)) header("Location: index.php");

$upgrade = array(
	"level" => 11,
	"hp" => 15,
	"bron" => 1,
	"stat" => 1,
	"mf" => 15,
	"udar" => 1,
	"nparam" => 1,
	"duration" => 15,
	"destiny" => false,
	"nintel" => 50
	);

if($_SERVER['PHP_SELF'] != '/repair.php')
{
	include "upgrade.php";
}
?>