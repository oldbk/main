<?php

//компресия для инфы
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

if (isset($_GET['key']) && $_GET['key'] == "246y426514256135y4315y1") {
	include "connect.php";
	// новое радио
	if (isset($_GET['radio'])) {
		$radio = intval($_GET['radio']);
		if ($radio == 1 || $radio == 2) {
			$q = mysql_query('START TRANSACTION') or die();
			$q = mysql_query('SELECT * FROM r_djse WHERE id_radio = '.$radio.' FOR UPDATE') or die();
			$r = mysql_fetch_assoc($q) or die();

			// забираем себе эфир и пишем в хистори
			if (isset($_GET['user'])) {
				mysql_query('UPDATE r_djse SET id_dj = '.intval($_GET['user']).', starttime = '.time().', efir_type = 1 WHERE id_radio = '.intval($radio)) or die();

				try {
					if(time() - $r['starttime'] > 600) {
						$User = \components\models\User::find(intval($_GET['user']))->toArray();
						if(!$User) {
							throw new Exception();
						}
						$_message = sprintf('В прямом эфире для вас работает RDJ %s. Включайте радио и будьте на волнах OldFM вместе с нами!', $User['login']);
						\components\models\Chat::addToAllSystem($_message, $User['id_city']);
					}

				} catch (Exception $ex) {

				}
			} else {
				mysql_query('UPDATE r_djse SET id_dj = 0, starttime = '.time().', efir_type = 0 WHERE id_radio = '.intval($radio)) or die();
			}

			/*
            if ($r['id_dj'] > 0) {
                mysql_query('
                    INSERT INTO r_djsh (id_radio,id_dj,starttime,endtime,efir_type)
                    VALUES('.$radio.','.$r['id_dj'].','.$r['starttime'].','.time().',1)
                ') or die();
            }*/

			$q = mysql_query('COMMIT') or die();
		}
	}
	ob_clean();
	header('Content-Encoding: '.$miniBB_gzipper_encoding);
	echo $miniBB_gzipper_out;
	die();
}

session_start();
$city_name[0]='CapitalCity';
$city_name[1]='AvalonCity';
$city_name[2]='AngelsCity';
if (!($_SESSION['uid'] >0)) {
	header("Location: index.php"); die();
}
include "connect.php";
include "functions.php";
require_once("config_ko.php");
if ($user[klan]=='radminion') {  echo "Admin-info:<!- GZipper_Stats -> <br>"; }
if (!$user['login']) {
	header("Location: index.php");
	die();
}

$rrr=array(1,2,3,4,5,10,11);
if (!(in_array($_GET['pals'],$rrr)))
{
	$_GET['pals']=1;
}


$lk=(int)$_GET['pals'];
$active_link[$lk]=' class="active" ';
?>
	<!DOCTYPE html>
	<html>
	<head lang="ru">
		<meta content='text/html; charset=windows-1251' http-equiv=Content-type>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title></title>
		<link rel="stylesheet" href="newstyle20.css" type="text/css">
		<META Http-Equiv=Cache-Control Content=no-cache>
		<meta http-equiv=PRAGMA content=NO-CACHE>
		<META Http-Equiv=Expires Content=0>
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
		<script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>
		<script type="text/javascript" src="/i/globaljs.js"></script>
		<script type="text/javascript" src="/i/bank9.js"></script>	
		<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/jscal2.js"></script>
		<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>
		<script src="i/jquery.drag.js" type="text/javascript"></script>
	<script>
		function getformdata(id,param,event)
		{
			if (window.event)
			{
				event = window.event;
			}
			if (event )
			{
				$.get('payform.php?id='+id+'&param='+param+'', function(data) {
					$('#pl').html(data);
					$('#pl').show(200, function() {
					});
				});
			}

		}

		function closeinfo()
		{
			$('#pl').hide(200);
		}
	</script>

		<SCRIPT LANGUAGE='JavaScript'>
			var Hint3Name = 'FindLogin';

		</SCRIPT>
	</head>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 >
<div id="pl" style="z-index: 300; position: absolute; left: 50%; top: 120px;
				width: 750px; height:365px; background-color: #eeeeee;
				margin-left: -375px;
				border: 1px solid black; display: none;"></div>
<div id="page-wrapper">
	<div class="btn-control">
		<div class="button-mid btn" onclick="location.href='friends.php?pals=<?echo $_GET['pals']?>&refresh=<?echo mt_rand(1111,9999);?>';" >Обновить</div>
		<div class="button-mid btn" onclick="location.href='main.php';" >Вернуться</div>
	</div>
<?
echo "<h3>".$city_name[$user[id_city]]."</h3>";
?>
	<table id="frendlist" align="center" class="table-list" cellspacing="0" cellpadding="0">
	<colgroup>
		<col width="330px">
		<col width="auto">
		<col width="330px">
	</colgroup>
	<thead>
	<tr class="head-line">
		<th colspan="3" class="center">
			<div class="head-left"></div>
			<a href="?pals=1" <?=$active_link[1]; ?>>список друзей</a>
			<a href="?pals=11" <?=$active_link[11]; ?>>игнор лист</a>
			<a href="?pals=4" <?=$active_link[4]; ?>>лекари</a>
			<a href="?pals=2" <?=$active_link[2]; ?>>паладины</a>
			<a href="?pals=3" <?=$active_link[3]; ?>>дилеры</a>
			<a href="?pals=10" <?=$active_link[10]; ?>>RDJ в эфире</a>
			<a href="?pals=5" <?=$active_link[4]; ?>>помощники</a>			
			<div class="head-right"></div>
		</th>
	</tr>
	</thead>
	<tbody>
<?


$show_advises=explode(',',$user['show_advises']);

//готовим префикс базы для выбора других
if ($user[id_city]==0) { $db_other_city='avalon.';  $id_other_city=1; }
else if ($user[id_city]==1) { $db_other_city='oldbk.' ; $id_other_city=0; }
else { $db_other_city='' ; $id_other_city=0;}

// обработчик
if ( (($_GET['pals']==1)||($_GET['pals']==11))  AND ($_REQUEST['FindLogin']!="") AND ($_REQUEST['update']!="") )
{
	//обнова друзей
	if ($_GET['pals']==11)
	{
		$t=2;
	}
	else
	{
		$t=0;
	}
	$us=mysql_fetch_row(mysql_query("SELECT id FROM oldbk.users WHERE login='".mysql_real_escape_string($_REQUEST['FindLogin'])."'  AND bot=0 "));
	if ((int)$us[0]==0) $message_text= "Ошибка при обновлении. Персонаж с таким ником в городе не найден";
	elseif ((int)$us[0]==$_SESSION['uid']) $message_text="Ошибка при обновлении. Дружить с самим собой? ;)<br>";
	elseif ((int)$us[0]==8540) $message_text="Но-но! ;)<br>";
	else
	{
		mysql_query("UPDATE oldbk.friends SET comment ='".mysql_escape_string($_POST['commentusr'])."' where  owner='".(int)$_SESSION['uid']."' and type='".$t."' and friend='".$us[0]."' ");
		if(mysql_affected_rows()>0)
		{
			$message_text="Информация обновлена!<br>";
		}
		else
		{
			$message_text= "<font color=#A42323><b>Ошибка при обновлении!</b></font><br><br>";
		}
	}
}
elseif ( (($_GET['pals']==2)||($_GET['pals']==3)||($_GET['pals']==4)||($_GET['pals']==5))  AND ($_REQUEST['FindLogin']!="") AND ($_REQUEST['update']!="") )
{
	//обнова 
	if ($_GET['pals']==2)
	{
		$t=3;
		$sql_filt=" and klan='pal' ";
		$er[3]='Персонаж с таким ником среди паладинов не найден';
	}
	elseif ($_GET['pals']==3)
	{
		$t=5;
		$sql_filt=" and deal>0 ";
		$er[5]='Персонаж с таким ником среди дилеров не найден';
	}
	elseif ($_GET['pals']==4)
	{
		$t=4;
		$sql_filt=" and id in (select owner from effects where type=40000) ";
		$er[4]='Персонаж с таким ником среди лекарей не найден';
	}
	elseif ($_GET['pals']==5)
	{
		$t=6;
		$sql_filt=" and deal=-1 ";
		$er[4]='Персонаж с таким ником среди помощников не найден';
	}	

	$us=mysql_fetch_row(mysql_query("SELECT id FROM oldbk.users WHERE login='".mysql_real_escape_string($_REQUEST['FindLogin'])."'  AND bot=0 ".$sql_filt));
	if ((int)$us[0]==0) $message_text= "Ошибка при обновлении.".$er[$t];
	elseif ((int)$us[0]==$_SESSION['uid']) $message_text="Ошибка при обновлении. Дружить с самим собой? ;)<br>";
	elseif ((int)$us[0]==8540) $message_text="Но-но! ;)<br>";
	else
	{
		mysql_query("INSERT INTO oldbk.friends (type, owner, friend, status, comment) VALUES('".$t."',".(int)$_SESSION['uid'].",".$us[0].",0,'".mysql_escape_string($_POST['commentusr'])."') on DUPLICATE KEY UPDATE  comment ='".mysql_escape_string($_POST['commentusr'])."' ");
		if(mysql_affected_rows()>0)
		{
			$message_text="Информация обновлена!<br>";
		}
		else
		{
			$message_text= "<font color=#A42323><b>Ошибка при обновлении!</b></font><br><br>";
		}
	}
}
else
	if ($_REQUEST['FindLogin']!="") {

		if (isset($_REQUEST['addenemy'])) {
			$us=mysql_fetch_row(mysql_query("SELECT id FROM oldbk.users WHERE login='".mysql_real_escape_string($_REQUEST['FindLogin'])."' AND bot=0 "));
			if ((int)$us[0]==0) $message_text="Ошибка при добавлении. Персонаж с таким ником в городе не найден";
			elseif ((int)$us[0]==$_SESSION['uid']) $message_text="Ошибка при добавлении. Игнор сам себе? ;)<br>";
			elseif ((int)$us[0]==8540) $message_text="Но-но! ;)<br>";
			else {
				if (!mysql_query("INSERT INTO oldbk.friends (type, owner, friend, status, comment) VALUES(2,".(int)$_SESSION['uid'].",".$us[0].",0,'".mysql_escape_string($_POST['commentusr'])."')")) $message_text="<font color=#A42323><b>Ошибка при добавлении. Возможно этот игрок уже у Вас в друзьях или игнор листе?</b></font><br><br>";
				else $message_text="Персонаж добавлен в список игнора.<br>";
			}
		} else {

			$us=mysql_fetch_row(mysql_query("SELECT id FROM oldbk.users WHERE login='".mysql_real_escape_string($_REQUEST['FindLogin'])."'  AND bot=0 "));
			if ((int)$us[0]==0) $message_text= "Ошибка при добавлении. Персонаж с таким ником в городе не найден";
			elseif ((int)$us[0]==$_SESSION['uid']) $message_text="Ошибка при добавлении. Дружить с самим собой? ;)<br>";
			elseif ((int)$us[0]==8540) $message_text="Но-но! ;)<br>";
			elseif ((int)$us[0]==3) $message_text="Но-но! ;)<br>";
			elseif ((int)$us[0]==4) $message_text="Но-но! ;)<br>";			
			else {
				if (!mysql_query("INSERT INTO oldbk.friends (type, owner, friend, status, comment)VALUES(0,".(int)$_SESSION['uid'].",".$us[0].",0,'".mysql_escape_string($_POST['commentusr'])."')")) $message_text= "<font color=#A42323><b>Ошибка при добавлении. Возможно этот игрок уже у Вас в друзьях или игнор листе?</b></font><br><br>";
				else $message_text="Персонаж добавлен в список друзей.<br>";
			}
		}

	}
	else
		if (((int)$_GET['delusr']>0)and($_GET['pals']==1))
		{
			if (!preg_match('/^(http:\/\/)(capitalcity\.|avaloncity\.|top\.|admin\.)?(oldbk.com)((\/friends.php(.)*)|(\b))/i',trim($_SERVER['HTTP_REFERER']))>0)
			{
				ob_clean();
				header('Content-Encoding: '.$miniBB_gzipper_encoding);
				echo $miniBB_gzipper_out;
				die("Ошибка... Возможно попытка взлома..</body></html>");
			}
			else { mysql_query("DELETE FROM oldbk.friends WHERE friend=".(int)$_GET['delusr']." AND type=0 AND owner=".$_SESSION['uid']); $message_text="Персонаж удалён из списка друзей.<br>"; }

		}
		else
			if (((int)$_GET['delusr']>0)and($_GET['pals']==11))  {
				mysql_query("DELETE FROM oldbk.friends WHERE friend = ".(int)$_GET['delusr']." AND type=2 AND owner=".$_SESSION['uid']);
				$message_text= "Персонаж удалён из списка игнора.<br>";
				//$message_text.="<meta http-equiv='refresh' content='1;url=/friends.php?pals=11'>";
			}
			elseif (((int)$_GET['editusr']>0)and(($_GET['pals']==1)||($_GET['pals']==11)||($_GET['pals']==4)||($_GET['pals']==5)||($_GET['pals']==2)||($_GET['pals']==3)) )
			{

				if ($_GET['pals']==11)
				{
					$t=2;
					$edit_btn=2;
				}
				elseif ($_GET['pals']==2)
				{
					$t=3;
					$edit_btn=3;
				}
				elseif ($_GET['pals']==4)
				{
					$t=4;
					$edit_btn=4;
				}
				elseif ($_GET['pals']==3)
				{
					$t=5;
					$edit_btn=5;
				}
				elseif ($_GET['pals']==5)
				{
					$t=5;
					$edit_btn=6;
				}				
				else
				{
					$t=0;
					$edit_btn=1;
				}

				$get_telo=mysql_fetch_array(mysql_query("select u.id, u.login, (select comment from oldbk.friends f where f.friend=u.id and f.type='".$t."' AND f.owner='".$_SESSION['uid']."' ) as comment  from users u where u.id=".(int)$_GET['editusr'] ));
				$edit_login=$get_telo['login'];
				$edit_coment=$get_telo['comment'];

			}



if (!isset($show_advises[0])) $show_advises[0] = 0;
if (!isset($show_advises[1])) $show_advises[1] = 0;
if (!isset($show_advises[2])) $show_advises[2] = 0;
if (!isset($show_advises[3])) $show_advises[3] = 0;
if (!isset($show_advises[4])) $show_advises[4] = 0;
if (!isset($show_advises[5])) $show_advises[5] = 0;

if(isset($_POST['chng'])) {
	if($_POST[sh_hellow]) {
		$show_advises[1]=1;
	} else {
		$show_advises[1]=0;
	}

	$show_advises_b=implode(',',$show_advises);

	mysql_query('update users set show_advises="'.$show_advises_b.'" WHERE id ='.$user[id].';');
}

//==========
$nw=time()-61;

if (!$_GET['pals'])
{
	$_GET['pals']="1";
}
$friends = array();
$friends_names = array();

if ($_GET['pals']=="1") {


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Дузья  в этом городе
	$data=mysql_query("SELECT u.*, f.owner, f.comment, f.friend, b.blood, b.type as btype,
						(select `type` from `effects` where owner = u.id AND type in (11,12,13,14) limit 1) as etype
						FROM oldbk.`friends` f, `users` u
                        left join `battle` b
						on u.battle= b.id
						WHERE u.id=f.friend AND f.owner={$user[id]} AND  f.type=0 and id_city={$user[id_city]} order by login asc ;");
	// echo mysql_error();
	$to_print_online='';
	$to_print_offline='';
	while ($row = mysql_fetch_array($data))
	{
		if($row['id']==102) // фикс на исчадие
		{
			$data2=mysql_query('SELECT u.*, b.blood, b.type as btype
																	FROM `users_clons` u
											                        left join `battle` b
																	on u.battle= b.id
																	WHERE u.id_user=102 limit 1 ;');
			if(mysql_affected_rows()>0)
			{
				while($isch=mysql_fetch_array($data2))
				{
					$row['battle']=$isch['battle'];
					$row['btype']=$isch['btype'];
				}
			}

		}


		// собираем вывод тех кто в онлайне + не видимка
		if ( ($row['hidden']==0) and ($row['odate']>=(time()-60)) )
		{
			$to_print_online.=render_item_row($row,1);
		}
		else  {

			$to_print_offline.=render_item_row($row,0);
		}
	}
	//выводим сортированые стринги
	if (($to_print_online!='') or ($to_print_offline!=''))
	{
		echo $to_print_online;
		echo $to_print_offline;
	}
	else
	{
		$on1=1;
	}

	if($on1==1)
	{
		echo render_item_message("Список друзей пуст :(<br>");
	}

	if ($edit_btn==1)
	{
		$print_form_start='<form method=post name=fml id=fml action="friends.php?pals=1">';
		$print_form='
		<tr>
                    <td class="form-left"></td>
                    <td class="form-input">
                    	<input type="hidden" name="update"  value="yes" >
                        <input type="text" name="FindLogin"  value="'.$edit_login.'" >
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <input type="text" name=commentusr value="'.$edit_coment.'">
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <div class="submit button-big btn"  onClick="document.fml.submit();" >Сохранить</div>
                    </td>
                    <td class="form-right"></td>
                </tr>            
	';
	}
	else
	{
		$print_form_start='<form method=post name=fml action="friends.php?pals=1">';
		$print_form='
		<tr>
                    <td class="form-left"></td>
                    <td class="form-input">
                        <input type="text" name="FindLogin" placeholder="Введите никнейм игрока">
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <input type="text" name=commentusr placeholder="Заметка об игроке">
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <div class="submit button-big btn"  onClick="document.fml.submit();" >Добавить в список</div>
                    </td>
                    <td class="form-right"></td>
                </tr>            
	';
	}
	$print_form2='<div align=left><form  method=post name=fmf action="friends.php">Оповещать о появлении друзей <input name=chng type="hidden" value=1><input name=sh_hellow type=checkbox value=on '.($show_advises[1]==1?'checked':'').' > <div class="button-mid btn" onClick="document.fmf.submit();">Изменить</div></form></div>';



}


elseif ($_GET['pals'] == "11") {
	// игнор в этом городе

	$sql="SELECT u.*, f.owner, f.comment, f.friend, b.blood, b.type as btype,
										(select `type` from `effects` where owner = u.id AND type in (11,12,13,14) limit 1) as etype
										FROM oldbk.`friends` f, `users` u
				                        left join `battle` b
										on u.battle= b.id
										WHERE u.id=f.friend AND f.owner={$user[id]} AND  f.type=2 and id_city={$user[id_city]} order by login asc ;";
	$data=mysql_query($sql);
	$to_print_online= '';
	$to_print_offline= '';
	while ($row = mysql_fetch_array($data))
	{

		// собираем вывод тех кто в онлайне + не видимка
		if ( ($row['hidden']==0) and ($row['odate']>=(time()-60)) )
		{
			$to_print_online.=render_item_row($row,1);
		}
		else  {

			$to_print_offline.=render_item_row($row,0);
		}
	}


	// выводим сортированые стринги
	if (($to_print_online!='') or ($to_print_offline!='')) {
		echo $to_print_online;
		echo $to_print_offline;
	} else {
		$on1=1;
	}

	if($on1==1)
	{
		echo render_item_message("Список игнорирования пуст<br>");
	}

	if ($edit_btn==2)
	{
		$print_form_start='<form method=post name=fml action="friends.php?pals=11">';
		$print_form='
		<tr>
                    <td class="form-left"></td>
                    <td class="form-input">
                    	<input type="hidden" name="update"  value="yes" >
                        <input type="text" name="FindLogin"  value="'.$edit_login.'" >
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <input type="text" name=commentusr value="'.$edit_coment.'">
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <div class="submit button-big btn"  onClick="document.fml.submit();" >Сохранить</div>
                    </td>
                    <td class="form-right"></td>
		
                </tr>            
	';
	}
	else
	{
		$print_form_start='<form method=post name=fml action="friends.php?pals=11">';
		$print_form='
		<tr>
                    <td class="form-left"></td>
                    <td class="form-input">
                        <input type="text" name="FindLogin" placeholder="Введите никнейм игрока">
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
	                <input type=hidden name="addenemy"> 
                        <input type="text" name=commentusr placeholder="Заметка об игроке">
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <div class="submit button-big btn"  onClick="document.fml.submit();" >Добавить в список</div>
                    </td>
                    <td class="form-right"></td>
                </tr>            
	';
	}


}
elseif ($_GET['pals']=="2")
{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Паладины  в этом городе
	$data=mysql_query("SELECT u.*, b.blood, b.type as btype, (select comment from  oldbk.`friends`  where owner={$user[id]} AND  type=3 and friend=u.id ) as comment,
						(select `type` from `effects` where owner = u.id AND type in (11,12,13,14) limit 1) as etype
						FROM  `users` u
						left join `battle` b
						on u.battle= b.id
						WHERE u.align>1 AND u.align<2 AND u.align!='1.2' AND u.odate>".(int)$nw." and u.id_city={$user[id_city]} ORDER BY u.align asc ;");

	$to_print_online='';
	while ($row = mysql_fetch_array($data))
	{

		// собираем вывод тех кто в онлайне + не видимка
		if ( ($row['hidden']==0) and ($row['odate']>=(time()-60)) )
		{
			$to_print_online.=render_item_row($row,1);
		}
	}
	//выводим сортированые стринги
	if ($to_print_online!='')
	{
		echo $to_print_online;
	}
	else
	{
		$on1=1;
		echo render_item_message("В городе нет ни одного паладина онлайн.");
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($edit_btn==3)
	{
		$print_form_start='<form method=post name=fml action="friends.php?pals=2">';
		$print_form='
		<tr>
                    <td class="form-left"></td>
                    <td class="form-input">
                    	<input type="hidden" name="update"  value="yes" >
                        <input type="text" name="FindLogin"  value="'.$edit_login.'" >
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <input type="text" name=commentusr value="'.$edit_coment.'">
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <div class="submit button-big btn"  onClick="document.fml.submit();" >Сохранить</div>
                    </td>
                    <td class="form-right"></td>
                </tr>            
	';
	}
}
elseif ($_GET['pals']=="5")
{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//поимшники
	$data=mysql_query("SELECT u.*, b.blood, b.type as btype, (select comment from  oldbk.`friends`  where owner={$user[id]} AND  type=6 and friend=u.id ) as comment,
						(select `type` from `effects` where owner = u.id AND type in (11,12,13,14) limit 1) as etype
						FROM  `users` u
						left join `battle` b
						on u.battle= b.id
						WHERE  deal=-1  AND u.odate>".(int)$nw." and u.id_city={$user[id_city]} ORDER BY u.align asc ;");

	$to_print_online='';
	while ($row = mysql_fetch_array($data))
	{

		// собираем вывод тех кто в онлайне + не видимка
		if ( ($row['hidden']==0) and ($row['odate']>=(time()-60)) )
		{
			$to_print_online.=render_item_row($row,1);
		}
	}
	//выводим сортированые стринги
	if ($to_print_online!='')
	{
		echo $to_print_online;
	}
	else
	{
		$on1=1;
		echo render_item_message("В городе нет ни одного помощника онлайн.");
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($edit_btn==6)
	{
		$print_form_start='<form method=post name=fml action="friends.php?pals=5">';
		$print_form='
		<tr>
                    <td class="form-left"></td>
                    <td class="form-input">
                    	<input type="hidden" name="update"  value="yes" >
                        <input type="text" name="FindLogin"  value="'.$edit_login.'" >
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <input type="text" name=commentusr value="'.$edit_coment.'">
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <div class="submit button-big btn"  onClick="document.fml.submit();" >Сохранить</div>
                    </td>
                    <td class="form-right"></td>
                </tr>            
	';
	}
}
elseif ($_GET['pals']=="4")
{


	echo '
                <tr class="item-row">
                    <td class="row-left"><div class="separate"></div></td>
                    <td class="row-center" align="center" style="height:40px;vertical-align:middle;">';

        	$price = 100;

		$effects = mysql_query('select * from effects WHERE type in (11,12,13) AND owner = '.$user[id]) or MyDieS();
		$ctravma = mysql_num_rows($effects);


	        if(isset($_POST['znahar']) && $user['money'] >= $price && $ctravma && $user['battle'] == 0) {
			while ($owntravma = mysql_fetch_array($effects)) {
				deltravma($owntravma['id']);
			}

            		mysql_query('UPDATE users SET money = money-'.$price.' WHERE id = '.$user['id'].' LIMIT 1;');

			$rec['owner'] = $user['id'];
			$rec['owner_login'] = $user['login'];
			$rec['owner_balans_do'] = $user['money'];
			$user['money'] -= $price;
			$rec['owner_balans_posle'] = $user['money'];
			$rec['target'] = 0;
			$rec['target_login'] = 'Самостоятельное лечение';
			$rec['type'] = 36;
			$rec['sum_kr'] = $price;
			add_to_new_delo($rec); //юзеру
            		err('Травма вылечена');
        	} elseif(isset($_POST['znahar']) && $user['money'] < $price && $ctravma) {
        		err('У вас не достаточно денег...');
		} elseif($user['battle'] > 0) {
        		err('Не в бою');
		} elseif(isset($_POST['znahar']) && !$ctravma) {
			err('У Вас нет травм.');
		}

	        echo '<form name="" action="?pals=4" method="post"><input name="znahar" type="hidden" value="1"><input type="submit" style="height:20px;" class="button-big btn" value="Вылечить травму за '.$price.' кр."></form><div class="separate"></div>';
		echo '</td>
                    <td class="row-right"><div class="separate"></div></td>
		</tr>
	';


	//лекари  в этом городе
	$data=mysql_query("SELECT u.*, b.blood, b.type as btype, (select comment from  oldbk.`friends`  where owner={$user[id]} AND  type=4 and friend=u.id ) as comment,
						(select `type` from `effects` where owner = u.id AND type in (11,12,13,14) limit 1) as etype
						FROM `users` u
						left join `battle` b
						on u.battle= b.id
						WHERE  u.id in (select owner from effects where type=40000) and  u.odate>".(int)$nw." and u.id_city={$user[id_city]} ORDER BY  login asc ;");
	$to_print_online='';
	while ($row = mysql_fetch_array($data))
	{
		// собираем вывод тех кто в онлайне + не видимка
		if ( ($row['hidden']==0) and ($row['odate']>=(time()-60)) )
		{
			$to_print_online.=render_item_row($row,1);
		}
	}
	//выводим сортированые стринги
	if ($to_print_online!='')
	{
		echo $to_print_online;
	}
	else
	{
		$on1=1;
		echo render_item_message("В городе нет ни одного лекаря онлайн. ");
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($edit_btn==4)
	{
		$print_form_start='<form method=post name=fml action="friends.php?pals=4">';
		$print_form='
		<tr>
                    <td class="form-left"></td>
                    <td class="form-input">
                    	<input type="hidden" name="update"  value="yes" >
                        <input type="text" name="FindLogin"  value="'.$edit_login.'" >
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <input type="text" name=commentusr value="'.$edit_coment.'">
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <div class="submit button-big btn"  onClick="document.fml.submit();" >Сохранить</div>
                    </td>
                    <td class="form-right"></td>

                </tr>            
	';
	}
}
elseif ($_GET['pals']=="10") {
	//echo '<br><br><br>';
	$radio_access = 0;
	$djs = array();

	$r1_access = 0;
	$r2_access = 0;


	$q = mysql_query('SELECT * FROM `r_djsn`');
	while($d = mysql_fetch_assoc($q))
	{
		$djs[$d['id_dj']] = $d;
		if ($d['id_dj'] == $user['id'] && $d['top_dj'] > 0) $radio_access = 1;

		// доступы на радио
		if ($d['id_dj'] == $user['id'] && $d['r1_access'] > 0) $r1_access = 1;
		if ($d['id_dj'] == $user['id'] && $d['r2_access'] > 0) $r2_access = 1;
	}


	if ($user['id']==14897 || $user['id']==102904 || $user['id'] == 684792 || $user['id'] == 546433)
	{
		$r1_access = 1;
		$r2_access = 1;
		$radio_access = 1;
	}

	if (count($djs) && $radio_access > 0) {
		$q = mysql_query('SELECT * FROM oldbk.users WHERE id IN ('.implode(",",array_keys($djs)).')');
		while($u = mysql_fetch_assoc($q)) {
			$djs[$u['id']]['info'] = $u;
		}
	}

	if ($radio_access > 0 && isset($_POST['djname']) && !empty($_POST['djname'])) {
		if (isset($_POST['rusfm']) || isset($_POST['oldfm'])) {
			$q = mysql_query('SELECT * FROM oldbk.users WHERE id = '.intval($_POST['djname']));
			$q2 = mysql_query('SELECT * FROM r_djsn WHERE id_dj = '.intval($_POST['djname']));
			if (mysql_num_rows($q) > 0 && mysql_num_rows($q2) == 0) {
				$r1 = isset($_POST['rusfm']) ? 1 : 0;
				$r2 = isset($_POST['oldfm']) ? 1 : 0;
				$q = mysql_query('
					insert into oldbk.r_djsn (id_dj,r1_access,r2_access,icq,skype)
					VALUES ('.intval($_POST[djname]).','.$r1.','.$r2.',"'.(int)$_POST[icq].'","'.$_POST[skype].'")
				');

				$message_text='DJ удачно добавлен';
			} else {
				$message_text='ID неверный или DJ уже добавлен.';
			}
		} else {
			$message_text='Выберите хотябы одно радио';
		}
	}

	if ($radio_access > 0 && isset($_GET['del_dj']) && !empty($_GET['del_dj'])) {
		mysql_query('delete from oldbk.r_djsn WHERE id_dj='.intval($_GET[del_dj]));
		if (mysql_affected_rows() > 0) {
			$message_text='DJ удачно удалён';
			$dd=intval($_GET[del_dj]);
			unset($djs[$dd]);
		} else {
			$message_text='DJ для удаления не найден';
		}
	}


	$radioname=array();
	$radioname[1]=array('OldFM','Лучшая музыка','http://blog.oldbk.com/radio/oldfm.m3u');
	$radioname[2]=array('OldFM','Лучшая музыка','http://blog.oldbk.com/radio/oldfm.m3u');

	$q = mysql_query('SELECT * FROM r_djse');
	if (mysql_num_rows($q) > 0) {
		$djsid = array();
		while($r = mysql_fetch_assoc($q)) {
			$radioname[$r['id_radio']]['info'] = $r;
			$djsid[$r['id_dj']] = 1;
		}

		$q = mysql_query('SELECT * FROM oldbk.users WHERE id IN ('.implode(",",array_keys($djsid)).')');
		while($u = mysql_fetch_assoc($q)) {
			reset($radioname);
			while(list($k,$v) = each($radioname)) {
				if ($v['info']['id_dj'] == $u['id']) {
					$u['hidden'] = 0;
					$u['hiddenlog'] = "";
					$radioname[$k]['infodj'] = $u;
				}
			}
		}
	}

	unset($radioname[1]);
	reset($radioname);
	while(list($k,$v) = each($radioname))
	{

		if (isset($v['info']))
		{
			$access = "r".$k."_access";
			echo  '
					                <tr class="item-row">
					                    <td class="row-left">';
			if ($v['info']['efir_type'] == 0)
			{
				$pl=". Играет плейлист";
			}
			else
			{
				echo "<i class=\"icon private\"><a href=\"#\" OnClick=\"top.AddToPrivate('".$v['infodj']['login']."', top.CtrlPress); return false;\"><img src='http://i.oldbk.com/i/lock.gif' width='20' height='15'></a></i>" . nick_align_klan($v['infodj'])." ";
				$pl=($v[icq]>0?" &nbsp;&nbsp;<b>icq: </b>".$v[icq]:""). " " .($v[skype]!=""?" &nbsp;&nbsp;<b>skype: </b>".$v[skype]:"")." ".($v['info']['efir_type'] == 0 ? "(плейлист)" : "(живой эфир)");
			}
			echo '<div class="separate"></div>
					                    </td>
					                    <td class="row-center">
					                        <em>';
			echo '<font color=#003388><b>Радио '.$v[0].'</b></font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$v[2].'" style="font-size: 12px; font-weight: bold;"><u>Прослушать в winamp</u></a><br>'.$v[1];
			echo $pl ;
			echo  '</em>
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-right">
					                        <div class="row-location"></div>
					                        <div class="separate"></div>
					                    </td>
					                </tr>';
		}
	}

	if ($radio_access > 0)
	{
		echo  '
					                <tr class="item-row">
					                    <td class="row-left">
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-center" align=center>
					                        <em><b>RusFM:</b></em>
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-right">
					                        <div class="row-location"></div>
					                        <div class="separate"></div>
					                    </td>
					                </tr>';

		reset($djs);
		foreach($djs as $k=>$value) {
			if($value[id_dj] != $user[id] && $value['r1_access'] > 0)
			{
				echo  '
					                <tr class="item-row">
					                    <td class="row-left">
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-center">
					                        <em>';
				echo nick_align_klan($djs[$k]['info'])." <a href=?pals=10&del_dj=".$value[id_dj]."><img src=http://capitalcity.oldbk.com/i/clear.gif></a>";
				echo '</em>
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-right">
					                        <div class="row-location"></div>
					                        <div class="separate"></div>
					                    </td>
					                </tr>';
			}
		}

		echo  '
					                <tr class="item-row">
					                    <td class="row-left">
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-center" align=center>
					                        <em><b>OldFM:</b></em>
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-right">
					                        <div class="row-location"></div>
					                        <div class="separate"></div>
					                    </td>
					                </tr>';
		reset($djs);
		echo '</td><td>';
		foreach($djs as $k=>$value) {
			if($value[id_dj] != $user[id] && $value['r2_access'] > 0)
			{
				echo  '
					                <tr class="item-row">
					                    <td class="row-left">
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-center">
					                  <em>';
				echo nick_align_klan($djs[$k]['info'])." <a href=?pals=10&del_dj=".$value[id_dj]."><img src=http://capitalcity.oldbk.com/i/clear.gif></a>";
				echo '</em>
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-right">
					                        <div class="row-location"></div>
					                        <div class="separate"></div>
					                    </td>
					                </tr>';
			}
		}


		$print_form2='
			Добавить DJ 
			<form name="adf" action="?pals=10" method="post"><br>
			<center>
			<table>
                  	<tr><td>ID</td><td><input name="djname" type="text" value=""></td></tr>
                  	<tr><td>ICQ</td><td><input name="icq" type="text" value=""></td></tr>
                  	<tr><td>Skype</td><td><input name="skype" type="text" value=""></td></tr>
                  	<tr><td>RusFM</td><td><input style="margin:0px;" name="rusfm" type="checkbox" value=""></td></tr>
                  	<tr><td>OldFM</td><td><input style="margin:0px;" name="oldfm" type="checkbox" value=""></td></tr>
                    	<tr><td colspan=2>
                    	<div class="button-mid btn" onClick="document.adf.submit();">Добавить</div></td></tr></table>
		      </form></td></tr></table></center>';

	}

}
elseif ($_GET['pals']=="3")
{

	//Дилеры онлайн  в этом городе

	$data=mysql_query(
			"SELECT u.*, b.blood, b.type as btype, (select comment from  oldbk.`friends`  where owner={$user[id]} AND  type=5 and friend=u.id ) as comment,
							(select `type` from `effects` where owner = u.id AND type in (11,12,13,14) limit 1) as etype
							FROM `users` u
							left join `battle` b
							on u.battle= b.id
						WHERE u.deal>0 AND u.hidden=0 AND u.odate>".(int)$nw."  AND u.id_city={$user[id_city]} ORDER BY u.login asc ;");

	$to_print_online1='';
	$to_print_online2='';
	while ($row = mysql_fetch_array($data))
	{
		if (!(strpos($row['login'], 'auto') !== false) )
		{
			// собираем вывод тех кто 1
			if  ($row['deal']==1)
			{
				$to_print_online1.=render_item_row($row,1,false,true);
			}
			else
			{
				$to_print_online2.=render_item_row($row,1,false,true);
			}
		}
	}
	//выводим сортированые стринги
	if (($to_print_online1!='') OR  ($to_print_online2!=''))
	{
		echo  '
					                <tr class="item-row">
					                    <td class="row-left">
					                        <div class="separate"></div>
					                    </td>
				
					                    <td class="row-center">';
								
								echo print_bank_buttons();			

					                    	
		echo '			                        
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-right">
					                        <div class="row-location"></div>
					                        <div class="separate"></div>
					                    </td>
					                </tr>';
		echo $to_print_online1;
		echo  '
					                <tr class="item-row">
					                    <td class="row-left">
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-center">
					                       <br>
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-right">
					                        <div class="row-location"></div>
					                        <div class="separate"></div>
					                    </td>
					                </tr>';
		echo $to_print_online2;
	}
	else
	{
		$on1=1;
		echo render_item_message(" В городе пока нет ни одного дилера онлайн! Вы можете воспользоваться автоматической системой покупки валюты.",print_bank_buttons());
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($edit_btn==5)
	{
		$print_form_start='<form method=post name=fml action="friends.php?pals=3">';
		$print_form='
		<tr>
                    <td class="form-left"></td>
                    <td class="form-input">
                    	<input type="hidden" name="update"  value="yes" >
                        <input type="text" name="FindLogin"  value="'.$edit_login.'" >
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <input type="text" name=commentusr value="'.$edit_coment.'">
                    </td>
                    <td class="form-separate"></td>
                    <td class="form-input">
                        <div class="submit button-big btn"  onClick="document.fml.submit();" >Сохранить</div>
                    </td>
                    <td class="form-right"></td>

                </tr>            
	';
	}

}

echo '<tfoot>
                <tr class="footer-frendlist">
                    <td class="" style="position: relative">
                        <div class="footer-left"></div>
                    </td>
                    <td class="" style="position: relative">
                        <div class="footer-center"></div>
                    </td>                    
                    <td class="" style="position: relative">
                        <div class="footer-right"></div>
                    </td>
                </tr>
            </tfoot>
 </table>';

?>

<?

if ($print_form_start)  echo $print_form_start." <input type=submit style='display:none' > " ;

echo '<table align="center" class="table-form" cellspacing="0" cellpadding="0">
            <colgroup>
                <col width="10px">
                <col width="305px">
                <col width="12px">
                <col>                
                <col width="12px">
                <col width="180xp">
                <col width="10px">
            </colgroup>
            <tbody>
              ';
echo $print_form;
echo '</tbody>
        </table>';
if ($print_form_start) echo '</form>';

if ($print_form2)
{
	echo '
        <div class="block-hint">
        '.$print_form2.'
        </div>';
}


if ($message_text)
{
	echo '
        <div class="block-hint">
        '.$message_text.'
        </div>';
}
echo ' </div>  ';

echo "</body></html>";

function print_bank_buttons()
{
global $user;
$out='';

if (($user['klan']=='radminion') OR ($user['klan']=='testTest') )//пока выключенно
				{
				if (isset($_SESSION['bankid']) && $_SESSION['bankid'] > 0) 
				{
					$out.= '<center><a onclick="getformdata(99,0,event);" href="#"><img src="http://i.oldbk.com/i/bank/knopka_ekr.gif"  alt="Купить еврокредиты через Банк" alt="Купить еврокредиты через Банк"></a> ';
					$out.= '<a onclick="getformdata(9,300,event);" href="#"><img src=http://i.oldbk.com/i/bank/knopka_repa.gif title="Купить репутацию" alt="Купить репутацию" ></a> ';
					
					if ((time()>$KO_start_time28) and (time()<$KO_fin_time28)) 	
					{
					$out.= "<a href=\"#\" onClick=\"getformdata(88,0,event);\"><img src=http://i.oldbk.com/i/knopka_coin3.gif title='Купить монеты' ></a>";
					}
					else
					{
					$out.= "<a href=\"#\" onClick=\"getformdata(87,0,event);\"><img src=http://i.oldbk.com/i/knopka_coin3.gif title='Купить монеты' ></a></center>";
					}					
					
					
				} else {
					$out.= '<center><a href="bank.php"><img src=http://i.oldbk.com/i/bank/knopka_ekr.gif title="Купить еврокредиты через Банк" alt="Купить еврокредиты через Банк" ></a>';
					$out.= ' <a href="bank.php"><img src=http://i.oldbk.com/i/bank/knopka_repa.gif title="Купить репутацию" alt="Купить репутацию" ></a>';
					$out.= "<a href=\"bank.php\"><img src=http://i.oldbk.com/i/knopka_coin3.gif title='Купить монеты' ></a></center>";					
					
				}
				//$out.='<hr>';
				}
return $out;				
}


function render_item_message($text,$bu='')
{
	return   '
					                <tr class="item-row">
					                    <td class="row-left">
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-center">'.$bu.'
					                        <em><b>'.$text.'</b></em>
					                        <div class="separate"></div>
					                    </td>
					                    <td class="row-right">
					                        <div class="row-location"></div>
					                        <div class="separate"></div>
					                    </td>
					                </tr>';
}

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