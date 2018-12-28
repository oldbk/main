<?php
//add by Umich 9 12 2010
	session_start();

	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
	include "connect.php";
	include "functions.php";
//	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));

	if ($user['room'] != 27) { header("Location: main.php");  die(); }
	if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
	if ($_SESSION['boxisopen']!='open') { header('location: main.php?edit=1'); die(); }

	$city_base[0]='oldbk';
	$city_base[1]='avalon';
	$top = "top";
     if(!$_SESSION['beginer_quest'][none])
     {
     	  $last_q=check_last_quest(5);
	      if($last_q)
	      {
	          quest_check_type_5($last_q);
	          //проверяем квесты на хар-и
	      }

     	  $last_q=check_last_quest(2);
	      if($last_q)
	      {
	          quest_check_type_2($last_q);
	          //проверяем квесты на хар-и
	      }
     }



?><HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
    <link rel="stylesheet" href="/i/btn.css" type="text/css">
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
	'<form action="'+script+'" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=sd4 value="6"><td colspan=2>'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT id="'+name+'" TYPE=text NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
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
<?
$step=1;
if ($step==1) $idkomu=0;
?>
function closehint3(){
	document.getElementById("hint3").style.visibility="hidden";
    Hint3Name='';
}


var transfersale = true;

function reloadit(){
   if (tologin != '') { location="post.php?FindLogin=0&to_id=<? echo $idkomu; ?>&sd4=<? echo $user['id']; ?>&0.760742158507544" }
}


	document.domain = "oldbk.com";

	function w(name,id,align,klan,level,slp,trv,deal,battle,war,r,rk,hh,tlvl,us) {
			var fight = '';
			var altext = '';

			if (align.length>0) {
				altext="";
				if (align>2 && align<3) altext = "Ангел";
				if (align>1 && align<2 && klan !="FallenAngels") altext = "Паладин";
				if (align>1 && align<2 && klan =="FallenAngels") altext = "Падший ангел";
				if ( align == 3 ) altext ="Тёмный";
				if ( align == 4 ) altext ="В хаосе";
				if ( align == 2 ) altext ="Нейтрал";
				if ( align == 5 ) altext ="Истинный Хаос";
				if ( align == 6 ) altext ="Светлый";
				if ( align == 1 ) altext ="Светлый";
				if ( align == "2.4") altext ="Нейтрал";
				align='<img src="http://i.oldbk.com/i/align_'+align+'.gif" title="'+altext+'" width=12 height=15>';
			}

			if (battle>0) { fight = '2'}
			if (klan.length>0) { klan='<A HREF="http://oldbk.com/encicl/klani/clans.php?clan='+klan+'" target=_blank><img src="http://i.oldbk.com/i/klan/'+klan+'.gif" title="'+klan+'" ></A>';}
			if (deal==1) { klan+='<img src="http://i.oldbk.com/i/deal.gif" width=15 height=15 title="Дилер">';}

			color = "";
			if (r > 0) { if (r == 1) { color="blue"; } if (r == 2) { color="red";} }
			colorstart = "<font color="+color+">";
			colorend = "</font>";
			if (color.length == 0) {
				colorstart = "";
				colorend = "";
			}
			keyowner = "";
			if (rk > 0) keyowner = " <img border=0 src=\"http://i.oldbk.com/i/sh/ruin_k.gif\"> ";
			if (hh > 0) keyowner = " <img border=0 src=\"http://i.oldbk.com/i/map/horse_chat.gif\"> ";

			lvlcolorstart = "";
			lvlcolorend = "";
			if (tlvl == 1) {
				lvlcolorstart = "<b style=\"color:#F03C0E;\">";
				lvlcolorend = "</b>";
			}

			document.write(keyowner+'<img OnClick="<?php echo $top; ?>.AddToPrivate(\''+name+'\', <?php echo $top; ?>.CtrlPress,event); return false;" src="http://i.oldbk.com/i/lock'+fight+'.gif" style="cursor:pointer;" title="Приват" width=20 height=15></A>'+align+klan+'<span OnClick="<?php echo $top; ?>.AddTo(\''+name+'\',event); return false;" class="ahm" style="cursor:pointer;">'+colorstart+name+colorend+'</span>'+'['+lvlcolorstart+level+lvlcolorend+']'+'<a href="http://<?=CITY_DOMEN;?>/inf.php?'+id+'" target=_blank title="Инф. о '+name+'">'+'<IMG SRC="http://i.oldbk.com/i/inf.gif" WIDTH=12 HEIGHT=11 BORDER=0 ALT="Инф. о '+name+'"></a>');
			if (slp>0) { document.write(' <IMG SRC="http://i.oldbk.com/i/sleep2.gif" WIDTH=24 HEIGHT=15 BORDER=0 ALT="Наложено заклятие молчания">'); }
			if (trv>0) { document.write(' <IMG SRC="http://i.oldbk.com/i/travma2.gif" WIDTH=24 HEIGHT=15 BORDER=0 ALT="Инвалидность">'); }
			if (war==1){ document.write(' <b><a href=# onclick="<?php echo $top; ?>.cht(\'http://capitalcity.oldbk.com/klan.php?razdel=wars&post_attack='+id+'\');"> X</a></b>'); }
			if (us !== undefined && us.length) {
				document.write(' <IMG SRC="http://i.oldbk.com/i/chat/chat_icon_status.png" BORDER=0 title="'+us+'" alt="'+us+'">');
			}
			document.write('<BR>');
	}

</SCRIPT>
<?
make_quest_div();

if (@!$_REQUEST['razdel']) { $_REQUEST['razdel']=1; }
if (@$_REQUEST['FindLogin']) {

//	$get_city=mysql_fetch_array(mysql_query("SELECT `id`,`id_city` FROM oldbk.`users` WHERE `login` ='".mysql_escape_string($_REQUEST['FindLogin'])."';"));
//	$res=mysql_fetch_array(mysql_query("SELECT * FROM  ".$city_base[$get_city[id_city]].".`users` WHERE id={$get_city[id]};"));
	$res=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `login` ='".mysql_escape_string($_REQUEST['FindLogin'])."';"));
	$step=3;
}
if (@$_REQUEST['to_id']) {
//	$get_city=mysql_fetch_array(mysql_query("SELECT `id`,`id_city` FROM oldbk.`users` WHERE `id` ='".(int)($_REQUEST['to_id'])."';"));
//	$res=mysql_fetch_array(mysql_query("SELECT * FROM  ".$city_base[$get_city[id_city]].".`users` WHERE  id={$get_city[id]};"));
	$res=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `id` ='".(int)($_REQUEST['to_id'])."';"));
	$step=3;
}
if (@$step==3){
	$step=0;
	$id_person_x=$res['id'];
	if (@!$id_person_x) $mess='Персонаж не найден';
	elseif ($id_person_x==$user['id']) $mess='Незачем передавать самому себе';
	elseif (($user['align']==4) and ($res['id'] != 8540) and ($res['id'] != 102904) ) $mess='Со склонностью хаос передачи предметов запрещены';
	elseif (($res['level']<1) AND !($user['klan'] == 'radminion') AND !($user['klan'] == 'Adminion')) $mess='К персонажам до 1-го уровня передачи предметов запрещены';
	elseif (($user['level']<1) AND !($user['klan'] == 'radminion') AND !($user['klan'] == 'Adminion')) $mess='Персонажам до 1-го уровня передачи предметов запрещены';
	elseif ($user['in_tower']==1) $mess='Персонаж находится в Башне Смерти';
	elseif ($user['in_tower']==2) $mess='Персонаж находится в Руинах старого замка';
	elseif ($res['in_tower'] == 1) $mess='Персонаж находится в Башне Смерти';
	elseif ($res['in_tower'] == 2) $mess='Персонаж находится в Руинах старого замка';
	elseif ($res['in_tower'] == 3) $mess='Персонаж находится в Турнире';
	elseif ($res['in_tower'] == 4) $mess='Персонаж находится в Темнице. Передачки возможны только через цветочный магазин';

	elseif (
		($res['room']>210)AND($res['room']<299)AND
		($res['room']!=240)AND($res['room']!=270)
		) $mess='Персонаж находится в Ристалище...';

	elseif ($res['block']>0) $mess='Персонаж заблокирован';
	elseif ($user['block']>0) $mess='Персонаж заблокирован';
	else{
		$idkomu=$id_person_x;
		$get_city=mysql_fetch_array(mysql_query("SELECT `id`,`id_city` FROM oldbk.`users` WHERE `id` ='".$idkomu."';"));
		$komu=mysql_fetch_array(mysql_query("SELECT *  FROM ".$city_base[$get_city[id_city]].".`users` WHERE `id` ='".$idkomu."';"));

		if (  ($komu[id]==8540) OR ($komu[id]==102904) OR  ($komu[id]==182783) OR  ($komu[id]==457757) OR ($komu[id]==326)  )     {  $VAUCHER='and prototype not in (900,901,902,903,904,905,906,907,908,200001,200002,200005,200010,200025,200050,200100,200250,200500,2014001,2014002,2014003,2014004,2014005,2014006,2014007,2014008,2013005) ';   }
	    	else   {    $VAUCHER='and prototype not in (900,901,902,903,904,905,906,907,908,100000,100005,100015,100020,100025,100040,100300,100100,100200,200001,200002,200005,200010,200025,200050,200100,200250,200500,2014001,2014002,2014003,2014004,2014005,2014006,2014007,2014008,2013005) ';    }

		$mess=$_REQUEST['FindLogin'];
		$step=3;
	}
}else $mess='';

if ($step==3) {
     $gift=0;

	if ($_POST['sendMessage'] && $_POST['message'] && $_POST['to_id'] && $_POST['sd4']==$user['id'] && $user['money'] >= 0.1)
	{
	 if  (strlen($_POST['message'])<=100)
	 	{
		$filter = mysql_query('SELECT * FROM oldbk.`friends` WHERE type = 2 AND owner = '.intval($_POST['to_id']).' AND friend = '.$user['id']);
		if (mysql_num_rows($filter) == 0)
			{
			$postmess=htmlspecialchars($_POST['message']);
			mysql_query("UPDATE `users` set money=money-'0.1' where id='".$user['id']."'");
			tele_check_new($komu,$postmess);
			$mess='Сообщение персонажу "'.$komu['login'].'" будет доставлено.';
			}
		}
		else
		{
		$mess="Допускается максимум 100 символов!)";
		}
	}
	elseif (($_POST['setkredit']>=1 && $_POST['to_id'] && $_POST['sd4']==$user['id'])
	     AND (($user[level]>=4) AND ($komu['level']>=4)))
	      {
		$_REQUEST['setkredit'] = round($_REQUEST['setkredit'],2);
		//$percent=round((strval($_REQUEST[setkredit])*5/100),2);
		$percent=0;
		if($percent>0)
		{
			$tttx="процент за передачу составил ".$percent."кр.";
		}

		if (is_numeric($_REQUEST['setkredit']) && ($_REQUEST['setkredit']>0) && (($_REQUEST['setkredit']+$percent) <= $user['money'])) {

		$ok=1;

				/*
						//подсчет и если ок то дальше - TEST
				if 	(
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
				///////////////////////////////////////////////////////////////////////////////////////////////////////
				*/

		if ($ok==1)
			{
			if (mysql_query("UPDATE `users` set `money`=money-'".(strval($_REQUEST[setkredit])+$percent)."' where id='".$user['id']."'") && mysql_query("UPDATE ".$city_base[$get_city[id_city]].".`users` set `money`=money+'".strval($_REQUEST[setkredit])."' where id='".$idkomu."'"))
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



				$mess='Удачно передано '.strval($_REQUEST[setkredit]).' кр к персонажу '.$komu['login']. ' '.$tttx.($text1!=''?'. Детали платежа: '.$text1:'');

				$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user['money'];
				$user['money']-=strval($_REQUEST[setkredit]+$percent);
				$rec['owner_balans_posle']=$user['money'];
				$rec['target']=$komu['id'];
				$rec['target_login']=$komu['login'];
				$rec['type']=166;//передача кредитов почтой
				$rec['sum_kr']=strval($_REQUEST[setkredit]);
				$rec['sum_ekr']=0;
				$rec['sum_kom']=$percent;
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
				$rec['type']=167;//получение кредитов
	 		    	$rec['owner']=$komu[id];
				$rec['owner_login']=$komu[login];
				$rec['owner_balans_do']=$komu['money'];
				$komu['money']+=$_REQUEST[setkredit];
				$rec['owner_balans_posle']=$komu['money'];
				$rec['target']=$user['id'];
				$rec['target_login']=$user['login'];
				add_to_new_delo($rec); //кому

				$message="<font color=red>Внимание!</font> Вам пришел почтовый перевод ".strval($_REQUEST[setkredit])." кр. ".($text1!=''?'. Детали платежа: '.$text1:' ')." от <span oncontextmenu=OpenMenu()>".$user['login']."</span>   ";
				echo telepost_new($res,$message);

			}
			else {
				$mess="Произошла ошибка";
			}

		  }


		}
		else {
			$mess="Недостаточно денег";
		}
	}
	elseif ( (((int)($_REQUEST['setobject']) && $_REQUEST['setobject']>0) && ((int)($_REQUEST['to_id']) && $_REQUEST['to_id']>0) && $_REQUEST['sd4']==$user['id'])
		AND (($user[level]>=4) AND ($komu['level']>=4)) )
		 {
		if(!$_POST['count'])
        {
        	$count=1;
        	$sql=' AND id='.(int)($_REQUEST['setobject']);
        }
        else
        {
        	$count=(int)$_POST['count'];
        	$sql=' AND prototype='.mysql_escape_string((int)$_POST['set']).' AND `group`=1 ';
        }


        if (($komu['klan']=='radminion') OR ($komu['klan']=='Adminion') OR ($komu['id']==8325)||
		($user['klan']=='radminion') OR ($user['klan']=='Adminion') OR ($user['id']==8325)
	 )
		{
		$ssqlp="";
		}
		else
		{
		$ssqlp=" AND `present` = '' and sowner = 0 ";
		}

  		$sql="SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' ".$sql."
		AND dressed=0 AND bs_owner='".$user[in_tower]."' AND `setsale` = 0 AND ekr_flag=0  AND prototype!=40000001 AND prototype!=2123456804 and type!=99 and otdel!=72 and type !=77
		AND duration=".(int)$_GET[tmp]." ".$VAUCHER."  ".$ssqlp." LIMIT ".$count.";";

	   $data = mysql_query($sql);
	   $ff=0;
	   if(mysql_num_rows($data)>0)
       {
      		 while($res=mysql_fetch_array($data))
      		 {
	      		 if (($row[type]==30) AND ($row[up_level]<5))
	      		 {
	      		 //не грузим  руны менее 5го ур

	      		 }
	      		 else
	      		 {
		            $item[$ff]=$res;
			    $ff++;
			 }
		}
       }
       else
	   {
		$mess=" Предмет не найден в рюкзаке";
	   }

        if(count($item)>0){
	        if($user[money]>=$ff)
	        {
	           if ($komu['in_tower']==0)
	             {
	             $ookk=1;


				if 	(
					($user['klan']!='radminion') AND ($user['klan']!='Adminion') AND ($komu['klan']!='radminion') AND ($komu['klan']!='Adminion') AND //не передачи от админов и к админам
					($user['id']!=8325) AND ($komu['id']!=8325) ) //не передачи от ПБ и к ПБ
					{
						 $tco=test_give_count($user['id'],$komu['id'],$ff);
						 if (!(is_array($tco)))
							{
							//тест успешно
							 	if (give_count($user['id'],$ff) )
							 	{
							 	//ok
								 	if (give_count($komu['id'],$ff) )
								 	{
								 	//все ок
								 	}
								 	else
								 	{
								 	$mess='У персонажа "'.$komu['login'].'" недостаточно лимита передач на сегодня! ' ;
									$ookk=0;
								 	}
							 	}
							 	else
							 	{
							 	$mess='У Вас недостаточно лимита передач на сегодня! ' ;
								$ookk=0;
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
							$ookk=0;
							}

					}
				///////////////////////////////////////////////////////////////////////////////////////////////////////



	             if ($ookk==1)
	             	{
		            $per=0;
			        for($jj=0;$jj<count($item);$jj++)
			        {
	                   if($per==10){$per=0; $pp='<br>';}else{$pp='';}
	                   $sql_it_id.= $item[$jj][id].',';
	                   $sql_delo.=get_item_fid($item[$jj]).','.$pp;
	                   $per++;
			        }
			          $sql_it_id=substr($sql_it_id,0,-1);
			          $sql_delo=substr($sql_delo,0,-1);

	                  mysql_query("update `users` set `money`=`money`-".$jj." where `id`='".$user['id']."'");
                      $prez='';
                      $txt='Почтой передан';
                      $txt1='передано';
                      $txt2='передал';


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
						$give_sql="update oldbk.`inventory` set `owner` = ".$komu['id']."  where `id` in (".$sql_it_id.") and `owner`= '".$user['id']."' AND ekr_flag=0 AND prototype!=40000001 AND prototype!=2123456804 and type!=99;";

						 if(mysql_query($give_sql)&&$ok1==1)
		                  {

                            $rec['owner']=$user[id];
							$rec['owner_login']=$user[login];
							$rec['owner_balans_do']=$user['money'];
							$rec['owner_balans_posle']=$user['money'];
							$rec['target']=$komu['id'];
							$rec['target_login']=$komu['login'];
							$rec['type']=168;//передаю предмет почтой
							$rec['sum_kr']=0;
							$rec['sum_ekr']=0;
							$rec['sum_kom']=0;
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
							$rec['item_incmagic']=$item[0]['includemagicname'];
							$rec['item_incmagic_count']=$item[0]['includemagicuses'];
							$rec['item_sowner']=($item[0]['sowner']>0?1:0);
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
							$rec['type']=169;//получаю/в подарок предмет

						    	add_to_new_delo($rec); //кому

		                			$mess='Удачно '.$txt1.' "'.$item[0]['name'].'" (x'.$jj.') к персонажу '.$komu['login'].' за '.$jj.'кр.';
							//addchp ('<font color=red>Внимание!</font> Персонаж "'.$user['login'].'" '.$txt2.' вам "'.$item[0]['name'].'" '.$jj.'шт.   ','{[]}'.$komu['login'].'{[]}');

							$message="<font color=red>Внимание!</font> Вам почтой передан предмет <b>".$item[0]['name']."</b> (x".$jj.") от <span oncontextmenu=OpenMenu()>".$user['login']."</span>   ";
							echo telepost_new($komu,$message);
							$user['money']-=$jj;
							$counter=100;
						  }
						  else
						  {
						     if($counter==0||$counter==10||$counter==50||$counter==99)
							{
						  	$mess = 'Произошла ошибка 1. попробуйте еще раз.';
						  	$counter++;
						  	}
						  }
					 }

				   }
				 }
				 else
				 {
				 	$mess = 'В данный момент персонаж не может принять от Вас предмет :-)';
				 }
	   		}
	        else
	        {
	         	$mess='Недостаточно денег на оплату передачи';
	        }
	    }


	}


}
?>

</HEAD>
<body bgcolor=e2e0e0><div id=hint3 class=ahint></div><div id=hint4 class=ahint></div>
<H3>Почта</H3>
<center><font color=#003388><b>Здесь можно отправить предметы, кредиты и телеграммы игрокам,<br>которых сейчас нет в клубе или они находятся в других городах.</b></font></center>

<TABLE width=100% cellspacing=0 cellpadding=0>
<TR><TD>
<? if ($step==3) {
	//echo 'К кому передавать: <font color=red><SCRIPT>drwfl("'.@$komu['login'].'",'.@$komu['id'].',"'.@$komu['level'].'","'.@$komu['align'].'","'.@$komu['klan'].'")</SCRIPT></font>';
	if ($komu['hidden']>0) $komu['battle']=0;
	echo 'К кому передавать: <font color=red><b><SCRIPT>w(\''.$komu['login'].'\','.$komu['id'].',\''.$komu['align'].'\',\''.$komu['klan'].'\',\''.$komu['level'].'\',\''.$komu['slp'].'\',\''.$komu['trv'].'\',\''.(int)$komu['deal'].'\',\''.(int)$komu['battle'].'\',\'0\',0,0,0,"");</SCRIPT></b></font>';
	?>
        <div class="btn-control">
            <INPUT class="button-mid btn" TYPE=button value="Сменить" onClick="findlogin('Передача предметов','post.php','FindLogin')">
        </div>
        <BR><?
} else {
	/*
	$roww = mysql_fetch_array(mysql_query("SELECT * FROM `trade` WHERE `baer` = {$user['id']} LIMIT 1;"));
	mysql_query("DELETE FROM `trade` WHERE `baer` = {$user['id']} LIMIT 1;");
	if (!$roww['id']) {
		?> <SCRIPT>findlogin('Передача предметов','post.php','FindLogin');</SCRIPT><?
	} else {
		?> <SCRIPT>transfer(<?=$roww['to_id']?>, '<?=$roww['login']?>', '<?=str_replace("\r\n","",$roww['txt'])?>', <?=$roww['kr']?>, <?=$roww['id']?>, '');</SCRIPT><?
	}*/
	?> <SCRIPT>findlogin('Передача предметов','post.php','FindLogin');</SCRIPT><?
}
?>

</td><TD align=right>
        <div class="btn-control">
            <INPUT class="button-dark-mid btn" TYPE=button value="Подсказка" style="background-color:#A9AFC0" onClick="window.open('help/post.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
            <INPUT class="button-mid btn" TYPE=button value="Вернуться" onClick="location.href='city.php?cp=1';">
        </div>
</td></tr><tr><td colspan=2 align=right><? if ($step!=4) {?> <FONT COLOR=red><B><? echo $mess; ?></B></FONT> <? } ?></td></tr></table>

<TABLE width=100% cellspacing=0 cellpadding=0>
<FORM ACTION="post.php" METHOD=POST>
<TR>
	<TD valign=top align=left width=30%>
<?
	if ($step==3) {
	?>
	<INPUT TYPE=hidden name=to_id value="<? echo $idkomu; ?>">
	<INPUT TYPE=hidden name=sd4 value="<? echo $user['id']; ?>">
	<BR>У вас на счету: <FONT COLOR=339900><B><? echo $user['money']; ?></B></FONT> кр.<BR>
	<?



	if (($user[level]>=4) AND ($komu['level']>=4))
	{
	 ?>
	<br/>
						<br/>
						<fieldset>
                        <legend><b>Передать кредиты</b></legend>
							Передать кредиты, минимально 1 кр. <br/>
							Укажите передаваемую сумму: <INPUT TYPE=text NAME=setkredit maxlength=8 size=6>&nbsp;
                            <div class="btn-control" style="display: inline-block">
                                <INPUT class="button-mid btn" TYPE=submit onclick="if(!confirm('Перевести деньги?')) { return false; }" VALUE="Передать">
                            </div>
                            <br>
							Детали платежа: <INPUT TYPE=text NAME=settext maxlength=70 size=41>
                        </fieldset>
        <?
        }
        else
        {
			if ($komu['level']<=3) echo '<font color=red>К персонажам до 4-го уровня передачи предметов запрещены <br></font>';
			elseif ($user['level']<=3) echo '<font color=red>Персонажам до 4-го уровня передачи предметов запрещены <br></font>';


        }
        ?>
						<br/>

	<?php
		$filter = mysql_query('SELECT * FROM oldbk.friends WHERE type = 2 AND owner = '.$komu['id'].' AND friend = '.$user['id']);
		if (mysql_num_rows($filter) == 0) {
	?>

			<fieldset>
                        <legend><b>Телеграф</b></legend>
						Вы можете отправить короткое сообщение любому персонажу, даже если он находится в offline или другом городе.<br/>
						Услуга платная: <b>0.1 кр.</b> <br/>
						Сообщение: (Максимум 100 символов)
						<input type="text" name="message" id="message" size="52">
                <div class="btn-control" style="display: inline-block">
                    <input class="button-mid btn" type="submit"  id="sendMessage" name="sendMessage" value="Отправить" onclick="if(!confirm('Послать сообщение?')) { return false; }">
                </div>
                        </fieldset>
	<?php

		} else {

	?>
			<fieldset>
                        <legend><b>Телеграф</b></legend>
				Вы не можете отправлять сообщения этому персонажу, он внёс вас в свой игнор лист!
                        </fieldset>

	<?php
		}
	?>


	<?
	}
?>
	</TD>
</FORM>

<FORM ACTION="post.php" METHOD=POST>
<INPUT TYPE=hidden name=sd4 value="<? echo @$user['id']; ?>">
<TD valign=top align=right>

<?
if ( ($step==3) AND (($user[level]>=4) AND ($komu['level']>=4)) ) {


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

	$d[0] = getmymassa($user);

	echo $d[0];
	?>/<?=get_meshok()?>)</B></TD>
</TR>
<TR><TD align=center><!--Рюкзак-->
<TABLE BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">
<?php

	if (($komu['klan']=='radminion') OR ($komu['klan']=='Adminion') OR ($komu['id']==8325)
		|| ($user['klan']=='radminion') OR ($user['klan']=='Adminion') OR ($user['id']==8325)
		)
		{
		$ssqlp="";
		}
		else
		{
		$ssqlp=" AND `present` = '' and sowner = 0 ";
		}

	if ($_SESSION['razdel']==null) {
		$data = mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' AND `dressed` = 0 AND `setsale` = 0 ".$VAUCHER." AND bs_owner = 0 AND ekr_flag=0 and type!=99 AND prototype!=40000001 AND prototype!=2123456804 ".$ssqlp." AND  (`type` < 12 OR `type` in (27,28,30,34,35)) ORDER by `update` DESC; ");
	}
	if ($_SESSION['razdel']==1) {
		$data = mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' AND `dressed` = 0 AND `setsale` = 0 ".$VAUCHER." AND bs_owner = 0 AND ekr_flag=0 and type!=99 AND prototype!= 40000001 AND prototype!=2123456804 ".$ssqlp." AND `type` = 12 ORDER by `update` DESC; ");
	}
	if ($_SESSION['razdel']==2) {
		$data = mysql_query("SELECT * FROM oldbk.`inventory` USE INDEX(owner_4) WHERE `owner` = '{$_SESSION['uid']}' AND `dressed` = 0 AND `setsale` = 0 ".$VAUCHER." AND bs_owner = 0 AND ekr_flag=0 and otdel!=72 and type!=99  and type!=77 AND prototype!= 40000001 AND prototype!=2123456804 ".$ssqlp." AND (`type` > 12 AND `type` not in (27,28,30,34,35)) ORDER by `update` DESC; ");
	}
  //<? ';
	while($row = mysql_fetch_array($data))
	{

		if (($row[type]==30) AND ($row[up_level]<5))
		{
		//руна меньше 5го уровня - не загружаем

		}
		else
		{

		if($row[present]!=''){
		   $prez=1;
		}else
		{
			$prez=0;
		}
		$inv_shmot[$prez][$row[duration]][$row[prototype]][]=$row;
  		$inv_gr_key[$row[prototype]]=$row[group];

  		}
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
						{ $value1[$i][GetShopCount()] = 1;}
						if($value1[$i]['add_pick']!=''&&$value1[$i]['pick_time']>time())
						{
						     $value1[$i]['img']=$value1[$i]['add_pick'];
						}
						echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$value1[$i]['img']}\" BORDER=0><BR>";

						$sh_id = get_item_fid($value1[$i]);
						echo "<center><small>(".$sh_id.")</small></center><br>";

						$money_out = 1;
						echo '<A HREF="post.php?to_id='.$idkomu.'&id_th='.$value1[$i]['id'].'&setobject='.$value1[$i]['id'].'
						&sd4='.$user['id'].'&tmp='.$value1[$i]['duration'].'&gift=0"'.'onclick="return confirm(\'Передать предмет '.$value1[$i]['name'].'?\')">передать&nbsp;за&nbsp;1&nbsp;кр.</A>';

						if($value1[$i]['group']==1)
						{
				        	?>
				        	<IMG SRC="http://i.oldbk.com/i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Передать несколько штук" style="cursor: pointer"
				        	onclick="AddCount('<?=$value1[$i][prototype]?>', '<?=$value1[$i][name]?>','1','post.php?to_id=<?=$idkomu?>&id_th=<?=$value1[$i]['id']?>&setobject=<?=$value1[$i]['id']?>&s4i=<?=$user['sid']?>&sd4=<?=$user['id']?>&tmp=<?=$value1[$i]['duration']?>&gift=0')">
				        	<?
						}
					// !=1 to disable in tower

						echo "</TD><TD valign=top>";

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
<br><div align=right>
<!--Rating@Mail.ru counter-->
<script language="javascript" type="text/javascript"><!--
d=document;var a='';a+=';r='+escape(d.referrer);js=10;//--></script>
<script language="javascript1.1" type="text/javascript"><!--
a+=';j='+navigator.javaEnabled();js=11;//--></script>
<script language="javascript1.2" type="text/javascript"><!--
s=screen;a+=';s='+s.width+'*'+s.height;
a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth);js=12;//--></script>
<script language="javascript1.3" type="text/javascript"><!--
js=13;//--></script><script language="javascript" type="text/javascript"><!--
d.write('<a href="http://top.mail.ru/jump?from=1765367" target="_top">'+
'<img src="http://df.ce.ba.a1.top.mail.ru/counter?id=1765367;t=49;js='+js+
a+';rand='+Math.random()+'" alt="Рейтинг@Mail.ru" border="0" '+
'height="31" width="88"><\/a>');if(11<js)d.write('<'+'!-- ');//--></script>
<noscript><a target="_top" href="http://top.mail.ru/jump?from=1765367">
<img src="http://df.ce.ba.a1.top.mail.ru/counter?js=na;id=1765367;t=49"
height="31" width="88" border="0" alt="Рейтинг@Mail.ru"></a></noscript>
<script language="javascript" type="text/javascript"><!--
if(11<js)d.write('--'+'>');//--></script><div>
</BODY>
</HTML>
