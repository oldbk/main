#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php"; //CAPLITAL CITY ONLY
if( !lockCreate("cron_hp_job") ) {
    exit("Script already running.");
}
echo date("D M j G:i:s T Y"); 
echo "\n";
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$tstart = $mtime;

	$HH=(int)(date("H",time()));




	$begin=mktime(0,0,1,3,8,2011);
	$end=mktime(23,59,59,3,8,2011);

$data = mysql_query("SELECT `id`, `fullhptime`, `align`, `sex`, room, id_grup ,  `pasbaf`,`in_tower`  FROM `users` WHERE `hp` < `maxhp` AND `battle` = 0 AND `in_tower` != 1 AND `lab`=0 ;"); //and block=0 - для увеличения производительности убрано из запроса
while($user = mysql_fetch_array($data)) {
	if ((time()-$user[1])/60 > 0) {
		
		/*
		if (
			 ( ($user[align]==6) OR (($user[align]>=1) and ($user[align]<2)) ) and
			 (((int)date("H") > 8) && ((int)date("H") < 18))  and ($user[in_tower]!=2) and (!(($user[room]>=50000) and ($user[room]<=60000) and ($user[id_grup]>0)))
		   )
 		{
		            if(time()>$begin && time()<$end && $user[sex]!=1)
        		    	{
        		    	if (($HH>=9) and ($HH<21)) 
				{
				//echo "День";
				if ($user[pasbaf]==850) { $hp_cof=2.1;} 
					elseif ($user[pasbaf]==861) { $hp_cof=1.4; }
							else { $hp_cof=0;}
				}
				else
				{
				//с 21:00 до 09:00
				//echo "Ночь";
				if ($user[pasbaf]==842) { $hp_cof=1.4;} else { $hp_cof=0;}
				}

			    	mysql_query("UPDATE `users` SET `hp` = `hp`+((".time()."-`fullhptime`)/60)*(`maxhp`/(7-{$hp_cof})), `fullhptime` = ".time()." WHERE  `hp` < `maxhp` AND `id` = '".$user[0]."' LIMIT 1;");
			        }
        		    else
			       {
			       	
			       	 if (($HH>=9) and ($HH<21)) 
				{
				//echo "День";
				if ($user[pasbaf]==850) { $hp_cof=4.5;}
					elseif ($user[pasbaf]==861) { $hp_cof=3; }					
					 else { $hp_cof=0;}
				}
				else
				{
				//с 21:00 до 09:00
				//echo "Ночь";
				if ($user[pasbaf]==842) { $hp_cof=3;} else { $hp_cof=0;}
				}
				mysql_query("UPDATE `users` SET `hp` = `hp`+((".time()."-`fullhptime`)/60)*(`maxhp`/(15-{$hp_cof})), `fullhptime` = ".time()." WHERE  `hp` < `maxhp` AND `id` = '".$user[0]."' LIMIT 1;");
				}
		}
		else
		*/
		{
			 if(time()>$begin && time()<$end && $user[sex]!=1)
		            {
		            	 if (($HH>=9) and ($HH<21)) 
				{
				//echo "День";
				if ($user[pasbaf]==850 && $user['in_tower'] == 0) { $hp_cof=3;} 
					elseif ($user[pasbaf]==861 && $user['in_tower'] == 0) { $hp_cof=2; }				
						else { $hp_cof=0;}
				}
				else
				{
				//с 21:00 до 09:00
				//echo "Ночь";
				if ($user[pasbaf]==842 && $user['in_tower'] == 0) { $hp_cof=2;} else { $hp_cof=0;}
				}		            
				mysql_query("UPDATE `users` SET `hp` = `hp`+((".time()."-`fullhptime`)/60)*(`maxhp`/(10-{$hp_cof})), `fullhptime` = ".time()." WHERE  `hp` < `maxhp` AND `id` = '".$user[0]."' LIMIT 1;");
		            }
		            else
		            {
		            	 if (($HH>=9) and ($HH<21)) 
				{
				//echo "День";
				if ($user[pasbaf]==850 && $user['in_tower'] == 0) { $hp_cof=6;} 
					elseif ($user[pasbaf]==861 && $user['in_tower'] == 0) { $hp_cof=4; }				
						else { $hp_cof=0;}
				}
				else
				{
				//с 21:00 до 09:00
				//echo "Ночь";
				if ($user[pasbaf]==842 && $user['in_tower'] == 0) { $hp_cof=4;} else { $hp_cof=0;}
				}		            
				mysql_query("UPDATE `users` SET `hp` = `hp`+((".time()."-`fullhptime`)/60)*(`maxhp`/(20-{$hp_cof})), `fullhptime` = ".time()." WHERE  `hp` < `maxhp` AND `id` = '".$user[0]."' LIMIT 1;");
			    }
		}



	}
}


//for users in lab

$data = mysql_query("SELECT `id`, `fullhptime`, (SELECT `val` FROM  `labirint_var`  where `var`='poison_trap' and  `owner`=`users`.`id` ) as poison,`hp`,`maxhp`,`align`,`lab`, `sex`,  `pasbaf`, `in_tower`  FROM `users` WHERE `battle` = 0 AND `lab`>0 ;"); //and block=0
while($user = mysql_fetch_array($data)) {
	if (($user[poison] >0) and ($user[hp]>20) and ($user['lab']==4))
	{
	//3д лаба и хп норм
	mysql_query("UPDATE `users` SET `hp` = `hp`-20  WHERE `id` = '".$user[0]."' LIMIT 1;");
	}
else	
	if (($user[poison] >0) and ($user[hp]>2))
	{
	// in poison
	//старая лаба
	mysql_query("UPDATE `users` SET `hp` = `hp`-2  WHERE `id` = '".$user[0]."' LIMIT 1;");
	}
	else
    	  if (($user[hp] < $user[maxhp]) and ((int)$user[poison]==0) and ( ($user[lab]==1) OR ($user[lab]==3) )  )
		{
			if ((time()-$user[1])/60 > 0) {

				/*
				if (
					 ( ($user[align]==6) OR (($user[align]>=1) and ($user[align]<2)) ) and
					 (((int)date("H") > 8) && ((int)date("H") < 18))
				   )
		 		{
					if(time()>$begin && time()<$end && $user[sex]!=1)
            					{
            		
            						 if (($HH>=9) and ($HH<21)) 
							{
							//echo "День";
							if ($user[pasbaf]==850) { $hp_cof=2.1;} 
								elseif ($user[pasbaf]==861) { $hp_cof=1.4; }							
									else { $hp_cof=0;}
							}
							else
							{
							//с 21:00 до 09:00
							//echo "Ночь";
							if ($user[pasbaf]==842) { $hp_cof=1.4;} else { $hp_cof=0;}
							}
            		
						mysql_query("UPDATE `users` SET `hp` = `hp`+((".time()."-`fullhptime`)/60)*(`maxhp`/(7-{$hp_cof})), `fullhptime` = ".time()." WHERE  `hp` < `maxhp` AND `id` = '".$user[0]."' LIMIT 1;");
            					}
            				else
            				{
            		            				if (($HH>=9) and ($HH<21)) 
								{
								//echo "День";
								if ($user[pasbaf]==850) { $hp_cof=4.5;} 
								elseif ($user[pasbaf]==861) { $hp_cof=3; }								
									else { $hp_cof=0;}
								}
								else
								{
								//с 21:00 до 09:00
								//echo "Ночь";
								if ($user[pasbaf]==842) { $hp_cof=3;} else { $hp_cof=0;}
								}
						mysql_query("UPDATE `users` SET `hp` = `hp`+((".time()."-`fullhptime`)/60)*(`maxhp`/(15-{$hp_cof})), `fullhptime` = ".time()." WHERE  `hp` < `maxhp` AND `id` = '".$user[0]."' LIMIT 1;");
					}
				}
				else
				 */
				{
					if(time()>$begin && time()<$end && $user[sex]!=1)
            					{

			                     	 if (($HH>=9) and ($HH<21)) 
						{
						//echo "День";
						if ($user[pasbaf]==850 && $user['in_tower'] == 0) { $hp_cof=3;} 
							elseif ($user[pasbaf]==861 && $user['in_tower'] == 0) { $hp_cof=2; }						
								else { $hp_cof=0;}
						}
						else
						{
						//с 21:00 до 09:00
						//echo "Ночь";
						if ($user[pasbaf]==842 && $user['in_tower'] == 0) { $hp_cof=2;} else { $hp_cof=0;}
						}            		
						mysql_query("UPDATE `users` SET `hp` = `hp`+((".time()."-`fullhptime`)/60)*(`maxhp`/(10-{$hp_cof})), `fullhptime` = ".time()." WHERE  `hp` < `maxhp` AND `id` = '".$user[0]."' LIMIT 1;");
            					}
            				else
            				{
            			            	 if (($HH>=9) and ($HH<21)) 
						{
						//echo "День";
						if ($user[pasbaf]==850 && $user['in_tower'] == 0) { $hp_cof=6;} 
							elseif ($user[pasbaf]==861 && $user['in_tower'] == 0) { $hp_cof=4; }						
								else { $hp_cof=0;}
						}
						else
						{
						//с 21:00 до 09:00
						//echo "Ночь";
						if ($user[pasbaf]==842 && $user['in_tower'] == 0) { $hp_cof=4;} else { $hp_cof=0;}
						}            		
            		
						mysql_query("UPDATE `users` SET `hp` = `hp`+((".time()."-`fullhptime`)/60)*(`maxhp`/(20-{$hp_cof})), `fullhptime` = ".time()." WHERE  `hp` < `maxhp` AND `id` = '".$user[0]."' LIMIT 1;");
					}
				}

			}
		}
}




$data = mysql_query("SELECT `id`, `fullmptime`, pasbaf, in_tower  FROM `users` WHERE `mana` < `maxmana` AND `battle` = 0  ;"); //and block=0
while($user = mysql_fetch_array($data)) {
	if ((time()-$user[1])/60 > 0 && $user[1]) {
	
	
							if (($HH>=9) and ($HH<21)) 
						{
						//echo "День";
						if ($user[pasbaf]==850 && $user['in_tower'] == 0) { $mp_cof=9;} 
							elseif ($user[pasbaf]==861 && $user['in_tower'] == 0) { $mp_cof=6; }						
								else { $mp_cof=0;}
						}
						else
						{
						//с 21:00 до 09:00
						//echo "Ночь";
						if ($user[pasbaf]==842 && $user['in_tower'] == 0) { $mp_cof=6;} else { $mp_cof=0;}
						}
	
		mysql_query("UPDATE `users` SET `mana` = `mana`+((".time()."-`fullmptime`)/60)*(`maxmana`/(30-{$mp_cof})), `fullmptime` = '".time()."' WHERE `id` = '".$user[0]."' LIMIT 1;");
	}
}

mysql_query("UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = ".time()." WHERE  `hp` > `maxhp` AND `battle` = 0 ;");//and block=0 
mysql_query("UPDATE `users` SET `mana` = `maxmana`, `fullmptime` = ".time()." WHERE  (`mana` > `maxmana` OR `fullmptime` = 0) AND `battle` = 0 ");//and block=0;


///////// Восстановление энергии для наемников всех  убрать AND id in (132761384) = тестовый наем только
//надо добавить проверку на максимум+
mysql_query("UPDATE `users_clons` SET `energy` = `energy`+(if((@ENY:=(((UNIX_TIMESTAMP()-`fullentime`))*(`level`*5)/10800))>((`level`*5)-`energy`),((`level`*5)-`energy`),@ENY)) , `fullentime`=UNIX_TIMESTAMP()  WHERE `owner`>0 and `energy` < (`level`*5)  AND `battle` = 0 ;");


	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	//Записываем время окончания в другую переменную
	$tend = $mtime;
	//Вычисляем разницу
	$totaltime = ($tend - $tstart);
	//Выводим
	echo $totaltime;
	echo "\n";
	//addchp ('<font color=red>Внимание!</font> old cron_hp_time: '.$totaltime,'{[]}Bred{[]}');



lockDestroy("cron_hp_job");
?>