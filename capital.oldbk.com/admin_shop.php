<?php
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
//////////////////////////////

session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

include "connect.php";
include "functions.php";



if  (!($user[klan]=='radminion' or  $user[klan]=='Adminion' or  $user[klan]=='testTest'  or $user['id']==697032 or $user['id']==8325) ) {  die('Страница не найдена...'); }

include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
include "clan_kazna.php";
if ($user[klan]!='')
{
  	$clan_id=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '{$user[klan]}' LIMIT 1;"));
   	if ($clan_id[id] >0)
   	{
    		if ($clan_id[glava]==$user[id])
		{
			$clan_kazna=clan_kazna_have($clan_id[id]);
		}
	}
}



$d = mysql_fetch_array(mysql_query("SELECT sum(`massa`) FROM oldbk.`inventory` WHERE `owner` = '{$_SESSION['uid']}' AND `dressed` = 0 AND bs_owner=0 AND `setsale` = 0 ; "));

$what_not_to_sell=' AND `prototype` not in (104,100000009,100000010,20000,1006232,1006233,1006234,510,550,599) and (`prototype` < 55510300 OR `prototype` > 55510400) ';
$resurs=' AND ((`prototype`>3000 AND `prototype` <3022 ) OR (`prototype`>103000 AND `prototype` <103022)) ';

//res koeficent
$kk_res=array(0=>'0',1=>'0',2=>'0',3=>'0',4=>'0',5=>'0',6=>'0',7=>'0',8=>'0.1',9=>'0.2',10=>'0.3',11=>'0.3',12=>'0.3',13=>'0.3');

	$arr_ot[1]="Оружие: кастеты,ножи";
	$arr_ot[11]="Оружие: топоры";
	$arr_ot[12]="Оружие: дубины,булавы";
	$arr_ot[13]="Оружие: мечи";
	$arr_ot[14]="Оружие: луки и арбалеты";
	$arr_ot[2]="Одежда: сапоги";
	$arr_ot[21]="Одежда: перчатки";
	$arr_ot[22]="Одежда: легкая броня";
	$arr_ot[23]="Одежда: тяжелая броня";
	$arr_ot[24]="Одежда: шлемы";
	$arr_ot[3]="Щиты";
	$arr_ot[4]="Ювелирные товары: серьги";
	$arr_ot[41]="Ювелирные товары: ожерелья";
	$arr_ot[42]="Ювелирные товары: кольца";
	$arr_ot[5]="Заклинания: нейтральные";
	$arr_ot[51]="Заклинания: боевые и защитные";
	$arr_ot[6]="Амуниция";
	$arr_ot[60]="Молитвенные предметы";
	$arr_ot[61]="Еда";
	$arr_ot[99]="Прилавок Великих";
	$arr_ot[100]="Прилавок Великих (100 побед) ";
	$arr_ot[62]="Ресурсы";
	$arr_ot[63]="Инструменты";
	$arr_ot[300]="Прилавок Великих (300 побед) ";
	$arr_ot[500]="Прилавок Великих (500 побед) ";
	$arr_ot[700]="Прилавок Великих (700 побед) ";

//таймеры работы скупок на будущее (мусоные дни, время старта, время финиша)
//при включении отрабатывает функция разборки вещи, подсчета ее АПов, МФов, подгонов. + сама шмотка.




	//коефицент скупки во время таких дней.(без учета дюрейшена)
	include "action_days_config.php";

	if($shop_skupka==1)
	{
		//проверяем привязку вещи при скупке. Если привязана, то стоимость подгонов не учитываем.
		$check_sowner=1;
		//условие скупки
	   	//$row[type] < 12 && ($row[nlevel] <= 4 || $row[type]=5 || ($row[nlevel] <= 5 && $row[type] = 10))
	}

	//цена, прибавляемая во вермя апанья хардкодед!! $up_lvl_cost=array(7=>'25',8=>'35',9=>'85',10=>'120');
	$skupka_sql='';





function curr_price($item,$check_sowner=0){
	global $user,$shop_skupka;

	$max_ups=5;   // Максимальное кол. апов шмотки - подобная запись есть В РЕМОНТКЕ! Там меня так же!!!

    $prot=mysql_fetch_array(mysql_query('select * from oldbk.shop where id = '.$item[prototype]));
    if($prot[id]>0)
    {
    	//все ок..
    }
    else
    {
    	$prot['cost']=$item[cost];
    	$prot[nlevel]=0;
    }
	    $mf_cost=0;
	    $is_mf = !(strpos($item['name'], '(мф)') === false);
	    if($is_mf>0){
	    	$mf_cost=$prot[cost]*0.5;
	    	if (($prot['gsila'] == 0) and ($prot['glovk'] == 0) and ($prot['ginta'] == 0) and ($prot['gintel'] == 0))
			{
				$mf_cost = round($prot['cost']*0.5, 0);
			}

	    }
	    if($user[id]==28453 || $user[id]==326)
	    {
	      // print_r($prot);
	       echo $prot[name].': МФ:'.$mf_cost;
	    }

	    $real_price[sowner]=$item[sowner];
	    $real_price[prot_cost]=$prot[cost];
	    $real_price[mf_cost]=$mf_cost;
	    $real_price[item_cost]=$item[cost];
	    $cost_add = round($prot['cost'], 0);
		$max_ups_left = $max_ups - $item['ups'];
	    $mx_op=array(1=>'5',2=>'4',3=>'3',4=>'2',5=>'1');
		$u_cost=0;


		if($item['ups']>0 && $real_price[sowner]==0 && $check_sowner==1 )
		{
			for($cc=$item['ups'];$cc>0;$cc--)
			{
				$costs[$cc]=upgrade_item($cost_add,$mx_op[$cc]);
				$u_cost+=$costs[$cc][up_cost];
			}
		}
		if($user[id]==28453 || $user[id]==326)
		    {
		       echo ' под:'.$u_cost;
		    }
		$up_price=0;

	    if($item[up_level]>6)
	    {
			$up_lvl_cost=array(7=>'25',8=>'35',9=>'85',10=>'120');
	  		// $prot[nlevel]>$item[up_level] && $prot[nlevel]>6

	        for($up_lvl=$item[up_level];$up_lvl>6;$up_lvl--)
	        {
	        	if($up_lvl>$prot[nlevel])
	        	{
	        		$up_price+=$up_lvl_cost[$up_lvl];
	        	}
	        }
	    }
	    if($user[id]==28453 || $user[id]==326)
	    {
	       echo ' лвл:'.$up_price;
	    }

	    $sharp_pr=0;
		if($item['type']==3 && $shop_skupka==1)
		{
			$sharp=explode("+",$item['name']);
			if((int)($sharp[1])>0)
			{
				$is_sharp=array(1=>20,2=>40,3=>80,4=>160,5=>320);
				$sharp_pr=$is_sharp[$sharp[1]];
			}
		}

	    if($user[id]==28453 || $user[id]==326)
	    {
	       echo ' точка:'.$sharp_pr;
	    }

	    //высчитываем от госцены цена + мф + подгоны + апы (цена свитков из храма), берем 90% и за это скупаем в госе
	    $real_price[up_price]=$up_price;
	    $real_price[summ]=$prot[cost]+$mf_cost+$u_cost+$up_price+$sharp_pr;
	    if($user[id]==28453 || $user[id]==326)
	    {
	       echo ' итог:'.$real_price[summ].' цена продажи:'.($real_price[summ]*0.9).'<br>';
	    }
	return $real_price;
}

function sellitems($id,$row,$add_money,$delo_txt,$echo_txt)
{

				// Changed 5.05.2010 Auth Weathered День Утиля
	//			$allcost=;
	             if($row[add_pick]!='')
			       {
			         undress_img($row);
			          $ok1=1;
			       }
			       else
			       {
			       	  $ok1=1;
			       }

	                 if($ok1==1){
						mysql_query("UPDATE `users` set `money` = `money`+ '".$add_money."' WHERE id = {$_SESSION['uid']}");
						if(olddelo==1)
						{
						//old_delo
						mysql_query("INSERT INTO oldbk.`delo` (`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES 	('','0','{$_SESSION['uid']}','".$delo_txt."',1,'".time()."');");
						}
						echo $echo_txt;
						$shmot=array(1,2,3,4,5,8,9,10,11,28,27);
		                if(in_array($row[type],$shmot))
		                {
		                   destructitem($row['id']);
		                }
		                else
		                {
							mysql_query("DELETE FROM oldbk.`inventory` WHERE `id` in (".$id.") and owner=".$_SESSION['uid'].";");
		                }
		             }
					 else
					 {
					 	echo 'Ошибка продажи 1. Попробуйте еще раз...';
					 }
}


	if ($_POST['sclid'])
	{
		$newclass=(int)$_POST['setitemclass'];
		$setitemid=(int)$_POST['sclid'];

		if ( ($newclass>=0) and ($newclass<=4))
		{
			mysql_query("UPDATE `oldbk`.`shop` SET `nclass`='{$newclass}' WHERE `id`='{$setitemid}' limit 1;");

		}

	}



	if (($_GET['set'] OR $_POST['set'])) {
		if ($_GET['set']) { $set = $_GET['set']; }
		if ($_POST['set']) { $set = $_POST['set']; }
		if($_POST['is_sale']==0){
		//add by Umk for group sell
			if ((!$_POST['count']) OR ($_POST['count']==0) OR ($_POST['count']<0) ) { $_POST['count'] =1; }
			$_POST['count']=(int)$_POST['count'];
			$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`shop` WHERE `id` = '{$set}' LIMIT 1;"));

			$gos_cost=$dress['cost'];
			if($user[prem]>0)
			{
			 $dress['cost']=$dress['cost']*0.9;
			}

/*			if ($dress[need_wins]>$user[winstbat]) // проверка на прямую сслку :)
			{
			 err("У вас нехватает побед в Великих битвах для покупки этого предмета!");
			}
			else
*/
			if (true)
			{

			//привязываем шмотки из раздела великих = если прописаны нужные попебы то одаем подарком
			 if (($dress[need_wins]>0) and ($dress[type]==12))
			    {
			    $as_present='Удача';
			    $as_sowner=0;
			    }
			 elseif (($dress[need_wins]>0) and ($dress[type]!=12)) //если вещи  то не подарком а привязанное
			    {
    			    $as_present='';
			    $as_sowner=$user[id];
			    }
			    else
			    {
			    $as_sowner=0;
			    $as_present='';
			    }


				for($k=1;$k<=$_POST['count'];$k++) { 					//echo "<font color=red><b>qwe.</b></font>";
					$str='';
					$sql='';
          $dress['up_level']=0;
          $dress['add_time']=0;
					if($dress[nlevel]>6)
					{
					  $dress['up_level']=$dress[nlevel];
					}


          if($dress['id']==6018) {
            $dress['ups']=257500;
            $dress['up_level']=10;
            $dress['add_time']=300000;
          } elseif($dress['id']==6019) {
            $dress['ups']=1200000;
            $dress['up_level']=20;
            $dress['add_time']=1500000;
          } elseif($dress['id']==6020) {
              $dress['ups']=11500000;
              $dress['up_level']=30;
              $dress['add_time']=115000000;
            }

					if(mysql_query("INSERT INTO oldbk.`inventory`
					(`prototype`,`sowner`,`present`,`owner`,`name`,`type`,`massa`,`cost`,`ecost`,`img`,`maxdur`,`isrep`,`nclass`,`ups`,`up_level`,`add_time`,
						`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
						`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`idcity`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`ab_mf`,  `ab_bron` ,  `ab_uron`,`ekr_flag`,`rareitem`,`stbonus`, `mfbonus`,
						`otdel`,`gmp`,`gmeshok`, `group`,`letter`,`img_big`,`notsell`,`craftspeedup`,`craftbonus` ".$str."
					)
					VALUES
					('{$dress['id']}',{$as_sowner},'{$as_present}','{$_SESSION['uid']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$gos_cost},'{$dress['ecost']}','{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['nclass']}','{$dress['ups']}','{$dress['up_level']}', '{$dress['add_time']}' , '{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
					'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
					'{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}','{$user[id_city]}', '{$dress[includemagic]}','{$dress[includemagicdex]}','{$dress[includemagicmax]}','{$dress[includemagicname]}','{$dress[includemagicuses]}','{$dress[includemagiccost]}','{$dress[includemagicekrcost]}', '{$dress['ab_mf']}',  '{$dress['ab_bron']}' ,  '{$dress['ab_uron']}' , {$dress['ekr_flag']} ,{$dress['rareitem']} ,{$dress['stbonus']} ,{$dress['mfbonus']}
					,'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}','{$dress['img_big']}','{$dress['notsell']}','{$dress['craftspeedup']}','{$dress['craftbonus']}' ".$sql."
					) ;"))
					{
						$good = 1;
						$insert_id[$k]=mysql_insert_id();
					}
					else {
						$good = 0;
            echo mysql_error();
            echo "INSERT INTO oldbk.`inventory`
  					(`prototype`,`sowner`,`present`,`owner`,`name`,`type`,`massa`,`cost`,`ecost`,`img`,`maxdur`,`isrep`,`nclass`,`ups`,`up_level`,`add_time`,
  						`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
  						`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`idcity`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`ab_mf`,  `ab_bron` ,  `ab_uron`,`ekr_flag`,`rareitem`,`stbonus`, `mfbonus`,
  						`otdel`,`gmp`,`gmeshok`, `group`,`letter`,`img_big`,`notsell`,`craftspeedup`,`craftbonus` ".$str."
  					)
  					VALUES
  					('{$dress['id']}',{$as_sowner},'{$as_present}','{$_SESSION['uid']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$gos_cost},'{$dress['ecost']}','{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['nclass']}','{$dress['ups']}','{$dress['up_level']}', '{$dress['add_time']}' , '{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
  					'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}',
  					'{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}','{$user[id_city]}', '{$dress[includemagic]}','{$dress[includemagicdex]}','{$dress[includemagicmax]}','{$dress[includemagicname]}','{$dress[includemagicuses]}','{$dress[includemagiccost]}','{$dress[includemagicekrcost]}', '{$dress['ab_mf']}',  '{$dress['ab_bron']}' ,  '{$dress['ab_uron']}' , {$dress['ekr_flag']} ,{$dress['rareitem']} ,{$dress['stbonus']} ,{$dress['mfbonus']}
  					,'{$dress['razdel']}','{$dress['gmp']}','{$dress['gmeshok']}','{$dress['group']}','{$dress['letter']}','{$dress['img_big']}','{$dress['notsell']}','{$dress['craftspeedup']}','{$dress['craftbonus']}' ".$sql."
  					) ;" ;
					}
				}
				if ($good)
				{
					//mysql_query("UPDATE oldbk.`shop` SET `".GetShopCount()."`=`".GetShopCount()."`-{$_POST['count']} WHERE `id` = '{$set}' LIMIT 1;");
					echo "<font color=red><b>Вы купили {$_POST['count']} шт. \"{$dress['name']}\".</b></font>";
					//mysql_query("UPDATE `users` set `money` = `money`- '".($_POST['count']*$dress['cost'])."' WHERE id = {$_SESSION['uid']} ;");
					//$user['money'] -= $_POST['count']*$dress['cost'];
					$limit=$_POST['count'];
					$invdb = mysql_query("SELECT * FROM oldbk.`inventory` WHERE id in (".implode(',',$insert_id).") ORDER by `id` DESC ;" );

					if ($limit == 1) {
						$dressinv = mysql_fetch_array($invdb);
						$dressid = get_item_fid($dressinv);
						$dresscount=" ";
					}
					else {
						$dressid="";
						while ($dressinv = mysql_fetch_array($invdb))  {
							$dressid .= get_item_fid($dressinv).",";
						}
						$dresscount="(x".$_POST['count'].") ";
					}
					$allcost=$_POST['count']*$dress['cost'];

					//new delo

					$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user[money];

					$user['money'] -= $_POST['count']*$dress['cost'];

					$rec['owner_balans_posle']=$user[money];
					$rec['target']=0;
					$rec['target_login']='гос.маг.';
					$rec['type']=1;//покупка из госа
					$rec['sum_kr']=$allcost;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=$dressid;
					$rec['item_name']=$dress['name'];
					$rec['item_count']=$_POST['count'];
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$gos_cost;
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']='';
					add_to_new_delo($rec);
				}
			} else {
				echo "<font color=red><b>Недостаточно денег или нет вещей в наличии.</b></font>";
			}
		}
	}

?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<link rel="stylesheet" href="newstyle_loc4.css" type="text/css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type='text/javascript' src='http://i.oldbk.com/i/js/recoverscroll.js'></script>
<SCRIPT LANGUAGE="JavaScript">
function save(f){
   f.submit();
   return true;
}

function save1(id){

	var gg;
	var f = document.f1;
	id='rzd'+id;
	gg=document.getElementById(id).value;
    if(gg==0)
    {document.getElementById(id).value=1;}
    else
    {document.getElementById(id).value=0;}
    f.submit();
    return true;
}

function AddCount(name, txt, sale, href) {
    var el = document.getElementById("hint3");
    if(sale==1)
    {
    	var sale_txt= 'Продать неск. штук';
    	var a_href='';
        var a_href='action="?sale=1&id=1'+href+'"';
    }
    else
    {
    	var sale=0;
    	var sale_txt= 'Купить неск. штук';
        var a_href='';
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

// Закрывает окно
function closehint3()
{
	document.getElementById("hint3").style.visibility="hidden";
}
</SCRIPT>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#e0e0e0>
<script type='text/javascript'>
RecoverScroll.start();
</script>
<TABLE border=0 width=100% cellspacing="0" cellpadding="0">
<FORM action="city.php" method="GET">
<tr><td><h3>Магазин admin</td><td align=right>
<INPUT TYPE="button" value="Вернуться" OnClick="location.href='main.php?tmp='+Math.random(); return false;" ></td></tr>
</FORM>
</table>

<table border=0>
<tr>Название вещи или айди: <td><form method="GET"><input value="<?php if (isset($_GET['search'])) echo htmlspecialchars($_GET['search'],ENT_QUOTES); ?>" type="text" name="search"> <input type="submit" value="Искать"></form></td></tr>
</table>

<TABLE border=0 width=100% cellspacing="0" cellpadding="4">
<TR>
	<TD valign=top align=left>
<!--Магазин-->
<form method=post name="f1">
<TABLE border=0 width=100% cellspacing="0" cellpadding="0" bgcolor="#A5A5A5">
<TR>
	<TD align=center><B>Отдел "<?php

	$otd=(int)$_GET['otdel'];
	if ($otd==0)
		{
		$_GET['otdel'] = 1;
		$otd=1;
		}

	$otst=$arr_ot[$otd];

	if ($otst=='') $frm='Раздел: '.$otd;


	?>"</B> <?=$frm;?>
    </form>
	</TD>
</TR>
<TR><TD><!--Рюкзак-->
<TABLE BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">
<?

if (($_GET['otdel']==100)and($user[winstbat]<100))
 {
 err('<div align=center><b>У вас еще нет 100 побед в Великих битвах!</b></div>');
 }
 else
  if (($_GET['otdel']==300)and($user[winstbat]<300))
 {
 err('<div align=center><b>У вас еще нет 300 побед в Великих битвах!</b></div>');
 }
 else
  if (($_GET['otdel']==500)and($user[winstbat]<500))
 {
 err('<div align=center><b>У вас еще нет 500 побед в Великих битвах!</b></div>');
 }
 else
  {
   	  $vitrina=")";

	if (isset($_GET['search'])) {
		$isid = intval($_GET['search']);
		if ($isid) {
			$data = mysql_query("SELECT * FROM oldbk.`shop` WHERE id = ".$isid);
		} else {
			$data = mysql_query("SELECT * FROM oldbk.`shop` WHERE (name LIKE '%".mysql_real_escape_string($_GET['search'])."%') and id not in (select id from art_prototype) and name not like '%прокат%'  ORDER by `id` ASC");
		}
	} else {
		$data = mysql_query("SELECT * FROM oldbk.`shop` WHERE ( (`razdel` = '{$_GET['otdel']}' ".$vitrina."  ) and id not in (select id from art_prototype) and name not like '%прокат%'  ORDER by `id` ASC");
	}

	while($row = mysql_fetch_array($data)) {
		if ($row['img_big']!='') { $row['img']=$row['img_big']; }
		$row['count']=9999;
		$row['avacount']=9999;
		if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5';}

		echo "<TR bgcolor={$color}><TD align=center style='width:150px'><a name=\"{$row['id']}\"><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
		?>
		<BR><A HREF="?otdel=<?=$_GET['otdel']?>&set=<?=$row['id']?>&sid=#<?=$row['id']?>">купить</A>
		<IMG SRC="http://i.oldbk.com/i/up.gif" WIDTH=11 HEIGHT=11 BORDER=0 ALT="Купить несколько штук" style="cursor: pointer" onclick="AddCount('<?=$row['id']?>', '<?=$row['name']?>','0')">

		<?

		?>

		</TD>
		<?php
		echo "<TD valign=top>";
		if ($row['repcost'] > 0) $row['getfrom'] = 43;
		$row['dategoden'] = 0;
		showitem ($row);
		echo "</TD></TR>";
	}
   }

?>
</TABLE>
</TD></TR>
</TABLE>

	</TD>
	<TD valign=top width=350>
	<div style="MARGIN-LEFT:15px; MARGIN-TOP: 10px;">
<div style="background-color:#d2d0d0;padding:1"><center><font color="#oooo"><B>Отделы магазина</B></center></div>

	<?
		$d = mysql_query("select razdel, count(id) as kol from shop where razdel>0 and id not in (select id from art_prototype) and name not like '%прокат%'  group by razdel");
		$arokol=array();
		while($row = mysql_fetch_array($d))
			{
			if ($arr_ot[$row['razdel']]=='')
				{
				$arr_ot[$row['razdel']]='Раздел '.$row['razdel'];
				}
			$arokol[$row['razdel']]=$row['kol'];
			}
	echo "<small>";
		 foreach ($arr_ot as $ot => $name)
		 {
		echo "<A HREF='?otdel=".$ot."'>".$name." (".$arokol[$ot]."шт ) </A><BR>";
		 }
	echo "</small>";
	?>

	</div>
<div id="hint3" class="ahint"></div>
    </TD>

</TR>
</TABLE>
<br><div align=left>
<?
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
    $miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    ob_clean();
    header('Content-Encoding: '.$miniBB_gzipper_encoding);
    echo $miniBB_gzipper_out;
    }
/////////////////////////////////////////////////////

?>
