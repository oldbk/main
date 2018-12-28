<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

	$KO_start_time20=mktime(16,0,0,6,1,2016);
	$KO_fin_time20 =mktime(23,59,59,6,30,2016);


if ($get_mag)
	{
	$magic =$get_mag;
	}
	else
	if ($tabil[magic])
	{
	$magic = magicinf($tabil[magic]);	
	}


 if (!$magic)
 	{
	$magic = magicinf(1);
	}

 if (!$magic['time']) {
	$tmp = magicinf(1);
	$magic['time'] = $tmp['time'];
 }

//$effect = mysql_fetch_array(mysql_query("SELECT `time` FROM `effects` WHERE `owner` = '{$user['id']}' and `type` = '200' LIMIT 1;"));

	if ($klan_abil != 1)
	{
		$int=$magic['chanse'] + ($user['intel'] - 1)*3;
		if ($int>99){$int=101;}

	}
	elseif($klan_abil==1)    //если юзается как абилка (наследие от клана, шанс 100%)
	{
		$int=101;
	}
	else {
		$int=0;
	}



if ($user['battle'] > 0) {echo "Не в бою...";}
elseif ($user['zayavka'] > 0) {echo "Не в заявке...";}
elseif (($user['lab']>0) or ($user['room']==45) or ($user['room']==10000) or ($user['room']==31) or ($user[in_tower] > 0) )  {
	echo "Не в этой локации!";
}
elseif ((time()>=$KO_start_time20)and (time()<=$KO_fin_time20))    { 	echo "Эта Магия временно не работает, до ".date("d.m.Y H:i",$KO_fin_time20);  }
elseif ($user[room]==60) { 	echo "Не в этой локации!"; }
elseif ($user[room]==999) { 	echo "Не в этой локации!"; }
elseif ($user['hidden'] > 0) {echo "На персонаже уже есть иллюзия..."; }
elseif (($user['room'] >=210)AND($user['room'] <299)) {  echo "Тут это не работает..."; }
elseif (rand(1,100) < $int) {

	     $duration = $magic['time']*60;
	     if (($rowm['prototype']==11302) OR ($rowm['prototype']==11303) or ($magic['id']==10101) )
	      {
	      //11302 Большой свиток «Невидимость»
	      //11303 Совершенный свиток «Невидимость»
	      //независимо от кол-ва мудрости, всегда статично дают 2 часа эффекта.
	      //10101 - новая абилка
	      	if ($magic['id']==10101)
	      		{
	      		$rowm['name']=$magic['name'];
	      		$rowm['img']=$magic['img'];
	      		}

	      }
	      elseif ($user[mudra]>5)
	      	{
	      	/*
	      	Bred: 120 базово + ( mudra*10 но не более 120)
			Котька: да
	      	*/
	      	$addd=($user[mudra]-5)*10;
	      	if ($addd>120) {$addd=120;}
	      	$duration=$duration+($addd*60);
	      	}
	      
		//addch("<img src=i/magic/hidden.gif>Персонаж &quot;{$user['login']}&quot; превратился в невидимку..");

	//$md=date("His");
	//$ren=rand(1000,9999);
	    $idiluz=mt_rand(10,99).date("H").mt_rand(1,9).date("i").mt_rand(1,9).date("s");
	    
	    $add_info='';
	    
	    if ($user['id']==14897)
				{
				print_r($magic);
				}
	    
	    if (($rowm['name']!='') and ($rowm['includemagic']==0))
	    	{
		$add_info=$rowm['name']."::".$rowm['img'];
		}
		else
		   if (($rowm['name']!='') and ($rowm['includemagic']>0))
		    	{
			$add_info="Иллюзия невидимости::hidden.gif";
			}
		
		

		mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`idiluz`,`add_info`) values ('".$user['id']."','Иллюзия невидимости',".(time()+$duration).",200,'".$idiluz."', '".$add_info."');");
		mysql_query("UPDATE `users` SET `hidden`='{$idiluz}' where `id`='{$user['id']}';");
		echo "<font color=red><b>Вы подверглись иллюзии...</b></font>";

		$bet=1;
		$sbet = 1;

		
		//fclose($f);
	}
	else
	{
		echo "Свиток рассыпался в ваших руках...";
		$bet=1;
	}
?>
