<?php
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
include "/www/oldbk.com/connect.php";
include "/www/oldbk.com/ny_events.php";
require_once('../mailer/send-email2.php');
include ("cloud_api.php");
include "/www/oldbk.com/config_ko.php";
include ('price.php');
include ('newdelo.php');
setcookie ("link_from_com", '',time()-86400,'/','.oldbk.com', false);//удаляем куку
if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']) )     	 	{     	$_SERVER['REMOTE_ADDR']=$_SERVER['HTTP_CF_CONNECTING_IP'];    	}
define("REMIP",$_SERVER['REMOTE_ADDR']); 



if (isset($_POST['express'])) { unset($_POST['express']);} // отключаем оплату експерсс проверок

if ((time()>$KO_start_time2) and (time()<$KO_fin_time2))
	{
	//акция картинки
	for ($pp=1;$pp<=91;$pp++)
			{
			if (isset($PRICE[$pp]['cost']))
				{
				$PRICE[$pp]['cost']=round($PRICE[$pp]['cost']*0.5,2);
				}
			}
	}
	 
include ('func.php');

function normJsonStr($str){
    $str = preg_replace_callback('/\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $str);
    return iconv('cp1251', 'utf-8', $str);
}

if ($_SESSION['uid']>0)
{
	//генерация счета для окпай
	if ( ($_POST['okpay_type']==1001) and (($_POST['okpay_subtype']>=41) and ($_POST['okpay_subtype']<=54)) )
	{
					$RUR=get_rur_curs();
 					$IncCurrLabel_str='';
					$ok_currency='USD';
					$srow='sum_usd';					
					
					if ($_POST['okpay_subtype']==41)
						{
						$IncCurrLabel='MTS';
						$ok_currency='RUB';
						$srow='sum_rub';
						}
					else
					if ($_POST['okpay_subtype']==42)
						{
						$IncCurrLabel='TL2';
						$ok_currency='RUB';						
						$srow='sum_rub';						
						}	
					else
					if ($_POST['okpay_subtype']==43)
						{
						$IncCurrLabel='BLN';
						$ok_currency='RUB';						
						$srow='sum_rub';						
						}			
					else
					if ($_POST['okpay_subtype']==44)
						{
						$IncCurrLabel='MGF';
						$ok_currency='RUB';												
						$srow='sum_rub';						
						}			
					else
					if ($_POST['okpay_subtype']==45)
						{
						$IncCurrLabel='YMO';
						$ok_currency='RUB';			
						$srow='sum_rub';															
						}			
					else
					if ($_POST['okpay_subtype']==46)
						{
						$IncCurrLabel='QIW';
						$ok_currency='RUB';
						$srow='sum_rub';																		
						}			
					else
					if ($_POST['okpay_subtype']==47)
						{
						$IncCurrLabel='ALF';
						$ok_currency='RUB';
						$srow='sum_rub';							
						}			
					else
					if ($_POST['okpay_subtype']==48)
						{
						$IncCurrLabel='SBR';
						$ok_currency='RUB';		
						$srow='sum_rub';																
						}			
					else
					if ($_POST['okpay_subtype']==49)
						{
						$IncCurrLabel='VMF';
						}			
					else
					if ($_POST['okpay_subtype']==50)
						{
						$IncCurrLabel='WMT';
						}
					else
					if ($_POST['okpay_subtype']==51)
						{
						$IncCurrLabel='BTC';
						}			
					else
					if ($_POST['okpay_subtype']==52)
						{
						$IncCurrLabel='MFS';
						}			
					else
					if ($_POST['okpay_subtype']==53)
						{
						$IncCurrLabel='WON';
						}			
					else
					if ($_POST['okpay_subtype']==54)
						{
						$IncCurrLabel='PSB';
						}															
 	
			echo "<html>"; 		
			echo "<body>"; 			
			$ur=check_users_city_data($_SESSION['uid']);

								if ($ok_currency=='USD')
					 			{
		 						$okpay_amount=$PRICE[228][cost];// 1$
								$okpay_amount_ekr=$okpay_amount; // екры					 			
					 			}
					 			else
					 			{
					 			$okpay_amount_ekr=$PRICE[228][cost];//1$
					 			$okpay_amount=round($okpay_amount_ekr*$RUR,2);	//рубли
					 			}			
						$okpay_description='Лечение травмы для персонажа: '.$ur['login'];
						
						
						mysql_query("INSERT INTO `oldbk`.`trader_balance_okpay` SET `owner`='{$ur[login]}',`owner_id`='{$ur[id]}',`bank_id`='".(int)($_SESSION['bankid'])."',`sum_ekr`='{$okpay_amount_ekr}',  `{$srow}`='{$okpay_amount}', `currency`='{$ok_currency}' ,  `param`='1001' , `description`='{$okpay_description}' ;");
						 if (mysql_affected_rows()>0)
						 	{
							$okpay_order_id=mysql_insert_id(); //Уникальный ID покупки в Вашем магазине.
						 	}	
						 	
					if ($okpay_order_id>0)
				 	{
					if ($IncCurrLabel!='') $IncCurrLabel_str="&ok_direct_payment=".$IncCurrLabel;
					
					// build URL
					$okpay_description=iconv('windows-1251','UTF-8', $okpay_description);
					
					$url = "https://checkout.okpay.com?ok_receiver=admin@oldbk.com&ok_currency=".$ok_currency.$IncCurrLabel_str."&ok_item_1_type=service&ok_item_1_price={$okpay_amount}&ok_item_1_name={$okpay_description}&ok_item_1_article={$okpay_order_id}";
				
					echo '<script language="JavaScript"> 	
						location.href="'.$url.'";
						</script>';
					}
		echo'</body>
			</html>'; 
		die();

	}
	elseif  ($_POST['liqpay_type']==1001) //генерация для ликпай 
	{

			echo "<html>"; 	
			echo "<body>"; 	
			$ur=check_users_city_data($_SESSION['uid']);			
			
			$private_key='P7ROKlOYV2ARpeBeADF7aQ25b0PTNi0IGrCKhNyN'; 
			$liqpay_array['version']=3;
			$liqpay_array['public_key']='i85877192887';		
			$liqpay_array['recurringbytoken']=0;		//default 0 Этот параметр позволяет генерировать card_token плательщика, который вы получите в callback запросе на server_url. card_token позволяет проводить платежи в offline используя метод payment/paytoken. Услуга активируется через менеджера LiqPay. Возможные значения: 1 
			$liqpay_array['type']='buy';
			$liqpay_array['server_url']='http://capitalcity.oldbk.com/bank_result_liqpay.php'; //обработчик
			$liqpay_array['result_url']='http://capitalcity.oldbk.com/bank.php?liqpayok=true'; //перекидка после оплаты

			$liqpay_array['sandbox']=0; //Включает тестовый режим для разработчиков. Деньги на карту не зачисляются. Чтобы включить тестовый режим, необходимо передать значение 1. Все тестовые платежи будут иметь статус sandbox - успешный тестовый платеж. 
		
			$liqpay_amount=$PRICE[228][cost];
			
			$liqpay_array['amount']=$liqpay_amount;			
			$liqpay_array['currency']='USD';
			$liqpay_array['description']='Лечение травмы для персонажа: '.$ur['login'];
			
			mysql_query("INSERT INTO `oldbk`.`trader_balance_liqpay` SET `owner`='{$ur[login]}',`owner_id`='{$ur[id]}',`bank_id`='".(int)($_SESSION['bankid'])."',`sum_ekr`='{$liqpay_amount}', `param`='1001' , `description`='{$liqpay_array['description']}' ;");
			if (mysql_affected_rows()>0)
					 	{
						$liqpay_array['order_id']=mysql_insert_id(); //Уникальный ID покупки в Вашем магазине. Максимальная длина 255 символов.					 	
					 	}
				
			if ($liqpay_array['order_id']>0)
				 	{
					$liqpay_array['description']=normJsonStr($liqpay_array['description']);
				 	
						$json_string=json_encode($liqpay_array);				 
						$data=base64_encode($json_string);
						$signature = base64_encode(sha1($private_key.$data.$private_key, 1));
						echo '<form method="POST" action="https://www.liqpay.com/api/checkout" accept-charset="utf-8" name="frm">';
						echo '<input type="hidden" name="data" value="'.$data.'"/>
						<input type="hidden" name="signature" value="'.$signature.'"/></form>';
						echo '<script language="JavaScript"> 	
						document.frm.submit(); 	
						</script>'; 
					}
			echo "</body>"; 										
		 	echo "</html>";
		 	die();
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
<body leftmargin=0 rightmargin=0 bottommargin=0 topmargin=0 marginwidth=0 marginheight=0>
<div id="pl" style="z-index: 300; position: absolute; left: 155px; top: 450px;width: 750px; height:365px; background-color: #eeeeee; border: 1px solid black; display: none;">
</div>			
<div align=center style="position:absolute;" id=hint3></div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="1065" valign="top" class="leftY"><table width="100%" height="270" border="0" cellpadding="0" cellspacing="0" class="headLeft">
<tr>
<td height="270">&nbsp;</td>
</tr>
</table></td>
    <td width="1018" valign="top" background="i/main_bg.jpg"><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td height="263" class="header">&nbsp;</td>
</tr>
<tr>
<td height="700" valign="top" class="cont_cracks"><table width="960" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
<td height="312" valign="top"><table width="885" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
<td height="15">&nbsp;</td>
</tr>
<!--content start-->

<?php



if ((isset($_REQUEST['login']) and $_POST['psw']!='') OR (($_REQUEST['alog']!='') and ($_REQUEST['uid']!='')))   {
	$user=AuthorizeToCommerce($_REQUEST['login'],$_POST['psw'],$_REQUEST['uid'],$_REQUEST['alog']);
} elseif ($_REQUEST['act']=='logout') {
	unset($_SESSION['com_user']);
	unset($_SESSION['save_proto_memory']);
	unset($_SESSION['mkartch']);
	unset($_SESSION['ekrids']);
	unset($_SESSION['mkartch_free']);
	unset($_SESSION['save_proto_memory']);
	unset($_SESSION['mkartch_new_slot']);	
	unset($_SESSION['mkartch_new_slot_from_hram']);	
	unset($_SESSION['mkartch_new_express']);	
	unset($_SESSION['mkartch2_pay_vau']);
	unset($_SESSION['mkartch2_pay_vau_ids']);
	unset($_SESSION['uid']);
	unset($_SESSION['select_present']);
	unset($_SESSION['2fa_state']);
	unset($_SESSION);
	session_unset();	
	session_destroy();	
}
	
if (!isset($_SESSION['com_user'])) {
	$_SESSION['com_user']=$user;
} else {
	$user=$_SESSION['com_user'];
}


if (isset($_GET['changeadmmode'])) {
	if ($user != '' && IsAdmin()) {
		if (isset($_SESSION['comadminmode'])) {
			if ($_SESSION['comadminmode'] == 1) {
				$_SESSION['comadminmode'] = 2;
			} elseif ($_SESSION['comadminmode'] == 2) {
				$_SESSION['comadminmode'] = 1;
			}
		} else {
			$_SESSION['comadminmode'] = 1;
		}
	}
	echo '<script>location.href = "index.php";</script>';
	die();                  
}

if (($user == '') OR (!($_SESSION['uid']>0)))  {
	ShowAuthForm();
} else 
{
	//авторизировались
	// Сначала  админчег послабже
	// Теперь админчег посильнее
	$isadmin = IsAdmin();
	if (isset($_SESSION['comadminmode'])) {
		if ($_SESSION['comadminmode'] == 1) {
			$isadmin = false;
		} elseif ($_SESSION['comadminmode'] == 2) {
			$isadmin = IsAdmin();
		}
	}
	if($isadmin && !isset($_SESSION['2fa_state'])) {
		/** @var \components\Eloquent\User2fa $user2fa */
		$user2fa = \components\Eloquent\User2fa::find($_SESSION['uid']);
		if($user2fa) {
			echo '<script>location.href = "2fa.php";</script>';
			die();
        }
	}


	if (!($isadmin)) {
		//если не админы
		$glava_test_clear=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.clans WHERE `glava` = '".$_SESSION['uid']."'  ;"));
		if (!($glava_test_clear[id] > 0)) {
		 	//не глава
		 	//затираем масивы для клана
			foreach ($PRICE as $i => $v ) {
				foreach ($v as $ii => $p) {
					if (($ii=='klan') and ($p==true)) {
						unset($PRICE[$i]);
					}
				}
			}
		 	
		 }	
	}


	//верхнее меню
echo '<tr>';	
	
		if ($isadmin==1) 
		{
	 		echo "<div align=right>
	 		<button id=enter1  onClick=\"location.href='index.php?act=logout'\">Выйти</button></div>";
			if (isset($_REQUEST['price'])) 
			{
				ShowPrice(); 
				page_bottom();
				exit;
		
			} 
			else 
			{
				ComAdminView($_REQUEST['act'],$_REQUEST['id']); 
				page_bottom();
				exit;
			}
		}
		else
		if ($isadmin==2) 
			{
			ComAdminView($_REQUEST['act'],$_REQUEST['id']); 
			}
	
	
	if (!isset($_POST['services'])) 
		{
		echo HMenu();
		echo '</tr>';
		if (($_REQUEST['act']=='mkpersonalart') /*or ($_REQUEST['act']=='mkclanart')*/) 
		{
			echo "<tr><td>";
			ShowMKPersonArt();
			echo "</td></tr>";
		}
		elseif ($_REQUEST['act']=='mkartch') 
		{
			echo "<tr><td>";
			ShowMKArtCh();
			echo "</td></tr>";
		}
		elseif ($_REQUEST['act']=='mkartch2') 
		{
			echo "<tr><td>";
			ShowMKArtChSlot();
			echo "</td></tr>";
		}		
		elseif ($_REQUEST['act']=='mkartch3') 
		{
			echo "<tr><td>";
			ShowMKArtChSlotFromHram();
			echo "</td></tr>";
		}		
		elseif ($_REQUEST['act']=='chhramart') 
		{
			echo "<tr><td>";
			CHHramArt();
			echo "</td></tr>";
		}		
		elseif ($_REQUEST['act']=='mkchart') 		
		{
			echo "<tr><td>";
			ShowArtChMenu();
			echo "</td></tr>";
		}
		elseif ($_REQUEST['act']=='mkartchf') 
		{
			echo "<tr><td>";
			ShowMKArtChFree();
			echo "</td></tr>";
		}		
		elseif ($_REQUEST['act']=='persobraz') 
		{
			echo "<tr><td>";
			ShowPObraz();
			echo "</td></tr>";			
		}
		elseif ($_REQUEST['act']=='klanobraz') 
		{
			echo "<tr><td>";
			 KlanObraz();
			echo "</td></tr>";	
		}
		elseif ($_REQUEST['act']=='persimgs') 
		{
			echo "<tr><td>";
			 PersImgs();
			echo "</td></tr>";			
		}
		elseif ($_REQUEST['act']=='exunik') 
		{
			echo "<tr><td>";
			 ExUnik();
			echo "</td></tr>";			
		}
		elseif ($_REQUEST['act']=='clanimgs') 
		{
			echo "<tr><td>";
			 ClanImgs();
			echo "</td></tr>";			
		}
		elseif ($_REQUEST['act']=='perspres') 
		{
			echo "<tr><td>";
			 PersPres();
			echo "</td></tr>";			
		}
		elseif ($_REQUEST['act']=='clanpres') 
		{
			echo "<tr><td>";
			 ClanPres();
			echo "</td></tr>";			
		}
		elseif ($_REQUEST['act']=='clanservice') 
		{
			echo "<tr><td>";
			 ClanServ();
			echo "</td></tr>";			
		}
		elseif ($_REQUEST['act']=='marservice') 
		{
			echo "<tr><td>";
			 MarServ();
			echo "</td></tr>";			
		}	
		elseif ($_REQUEST['act']=='sendmess') 
		{
			echo "<tr><td>";
			 SendMess();
			echo "</td></tr>";			
		}		
		elseif ($_REQUEST['act']=='other') 
		{
			echo "<tr><td>";
			 OtherServ();
			echo "</td></tr>";			
		}
		/*	
		elseif ($_REQUEST['act']=='money') 
		{
			echo "<tr><td>";
			 MoneyOut();
			echo "</td></tr>";			
		}*/		
		elseif ($_REQUEST['act']=='clansmile') 
		{
			echo "<tr><td>";
			 ClanSmile();
			echo "</td></tr>";			
		}		
		elseif ($_REQUEST['act']=='perssmile') 
		{
			echo "<tr><td>";
			 PersSmile();
			echo "</td></tr>";			
		}
		elseif ($_REQUEST['act']=='persabil') 
		{
			echo "<tr><td>";
			 PersAbil();
			echo "</td></tr>";			
		}			
		elseif ($_REQUEST['act']=='clansabil') 
		{
			echo "<tr><td>";
			 ClansAbil();
			echo "</td></tr>";			
		}		
		 else if ($_REQUEST['act']=='service') 
		{
		
			if ($_GET['menu']) 
			{
			echo "<tr><td>";
			ShowServ();
			echo "</td></tr>";						
			}
			else
			{
			echo "<tr><td>";
			?>
			<table width="880" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
			<td width="27" height="30">&nbsp;</td>
			<td align="center"><b style="color:#800000"> Выберите один из пунктов меню!</b>
			<br><br>
			<br><br>
			<CENTER><a href='javascript:window.history.go(-1);' class="button2" >НАЗАД</a></CENTER>
			
			</td>
			</tr>
			</table>
			<?
			echo "</td></tr>";						
			}
			
		}  /* elseif ($_REQUEST['act']=='mkdart') 
		{
			echo "<tr><td>";
			ShowDelPersonArt();
			echo "</td></tr>";						
		}*/
		 elseif ($_REQUEST['act']=='chruns') 
		{
			echo "<tr><td>";
			ShowDelRuns();
			echo "</td></tr>";						
		}
		 elseif ($_REQUEST['act']=='mkart') 
		{
			echo "<tr><td>";
			ShowMKArt();
			echo "</td></tr>";			
		} else if ($_REQUEST['act']=='answer') 
		{
			$get_id_char=mysql_fetch_array(mysql_query('SELECT * FROM `com_requests` WHERE `Id` = \''.intval($_POST['id']).'\' AND login = "'.$_SESSION['com_user'].'" LIMIT 1;'));
			$now=date("d.m.y H:i",time());
			if ($get_id_char['Id'] > 0) {
				
				$modername='[:'.$user.':] ';

				$addsql = "";
				if ($_REQUEST['act'] == "answer" && $get_id_char['status'] !='Ожидается оплата' && $get_id_char['status'] != '') $addsql = ', status = NULL ';

				mysql_query("UPDATE com_requests SET showed = 0, `comment`=CONCAT(`comment`,'<br>".$now.": ".$modername.mysql_real_escape_string($_REQUEST['coment'])."') ".$addsql." WHERE `Id`=".intval($_POST['id'])); 
			} 		                     
			echo "<script>this.document.location.href='index.php?act=myrequest';</script>"; 
			die();
	 	} 
	 	else if ($_REQUEST['act']=='myrequest') 
	 	{
			echo "<tr><td>";	 	
			ShowMyRequests();
			echo "</td></tr>";			
		} else {
			echo "<tr><td>";		
			ShowComMessageForm();
			echo "</td></tr>";
		}
	} else {
		echo HMenu();
		if ($_POST['services'] == "Смена главы клана") {
			echo "<tr><td>";
			ShowGlavaChange();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Восстановление клана после расформирования (при неуплате налога)") {
			echo "<tr><td>";
			ShowClanRestore();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Экспресс проверка на чистоту") {
			echo "<tr><td>";
			ShowExpressClear();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Сброс второго пароля персонажа") {
			echo "<tr><td>";
			ShowResetSecond();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Оплата Штрафа") {
			echo "<tr><td>";
			ShowPayPenalty();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Лечение неизлечимой травмы") {
			echo "<tr><td>";
			ShowPayTravm();
			echo "</td></tr>";
		}		
		elseif ($_POST['services'] == "Смена пола персонажа") {
			echo "<tr><td>";
			ShowChangeSex();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Смена даты рождения персонажа") {
			echo "<tr><td>";
			ShowResetBD();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Смена WMZ кошелька для вывода денежных средств") {
			echo "<tr><td>";
			ShowResetWMZ();
			echo "</td></tr>";
		}
		elseif ($_POST['services'] == "Смена пароля на казну клана") {
			echo "<tr><td>";
			ShowResetKlanPass();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Смена склонности клана и соклановцам") {
			echo "<tr><td>";
			ShowChangeKlanAlign();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Смена значка клана") {
			echo "<tr><td>";
			ShowChangeKlanImage();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Присоединить рекрут-клан") {
			echo "<tr><td>";
			ShowAddRekrut();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Отсоединить рекрут-клан") {
			echo "<tr><td>";
			ShowRemoveRekrut();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Отсоединить клан-основу") {
			echo "<tr><td>";
			ShowRemoveMainKlan();
			echo "</td></tr>";
/*		} elseif ($_POST['services'] == "Сброс модификаторов и статов для рун") {
			echo "<tr><td>";
			ShowResetRune();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Сброс статов для уникальных Плащей героя") {
			echo "<tr><td>";
			ShowReset_cloack_hero();
			echo "</td></tr>";
*/
		} elseif ($_POST['services'] == "Обмен футболок (мф)") {
			echo "<tr><td>";
			ShowChenge_fb_menu();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Обмен Плащей (мф)") {
			echo "<tr><td>";
			ShowChenge_cloack_menu();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Обмен Плащей рыцаря (мф) на Плащ героя (мф)") {
			echo "<tr><td>";
			ShowChenge_cloack_hero();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Обмен Плаща героя (мф) на Плащ легендарного рыцаря (мф)") {
			echo "<tr><td>";
			ShowChenge_cloack_legend_r();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Обмен Плаща рыцаря (мф) на Плащ легендарного рыцаря (мф)") {
			echo "<tr><td>";
			ShowChenge_cloack_legend_r2();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Обмен Плаща рыцаря (мф) на Плащ легендарного героя (мф)") {
			echo "<tr><td>";
			ShowChenge_cloack_legend_g1();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Обмен Плаща героя (мф) на Плащ легендарного героя (мф)") {
			echo "<tr><td>";
			ShowChenge_cloack_legend_g2();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Обмен Плаща легендарного рыцаря (мф) на Плащ легендарного героя (мф)") {
			echo "<tr><td>";
			ShowChenge_cloack_legend_g3();
			echo "</td></tr>";
		}
		elseif ($_POST['services'] == $PRICE[231][desc]) { //'Обмен Футболки Учителей (мф) на Легендарную футболку ОлдБК (мф)'
			echo "<tr><td>";
			ShowChenge_fu_to_lfb();
			echo "</td></tr>";
		}		
		elseif ($_POST['services'] == $PRICE[232][desc]) { //'Обмен Футболки ОлдБК (мф) на Легендарную футболку ОлдБК (мф)';
			echo "<tr><td>";
			ShowChenge_fb_to_lfb();
			echo "</td></tr>";
		}		
		elseif ($_POST['services'] == "Обмен Футболки ОлдБК (мф) на Футболку Учителей (мф)") {
			echo "<tr><td>";
			ShowChenge_fb_to_fu();
			echo "</td></tr>";
		}
		elseif ($_POST['services'] == "Обмен Легендарной футболки ОлдБК (мф) на Легендарную футболку Учителей (мф)") {
			echo "<tr><td>";
			ShowChenge_fb_to_lgu1();
			echo "</td></tr>";
		}
		elseif ($_POST['services'] == "Обмен Футболки Учителей (мф) на Легендарную футболку Учителей (мф)") {
			echo "<tr><td>";
			ShowChenge_fb_to_lgu2();
			echo "</td></tr>";
		}
		elseif ($_POST['services'] == "Обмен Футболки ОлдБК (мф) на Легендарную футболку Учителей (мф)") {
			echo "<tr><td>";
			ShowChenge_fb_to_lgu3();
			echo "</td></tr>";
		}				
	
		elseif ($_POST['services'] == "Cменить пароль от не основного счета на такой же как основной") {
			echo "<tr><td>";
			ShowChenge_bank();
			echo "</td></tr>";
		}				
		elseif ($_POST['services'] == "Возврат вещи, в случае, если вещь продана в магазин или выкинута по ошибке") {
			echo "<tr><td>";
			ShowItemReturn();
			echo "</td></tr>";
		}
		elseif ($_POST['services'] == "Снятие подарка с вещи, в случае, если вещь подарена по ошибке") {
			echo "<tr><td>";
			ShowDPItem();
			echo "</td></tr>";
		}		
		elseif ($_POST['services'] == "Смена емейла персонажа") {
			echo "<tr><td>";
			ShowResetEmail();
			echo "</td></tr>";
		} elseif ($_POST['services'] == "Обмен одной обычной вещи персонажу" || $_POST['services'] == "Обмен одной уникальной вещи персонажу" || $_POST['services'] == "Обмен уникальной вещи 7-10 уровней на уникальную вещь 11 уровня" || $_POST['services'] == "Обмен не привязанного артефакта") {
			echo "<tr><td>";
			ShowFindPers();
			echo "</td></tr>";
		} else {
			PutComMessage();
		}
		
	}

	
}

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