<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

//на бой<br>Х ”ровень жизни +70HP<br>Х Cила +2<br>Х Ћовкость +2<br>Х »нтуици€ +2<br>Х ќпыт +20%<br>

$addstat1='maxhp';
$addvalue1=70;

$addstat2='sila';
$addvalue2=2;

$addstat3='expbonus';
$addvalue3=0.20; //10%

$addstat4='lovk';
$addvalue4=2;

$addstat5='inta';
$addvalue5=2;

$addtxt='”ровень жизни +70HP,Cила +2,Ћовкость +2,»нтуици€ +2,ќпыт +20%';

include "food_base_obed.php"
?>
