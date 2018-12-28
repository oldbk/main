<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


	//конфиг
	//4 level
	$addstat1[4]='maxhp';
	$addvalue1[4]=10;
	$addstat2[4]='sila';
	$addvalue2[4]=1;
	$addstat3[4]='expbonus';
	$addvalue3[4]=0.10; //10%
	$addstat4[4]='lovk';
	$addvalue4[4]=1;
	$addstat5[4]='inta';
	$addvalue5[4]=1;
	$addtxt[4]='Уровень жизни +10HP,Cила +1,Ловкость +1,Интуиция +1,Опыт +10%';
	
	//5 level
	$addstat1[5]='maxhp';
	$addvalue1[5]=15;
	$addstat2[5]='sila';
	$addvalue2[5]=1;
	$addstat3[5]='expbonus';
	$addvalue3[5]=0.10; //10%
	$addstat4[5]='lovk';
	$addvalue4[5]=1;
	$addstat5[5]='inta';
	$addvalue5[5]=1;
	$addtxt[5]='Уровень жизни +15HP,Cила +1,Ловкость +1,Интуиция +1,Опыт +10%';

	//6 level
	$addstat1[6]='maxhp';
	$addvalue1[6]=20;
	$addstat2[6]='sila';
	$addvalue2[6]=1;
	$addstat3[6]='expbonus';
	$addvalue3[6]=0.10; //10%
	$addstat4[6]='lovk';
	$addvalue4[6]=1;
	$addstat5[6]='inta';
	$addvalue5[6]=1;
	$addtxt[6]='Уровень жизни +20HP,Cила +1,Ловкость +1,Интуиция +1,Опыт +10%';

	//7 level
	$addstat1[7]='maxhp';
	$addvalue1[7]=30;
	$addstat2[7]='sila';
	$addvalue2[7]=1;
	$addstat3[7]='expbonus';
	$addvalue3[7]=0.10; //10%
	$addstat4[7]='lovk';
	$addvalue4[7]=1;
	$addstat5[7]='inta';
	$addvalue5[7]=1;
	$addtxt[7]='Уровень жизни +30HP,Cила +1,Ловкость +1,Интуиция +1,Опыт +10%';

	//8 level
	$addstat1[8]='maxhp';
	$addvalue1[8]=50;
	$addstat2[8]='sila';
	$addvalue2[8]=1;
	$addstat3[8]='expbonus';
	$addvalue3[8]=0.10; //10%
	$addstat4[8]='lovk';
	$addvalue4[8]=1;
	$addstat5[8]='inta';
	$addvalue5[8]=1;
	$addtxt[8]='Уровень жизни +50HP,Cила +1,Ловкость +1,Интуиция +1,Опыт +10%';
	
	//9 level
	$addstat1[9]='maxhp';
	$addvalue1[9]=60;
	$addstat2[9]='sila';
	$addvalue2[9]=1;
	$addstat3[9]='expbonus';
	$addvalue3[9]=0.10; //10%
	$addstat4[9]='lovk';
	$addvalue4[9]=1;
	$addstat5[9]='inta';
	$addvalue5[9]=1;
	$addtxt[9]='Уровень жизни +60HP,Cила +1,Ловкость +1,Интуиция +1,Опыт +10%';	

	//10 level
	$addstat1[10]='maxhp';
	$addvalue1[10]=70;
	$addstat2[10]='sila';
	$addvalue2[10]=1;
	$addstat3[10]='expbonus';
	$addvalue3[10]=0.10; //10%
	$addstat4[10]='lovk';
	$addvalue4[10]=1;
	$addstat5[10]='inta';
	$addvalue5[10]=1;
	$addtxt[10]='Уровень жизни +70HP,Cила +1,Ловкость +1,Интуиция +1,Опыт +10%';	
	
	//11 level
	$addstat1[11]='maxhp';
	$addvalue1[11]=80;
	$addstat2[11]='sila';
	$addvalue2[11]=1;
	$addstat3[11]='expbonus';
	$addvalue3[11]=0.10; //10%
	$addstat4[11]='lovk';
	$addvalue4[11]=1;
	$addstat5[11]='inta';
	$addvalue5[11]=1;
	$addtxt[11]='Уровень жизни +80HP,Cила +1,Ловкость +1,Интуиция +1,Опыт +10%';	

	//12 level
	$addstat1[12]='maxhp';
	$addvalue1[12]=90;
	$addstat2[12]='sila';
	$addvalue2[12]=1;
	$addstat3[12]='expbonus';
	$addvalue3[12]=0.10; //10%
	$addstat4[12]='lovk';
	$addvalue4[12]=1;
	$addstat5[12]='inta';
	$addvalue5[12]=1;
	$addtxt[12]='Уровень жизни +90HP,Cила +1,Ловкость +1,Интуиция +1,Опыт +10%';	
	
	//13 level
	$addstat1[13]='maxhp';
	$addvalue1[13]=100;
	$addstat2[13]='sila';
	$addvalue2[13]=1;
	$addstat3[13]='expbonus';
	$addvalue3[13]=0.10; //10%
	$addstat4[13]='lovk';
	$addvalue4[13]=1;
	$addstat5[13]='inta';
	$addvalue5[13]=1;
	$addtxt[13]='Уровень жизни +100HP,Cила +1,Ловкость +1,Интуиция +1,Опыт +10%';	

	//14 level
	$addstat1[14]='maxhp';
	$addvalue1[14]=110;
	$addstat2[14]='sila';
	$addvalue2[14]=1;
	$addstat3[14]='expbonus';
	$addvalue3[14]=0.10; //10%
	$addstat4[14]='lovk';
	$addvalue4[14]=1;
	$addstat5[14]='inta';
	$addvalue5[14]=1;
	$addtxt[14]='Уровень жизни +110HP,Cила +1,Ловкость +1,Интуиция +1,Опыт +10%';	

	$lvl_conf=$user['level'];
	if ($lvl_conf<4)     $lvl_conf=4;
	if ($lvl_conf>14)   $lvl_conf=14;	


$addstat1=$addstat1[$lvl_conf];
$addvalue1=$addvalue1[$lvl_conf];

$addstat2=$addstat2[$lvl_conf];
$addvalue2=$addvalue2[$lvl_conf];

$addstat3=$addstat3[$lvl_conf];
$addvalue3=$addvalue3[$lvl_conf];

$addstat4=$addstat4[$lvl_conf];
$addvalue4=$addvalue4[$lvl_conf];

$addstat5=$addstat5[$lvl_conf];
$addvalue5=$addvalue5[$lvl_conf];

$addtxt=$addtxt[$lvl_conf];



include "food_base_obed.php"
?>
 