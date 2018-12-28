<?php
if (!($_SESSION['uid'] >0)) {  header("Location: index.php"); die(); }
$magic = magicinf(1111);
$target = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$_POST['target']}' LIMIT 1;"));

	$KO_start_time20=mktime(16,0,0,6,1,2016);
	$KO_fin_time20 =mktime(23,59,59,6,30,2016);


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
elseif (($user['lab']>0) or ($user['room']==45) or ($user['room']==31) or ($user[in_tower] > 0) )  {
	echo "Не в этой локации!";
}
elseif ((time()>=$KO_start_time20)and (time()<=$KO_fin_time20))    { 	echo "Эта Магия временно не работает, до ".date("d.m.Y H:i",$KO_fin_time20);  }
elseif ($user[room]==60) { 	echo "Не в этой локации!"; }
elseif ($user[room]==999) { 	echo "Не в этой локации!"; }
elseif ($user['hidden'] > 0) {echo "На персонаже уже есть иллюзия..."; }
elseif (($user['room'] >=210)AND($user['room'] <299)) {  echo "Тут это не работает..."; }
elseif(!($target[id])) { echo "Такой персонаж не найден..."; }
elseif(!($target[bot]==0)) { echo "Такой персонаж не найден..."; }
elseif( ($target[klan]=='radminion') OR ($target['align'] > 2 && $target['align'] < 3) OR $target['bot'] > 0 OR ($target['deal'] > 0) OR ($target['id']==190672) OR ($target[klan]=='Adminion') OR ($target[klan]=='pal'))   { echo "Такой персонаж не найден..."; }
elseif($target[id_city]!=$user[id_city] ) { echo "Персонаж в другом городе..."; }
elseif ($target['ldate'] < (time()-60) ) { echo "Персонаж не в игре!!"; }
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

	    $idiluz=mt_rand(10,99).date("H").mt_rand(1,9).date("i").mt_rand(1,9).date("s");
	   $hiddenlog=$target[id].",".$target[login].",".$target[level].",".$target[align].",".$target[sex].",".$target[klan];
	   
		mysql_query("INSERT INTO `effects` (`owner`,`name`,`time`,`type`,`idiluz`,`add_info`) values ('".$user['id']."','Иллюзия невидимости',".(time()+$duration).",1111,'".$idiluz."','{$hiddenlog}');");
		mysql_query("UPDATE `users` SET `hidden`='{$idiluz}' , hiddenlog='{$hiddenlog}' where `id`='{$user['id']}';");
		echo "<font color=red><b>Вы Перевоплотились в {$target[login]} </b></font>";

		$bet=1;
		$sbet = 1;
		
		
	}
	else
	{
;
		echo "Свиток рассыпался в ваших руках...";
		$bet=1;
	}
?>
