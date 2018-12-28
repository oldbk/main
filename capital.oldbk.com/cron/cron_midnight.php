#!/usr/bin/php
<?php
ini_set('display_errors','On');
$CITY_NAME='capitalcity';
include "/www/".$CITY_NAME.".oldbk.com/cron/init.php";
require_once("/www/".$CITY_NAME.".oldbk.com/config_ko.php");
if( !lockCreate("cron_midnight_job") ) {
    exit("Script already running.");
}
echo date("d.m.y H:i:s").'\r\n';
$gotime=time(); //время запуска

//чистим двевной	 счетчик в 0
mysql_query("UPDATE `variables` SET `value`=0 WHERE `var`='lab_key_count_d' ");	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// запуск в 00:03:00 - Чистка 
////////////////счетчики юзеровских боев для акции лето
//1. чистим из базы всех у кого актуальность по времени онлайна и боя - больше 24-х часов
mysql_query("delete from users_timer where (UNIX_TIMESTAMP(tbattle)<UNIX_TIMESTAMP()-86400) AND (UNIX_TIMESTAMP(ttime)<UNIX_TIMESTAMP()-86400)");
echo "\n Удалено старых:".mysql_affected_rows();


//обнуление всем дни в 0 кто неуспел получить  за день
mysql_query("update  users_timer set   cbattle=0, ctime=0,  cday=0 where  getflag=0;   ");
echo "\n Обнуление всем остальным :".mysql_affected_rows();

// счетчик дней больше 7 - шоб сбросить на первый день
//ищем укого был 6й день и выдали подарки более 24х часов назад
//ставим им все в 0 и дни тоже
//mysql_query("update  users_timer set   cbattle=0, ctime=0,  getflag=0 , cday=0 where  cday=6 and  getflag=1 AND   (UNIX_TIMESTAMP(tday)<=UNIX_TIMESTAMP()-86400)");
mysql_query("update  users_timer set   cbattle=0, ctime=0,  getflag=0 , cday=0 where  cday=6 and  getflag=1 "); //без времени т.е. всех переводим т.к. запускается 1 раз всутки
echo "\n Сброшено с 6го на 0 день:".mysql_affected_rows();
///перевод на новые сутки , того кто получил подарок за предыдущий день и время его выдачи было болле суток назад
//mysql_query("update  users_timer set   cbattle=0, ctime=0,  getflag=0 , cday=cday+1 where  getflag=1 AND   (UNIX_TIMESTAMP(tday)<=UNIX_TIMESTAMP()-86400)");


mysql_query("update  users_timer set   cbattle=0, ctime=0,  getflag=0 , cday=cday+1 where  getflag=1 "); //без времени т.е. всех переводим т.к. запускается 1 раз всутки
echo "\n Переведено на следующий день:".mysql_affected_rows();

//обнуление всем остальным
mysql_query("update  users_timer set   cbattle=0, ctime=0");
echo "\n Обнуление всем остальным :".mysql_affected_rows();
echo "-------------------------------\n";



lockDestroy("cron_midnight_job");
?>