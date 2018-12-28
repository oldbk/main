<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

//на бой<br>Х ”ровень жизни +10HP<br>Х Cила +1<br>Х Ћовкость +1<br>Х »нтуици€ +1<br>Х ќпыт +10%<br>

$addstat1='maxhp';
$addvalue1=10;

$addstat2='sila';
$addvalue2=1;

$addstat3='expbonus';
$addvalue3=0.10; //10%

$addstat4='lovk';
$addvalue4=1;

$addstat5='inta';
$addvalue5=1;

$addtxt='”ровень жизни +10HP,Cила +1,Ћовкость +1,»нтуици€ +1,ќпыт +10%';

include "food_base_obed.php"
?>
