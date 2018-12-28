#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php"; //CAPLITAL CITY ONLY
if( !lockCreate("cron_weeks_job") ) {
    exit("Script already running.");
}
//запуск крона 1 раз в 00 каждый понедельник

	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$tstart = $mtime;
	$ckl=0;
	//ищем активный ивент
	$get_active=mysql_fetch_array(mysql_query("select * from ivents where stat=1"));
	if ($get_active['id']>0)
		{
		$last_a_id=$get_active['id'];
		unset($arr_ivents[$last_a_id]);
		$ckl=$get_active['cc'];
		//выключаем 
		echo "Выключим:".$get_active['nazv'];
		mysql_query("update ivents set stat=0, last_finish=NOW(), cc=cc+1 where id='{$get_active['id']}' ");
		if (mysql_affected_rows()>0)
			{
			echo " - ok";
			}
		echo "\n";
		}


	echo "Создаем новый ивент:";
	$get_new=mysql_fetch_array(mysql_query("select * from ivents where cc<='{$ckl}' and `off`=0 and last_finish<(now()-INTERVAL 15 DAY) order by cc , rand()  limit 1")); //исключаем из выборки ивенты которые заканчивались последние 15 дней т.е. их должно быть 3 шт. 1 из них окончился только что

	
		if ($get_new['id']>0)
		{
		echo $get_new['id']." - ".$get_new['nazv'];
		echo "\n";
		}
		else
		{
		echo " надо искать уже в новом цикле ";
		echo "\n";
			$get_new=mysql_fetch_array(mysql_query("select * from ivents where `off`=0 and  last_finish<(now()-INTERVAL 15 DAY) order by rand() limit 1")); //исключаем из выборки ивенты которые заканчивались последние 15 дней т.е. их должно быть 3 шт. 1 из них окончился только что		
			
			if ($get_new['id']>0)
				{
				echo $get_new['id']." - ".$get_new['nazv'];
				echo "\n";
				}
				else
				{
				echo "Ошибка рандомного ивента!";
				echo "\n";
				}
		}
		
		if ($get_new['id']>0)
		{
		//включаем
		mysql_query("update ivents set stat=1 where id='{$get_new['id']}' ");
		}

	/*
	делаем на данный момент 12 эвентов. движок надо написать так, чтоб можно было туда добавлять эвенты если изобретем другие и чтоб он легко вставился в код.
	эвенты циклично, 12 штук в цикле. рандом выбирает первый эвент и включает. затем выбирает из оставшихся 11 и включает, затем из 10ти.. и так пока не кончится цикл. потом все сначала.
	последние 2 эвента в цикле запоминаются и первые 2 недели в новом цикле они в рандоме не участвуют (чтоб не получилось повторения одно за другим)
	*/


	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	//Записываем время окончания в другую переменную
	$tend = $mtime;
	//Вычисляем разницу
	$totaltime = ($tend - $tstart);
	//Выводим
echo "Время работы:".$totaltime;
echo "\n";

lockDestroy("cron_weeks_job");
?>