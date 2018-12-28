#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php";

if( !lockCreate("cron_bplace_job") ) {
    exit("Script already running.");
}
$VER='6.0';
//старт мега боя - запуск по расписанию! из крона для 3-х стороннего боя

function make_align_coff($lvl)
{
global $CITY_NAME;

if ($lvl!=21)
{

 if ($CITY_NAME=='capitalcity')
 	{
	 $get_all_align=mysql_query("select count(id) as kol,align from oldbk.users where level={$lvl} and id_city=0 and block=0 and bot=0 and ldate>=(".time()."-13*24*60*60) and align>0 group by align");
	 }
	 else if ($CITY_NAME=='avaloncity')
	 {
	 $get_all_align=mysql_query("select count(id) as kol,align from avalon.users where level={$lvl} and id_city=1 and block=0 and bot=0 and ldate>=(".time()."-13*24*60*60) and align>0 group by align");
	 }
	 else if ($CITY_NAME=='angelscity')
	 {
	 $get_all_align=mysql_query("select count(id) as kol,align from angels.users where level={$lvl} and id_city=2 and block=0 and bot=0 and ldate>=(".time()."-13*24*60*60) and align>0 group by align");
	 }	 
 
 	$count=array();
 
     while($al = mysql_fetch_array($get_all_align)) 
     	{
	     	$align=(int)($al[align]);
	     	if ($align==1) {$align=6;} //палы = свет
	     	if (($align==2) OR ($align==6) OR ($align==3))
	     	{
	     	$count[$align]+=$al[kol];
	     	}
     	}
 
 $coff=min($count); // берем минимальное

if (($coff>30) and ($coff<51))  {$coff=round($coff*0.8); }
elseif (($coff>50) and ($coff<71))  {$coff=round($coff*0.75); }
elseif (($coff>70) and ($coff<101))  {$coff=round($coff*0.7); }
elseif (($coff>100) and ($coff<151))  {$coff=round($coff*0.6); }
elseif ($coff>150)  {$coff=round($coff*0.45); }

 if ($coff<30) {$coff=30;} // если меньше 30 будет 30;
}
else
{
$coff=1000;
}

//ствим то что получилось в ограничение
mysql_query("UPDATE `place_battle` SET `val`='{$coff}' WHERE `var`='maxusers';");

return $coff;
}


function do_new_zay()
{
 //  mysql_query("INSERT INTO `place_zay` SET `coment`='СВЕТ VS TЬМА',`type`=61,`team1`='',`t1data`='',`team2`='',`t2data`='',`start`='".(time()+777600)."',`timeout`=5,`t1min`=7,`t1max`=21,`t2min`=7,`t2max`=21,`level`=6,`podan`='19:00',`t1c`=30,`t2c`=30,`blood`=1,`active`=0;");
}


function winNETRAL()
{
//сторона 3
global $zayava;
     {
    //удаляем старую заявку
    mysql_query("DELETE FROM `place_zay` WHERE id='".$zayava[id]."' ; ");
    mysql_query("UPDATE `place_battle` SET `val`=2 WHERE `var`='master' ; "); //2 тейтралы
    mysql_query("UPDATE `place_battle` SET `val`=3 WHERE `var`='winers' ; ");	// выиграла 3-й команда
    mysql_query("UPDATE `place_battle` SET `val`=`val`+1 WHERE `var`='netral_count' ; ");
     // закрыть заявку у людей
   mysql_query("UPDATE `users` SET `bpalign`=0, `bpstor`=0, `bpzay`=0 WHERE `bpzay`>0 ; ");
    do_new_zay();
     }
}

function winT()
{
//сторона 2
global $zayava;
     {
    //удаляем старую заявку
    mysql_query("DELETE FROM `place_zay` WHERE id='".$zayava[id]."' ; ");
    // нету света - тех победа тьме
    // выставляем поле в тьму и добавляем очко
    mysql_query("UPDATE `place_battle` SET `val`=3 WHERE `var`='master' ; ");
    mysql_query("UPDATE `place_battle` SET `val`=2 WHERE `var`='winers' ; ");	
    mysql_query("UPDATE `place_battle` SET `val`=`val`+1 WHERE `var`='darck_count' ; ");
     // закрыть заявку у людей
   mysql_query("UPDATE `users` SET `bpalign`=0, `bpstor`=0, `bpzay`=0 WHERE `bpzay`>0 ; ");
    //сообщение
   // addch2all('<font color=red>Внимание!</font> Бой СВЕТ-ТЬМА, не может начаться по причине "Свет не явилася"!');
    // создаем следующую заявку не 
    do_new_zay();
     }
}

function winS()
{
//сторона 1
global $zayava;
    {
     //удаляем старую заявку
     mysql_query("DELETE FROM `place_zay` WHERE id='".$zayava[id]."' ; ");
     // нету тьмы - тех победа свету
    // выставляем поле в свет и добавляем очко    
    mysql_query("UPDATE `place_battle` SET `val`=6 WHERE `var`='master' ; ");
    mysql_query("UPDATE `place_battle` SET `val`=1 WHERE `var`='winers' ; ");	    
    mysql_query("UPDATE `place_battle` SET `val`=`val`+1 WHERE `var`='light_count' ; ");
    //сообщение
   // addch2all('<font color=red>Внимание!</font> Бой СВЕТ-ТЬМА, не может начаться по причине "Тьма не явилась"!');
     // закрыть заявку у людей
    mysql_query("UPDATE `users` SET `bpalign`=0, `bpstor`=0, `bpzay`=0 WHERE `bpzay`>0 ; ");
   // создаем следующую заявку активную
    do_new_zay();

    }
}

function winN()
{
global $zayava;
    {
    //удаляем старую заявку
     mysql_query("DELETE FROM `place_zay` WHERE id='".$zayava[id]."' ; ");
    // в бою нет заявок c обоих сторон
    // выставляем поле в ничью
    mysql_query("UPDATE `place_battle` SET `val`=0 WHERE `var`='master' ; ");
    mysql_query("UPDATE `place_battle` SET `val`=0 WHERE `var`='winers' ; ");	
    //сообщение
   // addch2all('<font color=red>Внимание!</font> Бой СВЕТ-ТЬМА, не может начаться по причине "Никто не явился :("!');
    // создаем следующую заявку активную
    do_new_zay();
    }
}

function nickbp($uid)
{
$rout=array();
$rout[id]=0;


$usrdata=mysql_fetch_array(mysql_query("SELECT `id`, `login`, `level`, `klan` , `room`, `align`, hp  FROM `users` WHERE `hp` > 0 and `room`=60 and `id` = '".$uid."' ;"));

if (($usrdata[id] >0) and ($usrdata[ldate] >= (time()-60) ))
	{

	$rout[id]=$usrdata[id];
	$rout[login]=$usrdata[login];

	$mm = "<img src=\"http://i.oldbk.com/i/align_".($usrdata['align']>0 ? $usrdata['align']:"0").".gif\">";
	if ($usrdata[klan]!="")
	{

	$mm.= '<img title="'.$usrdata['klan'].'" src="http://i.oldbk.com/i/klan/'.$usrdata['klan'].'.gif">'; 
	}
	$mm.= "<B>{$usrdata['login']}</B> [{$usrdata['level']}]<a href=inf.php?{$usrdata['id']} target=_blank><IMG SRC=http://i.oldbk.com/i/inf.gif WIDTH=12 HEIGHT=11 ALT=\"Инф. о {$usrdata['login']}\"></a>";
	 
	$rout[logintext]=$mm;
	}
return $rout;
}


//$zayava=mysql_fetch_array(mysql_query("SELECT * FROM `place_zay` WHERE  `level`=6 and `active`=1 LIMIT 1;"));
$zayava=mysql_fetch_array(mysql_query("select * from place_zay ORDER by `start` LIMIT 1;")); // выбираем самую ближайшую арену

	$t=time();
	echo $t;
	echo "<br>";
	echo $zayava['start'];
	echo "<br>";

if($zayava[id] >0 ) // если ли ваще заявка
{

if ($zayava[active]==1) //заявка активная
 {
	if ( ($zayava[id]>0) and ($zayava['start'] <= $t) )
	   {
	   // есть такая
	   //деактивируем сразу для работы
	   mysql_query("UPDATE `place_zay` SET `active`=0 WHERE id='".$zayava[id]."' ; ");
	 
   ///
    if (($zayava[team1]=='')and($zayava[team2]=='') and ($zayava[team3]==''))
    {
    winN();
    }
    else
    if (($zayava[team1]=='')and($zayava[team2]!='') and ($zayava[team3]==''))
	{
	winT();
	}
    else
    if (($zayava[team1]!='')and($zayava[team2]=='') and ($zayava[team3]==''))
	{
	winS();
	}
    else
    if (($zayava[team3]!='')and($zayava[team2]=='') and ($zayava[team1]==''))
	{
	winNETRAL();
	}	
    else
    {
    //все есть
    //echo "есть люди создаем бой!";
     ////////////////////////
    // удаляем старую заявку     
      mysql_query("DELETE FROM `place_zay` WHERE id='".$zayava[id]."' ; ");
  //// можем стартовать бой  
     		{
				$cc=mysql_fetch_array(mysql_query("select * from place_battle where var='maxusers'"));

			    	$time = time(); 
				//создаем бой с пустыми тимами
				mysql_query("INSERT INTO `battle`
						(
							`id`,`nomagic`,`price`,`fond`,`coment`,`teams`,`timeout`,`type`,`status`,`status_flag`,`t1`,`t2`,`t3`,`to1`,`to2`,`to3`,`blood`,`CHAOS`
						)
						VALUES
						(
							NULL,'0','0','0','{$zayava[coment]}','','{$zayava['timeout']}','{$zayava['type']}','1','6','','','','".$time."','".$time."','".$time."','".$zayava['blood']."','0'
						)");
						
				$id = mysql_insert_id();
				//
				
				
				//
				mysql_query("INSERT INTO `place_logs` (`startdate`, `findate`, `win`, `battle`, `active`, `usrc` ) VALUES ('".date("Y.m.d H.i")."', '".date("Y.m.d H.i")."', '0', '".$id."', 1, '".$cc['val']."');");
				$bplogid = mysql_insert_id();
				
				mysql_query("UPDATE place_battle set val={$id} where var='battle'; ");
				mysql_query("UPDATE place_battle set val='".$time."' where var='starttime'; ");
				
				
				//1. апдейтнуть все кто нам нужен ставим бой и сторону 
				mysql_query("update users  set `battle`='{$id}', `battle_t`=`bpstor`, bpzay=0 where bpzay>0 AND room=60 AND ldate >= (".(time()-60).") AND bpstor>0 AND hidden=0 AND battle=0 AND zayavka=0 AND hp>1 AND sila>0 AND lovk>0 AND inta>0;");
				echo "SQL1:".mysql_error();
				mysql_query("COMMIT;");
				//2. выбрать для хистори кто нам нужен
				$GET_ALL=mysql_query("SELECT * from users where battle='{$id}'");
				$t1_hist=''; $t1_id=''; $new_t1='';
				$t2_hist=''; $t2_id='';	 $new_t2='';
				$t3_hist=''; $t3_id='';	 $new_t3='';				
				while ($rowa = mysql_fetch_array($GET_ALL))
				{
				//заполнитель
				if ($rowa[battle_t]==1)
				 {
				 $t1_hist.=nick_align_klan($rowa).", ";
				 $new_t1.=BNewHist($rowa);
				 $t1_id.=$rowa[id].";";
				 }
				 elseif ($rowa[battle_t]==2)
				 {
				 $t2_hist.=nick_align_klan($rowa).", ";
				 $new_t2.=BNewHist($rowa);				 
				 $t2_id.=$rowa[id].";";
				 }
				 elseif ($rowa[battle_t]==3)
				 {
				 $t3_hist.=nick_align_klan($rowa).", ";
				 $new_t3.=BNewHist($rowa);				 
				 $t3_id.=$rowa[id].";";
				 }				 
				 
				addchp ('<font color=red>Внимание!</font> Ваш бой начался! <BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ','{[]}'.$rowa[login].'{[]}');
				}
				
				if ($t1_hist=='') { $t1_hist ="<i>Свет не явилися</i>  "; $erst1=1; }
				if ($t2_hist=='') { $t2_hist ="<i>Тьма не явилась</i>  "; $erst2=1; }
				if ($t3_hist=='') { $t3_hist ="<i>Нейтралы не явилась</i>  "; $erst3=1; }				
				/////////////////////////////////////////////////////////////////////////
			        //make logs
				$t1_hist=substr($t1_hist, 0, -2);
				$t2_hist=substr($t2_hist, 0, -2);
				$t3_hist=substr($t3_hist, 0, -2);				
				 
				 $t1_id=substr($t1_id, 0, -1);
				 $t2_id=substr($t2_id, 0, -1);
				 $t3_id=substr($t3_id, 0, -1);				 

				// создаем лог
				$rr1 = "<b>".$t1_hist."</b>"; 
				$rr2 = "<b>".$t2_hist."</b>";
				$rr3 = "<b>".$t3_hist."</b>";				
				$rr=$rr1." и ".$rr2." и ".$rr3;
				
				// последняя проверка на тимы
				if (($erst1==1)and($erst2==1)and($erst3==1))
				{
				// обе стороны не пришли хоть и были завки
				winN();
				addch ("<a href=logs.php?log=".$id." target=_blank>Поединок</a> <B>".$zayava[coment]."</B> начался.   ",60);
				addlog($id,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда Свет, Тьма и Нейтралы бросили вызов друг другу. \n");
				addlog($id,'<span class=date>'.date("H:i").'</span> '.'Все стороны не явились на бой\n');
				addlog($id,'<span class=date>'.date("H:i").'</span> '.'Бой закончен. Ничья.\n');
				mysql_query("UPDATE `battle` SET `status`=1,`win`='0', `t1hist`='".$new_t1."', `t2hist`='".$new_t2."' , `t3hist`='".$new_t3."'  WHERE `id`='".$id."' ; ");
				mysql_query("UPDATE `place_logs` SET `active`=0 WHERE `id`='".$bplogid."'");
				addch ("<a href=logs.php?log=".$id." target=_blank>Бой закончен.</a> <B>".$zayava[coment]."</B> Ничья.",60);
				mysql_query("UPDATE users SET battle=0, battle_t=0 where battle='{$id}'");
				}
				elseif ( ($erst1==1) and ($erst3==1) )
				{
				// победа тьмы - свет и нейтралы не я вились
				winT();
				addch ("<a href=logs.php?log=".$id." target=_blank>Поединок</a> <B>".$zayava[coment]."</B> начался.   ",60);
				addlog($id,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. \n");
				addlog($id,'<span class=date>'.date("H:i").'</span> '.'Бой закончен. Победа за Тьмой!\n');
				mysql_query("UPDATE `battle` SET `status`=1,`win`='2', `t1hist`='".$new_t1."', `t2hist`='".$new_t2."' WHERE `id`='".$id."' ; ");
				mysql_query("UPDATE `place_logs` SET `active`=0, `win`='2' WHERE `id`='".$bplogid."'");
				addch ("<a href=logs.php?log=".$id." target=_blank>Бой закончен.</a> <B>".$zayava[coment]."</B> Победа за Тьмой!",60);		
				mysql_query("UPDATE users SET battle=0, battle_t=0 where battle='{$id}'");
				}
				elseif (($erst2==1)  and ($erst3==1) )
				{
				// победа света = тьма и нейтралы не пришли
				winS();
				addch ("<a href=logs.php?log=".$id." target=_blank>Поединок</a> <B>".$zayava[coment]."</B> начался.   ",60);
				addlog($id,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. \n");
				addlog($id,'<span class=date>'.date("H:i").'</span> '.'Бой закончен. Победа за Светом!\n');
				mysql_query("UPDATE `battle` SET `status`=1,`win`='1', `t1hist`='".$new_t1."', `t2hist`='".$new_t2."'  , `t3hist`='".$new_t3."'  WHERE `id`='".$id."' ; ");				
				mysql_query("UPDATE `place_logs` SET `active`=0, `win`='1' WHERE `id`='".$bplogid."'");
				addch ("<a href=logs.php?log=".$id." target=_blank>Бой закончен.</a> <B>".$zayava[coment]."</B> Победа за Светом!",60);
				mysql_query("UPDATE users SET battle=0, battle_t=0 where battle='{$id}'");
				}
				elseif (($erst1==1)  and ($erst3==1) )
				{
				// победа нейтралов = тьма и свет не пришли
				winNETRAL();
				addch ("<a href=logs.php?log=".$id." target=_blank>Поединок</a> <B>".$zayava[coment]."</B> начался.   ",60);
				addlog($id,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. \n");
				addlog($id,'<span class=date>'.date("H:i").'</span> '.'Бой закончен. Победа за Нейтралами!\n');
				mysql_query("UPDATE `battle` SET `status`=1,`win`='1', `t1hist`='".$new_t1."', `t2hist`='".$new_t2."'  , `t3hist`='".$new_t3."'  WHERE `id`='".$id."' ; ");				
				mysql_query("UPDATE `place_logs` SET `active`=0, `win`='4' WHERE `id`='".$bplogid."'");
				addch ("<a href=logs.php?log=".$id." target=_blank>Бой закончен.</a> <B>".$zayava[coment]."</B> Победа за Нейтралами!",60);
				mysql_query("UPDATE users SET battle=0, battle_t=0 where battle='{$id}'");
				}				
				else
				{
				// таки бой будет
				mysql_query("UPDATE `battle` SET `status`=0,`win`='3', `t1`='{$t1_id}', `t2`='{$t2_id}', `t3`='{$t3_id}' ,`t1hist`='".$new_t1."', `t2hist`='".$new_t2."', `t3hist`='".$new_t3."' WHERE `id`='".$id."' ; ");				
				addch ("<a href=logs.php?log=".$id." target=_blank>Поединок</a> <B>".$zayava[coment]."</B> начался.   ",60);
				//addlog($id,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. <BR>");
				addlog($id,"!:S:".time().":".$new_t1.":".$new_t2.":".$new_t3."\n");
				
			        // создаем следующую заявку НЕ активную - активируем после боя!! -активация по крону за 7 часов 
				do_new_zay();
				// чистка чаров заявок которые не попали в бой
				mysql_query("UPDATE users SET `bpzay`=0 WHERE `bpzay`>0 ");
				}
		}
     
     
     
     /// в конце боя 
    // обнулить поле bpstor - ВСЕМ
    
    }

   
   
   }
   else if (($zayava[sys_mess_10m]==0) and (($zayava['start']-600) <= $t) )
   {
 	mysql_query("UPDATE `place_zay` SET `sys_mess_10m`=1 WHERE id='".$zayava[id]."' ; ");
	$TEXT='<font color=red>Внимание!</font> На "Арене Богов" через 10 минут состоится бой "'.$zayava[coment].'", открыт прием заявок на бой! Важно за минуту до начала находиться в локации!';
	      addch2all($TEXT,$bot_city);

   }

} //активная заявка
else
{
echo " заявка не активная";
//проверяем не порали ее активировать
   if ( ($zayava[id]>0) and (($zayava['start']-3600) <= $t) ) // за час открываем
   {
   
   mysql_query("UPDATE `place_zay` SET `active`=1 WHERE id='".$zayava[id]."' ; ");
   mysql_query("UPDATE `place_battle` SET `val`='{$zayava['t1max']}' WHERE `var`='max_level';");
   mysql_query("UPDATE `place_battle` SET `val`='{$zayava['t1min']}' WHERE `var`='min_level';");

   mysql_query("UPDATE `place_battle` SET `val`='{$zayava['type']}' WHERE `var`='type';");  
   mysql_query("UPDATE `place_battle` SET `val`=0 WHERE `var`='master';"); // обновляем картину в нейтральную
   echo "Активировали\n";
   
   $maxusers=make_align_coff($zayava[t1max]);
   
   // отправляем системку о том что открылся набор в заявку
   if ($maxusers==1000)
   {
      mysql_query("UPDATE `place_battle` SET `val`='".($zayava['start']+21600)."' WHERE `var`='close';");   //close 6 часов
      $TEXT='<font color=red>Внимание!</font> На "Арене Богов" через 1 час состоится бой "'.$zayava[coment].'", открыт прием заявок на бой! ';
   }
   else
   {
   mysql_query("UPDATE `place_battle` SET `val`='".($zayava['start']+36000)."' WHERE `var`='close';");   //close 10 часов
   $TEXT='<font color=red>Внимание!</font> На "Арене Богов" через 1 час состоится бой "'.$zayava[coment].'", открыт прием заявок на бой! Максимальное количество участников для каждой склонности:'.$maxusers;
   }
    addch2all($TEXT,$bot_city);
   
   
   
   }
   else
   {
   echo "Еще не время активирвать";
   }


}


} // есть ли заявка

lockDestroy("cron_bplace_job");

?>