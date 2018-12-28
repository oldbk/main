#!/usr/bin/php
<?php
ini_set('display_errors','On');
$CITY_NAME='capitalcity';
include "/www/".$CITY_NAME.".oldbk.com/cron/init.php";
require_once("/www/".$CITY_NAME.".oldbk.com/config_ko.php");
if( !lockCreate("cron_1hr_job") ) {
    exit("Script already running.");
}
echo date("d.m.y H:i:s").'\r\n';
$gotime=time(); //время запуска

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//чистим часовой счетчик в 0
mysql_query("UPDATE `variables` SET `value`=0 WHERE `var`='lab_key_count_h' ");									
/*
function get_link_to_enc($row)
{
$ehtml = str_replace('.gif','',$row['img']);

	$razdel=array(
		1=>"kasteti", 11=>"axe", 12=>"dubini", 13=>"swords", 14=>"bow", 2=>"boots", 21=>"naruchi", 22=>"robi", 23=>"armors",
		24=>"helmet", 3=>"shields",4=>"clips", 41=>"amulets", 42=>"rings", 5=>"mag1", 51=>"mag2", 6=>"amun", 61=>'eda' , 72 =>''
	);

	$row['otdel'] == '' ? $xx = $row['razdel'] : $xx = $row['otdel'];

	if ($row['type']==30)
		{
		$razdel[$xx]="runs/".$ehtml;
		}
	else
	if($razdel[$xx] == '') {
            	$dola = array(5001,5002,5003,5005,5010,5015,5020,5025);

		if (in_array($row['prototype'],$vau4)) {
			$razdel[$xx]='vaucher';
		} elseif (in_array($row['prototype'],$dola)) {
			$razdel[$xx]='earning';
		}
		else {

			$oskol=array(15551,15552,15553,15554,15555,15556,15557,15558,15561,15562,15568,15563,15564,15565,15566,15567);
			if (in_array($row['prototype'],$oskol))
			{
			$razdel[$xx]="amun/".$ehtml;
			}
			else
			{
			$razdel[$xx]='predmeti/'.$ehtml;
			}
		}
	} else {

		$razdel[$xx]=$razdel[$xx]."/".$ehtml;

	}
	
$link= "http://oldbk.com/encicl/".$razdel[$xx].".html";	
return $link;
}
*/

function add_telo_rep($telo,$add_rep,$txtdata)
{
	$cit[0]='oldbk.';
	$cit[1]='avalon.';
	$cit[2]='angels.';		
	 mysql_query("UPDATE ".$cit[$telo['id_city']]."`users` SET  `rep`=`rep`+'{$add_rep}' , `repmoney` = `repmoney` + '{$add_rep}' WHERE `id`= '".$telo['id']."' LIMIT 1;");
	 if (mysql_affected_rows() >0)
	 	{
	 		//new_delo - записываем в новый тип
  		    			$rec['owner']=$telo['id'];
					$rec['owner_login']=$telo['login'];
					$rec['owner_balans_do']=$telo['money'];
					$rec['owner_balans_posle']=$telo['money'];
					$rec['owner_rep_do']=$telo['repmoney'];
					$rec['owner_rep_posle']=($telo['repmoney']+$add_rep);
					$rec['target']=450;
					$rec['target_login']='KO';
					$rec['type']=254;//за выполнение квеста
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_rep']=$add_rep;					
					$rec['sum_kom']=0;
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
					$rec['bank_id']=0;
					$rec['add_info']='Бонус по акции:'.$txtdata.' день в онлайне.';
					add_to_new_delo($rec); //юзеру
					addchp ('<font color=red>Внимание!</font> Вы получили <b>'.$add_rep.' реп.</b> ','{[]}'.$telo['login'].'{[]}',$telo['room'],$telo['id_city']);
					return true;
					
	 	}
return false;	 
}


function  mk_put_item ($telo,$param,$txtdata,$ac=7)
{
if ($param[id]==100000) 
	{
	//екры на счет
	//$param['cost']
		$get_bank=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`bank` WHERE `owner` = '{$telo['id']}' order by def desc, id asc limit 1;"));
		if ($get_bank['id']>0)
			{
			//ищем банк первый счет
			//добавляем в банк
			mysql_query("UPDATE oldbk.`bank` set `ekr` = ekr+'{$param['cost']}' WHERE `id` = '{$get_bank['id']}' LIMIT 1;") ;
			 if (mysql_affected_rows()>0)		
			 	{
				//пишем историю
				$message='Акция! '.$txtdata.' в онлайне.';
				$get_bank['ekr_do']=$get_bank['ekr'];
				$get_bank['ekr']+=$param['cost'];
				mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','{$message} <b> {$param['cost']} екр.</b>,<i>(Итого: {$get_bank[cr]} кр., {$get_bank['ekr']} екр.)</i>','{$get_bank['id']}');");
				//пишем в дело
				  	$rec['owner']=$telo['id'];
					$rec['owner_login']=$telo['login'];
					$rec['owner_balans_do']=$telo['money'];
					$rec['owner_balans_posle']=$telo['money'];
					$rec['owner_rep_do']=$telo['repmoney'];
					$rec['owner_rep_posle']=$telo['repmoney'];
					$rec['target']=450;
					$rec['target_login']='KO';
					$rec['type']=357;
					$rec['sum_ekr']=$param['cost'];
					$rec['bank_id']=$get_bank['id'];
					$rec['add_info']='Бонус по акции:'.$txtdata.' в онлайне. Баланс до '.$bank['ekr_do']. 'екр. после: ' .$bank['ekr'];
					add_to_new_delo($rec); //юзеру
				//отправляем системку					
				addchp ('<font color=red>Внимание!</font> Вы получили <b>'.$param['cost'].' екр.</b> на счет №'.$get_bank['id'].' в банке!','{[]}'.$telo['login'].'{[]}',$telo['room'],$telo['id_city']);
				return true;
				}
				
			}
			else
			{
			echo "У {$telo['login']} нет банковского счета! ";
			}
	}
else
 {
	$param['labonly']=0;
        $dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`{$param[shop]}` WHERE `id` = '{$param[id]}' LIMIT 1;"));
	if ($dress[id]>0)
	{
							if ($param[goden]>0) { $dress[goden]=$param[goden]; }
							$dress[dategoden]=(($dress['goden'])?($dress['goden']*24*60*60+time()):"");
							if ($param[dategoden]>0) { $dress[dategoden]=$param[dategoden]; }	
							
							
							/*
							if (($param[id]==150155) OR ($param[id]==920925) OR ($param[id]==130135) OR ($param[id]==930935) )
							 { $dress['nlevel']=4;  } //  ставим 4 лвл
							 */
							
							$sown=0;
							
							if (mysql_query("INSERT INTO oldbk.`inventory`
							(`prototype`,`owner`,`sowner`,`name`,`type`,`massa`,`cost`,`ecost`,`img`,`img_big`,`maxdur`,`isrep`,
								`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
								`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`nsex`,`otdel`,`present`,`labonly`,`labflag`,`group`,`idcity`,`getfrom`,`rareitem`,`ekr_flag`
							)
							VALUES
							('{$dress['id']}','{$telo['id']}',".$sown.",'{$dress['name']}','{$dress['type']}',{$dress['massa']},{$param['cost']},'{$dress['ecost']}','{$dress['img']}','{$dress['img_big']}',{$param['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
							'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$param['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$dress[dategoden]."','{$dress['goden']}','{$dress['nsex']}','{$dress['razdel']}','{$param['present']}','{$param['labonly']}','0','{$dress['group']}','{$telo[id_city]}','{$ac}','{$dress['rareitem']}','{$dress['ekr_flag']}'
							) ;") )
						     {
						     		$dress['id']=mysql_insert_id();
						     		$dress['idcity']=$telo[id_city];
						     		//new delo
								$rec['owner']=$telo[id]; $rec['owner_login']=$telo[login];
								$rec['owner_balans_do']=$telo['money'];$rec['owner_balans_posle']=$telo['money'];					
								$rec['target']=0;$rec['target_login']='бой';
								$rec['type']=180;
								$rec['sum_kr']=0; $rec['sum_ekr']=0;
								$rec['sum_kom']=0; $rec['item_id']=get_item_fid($dress);
								$rec['item_name']=$dress['name'];
								$rec['item_count']=1;
								$rec['item_type']=$dress['type'];
								$rec['item_cost']=$dress['cost'];
								$rec['item_dur']=$dress['duration'];
								$rec['item_maxdur']=$param['maxdur'];
								$rec['item_ups']=0;
								$rec['item_unic']=0;
								$rec['item_incmagic']='';
								$rec['item_incmagic_count']='';
								$rec['item_arsenal']='';
								$rec['add_info']=$txtdata.' в онлайне.';
								add_to_new_delo($rec); 
								
								if ($ac==7)
								{
								//лето
								$mtext="Вы выполнили задание <a href=http://oldbk.com/encicl/?/act_line.html target=_blank>".$txtdata."</a> и получили «".link_for_item($dress)."»!";
								}
								else
								{
								//зима
								$mtext="За выполнение квеста <a href=http://oldbk.com/encicl/?/act_line_winter.html target=_blank>".$txtdata."</a> Вы получили «".link_for_item($dress)."»!";
								}
								
						   		addchp ('<font color=red>Внимание!</font> '.$mtext,'{[]}'.$telo[login].'{[]}',$telo['room'],$telo['id_city']);						   		
						   		return true;
						     }
	}
}	
	
return false;
}


if ((time()>$KO_start_time3) and (time()<$KO_fin_time3))
{
//акция включена -ЛЕТО

//чистка и перевод дней перенес в крон суточный

	$get_all_owners=mysql_query("select * from users_timer where  (cday>=0 and cday<6 and cbattle>=5 and ctime>=5 and getflag=0) OR (cday=6 and cbattle>=10 and ctime>=5 and getflag=0) ");
		//ставим флаг что они получили  приз  и ставим время када они его получили - следующий раз они должны перейти наследующие сутки через 24 часа
		mysql_query("update  users_timer set  getflag=1 , tday=NOW() where  (cday>=0 and cday<6 and cbattle>=5 and ctime>=5 and getflag=0)  OR (cday=6 and cbattle>=10 and ctime>=5 and getflag=0) ");
		echo "\n Выданно в текущем часу:".mysql_affected_rows();		
	
		while($orow=mysql_fetch_array($get_all_owners))		
		{
		$param=array();
		$t =array();
		$rez=false;
		$rez2=false;
		$own=check_users_city_data($orow[owner]);
		if ($own[id]>0)
		{
		//выдаем призы
			if ($orow[cday]==0)
			{
			$param['id']=105103; //Сытный завтрак
			$param[shop]='shop';
			$param['goden']=7;
			$param['cost']=1;
			$param['maxdur']=10;
			$param['present']='Удача';
			$param['magic']=8;
			$rez=mk_put_item ($own,$param,'«Летний дух стойкости: день первый»',7);
			 if ($rez) echo $own[login]."(D1), ";
			}
			elseif ($orow[cday]==1)
			{
			$param['id']=55557;//Средний свиток «Защита от магии»
			$param[shop]='shop';			
			$param['goden']=5;
			$param['cost']=0;
			$param['maxdur']=1;
			$param['present']='Удача';
			$param['magic']=557557;			
			$rez=mk_put_item ($own,$param,'«Летний дух стойкости: день второй»',7);
			 if ($rez) echo $own[login]."(D2), ";			
			}			
			elseif ($orow[cday]==2)
			{
			$param['id']=14005;// Средний свиток «Призыв»
			$param[shop]='shop';			
			$param['goden']=5;
			$param['cost']=0;
			$param['maxdur']=3;
			$param['present']='Удача';
			$param['magic']=4003;
			$rez=mk_put_item ($own,$param,'«Летний дух стойкости: день третий»',7);
			 if ($rez) echo $own[login]."(D3), ";			
			}
			elseif ($orow[cday]==3)
			{
			$param['id']=125125;//лечение травм
			$param[shop]='shop';
			$param['goden']=5;
			$param['cost']=1;
			$param['maxdur']=3;
			$param['present']='Удача';
			$param['magic']=48;
			$rez=mk_put_item ($own,$param,'«Летний дух стойкости: день четвертый»',7);
			 if ($rez) echo $own[login]."(D4), ";			
			}						
			elseif ($orow[cday]==4)
			{
			
			$t = get_mag_stih($own);
				if ($t[0]==1)
				{
				$param['id']=150158; // Средний свиток «Гнев Ареса»
				$param[shop]='shop';				
				$param['goden']=5;
				$param['cost']=0;
				$param['maxdur']=1;
				$param['present']='Удача';
				$param['magic']=158;	
				}
				else
				if ($t[0]==2)
				{
				$param['id']=920928; //Средний свиток «Обман Химеры»
				$param[shop]='shop';				
				$param['goden']=5;
				$param['cost']=0;
				$param['maxdur']=1;
				$param['present']='Удача';
				$param['magic']=928;	
				}
				else
				if ($t[0]==3)
				{
				$param['id']=130138; //Средний свиток «Вой Грифона»
				$param[shop]='shop';				
				$param['goden']=5;
				$param['cost']=0;
				$param['maxdur']=1;
				$param['present']='Удача';
				$param['magic']=138;	
				}
				else
				if ($t[0]==4)
				{
				$param['id']=930938; //Средний свиток «Укус Гидры»
				$param[shop]='shop';				
				$param['goden']=5;
				$param['cost']=0;
				$param['maxdur']=1;
				$param['present']='Удача';
				$param['magic']=938;	
				}
						
			$rez=mk_put_item ($own,$param,'«Летний дух стойкости: день пятый»',7);
			 if ($rez) echo $own[login]."(D5), ";			
			}
			elseif ($orow[cday]==5)
			{
			$param['id']=353;// заступ
			$param[shop]='eshop';			
			$param['goden']=5;
			$param['cost']=1;
			$param['maxdur']=1;
			$param['present']='Удача';
			$param['magic']=5353;
			$rez=mk_put_item ($own,$param,'«Летний дух стойкости: день шестой»',7);
			
			$param['id']=5000;//фамильный герб
			$param[shop]='eshop';				
			$param['goden']=0;
			$param['cost']=1;
			$param['maxdur']=1;
			$param['present']='';
			$param['magic']=0;			
			$rez2=mk_put_item ($own,$param,'«Летний дух стойкости: день шестой»',7);			
			
			 if (($rez) and ($rez2)) echo $own[login]."(D6), ";			
			}
			elseif ($orow[cday]==6)
			{
			$param['id']=100000;
			$param['cost']=5; // 5 екр на счет
			$rez=mk_put_item ($own,$param,'«Летний дух стойкости: день седьмой»',7);
			//еще раз
			$param['id']=4161;//+10% опыта
			$param[shop]='shop';
			$param['goden']=5;
			$param['cost']=1;
			$param['maxdur']=1;
			$param['present']='Удача';
			$param['magic']=161;			
			$rez2=mk_put_item ($own,$param,'«Летний дух стойкости: день седьмой»',7);
			 if (($rez)AND($rez2))  echo $own[login]."(D7), ";			
			/// еще добавляем репу
			add_telo_rep($own,1000,'Седьмой');			 
			
				if (mt_rand(1,100)<=25) //выполняем 7й день линеечки (25% получаем карты), 
						{
						drop_card($own);
						}
			
			}																								
		}
		}

}
elseif ((time()>$KO_start_time4) and (time()<$KO_fin_time4))
{
//акция включена - ЗИМА

//чистка и перевод дней перенес в крон суточный

	$get_all_owners=mysql_query("select * from users_timer where  (cday>=0 and cday<6 and cbattle>=5 and ctime>=5 and getflag=0) OR (cday=6 and cbattle>=10 and ctime>=5 and getflag=0) ");
		//ставим флаг что они получили  приз  и ставим время када они его получили - следующий раз они должны перейти наследующие сутки через 24 часа
		mysql_query("update  users_timer set  getflag=1 , tday=NOW() where  (cday>=0 and cday<6 and cbattle>=5 and ctime>=5 and getflag=0)  OR (cday=6 and cbattle>=10 and ctime>=5 and getflag=0) ");
		echo "\n Выданно в текущем часу:".mysql_affected_rows();		
	
		while($orow=mysql_fetch_array($get_all_owners))		
		{
		$param=array();
		$rez=false;
		$rez2=false;
		$own=check_users_city_data($orow[owner]);
		if ($own[id]>0)
		{
		//выдаем призы
			if ($orow[cday]==0)
			{
			$param['id']=106102; //Среднее зелье маны
			$param['shop']='shop';
			$param['goden']=5;
			$param['cost']=1;
			$param['maxdur']=3;
			$param['present']='Удача';
			$param['magic']=106102;
			$rez=mk_put_item ($own,$param,'«Зимний штурм: день первый»',8);
			 if ($rez) echo $own[login]."(D1), ";
			}
			elseif ($orow[cday]==1)
			{
			$stih=1;
			$stih=get_mag_stih($own); // получаем ид стихии
			$stih=$stih[0]; //на 0м месте родная стихия
			
			if ($stih==0) { $stih=1; }
			
			if ($stih==1)
				{
				$param['id']=150152;// Гнев Ареса III 
				$param['magic']=155;			
				}
			elseif ($stih==2)
				{
				$param['id']=920925;// Обман Химеры III
				$param['magic']=925;			
				}
			elseif ($stih==3)
				{
				$param['id']=130135;// Вой Грифона III
				$param['magic']=135;			
				}				
			elseif ($stih==4)
				{
				$param['id']=930935;// Укус Гидры III
				$param['magic']=935;			
				}				
			
			$param['shop']='shop';			
			$param['goden']=5;
			$param['cost']=1;
			$param['maxdur']=1;
			$param['present']='Удача';

			$rez=mk_put_item ($own,$param,'«Зимний штурм: день второй»',8);
			 if ($rez) echo $own[login]."(D2), ";			
			}			
			elseif ($orow[cday]==2)
			{
			$param['id']=14003;//  Малый свиток «Призыв»
			$param['shop']='shop';			
			$param['goden']=5;
			$param['cost']=1;
			$param['maxdur']=2;
			$param['present']='Удача';
			$param['magic']=4003;
			$rez=mk_put_item ($own,$param,'«Зимний штурм: день третий»',8);
			 if ($rez) echo $own[login]."(D3), ";			
			}
			elseif ($orow[cday]==3)
			{
			$param['id']=19104;//Таймаут Лабиринта Хаоса -20%
			$param['shop']='shop';				
			$param['goden']=5;
			$param['cost']=1;
			$param['maxdur']=5;
			$param['present']='Удача';
			$param['magic']=9104;			
			$rez=mk_put_item ($own,$param,'«Зимний штурм: день четвертый»',8);
			 if ($rez) echo $own[login]."(D4), ";	
			}						
			elseif ($orow[cday]==4)
			{
			$param['id']=353;// Заступиться
			$param['shop']='eshop';			
			$param['goden']=5;
			$param['cost']=1;
			$param['maxdur']=1;
			$param['present']='Удача';
			$param['magic']=5353;
			$rez=mk_put_item ($own,$param,'«Зимний штурм: день пятый»',8);
			 if ($rez) echo $own[login]."(D5), ";			
			}
			elseif ($orow[cday]==5)
			{
			$param['id']=5100;//Фамильный Герб 2
			$param['shop']='shop';				
			$param['goden']=5;
			$param['cost']=3;
			$param['maxdur']=1;
			$param['present']='Удача';
			$param['magic']=0;			
			$rez=mk_put_item ($own,$param,'«Зимний штурм: день шестой»',8);
			/// и еще
			$param['id']=4162;//+20% опыта
			$param['shop']='shop';
			$param['goden']=5;
			$param['cost']=1;
			$param['maxdur']=1;
			$param['present']='Удача';
			$param['magic']=162;			
			$rez2=mk_put_item ($own,$param,'«Зимний штурм: день шестой»',8);
			 if (($rez)AND($rez2))  echo $own[login]."(D6), ";			
			}
			elseif ($orow[cday]==6)
			{
			$param['id']=100000;
			$param['cost']=5; // 5 екр на счет						
			$rez=mk_put_item ($own,$param,'«Зимний штурм: день седьмой»',8);
			//еще раз
				// тут смотрим - счет недель и после спросмотра добавляем в таблицу +1
				$get_week_count=mysql_fetch_array(mysql_query("select * from oldbk.users_timer_week where owner='{$own['id']}' "));
				//рунный опыт 100-1000 свитки id>=571 and id<=580
	
				$ruk=(int)$get_week_count['cweek']+1;
	
					if ($ruk>10) 	{
								//дополнительный свиток рунного опыта	
								$param['id']=571;
								$param['shop']='shop';
								$param['goden']=5;
								$param['cost']=1;
								$param['maxdur']=1;
								$param['present']='Удача';
								$param['magic']=87;			
								$rez3=mk_put_item ($own,$param,'«Зимний штурм: день седьмой»',8);
								$ruk=10;
								}
				$ruk=570+$ruk;
				$param['id']=$ruk;// свитки опыта рун
				$param['shop']='shop';
				$param['goden']=5;
				$param['cost']=1;
				$param['maxdur']=1;
				$param['present']='Удача';
				$param['magic']=87;			
				$rez2=mk_put_item ($own,$param,'«Зимний штурм: день седьмой»',8);
	
	
			 if (($rez)AND($rez2))  echo $own[login]."(D7), ";						
			//увеличиваем счет недель для юзера
			mysql_query("INSERT oldbk.`users_timer_week` (`owner`,`cweek`) values('".$own['id']."', '1' ) ON DUPLICATE KEY UPDATE `cweek` =`cweek`+1;");
			/// еще добавляем репу
			add_telo_rep($own,1000,'«Зимний штурм: день седьмой»');
			}
		}
		}

}
else
{
	echo "\n Акция не активна!";		
}


lockDestroy("cron_1hr_job");
?>