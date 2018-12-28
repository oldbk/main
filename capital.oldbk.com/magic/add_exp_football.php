<?php
//ТИп эфекта  171

if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}
$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '171' LIMIT 1;")); 

if (!($effect['id']))
	{

//Футбол
$gametime['15.06']=array('france','honduras');
$gametime['16.06']=array('argentina','bosnia_herzegovina','iran','nigeria','germany','portugal');
$gametime['17.06']=array('brazil','mexico','ghana','united_states','belgium','algeria');
$gametime['18.06']=array('netherlands','chile','australia','spain','russia','korea');
$gametime['19.06']=array('cameroon','croatia','colombia','cote_d_ivoire','england','uruguay');
$gametime['20.06']=array('japan','greece','costa_rica','italy','switzerland','france');
$gametime['21.06']=array('ecuador','honduras','argentina','iran','germany','ghana');
$gametime['22.06']=array('bosnia_herzegovina','nigeria','russia','belgium','korea','algeria');
$gametime['23.06']=array('netherlands','chile','australia','spain','united_states','portugal');
$gametime['24.06']=array('brazil','cameroon','mexico','croatia','costa_rica','italy','england','uruguay');
$gametime['25.06']=array('colombia','cote_d_ivoire','japan','greece','bosnia_herzegovina','argentina','iran','nigeria');
$gametime['26.06']=array('switzerland','ecuador','honduras','france','germany','portugal','united_states','ghana');
$gametime['27.06']=array('russia','belgium','korea','algeria');
$gametime['28.06']=array('brazil','chile');
$gametime['29.06']=array('colombia','uruguay','netherlands','mexico');
$gametime['30.06']=array('costa_rica','greece','france','nigeria');
$gametime['01.07']=array('argentina','switzerland','germany','algeria');
$gametime['02.07']=array('belgium','united_states');
$gametime['04.07']=array('germany','france');
$gametime['05.07']=array('brazil','colombia','argentina','belgium');
$gametime['06.07']=array('netherlands','costa_rica');
$gametime['09.07']=array('germany','brazil');
$gametime['10.07']=array('argentina','netherlands');
$gametime['13.07']=array('brazil','netherlands','germany','argentina');



$flag[171171]='brazil';
$flag[171172]='mexico';
$flag[171173]='cameroon';
$flag[171174]='croatia';
$flag[171175]='netherlands';
$flag[171176]='chile';
$flag[171177]='australia';
$flag[171178]='spain';
$flag[171179]='colombia';
$flag[171180]='cote_d_ivoire';
$flag[171181]='japan';
$flag[171182]='greece';
$flag[171183]='costa_rica';
$flag[171184]='italy';
$flag[171185]='england';
$flag[171186]='uruguay';
$flag[171187]='switzerland';
$flag[171188]='ecuador';
$flag[171189]='france';
$flag[171190]='honduras';
$flag[171191]='argentina';
$flag[171192]='bosnia_herzegovina';
$flag[171193]='iran';
$flag[171194]='nigeria';
$flag[171195]='germany';
$flag[171196]='portugal';
$flag[171197]='ghana';
$flag[171198]='united_states';
$flag[171199]='russia';
$flag[171200]='belgium';
$flag[171201]='korea';
$flag[171202]='algeria';


	$nnf=date("d.m");	
	
	$arraf=$gametime[$nnf];
	$useflag=$flag[$rowm['prototype']];
	$mag_add_exp=0.1;
	
	
	if (in_array($useflag,$arraf))
		{
		$add_time_eff=time()+($magic['time']*60);
		//в  add_info  пишем - название страны $rowm
		mysql_query("INSERT INTO `effects` SET `type`=171,`name`='{$magic[name]}',`time`='{$add_time_eff}',`owner`='{$user[id]}', add_info='{$rowm['name']}' ;");
		mysql_query("UPDATE users set expbonus=expbonus+{$mag_add_exp} where id='{$user[id]}' ; ");
		echo "<font color=red>Удачно использована магия <b>\"Повышеный опыт\". {$magic[name]}</b></font>";
		$bet=1;
		$sbet = 1;
		}
		else
		{
		echo "<font color=red><b>Можно использовать только флаг страны, которая играет сегодня!</b></font>";
		}
	} else {
		echo "<font color=red><b>Вы уже использовали такую магию!</b></font>";
	}
		

?>