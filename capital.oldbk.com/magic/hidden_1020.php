<?php
$magic = magicinf(1020);
//$effect = mysql_fetch_array(mysql_query("SELECT `time` FROM `effects` WHERE `owner` = '{$user['id']}' and `type` = '200' LIMIT 1;"));

$int=101;

	$KO_start_time20=mktime(16,0,0,6,1,2016);
	$KO_fin_time20 =mktime(23,59,59,6,30,2016);


if ($user['battle'] > 0) {echo "Не в бою...";}
elseif ($user['zayavka'] > 0) {echo "Не в заявке...";}
elseif (($user['lab']>0) or ($user['room']==45) or ($user['room']==31) or ($user[in_tower] > 0) )  {
	echo "Не в этой локации!";
}
elseif ((time()>=$KO_start_time20)and (time()<=$KO_fin_time20))    { 	echo "Эта Магия временно не работает, до ".date("d.m.Y H:i",$KO_fin_time20);  }
elseif ($user[room]==60) { 	echo "Не в этой локации!"; }
elseif ($user[room]==999) { 	echo "Не в этой локации!"; }
elseif ($user['hidden'] > 0) {echo "На персонаже уже есть иллюзия..."; }
elseif (($user['room'] >=210)AND($user['room'] <299)) { echo "Тут это не работает..."; }
elseif (rand(1,100) < $int) {

      $duration = $magic['time']*60;
        if ($user[mudra]>5)
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
$idiluz=rand(10,99).date("H").rand(1,9).date("i").rand(1,9).date("s");

			mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`idiluz`) values ('".$user['id']."','Иллюзия невидимости',".(time()+$duration).",200,'".$idiluz."');");
			mysql_query("UPDATE `users` SET `hidden`='{$idiluz}' where `id`='{$user['id']}';");
			echo "<font color=red><b>Вы подверглись иллюзии...</b></font>";
			$bet=1;
			$sbet = 1;

	
} else {
	
				echo "Свиток рассыпался в ваших руках...";
				$bet=1;
			}
?>
