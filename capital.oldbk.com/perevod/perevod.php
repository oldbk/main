<?php
if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
	$miniBB_gzipper_encoding = 'x-gzip';
}
if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
	$miniBB_gzipper_encoding = 'gzip';
}
if (isset($miniBB_gzipper_encoding)) {
	ob_start();
}

ini_set('log_errors',1);
ini_set('error_log',"/www/other/perevoderrors");
error_reporting(0);

function getApiRequest($url) {
	$opts = array(
		'http'=> array (
			'method' => "GET",
			'header' =>
				"Accept-language: en\r\n" .
				"Cookie: ".session_name()."=".$_REQUEST[session_name()]."\r\n",
		)
	);

	$context = stream_context_create($opts);
	return  @file_get_contents($url,false,$context);
}


if ($_SERVER['SERVER_NAME'] == "archive.oldbk.com") {
	ini_set('session.cookie_domain',".oldbk.com");
	if (isset($_REQUEST[session_name()])) {
		session_start();
		if (!isset($_SESSION['__user']) || !isset($_SESSION['__palrights'])) {
			$data = getApiRequest("http://capitalcity.oldbk.com/getperevoduid.php?key=q5tyv28tui245ti4ju5thn5tn4k5tj");
			if ($data !== false) {
				$tmp = unserialize($data);
				$_SESSION['__user'] = $tmp['__user'];
				$_SESSION['__palrights'] = $tmp[0]['__palrights'];

				$user = $_SESSION['__user'];
				$pal_rights = $_SESSION['__palrights'];
				if (isset($user['id'])) $_SESSION['uid'] = $user['id'];
			}
		} else {
			$user = $_SESSION['__user'];
			if (isset($user['id'])) $_SESSION['uid'] = $user['id'];
			$pal_rights = $_SESSION['__palrights'];
		}
	} else {
		die();
	}
} else {
	session_start();
}

ini_set('display_errors','On');
ini_set('max_execution_time','600');

$table_delo['0']='new_delo';
$table_delo['1']='new_delo_old';
$table_delo['2']='new_delo_old2';

$table_delo_tx['0']='new_delo_it_index';
$table_delo_tx['1']='new_delo_it_index_old';
$table_delo_tx['2']='new_delo_it_index_old2';

if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

if ($_SERVER['SERVER_NAME'] == "capitalcity.oldbk.com") {
	require_once "../connect.php";
	require_once "../functions.php";
} else {
	@mysql_connect("88.198.205.122","archive","2459rtidfh8qauisdfcuiqargedf");
	mysql_select_db('archive');
	mysql_query ("SET NAMES 'CP1251'");
	require_once "new_delo.php";

	define("EKR_TO_KR",100); // курс екра к креду глобальный

	if($user['klan']=='Adminion' || $user['klan']=='radminion') {
		define("ADMIN",true);
	} else {
		define("ADMIN",false);
	}

	function check_rights($user,$pal_rights) {
		$access=array();
		if (ADMIN) {
			$access['i_angel']=1;
		} else {
			$access['i_angel']=0;
		}
		if(($user['align']>1 && $user['align'] <2) || $access['i_angel']>0 || $user['align']==7 || $user['align']==5 || $user['id']==697032 ) {
			//призепить палрайтс.
			$access['i_pal']			=$user['align'];
			$access['can_forum_del']		=(($user['align']>='1.5'&&$user['align']<2) || $user['align']==7 ||$access['i_angel']>0)?1:0;	//удаление постов (скрытие)
			$access['can_forum_restore']	=(($user['align']>='1.91'&&$user['align']<2)|| $user['align']==7 ||$access['i_angel']>0)?1:0;
			$access['can_close_top']		=(($user['align']>='1.5'&&$user['align']<2) || $user['align']==7 ||$access['i_angel']>0)?1:0;
			$access['can_open_top']		=(($user['align']>='1.5'&&$user['align']<2) || $user['align']==7 ||$access['i_angel']>0)?1:0;
			$access['can_del_top']		=(($user['align']>='1.5'&&$user['align']<2) || $user['align']==7||$access['i_angel']>0)?1:0;     	//удаление топиков(скрытие)
			$access['can_del_top_all']	=(($user['align']>='1.91'&&$user['align']<2)||$access['i_angel']>0)?1:0;
			$access['can_rest_top_all']	=(($user['align']>='1.91'&&$user['align']<2)||$access['i_angel']>0)?1:0;
			$access['can_del_pal_comments']	=(($user['align']>='1.91'&&$user['align']<2)||$access['i_angel']>0)?1:0;
			$access['can_create_votes']	=(($user['align']>='1.91'&&$user['align']<2)||$access['i_angel']>0)?1:0;

			$access['view_ekr']		=($access['i_angel']>0)?1:0;  //видеть екры в переводах
			$access['can_comment']		=(($pal_rights['red_forum']==1)||$access['i_angel']>0)?1:0;		//Коментарий к посту
			$access['can_top_move']		=(($pal_rights['top_move']==1)||$access['i_angel']>0)?1:0;
			$access['perevodi']		=(($pal_rights['logs']==1)||$access['i_angel']>0)?5:0; //простые переводы + анализатор
			$access['item_hist']		=(($pal_rights['ext_logs']==1)||$access['i_angel']>0)?1:0; //открывает еще историю вещей
			$access['pal_tel']		=(($pal_rights['pal_tel']==1)||$access['i_angel']>0)?1:0;	//пал телеграф
			$access['zhhistory']		=(($pal_rights['zhhistory']==1)||$access['i_angel']>0)?1:0;	//пал жалобы


			$access['klans_kazna_view']	=(($pal_rights['klans_kazna_view']==1)||$access['i_angel']>0)?1:0; //просмотр казны кланов
			$access['klans_kazna_logs']	=(($pal_rights['klans_kazna_logs']==1)||$access['i_angel']>0)?1:0; //просмотр логов казны кланов
			$access['klans_ars_logs']		=(($pal_rights['klans_ars_logs']==1)||$access['i_angel']>0)?1:0; //просмотр логов арсеналов кланов

			$access['klans_ars_put']		=(($pal_rights['klans_ars_put']==1)||$access['i_angel']>0)?1:0; //изымать вещь из арсенала (привязанную к арсу) и также возможность привязывать вещь к арсеналу.

			$access['pals_delo']		=(($pal_rights['pals_delo']==1)||$access['i_angel']>0)?1:0; //просмотр пал дела
			$access['pals_online']		=(($pal_rights['pals_online']==1)||$access['i_angel']>0)?1:0; //просмотр палов онлайн

			$access['anonim_hist']		=(($user['align']>='1.91'&&$user['align']<2)||$access['i_angel']>0)?1:0; //смена анонима на ник
			$access['abils']			= $pal_rights['abils'];
			$access['loginip']		= $pal_rights['loginip'];
			$access['viewmanyips']		= $pal_rights['viewmanyips'];
		}

		if ($user['id']==648) {
			$access[view_ekr]=1;
		}
		return $access;
	}

	function upgrade_item($up_cost,$max_ups_left) {
      		$costs['up_cost']=$up_cost;
  		if($max_ups_left == 5) {
            		$costs['mfbonusadd']=2;
			$costs['cur_cost']= $costs['up_cost']+round($costs['up_cost'] / 2, 0);
			$costs['up_cost'] = round($costs['cur_cost'] / 2, 0);
			$costs['cost_add'] = round($costs['cur_cost'] * 0.2, 0);//cost wich will pluseed to item cost after mf
		} elseif($max_ups_left == 4) {
			$costs['mfbonusadd']=3;
			$costs['cur_cost']= $costs['up_cost']+round($costs['up_cost'] / 2, 0);
			$costs['cur_cost']=$costs['cur_cost']+round($costs['cur_cost']*0.2,0); //cost after previos UP
			$costs['up_cost'] = round($costs['cur_cost'] / 2, 0);
			$costs['cost_add'] = round($costs['cur_cost'] * 0.2, 0);//cost wich will pluseed to item cost after UP
		} elseif($max_ups_left == 3) {
			$costs['mfbonusadd'] = 4;
			$costs['cur_cost']= $costs['up_cost']+round($costs['up_cost'] / 2, 0);
			$costs['cur_cost']=$costs['cur_cost']+round($costs['cur_cost']*0.2,0); //cost after 1 UP
			$costs['cur_cost']=$costs['cur_cost']+round($costs['cur_cost']*0.2,0); //cost after 2 UP
			$costs['up_cost'] = round($costs['cur_cost'] / 2, 0);
			$costs['cost_add'] = round($costs['cur_cost'] * 0.4, 0);//cost wich will pluseed to item cost after UP
		} elseif($max_ups_left == 2) {
			$costs['mfbonusadd']=6;
			$costs['cur_cost']= $costs['up_cost']+round($costs['up_cost'] / 2, 0);
			$costs['cur_cost']=$costs['cur_cost']+round($costs['cur_cost']*0.2,0); //cost after 1 UP
			$costs['cur_cost']=$costs['cur_cost']+round($costs['cur_cost']*0.2,0); //cost after 2 UP
		    	$costs['cur_cost']=$costs['cur_cost']+round($costs['cur_cost']*0.4,0); //cost after 3 UP
			$costs['up_cost'] = round($costs['cur_cost'] / 2, 0);
			$costs['cost_add'] = round($costs['cur_cost'] * 0.7, 0);//cost wich will pluseed to item cost after UP
		} elseif($max_ups_left == 1) {
			$costs['mfbonusadd']=10;
			$costs['cur_cost']= $up_cost+round($up_cost / 2, 0);
			$costs['cur_cost']=$costs['cur_cost']+round($costs['cur_cost']*0.2,0); //cost after 1 UP
			$costs['cur_cost']=$costs['cur_cost']+round($costs['cur_cost']*0.2,0); //cost after 2 UP
		    	$costs['cur_cost']=$costs['cur_cost']+round($costs['cur_cost']*0.4,0); //cost after 3 UP
			$costs['cur_cost']=$costs['cur_cost']+round($costs['cur_cost']*0.7,0); //cost after 4 UP
			$costs['up_cost'] = round($costs['cur_cost'] / 2, 0);
			$costs['cost_add'] = round($costs['cur_cost'] * 0.1, 0);//cost wich will pluseed to item cost after UP
		}
		$costs['up_cost']=round($costs['up_cost']*0.7);
		return $costs;
	 }

}

header("Cache-Control: no-cache");

if(($user['align'] > 1 && $user['align'] < 2) || ($user['align'] > 2 && $user['align'] < 3)) {
	if ($_SERVER['SERVER_NAME'] == "capitalcity.oldbk.com") {
		$access = check_rights($user);
	} else {
		$access = check_rights($user,$pal_rights);
	}
}


if(!$access) {
	die('Страница не найдена...');
}  elseif($access['perevodi'] < 5) {
	die('Страница не найдена...');
}



?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://capitalcity.oldbk.com/i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content="no-cache, max-age=0, must-revalidate, no-store">
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="http://capitalcity.oldbk.com/i/globaljs.js"></script>
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/steel/steel.css" />
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/jscal2.js"></script>
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>
<style>
	.row {
		cursor:pointer;
	}
</style>
<script>
function showhide(id) {
	if (document.getElementById(id).style.display=="none")	{
		document.getElementById(id).style.display="block";
	} else {
		document.getElementById(id).style.display="none";
	}
}

function blank(f){
	if(f.newwin.checked){
 		f.target ='_blank';
 	} else {
 		f.target ='_self';
 	}
}

function blank_kazna(f) {
 	if(f.newwinkazna.checked) {
 		f.target ='_blank';
 	} else {
 		f.target ='_self';
 	}
}

function blank_ars(f) {
	if(f.newwinars.checked) {
 		f.target ='_blank';
 	} else {
 		f.target ='_self';
 	}
}
</SCRIPT>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 >
<table align=right><tr><td><INPUT TYPE="button" onClick="location.href='main.php';" value="Вернуться" title="Вернуться"></table>
<div align="center" id="hint3"></div>
<?php

function res_detecting($string) {
	$res_array=array('Гранит','Горсть Песка','Глина','Воск','Руда','Камень Алтаря','Кусок Настенного Рисунка','Стенной Камень','Золото','Изумруд','Серебро','Алмаз','Уголь','Уголёк','Рубины','Рубин','Портальный минерал','Портальный кристалл','Мел','Горсть Соли','Опал','Горный хрусталь','Бирюза','Гранат','Булыжник','Камень Лабиринта','Янтарь','Малахит','Cапфир','Жемчуг','Лазурит светлый','Лазурит темный','Аквамарин','Аквамарины','Солнечный камень','Лунный камень','Речной камень','Морской камень','Океанический камень','Яшма','Золотая нить','Слюда','Мрамор','Антрацит','Александрит','Веревка');
	foreach ($res_array as $k=>$v) {
		if (strpos($string,$v) !== false) {
			return true;
		}
	}
	return false;
}

if(!isset($_GET['sh'])) {	$_GET['sh'] = 2;
} elseif(isset($_POST['sh']) && $_POST['sh'] > 0) {	$_GET['sh'] = $_POST['sh'];}

if (!isset($_GET['new_delo_login'])) $_GET['new_delo_login'] = "";
if (!isset($_GET['new_delo_login2'])) $_GET['new_delo_login2'] = "";
if (!isset($_GET['olddelo'])) $_GET['olddelo'] = "";
if (!isset($_GET['olddelo2'])) $_GET['olddelo2'] = "";
if (!isset($_GET['ekr_reit'])) $_GET['ekr_reit'] = EKR_TO_KR;



if ($access['perevodi'] >= 5 && $_GET['sh'] == 2) {
	echo '<br>';
	if($_GET['sh'] == 2) {
		if (isset($_GET['new_delo_log'])) {
			if (isset($_GET['new_delo_date'])) {
				$new_delo_date_all=explode(".",$_GET['new_delo_date']);
				$new_delo_date = sprintf("%02d.%02d.%04d", (int)($new_delo_date_all[0]), (int)($new_delo_date_all[1]), (int)($new_delo_date_all[2]));
			} else {
				$log_date = date("d.m.Y");
			}

			if (isset($_GET['new_delo_fdate'])) {
				$new_delo_fdate_all=explode(".",$_GET['new_delo_fdate']);
				$new_delo_fdate = sprintf("%02d.%02d.%04d", (int)($new_delo_fdate_all[0]), (int)($new_delo_fdate_all[1]), (int)($new_delo_fdate_all[2]));
			} else {
				$new_delo_fdate = date("d.m.Y");
			}
		} else {
			$new_delo_date = "08.12.2016";
			$new_delo_fdate = date("d.m.Y");
		}

		$new_delo_login=htmlspecialchars($_GET['new_delo_login']);
		$new_delo_login2=htmlspecialchars($_GET['new_delo_login2']);

		if(is_numeric($new_delo_login) && isset($_GET['numericlogin'])) {
			$telo=mysql_fetch_array(mysql_query("select * from users where id='".(int)$new_delo_login."';"));
		} else {
			$telo=mysql_fetch_array(mysql_query("select * from users where login='".mysql_real_escape_string($new_delo_login)."';"));
		}

		if(strlen($new_delo_login2)>3) {
			if(is_numeric($new_delo_login2)) {
				$telo2=mysql_fetch_array(mysql_query("select * from users where
				id='".(int)$new_delo_login2."';"));
			} else {
				$telo2=mysql_fetch_array(mysql_query("select * from users where
				login='".mysql_real_escape_string($new_delo_login2)."';"));
			}
		}

		if (!isset($telo2)) $telo2['login'] = "";
		?>
		<hr>
		<h4>История переводов c 08/12/2016</h4><br><br><br>
		<form method="get" action='http://capitalcity.oldbk.com/perevod/perevod.php'><input name='sh' type='hidden' value='2'>

		Логин <input type=text name=new_delo_login value='<?=htmlspecialchars($telo['login'],ENT_QUOTES)?>'> <br>
		Логин2<input type=text name=new_delo_login2 value='<?=htmlspecialchars($telo2['login'],ENT_QUOTES)?>'> *можно оставить пустым<br>
		<?php
		if ($_GET['olddelo'] != 'true' && $_GET['olddelo2'] != 'true') {
			?>
			c: <input type=text name='new_delo_date' value='<?=$new_delo_date?>' id="delocalendar-inputField3" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;'>
			<?php
		} else {
			?>
			c: <input type=text name='new_delo_date' value='08.12.2016' id="delocalendar-inputField3" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' >
			<?php
		}
		?>

		<input type=button id="delocalendar-trigger3" value='...'>
		<script>
			Calendar.setup({
			trigger    : "delocalendar-trigger3",
			inputField : "delocalendar-inputField3",
			dateFormat : "%d.%m.%Y",
			onSelect   : function() { this.hide() }
			});
			document.getElementById('delocalendar-trigger3').setAttribute("type","BUTTON");
		</script>
		<?php
		if ($_GET['olddelo'] != 'true' && $_GET['olddelo2'] != 'true') {
			?> по: <input type=text name='new_delo_fdate' value='<?=$new_delo_fdate?>' id="delocalendar-inputField4" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;'> <?php
		} else {
			$new_delo_fdate2 = date("d.m.Y");
			?> по: <input type=text name='new_delo_fdate' value='<?=$new_delo_fdate2?>' id="delocalendar-inputField4" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;'> <?php
		}
		?>
		<input type=button id="delocalendar-trigger4" value='...'>
		<script>
			Calendar.setup({
			trigger    : "delocalendar-trigger4",
			inputField : "delocalendar-inputField4",
			dateFormat : "%d.%m.%Y",
			onSelect   : function() { this.hide() }
		});
		document.getElementById('delocalendar-trigger4').setAttribute("type","BUTTON");
		</script>
		<?php

		if ($_GET['olddelo'] != 'true' && $_GET['olddelo2'] != 'true') {
			?> <br> Курс екра: <input type='text' value='<?=($_GET['ekr_reit']>0?$_GET['ekr_reit']:EKR_TO_KR)?>' name='ekr_reit'><br> <?php
		} else {
			?> <br> Курс екра: <input type='text' value='<?=EKR_TO_KR;?>' name='ekr_reit'><br> <?php
		}

		?>
		Фильтры: <br>
		Без ресурсов <input name='res_off' type='checkbox' value='1' <?=(isset($_GET['res_off']) && $_GET['res_off'] == 1 ? 'checked' : '') ?>> <br>
		Без цветов/открыток/сувениров  <input name='gifts_off' type='checkbox' value='1' <?=(isset($_GET['gifts_off']) && $_GET['gifts_off'] == 1 ? 'checked' : '')?>><br>
		Без выброса/разрушения  <input name='destr_off' type='checkbox' value='1' <?=(isset($_GET['destr_off']) && $_GET['destr_off'] == 1 ? 'checked' : '')?>><br>
		Для публикации на форум <input name='add_info_off' type='checkbox' value='1' <?=(isset($_GET['add_info_off']) && $_GET['add_info_off'] == 1 ? 'checked' : '')?>><br>

		<input type=submit name=new_delo_log value='Просмотр'>&nbsp; анализ:<input name=analiz type=checkbox value=1 <?=((isset($_GET['analiz']) && $_GET['analiz'] == 1) || !isset($_GET['new_delo_log']) ? "checked" : "")?>> анализ по госу: <input name=analizgos type=checkbox value=1 <?=((isset($_GET['analizgos']) && $_GET['analizgos']==1) || !isset($_GET['new_delo_log']) ? "checked":"")?>><br>
		</form>


		<hr>
		<a onclick="showhide('oldnew_delo2');" href="javascript:Void();">История переводов c 23/04/2015 - по 08/12/2016</a>
		<?php
		if ($_GET['olddelo2'] == 'true') {
			?> <div id="oldnew_delo2" style="display:block;"> <?php
		} else {
			?> <div id="oldnew_delo2" style="display:none;"> <?php
		}
		?>

		<form method="get" action='http://archive.oldbk.com/perevod.php'><input name='sh' type='hidden' value='2'>

		Логин <input type=text name=new_delo_login value='<?=htmlspecialchars($telo['login'],ENT_QUOTES)?>'><br>
		Логин2<input type=text name=new_delo_login2 value='<?=htmlspecialchars($telo2['login'],ENT_QUOTES)?>'> *можно оставить пустым<br>

		<?php
		if ($_GET['olddelo2']=='true') {
			?> c: <input type=text name='new_delo_date' value='<?=$new_delo_date;?>' id="delocalendar-inputField1" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;'> <?php
		} else {
			?> c: <input type=text name='new_delo_date' value='23.04.2015' id="delocalendar-inputField1" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;'> <?php
		}
		?>

		<input type=button id="delocalendar-trigger1" value='...'>
		<script>
			Calendar.setup({
			trigger    : "delocalendar-trigger1",
			inputField : "delocalendar-inputField1",
			dateFormat : "%d.%m.%Y",
			onSelect   : function() { this.hide() }
			});
			document.getElementById('delocalendar-trigger1').setAttribute("type","BUTTON");
		</script>
		<?php
		if ($_GET['olddelo2']=='true') {
			?> по: <input type=text name='new_delo_fdate' value='<?=$new_delo_fdate?>' id="delocalendar-inputField2" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' > <?php
		} else {
			?> по: <input type=text name='new_delo_fdate' value='08.12.2016' id="delocalendar-inputField2" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' > <?php
		}
		?>

		<input type=button id="delocalendar-trigger2" value='...'>
		<script>
			Calendar.setup({
			trigger    : "delocalendar-trigger2",
			inputField : "delocalendar-inputField2",
			dateFormat : "%d.%m.%Y",
			onSelect   : function() { this.hide() }
			});
			document.getElementById('delocalendar-trigger2').setAttribute("type","BUTTON");
		</script>

		<br> Курс екра: <input type='text' value='<?=EKR_TO_KR?>' name='ekr_reit'><br>
		Фильтры: <br>
		Без ресурсов <input name='res_off' type='checkbox' value='1' <?=(isset($_GET['res_off']) && $_GET['res_off'] == 1 ? 'checked' : '')?>> <br>
		Без цветов/открыток/сувениров  <input name='gifts_off' type='checkbox' value='1' <?=(isset($_GET['gifts_off']) && $_GET['gifts_off'] == 1 ? 'checked' : '')?>><br>
		Без выброса/разрушения  <input name='destr_off' type='checkbox' value='1' <?=(isset($_GET['destr_off']) && $_GET['destr_off'] == 1 ? 'checked' : '')?>><br>
		Для публикации на форум <input name='add_info_off' type='checkbox' value='1' <?=(isset($_GET['add_info_off']) && $_GET['add_info_off'] == 1 ? 'checked' : '')?>><br>

		<input type=submit name=new_delo_log value='Просмотр'>&nbsp; анализ:<input name=analiz type=checkbox value=1 <?=((isset($_GET['analiz']) && $_GET['analiz'] == 1) || !isset($_GET['new_delo_log']) ? "checked" : "")?>> анализ по госу:<input name=analizgos type=checkbox value=1 <?=((isset($_GET['analizgos']) && $_GET['analizgos'] == 1) || !isset($_GET['new_delo_log']) ? "checked" : "")?>><br>
		<input type=hidden name='olddelo2' value='true'>
		</form>
		</div>

		<hr>
		<a onclick="showhide('oldnew_delo');" href="javascript:Void();">История переводов c 1/12/2011 - по 23/04/2015</a>
		<?php
		if ($_GET['olddelo']=='true') {
			?> <div id="oldnew_delo" style="display:block;"> <?php
		} else {
			?> <div id="oldnew_delo" style="display:none;"> <?php
		}
		?>

		<form method="get" action='http://archive.oldbk.com/perevod.php'><input name='sh' type='hidden' value='2'>

		Логин  <input type=text name=new_delo_login value='<?=$telo['login'];?>'> <br>
		Логин2 <input type=text name=new_delo_login2 value='<?=$telo2['login']?>'> *можно оставить пустым<br>
		<?php
		if ($_GET['olddelo']=='true') {
			?> c: <input type=text name='new_delo_date' value='<?=$new_delo_date;?>' id="delocalendar-inputField5" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' > <?php
		} else {
			?> c: <input type=text name='new_delo_date' value='01.12.2011' id="delocalendar-inputField5" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' > <?php
		}
		?>

		<input type=button id="delocalendar-trigger5" value='...'>
		<script>
		Calendar.setup({
			trigger    : "delocalendar-trigger5",
			inputField : "delocalendar-inputField5",
			dateFormat : "%d.%m.%Y",
			onSelect   : function() { this.hide() }
		});
		document.getElementById('delocalendar-trigger5').setAttribute("type","BUTTON");
		</script>

		<?php

		if ($_GET['olddelo'] == 'true') {
			?> по: <input type=text name='new_delo_fdate' value='<?=$new_delo_fdate;?>' id="delocalendar-inputField6" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' > <?php
		} else {
			?> по: <input type=text name='new_delo_fdate' value='23.04.2015' id="delocalendar-inputField6" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;'> <?php
		}

		?>

		<input type=button id="delocalendar-trigger6" value='...'>
		<script>
			Calendar.setup({
			trigger    : "delocalendar-trigger6",
			inputField : "delocalendar-inputField6\",
			dateFormat : "%d.%m.%Y",
			onSelect   : function() { this.hide() }
			});
		document.getElementById('delocalendar-trigger6').setAttribute("type","BUTTON");
		</script>

		<br> Курс екра: <input type='text' value='<?=(EKR_TO_KR)?>' name='ekr_reit'><br>
		Фильтры: <br>
		Без ресурсов <input name='res_off' type='checkbox' value='1' <?=(isset($_GET['res_off']) && $_GET['res_off'] == 1 ? 'checked' : '')?>> <br>
		Без цветов/открыток/сувениров  <input name='gifts_off' type='checkbox' value='1' <?=(isset($_GET['gifts_off']) && $_GET['gifts_off'] == 1 ? 'checked' : '')?>><br>
		Без выброса/разрушения  <input name='destr_off' type='checkbox' value='1' <?=(isset($_GET['destr_off']) && $_GET['destr_off'] == 1 ? 'checked' : '')?>><br>
		Для публикации на форум <input name='add_info_off' type='checkbox' value='1' <?=(isset($_GET['add_info_off']) && $_GET['add_info_off'] == 1 ? 'checked' : '')?>><br>

		<input type=submit name=new_delo_log value='Просмотр'>&nbsp; анализ:<input name=analiz type=checkbox value=1 <?=((isset($_GET['analiz']) && $_GET['analiz'] == 1) || !isset($_GET['new_delo_log']) ? "checked":"")?>>
		анализ по госу:<input name=analizgos type=checkbox value=1 <?=((isset($_GET['analizgos']) && $_GET['analizgos']==1) || !isset($_GET['new_delo_log']) ? "checked" : "")?>><br>
		<input type=hidden name='olddelo' value='true' >
		</form>
		</div>
		<hr>


		<?php
		if (isset($_GET['new_delo_log']) and (isset($_GET['new_delo_login']))) {
			$stamp_start=mktime(0, 0, 0, (int)($new_delo_date_all[1]), (int)($new_delo_date_all[0]), (int)($new_delo_date_all[2]));
			$stamp_fin=mktime(23, 59, 59,(int)($new_delo_fdate_all[1]), (int)($new_delo_fdate_all[0]), (int)($new_delo_fdate_all[2]));

			if  ($telo['id'] == 2 && $user['align']=='1.99') {

			} elseif (($telo['klan'] == 'Adminion' OR $telo['klan'] == 'radminion' OR $telo['id'] == 6 OR $telo['id'] == 2 OR $telo['id'] == 4 OR $telo['id'] == 3 OR $telo['id'] == 5 OR $telo['deal'] > 0) AND ($user['klan'] != 'Adminion' && $user['klan'] != 'radminion')) {
				unset($telo);
			} elseif($user['align'] >= 1.1 && $user['align'] <= 1.99 && $telo['align'] >= 1.1 && $telo['align'] <= 1.99 && $user['align'] < $telo['align']) {			 	unset($telo);			}

			if ($telo['id'] == 326 && !ADMIN) die();

			if ($telo) {

				$sql_delo_type='';
				if($access['view_ekr'] != 1)  {
					$sql_delo_type.='51,68,';
				}

				if(isset($_GET['destr_off']) && $_GET['destr_off'] == 1)	{
					$sql_delo_type .= ' 19,35,';
				}
				if(isset($_GET['gifts_off']) && $_GET['gifts_off'] == 1) {					$sql_delo_type.='206,207,208,209,401,';				}
				if($user['klan'] != 'Adminion' && $user['klan'] != 'radminion') {
					$sql_delo_type.='10001,10002,10003,10007,';

				}
				if($user['klan'] != 'Adminion' && $user['klan'] != 'radminion' && $user['align'] != "1.99") {
					$sql_delo_type.='3001, 3011,';

				}

				if($sql_delo_type != '') {
					$sql_delo_type = substr($sql_delo_type,0,-1);
					$sql_delo_type = 'AND `type` not in ('.$sql_delo_type.')';
				}


				$sql_item_type='';
				if(isset($_GET['res_off']) && $_GET['res_off']==1) 	{
					$sql_item_type .= '210,';
				}

				if($sql_item_type != '') {
					$sql_item_type=substr($sql_item_type,0,-1);
					$sql_item_type=' AND not ((item_proto > 3000 and item_proto < 3030) OR (item_proto > 103000 and item_proto < 103030)) ';
				}

				$pers_balans = array();
				$pers_sebes = array();
				$pers_balans['shop_balans'] = 0;
				$pers_balans['repair_balans'] = 0;
				$pers_balans['repair_balans_gold'] = 0;
				$pers_balans['church_balans'] = 0;
				$pers_balans['church_balans_kr'] = 0;
				$pers_balans['fshop_balans'] = 0;
				$pers_balans['eshop_balans'] = 0;
				$pers_balans['znahar_balans_e'] = 0;
				$pers_balans['znahar_balans'] = 0;
				$pers_balans['repair_balans_e'] = 0;
				$pers_balans['ars_balans'] = array();
				$pers_balans['komok_balans'] = 0;
				$pers_balans['zagorod_balans_get'] = 0;
				$pers_balans['zagorod_balans_rep'] = 0;
				$pers_balans['zagorod_balans_fin'] = 0;
				$pers_balans['zagorodmage_balans'] = 0;
				$pers_balans['fair_balans'] = 0;
				$pers_balans['fontan_balans'] = 0;
				$pers_balans['quest_balans'] = 0;
				$pers_balans['buker_balans_e'] = 0;
				$pers_balans['buker_blans_gold']=0;
				$pers_balans['buker_balans'] = 0;
				$pers_balans['ruines_balans'] = 0;
				$pers_balans['laba_balans'] = 0;
				$pers_balans['laba_balans_kr'] = 0;
				$pers_balans['bs_balans'] = 0;
				$pers_balans['bank_balans_e'] = 0;
				$pers_balans['bank_balans'] = 0;
				$pers_balans['prokat_balans'] = 0;
				$pers_balans['kazna_balans'] = array();
				$pers_balans['zagorodloot_balans'] = 0;


				$prototype_shop = array();
				$prototype_magic_shop = array();
				$data=mysql_query('SELECT * FROM shop');
				while($row = mysql_fetch_assoc($data)) {					$prototype_shop['k'][$row['id']] = $row;
					if($row['magic'] > 0) {						$prototype_magic_shop['k'][$row['name']] = $row;					}				}

				$data=mysql_query('SELECT * FROM cshop');
				while($row = mysql_fetch_assoc($data)) {
					$prototype_shop['c'][$row['id']]=$row;
				}

				$data=mysql_query('SELECT * FROM eshop');
				while($row=mysql_fetch_assoc($data)) {
					$prototype_shop['e'][$row['id']]=$row;
					if($row['magic'] > 0) {
						$prototype_magic_shop['e'][$row['name']] = $row;
					}
				}

				if($_GET['ekr_reit'] > 0) {
					$bank_reit=(int)$_GET['ekr_reit'];
				} else {
					$bank_reit=0;
				}

				//пушки хаоса (1 кр)     1006232
				//weap_haos - анализируется по 1 кр
				$weap_haos = array(1006232,1006233,1006234,1006241,1006242,199,201,204);
				$analiz_types = array(39,99,98,38,169,168,40,41,122,123,225,227,207,402,410,405,266,267,262,263,264,265,62,67,187);
				$quest_types = array(253,255,252,256,257,258,259);//совмещает все квестовые дела в раздел "Квестовые персонажи"

				/*
				в ешопе есть ХАРДКОДЕД ПОДМЕНА некоторых прототипов (на кольца на пример
				1005203=>5278
				1005202=>5277

				в цикле анализхатора МЕНЯЕМ из назад, дабы видеть ПРАВИЛЬНУЮ цену
				//старый масив с кольцами
				$arts_array=array(204204,210,209,199199,206206,200200,198198,200200,210210,5278,5277,262,260,2001,100029,1002222,2002,2000,2003,55510323,55510324,55510325,55510326,55510327,100028,7001,55510317,55510318,55510319,55510320,55510321,55510322,121121122,121121123,121121124,222222230,222222231,222222232,222222233,222222234,222222235,7002,7003,200273,222222242,222222243,222222244,222222245,222222246,222222247,222222248,222222249,222222250,222222251,222222252,222222253,222222254,222222255,9090,200272,5205,5204,5203,5202,18475,18461,18462,18463,18464,18465,18466,18467,18468,18469,18470,18471,18472,18473,18474,18476,18477,18478,18479,18480,18481,18482,18483,18484,18485,18486,18487,18488,18489,18490,18491,18492,18493,18494,18495,18496,18497,18498,18499,18500,18501,18502,18503,18526,222222230,222222231,222222232,222222233,222222234,222222235,222222242,222222243,222222244,222222245,222222246,222222247,222222248,222222249,222222250,222222251,222222252,222222253,222222254,222222255,190190,1120); //прототипы из ешопа, которые считаем по РЕЙТУ екр к КР

				убрали екр кольца - 14/10/2015
				*/
				$arts_array=array(204204,210,209,199199,206206,200200,198198,200200,210210,5278,5277,262,260,2001,100029,1002222,2002,2000,2003,55510323,55510324,55510325,55510326,55510327,100028,7001,55510317,55510318,55510319,55510320,55510321,55510322,121121122,121121123,121121124,7002,7003,200273,9090,200272,5205,5204,5203,5202,190190,1120); //прототипы из ешопа, которые считаем по РЕЙТУ екр к КР
				/*


				глюк с айди 1005202,1005203 - со свитками и кольцами
				$podmena=array( 5278,5277,
						5278=>1005203,
						5277=>1005202);
				*/

				$dvp=array(4=>300,5=>500,6=>850,7=>1200,8=>2500,9=>5000,10=>7500,11=>12000);

				$pers_sort = " `owner` = '{$telo['id']}' AND ";
				$target_logins=array();
				if(isset($telo2) && isset($telo2['id'])) {					$pers_sort.= " target='{$telo2['id']}' AND ";				}

				if ($_GET['olddelo']=='true') {
					$dd=1;
				} elseif ($_GET['olddelo2'] == 'true') {
					$dd=2;
				} else {
					$dd=0;
				}

				// MAIN SQL!
				$sql = "SELECT * FROM `{$table_delo[$dd]}` WHERE ".$pers_sort."`sdate` > '".$stamp_start."' AND `sdate` < '".$stamp_fin."' ".$sql_delo_type." ".$sql_item_type." ;";
				$data = mysql_query($sql);

				$search=1;
				if(isset($_GET['add_info_off']) && $_GET['add_info_off'] == 1) {
					$add_info_off = 1;
				} else {
					$add_info_off=2;
				}

				if($search == 1) {
					while ($row = mysql_fetch_array($data)) {

						if (!ADMIN && ($row['owner'] == 326 || $row['target'] == 326)) continue;

						if (isset($_GET['res_off']) && $_GET['res_off'] != '') {
							if (res_detecting($row['item_name']))  {
								continue;
							}
						}


						$sebes=0;
						unset($row['spend_money']);
						//правим прототит, так как в ешопе идет подмена прототипа ешопа на прототип шопа.. вот вертаем назад для верного анализа

						/*
						if(in_array($row['item_proto'],$podmena)) {
							$row['item_proto'] = $podmena[$row['item_proto']];
						}
						*/

						if(($row['type'] == 510) && ($row['target_login'] == 'Мастер' or $row['target_login'] == 'Архитектор')) {
							continue;
						}

						//подменяем анонимов
						if($access['anonim_hist'] == 1 && ($row['type']==209) && $row['target_login'] == 'Аноним') {
							$row['target_login'] = trim($row['add_info']);
						} elseif($row['type'] == 209 && $row['target_login'] == 'Аноним') {
							$row['target']='anonim1';
						}


						$stop_item_analiz = 0; // если находим пушку хаоса. или еще какойнить предмет временный, то его не анализируем по апам, подгонам и точкам
						$err = '';
						//простой вывод дела (список всего)

						if($_GET['analiz'] != 1) {
							$d_out = get_delo_rec($row,$access,'',$add_info_off);
							$l = strlen($d_out);
							if($l > 250) {
								$d_out=wordwrap($d_out, 150, "\n", 1);
							}
							echo $d_out.'<br>';
						} elseif($_GET['analiz'] == 1) {
							$d_out=get_delo_rec($row,$access,'',$add_info_off);
							$l=strlen($d_out);
							if($l > 250) {								$d_out=wordwrap($d_out, 150, "\n", 1);							}

							$d_out=$d_out;

							//только Админы видят
							if ($row['type'] == 510 or $row['type'] == 511) {
								if (!($access['i_angel'] > 0)) {
									$row['target_login']='Аноним';
									$row['target']=6;
								}
							}

							//Проверка анонима при подарках, видят реальный ник только ангелы
							if($row['type'] == 208 || $row['type'] == 209 || $row['type'] == 410 || $row['type'] == 405) {
								if ($access['i_angel'] > 0) {									if($row['target_login'] == 'Аноним') {										$row['target_login']=trim($row['add_info']);									}								} else {									if($row['target_login'] == 'Аноним') {
										$row['target'] = 6;
									}								}
							}


							// фикс на лавку
							if ($row['target'] == 0 && $row['target_login'] == "арендная лавка") {
								$row['target'] = 449;
							}

							// совмещаем старьевщика (у одного из низх ЛАТИНСКАЯ е)
							if($row['target_login'] == 'Старьeвщик') {
								$row['target_login'] = 'Старьевщик'; $row['target'] = 75;
							}


							//квестовые персонажи
							if(in_array($row['type'],$quest_types) && $row['target_login'] != "ристалище") {
								$row['target_login'] = "Квестовые персонажи";
							}
							//конец персонажей

							//Воровство лошадей. Меняем логины(Урал.Украли лошадь, на совершившего) для балансов
							if($row['type'] == 266 || $row['type'] == 267) {
								$log=explode('/',$row['add_info']);
								if (count($log) == 2) {
									$row['target_login']=trim($log[1]);
									$row['target']=trim($log[0]);
								}
							}


							//фиксим логины для отражения  в анализаторе
							if($row['type'] == 208 || $row['type']==209) {
								$row['target_login']="Подарки из цветочного";
								$row['target']=0;
							}

							if($row['type'] == 120 || $row['type']==124) {
								$row['target_login']="Комиссионный магазин";
								$row['target']=0;
							}

							$row = login_fix_for_delo($row);

							if($row['type'] == 32 || $row['type'] == 33 || $row['type'] == 81) {
								$row['target_login'] = 'Использование предмета';
								$row['target'] = 0;
							}

							if ($row['target_login'] == "Срок годности") $row['target_login'] = "Срок годности предмета";

							//фикс на пустые ники таргета
							if($row['target_login'] == '') {

								if($row['type']==19) {									$row['target_login']='Выбросил';								} elseif($row['type']==6) {									$row['target_login']='Оплата поединка';								} elseif($row['type']==206) {									$row['target_login']='цветочный маг.';								} elseif($row['type']==15 || $row['type']==7 || $row['type']==18 || $row['type']==16) {
									$row['target_login']='Бой на деньги.';
								} elseif($row['type']==321) {
									$row['target_login']='Клановые войны';
								}

							}

							if($row['type']==215 || $row['type']==217 || $row['type']==214 || $row['type']==216) {
									$targ=explode('/',$row['add_info']);
									$row['target']=trim($targ[2]);
									$row['target_login']=trim($targ[1]);
							}

							$target_logins[$row['target']]=trim($row['target_login']);

							if($row['type']==1) {								$pers_balans['shop_balans']-=$row['sum_kr'];
								$sebes-=$row['sum_kr'];
								$pers_balans_txt['shop_balans'][$row['id']]['delo']=$d_out;							} elseif($row['type']==34) {								$pers_balans['shop_balans']+=$row['sum_kr'];
								$pers_balans_txt['shop_balans'][$row['id']]['delo']=$d_out;
								$sebes+=$row['sum_kr'];							} elseif($row['type']==172 || $row['type'] == 386) {
								$pers_balans['church_balans']-=$row['sum_rep'];
								$pers_balans_txt['church_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==233 || $row['type']==235 || $row['type']==234) {
								$pers_balans['registr_balans']+=$row['sum_kr'];
								$pers_balans_txt['registr_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==177 || $row['type']==194 || $row['type']==193 || $row['type']==190 || $row['type']==191 || $row['type']==198 || $row['type']==192 || $row['type']==179) {
								if ($row['sum_kr'] > 0) $pers_balans['repair_balans']-=$row['sum_kr'];
								if ($row['sum_ekr'] > 0) $pers_balans['repair_balans_e']-=$row['sum_ekr'];
								if (!empty($row['add_info'])) {
									$t = explode("/",$row['add_info']);
									$pers_balans['repair_balans_gold'] -= $t[0];
								}
								$pers_balans_txt['repair_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==197 || $row['type']==196) {
								$pers_balans['repair_balans_e']-=$row['sum_ekr'];
								$pers_balans_txt['repair_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==3434) {
								$pers_balans['eshop_balans']+=$row['sum_ekr'];
								$pers_balans_txt['eshop_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==140) {
								$pers_balans['eshop_balans']-=$row['sum_ekr'];
								$pers_balans_txt['eshop_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==25 ||$row['type']==24) {
								$pers_balans['bank_balans']-=$row['sum_kr'];
								$pers_balans_txt['bank_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==2510) {
								$pers_balans['bank_balans']-=$row['sum_kr'];
								$pers_balans_txt['bank_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==2520 || $row['type']==513 ) {
								$pers_balans['bank_balans_e']-=$row['sum_ekr'];
								$pers_balans_txt['bank_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==514 ) {
								$pers_balans['bank_balans_e']+=$row['sum_ekr'];
								$pers_balans_txt['bank_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==26 ) {
								$pers_balans['bank_balans']+=$row['sum_kr'];
								$pers_balans_txt['bank_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==1106) {
								$pers_balans['buker_balans_e']+=$row['sum_ekr'];
								$pers_balans_txt['buker_balans_e'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==1107) {
								$pers_balans['buker_balans_e']-=$row['sum_ekr'];
								$pers_balans_txt['buker_balans_e'][$row['id']]['delo']=$d_out;
						 	} elseif($row['type']==1351) {
									$pers_balans['buker_balans_gold']+=$row['sum_kr'];
									$pers_balans_txt['buker_balans_gold'][$row['id']]['delo']=$d_out;
								} elseif($row['type']==1352) {
									$pers_balans['buker_balans_gold']-=$row['sum_kr'];
									$pers_balans_txt['buker_balans_gold'][$row['id']]['delo']=$d_out;
								}
							elseif($row['type']==1104) {
								$pers_balans['buker_balans']+=$row['sum_kr'];
								$pers_balans_txt['buker_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==1105) {
								$pers_balans['buker_balans']-=$row['sum_kr'];
								$pers_balans_txt['buker_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==310 || $row['type']==47 || $row['type']==48 || $row['type']==96 || $row['type']==310 || $row['type']==97 || $row['type']==44) {
								$pers_balans['bank_balans_e']-=$row['sum_ekr'];
								$pers_balans_txt['bank_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==45 || $row['type']==46 || $row['type']==29 || $row['type']==311 || $row['type']==357) {
								$pers_balans['bank_balans_e']+=$row['sum_ekr'];
								$pers_balans_txt['bank_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==28) {
								$pers_balans['bank_balans_r']+=$row['sum_rep'];
								$pers_balans['bank_balans_e']-=$row['sum_ekr'];
								$pers_balans_txt['bank_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==254 or $row['type']==270 or $row['type']==271) {
								if($row['type']==254) {
									//репа
									$pers_balans['zagorod_balans_rep']+=$row['sum_rep'];
								} elseif($row['type']==271) {
									$pers_balans['zagorod_balans_fin']+=1;
								} elseif($row['type']==270) {
									$pers_balans['zagorod_balans_get']+=1;
								}

								$pers_balans_txt['zagorod_balans'][$row['id']]['delo']=$d_out;

							} elseif($row['type']==264 || $row['type']==262) {
								if ($row['sum_kr'] > 0) {
									$pers_balans['zagorodloot_balans']+=$row['sum_kr'];
								} else {
									$pers_balans['zagorodloot_balans']+=$row['item_count'] * $row['item_cost'];
								}
								$pers_balans_txt['zagorodloot_balans'][$row['id']]['delo']=$d_out;

							} elseif($row['type']==375 || $row['type']==374) {
								$pers_balans['zagorodmage_balans']-=$row['sum_rep'];
								$pers_balans_txt['zagorodmage_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==509 || $row['type']==508) {
								if($row['type']==509) {
									//покупка
									$ingold=explode("/",$row['add_info']);
									$allcost = $ingold[0];
									$pers_balans['fair_balans']-=$allcost;
								} elseif($row['type']==508) {
									//возврат
									$ingold=explode("/",$row['add_info']);
									$allcost =$ingold[0];
									$pers_balans['fair_balans']+=$allcost;
								}

								$pers_balans_txt['fair_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==263 || $row['type']==265) {
								if ($row['sum_kr'] > 0) {
									$pers_balans['zagorodloot_balans']-=$row['sum_kr'];
								} else {
									$pers_balans['zagorodloot_balans']-=$row['item_count'] * $row['item_cost'];
								}
								$pers_balans_txt['zagorodloot_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==251 || $row['type']==381 || $row['type']==382 || $row['type']==383 || $row['type']==707 || $row['type']==700 || $row['type']==701 || $row['type'] == 1175) {
								if ($row['type'] == 1175) {
									$pers_balans['laba_balans_kr']-=$row['sum_kr'];
								} else {
									$pers_balans['laba_balans']+=$row['sum_rep'];
								}
								$pers_balans_txt['laba_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==182 && $row['target_login'] == "Квесты") {
								$pers_balans['quest_balans']+=$row['sum_rep'];
								$pers_balans_txt['quest_balans'][$row['id']]['delo']=$d_out;
							} elseif(in_array($row['type'],array(275,276,277,278,274,279,291,292)) || ($row['type'] == 182 && $row['target_login'] == "Квест")) {
								if ($row['type'] == 279) $pers_balans[quest_balans2_kr] += $row['sum_kr'];
								if ($row['type'] == 291) $pers_balans[quest_balans2_ekr] += $row['sum_ekr'];
								if ($row['type'] == 182) $pers_balans[quest_balans2_rep] += $row['sum_rep'];

								$pers_balans_txt['quest_balans2'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==5010 || $row['type']==171 || $row['type']==170 || $row['type'] == 323) {
								if ($row['type'] == 323) {
									$pers_balans['znahar_balans_e']-=$row['sum_ekr'];
								} else {
									$pers_balans['znahar_balans']-=$row['sum_kr'];
								}
								$pers_balans_txt['znahar_balans'][$row['id']]['delo']=$d_out;
							} elseif(($row['type']==8) or ($row['type']==9) or  ($row['type']==186)) {
								$row['target_login']='Прокатная лавка';

								if ($row['type'] != 186) {
									$pers_balans['prokat_balans']-=$row['sum_ekr'];
								}

								$pers_balans_txt['prokat_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==12 || $row['type']==13 || $row['type']==14 || $row['type']==1101)  {
								if ($row['type']==13) {
									$pers_balans['star_balans']+=$row['sum_kr'];
								} else {
									$pers_balans['star_balans']+=$row['sum_kr'];
									$pers_balans['star_balans']+=$row['item_cost'];
								}
								$pers_balans_txt['star_balans'][$row['id']]['delo']=$d_out;
							} elseif ((($row['type']==103 || $row['type']==104) and $row['target_login']=='Фонтан Удачи') || ($row['type']==455 and $row['target_login']=='Фонтан')) {
								if ($row['type']==103) {
									$pers_balans['fontan_balans']-=$row['sum_kr'];
								} elseif ($row['type']==455) {
									$pers_balans['fontan_balans']+=$row['sum_kr'];
								} elseif ($row['type']==104) {
									$pers_balans['fontan_balans']+=$row['sum_kr'];
								}
								$pers_balans_txt['fontan_balans'][$row['id']]['delo']=$d_out;
							} elseif (($row['type']==259 ) and ($row['target_login']=='Скупщик')) {
								$pers_balans['skupsh_balans']+=$row['sum_kr'];
								$pers_balans_txt['skupsh_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==124 || $row['type']==120 || $row['type']==121) {
								$pers_balans['komok_balans']+=$row['sum_kom'];
								$pers_balans_txt['komok_balans'][$row['id']]['delo']=$d_out;
							} elseif($row['type']==408) {
								$tmp = strpos($row['add_info'],' ');
								if ($tmp !== false) {
									$tmpk = substr($row['add_info'],0,$tmp);
								} else {
									$tmpk = $row['add_info'];
								}
								if ($tmpk[strlen($tmpk)-1] == ".") $tmpk = substr($tmpk,0,-1);

								if (!isset($pers_balans['kazna_balans'][$tmpk])) $pers_balans['kazna_balans'][$tmpk] = 0;
								$pers_balans['kazna_balans'][$tmpk] += $row['sum_kr'];
								$pers_balans_txt['kazna_balans'][$tmpk][$row['id']]['delo']=$d_out;
							} elseif($row['type']==134 || $row['type']==407) {
								if ($row['type'] != 407) {
									$tmpk = $row['target_login'];
									if (!isset($pers_balans['kazna_balans'][$tmpk])) $pers_balans['kazna_balans'][$tmpk] = 0;
									$pers_balans['kazna_balans'][$tmpk] -= $row['sum_kr'];
									$pers_balans_txt['kazna_balans'][$tmpk][$row['id']]['delo']=$d_out;
								}
							} elseif ($row['item_arsenal'] != "") {
								if ($row['type'] == 62 || $row['type'] == 61 || $row['type'] == 63 || $row['type'] == 21 || $row['type'] == 22) {
									if($row['item_incmagic'] != "Призвать Огненную Элементаль") {
										if (!isset($pers_balans['ars_balans'][$row['item_arsenal']])) $pers_balans['ars_balans'][$row['item_arsenal']] = 0;
										$pers_balans['ars_balans'][$row['item_arsenal']] += $row['item_cost'];
										$pers_balans_txt['ars_balans'][$row['item_arsenal']][$row['id']]['delo']=$d_out;
									}
								}
								if ($row['type'] == 67 || $row['type'] == 66 || $row['type'] == 64 || $row['type'] == 20 || $row['type'] == 65 || $row['type'] == 188)  {
									if($row['item_incmagic'] != "Призвать Огненную Элементаль") {
										if (!isset($pers_balans['ars_balans'][$row['item_arsenal']])) $pers_balans['ars_balans'][$row['item_arsenal']] = 0;
										$pers_balans['ars_balans'][$row['item_arsenal']] -= $row['item_cost'];
										$pers_balans_txt['ars_balans'][$row['item_arsenal']][$row['id']]['delo']=$d_out;
									}
								}
							} elseif ($row['type'] == 187) {
								$tmpk = str_replace('Арсенал клана ','',$row['target_login']);
								if($row['item_incmagic'] != "Призвать Огненную Элементаль") {
									if (!isset($pers_balans['ars_balans'][$row['item_arsenal']])) $pers_balans['ars_balans'][$row['item_arsenal']] = 0;
									$pers_balans['ars_balans'][$tmpk] -= $row['item_cost'];
									$pers_balans_txt['ars_balans'][$tmpk][$row['id']]['delo']=$d_out;
								}

							} elseif ($row['type'] == 510 || $row['type'] == 511) {
								if ($row['type'] == 510) {
									$pers_balans['ex_balans_kr'] -=$row['sum_kr'];
									$pers_balans['ex_balans_ekr'] +=$row['sum_ekr'];
								} else {
									$pers_balans['ex_balans_kr'] +=$row['sum_kr'];
									$pers_balans['ex_balans_ekr'] -=$row['sum_ekr'];
								}
								$pers_balans_txt['ex_balans'][$row['id']]['delo']=$d_out;
							} elseif ($row['type'] == 102 || $row['type'] == 101 || $row['type'] == 100) {
								if ($row['type'] == 100) {
									$pers_balans['bs_balans'] -= $row['sum_kr'];
								} else {
									$pers_balans['bs_balans'] += $row['sum_kr'];
								}
								$pers_balans_txt['bs_balans'][$row['id']]['delo']=$d_out;
							} elseif ($row['target_login'] == "КО" || $row['owner_login'] == "КО" || $row['target_login'] == "KO" || $row['owner_login'] == "KO" || $row['target_login'] == "Коммерческий отдел" || $row['owner_login'] == "Коммерческий отдел" || $row['target_login'] == "ФинБот" || $row['owner_login'] == "ФинБот") {
								$pers_balans_txt['ko_balans'][$row['id']]['delo']=$d_out;
							} elseif ($row['type'] == 200 || $row['type'] == 201 || $row['type'] == 202 || $row['type'] == 203 || $row['type'] == 204) {
								if ($row['type'] == 200) {
									$pers_balans['ruines_balans'] -=$row['sum_kr'];
								} elseif ($row['type'] == 201) {
									$pers_balans['ruines_balans'] +=$row['sum_kr'];
								} else if ($row['type'] == 202) {
									if ($row['item_name'] == "Золотой слиток 1 екр") $pers_balans['ruines_balans_e'] += 1;
									if ($row['item_name'] == "Золотой слиток 10 екр") $pers_balans['ruines_balans_e'] += 10;
									if ($row['item_name'] == "Золотой слиток 20 екр") $pers_balans['ruines_balans_e'] += 20;
								} else if ($row['type'] == 203 || $row['type'] == 204) {
									$pers_balans['ruines_balans_r'] +=$row['sum_rep'];
								}
								$pers_balans_txt['ruines_balans'][$row['id']]['delo']=$d_out;
							} elseif ($row['type'] == 366 || $row['type'] == 4) {
								if ($row['type'] == 366) {
									$pers_balans['rist_balans'] -= $row['sum_kr'];
								} elseif ($row['type'] == 4) {
									if ($row['sum_kr'] > 0) {
										$pers_balans['rist_balans'] += $row['sum_kr'];
									} else {
										$pers_balans['rist_balans'] += $row['item_cost'];
									}

									if ($row['sum_rep'] > 0) {
										$pers_balans['rist_balans_r'] += $row['sum_rep'];
									}
								}
								$pers_balans_txt['rist_balans'][$row['id']]['delo']=$d_out;
							} elseif ($row['type'] == 209 || $row['type'] == 208 || $row['type'] == 401) {
								if ($row['type'] == 401) {
									$pers_balans['fshop_balans'] -= $row['sum_kr'];
								}
								$pers_balans_txt['fshop_balans'][$row['id']]['delo']=$d_out;
							} elseif (($row['type'] == 256 && $row['target_login'] == "ристалище") || $row['type'] == 183 || $row['type'] == 1184 || $row['type'] == 367 || $row['type'] == 1323) {
								if ($row['type'] == 1184) {
									$pers_balans['rist2_balans'] += $row['item_cost'];
								} elseif ($row['type'] == 1323) {
									$pers_balans['rist2_balans_ekr'] -= $row['sum_ekr'];
								} elseif ($row['type'] == 367) {
									$pers_balans['rist2_balans'] -= $row['sum_kr'];
								} else {
									$pers_balans['rist2_balans'] -= $row['sum_kr'];
									$pers_balans['rist2_balans_r'] += $row['sum_rep'];
								}
								$pers_balans_txt['rist2_balans'][$row['id']]['delo']=$d_out;
							} else {
								if (strpos($row['target_login'],'Исчадие Хаоса (') !== false) {									$pers_balans_txt['0:'.$row['target_login']][$row['id']]['delo']=$d_out;									$row['target'] = 0;								} else {									$pers_balans_txt[$row['target'].':'.$row['target_login']][$row['id']]['delo']=$d_out;								}							}

							if($row['target'] > 0) {							        if (!isset($pers_balans[$row['target'].':'.$row['target_login']])) $pers_balans[$row['target'].':'.$row['target_login']] = 0;								if($row['type']==166) {
									$pers_balans[$row['target'].':'.$row['target_login']]-=$row['sum_kr'];
									$sebes-=$row['sum_kr'];
								} elseif($row['type']==167) {									$pers_balans[$row['target'].':'.$row['target_login']]+=$row['sum_kr'];
									$sebes+=$row['sum_kr'];								} elseif($row['type']==3373) {
									$pers_balans[$row['target'].':'.$row['target_login']]+=$row['sum_kr'];
									$sebes+=$row['sum_kr'];
								} elseif($row['type']==49) {
									$pers_balans[$row['target'].':'.$row['target_login']]-=$row['sum_kr'];
									$sebes-=$row['sum_kr'];
								} elseif($row['type']==50) {
									$pers_balans[$row['target'].':'.$row['target_login']]+=$row['sum_kr'];
									$sebes+=$row['sum_kr'];
								} elseif($row['type']==36) {
									$pers_balans[$row['target'].':'.$row['target_login']]-=$row['sum_kr'];
									$sebes-=$row['sum_kr'];
								} elseif($row['type']==37) {
									$pers_balans[$row['target'].':'.$row['target_login']]+=$row['sum_kr'];
									$sebes+=$row['sum_kr'];
								} elseif($row['type']==3737) {
									$pers_balans[$row['target'].':'.$row['target_login']]+=$row['sum_kr'];

									$golds=explode("/",$row['add_info']);
									$golds=$golds[0];
									$pers_balans_gold[$row['target'].':'.$row['target_login']]-=$golds;

									$sebes+=$row['sum_kr'];
								} elseif($row['type']==3636) {
									$pers_balans[$row['target'].':'.$row['target_login']]-=$row['sum_kr'];

									$golds=explode("/",$row['add_info']);
									$golds=$golds[0];
									$pers_balans_gold[$row['target'].':'.$row['target_login']]+=$golds;

									$sebes-=$row['sum_kr'];
								}
								elseif($row['type']==42) {
									$pers_balans[$row['target'].':'.$row['target_login']]-=$row['sum_kr'];
									$sebes-=$row['sum_kr'];
								} elseif($row['type']==43) {
									$pers_balans[$row['target'].':'.$row['target_login']]-=$row['sum_kr'];
									$sebes-=$row['sum_kr'];
								} elseif($row['type']==207){
									$pers_balans[$row['target'].':'.$row['target_login']]-=$row['item_cost']*0.5;
									$sebes-=$row['sum_kr'];
								} elseif($row['type']==266) {
									$pers_balans[$row['target'].':'.$row['target_login']]-=0;
									$sebes=0;
								} elseif($row['type']==267) {
									$pers_balans[$row['target'].':'.$row['target_login']]+=0;
									$sebes=0;
								} elseif($row['type']==215 || $row['type']==217) {
									$pers_balans[$row['target'].':'.$row['target_login']]+=$row['sum_kr'];
									$sebes+=$row['sum_kr'];
								} elseif($row['type']==214 || $row['type']==216) {
									$pers_balans[$row['target'].':'.$row['target_login']]-=$row['sum_kr'];
									$sebes-=$row['sum_kr'];
								} elseif($row['type']==262) {
									$pers_balans[$row['target'].':'.$row['target_login']]+=$row['sum_kr'];
									$sebes+=$row['sum_kr'];
								}

								//вычисляем реальную стоимость предмета при его передаче.
								if((in_array($row['type'],$analiz_types)) && $row['item_count']>0 && $row['item_proto']>0) {									//чекаем магазин
									$intem_print_info='';

									if(in_array($row['item_proto'],$weap_haos)) {										$stop_item_analiz=1;

										$row['item_real_cost']=1;
										$intem_print_info.= ' хаос:'.$row['item_real_cost'];
										$search_inc=0;
										$row['spend_money']=$row['item_real_cost'];
									} elseif(in_array($row['item_proto'],$arts_array)) {
										$shop='e';
										$row['item_real_cost']=$prototype_shop[$shop][$row['item_proto']]['ecost']*$bank_reit;
										$row['item_real_cost_for_mf']=$prototype_shop[$shop][$row['item_proto']]['cost'];
										$row['spend_money']=$row['item_real_cost']*$row['item_count'];
										if ($row['item_sowner'] > 0) {
											$intem_print_info.= ' итем(реп):'.$row['item_cost'].'кр. х'.$row['item_count'].'шт.';
										} else {
											$intem_print_info.= ' итем(арт):'.$row['item_real_cost'].'кр. х'.$row['item_count'].'шт.';
										}
										$search_inc=1;
									} elseif($row['item_type'] == 200) {
										$shop='e';
										if ($row['item_sowner'] == 0) {
											if (!isset($prototype_shop[$shop][$row['item_proto']]['cost'])) $prototype_shop[$shop][$row['item_proto']]['cost'] = 0;
											$row['item_real_cost']=$prototype_shop[$shop][$row['item_proto']]['cost'];
											$row['spend_money']=$prototype_shop[$shop][$row['item_proto']]['cost']*$row['item_count'];
											$intem_print_info.= ' итем(е):'.$prototype_shop[$shop][$row['item_proto']]['cost'].'кр. х'.$row['item_count'].'шт.';
										} else {
											if (!isset($prototype_shop[$shop][$row['item_proto']]['cost'])) $prototype_shop[$shop][$row['item_proto']]['cost'] = 0;
											$row['item_real_cost']=$row['item_cost'];
											$row['spend_money']=$prototype_shop[$shop][$row['item_proto']]['cost']*$row['item_count'];
											$intem_print_info.= ' итем(реп):'.$prototype_shop[$shop][$row['item_proto']]['cost'].'кр. х'.$row['item_count'].'шт.';
										}
										$search_inc=1;
									} elseif(isset($prototype_shop['k'][$row['item_proto']]) && count($prototype_shop['k'][$row['item_proto']])>0) {										$shop='k';

										if ($row['item_sowner'] == 0) {
											if ($prototype_shop[$shop][$row['item_proto']]['ecost']>0) {
												$row['item_real_cost']=$prototype_shop[$shop][$row['item_proto']]['ecost']*$bank_reit;
											} else {
												$row['item_real_cost']=$prototype_shop[$shop][$row['item_proto']]['cost'];
											}

											$row['item_real_cost_for_mf']=$prototype_shop[$shop][$row['item_proto']]['cost'];
											$row['spend_money']=$row['item_real_cost']*$row['item_count'];
											$intem_print_info.= ' итем(к):'.$row['item_real_cost'].'кр. х'.$row['item_count'].'шт.';
	        								} else {
	        									if ($prototype_shop[$shop][$row['item_proto']]['ecost']>0) {
												$row['item_real_cost']=$row['item_ecost']*$bank_reit;
											} else {
												$row['item_real_cost']=$row['item_cost'];
											}

											$row['item_real_cost_for_mf']=$prototype_shop[$shop][$row['item_proto']]['cost'];
											$row['spend_money']=$row['item_real_cost']*$row['item_count'];
											$intem_print_info.= ' итем(реп):'.$row['item_real_cost'].'кр. х'.$row['item_count'].'шт.';
										}
										$search_inc=1;
									} elseif(isset($prototype_shop['c'][$row['item_proto']]) && count($prototype_shop['c'][$row['item_proto']])>0) {
										$shop='c';
										if ($row['item_sowner'] == 0) {
											$row['item_real_cost']=$prototype_shop[$shop][$row['item_proto']]['cost'];
											$row['item_real_cost_for_mf']=$prototype_shop[$shop][$row['item_proto']]['cost'];
											$row['spend_money']=0;
											$intem_print_info.= ' итем(с):'.$row['item_real_cost'].'кр. х'.$row['item_count'].'шт.';
										} else {
											$row['item_real_cost']=$row['item_cost'];
											$row['item_real_cost_for_mf']=$prototype_shop[$shop][$row['item_proto']]['cost'];
											$row['spend_money']=0;
											$intem_print_info.= ' итем(реп):'.$row['item_real_cost'].'кр. х'.$row['item_count'].'шт.';
										}
											$search_inc=1;
									} elseif(isset($prototype_shop['e'][$row['item_proto']]) && count($prototype_shop['e'][$row['item_proto']])>0) {										$shop='e';
										if ($row['item_sowner'] == 0) {
											$row['item_real_cost']=$prototype_shop[$shop][$row['item_proto']]['cost'];
											$row['spend_money']=$prototype_shop[$shop][$row['item_proto']]['cost']*$row['item_count'];
											$intem_print_info.= ' итем(е):'.$prototype_shop[$shop][$row['item_proto']]['cost'].'кр. х'.$row['item_count'].'шт.';
										} else {
											$row['item_real_cost']=$row['item_cost'];
											$row['spend_money']=$prototype_shop[$shop][$row['item_proto']]['cost']*$row['item_count'];
											$intem_print_info.= ' итем(реп):'.$prototype_shop[$shop][$row['item_proto']]['cost'].'кр. х'.$row['item_count'].'шт.';
										}
										$search_inc=1;
									} else {										$err.='<BR><font color=red>ОШИБКА ПОИСКА ПРЕДМЕТА В МАГАЗИНАХ, ID='.$row['id'].' ITEM_ID='.$row['item_id'].'</font></BR>';									}

									if($stop_item_analiz < 1) {
										if (strpos($row['item_name'],"(мф)") !== FALSE) {
											$mf_pr = round($row['item_real_cost_for_mf']*0.5);

											if(($prototype_shop[$shop][$row['item_proto']]['gsila']<1) AND ($prototype_shop[$shop][$row['item_proto']]['glovk'] <1)
											AND ($prototype_shop[$shop][$row['item_proto']]['ginta'] <1) AND ($prototype_shop[$shop][$row['item_proto']]['gintel'] <1)) {
												$mf_pr=round($mf_pr*0.5);											}
												$row['spend_money']+=$mf_pr;
												$intem_print_info.= ' МФ:'.$mf_pr;
										}

										if($row['item_type']==3) {
											$sharp=explode("+",$row['item_name']);
											if (isset($sharp[1]) && (int)($sharp[1])>0) 	{
												$is_sharp=array(1=>20,2=>40,3=>80,4=>160,5=>320, 6 => 640, 7 => 1280, 8 => 2560 , 9 => 5120);
												$row['spend_money']+=$is_sharp[$sharp[1]];
												$intem_print_info.=' заточка +'.$sharp[1].': '.$is_sharp[$sharp[1]].'кр. ';
											}
										}

											//подгон
										if($row['item_ups']>0 && $row['item_sowner'] == 0) {
											$up_cost=$prototype_shop[$shop][$row['item_proto']]['cost'];
											$podgon_c=''; $i=1;
											for($j=5;$j>5-$row['item_ups'];$j--) {
												$pod_cost=upgrade_item($up_cost,$j);
												$row['spend_money']+=$pod_cost['up_cost'];
												$podgon_c.='P'.$i.':'.$pod_cost['up_cost'].',';
												$i++;											}

											$intem_print_info.= ' подгон:'.$podgon_c;
										}

										preg_match_all('#\[(\d*)]#si', $row['item_name'], $lvl);
										$l = 0;
										if(isset($lvl[1][0])) $l=(int)$lvl[1][0];
										if($l>0) {
											$prot_lvl=$prototype_shop[$shop][$row['item_proto']]['nlevel'];
											if($prot_lvl<$l) {
												$lvl_cost=$lvl_cost=array(5=> 10, 6=> 10,7=>24,8=>35,9=>85,10=>120,11=>180,12=>220);
												for($j=$l;$j>$prot_lvl;$j--) {
													if($lvl_cost[$j]) {
														$row['spend_money']+=$lvl_cost[$j];
														$intem_print_info.= ' АП'.$j.':'.$lvl_cost[$j];
													}
												}
											}
										}

										if($row['item_incmagic']!='' && $row['item_incmagic_id'] != 0 && $search_inc==1) {											$row['spend_money']+=$row['item_incmagic_count'];
											$intem_print_info.= ' Встр_свит: '.$row['item_incmagic_count'];
											if($prototype_magic_shop['k'][$row['item_incmagic']]) {												$row['spend_money']+=$prototype_magic_shop['k'][$row['item_incmagic']]['cost'];
												$intem_print_info.= ' Встр:'.$prototype_magic_shop['k'][$row['item_incmagic']]['cost'];
											} elseif ($prototype_magic_shop['e'][$row['item_incmagic']]) {												$row['spend_money']+=$prototype_magic_shop['e'][$row['item_incmagic']]['cost'];
												$intem_print_info.= ' Встр:'.$prototype_magic_shop['e'][$row['item_incmagic']]['cost'];
											} else {												$err.='<BR><font color=red>ОШИБКА ПОИСКА ВСТРОЕННОЙ МАГИ В МАГАЗИНАХ, ITEM_ID='.$row[item_id].'</font></BR>';											}
										}
									}

									if (isset($_GET['analizgos'])) {
										$row['spend_money'] = $row['item_cost']*$row['item_count'];
										$sebes = 0;
									}

									//считаем балансы с персонажами
									if($row['target']>0 && $row['target_login'] != '') {
										//а теперь по типам операций начанаем считать (передал, продал, получил, подарил)
										if($row['type']==39 || $row['type']==168 || $row['type']==38 || $row['type']==405) {
											$pers_balans[$row['target'].':'.$row['target_login']]-=$row['spend_money'];
											$sebes-=$row['spend_money'];
										} elseif($row['type']==99 || $row['type']==98 ||$row['type']==169  || $row['type']==410) {											$pers_balans[$row['target'].':'.$row['target_login']]+=$row['spend_money'];
											$sebes+=$row['spend_money'];
										} elseif($row['type']==40 || $row['type']==122 || $row['type']==227) {											$pers_balans[$row['target'].':'.$row['target_login']]+=($row['spend_money']-$row['sum_kr']);
											$sebes+=($row['spend_money']-$row['sum_kr']);										} elseif($row['type']==41 || $row['type']==225) {											$pers_balans[$row['target'].':'.$row['target_login']]-=($row['spend_money']-$row['sum_kr']);
											$sebes-=($row['spend_money']-$row['sum_kr']);										} elseif($row['type']==123) {											$pers_balans[$row['target'].':'.$row['target_login']]-=($row['spend_money']-$row['sum_kr']+$row['sum_kom']);
											$sebes-=($row['spend_money']-$row['sum_kr']+$row['sum_kom']);										}
									}
								}

								if($row['type']!=1 && $row['type']!=34 && $row['type']!=172 &&
								$row['type']!=233 && $row['type']!=235 && $row['type']!=234 &&
								$row['type']!=179 && $row['type']!=140 &&
								$row['type']!=177 && $row['type']!=194 && $row['type']!=193 &&
								$row['type']!=190 && $row['type']!=191 && $row['type']!=198 &&
								$row['type']!=192 && $row['type']!=196 && $row['type']!=197 &&
								$row['type']!=25 && $row['type']!=26 && $row['type']!=29 &&
								$row['type']!=28 && $row['type']!=12 && $row['type']!=13 &&
								$row['type']!=14 && $row['type']!=45 && $row['type']!=24 &&
								$row['type']!=46 && $row['type']!=47 && $row['type']!=48 &&
								$row['type']!=311 && $row['type']!=310 && $row['type']!=251 &&
								$row['type']!=182 && $row['type']!=170 && $row['type']!=171 &&
								$row['type']!=5010 && $row['type']!=96 && $row['type']!=97 && $row['type']!=44 /*&& $row['type']!=32 && $row['type']!= 33 && $row['type']!=264 && $row['type']!=265 && $row['type']!=262*/
								)
								{	//собираем массив на печать, исключая данные по магазинам (они отдельно считаются)
									if (isset($pers_balans[$row['target'].':'.$row['target_login']])) {
										$pers_balans_txt[$row['target'].':'.$row['target_login']][$row['id']]['sebes']='<font color='.($sebes>0?'green':($sebes==0?'black':'red')).'>'.$sebes.'кр. </font>';
										$pers_balans_txt[$row['target'].':'.$row['target_login']][$row['id']]['saldo']=$pers_balans[$row['target'].':'.$row['target_login']];
									}

									if((isset($row['spend_money']) && $row['spend_money']>0) || $row['item_proto']>0) {
										if ($pers_balans_txt[$row['target'].':'.$row['target_login']][$row['id']]['saldo']) {
											if (!isset($row['spend_money'])) $row['spend_money'] = 0;
											$pers_balans_txt[$row['target'].':'.$row['target_login']][$row['id']]['analiz']=$err.($row['spend_money']>0?"себес: <b>".$row['spend_money']."кр.</b><br>":"").' <small> тип: '.$row['item_proto'].'. '.$intem_print_info .'</small> ';
											$pers_sebes[$row['id']] = $pers_balans_txt[$row['target'].':'.$row['target_login']][$row['id']]['analiz'];
										}
									}
								}
							}
						} //выбор - анализ или нет
					} // цикл запроса

					if($_GET['analiz'] == 1) {
						reset($pers_balans);
						$tmplist = array();

						while(list($k,$v) = each($pers_balans)) {
							$t = explode(":",$k);

							// фикс на одинаковые айди, но разные имена
							if (!isset($tmplist[$t[0]])) {
								if (!isset($t[1])) $t[1] = "";
								$tmplist[$t[0]] = $t[1];
							} else {
								// нашли чела с ремеймом
								$pers_balans[$t[0].":".$tmplist[$t[0]]] += $pers_balans[$k];
								$pers_balans_txt[$t[0].":".$tmplist[$t[0]]] += $pers_balans_txt[$k];

								unset($pers_balans[$k]);
								unset($pers_balans_txt[$k]);
							}
						}
						echo '</table><tr><td>';

						//сначало выводим баланс по локациям, гос, храмовая и тд и тп.

						//гос
						if (isset($pers_balans['shop_balans'])) {
							echo "<b><a onclick=\"showhide('shop_balans');\" href=\"javascript:Void();\">Баланс с Государственный магазин:</a><font color=".($pers_balans['shop_balans'] >= 0 ? "green" : "red")."> ".$pers_balans['shop_balans']."</font> кр.</b>";
							echo "<div id=shop_balans style=\"display:none;\">
								<table>";
							foreach ($pers_balans_txt['shop_balans'] as $k=>$v)
							{								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['shop_balans'][$k]);							}
							unset($pers_balans_txt['shop_balans']);
							unset ($pers_balans['shop_balans']);

							echo "</table></div><br>";
						}

						if(($user['klan'] == 'Adminion' || $user['klan']=='radminion') || ($user['align'] >= '1.9' && $user['align'] < '2')) {
							//Букер екр
							if ($pers_balans['buker_balans_e']=='') $pers_balans['buker_balans_e']=0;
							if ($pers_balans['buker_balans']=='') $pers_balans['buker_balans']=0;
							if ($pers_balans['buker_balans_gold']=='') $pers_balans['buker_balans_gold']=0;

							echo "<b><a onclick=\"showhide('buker_balans_e');\" href=\"javascript:Void();\">Баланс с Букмекер :</a><font color=".($pers_balans['buker_balans_e'] >= 0 ? "green" : "red")."> ".$pers_balans['buker_balans_e']."</font> екр. <font color=".($pers_balans['buker_balans'] >= 0 ? "green" : "red")."> ".$pers_balans['buker_balans']."</font> кр. <font color=".($pers_balans['buker_balans_gold'] >= 0 ? "green" : "red")."> ".$pers_balans['buker_balans_gold']."</font> монет.</b>";
							echo "<div id=buker_balans_e style=\"display:none;\">
								<table>";
							foreach ($pers_balans_txt['buker_balans_e'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['buker_balans_e'][$k]);
							}
							unset($pers_balans_txt['buker_balans_e']);
							unset($pers_balans['buker_balans_e']);
							echo "</table><br>";

							echo "<table>";
							foreach ($pers_balans_txt['buker_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['buker_balans'][$k]);
							}
							unset($pers_balans_txt['buker_balans']);
							unset ($pers_balans['buker_balans']);
							echo "</table><br>";

							echo "<table>";
							foreach ($pers_balans_txt['buker_balans_gold'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['buker_balans_gold'][$k]);
							}
							unset($pers_balans_txt['buker_balans_gold']);
							unset ($pers_balans['buker_balans_gold']);
							echo "</table></div><br>";

						}

						//храмовая лавка
						if(isset($pers_balans['church_balans']) || isset($pers_balans['church_balans_kr'])) {
							echo "<b><a onclick=\"showhide('church_balans');\" href=\"javascript:Void();\">Баланс с Храмовая лавка:</a><font color=".($pers_balans['church_balans'] >= 0 ? "green" : "red")."> ".$pers_balans['church_balans']."</font> реп<!--., <font color=".($pers_balans['church_balans_kr'] >= 0 ? "green" : "red").">".$pers_balans['church_balans_kr']."</font> кр.--></b>";
							echo "<div id=church_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['church_balans'] as $k=>$v)
							{
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['church_balans'][$k]);

							}
							unset($pers_balans_txt['church_balans']);
							unset ($pers_balans['church_balans']);
							unset ($pers_balans['church_balans_kr']);

							echo "</table></div><br>";
						}

						//бс баланс
						if(isset($pers_balans['bs_balans'])) {
							echo "<b><a onclick=\"showhide('bs_balans');\" href=\"javascript:Void();\">Баланс с БС:</a><font color=".($pers_balans['bs_balans'] >= 0 ? "green" : "red")."> ".$pers_balans['bs_balans']." кр.</font></b>";
							echo "<div id=bs_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['bs_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['bs_balans'][$k]);

							}
							unset($pers_balans_txt['bs_balans']);
							unset ($pers_balans['bs_balans']);
							echo "</table></div><br>";
						}


						//биржа баланс
						if(isset($pers_balans['ex_balans_kr']) || isset($pers_balans['bs_balans_ekr'])) {
							echo "<b><a onclick=\"showhide('ex_balans');\" href=\"javascript:Void();\">Баланс с Биржей:</a> <font color='".($pers_balans['ex_balans_kr'] < 0 ? "red" : "green" )."'>  ".$pers_balans['ex_balans_kr']." кр.</font> <font color='".($pers_balans['ex_balans_ekr'] < 0 ? "red" : "green" )."'>  ".$pers_balans['ex_balans_ekr']." екр.</font></b>";
							echo "<div id=ex_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['ex_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['ex_balans'][$k]);

							}
							unset($pers_balans_txt['ex_balans']);
							unset ($pers_balans['ex_balans']);

							echo "</table></div><br>";
						}


						//регистратура кланов
						if(isset($pers_balans['registr_balans'])) {
							echo "<b><a onclick=\"showhide('registr_balans');\" href=\"javascript:Void();\">Баланс с Регистратура:</a><font color=".($pers_balans['registr_balans'] >= 0 ? "green" : "red")."> ".$pers_balans['registr_balans']." кр.</font></b>";
							echo "<div id=registr_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['registr_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['registr_balans'][$k]);
							}
							unset($pers_balans_txt[registr_balans]);
							unset ($pers_balans[registr_balans]);

							echo "</table></div><br>";
						}

						//ремонтка
						if(isset($pers_balans['repair_balans']) || isset($pers_balans['repair_balans_e'])) {
							if (!isset($pers_balans['repair_balans_e'])) $pers_balans['repair_balans_e'] = 0;
							if (!isset($pers_balans['repair_balans'])) $pers_balans['repair_balans'] = 0;
							if (!isset($pers_balans['repair_balans_gold'])) $pers_balans['repair_balans_gold'] = 0;

							echo "<b><a onclick=\"showhide('repair_balans');\" href=\"javascript:Void();\">Баланс с Ремонтная мастерская:</a><font color=".($pers_balans['repair_balans'] >= 0 ? "green" : "red")."> ".$pers_balans['repair_balans']."</font> кр. <font color=".($pers_balans['repair_balans_e'] >= 0 ? "green" : "red").">".$pers_balans['repair_balans_e']." екр.</font> <font color=".($pers_balans['repair_balans_gold'] >= 0 ? "green" : "red").">".$pers_balans['repair_balans_gold']." монет.</font></b>";
							echo "<div id=repair_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['repair_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['repair_balans'][$k]);
							}
							unset($pers_balans_txt['repair_balans']);
							unset ($pers_balans['repair_balans']);

							echo "</table></div><br>";
						}

						//eshop
						if(isset($pers_balans['eshop_balans']))	{
							echo "<b><a onclick=\"showhide('eshop_balans');\" href=\"javascript:Void();\">Баланс с Березка:</a><font color=".($pers_balans['eshop_balans'] >= 0 ? "green" : "red")."> ".$pers_balans['eshop_balans']." екр.</font></b>";
							echo "<div id=eshop_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['eshop_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['eshop_balans'][$k]);
							}
							unset($pers_balans_txt['eshop_balans']);
							unset ($pers_balans['eshop_balans']);

							echo "</table></div><br>";
						}
						// цветочка
						if(isset($pers_balans['fshop_balans'])) {
							echo "<b><a onclick=\"showhide('fshop_balans');\" href=\"javascript:Void();\">Баланс с Цветочный магазин:</a></b><font color=".($pers_balans['fshop_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['fshop_balans'])."</font> <b>кр.</b>";
							echo "<div id=fshop_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['fshop_balans'] as $k=>$v)
							{
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['fshop_balans'][$k]);
							}
							unset($pers_balans_txt['fshop_balans']);
							unset($pers_balans['fshop_balans']);
							echo "</table></div><br>";
						}


						if(isset($pers_balans['bank_balans']) || isset($pers_balans['bank_balans_r']) || isset($pers_balans['bank_balans_e'])) {

							if (!isset($pers_balans['bank_balans'])) $pers_balans['bank_balans'] = 0;
							if (!isset($pers_balans['bank_balans_r'])) $pers_balans['bank_balans_r'] = 0;
							if (!isset($pers_balans['bank_balans_e'])) $pers_balans['bank_balans_e'] = 0;

							echo "<b><a onclick=\"showhide('bank_balans');\" href=\"javascript:Void();\">Баланс с Банк:</a><font color=".($pers_balans['bank_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['bank_balans'])."</font> кр. <font color=".($pers_balans['bank_balans_e'] >= 0 ? "green" : "red").">".($pers_balans['bank_balans_e'])."</font> екр. <font color=".($pers_balans['bank_balans_r'] >= 0 ? "green" : "red").">".($pers_balans['bank_balans_r'])."</font> реп.</b>";
							echo "<div id=bank_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['bank_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['bank_balans'][$k]);
							}
							unset($pers_balans_txt['bank_balans']);
							unset ($pers_balans['bank_balans']);

							echo "</table></div><br>";
						}


						if(isset($pers_balans['laba_balans']) || isset($pers_balans['laba_balans_kr'])) {
							if (!isset($pers_balans['laba_balans'])) $pers_balans['laba_balans'] = 0;
							if (!isset($pers_balans['laba_balans_kr'])) $pers_balans['laba_balans_kr'] = 0;

							echo "<b><a onclick=\"showhide('laba_balans');\" href=\"javascript:Void();\">Баланс с Лабиринт:</a> <font color=".($pers_balans['laba_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['laba_balans'])." реп. </font> <font color=".($pers_balans['laba_balans_kr'] >= 0 ? "green" : "red")."> ".($pers_balans['laba_balans_kr'])." кр. </font></b>";
							echo "<div id=laba_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['laba_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['laba_balans'][$k]);
							}
							unset($pers_balans_txt['laba_balans']);
							unset ($pers_balans['laba_balans']);

							echo "</table></div><br>";
						}

						if((isset($pers_balans['zagorod_balans_rep'])) or (isset($pers_balans['zagorod_balans_get'])) or (isset($pers_balans['zagorod_balans_fin']))) {
							echo "<b><a onclick=\"showhide('zagorod_balans');\" href=\"javascript:Void();\">Баланс с Квесты загорода:</a><font color=".($pers_balans['zagorod_balans_rep'] >= 0 ? "green" : "red")."> ".($pers_balans['zagorod_balans_rep'])." реп. </b></font>, <font color=green> Квестов взято/окончено ".$pers_balans['zagorod_balans_get']." / ".$pers_balans['zagorod_balans_fin']."</b></font>";
							echo "<div id=zagorod_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['zagorod_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['zagorod_balans'][$k]);
							}
							unset($pers_balans_txt['zagorod_balans']);
							unset($pers_balans['zagorod_balans']);

							unset($pers_balans_txt['0:Квесты загорода']);
							unset($pers_balans_txt['0:Квесты']);

							echo "</table></div><br>";
						}

						if(isset($pers_balans['zagorodloot_balans'])) {
							echo "<b><a onclick=\"showhide('zagorodloot_balans');\" href=\"javascript:Void();\">Баланс с Грабежи загорода:</a><font color=".($pers_balans['zagorodloot_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['zagorodloot_balans'])." кр. </font></b>";
							echo "<div id=zagorodloot_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['zagorodloot_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['zagorodloot_balans'][$k]);
							}
							unset($pers_balans_txt['zagorodloot_balans']);
							unset($pers_balans['zagorodloot_balans']);

							echo "</table></div><br>";
						}

						if(isset($pers_balans['zagorodmage_balans'])) {
							echo "<b><a onclick=\"showhide('zagorodmage_balans');\" href=\"javascript:Void();\">Баланс с Магом по рунам:</a><font color=".($pers_balans['zagorodmage_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['zagorodmage_balans'])." реп. </font></b>";
							echo "<div id=zagorodmage_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['zagorodmage_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['zagorodmage_balans'][$k]);
							}
							unset($pers_balans_txt['zagorodmage_balans']);
							unset($pers_balans['zagorodmage_balans']);

							echo "</table></div><br>";
						}

						if(isset($pers_balans['fair_balans'])) {
							echo "<b><a onclick=\"showhide('quest_balans');\" href=\"javascript:Void();\">Баланс с Ярмаркой:</a><font color=".($pers_balans['fair_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['fair_balans'])."</font> монет.</b>";
							echo "<div id=quest_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['fair_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['fair_balans'][$k]);
							}
							unset($pers_balans_txt['fair_balans']);
							unset ($pers_balans['fair_balans']);

							echo "</table></div><br>";
						}

						if(isset($pers_balans['quest_balans']))
						{
							echo "<b><a onclick=\"showhide('quest_balans');\" href=\"javascript:Void();\">Баланс с Квесты Храмовые:</a><font color=".($pers_balans['quest_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['quest_balans'])."</font> реп.</b>";
							echo "<div id=quest_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['quest_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['quest_balans'][$k]);
							}
							unset($pers_balans_txt['quest_balans']);
							unset ($pers_balans['quest_balans']);

							echo "</table></div><br>";
						}

						if(isset($pers_balans['quest_balans2']) || isset($pers_balans['quest_balans2_kr']) || isset($pers_balans['quest_balans2_ekr']) || isset($pers_balans['quest_balans2_rep'])) {

							if (!isset($pers_balans['quest_balans2_kr'])) $pers_balans['quest_balans2_kr'] = 0;
							if (!isset($pers_balans['quest_balans2_ekr'])) $pers_balans['quest_balans2_ekr'] = 0;
							if (!isset($pers_balans['quest_balans2_rep'])) $pers_balans['quest_balans2_rep'] = 0;

							echo "<b><a onclick=\"showhide('quest_balans2');\" href=\"javascript:Void();\">Баланс с Квесты:</a> <font color=".($pers_balans['quest_balans2_kr'] >= 0 ? "green" : "red")."> ".($pers_balans['quest_balans2_kr'])."</font> кр. <font color=".($pers_balans['quest_balans2_ekr'] >= 0 ? "green" : "red")."> ".($pers_balans['quest_balans2_ekr'])."</font> екр. <font color=".($pers_balans['quest_balans2_rep'] >= 0 ? "green" : "red")."> ".($pers_balans['quest_balans2_rep'])."</font> реп.</b>";
							echo "<div id=quest_balans2 style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['quest_balans2'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['quest_balans2'][$k]);
							}
							unset($pers_balans_txt['quest_balans2']);
							unset($pers_balans['quest_balans2']);

							echo "</table></div><br>";
						}


						if((isset($pers_balans['znahar_balans']) || isset($pers_balans['znahar_balans_e'])) && isset($pers_balans_txt['znahar_balans'])) {
							if (!isset($pers_balans['znahar_balans'])) $pers_balans['znahar_balans'] = 0;
							if (!isset($pers_balans['znahar_balans_e'])) $pers_balans['znahar_balans_e'] = 0;

							echo "<b><a onclick=\"showhide('znahar_balans');\" href=\"javascript:Void();\">Баланс с Знахарь:</a> <font color=".($pers_balans['znahar_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['znahar_balans'])."</font> кр. <font color=".($pers_balans['znahar_balans_e'] >= 0 ? "green" : "red")."> ".($pers_balans['znahar_balans_e'])."</font> екр. </b>";
							echo "<div id=znahar_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['znahar_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['znahar_balans'][$k]);
							}
							unset($pers_balans_txt['znahar_balans']);
							unset ($pers_balans['znahar_balans']);

							echo "</table></div><br>";
						}

						if(isset($pers_balans['fontan_balans'])) {
							echo "<b><a onclick=\"showhide('fontan_balans');\" href=\"javascript:Void();\">Баланс с Фонтан Удачи: </a><font color=".($pers_balans['fontan_balans'] >0 ? "green>":"red>").$pers_balans['fontan_balans']."</font> кр. </b>";
							echo "<div id=fontan_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['fontan_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['fontan_balans'][$k]);
							}
							unset($pers_balans_txt['fontan_balans']);
							unset($pers_balans['fontan_balans']);
							unset($pers_balans_txt['0:Фонтан Удачи']);
							echo "</table></div><br>";
						}

						if(isset($pers_balans['prokat_balans'])) {
							echo "<b><a onclick=\"showhide('prokat_balans');\" href=\"javascript:Void();\">Баланс с Прокатной лавкой:</a><font color=".($pers_balans['prokat_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['prokat_balans'])."</font> екр.</b>";
							echo "<div id=prokat_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['prokat_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['prokat_balans'][$k]);
							}
							unset($pers_balans_txt['prokat_balans']);
							unset ($pers_balans['prokat_balans']);
							unset($pers_balans_txt['0:Прокатная лавка']);
							echo "</table></div><br>";
						}


						if(isset($pers_balans['skupsh_balans'])) {
							echo "<b><a onclick=\"showhide('skupsh_balans');\" href=\"javascript:Void();\">Баланс с Скупщик :</a><font color=".($pers_balans['skupsh_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['skupsh_balans'])."</font> кр.</b>";
							echo "<div id=skupsh_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['skupsh_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['skupsh_balans'][$k]);
							}
							unset($pers_balans_txt['skupsh_balans']);
							unset($pers_balans['skupsh_balans']);
							unset($pers_balans_txt['0:Скупщик']);
							echo "</table></div><br>";
						}


						if(isset($pers_balans['star_balans']))
						{
							echo "<b><a onclick=\"showhide('star_balans');\" href=\"javascript:Void();\">Баланс с Старьевщик: </a> <font color=".($pers_balans['star_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['star_balans'])."</font> кр.</b>";
							echo "<div id=star_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['star_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['star_balans'][$k]);
							}
							unset($pers_balans_txt['star_balans']);
							unset ($pers_balans['star_balans']);

							echo "</table></div><br>";
						}

						if(isset($pers_balans['komok_balans'])) {
							echo "<b><a onclick=\"showhide('komok_balans');\" href=\"javascript:Void();\">Баланс с Комисионный магазин: </a> <font color=".($pers_balans['komok_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['komok_balans'])."</font> кр.</b>";
							echo "<div id=komok_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['komok_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['komok_balans'][$k]);
							}
							unset($pers_balans_txt['komok_balans']);
							unset($pers_balans['komok_balans']);

							echo "</table></div><br>";
						}

						if(isset($pers_balans['kazna_balans']))	{
							while(list($kaa,$vaa) = each($pers_balans['kazna_balans'])) {
								echo "<b><a onclick=\"showhide('kazna_balans".$kaa."');\" href=\"javascript:Void();\">Баланс с Клановой казной ".$kaa.":</a><font color=".($pers_balans['kazna_balans'][$kaa] >= 0 ? "green" : "red")."> ".($pers_balans['kazna_balans'][$kaa])."</font> кр.</b>";
								echo "<div id=\"kazna_balans".$kaa."\" style=\"display:none;\"><table>";

								foreach ($pers_balans_txt['kazna_balans'][$kaa] as $k=>$v) {
									echo '<tr><td>'.$v['delo'].'</td>';
									echo '</tr>';
									unset($pers_balans_txt['kazna_balans'][$kaa][$k]);
								}
								echo "</table></div><br>";
							}

							unset($pers_balans_txt['kazna_balans']);
							unset($pers_balans['kazna_balans']);

						}

						if(isset($pers_balans['ars_balans'])) {
							while(list($kaa,$vaa) = each($pers_balans['ars_balans'])) {
								echo "<b><a onclick=\"showhide('ars_balans".$kaa."');\" href=\"javascript:Void();\">Баланс с арсеналом ".$kaa.":</a><font color=".($pers_balans['ars_balans'][$kaa] >= 0 ? "green" : "red")."> ".($pers_balans['ars_balans'][$kaa])."</font> кр.</b>";
								echo "<div id=\"ars_balans".$kaa."\" style=\"display:none;\"><table>";

								foreach ($pers_balans_txt['ars_balans'][$kaa] as $k=>$v) {
									echo '<tr><td>'.$v['delo'].'</td><td>'.$pers_sebes[$k].'</td></tr>';
									echo '</tr>';
									unset($pers_balans_txt['ars_balans'][$kaa][$k]);
								}
								echo "</table></div><br>";
							}

							unset($pers_balans_txt['ars_balans']);
							unset($pers_balans['ars_balans']);

						}


						if((isset($pers_balans['ruines_balans']) || isset($pers_balans['ruines_balans_e']) || isset($pers_balans['ruines_balans_r'])) && isset($pers_balans_txt['ruines_balans'])) {
							if (!isset($pers_balans['ruines_balans'])) $pers_balans['ruines_balans'] = 0;
							if (!isset($pers_balans['ruines_balans_e'])) $pers_balans['ruines_balans_e'] = 0;
							if (!isset($pers_balans['ruines_balans_r'])) $pers_balans['ruines_balans_r'] = 0;

							echo "<b><a onclick=\"showhide('ruines_balans');\" href=\"javascript:Void();\">Баланс с Руинами:</a> <font color=".($pers_balans['ruines_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['ruines_balans'])."</font> кр. <font color=".($pers_balans['ruines_balans_e'] >= 0 ? "green" : "red")."> ".($pers_balans['ruines_balans_e'])."</font> екр. <font color=".($pers_balans['ruines_balans_r'] >= 0 ? "green" : "red")."> ".($pers_balans['ruines_balans_r'])."</font> реп.</b>";
							echo "<div id=ruines_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['ruines_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['ruines_balans'][$k]);
							}
							unset($pers_balans_txt['ruines_balans']);
							unset($pers_balans['ruines_balans']);

							echo "</table></div><br>";
						}


						if(isset($pers_balans['rist_balans']) || isset($pers_balans['rist_balans_r']))
						{
							if (!isset($pers_balans['rist_balans'])) $pers_balans['rist_balans'] = 0;
							if (!isset($pers_balans['rist_balans_r'])) $pers_balans['rist_balans_r'] = 0;

							echo "<b><a onclick=\"showhide('rist_balans');\" href=\"javascript:Void();\">Баланс с Ристалище Одиночки:</a><font color=".($pers_balans['rist_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['rist_balans'])."</font> кр. <font color=".($pers_balans['rist_balans_r'] >= 0 ? "green" : "red")."> ".($pers_balans['rist_balans_r'])."</font> реп.</b>";
							echo "<div id=rist_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['rist_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['rist_balans'][$k]);
							}
							unset($pers_balans_txt['rist_balans']);
							unset($pers_balans['rist_balans']);
							echo "</table></div><br>";
						}


						if(isset($pers_balans['rist2_balans']) || isset($pers_balans['rist2_balans_r']) || isset($pers_balans['rist2_balans_ekr'])) {
							if (!isset($pers_balans['rist2_balans'])) $pers_balans['rist2_balans'] = 0;
							if (!isset($pers_balans['rist2_balans_r'])) $pers_balans['rist2_balans_r'] = 0;
							if (!isset($pers_balans['rist2_balans_ekr'])) $pers_balans['rist2_balans_ekr'] = 0;

							echo "<b><a onclick=\"showhide('rist2_balans');\" href=\"javascript:Void();\">Баланс с Ристалище Групповые:</a><font color=".($pers_balans['rist2_balans'] >= 0 ? "green" : "red")."> ".($pers_balans['rist2_balans'])."</font> кр. <font color=".($pers_balans['rist2_balans_r'] >= 0 ? "green" : "red")."> ".($pers_balans['rist2_balans_r'])."</font> реп.</b> <font color=".($pers_balans['rist2_balans_ekr'] >= 0 ? "green" : "red")."> ".($pers_balans['rist2_balans_ekr'])."</font> екр</b>";
							echo "<div id=rist2_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['rist2_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['rist2_balans'][$k]);
							}
							unset($pers_balans_txt['rist2_balans']);
							unset($pers_balans['rist2_balans']);
							echo "</table></div><br>";
						}

						if(isset($pers_balans_txt['ko_balans']))
						{
							echo "<b><a onclick=\"showhide('ko_balans');\" href=\"javascript:Void();\">Баланс с КО </a></b>";
							echo "<div id=ko_balans style=\"display:none;\"><table>";

							foreach ($pers_balans_txt['ko_balans'] as $k=>$v) {
								echo '<tr><td>'.$v['delo'].'</td>';
								echo '</tr>';
								unset($pers_balans_txt['ko_balans'][$k]);
							}
							unset($pers_balans_txt['ko_balans']);
							unset($pers_balans['ko_balans']);

							echo "</table></div><br>";
						}


						echo "<hr>";

		                                //$count
						if (is_array($pers_balans) && count($pers_balans)) {
							reset($pers_balans);
							foreach ($pers_balans as $pers_target=>$count) {								$sorted_pers_balans[$pers_target]=abs($count);
							}

							arsort($sorted_pers_balans, SORT_NUMERIC);
						} else {
							$sorted_pers_balans = array();
						}

		                                $i=1;
		                                $log_for_delo=array();

						foreach ($sorted_pers_balans as $pers_target=>$count) {
							$pers_inf=explode(':',$pers_target);

							if($pers_inf[0]>0 && $pers_inf[0]!=182783 && $pers_inf[0]!=8540 && $pers_inf[0]!=28453 && $pers_inf[0]!=326 && $pers_inf[0]!=14897 && $pers_inf[0]!=102904 && $pers_inf[0]!=30967 && $pers_inf[0]!=684792) {
								if($pers_balans[$pers_target]>0) {									$color='green';								} elseif($pers_balans[$pers_target]<0) {									$color='red';								} else {									$color='black';								}

								echo "<font color=".$color."><b>
								<a onclick=\"showhide('".$i."');\" href=\"javascript:Void();\">".$target_logins[$pers_inf[0]]."</a>
								<a target='_blank' href='http://capitalcity.oldbk.com/inf.php?".$pers_inf[0]."'><img src='http://i.oldbk.com/i/inf.gif'></a> : ".$pers_balans[$pers_target]."кр.</b></font>";

								if (isset($pers_balans_gold[$pers_target]))
									{
										if($pers_balans_gold[$pers_target]>0) {
											$gcolor='green';
										} elseif($pers_balans_gold[$pers_target]<0) {
											$gcolor='red';
										} else {
											$gcolor='black';
										}
										echo " <font color=".$gcolor."><b>".$pers_balans_gold[$pers_target]." монет.</b></font>";
									}


								$dvp_print='';

								if($pers_balans[$pers_target]>0) {
									$log_for_delo_pl[$target_logins[$pers_inf[0]]]=$pers_balans[$pers_target];
								} elseif($pers_balans[$pers_target]<0) {
									$log_for_delo_mn[$target_logins[$pers_inf[0]]]=$pers_balans[$pers_target];
								}

								foreach($dvp as $lvl => $krval) {									if($krval<abs($pers_balans[$pers_target])) {										$dvp_print.=$lvl.',';									}								}

								if($dvp_print != '') {									$dvp_print=substr($dvp_print,0,-1);
									echo " (Превышено ДВП для ".$dvp_print." ур.)";								}
								echo "<br>";
								unset($count);
								echo "<div id=".$i." style=\"display:none;\"><table border=1>";

								foreach($pers_balans_txt[$pers_target] as $k=>$v) {									if (!isset($v['saldo'])) $v['saldo'] = "";									if (!isset($v['analiz'])) $v['analiz'] = "";									if (!isset($v['delo'])) $v['delo'] = "";									echo '<tr><td>'.$v['delo'].'</td>';
									echo '<td width=100 align=middle valign=top><B>'.$v['sebes'].'</b></td>';
									echo '<td align=left valign=top>'.$v['analiz'].'</td>';
									echo '<td valign=top>&nbsp;&nbsp;<b>'.$v['saldo'].'</b>&nbsp;&nbsp;</td>';
									echo '</tr>';
								}
								unset($pers_balans_txt[$pers_target]);
								unset($sorted_pers_balans[$pers_target]);
								unset($pers_balans[$pers_target]);
								echo "</table></div>";
							}
							$i++;
						}
					}
				}
			}
		}
	}

	if(isset($_GET['analiz']) && $_GET['analiz'] == 1) {
		echo 'Остальные переводы персонажа: <br>';

		if (is_array($pers_balans_txt) && count($pers_balans_txt)) {
			reset($pers_balans_txt);
			foreach ($pers_balans_txt as $k=>$v) {
				$other_inf=explode(':',$k);
				if (!isset($other_inf[1])) $other_inf[1] = "";
				$other_inf[1]=(strlen($other_inf[1])>0?$other_inf[1]:'ПУСТО, Искать и заполнять');

				if ($other_inf[1] == "бой") $other_inf[1] = "Предметы, полученные в боях";
				echo "<a onclick=\"showhide('".$i."');\" href=\"javascript:Void();\"><b>".$other_inf[1]."</b></a><br> ";
				echo "<div id=".$i." style=\"display:none;\">";

				foreach($v as $vv=>$kk) {
					echo $kk['delo']."<br>";
				}
				echo "</div>";
				$i++;
			}
		}
	}
} elseif($access['item_hist'] == 1 && $_GET['sh'] == 3) {	if($_GET['item_hist']) { $_GET['item_log']=1;}

	?>

	<script>
	function BeforeHistSubmit() {
		if (document.getElementById("search_oldnew").checked || document.getElementById("search_oldnew2").checked) {
			document.getElementById("itemhistform").action = "http://archive.oldbk.com/perevod.php";
		}
		return true;
	}
	</script>

	<h4>История вещей после 1/12/2011</h4><br><br><br>
	<form onSubmit="return BeforeHistSubmit();" method=get id="itemhistform" action='http://capitalcity.oldbk.com/perevod/perevod.php'><input name='sh' type='hidden' value='3'>
	id вещи<input type=text name=item_hist value='<?=$_GET['item_hist'];?>'> <br>
	искать в старом деле(c 1/12/2011 по 24/04/2015): <input id='search_oldnew' name='search_oldnew' type='checkbox' value='ON' <?=(isset($_GET['search_oldnew'])?'checked':'')?>> <br>
	искать в старом деле(c 24/04/2015 по 08/12/2016): <input id='search_oldnew2' name='search_oldnew2' type='checkbox' value='ON' <?=(isset($_GET['search_oldnew2'])?'checked':'')?>> <br>
	<input type=submit name=item_log value='Просмотр'><br>
	</form>

	<?php
      	if(isset($_GET['item_hist']) && isset($_GET['item_log'])) {
		if (isset($_GET['search_oldnew'])) {
			$dd = 1;
		} elseif (isset($_GET['search_oldnew2'])) {
			$dd = 2;
		} else {
			$dd = 0;
		}

		$i = 0;		$alllist = array();
		$data=mysql_query("SELECT nd.* FROM `{$table_delo_tx[$dd]}` ndii            	left join `{$table_delo[$dd]}` nd            	on ndii.delo_id=nd.id            	WHERE ndii.item_id = '".$_GET['item_hist']."';") or die();		$type1315 = "";			$last1315 = "";                while($row=mysql_fetch_assoc($data)) {			$alllist[] = $row;			if ($row['type'] == 1315) {				$t = explode(":",$row['add_info']);				$type1315 = $t[1];				$last1315 = $t[1].":".$t[2];			}		}// поиск обмена до
		while($type1315 != "")
		{
			$i++;
			if ($i > 20) break;
		$data=mysql_query("SELECT nd.* FROM `{$table_delo_tx[$dd]}` ndii		            	left join `{$table_delo[$dd]}` nd		            	on ndii.delo_id=nd.id		            	WHERE ndii.item_id = '".$type1315."';");			$type1315 = "";	                while($row=mysql_fetch_assoc($data)) {				$alllist[] = $row;				if ($row['type'] == 1315) {					$t = explode(":",$row['add_info']);					$type1315 = $t[1];					$last1315 = $t[1].":".$t[2];				}			}		}

		// поиск цепочки после
		$after1315 = $_GET['item_hist'];

		$i = 0;
		while($after1315 != "") {
			$i++;
			if ($i > 20) break;

			$q = mysql_query('SELECT * FROM `'.$table_delo[$dd].'` WHERE type = 1315 and add_info LIKE "Обмен уникального предмета:'.$after1315.':%"') or die();
			if (mysql_num_rows($q) == 0) break;

			while($rowdata = mysql_fetch_assoc($q)) {

	        	    	$data=mysql_query("SELECT nd.* FROM `{$table_delo_tx[$dd]}` ndii
				            	left join `{$table_delo[$dd]}` nd
				            	on ndii.delo_id=nd.id
				            	WHERE ndii.item_id = '".$rowdata['item_id']."';");


		                while($row=mysql_fetch_assoc($data)) {
					$alllist[] = $row;
				}

				$after1315 = $rowdata['item_id'];
			}
		}


		function cmpsdate($a, $b) {
	    		if ($a['sdate'] == $b['sdate']) {
	        		return 0;
	    		}
	    		return ($a['sdate'] < $b['sdate']) ? -1 : 1;
		}

		uasort($alllist, 'cmpsdate');

		reset($alllist);
                while(list($k,$row) = each($alllist)) {
			$d_out=get_delo_rec($row,$access,'',0);
			$l=strlen($d_out);
			if($l>250) {
				$d_out=wordwrap($d_out, 150, "\n", 1);
			}
                	echo $d_out.'<br>';
                }
	}
}

if(isset($_GET['analiz']) && $_GET['analiz'] == 1) {
        echo '<hr>';
        echo "Фин. прокачка от: ";
	if (is_array($log_for_delo_pl) && count($log_for_delo_pl)) {
	        foreach($log_for_delo_pl as $nick=>$val) {
	        	 echo $nick. " (".$val."кр.); ";
	        }
	}

        echo '<hr>';
        echo "Фин. прокачка к: ";
	if (is_array($log_for_delo_mn) && count($log_for_delo_mn)) {
	        foreach($log_for_delo_mn as $nick=>$val) {
	        	 echo $nick. " (".$val."кр.); ";
	        }
	}
}
?>
<br><br><br>
</body>
</html>
<?

if (isset($miniBB_gzipper_encoding)) {
	$miniBB_gzipper_in = ob_get_contents();
	$miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
	$miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
	$miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
	$miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
	$miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
	ob_clean();
	header('Content-Encoding: '.$miniBB_gzipper_encoding);
	echo $miniBB_gzipper_out;
}
?>
