<?php
/**
 * Created by PhpStorm.
 * User: me
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//компресия для инфы
///////////////////////
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

session_start();
include "../connect.php";
include "../ny_events.php";
require_once('../mailer/send-email2.php');
include ("cloud_api.php");
include "../config_ko.php";
include ('price.php');
include ('func.php');
setcookie ("link_from_com", '',time()-86400,'/','.oldbk.com', false);//удаляем куку
if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']) )     	 	{     	$_SERVER['REMOTE_ADDR']=$_SERVER['HTTP_CF_CONNECTING_IP'];    	}
define("REMIP",$_SERVER['REMOTE_ADDR']);


$user=$_SESSION['com_user'];
/** @var \components\Eloquent\User2fa $user2fa */
$user2fa = \components\Eloquent\User2fa::find($_SESSION['uid']);
if(!$user2fa) {
	$_SESSION['2fa_state'] = true;
	echo '<script>location.href = "index.php";</script>';
	die();
}

$err = '';
if(isset($_POST["2fa_code"])) {
	$google2fa = new \PragmaRX\Google2FA\Google2FA();
	$valid = $google2fa->verifyKey($user2fa->secret, $_POST["2fa_code"]);
	if($valid) {
		$_SESSION['2fa_state'] = true;
		echo '<script>location.href = "index.php";</script>';
		die();
    } else {
	    $err = 'Цифры не верны';
    }
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>ОлдБК - Коммерческий отдел</title>
	<meta name="keywords" content="бойцовский клуб, бк, онлайн игра, rpg, магия бой, игра фэнтези, fantasy, маг " />
	<meta name="description" content="Бойцовский клуб - rpg онлайн игра, он же БК, созданный в 2003 году. Борьба Тьмы и Света. Бои, магия, персонажи - всё это Бойцовский клуб ">
	<meta name="robots" content="index, follow"/>
	<meta name="author" content="oldbk.com">
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
	<link rel="apple-touch-icon-precomposed" sizes="512x512" href="https://i.oldbk.com/i/icon/oldbk_512x512.png" />
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="https://i.oldbk.com/i/icon/oldbk_144x144.png" />
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="https://i.oldbk.com/i/icon/oldbk_114x114.png" />
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="https://i.oldbk.com/i/icon/oldbk_72x72.png" />
	<link rel="apple-touch-icon-precomposed" sizes="58x58" href="https://i.oldbk.com/i/icon/oldbk_58x58.png" />
	<link rel="apple-touch-icon-precomposed" sizes="48x48" href="https://i.oldbk.com/i/icon/oldbk_48x48.png" />
	<link rel="apple-touch-icon-precomposed" sizes="29x29" href="https://i.oldbk.com/i/icon/oldbk_29x29.png" />
	<link rel="apple-touch-icon-precomposed" href="https://i.oldbk.com/i/icon/oldbk_57x57.png" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js" type="text/javascript"></script>
	<script src='https://oldbk.com/js/jquery.ddslick.min.js'></script>


	<link rel="stylesheet" type="text/css" href="https://i.oldbk.com/i/jscal/css/jscal2.css" />
	<link rel="stylesheet" type="text/css" href="https://i.oldbk.com/i/jscal/css/border-radius.css" />
	<link rel="stylesheet" type="text/css" href="https://i.oldbk.com/i/jscal/css/steel/steel.css" />
	<script type="text/javascript" src="https://i.oldbk.com/i/jscal/js/jscal2-1.9.js"></script>
	<script type="text/javascript" src="https://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>

	<script type="text/javascript" src="fullajax.js"></script>
	<script type="text/javascript" src="ajaxfileupload.js"></script>
	<script type="text/javascript" src="/i/showthing.js"></script>
	<script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-17715832-1']);
        var rsrc = /mgd_src=(\d+)/ig.exec(document.URL);
        if(rsrc != null) {
            _gaq.push(['_setCustomVar', 1, 'mgd_src', rsrc[1], 2]);
        }
        _gaq.push(['_trackPageview']);
        (function() {
            var ga = document.createElement('script');
            ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
	</script>
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

                $('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: '450px'  });


            }

        }

        function closeinfo()
        {
            $('#pl').hide(200);
        }

        $(window).resize(function() {
            $('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: '450px'  });
        });

        var Hint3Name = '';

        function showhide(id)
        {
            if (document.getElementById(id).style.display=="none")
            {document.getElementById(id).style.display="block";}
            else
            {document.getElementById(id).style.display="none";}
        }

        function showhide2(id)
        {
            if (document.getElementById(id).style.display=="none")
            {document.getElementById(id).style.display="inline";}
            else
            {document.getElementById(id).style.display="none";}
        }


        function new_runmagic(but,id,what){
//alert('test');
            var title='Впишите комментарий';
            var submbutton='';
            var magicformcontent='';
            var el = document.getElementById("hint3");
            magicformcontent=	'</TD></TR><TR><TD align=left><br><INPUT TYPE=text id="inp" NAME="coment">';
            submbutton='<br><br><center><INPUT id="button3" TYPE="submit" value=" «« ОТПРАВИТЬ »» "></center><br></TD></TR></TABLE></FORM></td></tr></table>';
//	alert (what);
            if (what==1)
            {el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
                '<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makedone><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}
            if (what==2)
            {el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
                '<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=answer><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}
            if (what==3)
            {el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
                '<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makepnotclear><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}
            if (what==4)
            {el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
                '<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenreset><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

            if (what==5)
            {el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
                '<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenresetemail><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

            if (what==6)
            {el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
                '<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenklanchange><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

            if (what==7)
            {el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
                '<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenaddrekrut><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

            if (what==8)
            {el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
                '<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenremoverekrut><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

            if (what==9)
            {el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
                '<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenresetbd><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

            if (what==10)
            {el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
                '<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makenchangesex><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}

            if (what==11)
            {el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=#CCC3AA><tr><td align=left bgcolor=#CCC3AA width=410><B>'+title+'</td><td width=20 align=right valign=top bgcolor=#CCC3AA style="cursor: pointer" onclick="closehint3();"><BIG><B>x</b></BIG></td></tr><tr><td colspan=2>'+
                '<form action="index.php" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=#FFF6DD><tr><td><INPUT TYPE=hidden name=act value=makeglavacancel><INPUT TYPE=hidden name=id value='+id+'>'+magicformcontent+submbutton;}


            var x=findPosX(but);
            var y=findPosY(but);
            var posx=x-150;
            var posy=y-200;
            el.style.visibility = "visible";
            el.style.left = posx + 'px';
            el.style.top = posy + 'px';
            el.style.zIndex = 999;
            document.getElementById('inp').focus();
            Hint3Name = 'coment';
        }
        function closehint3(){
            document.getElementById("hint3").style.visibility="hidden";
            Hint3Name='';
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

        function CheckComment() {
            if (document.getElementById("otherserv").checked) {
                var content=document.getElementById("commentarea").value;
                if (content=="") {
                    alert("Вы не заполнили поле Комментарий!");
                    return false;
                }
            }

            var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
            var address = document.getElementById("comemail").value;
            if(reg.test(address) == false) {
                alert('Введите корректный email адрес');
                return false;
            }

            document.getElementById("mainform").submit();
        }

        function Select_oplata() {
            if (document.getElementById("otype").value!=0) {
                document.getElementById("oplata").style.display="none";
                document.getElementById("step2").style.display="block";
                if (document.getElementById("otype").value==1) {
                    document.getElementById("obank").style.display="block";
                    document.getElementById("ekrinfo").style.display="block";
                    document.getElementById("sertinfo").style.display="none";
                    elements=document.getElementsByName("prot541");
                    for (var i = 0; i < elements.length; i++) {
                        elements[i].style.display="none";
                    }
                } else if (document.getElementById("otype").value==2) {
                    document.getElementById("sertinfo").style.display="block";
                    document.getElementById("ekrinfo").style.display="none";
                } else if (document.getElementById("otype").value==3) {
                    document.getElementById("ogold").style.display="block";
                    document.getElementById("ekrinfo").style.display="block";
                    document.getElementById("sertinfo").style.display="none";
                    elements=document.getElementsByName("prot541");
                    for (var i = 0; i < elements.length; i++) {
                        elements[i].style.display="none";
                    }
                }
            } else {
                alert('Выберите способ оплаты!');
            }
        }



	</script>
	<link rel="stylesheet" href="styles.css" type="text/css" media="screen"/>
	<style type="text/css">
		.dd-selected-image {float:none;}
		.dd-selected {display:inline;}
		.dd-option {display:inline-block;padding:0px;padding-left:10px;border-bottom:0px;}
		.dd-selected-text {font-weight:normal;font-size:12px;}
		#arttype {width:270px;}
		a.button{
			padding: 3px 4px;
			height: 30px;
			line-height: 30px;
			border-radius: 8px;
			border-bottom: solid 2px #919191;
			background: #fdeaa8;
			font-family: "Georgia", "Times New Roman", "Times", serif;
			color: black;
			text-align: center;
			text-decoration: none;
			tttext-shadow: 0 1px 1px #003A6C;
		}

		a.button:hover{
			background: #919191;
		}

		a.hot
		{
			color: #b2aea1;
		}
		a.hot:hover
		{
			color: #9a0902;
		}

		#enter1
		{
			font-weight: bold;
			FONT-SIZE: 11px;
			COLOR: #4d4528;
			FONT-FAMILY: tahoma;
			background: #f4f2e9;
			padding: 1px 10px;
			border: 2px solid #908869;
			text-decoration: none;
			outline: none;
		}


		#button3 {
			font-weight: bold;
			FONT-SIZE: 11px;
			COLOR: #4d4528;
			FONT-FAMILY: tahoma;
			background: #EDEADB;
			padding: 1px 10px;
			border: 2px solid #908869;
			text-decoration: none;
			outline: none;
		}
		#hint3 {
			VISIBILITY: hidden; WIDTH: 440px; POSITION: absolute; BACKGROUND-COLOR: #fff6dd; layer-background-color: #FFF6DD; border: 1px solid #003338;
		}
		#inp {
			BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif; margin-left: 4px; margin-right: 4px; width: 432px;
		}
		#inp1 {
			BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif; margin-left: 4px; margin-right: 4px; width: 300px;
		}
		SELECT {
			BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
		}
	</style>

</head>
<body leftmargin="0" rightmargin="0" bottommargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div id="pl" style="z-index: 300; position: absolute; left: 584px; top: 450px; width: 750px; height: 365px; background-color: rgb(238, 238, 238); border: 1px solid black; display: none;">
</div>
<div align="center" style="position:absolute;" id="hint3"></div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody><tr>
		<td height="1065" valign="top" class="leftY"><table width="100%" height="270" border="0" cellpadding="0" cellspacing="0" class="headLeft">
				<tbody><tr>
					<td height="270">&nbsp;</td>
				</tr>
				</tbody></table></td>
		<td width="1018" valign="top" background="i/main_bg.jpg"><table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tbody><tr>
					<td height="263" class="header">&nbsp;</td>
				</tr>
				<tr>
					<td height="700" valign="top" class="cont_cracks"><table width="960" border="0" align="center" cellpadding="0" cellspacing="0">
							<tbody><tr>
								<td height="312" valign="top">

							<div style="width: 300px;text-align: center;margin: 0 auto;margin-top: 150px;">
                                <h4 style="color:#800000;">Введите ваш Google Authenticator код</h4>
                                <form method="post">
                                    <input id='2fa_code' name='2fa_code' type="text" value="" style="padding: 5px;text-align: center" placeholder="Введите 6 цифр кода" autofocus autocomplete="off">
                                    <input type="submit" class="button2" name="saveproto" value="Отправить">
									<?php if($err): ?>
                                        <br><div style="color: #800000;"><strong><?= $err ?></strong></div>
									<?php endif; ?>
                                </form>
							</div>
<?php
page_bottom();

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
?>