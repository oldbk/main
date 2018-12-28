<?php
//компресия 
///////////////////////////
if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
	$miniBB_gzipper_encoding = 'x-gzip';
}
if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
	$miniBB_gzipper_encoding = 'gzip';
}
if (isset($miniBB_gzipper_encoding)) {
	ob_start();
}
function percent($a, $b) {
	$c = $b/$a*100;
	return $c;
}
//////////////////////////////
$NEW_ABIL=1;
session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
include "connect.php";
include "functions.php";
include "config_myabil.php";

if ($user['room'] == 76)  { header('Location: class_armory.php'); die(); }

//фильтр на абилки склонок
$get_eff=mysql_query("select * from effects where owner='{$user[id]}'  and  type in (10904,10903,10902,10901) ");
	if (mysql_num_rows($get_eff) > 0)
	{
		while ($row = mysql_fetch_array($get_eff))
		{
		$user_eff[$row['type']]=$row;
		}
	}
	
$user_stih=get_mag_stih($user,$user_eff);
$filt_mag=array(1=> 5007152,2=>	5007154, 3=>5007153,4=>5007155);


// универсальная банковская авторизация
if (isset($_GET['view'],$_GET['link']) && $_GET['view'] == "bankauth") {
	if (!isset($_GET['type'])) $_GET['type'] = 0;
	$error = 0;
	if (isset($_GET['bankpass'],$_GET['bankid'])) {
		$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id'].' and id = '.intval($_GET['bankid']).' and pass = "'.md5($_GET['bankpass']).'"');	
		if (mysql_num_rows($q) > 0) {
			$_SESSION['bankid'] = intval($_GET['bankid']);					
			if (isset($_GET['type']) && intval($_GET['type']) == 1) {
				$bank = mysql_fetch_assoc($q);
				echo '<script>closeinfo();bankauth = true;bankbalance = '.$bank['ekr'].';CalcAll();</script>';
			} else {
				$_GET['link'] = str_ireplace('://',"",$_GET['link']);
				echo '<script>closeinfo();bankauth = true;location.href="'.$_GET['link'].'";</script>';
			}
			die();
		} else {
			$error = 1;
		}
	}                                  

	$auth =  '<table border=0 width=400 height=100><tr><td  valign=top align="center" height=5 colspan="4"><font style="COLOR:#8f0000;FONT-SIZE:12pt">'; 
	$auth .= "Авторизация в банке";
	$auth .= '</font><a onClick="closeinfo();" title="Закрыть" style="cursor: pointer;" >
	<img src="http://i.oldbk.com/i/bank/bclose.png" style="position:relative;top:-20px;right:-220px;" border=0 title="Закрыть"></a></td></tr>
	<tr><td colspan="4" class="center" valign=top>';   


	$q = mysql_query('SELECT * FROM bank WHERE owner = '.$user['id']);
	if (mysql_num_rows($q) > 0) {
		$auth .= '<select id="bankid" style="width:100px" name="bankid">';
		while ($rah = mysql_fetch_array($q)) {
			$auth .= "<option>".$rah['id']."</option>";
		}
		$auth .= "</select> ";
		$auth .= 'Пароль: <input type=password name="bankpass" id="bankpass" style="width:100px"> <button style="height:23px;" OnClick="doauth(\''.$_GET['link'].'\','.intval($_GET['type']).');">Войти</button>';

	} else {
		$auth .= '<font color="red">Банковские счета не найдены</font>';
	}

	if ($error > 0) {
		$auth .= '<br><font color="red">Не правильный пароль</font>';				
	}

	$auth .= '</td>
		</tr><tr><td align="center"  colspan="3">
		</td></tr>
		</table>';
	echo $auth;
	die();
}


	if ($_SESSION['bankid']>0)
	{
	$bank = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`bank` WHERE `id` = ".$_SESSION['bankid'].";"));
	}


if ($user['align'] == 3) {
	require('./magic/moon.php');
	$mp = new moonPhase("");
	$fullmoon = false;
	if ($mp->getPhaseName() == "Full Moon")	$fullmoon = true;
} else {
	$fullmoon = false;
}

function get_pass_active($pabid) {
	global $user;
	$deny_rooms=array(197,198,199,211,212,213,214,215,216,217,218,219,220,221,222,271,272,273,274,275,276,277,278,279,280,281,282,241,242,243,244,245,246,247,248,249,250);
	if ((in_array($user[room],$deny_rooms)) OR ($user[in_tower] >0)) {
		return false;
	}

	if  ($pabid==862) { return true; }

	$HH=(int)(date("H",time()));
	if (($HH>=9) and ($HH<21)) {
		//echo "День";
		if (($pabid==850) or ($pabid==851) or ($pabid==852) or ($pabid==861)) {
			return true;
		}
	} else {
		if (($pabid==840) or ($pabid==841) or ($pabid==842) or ($pabid==860)) {
			return true;
		}
		//echo "Ночь"; снимаем для травмы на 10%
	}
	
	return false;
}

function render_new_abil() {
	global $user;	
	$deny_rooms=array(197,198,199,211,212,213,214,215,216,217,218,219,220,221,222,271,272,273,274,275,276,277,278,279,280,281,282,241,242,243,244,245,246,247,248,249,250);

	include "abiltxt.php";

	if (($_POST['abit']=='abil') and ($_POST['use'])) {
		//проверка на баф 805
		$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and (type=805 ) ; "));
	 	if ($get_test_baff[id]>0) {
	 		if ($get_test_baff[type]==805) {
		 		err("Вы не можете использовать личные реликты, еще ".floor(($get_test_baff['time']-time())/60/60)." ч. ".round((($get_test_baff['time']-time())/60)-(floor(($get_test_baff['time']-time())/3600)*60))." мин.");
			}
	 		err('<br> На Вас наложено заклятие "'.$get_test_baff[name].'"');
	 	} elseif (in_array($user[room],$deny_rooms)) {
	 		err('Вы не можете использовать абилити в ристалище!');
	 	} elseif ($user['in_tower'] > 0) {
	 		err('Вы не можете использовать абилити тут!');
	 	} else {
			$abaid=(int)$_POST['use'];
			$get_abil=mysql_fetch_array(mysql_query("select * from oldbk.users_babil ab LEFT JOIN magic m ON ab.magic=m.id where ab.owner='{$user[id]}' and ab.magic='{$abaid}' and  ab.btype=0 ;"));
			if ($get_abil[magic]>0) {
				$ABIL=1;
				echo "<font color=red>";
		
				include("./magic/".$get_abil[file]);
				
				echo "</font><br><br>";		
			} else {
				err('У Вас нет такой магии!');
			}
	
			unset($_POST['use']);
		}
	}
	
	$n=array();
	$b=array();

	if ($user[pasbaf]>0) {
		$get_pasabil=mysql_fetch_array(mysql_query("select *  from magic  where id='{$user[pasbaf]}'  ;"));	
		$epab='<tr valign=top><td align="center"><img border=0 src="http://i.oldbk.com/i/magic/'.$get_pasabil[img].'"   onMouseOut="HideThing(this);" onMouseOver="ShowThing(this,25,25,\''.$atext[$user[pasbaf]].'\');" >&nbsp;<br><font color="red"><b>'.((get_pass_active($get_pasabil[id]))?"Активен":"Неактивен").'</b></font></td>';
	} else {
		$epab='<tr valign=top><td align="center"  width=45>&nbsp;<br></td>';
	}
	
	$get_babil=mysql_query("select * from oldbk.users_babil ab LEFT JOIN magic m ON ab.magic=m.id where ab.owner='{$user[id]}'  order BY  btype ;");
	if (mysql_num_rows($get_babil)>0) {
		while($abilrow=mysql_fetch_array($get_babil)) {
			if ($abilrow[btype]==0) {
				$n[]="<td align=\"center\" width=50><a onclick=\"javascript:new_runmagic('".$abilrow[name]."','".$abilrow[magic]."','target','target1','".$abilrow[targeted]."','abil'); \" href='#'><img src='http://i.oldbk.com/i/magic/".$abilrow[img]."'  onMouseOut=\"HideThing(this);\" onMouseOver=\"ShowThing(this,25,25,'".$atext[$abilrow[id]]."');\" ></a>&nbsp;</td>";
			} else {
				$b[]="<td align=\"center\" width=50> <img border=0 src='http://i.oldbk.com/i/magic/".$abilrow[img]."' onMouseOut=\"HideThing(this);\" onMouseOver=\"ShowThing(this,25,25,'".$atext[$abilrow[id]]."');\" >&nbsp;<br>$abilrow[dur] /  $abilrow[maxdur] </td>";
			}
		}
	
		echo '<center><table border=1 cellpadding=2 style="width:550px;">
			<tr>
			<td  align="center" width=80><b>Пассивные</b></td>
			<td colspan=4 align="center"><b>Боевые</b></td>
			<td colspan=2 align="center"><b>Небоевые</b></td></tr>';
		echo $epab;
		for ($bb=0;$bb<=3;$bb++) {
			if ($b[$bb]) {
				echo $b[$bb];
			} else {
				echo "<td align=\"center\" width=45>&nbsp;<br></td>";
			}
		}

		for ($nn=0;$nn<=1;$nn++) {
			if ($n[$nn]) {
				echo $n[$nn];
			} else {
				echo "<td align=\"center\" width=45>&nbsp;<br></td>";
			}
		}

		echo '</tr></table></center>';
		echo "<hr>";
	} else {
		err ("<center>У Вас не установлены абилити склонности.<br>Посетите Хижину Знахаря для установки!</center>");
	}
}

function render_books(){
global $user;
require('cards_config.php');

	if ((int)($_GET[usebook])>0)
	{
	$bookid=(int)($_GET[usebook]);
	
	$mbook=mysql_fetch_array(mysql_query("select * from oldbk.inventory where owner='{$user[id]}' and id='{$bookid}' and prototype in (3003131,3003132,3003133,3003134,3003135)  LIMIT 1;"));
		if ($mbook[id]>0)
		{
			if ($user[battle]>0)  
			{
			//проверка боя
			 $test_battle=mysql_fetch_array(mysql_query("select * from battle where id='{$user[battle]}' "));
			
				if ($user[id]==14897)  
				{
					usemagic($bookid,'');
				}
				else
				if ($test_battle[nomagic]>0)
				{
				err('В этом бою нельзя использовать книги!');
				}
				else		
				if ( ($mbook[prototype]==3003134)  OR ($mbook[prototype]==3003135) )
				{
					usemagic($bookid,'');
				}
				else
				{
				err('Эту книгу нельзя  использовать в бою!');
				}
			}
			else
			{
			usemagic($bookid,'');
			}
		}
		else
		{
		err('Книга не найдена!');
		}
	
	
	}
	elseif ((int)($_GET[usecards])>0)
	{
		if ((time()>$coll3_start) and (time()<$coll3_end)) 
		{
		$cid=(int)($_GET[usecards]);
		
			$mycard=mysql_fetch_array(mysql_query("select * from effects where  id='{$cid}' and  owner='{$user[id]}' and  type=113010 LIMIT 1"));
			if (($mycard['id']>0) and ((int)$mycard['lastup']<(int)$mycard['add_info']))
				{
				if ($user[battle]>0)  
						{
						//проверка боя
						 $test_battle=mysql_fetch_array(mysql_query("select * from battle where id='{$user[battle]}' "));

							if ($test_battle[nomagic]>0)
							{
							err('В этом бою нельзя использовать магию!');
							}
							else		
							{
							$bet=0;
								include("fsystem.php"); //использование коллекции
								include("./magic/113010.php"); //использование коллекции
								
								if ($bet==1)
									{
									mysql_query("update  effects set `lastup`=`lastup`+1  where  id='{$cid}' and  owner='{$user[id]}' and  type=113010");
									}
							
							}

						}
						else
						{
							err('Это боевая магия!');
						}
				}
		}

	}


$mybooks=mysql_query("select * from oldbk.inventory where owner='{$user[id]}' and prototype in (3003131,3003132,3003133,3003134,3003135) and setsale = 0 ORDER BY `update` DESC");
if (mysql_num_rows($mybooks) > 0) 
	{
	echo "<h3>Доступные личные книги!</h3>";
	$k=0;
		while($book=mysql_fetch_array($mybooks)) 
		{
		$k++;
		$text="Использовать:<b>".$book[name]."</b><br>Долговечность: ".$book[duration]."/".$book[maxdur]." <br>";
		echo "<a href=?usebook=".$book[id]."><img border=0 src=http://i.oldbk.com/i/sh/".$book[img]." onMouseOut=\"HideThing(this);\" onMouseOver=\"ShowThing(this,60,60,'".$text."');\" ></a>";
		echo "&nbsp;" ;
		}
	}


	if ((time()>$coll3_start) and (time()<$coll3_end)) 
	{
	
		$mycards=mysql_query("select * from effects where   owner='{$user[id]}' and  type=113010");
		if (mysql_num_rows($mycards) > 0) 
			{

			$k=0;
				while($book=mysql_fetch_array($mycards)) 
				{
			
				$k++;
		
				
				if ((int)$book['lastup']==(int)$book['add_info'])
					{
					$text="<b>".htmlspecialchars($coll3['113000']['name'])."</b><br>Использованно: ".(int)$book['lastup']."/".(int)$book['add_info']." <br>";					
					echo '<img class="gift-image" id="imginv'.$book['id'].'" style="opacity: 0.2;cursor:pointer;" src="http://i.oldbk.com/i/sh/'.$coll3['113000']['img']."\" onMouseOut=\"HideThing(this);\" onMouseOver=\"ShowThing(this,60,60,'".$text."');\" >";
					}
					else
					{
					$text="Использовать:<b>".htmlspecialchars($coll3['113000']['name'])."</b><br>Осталось: ".(int)$book['lastup']."/".(int)$book['add_info']." <br>";					
					echo "<a href=?usecards=".$book[id]."><img border=0 src=http://i.oldbk.com/i/sh/".$coll3['113000']['img']." onMouseOut=\"HideThing(this);\" onMouseOver=\"ShowThing(this,60,60,'".$text."');\" ></a>";
					}
				echo "&nbsp;" ;
				}
			
			
			
			}	
	}


}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<title>Old BK - Личные реликты</title>	
	<script type="text/javascript" src="/i/globaljs.js"></script>    
	<script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script type='text/javascript' src='http://i.oldbk.com/i/js/recoverscroll.js'></script>	
	<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/jscal2.css" />
	<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/border-radius.css" />
	<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/steel/steel.css" />
	<link rel="stylesheet" type="text/css" href="i/main.css">	

	
	<link rel="stylesheet" type="text/css" href="http://capitalcity.oldbk.com/newstyle_loc4.css" />
	<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/jscal2.js"></script>
	<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>
	<script type="text/javascript" src="i/showthing.js"></script>
	<script type="text/javascript" src="/i/globaljs.js"></script>
	<style>
		.row {
			cursor:pointer;
		}
	</style>
	<script>
	<?php
	include("jsfunction.php");
	?>
RecoverScroll.start();	
var bankauth = <?php echo (isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0) ? "true" : "false" ?>;
var bankbalance = <?php echo (isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0) ? $bank['ekr'] : "false" ?>;

	

function doauth(mylink,type) {
	var bankid = $('#bankid').val();
	var bankpass = $('#bankpass').val();

	$.get('?view=bankauth&link='+encodeURIComponent(mylink)+'&type='+type+'&bankid='+bankid+'&bankpass='+bankpass, function(data) {
		$('#pl').show(200);
		$('#pl').html(data);
		$("input, select, button").bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
		    e.stopImmediatePropagation();
		});
	});
					
	return true;
}

function checkbank(mylink,type) {
	if (bankauth) {
		if (type != 1) {
			location.href = mylink;
		}
		return true;
	} else {
		$.get('?view=bankauth&link='+encodeURIComponent(mylink)+'&type='+type, function(data) {
			$('#pl').html(data);
			
			
		 	$('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: +'600px'  });
		 	

		 	
			$('#pl').show(200);

			$("input, select, button").bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e){
			    e.stopImmediatePropagation();
			});
		});
					
		return false;
	}
}


function CalcAll() {
	 total_price = 0;
	d = document;
	var els=d.getElementsByTagName('input');
	 for( var el, i = 0; el = els[ i++ ]; ) {

	        if ( /^pricet/.test( el.id ) ) {

	            total_price += Number( el.value );

	        }

	    }
	 
d.getElementById("calcsum").innerHTML = "<b>"+total_price+"</b> екр.";

if (bankauth)
	{
	 d.getElementById("pbutton").innerHTML = 'В банке: <font color=green><b>'+bankbalance+'</b></font> екр.<br><a href="javascript:void(0);" class="button-big btn" title="Оплатить" onClick="document.byabils.submit();">Оплатить</a>';
	}
}
	

function OnPChange(t) {
	d = document;
	val = d.getElementById("price_"+t);
	if (val) {
		val = parseFloat(val.value);
		if (val > 0) {
			p = d.getElementById("pricev_"+t).value;
			sum = val * p;
			d.getElementById("pricet_"+t).value = sum;
		} else {
			d.getElementById("pricet_"+t).value = 0;
		}
	}
CalcAll();
}


function closeinfo() {
	$('#pl').hide(200);
}
			
$(window).resize(function() {
 $('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: ($(window).scrollTop()+500)+'px'  });
});


	
	</script>
</head>
<body id="arenda-body" leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 >
<div id="page-wrapper">
<div align=center id=hint3></div>
<div id="pl" style="z-index: 300; position: absolute; left: 155px; top: 500px;
				width:600px; background-color: #eeeeee; cursor: move;
				border: 1px solid black; display: none;">
</div>
	<?php 
	if ($fullmoon && $user['align'] == 3) 
	{
		echo '<div style="position:absolute;text-align:right;width:100%;z-index:-1;"><img style="margin-right:110px;" src="http://i.oldbk.com/i/moon.jpg" align="right"></div>';
	} 
	?>
	    <div class="title">
	        <div id="buttons">
	            <a class="button-dark-mid btn" href="javascript:void(0);" title="Подсказка" onclick="window.open('help/myabil.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes');">Подсказка</a>            
			<?
			if ($user['align'] == 4) { echo "<INPUT TYPE=\"button\" onClick=\"location.href='haosexit.php';\" value=\"НЕ ХОЧУ БЫТЬ В ХАОСЕ!\" title=\"НЕ ХОЧУ БЫТЬ В ХАОСЕ!\" style=\"background-color:red;color:white;font-weight:bold;\">"; }
			 ?>	            
	            <a class="button-mid btn" href="javascript:void(0);" title="Обновить" onclick="location.href='myabil.php?refresh='+Math.random();" >Обновить</a>                    
	            <a class="button-mid btn" href="javascript:void(0);" title="Вернуться" onclick="location.href='main.php';">Вернуться</a>
	        </div>
	    </div>
	 
	 
	 
<table align=center width=95% border=0>
	<tr>
	<td>
<?php

function render_my_abils()
{
include "config_myabil.php"; //перечитываем
global  $print_out,$text_abil;
foreach($print_out[1] as $idmag=>$arr)
				{
//echo $idmag;				
						$imgg=$text_abil[str_ireplace('../sh/',"",$arr['img'])];
						
						/*if ($imgg!='')
							{
							//echo str_ireplace('%TITLE%',$imgg,$arr['code']);
							echo str_ireplace('%TITLE%',$abilcfg[$idmag]['desc'],$arr['code']);
							}
							else
							{
							echo str_ireplace('%TITLE%',$arr['name'],$arr['code']);
							}
						*/	
						if ($abilcfg[$idmag]['desc']!='')
							{
							echo str_ireplace('%TITLE%',$abilcfg[$idmag]['desc'],$arr['code']);
							}
							else
							{
							echo str_ireplace('%TITLE%',$arr['name'],$arr['code']);
							}
						echo " ";	

				}

}

function render_list_abil($btype=0)
{
global $abilcfg, $print_out,$text_abil,$filt_mag,$user_stih;
	//выводим те которых нет в продаже - "Они всегда наверху над покупными."

		
			foreach($print_out[1] as $idmag=>$arr)
				{
				if ($arr['type']==$btype) // выводим не боевые
				{
					if (!(is_array($print_out[2][$idmag])))
						{
						echo "<tr>";
						echo "<td style=\"vertical-align: middle;\">";	
						
						$imgg=$text_abil[str_ireplace('../sh/',"",$arr['img'])];
						if ($imgg!='')
							{
							echo str_ireplace('%TITLE%',$imgg,$arr['code']);
							}
							else
							{
							echo str_ireplace('%TITLE%',$arr['name'],$arr['code']);
							}
						echo "</td>";	
						echo "<td style=\"vertical-align: middle;\">";		
							echo $arr['name'];
						echo "</td>";			  	
						echo "<td style=\"vertical-align: middle;\">";		
							echo $arr['allcount']." шт.";
						echo "</td>";
						echo "<td style=\"vertical-align: middle;\">";		
							if ($arr['daily']!='') {  echo $arr['daily']; }
						echo "</td>";
												
						echo "<td bgcolor=\"#A5A5A5\" style=\"vertical-align: middle;text-align:center;\" >";	
							
						if (($idmag==4848) or ($idmag==4847) )
							{
							echo "Доступно с <a href='http://oldbk.com/encicl/?/prem.html' target=_blank>«Platinum»</a> аккаунт";
							}
							else
							{
							echo "";
							}
						echo "</td>";
						echo "<td style=\"vertical-align: middle;\">";		
							echo '<input size=5 readonly type=text  style="height:21px;">';
						echo "</td>";
						echo "</tr>";
						
						}
				  }
				
				}
				
$myabilcfg=$abilcfg;

foreach($filt_mag as $sth=>$mid)	
		{
			if (!(in_array($sth,$user_stih)))
				{
				unset($myabilcfg[$mid]);
				}
		}				

			//выводим  остальные
			foreach($abilcfg as $idmag=>$info)
				{
					
				//if  ((!(is_array($myabilcfg[$mid]))) AND (!(is_array($print_out[1][$idmag]))) ) continue; //прячем если не купленна и не наша магия
				
				
				if ($info['type']==$btype) // выводим не боевые
				  {
				   if ( (!($info['cost']>0)) and ($print_out[1][$idmag]['allcount']==0) )
				   	{
				   	// нет цены и нет количества = не выводим
				   	}
				   else
				   	{
				  
				  				
					if (is_array($print_out[1][$idmag]))
						{
						$arr=$print_out[1][$idmag];
						//уже есть купленна
						echo "<tr>";
						echo "<td style=\"vertical-align: middle;\">";	
						
						$imgg=$text_abil[str_ireplace('../sh/',"",$info['img'])];
						if ($imgg!='')
							{
							echo str_ireplace('%TITLE%',$imgg,$arr['code']);
							}
							else
							{
							echo str_ireplace('%TITLE%',$info['desc'],$arr['code']);
							}
								
						echo "</td>";	
						echo "<td style=\"vertical-align: middle;\">";		
							echo $info['desc'];
						echo "</td>";			  	
						echo "<td style=\"vertical-align: middle;\">";		
							echo $arr['allcount']." шт.";
						echo "</td>";
						echo "<td style=\"vertical-align: middle;\">";		
							if ($arr['daily']!='') {  echo $arr['daily']; } 
						echo "</td>";
						
						}
						else
						{
						//нет
						echo "<tr >";
						echo "<td style=\"vertical-align: middle;\">";	
								echo  link_for_magic($info['img'],"<img src='http://i.oldbk.com/i/magic/".$info['img']."' title='".$text_abil[str_ireplace('../sh/',"",$info['img'])]."'>");
						echo "</td>";	
						echo "<td style=\"vertical-align: middle;\">";	
							echo $info['desc'];
						echo "</td>";			  	
						echo "<td style=\"vertical-align: middle;\">";		
							echo "0 шт.";
						echo "</td>";
						echo "<td>";	
						echo "</td>";
						
						}
						
						if ($info['cost']>0)
							{
							echo "<td bgcolor=\"#A5A5A5\" style=\"vertical-align: middle;text-align:center;\" >";	
							echo "<strong>".$info['kol']." шт.</strong> за ".$info['cost']." екр. ";
							echo "</td>";
							echo "<td style=\"vertical-align: middle;\">";		
							echo '<input type="hidden" id="pricev_'.$idmag.'" value="'.$info['cost'].'">';
							echo '<input size=5 value=0 onkeyup="OnPChange('.$idmag.');" name="price_'.$idmag.'" type=text id="price_'.$idmag.'" style="height:21px;">';
							echo '<input type=hidden id="pricet_'.$idmag.'" value=0>';
							echo "</td>";
							}
							else
							{
							echo "<td bgcolor=\"#A5A5A5\" style=\"vertical-align: middle;text-align:center;\" >";	
							echo "</td>";
							echo "<td style=\"vertical-align: middle;\">";		
							echo '<input size=5 readonly type=text  style="height:21px;">';
							echo "</td>";
							}
						echo "</tr>";
					}
				  }	
				}
					
}



function render_new_op() {
global $user;
	if ($user[level]>=9)
	{
	$res = GetOpRs();
	
		echo "<table border=0 width=100%>";
		echo "<tr style=\"vertical-align:middle;\">";
		echo "<td style=\"text-align: left;\">";		

			if ($res == 6) 
			{
				echo '<img src="http://i.oldbk.com/i/i/pr1.jpg">';	
			} else {
				echo '<img src="http://i.oldbk.com/i/i/pr'.$res.'.jpg">';	
			}
		echo "</td>";
		echo "<td style=\"text-align: right;\">";		

			$op = CheckOpDay();
								/*if (($user['klan']=='radminion') or ($user['klan']=='Adminion'))
									{
									$op = true;
									}
								*/
											
	
				if ($_POST['use']=='opposition' && $op == true) 
				{
				
								echo "<font color=red>";
								include("./magic/opposition.php");
								echo "</font><br><br>";
				}

				$k="opposition_off";
				$magic_name="Противостояние";
				if ($op == true) 
				{
				// противостояние
				$script_name="1"; $magic_name="Противостояние";
				$k="opposition";
				echo "<a onclick=\"javascript:new_runmagic('$magic_name','$k','target','target1','$script_name'); \" href='#'><img src='http://i.oldbk.com/i/icon_opposition_on.png' title='".$magic_name."' style=\"vertical-align:middle;float: right;margin: 7px 0 7px 7px;\"></a>";
				echo "<br><font color=red><b>День противостояния</b></font><br>Сегодня";
				} else 
				{
				// пишем когда начнётся наш день

				echo "<img src='http://i.oldbk.com/i/icon_opposition_off.png' title='".$magic_name."' style=\"vertical-align:middle;float: right;margin: 7px 0 7px 7px;\">";
				echo "<br><font color=red><b>День противостояния</b></font><br>";
				$v = mysql_fetch_array(mysql_query('SELECT * FROM oldbk.variables WHERE var = "opposition_today"'));
				$st=str_replace('/', '-', $v['value'])." 06:00";
				$next_op_time = strtotime($st);
					if (date("d/m/Y",time()+(24*3600*4)) == $v['value']) {
						echo "через ".prettyTime(null,mktime(6,0,0));	
					} else {
						echo "через ".prettyTime(null,$next_op_time);	
					}

				}
	}	
	echo "</td>";			
	echo "</tr>";
	echo "</table>";		
	
}

echo "</td></tr></table>";
//абилки по клонкам
////////////////////////////////////////

if ($user['align'] == '5') {
	echo "<h3><img src=http://i.oldbk.com/i/align_".$user['align'].".gif> Абсолютный хаос</h3>";

	render_new_op();
	render_new_abil();	
} else if ($user['align'] == '3') {
	//темные
	if ($fullmoon == true) {
		if ($user['sex'] == 1) { 
			echo '<h3>Полнолуние настало, собрат '.$user['login'].'!</h3>';
		} else {
			echo '<h3>Полнолуние настало, сестра '.$user['login'].'!</h3>';
		}
	} else {
		if ($user['sex'] == 1) { 
			echo "<h3><img src=http://i.oldbk.com/i/align_".$user['align'].".gif>Мусорщик с нами, собрат {$user['login']}!</h3>";
		} else {
			echo "<h3><img src=http://i.oldbk.com/i/align_".$user['align'].".gif>Мусорщик с нами, сестра {$user['login']}!</h3>";
		}
	}
	render_new_op();
	render_new_abil();	
} elseif (($user['align'] == '6') or ($user['klan'] == 'pal')) {
	//светлые+паладины
	if ($user['sex'] == 1) {
		echo "<h3><img src=http://i.oldbk.com/i/align_".$user['align'].".gif> Да пребудет с тобой сила, брат {$user['login']}!</h3>";
	} else {
		echo "<h3><img src=http://i.oldbk.com/i/align_".$user['align'].".gif> Да пребудет с тобой сила, сестра {$user['login']}!</h3>";
	}
	render_new_op();
	render_new_abil();	
} elseif ((int)($user['align']) == '2' || $user['klan'] == 'Radminion') {
	//нейтралы/админы
	if ($user['sex'] == 1) {
		echo "<h3> <img src=http://i.oldbk.com/i/align_".$user['align'].".gif>Да пребудет с тобой сила, брат {$user['login']}!</h3>";
	} else {
		echo "<h3> <img src=http://i.oldbk.com/i/align_".$user['align'].".gif>Да пребудет с тобой сила, сестра {$user['login']}!</h3>";
	}
	render_new_op();
	render_new_abil();	
} elseif ((int)($user['align']) == '4') {
	//хаосники
	if ($user['sex'] == 1) {
		echo "<h3><img src=http://i.oldbk.com/i/align_".$user['align'].".gif> Склонность хаоса.</h3>";
	} else {
		echo "<h3><img src=http://i.oldbk.com/i/align_".$user['align'].".gif> Склонность хаоса.</h3>";
	}
	
	echo "<img src='http://i.oldbk.com/i/magic/haos.gif' title='\"Получаемый опыт -50%\"'> - \"Получаемый опыт -50%\"";
	render_new_op();
	render_new_abil();	
} else {
	//новые абилки
	render_new_abil();	
}

echo "<center>";
render_books();
echo "</center>";
/////////////////////////////////////////

echo "<hr>";
echo "<table>";
echo "<tr><td>";
echo "<h3> Личные реликты!</h3>";

$deny_rooms=array(197,198,199,211,212,213,214,215,216,217,218,219,220,221,222,271,272,273,274,275,276,277,278,279,280,281,282,241,242,243,244,245,246,247,248,249,250);

//проверка на баф 805
$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}'  and (type=805 ) ; "));
if ($get_test_baff[id]>0) {
	if ($get_test_baff[type]==805) {
		err("Вы не можете использовать личные реликты, еще ".floor(($get_test_baff['time']-time())/60/60)." ч. ".round((($get_test_baff['time']-time())/60)-(floor(($get_test_baff['time']-time())/3600)*60))." мин.");
	}
	err('<br> На Вас наложено заклятие "'.$get_test_baff[name].'"');
} elseif (in_array($user[room],$deny_rooms)) {
	err('Вы не можете использовать абилити в ристалище!');
} elseif ($user['in_tower'] > 0) {
	err('Вы не можете использовать абилити тут!');
} else {
	$magic_use_id=(int)($_POST['use']);
	if ($magic_use_id > 0) {
		echo "<font color=red>";
	 	//use_magic
	 	$get_mag=mysql_fetch_array(mysql_query("select * from oldbk.magic where id='{$magic_use_id}' ;"));
		if (($get_mag[id]>0) and ($get_mag[file]!='')) {
	 	 	//есть такая магия
	 	 	//проверяем наличие

	 	 	$get_abil=mysql_fetch_array(mysql_query("select * from oldbk.users_abils where owner='{$user[id]}' and magic_id='{$magic_use_id}'  ;"));
	 	 	if (($get_abil[magic_id]>0) and (($get_abil[allcount]>0)OR($get_abil[dailyc]>0)) ) {
	 	 		//переменные для идентификации абилок
	 	 		$ABIL=1;
	 	 		$PERSON=1;
	 	 		$klan_abil=1;
		 	 	
				include("./magic/".$get_mag[file]);
				if ($sbet==1) 
				{
					if ($get_abil[dailyc]>0) { // если есть суточные то -1 к соточным
						mysql_query("update oldbk.users_abils set dailyc=dailyc-1  where owner='{$user[id]}' and magic_id='{$magic_use_id}' ;");						
					} else {
						mysql_query("update oldbk.users_abils set allcount=allcount-1  where owner='{$user[id]}' and magic_id='{$magic_use_id}' ;");
					}
				

					$rec=array();
  		    			$rec['owner']=$user['id'];
					$rec['owner_login']=$user['login'];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0;
					$rec['target_login']='Личные реликты!';
					$rec['type']=3232;
					$rec['item_name']=$get_mag['name'];
					$rec['battle']=$user['battle'];
					add_to_new_delo($rec);
				}
			} else {
	 		 	echo "У вас нет такого реликта!";
			}
		}
	 	echo "</font>";	 	 	
	}


	if ($_SERVER['REQUEST_METHOD'] == "POST") 
	{
	$sum_for_all=0;
	$array_of_magic=array();
		foreach ($_POST as $kk=>$pkol ) 
		{
		$kol=(int)($pkol);
		if ($kol>0)
			{
			$mag=explode("_",$kk);
			$mag_id=(int)$mag[1];
			if (($mag[0]=='price') and (is_array($abilcfg[$mag_id])))
				{
				$sum_for_all+=$abilcfg[$mag_id]['cost']*$kol;
				$array_of_magic[$mag_id]=$kol;
				}
			}
		}
		
		
		if(($_SESSION['bankid']>0) and ($sum_for_all>0))
			{
					if (($bank['ekr']) < $sum_for_all) 
							{
							err("Суммы на вешем счету недостаточно для совершения операции. Необходимая сумма ".$sum_for_all." екр.");
							} 
							else 
							{
							// всё ок - начинаем
							$q = mysql_query('START TRANSACTION') or die();
							$get_bank=mysql_query("SELECT * FROM oldbk.`bank` WHERE `id`= '".$bank[id]."' AND owner='{$user['id']}'  AND ekr>=".$sum_for_all."  FOR UPDATE ;");
										if (!(mysql_num_rows($get_bank) > 0) )
										{
										die();
										}
										else
										{
										$bank = mysql_fetch_assoc($get_bank);
										mysql_query("UPDATE `oldbk`.`bank` SET `ekr` = `ekr` - '{$sum_for_all}' WHERE `id`= '{$bank['id']}' and owner='{$user['id']}'  ") or die();
										//  пишем в хистори банка
										$bank['ekr']-=$sum_for_all;
										mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','Покупка личных риликтов на сумму:<b>{$sum_for_all} екр.</b>, <i>(Итого: ".($bank['cr'])." кр., {$bank['ekr']} екр.)</i>','{$bank['id']}');") or die();							
										
										$rec = array();
							    			$rec['owner']=$user[id]; 
										$rec['owner_login']=$user[login];
										$rec['owner_balans_do']=$user['money'];
										$rec['owner_balans_posle']=$user['money'];
										$rec['target_login'] = "КО";
										$rec['sum_ekr']=$sum_for_all;
										$rec['bank_id']=$bank['id'];
										
										foreach ($array_of_magic as $id=>$kol ) 
										{
												$add_info_txt.=$abilcfg[$id]['desc'].' - '.($kol*$abilcfg[$id]['kol']).' шт.;';
										}
										$rec['add_info']=$add_info_txt;
										$rec['type'] = 1281;
										if (add_to_new_delo($rec) === FALSE) die();
										
										//инсертим абилки
											foreach ($array_of_magic as $id=>$kol ) 										
											{
												mysql_query('
														INSERT INTO oldbk.users_abils (owner,magic_id, allcount, findata)
														VALUES(
															"'.$user['id'].'",
															"'.$id.'",
															"'.($kol*$abilcfg[$id]['kol']).'",
															"0"
														) ON DUPLICATE KEY UPDATE
															`allcount` = `allcount` + '.($kol*$abilcfg[$id]['kol'])
														) or die();
											}						
										
											err("Успешно куплены личные абилити на сумму ".$sum_for_all." екр.");
											echo "
											<script>
											var bankbalance = {$bank['ekr']};
											</script>";
											$q = mysql_query('COMMIT') or die();											
										}	
							}
				
									
			}elseif ($sum_for_all>0)
			{
			$error='Ошибка авторизации!';
			}
			
	
	}
	
	
	
	
	$print_out=array();
	$all_mag_ids=array();
	
	$get_lich_abil=mysql_query("select * from oldbk.users_abils ua LEFT JOIN magic m ON ua.magic_id=m.id where owner='{$user[id]}' and (allcount>0 or  daily >0)  ORDER BY daily DESC  ;");
	if (mysql_num_rows($get_lich_abil)>0) 
	{
		while($abilrow=mysql_fetch_array($get_lich_abil)) 
		{
		
				if (($abilrow[img]=='') and ($abilrow[magic_id]==2525)) $abilrow[img]='attackbv.gif';
			
				$print_out[1][$abilrow['magic_id']]['code']="<a onclick=\"javascript:new_runmagic('".$abilrow[name]."','".$abilrow[magic_id]."','target','target1','".$abilrow[targeted]."'); \" href='#'><img src='http://i.oldbk.com/i/magic/".$abilrow[img]."' title='%TITLE%'></a>";
				$print_out[1][$abilrow['magic_id']]['img']=$abilrow['img'];
				$print_out[1][$abilrow['magic_id']]['name']=$abilrow['name'];
				$print_out[1][$abilrow['magic_id']]['allcount']=$abilrow['allcount'];
				$print_out[1][$abilrow['magic_id']]['type']=(int)$abilcfg[$abilrow['magic_id']]['type'];

				if ($abilrow[magic_id]==5151) 			{ $print_out[1][$abilrow['magic_id']]['type']=1;	}
				else
				if ($abilrow[magic_id]==5017152) 			{ $print_out[1][$abilrow['magic_id']]['type']=1; $print_out[1][$abilrow['magic_id']]['name']='Магия «Гнев Ареса», 360 мин.';	}				
				else
				if ($abilrow[magic_id]==5017153) 			{ $print_out[1][$abilrow['magic_id']]['type']=1;  $print_out[1][$abilrow['magic_id']]['name']='Магия «Вой Грифона», 360 мин.';	}				
				else
				if ($abilrow[magic_id]==5017154) 			{ $print_out[1][$abilrow['magic_id']]['type']=1; $print_out[1][$abilrow['magic_id']]['name']='Магия «Обман Химеры», 360 мин.';	}				
				else
				if ($abilrow[magic_id]==5017155) 			{ $print_out[1][$abilrow['magic_id']]['type']=1; $print_out[1][$abilrow['magic_id']]['name']='Магия «Укус Гидры», 360 мин.';	}				
				
				

				if ($abilrow['daily']>0) 
						{				
						$print_out[1][$abilrow['magic_id']]['daily']="<small>$abilrow[dailyc] из $abilrow[daily] в сут.</small>";
						} 


				if ( (is_array($abilcfg[$abilrow['magic_id']])) )
					{
					//есть для покупки
					$print_out[2][$abilrow['magic_id']]['pack']=$abilcfg[$abilrow['magic_id']]['kol'];
					$print_out[2][$abilrow['magic_id']]['cost']=$abilcfg[$abilrow['magic_id']]['cost'];					
					$print_out[2][$abilrow['magic_id']]['desc']=$abilcfg[$abilrow['magic_id']]['desc'];
					$print_out[2][$abilrow['magic_id']]['type']=(int)$abilcfg[$abilrow['magic_id']]['type'];					
					}
				
					
		
		$all_mag_ids[]=str_ireplace('../sh/',"",$abilrow['img']);
		}
	} 




	foreach($abilcfg as $idmag=>$info)	
	{
		$all_mag_ids[]=str_ireplace('../sh/',"",$info['img']);		
				
		if ($info[cost]>0)
		{
			/*if (!(is_array($print_out[2][$idmag])) )
			{
					$print_out[2][$idmag]['pack']=$info['kol'];
					$print_out[2][$idmag]['cost']=$info['cost'];					
					$print_out[2][$idmag]['desc']=$info['desc'];
					$print_out[2][$idmag]['type']=(int)$info['type'];					

			}
			*/
		}

	}
	

	//загрузка описаний из базы для титлов
	$get_text_abil=mysql_query("select img, letter from eshop where img in ('".implode("','",$all_mag_ids)."') and letter!='' UNION select img, letter from shop where img in ('".implode("','",$all_mag_ids)."') and letter!=''  group by magic");

	if (mysql_num_rows($get_text_abil)>0) 
	{
		while($trow=mysql_fetch_array($get_text_abil)) 
		{
		$text_abil[$trow['img']]=$trow['letter'];
		}
	}



echo "<div align=center>";
render_my_abils();
echo "</div>";
echo "<hr>";
 echo "<form method=post name=byabils>";	  
 
echo "<table border=0 width=100%>";
echo "<tr>";
echo "<td width=50%>";
echo "<table border=0>";
echo "<tr>";
echo "<td>";	  
	  

	echo "<table border=0 cellspacing=2 cellpadding=0 >
	<tr >
		<td width=40> </td>
		<td width=50%><h4>Не боевые:</h4></td>
		<td width=9%></td>		
		<td width=9%></td>				
		<td width=17%><h4>Купить:</h4></td>	
		<td width=10%></td>						

	</tr>";

	render_list_abil(0);
	
	echo "</table>";
	echo "</td>";
	echo "</tr>";	
	echo "</table>";	
	
	echo "</td>";
	echo "<td width=50% align=center>";	
			
		echo "<table border=0>";
		echo "<tr>";
		echo "<td>";	  
			
			echo "<table border=0 cellspacing=2 cellpadding=0 >
			<tr >
			<td width=40> </td>
			<td width=50%><h4>Боевые:</h4></td>
			<td width=9%></td>		
			<td width=9%></td>				
			<td width=17%><h4>Купить:</h4></td>	
			<td width=10%></td>						
			</tr>";

			render_list_abil(1);

			echo "</table>";
		echo "</td>";
		echo "</tr>";	
		echo "</table>";	
			
	echo "</td>";
	echo "</tr>";
	
	echo "<tr >";



	echo "<td colspan=2 style=\"vertical-align: middle;text-align:center;\" align=center>";	

						echo "<b>Итого к оплате: </b>";
						echo "<span id='calcsum'>0 екр.</span>";
						echo "<br>";	
						echo "<span id='pbutton'>";
						if($_SESSION['bankid']) 
							{
							echo "В банке: <font color=green><b>{$bank['ekr']}</b></font> екр.<br>";
							echo '<a href="javascript:void(0);" class="button-big btn" title="Оплатить" onClick="document.byabils.submit();">Оплатить</a>';							
							}
							else
							{
							echo "Требуется <a href='#' OnClick='checkbank(\"\",1);return false;'>вход</a> в банк" ;
							}
						echo "</span>";
						echo "<br><br><br>";
	echo "</table></form>";
	
	
	echo "<script>CalcAll(); </script>";
	
}	
?>
</td>
</tr>
</table>
</div>
<script type="text/javascript">
$(function() {
	$("#pl").draggable();
	$(window).resize();

});
</script>
</body>
</html>
<?php
/////////////////////////////////////////////////////
if (isset($miniBB_gzipper_encoding)) {
	$miniBB_gzipper_in = ob_get_contents();
	$miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
	$miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
	$miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
	$miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
	$gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
	$percent = round($gzpercent);
	$miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);
	$miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
	ob_clean();
	header('Content-Encoding: '.$miniBB_gzipper_encoding);
	echo $miniBB_gzipper_out;
}
/////////////////////////////////////////////////////
?>