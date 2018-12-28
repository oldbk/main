<?php
//ini_set('display_errors','On');
//add by Fred 9 12 2010
	session_start();
	if (!($_SESSION['uid'] >0))
	{
	 header("Location: index.php");
	 die();
	}
	include "connect.php";
	include "functions.php";


if ($user['in_tower'] == 4) { header('Location: jail.php'); die(); }

$begin1=mktime(0,0,1,12,29,2011);
$end1=  mktime(0,0,1,12,31,2011);

$GOLD_GIVE_KURS=11;


$vauch_a = array(100000,100005,100015,100020,100025,100040,100100,100200,100300); //+ и бумажка КО

//print_r($_POST);



function get_free_stats_up($intel)
{
//	if($intel < 75)
//	{
//		$intel = 75;
//	}
	$chance = $intel / 25;
	if($intel < 50) { $chance = 0; return; }
	$range = round($chance);
	if($range < 3)
	{
		if(get_chanse($chance))
		{
			return 2;
		}
		else
		{
			return 1;
		}
	}
	else
	{
		$unique_chance = 1 + $chance / 25;
		$unique_chance=round($unique_chance*0.95); // [6:08:07 10.11.12] Deni:  шанс выпадения уника уменьшить на 5%
		if(get_chanse($unique_chance))
		{
			return 3;
		}
		else
		{
			$chance = 83 +  round($chance) * 5;
			if(get_chanse($chance))
			{
				return 2;
			}
			else
			{
				return 1;
			}
		}
	}
}

function get_chanse ($persent)
{
	if($persent > 99) { $persent = 99; };
	$mm = 1000000;
	return (mt_rand($mm, 100 * $mm) <= $persent*$mm);
}



//	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
   	$tower_type=FALSE;
	$ttype=0;
	if($user['in_tower']==1)
	{
		$tower=mysql_fetch_array(mysql_query('select * from deztow_turnir where active = TRUE'));
		if($tower[type]==12 || $tower['type'] == 13 || $tower['type'] == 14 || $tower['type'] == 15 || $tower['type'] == 16)
		{
			$ttype=1;
		}
	}

	if($user['in_tower']==15)
	{
		$tower=mysql_fetch_array(mysql_query('select * from dt_map where active = 1'));
		if($tower['greedtype'])
		{
			$ttype=2;
		}
	}



	if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
	if ($_SESSION['boxisopen']!='open') { header('location: main.php?edit=1'); die(); }
	if (($user['room'] >= 197)AND($user['room'] <= 199))  { header('Location: armory.php'); die(); }
	if ($user['room'] == 76)  { header('Location: class_armory.php'); die(); }
	if (($user['room'] > 210)AND($user['room'] <= 239))  { header('Location: restal210.php'); die(); }

  /*
print_r($_POST);
echo '<br>';
print_r($_GET);
     */
$step=1;
if ($step==1)
	{
		$idkomu=0;
	}

	if (!$_REQUEST['razdel']) { $_REQUEST['razdel']=1; }

	if ($_REQUEST['FindLogin']) {
		$res=mysql_fetch_array(mysql_query("SELECT `id`, `id_grup`, `ruines`, `level`, `room`, `align`, `odate` as `online` FROM `users` WHERE `login` ='".mysql_escape_string($_REQUEST['FindLogin'])."';"));

		if (!$res['id'] && $user['in_tower'] == 15 && strpos($_REQUEST['FindLogin'],'pxива') !== FALSE) {
			$res=mysql_fetch_array(mysql_query("SELECT * FROM `users_clons` WHERE bot_room = ".$user['room']." AND `login` ='".mysql_escape_string($_REQUEST['FindLogin'])."' and id_user = 84"));
			$res['room'] = $res['bot_room'];
		}
		$step=3;
	}
	if ($_REQUEST['to_id']) {
		$res=mysql_fetch_array(mysql_query("SELECT `id`, `id_grup`, `ruines`, `level`, `room`, `align`, `odate` as `online` FROM `users` WHERE `id` ='".(int)($_REQUEST['to_id'])."';"));
		if (!$res['id'] && $user['in_tower'] == 15) {
			$res=mysql_fetch_array(mysql_query("SELECT * FROM `users_clons` WHERE `id` ='".intval($_REQUEST['to_id'])."' and id_user = 84"));
			$res['room'] = $res['bot_room'];
		}
		$step=3;
	}

	if (@$step==3){
		$step=0;
		
		$del_trade=false;
		
		$id_person_x=$res['id'];
		if (@!$id_person_x) $mess='Персонаж не найден';
		elseif ($id_person_x==$user['id']) $mess='Незачем передавать самому себе';
		elseif ($user['align']==4 && $user['id']!='188') { $mess='Со склонностью хаос передачи предметов запрещены'; $del_trade=true; }
		elseif ($res['online'] < (time()-120) && !($user['klan'] == 'radminion') && !($user['klan'] == 'Adminion') && !($user['id'] == 8325) && $user['in_tower'] != 15) { $mess='Персонаж не онлайн'; $del_trade=true; }
		elseif ($res['room']!=$user['room'] && !($user['klan'] == 'radminion') && !($user['klan'] == 'Adminion') && !($user['id'] == 8325)) { $mess='Вы должны находиться в одной комнате с тем, кому хотите передать вещи'; $del_trade=true; }
		elseif ($user['ruines'] > 0 && $user['id_grup'] != $res['id_grup']) { $mess='Нельзя передавать врагам!'; $del_trade=true; }
		elseif ($res['level']<4 && !($user['klan'] == 'radminion') && !($user['klan'] == 'Adminion') && !($user['id'] == 8325)) { $mess='К персонажам до 4-го уровня передачи предметов запрещены'; $del_trade=true; }
		elseif ($user['level']<4 AND !($user['klan'] == 'radminion') AND !($user['klan'] == 'Adminion') && !($user['id'] == 8325)) { $mess='Персонажам до 4-го уровня передачи предметов запрещены'; $del_trade=true; }
		else{
			$idkomu=$id_person_x;
			if (!isset($res['id_user']) || $res['id_user'] != 84) {
				$komu=mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` ='".$idkomu."';"));
			} else {
				$komu=mysql_fetch_array(mysql_query("SELECT * FROM `users_clons` WHERE `id` ='".$idkomu."';"));			
				$idkomubot = 84;
			}


			$VAUCHER='and prototype not in (900,901,902,903,904,905,906,907,908,200001,200002,200005,200010,200025,200050,200100,200250,200500,2013005) ';   
			/*
			if (  ($komu[id]==8540) OR  ($komu[id]==182783) OR ($komu[id]==457757)  OR ($komu[id]==326)  )     {   
				$VAUCHER='and prototype not in (900,901,902,903,904,905,906,907,908,200001,200002,200005,200010,200025,200050,200100,200250,200500,2014001,2014002,2014003,2014004,2014005,2014006,2014007,2014008) ';   }
			    else   {    
				$VAUCHER='and (prototype not in (100005,100015,100020,100025,100040,100300,100100,100200,900,901,902,903,904,905,906,907,908,200001,200002,200005,200010,200025,200050,200100,200250,200500,2014001,2014002,2014003,2014004,2014005,2014006,2014007,2014008)';
			}
			*/
			$mess=$_REQUEST['FindLogin'];
			$step=3;
		}

		if ($del_trade==true)
			{
			mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} LIMIT 1;");
			}
		
	}
	else
	{
		$mess='К персонажам до 4-го уровня передачи предметов запрещены';
	}

?><HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<SCRIPT src='i/commoninf.js'></SCRIPT>
<script type="text/javascript" src="/i/globaljs.js"></script>
<SCRIPT>

function showhide(id)
	{
	 if (document.getElementById(id).style.display=="none")
	 	{document.getElementById(id).style.display="block";}
	 else
	 	{document.getElementById(id).style.display="none";}
	}

function AddCount(name, txt, sale, href) {     var el = document.getElementById("hint3");

    if(sale==1)
    {
    	var sale_txt= 'Передать неск. штук (1 кр. за каждую вещь)';
        var a_href='action="'+href+'"';
    }
    if(sale==2)
    {
    	var sale_txt= 'Подарить неск. штук';
        var a_href='action="'+href+'"';
    }

	el.innerHTML = '<form '+a_href+' method="post" style="margin:0px; padding:0px;"><table border=0 width=100% cellspacing=1 cellpadding=0 bgcolor="#CCC3AA"><tr><td align=center><B>'+sale_txt+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</TD></tr><tr><td colspan=2>'+
	'<table border=0 width=100% cellspacing=0 cellpadding=0 bgcolor="#FFF6DD"><tr><INPUT TYPE="hidden" name="is_sale" value="'+sale+'"><INPUT TYPE="hidden" name="set" value="'+name+'"><td colspan=2 align=center><B><I>'+txt+'</td></tr><tr><td width=80% align=right>'+
	'Количество (шт.) <INPUT TYPE="text" NAME="count" size=4 ></td><td width=20%>&nbsp;<INPUT TYPE="submit" value=" »» ">'+
	'</TD></TR></TABLE></td></tr></table></form>';
	el.style.visibility = "visible";
	el.style.left = (document.body.scrollLeft - 20) + 100 + 'px';
	el.style.top = (document.body.scrollTop + 5) + 100 + 'px';
	document.getElementById("count").focus();
}



var Hint3Name = '';
// Заголовок, название скрипта, имя поля с логином
function findlogin(title, script, name){
    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>"><td colspan=2>'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT TYPE=text id="'+name+'" NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	document.getElementById(name).focus();
	Hint3Name = name;
}



function returned2(s){
	if (top.oldlocation != '') { top.frames['main'].location=top.oldlocation+'?'+s+'tmp='+Math.random(); top.oldlocation=''; }
	else { top.frames['main'].location='main.php?edit='+Math.random() }
}

function closehint3(){
	document.getElementById("hint3").style.visibility="hidden";
    Hint3Name='';
}


var transfersale = true;
function findmoney(title, script, name, obj){
    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=get><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=id_th value="'+obj+'">'+
	'<INPUT TYPE=hidden name=to_id value="<?=$komu['id']?>"><td colspan=2>'+
	'Укажите cумму (мин 1кр.):<small></TD></TR><TR><TD width=50% align=right><INPUT TYPE=text id="'+name+'" NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	document.getElementById(name).focus();
	Hint3Name = name;
}


function findmoney2(title, script, name, obj, proto){
	var price = 0;
	if (proto == 100005) price = 5;
	if (proto == 100015) price = 15;
	if (proto == 100020) price = 20;
	if (proto == 100025) price = 25;
	if (proto == 100040) price = 40;
	if (proto == 100100) price = 100;
	if (proto == 100200) price = 200;
	if (proto == 100300) price = 300;
	price = price * 18;


    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=get><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=id_th value="'+obj+'">'+
	'<INPUT TYPE=hidden name=to_id value="<?=$komu['id']?>"><td colspan=2>'+
	'Сумма:<small></TD></TR><TR><TD width=50% align=right><INPUT TYPE=hidden value="'+price+'" id="'+name+'" NAME="'+name+'"><INPUT TYPE=text disabled value="'+price+' кр." id="dummy" NAME="dummy"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></td></tr><tr><td colspan=3><font color=red>Ваучер будет привязан к покупателю после продажи</font></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	document.getElementById(name).focus();
	Hint3Name = name;
}

function findmoney3(title, script, name, obj, price){

    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=get><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=id_th value="'+obj+'">'+
	'<INPUT TYPE=hidden name=to_id value="<?=$komu['id']?>"><td colspan=2>'+
	'Сумма:<small></TD></TR><TR><TD width=50% align=right><INPUT TYPE=hidden value="'+price+'" id="'+name+'" NAME="'+name+'"><INPUT TYPE=text disabled value="'+price+' кр." id="dummy" NAME="dummy"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></td></tr><tr><td colspan=3><font color=red>Предмет будет передан как подарок!</font></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	document.getElementById(name).focus();
	Hint3Name = name;
}

function findmoney4(title, script, name, obj, price){

    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=get><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=id_th value="'+obj+'">'+
	'<INPUT TYPE=hidden name=to_id value="<?=$komu['id']?>"><td colspan=2>'+
	'Сумма:<small></TD></TR><TR><TD width=50% align=right><INPUT TYPE=hidden value="'+price+'" id="'+name+'" NAME="'+name+'"><INPUT TYPE=text disabled value="'+price+' кр." id="dummy" NAME="dummy"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></td></tr><tr><td colspan=3></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	document.getElementById(name).focus();
	Hint3Name = name;
}


function CheckEkr3Price(pr) {
	var pr2 = parseFloat(document.getElementById("checkekr3price").value);
	if (pr2 < pr) {
		alert("Минимальная цена продажи этого предмета "+pr+" кр.");
		return false;
	}
	return true;
}

function findmoney5(title, script, name, obj, price){

    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=get><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=id_th value="'+obj+'">'+
	'<INPUT TYPE=hidden name=to_id value="<?=$komu['id']?>"><td colspan=2>'+
	'Сумма:<small></TD></TR><TR><TD width=50% align=right><INPUT id="checkekr3price" TYPE=text value="'+price+'" id="'+name+'" NAME="'+name+'"></TD><TD width=50%><INPUT OnClick="return CheckEkr3Price('+price+');" TYPE="submit" value=" »» "></td></tr><tr><td colspan=3><font color=red>Минимальная цена продажи<br> '+price+' кр.</font></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	document.getElementById(name).focus();
	Hint3Name = name;
}


var tologin = '<? echo @($step==3?$komu['login']:''); ?>';


function Sale(to_id, name, n, txt, transfer_kredit){
	var s = prompt("Продать \""+txt+"\" к \""+tologin+"\". Укажите цену:", '');
	if (s != null && s!= '') { // продаем
	    if (confirm("Продать \""+txt+"\" к \""+tologin+"\" за "+parseFloat(s)+" кр. Вы заплатите "+transfer_kredit+"кр. за передачу! Ваш партнер по сделке должен открыть у себя окно обмена. Продолжить?")) {
		   location="/main.php?to_id="+to_id+"&setobject="+name+"&n="+n+"&s4i=<?=$user['sid']?>&sale="+s+"&sd4=<? echo @$user['id']; ?>&0.760742158507544";
		}
	}
}


function transfer(to_id, login, txt, kredit, id, destiny,proto){
	var warn = "";
	if (proto == 100005 || proto == 100015 || proto == 100020 || proto == 100025 || proto == 100040 || proto == 100100 || proto == 100200 || proto == 100300) {
		warn = "</tr><tr><td colspan=3><font color=red>Ваучер будет привязан к покупателю после продажи</font>";
	}
	else
	if (proto == 1) {
		warn = "</tr><tr><td colspan=3><font color=red>Внимание! После покупки этот предмет нельзя будет передать или продать!</font>";
	}
	else
	if (proto == 2) {
		warn = "</tr><tr><td colspan=3>";
	}
	else
	if (proto == 3) {
		warn = "</tr><tr><td colspan=3>";
	}

	document.getElementById("hint3").innerHTML = '<table width=500 cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>Продажа предмета</td></tr><tr><td>'+
	'<form action="give.php" method=get><table width=100% cellspacing=0 cellpadding=5 bgcolor=FFF6DD><tr><td><INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>"><INPUT TYPE=hidden name=FindLogin value=0><INPUT TYPE=hidden name=to_id value="'+to_id+'"><INPUT TYPE=hidden name=transfersale value="'+id+'">'+
	'<b>'+login+'</b> <a href="inf.php?'+to_id+'" target=_blank><IMG SRC=i/inf.gif WIDTH=12 HEIGHT=11></a> предлагает Вам купить предмет:<BR>'+
	txt+'<BR>за <font color=red><b>'+kredit+' кр.</b></font><BR>Проводим сделку?</TD></TR><TR><TD align=center><INPUT TYPE=submit '+(destiny?" onclick='return confirm(\"Этот предмет может использовать только "+destiny+" Вы уверены, что хотите его купить?\")'":"")+' value="  ДА  " name="confirm"> &nbsp;&nbsp; <INPUT TYPE="submit" name="cancel" value=" НЕТ "">'+warn+'</TD></TR></TABLE></FORM></td></tr></table>';
	document.getElementById("hint3").style.visibility = "visible";
	document.getElementById("hint3").style.left = 100;
	document.getElementById("hint3").style.top = 60;
}

function mftransfer(to_id, login, txt, id){
	document.getElementById("hint3").innerHTML = '<table width=500 cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>Услуги мага</td></tr><tr><td>'+
	'<form action="give.php" method=get><table width=100% cellspacing=0 cellpadding=5 bgcolor=FFF6DD><tr><td><INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>"><INPUT TYPE=hidden name=FindLogin value=0><INPUT TYPE=hidden name=to_id value="'+to_id+'"><INPUT TYPE=hidden name=transfermf value="'+id+'">'+
	'<b>'+login+'</b> <a href="inf.php?'+to_id+'" target=_blank><IMG SRC=i/inf.gif WIDTH=12 HEIGHT=11></a> предлагает Вам модифицировать предмет:<BR>'+
	txt+'<BR>Укажите стоимость Вашиx услуг (без учета стоимости модификации):<input type=text name=prise> <b>кр.</b></font><BR>Проводим сделку?</TD></TR><TR><TD align=center><INPUT TYPE=submit value="  ДА  "> &nbsp;&nbsp; <INPUT TYPE=button value=" НЕТ " onclick="closehint3()"></TD></TR></TABLE></FORM></td></tr></table>';
	document.getElementById("hint3").style.visibility = "visible";
	document.getElementById("hint3").style.left = 100;
	document.getElementById("hint3").style.top = 60;
}

function mftransfershow(to_id, login, txt, id){
	document.getElementById("hint3").innerHTML = '<table width=500 cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>Услуги мага</td></tr><tr><td>'+
	'<form action="give.php" method=get><table width=100% cellspacing=0 cellpadding=5 bgcolor=FFF6DD><tr><td><INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>"><INPUT TYPE=hidden name=FindLogin value=0><INPUT TYPE=hidden name=to_id value="'+to_id+'"><INPUT TYPE=hidden name=okit value="'+id+'">'+
	'<b>'+login+'</b> <a href="inf.php?'+to_id+'" target=_blank><IMG SRC=i/inf.gif WIDTH=12 HEIGHT=11></a> Вы модифицировали предмет:<BR>'+
	txt+'<BR></font><BR></TD></TR><TR><TD align=center><INPUT TYPE=button value=" OK " onclick="closehint3()"></TD></TR></TABLE></FORM></td></tr></table>';
	document.getElementById("hint3").style.visibility = "visible";
	document.getElementById("hint3").style.left = 100;
	document.getElementById("hint3").style.top = 60;
}

function transfergold(to_id, login, id ,txt, gold, kr, mylim, err){

	if (err==0) {  mmsg='<BR>Проводим сделку?</center></TD></TR><TR><TD align=center><INPUT TYPE=submit name="confim" value="  ДА  "> &nbsp;&nbsp; <INPUT TYPE=submit name="cancel" value=" НЕТ " >' ; }
	else if (err==1) {  mmsg='<BR><b>Предложение превышает остаточный лимит покупки!</b></center></TD></TR><TR><TD align=center>' ; }
	else if (err==2) {  mmsg='<BR><b>У вас недостаточно кредитов для этой покупки!</b></center></TD></TR><TR><TD align=center>' ; }

	document.getElementById("hint3").innerHTML = '<table width=500 cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>Продажа монет</td></tr><tr><td>'+
	'<form action="give.php" method=post><table width=100% cellspacing=0 cellpadding=5 bgcolor=FFF6DD><tr><td><INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>"><INPUT TYPE=hidden name=to_id value="'+to_id+'"><INPUT TYPE=hidden name=tcodeid value="'+id+'"><INPUT TYPE=hidden name=transfergoldconf value="gold">'+
	'<b>'+login+'</b> <a href="inf.php?'+to_id+'" target=_blank><IMG SRC=i/inf.gif WIDTH=12 HEIGHT=11></a> предлагает Вам купить:<BR><BR><center> <b>'+gold+'</b> <img src="http://i.oldbk.com/i/icon/coin_icon.png" alt="Монеты" title="Монеты" style="margin-bottom: -2px;">  за <b>'+kr+' кр.</b><BR><BR><BR>Доступно для покупки еще:<b> '+mylim+' монет</b><BR>(лимит обновляется в полночь)<br>'+mmsg+'</TD></TR></TABLE></FORM></td></tr></table>';
	document.getElementById("hint3").style.visibility = "visible";
	document.getElementById("hint3").style.left = 100;
	document.getElementById("hint3").style.top = 60;
}

function mftransferconf(to_id, login, txt, id , kredit, mfprise){
	document.getElementById("hint3").innerHTML = '<table width=500 cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>Услуги мага</td></tr><tr><td>'+
	'<form action="give.php" method=get><table width=100% cellspacing=0 cellpadding=5 bgcolor=FFF6DD><tr><td><INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>"><INPUT TYPE=hidden name=FindLogin value=0><INPUT TYPE=hidden name=to_id value="'+to_id+'"><INPUT TYPE=hidden name=transfermfconf value="'+id+'">'+
	'<b>'+login+'</b> <a href="inf.php?'+to_id+'" target=_blank><IMG SRC=i/inf.gif WIDTH=12 HEIGHT=11></a> предлагает Вам модифицировать предмет:<BR>'+
	txt+'<BR>Стоимость Модификации:<b>'+mfprise+' кр.</b><BR>Cтоимость услуг мага (без учета стоимости модификации):<b>'+kredit+' кр.</b><br>Комиссия: <b>1 кр.</b> <br> Итого:<b>'+(kredit+mfprise+1)+' кр.</b></font><BR>Проводим сделку?</TD></TR><TR><TD align=center><INPUT TYPE=submit value="  ДА  "> &nbsp;&nbsp; <INPUT TYPE=button value=" НЕТ " onclick="closehint3()"></TD></TR></TABLE></FORM></td></tr></table>';
	document.getElementById("hint3").style.visibility = "visible";
	document.getElementById("hint3").style.left = 100;
	document.getElementById("hint3").style.top = 60;
}

function reloadit(){
   if (tologin != '') { location="give.php?FindLogin=0&to_id=<? echo $idkomu; ?>&sd4=<? echo $user['id']; ?>&0.760742158507544" }
}

</SCRIPT>
<?
if (isset($_POST['tcodeid']) AND isset($_POST['transfergoldconf']) )
	{
	$step=1;
	}


if ($step==3) {
        $item=array();
       	$it_id='';
        $chk_massa=0;
        $ff=0;
        $okk=1;
   //перевод кредов
	if ($_REQUEST['setkredit']>0 && $_REQUEST['to_id'] && $_REQUEST['sd4']==$user['id'] && $idbotkomu != 84) 
	{
	
		$_REQUEST['setkredit'] = round($_REQUEST['setkredit'],2);
		if (($user['money']<$_REQUEST['setkredit']) OR ($_REQUEST['setkredit']<=0) ) $mess="Недостаточно денег или неверная сумма";
		else {
		
				//подсчет и если ок то дальше - TEST
				/*
				if (($okk==1) )
				{
				if ( ($user['in_tower']!=15)  AND //не бс, не руины
					($user['ruines']==0) AND //не в руинах
					($user['klan']!='radminion') AND ($user['klan']!='Adminion') AND ($komu['klan']!='radminion') AND ($komu['klan']!='Adminion') AND //не передачи от админов и к админам
					($user['id']!=8325) AND ($komu['id']!=8325) ) //не передачи от ПБ и к ПБ
					{
						 $tco=test_give_count($user['id'],$komu['id'],1);
						 if (!(is_array($tco)))
							{
							//тест успешно
							 	if (give_count($user['id'],1) )
							 	{
							 	//ok
								 	if (give_count($komu['id'],1) )
								 	{
								 	//все ок
								 	}
								 	else
								 	{
								 	$mess='У персонажа "'.$komu['login'].'" недостаточно лимита передач на сегодня! ' ;
									$okk=0;															 	
								 	}
							 	}
							 	else
							 	{
							 	$mess='У Вас недостаточно лимита передач на сегодня! ' ;
								$okk=0;							 	
							 	}
							}
							else
							{
							$tlo[$user['id']]=$user['login'];
							$tlo[$komu['id']]=$komu['login'];							
							
							$mess='';
							 foreach ($tco as $k => $l)
							 	{
								$mess.='У Персонажа "'.$tlo[$l].'" недостаточно лимита передач на сегодня! <br> ' ;
								}
							$okk=0;
							}
					
					}
				}////////////////////////////////////////////////////////////////////////////////////////////////////////			
				*/
		if ($okk==1)
		{
			if ((mysql_query("UPDATE `users` set money=money-".strval($_REQUEST[setkredit])." where id='".$user['id']."'")) &&
			    (mysql_query("UPDATE `users` set money=money+".strval($_REQUEST[setkredit])." where id='".$idkomu."'")))

				{
					if($_POST[settext])
					{
						$text1=$_POST['settext'];
						$text1 = preg_replace("~&amp;~i","&",$text1);
						$text1 = preg_replace("~&lt;B&gt;~i","<B>",$text1);
						$text1 = preg_replace("~&lt;/B&gt;~i","</B>",$text1);
						$text1 = preg_replace("~&lt;U&gt;~i","<U>",$text1);
						$text1 = preg_replace("~&lt;/U&gt;~i","</U>",$text1);
						$text1 = preg_replace("~&lt;I&gt;~i","<I>",$text1);
						$text1 = preg_replace("~&lt;/I&gt;~i","</I>",$text1);
						$text1 = preg_replace("~&lt;CODE&gt;~i","<CODE>",$text1);
						$text1 = preg_replace("~&lt;/CODE&gt;~i","</CODE>",$text1);
						$text1 = preg_replace("~&lt;b&gt;~i","<b>",$text1);
						$text1 = preg_replace("~&lt;/b&gt;~i","</b>",$text1);
						$text1 = preg_replace("~&lt;u&gt;~i","<u>",$text1);
						$text1 = preg_replace("~&lt;/u&gt;~i","</u>",$text1);
						$text1 = preg_replace("~&lt;i&gt;~i","<i>",$text1);
						$text1 = preg_replace("~&lt;/i&gt;~i","</i>",$text1);
						$text1 = preg_replace("~&lt;code&gt;~i","<code>",$text1);
						$text1 = preg_replace("~&lt;/code&gt;~i","</code>",$text1);
						$text1 = preg_replace("~&lt;br&gt;~i","<br>",$text1);
						if(strlen($text1)>70)
						{
							$text1=substr($text1,0,70);
						}
						
					}
					
					
					$mess='Удачно переданы '.strval($_REQUEST[setkredit]).' кр персонажу '.$komu['login']. ($text1!=''?'. Детали платежа: '.$text1:'');
					addchp ('<font color=red>Внимание!</font> Персонаж "'.$user['login'].'" передал вам <B>'.strval($_REQUEST[setkredit]).' кр</B>.'.($text1!=''?' Детали платежа: '.$text1:''),'{[]}'.$komu['login'].'{[]}',$komu['room'],$komu['id_city']);
						//new_delo
	  		    		$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$user['money']-=$_REQUEST[setkredit];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=$komu['id'];
					$rec['target_login']=$komu['login'];
					$rec['type']=36;//передача кредитов
					$rec['sum_kr']=strval($_REQUEST[setkredit]);
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']='';
					$rec['item_name']='';
					$rec['item_count']=0;
					$rec['item_type']=0;
					$rec['item_cost']=0;
					$rec['item_dur']=0;
					$rec['item_maxdur']=0;
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']='';
					$rec['add_info']=$text1;
					add_to_new_delo($rec); //юзеру
					$rec['type']=37;//получение кредитов
  		    			$rec['owner']=$komu[id];
					$rec['owner_login']=$komu[login];
					$rec['owner_balans_do']=$komu['money'];
					$komu['money']+=$_REQUEST[setkredit];
					$rec['owner_balans_posle']=$komu['money'];
					$rec['target']=$user['id'];
					$rec['target_login']=$user['login'];
					add_to_new_delo($rec); //кому

					
			}
			else {
				$mess='Произошла ошибка!';
			}
		
		}
		
		}
	}
	else if (($_REQUEST['setgold']) and ($user['level']>=10) )
	{
		$testrow = mysql_fetch_array(mysql_query("SELECT * FROM `trade` WHERE to_id='{$user['id']}' and `baer` ='{$komu['id']}'  LIMIT 1;"));
		
		if ($testrow['id']>0)
		{
				$mess="С этим персонажем есть незаконченная сделка!";
		}
		elseif ($komu['level']>=10)
		{
		$send_gold=(int)$_REQUEST['setgold'];
		if (($send_gold>0) AND ($send_gold<=$user['gold']) )
						{
								$vkr=round($send_gold*$GOLD_GIVE_KURS);
								mysql_query("INSERT INTO `trade`(`to_id` ,`login`  ,`txt` ,`kr` ,`id` ,`baer` ,`zalog`) VALUES 	('{$user['id']}','{$user['login']}','Продажа монет','$vkr','".mt_rand(111111,999999)."',{$_REQUEST['to_id']} ,4);") or die(mysql_error()."!!!");
								$mess = 'Предложение персонажу '.$komu['login'].' сделано.';
								addchp('<font color=red>Внимание!</font> <B>'.$user[login].'</B> предлагает Вам купить <b>'.$send_gold.'</b> монет за <b>'.$vkr.'</b> кр. <BR>\'; top.frames[\'main\'].location=\'http://capitalcity.oldbk.com/give.php\'; var z = \'   ','{[]}'.$komu['login'].'{[]}',$komu['room'],$komu['id_city']);
						}
						else
						{
						$mess="Недостаточно денег или неверная сумма";
						}
		}
		else
		{
						$mess="Продажа монет доступна чарам 10 уровня и выше!";
		}
	
	}

	//передача предмета за 1 кр и подарок

    $_REQUEST['gift']== '1'? '1' : '0';
    $gift=$_REQUEST['gift'];

	if ($_REQUEST['setobject'] && $_REQUEST['to_id'] && $gift>=0 && $_REQUEST['sd4']==$user['id'] && $_GET['s4i']==$user['sid']) 
	{

        if(!$_POST['count'])
        {
        	$count=1;
        	$sql=' AND id='.mysql_escape_string($_REQUEST['setobject']);
        }
        else
        {
        	$count=(int)$_POST['count'];
        	$sql=' AND prototype='.mysql_escape_string((int)$_POST['set']).' AND `group`=1 ';
        }
        //делаем доп проверку веса лдя архивариусов, так как их шмот висит в инвентаре олдбк, с разделением по месту производства шмотки (сити)
        if($idkomu==83 || $idkomu==136)
        {
        	//Fix на переполнение мешка арха в разных городах
        	$sql1="SELECT sum(`massa`) as massa FROM oldbk.`inventory` USE INDEX (owner_3) WHERE `owner` = '".$idkomu."' AND duration=".(int)$_GET[tmp]." AND `dressed` = 0  AND `setsale` = 0 AND  bs_owner='".$user[in_tower]."' AND idcity='".$user[id_city]."'; ";
        	$mto = mysql_fetch_array(mysql_query($sql1));
        	
        	$d = mysql_fetch_array(mysql_query("SELECT sum(`gmeshok`) FROM oldbk.`inventory` WHERE `owner` = '{$idkomu}' AND bs_owner='".$user['in_tower']."' AND `setsale` = 0 AND `gmeshok`>0 AND idcity='".$user[id_city]."'; "));
		$s = mysql_fetch_array(mysql_query("SELECT sila FROM `users` WHERE `id` = '{$idkomu}' LIMIT 1 ; "));
	//	return('30000');
		$allmass=($s['sila']*4+$d[0]);
		$ttype=0;
        }
        else
        {
		if ($idkomubot != 84) {
	        	$sql1="SELECT sum(`massa`) as massa FROM oldbk.`inventory` USE INDEX (owner_3) WHERE `owner` = ".$idkomu." AND duration=".(int)$_GET[tmp]." AND `dressed` = 0 AND `setsale` = 0 AND  bs_owner='".$user[in_tower]."'; ";
        		$mto = mysql_fetch_array(mysql_query($sql1));
        		$allmass=get_meshok_to($idkomu);
		}
	}
	//2123456804 нельзя передавать, упакованный подарок. ekr_flag=0 нельзя передавать предметы только что купленные в березе
	
	if ($user['klan']=='radminion' || $user['klan']=='Adminion' || $user['id'] == 8325) //AND prototype!=2123456804 and ekr_flag=0 and otdel!=72 and type!=77
	{
	        $sql="SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' ".$sql."
		AND dressed=0 AND bs_owner='".$user[in_tower]."' AND `setsale` = 0
		AND duration=".(int)$_GET[tmp]."  ".$VAUCHER." AND `present` = '' and type!=99  LIMIT ".$count.";";
	}
	else
	{
        	$sql="SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' ".$sql."
		AND dressed=0 AND bs_owner='".$user[in_tower]."' AND `setsale` = 0 AND prototype!=40000001 AND prototype!=2123456804 and ekr_flag=0 and otdel!=72 and type!=77 and sowner=0
		AND duration=".(int)$_GET[tmp]."  ".$VAUCHER." AND `present` = '' and type!=99  LIMIT ".$count.";";
	}
	  
	$data = mysql_query($sql);
	
	  	if (mysql_error())
	  		{
	  			$fp = fopen ("/www/other/giveerror.txt","a");
				flock ($fp,LOCK_EX); 
				fputs($fp , $sql."\n"); 
				fflush ($fp); 
				flock ($fp,LOCK_UN); 
				fclose ($fp); 
				die();
	  		}
		
 	if(mysql_num_rows($data)>0)
	{
      		while($res=mysql_fetch_array($data))
      		{
			if (in_array($res['prototype'],$vauch_a) && $komu['id'] != 102904 && $komu['id'] != 8540 && $komu['id'] !=182783 && $komu['id']!=457757 && $komu['id'] !=8325) continue;

			  if($gift==1) { 
			  			if ( (($res['art_param'] !='') or ($res['ab_mf'] >0 )  or ($res['ab_bron'] >0 )  or ($res['ab_uron'] >0 ))     and ($res['sowner'] !=0)) continue;
			                    }

                	$chk_massa+=$res[massa];
                	$item[$ff]=$res;
			$ff++;
		}
	}
	else
	{
		$mess=" Предмет не найден в рюкзаке";
	}
        //тут делаем все расчеты:
        if(count($item)>0){
		
		        $newmass=$mto[massa]+$chk_massa;
		        if (($newmass<=$allmass) OR ($user['klan']=='radminion') OR ($user['id'] == 8325) OR ($user['klan']=='Adminion') or $idkomubot == 84)
		        {
		          $prez='';
		          $per=0;
			        for($jj=0;$jj<count($item);$jj++)
			        {
		                   if($per==10)
		                   {
		                   	$per=0;
		                   	$pp='<br>';
		                   }
		                   else{
		                   	$pp='';
		                   }
			                   	       /*  if($dem[type]=='200' && ($dem[otdel]=='7' || $dem[otdel]=='77') && $dem[dategoden]==0)
		                                {
		                                	$sql=' `goden`="180", `dategoden`="'.(time()+60*60*24*30*3).'", ';
		                                }
		                                 if($dem[type]=='200' && ($dem[otdel]=='71' || $dem[otdel]=='73') && $dem[dategoden]==0)
		                                {
		                                	$sql=' `goden`="90", `dategoden`="'.(time()+60*60*24*30*6).'", ';
		                                }
			                        */
			                   if($gift==1 && $item[$jj][type]==200 && ($item[$jj][otdel]=='7' || $item[$jj][otdel]=='77') && $item[$jj][dategoden]==0 )
		                       {
		                          $prezs=' `goden`="90", `dategoden`="'.(time()+60*60*24*30*3).'", ';
		                       }
		
			                   if($gift==1 && $item[$jj][type]==200 && ($item[$jj][otdel]=='71' || $item[$jj][otdel]=='73') && $item[$jj][dategoden]==0 )
		                       {
		                          $prezs=' `goden`="180", `dategoden`="'.(time()+60*60*24*30*6).'", ';
		                       }
		
			                   $sql_it_id.= $item[$jj][id].',';
			                   $sql_delo.=get_item_fid($item[$jj]).','.$pp;
			                   $per++;
				}

			        if($ttype==1)
			        {
			        	$jj=$jj*10;
			        	
		                        if($idkomu==83 || $idkomu==136 || $idkomubot == 84)
		                        {
						$jj = 1;
		                        	$gift=1;
		                        }
		                        else
		                        {
					        $gift=0;
		                        }
			        }

			        if($ttype==2)
			        {
			        	$jj=$jj*100;
					//if ($idkomubot != 84) $gift = 0;
		                        if($idkomu==83 || $idkomu==136 || $idkomubot == 84)
		                        {
						$jj = 1;
		                        	$gift=1;
		                        }
		                        else
		                        {
					        $gift=0;
		                        }

			        }


			        $sql_it_id=substr($sql_it_id,0,-1);
			        $sql_delo=substr($sql_delo,0,-1);
			        $ook=0;

			if($gift==0)
			{
				if($user[money]>=$jj)
				{
					$money_sql="update `users` set `money`=`money`-".$jj." where `id`='".$user['id']."'";
					$prez='';
					$txt='Передан';
					$txt1='передано';
					$txt2='передал';
					$ook=1;
				}
				else
				{
					$mess='Недостаточно денег на оплату передачи!';
				}
			}
			else
			{
				if($user[money]>=0)
				{
					$money_sql="";
					
					$prez=', present = "'.$user['login'].($item[otdel]==72?':|:'.$user[id]:'').'"';
					$txt='Подарен';
					$txt1='подарено';
					$txt2='подарил';
					$ook=1;
				}
			}
                    if($gift==1)
                       {
                       	 $gsql=',add_time='.time();
                       }
                       else
                       {
                       	 $gsql='';
                       }

				//подсчет и если ок то дальше - TEST
				if (($ook==1) )
				{
				if ( ($user['in_tower']!=15)  AND //не бс, не руины
					($user['ruines']==0) AND //не в руинах
					($user['klan']!='radminion') AND ($user['klan']!='Adminion') AND ($komu['klan']!='radminion') AND ($komu['klan']!='Adminion') AND //не передачи от админов и к админам
					($user['id']!=8325) AND ($komu['id']!=8325) ) //не передачи от ПБ и к ПБ
					{
						 $tco=test_give_count($user['id'],$komu['id'],$jj);
						 if (!(is_array($tco)))
							{
							//тест успешно
							 	if (give_count($user['id'],$jj) )
							 	{
							 	//ok
								 	if (give_count($komu['id'],$jj) )
								 	{
								 	//все ок
								 	}
								 	else
								 	{
								 	$mess='У персонажа "'.$komu['login'].'" недостаточно лимита передач на сегодня! ' ;
									$ook=0;															 	
								 	}
							 	}
							 	else
							 	{
							 	$mess='У Вас недостаточно лимита передач на сегодня! ' ;
								$ook=0;							 	
							 	}
							}
							else
							{
							$tlo[$user['id']]=$user['login'];
							$tlo[$komu['id']]=$komu['login'];							
							
							 foreach ($tco as $k => $l)
							 	{
								$mess.='У Персонажа "'.$tlo[$l].'" недостаточно лимита передач на сегодня! <br> ' ;
								}
							$ook=0;
							}
					
					}
				}////////////////////////////////////////////////////////////////////////////////////////////////////////
					




		           if($ook==1){
		           	    $counter=0;
                         		while($counter<100)
			            {
			                if($item[0][add_pick]!='')
			                {
			                	undress_img($item[0]);
			                	$ok1=1;
			                }
			                else
			                {
			                	$ok1=1;
			                }

			                $sql="update oldbk.`inventory` set ".$prezs." `owner` = ".$komu['id']." ".$prez." ".$gsql." where `id` in (".$sql_it_id.") AND prototype!=40000001 AND prototype!=2123456804 and `owner`= '".$user['id']."';";
                            //echo $sql;
			                if(mysql_query($sql) && $ok1==1)
			                {
				               	 	if($money_sql){
				               	 	mysql_query($money_sql);
					               	 }
					               	if (($user['in_tower']==0 || $ttype==1 || $ttype==2) && $item[0][labonly]==0 && ($item[0][bs_owner]==0 || ($item[0][bs_owner]==15 && $user['in_tower'] == 15)))
					               	{
				        		//new_delo
				        			if(($ttype==1 || $ttype==2)  && $gift<1)
				        			{
                                        					$rec['owner']=$user[id];
										$rec['owner_login']=$user[login];
										$rec['owner_balans_do']=$user['money'];
										$rec['owner_balans_posle']=$user['money']-$jj;;
										$rec['target']=$komu['id'];
										$rec['target_login']=$komu['login'];
										$rec['type']=237;
										$rec['sum_kr']=0;
										$rec['sum_kom']=$jj;
					                                        $rec['add_info']='Заплатил за передачу в БС';
					                                        add_to_new_delo($rec);
				        			}
				        			else
				        			{
					  		    			$rec['owner']=$user[id];
										$rec['owner_login']=$user[login];
										$rec['owner_balans_do']=$user['money'];
										$rec['owner_balans_posle']=($gift==1?$user[money]:$user[money]-$jj);
										$rec['target']=$komu['id'];
										$rec['target_login']=$komu['login'];
										$rec['type']=($gift==1?38:39);//дарю/передаю предмет
										$rec['sum_kr']=0;
										$rec['sum_ekr']=0;
										$rec['sum_kom']=($gift==1?0:1);
										$rec['item_id']=$sql_delo;
										$rec['item_name']=$item[0]['name'];
										$rec['item_count']=$jj;
										$rec['item_type']=$item[0]['type'];
										$rec['item_cost']=$item[0]['cost'];
										$rec['item_dur']=$item[0]['duration'];
										$rec['item_maxdur']=$item[0]['maxdur'];
										$rec['item_ups']=$item[0]['ups'];
										$rec['item_unic']=$item[0]['unik'];

										$rec['item_incmagic_id']=$item[0]['includemagic'];
	                                    					$rec['item_ecost']=$item[0]['ecost'];
										$rec['item_proto']=$item[0]['prototype'];
                                        					$rec['item_sowner']=($item[0]['sowner']>0?1:0);
										$rec['item_incmagic']=$item[0]['includemagicname'];
										$rec['item_incmagic_count']=$item[0]['includemagicuses'];
										$rec['item_arsenal']='';
										$rec['item_mfinfo']=$item[0]['mfinfo'];
										$rec['item_level']=$item[0]['nlevel'];

										add_to_new_delo($rec); //юзеру
										$rec['owner']=$komu[id];
										$rec['owner_login']=$komu[login];
										$rec['owner_balans_do']=$komu['money'];
										$rec['owner_balans_posle']=$komu['money'];
										$rec['target']=$user['id'];
										$rec['target_login']=$user['login'];
										$rec['type']=($gift==1?98:99);//получаю/в подарок предмет
										add_to_new_delo($rec); //кому

										//region gift checker
										if($gift == 1) {
											try {
												$UserObj = new \components\models\User($user);
												$Quest = $app->quest->setUser($UserObj)->get();

												$Checker = new \components\Component\Quests\check\CheckerGift();
												$Checker->shop_id = \components\Helper\ShopHelper::TYPE_ALL;
												$Checker->item_id = $item[0]['prototype'];
												$Checker->user_to = new \components\models\User($komu);
												$Checker->operation_type = \components\Component\Quests\pocket\questTask\GiftTask::OPERATION_TYPE_GIVE;
												if (($Item = $Quest->isNeed($Checker)) !== false) {
													$Quest->taskUp($Item);
												}

												unset($UserObj);
												unset($Quest);
											} catch (Exception $ex) {
												\components\Helper\FileHelper::writeException($ex, 'fshop');
											}
										}
										//endregion

			                                    }
			
							}

								if ($ttype == 2) {
				                			$mess='Удачно '.$txt1.' "'.$item[0]['name'].'" (x'.($jj/100).'), ('.$sql_delo.') персонажу '.$komu['login'];
									addchp ('<font color=red>Внимание!</font> Персонаж "'.$user['login'].'" '.$txt2.' вам "'.$item[0]['name'].'" (x'.($jj/100).')  ','{[]}'.$komu['login'].'{[]}',$komu['room'],$komu['id_city']);
								} else {
				                			$mess='Удачно '.$txt1.' "'.$item[0]['name'].'" (x'.($jj).'), ('.$sql_delo.') персонажу '.$komu['login'];
									addchp ('<font color=red>Внимание!</font> Персонаж "'.$user['login'].'" '.$txt2.' вам "'.$item[0]['name'].'" (x'.$jj.')  ','{[]}'.$komu['login'].'{[]}',$komu['room'],$komu['id_city']);
								}
								if($gift==0){
									$user['money']-=$jj;
									$mess.=' за '.$jj.'кр.';
								}

								$counter=100;
							}
							else
							{
							    	if($counter==0||$counter==10||$counter==50||$counter==99)
								{
									$mess = 'Произошла ошибка 1. попробуйте еще раз.';
									
									///telepost('A-Tech','<font color=red>Внимание! give.php str 571</font> Ошибка передачи: каунтер='.$counter.' Персонаж '.$user['login'].' '.$txt.'/'.$txt2. ' ' . $item[0]['name']. ' '.$item[0]['id'].' кол-во'.$jj . ' Кому:'.$komu['login']);
		                            				
		                            				telepost('Bred','<font color=red>Внимание! give.php str 571</font> Ошибка передачи: каунтер='.$counter.' Персонаж '.$user['login'].' '.$txt.'/'.$txt2. ' ' . $item[0]['name']. ' '.$item[0]['id'].' кол-во'.$jj . ' Кому:'.$komu['login']);
							    	}
							    	$counter++;
							}
						}
					}
		        }
		        else {
					$mess='У персонажа "'.$komu['login'].'" переполнен рюкзак!';
				}
	    }
	}
// "передача"	залоговая для модификации
/*
	else
	if ($_REQUEST['mfobject'] && $_REQUEST['to_id'] && $_REQUEST['sd4']==$user['id'] && $_GET['s4i']==$user['sid'])
	{
		if (time()>$begin1 && time()<$end1)
		{
			return;
		}

		$res = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$_SESSION['uid']}' AND `dressed`=0 AND `id` = '{$_REQUEST['id_th']}' AND `bs_owner` ='0' LIMIT 1;"));

		if (!$res['id']) $mess="Предмет не найден в рюкзаке";
		elseif ($komu['align']==4) $mess="С хаосниками торговые сделки запрещены.";
		elseif ($komu['intel']<50) $mess="Маг должен иметь интеллект выше 50 для модификации";
		elseif (($komu['klan']=='Adminion') OR ($komu['klan']=='radminion') && $user[id]!=135821)  $mess="Это не маг :(";
		elseif ($user['money']<1) { $mess="Недостаточно денег, чтобы оплатить налог !"; }
		else {
			$row = $res;
			$row[GetShopCount()] = 1;
			
			$re .= "<table width=100%><TR ><TD align=center ><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0><BR></TD>";
			$re .= "<TD valign=top>";

			ob_start();
			showitem ($row);
			$re .= ob_get_clean();
			$re .= "</TD></TR></table>";
			$re = str_replace("\r\n", "", $re);
			$re = str_replace("\n", "", $re);
			$mess = 'Предложение на модификацию персонажу '.$komu['login'].' сделано... <a href=give.php?refresh=2>Обновить</a>';

			addchp ('<font color=red>Внимание!</font> <B>'.$user[login].'</B> предлагает Вам модифицировать предмет .<BR>\'; top.frames[\'main\'].location=\'give.php\'; var z = \'   ','{[]}'.$komu['login'].'{[]}',$komu['room'],$komu['id_city']);


			mysql_query("INSERT INTO `trade`(`to_id` ,`login`  ,`txt` ,`kr` ,`id` ,`baer`, `zalog` ) VALUES
							('{$_SESSION['uid']}','{$user['login']}','".$re."',0,'{$_REQUEST['id_th']}',{$_REQUEST['to_id']},1);");

		}

	}
*/


	//подарок
/*	if ($_REQUEST['setobject'] && $_REQUEST['to_id'] && $_REQUEST['gift'] && $_REQUEST['sd4']==$user['id'] && $_GET['s4i']==$user['sid']) {
		$res = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$_SESSION['uid']}' AND `id` = '{$_REQUEST['setobject']}' AND dressed=0 AND bs_owner='".$user[in_tower]."' AND `setsale` = 0 AND `present` = '' LIMIT 1;"));
		if (!$res['id']) $mess="Предмет не найден в рюкзаке";
		elseif ($res['dressed']!=0) $mess="Сначала необходимо снять предмет с себя.";
		else {
			$value=$res;
			if (@$value['present']) $mess='Нельзя передавать подарки';
			//elseif($user['in_tower'] == 1 && $_REQUEST['to_id']!=83) $mess = 'Только Архивариус может принимать подарки в Башне Смерти...';
			else{
				$mto = mysql_fetch_array(mysql_query("SELECT sum(`massa`) FROM oldbk.`inventory` WHERE `owner` = '$idkomu' AND `dressed` = 0 AND `setsale` = 0 AND `bs_owner` ='".$user[in_tower]."'; "));

				$u = $user;
				$user['id'] = $idkomu;
				$allmass=get_meshok_to($idkomu);
				$user = $u;

				$newmass=$mto[0]+$res['massa'];
				if ($newmass<=$allmass) {
					if (mysql_query("update oldbk.`inventory` set `present` = '{$user['login']}' ,`owner` = ".$komu['id']." where `id`='".$res['id']."' and `owner`= '".$user['id']."';")) {
						addchp ('<font color=red>Внимание!</font> Персонаж "'.$user['login'].'" подарил вам "'.$value['name'].'"</B>.   ','{[]}'.$komu['login'].'{[]}');
						if (($user['room'] < 501) || ($user['room'] > 560)) {
							mysql_query("INSERT INTO `delo`(`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES ('','0','{$_SESSION['uid']}','Подарен предмет \"".$res['name']."\" id:(cap".$res['id'].") [".$res['duration']."/".$res['maxdur']."] [ups:".$res['ups']."/unik:".$res['unik']."/inc:".$res['includemagicname']."] от \"".$user['login']."\" к \"".$komu['login']."\"','1','".time()."');");
							mysql_query("INSERT INTO `delo`(`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES ('','0','{$idkomu}','Подарен предмет \"".$res['name']."\" id:(cap".$res['id'].") [".$res['duration']."/".$res['maxdur']."] [ups:".$res['ups']."/unik:".$res['unik']."/inc:".$res['includemagicname']."] от \"".$user['login']."\" к \"".$komu['login']."\"','1','".time()."');");
						}
						$mess='Удачно подарен предмет "'.$value['name'].'"  персонажу '.$komu['login'];
					}
					else {
						$mess='Произошла ошибка!';
					}
				}
				else {
					$mess='У персонажа "'.$komu['login'].'" переполнен рюкзак!';
				}
			}
		}
	}       */
   //продажа
	if ($_REQUEST['cost'] >= 1 && $_REQUEST['to_id'])
		{
	    	if($user['room']>500&&$user['room']<=560)
	    			{
			    	//Тут продажи фигни за нал апрещены
			    	//думаю сюда же лабу надо добавить =)
			    	}
			    else
	    		{
	    		$_REQUEST['cost']=round(floatval($_REQUEST['cost']),2);
	    		
					$res = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' AND `dressed`=0 AND `id` = '{$_REQUEST['id_th']}' ".$VAUCHER." AND `bs_owner` ='0' AND prototype!=40000001 AND prototype!=2123456804 and type!=99 AND setsale=0 and labonly = 0 LIMIT 1;"));
					if (!$res['id']) $mess="Предмет не найден в рюкзаке";
					elseif ($res['dressed']!=0) $mess="Сначала необходимо снять предмет.";
					elseif ($komu['align']==4) $mess="С хаосниками торговые сделки запрещены.";
					elseif ($res['sowner'] !=0) $mess="Этот предмет нельзя продать!";
					elseif ($res['otdel'] ==72) $mess="Этот предмет нельзя продать!";					
					elseif ($res['type'] ==77) $mess="Этот предмет нельзя продать!";										
					elseif (in_array($res['prototype'],$vauch_a) && $res['sowner'] > 0) $mess="Предмет не найден в рюкзаке";
					elseif ($user['money']<1) { $mess="Недостаточно денег, чтобы оплатить налог на продажу!"; }
					//elseif ($user['in_tower'] == 1) {$mess = "Не в Башне Смерти.......";}
					else {
						$value=$res;
						if (@$value['present']) $mess='Нельзя передавать подарки';
						else{
							#KOMOK_LOG
							$row = $res;
								function calb ($b) {
									global $re;
										$re .= $b;
								}
								$row[GetShopCount()] = 1;
								//$color = '#D5D5D5';
								$re .= "<table width=100%><TR ><TD align=center ><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0><BR></TD>";
								$re .= "<TD valign=top>";

								if ($res['prototype'] == 100005) $_REQUEST['cost'] = 5*18;
								if ($res['prototype'] == 100015) $_REQUEST['cost'] = 15*18;
								if ($res['prototype'] == 100020) $_REQUEST['cost'] = 20*18;
								if ($res['prototype'] == 100025) $_REQUEST['cost'] = 25*18;
								if ($res['prototype'] == 100040) $_REQUEST['cost'] = 40*18;
								if ($res['prototype'] == 100100) $_REQUEST['cost'] = 100*18;
								if ($res['prototype'] == 100200) $_REQUEST['cost'] = 200*18;
								if ($res['prototype'] == 100300) $_REQUEST['cost'] = 300*18;
								
								
								if ($res['ekr_flag'] == 3) {
									if ($res['ecost'] > 0) {
										$testcost = ($res['ecost']*EKR_TO_KR); // для предметов из березы цена жесткая екры * курс
										if ($res['type'] == 50) {
											$testcost = $testcost - (($testcost / $res['maxdur']) * $res['duration']);
										}
	
										if ($_REQUEST['cost'] < $testcost) $_REQUEST['cost'] = $testcost;

										if ($_REQUEST['cost']<=0) { die(); }
									} else {
										die();
									}
								} elseif ($res['ekr_flag'] > 0 && $res['ekr_flag'] != 3) {
									$_REQUEST['cost'] = ($res['ecost']*EKR_TO_KR); // для предметов из березы цена жесткая екры * курс
									if ($res['type'] == 50) {
										$_REQUEST['cost'] = $_REQUEST['cost'] - (($_REQUEST['cost'] / $res['maxdur']) * $res['duration']);
									}

									if ($_REQUEST['cost']<=0) { die(); }

									if ($res['prototype'] == 55510350 || $res['prototype'] == 55510352 || $res['prototype'] == 55510351 || $res['prototype'] == 410021 || $res['prototype'] == 410022 || $res['prototype'] == 410026 || $res['prototype'] == 410027 || $res['prototype'] == 410028 ) {
										if (ceil(($res['dategoden'] - time())/(60*60*24)) <= 6) {
											die();
										}
									}
									
								}
								

								//function calb($t) {
								//    global $re;
								//    $re .= $t;
								//}

								ob_start();
									showitem ($row);
								//ob_end_flush();
								$re .= ob_get_clean();
								$re .= "</TD></TR></table>";
								$re = str_replace("\r\n", "", $re);
								$re = str_replace("\n", "", $re);
								$re = str_replace("'", "\'", $re);
								$mess = 'Предложение персонажу '.$komu['login'].' сделано.';
								
								mysql_query("update oldbk.`inventory` set `tradesale` = '".$_REQUEST['cost']."' where `id`='".$res['id']."' AND prototype!=40000001 AND prototype!=2123456804 and `owner`= '".$res['owner']."';") or die(mysql_error()."!!");
								mysql_query("INSERT INTO `trade`(`to_id` ,`login`  ,`txt` ,`kr` ,`id` ,`baer` ) VALUES
										('{$_SESSION['uid']}','{$user['login']}','".mysql_escape_string($re)."','{$_REQUEST['cost']}','{$_REQUEST['id_th']}',{$_REQUEST['to_id']});") or die(mysql_error()."!!!");

						}
					}

				}
			}
			

	if ($_REQUEST['transfermf'] && $_REQUEST['to_id'] && $_REQUEST['prise']>0 )
	{
	// проверяем предмет у заказчика и ставим в трейд шаг два c условиями МФ
		/*
	 	$res = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `dressed`=0 AND `owner` = '{$_REQUEST['to_id']}' AND `id` = '{$_REQUEST['transfermf']}' AND present!='Арендная лавка' and prototype not in (260,262,100015,100020,100040,100200)
	 	AND (type < 12 OR type = 28 ) AND type != 3 AND bs_owner = 0 AND name NOT LIKE '% (мф)%' AND name NOT LIKE '%Букет%' AND setsale=0 AND prokat_idp=0 AND arsenal_klan = '' AND (gsila > 0 OR glovk > 0 OR ginta > 0 OR gintel > 0 OR mfkrit> 0 OR mfakrit > 0 OR mfuvorot > 0 OR mfauvorot > 0 OR bron1 > 0 OR bron2 > 0 OR bron3 > 0 OR bron4 > 0 OR ghp > 0) LIMIT 1;"));
	 	if ($res[id])
	 	{
	 	$row = $res;
		$row[GetShopCount()] = 1;
		$re .= "<table width=100%><TR ><TD align=center ><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0><BR></TD>";
		$re .= "<TD valign=top>";
		ob_start();
		showitem ($row);
		$re .= ob_get_clean();
		$re .= "</TD></TR></table>";
		$re = str_replace("\r\n", "", $re);
		$re = str_replace("\n", "", $re);
		mysql_query("UPDATE oldbk.`inventory` set tradesale='{$_REQUEST['prise']}' where `owner` = '{$_REQUEST['to_id']}' AND `id` = '{$_REQUEST['transfermf']}' ");
		mysql_query("INSERT INTO `trade`(`to_id` ,`login`  ,`txt` ,`kr` ,`id` ,`baer`, `zalog` ) VALUES	('{$_SESSION['uid']}','{$user['login']}','".$re."','{$_REQUEST['prise']}','{$_REQUEST['transfermf']}',{$_REQUEST['to_id']},2);");
	     	$mess ='<font color=red><b>Ожидаем подтверждения от заказчика...<a href=give.php?refresh=1>Обновить</a></b></font>';
		addchp ('<font color=red>Внимание!</font> <B>'.$user[login].'</B> установил стоимость магических услуг .<BR>\'; top.frames[\'main\'].location=\'give.php\'; var z = \'   ','{[]}'.$komu['login'].'{[]}',$komu['room'],$komu['id_city']);
	     	}
	     	else
	     	{
	     	$mess ='<font color=red><b>Предмет не может быть модифицирован...</b></font>';
	     	}
	     	*/
	}

	/*else if ($_REQUEST['transfermfconf'] && $_REQUEST['to_id'] )
	{
	//1. проверить есть ли у юзера бабки На все это дело + предмет
	
		$res = mysql_fetch_array(mysql_query("SELECT i.*, sh.cost as shcost FROM oldbk.inventory as i INNER JOIN oldbk.shop as sh  on sh.id = i.prototype WHERE i.`dressed`=0 AND i.`owner` = '{$user[id]}' AND i.`id` = '{$_REQUEST['transfermfconf']}' AND i.present!='Арендная лавка' and prototype not in (260,262,100015,100020,100040,100200)
	 	AND (i.type < 12 OR i.type=28 ) AND i.type != 3 AND i.tradesale > 0 AND i.bs_owner = 0 AND i.name NOT LIKE '% (мф)%' AND i.name NOT LIKE '%Букет%' AND i.setsale=0 AND i.prokat_idp=0 AND i.arsenal_klan = '' AND (i.gsila > 0 OR i.glovk > 0 OR i.ginta > 0 OR i.gintel > 0 OR i.mfkrit> 0 OR i.mfakrit > 0 OR i.mfuvorot > 0 OR i.mfauvorot > 0 OR i.bron1 > 0 OR i.bron2 > 0 OR i.bron3 > 0 OR i.bron4 > 0 OR i.ghp > 0) LIMIT 1;"));
	 	if ($res[id])
	 	{
		$mf_cst = (int)$res['shcost'];
		if (($res['gsila'] == 0) and ($res['glovk'] == 0) and ($res['ginta'] == 0) and ($res['gintel'] == 0))
		{
			$mf_cst = round((int)$res['shcost']/2, 0);
		}
		$need_k=round($mf_cst*0.5);

	 	$all_need_money=$need_k+$res['tradesale']+1;
	 	if  ($user['money'] < $all_need_money)
	 		{
		        $mess ='<font color=red><b>Не хватает денег для совершения операции</b></font>';
			}
			else
			{
			//1 кр. за услугу
			//$res['tradesale'] - за услуги мага - магу
			// $res['cost']=round($res['cost']*0.5); - за модификацию
	 		//тут непосредственно делаем модификацию
	 		// и экран для мага с разультатом МФ
	 		$user_mag= mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_REQUEST['to_id']}' and room=23  LIMIT 1;"));
	 		 if ($user_mag[id])
	 		 {
	 		$intel = (int)$user_mag['intel'];
			if($intel < 0) { $intel = 0; }
			$up_stats = get_free_stats_up($intel);
			$up_hp = round(rand(5, $up_stats * 10));
			if($up_hp > 20) { $up_hp = 20; }
			$up_bron = round(rand(10, $up_stats * 10)/10);
			if($up_bron > 3) { $up_bron = 3; }
			if (($res['gsila'] == 0) and ($res['glovk'] == 0) and ($res['ginta'] == 0) and ($res['gintel'] == 0))
				{
				$up_stats = 0;
				}
			$bron1 = (($res['bron1'] > 0) ? ($res['bron1'] + $up_bron) : "0");
			$bron2 = (($res['bron2'] > 0) ? ($res['bron2'] + $up_bron) : "0");
			$bron3 = (($res['bron3'] > 0) ? ($res['bron3'] + $up_bron) : "0");
			$bron4 = (($res['bron4'] > 0) ? ($res['bron4'] + $up_bron) : "0");
			$hp = (($res['ghp'] > 0) ? ($res['ghp'] + $up_hp):"0");

			if($up_stats == 0)
			{
			   if(!($res['ghp'] > 0))
				{
					$hp = $up_hp;
				}
			}


				if ($up_stats >2)
				{
				$marka=1; $tt='(Уинк)';
				$sql='insert into unic_log (`item_id`,`time`,`creater`,`where_cr`,`what_add`) values ('.$res[id].','.time().','.$user[id].',0,"st_");';
		                mysql_query($sql);
				} else {$marka=0; $tt='';}

				if(mysql_query("UPDATE oldbk.`inventory` SET
							`ghp` = '".(int)$hp."',
							`bron1` = '".(int)$bron1."',
							`bron2` = '".(int)$bron2."',
							`bron3` = '".(int)$bron3."',
							`bron4` = '".(int)$bron4."',
							`stbonus` = `stbonus` + '".(int)$up_stats."',
							`type3_updated`='".$marka."',
							`unik`='".$marka."',
							`tradesale`=0,
							`cost` = `cost` + '".$need_k."',
							`name` = CONCAT(`name`, ' (мф)')
							WHERE `id` = '{$res[id]}' LIMIT 1;"))
				{
						mysql_query("UPDATE `users` set `money` = `money`- '".$all_need_money."' WHERE id = {$_SESSION['uid']} LIMIT 1 ; ");
						mysql_query("UPDATE `users` set `money` = `money`+ '".$res['tradesale']."' WHERE id = {$user_mag[id]}  LIMIT 1  ; ");
						addchp ('<font color=red>Внимание!</font> Персонаж "'.$user['login'].'" передал вам <B>'.strval($res['tradesale']).' кр</B>. за модификацию предмета .<BR>\'; top.frames[\'main\'].location=\'give.php\'; var z = \'   ','{[]}'.$user_mag['login'].'{[]}',$user_mag['room'],$user_mag['id_city']);


						        //new_delo
		  		    		$rec['owner']=$user[id];
							$rec['owner_login']=$user[login];
							$rec['owner_balans_do']=$user['money'];
							$rec['owner_balans_posle']=$user['money']-$all_need_money;
							$rec['target']=$user_mag['id'];
							$rec['target_login']=$user_mag['login'];
							$rec['type']=42;//заплатил за МФ
							$rec['sum_kr']=strval($res['tradesale']);
							$rec['sum_ekr']=0;
							$rec['sum_kom']=1;//комиссия
							$rec['item_id']=get_item_fid($res);
							$rec['item_name']=$res['name'];
							$rec['item_count']=1;
							$rec['item_type']=$res['type'];
							$rec['item_cost']=$res['cost'];
							$rec['item_dur']=$res['duration'];
							$rec['item_maxdur']=$res['maxdur'];
							$rec['item_ups']=$res['ups'];
							$rec['item_unic']=$res['unik'];

							$rec['item_incmagic_id']=$res['includemagic'];
		                    $rec['item_ecost']=$res['ecost'];
							$rec['item_proto']=$res['prototype'];
                            $rec['item_sowner']=($res['sowner']>0?1:0);
							$rec['item_incmagic']=$res['includemagicname'];
							$rec['item_incmagic_count']=$res['includemagicuses'];
							$rec['item_arsenal']='';
							add_to_new_delo($rec); //юзеру
							$rec['sum_kom']=0;
							$rec['item_unic']=$marka;
							$rec['owner']=$user_mag[id];
							$rec['owner_login']=$user_mag[login];
							$rec['owner_balans_do']=$user_mag['money'];
							$rec['owner_balans_posle']=$user_mag['money']+$res['tradesale'];
							$rec['target']=$user['id'];
							$rec['target_login']=$user['login'];
							$rec['type']=43;//мфнул забабки
						        add_to_new_delo($rec); //кому
					
					$user[money]=$user[money]-$all_need_money;

					//делаем картинку из того что получилось для отображения магу

					$row = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `id` = '{$res[id]}'  LIMIT 1;"));
					$row[GetShopCount()] = 1;
					$re .= "<table width=100%><TR ><TD align=center ><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0><BR></TD>";
					$re .= "<TD valign=top>";
					ob_start();
					showitem ($row);
					$re .= ob_get_clean();
					$re .= "</TD></TR></table>";
					$re = str_replace("\r\n", "", $re);
					$re = str_replace("\n", "", $re);
					mysql_query("INSERT INTO `trade`(`to_id` ,`login`  ,`txt` ,`kr` ,`id` ,`baer`, `zalog` ) VALUES	('{$_SESSION['uid']}','{$user['login']}','".$re."','0','{$res[id]}',{$user_mag[id]},3);");
				        $mess ='<font color=red><b>Предмет удачно модифицирован</b></font>';

				}



		         }
		         else
		         {
		          $mess ='<font color=red><b>Маг вышел из ремонтной мастерской</b></font>';
		         }

	 		}
	 	}
      		else
	     	{
	     	$mess ='<font color=red><b>Предмет не может быть модифицирован...</b></font>';
	     	}
	}*/
	else
	if ($_REQUEST['transfersale'] && $_REQUEST['to_id']) {
	    $_transfersale = (int)$_REQUEST['transfersale'];
		$_trade_row = mysql_fetch_array(mysql_query("SELECT count(*) as cnt FROM `trade` WHERE `baer` = {$user['id']} and `id` = {$_transfersale} LIMIT 1;"));
		$_is_trade = false;
		if($_trade_row && $_trade_row['cnt'] > 0) {
			$_trade_row = true;
        }

		$res = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `dressed`=0 and setsale=0 and type!=99 and labonly = 0 and sowner=0
				AND `owner` = '{$_REQUEST['to_id']}' AND `id` = '{$_transfersale}' ".$VAUCHER." AND `bs_owner` = 0 AND prototype!=40000001 AND prototype!=2123456804 LIMIT 1;"));

		if(!$_trade_row) {
			$mess = '<font color=red><b>Сделка не найдена</b></font>';
		}  elseif(isset($_REQUEST['cancel']) && $_trade_row) {
			mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} and `id` = {$_transfersale}  LIMIT 1;");
			$mess = '<font color=red><b>Вы отказались от сделки</b></font>';
		} elseif($user['money'] < $res['tradesale']) {

		    $mess ='<font color=red><b>Не хватает денег для совершения операции</b></font>';
		} elseif ($komu['money']<1) {
		    $mess ='<font color=red><b>У продающей стороны недостаточно средств для оплаты комиссии.</b></font>';
		} elseif (in_array($res['prototype'],$vauch_a) && $res['sowner'] > 0) {
		    $mess ='<font color=red><b>Нельзя передавать привязанный ваучер.</b></font>';
		} elseif($res[id]>0)
		{
		    if($res[add_pick]!='')
              {
              	undress_img($res);
              	$ok=1;
              }
			  else
			  {
			      $ok=1;
			  }
			  
			  
				//подсчет и если ок то дальше - TEST
				if (($ok==1)  )
				{
				if ( ($user['in_tower']!=15)  AND //не бс, не руины
					($user['ruines']==0) AND //не в руинах
					($user['klan']!='radminion') AND ($user['klan']!='Adminion') AND ($komu['klan']!='radminion') AND ($komu['klan']!='Adminion') AND //не передачи от админов и к админам
					($user['id']!=8325) AND ($komu['id']!=8325) ) //не передачи от ПБ и к ПБ
					{
						 $tco=test_give_count($user['id'],$komu['id'],1);
						 if (!(is_array($tco)))
							{
							//тест успешно
							 	if (give_count($user['id'],1) )
							 	{
							 	//ok
								 	if (give_count($komu['id'],1) )
								 	{
								 	//все ок
								 	}
								 	else
								 	{
								 	$mess='У персонажа "'.$komu['login'].'" недостаточно лимита передач на сегодня! ' ;
									$ok=0;															 	
								 	}
							 	}
							 	else
							 	{
							 	$mess='У Вас недостаточно лимита передач на сегодня! ' ;
								$ok=0;							 	
							 	}
							}
							else
							{
							$tlo[$user['id']]=$user['login'];
							$tlo[$komu['id']]=$komu['login'];							
							
							$mess='';
							 foreach ($tco as $k => $l)
							 	{
								$mess.='У Персонажа "'.$tlo[$l].'" недостаточно лимита передач на сегодня! <br> ' ;
								}
							$ok=0;
							}
					
					}
				}////////////////////////////////////////////////////////////////////////////////////////////////////////			  
			  
			  
			  
			  
			  
			  
			  
			  if($ok==1)
			  {
				if ($res['ekr_flag'] == 1) {
				  	$add_present=",  present='".$komu['login']."'  "; //  после продажи покупателю падает подарком
				  	
				  if ($res['prototype']==33333)
				  	{
				  	//если билет лото, то правим индекс
				  	mysql_query("UPDATE `oldbk`.`item_loto` SET `owner`='{$user['id']}' WHERE `id`='{$res['mffree']}' ");
				  	}	
				  	
				  	
			  	}

				if ($res['ekr_flag'] == 2) {
				  	//$add_present=",  sowner='".$komu['id']."'  "; //  после продажи перевязываем вещь
				}

			  
			    	mysql_query_100("update oldbk.`inventory` set `owner` = ".$user['id']." ".$add_present."  where `id`='".$res['id']."' and `owner`= '".$res['owner']."' AND prototype!=2123456804 AND prototype!=40000001");
			    	mysql_query("update `users` set `money`=`money`-{$res['tradesale']} where `id`='".$user['id']."'");
			    	mysql_query("update `users` set `money`=`money`+{$res['tradesale']}-1 where `id`='{$_REQUEST['to_id']}'");
				    mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} and `id` = {$_transfersale}  LIMIT 1;");
				    if ($user['in_tower']==0 && $res[labonly]==0 && $res[bs_owner]==0)
				    {
				    		//new_delo
	  		    			$rec['owner']=$user[id];
						$rec['owner_login']=$user[login];
						$rec['owner_balans_do']=$user['money'];
						$rec['owner_balans_posle']=$user['money']-$res['tradesale'];
						$rec['target']=$komu['id'];
						$rec['target_login']=$komu['login'];
						$rec['type']=40;//купил предмет
						$rec['sum_kr']=$res['tradesale'];
						$rec['sum_ekr']=0;
						$rec['sum_kom']=0;
						$rec['item_id']=get_item_fid($res);
						$rec['item_name']=$res['name'];
						$rec['item_count']=1;
						$rec['item_type']=$res['type'];
						$rec['item_cost']=$res['cost'];
						$rec['item_dur']=$res['duration'];
						$rec['item_maxdur']=$res['maxdur'];
						$rec['item_ups']=$res['ups'];
						$rec['item_unic']=$res['unik'];
						$rec['item_incmagic_id']=$res['includemagic'];
	                    			$rec['item_ecost']=$res['ecost'];
						$rec['item_proto']=$res['prototype'];
                        			$rec['item_sowner']=($res['sowner']>0?1:0);
						$rec['item_incmagic']=$res['includemagicname'];
						$rec['item_incmagic_count']=$res['includemagicuses'];
						$rec['item_arsenal']='';
						$rec['item_mfinfo']=$rec['mfinfo'];
						$rec['item_level']=$rec['nlevel'];

						add_to_new_delo($rec); //юзеру
						$rec['sum_kom']=1;
						$rec['owner']=$komu[id];
						$rec['owner_login']=$komu[login];
						$rec['owner_balans_do']=$komu['money'];
						$rec['owner_balans_posle']=$komu['money']+$res['tradesale']-1;
						$rec['target']=$user['id'];
						$rec['target_login']=$user['login'];
						$rec['type']=41;//продал предмет
					    	add_to_new_delo($rec); //кому

				    }
				    else
				    {
	                    			$rec['owner']=$user[id];
						$rec['owner_login']=$user[login];
						$rec['owner_balans_do']=$user['money'];
						$rec['owner_balans_posle']=$user['money']-$res['tradesale'];
						$rec['target']=$komu['id'];
						$rec['target_login']=$komu['login'];
						$rec['type']=237;
						$rec['sum_kr']=$res['tradesale'];
						$rec['sum_kom']=0;
						$rec['add_info']='Купил не выносимый предмет в БС/лабе';
						add_to_new_delo($rec);
				    }

				if(in_array($res['prototype'],$vauch_a)) {
					$txt1 = 'Я, персонаж <b>'.$user['login'].'</b> покупаю <b>'.$res['name'].'</b> у персонажа <b>'.$komu['login'].'</b> за '.$res['tradesale'].' кредитов';
					$a_info = $user[login].','.$user[klan].','.$user[align].','.$user[level].',0,'.$user['id'];
					$q1 = 'INSERT INTO forum 
							(`type`,`text`,`date`,`parent`,`author`,`a_info`,`min_align`,`max_align`,`icon`)
						VALUES(2,"'.mysql_real_escape_string($txt1).'","'.date("d.m.y H:i").'",229687002,"'.$user['id'].'","'.$a_info.'",15,15,13)
					';

					mysql_query($q1) or die(mysql_error());


					$txt2 = 'Я, персонаж <b>'.$komu['login'].'</b> выношу <b>'.$res['name'].'</b> персонажу <b>'.$user['login'].'</b> за '.$res['tradesale'].' кредитов';
					$a_info = $komu[login].','.$komu[klan].','.$komu[align].','.$komu[level].',0,'.$komu['id'];
					$q2 = 'INSERT INTO forum 
							(`type`,`text`,`date`,`parent`,`author`,`a_info`,`min_align`,`max_align`,`icon`)
						VALUES(2,"'.mysql_real_escape_string($txt2).'","'.date("d.m.y H:i").'",229687002,"'.$komu['id'].'","'.$a_info.'",15,15,13)
					';

					mysql_query($q2) or die(mysql_error());
					mysql_query('UPDATE oldbk.inventory SET sowner = '.$user['id'].' WHERE id = '.$res['id']);
				}

			    	$mess='Удачно куплено "'.$res['name'].'"  у персонажа '.$komu['login'];
			    	$mess2='Удачно куплено "'.$res['name'].'"  персонажем '.$user['login'];
			    	addchp ('<font color=red>Внимание!</font>  '.$mess2,'{[]}'.$komu['login'].'{[]}',$komu['room'],$komu['id_city']);
			    	$user['money']-=$res['tradesale'];
       		  }
       		  else
       		  {
       		  	if ($mess=='')
       		  	{
       		  	$mess='Ошибка продажи. попробуйте еще раз';
       		  	}
       		  }
		}
	}

}
?>

</HEAD>
<body bgcolor=e2e0e0><div id=hint3 class=ahint></div><div id=hint4 class=ahint></div>
<H3>Передача предметов/кредитов другому игроку</H3>
<TABLE width=100% cellspacing=0 cellpadding=0>
<TR><TD>
<? if ($step==3) {
echo 'К кому передавать: <font color=red><SCRIPT>drwfl("'.@$komu['login'].'",'.@$komu['id'].',"'.@$komu['level'].'","'.@$komu['align'].'","'.@$komu['klan'].'")</SCRIPT></font>';
?> <INPUT TYPE=button value="Сменить" onClick="findlogin('Передача предметов','give.php','FindLogin')"><BR><?
}else
{
	$roww = mysql_fetch_array(mysql_query("SELECT * FROM `trade` WHERE `baer` = {$user['id']} LIMIT 1;"));
$del_trade=true;
		if (($roww[zalog]==1) AND ($roww['id']))
		{
			?> <SCRIPT>mftransfer(<?=$roww['to_id']?>, '<?=$roww['login']?>', '<?=str_replace("\r\n","",$roww['txt'])?>',  <?=$roww['id']?>);</SCRIPT><?
		}
		elseif (($roww[zalog]==2) AND ($roww['id']))
		{
		//окно подтверждения цены
	 	$row = mysql_fetch_array(mysql_query("SELECT i.*, sh.cost as shcost FROM oldbk.inventory as i INNER JOIN oldbk.shop as sh  on sh.id = i.prototype WHERE  i.id = '".$roww['id']."' AND setsale=0 LIMIT 1;"));
		$mf_cst = (int)$row['shcost'];
		if (($row['gsila'] == 0) and ($row['glovk'] == 0) and ($row['ginta'] == 0) and ($row['gintel'] == 0))
		{
			$mf_cst = round((int)$row['shcost']/2, 0);
		}
		$need_k=round($mf_cst*0.5);
		?> <SCRIPT>mftransferconf(<?=$roww['to_id']?>, '<?=$roww['login']?>', '<?=str_replace("\r\n","",$roww['txt'])?>',  <?=$roww['id']?>, <?=$roww['kr']?> , <?=$need_k?>   );</SCRIPT><?
		}
		elseif (($roww[zalog]==3) AND ($roww['id']))
		{
			?> <SCRIPT>mftransfershow(<?=$roww['to_id']?>, '<?=$roww['login']?>', '<?=str_replace("\r\n","",$roww['txt'])?>',  <?=$roww['id']?>);</SCRIPT><?
		}
		elseif (($roww[zalog]==4) AND ($roww['id'])) //если =4 то это продажа золота
		{
		//echo "start.....";
		$send_gold=round($roww['kr']/$GOLD_GIVE_KURS);
	
		$gold_lim=800; //общий лимит для покупок на день http://tickets.oldbk.com/issue/oldbk-2618
		
		$get_my_lim=mysql_fetch_array(mysql_query("select sum(gold) as gold  from users_gold_log where baer_owner='{$user['id']}' and `tdate`=CURDATE() "));

		$my_lim=(int)$get_my_lim['gold'];
		$gold_lim-=$my_lim;
		
				if (($_REQUEST['cancel']) and  ($_REQUEST['tcodeid']==$roww['id']) )
				{
				//отказ удаляем заявку
				err('Вы отказались от сделки!');
				$del_trade=true;		
				}
				elseif (($_REQUEST['confim']) and  ($_REQUEST['tcodeid']==$roww['id']) )
				{
				//echo "проводим операцию...";

					 if ($gold_lim>=$send_gold)  	//1. проверка лимита на возможность покупки
					 	{
					 		
					 		if ($user['money']>=$roww['kr']) //2. проверка КР на покупателе
					 			{
									$ftelo = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$roww['to_id']}' ")); // продавец
					 			
	 								//3. проверка доступности золота на продавце
									$q = mysql_query('START TRANSACTION') or die();
									$q = mysql_query('SELECT * FROM users WHERE id = '.$roww['to_id'].' OR id = '.$user['id'].' FOR UPDATE') or die("stop:1");

									if ($ftelo['gold']>=$send_gold)
											{
												//4. все ок снимаем там золото и добавляем кр.
												//пишем в дело продавану
												//5. добавляем золото и отнимаем кр
												mysql_query('UPDATE users  SET `gold` = `gold` - '.$send_gold.' , `money`= `money` + '.$roww['kr'].' WHERE id = '.$roww['to_id']) or die("stop:3");
												mysql_query('UPDATE users  SET `gold` = `gold` + '.$send_gold.' , `money`= `money` - '.$roww['kr'].' WHERE id = '.$user['id']) or die("stop:4");	
																							
												//new_delo
												$rec=array();
								  		    		$rec['owner']=$user[id];
												$rec['owner_login']=$user[login];
												$rec['owner_balans_do']=$user['money'];
												
												$user['money']-=$roww['kr'];
												$user['gold']+=$send_gold;
												
												$rec['owner_balans_posle']=$user['money'];
												$rec['target']=$ftelo['id'];
												$rec['target_login']=$ftelo['login'];
												$rec['type']=3636;//передача монет
												$rec['sum_kr']=$roww['kr'];
												$rec['add_info'] = $send_gold."/".$user['gold'];																	
												if (add_to_new_delo($rec) === FALSE) die();

												$rec=array();												
							  		    			$rec['owner']=$ftelo['id'];
												$rec['owner_login']=$ftelo['login'];
												$rec['owner_balans_do']=$ftelo['money'];
												
												$ftelo['money']+=$roww['kr'];
												$ftelo['gold']-=$send_gold;												
												
												$rec['owner_balans_posle']=$ftelo['money'];
												$rec['type']=3737;//получение кредитов												
												$rec['sum_kr']=$roww['kr'];												

												$rec['target']=$user['id'];
												$rec['target_login']=$user['login'];
												$rec['sum_kr']=$roww['kr'];
												$rec['add_info'] = $send_gold."/".$ftelo['gold'];												

												if (add_to_new_delo($rec) === FALSE) die();
												
												//лог лимитов
												mysql_query("INSERT INTO `oldbk`.`users_gold_log` SET `trade_owner`='{$ftelo['id']}',`gold`='{$send_gold}',`baer_owner`='{$user['id']}',`kr`='{$roww['kr']}',`tdate`=NOW();") or die();
												err('Сделка прошла удачно, вы купили <b>'.$send_gold.'</b> монет за <b>'.$roww['kr'].'</b> кр. у персонажа <b>'.$ftelo['login'].'</b>');
				 								$del_trade=true;
				 								addchp('<font color=red>Внимание!</font> Удачно продано <b>'.$send_gold.'</b> монет за <b>'.$roww['kr'].'</b> кр.  персонажу <B>'.$user[login].'</B> ','{[]}'.$ftelo['login'].'{[]}',$ftelo['room'],$ftelo['id_city']);
												addchp('<font color=red>Внимание!</font> Удачно куплено <b>'.$send_gold.'</b> монет за <b>'.$roww['kr'].'</b> кр.  у персонажа <B>'.$ftelo[login].'</B> ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);				 								
				 								//1. продавцу монет чтобы приходило уведомление что сделка прошла (сейчас оно ему не приходит)
				 										
											}
											else
											{
								 			err('У Продавца уже нет этих монет! Сделка отменена!');
			 								$del_trade=true;		
											}
									$q = mysql_query('COMMIT') or die();
					 			}
					 			else
					 			{
					 			err('У Вас не достаточно кредитов для покупки этих монет!');
					 			}
					 	}
					 	else
					 	{
					 	err('У Вас исчерпан лимит покупки монет на сегодня!');
					 	}

				}
				else
				{
				$err=0;
					
					 if (!($gold_lim>=$send_gold))  	//1. проверка лимита на возможность покупки
					 	{
					 	$err=1;
					 	}
					 	elseif (!($user['money']>=$roww['kr'])) 
					 	{
					 	$err=2;
					 	}

				echo "<SCRIPT>transfergold({$roww['to_id']}, '{$roww['login']}', '{$roww['id']}',  '".str_replace("\r\n","",$roww['txt'])."', {$send_gold} , '{$roww['kr']}' , '{$gold_lim}', '{$err}' );</SCRIPT>";
				if ($err==0) { $del_trade=false; }
				}
		}
		else
		{
	  	    $rwx = mysql_fetch_array(mysql_query("SELECT `id` FROM oldbk.`inventory` WHERE `bs_owner` ='".$user['in_tower']."' AND `owner` = '".$roww['to_id']."' AND `tradesale` > 0 AND `id` = '".$roww['id']."' AND prototype!=2123456804 AND prototype!=40000001 and type!=99 AND setsale=0 LIMIT 1;"));
			if (!$roww['id'] OR !$rwx['id'])
			{
			//проверим рефреш?
			if ($_REQUEST['refresh'] )
			   {
			   echo "Ожидаем ответа... <a href=give.php?refresh=".$_REQUEST['refresh'].">Обновить</a>";
			   }
			   else
			   {
			    ?> <SCRIPT>findlogin('Передача предметов','give.php','FindLogin');</SCRIPT><?
			   }
			}
			else {
//echo "start";
			$tmp = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.inventory WHERE id = '.$roww['id']));


			
			if ($tmp['ekr_flag']>0)	{
				$roww['prototype'] = $tmp['ekr_flag']; // подсказка о том что Внимание! После покупки этот предмет нельзя будет передать или продать! 
			} else {
				$roww['prototype'] = $tmp['prototype'];
			}

			?> <SCRIPT>transfer(<?=$roww['to_id']?>, '<?=$roww['login']?>', '<?=str_replace("\r\n","",$roww['txt'])?>', <?=$roww['kr']?>, <?=$roww['id']?>, '',<?=$roww['prototype']?>);</SCRIPT><?
	            $del_trade = false;

			}
		}
if ($del_trade)		
	{
	mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} LIMIT 1;");
	}
}
?>

</td><TD align=right>
	<INPUT TYPE=button value="Подсказка" style="background-color:#A9AFC0" onClick="window.open('help/transfer.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
	<form action=main.php><INPUT TYPE=submit value="Вернуться"></form>
</td></tr><tr><td colspan=2 align=right><? if ($step!=4) {?> <FONT COLOR=red><B><? echo $mess; ?></B></FONT> <? } ?></td></tr></table>

<TABLE width=100% cellspacing=0 cellpadding=0>
<FORM ACTION="?" METHOD=POST NAME="KR">
<TR>
	<TD valign=top align=left width=40%>
<?
	if ($step==3) { ?>
	<INPUT TYPE=hidden name=to_id value="<? echo $idkomu; ?>">
	<INPUT TYPE=hidden name=sd4 value="<? echo $user['id']; ?>">
	<BR>У вас на счету: <FONT COLOR=339900><B><? echo $user['money']; ?></B></FONT> кр.<BR>
	Передать кредиты, минимально 0.01кр.<BR>
	Укажите передаваемую сумму: <INPUT TYPE=text NAME=setkredit maxlength=8 size=6>
	<br>
	Детали платежа: <br><INPUT TYPE=text NAME=settext maxlength=70 size=30><br> &nbsp <INPUT TYPE=submit VALUE="Передать">
	</FORM>	
	<BR>
	<?
	if  ($user['level']>=10)
	 {
	?>
	<FORM ACTION="?" METHOD=POST NAME="GOLD">	
	<INPUT TYPE=hidden name=to_id value="<? echo $idkomu; ?>">
	<INPUT TYPE=hidden name=sd4 value="<? echo $user['id']; ?>">
	<BR>У вас на счету: <FONT COLOR=339900><B><? echo $user['gold']; ?></B></FONT><img src="http://i.oldbk.com/i/icon/coin_icon.png" alt="Монеты" title="Монеты" style="margin-bottom: -2px;"><BR>
	Продать монеты: <INPUT TYPE=text NAME=setgold maxlength=8 size=6> <INPUT TYPE=submit VALUE="Продать">
	<br>
	Курс продажи: 1 монета = <?=$GOLD_GIVE_KURS;?> кр.	
	<?
	 }
	
	
	}
?>
	</TD>
</FORM>

<FORM ACTION="give.php" METHOD=POST>
<INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>">
<TD valign=top align=right>

<?
if ($step==3) {


	if (@$_GET['razdel'] == '0') { $_SESSION['razdel'] = 0; }
	if (@$_GET['razdel'] == 1) { $_SESSION['razdel'] = 1; }
	if (@$_GET['razdel'] == 2) { $_SESSION['razdel'] = 2; }

?>
<TABLE border=0 width=100% cellspacing="0" cellpadding="0" bgcolor="#A5A5A5">
<TR><TD>
	<TABLE border=0 width=100% cellspacing="0" cellpadding="3" bgcolor=#d4d2d2><TR>
	<TD  align=center bgcolor="<?=($_SESSION['razdel']==null)?"#A5A5A5":"#C7C7C7"?>"><A HREF="?to_id=<? echo $idkomu; ?>&edit=1&razdel=0&sd4=<? echo $user['id']; ?>">Обмундирование</A></TD>
	<TD  align=center bgcolor="<?=($_SESSION['razdel']==1)?"#A5A5A5":"#C7C7C7"?>"><A HREF="?to_id=<? echo $idkomu; ?>&edit=1&razdel=1&sd4=<? echo $user['id']; ?>">Заклятия</A></TD>
	<TD  align=center bgcolor="<?=($_SESSION['razdel']==2)?"#A5A5A5":"#C7C7C7"?>"><A HREF="?to_id=<? echo $idkomu; ?>&edit=1&razdel=2&sd4=<? echo $user['id']; ?>">Прочее</A></TD>
	</TR></TABLE>
</TD></TR>
<TR>
	<TD align=center><B>Рюкзак (масса: <?php

	$d = getmymassa($user);

	echo $d[0];
	?>/<?=get_meshok()?>)</B></TD>
</TR>
<TR><TD align=center><!--Рюкзак-->
<TABLE BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">
<?php


	if ($_SESSION['razdel']==null) {
		$where = 'AND (`type` < 12 OR `type`=27 OR `type`=28 OR `type`=34 OR `type`=35 )';
	}
	if ($_SESSION['razdel']==1) {
		$where = 'AND (`type` = 12)';
	}
	if ($_SESSION['razdel']==2) {
		$where = 'AND (`type` > 12 AND `type`!=27 AND `type`!=28 AND `type`!=34 AND `type`!=35)';
	}



	if ($user['klan']=='radminion' || $user['klan']=='Adminion' || $user['id'] == 8325) {
		$addsql = "";
	} else {
		$addsql = " and sowner=0 ";
	}

    $data = mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}'
		AND `dressed` = 0 ".$where." AND `setsale` = 0 ".$addsql." AND prototype!=40000001 AND prototype!=2123456804 and type!=99 and otdel!=72 and type!=77  AND `present` = '' ".$VAUCHER." AND `bs_owner` ='".$user['in_tower']."' ORDER by `update` DESC; ");

	$inv_shmot = array();
	$inv_gr_key = array();
	while($row = mysql_fetch_array($data)) {
		if (in_array($row['prototype'],$vauch_a) && $row['sowner'] > 0 && $idkomu != 8540 && $idkomu !=182783 && $idkomu !=457757 && $idkomu !=8325) continue;
		if (($row['ekr_flag']>0) and ($row['ecost']<=0)) continue;
		if (in_array($row['prototype'],$vauch_a)) $row['group'] = 0;


		if($row[present]!=''){
		   $prez=1;
		}else
		{
			$prez=0;
		}
		$inv_shmot[$prez][$row[duration]][$row[prototype]][]=$row;
  		$inv_gr_key[$row[prototype]]=$row[group];
	}

 foreach ($inv_shmot as $key2 => $value2)
	{
	 foreach ($value2 as $key => $value)
		{
		     foreach ($value as $key1 => $value1)
			{
				    if($inv_gr_key[$key1]==1)
				    {
		                	$group_key=1;
				    }
					else
					{
		                $group_key=count($value1);
					}
					for($i=0;$i<$group_key;$i++)
					{
						if ($ff==0) { $ff = 1; $color = '#C7C7C7';} else { $ff = 0; $color = '#D5D5D5'; }
						if($inv_gr_key[$key1]==1)
					    	{$value1[$i][GetShopCount()] =  count($value1);}
						else
						{ 
							$value1[$i][GetShopCount()] = 1;
						}

                				if($value1[$i]['add_pick']!=''&&$value1[$i]['pick_time']>time())
						{
					       		$value1[$i]['img']=$value1[$i]['add_pick'];
						}
						//$sh_id=get_item_fid($value1[$i]);
						echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$value1[$i]['img']}\" BORDER=0><BR>";
						$sh_id = get_item_fid($value1[$i]);
						echo "<center><small>(".$sh_id.")</small></center><br>";
						

						//echo "<center><small>(".$sh_id.")</small></center><br>";
						$money_out=($ttype==1?10:1);
						$money_out=($ttype==2?100:1);

						
						
						if ((($value1[$i]['ekr_flag']==0) and ($value1[$i]['otdel']!=72)) OR ($user['klan']=='radminion') OR ($user['id']==8325) )  // купленые в березе с этим флагом передавать / дарить нельзя , уники запрещаем передавать
						{
						if (!(in_array($value1[$i]['prototype'],$vauch_a) && $idkomu != 102904 && $idkomu != 8540 && $idkomu !=182783 && $idkomu !=457757 && $idkomu != 8325 )) 
						{
							echo "<A HREF=\"give.php?to_id=".$idkomu."&id_th=".$value1[$i]['id']."&setobject=".$value1[$i]['id']."
							&s4i=".$user['sid']."&sd4=".$user['id']."&tmp=".$value1[$i]['duration']."&gift=0\"".'
							onclick="return confirm(\'Передать предмет '.$value1[$i]['name'].'?\')">передать&nbsp;за&nbsp;'.$money_out.'&nbsp;кр.</A>';
						}

						if($value1[$i]['group']==1)
						{

							if (!(in_array($value1[$i]['prototype'],$vauch_a) && $idkomu != 102904 && $idkomu != 8540 && $idkomu !=182783 && $idkomu !=457757 && $idkomu !=8325)) 
							{
				        	?>
				        	<IMG SRC="http://i.oldbk.com/i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Передать несколько штук" style="cursor: pointer"
				        	onclick="AddCount('<?=$value1[$i][prototype]?>', '<?=$value1[$i][name]?>','1','give.php?to_id=<?=$idkomu?>&id_th=<?=$value1[$i]['id']?>&setobject=<?=$value1[$i]['id']?>&s4i=<?=$user['sid']?>&sd4=<?=$user['id']?>&tmp=<?=$value1[$i]['duration']?>&gift=0')">
				        	<?
							}
						}
						}
					// !=1 to disable in tower
						if($idkomu == 83 || $idkomu == 136 || $idkomubot == 84)
						{
							$ttype=0;
						}
					
					if ($value1[$i]['ekr_flag']==0 || ADMIN) {
						if ( (($value1[$i]['art_param'] !='') or ($value1[$i]['ab_mf'] >0 )  or ($value1[$i]['ab_bron'] >0 )  or ($value1[$i]['ab_uron'] >0 ))     and ($value1[$i]['sowner'] !=0)) {
							if (ADMIN) {
								echo "<br><A HREF=\"give.php?to_id=".$idkomu."&id_th=".$value1[$i]['id']."&setobject=".$value1[$i]['id']."
								&s4i=".$user['sid']."&sd4=".$user['id']."&tmp=".$value1[$i]['duration']."&gift=1\"".'
								onclick="return confirm(\'Подарить предмет '.$value1[$i]['name'].'?\')">подарить</A>';
							}
						} elseif(ADMIN || ($user['in_tower'] == 0 || $user['in_tower']==2 || ($user['in_tower']==1 && $ttype!=1 ) || ($user['in_tower']==15 && $ttype != 2))) {
							if (ADMIN || (!(in_array($value1[$i]['prototype'],$vauch_a) && $idkomu != 102904 && $idkomu != 8540 && $idkomu !=182783 && $idkomu !=457757 && $idkomu !=8325))) {
								echo "<br><A HREF=\"give.php?to_id=".$idkomu."&id_th=".$value1[$i]['id']."&setobject=".$value1[$i]['id']."
								&s4i=".$user['sid']."&sd4=".$user['id']."&tmp=".$value1[$i]['duration']."&gift=1\"".'
								onclick="return confirm(\'Подарить предмет '.$value1[$i]['name'].'?\')">подарить</A>';
							        if($value1[$i]['group']==1)
								{
						        	?>
						        	<IMG SRC="http://i.oldbk.com/i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Подарить несколько штук" style="cursor: pointer"
						        	onclick="AddCount('<?=$value1[$i][prototype]?>', '<?=$value1[$i][name]?>','2','give.php?to_id=<?=$idkomu?>&id_th=<?=$value1[$i]['id']?>&setobject=<?=$value1[$i]['id']?>&s4i=<?=$user['sid']?>&sd4=<?=$user['id']?>&tmp=<?=$value1[$i]['duration']?>&gift=1')">
						        	<?
								}
							}

						}
					}
						
						if($user['in_tower'] == 0)
						{
							if ($value1[$i]['sowner']!=0)
							{
							//не протаюутся sowner!=0
							}						
							elseif ($value1[$i]['otdel']==72 || $value1[$i]['labonly'] > 0)
							{
							//не протаюутся уники и вещи из лабы
							} 
							else
							if ( (($value1[$i]['art_param'] !='') or ($value1[$i]['ab_mf'] >0 )  or ($value1[$i]['ab_bron'] >0 )  or ($value1[$i]['ab_uron'] >0 ))     and ($value1[$i]['sowner'] !=0)) {
							
							//echo "не продается"; 
							
							}
							elseif ( ($value1[$i]['ekr_flag']  == 1)) {
								if ($value1[$i]['ecost']>0) {
									if ($value1[$i]['prototype'] == 55510350 || $value1[$i]['prototype'] == 55510352 || $value1[$i]['prototype'] == 55510351 || $value1[$i]['prototype'] == 410021 || $value1[$i]['prototype'] == 410022 || $value1[$i]['prototype'] == 410026 || $value1[$i]['prototype'] == 410027 || $value1[$i]['prototype'] == 410028) {
										if (ceil(($value1[$i]['dategoden'] - time())/(60*60*24)) >= 7) {
											echo "<br><A HREF=#".' onClick="findmoney3(\'Продажа предмета\',\'give.php\',\'cost\','.$value1[$i]['id'].','.($value1[$i]['ecost']*EKR_TO_KR).')">продать<br>(комиссия 1 кр.)</A>';
										}
									} else {
										$tcost = $value1[$i]['ecost']*EKR_TO_KR;
										if ($value1[$i]['type'] == 50) {
											$tcost = $tcost - (($tcost / $value1[$i]['maxdur']) * $value1[$i]['duration']);
										}

										echo "<br><A HREF=#".' onClick="findmoney3(\'Продажа предмета\',\'give.php\',\'cost\','.$value1[$i]['id'].','.$tcost.')">продать<br>(комиссия 1 кр.)</A>';
									}
								}
							} elseif ( ($value1[$i]['ekr_flag']  == 2)) {
								if ($value1[$i]['ecost']>0) {
									$tcost = $value1[$i]['ecost']*EKR_TO_KR;
									if ($value1[$i]['type'] == 50) {
										$tcost = $tcost - (($tcost / $value1[$i]['maxdur']) * $value1[$i]['duration']);
									}

									echo "<br><A HREF=#".' onClick="findmoney4(\'Продажа предмета\',\'give.php\',\'cost\','.$value1[$i]['id'].','.($tcost).')">продать<br>(комиссия 1 кр.)</A>';
								}
							} elseif ( ($value1[$i]['ekr_flag']  == 3)) {
								if ($value1[$i]['ecost']>0) {
									$tcost = $value1[$i]['ecost']*EKR_TO_KR;
									if ($value1[$i]['type'] == 50) {
										$tcost = $tcost - (($tcost / $value1[$i]['maxdur']) * $value1[$i]['duration']);
									}

									echo "<br><A HREF=#".' onClick="findmoney5(\'Продажа предмета\',\'give.php\',\'cost\','.$value1[$i]['id'].','.($tcost).')">продать<br>(комиссия 1 кр.)</A>';
								}
							} elseif (in_array($value1[$i]['prototype'],$vauch_a)) {
								echo "<br><A HREF=#".' onClick="findmoney2(\'Продажа предмета\',\'give.php\',\'cost\','.$value1[$i]['id'].','.$value1[$i]['prototype'].')">продать<br>(комиссия 1 кр.)</A>';
							} else {
								echo "<br><A HREF=#".' onClick="findmoney(\'Продажа предмета\',\'give.php\',\'cost\','.$value1[$i]['id'].')">продать<br>(комиссия 1 кр.)</A>';
							}
						}

					   // if (($user[id]==14897) OR ($user[id]==188) )
					      {
							if (time()>$begin1 && time()<$end1) {
								// закрыто
							} 
							else 
							{
								/*
								if($user['room'] == 23)
								{
									if ( ( ($value1[$i]['type'] < 12) OR ($value1[$i]['type'] ==28)   ) AND ($value1[$i]['type'] != 3) AND ($value1[$i]['bs_owner'] == 0) AND ($value1[$i]['owner'] == $_SESSION['uid'])
										AND ($value1[$i]['dressed'] == 0) AND ( strpos($value1[$i]['name'],'(мф)')===FALSE) AND ( strpos($value1[$i]['name'],'Букет')===FALSE)
										AND ($value1[$i]['setsale'] == 0) AND ($value1[$i]['prokat_idp'] == 0)	AND ($value1[$i]['arsenal_klan'] =='') and ( $value1[$i]['present']!='Арендная лавка' ) and ( $value1[$i]['prototype']!=260 ) and ( $value1[$i]['prototype']!=262 )
										AND (($value1[$i]['gsila'] > 0) OR ($value1[$i]['glovk'] > 0) OR ($value1[$i]['ginta'] > 0) OR ($value1[$i]['gintel'] > 0)
										           OR ($value1[$i]['mfkrit']> 0) OR ($value1[$i]['mfakrit'] > 0) OR ($value1[$i]['mfuvorot'] > 0) OR ($value1[$i]['mfauvorot'] > 0)
										           OR ($value1[$i]['bron1'] > 0) OR ($value1[$i]['bron2'] > 0) OR ($value1[$i]['bron3'] > 0) OR ($value1[$i]['bron4'] > 0) OR ($value1[$i]['ghp'] > 0) )  )
										           {
											echo "<br><br><A HREF=\"give.php?to_id=".$idkomu."&id_th=".$value1[$i]['id']."&mfobject=".$value1[$i]['id']."
									&s4i=".$user['sid']."&sd4=".$user['id']."&tmp=".$value1[$i]['duration']."&mfzalog=1\"".'
									onclick="return confirm(\'Отдать на модификацию предмет '.$value1[$i]['name'].'?\')">Отдать на модификацию <br>(комиссия 1 кр.)</A>';
											   }
	
								
								}
								*/
							}
					     }

						echo "</TD><TD valign=top>";
						//var_dump($value1[$i]);die();
						showitem($value1[$i]);
						echo "</TD></TR>";

					}
			}
	    }
    }


	if (mysql_num_rows($data) == 0) {
		echo "<tr><td align=center bgcolor=#C7C7C7>Пусто</td></tr>";
	}
?>



</TABLE>
</TD></TR>
</TABLE><?php
 }
?>


</TD></TR>
</FORM>
</TABLE>

</BODY>
</HTML>
