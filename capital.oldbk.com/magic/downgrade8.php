<?
if (!($_SESSION['uid'] > 0)) header("Location: index.php");

$upgrade = array(
	"level" => 9,
	"hp" => 10,
	"bron" => 1,
	"stat" => 1,
	"mf" => 10,
	"udar" => 3,
	"nparam" => 1,
	"duration" => 10,
	"destiny" => false,
	"nintel" => 0
	);

if($_SERVER['PHP_SELF'] != '/repair.php')
{
	include "downgrade.php";
}
?>