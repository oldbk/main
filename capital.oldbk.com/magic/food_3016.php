<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

//на бой<br>Х ”ровень жизни +20HP<br>Х Ћовкость +1<br>Х ќпыт +10%<br>

$addstat1='maxhp';
$addvalue1=20;

$addstat2='lovk';
$addvalue2=1;

$addstat3='expbonus';
$addvalue3=0.10; //10%

$addstat4='';
$addvalue4=0;

$addstat5='';
$addvalue5=0;

$addtxt='”ровень жизни +20HP,Ћовкость +1,ќпыт +10%';

include "food_base_obed.php"
?>
