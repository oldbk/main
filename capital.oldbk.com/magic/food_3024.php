<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

//на бой<br>Х ”ровень жизни +30HP<br>Х »теллект +1<br>Х ќпыт +10%<br>

$addstat1='maxhp';
$addvalue1=30;

$addstat2='intel';
$addvalue2=1;

$addstat3='expbonus';
$addvalue3=0.10; //10%

$addstat4='';
$addvalue4=0;

$addstat5='';
$addvalue5=0;

$addtxt='”ровень жизни +30HP,»нтеллект +1,ќпыт +10%';

include "food_base_obed.php"
?>
