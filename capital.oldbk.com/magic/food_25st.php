<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


$addstat1='sila';
$addvalue1=25;

$addstat2='lovk';
$addvalue2=25;

$addstat3='intel';
$addvalue3=25;

$addstat4='inta';
$addvalue4=25;

$addstat5='mudra';
$addvalue5=25;

$addtxt='Cила +25,Ловкость +25,Интуиция +25,Интеллект +25,Мудрость +25';

include "food_base_obed.php";
	
	if ($bet==1)
		{
		$baff_type=3053;
		$baff_name="Большой ужин дракона";
		mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}', `time`='1999999999', `owner`='{$user[id]}'  ;");
		include "dragon_illuz.php";
		}	
?>
