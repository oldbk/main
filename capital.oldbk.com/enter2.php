<?php
require 'connect.php';
include("functions.php");
session_start();
//Предпологается, что после логина на главной создаются две куки PHPSESSID, battle
//Предпологаемые цифры: 222

use GeoIp2\Database\Reader;

$query = mysql_query("SELECT * FROM users WHERE id = " . intval($_SESSION['uid2'])) or die();
$user = mysql_fetch_assoc($query);

if($user['second_password'] == "") {
    header("location: battle.php");
	exit;
}

/** @var \components\models\User2fa $user2fa */
$user2fa = \components\models\User2fa::find($user['id']);
if($user2fa && $user2fa->isEnabled()) {
	header("location: enter3.php");
	exit;
}


if(isset($_POST["secondsession"])) {
	/*
	Флеш создаёт следующий хеш мд5(PHPSESSID.SECOND_PATH.battle)
	Посылает форму с переменными
	phpsessionid
	battle
	secondsession
	*/
	     if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
   {
   $ip=$_SERVER['HTTP_CLIENT_IP'];
   }
  if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
   {
   $ip.='|'.$_SERVER['HTTP_X_FORWARDED_FOR'];
   }
   

   	 if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))  
   	 	{
	   	 $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP']; 
   		 $ip.='|'.$_SERVER['REMOTE_ADDR'];
	   	 }
   	elseif (!empty($_SERVER['REMOTE_ADDR']))
	   {
	   $ip.='|'.$_SERVER['REMOTE_ADDR'];
	   }
	/*Тест проверка для показания действия. */
	if(md5(session_id().$user['second_password'].$_SESSION["uid2"]) == $_POST["secondsession"]) {
		$str             = "Цифры верны";
		$_SESSION['uid'] = $_SESSION['uid2'];

		mysql_query("UPDATE `users` SET  `sid` = '".session_id()."' WHERE `id` = " . intval($user['id']));

        if(($user[align]>2 && $user[align]<3)||$user[align]==7)
        {
        	 $_SESSION['adm_view']=1;
        }
        else
        {
//        	 	mysql_query("INSERT INTO `online` (id, date, room) values ('".$user['id']."','".time()."', '".$user['room']."') ON DUPLICATE KEY UPDATE `date` ='".time()."';");
        }

       	$last_ip=mysql_fetch_array(mysql_query('select max(id),ip,owner from `iplog` where owner='.$user['id'].' group by id,ip order by id desc limit 1;'));

	    mysql_query("INSERT INTO oldbk.`iplog` (owner,ip,date) values ('".$user['id']."','$ip','".time()."');");

	    if($_SESSION['adm_view']!=1)
	    {

	   		 //логирование в xml если чар есть в партнерке
		            $pidd=get_pid($user);
		            if ($pidd[partner]>0)
		            {
		            //чар партнерский
		            		$rezzz=make_record($user[id],$pidd[partner],11,1);
		            }
		            /////////////////////////////////////


	    	if ($data['hidden'] == 0) addch ("Вас приветствует <a href=javascript:top.AddTo(\"".$user['login']."\")><span oncontextmenu=\"return OpenMenu(event,8)\">".$user['login']."</span></a>!   ",$user['room'],$user['id_city']);
	        $data1 = mysql_query("SELECT f.*, u.login as owlogin ,  u.id as ownid , u.show_advises, u1.login as frlogin FROM `friends` f
				inner join users u
				on u.id=f.owner
				left join users u1
				on u1.id=f.friend
            WHERE f.friend = '".$user['id']."' AND (`type`=0 OR `type`=2 ) and u.hidden=0 and u.ldate >'".(time()-60*3)."';");
            
            $frend_ids=array();
            $enemy_ids=array();
            while($row=mysql_fetch_array($data1))
            {
            	$show_advises=explode(',',$row['show_advises']);
		if($show_advises[1]==1 && $row['type'] == 0)
		{
		 $frend_ids[]=$row['ownid'];
		}
		else
		if($show_advises[5]==1 && $row['type'] == 2)
		{

		$enemy_ids[]=$row['ownid'];
		}
            }
            
            //шелам системку
             if (count($frend_ids) > 0)
             {
             $txt="Вас приветствует <a href=javascript:top.AddTo(\"".$user['login']."\")><span oncontextmenu=\"return OpenMenu(event,8)\">".$user['login']."</span></a>!";
             if ($data['hidden'] == 0) addch_group($txt,$frend_ids);
             }
            
             if (count($enemy_ids) > 0)
             {
	     $txt="Вас приветствует ваш враг <a href=javascript:top.AddTo(\"".$user['login']."\")><span oncontextmenu=\"return OpenMenu(event,8)\">".$user['login']."</span></a>!";             
             if ($data['hidden'] == 0) addch_group($txt,$enemy_ids);
             }
            
            
	  }

	mysql_query("INSERT INTO `users_counter` (owner,count,logdate) values ('".$user['id']."','1', '".date("Y-m-d")."') ON DUPLICATE KEY UPDATE `count` =`count`+1  ;");

	$get_ip_setups=unserialize($user['gruppovuha']);
	if ($get_ip_setups[6]==1)
	{	
            if (($last_ip['ip']!=$ip) and ($last_ip[0]>0)) 
       		{
			$reader = new Reader('./GeoIP/GeoLite2-City.mmdb');

			$addnfo = "";

			$v = explode("|",$last_ip['ip']);
			$v = $v[0];

			$record = false;
			try {
				$record = $reader->city($v);
            		} catch (Exception $ex) {

		        }

			if ($record) {
				if (isset($record->city->names["ru"])) $addnfo .= iconv("UTF-8","windows-1251"," ".$record->city->names["ru"].", ");
				if (isset($record->country->names['ru'] )) $addnfo .= iconv("UTF-8","windows-1251",$record->country->names['ru']);
			}

            		addchp ('<font color=red>Внимание!</font> В предыдущий раз вашим персонажем заходили с другого IP ( '.$last_ip['ip'].' '.$addnfo.').','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
      	 	}
       	}
       	
	if ($get_ip_setups[9]>0)
		{
			$getbank=mysql_fetch_array(mysql_query("select * from oldbk.bank where id='{$get_ip_setups[9]}' and owner='{$user['id']}' "));
			if ($getbank['id']>0)
			{
			$_SESSION['bankid'] =$getbank['id'];
			}
		}
       	
	    $rs = mysql_query("SELECT * FROM oldbk.`telegraph` WHERE `owner` = '".$user['id']."';");

	    mysql_query("DELETE FROM oldbk.`telegraph` WHERE `owner` = '".$user['id']."';");

	    while($r = mysql_fetch_assoc($rs)) {
			addchp ($r['text'],'{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
	   	}

 	$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where stat=1 limit 1"));
	if ($get_ivent['id']>0)
		{
		  addchp ('<font color=red>Внимание!</font> '.$get_ivent['info'],'{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);
		}

		unset($_SESSION['uid2']);

     //   include('war_check_enter.php');

	if(!$_SESSION['boxisopen'])
	{
	  $effect88 = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}' and `type` = '88' LIMIT 1;"));
	  if ($effect88[id]>0)
	  {
	  }
	  else
	  {
	  	$_SESSION['boxisopen']='open';
	  }
    	}
    	
    		if (($user['id']==3) OR ($user['id']==4))
		{
		//ангелы
		}
		else
    		if ($user['align']!=2.4)
    		{
		mysql_query("UPDATE `users` SET `odate` = ".time()." , `ldate` = ".time()."  WHERE `id` = {$user['id']};");
		}
		
		find_items_timeout($user);//системки о предметах срок которых заканчивается менее суток
		do_present_items($user); //подарки нубам	
		
		if (isset($_SESSION['KO_login'])) 
			{
			unset($_SESSION['KO_login']);
			}
	
		header("location: battle.php");
		exit;
	}
	else
	{
		$str = "Цифры не верны";
	}
} else {
	$str="Введите цифры";
	// В переменных флеша передаётся строчка которая отображается в флеше как статус сообщение
};
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>
<title>ОлдБК - Старый Бойцовский клуб</title>
<link rel="stylesheet" href="i/enter2.css" type="text/css"/>
<meta http-equiv="expires" content="0"/>
<meta name="resource-type" content="document"/>
<meta name="distribution" content="global"/>
<meta name="keywords" content="БК, combats, Бойцовский клуб, История, Предметы БК, БК 2003, Броня Печали, Ветераны, Старый клуб, Старый БК, Старый бойцовский клуб, Ностальгия"/>
<meta name="description" content=""/>
<meta name="robots" content="index, follow"/>
<meta name="revisit-after" content="30 days"/>
<meta name="rating" content="general"/>
<style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background-color: #e2e0e0;
        }
        .password_wrapper {
            background: url("i/enter/x-bg.jpg") repeat-x;
            height: 703px;
            width: 100%;
            margin-top: 50px;
        }
        .password_wrapper .p-middle {
            background: url("i/enter/center.jpg") no-repeat;
            width: 1376px;
            height: 703px;
            margin: 0 auto;
            position: relative;
        }
        .password_wrapper .p-middle .l-side {
            background: url("i/enter/left.png") no-repeat;
            height: 703px;
            width: 45px;
            float: left;
            margin-left: -45px;
        }
        .password_wrapper .p-middle .r-side {
            background: url("i/enter/right.png") no-repeat right;
            height: 703px;
            width: 45px;
            float: right;
            margin-right: -43px;
        }
        .password_wrapper #second_pass_div, .password_wrapper #content {
            position: absolute;
            width: 235px;
            height: 281px;
            left: 569px;
            top: 170px;
        }
		.password_wrapper table#calc {
			background-color:#e2e0e0;
			font-size: 14px;
			height: 100%;
			width: 100%;
			overflow: hidden;
		}
		.password_wrapper table#calc td {
			font-size: 14px;
			text-align: center;
		}
		.password_wrapper table#calc input {
			cursor:pointer;
			font-size:17px;
		}
    </style>
<?php
$isphone = false;
if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || stristr($_SERVER['HTTP_USER_AGENT'],'ipod') || stristr($_SERVER['HTTP_USER_AGENT'],'ipad') || stristr($_SERVER['HTTP_USER_AGENT'],'android') || isset($_GET['noflash'])) {
	$isphone = true;
}
?>

<?php
if (!$isphone) {
?>
    <script type="text/javascript" src="http://capitalcity.oldbk.com/i/swfobject.js"></script>
    <script type="text/javascript" src="http://capitalcity.oldbk.com/i/flash_api.js"></script>
    <script type="text/javascript">
	var isFlashInstalled = (function(){
		var b=new function(){var n=this;n.c=!1;var a="ShockwaveFlash.ShockwaveFlash",r=[{name:a+".7",version:function(n){return e(n)}},{name:a+".6",version:function(n){var a="6,0,21";try{n.AllowScriptAccess="always",a=e(n)}catch(r){}return a}},{name:a,version:function(n){return e(n)}}],e=function(n){var a=-1;try{a=n.GetVariable("$version")}catch(r){}return a},i=function(n){var a=-1;try{a=new ActiveXObject(n)}catch(r){a={activeXError:!0}}return a};n.b=function(){if(navigator.plugins&&navigator.plugins.length>0){var a="application/x-shockwave-flash",e=navigator.mimeTypes;e&&e[a]&&e[a].enabledPlugin&&e[a].enabledPlugin.description&&(n.c=!0)}else if(-1==navigator.appVersion.indexOf("Mac")&&window.execScript)for(var t=-1,c=0;c<r.length&&-1==t;c++){var o=i(r[c].name);o.activeXError||(n.c=!0)}}()};  
		return b.c;
	})();


	if (!isFlashInstalled) location.href = 'enter2.php?noflash=1';


        var flashvars = {};
        var params = {};
	flashvars.info = "<?=$str?>";
        params.menu = "false";
        params.quality = "best";
        params.scale = "noscale";
        params.wmode = "transparent";
        params.swliveconnect = "true";
        params.allowfullscreen = "true";
        params.allowscriptaccess = "always";
        params.allownetworking = "all";
        var attributes = {};
        attributes.id = "content";
        swfobject.embedSWF("i/second_pass.swf", "second_pass_div", "325", "281", "9.0.0", "expressInstall.swf", flashvars, params, attributes);
    </script>
<?php
} else {
?>
<script type="text/javascript" src="http://i.oldbk.com/i/js/md5-min.js"></script>
<script type="text/javascript">

var digstr = "";

function ClickDig(dig) {
	digstr += dig;
	ShowMask();
}

function ShowMask() {
	str = "";
	for (i = 0; i < digstr.length; i++) {
		str += "*";
	}	
	document.getElementById("digits").value = str;
}            

function ResetDig() {
	digstr = "";
	ShowMask();
}

function getCookie(c_name) 
{
    var i,x,y,ARRcookies=document.cookie.split(";");

    for (i=0;i<ARRcookies.length;i++)
    {
        x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
        y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
        x=x.replace(/^\s+|\s+$/g,"");
        if (x==c_name)
        {
            return unescape(y);
        }
     }
}

function EnterDig() {
	mystr = getCookie("PHPSESSID");
	mystr += hex_md5(digstr);
	mystr += getCookie("battle");
	document.getElementById("secondsession").value = hex_md5(mystr);
	document.getElementById("secondform").submit();
}

function ShowCalc() {
	var obj = document.getElementById("second_pass_div");
	var table = "";
	table += '<table id="calc" border=1 cellpadding=3 cellspacing=3><tr><td valign=center colspan=3><font style="font-size:22px;" color=red><?php echo $str?></font> &nbsp;&nbsp;&nbsp;&nbsp;<input style="width:98%; height: 50px; font-size: 45px;" type=text size=10 length=10 id="digits" disabled name="digits"></td></tr>';
	table += '<tr><td style="cursor:pointer;" OnClick="ClickDig(7);"> 7 </td><td style="cursor:pointer;" OnClick="ClickDig(8);"> 8 <td style="cursor:pointer;" OnClick="ClickDig(9);"> 9 </td></tr>';
	table += '<tr><td style="cursor:pointer;" OnClick="ClickDig(4);"> 4 </td><td style="cursor:pointer;" OnClick="ClickDig(5);"> 5 <td style="cursor:pointer;" OnClick="ClickDig(6);"> 6 </td></tr>';
	table += '<tr><td style="cursor:pointer;" OnClick="ClickDig(1);"> 1 </td><td style="cursor:pointer;" OnClick="ClickDig(2);"> 2 <td style="cursor:pointer;" OnClick="ClickDig(3);"> 3 </td></tr>';
	table += '<tr><td colspan=3 style="cursor:pointer;" OnClick="ClickDig(0);"> 0 </td></tr>';
	table += '<tr><td colspan=3><input type=button value="Войти" OnClick="EnterDig();"> <input type=button value="Очистить" OnClick="ResetDig();"></td></tr>';
	table += '</table><form method="POST" id="secondform"><input type=hidden name="secondsession" id="secondsession" value="">';
	obj.innerHTML = table;
}
</script>


<?php 
}
?>
</head>
<body <?php if ($isphone) echo 'OnLoad="ShowCalc();"'; ?>>

<div class="password_wrapper">
        <div class="p-middle">
		
<?php
if (!$isphone) {
?>
		<form id="second_form" name="second_form" method="post" style="display:none;">
		<!-- Посылаемая форма -->
			<input id='battle' name='battle' type="text" value="ddd"><br />
			<input id='phpsessionid' name='phpsessionid' type="text" value="ddd"><br />
			<input id='secondsession' name='secondsession' type="text" value="ddd">
			<input type="submit" name="enter" value="Send">
		</form>
<?php
} else { 
?>
<?php
}
?>
		<div id="second_pass_div" style="background-color:black;"></div>
        </div>
    </div>

</body>
</html>