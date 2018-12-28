<?php
	session_start();

	if (!($_SESSION['uid'] >0)) header("Location: index.php");
	include "connect.php";
	include "functions.php";	

	
	if ($user['room'] != 46) { header("Location: main.php"); die(); }
	
	if (isset($_GET['exit'])) {
		mysql_query("UPDATE `users` SET `users`.`room` = '66' WHERE `users`.`id` = '{$_SESSION['uid']}' ;") or die();
		{ header("Location: city.php"); die(); }
	}
	else
	if ((isset($_GET['bankexit'])) 	 and ($_SESSION['bankid']>0) ) 
	{
		unset($_SESSION['bankid']);
	}

	if (isset($_POST['fall'])) {
		$_SESSION['fall']=(int)$_POST['fall'];
	} else {
		$_POST['fall']=(int)$_SESSION['fall'];	
	}

		
	if ($_POST['fall']>0) {$viewlevel=true; } 

	
		$otels[1]="Оружие: кастеты,ножи";
		$otels[11]="Оружие: топоры";
		$otels[12]="Оружие: дубины,булавы";
		$otels[13]="Оружие: мечи";
		$otels[2]="Одежда: сапоги";
		$otels[21]="Одежда: перчатки";
		$otels[22]="Одежда: легкая броня";
		$otels[23]="Одежда: тяжелая броня";
		$otels[24]="Одежда: шлемы";
		$otels[3]="Щиты";
		$otels[4]="Ювелирные товары: серьги";
		$otels[41]="Ювелирные товары: ожерелья";
		$otels[42]="Ювелирные товары: кольца";
		$otels[5]="Заклинания: нейтральные";
		$otels[51]="Заклинания: боевые и защитные";
		$otels[52]="Прочее";
		$otels[6]="Амуниция";
		$otels[61]="Еда";
		$otels[7]="Сувениры: открытки";
		$otels[71]="Сувениры: другие подарки";
		$otels[73]="Сувениры: подарки";
		$otels[72]="Уникальные подарки";
		
		$_GET['otdel']=(int)$_GET['otdel'];
		$otel=$otels[$_GET['otdel']];
		if ($otel=='') 
			{
			$otel=$otels[2];
			$_GET['otdel']=2;
			}

	
	
	$d[0] = getmymassa($user);
	$prokat_count=mysql_fetch_array(mysql_query("select count(*) from oldbk.inventory where prokat_idp > 0 and  `owner` = '{$_SESSION['uid']}' ; "));

	if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
	//$_GET['otdel'] = 1;

	if($_POST['enter']!='' && $_POST['pass']!='') {
					$data = mysql_query("SELECT * FROM oldbk.`bank` WHERE `owner` = '".$user['id']."' AND `id`= '".$_POST['id']."' AND `pass` = '".md5($_POST['pass'])."';");

					$data = mysql_fetch_array($data);
					if($data) {
						$_SESSION['bankid'] = $_POST['id'];
						//$error='Удачный вход.';
					}
					else
					{
					$error='Ошибка входа.';
					}
	}

	if ($_SESSION['bankid']>0)
	{
	$bank = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`bank` WHERE `id` = ".$_SESSION['bankid'].";"));
	}


# Fix 22.04.2010, by 2FED
if($_SESSION['bankid']>0) {
$bank_owner = mysql_query("SELECT owner FROM oldbk.bank WHERE id='{$_SESSION['bankid']}'");
$bank_owner = mysql_fetch_array($bank_owner);
}

# Fix 22.04.2010, by 2FED
if($bank_owner['owner'] > 0 && ($bank_owner['owner'] != $user['id'])) {  err('Попытка чита...'); $_SESSION['bankid'] = null; }





	if (($_GET['set'] OR $_POST['set'])) {
		if ($_GET['set']) { $set =(int)($_GET['set']); }
		if ($_POST['set']) { $set =(int)($_POST['set']); }
		if ($_POST['count'] < 1) { $_POST['count'] =1; }
		$_POST['count']=(int)($_POST['count']);

		$dress = mysql_fetch_array(mysql_query("select *  from oldbk.shop id LEFT JOIN prokat itemid ON id=itemid where  `kol` > 0 AND `idp` = '{$set}' LIMIT 1;"));

		if ($dress[0]>0)
		 {
	 		$test=mysql_fetch_array(mysql_query("select count(*) from oldbk.inventory where prototype='{$dress[id]}' and prokat_idp > 0 and  `owner` = '{$_SESSION['uid']}' ; "));
		 }


		if ($_POST['count'] == 1)
			{
			$needpay=$dress['startpay'];
			$date_do_stamp=time()+86400; //сейчас + сутки;
			 }
			 else
			 {
			$needpay=$dress['startpay']+($dress['daypay']*($_POST['count']-1));
			$date_do_stamp=time()+(86400*$_POST['count']); //сейчас + сутки*кол;
			 }
					 
		if ((int)$_GET['pveks']>0)
			{
			$pveks=(int)$_GET['pveks'];
			$load_veks=mysql_fetch_array(mysql_query("select * from oldbk.inventory where id='{$pveks}' and  owner='{$user['id']}' and prototype in (2016011,2016012,2016013,2016014,2016015,2016016) and setsale=0 "));
				if ($load_veks['id']>0)
					{
					$needpay=0;
					$time_config['2016011']=90;//суток
					$time_config['2016012']=60;//суток			
					$time_config['2016013']=30;//суток
					$time_config['2016014']=30;//суток
					$time_config['2016015']=30;//суток
					$time_config['2016016']=30;//суток			
					$add_days=$time_config[$load_veks['prototype']];
					if ($add_days<1) $add_days=1; // заглушка
					$_POST['count']=$add_days;
					$date_do_stamp=time()+(86400*$add_days); //сейчас + сутки*кол;			
					}
			}
			 
		$date_do=date("d/m/Y H:i:s",$date_do_stamp);

 		if (($dress[0]>0) and ($test[0]>0))
 		{
		 $error = "<font color=red><b>У Вас в аренде уже есть такого типа предмет.</b></font>";
 		}
		elseif($prokat_count[0]>2)
		 {
		 $error = "<font color=red><b>У Вас в аренде уже есть три предмета.</b></font>";
		 }
		 elseif (($dress['massa']+$d[0]) > (get_meshok())) {
			$error = "<font color=red><b>Недостаточно места в рюкзаке.</b></font>";
		}
		elseif((($bank['ekr']>= ($needpay)) && ($dress['kol'] >= 1)) or
			 (($load_veks['id']>0) && ($dress['kol'] >= 1))	) 
		{


		if (give_count($user['id'],1) )
		{
		if (((int)($dress['mag'])>0) and ($dress['magname']!=''))
		 {
		$includemagic=$dress['mag'];
		$includemagicname=$dress['magname'];
		$includemagicdex=$dress['magcount'];
		$includemagicmax=$dress['magcount'];
		$includemagicuses='200';
		$includemagiccost=$dress['magcost'];
		 }
		 else
		 {
 		$includemagic='';
		$includemagicname='';
		$includemagicdex='';
		$includemagicmax='';
		$includemagicuses='';
		$includemagiccost='';
		 }

		$dress['name'].=' (прокат)';

			//просчет цены согласно ремотке = за апы
			$new_cost=0;
			for ($yy=1;$yy<=5;$yy++)
			{
			$new_cost_a=upgrade_item($dress['cost'],$yy);
			$new_cost+=$new_cost_a['cost_add'];
			}
		//и за МФ+АПЫ		
		$dress['cost']+=round($dress['cost']/2)+$new_cost+10;
		
		
		$dress['ghp']+=$dress['addhp'];

		$dress['bron1']=$dress['bron1']!=0 ?$dress['bron1']+$dress['addbron']:0;
		$dress['bron2']=$dress['bron2']!=0 ?$dress['bron2']+$dress['addbron']:0;		
		$dress['bron3']=$dress['bron3']!=0 ?$dress['bron3']+$dress['addbron']:0;				
		$dress['bron4']=$dress['bron4']!=0 ?$dress['bron4']+$dress['addbron']:0;				

		$mffbroninfo=0;
		if (($dress['bron1']>0) OR ($dress['bron2']>0) OR ($dress['bron3']>0) OR ($dress['bron4']>0))
		{
		$mffbroninfo=$dress['addbron'];
		}

		$dress['mfinfo']='a:3:{s:5:"stats";i:'.$dress['fstat'].';s:2:"hp";i:'.$dress['addhp'].';s:4:"bron";i:'.$mffbroninfo.';}';

		if ($dress['new_item']>0)			
		{
		//чарки для классовых смоток
		$dress['charka']='5|a:1:{i:5;a:5:{i:0;a:1:{s:3:"ghp";i:35;}i:1;a:1:{s:5:"fstat";i:5;}i:2;a:1:{s:3:"fmf";i:60;}i:3;a:1:{s:2:"gw";i:2;}i:4;a:1:{s:2:"gm";i:2;}}}';
		$dress['ghp']+=35;
		$dress['fstat']+=5;
		$dress['fmf']+=60;
		$dress['gnoj']+=2;
		$dress['gtopor']+=2;
		$dress['gdubina']+=2;
		$dress['gmech']+=2;
		$dress['gfire']+=2;
		$dress['gwater']+=2;
		$dress['gair']+=2;
		$dress['gearth']+=2;
			/*
			Список чарований:
			Чарование V уровня:
			• Уровень жизни: +35
			• Возможных увеличений: +5
			• Возможных увеличений мф: +60
			• Мастерство владения оружием: +2
			• Магическое мастерство cтихий: +2		
			*/
		}
		else
		{
		$dress['charka']='4|a:1:{i:4;a:5:{i:0;a:1:{s:3:"ghp";i:25;}i:1;a:1:{s:5:"fstat";i:3;}i:2;a:1:{s:3:"fmf";i:30;}i:3;a:1:{s:2:"gw";i:1;}i:4;a:1:{s:2:"gm";i:1;}}}';
		$dress['ghp']+=25;
		$dress['fstat']+=3;
		$dress['fmf']+=30;
		$dress['gnoj']+=1;
		$dress['gtopor']+=1;
		$dress['gdubina']+=1;
		$dress['gmech']+=1;
		$dress['gfire']+=1;
		$dress['gwater']+=1;
		$dress['gair']+=1;
		$dress['gearth']+=1;
		}


		 if(mysql_query("INSERT INTO oldbk.`inventory`
				(`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,`nclass`,`mfinfo`,
					`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
					`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`unik`,
					`letter`,`present`, `includemagic`, `includemagicdex`, `includemagicmax`, `includemagicname`, `includemagicuses`, `includemagicekrcost`, `stbonus`, `ups`, `mfbonus`, `prokat_idp`, `prokat_do`,`idcity`,`charka`,`getfrom`
				)
				VALUES
				('{$dress['id']}','{$user['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['nclass']}' , '{$dress['mfinfo']}'  , '{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','0','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
				'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}','{$dress['razdel']}',2,
				'Аренда до: {$date_do}','Прокатная лавка','{$includemagic}','{$includemagicdex}','{$includemagicmax}','{$includemagicname}','{$includemagicuses}','{$includemagiccost}','{$dress['fstat']}','{$dress['fups']}','{$dress['fmf']}','{$dress[idp]}','{$date_do_stamp}','{$user[id_city]}','{$dress['charka']}','44'
				) ;"))
				{
				
				if ($_SERVER["SERVER_NAME"]=='capitalcity.oldbk.com')
					{
					$mc='cap';
					}
				elseif ($_SERVER["SERVER_NAME"]=='avaloncity.oldbk.com')
					{
					$mc='ava';
					}
				else   {
					$mc='none';
					}
				$dressid = $mc.mysql_insert_id();
				
				mysql_query("UPDATE `prokat` SET `kol`=`kol`-1 WHERE `idp` = '{$set}' LIMIT 1;");
				

				
				if ($load_veks['id']>0)
						{
								mysql_query("DELETE from oldbk.inventory where id='{$load_veks['id']}' LIMIT 1; ");
								$veks_info=" Оплачено векселем:(id:{$load_veks['id']}) {$load_veks['name']}  ";
						}
					else
					{
					mysql_query("UPDATE oldbk.`bank` set `ekr` = `ekr`- '".($needpay)."' WHERE id = {$bank['id']}");
					$bank['ekr'] -=$needpay;
					mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date` , `text` , `bankid`) VALUES ('".time()."','Аренда товара \"".$dress['name']."\"  <b>{$needpay} екр.</b>, <i>(Итого: {$bank['cr']} кр., {$bank['ekr']} екр.)</i>','{$bank['id']}');");
					}


				//new_delo
  		    			$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0;
					$rec['target_login']='прокатная лавка';
					$rec['type']=8;
					$rec['sum_kr']=0;
					$rec['sum_ekr']=$needpay;
					$rec['sum_kom']=0;
					$rec['item_id']=$dressid;
					$rec['item_name']=$dress['name'];
					$rec['item_count']=1;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=$dress['fups'];
					$rec['item_unic']=0;
					$rec['item_incmagic']=$includemagicname;
					$rec['item_incmagic_count']=$includemagicuses;
					$rec['item_arsenal']='';
					$rec['bank_id']=$bank['id'];
					$rec['add_info']=$date_do.$veks_info;
					add_to_new_delo($rec); //юзеру				
				
				
				$error = "<font color=red><b>Вы Арендовали \"{$dress['name']}\" Сроком  {$_POST['count']} суток за {$needpay} екр. <i>(до: {$date_do})</i>.<br>{$veks_info}</b></font>";
				$prokat_count[0]++;
				}
				else {
					$error = "<font color=red><b>Ошибка проката, Сообщите Администрации.</b></font>";
				}



		}
		else
		{
		$error = "<font color=red><b>У Вас недостаточно лимита передач!</b></font>";
		}



		}
		else {
			$error = "<font color=red><b>Недостаточно денег или нет вещей в наличии.</b></font>";
		}
	}
	else if (($_POST['setprod']) OR ($_GET['setprod']))
		{
		$_POST['prodlenie']=true; // шоб не выходило из режима при отобродении

		if ($_GET['setprod']) { $setprod =(int)($_GET['setprod']); }
		if ($_POST['setprod']) { $setprod =(int)($_POST['setprod']); }
		if ($_POST['count'] < 1) { $_POST['count'] =1; }
		$_POST['count']=(int)($_POST['count']);
		//тест итема
		$dress = mysql_fetch_array(mysql_query("select *  from oldbk.`inventory` LEFT JOIN `prokat` ON `prokat_idp`=`idp` where prokat_idp >0 and owner='{$user[id]}' and id={$setprod} LIMIT 1;"));
		// считаем на сколько продлить
		$needpay=$dress['daypay']*($_POST['count']);
		$date_do_stamp=$dress['prokat_do']+(86400*$_POST['count']); // + сутки*кол;
		$date_do=date("d/m/Y H:i:s",$date_do_stamp);
		// проверяем есть ли бабло и итем
		if(($bank['ekr']>= ($needpay)) && ($dress['prokat_idp'] > 0))
			{
	//продливаем
			if (
			mysql_query("UPDATE oldbk.`inventory` SET `prokat_do`='{$date_do_stamp}', `letter`='Аренда до: {$date_do}'  WHERE `owner`='{$user[id]}' and `id`='{$dress[id]}' and `prokat_idp`='{$dress[idp]}' LIMIT 1 ;")
			)
			{
			//снимаем бабки
			//new_delo
  		    			$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0;
					$rec['target_login']='прокатная лавка';
					$rec['type']=9;
					$rec['sum_kr']=0;
					$rec['sum_ekr']=$needpay;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($dress);
					$rec['item_name']=$dress['name'];
					$rec['item_count']=1;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=$dress['fups'];
					$rec['item_unic']=0;
					$rec['item_incmagic']=$dress['includemagicname'];
					$rec['item_incmagic_count']=$dress['includemagicuses'];
					$rec['item_arsenal']='';
					$rec['bank_id']=$bank['id'];
					$rec['add_info']=$date_do;
					add_to_new_delo($rec); //юзеру
			
			if (olddelo==1)
			{
			mysql_query("INSERT INTO oldbk.`delo` (`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES ('','0','{$user['id']}','\"".$user['login']."\" продлил аренду предмету: \"".$dress['name']."\" id:(".get_item_fid($dress).") [{$dress['duration']}/".$dress['maxdur']."]/Встр.Магия:{$includemagicname}/ups:{$dress['fups']}/ продлил сроком  {$_POST['count']} суток за {$needpay} екр. (до: {$date_do})',1,'".time()."');");
			}
			mysql_query("UPDATE oldbk.`bank` set `ekr` = `ekr`- '".($needpay)."' WHERE id = {$bank['id']}");
			$bank['ekr'] -=$needpay;
			mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date` , `text` , `bankid`) VALUES ('".time()."','Продление аренды товара \"".$dress['name']."\"  <b>{$needpay} екр.</b>, <i>(Итого: {$bank['cr']} кр., {$bank['ekr']} екр.)</i>','{$bank['id']}');");
			$error = "<font color=red><b>Вы продлили аренду \"{$dress['name']}\" еще на {$_POST['count']} суток за {$needpay} екр. <i>(до: {$date_do})</i>.</b></font>";
			}
			else
			{
			$error = "<font color=red><b>Ошибка продления аренды, Сообщите Администрации.</b></font>";
			}




			}
			else
			{
			$error="<font color=red><b>Недостаточно денег или у Вас нет такого предмета.</b></font>";
			}




		}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<script type="text/javascript" src="/i/globaljs.js"></script>    
	<title>Old BK - Прокатная лавка</title>
	<link rel="StyleSheet" href="newstyle_loc4.css" type="text/css">
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<SCRIPT LANGUAGE="JavaScript">
	function AddDay(varname, name, txt, cost) {
	    var el = document.getElementById("hint3");
		el.innerHTML = '<form method=post style="margin:0px; padding:0px;" name="getd"><table border=0 width=100% cellspacing=1 cellpadding=0 bgcolor="#CCC3AA"><tr><td align=center><B>Аренда на несколько дней</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</TD></tr><tr><td colspan=2>'+
		'<table border=0 width=100% cellspacing=0 cellpadding=0 bgcolor="#FFF6DD"><tr><INPUT TYPE="hidden" name="'+varname+'" value="'+name+'"><td colspan=2 align=center><B>'+txt+' за '+cost+' екр. на <INPUT TYPE="text" NAME="count" ID="count" size=4 > суток</B></td></tr><tr><td align=center colspan=2>'+
		'<a href="javascript:void(0);" class="button-mid btn" title="Арендовать" onClick="document.getd.submit();">Арендовать</a>'+
		'</TD></TR></TABLE></td></tr></table></form>';
		el.style.visibility = "visible";
			el.style.left = (document.body.scrollLeft - 20) + 100 + 'px';
		el.style.top = (window.pageYOffset + 5) + 100 + 'px';
		document.getElementById("count").focus();

	}
	// Закрывает окно
	function closehint3()
	{
		document.getElementById("hint3").style.visibility="hidden";
	}
	
	function showhide(id)
	{
	 if (document.getElementById(id).style.display=="none")
	 	{document.getElementById(id).style.display="block";}
	 else
	 	{document.getElementById(id).style.display="none";}
	}
	
	</SCRIPT>    
</head>
<body id="arenda-body">
<div id="page-wrapper">

    <div class="title">
        <div class="h3">Прокатная лавка</div>
        <div id="buttons">
            <a class="button-dark-mid btn" href="javascript:void(0);" title="Подсказка" onclick="window.open('help/prokat.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes');">Подсказка</a>            
            <a class="button-mid btn" href="javascript:void(0);" title="Обновить" onclick="location.href='prokat.php?refresh='+Math.random();" >Обновить</a>                    
            <a class="button-mid btn" href="javascript:void(0);" title="Вернуться" onclick="location.href='prokat.php?exit=1';">Вернуться</a>
        </div>
    </div>



<?

if(!$_SESSION['bankid']) {
?>
    <div id="prokat">
        <table cellspacing="0" cellpadding="0">
            <colgroup>
                <col width="313px">
                <col width="900px">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <td id="auth">
                    <div class="auth-block"><form method=post name="loginbank" action="prokat.php">
                        <div class="inner-auth">
                            <div class="auth-num">
                                <strong>№</strong>
<?


	$banks = mysql_query("SELECT * FROM oldbk.`bank` WHERE `owner` = ".$user['id'].";");
	echo "<select style='width:150px' name=id>";
	while ($rah = mysql_fetch_array($banks)) {
			echo "<option>",$rah['id'],"</option>";
	}
	echo "</select>";
?>
                            </div>
                            <div class="auth-pass">
                                <strong>Пароль</strong> <input type=password name=pass size=21>
									   <input type=hidden name='enter' value='yes'>
                            </div>
                            <div class="center enter">
                                <a href="javascript:void(0);" class="button-big btn" title="Войти" onClick="document.loginbank.submit();">Войти</a>			
                            </div>
                        </div>
                        <div class="hint-block center"><? if ($error) {echo '<strong>'.$error.'</strong>'; }?>
                            Авторизуйтесь для совершения покупок за <strong>екр</strong>
                        </div>
                        </form>
                    </div>
                </td>

                <td style="padding-left: 50px;">
                    <div>
                                   <br><br>
                        <strong>
                            <span style="color: #003585;">Если Вы хотите усилиться и готовы за это заплатить, Прокатная Лавка предоставляет Вам такую возможность.</span>
                        </strong>
                    </div>

                    <div style="margin-top: 20px;">
                        Здесь можно взять усиленное оружие или обмундирование на день или несколько дней. <br><br>
                        Оплата проката подневная.<br><br>
                        Вы можете арендовать до 3х различных вещей одновременно.<br><br>
                        Невозможно взять в прокат две одинаковые вещи.<br>
                    </div>
                    <br>
                    <div style="margin-top: 20px;">
                        При оплате проката сразу на несколько дней, начиная со 2го дня и дальше действует скидка, однако, досрочный возврат уже оплаченного проката невозможен.<br><br>
                        До окончания срока проката можно продлить аренду вещи, не дожидаясь, когда вещь будет возвращена в Лавку.<br><br>
			<font color=#003388><b>Не забывайте проверять в инвентаре срок окончания проката</b></font>, ибо по истечению срока вещь будет автоматически снята с вас и возвращена в Прокатную Лавку, даже если в это время вы будете в бою.</font><br><br>
                    </div>
                </td>
                <td></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?
	die();
}
?>
    <div id="aukcion">
    <?=$error;?>
	 <table cellspacing="0" cellpadding="0" border="0">
            <colgroup>
                <col>
                <col width="300px">
            </colgroup>
            <tbody>
<TR>
	<TD valign=top align=left>

 <table cellspacing="0" cellpadding="0"  border="0" bgcolor="#A5A5A5">
<?
if ($_REQUEST['prodlenie']) { $otel="Продление аренды" ; }
?>

<TR>
	<TD align=center><B><?=$otel;?></B></TD>
</TR>
<TR><TD>
<TABLE CLASS="a_strong" BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">
<?

 if ($_POST['prodlenie'])
	{
	
		$data = mysql_query("select *  from oldbk.`inventory` LEFT JOIN `prokat` ON `prokat_idp`=`idp` where prokat_idp >0 and owner='{$user[id]}' ORDER by `prokat_do` ASC;");
	while($row = mysql_fetch_array($data)) {
		if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5'; }

		if($row['add_pick']!=''&&$row['pick_time']>time())
			{
			$row['img']=$row['add_pick'];
			}

		echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
		?>
		<BR><A HREF="prokat.php?otdel=<?=$_GET['otdel']?>&setprod=<?=$row['id']?>&sid=">Продлить на сутки</A>
		<IMG SRC="i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Продлить на несколько дней" style="cursor: pointer" onclick="AddDay('setprod','<?=$row['id']?>', '<?=$row['name']?>','<?=$row['daypay']?>')"></TD>
		<?php
		echo "<TD valign=top>";
		$row['count']=1; // заглушка

		echo "Продление аренды:<b>$row[daypay] екр. за сутки</b><br>";
		$row[needident]=0;
		showitem ($row);
		echo "</TD></TR>";
	}

	}
else
	{
	
	if ($viewlevel==true) 
  		{
/*
  			if ($user['level']>13)
  			{
	  		$addlvl=" and nlevel='13'  ";  			
  			}
  			else
  			{*/
	  		$addlvl=" and nlevel='{$user['level']}'  ";
	  		//}
  		}
  		else
  		{
  		$addlvl="";
  		}
  		
	$bonus_veks=array();
	$load_veks=mysql_query("select * from oldbk.inventory where owner='{$user['id']}' and prototype in (2016011,2016012,2016013,2016014,2016015,2016016) and setsale=0 ");
	if (mysql_num_rows($load_veks) > 0) 
		{	
		while($veks = mysql_fetch_array($load_veks)) 
			{
			$bonus_veks[$veks[id]]=$veks;
			}
		}
	
	
	$data = mysql_query("select *  from oldbk.shop id LEFT JOIN prokat itemid ON id=itemid where  `kol` > 0  ".$addlvl." AND `razdel` = '".(int)$_GET['otdel']."'  ORDER by `startpay` ASC;");
	while($row = mysql_fetch_array($data)) 
	{
		if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5'; }
		echo "<TR bgcolor={$color}><TD align=center style='width:150px;vertical-align:middle;'><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
		?>
		<BR><A HREF="prokat.php?otdel=<?=$_GET['otdel']?>&set=<?=$row['idp']?>&sid=">Арендовать на сутки</A>
		<IMG SRC="i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Арендовать на несколько дней" style="cursor: pointer" onclick="AddDay('set','<?=$row['idp']?>', '<?=$row['name']?>','<?=$row['daypay']?>')">
		<BR>
		<?
				$showarr=array();
			    foreach($bonus_veks as $id => $val) 
			    {
			    	
			    	if ($showarr[$val['prototype']]==0)
			    		{
					    echo "<br><a href='prokat.php?otdel={$_GET['otdel']}&set={$row['idp']}&pveks={$id}'><img src=http://i.oldbk.com/i/sh/{$val['img']} title='Оплатить векселем {$val['name']}'></a><br><small><b>Оплатить векселем</b></small><br>";
					    $showarr[$val['prototype']]=1;
					}
			    }
		?>
		</TD>
		<?php
		echo "<TD valign=top>";
		
		$row['name'].=' (прокат)';
		
		
		//просчет цены согласно ремотке = за апы
			$new_cost=0;
			for ($yy=1;$yy<=5;$yy++)
			{
			$new_cost_a=upgrade_item($row['cost'],$yy);
			$new_cost+=$new_cost_a['cost_add'];
			}
		//и за МФ+АПЫ		+ 10 кр
		$row['cost']+=round($row['cost']/2)+$new_cost+10;
		
		$row[GetShopCount()]=$row['kol'];
		$row[needident]=0;
		if ((int)($row['mag'])>0)
		 {
			$row['includemagic']=$row['mag'];
			$row['includemagicname']=$row['magname'];
			$row['includemagicdex']=$row['magcount'];
			$row['includemagicmax']=$row['magcount'];
			$row['includemagicuses']=200;			
			
		 }
 		if ($row['fups'] >0)
 		{
		 $row['ups']=$row['fups'];
		 }
		if ($row['fstat'] >0)
		{
		$row['stbonus']=$row['fstat'];
		}
		if ($row['fmf'] >0)
		{
		$row['mfbonus']=$row['fmf'];
		}
		
		$row['unik']=2;
		
		$row['ghp']+=$row['addhp'];

		$row['bron1']=$row['bron1']!=0 ?$row['bron1']+$row['addbron']:0;
		$row['bron2']=$row['bron2']!=0 ?$row['bron2']+$row['addbron']:0;		
		$row['bron3']=$row['bron3']!=0 ?$row['bron3']+$row['addbron']:0;				
		$row['bron4']=$row['bron4']!=0 ?$row['bron4']+$row['addbron']:0;	
		
		$mffbroninfo=0;
		if (($row['bron1']>0) OR ($row['bron2']>0) OR ($row['bron3']>0) OR ($row['bron4']>0))
		{
		$mffbroninfo=$row['addbron'];
		}

		$row['mfinfo']='a:3:{s:5:"stats";i:'.$row['fstat'].';s:2:"hp";i:'.$row['addhp'].';s:4:"bron";i:'.$mffbroninfo.';}';

		if ($row['new_item']>0)			
		{
		//чарки для классовых смоток
		$row['charka']='5|a:1:{i:5;a:5:{i:0;a:1:{s:3:"ghp";i:35;}i:1;a:1:{s:5:"fstat";i:5;}i:2;a:1:{s:3:"fmf";i:60;}i:3;a:1:{s:2:"gw";i:2;}i:4;a:1:{s:2:"gm";i:2;}}}';
		$row['ghp']+=35;
		$row['stbonus']+=5;
		$row['mfbonus']+=60;
		$row['gnoj']+=2;
		$row['gtopor']+=2;
		$row['gdubina']+=2;
		$row['gmech']+=2;
		$row['gfire']+=2;
		$row['gwater']+=2;
		$row['gair']+=2;
		$row['gearth']+=2;
		}
		else
		{
		$row['charka']='4|a:1:{i:4;a:5:{i:0;a:1:{s:3:"ghp";i:25;}i:1;a:1:{s:5:"fstat";i:3;}i:2;a:1:{s:3:"fmf";i:30;}i:3;a:1:{s:2:"gw";i:1;}i:4;a:1:{s:2:"gm";i:1;}}}';
		$row['ghp']+=25;
		$row['stbonus']+=3;
		$row['mfbonus']+=30;
		$row['gnoj']+=1;
		$row['gtopor']+=1;
		$row['gdubina']+=1;
		$row['gmech']+=1;
		$row['gfire']+=1;
		$row['gwater']+=1;
		$row['gair']+=1;
		$row['gearth']+=1;
		}		
		

		
		

		echo "Первый день аренды:<b>{$row['startpay']} екр.</b><br>";
		echo "Последующие:<b>{$row['daypay']} екр. за сутки</b><br>";
		showitem ($row);
		echo "</TD></TR>";
	}
}


?>
</TABLE>
</TD></TR>
</TABLE>

	</TD>
	<TD valign=top width=280>
			<div style="text-align:right;">
			<form method="post" name="cont">
			<input type=hidden name=prodlenie value='yes'>
	                 <a href="javascript:void(0);" class="button-big btn" title="Продлить Аренду" onClick="document.cont.submit();">Продлить Аренду</a><br>
			 <a class="button-big btn" href="javascript:void(0);" title="Сменить счет" onclick="location.href='prokat.php?bankexit=1';">Сменить счет</a>
			</form>
			</div>

<table id="filter" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td align="left">
                                 <strong>Вес всех ваших вещей: <span class="money"><? echo $d[0].'/'.get_meshok();?></span></strong><br>
                                У Вас в наличии: <span class="money"><strong><?=$bank['ekr']?></strong></span><strong> екр.</strong><br>
                                Уже в аренде: <span class="money"><strong><?=$prokat_count[0]?>/3</strong></span><strong> вещей</strong><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="hint-block size11 center">
                                <strong><span class="money">
                                <div style="text-align:left;">
					<form method=post>
					<small>
					<input type="radio" name=fall value=0 <? if ((int)($_POST['fall'])==0) { echo 'checked="checked"' ; } ?> onchange="submit();">Показывать все вещи<br>
					<input type="radio" name=fall value=1 <? if ($_POST['fall']>0) { echo 'checked="checked"' ; $viewlevel=true; } ?> onchange="submit();">Показывать вещи только моего уровня
					</small>
					</form>
				</div>
                                </span></strong>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                        </tr>
		<?
echo '
<tr>
                            <td class="filter-title">Оружие</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a HREF="?tmp='.mt_rand(1111,9999).'&otdel=1">кастеты и ножи</a>
                                    </li>
                                    <li>
					<a href="?tmp='.mt_rand(1111,9999).'&otdel=11">топоры</a>
                                    </li>
                                    <li>
                                        <a href="?tmp='.mt_rand(1111,9999).'&otdel=12">дубины и булавы</a>
                                    </li>
                                    <li>
                                        <a href="?tmp='.mt_rand(1111,9999).'&otdel=13">мечи</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="filter-title">Одежда и броня</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a href="?tmp='.mt_rand(1111,9999).'&otdel=2">сапоги</a>
                                    </li>
                                    <li>
                                        <a href="?tmp='.mt_rand(1111,9999).'&otdel=21">перчатки</a>
                                    </li>
                                    <li>
                                        <a href="?tmp='.mt_rand(1111,9999).'&otdel=22">легкая броня</a>
                                    </li>
                                    <li>
                                        <a href="?tmp='.mt_rand(1111,9999).'&otdel=23">тяжелая броня</a>
                                    </li>
                                    <li>
                                        <a href="?tmp='.mt_rand(1111,9999).'&otdel=24">шлемы</a>
                                    </li>
                                    <li>
                                        <a href="?tmp='.mt_rand(1111,9999).'&otdel=3&">щиты</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="filter-title">Ювелирные товары</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a href="?tmp='.mt_rand(1111,9999).'&otdel=4">серьги</a>
                                    </li>
                                    <li>
                                       <a href="?tmp='.mt_rand(1111,9999).'&otdel=41">ожерелье</a>
                                    </li>
                                    <li>
                                        <a href="?tmp='.mt_rand(1111,9999).'&otdel=42">кольца</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="filter-title">Прочее</td>
                        </tr>
                        <tr>
                            <td class="filter-item">
                                <ul>
                                    <li>
                                        <a href="?tmp='.mt_rand(1111,9999).'&otdel=6">амуниция</a>
                                    </li>
                                </ul>
                            </td>
                        </tr>';
	?>
                        <tr>
                            <td style="text-align: right;">
                                <img src="http://i.oldbk.com/i/images/arenda/arenda_illustration.jpg">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    
	<div id="hint3" class="ahint" style="position: absolute;width: 340px;FONT-SIZE: 13px;color:#000000;"></div>
    </TD>
    </FORM>
</TR>
</TABLE>


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
d.write('<a href="http://top.mail.ru/jump?from=1765367" target="_blank">'+
'<img src="http://df.ce.ba.a1.top.mail.ru/counter?id=1765367;t=49;js='+js+
a+';rand='+Math.random()+'" alt="Рейтинг@Mail.ru" border="0" '+
'height="31" width="88"><\/a>');if(11<js)d.write('<'+'!-- ');//--></script>
<noscript><a target="_blank" href="http://top.mail.ru/jump?from=1765367">
<img src="http://df.ce.ba.a1.top.mail.ru/counter?js=na;id=1765367;t=49"
height="31" width="88" border="0" alt="Рейтинг@Mail.ru"></a></noscript>
<script language="javascript" type="text/javascript"><!--
if(11<js)d.write('--'+'>');//--></script>
<!--// Rating@Mail.ru counter-->
	
	</div>
</div>
</body>
</html>
