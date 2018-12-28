<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

//print_r($_GET);

$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = ".$_SESSION['uid']." AND `id` = ".$_GET['use'].";"));

if((int)$rowm['magic'] > 0)
{
	$magic = mysql_fetch_array(mysql_query("SELECT * FROM `magic` WHERE `id` = ".$rowm['magic']." LIMIT 1;"));
}

	if($user[klan]!='radminion' and $user[klan]!='Adminion'){
		$begin1=mktime(0,0,1,01,14,2012);
	}

if($rowm[id]>0 && $magic[id]>0)
{	echo "<font color=red><B>";

	if($begin1<time())
	{
		if ($user['battle']>0)
		{ 
		echo "Не в бою!"; 
		}
		else
		 {
	       $gifts=array(
'Открытка'=>					array('name'=>'Открытка',			'maxdur'=>'1',	'cost'=>'1','owner'=>$user[id],'img'=>'2goda.gif',		'type'=>'200','magic'=>0,	'massa'=>1,'isrep'=>0,'prototype'=>0,  'otdel'=>72, 'add_time'=>time(),'present_text'=>'С годовщиной!',	'letter'=>'Поздравляем со 2 годовщиной ОлдБК! Желаем удачных и интересных боев, добрых друзей, отличного настроения и много удовольствия от Игры! С уважением, Администрация ОлдБК!',	'present'=>'Администрации'),
'Клонирование'=>				array('name'=>'Клонирование',			'maxdur'=>'10',	'cost'=>'5','owner'=>$user[id],'img'=>'mirror.gif',		'type'=>'12','magic'=>1616,	'massa'=>1,'isrep'=>0,'prototype'=>119,  'otdel'=>51, 'add_time'=>time(),'letter'=>'',	'present_text'=>'С годовщиной!',	'present'=>'Администрации'),
'Переманить клона'=>				array('name'=>'Переманить клона',		'maxdur'=>'10',	'cost'=>'5','owner'=>$user[id],'img'=>'antimirror.gif',		'type'=>'12','magic'=>1717,	'massa'=>1,'isrep'=>0,'prototype'=>120,  'otdel'=>51, 'add_time'=>time(),'letter'=>'',	'present_text'=>'С годовщиной!',	'present'=>'Администрации'),
'Заступиться'=>					array('name'=>'Заступиться',			'maxdur'=>'10',	'cost'=>'5','owner'=>$user[id],'img'=>'helpbattle_e.gif', 	'type'=>'12','magic'=>5353,	'massa'=>1,'isrep'=>0,'prototype'=>353,	 'otdel'=>51, 'add_time'=>time(),'letter'=>'',	'present_text'=>'С годовщиной!',	'present'=>'Администрации'),
'Кровавое нападение'=>				array('name'=>'Кровавое нападение',		'maxdur'=>'10',	'cost'=>'5','owner'=>$user[id],'img'=>'attackb.gif',		'type'=>'12','magic'=>4545,	'massa'=>1,'isrep'=>0,'prototype'=>134,  'otdel'=>51, 'add_time'=>time(),'letter'=>'',	'present_text'=>'С годовщиной!',	'present'=>'Администрации'),
'Торт'=>					array('name'=>'Праздничный Торт',		'maxdur'=>'20',	'cost'=>'5','owner'=>$user[id],'img'=>'tort.gif',		'type'=>'50','magic'=>8,	'massa'=>1,'isrep'=>0,'prototype'=>105,  'otdel'=>61, 'add_time'=>time(),'letter'=>'',	'present_text'=>'С годовщиной!',	'present'=>'Администрации')
			);



	      //  внутри подарка лежит - невидимка без требований, хилка 180 на 5 юзов, лечилка всех травм на 2 юза, ключ от лабы.
	      //  mysql_query('delete from oldbk.inventory where id='.$_GET['use'].' AND owner = '.$_SESSION['uid'].'; ');
	      	foreach ($gifts as $k=>$v)
            {
            	$sql='insert into oldbk.inventory
		         (name, 			maxdur,		cost,		owner,			img,			type,	magic,	letter,		massa,		isrep,			prototype,		otdel,	add_time,	present_text,			present)
		    	 VALUES

		    	 ("'.$v[name].'",'.$v[maxdur].','.$v[cost].','.$v[owner].',"'.$v[img].'",'.$v[type].','.$v[magic].',"'.$v[letter].'",'.$v[massa].','.$v[isrep].','.$v[prototype].','.$v[otdel].','.$v[add_time].',"'.$v[present_text].'","'.$v[present].'");';
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
	}
	else
	{		echo 'Праздник еще не наступил...';	}
	echo "</B></FONT>";
}
?>
