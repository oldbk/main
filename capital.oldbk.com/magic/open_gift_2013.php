<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = ".$_SESSION['uid']." AND `id` = ".$_GET['use'].";"));

if((int)$rowm['magic'] > 0) {
	$magic = magicinf($rowm['magic']);
}


include "ny_events.php";
	
if($rowm[id]>0 && $magic[id]>0) {
	echo "<font color=red><B>";

	if(date("m") == 1 || ADMIN) {
		if ($user['battle']>0) { 
			echo "Не в бою!"; 
		} else {
			$today=date('j');
			$last_days=30-$today;
			if($last_days<1){$last_days=1;}
			if (date("m") == 12) {
				$godendo = mktime(23,59,59,1,30,date("Y")+1);
			} else {
				if (ADMIN) {
					$godendo = mktime(23,59,59,1,30,date("Y")+1);
				} else {
					$godendo = mktime(23,59,59,1,30,date("Y"));
				}
			}

			$gifts=array(
			'Лечение травм'=>				array('name'=>'Лечение травм',					'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'cure3.gif',		'img_big' =>'', 		'rareitem'=>0, 'type'=>'12','magic'=>1021,	'massa'=>1,	'isrep'=>0,'prototype'=>125,		'add_time'=>time(),	'goden'=>$last_days,	'dategoden'=> $godendo,	'otdel'=>5,	'letter'=>'Поздравляем с Новым 2019 Годом! Желаем любви, удачи во всех делах, много радости и счастья! Счастливого Нового Года! Администрация ОлдБК.',	'present'=>'Новый Год'),
			'Невидимость'=>					array('name'=>'Невидимость',					'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'hidden.gif',		'img_big' =>'', 		'rareitem'=>0,'type'=>'12','magic'=>1020,	'massa'=>1,	'isrep'=>0,'prototype'=>301,		'add_time'=>time(),	'goden'=>$last_days,	'dategoden'=> $godendo,	'otdel'=>5,	'letter'=>'Поздравляем с Новым 2019 Годом! Желаем любви, удачи во всех делах, много радости и счастья! Счастливого Нового Года! Администрация ОлдБК.',	'present'=>'Новый Год'),
			'Совершенный свиток «Восстановление 360HP»'=>	array('name'=>'Совершенный свиток «Восстановление 360HP»',	'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'cure360HP3_2.gif', 	'img_big' =>'cure360HP3.gif', 	'rareitem'=>3,'type'=>'12','magic'=>278,	'massa'=>1,	'isrep'=>0,'prototype'=>200278,		'add_time'=>time(),	'goden'=>$last_days,	'dategoden'=> $godendo,	'otdel'=>51,	'letter'=>'Поздравляем с Новым 2019 Годом! Желаем любви, удачи во всех делах, много радости и счастья! Счастливого Нового Года! Администрация ОлдБК.',	'present'=>'Новый Год'),
			'Совершенный свиток «Восстановление 360 маны»'=>array('name'=>'Совершенный свиток «Восстановление 360 маны»',	'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'cure360mana3_2.gif', 	'img_big' =>'cure360mana3.gif', 'rareitem'=>3,'type'=>'12','magic'=>310321,	'massa'=>1,	'isrep'=>0,'prototype'=>321,		'add_time'=>time(),	'goden'=>$last_days,	'dategoden'=> $godendo,	'otdel'=>51,	'letter'=>'Поздравляем с Новым 2019 Годом! Желаем любви, удачи во всех делах, много радости и счастья! Счастливого Нового Года! Администрация ОлдБК.',	'present'=>'Новый Год'),
			'Большой свиток «Пропуск в Лабиринт»'=>		array('name'=>'Большой свиток «Пропуск в Лабиринт»',		'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'labticket2_2.gif',	'img_big' =>'labticket2.gif', 	'rareitem'=>2,'type'=>'12','magic'=>66, 	'massa'=>1,	'isrep'=>0,'prototype'=>4007,		'add_time'=>time(),	'goden'=>$last_days,	'dategoden'=> $godendo,	'otdel'=>52,	'letter'=>'Поздравляем с Новым 2019 Годом! Желаем любви, удачи во всех делах, много радости и счастья! Счастливого Нового Года! Администрация ОлдБК.',	'present'=>'Новый Год'),
			'Большой свиток «Пропуск в Руины»'=>		array('name'=>'Большой свиток «Пропуск в Руины»',		'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'ruineticket2_2.gif',	'img_big' =>'ruineticket2.gif', 'rareitem'=>0,'type'=>'12','magic'=>228, 	'massa'=>1,	'isrep'=>0,'prototype'=>4019,		'add_time'=>time(),	'goden'=>$last_days,	'dategoden'=> $godendo,	'otdel'=>52,	'letter'=>'Поздравляем с Новым 2019 Годом! Желаем любви, удачи во всех делах, много радости и счастья! Счастливого Нового Года! Администрация ОлдБК.',	'present'=>'Новый Год'),
			'Большой ужин дракона'=>			array('name'=>'Большой ужин дракона',				'maxdur'=>'10',	'cost'=>'5','owner'=>$user[id],'img'=>'dinner_dragon2.gif',	'img_big' =>'', 		'rareitem'=>0,'type'=>'50','magic'=>3053, 	'massa'=>1,	'isrep'=>0,'prototype'=>33053,		'add_time'=>time(),	'goden'=>$last_days,	'dategoden'=> $godendo,	'otdel'=>61,	'letter'=>'Поздравляем с Новым 2019 Годом! Желаем любви, удачи во всех делах, много радости и счастья! Счастливого Нового Года! Администрация ОлдБК.',	'present'=>'Новый Год'),
			'Кровавое нападение Вендетта'=>			array('name'=>'Кровавое нападение Вендетта',			'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'attackbv.gif',		'img_big' =>'', 		'rareitem'=>0,'type'=>'12','magic'=>5007002, 	'massa'=>1,	'isrep'=>0,'prototype'=>2525,		'add_time'=>time(),	'goden'=>$last_days,	'dategoden'=> $godendo,	'otdel'=>5,	'letter'=>'Поздравляем с Новым 2019 Годом! Желаем любви, удачи во всех делах, много радости и счастья! Счастливого Нового Года! Администрация ОлдБК.',	'present'=>'Новый Год'),
			'Защита от травм на бой'=>			array('name'=>'Защита от травм на бой',				'maxdur'=>'10',	'cost'=>'5','owner'=>$user[id],'img'=>'no_cure2.gif',		'img_big' =>'', 		'rareitem'=>0,'type'=>'12','magic'=>556556,  	'massa'=>1,	'isrep'=>0,'prototype'=>55556,		'add_time'=>time(),	'goden'=>$last_days,	'dategoden'=> $godendo,	'otdel'=>51,	'letter'=>'Поздравляем с Новым 2019 Годом! Желаем любви, удачи во всех делах, много радости и счастья! Счастливого Нового Года! Администрация ОлдБК.',	'present'=>'Новый Год'),
			'Снежок'=>					array('name'=>'Снежок',						'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'snezhok.gif',		'img_big' =>'', 		'rareitem'=>0,'type'=>'12','magic'=>1001, 	'massa'=>1,	'isrep'=>0,'prototype'=>5276,		'add_time'=>time(),	'goden'=>$last_days,	'dategoden'=> $godendo,	'otdel'=>5,	'letter'=>'Поздравляем с Новым 2019 Годом! Желаем любви, удачи во всех делах, много радости и счастья! Счастливого Нового Года! Администрация ОлдБК.',	'present'=>'Новый Год'),
			'С Новым годом!'=>				array('name'=>'С Новым годом!',					'maxdur'=>'1',	'cost'=>'5','owner'=>$user[id],'img'=>'gift_ny2019card.gif',		'img_big' =>'', 		'rareitem'=>0,'type'=>'200','magic'=>0, 	'massa'=>0.1,	'isrep'=>0,'prototype'=>15276,		'add_time'=>time(),	'goden'=>'',		'dategoden'=> '',		'otdel'=>72,	'letter'=>'Поздравляем с Новым 2019 Годом! Желаем любви, удачи во всех делах, много радости и счастья! Счастливого Нового Года! Администрация ОлдБК.',	'present'=>'Новый Год'),
			'Ловушка'=>					array('name'=>'Ловушка',					'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'trap_e.gif',		'img_big' =>'', 		'rareitem'=>0,'type'=>'12','magic'=>5007001, 	'massa'=>0.2,	'isrep'=>0,'prototype'=>40000000,	'add_time'=>time(),	'goden'=>$last_days,	'dategoden'=> $godendo,	'otdel'=>5,	'letter'=>'Поздравляем с Новым 2019 Годом! Желаем любви, удачи во всех делах, много радости и счастья! Счастливого Нового Года! Администрация ОлдБК.',	'present'=>'Новый Год')
			
			);
			
			
			
			$txt='';
			foreach ($gifts as $k=>$v) {
				$sql='insert into oldbk.inventory
				(name, 			maxdur,		cost,	owner,		img,	type,		magic,		massa,	isrep,		prototype,	otdel,		letter,		present, 	dategoden,		goden,		add_time, img_big, rareitem)
				VALUES
				
				("'.$v[name].'",'.$v[maxdur].','.$v[cost].','.$v[owner].',"'.$v[img].'",'.$v[type].','.$v[magic].','.$v[massa].','.$v[isrep].','.$v[prototype].','.$v[otdel].',"'.$v[letter].'","'.$v[present].'","'.$v[dategoden].'","'.$v[goden].'","'.$v[add_time].'","'.$v[img_big].'","'.$v[rareitem].'");';
				// echo $sql.'<br>';
				mysql_query($sql);
				
				
				$it_id[id]=mysql_insert_id();
				$it_id[idcity]=$user[id_city];
				$dressid = get_item_fid($it_id);
				
				$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user[money];
				$rec['owner_balans_posle']=$user[money];
				$rec['target']=0;
				$rec['target_login']='Вскрытие подарка.';
				$rec['type']=300;//
				$rec['sum_kr']=0;
				$rec['sum_ekr']=0;
				$rec['sum_kom']=0;
				$rec['item_id']=$dressid;
				$rec['item_name']=$v[name];
				$rec['item_count']=1;
				$rec['item_type']=$v[type];
				$rec['item_cost']=$v[cost];
				$rec['item_dur']=0;
				$rec['item_maxdur']=$v[maxdur];
				$rec['item_ups']=0;
				$rec['item_unic']=0;
				$rec['item_incmagic']='';
				$rec['item_incmagic_count']='';
				
				add_to_new_delo($rec);
				$txt.=$k.', ';
			}
			$txt=substr($txt, 0,-2);
			
			echo "Вы открыли подарок! <br>
			Вы получили: ".$txt;
			$bet=1;
			$sbet = 1;
		}
	} else {		echo 'Рано... Еще не наступила Новогодняя ночь...';	}
	echo "</B></FONT>";
}
?>