<?
//создание расписания
session_start();
include "connect.php";
include "functions.php";
 if ($user['klan']=='radminion')
 {
 echo "<h3>Создаем расписание для арен!</h3>";
//1. получаем последнюю заявку
$get_last_rec=mysql_fetch_array(mysql_query("select * from place_zay order by start DESC limit 1;"));
 if ($get_last_rec[start]>0)
 {
 //если есть
 // то добавляем
 echo $get_last_rec[start] ;
 $last_rec_name=$get_last_rec[coment];
 $last_rec_time=$get_last_rec[start];
 echo "<br>";
 echo  $last_rec_name ;
 
  for ($k=1;$k<=20;$k++)
   {
   //echo "<br> $k ";
   //определяем следующее время

	if ($last_rec_name=='СВЕТ VS TЬМА')
	{
	$next_rec_time=$last_rec_time+(4*24*60*60);	
	mysql_query("INSERT INTO `oldbk`.`place_zay` SET `coment`='СВЕТ VS TЬМА [9-10]',`type`=61,`team1`='',`t1data`='',`team2`='',`t2data`='',`team3`='',`t3data`='',`start`='{$next_rec_time}',`timeout`=5,`t1min`=9,`t1max`=10,`t2min`=9,`t2max`=10,`t3min`=9,`t3max`=10,`level`=6,`podan`='00:00',`t1c`=30,`t2c`=30,`t3c`=30,`blood`=1,`active`=0,`z_curent1`=0,`z_curent2`=0,`z_curent3`=0;");
	$last_rec_name='СВЕТ VS TЬМА [9-10]';
	$last_rec_time=$next_rec_time;	
	}	
	else
	if ($last_rec_name=='СВЕТ VS TЬМА [9-10]')
	{
	$next_rec_time=$last_rec_time+(1*24*60*60);	
	mysql_query("INSERT INTO `oldbk`.`place_zay` SET `coment`='СВЕТ VS TЬМА [11-12]',`type`=61,`team1`='',`t1data`='',`team2`='',`t2data`='',`team3`='',`t3data`='',`start`='{$next_rec_time}',`timeout`=5,`t1min`=11,`t1max`=12,`t2min`=11,`t2max`=12,`t3min`=11,`t3max`=12,`level`=6,`podan`='00:00',`t1c`=30,`t2c`=30,`t3c`=30,`blood`=1,`active`=0,`z_curent1`=0,`z_curent2`=0,`z_curent3`=0;");
	$last_rec_name='СВЕТ VS TЬМА [11-12]';
	$last_rec_time=$next_rec_time;	
	}
	else
	if ($last_rec_name=='СВЕТ VS TЬМА [11-12]')
	{
	$next_rec_time=$last_rec_time+(1*24*60*60);	
	mysql_query("INSERT INTO `oldbk`.`place_zay` SET `coment`='СВЕТ VS TЬМА [13-14]',`type`=61,`team1`='',`t1data`='',`team2`='',`t2data`='',`team3`='',`t3data`='',`start`='{$next_rec_time}',`timeout`=5,`t1min`=13,`t1max`=14,`t2min`=13,`t2max`=14,`t3min`=13,`t3max`=14,`level`=6,`podan`='00:00',`t1c`=30,`t2c`=30,`t3c`=30,`blood`=1,`active`=0,`z_curent1`=0,`z_curent2`=0,`z_curent3`=0;");
	$last_rec_name='СВЕТ VS TЬМА [13-14]';
	$last_rec_time=$next_rec_time;	
	}
	else
	if ($last_rec_name=='СВЕТ VS TЬМА [13-14]')
	{
	$next_rec_time=$last_rec_time+(1*24*60*60);	
	mysql_query("INSERT INTO `oldbk`.`place_zay` SET `coment`='СВЕТ VS TЬМА',`type`=61,`team1`='',`t1data`='',`team2`='',`t2data`='',`team3`='',`t3data`='',`start`='{$next_rec_time}',`timeout`=5,`t1min`=7,`t1max`=21,`t2min`=7,`t2max`=21,`t3min`=7,`t3max`=21,`level`=6,`podan`='00:00',`t1c`=30,`t2c`=30,`t3c`=30,`blood`=1,`active`=0,`z_curent1`=0,`z_curent2`=0,`z_curent3`=0;");
	$last_rec_name='СВЕТ VS TЬМА';
	$last_rec_time=$next_rec_time;	
	}	
	/*else
	if ($last_rec_name=='СВЕТ VS TЬМА [12]')
	{
	$next_rec_time=$last_rec_time+(1*24*60*60);	
	mysql_query("INSERT INTO `oldbk`.`place_zay` SET `coment`='СВЕТ VS TЬМА [13]',`type`=61,`team1`='',`t1data`='',`team2`='',`t2data`='',`team3`='',`t3data`='',`start`='{$next_rec_time}',`timeout`=5,`t1min`=13,`t1max`=13,`t2min`=13,`t2max`=13,`t3min`=13,`t3max`=13,`level`=6,`podan`='00:00',`t1c`=30,`t2c`=30,`t3c`=30,`blood`=1,`active`=0,`z_curent1`=0,`z_curent2`=0,`z_curent3`=0;");
	$last_rec_name='СВЕТ VS TЬМА [13]';
	$last_rec_time=$next_rec_time;	
	}
	else
	if ($last_rec_name=='СВЕТ VS TЬМА [13]')
	{
	$next_rec_time=$last_rec_time+(1*24*60*60);	
	mysql_query("INSERT INTO `oldbk`.`place_zay` SET `coment`='СВЕТ VS TЬМА [13-14]',`type`=61,`team1`='',`t1data`='',`team2`='',`t2data`='',`team3`='',`t3data`='',`start`='{$next_rec_time}',`timeout`=5,`t1min`=13,`t1max`=14,`t2min`=13,`t2max`=14,`t3min`=13,`t3max`=14,`level`=6,`podan`='00:00',`t1c`=30,`t2c`=30,`t3c`=30,`blood`=1,`active`=0,`z_curent1`=0,`z_curent2`=0,`z_curent3`=0;");
	$last_rec_name='СВЕТ VS TЬМА [13-14]';
	$last_rec_time=$next_rec_time;	
	}	
	else
	if ($last_rec_name=='СВЕТ VS TЬМА [13-14]')
	{
	$next_rec_time=$last_rec_time+(1*24*60*60);	
	mysql_query("INSERT INTO `oldbk`.`place_zay` SET `coment`='СВЕТ VS TЬМА',`type`=61,`team1`='',`t1data`='',`team2`='',`t2data`='',`team3`='',`t3data`='',`start`='{$next_rec_time}',`timeout`=5,`t1min`=7,`t1max`=21,`t2min`=7,`t2max`=21,`t3min`=7,`t3max`=21,`level`=6,`podan`='00:00',`t1c`=30,`t2c`=30,`t3c`=30,`blood`=1,`active`=0,`z_curent1`=0,`z_curent2`=0,`z_curent3`=0;");
	$last_rec_name='СВЕТ VS TЬМА';
	$last_rec_time=$next_rec_time;	
	}
	*/	
	
   }
 echo "<br>Добавили $k арен!";
 }
else
	{
	echo "ERROR! нет первой записи расписания (";
	}

}
else
	{
	echo "что-то не то :)";
	}
?>