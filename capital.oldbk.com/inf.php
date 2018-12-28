<?php



$GID=(int)$_SERVER['QUERY_STRING'];
 if ($GID>0)
 	{
 	$GLOGIN='';
 	}
 	else
 	{
 	 $GLOGIN=(htmlspecialchars($_GET['login']));
	}

function get_file_time($name)
{
	$filename='/www/cache/inf/'.$name.'.dat';
	$file_ok=file_exists($filename);
	$cache_time=10; // 3 sec

	if ($file_ok)
	    {
	    $file_stat=stat($filename);
	    $file_mk_time=$file_stat[9];
	    }

		if ( ($file_ok) and ($file_mk_time+$cache_time)>=time())
		{
		//файл есть и актуальный
		// выдаем его и все
			    if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
			    $miniBB_gzipper_encoding = 'x-gzip';
			    }
			    if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
			    $miniBB_gzipper_encoding = 'gzip';
			    }
		header('Content-Encoding: '.$miniBB_gzipper_encoding);
		print file_get_contents($filename);
		return true;
		}
return false;
}

function save_inf_tofile($data,$name)
{
$filename='/www/cache/inf/'.$name.'.dat';
	$fp = fopen ($filename,"w");
	flock ($fp,LOCK_EX);
	fputs($fp,$data);
	fflush ($fp);
	flock ($fp,LOCK_UN);
	fclose ($fp);
}

function echo_history($user)
{
	echo  '<div style="margin-top:10px;margin-left:3px;margin-bottom:10px;display:none;" id="inftab2">
	<H3>Хроника персонажа</H3>';


		$get_hist=mysql_query("select * from oldbk.users_history h where owner='{$user['id']}' order by hdate desc");
		$cnt=0;
		$open_other=false;

		$stype[1]='Вступление в клан';
		$stype[2]='Выход из клана';
		$stype[3]='Смена склонности:';
		$stype[4]='Взятие уровня:';


     /*взятие уровня ДД.ММ.ГГГГ Персонаж НИК перешел на Х уровень
     вступление в клан ДД.ММ.ГГГГ Персонаж НИК вступил в клан [склонка] [значок] КЛИКАБЕЛЬНОЕ_НАЗВАНИЕ_КЛАНА
     выход из клана ДД.ММ.ГГГГ Персонаж НИК вышел из клана [склонка] [значок] КЛИКАБЕЛЬНОЕ_НАЗВАНИЕ_КЛАНА
     смена склонности ДД.ММ.ГГГГ Персонаж НИК сменил склонность на [склонка]
     */

		 while($hrow=mysql_fetch_array($get_hist))
		 	{
			$cnt++;

			echo "<span class=date>".date("d.m.Y H:i",strtotime($hrow['hdate']))."</span><small> ".$stype[$hrow['itype']];

			if ($hrow['halign']==(int)$hrow['halign'])
				{
				$hrow['halign']=(int)$hrow['halign'];
				}

				if (($hrow['itype']==1) OR ($hrow['itype']==2))
						{
						if ($hrow['hclan']=='pal') { $hrow['halign']='1.1'; }
						echo " <img src=http://i.oldbk.com/i/align_{$hrow['halign']}.gif border=0> ";
						//echo "<img src=http://i.oldbk.com/i/klan/{$hrow['hclan']}.gif border=0>";
						//echo "<img src=http://i.oldbk.com/i/klan/{$hrow['hclan']}.gif border=0 onerror = \"this.style.display = 'none'\" >";
						echo "<img src=http://i.oldbk.com/i/klan/{$hrow['hclan']}.gif border=0 class=\"can-hidde\" >";



						echo "<a href='https://oldbk.com/encicl/clans.html?clan={$hrow['hclan']}' target=_blank>{$hrow['hclan']}</a>";
						}
				elseif ($hrow['itype']==3)
						{
						 if ($hrow['halign']==0)
						 	{
							echo " персонаж стал без склонности";
						 	}
						 	elseif ($hrow['halign']!=4)
						 	{
							echo " персонаж сменил склонность на <img src=http://i.oldbk.com/i/align_{$hrow['halign']}.gif border=0>";
							}
						}
				elseif ($hrow['itype']==4)
						{

							echo " персонаж перешел на {$hrow['hlevel']} уровень";
						}

			echo "</small><br>";

				if ($cnt==21)
				{
				echo "<a onclick=\"showhide('other_hist');\" href=\"javascript:Void();\">Показать все</a><br>";
				echo '<div style="display:none;" id="other_hist">';
				}

		 	}

		 	if ($open_other)
		 		{
		 		echo "</div>";
		 		}


	echo '</div>';
}

$REAL_LOGIN=$_GET['login'];
//////////////////////////////
session_start();
include "connect.php";
include "functions.php";
$need_to_save=false;
$_user_all_privilege = array(546433, 6745,684792);


use GeoIp2\Database\Reader;

if ($user['klan']=='Adminion' || $user['klan']=='radminion' || $user['klan']=='pal' || ($user['align'] > '2' && $user['align'] < '3') || ($user['align'] > '1' && $user['align'] < '2')  )
{
// без кеширования
}
else
{
//кешируем
	if ($GID>0)
	{
		if (get_file_time($GID)==true)
			{
			die();
			}
			else
			{
			$need_to_save=true;
			}
	}
	else
	{
		if (get_file_time($GLOGIN)==true)
			{
			die();
			}
			else
			{
			$need_to_save=true;
			}
	}
}
unset($user);
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


//if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die();}

$location[0]='capitalcity';
$location[1]='avaloncity';
$location[2]='angelscity';

/*
$_SESSION['looo_count']=$_SESSION['looo_count']+1; // считаем просмотры

if ($_SESSION['looo_time'] < (time()-600) )
	{
	$_SESSION['looo_count']=0; // сброс в 0
	}
*/
 if (!($_SESSION['loookinfa'] >0)) {
				   $_SESSION['loookinfa']=time();
 				   }
 	else if ($_SESSION['loookinfa']==time())
 				   {
 				   die("AntiDDOS...refresh page");
 				   }
 				   else
 				   {
 				   $_SESSION['loookinfa']=time();
 				   }

$own=mysql_fetch_array(mysql_query("SELECT `id`,`align`, `klan`,`login` FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));

if (isset($_GET['showscan']) || isset($_GET['approvescan']) || isset($_GET['rejectscan']) || isset($_GET['deletescan'])) {
	if(($own['klan'] == 'Adminion' || $own['klan']=='radminion') )
	{
		if (isset($_GET['showscan'])) {
			$q = mysql_query('SELECT * FROM oldbk.users_scans WHERE id = '.intval($_GET['showscan']));
			$q = mysql_fetch_assoc($q);
			if ($q !== FALSE) {
				header('Content-Type: image/jpeg');
				$img = imagecreatefromstring(file_get_contents('http://i.oldbk.com/i/24b789yuwmsotfigj8yw54iustjqw45ys/'.$q['filename']));
				imagejpeg($img);
				imagedestroy($img);
				die();
			}
		}
		if(($own['klan'] == 'Adminion' || $own['klan']=='radminion') && isset($_GET['deletescan'])) {
			$q = mysql_query('SELECT * FROM oldbk.users_scans WHERE id = '.intval($_GET['deletescan']));
			$q = mysql_fetch_assoc($q);
			if ($q !== FALSE) {
				mysql_query('DELETE FROM oldbk.users_scans WHERE id = '.intval($_GET['deletescan']));
				require_once('./clouddndtrack/cloud_api.php');
				CloudDelete('oldbkstatic','i/24b789yuwmsotfigj8yw54iustjqw45ys/',$q['filename']);
				$_SESSION['loookinfa']=time()-1;
				header('Location: '.$_SERVER['PHP_SELF'].'?'.$q['owner']);
				die();
			}
		}
		if (isset($_GET['rejectscan'])) {
			$q = mysql_query('SELECT * FROM oldbk.users_scans WHERE id = '.intval($_GET['rejectscan']));
			$q = mysql_fetch_assoc($q);
			if ($q !== FALSE) {
				mysql_query('UPDATE oldbk.users_scans SET status = 2 WHERE id = '.intval($_GET['rejectscan']));
				$mess = "Скан отклонён. Отклонил: ".$own['login'];
				mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$q['owner']."','$mess','".time()."');");

				if (isset($_POST['mess'])) {
					$qa = mysql_query('SELECT * FROM users WHERE id = '.$q['owner']);
					$telo = mysql_fetch_assoc($qa);
					if ($telo !== FALSE) {
						if ($telo['odate'] > (time()-60)) {
							addchp('<font color=red>Внимание!</font> Ваш скан отклонен. Причина: '.htmlspecialchars($_POST['mess']).'.','{[]}'.$telo['login'].'{[]}',$telo['room'],$telo['id_city']);
						} else {
							mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$telo['id']."','','<font color=red>Внимание!</font> Ваш скан отклонен. Причина: ".htmlspecialchars($_POST['mess']).".');");
						}
					}
				}

				$_SESSION['loookinfa']=time()-1;
				header('Location: '.$_SERVER['PHP_SELF'].'?'.$q['owner']);
				die();
			}
		}
		if (isset($_GET['approvescan'])) {
			$q = mysql_query('SELECT * FROM oldbk.users_scans WHERE id = '.intval($_GET['approvescan']));
			$q = mysql_fetch_assoc($q);
			if ($q !== FALSE) {
				mysql_query('UPDATE oldbk.users_scans SET status = 1 WHERE id = '.intval($_GET['approvescan']));
				$mess = "Данные скана паспорта совпадают с датой рождения персонажа. Утвердил: ".$own['login'];
				mysql_query("INSERT INTO oldbk.`lichka`(`id`,`pers`,`text`,`date`) VALUES ('','".$q['owner']."','$mess','".time()."');");

				$qa = mysql_query('SELECT * FROM users WHERE id = '.$q['owner']);
				$telo = mysql_fetch_assoc($qa);
				if ($telo !== FALSE) {
					if ($telo['odate'] > (time()-60)) {
						addchp('<font color=red>Внимание!</font> Ваш скан подтверждён. ','{[]}'.$telo['login'].'{[]}',$telo['room'],$telo['id_city']);
					} else {
						mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$telo['id']."','','<font color=red>Внимание!</font> Ваш скан подтвержден.');");
					}
				}

				$_SESSION['loookinfa']=time()-1;
				header('Location: '.$_SERVER['PHP_SELF'].'?'.$q['owner']);
				die();
			}
		}
	}
}


	if ( strpos($_GET['login'],"Невидимка:" ) !== FALSE )
	{
		$hiddenid=explode(":",$_GET['login']);
		unset($_GET['login']);
		$_SERVER['QUERY_STRING']=(int)$hiddenid[1];
	}


	if ($_GET['login']) {
		$ary[] = "ASCII";
		$ary[] = "UTF-8";
		$ary[] = "windows-1251";

		$mb = mb_detect_encoding($REAL_LOGIN, $ary,true);
		if ($mb == "UTF-8") {
			$nick = trim(iconv("UTF-8", "windows-1251", $REAL_LOGIN));
		} else {
			$nick = trim($_GET['login']);
		}



		$us = " `login` = '".mysql_real_escape_string($nick)."' ";
		$ss='login='.$nick;
	}
	else {
		$_SERVER['QUERY_STRING']=(int)$_SERVER['QUERY_STRING'];
		$us = " `id` = '{$_SERVER['QUERY_STRING']}' ";
		$ss=$_SERVER['QUERY_STRING'];
	}
	if($_SERVER['QUERY_STRING'] >  1000000000)
	{
	  if (($own['id']==14897) or ($own['id']==326) or ($own['id']==8540) or ($own['id']==102904) or ($own['id']==28453) or ($own['id']==684792)  || in_array($own['id'], $_user_all_privilege))
	   {
		   $us = " `hidden` = '{$_SERVER['QUERY_STRING']}' ";
		   $user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE {$us} LIMIT 1;"));
		   $_SERVER['QUERY_STRING']=$user[id];
		   $ss=$_SERVER['QUERY_STRING'];
	   }

	}
	else
	if($_SERVER['QUERY_STRING'] > _BOTSEPARATOR_)
	{
	    $bots= mysql_fetch_array(mysql_query("SELECT * FROM `users_clons` WHERE {$us} LIMIT 1;"));
	    $us = " `id` = '{$bots['id_user']}' ";

	    if (($own['id']==14897) or ($own['id']==326)  or ($own['id']==85407) or ($own['id']==102904) or ($own['id']==28453) or ($own['id']==8540) or ($own['id']==684792) || in_array($own['id'], $_user_all_privilege))
	  	{ 	$hihi='';   	}else {   	$hihi='and hidden=0'; 	   	}


	if ($bots['owner']==0)
	{
	    $user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE {$us} ".$hihi."  LIMIT 1;"));
	    $user[hp]=$bots[hp];
    	    $user[maxhp]=$bots[maxhp];
 	    $user[login]=$bots[login];
     	    $user[level]=$bots[level];
 	    $ss=$bots['id_user'];
 	  }
 	  else
 	  {
 	  $user=$bots;
 	  }

	    if  (
	        ((($user[id_city]==0) OR ($user[id_city]==null)) and ($_SERVER["SERVER_NAME"]=='capitalcity.oldbk.com') ) OR
		( ((($user[id_city]==1) OR ($user[id_city]==null)) AND ($user[id]>0)) AND ($_SERVER["SERVER_NAME"]=='avaloncity.oldbk.com') ) OR
		( ((($user[id_city]==2) OR ($user[id_city]==null)) AND ($user[id]>0)) AND ($_SERVER["SERVER_NAME"]=='angelscity.oldbk.com') )
		)

 	    {
 	    //этот город
 	    }
 	    else
 	    {
 	 //   header("Location: http://".$location[$user[id_city]].".oldbk.com/inf.php?".$bots['id_user']);
 	    $user[id_city]=(int)$user[id_city];
 	     if ($_GET[short]) {$ss.='&short=1';}
 	    header("Location: http://".$location[$user[id_city]].".oldbk.com/inf.php?".$ss);
 	    die("");
 	    }

	} else
	{

	 // определяем город
	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE {$us} LIMIT 1;"));

	   if ($user['id'] == 326) {
		$_SESSION['loookinfa'] = time() - 1;
		header('Location: inf.php?8540');
		die();
	   }

	   if  (
	        ((($user[id_city]==0) OR ($user[id_city]==null)) and ($_SERVER["SERVER_NAME"]=='capitalcity.oldbk.com') ) OR
		( ((($user[id_city]==1) OR ($user[id_city]==null)) AND ($user[id]>0)) AND ($_SERVER["SERVER_NAME"]=='avaloncity.oldbk.com') ) OR
		( ((($user[id_city]==2) OR ($user[id_city]==null)) AND ($user[id]>0)) AND ($_SERVER["SERVER_NAME"]=='angelscity.oldbk.com') )
		)
	    {
	    //этот город
	    }
	    else
	    {
	     	 // header("Location: http://".$location[$user[id_city]].".oldbk.com/inf.php?".$user[id]);
	     	  $user[id_city]=(int)$user[id_city];
	 	  if ($_GET[short]) {$ss.='&short=1';}
	     	  header("Location: http://".$location[$user[id_city]].".oldbk.com/inf.php?".$ss);
	 	  die();
	    }
	}

	if(($own['klan'] == 'Adminion' || $own['klan']=='radminion') )
		{

		if ($user['bot']>0)
			{
					if ($_GET['up']=='sila' || $_GET['up']=='lovk' || $_GET['up']=='inta' || $_GET['up']=='vinos' || $_GET['up']=='intel' || $_GET['up']=='modra' || $_GET['up']=='maxhp' || $_GET['up']=='level')
						{
						$SETparam=mysql_real_escape_string($_GET['up']);
						$add_val=1;
						$addsql='';
						if ($SETparam=='maxhp') {  $add_val=100; $addsql=' , hp=maxhp'; }

						mysql_query("UPDATE `oldbk`.`users` SET `{$SETparam}`= `{$SETparam}` + {$add_val} ".$addsql."  WHERE `id`='{$user['id']}' limit 1;");
						$user[$SETparam]+=$add_val;
						}
					elseif ($_GET['down']=='sila' || $_GET['down']=='lovk' || $_GET['down']=='inta' || $_GET['down']=='vinos' || $_GET['down']=='intel' || $_GET['down']=='modra' || $_GET['down']=='maxhp' || $_GET['down']=='level')
						{
						$SETparam=mysql_real_escape_string($_GET['down']);
						$add_val=1;
						$addsql='';
						if ($SETparam=='maxhp') {  $add_val=100; $addsql=' , hp=maxhp'; }
						mysql_query("UPDATE `oldbk`.`users` SET `{$SETparam}`= `{$SETparam}` - {$add_val} ".$addsql."  WHERE `id`='{$user['id']}' limit 1;");
						$user[$SETparam]-=$add_val;
						}
					elseif ($_GET['up']=='mfkrit' || $_GET['up']=='mfakrit' || $_GET['up']=='mfuvorot' || $_GET['up']=='mfauvorot' || $_GET['up']=='minu' || $_GET['up']=='maxu' || $_GET['up']=='bron1' || $_GET['up']=='bron2' || $_GET['up']=='bron3' || $_GET['up']=='bron4')
						{
						$add_val=100;
						$SETparam=mysql_real_escape_string($_GET['up']);
						if  ($_GET['up']=='bron1' || $_GET['up']=='bron2' || $_GET['up']=='bron3' || $_GET['up']=='bron4' ||  $_GET['up']=='minu' || $_GET['up']=='maxu' ) {  $add_val=10; }
						mysql_query("UPDATE `oldbk`.`inventory` SET `{$SETparam}`= `{$SETparam}`+ {$add_val}  WHERE `owner`='{$user['id']}' and dressed=1 limit 1 ;") ;
						}
					elseif ($_GET['down']=='mfkrit' || $_GET['down']=='mfakrit' || $_GET['down']=='mfuvorot' || $_GET['down']=='mfauvorot' || $_GET['down']=='minu' || $_GET['down']=='maxu' || $_GET['down']=='bron1' || $_GET['down']=='bron2' || $_GET['down']=='bron3' || $_GET['down']=='bron4')
						{

						$add_val=100;
						$SETparam=mysql_real_escape_string($_GET['down']);
						if  ($_GET['down']=='bron1' || $_GET['down']=='bron2' || $_GET['down']=='bron3' || $_GET['down']=='bron4' ||  $_GET['down']=='minu' || $_GET['down']=='maxu' ) {  $add_val=10; }
						mysql_query("UPDATE `oldbk`.`inventory` SET `{$SETparam}`= `{$SETparam}`- {$add_val}  WHERE `owner`='{$user['id']}' and dressed=1 limit 1 ;") ;
						}
			}
		}



	if (preg_match("/^(.*?)\.(.*?)\.(.*?) (.*?):(.*?) /",$user['palcom'],$mt)) {
			 $red_dat=$mt[2].".".$mt[1].".".$mt[3]." ".$mt[4].":".$mt[5];
			 $user['palcom']=$red_dat." ".preg_replace("/^(.*?)\.(.*?)\.(.*?) (.*?):(.*?) /","",$user['palcom']);
	}

	if ($bots[id] < _BOTSEPARATOR_ )
		{
		$_SERVER['QUERY_STRING'] = $user[0];
		}


	if( ($user[0] == null) OR ($user['id'] == 31109) OR ($user['id'] == 344995) OR ($user['id'] == 39935) OR ($user['id'] == 9227) )
		{
		?>
		<html><head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
		<meta http-equiv="Content-Language" content="ru">
		<script type="text/javascript" src="/i/globaljs.js"></script>
		<TITLE>Произошла ошибка</TITLE>
		<!-- Asynchronous Tracking GA top piece counter -->
<script type="text/javascript">

var _gaq = _gaq || [];

var rsrc = /mgd_src=(\d+)/ig.exec(document.URL);
    if(rsrc != null) {
        _gaq.push(['_setCustomVar', 1, 'mgd_src', rsrc[1], 2]);
    }

_gaq.push(['_setAccount', 'UA-17715832-1']);
_gaq.push(['_addOrganic', 'm.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'images.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'blogs.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'video.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'go.mail.ru', 'q']);
_gaq.push(['_addOrganic', 'm.go.mail.ru', 'q', true]);
_gaq.push(['_addOrganic', 'mail.ru', 'q']);
_gaq.push(['_addOrganic', 'google.com.ua', 'q']);
_gaq.push(['_addOrganic', 'images.google.ru', 'q', true]);
_gaq.push(['_addOrganic', 'maps.google.ru', 'q', true]);
_gaq.push(['_addOrganic', 'nova.rambler.ru', 'query']);
_gaq.push(['_addOrganic', 'm.rambler.ru', 'query', true]);
_gaq.push(['_addOrganic', 'gogo.ru', 'q']);
_gaq.push(['_addOrganic', 'nigma.ru', 's']);
_gaq.push(['_addOrganic', 'search.qip.ru', 'query']);
_gaq.push(['_addOrganic', 'webalta.ru', 'q']);
_gaq.push(['_addOrganic', 'sm.aport.ru', 'r']);
_gaq.push(['_addOrganic', 'akavita.by', 'z']);
_gaq.push(['_addOrganic', 'meta.ua', 'q']);
_gaq.push(['_addOrganic', 'search.bigmir.net', 'z']);
_gaq.push(['_addOrganic', 'search.tut.by', 'query']);
_gaq.push(['_addOrganic', 'all.by', 'query']);
_gaq.push(['_addOrganic', 'search.i.ua', 'q']);
_gaq.push(['_addOrganic', 'index.online.ua', 'q']);
_gaq.push(['_addOrganic', 'web20.a.ua', 'query']);
_gaq.push(['_addOrganic', 'search.ukr.net', 'search_query']);
_gaq.push(['_addOrganic', 'search.com.ua', 'q']);
_gaq.push(['_addOrganic', 'search.ua', 'q']);
_gaq.push(['_addOrganic', 'poisk.ru', 'text']);
_gaq.push(['_addOrganic', 'go.km.ru', 'sq']);
_gaq.push(['_addOrganic', 'liveinternet.ru', 'ask']);
_gaq.push(['_addOrganic', 'gde.ru', 'keywords']);
_gaq.push(['_addOrganic', 'affiliates.quintura.com', 'request']);
_gaq.push(['_trackPageview']);
_gaq.push(['_trackPageLoadTime']);
</script>
            <style>
                .big-dadge-image {
                    margin-bottom: 5px;
                }


            </style>
<!-- Asynchronous Tracking GA top piece end -->
		</HEAD><BODY text="#FFFFFF">
		<p><font color=black>

		Произошла ошибка: <pre>Персонаж <?=($_GET['login']?"\"".$nick."\"":"{$bots['prototype']}")?> не найден...</pre>
		<b><p><a href = "javascript:window.history.go(-1);">Назад</b></a>
		<HR>
		<p align="right">(c) <a href="http://oldbk.com">Бойцовский Клуб - ОлдБК</a></p>
		</body></html>
		<?
		die();
	}

	if($_GET['short']) {
		header('Location: http://api.oldbk.com/info?uid='.$user['id']);
		die();
	}

if (isset($_POST['savenotepad'],$_POST['note_text']) && ($own['klan'] == 'Adminion' || $own['klan']=='radminion' /*|| $own['id'] == 15170*/)) {
	$text1 = mysql_real_escape_string(str_replace("\r","",$_POST['note_text']));
	$sql='insert into oldbk.`users_adminnotepad`
		set owner = '.$user['id'].', txt="'.($text1).'"
		ON DUPLICATE KEY UPDATE txt = "'.($text1).'"';
	mysql_query($sql);

}

?>
<HTML><HEAD><TITLE>ОлдБК - Информация о <?=$user['login']?></TITLE>
<link rel="stylesheet" href="http://i.oldbk.com/i/main.css" type="text/css"/>
<META content="text/html; charset=windows-1251" http-equiv=Content-type>
<META content=no-cache http-equiv=Cache-Control>
<META content=NO-CACHE http-equiv=PRAGMA>
<META content=0 http-equiv=Expires>
<meta name="keywords" content="бойцовский клуб, бк, онлайн игра, rpg, магия бой, игра фэнтези, fantasy, маг " />
<meta name="description" content="Бойцовский клуб - rpg онлайн игра, он же БК, созданный в 2003 году. Борьба Тьмы и Света. Бои, магия, персонажи - всё это Бойцовский клуб ">
<meta name="robots" content="index, nofollow"/>
<meta name="author" content="oldbk.com">
<link rel="apple-touch-icon-precomposed" sizes="512x512" href="http://i.oldbk.com/i/icon/oldbk_512x512.png" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://i.oldbk.com/i/icon/oldbk_144x144.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="http://i.oldbk.com/i/icon/oldbk_114x114.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="http://i.oldbk.com/i/icon/oldbk_72x72.png" />
<link rel="apple-touch-icon-precomposed" sizes="58x58" href="http://i.oldbk.com/i/icon/oldbk_58x58.png" />
<link rel="apple-touch-icon-precomposed" sizes="48x48" href="http://i.oldbk.com/i/icon/oldbk_48x48.png" />
<link rel="apple-touch-icon-precomposed" sizes="29x29" href="http://i.oldbk.com/i/icon/oldbk_29x29.png" />
<link rel="apple-touch-icon-precomposed" href="http://i.oldbk.com/i/icon/oldbk_57x57.png" />
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="http://i.oldbk.com/i/js/store.min.js"></script>
<SCRIPT>
var CtrlPress = false;

function StrToPrivate(str) {
	if (store.enabled) {
		store.set('toprivate', str);
	}
}

function showhide(id)
{
	if (document.getElementById(id).style.display=="none") {
	 	document.getElementById(id).style.display="block";
	}
	else {
		document.getElementById(id).style.display="none";
	}
}

function info(login)
{
    login = login.replace('%', '%25');
	while (login.indexOf('+')>=0) login = login.replace('+', '%2B');
    while (login.indexOf('#')>=0) login = login.replace('#', '%23');
	while (login.indexOf('?')>=0) login = login.replace('?', '%3F');
	if (CtrlPress) { window.open('/zayavka.pl?logs=1&date=&filter='+login, '_blank'); }
	else { window.location.href='/inf.pl?login='+login; }
}


$(function() {

    $('.can-hidde').on("load", function() {
        return;
   }).on("error ", function() {
        $(this).hide();
   });

})



var currenttab = 1;

function inftab(n) {
	if (n == currenttab) return;
	document.getElementById("tctab"+currenttab).className = "ainfpas";
	document.getElementById("tctab"+n).className = "ainfact";

	document.getElementById("inftab"+currenttab).style.display = "none";
	document.getElementById("inftab"+n).style.display = "";

	currenttab=n;
	}

</SCRIPT>
<script type="text/javascript" src="i/showthing.js"></script>

    <style>
        .big-dadge-image {
            margin-bottom: 5px;
        }

	.ainflable{font-family:Arial,Helvetica,sans-serif;color:#8f0000;font-size:14px;text-align:center;text-decoration:none}
	.ainfpas{background:url(http://i.oldbk.com/i/chat/chat_passive.jpg);background-repeat:no-repeat;text-align:center;}
	.ainfpas{cursor:pointer;}
	.ainfpas.ainflable:hover{font-family:Arial,Helvetica,sans-serif;color:#8f0000;font-size:14px;text-align:center;text-decoration:none;}
	.ainflable:visited{font-family:Arial,Helvetica,sans-serif;color:#8f0000;font-size:14px;text-align:center;text-decoration:none}
	.ainfpas:hover a{color:#000000;}
	.ainfact{background:url(http://i.oldbk.com/i/chat/chat_aaactive.jpg);background-repeat:no-repeat;text-align:center;}
	.ainfact:hover a{color:#8f0000;}
	.ainfact a{color:#000000;}
	.ainfact:hover a{color:#000000;}
	.ainfact:hover a{cursor:default;}
   </style>

</HEAD>
<BODY bgColor=#e2e0e0>
<?
$effect = mysql_fetch_array(mysql_query("SELECT `time` FROM `effects` WHERE `owner` = '{$user['id']}' and `type` = '5' LIMIT 1;"));
?>
<TABLE cellPadding=0 cellSpacing=0 width=100% border=0>
  <TBODY>
  <TR>
    <TD align=left vAlign=top  style="width:250px;">
      <?
       if (($own[id]==14897) OR ($own[id]==8540) )  {$sha=1;} else {$sha=0;}

	$secondklan = false;
	if ($user['in_tower'] == 16) {
		$selfclan = mysql_query('SELECT * FROM oldbk.clans WHERE short = "'.$own['klan'].'"');
		$selfclan = mysql_fetch_assoc($selfclan);
		if ($selfclan !== FALSE) {
			if ($selfclan['rekrut_klan'] > 0) {
				// мы клан основа
				$q = mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$selfclan['rekrut_klan']);
				$c = mysql_fetch_assoc($q);
				$secondklan = $c['short'];
			} elseif ($selfclan['base_klan'] > 0) {
				// мы клан рекрут
				$q = mysql_query('SELECT * FROM oldbk.clans WHERE id = '.$selfclan['base_klan']);
				$c = mysql_fetch_assoc($q);
				$secondklan = $c['short'];
			}
		}

	}

	if (($user[in_tower]==3 AND $user[battle]==0) || ($user['in_tower'] == 16 && $user['klan'] != $own['klan'] && $user['klan'] != $secondklan))
		{
		//пказываем образ невидимы
		echo "<CENTER>";
		?>
		<A HREF="javascript:top.AddToPrivate('<?=$user['login']?>', top.CtrlPress)" target=refreshed><img src="http://i.oldbk.com/i/lock.gif" width=20 height=15></A><img src="http://i.oldbk.com/i/align_<?echo ($user['align']>0 ? $user['align']:"0");?>.gif"><?php if ($user['klan'] <> '') { echo '<img title="'.$user['klan'].'" src="http://i.oldbk.com/i/klan/'.$user['klan'].'.gif">'; } ?><B><?=$user['login']?></B> [<?=$user['level']?>]<a href=inf.php?<?=$user['id']?> target=_blank><IMG SRC='http://i.oldbk.com/i/inf.gif' WIDTH=12 HEIGHT=11 ALT="Инф. о <?=$user['login']?>"></a>
		<?
		echo setHP('??','??',1);
		echo setMP('??','??',1);
		echo "<TABLE cellspacing=0 cellpadding=0>";
		echo "<TR>";
		echo "<TD valign=top>";
		echo "<TABLE width=196 cellspacing=0 cellpadding=0 ><tr><td valign=top>";
		echo "<TABLE width=100% cellspacing=0 cellpadding=0>";
		echo "<TR><TD >";
		echo "</TD></TR>";
		echo "</TABLE>";
		echo "</TD>";
		echo "<TD valign=top>";

	   	echo " <img src='http://i.oldbk.com/i/shadow/mhidden_full.gif' title='{$user['login']}' alt='{$user['login']}'><br />";
		echo "</TD>
		<TD width=62 valign=top>";
		echo	"</td></tr></table>";
		echo "</TD></TR>
		</TABLE>
		</CENTER>";
		echo "<CENTER>

		<TABLE cellPadding=0 cellSpacing=0 width=\"100%\">
		        <TBODY>
		          <TR>
	          <TD align=middle colSpan=2><B>
		          CapitalCity</B></TD></TR>
	        <TR>
        	  <TD colSpan=2 align=center>
		";

		if ($user['in_tower'] == 16) {
				  echo "<SMALL>Персонаж сейчас находится в клубе.<BR><CENTER><B>\"Замки\"</B>";
		} else if ( ($user['id_grup']>=1) and ($user['id_grup']<=1000) and ($user['in_tower'] == 3) )
		{

				  echo "<SMALL>Персонаж сейчас находится в клубе.<BR><CENTER><B>\"Турниры:Одиночные сражения\" <a target=_blank href=/sturlog.php?id={$user['id_grup']}>»»</a></B>";
		}
		else
		{

				  echo "<SMALL>Персонаж сейчас находится в клубе.<BR><CENTER><B>\"Турниры:Одиночные сражения\" <a target=_blank href=/nturlog.php?id={$user['id_grup']}>»»</a></B>";
		}

		echo "
		  </CENTER></SMALL></TD></TR>";
		  echo "</TBODY></TABLE></CENTER>";

		echo "</TD>
	<TD valign=top >
	<table><tr><td>
	Сила: ??<BR>
	Ловкость: ??<BR>
	Интуиция: ??<BR>
	Выносливость: ??<BR>
	Интеллект: ??<BR>
	Мудрость: ??<BR>
	<HR>
	Уровень: {$user[level]}<BR>
	Побед: {$user['win']}<BR>
	Поражений: {$user['lose']}<BR>
	Собрано черепов: {$user['skulls']}<BR>";

	if(($user['klan']) and ($user['klan']!='pal'))
	{
	echo "<a href='http://oldbk.com/encicl/klani/clans.php?clan=".close_dangling_tags($user['klan'])."' target=_blank>".close_dangling_tags($user['klan'])."</a> - ".close_dangling_tags($user['status'])."<BR>";
	} elseif($user['align'] > 0)
	{
	if ((($user['align'] > 1) && ($user['align'] < 2)) or ($user['klan']=='pal'))   { echo "<b>Паладинский орден</B> - {$user['status']}<BR>"; }

	if (($user['align'] == 3)) { echo "<b>Темное братство</B><BR>"; }
	if (($user['align'] == 2)) { echo "<b>Нейтральное братство</B><BR>"; }
	if (($user['align'] == 6)) { echo "<b>Светлое братство</B><BR>"; }
	if (($user['align'] == 1)) { echo "<b>Светлое братство</B><BR>"; }
	}

$date1 = explode(" ", $user['borntime']);
$date2 = explode("-", $date1[0]);
$date3 = "".$date2[2].".".$date2[1].".".$date2[0]."";

	echo "Место рождения: <b>{$user['borncity']}</b><BR>
	Гражданство: <b>{$user['citizen']}</b><BR>";
//	echo "День рождения персонажа: {$date3}<BR>";
	echo "<hr>
	</td></tr></table>";

		}
		else
		{
		showpersout($_SERVER['QUERY_STRING'],1,0,0,$sha);
		}


	if (!($effect['time']) || ($own['align'] > '2' && $own['align'] < '3') || ($own['align'] > '1' && $own['align'] < '2'))
	{


	if (($user[id]==102) or ($user[id]==302))
	{
		$sqlget="select * from variables where var='ghost_all_time' ; ";
		$q_get=mysql_query($sqlget);
       		if (mysql_affected_rows() > 0)
		{
		$t=mysql_fetch_array($q_get);
		$freedomt=$t[value];
		if ($freedomt-time() > 0)
		   {
		    $get_bot_next=mysql_fetch_array(mysql_query("select *  from users where id=(select value from variables where var='ghost_next_id');"));
		    echo "<font color=red><b>".$get_bot_next[login]." - вырвусь на свободу через:".floor(($freedomt-time())/60/60)." ч. ".round((($freedomt-time())/60)-(floor(($freedomt-time())/3600)*60))." мин.</b></font>";
		    }
		}
	}
	elseif ( (($user[id]>=101)and($user[id]<=110)) OR (($user[id]>=303)and($user[id]<=309)))
	{

	  $get_bot_next=mysql_fetch_array(mysql_query("select *  from users where id=(select value from variables where var='ghost_next_id');"));
	  if ($get_bot_next[id]==$user[id])
	     {
		$sqlget="select * from variables where var='ghost_all_time' ; ";
		$q_get=mysql_query($sqlget);
       		if (mysql_affected_rows() > 0)
		{
		$t=mysql_fetch_array($q_get);
		$freedomt=$t[value];
		if ($freedomt-time() > 0)
		   {
		    echo "<font color=red><b>".$get_bot_next[login]." - вырвусь на свободу через:".floor(($freedomt-time())/60/60)." ч. ".round((($freedomt-time())/60)-(floor(($freedomt-time())/3600)*60))." мин.</b></font>";
		    }
		}
	   }
	   else
	   {
   	    echo "<font color=red><b>Скоро вырвусь….</b></font>";
	   }
	}

if ($user[id]!=448 && $user[id]!=488) //непоказываем им подарки :) так надо )
      {
		if ($LIC_NAIM)
			{
			//$LIC_NAIM; - время окончани лицензии
			echo '<img src="http://i.oldbk.com/i/inf_naim.gif"
			onMouseOut="HideThing(this)"
			onMouseOver="ShowThing(this,35,25,\'Лицензия наемника\')">';
			$hrhr=true;
			}

		if ($LIC_LEK)
			{
			//$LIC_NAIM; - время окончани лицензии
			echo '<img src="http://i.oldbk.com/i/inf_lekar.gif"
			onMouseOut="HideThing(this)"
			onMouseOver="ShowThing(this,35,25,\'Лицензия лекаря\')">';
			$hrhr=true;
			}

		if ($LIC_MAG)
			{
			//$LIC_NAIM; - время окончани лицензии
			echo '<img src="http://i.oldbk.com/i/inf_mag.gif"
			onMouseOut="HideThing(this)"
			onMouseOver="ShowThing(this,35,25,\'Лицензия мага\')">';
			$hrhr=true;
			}

		if ($LIC_TORG)
			{
			//$LIC_NAIM; - время окончани лицензии
			echo '<img src="http://i.oldbk.com/i/inf_torg.gif"
			onMouseOut="HideThing(this)"
			onMouseOver="ShowThing(this,35,25,\'Лицензия торговца\')">';
			$hrhr=true;
			}


		if ($hrhr)
			{
			echo '<hr width=330 align="left">';
			}


		if ($user['deal']==1 and $user['id']!=7363) {
			echo '<img src="http://i.oldbk.com/i/alchemy1.gif" style="cursor: pointer;"  onclick="showhide(\'mysub\'); return(false);"
			onMouseOut="HideThing(this)"
			onMouseOver="ShowThing(this,35,25,\'Официальный дилер ОлдБК\')"> - <a href="http://oldbk.com/encicl/diler.html" target=_blank>Посмотреть дилерский ассортимент</a>';
			$my_subdil=mysql_query("SELECT  subdil , subdil_login  FROM oldbk.sub_dil WHERE `dil` = '{$user['id']}';");
			if (mysql_num_rows($my_subdil) > 0)
				{
//				echo "<br><a href=# onclick=\"showhide('mysub'); return(false);\"><small>Нажмите сюда, чтобы посмотреть список субдилеров:</small></a>";
				echo "<br>";
					echo '<br><div id=mysub style="display: none;"><small>Субдилеры:  </small>';
					while ($subd = mysql_fetch_array($my_subdil))
						{
						$str_sub.="<b>".$subd[subdil_login]."</b><a target=_blank href='http://capitalcity.oldbk.com/inf.php?{$subd[subdil]}'><img src='http://i.oldbk.com/i/inf.gif'></a>, ";
						}

					$str_sub=substr($str_sub, 0, -2);
					echo $str_sub;
					echo '</small></div>';
				}
			echo '<hr width=330 align="left">';
		}
		if ($user['deal']==2) {
		$my_dil=mysql_fetch_array(mysql_query("SELECT  dil , dil_login  FROM oldbk.sub_dil WHERE `subdil` = '{$user['id']}' LIMIT 1;"));

			echo '<a target=_blank href="http://capitalcity.oldbk.com/inf.php?'.$my_dil[dil].'">
			<img src="http://i.oldbk.com/i/alchemy2.gif" style="cursor: pointer;"
			onMouseOut="HideThing(this)"
			onMouseOver="ShowThing(this,35,25,\'Официальный субдилер - Дилера <b>'.$my_dil[dil_login].'</b> \')"></a> - <a href="http://oldbk.com/encicl/diler.html" target=_blank>Посмотреть дилерский ассортимент</a><hr width=330 align="left">';
		}

		if (($user['deal']>0))
		{
		include "config_ko.php";
		include "ny_events.php";


		////////////////////////////////////
		if (((time()>$KO_start_time2) and (time()<$KO_fin_time2)) OR
		    ((time()>$KO_start_time) and (time()<$KO_fin_time))  OR
		    ((time()>$KO_start_time7) and (time()<$KO_fin_time7))  OR
		    ((time()>$KO_start_time8-14400) and (time()<$KO_fin_time8-14400))  OR
		    (((time()>$ny_events['larcistart']) and (time()<$ny_events['larciend'])))
		)
		{
		echo "<table border=0>
			<tr>
			<td><b>Текущие акции :</b></td>
			<td>";
			$tab_open=1;
		}
		///////////////////////////////////


		if ((time()>$KO_start_time) and (time()<$KO_fin_time))
				{
				echo '<a href="'.$KO_A_URL.'" target=_blank><span style="cursor: pointer;"   onMouseOut="HideThing(this)"  onMouseOver="ShowThing(this,35,25,\''.$KO_A_MESS.'\')">'.$KO_A_NAME.'</span>';
				}

		if ((time()>$KO_start_time7) and (time()<$KO_fin_time7))
				{
				echo '<a href="'.$KO_A_URL7.'" target=_blank><span style="cursor: pointer;"   onMouseOut="HideThing(this)"  onMouseOver="ShowThing(this,35,25,\''.$KO_A_MESS7.'\')">'.$KO_A_NAME7.'</span></a>';
				}

		if ((time()>$KO_start_time8-14400) and (time()<$KO_fin_time8-14400))
				{
				echo '<span style="cursor: pointer;"   onMouseOut="HideThing(this)"  onMouseOver="ShowThing(this,35,25,\''.$KO_A_MESS8.'\')">'.$KO_A_NAME8.'</span>';
				}

		if ((time()>$ny_events['larcistart']) and (time()<$ny_events['larciend']))
				{
			echo '<span style="cursor: pointer;" ><img src="http://i.oldbk.com/i/dill_action_2new3.gif" onclick="showhide(\'boxes\'); return(false);"  ></span>';
			$divs_box=1;
				}
		/*
		if ((time()>mktime(0,0,0,4,2)) and (time()<mktime(23,59,59,4,19)))
				{
			echo '<span style="cursor: pointer;" ><img src="http://i.oldbk.com/i/dill_action_6-1.gif" onclick="showhide(\'boxes\'); return(false);"  ></span>';
			$divs_box=1;
				}*/


		if ((time()>$KO_start_time2) and (time()<$KO_fin_time2))
				{
				echo '<span style="cursor: pointer;" ><img src="http://i.oldbk.com/i/dill_action_3new3.gif" onclick="showhide(\'action3\'); return(false);"  ></span>';
				$divs_ko2=1;
				}

			if ($tab_open==1)
			{
			echo "</td>
			</tr>
			</table><hr width=330 align='left'>";
			}

		//////////divs
		/*
		if ($divs_box==1)
			{
			$boxs_kol[1]=0;			$boxs_kol[2]=0; 			$boxs_kol[3]=0; 			$boxs_kol[4]=0;

			$get_all_boxes=mysql_query("select box_type , count(id) as kol from oldbk.boxsapril where item_id=0 group by box_type");
			 while($bbo=mysql_fetch_array($get_all_boxes))
                	{
	                	$boxs_kol[$bbo[box_type]] = $bbo[kol];
               		}

			echo "<div id=\"boxes\" style=\"display: none;\">
			<table border=0 cellpadding= 5 cellspacing=5>
			<td><table>
						<tr align=center>
							<td><a href='http://oldbk.com/encicl/?/eggs.html' target='_blank'><img src=\"http://i.oldbk.com/i/sh/egg_box3.gif\"></a></td>
							<td><a href='http://oldbk.com/encicl/?/eggs.html' target='_blank'><img src=\"http://i.oldbk.com/i/sh/egg_box4.gif\"></a></td>
						</tr>
						<tr align=center>
							<td><b>Зеленое Пасхальное яйцо</b>&nbsp;&nbsp;&nbsp;<br><small>Осталось: {$boxs_kol[3]} шт.</small></td>
							<td><b>Золотое Пасхальное яйцо</b>&nbsp;&nbsp;&nbsp;<br><small>Осталось: {$boxs_kol[4]} шт.</small></td>
						</tr>
						</table>
						</td>
			</tr>
			</table><hr width=330 align='left'></div>";
		}
		*/

		if ($divs_box==1) {
			$boxs_kol[1]=0;			$boxs_kol[2]=0; 			$boxs_kol[3]=0; 			$boxs_kol[4]=0;

			$get_all_boxes=mysql_query("select box_type , count(id) as kol from oldbk.boxs where item_id=0 group by box_type");
			 while($bbo=mysql_fetch_array($get_all_boxes))
                	{
	                	$boxs_kol[$bbo[box_type]]=$bbo[kol];
               		}
			echo "<div id=\"boxes\" style=\"display: none;\">
			<table border=0 cellpadding= 5 cellspacing=5>
			<td><table>
						<tr align=center>
							<td><a href='http://oldbk.com/encicl/?/laretz.html' target='_blank'><img src=\"http://i.oldbk.com/i/sh/larec_1.gif\"></a></td>
							<!--<td><a href='http://oldbk.com/encicl/?/laretz.html' target='_blank'><img src=\"http://i.oldbk.com/i/sh/larec_2.gif\"></a></td>-->
							<!--<td><a href='http://oldbk.com/encicl/?/laretz.html' target='_blank'><img src=\"http://i.oldbk.com/i/sh/larec_3.gif\"></a></td>-->
							<td><a href='http://oldbk.com/encicl/?/laretz.html' target='_blank'><img src=\"http://i.oldbk.com/i/sh/larec_4.gif\"></a></td>
						</tr>
						<tr align=center>
							<td><b>Рубиновый ларец</b>&nbsp;&nbsp;&nbsp;<br><small>Осталось: {$boxs_kol[1]} шт.</small></td>
							<!--
							<td><b>Бирюзовый ларец</b>&nbsp;&nbsp;&nbsp;<br><small>Осталось: {$boxs_kol[2]} шт.</small></td>
							<td><b>Малахитовый ларец</b>&nbsp;&nbsp;&nbsp;<br><small>Осталось: {$boxs_kol[3]} шт.</small></td>
							-->
							<td><b>Золотой ларец</b>&nbsp;&nbsp;&nbsp;<br><small>Осталось: {$boxs_kol[4]} шт.</small></td>
						</tr>
						</table>
						</td>
			</tr>
			</table><hr width=330 align='left'></div>";
		}
		if ($divs_ko2==1)
			{
				echo "<div id=\"action3\" style=\"display: none;\"><table border=0 cellpadding= 5 cellspacing=5>
				<tr>
				<td>{$KO_A_MESS2}</td>
				</tr>
				</table><hr width=330 align='left'></div>";
			}

		}




		if (($user['deal'] == -1 && $user['id'] != 8540 && $user['id'] != 182783 && $user['id'] != 7937) ) {
			echo '<a href=http://oldbk.com/encicl/gamesupport.html target=_blank><img src="http://i.oldbk.com/i/support/support.gif" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,25,25,\'Помощник по игровым вопросам!\')"></a><hr width=330 align="left">';
		}
		if ($user['id']==8540) {
			echo ' <img src="http://i.oldbk.com/i/comm.gif" onMouseOut="HideThing(this)"
			onMouseOver="ShowThing(this,35,25,\'Коммерческий отдел - Глава\')"> - <a href="http://oldbk.com/commerce/" target=_blank>Посмотреть ассортимент ком. отдела</a><hr width=330 align="left">';
		}
		if ($user['id']==182783) {
			echo '<a href=http://oldbk.com/encicl/gamesupport.html target=_blank> <img src="http://i.oldbk.com/i/support/support.gif" onMouseOut="HideThing(this)"
			onMouseOver="ShowThing(this,35,25,\'Помощь по Игре - Сотрудник\')"></a> <img src="http://i.oldbk.com/i/comm.gif" onMouseOut="HideThing(this)"
			onMouseOver="ShowThing(this,35,25,\'Коммерческий отдел - Сотрудник\')"> - <a href="http://oldbk.com/commerce/" target=_blank>Посмотреть ассортимент ком. отдела</a><hr width=330 align="left">';
		}
		if ($user['id']==7937) {
			echo '<a href=http://oldbk.com/encicl/gamesupport.html target=_blank> <img src="http://i.oldbk.com/i/support/support.gif" onMouseOut="HideThing(this)"
			onMouseOver="ShowThing(this,35,25,\'Помощь по Игре - Сотрудник\')"></a> <img src="http://i.oldbk.com/i/comm.gif" onMouseOut="HideThing(this)"
			onMouseOver="ShowThing(this,35,25,\'Коммерческий отдел - Сотрудник\')"> - <a href="http://oldbk.com/commerce/" target=_blank>Посмотреть ассортимент ком. отдела</a><hr width=330 align="left">';
		}

		if (false) //$user['id']==7108
			{
			//билеты
			$max_bill=60;
			$last_bil_id=mysql_fetch_array(mysql_query("select id from bilet ORDER by id desc limit 1;"));
			$bdill=$max_bill-$last_bil_id[0];
			if ($bdill<0) {$bdill=0;}

			echo '<table border=0 cellPadding=0 cellSpacing=0><tr><td align=left valign=center><a href=http://oldbk.com/encicl/?/predmeti/7_year.html target=_blank><img src="http://i.oldbk.com/i/sh/7_year.gif" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,25,25,\'Билеты на 7ю годовщину ОлдБК\')" width=40></a></td><td>&nbsp;&nbsp;<b>Билеты на 7ю годовщину ОлдБК</b><br>&nbsp;&nbsp;доступно '.(int)$bdill.' шт. </td></tr></table><hr width=330 align="left">';
			}

		if ($user['married']) {
				$m_ring['img']='married.gif';
				$para=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.users WHERE id='".$user['married']."' LIMIT 1"));

				$data=mysql_query("SELECT * FROM oldbk.gellery WHERE otdel=99 AND owner ='".$user['id']."' and dressed=1 LIMIT 1");
				if(mysql_num_rows($data))
				{
					$m_ring=mysql_fetch_assoc($data);
				}
				if($para[id]>0)
				{

					$src = str_ireplace(" ","%20",$para['login']);
					if ($user['sex'] == 1) {echo '
						<a href="/inf.php?login='.$src.'">
						<img src="http://i.oldbk.com/i/sh/'.$m_ring['img'].'"
						onMouseOut="HideThing(this)"
						onMouseOver="ShowThing(this,35,25,\'Женат на '.$para['login'].'\')"></a>';}
					else
					{
						echo '
						<a href="/inf.php?login='.$src.'">
						<img src="http://i.oldbk.com/i/sh/'.$m_ring['img'].'"
						onMouseOut="HideThing(this)"
						onMouseOver="ShowThing(this,35,25,\'Замужем за '.$para['login'].'\')"></a> ';
					}
				}
		}
		$med = explode("|",$user['medals']);
		$medals[0] = explode(";",$med[0]); //открытые значки

		if(count($med[1]>0))
		{
			/*if(isset($_GET['showallmedals']))
			{
				$medals[1] = explode(";",$med[1]); //открытые значки
			}
			else
			{
				$show_med="<a href=?{$user['id']}&showallmedals=1><small>Нажмите сюда, чтобы увидеть все медали</small></a>";
			}*/
		}

		foreach($medals as $k=>$v)
		{
			for ($i=0;$i<count($v);$i++)
			{
				show_medals($v[$i]);
			}
		}

		if($user[prem]==1)
		{
			echo ' <a href=https://oldbk.com/encicl/prem.html target=_blank><img src="http://i.oldbk.com/i/036.gif" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,45,25,\'Silver account\')"></a> ';
		}
		else
		if($user[prem]==2)
		{
			echo ' <a href=https://oldbk.com/encicl/prem.html target=_blank><img src="http://i.oldbk.com/i/037.gif" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,45,25,\'Gold account\')"></a> ';
		}
		else
		if($user[prem]==3)
		{
			echo ' <a href=https://oldbk.com/encicl/prem.html target=_blank><img src="http://i.oldbk.com/i/137.gif" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,45,25,\'Platinum account\')"></a> ';
		}


//Викторина
		if (($user[victorina] >= 10) and ($user[victorina] < 50))
		{
			echo ' <img src="http://i.oldbk.com/i/victorina1.gif" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,35,25,\'Знаток Первого уровня\')"> ';
		}
		else
		if (($user[victorina] >= 50) and ($user[victorina] < 100))
		{
			echo ' <img src="http://i.oldbk.com/i/victorina2.gif" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,35,25,\'Знаток Второго уровня\')"> ';
		}
		else if ($user[victorina] >= 100)
		{
			echo ' <img src="http://i.oldbk.com/i/victorina3.gif" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,35,25,\'Знаток Третьего уровня\')"> ';
		}
//елочные бои 2016
		if (($user[elkbat] >= 300) and ($user[elkbat] < 500))
		{
			echo ' <img src="http://i.oldbk.com/i/medal_event2016_300.png" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,35,25,\'Елочное безумие!\')"> ';
		}
		else
		if (($user[elkbat] >= 500) and ($user[elkbat] < 1000))
		{
			echo ' <img src="http://i.oldbk.com/i/medal_event2016_500.png" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,35,25,\'Елочное безумие!\')"> ';
		}
		else if (($user[elkbat] >= 1000) and ($user[elkbat] < 3000))
		{
			echo ' <img src="http://i.oldbk.com/i/medal_event2016_1000.png" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,35,25,\'Елочное безумие!\')"> ';
		}
		else if (($user[elkbat] >= 3000) and ($user[elkbat] < 7000))
		{
			echo ' <img src="http://i.oldbk.com/i/medal_event2016_3000.png" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,35,25,\'Елочное безумие!\')"> ';
		}
    else if (($user[elkbat] >= 7000) and ($user[elkbat] < 15000))
    {
      echo ' <img src="http://i.oldbk.com/i/medal_event2016_7000.png" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,35,25,\'Елочное безумие!\')"> ';
    }
    else if (($user[elkbat] >= 15000) and ($user[elkbat] < 25000))
    {
      echo ' <img src="http://i.oldbk.com/i/medal_event2016_15k.png" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,35,25,\'Елочное безумие!\')"> ';
    }
    else if (($user[elkbat] >= 25000) )
    {
      echo ' <img src="http://i.oldbk.com/i/medal_event2016_25k.png" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,35,25,\'Елочное безумие!\')"> ';
    }

		try {
			foreach (\components\models\UserBadge::findByUserId($user['id']) as $item) {
				$badge = ' <img src="'.$item['img'].'" alt="'.$item['alt'].'" onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,35,25,\''.$item['alt'].'\')"> ';
				if($item['link'] !== null) {
					echo sprintf('<a href="%s" target="_blank">%s</a>', $item['link'], $badge);
				} else {
					echo $badge;
				}
			}
		} catch (Exception $ex) {

		}
///
		if(isset($show_med))
		{
			echo '<br>'.$show_med;
		}

		$data = mysql_query_cache("select * from oldbk.inventory
					where `owner` = '{$_SERVER['QUERY_STRING']}' AND
						(
					            (`prototype`=20000 or (prototype>=55510000 and prototype<=55520000 and arsenal_klan='') or (prototype>=410130 and prototype<=410135) )
					            or (otdel >70 AND otdel <80)
					            or `type`=200 or `type`=100
					            or (`type`=200 and otdel=7)
					            or (`prototype` >= 2014001 and prototype <= 2014008)
					       	)
					       	AND
					       	prototype not in (304013,304014,304015,304016,304017,304018,304019)
					       	AND `present` !='' and otdel!=''
					order by add_time DESC ",false,60);

//".isset($_REQUEST['showallflowers'])

		if(count($data)>0)
		{
			$pr=array();
			$i=0;
			$buk=array();
			$unik=array();
			$gift=array();
			$dump=array();

			while(list($k,$row) = each($data))
			{
				if($row[prototype]==12801 || $row[prototype]==20000 || ($row[prototype]>=55510000 && $row[prototype]<=55520000 && $row[arsenal_klan]=='') || ($row[prototype]>=410130 && $row[prototype]<=410135 && $row['present']!='Торговец Галиас'))
				{
					$buk[$i]=$row;
				}
				else
				if($row[otdel]==72 || ($row['otdel']==77 && $row['type']==200) || ($row['prototype'] >= 2014001 && $row['prototype'] <= 2014008))
				{
					$unik[$i]=$row;
				}
				else
				if($row[otdel]==71 || $row[otdel]==73 || $row[otdel]==74 || $row[otdel]==75 || $row[otdel]==79 || ($row[type]==200 && $row[otdel]==7))
				{
					$gift[$i]=$row;
				}
				else
				{
					$dump[$i]=$row;
				}
				$i++;
			}

			$c_buk=count($buk);
			if($c_buk>0)
			{

				echo '<BR>Букеты:<br /><div style="float: left;">';
				$max=$c_buk>20?((isset($_REQUEST['showallflowers']))?$c_buk:20):$c_buk;
				$i=0;

				foreach($buk as $k=>$v)
				{
			    		echo "<img width=60px height=60x src='http://i.oldbk.com/i/sh/{$v['img']}' onMouseOut='HideThing(this)' onMouseOver=\"ShowThing(this,55,55,'<b>{$v['name']}</b><br>Подарок от {$v['present']}".(($v['letter'])?"<br>".$v['letter']:"")."')\">";
					$i++;
					if($i==$max)
					{
						break;
					}
				}
				echo "</div><br /><br /><br />";
				if(!isset($_REQUEST['showallflowers']) && $c_buk>20)
				{
					echo "<br /><a href=\"inf.php?{$user['id']}&showallflowers=1\"><small>Нажмите сюда, чтобы увидеть все цветы...</small></a>";
				}
			}

			$c_unik=count($unik);

			if($c_unik>0)
			{
				echo '<BR><br />Уникальные подарки:<br /><div style="float: left;">';
				$max=$c_unik>20?((isset($_REQUEST['showallunicgifts']))?$c_unik:20):$c_unik;
				$i=0;
				foreach($unik as $k=>$v)
				{
					$present=explode(':|:',$v['present']);
					if(strpos($present[0],'клан') || strpos($present[0],'Клан'))
					{

						$show=str_replace(' клан ','',$present[0]);
						$show=str_replace(' Клан ','',$show);
						$present[0]=str_replace('клан ','клана ',$present[0]);

						$link='<a target="_blank" href="http://oldbk.com/encicl/klani/clans.php?clan='.$show.'">';
					    	$link_cl='</a>';
					}
					else
					if(strlen($present[1])>0)
					{
						$link='<a target="_blank" href="inf.php?'.$present[1].'">';
					    	$link_cl='</a>';
					}
					else
				    	{
				    		$link='';
				    		$link_cl='';
				    	}
				    	echo $link."<img onMouseOut=\"HideThing(this)\" onMouseOver=\"ShowThing(this,55,55,'<b>".$v['name']."</b><br>Подарок от ".$present[0]."".(($v['letter'])?"<br>".$v['letter']:"")."')\" width=60px height=60x src='http://i.oldbk.com/i/sh/".$v['img']."'>".$link_cl;
					$i++;
					if($i==$max)
					{
						break;
					}
				}
				echo "</div>";
				if(!isset($_REQUEST['showallunicgifts']) && $c_unik>20)
				{
				    echo "<br /><br /><br /><br /><a href=\"inf.php?{$user['id']}&showallunicgifts=1\"><small>Нажмите сюда, чтобы увидеть все уникальные подарки...</small></a>";
				}
				else
				{
					echo "<br><br><br>";
				}
			}
			$c_gift=count($gift);
			if($c_gift>0)
			{
				echo '<BR><br />Подарки:<br /><div style="float: left;">';
				$max=$c_gift>10?((isset($_REQUEST['showallgifts']))?$c_gift:10):$c_gift;
				$i=0;
				foreach($gift as $k=>$v)
				{
					$v['present']= str_ireplace("\"", "&quot;", $v['present']);
					echo "<img onMouseOut=\"HideThing(this)\" onMouseOver=\"ShowThing(this,55,55,'<b>".$v['name']."</b><br>Подарок от ".$v['present']."".(($v['letter'])?"<br>".$v['letter']:"")."')\" width=60px height=60x src='http://i.oldbk.com/i/sh/".$v['img']."'>";
					$i++;
					if($i==$max)
					{
						break;
					}
				}
				echo "</div>";
				if(!isset($_REQUEST['showallgifts']) && $c_gift>10) {
					echo "<br /><br /><br /><br /><a href=\"inf.php?{$user['id']}&showallgifts=1\"><small>Нажмите сюда, чтобы увидеть все подарки...</small></a>";
				}
			}
		}


//===================================================================



	}

	function star_sign($month, $day) {


 //  $time = mktime(0, 0, 0, $month, $day, $year); //return the Unix timestamp
 //  $day_of_year = date("z", $time);  // "z" is equal to  the day of the year 0 to 365

//   if (date("L", $time) && ($day_of_year > 59)) // for leap years "L" is LEAP YEAR
//      $day_of_year -= 1; // if it is FEB 29 (59) Subtract 1 from the day of year

$month=(int)$month;
$day=(int)$day;

	  if ((int)$month == 1) {
         if ($day >= 21) {return "11";} else {return "10";}}
      else if ($month == 2) {
         if ($day >= 21) {return "12";} else {return "11";} }
       else if ($month == 3) {
         if ($day >= 21) {return "1";} else {return "12";} }
       else if ($month == 4) {
         if ($day >= 21) {return "2";} else {return "1";} }
       else if ($month == 5) {
         if ($day >= 21) {return "3";} else {return "2";} }
       else if ($month == 6) {
         if ($day >= 22) {return "4";} else {return "3";} }
       else if ($month == 7) {
         if ($day >= 23) {return "5";} else {return "4";} }
       else if ($month == 8) {
         if ($day >= 24) {return "6";} else {return "5";} }
       else if ($month == 9) {
         if ($day >= 24) {return "7";} else {return "6";} }
       else if ($month == 10) {
         if ($day >= 24) {return "8";} else {return "7";} }
       else if ($month == 11) {
         if ($day >= 23) {return "9";} else {return "8";} }
       else if ($month == 12) {
         if ($day >= 22) {return "10";} else {return "9";}}
}

	$zodik_gif=star_sign(substr($user['borndate'],3,2), substr($user['borndate'],0,2));

	$smagic='';
	if (($user['smagic']>0) and ($user['smagic']<5))
		{
		$st[1]='infoicon_fire';
		$st[2]='infoicon_ground';
		$st[3]='infoicon_air';
		$st[4]='infoicon_water';
		$smagic="<IMG class=\"big-dadge-image\" align=right onMouseOut=\"HideThing(this)\" onMouseOver=\"ShowThing(this,-55,5,'Знак стихии')\" height=100 src=\"http://i.oldbk.com/i/".$st[$user['smagic']].".png\" width=100><br><br>";
		}
	?>
	</TD>
	<?
		$unaem=mysql_fetch_assoc(mysql_query('SELECT * FROM users_clons WHERE owner = '.$user['id'].' and naem_status = 1'));
		if ($unaem['id']>0)
		{
		$unaemico="http://i.oldbk.com/i/naem/infoicon_naem{$unaem['naem_id']}.png";
		echo '<td valign="top" width="120" align=right>';
		echo '<a href="http://capitalcity.oldbk.com/inf.php?'.$unaem[id].'" target="_blank"><IMG class="big-dadge-image" align=right onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,-30,5, \'Наемник '.$unaem['login'].'\')" height=100 src="'.$unaemico.'" width=100></a>';

		echo '<span style="position: relative;top: 76px;left: '.(88+strlen($unaem['level'])*2).'px;color: white;text-align: center; margin-left: auto;">'.$unaem['level'].'</span>';
		echo '</td>';

		}

	?>
	<td valign="top" width="120" align=right>
	<?
	/*
	<IMG align=right onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,-55,5,'Знак зодиака')" height=100 src="http://i.oldbk.com/i/<?=$zodik_gif;?>.gif" width=100>
	*/
	if(in_array($user['uclass'], [1, 2, 3])) {
        $uclass = [
            1 => [
                'img' => 'vert.gif',
                'title' => 'Уворот'
            ],
			2 => [
				'img' => 'krit.gif',
				'title' => 'Критовик'
			],
			3 => [
				'img' => 'tank.gif',
				'title' => 'Танк'
			],
        ]; ?>

        <IMG class="big-dadge-image" align=right onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,-30,5, '<?= $uclass[$user['uclass']]['title'] ?>')" height=100 src="http://i.oldbk.com/i/classes/<?= $uclass[$user['uclass']]['img'] ?>" width=100>


	<?php } ?>
	<?=$smagic;?>

<?
  if (($user[bot]==0) AND ($user[level]>=5))
  {
?>
<br><br><br><br><br>
<a href='http://blog.oldbk.com/~<?=$user[id]; ?>/' target="_blank"><IMG class="big-dadge-image" align=right onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,-80,5,'Личный блог персонажа <?=$user[login]; ?>')"  src="http://i.oldbk.com/i/blogs.png"></a>
<?
  }
	$KO_start_time31=mktime(15,0,0,6,15,2016);
	$KO_fin_time31=mktime(23,59,59,7,9,2016);
	if ((time()>$KO_start_time31) and (time()<$KO_fin_time31))
		{
		if  (($obezlichen!=true) or ($obezlichen_moder==true))
		{
		$get_flag=mysql_fetch_assoc(mysql_query("select * from oldbk.users_flag where owner='{$user['id']}'"));
			if ($get_flag['flag']!='')
			{
			echo '<br><br><br><br><br><br><br>';
			echo "<table border=0>";
			echo "<tr>";
			echo "<td>";
			echo '<IMG align=center onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,-80,5,\''.$get_flag['flag_name'].'\')"  src="http://i.oldbk.com/i/euro2016/'.$get_flag['flag'].'"></a>';
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			}
		  }
		}
		else
	if ( time()<mktime(23,59,59,07,16,2018) ) // 16.07.2018 23:5
		{
		if  (($obezlichen!=true) or ($obezlichen_moder==true))
		{
		$get_flag=mysql_fetch_assoc(mysql_query("select * from oldbk.users_flag where owner='{$user['id']}'"));
			if ($get_flag['flag']!='')
			{
			echo '<br><br><br><br><br><br><br>';
			echo "<table border=0>";
			echo "<tr>";
			echo "<td>";
			echo '<IMG align=center onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,-80,5,\''.$get_flag['flag_name'].'\')"  src="http://i.oldbk.com/i/chm2018/'.$get_flag['flag'].'"></a>';
			echo "</td>";
			echo "</tr>";
			echo "</table>";

			/*echo '<br><br><br><br><br><br>';
			echo "<fieldset style=\"text-align:justify; width:200px; height:100px;border: solid 1px #CCC;-moz-border-radius: 16px;-webkit-border-radius: 16px;border-radius: 16px;padding: 1em 2em;margin: 1em 0em;\"><legend></legend>";
			echo $get_flag['flag_name'];
			echo '<IMG align=right onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,-80,5,\''.$get_flag['flag_name'].'\')"  src="http://i.oldbk.com/i/flags/'.$get_flag['flag'].'"></a>';
			echo "</fieldset>";
			*/
			}
		  }
		}

?>

</td>
   </TR>
   </TBODY>
</TABLE>

<?
}
else
	{
	echo '

	   &nbsp;
	   </TD>
	      </TR>
	</TABLE>';
	}

echo '<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td background="http://i.oldbk.com/i/chat/x_bg.jpg" height="26">&nbsp;</td>
<td nowrap width=115 height="26" id="tctab1" class="ainfact" OnClick="inftab(1);return false;"><a href="#" OnClick="inftab(1);return false;" class="ainflable">Анкета</a></td>
<td nowrap width=115 height="26" id="tctab2" class="ainfpas" OnClick="inftab(2);return false;"><a href="#" OnClick="inftab(2);return false;" class="ainflable">Хроника</a></td>
<td background="http://i.oldbk.com/i/chat/x_bg.jpg" height="26">&nbsp;</a></td>
</tr>
</table>
	<div id="inftab1">';

if ($effect['time'])
{
	$eff=$effect['time'];
	$tt=time();
	$time_still=$eff-$tt;
	$tmp = floor($time_still/2592000);
	$id=0;
	if ($tmp > 0) {
		$id++;
		if ($id<3) {$out .= $tmp." мес. ";}
		$time_still = $time_still-$tmp*2592000;
	}
	$tmp = floor($time_still/604800);
	if ($tmp > 0) {
		$id++;
		if ($id<3) {$out .= $tmp." нед. ";}
		$time_still = $time_still-$tmp*604800;
	}
	$tmp = floor($time_still/86400);
	if ($tmp > 0) {
		$id++;
		if ($id<3) {$out .= $tmp." дн. ";}
		$time_still = $time_still-$tmp*86400;
	}
	$tmp = floor($time_still/3600);
	if ($tmp > 0) {
		$id++;
		if ($id<3) {$out .= $tmp." ч. ";}
		$time_still = $time_still-$tmp*3600;
	}
	$tmp = floor($time_still/60);
	if ($tmp > 0) {
		$id++;
		if ($id<3) {$out .= $tmp." мин. ";}
	}
	echo "<H3>Обезличен. Еще $out</H3>";
	$obezlichen=true;
	if (($own['align'] > '2' && $own['align'] < '3') || ($own['align'] > '1' && $own['align'] < '2'))
	{
	$obezlichen_moder=true;
	?>
	Имя: <?=$user['realname']?><BR>Пол: <?php
	if($user['id']==190672) //пятницо.
	{
		echo "Средний";
	}
	else
	{
		if($user['sex']) { echo "Мужской";} else {echo "Женский";}
	}
	if ($user['city']) { echo "<BR>Город: {$user['city']}"; }

	/*
	$user['http'] = str_replace("'", "", $user['http']);
	$user['http'] = str_replace("\"", "", $user['http']);
	$user['http'] = str_replace("<", "", $user['http']);
	$user['http'] = str_replace(">", "", $user['http']);
	$user['http'] = str_replace("|", ";", $user['http']);
	$user['http'] = str_replace("`", "", $user['http']);
	$user['http'] = str_replace("’", "", $user['http']);
	$user['http'] = str_replace("&#39;", "", $user['http']);
	*/

	//if ($user['http']) { echo "<BR>Домашняя страница: <noindex><A href=\"".((substr($user['http'],0,4)=='http'?"":"http://").$user['http'])."\" target=_blank rel=\"nofollow\" >".((substr($user['http'],0,4)=='http'?"":"http://").$user['http'])."</a></noindex>"; }
	//if ($user['icq']) {echo "<BR>ICQ: {$user['icq']}"; }
	if ($user['lozung']) { echo "<BR>Девиз: <CODE>{$user['lozung']}</CODE>"; }?>
	<BR>Увлечения / хобби:<BR><CODE>
    <?


	    	if (( ($user['id']==190672) or ($user['id']==9) or (($user['id']>=102) and ($user['id']<=110)) ) or  ($user['bot']>0))
		{
		   echo $user['info'];
		}
		else
		{
		    echo nl2br(htmlspecialchars($user['info']));
		 }


	echo '</CODE></div>';
	echo_history($user);
	echo "</div>";
	}
}
else {

?>


	<H3>Анкетные данные</H3>Имя: <?=$user['realname']?><BR>Пол: <? if($user['id']==190672) //пятницо.
								{
									echo "Средний";
								}
								else
								{
									if($user['sex']) { echo "Мужской";} else {echo "Женский";}
								}

	if ($user['city']) { echo "<BR>Город: {$user['city']}"; }
	if ($user['lozung']) { echo "<BR>Девиз: <CODE>{$user['lozung']}</CODE>"; }?>
	<BR>Увлечения / хобби:<BR><CODE>
	<?
	  if (($user[klan]=='radminion') OR ($user[klan]=='Adminion'))
	    {
		 echo str_replace("\n","<br>",$user['info']);
	    }
	    else
	    {
	    	if (( ($user['id']==190672) or ($user['id']==9) or (($user['id']>=102) and ($user['id']<=110)) ) or ($user['bot']>0) )
		{
		   echo $user['info'];
		}
		else
		{
		    echo nl2br(htmlspecialchars($user['info']));
		}
	    }

	echo '</CODE></div>';

	echo_history($user);
	echo "</div>";
}

		flush();

		$okld=0;
		if ($own['align'] > '2' && $own['align'] < '3') {
			$okld=1;
		}
		elseif (($own['klan'] == "radminion") && !($user['align'] > '2' && $user['align'] < '3')) {
			$okdop=1;
		}
		elseif (($own['align'] > '1.2' && $own['align'] < '2') && ($user['align'] > '1' && $user['align'] < '2') && ($own['align'] >= $user['align'])) {
			$okld=1;
		}
			elseif (($own['align'] > '1.2' && $own['align'] < '2') && !($user['align'] > '2' && $user['align'] < '3') && !($user['align'] > '1' && $user['align'] < '2') && !($user['klan'] == "radminion")) {
			$okld=1;
		}

		if ($user['id']=='395467' || $user['id'] == '546433') {
			$okld=0;
		}

		if ( (!($own['klan'] == "radminion")) and ($user['id']==5) )
		{
			$okld=0;
		}

if ($okld==1) {
	if ($user['unikstatus'] != "") {
		echo '<br><br><B>Уникальный статус:</b> '.$user['unikstatus']."<br>";
	}

	$fppal = fopen('/www/cache/inftxt/inflog','a+');
	fwrite($fppal,time().":".$own['id'].":".$own['login'].":".$user['id'].":".$user['login']."\r\n");
	fclose($fppal);

	echo "<br><br><font style='text'>За персонажем замечены следующие темные делишки:</font><br><br>";
	$ldd = mysql_query("SELECT * FROM oldbk.`lichka` WHERE `pers` = '{$user['id']}' ORDER by `id` ASC;");


	$beforeamns = array();
	$afteramns = array();
	$amnstime = mktime(0,0,0,12,8,2016);

	while ($ld = mysql_fetch_array($ldd)) {
		if ($ld['date'] <= $amnstime) {
			$beforeamns[] = $ld;
		} else {
			$afteramns[] = $ld;
		}
	}


	// показываем ЛД до амнистии
	if(count($beforeamns) && ($own['klan']=='Adminion' || $own['klan']=='radminion' || $own['align']=='1.99')) {
		echo "<a onclick=\"showhide('oldlichka');\" href=\"javascript:Void();\">История дел до аминистии 08/12/2016</a><br>";
		echo "<div id=oldlichka style=\"display:none;\">";

		while(list($k,$ld) = each($beforeamns)) {
			$dat=date("d.m.Y H:i",$ld['date']);
			$text=$ld['text'];
		        if (preg_match("/http:(.*?)oldbk.com/",$text)) {
	             		$text=preg_replace("/http:(.*?)(\s|$)/","$1 ",$text);
	            	}
	        	if (preg_match("/{ee}(.*?){ee}/",$text)) {
	             		if($own['klan']=='Adminion' || $own['klan']=='radminion' || $own['align']=='1.99') {
	             		} else {
	             			$text=preg_replace("/{ee}(.*?){ee}/"," Выход через ком.отдел ",$text);
	             		}
	            	}

			$text = preg_replace('#([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}?)#iU', '<a href=https://apps.db.ripe.net/search/query.html?searchtext=$1+&search%3AdoSearch=Search target=_blank>$1</a>', $text);

	        	$BR_COUNT=substr_count($text, '<b>');
	        	$BR_COUNT2=substr_count($text, '</b>');
	        	if ($BR_COUNT>$BR_COUNT2) {$text.="</b>"; }

			echo "<CODE>$dat $text </CODE><br>";
		}

		echo "</div>";
	}

	while(list($k,$ld) = each($afteramns)) {
		$dat=date("d.m.Y H:i",$ld['date']);
		$text=$ld['text'];
	        if (preg_match("/http:(.*?)oldbk.com/",$text)) {
             		$text=preg_replace("/http:(.*?)(\s|$)/","$1 ",$text);
            	}
        	if (preg_match("/{ee}(.*?){ee}/",$text)) {
             		if($own['klan']=='Adminion' || $own['klan']=='radminion' || $own['align']=='1.99') {
             		} else {
             			$text=preg_replace("/{ee}(.*?){ee}/"," Выход через ком.отдел ",$text);
             		}
            	}

		$text = preg_replace('#([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}?)#iU', '<a href=https://apps.db.ripe.net/search/query.html?searchtext=$1+&search%3AdoSearch=Search target=_blank>$1</a>', $text);

        	$BR_COUNT=substr_count($text, '<b>');
        	$BR_COUNT2=substr_count($text, '</b>');
        	if ($BR_COUNT>$BR_COUNT2) {$text.="</b>"; }

		echo "<CODE>$dat $text </CODE><br>";
	}

if(($own['klan'] == 'Adminion' || $own['klan']=='radminion') || ($own['align'] >= '1.9' && $own['align'] < '2'))
	{
	$re = mysql_query("select u.id, u.login, u.level, r.ref, r.user  from users_referals r LEFT JOIN oldbk.users u on u.id=r.user where owner='{$user['id']}' ORDER by `id` ASC;");
	if (mysql_num_rows($re))
	{
	echo "<BR><BR><BR>Рефералы этого персонажа:<a href=javascript:void(0); id=\"multi_ref\" onClick=\"showhideadm('reff'); return false;\">(развернуть)</a><br><div id='reff' style='display: none;'><pre><small>";
	while ($rr = mysql_fetch_array($re))
		{
			if ($rr['login']!='')
				{
				echo $rr['login']."[".$rr['level']."] <a href=/inf.php?{$rr['id']} target=_blank>[i]</a> \n";
				}
				else
				{
				echo "Чар уже удален oldID:".$rr['user']." \n";
				}

		}
	echo "-----------------------------------------------\n";
	echo "Итого: заработал на рефералах:\n";
	$re = mysql_query("select sum(ekr) as smekr, bank from oldbk.dilerdelo where dilerid=8 and owner='{$user['login']}' Group by bank");
	$total=0;
	while ($rr = mysql_fetch_array($re))
		{

			echo "Счет №:".$rr['bank']." - всего:".$rr['smekr']." \n";
				$total+=$rr['smekr'];
		}
	echo "-----------------------------\n";
	echo "Всего:".$total."\n";
	echo "</small></pre></div>";
	}


	$itref=mysql_fetch_array(mysql_query("select u.id, u.login, u.level, r.ref, r.user  from users_referals r LEFT JOIN oldbk.users u on u.id=r.owner where user='{$user['id']}' "));
	if ($itref['id']>0)
		{
		echo "<BR><BR><BR><small>Персонаж является рефералом для :".$itref['login']."[".$itref['level']."] <a href=/inf.php?{$itref['id']} target=_blank>[i]</a></small>";
		}
	}
}

$okdop=0;
$admdop=0;
if ($own['align'] > '2' && $own['align'] < '3') {
	$okdop=1;
	$admdop=1;
}
elseif (($own['klan'] == "radminion") && !($user['align'] > '2' && $user['align'] < '3') && !($user['id'] == 190672)) {
	$admdop=1;
	$okdop=1;
}
elseif (($own['align'] > '1.3' && $own['align'] < '2') && ($user['align'] > '1' && $user['align'] < '2') && ($own['align'] >= $user['align']) && !($user['id'] == 190672)) {
	$okdop=1;
}
	elseif (($own['align'] > '1.3' && $own['align'] < '2') && !($user['align'] > '2' && $user['align'] < '3') && !($user['align'] > '1' && $user['align'] < '2') && !($user['klan'] == "radminion") && !($user['id'] == 190672)) {
	$okdop=1;
}

if ($user['id']=='395467' || $user['id'] == '546433') {
	if ($own['klan'] != "radminion") {
		$okdop=0;
		$admdop=0;
	}
}


if(($own['klan'] == 'Adminion' || $own['klan']=='radminion') ) {
	$q = mysql_query('SELECT * FROM oldbk.users_scans WHERE owner = '.$user['id']);
	if (mysql_num_rows($q) > 0) {
		echo "<br><H4><u>Документы: </u></H4>";
		?>
<script>
var Hint33Name = '';

function new_ret(but,id){
	var title='Впишите комментарий отказа';
	var submbutton='';
	var magicformcontent='';
    	var el = document.getElementById("hint33");
	magicformcontent='</TD></TR><TR><TD align=left><br><INPUT size=40 TYPE=text id="inp" NAME="mess">';
	submbutton='<br><br><center><INPUT id="button3" TYPE="submit" value=" Отклонить "></center><br></TD></TR></TABLE></FORM></td></tr></table>';
//	alert (what);
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=250><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint33();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
	'<form action="?rejectscan='+id+'" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td>'+magicformcontent+submbutton;

	var x=findPosX(but);
	var y=findPosY(but);
	var posx=x-150;
	var posy=y-200;
	el.style.visibility = "visible";
	el.style.left = posx + 'px';
	el.style.top = posy + 'px';
	el.style.zIndex = 999;
	document.getElementById('inp').focus();
	Hint33Name = 'coment';
}
function closehint33(){
	document.getElementById("hint33").style.visibility="hidden";
    	Hint33Name='';
}
function findPosX(obj)
  {
    var curleft = 0;
    if(obj.offsetParent)
        while(1)
        {
          curleft += obj.offsetLeft;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.x)
        curleft += obj.x;
    return curleft;
  }

  function findPosY(obj)
  {
    var curtop = 0;
    if(obj.offsetParent)
        while(1)
        {
          curtop += obj.offsetTop;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.y)
        curtop += obj.y;
    return curtop;
  }
</script>
<?php
		$i = 1;
		while($d = mysql_fetch_assoc($q)) {
			$f = explode('_',$d['filename']);
			$st = "";
			if ($d['status'] == 0) $st = "На проверке";
			if ($d['status'] == 1) $st = "Подтверждён";
			if ($d['status'] == 2) $st = "Отклонён";

			echo '<b>'.$i.'.</b> Дата: <b>'.date("d/m/Y H:i:s",$d['sdate']).'</b> Статус: <b>'.$st.'</b> ';

			if (($own['align'] == '1.99') || ($own['klan'] == 'Adminion' || $own['klan']=='radminion')) {
				echo '<a target="_blank" href="?showscan='.$d['id'].'">Посмотреть</a> ';

				if ($d['status'] == 0) {
					echo '<a style="cursor:pointer;" OnClick="javascript: if (confirm(\'Вы уверены?\')) {location.href = \'?approvescan='.$d['id'].'\'} ">Подтвердить</a> ';
					echo '<a style="cursor:pointer;" OnClick="javascript:new_ret(this,'.$d['id'].'); return false;">Отклонить</a> ';
				}
			}
			if($own['klan'] == 'Adminion' || $own['klan']=='radminion') {
				echo '<a target="_blank" href="?deletescan='.$d['id'].'">Удалить</a> ';
			}

			echo '<br>';
			$i++;
		}
	}
}

if ($user['id']=='395467') {$okdop=0;}

		if ( (!($own['klan'] == "radminion")) and ($user['id']==5) )
		{
			$okdop=0;
		}

if ($okdop==1) {
	echo "<br><H4><u>Дополнительные сведения: </u></H4>";
?>
	<?
	if(($own['klan'] == 'Adminion' || $own['klan']=='radminion') )
	{
	echo"День рождения: {$user['borndate']} <br>";
	}
	 ?>
	<?if($admdop==1)
	{
	echo 'E-mail: '.$user['email'].'<br>';
	}
	?>
	<?if($user['level']<7)
	{
		echo "Oпыт: ".$user['exp']." <br>";
		echo "Число неиспользованных UP-ов: ".$user['stats']." <br>";
	}

	$reader = new Reader('./GeoIP/GeoLite2-City.mmdb');

	$user['ip'] = trim(str_replace(',','',$user['ip']));

	try {
		$record = $reader->city($user['ip']);
       	} catch (Exception $ex) {

	}


	$country = "";

	if ($record) {
		$country .= iconv("UTF-8","windows-1251",$record->country->names['ru']);
		if (isset($record->city->names["ru"])) $country .= iconv("UTF-8","windows-1251"," (".$record->city->names["ru"].") ");
	}


	?>

	IP при регистрации: <? echo"<a href=https://apps.db.ripe.net/search/query.html?searchtext={$user['ip']}+&search%3AdoSearch=Search target=_blank>{$user['ip']}</a> ".$country."<br>";?>
	Кредитов: <? echo"{$user['money']} <br>";?>
	</font>
  <?

    if($own['align'] > '2' && $own['align'] < '3') {

    if ($user[lab]>0)
      {
      $llabb=mysql_fetch_array(mysql_query("SELECT * FROM labirint_users WHERE owner='{$user['id']}' LIMIT 1 ;"));
      echo "Чар в лабе".$user[lab]." :№<b>".$llabb[map]."</b> X=".$llabb[x]." Y=".$llabb[y]." start time:".$llabb[start]."<br>";
      }
    if ($user[ruines] > 0 )
      {
      echo "Чар в руинах:".$user[ruines];
      echo " / real_room:".$user[room] ;
      }


    	 echo "<hr><b>Только для ангелов: Банковские счета</b><br/>";
       $bq = mysql_query("SELECT id,cr,ekr, def FROM oldbk.bank WHERE owner='{$user['id']}'");
       while($b = mysql_fetch_array($bq)) {
          echo "{$b['id']}: <b>{$b['cr']}</b> кр. <b>{$b['ekr']}</b> екр. ".($b['def']==1?" <b>Основной</b>":"")."<br/>";
       }
		?> Золотых монет: <? echo $user['gold'] ?> <img src="http://i.oldbk.com/i/icon/coin_icon.png" style="margin-bottom:-2px;"> <br> <?
	   	echo "Oпыт: ".$user['exp']." <br>";
	   	echo "Репутация: ".$user['repmoney']." (всего ".$user['rep'].")<br>";
		echo "Число неиспользованных UP-ов: ".$user['stats']." <br>";

	$chk_act=mysql_fetch_array(mysql_query("select * from oldbk.confirmpasswd_new where owner={$user['id']} AND active=1")); ?>
	<?php if($chk_act): ?>
    <span style="color:red">Проверочный код: <?= $chk_act['active_key'] ?></span><br>

<?php endif; ?>

<?php
	echo "<b>Хр-ки:</b><br/>";
	$ICAN_EDIT=flase;
		if(($own['klan'] == 'Adminion' || $own['klan']=='radminion') )
		{

		if ($user['bot']>0)
			{
			$ICAN_EDIT=true;
			echo "<hr>";
			echo "	Сила: {$user['sila']} <a href=?{$user['id']}&up=sila><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=sila><img src='http://i.oldbk.com/i/down.gif'></a> <br>";
			echo "	Ловкость: {$user['lovk']} <a href=?{$user['id']}&up=lovk><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=lovk><img src='http://i.oldbk.com/i/down.gif'></a> <br>";
			echo "	Интуиция: {$user['inta']} <a href=?{$user['id']}&up=inta><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=inta><img src='http://i.oldbk.com/i/down.gif'></a> <br>";
			echo "	Выносливость: {$user['vinos']} <a href=?{$user['id']}&up=vinos><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=vinos><img src='http://i.oldbk.com/i/down.gif'></a> <br>";
			echo "	Интеллект: {$user['intel']} <a href=?{$user['id']}&up=intel><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=intel><img src='http://i.oldbk.com/i/down.gif'></a> <br>";
			echo "	Мудрость: {$user['mudra']} <a href=?{$user['id']}?up=mudra><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=mudra><img src='http://i.oldbk.com/i/down.gif'></a> <br>";
			echo "	MAXHP(+100) {$user['maxhp']}: <a href=?{$user['id']}&up=maxhp><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=maxhp><img src='http://i.oldbk.com/i/down.gif'></a> <br>";
			echo "<hr>";

			}

		}

	function get_wep_type($idwep)
	{
		if ($idwep == 0 || $idwep == null || $idwep == '') { return "kulak"; }
		$wep = mysql_fetch_array(mysql_query('SELECT `otdel`,`minu` FROM oldbk.`inventory` WHERE `id` = '.$idwep.' LIMIT 1;'));
		if($wep[0] == '1') { return "noj"; }
		elseif($wep[0] == '12') { return "dubina"; }
		elseif($wep[0] == '11') { return "topor"; }
		elseif($wep[0] == '13') {return "mech";	}
		elseif($wep[1] > 0) { return "buket"; } else { return "kulak"; }
	}

	if ($user['naem_status']>0) {
		$arrmf[uvorota] = $user['sum_mfuvorot'];
		$arrmf[auvorota] = $user['sum_mfauvorot'];
		$arrmf[krita] = $user['sum_mfkrit'];
		$arrmf[akrita] = $user['sum_mfauvorot'];
		$min_damage = $user['sum_minu'];
		$max_damage = $user['sum_maxu'];
		$user_dressed[6] = $user['sum_bron1'];
		$user_dressed[7] = $user['sum_bron2'];
		$user_dressed[8] = $user['sum_bron3'];
		$user_dressed[9] = $user['sum_bron4'];
	} else {
		$user_dressed = mysql_fetch_array(mysql_query('SELECT sum(minu),sum(maxu),sum(mfkrit),sum(mfakrit),sum(mfuvorot),sum(mfauvorot),sum(bron1),sum(bron2),sum(bron3),sum(bron4),sum(ab_mf), sum(ab_bron), sum(ab_uron), count(if(unik=1,1,null)) as unik , count(if(unik=2,1,null)) as supunik  FROM oldbk.`inventory` WHERE `dressed`=1 AND `owner` = \''.$user['id'].'\' LIMIT 1;'));

		if (!($user_dressed[0]>0)) { if ($ICAN_EDIT) echo " <b>Бот не имеет вещей для правки мф!</b> <br>"; ; $ICAN_EDIT=false; }

		$aeff = getalleff($user['id']);

		$user_level = $user['level'];

		$master = 0;
		switch(get_wep_type($user['weap']))
		{
			case "noj": $master += $user['noj']; break;
			case "dubina": $master += $user['dubina']; break;
			case "topor": $master += $user['topor']; break;
			case "mech": $master += $user['mec']; break;
		}

		$min_damage = round((floor($user['sila']/3) + 1) + $user_level + $user_dressed[0] * (1 + 0.07 * $master));
		$max_damage =  round((floor($user['sila']/3) + 4) + $user_level + $user_dressed[1] * (1 + 0.07 * $master));

		$prof_data=GetUserProfLevels($user);

		if($weapon_type == 'kulak' && $user['align'] == '2')
		{
			$min_damage += $user_level;
			$max_damage += $user_level;
		};

							// Бонус урона:  1-2 за каждый уровень мастерства (в минимальный и максимальный урон)
							if ($prof_data['smithlevel']>0)
							{
							$min_damage += (int)($prof_data['smithlevel']*1) ;
							$max_damage +=(int)($prof_data['smithlevel']*2) ;
							}

						//Оружейник     Модификатор урона: +...% (абсолютный, как на артах)      0,25% за каждый уровень мастерства
						if ($prof_data['armorerlevel']>0)
								{
								$user_dressed[12]+=($prof_data['armorerlevel']*0.25);
								}

						// Бронник      Усиление брони: +...%      0,5% за каждый уровень мастерства
						if ($prof_data['armorsmithlevel']>0)
							{
								$user_dressed[11]+=($prof_data['armorsmithlevel']*0.5);
							}

						// Ювелир (профессиональная точность)    //Бонус от ювелира: + 20 антиуворота / уровень ремесла
						if ($prof_data['jewelerlevel']>0)
							{
							$user_dressed[5]+=round(20*$prof_data['jewelerlevel']);
							}

							// Портной (удобно подогнанная одежда)     //Бонус от портного: +20 антикрита / уровень ремесла
						if ($prof_data['tailorlevel']>0)
							{
							$user_dressed[3]+=round(20*$prof_data['tailorlevel']);
							}


			$arrmf[uvorota]=$user_dressed[4] + $user['lovk'] * 5;
			$arrmf[auvorota]=$user_dressed[5] + $user['lovk'] * 5 + $user['inta'] * 2;
			$arrmf[krita]=$user_dressed[2] + $user['inta'] * 5;
			$arrmf[akrita]=$user_dressed[3] + $user['inta'] * 5 + $user['lovk'] * 2;

			//запоминаем 100-е значения
			$arrmf_uvorota=$arrmf[uvorota];
			$arrmf_auvorota=$arrmf[auvorota];
			$arrmf_krita=$arrmf[krita];
			$arrmf_akrita=$arrmf[akrita];

			if ($user_dressed[10]>0)
			{
			//если есть бонусы на МФ то
			//Если бонус на мф - он добавляется в максимальный глобальный параметр игрока.
			$add_to_mf=getmaxmf($arrmf);
			$arrmf[$add_to_mf]+=(int)($arrmf[$add_to_mf]*($user_dressed[10]/100));
			$green_out[$add_to_mf]=$user_dressed[10];
			}

			if ($user_dressed[11]>0 || isset($aeff[791])) {
				$plusbron = 0;

				if ($user_dressed[11] > 0) {
					$user_dressed[6]+=(int)($user_dressed[6]*($user_dressed[11]/100));
					$user_dressed[7]+=(int)($user_dressed[7]*($user_dressed[11]/100));
					$user_dressed[8]+=(int)($user_dressed[8]*($user_dressed[11]/100));
					$user_dressed[9]+=(int)($user_dressed[9]*($user_dressed[11]/100));
					$plusbron += $user_dressed[11];
				}

				if (isset($aeff[791])) {
					$user_dressed[6]+=(int)($user_dressed[6]*(15/100));
					$user_dressed[7]+=(int)($user_dressed[7]*(15/100));
					$user_dressed[8]+=(int)($user_dressed[8]*(15/100));
					$user_dressed[9]+=(int)($user_dressed[9]*(15/100));
					$plusbron += 15;
				}

				$gree_out_bron=" <font color=green>(+".$plusbron."%)</font>";
			}


			if ($user_dressed[12]>0 || isset($aeff[792])) {

				$plusuron = 0;
				if($user_dressed[12]>0) {
					$min_damage+=(int)($min_damage*($user_dressed[12]/100));
					$max_damage+=(int)($max_damage*($user_dressed[12]/100));
					$plusuron += $user_dressed[12];
				}

				if (isset($aeff[792])) {
					$min_damage+=(int)($min_damage*(5/100));
					$max_damage+=(int)($max_damage*(5/100));
					$plusuron += 5;
				}

				$gree_out_uron=" <font color=green>(+".$plusuron."%)</font>";
			}

			if ($user_dressed[13]>=13)
			{
			$arrmf[uvorota]+=round($arrmf_uvorota*0.04);
			$arrmf[auvorota]+=round($arrmf_auvorota*0.04);
			$arrmf[krita]+=round($arrmf_krita*0.04);
			$arrmf[akrita]+=round($arrmf_akrita*0.04);
			$green_out[uvorota]+=4;
			$green_out[auvorota]+=4;
			$green_out[krita]+=4;
			$green_out[akrita]+=4;
			}
			else
			if ($user_dressed[13]>=12)
			{
			$arrmf[uvorota]+=round($arrmf_uvorota*0.03);
			$arrmf[auvorota]+=round($arrmf_auvorota*0.03);
			$arrmf[krita]+=round($arrmf_krita*0.03);
			$arrmf[akrita]+=round($arrmf_akrita*0.03);
			$green_out[uvorota]+=3;
			$green_out[auvorota]+=3;
			$green_out[krita]+=3;
			$green_out[akrita]+=3;
			}
			else
			if ($user_dressed[13]>=9)
			{
			$arrmf[uvorota]+=round($arrmf_uvorota*0.02);
			$arrmf[auvorota]+=round($arrmf_auvorota*0.02);
			$arrmf[krita]+=round($arrmf_krita*0.02);
			$arrmf[akrita]+=round($arrmf_akrita*0.02);
			$green_out[uvorota]+=2;
			$green_out[auvorota]+=2;
			$green_out[krita]+=2;
			$green_out[akrita]+=2;
			}
			else
			if ($user_dressed[13]>=6)
			{
			$arrmf[uvorota]+=round($arrmf_uvorota*0.01);
			$arrmf[auvorota]+=round($arrmf_auvorota*0.01);
			$arrmf[krita]+=round($arrmf_krita*0.01);
			$arrmf[akrita]+=round($arrmf_akrita*0.01);
			$green_out[uvorota]+=1;
			$green_out[auvorota]+=1;
			$green_out[krita]+=1;
			$green_out[akrita]+=1;
			}

	///////////////
			if ($user_dressed[14]>=13)
			{
			$arrmf[uvorota]+=round($arrmf_uvorota*0.08);
			$arrmf[auvorota]+=round($arrmf_auvorota*0.08);
			$arrmf[krita]+=round($arrmf_krita*0.08);
			$arrmf[akrita]+=round($arrmf_akrita*0.08);
			$green_out[uvorota]+=8;
			$green_out[auvorota]+=8;
			$green_out[krita]+=8;
			$green_out[akrita]+=8;
			}
			else
			if ($user_dressed[14]>=12)
			{
			$arrmf[uvorota]+=round($arrmf_uvorota*0.06);
			$arrmf[auvorota]+=round($arrmf_auvorota*0.06);
			$arrmf[krita]+=round($arrmf_krita*0.06);
			$arrmf[akrita]+=round($arrmf_akrita*0.06);
			$green_out[uvorota]+=6;
			$green_out[auvorota]+=6;
			$green_out[krita]+=6;
			$green_out[akrita]+=6;
			}
			else
			if ($user_dressed[14]>=9)
			{
			$arrmf[uvorota]+=round($arrmf_uvorota*0.04);
			$arrmf[auvorota]+=round($arrmf_auvorota*0.04);
			$arrmf[krita]+=round($arrmf_krita*0.04);
			$arrmf[akrita]+=round($arrmf_akrita*0.04);
			$green_out[uvorota]+=4;
			$green_out[auvorota]+=4;
			$green_out[krita]+=4;
			$green_out[akrita]+=4;
			}
			else
			if ($user_dressed[14]>=6)
			{
			$arrmf[uvorota]+=round($arrmf_uvorota*0.02);
			$arrmf[auvorota]+=round($arrmf_auvorota*0.02);
			$arrmf[krita]+=round($arrmf_krita*0.02);
			$arrmf[akrita]+=round($arrmf_akrita*0.02);
			$green_out[uvorota]+=2;
			$green_out[auvorota]+=2;
			$green_out[krita]+=2;
			$green_out[akrita]+=2;
			}


			// мф+1% от книг
			if (isset($aeff[793])) {
				$arrmf[uvorota]+=round($arrmf[uvorota]*0.01);
				$arrmf[auvorota]+=round($arrmf[auvorota]*0.01);
				$arrmf[krita]+=round($arrmf[krita]*0.01);
				$arrmf[akrita]+=round($arrmf[akrita]*0.01);

				$green_out[uvorota]+=1;
				$green_out[auvorota]+=1;
				$green_out[krita]+=1;
				$green_out[akrita]+=1;
			}
	}

if ($user['bot']==0)
	{
	$ICAN_EDIT=false;
	}

?>
Урон: <? echo $min_damage; if ($ICAN_EDIT) { echo "<a href=?{$user['id']}&up=minu><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=minu><img src='http://i.oldbk.com/i/down.gif'></a>"; } ?> - <? echo $max_damage.$gree_out_uron; if ($ICAN_EDIT) { echo " <a href=?{$user['id']}&up=maxu><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=maxu><img src='http://i.oldbk.com/i/down.gif'></a> (+/-10)"; } ?>  <br>
Модификаторы:<br>
&nbsp; уворот: &nbsp;<? echo $arrmf[uvorota]."% ".(($green_out[uvorota]>0)?"<font color=green>(+".$green_out[uvorota]."%)</font>":""); if ($ICAN_EDIT) { echo "(+/-100) <a href=?{$user['id']}&up=mfuvorot><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=mfuvorot><img src='http://i.oldbk.com/i/down.gif'></a>"; } ?><br/>
&nbsp; антиуворот: &nbsp;<? echo $arrmf[auvorota]."% ".(($green_out[auvorota]>0)?"<font color=green>(+".$green_out[auvorota]."%)</font>":""); if ($ICAN_EDIT) { echo "(+/-100) <a href=?{$user['id']}&up=mfauvorot><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=mfauvorot><img src='http://i.oldbk.com/i/down.gif'></a>"; } ?><br/>
&nbsp; крит: &nbsp;<? echo $arrmf[krita]."% ".(($green_out[krita]>0)?"<font color=green>(+".$green_out[krita]."%)</font>":""); if ($ICAN_EDIT) { echo "(+/-100) <a href=?{$user['id']}&up=mfkrit><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=mfkrit><img src='http://i.oldbk.com/i/down.gif'></a>"; } ?><br/>
&nbsp; антикрит: &nbsp;<? echo $arrmf[akrita]."% ".(($green_out[akrita]>0)?"<font color=green>(+".$green_out[akrita]."%)</font>":""); if ($ICAN_EDIT) { echo "(+/-100) <a href=?{$user['id']}&up=mfakrit><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=mfakrit><img src='http://i.oldbk.com/i/down.gif'></a>"; } ?><br/>
Броня<br>
головы:<? echo $user_dressed[6].$gree_out_bron; if ($ICAN_EDIT) { echo "(+/-10) <a href=?{$user['id']}&up=bron1><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=bron1><img src='http://i.oldbk.com/i/down.gif'></a>"; } ?><br/>
корпуса:<? echo $user_dressed[7].$gree_out_bron; if ($ICAN_EDIT) { echo "(+/-10) <a href=?{$user['id']}&up=bron2><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=bron2><img src='http://i.oldbk.com/i/down.gif'></a>"; } ?><br/>
пояса:<? echo $user_dressed[8].$gree_out_bron; if ($ICAN_EDIT) { echo "(+/-10) <a href=?{$user['id']}&up=bron3><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=bron3><img src='http://i.oldbk.com/i/down.gif'></a>"; } ?><br/>
ног:<? echo $user_dressed[9].$gree_out_bron; if ($ICAN_EDIT) { echo "(+/-10) <a href=?{$user['id']}&up=bron4><img src='http://i.oldbk.com/i/up.gif'></a> <a href=?{$user['id']}&down=bron4><img src='http://i.oldbk.com/i/down.gif'></a>"; } ?><br/>
Мастерство владения: ножами: <?=$user['noj']?> , мечами: <?=$user['mec']?> , дубинами: <?=$user['dubina']?>,топорами: <?=$user['topor']?><BR>
Магическое мастерство:Стихия огня: <?=$user['mfire']?>,Стихия воды: <?=$user['mwater']?>,Стихия воздуха: <?=$user['mair']?>,Стихия земли: <?=$user['mearth']?>,Магия Света: <?=$user['mlight']?>,Серая магия: <?=$user['mgray']?>,Магия Тьмы: <?=$user['mdark']?><BR>
<FONT COLOR="#333399">Возможных увеличений умелок: <?=$user['master']?></font>
<br><br><b>Ремесла:</b><br>
<?php

include "craft_config.php";
include "craft_functions.php";

$prof = GetUserProfData($user);
reset($craftlist);
echo '<table>';
while(list($k,$v) = each($craftlist)) {
	echo '<tr><td>'.$craftlistrname[$k]."</td><td><b>".$prof[$v.'level'].'</b> ('.$prof[$v.'exp'].' / '.$craftexptable[$prof[$v.'level']+1].')</td></tr>';
}
echo '</table>';
?>
<hr>
<?


}

$other=true;

		if ( (!($own['klan'] == "radminion")) and ($user['id']==5) )
		{
		$other=false;
		}

if ($other)
{

if($own['klan'] == 'Adminion' || $own['klan']=='radminion' /*|| $own['id'] == 15170*/) {

	?>
<H4><u>Квесты: </u></H4>
<ul>
	<?php
	$User = new \components\models\User($user);
	$Quest = $app->quest
		->setUser($User)
		->get();
	foreach($Quest->getDescriptionsInfo() as $_quest) { ?>
		<li><?= $_quest[1] ?> <?= $_quest[2] ?>. <?= $_quest[3] ?></li>
	<?php } ?>
</ul>
	<hr>

	<?php

	echo "<br><H4><u>Блокнот: </u></H4>";
	$q = mysql_fetch_array(mysql_query('Select * FROM oldbk.`users_adminnotepad` WHERE owner = '.$user['id'].' limit 1;'));
	$txt = $q['txt'];
	$txt = htmlspecialchars($txt,ENT_QUOTES);
	echo '<form  action="'.$_SERVER["REQUEST_URI"].'" method="post">';
	echo '<b>Добавить/редактировать сообщение </b><br>';
	echo '<textarea id="txtdata" name="note_text" rows=8 cols=85 wrap="on">'.$txt.'</textarea><br>';
	echo '<input type="submit" name="savenotepad" value="Сохранить">';
        echo '</form>';
}
echo '<hr>';

if($own['klan'] == 'Adminion' || $own['klan']=='radminion' || $own['id'] == 7937 /*|| $own['id'] == 15170*/)  {
	echo '<br><H4><u>Ограничение входа: </u></H4>';
	$q = mysql_query('SELECT * FROM effects WHERE owner = '.$user['id'].' AND type = 9898');

	if (isset($_POST['blockcountry']) && mysql_num_rows($q) == 0) {
		mysql_query('INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`add_info`) VALUES ('.$user['id'].',"Ограничение входа",1999999999,9898,"'.$_POST['blockcountry'].'")') or die();
		mysql_query('UPDATE users SET sid = CONCAT(sid,"'.mt_rand(0,999999999).'") WHERE id = '.$user['id']);
		$q = mysql_query('SELECT * FROM effects WHERE owner = '.$user['id'].' AND type = 9898');
	}
	if (isset($_POST['unblockcountry']) && mysql_num_rows($q) == 1) {
		mysql_query('DELETE FROM effects WHERE owner = '.$user['id'].' AND type = 9898') or die();
		$q = mysql_query('SELECT * FROM effects WHERE owner = '.$user['id'].' AND type = 9898');
	}


	if (mysql_num_rows($q) == 0) {
		require_once("GeoIP/geoip.inc");
		require_once("GeoIP/geoipregionvars.php");
		$gi = geoip_open("GeoIP/GeoIP.dat",GEOIP_STANDARD);
		echo '<form  action="'.$_SERVER["REQUEST_URI"].'" method="post">';
		echo 'Страна: <select name="blockcountry"> ';
		$i = 0;
		reset($gi->GEOIP_COUNTRY_CODES);
		each($gi->GEOIP_COUNTRY_CODES);
		while(list($k,$v) = each($gi->GEOIP_COUNTRY_CODES)) {
			echo '<option value="'.$gi->GEOIP_COUNTRY_CODES[$k].'">'.$gi->GEOIP_COUNTRY_NAMES[$k].'</option>';
			$i++;
		}
		echo '</select> <input type="submit" value="Ограничить">';
	        echo '</form>';
		geoip_close($gi);
	} else {
		require_once("GeoIP/geoip.inc");
		require_once("GeoIP/geoipregionvars.php");
		$gi = geoip_open("GeoIP/GeoIP.dat",GEOIP_STANDARD);
		$cname = "";
		$q = mysql_fetch_assoc($q);
		while(list($k,$v) = each($gi->GEOIP_COUNTRY_CODES)) {
			if ($v == $q['add_info']) {
				$cname = $gi->GEOIP_COUNTRY_NAMES[$k];
			}
		}

		echo 'Ограничен вход страной: '.$cname.' <form action="'.$_SERVER["REQUEST_URI"].'" method="post">';
		echo '<input type="submit" value="Снять ограничение" name="unblockcountry">';
	        echo '</form>';
		geoip_close($gi);
	}

}


if($own['klan'] == 'Adminion' || $own['klan']=='radminion' || $own['id'] == 7937 /*|| $own['id'] == 15170*/)  {
	echo '<br><H4><u>Ограничение входа (только НЕ из этой страны): </u></H4>';
	$q = mysql_query('SELECT * FROM effects WHERE owner = '.$user['id'].' AND type = 9899');

	if (isset($_POST['blockcountry2']) && mysql_num_rows($q) == 0) {
		mysql_query('INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`add_info`) VALUES ('.$user['id'].',"Ограничение входа (только не из этой страны)",1999999999,9899,"'.$_POST['blockcountry2'].'")') or die();
		mysql_query('UPDATE users SET sid = CONCAT(sid,"'.mt_rand(0,999999999).'") WHERE id = '.$user['id']);
		$q = mysql_query('SELECT * FROM effects WHERE owner = '.$user['id'].' AND type = 9899');
	}
	if (isset($_POST['unblockcountry2']) && mysql_num_rows($q) == 1) {
		mysql_query('DELETE FROM effects WHERE owner = '.$user['id'].' AND type = 9899') or die();
		$q = mysql_query('SELECT * FROM effects WHERE owner = '.$user['id'].' AND type = 9899');
	}


	if (mysql_num_rows($q) == 0) {
		require_once("GeoIP/geoip.inc");
		require_once("GeoIP/geoipregionvars.php");
		$gi = geoip_open("GeoIP/GeoIP.dat",GEOIP_STANDARD);
		echo '<form  action="'.$_SERVER["REQUEST_URI"].'" method="post">';
		echo 'Страна: <select name="blockcountry2"> ';
		$i = 0;
		reset($gi->GEOIP_COUNTRY_CODES);
		each($gi->GEOIP_COUNTRY_CODES);
		while(list($k,$v) = each($gi->GEOIP_COUNTRY_CODES)) {
			echo '<option value="'.$gi->GEOIP_COUNTRY_CODES[$k].'">'.$gi->GEOIP_COUNTRY_NAMES[$k].'</option>';
			$i++;
		}
		echo '</select> <input type="submit" value="Ограничить">';
	        echo '</form>';
		geoip_close($gi);
	} else {
		require_once("GeoIP/geoip.inc");
		require_once("GeoIP/geoipregionvars.php");
		$gi = geoip_open("GeoIP/GeoIP.dat",GEOIP_STANDARD);
		$cname = "";
		$q = mysql_fetch_assoc($q);
		while(list($k,$v) = each($gi->GEOIP_COUNTRY_CODES)) {
			if ($v == $q['add_info']) {
				$cname = $gi->GEOIP_COUNTRY_NAMES[$k];
			}
		}

		echo 'Ограничен вход только не из этой страны: '.$cname.' <form action="'.$_SERVER["REQUEST_URI"].'" method="post">';
		echo '<input type="submit" value="Снять ограничение" name="unblockcountry2">';
	        echo '</form>';
		geoip_close($gi);
	}

}

  ?>

 <script>
function showhideadm(id)
{
	if (document.getElementById(id).style.display=="none") {
	 	document.getElementById(id).style.display="block";
		document.getElementById("multi_link").innerHTML="(Свернуть)";
	}
	else {
		document.getElementById(id).style.display="none";
		document.getElementById("multi_link").innerHTML="(Развернуть)";
	}
}
 </script>


<?
if($_GET['delmulti']) {
 if($own['align'] == 2.4 or $own['align'] == 2.7) {
	mysql_query("delete from oldbk.delo_multi where id={$_GET[delmulti]}");
 }
}


	echo "<br><H4><u>Заходы с одного компьютера: </u></H4> <a href=javascript:void(0); id=\"multi_link\" onClick=\"showhideadm('multi'); return false;\">(развернуть)</a><br><div id='multi' style='display: none;'>";
	$i=0;
	$lplist = mysql_query("SELECT * FROM oldbk.`delo_multi` WHERE `idperslater` = '{$user['id']}' OR `idpersnow` = '{$user['id']}';");

	$ulist = array();

	while ($iplog = mysql_fetch_array($lplist)) {
		$ookk=1;
		if ($iplog[1] == 3 || $iplog[2] == 3 || $iplog[1] == 4 || $iplog[2] == 4 || $iplog[1] == 5 || $iplog[2] == 5  || $iplog[1] == 6 || $iplog[2] == 6 || $iplog[1] == 12 || $iplog[2] == 12) {
			$ookk=0;
		}

		if ($iplog[1] == 703213 || $iplog[2] == 703213) {
			$ookk=0;
		}

		if ($iplog[1] == 5 || $iplog[2] == 5) {
			$ookk=0;
		}


		if ($iplog[1] == 2 || $iplog[2] == 2) {
			$ookk=0;
		}
		if ($iplog[1] == 8325 || $iplog[2] == 8325 || $iplog[1] == 188 || $iplog[2] == 188 || $iplog[1] == 395467 || $iplog[2] == 395467) {
			$ookk=0;
		}
		if ($iplog[1] == 8540 || $iplog[2] == 8540 || $iplog[1] == 326 || $iplog[2] == 326 || $iplog[1] == 66432 || $iplog[2] == 66432 || $iplog[1] == 190672|| $iplog[2] == 190672 || $iplog[1] == 546433 || $iplog[2] == 546433) {
			$ookk=0;
		}
		if ($iplog[1] == 14896 || $iplog[2] == 14896 || $iplog[1] == 9 || $iplog[2] == 9 || $iplog[1] == 14897 || $iplog[1] ==690426 || $iplog[1] == 8383 || $iplog[2] == 14897|| $iplog[2] == 8383  || $iplog[1] == 17936 || $iplog[2] == 17936 ||  $iplog[1] == 14895 || $iplog[2] == 14895 || $iplog[1] == 15847 || $iplog[2] == 15847  || $iplog[1] == 15100 || $iplog[2] == 15100 ) {
			$ookk=0;
		}
		if (($iplog[1] == 15593 && $iplog[2] == 7692 ) || ($iplog[1] == 7692 && $iplog[2] == 15593)) {
			$ookk=0;
		}
		if ($ookk == 1) {
			if($own['align'] == '2.4' or $own['align'] == '2.7') {
				$del = "<a href='/inf.php?{$user['id']}&delmulti={$iplog[0]}'><img src='/i/clear.gif'></a>";
			}

			preg_match("/^(.*?)-(.*?)-(.*?) (.*?):(.*?):(.*?)$/",$iplog[3],$mt);
			$dat=$mt[3].".".$mt[2].".".$mt[1]." ".$mt[4].":".$mt[5];

			if (!isset($ulist[$iplog[1]])) $ulist[$iplog[1]] = check_users_city_data($iplog[1]);
			if (!isset($ulist[$iplog[2]])) $ulist[$iplog[2]] = check_users_city_data($iplog[2]);

			$ulist[$iplog[1]]['hidden'] = 0;
			$ulist[$iplog[2]]['hidden'] = 0;
			echo $dat." ".nick_hist($ulist[$iplog[1]])." => ".nick_hist($ulist[$iplog[2]]).$del."<BR>";
			$allsecondnicks[$i]=$iplog[1]; $i++; $allsecondnicks[$i]=$iplog[2]; $i++;
		}
	}
	echo "</div>Другие ники персонажа: ";
	$allsecondnicks=array_unique($allsecondnicks);
	foreach($allsecondnicks as $key => $val) {
		$ulist[$val]['hidden'] = 0;
		$asn.= nick_hist($ulist[$val]).", ";
	}

	$asn=rtrim($asn,", ");
	echo $asn;

	$access=check_rights($own);

	$limit = 10;
	if (isset($_GET['view100']) && (ADMIN || $access['viewmanyips'])) $limit = 100;

	$lplist = mysql_query("SELECT * FROM oldbk.`iplog` WHERE `owner` = '{$user['id']}' ORDER by `id` DESC LIMIT ".$limit);
	echo "<br><br>Последние заходы персонажа:";
	echo "<table border=1><tr><td>&nbsp;</td><td><center><b>Дата</b></center></td><td><center><b>IP</b></center></td><td><center><b>Геолокация</b></center></td></tr>";
	$ind=0;


	if (!isset($reader)) $reader = new Reader('./GeoIP/GeoLite2-City.mmdb');

	while ($iplog = mysql_fetch_array($lplist)) {
		$ind++;
		$dat=date("d.m.y H:i",$iplog['date']);
		$ip=$iplog['ip'];
		if ($user['klan']=='Adminion' || $user['klan']=='radminion') 	{
			if ($iplog['owner'] == 703213) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 102904) {$ip="0.0.0.0";}
		} else {
			if ($iplog['owner'] == 2) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 3) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 5) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 9) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 12) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 190672) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 76009) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 326) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 703213) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 14897) {$ip="0.0.0.0";}

			if ($iplog['owner'] == 690426) {$ip="0.0.0.0";}

			if ($iplog['owner'] == 697032) {$ip="0.0.0.0";}

			if ($iplog['owner'] == 8383) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 6745) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 1011) {$ip="80.178.242.188";}
			if ($iplog['owner'] == 272447) {$ip="84.16.235.118";}
			if ($iplog['owner'] == 219523) {$ip="188.162.39.103";}
			if ($iplog['owner'] == 102904) {$ip="0.0.0.0";}
			if ($iplog['owner'] == 654040) {$ip="91.194.190.168";}
		}

		$t = explode("|",$ip);
		$country = "";
		foreach($t as $k => $v) {
			try {
				$record = $reader->city($v);
		       	} catch (Exception $ex) {

			}

			if ($record) {
				if (strlen($country)) $country .= " | ";
				$country .= iconv("UTF-8","windows-1251",$record->country->names['ru']);
				if (isset($record->city->names["ru"])) $country .= iconv("UTF-8","windows-1251"," (".$record->city->names["ru"].") ");
			}
		}


		$ip = preg_replace('#([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}?)#iU', '<a href=https://apps.db.ripe.net/search/query.html?searchtext=$1+&search%3AdoSearch=Search target=_blank>$1</a>', $ip);

		echo "<tr><td>&nbsp; $ind &nbsp;</td><td>&nbsp;&nbsp; $dat &nbsp;&nbsp;</td><td>&nbsp; $ip &nbsp;&nbsp;</td><td> $country </td></tr>";
	}
	echo "</table>";
	if ($limit == 10 && (ADMIN || $access['viewmanyips'])) echo '<a href="?'.$user['id'].'&view100=1">Показать 100 IP адресов</a>';
}

}
?>
<div align=center style="position:absolute; visibility: hidden;" id=hint33></div>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter1256934 = new Ya.Metrika({id:1256934,
                    accurateTrackBounce:true, webvisor:true});
        } catch(e) {}
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/1256934" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<BR>
<HR>
			<!--LiveInternet counter--><script type="text/javascript"><!--
			document.write("<a href='http://www.liveinternet.ru/click' "+
			"target=_blank><img style='float:left; ' src='http://counter.yadro.ru/hit?t54.2;r"+
			escape(document.referrer)+((typeof(screen)=="undefined")?"":
			";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
			screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
			";"+Math.random()+
			"' alt='' title='LiveInternet: показано число просмотров и"+
			" посетителей за 24 часа' "+
			"border='0' ><\/a>")
			//--></script><!--/LiveInternet-->


		<!--Rating@Mail.ru counter-->
		<script data-cfasync="false" language="javascript" type="text/javascript"><!--
			d=document;var a='';a+=';r='+escape(d.referrer);js=10;//--></script>
		<script data-cfasync="false" language="javascript1.1" type="text/javascript"><!--
			a+=';j='+navigator.javaEnabled();js=11;//--></script>
		<script data-cfasync="false" language="javascript1.2" type="text/javascript"><!--
			s=screen;a+=';s='+s.width+'*'+s.height;
			a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth);js=12;//--></script>
		<script data-cfasync="false" language="javascript1.3" type="text/javascript"><!--
			js=13;//--></script><script data-cfasync="false" language="javascript" type="text/javascript"><!--
			d.write('<a href="http://top.mail.ru/jump?from=1765367" target="_blank">'+
					'<img src="http://df.ce.ba.a1.top.mail.ru/counter?id=1765367;t=49;js='+js+
					a+';rand='+Math.random()+'" alt="Рейтинг@Mail.ru" border="0" '+
					'height="31" width="88"><\/a>');if(11<js)d.write('<'+'!-- ');//--></script>
		<noscript><a target="_blank" href="http://top.mail.ru/jump?from=1765367">
				<img src="http://df.ce.ba.a1.top.mail.ru/counter?js=na;id=1765367;t=49"
					 height="31" width="88" border="0" alt="Рейтинг@Mail.ru"></a></noscript>
		<script data-cfasync="false" language="javascript" type="text/javascript"><!--
			if(11<js)d.write('--'+'>');//--></script>
		<!--// Rating@Mail.ru counter-->

			<div style="font-size:10px;">
			<?=$_COPYRIGHT;?>
			</div>

		</div>
<!-- Asynchronous Tracking GA bottom piece counter-->
<script type="text/javascript">
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
})();
</script>

<!-- Asynchronous Tracking GA bottom piece end -->
<?

if ($user[id]==8540)
	{
	echo "<div align=right>
	<a target=_blank href=https://passport.webmoney.ru/asp/certview.asp?wmid=263469999071><img src='http://www.webmoney.ru/img/icons/88x31_wm_blue_on_white_en.png'></a>
	<!-- begin WebMoney Transfer : attestation label -->
	<a href=\"https://passport.webmoney.ru/asp/certview.asp?wmid=263469999071\" target=_blank><IMG SRC=\"http://www.webmoney.ru/img/icons/88x31_wm_v_blue_on_white_ru.png\" title=\"Здесь находится аттестат нашего WM идентификатора 263469999071\" border=\"0\"><br><font size=1>Проверить аттестат</font></a>
	<!-- end WebMoney Transfer : attestation label -->
	</div>";
	} elseif($user['id'] == '546433') {
		echo "<div align=right>
		<a target=_blank href=https://passport.webmoney.ru/asp/certview.asp?wmid=374554818254><img src='http://www.webmoney.ru/img/icons/88x31_wm_blue_on_white_en.png'></a>
		<!-- begin WebMoney Transfer : attestation label -->
		<a href=\"https://passport.webmoney.ru/asp/certview.asp?wmid=374554818254\" target=_blank><IMG SRC=\"http://www.webmoney.ru/img/icons/88x31_wm_v_blue_on_white_ru.png\" title=\"Здесь находится аттестат нашего WM идентификатора 374554818254\" border=\"0\"><br><font size=1>Проверить аттестат</font></a>
		<!-- end WebMoney Transfer : attestation label -->
		</div>";
	}
include "end_files.php";
?>
</BODY>
</HTML>
<?

/////////////////////////////////////////////////////
    if (isset($miniBB_gzipper_encoding)) {
    $miniBB_gzipper_in = ob_get_contents();
    $miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    $miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
    $miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
    $gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
    $percent = round($gzpercent);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    ob_clean();
    header('Content-Encoding: '.$miniBB_gzipper_encoding);
    echo $miniBB_gzipper_out;

 	if ($own['klan']=='Adminion' || $own['klan']=='radminion' || $own['klan']=='pal' || ($own['align'] > '2' && $own['align'] < '3') || ($own['align'] > '1' && $own['align'] < '2') || ($own['id']==$user['id'])  )
 	{
 	//не сохраняем
 	}
 	else
 	{
		if (($need_to_save==true) and ($GID>0))  { save_inf_tofile($miniBB_gzipper_out,$user['id']);  }
		elseif (($need_to_save==true) and ($GLOGIN!=''))  { save_inf_tofile($miniBB_gzipper_out,$user['login']);  }
	}



    }
/////////////////////////////////////////////////////

?>
