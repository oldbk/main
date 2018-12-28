<?
echo "<font color='red'><b>";
	session_start();

/*
	if($_SESSION[ip]!=$_SERVER['REMOTE_ADDR'] )
{
die('');
}
*/
	$google = 1;
	include "connect.php";
	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
//	if ($user['align']!=2.4 and $user['align']!=2.7 and $user['align']!=2.8 and $user['align']!=2.6) die('Страница не найдена :)');
if ($user['align']!=2.4 and $user['align']!=2.7) die('Страница не найдена :)');

include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
	$online = mysql_fetch_row(mysql_query("select COUNT(*) from `users`  WHERE `ldate` >= ".(time()-60).";"));
	include "functions.php";
	if($_POST['add'] OR $_POST['add2']) {
		@setcookie("time",time());
	}
	if ($_POST['action']!="") {
		switch ($_POST['action']) {
			case "sysmsg":
				if (strlen($_POST['msg'])) {
					addch2all('<img src="http://i.oldbk.com/i/klan/radminion.gif"> <b>'.$_POST['msg'].'</b>');
					echo "Отправлено системное сообщение в чат.";
				}
				break;
			}
		
			
			
			
		}
?>
</b></font><p>
<HTML><HEAD><TITLE>Админ. панель Бойцовского клуба </TITLE>
<META content=INDEX,FOLLOW name=robots>
<META content="1 days" name=revisit-after>
<META http-equiv=Content-type content="text/html; charset=windows-1251">
<META http-equiv=Pragma content=no-cache>
<META http-equiv=Cache-control content=private>
<META http-equiv=Expires content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
<LINK href="i/main.css" type=text/css rel=stylesheet>
</HEAD>
<style>
.pleft {
	PADDING-RIGHT: 0px; PADDING-LEFT: 20px; PADDING-BOTTOM: 7px; MARGIN: 0px; PADDING-TOP: 3px
}
</style>
<script>
var xmlHttpp=[]

var chbuf = "";

function p(text) {
	chbuf += text+'<BR>';
}

function ajax_func(func,iid,getpar,postpar){
  xmlHttpp[iid]=GetXmlHttpObject1()
  if (xmlHttpp[iid]==null){
    alert ("Browser does not support HTTP Request")
    return
    }
  document.getElementById(iid).innerHTML="<img src='./i/loading2.gif' />";
  var url="./ajax/"+func+".php"
  url=url+"?"+getpar
  xmlHttpp[iid].open("POST",url,true);
  xmlHttpp[iid].onreadystatechange=function() {
  	if (xmlHttpp[iid].readyState==4 || xmlHttpp[iid].readyState=="complete") {
		document.getElementById("chat").innerHTML = "";
		chbuf = "";
		eval(xmlHttpp[iid].responseText);
		document.getElementById("chat").innerHTML = chbuf;
	//document.getElementById(iid).innerHTML=xmlHttpp[iid].responseText;
      }
    }
  xmlHttpp[iid].setRequestHeader("Content-Type","application/x-www-form-urlencoded");
  xmlHttpp[iid].send(postpar);
}

function GetXmlHttpObject1()
{
var xmlHttp1=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 xmlHttp1=new XMLHttpRequest();
 }
catch (e)
 {
 //Internet Explorer
 try
  {
  xmlHttp1=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  xmlHttp1=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return xmlHttp1;
}
</script>
<?
if ($user['align']<2.4 and $user['align']>2.8) die('У Вас недостаточно прав!');
?>

<div style='width:100%; height:10%;' id=adm_act>
Сейчас в клубе: <?=$online[0];?><br/>




<div style='clear:both; height:80%;'></div>
</div>
<div><input type=button OnClick="ajax_func('chat','chat','','filter='+document.getElementById('filter').value);" value='Refresh'><input id='filter'></div>
<div id=chat>
</div>
<br><br>
Отправить системное сообщение в чат:<br>
<form method=POST name='actform'>
<input type=hidden name='action' id='action' value="sysmsg">
<input type=text name='msg' id='msg' value=""> 
<input type="submit" value="Отправить">
</form>
