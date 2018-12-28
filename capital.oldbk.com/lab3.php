<?

//компресия для инфы
///////////////////////////
/*
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
*/
//////////////////////////////

//ini_set('display_errors','On');
//v.7.5a +add set time out by clons+add Bs +add ClanWar +rep from battle+multicity
$maxkoldrop=2; // максимальное падение итемов с 1 боя
$rep_kof=2; // множитель репутации на 1 бой

		session_start();

		if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
		include "connect.php";
		include "functions.php";
		header("Cache-Control: no-cache");
		if ($user[klan]=='radminion') {  echo "Admin-info:<!- GZipper_Stats -> <br>"; }		
		if ($user['lab'] == 0) { header("Location: startlab.php"); die(); }
		if ($user['lab'] == 2) { header("Location: lab2.php"); die(); }
		if ($user['lab'] == 1) { header("Location: lab.php"); die(); }
		if ($user['battle'] > 0) {  header("Location: fbattle.php"); die();}

	   if ($_SERVER["SERVER_NAME"]=='capitalcity.oldbk.com') { $cityname='capitalcity' ;  }
	    else if ($_SERVER["SERVER_NAME"]=='avaloncity.oldbk.com') { $cityname='avaloncity' ;  }
	    	    else if ($_SERVER["SERVER_NAME"]=='angelscity.oldbk.com') { $cityname='angelscity' ;  }	    
		else {$cityname='noname' ; }

		include ("labconfig_s3.php"); // настройки

////////////load user poz///////////////////////////////////////////////////////////////////////////////////
$labpoz=mysql_fetch_array(mysql_query("SELECT * FROM `labirint_users` WHERE `owner` = '{$_SESSION['uid']}' LIMIT 1;"));
////////////////////////////////////////////load map file//////////////////////////////////////////////////
		$mapid=$labpoz[map];
		echo "Карта:".$mapid;
		echo "<br>";
		
		
		$mfile='/www/capitalcity.oldbk.com/labmaps/'.$mapid.'.map';
		if (file_exists($mfile)) 
			{
				$map=file($mfile);
			} else 
			{
			copy('/www/capitalcity.oldbk.com/labmaps/default_lab3.map', $mfile);
			$map=file($mfile);
			}
		
///////////////////////////////////////////////////////////////////////////
function do_auto($itm)
{
global $user;

if (getcheck_auto_get($user)==1)
	{
	$AUTO_GET_ITEM="<script> location.href='?getitem={$itm}'; </script>";
	}
	else
	{
	$AUTO_GET_ITEM="";
	}
echo $AUTO_GET_ITEM;
}
// Load quests is
     if ($_SESSION['quest']=='')
     				{
     				//echo "sql1- read<br>";
     				$qu=mysql_fetch_array(mysql_query("select * from users_quest where owner='{$user[id]}' and status=0 and city='{$cityname}' ;"));
     				}
 /*
if($user[id]==28453)
{
	print_r($_SESSION['beginer_quest']);
	$last_q=check_last_quest(9);
    echo '<br>';
    print_r($last_q);
}
   */
             if   (($qu[id] >0) OR ($_SESSION['quest']=='ihave'))
		      {
		  		      $_SESSION['quest']='ihave';
     			        if (($qu[id] >0)OR($_SESSION['questid']==''))
     		        	{
				$_SESSION['questid']=$qu[quest_id];
				$_SESSION['questdata']=mysql_fetch_array(mysql_query("select * from quests where id='{$_SESSION['questid']}' ;"));
    				//echo "sql2- read<br>";
 				 }

     		      //echo "ihave<br>";

       		        if ($_SESSION['questdata'][id] > 0)
       		        {
       		          //print_r($_SESSION['questdata']);
	                      //подключаем квест файл
		                include('./quests/quest'.$_SESSION['questdata'][id].'.php');
             	                   echo "<b>Ваше задание:".$_SESSION['questdata'][qname].":";
             	                   get_qcount($_SESSION['questdata'],$user,$mapid);
		                   echo "</b><br>";
					 $fmap='/www/capitalcity.oldbk.com/labmapsq/'.$mapid.'-'.$user[id].'.qst';

					 if (file_exists($fmap))
				       {
					   // echo "The file $fmap exists";
					    //файл есть загружаем из него данные - т.е.
					    $qdata_array = file($fmap);
							foreach($qdata_array as $qi=>$qstring)
						                        {
						                        $qmas=explode("::","$qstring");
						                        $Qmap[$qmas[0]][$qmas[1]]=(int)$qmas[2];
						                        if ($map[$qmas[0]][$qmas[1]]!='R') //накладываем если точка не монстр
						                          {
						                          $map[$qmas[0]][$qmas[1]]='Q'; // накладываем в карту лабы карту разбросаных итемов
						                          }
									}

					   } else
					   {
					    //echo "The file $fmap does not exist";
					    //файла сданными нет надо запустить квест
					    make_qstart($_SESSION['questdata'],$user,$mapid) ;

					   }
					}
		      }
		      else
		      {
		      $_SESSION['quest']='none';
      		      //echo "none<br>";
		      }
//$Qmap - масив с координатами - квестовый предметов

/////////////////////////////////////////////////////////////////////////
		//setup start point
		if ( ($labpoz[x] ==0 ) OR ($labpoz[y] ==0 ) )
			{
				$fotofile='i/laba/foto1.jpg';
			unset($_SESSION['charka_lab_count_ger']);
			//$msg = "<br>";
			//setup count user lab var
//убрано в fbattle     mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', 'labcount', '1' ) ON DUPLICATE KEY UPDATE `val` =`val`+1;");
		      //$deads=mysql_fetch_array(mysql_query("select `val` from `labirint_var` where `var`='labcount' and `owner`='".$user[id]."' ;"));


				if ($labpoz[dead] > 2)
				{
				$_POST['exit']=true;
				$msg = "<font color=red>Вы покидаете лабиринт...</font><br>";
				$_SESSION['quest']='';
				$_SESSION['questid']='';
				unset($_SESSION['quest']); //очистка
			     	unset($_SESSION['questdata']);//очистка
			     	unset($_SESSION['questid']);//очистка
				}
				else
				{
					foreach($map as $k=>$v ) if (strpos($v,"I")) $startx=$k;
					//echo $startx;
					mysql_query("UPDATE `labirint_users` SET `x`='".$startx."', `y`=1 WHERE `owner`='".$user[id]."' LIMIT 1;");
					//
					$labpoz[x]=$startx;
					$labpoz[y]=1;
				}
			}
			else
			{
				$dfot=round(($labpoz[y]*$MAXFOTO/(strlen($map[1])))); // получаем уровень сложности взависимости от положения перса к выходу.
				$rs=$dfot-1;

				if ($rs < 1)
				{
					$rs=1;
				}
				$rmt=rand($rs,$dfot);
				$fotofile='http://i.oldbk.com/i/laba/foto/'.$rmt.'.jpg';
			}


		////////////////////LOAD ITEMS POZ ///////////////////////////////////////////////////////////////////////
		$items=mysql_query("SELECT * FROM `labirint_items` WHERE  (`owner`=0 OR `owner`='{$user[id]}')  and  `active`=1 and  `map`='".$mapid."' ;");
		// надо грузить весь масив итемов ВСЕх...+
			$Aitems=array();
			while ($row = mysql_fetch_array($items))
			{
				$Aitems[$row[x]][$row[y]]=$row[item];
				$Aitems_val[$row[x]][$row[y]]=$row[val];
				$Aitems_count[$row[x]][$row[y]]=$row[count];
			}

//if ($user[id]==14897) { echo "<br>ДО".$Aitems[$labpoz[x]][$labpoz[y]]; }

if ($Aitems[$labpoz[x]][$labpoz[y]]!='R') // не ставим Q-поинт
{
   if ($Qmap[$labpoz[x]][$labpoz[y]]!='') // сверка позиции чара и карты итемов
     {
     // если кать квестовый итем на карте то надо проверить отработам или нет
      // echo "Q-item<br>";
         if ($Aitems[$labpoz[x]][$labpoz[y]]=='Q')
            {
            // if ($user[id]==14897) { echo "Есть в базе уже<br>"; }
            }
            else
            {
          	//  if ($user[id]==14897) { echo "Нет в базе надо проставить<br>"; }
	            make_qpoint($_SESSION['questdata'],$user,$mapid);
	            $Aitems[$labpoz[x]][$labpoz[y]]='Q';
	            $Aitems_val[$labpoz[x]][$labpoz[y]]=$Qmap[$labpoz[x]][$labpoz[y]];
	            $Aitems_count[$labpoz[x]][$labpoz[y]]=1;
            }
     }
}
//if ($user[id]==14897) {echo "<br>после".$Aitems[$labpoz[x]][$labpoz[y]];}

/////////

///////////////////LOAD MY TEAM ////////////////////////////////////////////////////////////////////////////
		$team=mysql_query("SELECT * from `labirint_users` where `map`='".$mapid."' and `owner`!='".$user[id]."' ;  ");

		$TA=array();
		$Tcount=0;
		while ($reamrow = mysql_fetch_array($team))
		{
			$Tcount++;
			$Displ_team.=" ".nick33($reamrow[owner])." X[".$reamrow[x]."]/Y[".$reamrow[y]."] <br>";
			$TA[$reamrow[x]][$reamrow[y]].=nick7($reamrow[owner])." ";
		}


/////////////////IPUT VAR USERS///////////////////////////////////////////////////////////////////////////
if (($_GET[talk]==73)and($map[$labpoz[x]][$labpoz[y]]=='F'))
	{
		// проверка на вшивость - скрипт дергаем на нужном месте на карте для рихтовки
		$OPEN_FROM_LAB=TRUE;
		$T_BOT=73;

		include('qbattle.php');
		die();
	}
elseif (($_POST[exit_good])and($map[$labpoz[x]][$labpoz[y]]=='F'))
		{
		// правильный выход
			$_SESSION['quest']='';
			$_SESSION['questid']='';
			unset($_SESSION['quest']); //очистка
	     	unset($_SESSION['questdata']);//очистка
	     	unset($_SESSION['questid']);//очистка

	     	//проскочит ли бот?

					if (mt_rand(1,3)==2)
					{
					$LAB=3;
					$RUN_FROM_LAB=1;
					include ("make_lbot.php"); // настройки
					}

			$msg="Выходим...и уносим добычу.";
			// считаем убитых монстров
             if(!$_SESSION['beginer_quest'][none])
		     {
		     	  $last_q=check_last_quest(9);
			      if($last_q)
			      {
			          quest_check_type_91($last_q);
			      }
		     }


			//$get_mobs=mysql_fetch_array(mysql_query("select count(*) as mobs from labirint_items where map={$mapid} AND  item='R' and val=0"));
			$get_mobs=mysql_fetch_array(mysql_query("select val  from labirint_var where owner='{$user[id]}' and var='labkillrep'; "));
			$add_rep=$rep_kof*(int)($get_mobs[0]);

			//perm
			$ar = 0;
			if ($user['prem']) $ar += 0.1;
			
						//дополнительный бонус
						$eff = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '9101' ;")); 	
						if ($eff['id']>0)
							{
							 $ar +=$eff['add_info'];
							}
			
			$ua = intval($user['align']);
			if ($ua == 1) $ua = 6;
			if (GetOpRs() == $ua) $ar += 0.05; 
	
			$add_rep = intval($add_rep * (1+$ar));

			$add_rep=(int)($add_rep);
			addch("<img src=i/magic/event_exit_yes.gif> {$user[login]} нашел выход из лабиринта, и получил <b>".$add_rep."</b> репутации!");
			addchp ('<font color=red>Внимание!</font> Вы получили <b>'.$add_rep.'</b> репутации за прохождение лабиринта!','{[]}'.$user['login'].'{[]}',45,$user['id_city']);
			//new delo
					$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user[money];
					$rec['owner_balans_posle']=$user[money];					
					$rec['owner_rep_do']=$user[repmoney];
					$rec['owner_rep_posle']=($user[repmoney]+$add_rep);					
					$rec['target']=0;
					$rec['target_login']='лаба3';
					$rec['type']=251;
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['sum_rep']=$add_rep;					
					$rec['item_id']='';
					$rec['item_name']="";
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
					add_to_new_delo($rec);
			

			mysql_query("DELETE FROM `labirint_users` WHERE `owner`='".$user[id]."';");
			mysql_query("DELETE FROM `labirint_var` WHERE owner='{$user[id]}' and var='labkillrep'; ");
			mysql_query("DELETE FROM `labirint_var` WHERE  (`var`='timer_trap'  or `var`='poison_trap' or `var`='labcount' ) and `owner`='".$user[id]."';");

			mysql_query("INSERT oldbk.`labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', 'labstarttime', '".time()."' ) ON DUPLICATE KEY UPDATE `val` ='".time()."';");

			mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', 'labgoodexit', '1' ) ON DUPLICATE KEY UPDATE `val` =`val`+1;");

		//проверяем не одето ли то что должно быть дропнуто
			$testdress=mysql_fetch_array(mysql_query("select `id` from oldbk.`inventory` where `dressed`=1 and `labonly`=1 and `owner`='".$user[id]."' LIMIT 1; "));
			if ($testdress[id] >0)
			{
			//надо раздеть
				undressall($user[id]);
			}



			mysql_query("DELETE FROM oldbk.`inventory` WHERE  (`labonly`=1 ) and `owner`='".$user[id]."';");
			mysql_query("UPDATE oldbk.`inventory` SET `labflag`=0  WHERE  `labflag`=1 and `owner`='".$user[id]."';");



				mysql_query("UPDATE `users` SET  `podarokAD`=0 ,`labzay`='0', `lab`=0 , `room`=45, battle=0, `rep`=`rep`+'".$add_rep."', `repmoney` = `repmoney` + '".$add_rep."'  WHERE `id`='".$user[id]."' ; ");
				//mysql_query("UPDATE `online` SET `room`='45' WHERE `id`='".$user[id]."' ; ");

				if ($Tcount==0)
				{
				echo "Я последний герой!<br>";
				//чистим итемы по карте
				mysql_query("DELETE FROM `labirint_items` where `map`='".$mapid."'; ");

				//удаляем файл карты
				unlink('/www/capitalcity.oldbk.com/labmaps/'.$mapid.'.map');
				}
//

			header("Location: startlab.php");
			die("<script>location.href='startlab.php';</script>");
		}
	else
		if (($_POST['exit']))
					{
					$_SESSION['quest']='';
					$_SESSION['questid']='';

					//проскочит ли бот?
	     				/*
					if (mt_rand(1,2)==2)
					{
					$LAB=3;
					$RUN_FROM_LAB=1;
					include ("make_lbot.php"); // настройки
					}
					*/
					$msg="Выходим...и теряем все найденное.";
		//системку в чат
		addch("<img src=i/magic/event_exit_no.gif> {$user[login]} бежит из лабиринта...");

		mysql_query("DELETE FROM `labirint_users` WHERE `owner`='".$user[id]."';");
		mysql_query("DELETE FROM `labirint_var` WHERE owner='{$user[id]}' and var='labkillrep'; ");
		mysql_query("DELETE FROM `labirint_var` WHERE  (`var`='timer_trap'  or `var`='poison_trap' or `var`='labcount'  ) and `owner`='".$user[id]."';");

		mysql_query("INSERT oldbk.`labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', 'labstarttime', '".time()."' ) ON DUPLICATE KEY UPDATE `val` ='".time()."';");

	mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', 'labexitfalse', '1' ) ON DUPLICATE KEY UPDATE `val` =`val`+1;");

		//надо раздеть
		undressall($user[id]);

			//логируем предметы 
			$get_all_items=mysql_query("select * from oldbk.`inventory` WHERE  (`labonly`=1 or `labflag`=1) and `owner`='".$user[id]."';");
			while ($rw = mysql_fetch_array($get_all_items))
				{
								//add to new delo
								$rec['owner']=$user['id']; 
								$rec['owner_login']=$user['login'];
								$rec['owner_balans_do']=$user['money'];
								$rec['owner_balans_posle']=$user['money'];					
								$rec['target']=0;
								$rec['target_login']='Лабиритн Хаоса';
								$rec['type']=707;
								$rec['item_id']=get_item_fid($rw);
								$rec['item_name']=$rw['name'];
								$rec['item_count']=1;
								$rec['item_type']=$rw['type'];
								$rec['item_cost']=$rw['cost'];
								$rec['item_ecost']=$rw['ecost'];
								$rec['item_dur']=$rw['duration'];
								$rec['item_maxdur']=$rw['maxdur'];
								$rec['battle']=$user['battle'];
								add_to_new_delo($rec); 				
				}
		//удаляем	
		mysql_query("DELETE FROM oldbk.`inventory` WHERE  (`labonly`=1 or `labflag`=1) and `owner`='".$user[id]."';");



				mysql_query("UPDATE `users` SET `podarokAD`=0, `labzay`='0', `lab`=0 , `room`=45, battle=0 WHERE `id`='".$user[id]."' ; ");
				//mysql_query("UPDATE `online` SET `room`='45' WHERE `id`='".$user[id]."' ; ");

if ($Tcount==0)
	{
	//// Тут надо добавить процедуру
	//// если я последний в лабиринте то надо убить все следы
	/// убить все итемы и можно грохнуть файл карты
	echo "Я последний герой!<br>";
	//чистим итемы по карте
	mysql_query("DELETE FROM `labirint_items` where `map`='".$mapid."'; ");
	}



			echo "
			<script>
				function cityg(){
					location.href='startlab.php';
				}
				setTimeout('cityg()', 1000);
			</script>
			";

		die("<script>location.href='startlab.php';</script>");
					}
else
if ($_GET[getitem])
				{

			           //поверку надо
			           //1. насуществование шмотки
			           $getitem=mysql_fetch_array(mysql_query("SELECT *,  (select `massa` from oldbk.eshop WHERE id=`labirint_items`.val) as massa  FROM `labirint_items` where `x`='".$labpoz[x]."' and `y`='".$labpoz[y]."' and `map`='".$mapid."' and `active`=0 and `item`='I' and `count` >=0 and (`owner`=0 OR `owner`='{$user[id]}') and `val`='".(int)($_GET[getitem])."' LIMIT 1;"));

			           if ($getitem[0]>0)
			           {
			           //есть такой итем
			           		//вычисляем быстро массу моих вещей
			           		$my_massa=0;    
						$q = mysql_query("SELECT IFNULL(sum(`massa`),0) as massa , setsale,  dressed  FROM oldbk.inventory WHERE `owner` = '{$user['id']}'   GROUP by setsale,dressed ");
						while ($row = mysql_fetch_array($q)) 
						{
						if (($row['setsale'] ==0 )  AND  ($row['dressed'] ==0 )  )
							{
							$my_massa+=$row['massa'];
							}
						}

		           		if (($getitem['massa']+$my_massa) > (get_meshok())) {	$msg.="<font color=red><b>Недостаточно места в рюкзаке.</b></font>";}
		           		else
		           		{
		           		//all is good
		           		//del item from map
		           		//ТУТ ЗАМЕНИТЬ НА АПДЕЙТ
		          ///old 		if (mysql_query("DELETE FROM `labirint_items` where `x`='".$labpoz[x]."' and `y`='".$labpoz[y]."' and `map`='".$mapid."' and `active`=0 and `item`='I' and `val`='".$getitem[val]."' LIMIT 1;"))
		          			if (mysql_query("UPDATE `labirint_items` SET `active`=1 where `x`='".$labpoz[x]."' and `y`='".$labpoz[y]."' and `map`='".$mapid."' and `active`=0 and `item`='I' and `val`='".$getitem[val]."' LIMIT 1;"))
		           		{
		           		//add item to ivent
				         $dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`eshop` WHERE `id` = '{$getitem[val]}' LIMIT 1;"));

			            if ($dress['id'] > 1010000) {
		 	         			$checkok=get_qitem_check($_SESSION['questdata'],$user,$mapid);
          				                         }else{ $checkok="TRUE"; }

				   if ($checkok=="TRUE")
				    {
       				         $msg='<font color=red>Вы подняли "'.$dress[name].'" </font>';
       				         if (   (($dress['id']>3000) and ($dress['id']<3013)) OR // ресы
	       				        (($dress['id']>103000) and ($dress['id']<103030)) OR // ресы авалона       				         
       				         	(($dress['id']>2966) and ($dress['id']<2975)) OR  // свитки подгона
       				         	(($dress['id']>15560) and ($dress['id']<15570))       	 ) // осколки
       				         {
       				         $needpres='Лабиринт';
       				         }
       				         else
       				         {
       				         $needpres='';
       				         }
       				         //////////
       				         // подбор квестовых предметов
	         		         if ($dress['id'] > 1010000)
        	 		         	{
				         	$needpres='Лабиринт';
        	 		         	$soown=$user[id];
        	 		         	get_qitem($_SESSION['questdata'],$user,$mapid) ;

        	 		         	}
        	 		         	else
        	 		         	{
        	 		         	$soown='';
        	 		         	}

       				         ///////fix new 22 12 2010 бутыры на подборе
       				         if (($dress['id']==105) or ($dress['id']==105101) )
       				          {
       				          $dress['cost']=1;
       				          $needpres='Лабиринт';
       				          $dress['ekr_flag']=0;       				          
       				          }
       				          //fix для чеков
       				         $labonly=0;
       				         if ( ($dress['id']>3100)and($dress['id']<3210))
       				         	{
       				         	$labonly=1;
       				         	}

       				         if ( ($dress['id']>=56661)and($dress['id']<=56663))
       				        {
       				        $needpres='Лабиринт';
       	 		         	$soown=$user[id];
       				        }       	
       				        
			        if ($labonly>0)
       				         {
					 $dress['nclass']=4;
					 }			         

mysql_query("INSERT INTO oldbk.`inventory`
				(`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`img_big`,`getfrom`,`rareitem`,`ekr_flag` ,`duration`,`maxdur`,`isrep`,`nclass`,
					`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
					`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`nsex`,`otdel`,`labonly`,`labflag`,`present`,`group`,`letter`,`sowner`,`idcity`
				)
				VALUES
				('{$dress['id']}','{$user['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}', '{$dress['img_big']}' , '13','{$dress['rareitem']}','{$dress['ekr_flag']}'  ,{$dress['duration']},{$dress['maxdur']},{$dress['isrep']},'{$dress['nclass']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
				'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}','{$dress['nsex']}','{$dress['razdel']}','{$labonly}','1','{$needpres}','{$dress['group']}','{$dress['letter']}','{$soown}','{$user[id_city]}'
				) ;");


						if ((mysql_affected_rows()>0) and ($dress['labonly']==0) )
							{
						     		//add to new delo
								$rec['owner']=$user['id']; 
								$rec['owner_login']=$user['login'];
								$rec['owner_balans_do']=$user['money'];
								$rec['owner_balans_posle']=$user['money'];					
								$rec['target']=0;
								$rec['target_login']='Лабиритн Хаоса';
								$rec['type']=700;
								$rec['item_id']="cap".mysql_insert_id();
								$rec['item_name']=$dress['name'];
								$rec['item_count']=1;
								$rec['item_type']=$dress['type'];
								$rec['item_cost']=$dress['cost'];
								$rec['item_ecost']=$dress['ecost'];
								$rec['item_dur']=$dress['duration'];
								$rec['item_maxdur']=$dress['maxdur'];
								$rec['battle']=$telo['battle'];
								add_to_new_delo($rec); 
							}

		           		}
		           		else
		           		   {
		           		    $msg='<font color=red>"'.$checkok.'" </font>';
		           		   }
		           		}


		           		}

			           }
			           else
			           {
				   $msg="Кто не успел, тот опоздал...";
			           }



				}
else
 if ($_GET[openbox]) {

 			if (($map[$labpoz[x]][$labpoz[y]]==$Aitems[$labpoz[x]][$labpoz[y]]) and ($map[$labpoz[x]][$labpoz[y]]=='P'))
			{
			$msg="уже кто-то открыл...";
			}
			else
			if ($map[$labpoz[x]][$labpoz[y]]=='P')
			{
			$Aitems[$labpoz[x]][$labpoz[y]]='P';



			$dpbox=count($pbox); // грузим линейку
			$dpbox=round(($labpoz[y]*$dpbox/(strlen($map[1])))); // получаем уровень сложности взависимости от положения перса к выходу.
				$rs=$dpbox-2;
				if ($rs < 0) {  $rs=0; }
				$rmt=rand($rs,$dpbox);
				if ($rmt>=13)
					{
					//если больше половины карты проверяем ивент
					$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=1"));
					if ($get_ivent['stat']==1)
							{
							//иветн работает
							$get_day_key_drop=mysql_fetch_array(mysql_query("select * from `variables` where var='lab_key_count_d' "));
							if ($get_h_key_drop['value']<=480)	//в день
							  {
								$get_h_key_drop=mysql_fetch_array(mysql_query("select * from `variables` where var='lab_key_count_h' "));
								if ($get_h_key_drop['value']<20) //в час
								{
								//проверяем кол.выпавших
								$get_kycount=mysql_fetch_array(mysql_query("select * from labirint_var where owner='{$user['id']}' and var='keylab'"));
								$tk=(int)($get_kycount['val']);
								if ($tk==0)
									{
									//добавка ключа
									$pbox[$rmt] = array("labkey" => 4001);
									mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user['id']."','keylab','1') ON DUPLICATE KEY UPDATE `val`=`val`+1;");
									mysql_query("UPDATE `variables` SET `value`=`value`+1 WHERE `var`='lab_key_count_h' or `var`='lab_key_count_d'");									
									}
								}
							 }
							}
					}
					
					//выпадение чарок = героик
					if ($rmt>=13)
					{
					//если больше половины карты проверяем ивент
							if (!(isset($_SESSION['charka_lab_count_ger'])))
								{
								$_SESSION['charka_lab_count_ger']=mysql_fetch_array(mysql_query("select * from labirint_var where owner='{$user['id']}' and var='charka_lab_count_ger'"));
								}
						
						if ($_SESSION['charka_lab_count_ger']>=5)
							{
									//добавка чарки
									$pbox[$rmt] = array("charka1" => 56661);
									$_SESSION['charka_lab_count_ger']=1;
									mysql_query("UPDATE `oldbk`.`labirint_var` SET `val`=1 WHERE  owner='{$user['id']}'  AND `var`='charka_lab_count_ger' ");
							}
					
					}
					
					
				foreach ($pbox[$rmt] as $item_name=>$v)
					{

		if (($item_name!='poison_trap') and ($item_name!='timer_trap'))
					{
					//выпали итемы
					mysql_query("INSERT IGNORE `labirint_items` (`map`,`item`,`x`,`y`,`active`,`count`) values('".$mapid."','P','".$labpoz[x]."','".$labpoz[y]."','1','1' ) ;");
					if(mysql_affected_rows()>0)					
						{
						$Aitems_count[$labpoz[x]][$labpoz[y]]++; // считаем только итемы
						mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`,`val`) values('".$mapid."','I','".$labpoz[x]."','".$labpoz[y]."','0','".$v."' ) ON DUPLICATE KEY UPDATE `active` =0, `val`='".$v."' ;");
						}
					}
					else
					{
					//выпала ловушка выполняем ее
				//var global B count
				$trap_name=$item_name;
				mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', '".$trap_name."', '".(time()+($v*60))."' ) ON DUPLICATE KEY UPDATE `val` =`val`+".($v*60).";");


				//системку в чат
				if ($user[sex]==1) { $sexi='угодил'; } else { $sexi='угодила'; }
				if ($trap_name=='timer_trap') { $_SESSION['time_trap']=1; $kom_trap='<i>Время перехода +3 секунды (Длительность:+'.$v.'мин.)</i> '; }
				else if ($trap_name=='poison_trap') {
									// add poison flag
									mysql_query("UPDATE users set `podarokAD`=1 where id='{$user[id]}';");
									$kom_trap='<i>Уровень жизни -2HP в минуту (Длительность:+'.$v.'мин.)</i>';
									}
				else {$kom_trap='';}

				addch("<img src=i/magic/event_".$trap_name.".gif> {$user[login]} ".$sexi." в ловушку...".$kom_trap);
				$msg.="<img src=i/magic/event_".$trap_name.".gif> {$user[login]} ".$sexi." в ловушку...".$kom_trap."<br>";

					}
//var global B count
//				mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', '".$trap_name."', '".(time()+($v*60))."' ) ON DUPLICATE KEY UPDATE `val` =`val`+".($v*60).";");
					} //цикл простотра ящика
					if ($Aitems_count[$labpoz[x]][$labpoz[y]]==0)
					{
					$kom_box='<i>а там ничего нет.</i>';
					mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active` ) values('".$mapid."','P','".$labpoz[x]."','".$labpoz[y]."','1' ) ON DUPLICATE KEY UPDATE `active` =1 ;");
					}

//системку в чат

				if ($user[sex]==1) { $sexi='открыл'; } else { $sexi='открыла'; }

				addch("<img src=i/magic/event_pandbox.gif> {$user[login]} ".$sexi." Ящик Пандоры...".$kom_box);
				$msg.= "<img src=i/magic/event_pandbox.gif> {$user[login]} ".$sexi." Ящик Пандоры...".$kom_box."<br>";


			}
else
			{
			$msg='На этом месте есть только дырка...';
			}

 }
 else
 if ($_GET[hill])
		{

// проверить реально ли  она тут.
/// хилка
/// добавляем в запись итемы лабиринта о срабоботке
			if (($map[$labpoz[x]][$labpoz[y]]==$Aitems[$labpoz[x]][$labpoz[y]]) and ($map[$labpoz[x]][$labpoz[y]]=='H'))
			{
			$msg="уже кто-то выпил...";
			}
			else
			if ($map[$labpoz[x]][$labpoz[y]]=='H')
			{

				$dhils=count($hils); // грузим линейку
				$dhils=round(($labpoz[y]*$dhils/(strlen($map[1])))); // получаем уровень сложности взависимости от положения перса к выходу.
				$rs=$dhils-2;
				if ($rs < 0) {  $rs=0; }
				$rmt=rand($rs,$dhils);
				foreach ($hils[$rmt] as $H=>$v)
				{
		if ($H=='H')
			{
			$addhp=((int)(($user[maxhp]-$user[hp]+1)/100*$v));
			$newhp=$user[hp]+$addhp;
			mysql_query("UPDATE `users` SET `hp`='".$newhp."' where id='".$user[id]."' LIMIT 1;   ");

				if ($user[sex]==1) { $sexi='пополнил'; } else { $sexi='пополнила'; }
				//системку в чат
				addch("<img src=i/magic/event_heal.gif> {$user[login]} ".$sexi." здоровье <i> Уровень Жизни +".$v."% </i>");
			$msg="<img src=i/magic/event_heal.gif alt='Живая вода' title='Живая вода'> {$user[login]} ".$sexi." здоровье <i> Уровень Жизни +".$v."% (+".$addhp."HP)</i>";
			mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`) values('".$mapid."','H','".$labpoz[x]."','".$labpoz[y]."','1' ) ON DUPLICATE KEY UPDATE `active` =1;");
			$Aitems[$labpoz[x]][$labpoz[y]]="H";
			mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', 'hcount', '1' ) ON DUPLICATE KEY UPDATE `val` =`val`+1;");
			$user[hp]=$newhp;
			}

				}

			//
			}
			else
			{
			$msg='На этом месте есть только дырка...';
			}
		 }
		else
if ($_GET[attak])
			{

			$msg='Показалось...';

			}
	else
if ($_GET[sunduk])
			{
			if (($map[$labpoz[x]][$labpoz[y]]==$Aitems[$labpoz[x]][$labpoz[y]]) and ($map[$labpoz[x]][$labpoz[y]]=='S'))
			{
			$msg="Сундук пуст...уже кто-то открыл..";
			}
			else if ($map[$labpoz[x]][$labpoz[y]]=='S')
			{

			//окрыть сундук
			// drop item from sundul ^)
				$dsund=count($sund); // грузим линейку
				$dsund=round(($labpoz[y]*$dsund/(strlen($map[1])))); // получаем уровень сложности взависимости от положения перса к выходу.
				$delta=round(($labpoz[y]*10/(strlen($map[1])))); //5%
				$rs=$dsund-$delta; //-5%
				$rf=$dsund+$delta; //+5%

				if ($rs < 0) {  $rs=0; }
				if ($rf > (strlen($map[1]))) {  $rf=(strlen($map[1])); }
				/// тут проверяем повторы итемов
				$kkk=0;
				//echo "$rs - $rf <br>";
				do
				{
				//echo "$kkk <br>";
				 	$rmt=rand($rs,$rf);
					$param=$sund[$rmt];
					//if ($user[id]==188) { $param=$sund[19];}
					$povtorov=$reitem[$param[id]]; // загрузка счетчика повтора из конфиги
				//echo "$param[id] <br>";
				 if ($povtorov >0 ) // если есть настройка повтора то проеряем
				  {
					$history=mysql_fetch_array(mysql_query("select count(*) FROM `labirint_items` where `item`='I' and `map`='{$mapid}' and `active`=1 and `val`='{$param[id]}' ;"));
					if ( ($history[0]<$povtorov) )
					  {
					  $kkk=10;
						  }
				  }
				  else
				  {
				  $kkk=10;
				  }
				}
				while ($kkk++<5) ;
			// если уж и повыходу из цикла хистори больше счетчика повторов то говорим что ящик пуст
			// не пуст а даем гранит
			if (( ($history[0]>$povtorov) and ($povtorov > 0)) or ($param[id]==0))
			{
			 if (CITY_ID==1)
			    {
			    $param[id]=103001;
			    }
			    else
			    {
			    $param[id]=3001;
			    }
			$param[shop]="eshop";
			$param[labonly]=0;
			$param[maxdur]=1;
			$param[present]="Лабиринт";
			}



			if ($param[id] > 0)
			{
		mysql_query("INSERT  IGNORE `labirint_items` (`map`,`item`,`x`,`y`,`active`) values('".$mapid."','S','".$labpoz[x]."','".$labpoz[y]."','1' ) ;");
		if(mysql_affected_rows()>0)
				{
			      $dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`{$param[shop]}` WHERE `id` = '{$param[id]}' LIMIT 1;"));
				//в таблицу итемов выпавший итем но с актив =1 нужно для определения повторения
				if ($param['maxdur'] > 0)
				{ $param_maxdur=$param['maxdur']; } else { $param_maxdur=$dress['maxdur']; }
				if ($param['sowner']==true)
				{ $param_sowner=$user[id]; } else { $param_sowner=0; }
				
				if ($param[labonly]>0)
       				         {
					 $dress['nclass']=4;
					 }				

			mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`,`val`) values('".$mapid."','I','".$labpoz[x]."','".$labpoz[y]."','1','".$dress['id']."' ) ON DUPLICATE KEY UPDATE `active` =1, `val`='".$dress['id']."' ;");
			mysql_query("INSERT INTO oldbk.`inventory`
				(`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`img_big`,`getfrom`,`rareitem`,`ekr_flag` ,`duration`,`maxdur`,`isrep`,`nclass`,
					`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
					`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`nsex`,`otdel`,`present`,`labonly`,`labflag`,`group`,`letter`,`sowner`,`idcity`
				)
				VALUES
				('{$dress['id']}','{$user['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}' , '{$dress['img_big']}' , '13','{$dress['rareitem']}','{$dress['ekr_flag']}'  ,{$dress['duration']},'{$param_maxdur}','{$dress['isrep']}','{$dress['nclass']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
				'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."','{$dress['goden']}','{$dress['nsex']}','{$dress['razdel']}','{$param['present']}','{$param['labonly']}','1','{$dress['group']}','{$dress['letter']}','{$param_sowner}','{$user[id_city]}'
				) ;");
			if ($user[id]==188) { echo mysql_error(); }

			if ($user[sex]==1) { $sexi='забрал'; } else { $sexi='забрала'; }
			$msund='<i>и '.$sexi.' "'.$dress[name].'" </i>';
			
						if ((mysql_affected_rows()>0) and ($dress['labonly']==0) )
							{
						     		//add to new delo
								$rec['owner']=$user['id']; 
								$rec['owner_login']=$user['login'];
								$rec['owner_balans_do']=$user['money'];
								$rec['owner_balans_posle']=$user['money'];					
								$rec['target']=0;
								$rec['target_login']='Лабиритн Хаоса';
								$rec['type']=701;
								$rec['item_id']="cap".mysql_insert_id();
								$rec['item_name']=$dress['name'];
								$rec['item_count']=1;
								$rec['item_type']=$dress['type'];
								$rec['item_cost']=$dress['cost'];
								$rec['item_ecost']=$dress['ecost'];
								$rec['item_dur']=$dress['duration'];
								$rec['item_maxdur']=$dress['maxdur'];
								$rec['battle']=$telo['battle'];
								add_to_new_delo($rec); 
							}
			
			  	}
				else
				{
				$msund='<i>а он пуст.</i>';
				}
			}
			else
			{
			$msund='<i>а он пуст.</i>';
			}
///

			if ($user[sex]==1) { $sexi='открыл'; } else { $sexi='открыла'; }
			$msg="<img src=i/magic/event_sunduk.gif> {$user[login]} ".$sexi." сундук...".$msund;
			addch("<img src=i/magic/event_sunduk.gif> {$user[login]} ".$sexi." сундук...".$msund);


			//setup count user lab var
			mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', 'scount', '1' ) ON DUPLICATE KEY UPDATE `val` =`val`+1;");
			//
			$Aitems[$labpoz[x]][$labpoz[y]]="S";
			}
			else
			{
			$msg='Показалось что тут сундук...';
			}
		 }
	else
		if($_GET[go])
		{
		$msg="<font color=red>Бот ?</font>";
		//telepost('Bred','<font color=red>Внимание! laba-BOT </font> Персонаж:'.$user[login]);
		}
		if ($_GET['goto']) {

		if ($_SESSION['time_trap']==1) {$time_go=6;} else {$time_go=3;}

			if(time()-$_SESSION['time'] <= $time_go) {
				$msg="<font color=red>Не так быстро</font>";
			}
			else
			switch($_GET['goto']) {
				case "p1":
					//$msg="P1<br>";
				if ((($map[$labpoz[x]-1][$labpoz[y]]=="M") OR ($map[$labpoz[x]-1][$labpoz[y]]==" ")) and ($_SESSION['sumrak']==0 ))
					{
					$msg= "Стена..";
					}
					else
					{
					if (($_SESSION['sumrak']>0) and (($labpoz[x]==2) OR ($labpoz[y]==1) OR ($labpoz[y]==strlen($map[1])-2) ))
					  {
	 				$msg= "Стена очень уж большая..";
					  }
					  else
					  {
					  if ($_SESSION['sumrak']>0) {$_SESSION['sumrak']=$_SESSION['sumrak']-1;}
					  mysql_query("UPDATE `labirint_users` SET  `x`=`x`-1 WHERE `owner`='".$user[id]."' LIMIT 1;");
					  $labpoz[x]=$labpoz[x]-1;
					  }
					}
					$_SESSION['time'] = time();
				break;
				case "p2":
					//$msg="P2<br>";
				if (ord($map[$labpoz[x]][$labpoz[y]+1])==10)
				{
					$msg= "Поздравляем! Вы нашли выход...!";

				}
				else
				if ((($map[$labpoz[x]][$labpoz[y]+1]=="M") OR ($map[$labpoz[x]][$labpoz[y]+1]==" ") ) and ($_SESSION['sumrak']==0 ))
					{
					$msg= "Стена...";
					}
					else
					{
					if (($_SESSION['sumrak']>0) and ($labpoz[y]==(strlen($map[1])-3))  )
					  {
	 				$msg= "Стена очень уж большая..";
					  }
					  else
					  {
					  if ($_SESSION['sumrak']>0) {$_SESSION['sumrak']=$_SESSION['sumrak']-1;}
					//шаг
					  mysql_query("UPDATE `labirint_users` SET  `y`=`y`+1 WHERE `owner`='".$user[id]."' LIMIT 1;");
					  $labpoz[y]=$labpoz[y]+1;
					  }
					}
					$_SESSION['time'] = time();
				break;
				case "p3":
					//$msg="P3<br>";
				if ((($map[$labpoz[x]+1][$labpoz[y]]=="M") OR ($map[$labpoz[x]+1][$labpoz[y]]==" ") ) and ($_SESSION['sumrak']==0 ))
					{
					$msg= "Стена...";
					}
					else
					{
					if (($_SESSION['sumrak']>0) and (($labpoz[x]==(count($map)-2)) OR ($labpoz[y]==1) OR ($labpoz[y]==strlen($map[1])-2) ) )
					  {
	 				$msg= "Стена очень уж большая..";
					  }
					  else
					  {
					  if ($_SESSION['sumrak']>0) {$_SESSION['sumrak']=$_SESSION['sumrak']-1;}
					  mysql_query("UPDATE `labirint_users` SET  `x`=`x`+1 WHERE `owner`='".$user[id]."' LIMIT 1;");
					  $labpoz[x]=$labpoz[x]+1;
					  }
					}
					$_SESSION['time'] = time();
				break;
				case "p4":
					//$msg="P4<br>";
if (ord($map[$labpoz[x]][$labpoz[y]-1])==32)
				{
				 $msg= "Пути назад нет!";
				}
			else
				if ((($map[$labpoz[x]][$labpoz[y]-1]=="M") OR ($map[$labpoz[x]][$labpoz[y]-1]==" ")) and ($_SESSION['sumrak']==0 ))
					{
					$msg= "Стена...";
					}
					else
					{
					if (($_SESSION['sumrak']>0) and ($labpoz[y]==2))
					  {
	 				$msg= "Стена очень уж большая..";
					  }
					  else
					  {
					  if ($_SESSION['sumrak']>0) {$_SESSION['sumrak']=$_SESSION['sumrak']-1;}
					  mysql_query("UPDATE `labirint_users` SET  `y`=`y`-1 WHERE `owner`='".$user[id]."' LIMIT 1;");
					  $labpoz[y]=$labpoz[y]-1;
					  }
					}
					$_SESSION['time'] = time();
				break;
			}
			//header("Location: lab.php"); //FREFRESH!!
			// тут произходит переход если он был!!
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			//die("<script>location.href='lab.php';</script>");
			//if ($user[id]==14897) { echo "<br>ДО".$Aitems[$labpoz[x]][$labpoz[y]]; }
			//echo $map[$labpoz[x]][$labpoz[y]];

if ($map[$labpoz[x]][$labpoz[y]]!='R') // не ставим Q-поинт - не ставим тут поинт если моб на клетке!!!
{
   if ($Qmap[$labpoz[x]][$labpoz[y]]!='') // сверка позиции чара и карты итемов
     {
     // если кать квестовый итем на карте то надо проверить отработам или нет
      // echo "Q-item<br>";
         if ($Aitems[$labpoz[x]][$labpoz[y]]=='Q')
            {
            // if ($user[id]==14897) { echo "Есть в базе уже<br>"; }
            }
            else
            {
          //  if ($user[id]==14897) { echo "Нет в базе надо проставить<br>"; }
            make_qpoint($_SESSION['questdata'],$user,$mapid);
            $Aitems[$labpoz[x]][$labpoz[y]]='Q';
            $Aitems_val[$labpoz[x]][$labpoz[y]]=$Qmap[$labpoz[x]][$labpoz[y]];
            $Aitems_count[$labpoz[x]][$labpoz[y]]=1;
            }
     }
}
//if ($user[id]==14897) {echo "<br>после".$Aitems[$labpoz[x]][$labpoz[y]];}

/////////


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
////тут подставляем квестовые итемы - мешаем с лабой т.к. тут обновленные данные если я сделал шаГ
/////////////////////////////////////////////////////////////////////////////////////////////////////////



		if((time()-$_SESSION['time']) > 3) {
			$tt = 3;
		}
		else {
			$tt =(time()-$_SESSION['time']);
		}
?>
<html>
<head>
	<link rel=stylesheet type="text/css" href="i/main.css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<script type="text/javascript" src="/i/globaljs.js"></script>
	<style>
		body {
			background-repeat: no-repeat;
			background-position: top right;
		}
		.INPUT {
			width:50px; height:50px;
			BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
		}
	</style>
	<script>

			function refreshPeriodic()
			{
				location.href='lab3.php?';//reload();
								timerID=setTimeout("refreshPeriodic()",30000);
			}
			timerID=setTimeout("refreshPeriodic()",30000);

	</script>


</head>
<body leftmargin=5 topmargin=0 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 onload="top.setHP(<?=$user['hp']?>,<?=$user['maxhp']?>)">
<?

///Paint map prep
////////////////////////////////////////////////////////////////////////////////
//set user poz to mass
$room_map=$map[$labpoz['x']][$labpoz['y']];
$map[$labpoz['x']][$labpoz['y']]='U';


//////////////////////////////////////////////////////////////////////////

///Организация видимого квадрата карты////////////////
$disp=$labpoz[x]-5;
$dispy=$labpoz[y]-5;
if ($disp < 1) {$disp=1;}
if ($dispy < 1) {$dispy=1;}
if ($disp < 1) {$disp=1;}
if ($dispy < 1) {$dispy=1;}
if ($dispy > strlen($map[1])-12)  {$dispy=strlen($map[1])-12;} //ограничитель экрана карты по y
if ($disp > count($map)-11) 	{ $disp=count($map)-11;  } //ограничитель экрана карты по х
$dispfin=$disp+10; //квадрат видимый х фин
$dispfiny=$dispy+10; //квадрат видимый y фин
/////////////////////////////////////////////

 for ($i=$disp;$i<=$dispfin;$i++)
	{
 	$linte=""; // for visualimage
	for ($j=$dispy;$j<=$dispfiny;$j++)
		{




switch($map[$i][$j])
			{

case "I":
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://i.oldbk.com/llabb/os.gif title="Вход" alt="Вход">';
			}
			else
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">';
			}
		 } // вход
break;


case "F":
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://i.oldbk.com/llabb/of.gif title="Выход" alt="Выход">';
			}
			else
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">';
			}
		 } // вЫход
break;

case "O":
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://i.oldbk.com/llabb/o.gif>';
			}
			else
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">';
			}
		 } // проход
break;

case "Q":
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://i.oldbk.com/llabb/o.gif>';
			}
			else
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">';
			}
		 } // проход 2 для квеста
break;

case 'M' :
		{
		$linte.='<img src=http://i.oldbk.com/llabb/m.gif>';
		} // стенка
break;

case 'R' :
		{
		 if ($TA[$i][$j]=='')
			{
			if ($Aitems[$i][$j]!='R')
					{
					  $linte.='<img src=http://i.oldbk.com/llabb/r.gif title="Опасная зона" alt="Опасная зона">'; // активный монстр
					}
					else
					{
					  $linte.='<img src=http://i.oldbk.com/llabb/o.gif>'; // труп монстра
					}

			}
			else
			{
				if ($Aitems[$i][$j]!='R')
					{
					$linte.='<img src=http://i.oldbk.com/llabb/rt.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый тип у монстра
					}
					else
					{
					 if ($Aitems_val[$i][$j]>0)
						{
						$linte.='<img src=http://i.oldbk.com/llabb/rt.gif title="'.$TA[$i][$j].' - в бою" alt="'.$TA[$i][$j].' - в бою">'; // левый тип у монстра-в бою
						}
						else
						{
						$linte.='<img src=http://i.oldbk.com/llabb/ot.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый тип у монстра-трупа
						}


					}
			}
		 } // mob
break;


case 'B' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='B')
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/b.gif alt="Ловушка" title="Ловушка" >'; // активная ловушка
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o.gif>'; // отюзаная ловушка
					}

			}
			else
			{
			$linte.='<img src=http://i.oldbk.com/llabb/bt.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в ловушке
			}
		 } // ловушка

break;

case 'D' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='D')
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/d.gif alt="Дверь" title="Дверь" >'; // активная дверь
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o.gif>'; // отюзаная Дверь
					}

			}
			else
			{
			$linte.='<img src=http://i.oldbk.com/llabb/dt.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип  в Двери
			}
		 } // Дверь

break;


case 'P' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='P')
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/p.gif alt="Ящик Пандоры" title="Ящик Пандоры">'; // активная коробка
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o.gif>'; // отюзаная ловушка
					}

			}
			else
			{
			$linte.='<img src=http://i.oldbk.com/llabb/pt.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в ловушке
			}
		 } // BOX P) Пандоры

break;


case 'S' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='S')
							{
							$linte.='<img src=http://i.oldbk.com/llabb/s.gif alt="Сундук" title="Сундук">' ; //полный
							}
							else
							{
							$linte.='<img src=http://i.oldbk.com/llabb/o.gif>' ;  //пустой сундук
							}
			}
			else
			{
			$linte.='<img src=http://i.oldbk.com/llabb/st.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый чар копается в сундуке
			}
		 } // сундук

break;

case 'H' :
		{
		 if ($TA[$i][$j]=='')
			{
					if ($Aitems[$i][$j]!='H')
							{
							  $linte.='<img src=http://i.oldbk.com/llabb/h.gif alt="Живая Вода" title="Живая Вода" >'; // хилка
							}
							else
							{
							  $linte.='<img src=http://i.oldbk.com/llabb/o.gif>';	// пустая лилка
							}

			}
			else
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ht.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый чар копается в хилке
			}
		 } // хилка

break;

case 'U' :
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://i.oldbk.com/llabb/u.gif title="Я" alt="Я">'; // Я на карте
			}
			else
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ut.gif title="Я ,'.$TA[$i][$j].'" alt="Я, '.$TA[$i][$j].'">'; //я и еще ктото из тимы
			}
		 } // user
break;




			}

            }
	  $MAP_SCREEN.=$linte."<br>\n";
	}

/// map prep
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#E2E0E0">
  <tr>
    <td ></td>
    <td width="307" ></td>
    <td width="300" ></td>
  </tr>
  <tr>
    <td height="409"  valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="5">&nbsp;</td>
    <td width="99%">
<?
		nick($user);
?>
<script LANGUAGE="JavaScript">
function confirmSubmit()
{
var agree=confirm('Действительно хотите Выйти и потерять все найденное?');
if (agree)
	return true ;
else
	return false ;
}
</script>

<form method=POST >
    <div class="btn-control">
        <input class="button-big btn" type=Submit name=exit value='Выйти и потерять все найденное!' onClick="return confirmSubmit()" >
    </div>
</form>
<?

/////////LOAD USERS VARs//////////////////
 if ($labpoz[dead] >0)
					{
					echo " Вы погибли в лабиринте:<b>".($labpoz[dead])." из 3-х</b> раз<br>";
					}

$uvars=mysql_query("SELECT * FROM `labirint_var` WHERE  `owner`='".$user[id]."' ;");
/// рисуем эфекты и другие переменные и не только рисуем а и выполняем их действие
	while ($row = mysql_fetch_array($uvars))
			{

//else
 {
			//осталось
			$delay=round(($row[val]-time())/60,2);
			if ($delay < 0 )
			{
			if ($row['var']=='timer_trap') { $_SESSION['time_trap']=0;}

			mysql_query("DELETE FROM `labirint_var` WHERE  (`var`='timer_trap' or `var`='poison_trap' )     and  `owner`='".$user[id]."' and `var`='".$row['var']."' ;");

					if ($row['var']=='poison_trap')
					{
							// del poison flag
					mysql_query("UPDATE users set `podarokAD`=0 where id='{$user[id]}';");
					}

			$row['var']='';

			}
			else
			{
				if ($row['var']=='poison_trap')
					{
				// рисуем
				echo "<img src=i/magic/event_poison_trap.gif alt='Яд' title='Яд'>-<i>Уровень жизни -2HP в минуту (Осталось:".$delay."мин.)</i><br>\n";

				//делаем - надо в крон!!


					}
				else if ($row['var']=='timer_trap')
					{
				// рисуем
				echo "<img src=i/magic/event_timer_trap.gif alt='Путы' title='Путы'>-<i>Время перехода +3 секунды (Осталось:".$delay."мин.)</i><br>\n";
				$_SESSION['time_trap']=1;
				//делаем
					}

			}


	}

		}
//сумрак
if ($_SESSION['sumrak']>0)
    {
   //	echo $_SESSION['sumrak']."<br>";
	echo "<img src=http://i.oldbk.com/i/sh/sumrak.gif alt='Сумрак' title='Сумрак'>-<i>Следующие: <b>".$_SESSION['sumrak']."</b> хода позволит пройти стенку</i><br>\n";
    }

		echo "<br>".$msg."<br>";
		echo "координаты : X=".$labpoz['x']."  Y=".$labpoz['y']."<br>";

?>
	</td>
    <td width="5">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
<?
		if ($Displ_team!="")
			{
			echo "<U>Ваша группа</U><br>";
			echo 	$Displ_team;
			}

?>
</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
<?
		if ($room_map!="O")
		{
		 echo "<u>В этой комнате находится:</u><br>";
		 }



///echo "Dif =$room_map | RMT = $rmt <br> |". $Aitems_val[$labpoz[x]][$labpoz[y]];
//echo "<br>";
//echo $user['battle'];
///
		if (($room_map=="R"))

	{
	//echo "СБ 1<br>";
				$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
				if  (($Aitems_val[$labpoz[x]][$labpoz[y]]>0) and ($user['battle'] == 0))     // уже идет бой тут надо вмешаться за своих
				{
				$batl_id=$Aitems_val[$labpoz[x]][$labpoz[y]];
				// БАГИ надо проверить статус боя
				$test_bttle=mysql_fetch_array(mysql_query("SELECT `win` from battle where `id`='".$batl_id."' LIMIT 1; "));
				if (($test_bttle[win]==3) and ($user['battle'] == 0) )
				{
				//бой еще идет
				//echo "уже идет бой тут надо вмешаться за своих<br>";

					//баг все еще дабл вход в бой
				/* $inbatuser = mysql_fetch_array(mysql_query("select * from battle where id='".$batl_id."' and (t1 like '%;{$user['id']};%' or t1 like '{$user['id']};%' or t1 like '%;{$user['id']}') LIMIT 1; "));
				if ($inbatuser[id]>0)
				  {
				  // есть уже в бою
				  //навсякий
				mysql_query("UPDATE users SET `battle` =".$batl_id.",`zayavka`=0, `battle_t`=1 WHERE `id`= ".$user['id']);
				 die("<script>location.href='fbattle.php';</script>");
				  }
				  else */
				  {

							//echo "//вмешиваемся";
							$ttt = 1; //тима за которую влезаем

						addch (nick_hist($user)." вмешался в <a href=logs.php?log=".$batl_id." target=_blank>поединок »»</a>.  ",$user['room']);
						$user[battle_t]=$ttt;
						$ac=($user[sex]*100)+mt_rand(1,2);
//						addlog($batl_id,"!:V:".time().":".nick_new_in_battle($user).":".$ac."\n");													
						addlog($batl_id,"!:W:".time().":".BNewHist($user).":".$user[battle_t].":".$ac."\n");	
//						addlog($batl_id,'<span class=date>'.date("H:i").'</span> '.nick_in_battle_hist($user,$ttt).' вмешался в поединок!<BR>');

						$time = time();
						mysql_query('UPDATE `battle` SET to1='.$time.', to2='.$time.', `t1`=CONCAT(`t1`,\';'.$user['id'].'\'), `t1hist`=CONCAT(`t1hist`,\''.BNewHist($user).'\')  WHERE `id` = '.$batl_id.' ;');
						mysql_query("UPDATE users SET `battle` =".$batl_id.",`zayavka`=0, `battle_t`=1 WHERE `id`= ".$user['id']);
						//mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$batl_id}','{$user['id']}')");
						$check =  mysql_fetch_array(mysql_query("SELECT count(*) FROM `battle_vars` WHERE `battle` = '".$batl_id."' and `owner`='".$user['id']."' LIMIT 1;"));
						if($check[0]>0) {
			           mysql_query("UPDATE battle_vars SET `update_time` = '{$time}' WHERE `battle`='".$batl_id."' and `owner`='".$user['id']."'");
								} else {
			           mysql_query("INSERT INTO battle_vars (battle,owner,update_time,type) VALUES ('{$batl_id}','{$user['id']}','{$time}','1')");
					          }

					die("<script>location.href='fbattle.php';</script>");
				   }
				}
				else
				{
				//баг пойман чистим клетку
				//mysql_query("UPDATE `labirint_items` SET `val`=0  WHERE `map`='".$mapid."' and `item`='R' and `x`='".$labpoz[x]."' and `y`='".$labpoz[y]."' and `active`=1 ;");
				//mysql_query("UPDATE `labirint_items` SET `count`=0  WHERE `map`='".$mapid."' and `item`='I' and `x`='".$labpoz[x]."' and `y`='".$labpoz[y]."' and `count`=-1 ;");


				}
////////////////////////////
				}
				else
				if (($Aitems[$labpoz[x]][$labpoz[y]]!='R') and (!$Aitems_val[$labpoz[x]][$labpoz[y]]>0)  )  // нету боя на клетке
	{
		//echo "СБ 2<br>";

	//баг все еще дабл вход в бой
	/* $inbatuser = mysql_fetch_array(mysql_query("select * from battle where `win`=3 and `type`=30 and (t1 like '%;{$user['id']};%' or t1 like '{$user['id']};%' or t1 like '%;{$user['id']}') LIMIT 1; "));
	if ($inbatuser[id]>0)
	  {
	  // есть уже в бою
	  //навсякий
	mysql_query("UPDATE users SET `battle` =".$inbatuser[id].",`zayavka`=0, `battle_t`=1 WHERE `id`= ".$user['id']);
		header("Location: fbattle.php");
		die("<script>location.href='fbattle.php';</script>");
	 }
	 else */
	{
// делаем уловку для двойных боев
//$_SESSION[runb]=1;
//баг по хп 0 и вмешивание в бой
if ($user[hp]==0)
{
mysql_query("UPDATE `users` SET `hp` = `hp`+2  WHERE `id` = '".$user[id]."' LIMIT 1;");
}
// Создаем бой с нужными мобами
//чем ближе к выходу сильнне монстры группы
$dmob=count($mob); // грузим линейку монсторо-груп
$dmob=round(($labpoz[y]*$dmob/(strlen($map[1])))); // получаем уровень сложности взависимости от положения перса к выходу.
$rs=$dmob-2;
if ($rs < 0) {  $rs=0; }

$rmt=mt_rand($rs,$dmob);
//get goup mobs


// расчет оружия + соответствующее мастерство
function load_wep($r_wep,$telo)
{
$tout= array();
// надо вернуть масив . шо за пушка
switch($r_wep['otdel'])
			 	{
			 	case 0:
			 		{
			 	//кулак
				$tout[chem]='kulak';
				$tout[mast]=0;
			 		}
				break;
				########

			 	case 1:
			 		{
			 	//ножи кастеты
				$tout[chem]='noj';
				$tout[mast]=$telo[noj];
			 		}
				break;
				########
		 		case 6:
			 		{
			 			// букет тип 50
				 		if ($r_wep[type]==50)
				 		{
						$tout[chem]='buket';
						$tout[mast]=0;
				 		} else
				 		{ // тут можно потом вводить новые типы для всякой хрени
				 	//Мешок Торговца
					$tout[chem]='meshok';
					$tout[mast]=0;
						}

			 		}
				break;
				########
		 		case 11:
			 		{
			 	//топоры
				$tout[chem]='topor';
				$tout[mast]=$telo[topor];
			 		}
				break;
				########
		 		case 12:
			 		{
			 	//дубина
				$tout[chem]='dubina';
				$tout[mast]=$telo[dubina];

			 		}
				break;
				########
		 		case 13:
			 		{
			 	//мечь
				$tout[chem]='mec';
				$tout[mast]=$telo[mec];

			 		}
				break;
		 		case 14:
			 		{
			 	//лук
				$tout[chem]='luk';
				$tout[mast]=0; // лук

			 		}
				break;


			 	}
return $tout;
}




function load_mass_items_by_id($telo)
{

//загружаем шмотки все кроме магий и подарков и всякой херни надо добавить
// загруженный масив буит нужен для расчетов и отображения
//$query_telo_dess = mysql_query("SELECT * FROM oldbk.inventory WHERE dressed = 1 AND `type`!=12 AND owner ={$telo[id]} ");
$query_telo_dess =mysql_query_cache("SELECT * FROM oldbk.inventory WHERE dressed = 1 AND `type`!=12 AND owner ={$telo[id]} ",false,24*3600);

	$telo_magicIds   = array();
	$telo_magicIds[] = 0;
	$telo_wearItems  = array();

////////////////////////////////
	$totsumm=0;
$telo_wearItems[krit_mf]=0;
$telo_wearItems[akrit_mf]=0;
$telo_wearItems[uvor_mf]=0;
$telo_wearItems[auvor_mf]=0;
$telo_wearItems[bron1]=0;
$telo_wearItems[bron2]=0;
$telo_wearItems[bron3]=0;
$telo_wearItems[bron4]=0;
$telo_wearItems[min_u]=0;
$telo_wearItems[max_u]=0;
$telo_wearItems[allsumm]=0;
$telo_wearItems[ups]=0;
$telo_wep[mast]=0;
$telo_wearItems[сhem]='';

//// тут потом можно загружать еще чето что надо
/// а пока тольк огрузим ниже мастерство для профильного оружия
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//	while($row = mysql_fetch_assoc($query_telo_dess)) {
	while(list($k,$row) = each($query_telo_dess)) {
	    $telo_wearItems[$row['id']] = $row;
	        $totsumm+=$row['cost'];
	$telo_wearItems[krit_mf]+=$row[mfkrit];
	$telo_wearItems[akrit_mf]+=$row[mfakrit];
	$telo_wearItems[uvor_mf]+=$row[mfuvorot];
	$telo_wearItems[auvor_mf]+=$row[mfauvorot];
		$telo_wearItems[bron1]+=$row[bron1];
		$telo_wearItems[bron2]+=$row[bron2];
		$telo_wearItems[bron3]+=$row[bron3];
		$telo_wearItems[bron4]+=$row[bron4];
	$telo_wearItems[min_u]+=$row[minu];
	$telo_wearItems[max_u]+=$row[maxu];
		$telo_wearItems[ups]+=$row[ups];

		if($row['includemagic'] > 0) {
	        $telo_magicIds[] = $row['includemagic'];
		}
		// шо за пушка
		if ($row[id]==$telo[weap])
		 	{
			$telo_wep=load_wep($row,$telo);
		 	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	} // end of while
//////////////////////////////////////////////////////////////////////////////////////////////////////
	$telo_wearItems[allsumm]=$totsumm; // запомним общую стоимость своий шмоток
	//fix если кулак и оружие не было просчитано
		if (($telo[weap]==0) and (!$telo_wep))
		 	{
		 	$kulak[otdel]=0;
		 	$telo_wep=load_wep($kulak,$telo);
		 	}
//////////////////////////////////////////////////////////////////////////////////////////////////////
	// формула вычисления мин - макс урон для меня
	$telo_wearItems[min_u] = round((floor($telo['sila']/3) + 1) + $telo['level'] + $telo_wearItems[min_u] * (1 + 0.07 * $telo_wep[mast]));
	$telo_wearItems[max_u] = round((floor($telo['sila']/3) + 4) + $telo['level'] + $telo_wearItems[max_u] * (1 + 0.07 * $telo_wep[mast]));
//////////////////////////////////////////////////////////////////////////////////////////////////////
// текстовый указатель на оружие
 	$telo_wearItems[сhem]=$telo_wep[chem];
 	$telo_wearItems[mast]=$telo_wep[mast];
///  fix из старой боевки для младших уровней по урону //////////////////////////////////
	if($telo_wearItems[сhem] == 'kulak' && (int)$telo['level'] < 4)
				{
					$telo_wearItems[min_u] += 3;
					$telo_wearItems[max_u] += 6;
				}
////////// бонус в кулачке нейтарлам мастерство + иего уровень	////////////////////////
	if($telo_wearItems[сhem] == 'kulak' && (int)$telo['align'] ==2)
				{
					$telo_wearItems[min_u] += $telo[level];
					$telo_wearItems[max_u] += $telo[level];
				}
///////////////////////////////////////////////////////////////////////////////////
///закрузка названий для магии встроеной
//	$query_telo_mag = mysql_query("SELECT * FROM magic WHERE id IN (" . implode(", ", $telo_magicIds) . ")");
//	while($row = mysql_fetch_assoc($query_telo_mag)) {
//	    $telo_magicItems[$row['id']] = $row;
//	}
//////////////////////////////////////////////////////////////////////////////////
	$telo_wearItems[incmagic]=$telo_magicItems;
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

return $telo_wearItems;
}


/////Добавим присоздании квестового бота если он есть в квесте и на клетке есть квесто поинт
   if   ( ($_SESSION['questdata'][q_bot] > 0) AND
    	( $Qmap[$labpoz[x]][$labpoz[y]]>0) )
    {
    make_qpoint($_SESSION['questdata'],$user,$mapid);// квест точка для выбивалок или убивалок -срабатывает присоздании боя с квестовым моснстором
    }

$c=0;
//готовим ботов

foreach ($mob[$rmt] as $k=>$v)
			{
//echo "СБ 3<br>";
			for ($l=0;$l<$v;$l++)
				{
				$c++;


				//$BOT=mysql_fetch_array(mysql_query("SELECT * from `users` where `id`='".$k."' ;"));
				$BOT=mysql_query_cache("SELECT * from `users` where `id`='".$k."' ;",false,24*3600);
				$BOT = $BOT[0];				
								
				$BOT['login'].=" (kлoн ".$c.")";
				$BNAME=BNewHist($BOT);
				$BNAME_chat=nick_hist($BOT);
				
				$BOT_items=load_mass_items_by_id($BOT);
					mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
					`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
					`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
					`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
					`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
					`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
					`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
					`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`=1,`bot`=1,
					`id_user`='{$BOT['id']}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
					`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
					`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
					`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
					`injury_possible`=0, `battle_t`=2;");

				$id_bot[$c]=mysql_insert_id();


				if ($bot_team!='') {
							$bots_names.=$BNAME;
							$bots_names_chat.=", ".$BNAME_chat;
							$bot_team.=";".$id_bot[$c];
							$bot_team_sql.=",".$id_bot[$c];
							}
							else
							{
							$bots_names=$BNAME;
							$bots_names_chat=$BNAME_chat;							
							$bot_team=$id_bot[$c];
							$bot_team_sql=$id_bot[$c];
							}
				//echo "<br> INS ID = $c <br>"; // insid

				}
				///работает!!
			$allch[$k]=$mdrop[$k]; // запоминаем все шансы из разных ид мобов
			}

//fix bag ставим пока все не подготовили win=0
	mysql_query("INSERT INTO `battle` (`id`,`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`)
						VALUES
						(NULL,'Бой в лабиринте Хаоса','','10','30','0','".$user[id].";','".$bot_team."','".time()."','".time()."',0,'".BNewHist($user)."','{$bots_names}')");


				$id_battl=mysql_insert_id();

			//создаем запись о том что бой с монстрами начался
			mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`,`val`) values('".$mapid."','R','".$labpoz[x]."','".$labpoz[y]."','1','".$id_battl."' ) ON DUPLICATE KEY UPDATE `active` =1;");

					// апдейтим ботов

			mysql_query("UPDATE `users_clons` SET `battle` = {$id_battl} WHERE `id` in (".$bot_team_sql.") ");

	/*
	foreach ($id_bot as $k=>$v)
				{
				//	mysql_query("UPDATE `users_clons` SET `battle` = {$id_battl} WHERE `id` = {$v} LIMIT 1;");
					//mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$id_battl}','{$v}')");
				}

					mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$id_battl}','".$user[id]."')");
*/
				// создаем лог
					$rr = "<b>".nick_hist($user)."</b> и <b>".$bots_names_chat."</b>";
//					addlog($id_battl,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. <BR>");
					addlog($id_battl,"!:S:".time().":".BNewHist($user).":".$bots_names."\n");
					mysql_query("UPDATE users SET `battle` ={$id_battl},`zayavka`=0, `battle_t`=1 WHERE `id`= '".$user[id]."';");


// грузим шансы дропа итемов
$cs=0;
if ($c==1) {$maxkoldrop=1;}

//$qeff = mysql_query('SELECT * FROM effects WHERE type = 813 AND owner = '.$_SESSION['uid']);
$qeff=mysql_query_cache('SELECT * FROM effects WHERE type = 813 AND owner = '.$_SESSION['uid'],false,60);

//if (mysql_num_rows($qeff) > 0) {
if (count($qeff) > 0) {
	$maxkoldrop = $maxkoldrop * 2;
	$dooble=true;	
}

$ar_shans=array();
foreach ($allch as $BOT=>$ARRAY)
	{
	foreach ($ARRAY as $iditema=>$shans)
		{
		if ($dooble) {$shans=$shans*2;}
		for ($ss=0;$ss<$shans;$ss++)
			{
			$cs++;
			$ar_shans[$cs]=$iditema;
			}

		}
	}


if (count($ar_shans)>0) 
	{
	shuffle($ar_shans);//мешаем
	}
	
$prev=0;



for ($ss=0;$ss<$maxkoldrop;$ss++)
{
$rnd=rand(0,count($ar_shans));
if($ar_shans[$rnd]>0)
   	{
   	if ($prev==$ar_shans[$rnd]) {$ss--;}
		else
		{
   		mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`,`count`,`val`) values('".$mapid."','I','".$labpoz[x]."','".$labpoz[y]."','0','-1','".$ar_shans[$rnd]."' ) ON DUPLICATE KEY UPDATE `active` =1, `val`='".$ar_shans[$rnd]."' ;");
   		$prev=$ar_shans[$rnd];
   		}
   	}
}
//////////////////////
			//fix bag апаем лог ставим win=3 и запускаем бой

			mysql_query("UPDATE `battle` set win=3 where id={$id_battl};");
					header("Location: fbattle.php");
					die("<script>location.href='fbattle.php';</script>");
				}

			}
				else
				{
				mysql_query("UPDATE `labirint_items` SET `count`=0  WHERE `map`='".$mapid."' and `item`='I' and `x`='".$labpoz[x]."' and `y`='".$labpoz[y]."' and `count`=-1 ;");

				// уже нету боев тут только труп возможно есть несобраные итемы
				$dropitems=mysql_query("SELECT * FROM `labirint_items` where `x`='".$labpoz[x]."' and `y`='".$labpoz[y]."' and `map`='".$mapid."' and `count` >=0 and `active`=0 and  (`owner`=0 OR `owner`='{$user[id]}')  and `item`='I' ; ");
				   	while ($row = mysql_fetch_array($dropitems))
						{
						$itis=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`eshop` where `id`='".$row[val]."'  LIMIT 1;    "));
						echo "<a href=?getitem={$row[val]}><IMG SRC=\"http://i.oldbk.com/i/sh/{$itis['img']}\" BORDER=0 alt='{$itis[name]}' title='{$itis[name]}'></a> ";
						do_auto($row[val]);
						}

				}

		}
		else
		if (($room_map=="P"))
			{
			if ($Aitems[$labpoz[x]][$labpoz[y]]=="P")
				{
				echo "<br><img src=http://i.oldbk.com/llabb/panbox_off.gif alt='Открытый Ящик' title='Открытый Ящик'>";

				   //проверяем 			$Aitems_count;
				   if ($Aitems_count[$labpoz[x]][$labpoz[y]]>0)
				   	{
				   	//есть насобраные итемы
					$dropitems=mysql_query("SELECT * FROM `labirint_items` where `x`='".$labpoz[x]."' and `y`='".$labpoz[y]."' and `map`='".$mapid."' and `count` >=0 and `active`=0 and `item`='I' ; ");
				   	while ($row = mysql_fetch_array($dropitems))
						{
						$itis=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`eshop` where `id`='".$row[val]."'  LIMIT 1;    "));
						echo "<a href=?getitem={$row[val]}><IMG SRC=\"http://i.oldbk.com/i/sh/{$itis['img']}\" BORDER=0 alt='{$itis[name]}' title='{$itis[name]}'></a> ";
						do_auto($row[val]);
						}
				   	}


				}
				else
				{
				echo "<br><a href=?openbox=1><img src=http://i.oldbk.com/llabb/panbox_on.gif alt='Открыть Ящик Пандоры' title='Открыть Ящик Пандоры' ></a>";
				}
			echo "<br>";
				}
		else
		if (($room_map=="B"))
			{
			if ($Aitems[$labpoz[x]][$labpoz[y]]=="B")
				{
				///echo "<br>Ловушка отработала...";
				}
				else
				{
				//попадоз
				// или рандомим ефект ловушки добавляем в юз

				$dlov=count($lov); // грузим линейку
				$dlov=round(($labpoz[y]*$dlov/(strlen($map[1])))); // получаем уровень сложности взависимости от положения перса к выходу.
				$rs=$dlov-2;
				if ($rs < 0) {  $rs=0; }
				$rmt=rand($rs,$dlov);
				foreach ($lov[$rmt] as $trap_name=>$v)
					{
				mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`) values('".$mapid."','B','".$labpoz[x]."','".$labpoz[y]."','1' ) ON DUPLICATE KEY UPDATE `active` =1;");
				//var global B count
				mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', '".$trap_name."', '".(time()+($v*60))."' ) ON DUPLICATE KEY UPDATE `val` =`val`+".($v*60).";");
				//системку в чат
				if ($user[sex]==1) { $sexi='угодил'; } else { $sexi='угодила'; }
				if ($trap_name=='timer_trap') { $_SESSION['time_trap']=1; $kom_trap='<i>Время перехода +3 секунды (Длительность:+'.$v.'мин.)</i> '; }
				else if ($trap_name=='poison_trap') {
									// add poison flag
									mysql_query("UPDATE users set `podarokAD`=1 where id='{$user[id]}';");

									$kom_trap='<i>Уровень жизни -2HP в минуту (Длительность:+'.$v.'мин.)</i>';
									}
				else {$kom_trap='';}

				addch("<img src=i/magic/event_".$trap_name.".gif> {$user[login]} ".$sexi." в ловушку...".$kom_trap);
				echo "<img src=i/magic/event_".$trap_name.".gif> {$user[login]} ".$sexi." в ловушку...".$kom_trap;

					}



				}
			echo "<br>";
				}
		else
		if (($room_map=="S"))
			{
			if ($Aitems[$labpoz[x]][$labpoz[y]]=="S")
			{
			echo "<br><img src='http://i.oldbk.com/llabb/use_sunduk_off.gif' alt='Открытый Сундук' title='Открытый Сундук' border=0>";
			}
			else {
			echo "<br><a href=?sunduk=get><img src='http://i.oldbk.com/llabb/use_sunduk_on.gif' alt='Сундук' title='Сундук' border=0></a>";
				}
			echo "<br>";
				}
		else
		if (($room_map=="Q"))
			{
			   if ($Aitems_count[$labpoz[x]][$labpoz[y]]>0)
				   	{
				   	//есть насобраные итемы
					$dropitems=mysql_query("SELECT * FROM `labirint_items` where `x`='".$labpoz[x]."' and `y`='".$labpoz[y]."' and `map`='".$mapid."' and (`owner`=0 OR `owner`='{$user[id]}') and `count` >=0 and `active`=0 and `item`='I' ; ");
				   	while ($row = mysql_fetch_array($dropitems))
						{
						$itis=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`eshop` where `id`='".$row[val]."'  LIMIT 1;    "));
						echo "<a href=?getitem={$row[val]}><IMG SRC=\"http://i.oldbk.com/i/sh/{$itis['img']}\" BORDER=0 alt='{$itis[name]}' title='{$itis[name]}'></a> ";
						do_auto($row[val]);
						}
				   	}
			}
		else
		if (($room_map=="H"))
			{
				if ($Aitems[$labpoz[x]][$labpoz[y]]=="H")
				{
				echo "<br><img src=http://i.oldbk.com/llabb/use_heal_off.gif alt='Пустая бутылка' title='Пустая бутылка' border=0>";
				}
				else
				{
				echo "<br><a href=?hill=1><img src=http://i.oldbk.com/llabb/use_heal_on.gif alt='Восстановление жизни' title='Восстановление жизни' border=0></a>";
				}
			echo "<br>";
				}
		else
		if (($room_map=="F"))
			{

				echo '<br><A HREF="?talk=73"><img src="http://i.oldbk.com/i/lock.gif" width=20 height=15></A><img src="http://i.oldbk.com/i/align_2.gif"><B>Старьевщик</B> [9]<a href=inf.php?73 target=_blank><IMG SRC=http://i.oldbk.com/i/inf.gif WIDTH=12 HEIGHT=11 ALT="Инф. о Старьевщик"></a>';
/*
				if ($_SESSION['sysmess']!=1)
				{
				$_SESSION['sysmess']=1;
				addchp ('<font color=red>[Старьевщик] Еще один безумец? Тебе тоже нужны сокровища? А ведь я предупреждал тебя и многих других, но вы не слушали старика.</font>','{[]}'.$user[login].'{[]}');
				addchp ('<font color=red>[Старьевщик] Жажда наживы закрыла вам глаза. Теперь примите же гнев духов и смиритесь со своей участью. </font>','{[]}'.$user[login].'{[]}');
				addchp ('<font color=red>[Старьевщик] Что смотришь ты на меня?</font>','{[]}'.$user[login].'{[]}');
				addchp ('<font color=red>[Старьевщик] Каждый раз, день за днем, час за часом вы пробирались в подземелья и воровали сокровища, вы пренебрегли покоем мертвых, вы надругались над верой и уважением к обитателям подземелий.</font>','{[]}'.$user[login].'{[]}');
				addchp ('<font color=red>[Старьевщик] Все чаще и чаще я слышал их гневные возгласы и  замечал следы их ног возле выхода из Лабиринта. Они  шепчутся в темноте о том, что  нашли способ выбираться из подземелья вслед за людьми, и с каждым днем их будет становиться все больше и больше...</font>','{[]}'.$user[login].'{[]}');
				addchp ('<font color=red>[Старьевщик] Они покарают всех кто нарушал их покой. Хочешь совет? Либо беги из города куда глаза глядят насколько хватит сил, либо смирись и прими грядущий бой, как подобает воину.</font>','{[]}'.$user[login].'{[]}');
				addchp ('<font color=red>[Старьевщик] Я вижу как побелели костяшки твоих пальцев,судорожно сжимающие рукоять меча. Беги прочь отсюда глупец.... жить тебе осталось совсем чуть чуть, ибо солнце уже садится....</font>','{[]}'.$user[login].'{[]}');
				}
*/
				echo " <b><INPUT TYPE=button onclick=\"location.href='?talk=73';\" value=\"Поговорить\" name=\"talk\" style=\"background-color:#A9AFC0\"><b>";
				echo "<br>Поздравляем, Вы нашли выход...<br>";
				echo "<form method=POST><input type=submit name=exit_good value='Ура Выход!'></form>";
				echo "<br>";



			}
//		if (($room_map!="F")) {	$_SESSION['sysmess']=0;	}
/*
if ($user[id]==14897)
	{
	echo "ROOM:";
	print_r($room_map);
	echo "<br> Qmap:";
	print_r($Qmap);
	echo "<br>Aitem:";
	print_r($Aitems);
	echo "<br>Ait_val";
	print_r($Aitems_val);
	echo "<br>$Aite_cout";
	print_r($Aitems_count);
	echo "<br>";
	}
*/
?>


</td>
    <td>&nbsp;</td>
  </tr>
</table>

    </td>
    <td style="background-repeat:repeat; width:300px; height:410px" align="right"><img src="<?=$fotofile?>" />  </td>
    <td height="409" width="300" valign="top"  align="center">
    <table width="100%" height="396" border="0" cellpadding="0" cellspacing="0" style="background-position: top right; background-repeat: no-repeat; width: 300px; height: 410px; background: url('http://i.oldbk.com/i/laba/navbg_big.gif'); ">
    <tr>
    <td height="34" >

<table align="center" height="25" border=0 style="background:url(http://i.oldbk.com/i/laba/ramka_s2.gif); background-repeat:no-repeat; background-position:left;">
<tr valign="middle">
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td>
<div id="showbar" style="font-size:2pt;padding:2px;border:solid black 0px;visibility:hidden">
<span id="progress1">&nbsp;&nbsp;</span>
<span id="progress2">&nbsp;&nbsp;</span>
<span id="progress3">&nbsp;&nbsp;</span>
<span id="progress4">&nbsp;&nbsp;</span>
<span id="progress5">&nbsp;&nbsp;</span>
<span id="progress6">&nbsp;&nbsp;</span>
<span id="progress7">&nbsp;&nbsp;</span>
<span id="progress8">&nbsp;&nbsp;</span>
<span id="progress9">&nbsp;&nbsp;</span>
<span id="progress10">&nbsp;&nbsp;</span>
<span id="progress11">&nbsp;&nbsp;</span>
<span id="progress12">&nbsp;&nbsp;</span>
<span id="progress13">&nbsp;&nbsp;</span>
<span id="progress14">&nbsp;&nbsp;</span>
<span id="progress15">&nbsp;&nbsp;</span>
<span id="progress16">&nbsp;&nbsp;</span>
<span id="progress17">&nbsp;&nbsp;</span>
<span id="progress18">&nbsp;&nbsp;</span>
<span id="progress19">&nbsp;&nbsp;</span>
<span id="progress20">&nbsp;&nbsp;</span>
<span id="progress21">&nbsp;&nbsp;</span>
<span id="progress22">&nbsp;&nbsp;</span>
<span id="progress23">&nbsp;&nbsp;</span>
<span id="progress24">&nbsp;&nbsp;</span>
<span id="progress25">&nbsp;&nbsp;</span>
<span id="progress26">&nbsp;&nbsp;</span>
<span id="progress27">&nbsp;&nbsp;</span>
<span id="progress28">&nbsp;&nbsp;</span>
<span id="progress29">&nbsp;&nbsp;</span>
<span id="progress30">&nbsp;&nbsp;</span>
<span id="progress31">&nbsp;&nbsp;</span>
<span id="progress32">&nbsp;&nbsp;</span>
<span id="progress33">&nbsp;&nbsp;</span>
<span id="progress34">&nbsp;&nbsp;</span>
<span id="progress35">&nbsp;&nbsp;</span>
<span id="progress36">&nbsp;&nbsp;</span>
<span id="progress37">&nbsp;&nbsp;</span>
<span id="progress38">&nbsp;&nbsp;</span>
<span id="progress39">&nbsp;&nbsp;</span>
<span id="progress40">&nbsp;&nbsp;</span>

<? $TTT=time()-$_SESSION['time']; $TTTJ=($TTT+1)*10-1; ?>
</div>
</td>
<td>&nbsp;&nbsp;</td>
</tr></table>
<script language="javascript">
var progressEnd = 40; // set to number of progress <span>'s.
var progressColor = 'green'; // set to progress bar color
<?
if ($_SESSION['time_trap']==1)
	{
	echo "var progressInterval = 200;";
	}
	else
	{
	echo "var progressInterval = 100;";
	}
?>

var progressAt = <?=$TTTJ?>;
var progressTimer;


function progress_set(too) {
for (var i = 1; i <= too; i++) document.getElementById('progress'+i).style.backgroundColor = progressColor;
}

function progress_none() {
for (var i = 1; i <= 40; i++) document.getElementById('progress'+i).style.backgroundColor = progressColor;
}

function progress_clear() {
for (var i = <?=$TTTJ?>; i <= progressEnd; i++) document.getElementById('progress'+i).style.backgroundColor = 'transparent';
progressAt = <?=$TTTJ?>;
}
function progress_update() {
document.getElementById('showbar').style.visibility = 'visible';
progressAt++;
		if (progressAt > progressEnd)
			{
			clearTimeout(progressTimer);
			return;
			}
		else document.getElementById('progress'+progressAt).style.backgroundColor = progressColor;
progressTimer = setTimeout('progress_update()',progressInterval);
}


<?
if($TTT <= 3) {
			echo "progress_clear(); \n";
			echo "progress_set($TTTJ); \n";
			echo "progress_update(); \n";
			}
			else
			{
			echo "progress_set(40); \n";
			echo "document.getElementById('showbar').style.visibility = 'visible'; \n";
			//echo "progress_update(); \n";
			}
?>
</script>


	<div align="right">&nbsp;</div></td>
    </tr>
    <tr>
      <td height="17"></td>
    </tr>
    <tr>
      <td height="102" valign="top" align="center">

      <table width="100%" height="102" border="0" cellpadding="0" cellspacing="0"  >
  <tr>
    <td width="95" height="102"></td>
    <td width="103" style="background:url(http://i.oldbk.com/i/laba/in_nav_bg.gif); width:103px; height:102px; background-repeat: no-repeat;">
        <table width="103" height="102" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="26" height="26"></td>
    <td width="12" ></td>
    <td width="26" height="26"><a href="#" onclick='location.href="lab3.php?goto=p1";'><img src="http://i.oldbk.com/i/laba/arr1.gif" border="0" title="Вверх" alt="Вверх" /></a></td>
    <td width="13"></td>
    <td width="26"></td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td height="11"></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td width="26" height="26"><a href="#" onclick='location.href="lab3.php?goto=p4";'><img src="http://i.oldbk.com/i/laba/arr4.gif" border="0" title="Налево" alt="Налево" /></a></td>
    <td></td>
    <td width="26" height="26"><a href="#" onclick='location.href="lab3.php?refresh=13.4";'><img src="http://i.oldbk.com/i/laba/refresh.gif" border="0" title="Обновить" alt="Обновить" /></a></td>
    <td></td>
    <td width="26" height="26"><a href="#" onclick='location.href="lab3.php?goto=p2";'><img src="http://i.oldbk.com/i/laba/arr2.gif" border="0" title="Направо" alt="Направо" /></a></td>
  </tr>
  <tr>
    <td ></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="13"></td>
    <td height="13"></td>
    <td height="13"></td>
    <td height="13"></td>
    <td height="13"></td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td width="26" height="26"><a href="#" onclick='location.href="lab3.php?goto=p3";'><img src="http://i.oldbk.com/i/laba/arr3.gif" border="0" title="Вниз" alt="Вниз" /></a></td>
    <td></td>
    <td></td>
  </tr>
</table>    </td>
    <td width="105" height="5"></td>
  </tr>
</table>      </td>
    </tr>
    <tr>
      <td height="5"></td>
    </tr>
    <tr valign=top>
      <td height="165">
      <table width="303" height="165" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="67" height="74"></td>
    <td width="165" height="165">
    <? echo $MAP_SCREEN; ?>
    </td>
    <td width="64"></td>
  </tr>
</table>

      </td>
    </tr>
    <tr>
      <td height="25"></td>
    </tr>
    <tr>
      <td height="25"></td>
    </tr>
    </table>
    </td>
  </tr>
</table>
<?
include "end_files.php";
?>
</body>
</html>
<?

/////////////////////////////////////////////////////
/*
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
*/
/////////////////////////////////////////////////////

?>