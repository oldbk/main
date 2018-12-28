<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

//print_r($_GET);

$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = ".$_SESSION['uid']." AND `id` = ".$_GET['use'].";"));

if((int)$rowm['magic'] > 0)
{
	$magic = mysql_fetch_array(mysql_query("SELECT * FROM `magic` WHERE `id` = ".$rowm['magic']." LIMIT 1;"));
}

	if($user[klan]!='radminion' and $user[klan]!='Adminion'){
		$begin1=mktime(0,0,1,01,01,2012);
	}

if($rowm[id]>0 && $magic[id]>0)
{	echo "<font color=red><B>";

	if($begin1<time())
	{
		if ($user['battle']>0)
		{ echo "Не в бою!"; }
		else
		 {
	       $gifts=array(
	        'Лечение травм'=>				array('name'=>'Лечение травм',				'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'cure3.gif',			'type'=>'12','magic'=>1021,	'massa'=>1,'isrep'=>0,'prototype'=>125,	 'otdel'=>5,	'present_text'=>'С Новым 2012 Годом!',	'present'=>'подарок от Администрации'),
			'Невидимость'=>					array('name'=>'Невидимость',				'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'hidden.gif',			'type'=>'12','magic'=>1020,	'massa'=>1,'isrep'=>0,'prototype'=>301,	 'otdel'=>5,	'present_text'=>'С Новым 2012 Годом!',	'present'=>'подарок от Администрации'),
			'Восстановление энергии 180HP'=>array('name'=>'Восстановление энергии 180HP','maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'cure180.gif', 		'type'=>'12','magic'=>1022,	'massa'=>1,'isrep'=>0,'prototype'=>5205, 'otdel'=>51,	'present_text'=>'С Новым 2012 Годом!',	'present'=>'подарок от Администрации'),
			'Ключ от Лабиринта'=>			array('name'=>'Ключ от Лабиринта',			'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'labticket.gif',		'type'=>'12','magic'=>66, 	'massa'=>1,'isrep'=>0,'prototype'=>4001, 'otdel'=>52,	'present_text'=>'С Новым 2012 Годом!',	'present'=>'подарок от Администрации'),
			'Пропуск в Руины'=>				array('name'=>'Пропуск в Руины',			'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'ruin_propusk.gif',	'type'=>'12','magic'=>228, 	'massa'=>1,'isrep'=>0,'prototype'=>4015, 'otdel'=>52,	'present_text'=>'С Новым 2012 Годом!',	'present'=>'подарок от Администрации'),
			'Обед воина'=>					array('name'=>'Обед воина',					'maxdur'=>'10',	'cost'=>'5','owner'=>$user[id],'img'=>'obed_4e.gif',		'type'=>'50','magic'=>3031, 'massa'=>1,'isrep'=>0,'prototype'=>34001,'otdel'=>61,	'present_text'=>'С Новым 2012 Годом!',	'present'=>'подарок от Администрации'),
			'Кровавое нападение Вендетта'=>	array('name'=>'Кровавое нападение Вендетта','maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'attackbv.gif',		'type'=>'12','magic'=>2525, 'massa'=>1,'isrep'=>0,'prototype'=>2525, 'otdel'=>5,	'present_text'=>'С Новым 2012 Годом!',	'present'=>'подарок от Администрации'),
			'Защита от травм'=>				array('name'=>'Защита от травм',			'maxdur'=>'1',	'cost'=>'1','owner'=>$user[id],'img'=>'no_cure2.gif',		'type'=>'12','magic'=>555,  'massa'=>1,'isrep'=>0,'prototype'=>55555, 'otdel'=>51,	'present_text'=>'С Новым 2012 Годом!',	'present'=>'подарок от Администрации'),
			'Снежок'=>						array('name'=>'Снежок',						'maxdur'=>'5',	'cost'=>'5','owner'=>$user[id],'img'=>'snezhok.gif',		'type'=>'12','magic'=>1001, 'massa'=>1,'isrep'=>0,'prototype'=>5276, 'otdel'=>5,	'present_text'=>'С Новым 2012 Годом!',	'present'=>'подарок от Администрации')
			);


	$txt_t='';
	      //  внутри подарка лежит - невидимка без требований, хилка 180 на 5 юзов, лечилка всех травм на 2 юза, ключ от лабы.
	      //  mysql_query('delete from oldbk.inventory where id='.$_GET['use'].' AND owner = '.$_SESSION['uid'].'; ');
	      	foreach ($gifts as $k=>$v)
            {
            	$sql='insert into oldbk.inventory
		         (name, 			maxdur,		cost,		owner,			img,			type,	magic,			massa,		isrep,			prototype,		otdel,		present_text,			present)
		    	 VALUES

		    	 ("'.$v[name].'",'.$v[maxdur].','.$v[cost].','.$v[owner].',"'.$v[img].'",'.$v[type].','.$v[magic].','.$v[massa].','.$v[isrep].','.$v[prototype].','.$v[otdel].',"'.$v[present_text].'","'.$v[present].'");';
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
                $txt_t.=$k.', ';
            }
            $txt_t=substr($txt_t, 0,-2);

			echo "Вы открыли подарок! <br>
			Вы получили: ".$txt_t;
			$bet=1;
			$sbet = 1;
		 }
	}
	else
	{		echo 'Рано... Еще не наступила Новогодняя ночь...';	}
	echo "</B></FONT>";
}
?>
