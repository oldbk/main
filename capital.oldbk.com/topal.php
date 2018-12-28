<?php
	session_start();
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<META HTTP-EQUIV="imagetoolbar" CONTENT="no">
<script type="text/javascript" src="/i/globaljs.js"></script>
</HEAD>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 onLoad="top.setHP(<?=$user['hp']?>,<?=$user['maxhp']?>)">
<div id=hint3 class=ahint></div>
<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

include "connect.php";
include "functions.php";
$add_timer=1;

$_GET['id'] = str_replace(" ","+",$_GET['id']);

$temp=explode("::",$_GET['id']);
$_GET['id']=$temp[0];

$real_id=decodein(dexorit($_GET['id']));
$_GET['id']=xorit(codein($real_id));



$usid=decodein(dexorit($temp[1]));

if ($usid!=$_SESSION['uid']) 
	{
	die("Сообщение не найдено!</body></html>");
	}

if(!$_SESSION['jaloba']) {
	$_SESSION['jaloba']=time()-($add_timer+1);
}
$pal_sleep=0;

if(!$real_id) {
	die('<font color=red><b>Наверное Архивариус растерял записи этого дела...1</b></font>');
} else if($pal_sleep==1) {
	die ('С молчанкой этого не сделать...');
} else if($_SESSION['jaloba']+$add_timer>time()) {
	die('<font color=red><b>Не чаще чем раз в '.$add_timer.' секунд!!!</b></font>');
} else if($real_id>0) {
	$cha = mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.chat where id='{$real_id}' and (room='{$user[room]}' OR room=-1) LIMIT 1 "));

	$cha[text] = str_replace(" privatehelpers ","",$cha[text]);
	preg_match("/:\[(.*)\]:\[(.*)\]:\[(.*)]:\[(.*)\]/",$cha[text],$math);

	$math[3] = preg_replace("/private \[team-1-(.*)-([1-9])]/iU", "private [team-1]</a>", $math[3]);
	$math[3] = preg_replace("/private \[team-2-(.*)-([1-9])]/iU", "private [team-2]</a>", $math[3]);

	$author=explode(':|:',$math[2]);

	if($cha[id]==$real_id) {
		if(!$_GET['add']) {
			mysql_query('SELECT * FROM zhalobi WHERE m_id='.$cha['id'].' LIMIT 1;');
			if(mysql_affected_rows()>0)  {
				echo '<font color=red><b>На это сообщение уже поступила жалоба.</b></font><br>';
			} else {
				echo ' <b>'.$author[0].':</b> '.$math[3].'
				<br><a href=?id='.$_GET['id'].'::'.$temp[1].'&add=1> Сообщить паладинам?</a><br>';
			}

			$arh[0]=1;
		} elseif($_GET['add']==1 && ($_SESSION['jaloba']+$add_timer<time()) && $real_id==$cha[id]) {
			mysql_query('SELECT * FROM zhalobi WHERE m_id='.$cha['id'].' LIMIT 1;');
			if(mysql_affected_rows()>0) {
				echo '<font color=red><b>На это сообщение уже поступила жалоба.</b></font>';
	                } else {
	                	echo '<font color=red><b>Сообщение отправлено.</b></font>';

                     		$realnick=$author[0];
	                	$_SESSION['jaloba']=time();
	                	mysql_query('INSERT INTO zhalobi SET m_id='.$cha['id'].';');

	                	$math[3]=str_replace('private','priv_ate',$math[3]);
                        	$math[3]=str_replace('team','tea_m',$math[3]);

                  		if  ($_SERVER["SERVER_NAME"]=='capitalcity.oldbk.com') {$ci=1; }
				else if ($_SERVER["SERVER_NAME"]=='avaloncity.oldbk.com') {$ci=2;}
				if($rooms[$math[4]]!='') {
					$room=$rooms[$math[4]];
				} else {
					$room='Комната не определена';
				}

				$aaa=$author[0];
				
				$auid=(int)($author[1]);
				 if ($auid>0)
				{
				$autor_user = mysql_fetch_assoc(mysql_query("SELECT * FROM `users` WHERE `id` = '{$auid}' LIMIT 1;"));
				if ($autor_user[id]>0)
					{
					if ($autor_user[hidden]>0)					
						{
						$author[0]='Невидимка:'.$autor_user[hidden];
						$aaa='Невидимка:'.$autor_user[hidden];						
						}
						else
						{
						$author[0]=$autor_user[login];
						$aaa=$autor_user[login];
						}
					}
				}

				$author[0]=str_replace(' ','%20',$author[0]);
				mysql_query("INSERT INTO `oldbk`.`chat` SET `text`= ':[".time().mysql_real_escape_string("]:[Архивариус:|:83]:[private [klan-pal-3] Жалоба от <b>".$user['login']."</b> на: [".$room."] <span class=date2>".$cha['cdate']."</span> <a href=javascript:top.AddTo(\"".$author[0]."\")><span oncontextmenu=\"return OpenMenu(event,10)\"><b>".$aaa."</b></span></a> ".$math[3]."]:[".$user['room'])."]', `city`='{$ci}'"); //работа с файлом

	     			$filename="chat_zh_".(date('d_m_y'));
		 		$fp = fopen ("/www/cache/chat/".$filename.".txt","a"); //открытие
				if ($fp) {
			       		if(flock ($fp,LOCK_EX)) {
						flock($fp,LOCK_UN);
						fwrite($fp,"Жалоба от <b>".$user['login']."</b> на: [".mysql_real_escape_string($room)."] <span class=date2>".$cha['cdate']."</span> <a href=javascript:top.AddTo(\"".$author[0]."\")><span oncontextmenu=\"return OpenMenu(event,10)\"><b>".$aaa."</b></span></a> ".$math[3]."\r\n");
					}
					fclose($fp);
				}
	                } 

			$arh[0]=1;
		} else {
			$arh[1]=1;

		}
		        
	} else {
		die('<font color=red><b>Наверное Архивариус растерял записи этого дела...2</b></font>');
	}
} else {
	die();
}

?>


</body>
</html>