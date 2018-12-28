#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php";
if( !lockCreate("cron_victorina_job") ) {
    exit("Script already running.");
}

// Параметры викторины
// Имя бота
$botname="Баба Маня";
$bot_id=21438;
// Ожидание верного ответа
$bottime=40;
// Сумма разового выигрыша
$win=1;

//$room=16;
//$room_s(1,2,3,4,);

$room=mt_rand(1,4);


//include "/www/capitalcity.oldbk.com/connect.php";
//include "functions.php";
$starttime = time();

// Выбираем вопрос-ответ.
function SelectQuestion()	{
	$query      = mysql_query("SELECT count(*) AS count FROM victorina");
	$count      = mysql_fetch_assoc($query);
	$quest = mt_rand(1, $count['count']);
	//$quest=10;
	$data = mysql_fetch_array(mysql_query("SELECT * FROM `victorina` WHERE `id` = ".$quest." LIMIT 1;"));
	$data['Q'] = ObfuscateQ($data['Q']);
	return $data;
}

// Меняем буквы против тупого гугления.
function ObfuscateQ($q)	{
	//$letters = array("а", "е", "А", "о", "р", "и", "Е", "К", "М", "С", "с");
	//$replace = array("a", "e", "A", "o", "p", "u", "E", "K", "M", "C", "c");

	$letters = array("а", "е", "А", "о", "р", "и", "Е", "К", "М", "С", "с", "у", "З","з", "Н", "В", "О", "Р", "к", "п", "Т", "Х", "ь","б","х","й","ы","Ы","ш","Ш" ); 
	$replace = array("a", "e", "A", "0", "p", "u", "E", "K", "M", "C", "c", "y", "3","3","H", "B", "O", "P", "k", "n", "T", "X", "b","6", "x","u","bI","bI","I_I_I","I_I_I");

	$q=str_replace($letters,$replace,$q);
	return $q;
}

$vict=SelectQuestion();
$lastpost =time()-1; //надо чтоб захватить все время
$kontrol='';
$laastchatid=0;	
addchp("<font color=red>Внимание! Проводится викторина! Первому, кто правильно ответит на следующий вопрос будет выплачен 1 кредит. Время ожидания ответа 40 секунд. Ваши ответы пишите в чат <b>to [".$botname."]</b>. Внимание, вопрос: <b>".$vict['Q']."</b></font>",$botname,$room,$bot_city);



// Циклим бота на мониторинг чата
while(1)
	{
	 //$cha = file("/w/www/chat/chat.txt");
	 //foreach($cha as $k => $v)
	 if ($laastchatid>0) $kontrol=" or (id>'".$laastchatid."') ";
	$get_chat = mysql_query("SELECT UNIX_TIMESTAMP(`cdate`) as tt,text,id FROM oldbk.chat where (UNIX_TIMESTAMP(`cdate`) >= '{$lastpost}' ".$kontrol." ) and (city=".($bot_city+1)." ) and (room='{$room}' ) and owner>0 order by id");
	while($chatrow = mysql_fetch_array($get_chat))
        {
	$v=$chatrow[text];


		 preg_match("/:\[(.*)\]:\[(.*)\]:\[(.*)]:\[(.*)\]/",$v,$math);

 		 //fix for user_id
		$all_user_dat = explode(":|:",$math[2]);
		$chat_user_id = $all_user_dat[1]; //узер ид
		// а терь возвращаем все как было
		$math[2]=$all_user_dat[0];

		 if ($math[4]!=$room)	{continue;}
		 $math[3] = stripslashes($math[3]);
		 $pattern="/to \[".$botname."/";
		if ($lastpost < $math[1])
			{
	 		 if (preg_match($pattern,$math[3]))
				{
				 // Конвертим для правильной работы локали.
				 $ch=iconv("cp1251","utf-8", $math[3]);
				 $answ=iconv("cp1251","utf-8", $vict['A']);
		 		    if (preg_match("/".$answ."/sui",$ch))
					 {
					  // Строка содержит правильный ответ
					  addchp("to [".$math[2]."]: <font color=red>Поздравляю! ".$vict['A']." это правильный ответ!</font>",$botname,$room,$bot_city);

					  
						 // Пишем выигравшему в чат и перевод в дело. Перевод стандартного плана, что б анализатор считал.
						    $uid=mysql_fetch_array(mysql_query('SELECT * FROM users WHERE id ="'.$chat_user_id.'" LIMIT 1'));


					  // Даем бабла и пишем в таблицу топа викторины (на будущее может плюшки им)
					  if (mysql_query("UPDATE `users` set victorina=victorina+1 , money=money+".$win." where `id`='".$chat_user_id."'") and mysql_query("INSERT INTO oldbk.`top_victorina` (id, answers) VALUES (".$chat_user_id.", 1) ON DUPLICATE KEY UPDATE answers=answers+1;"))
					  	{

						    addchp ('<font color=red>Внимание!</font> Персонаж "'.$botname.'" передал вам <B>'.$win.' кр</B>.   ','{[]}'.$uid[login].'{[]}',$room,$bot_city);
                            $rec['owner']=$uid[id];
							$rec['owner_login']=$uid[login];
							$rec['owner_balans_do']=$uid['money'];
							$uid['money']+=$win;
							$rec['owner_balans_posle']=$uid['money'];
							$rec['target']=0;
							$rec['target_login']=$botname;
							$rec['type']=184;//креды от бота
							$rec['sum_kr']=$win;

                            if(olddelo==1)
							{
						    	mysql_query("INSERT INTO oldbk.`delo`(`id` , `author` ,`pers`, `text`, `type`, `date`) VALUES
						    	('','0','{$uid}',' Переведены кредиты ".$win." от \"".$botname."\" к \"".$uid['login']."\"','1','".time()."');");
           					}
						}
					  lockDestroy("cron_victorina_job");
					  exit();
					 }
				 else
				 	 {
					  // В строке нет правильного ответа
//					  addchp("Лошара!",$botname,$room);
					 }

				}
			}
		$chat_id=$chatrow[id];			
		}
	 $lastpost = $math[1];
	 $laastchatid=$chat_id;	 
	 $now=time();
	 if ($now > ($starttime+$bottime))
		{
		 addchp("<font color=red>Никто не угадал! Правильный ответ: ".$vict['A']."</font>",$botname,$room,$bot_city);
		 lockDestroy("cron_victorina_job");
		 exit();
		}
	 sleep(5);
	}
lockDestroy("cron_victorina_job");
?>