<?
	session_start();

	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

	include "connect.php";
	include "functions.php";

	$access = false;
	
   	if(($user[align]>1&&$user[align]<2)||($user[align]>2&&$user[align]<3))
	{
		$access=check_rights($user);
	}
    	if(!$access) {
		die('Страница не найдена...');
	} elseif($access[zhhistory] != 1){
		die('Страница не найдена...');
	}
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content="no-cache, max-age=0, must-revalidate, no-store">
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<script type="text/javascript" src="http://i.oldbk.com/i/popup/ZeroClipboard.js"></script>
<script type="text/javascript" src="/i/globaljs.js"></script>
<SCRIPT LANGUAGE="JavaScript" SRC="http://chat.oldbk.com/i/ch5.js?ver=3"></SCRIPT>
<script>

function OpenMenuZH2(evt,level){
    evt = evt || window.event;
    evt.cancelBubble = true;
    // Показываем собственное контекстное меню
    var menu = document.getElementById("oMenu");
    var html = "";
	login=(evt.target || evt.srcElement).innerHTML;

	var i1, i2;
	if ((i1 = login.indexOf('['))>=0 && (i2 = login.indexOf(']'))>0) login=login.substring(i1+1, i2);

	var login2 = login;
	login2 = login2.replace('%', '%25');
	while (login2.indexOf('+')>=0) login2 = login2.replace('+', '%2B');
	while (login2.indexOf('#')>=0) login2 = login2.replace('#', '%23');
	while (login2.indexOf('?')>=0) login2 = login2.replace('?', '%3F');

       	leveltxt = "";

	if (typeof level != "undefined") {
		//leveltxt = ' ['+level+']';
	}

	html  = '<span style="cursor: default; color:black; white-space:nowrap;" class="menuItem">'+login+leveltxt+'</span><br>'+
	'<a href="javascript:void(0)" class="menuItem" onclick="window.open(\'http://capitalcity.oldbk.com/inf.php?login='+login+'\')"; cMenu();">Инфо</a>'+
	'<div class="d_clip_button" data-clipboard-text="'+login+'" id="d_clip_button"><A href="#" class="menuItem" onclick="return false;">Скопировать</a></div>';


 // Если есть что показать - показываем
    if (html){
        menu.innerHTML = html;
        posx = defPosition2(evt).y;

	if (posx > 100) {
		if (document.body.offsetHeight - posx < 80) posx = posx - 80;
	}

	menu.style.top = posx + "px";
 
        menu.style.left = defPosition2(evt).x + "px";
        menu.style.display = "";
    }
	if (flagpop==0){
		flagpop=1;
	} else {
	}
    // Блокируем всплывание стандартного браузерного меню
    //clip = new Clipboard(document.getElementById('d_clip_button'));
    return false;
}
</script>
<body leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 >
<h1>История жалоб</h1>
<form>
Дата: <input type="text" name="date" value="<?php if (isset($_GET['date'])) echo htmlspecialchars($_GET['date'],ENT_QUOTES); else echo date("d.m.Y") ?>"> <input type="submit" value="Просмтреть">
<br>
</form>
<?php

	if (isset($_GET['date'])) {
		$t = explode(".",$_GET['date']);
		$filepath = '/www/cache/chat/chat_zh_'.sprintf("%02d",intval($t[0]))."_".sprintf("%02d",intval($t[1]))."_".intval(substr($t[2],2,2)).".txt";
		if (file_exists($filepath)) {
			$t = file_get_contents($filepath);
			$t = explode("\r\n",$t);
			while(list($k,$v) = each($t)) {
				preg_match('~<span class=date2>(.*)</span>~iU',$v,$m);
				echo $m[0]." ";
				$t2 = preg_replace('~<span class=date2>(.*)</span>~iU','',$v)."<br>";
				$t2 = str_replace('{[]}','',$t2);
				$t2 = str_replace('OpenMenu(','OpenMenuZH2(',$t2);
				$t2 = preg_replace('~<span oncontextmenu=OpenMenuZH2\(\)>(.*)</span>~iU','<a href="#"><span oncontextmenu="return OpenMenuZH2(event,10);"><b>$1</b></span></a>',$t2);
				echo $t2;
			}
		} else {
			echo 'Жалобы не найдены';
		}
	}
?>
<DIV ID="oMenu" style="position:absolute; border:1px solid #666; background-color:#CCC; display:none;margin:5px;padding:5px;"></DIV>
<DIV ID="ClearMenu" style="position:absolute; border:1px solid #666; background-color:#e2e0e0; display:none; "></DIV>
</body>
</html>