<?
	session_start();
	if (!($_SESSION['uid'] >0))
	{
	 header("Location: index.php");
	 die();
	}
	include "connect.php";
	include "functions.php";
	
	if ($user['room']!=44) { die(); }

die();


function showitem_edit ($row, $orden = 0, $check_price = false,$color='',$act='',$rep_rate=0, $priv=0,$retdata = 0) {
	global $user, $klan_ars_back, $giftars, $IM_glava, $anlim_show, $anlim_items, 	$nodress  ;
	$vau4 = array(100005,100015,100020,100025,100040,100100,100200,100300);
	$unikrazdel = array(6,2,21,22,23,24,3,4,41,42);

	$ret = "";

	if($row['add_pick'] != '' && $row['pick_time']>time()) {
       		$row['img'] = $row['add_pick'];
	}

	if (($row['type'] == 30) and ($row['owner']==$user['id']) ) // смотрим на свой предмет
	{
	// показываем  кто руну надо апнуть
		if ($row['ups'] >= $row['add_time']) 
		{
		$mig=explode(".",$row['img']);
		$row['img']=$mig[0]."_up.".$mig[1];
		}
	}

	if($row['dategoden'] && $row['dategoden'] <= time()) {
		destructitem($row['id']);
		if($row['setsale']>0) {
			mysql_query("DELETE FROM oldbk.`comission_indexes` WHERE id_item = '".$row[id]."' LIMIT 1");
		}

		if ($row['labonly']==0) {
			$rec['owner']=$user['id'];
			$rec['owner_login']=$user[login];
			$rec['owner_balans_do']=$user['money'];
			$rec['owner_balans_posle']=$user['money'];
			$rec['target']=0;
			$rec['target_login']='Срок годности';
			$rec['type']=35;
			$rec['sum_kr']=0;
			$rec['sum_ekr']=0;
			$rec['sum_kom']=0;
			$rec['item_id']=get_item_fid($row);
			$rec['item_name']=$row['name'];
			$rec['item_count']=1;
			$rec['item_type']=$row['type'];
			$rec['item_cost']=$row['cost'];
			$rec['item_dur']=$row['duration'];
			$rec['item_maxdur']=$row['maxdur'];
			$rec['item_ups']=$row['ups'];
			$rec['item_unic']=$row['unik'];
			$rec['item_incmagic']=$row['includemagicname'];
			$rec['item_incmagic_count']=$row['includemagicuses'];
			$rec['item_arsenal']='';
			add_to_new_delo($rec); //юзеру
		}
	}

	$magic = magicinf ($row['magic']);

	$incmagic = magicinf($row['includemagic']);
	$incmagic['name'] = $row['includemagicname'];
	$incmagic['cur'] = $row['includemagicdex'];
	$incmagic['max'] = $row['includemagicmax'];
	$incmagic['uses'] = $row['includemagicuses'];

	if(!$magic) {
		$magic['chanse'] = $incmagic['chanse'];
		$magic['time'] = $incmagic['time'];
		$magic['targeted'] = $incmagic['targeted'];
	}

	$artinfo = "";
	$issart = 0;
	if ((($row['ab_uron'] > 0 || $row['ab_bron'] > 0 || $row['ab_mf'] > 0 || $row['art_param'] != "")  AND $row['type'] != 30) || ($row['type'] == 30 && $row['up_level'] > 5)) {
		if ($row['type'] != 30) $artinfo = ' <IMG SRC="http://i.oldbk.com/i/artefact.gif" WIDTH="18" HEIGHT="16" BORDER=0 TITLE="Артефакт" alt="Артефакт"> ';
		$issart = 1;
	}

	if ($row['prototype'] == 1236) {
		$act = '<a target="_blank" href="printticket.php?id='.$row['id'].'">Распечатать</a>';
	}


	if (!$row[GetShopCount()] || $row['inv']==1) {
		$ch=0;

		if($row['type'] < 12) {
			$ch=1;
		} elseif($row['type'] == 27 || $row['type'] == 28) {
			$ch=2;
		}
		$ret .= "<TR bgcolor=".$color.">";
		$ret .= "<TD align=center width=150 ";
		if ($ch > 0) {
			if (($row['maxdur']-2)<=$row['duration'] && $row['duration'] > 2) {
				$ret .= " style=\"background-image:url('http://i.oldbk.com/i/blink.gif');\" ";
			}
		}
		$ret .= " >";

		$dr=shincmag($row);

		if ($row['prototype']>=2013001 && $row['prototype']<=2013004) {
			$ret .= "<a href='http://oldbk.com/encicl/?/laretz.html' target=_blank><img ";
			if ($ch == 1) {
				$ret .= "style=\"background-image:url(http://i.oldbk.com/i/sh/vstr1.gif); background-repeat: no-repeat; background-position: 3px ".($dr['res_y']*$dr['koef'])."px;\"";
			}
			$ret .= " src='http://i.oldbk.com/i/sh/".$row['img']."'></a><BR>";
		} else {
			$ret .= "<img ";
			if ($ch == 1) {
				$ret .= "style=\"background-image:url(http://i.oldbk.com/i/sh/vstr1.gif); background-repeat: no-repeat; background-position: 3px ".($dr['res_y']*$dr['koef'])."px;\"";
			}
			$ret .= " src='http://i.oldbk.com/i/sh/".$row['img']."'><BR>";
		}

		if(isset($row['idcity'])) {
			if ($row['showbill'] == true) {
				$sh_id = "Заказ №: ".$row['id'];
			} else {
				$sh_id = get_item_fid($row);
			}
			$ret .= "<center><small>(".$sh_id.")</small></center><br>";
		}

		if($row['chk_arsenal'] == 0) {
			$ch_al=$user['align'];
			if($user['klan']=='pal') {
				$ch_al = 6; //фикс для палов
				$ch_al2 = 1 ; //фикс для палов
			}
			else
			{
			$ch_al2=0;
			}

			if ( (	($user['sila'] >= $row['nsila']) &&
				($user['lovk'] >= $row['nlovk']) &&
				($user['inta'] >= $row['ninta']) &&
				($user['vinos'] >= $row['nvinos']) &&
				($user['intel'] >= $row['nintel']) &&
				($user['mudra'] >= $row['nmudra']) &&
				($user['level'] >= $row['nlevel']) &&
				(((int)$ch_al == $row['nalign']) OR ($row['nalign'] == 0) OR ($user[align]==5) OR ($ch_al2 == $row['nalign']) ) &&
				($user['noj'] >= $row['nnoj']) &&
				($user['topor'] >= $row['ntopor']) &&
				($user['dubina'] >= $row['ndubina']) &&
				($user['mec'] >= $row['nmech']) &&
				($user['mfire'] >= $row['nfire']) &&
				($user['mwater'] >= $row['nwater']) &&
				($user['mair'] >= $row['nair']) &&
				($user['mearth'] >= $row['nearth']) &&
				($user['mlight'] >= $row['nlight']) &&
				($user['mgray'] >= $row['ngray']) &&
				($user['mdark'] >= $row['ndark']) &&
				($row['type'] < 13 OR ($row['type']==50) OR $row['type']==27 OR $row['type']==28 OR $row['type']==30  ) &&
				($row['needident'] == 0)
			) OR ($row['type']==33)  )
			 {
				if ((($row['type']==12) OR ($row['magic']) OR ($incmagic['cur'])) && $orden == 0 && $act == '') {
					if ($user['align'] != 4) {
						$ret .= "<a  onclick=\"";

						if($magic['id'] == 109 OR $magic['id'] == 43 OR $magic['id'] == 200 OR $magic['id'] == 500 OR $magic['id'] == 65 OR $magic['id'] == 95 OR $magic['id'] == 96) {
							$ret .= "showitemschoice('Выберите встраиваемый свиток', 'scrolls', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 110) {
							$ret .= "showitemschoice('Выберите откуда вынуть свиток', 'moveemagic', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 227) {
							$ret .= "showitemschoice('Выберите подарок для сохранения', 'del_time', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 67) {
							$ret .= "showitemschoice('Выберите Бронь для подгонки', 'makefreeup_bron', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 68) {
							$ret .= "showitemschoice('Выберите Кольцо для подгонки', 'makefreeup_ring', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 69) {
							$ret .= "showitemschoice('Выберите Кулон для подгонки', 'makefreeup_kulon', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 70) {
							$ret .= "showitemschoice('Выберите Перчатки для подгонки', 'makefreeup_perchi', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 71) {
							$ret .= "showitemschoice('Выберите Шлем для подгонки', 'makefreeup_shlem', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 72) {
							$ret .= "showitemschoice('Выберите Щит для подгонки', 'makefreeup_shit', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 73) {
							$ret .= "showitemschoice('Выберите Серьги для подгонки', 'makefreeup_sergi', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 74) {
							$ret .= "showitemschoice('Выберите Сапоги для подгонки', 'makefreeup_boots', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 76) {
							$ret .= "showitemschoice('Выберите Портал для перемещения', 'lab_teleport', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 201) {
							$ret .= "showitemschoice('Выберите предмет для удаления магии', 'delitems', 'main.php?edit=1&use=".$row['id']."');";
						} elseif(($magic['id'] == 84) OR ($magic['id'] == 85) OR ($magic['id'] == 86) OR ($magic['id'] == 87) ) {
							$ret .= "showitemschoice('Выберите руну', 'add_runs_exp', 'main.php?edit=1&use=".$row['id']."');";


						} elseif($magic['id'] == 4) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_m5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 29) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_m1', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 30) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_m2', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 31) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_m3', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 32) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_m4', 'main.php?edit=1&use=".$row['id']."');";
						}
						elseif($magic['id'] == 5) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_t5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 25) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_t4', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 26) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_t3', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 27) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_t2', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 28) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_t1', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 33) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_d1', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 34) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_d2', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 35) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_d3', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 36) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_d4', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 37) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_d5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 38) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_n1', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 39) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_n2', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 40) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_n3', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 41) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_n4', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 42) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_n5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 100001) {
							$ret .= "showitemschoice('Выберите Артефакт для улучшения', 'art_bonus_1', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 100002) {
							$ret .= "showitemschoice('Выберите Артефакт для улучшения', 'art_bonus_2', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 100003) {
							$ret .= "showitemschoice('Выберите Артефакт для улучшения', 'art_bonus_3', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 100004) {
							$ret .= "showitemschoice('Выберите Артефакт для улучшения', 'art_bonus_4', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 100005) {
							$ret .= "showitemschoice('Выберите Артефакт для улучшения', 'art_bonus_5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 100006) {
							$ret .= "showitemschoice('Выберите Артефакт для улучшения', 'art_bonus_6', 'main.php?edit=1&use=".$row['id']."');";

						} elseif($magic['id'] == 100011) {
							$ret .= "showitemschoice('Выберите предмет для чарования 1', 'item_bonus_1', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 100012) {
							$ret .= "showitemschoice('Выберите предмет для чарования 2', 'item_bonus_2', 'main.php?edit=1&use=".$row['id']."');";
						} elseif(($magic['id'] == 100013) and ($row['sowner']==0)) {
							$ret .= "showitemschoice('Выберите предмет для чарования 3', 'item_bonus_3e', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 100013) {
							$ret .= "showitemschoice('Выберите предмет для чарования 3', 'item_bonus_3', 'main.php?edit=1&use=".$row['id']."');";
						}
						elseif($magic['id'] == 90) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_6', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 91) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_ekr_d5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 92) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_ekr_m5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 93) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_ekr_n5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 94) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_ekr_t5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 190) {
							$ret .= "showitemschoice('Выберите оружие для заточки или перезаточки', 'sharp_7', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 191) {
							$ret .= "showitemschoice('Выберите оружие для заточки или перезаточки', 'sharp_8', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 192) {
							$ret .= "showitemschoice('Выберите оружие для заточки или перезаточки', 'sharp_9', 'main.php?edit=1&use=".$row['id']."');";
						} elseif(($magic['id'] == 181)||($magic['id'] == 182)||($magic['id'] == 183)||($magic['id'] == 184)||($magic['id'] == 185))  {
							$ret .= "showitemschoice('Выберите обмундирование для активации скидки', 'bysshop', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 172)   {
							$ret .= "showitemschoice('Подтверждение:', 'usedays', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 3) {
							$ret .= "showitemschoice('Выберите предмет для идентификации', 'identitems', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 214 or $magic['id'] == 218 or $magic['id'] == 222) {
							$ret .= "showitemschoice('Выберите предмет для улучшения', 'upgrade_7', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 215 or $magic['id'] == 219 or $magic['id'] == 223) {
							$ret .= "showitemschoice('Выберите предмет для улучшения', 'upgrade_8', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 216 or $magic['id'] == 220 or $magic['id'] == 224) {
							$ret .= "showitemschoice('Выберите предмет для улучшения', 'upgrade_9', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 217 or $magic['id'] == 221 or $magic['id'] == 225) {
							$ret .= "showitemschoice('Выберите предмет для улучшения', 'upgrade_10', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 317 or $magic['id'] == 321 or $magic['id'] == 325) {
							$ret .= "showitemschoice('Выберите предмет для улучшения', 'upgrade_11', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 1025) {
							$ret .= "shownoobrings('Выберите кольцо', 'noobrings', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 1030) {
							$ret .= "showelka('Выберите екровую ёлку', 'elka', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 1031) {
							$ret .= "showelka2('Выберите артовую ёлку', 'elka2', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 326 or $magic['id'] == 327 or $magic['id'] == 328) {
							$ret .= "showitemschoice('Выберите предмет для улучшения', 'upgrade_12', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['targeted'] == 8) {
							$ret .= "oknoPass('Введите Пароль', 'main.php?edit=1&use=".$row['id']."', 'target')";
						} elseif($magic['targeted'] == 1) {
							$ret .= "okno('Введите название предмета', 'main.php?edit=1&use=".$row['id']."', 'target')";
						} elseif($magic['targeted'] == 10) {
							$ret .= "oknoCity('Введите название города (capital,avalon)', 'main.php?edit=1&use=".$row['id']."', 'target')";
						} elseif($magic['targeted'] == 13) {
							$ret .= "oknoTeloCity('Введите ник и название города (capital,avalon)', 'main.php?edit=1&use=".$row['id']."', 'target','city')";
						} elseif($magic['targeted'] == 15) {
							$ret .= "okno('Введите номер своего банковского счета', 'main.php?edit=1&use=".$row['id']."', 'target',null,2)";
						} elseif($magic['targeted'] == 2) {
							$ret .= "findlogin('Введите имя персонажа', 'main.php?edit=1&use=".$row['id']."', 'target')";
						} elseif(($magic['targeted'] == 0) AND ($magic['name'] == 'исцеление') and ($user['in_tower'] > 0)) {
							$ret .= "findlogin('Введите имя персонажа', 'main.php?edit=1&use=".$row['id']."', 'target')";
						} elseif($magic['id'] == 100) {
							$ret .= "usepaper('Бумага', 'main.php?edit=1&use=".$row['id']."', 'target','100')";
						} elseif($magic['id'] == 101) {
							$ret .= "usepaper('Бумага', 'main.php?edit=1&use=".$row['id']."', 'target','200')";
						} elseif($magic['id'] == 102) {
							$ret .= "usepaper('Бумага', 'main.php?edit=1&use=".$row['id']."', 'target','500')";
						}
						 elseif($magic['id'] == 120) {
							$ret .= "showitemschoice('Выберите уникальный предмет для улучшения', 'upunik', 'main.php?edit=1&use=".$row['id']."');";
						}
						elseif(($magic['targeted'] == 0) AND ($magic['name'] == 'исцеление') and ($user['in_tower']==0) )
				    		{
				    		  $ret .= "if(confirm('Использовать сейчас?')) { window.location='main.php?edit=1&use=".$row['id']."';}";
				    		}
						else {
							$ret .= "window.location='main.php?edit=1&use=".$row['id']."';";
						}

						if ($magic['id'] == 4004000) {
							$ret .= "\" href='#'>вскрыть</a> ";
						} else {
							$ret .= "\" href='#'>исп-ть</a> ";
						}

						if ($magic['id'] == 171) {
							$ret .= "<br><a href=\"main.php?edit=1&flag=".$row['id']."\">надеть</a> ";
						}
						elseif ($magic['id'] == 172) {
							$ret .= "<br><a onclick=\"okno('Введите текст:', 'main.php?edit=1&usedays=".$row['id']."', 'daystext','',2);\" href='#'>надеть</a> ";
						}

					}

				}

				if($act == '') {
					if (($row['type'] != 50 && $orden == 0) AND ((($row['sowner']>0) AND ($user['id'] == $row['sowner'])) OR ($row['sowner'] == 0))) {

					if (!(in_array($row['prototype'],$nodress) ))
							{
 							$dress_yes=true;
								if ($row['gsila']<0)
									{
										if ($row['gsila']+$user['sila']<$row['nsila'])
											{
											$dress_yes=false;
											 }
									}
								elseif ($row['glovk']<0)
									{
											if  ($row['glovk']+$user['lovk']<$row['nlovk'])
											{
											$dress_yes=false;
											}
									}
								elseif ($row['ginta']<0)
									{
										 if  ($row['ginta']+$user['inta']<$row['ninta'])
										 	{
											$dress_yes=false;
										 	}
									}

								if ($dress_yes==true)
									{
									if ($user['align'] != 4) $ret .= "<BR><a href='?edit=1&dress=".$row['id']."'>надеть</a> ";
									}
							 }
					}

					$is_in_pocket = (int)$row['karman'];
					if($is_in_pocket == 0 && $orden==0 && $user['in_tower'] != 16) {
						$ret .= "<br><a href='?edit=1&pocket=1&item=".$row['id']."'>положить</a> ";
					} elseif($is_in_pocket != 0 && $orden == 0 && $user['in_tower'] != 16) {
						$ret .= "<br><a href='?edit=1&pocket=2&item=".$row['id']."'>достать</a> ";
					}
				 }
			} elseif ((($row['type']==50) OR ($row['type']==12) OR ($row['magic']) OR ($incmagic['cur'])) and ($row['type'] != 13)) {
				if($act == '') {
					if ($user['align'] != 4) {
						$ret .= "<a  onclick=\"";
						if($magic['id'] == 43 OR $magic['id'] == 200 OR $magic['id'] == 65) {
							$ret .= "showitemschoice('Выберите встраиваемый свиток', 'scrolls', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 201) {
							$ret .= "showitemschoice('Выберите предмет для удаления магии', 'delitems', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 3) {
							$ret .= "showitemschoice('Выберите предмет для идентификации', 'identitems', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 214 or $magic['id'] == 218 or $magic['id'] == 222) {
							$ret .= "showitemschoice('Выберите предмет для улучшения', 'upgrade_7', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 215 or $magic['id'] == 219 or $magic['id'] == 223) {
							$ret .= "showitemschoice('Выберите предмет для улучшения', 'upgrade_8', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 216 or $magic['id'] == 220 or $magic['id'] == 224) {
							$ret .= "showitemschoice('Выберите предмет для улучшения', 'upgrade_9', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 217 or $magic['id'] == 221 or $magic['id'] == 225) {
							$ret .= "showitemschoice('Выберите предмет для улучшения', 'upgrade_10', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['targeted'] == 1) {
							$ret .= "okno('Введите название предмета', 'main.php?edit=1&use=".$row['id']."', 'target')";
						} elseif($magic['targeted'] == 10) {
							$ret .= "oknoCity('Введите название города (capital,avalon)', 'main.php?edit=1&use=".$row['id']."', 'target')";
						} elseif($magic['targeted'] == 13) {
							$ret .= "oknoTeloCity('Введите ник и название города (capital,avalon)', 'main.php?edit=1&use=".$row['id']."', 'target','city')";
						} elseif($magic['targeted'] == 15) {
							$ret .= "okno('Введите номер своего банковского счета', 'main.php?edit=1&use=".$row['id']."', 'target',null,2)";
						} elseif($magic['targeted'] == 2) {
							$ret .= "findlogin('Введите имя персонажа', 'main.php?edit=1&use=".$row['id']."', 'target')";
						} elseif($magic['id'] == 4) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_m5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 29) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_m1', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 30) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_m2', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 31) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_m3', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 32) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_m4', 'main.php?edit=1&use=".$row['id']."');";
						}
						elseif($magic['id'] == 5) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_t5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 25) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_t4', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 26) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_t3', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 27) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_t2', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 28) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_t1', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 33) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_d1', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 34) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_d2', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 35) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_d3', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 36) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_d4', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 37) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_d5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 38) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_n1', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 39) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_n2', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 40) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_n3', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 41) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_n4', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 42) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_n5', 'main.php?edit=1&use=".$row['id']."');";
						}
						elseif($magic['id'] == 90) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_6', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 91) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_ekr_d5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 92) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_ekr_m5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 93) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_ekr_n5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 94) {
							$ret .= "showitemschoice('Выберите оружие для заточки', 'sharp_ekr_t5', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 190) {
							$ret .= "showitemschoice('Выберите оружие для заточки или перезаточки', 'sharp_7', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 191) {
							$ret .= "showitemschoice('Выберите оружие для заточки или перезаточки', 'sharp_8', 'main.php?edit=1&use=".$row['id']."');";
						} elseif($magic['id'] == 910) {
							$ret .= "showitemschoice('Выберите желаемый ефект', 'usev2015', 'main.php?edit=1&use=".$row['id']."');";
						}
						 else {
							$ret .= "window.location='main.php?edit=1&use=".$row['id']."';";
						}
						if ($magic['id'] == 4004000) {
							$ret .= "\" href='#'>вскрыть</a><br> ";
						} elseif (($row['magic']>0) or ($incmagic['cur'])) {
							$ret .= "\" href='#'>исп-ть</a><br> ";
						}
					}
				}
			}
			if (in_array($row['prototype'],$vau4) && $row['prototype'] != 100005) {
				$ret .= "<a  onclick=\"";
				$ret .= "oknovauch('Разменять ваучер', 'main.php?edit=1&exchange=".$row['id']."', 'target','".$row['prototype']."')";
				$ret .= "\" href='#'>разменять</a> ";
			}

			if($orden == 0 && $act=='') {
				//fixed for group deleting resources in inv  by Umk
				if ($user['align'] != 4) {
					if($row['group_by'] == 1 && $row[GetShopCount()]>1) {
						if($row['present'] != '') {
							$gift=1;
						} else {
							$gift=0;
						}
	        				$ret .= "<img src=http://i.oldbk.com/i/clear.gif style=\"cursor: pointer;\"  alt=\"Выбросить несколько штук\" onclick=\"AddCount('".$row['prototype']."', '".$row['name']."','".$gift."','".$row['duration']."')\"></TD>";
					} elseif($row['present'] != 'Арендная лавка' && $user['in_tower'] != 16) {
						if (!$issart || ($issart && ($user['in_tower'] > 0 || $row['labonly'] > 0))) {
							$ret .= "<img src=http://i.oldbk.com/i/clear.gif style=\"cursor: pointer;\" onclick=\"if (confirm('Предмет ".$row['name']." будет утерян, вы уверены?')) window.location='main.php?edit=1&destruct=".$row['id']."'\"></TD>";
						}
			    		} elseif ($user['in_tower'] == 16 && $row['type'] != 12 && $row['type'] != 13) {
						$ret .= "<br><a href='castles_o.php?ret=".$row['id']."&razdel=".$row['type']."'>вернуть на склад</a> ";
			    		}
				}

			} elseif($act != '') {
				$ret .= $act;
			}

		} elseif($row['chk_arsenal'] == 1) {
			$ret .= "<A HREF='klan_arsenal.php?get=1&sid=".$user['sid']."&item=".$row['id_ars']."'>взять из арсенала</A>";

			if ($row['owner_original'] == 1) {
				$ret .= '<br><b>куплено из казны</b>';

				if ($IM_glava==1) {
	            			//глава клана может управлять доступами
					if ($row['all_access'] == 1) {
		            			$ret .= "<small><input type=checkbox name='mass_cl[".$row['id']."]' checked='checked' onclick=\"show('all_cl_".$row['id']."'); \"> доступ всем</small>";
					} else {
		            			$ret .= "<small><input type=checkbox name='mass_cl[".$row['id']."]' onclick=\"show('all_cl_".$row['id']."'); \"> доступ всем</small>";
					}

					if ($row['all_access'] == 0) {
						$ret .= "<br><div style=\"display:block;\" id=\"all_cl_".$row['id']."\"><small><a href=# onclick=\"window.open('klan_arsenal_access.php?it=".$row['id']."', 'access', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')\">Выборочный доступ</a></small></div>";
					} else {
						$ret .= "<br><div style=\"display:none;\" id=\"all_cl_".$row['id']."\"><small><a href=# onclick=\"window.open('klan_arsenal_access.php?it=".$row['id']."', 'access', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')\">Выборочный доступ</a></small></div>";
					}
				}
			} else {
				$ret .= '<br>Сдал:<br><b>'. global_nick($row['owner_original']).'</b> <a target=_blank href=/inf.php?'.$row['owner_original'].'><img border=0 src="http://i.oldbk.com/i/inf.gif"></a>';
			}
		} elseif($row['chk_arsenal'] == 2) {
			if($row['owner_current'] == 0) {
				$ret .= "<BR><A HREF='klan_arsenal.php?my=1&get=1&sid=".$user['sid']."&item=".$row['id_ars']."'>забрать</A>";
			} else {
				$ret .= "<BR>Используется: <BR>";
				$ret .= "<b>".global_nick($row['owner_current'])."</b>";
				$ret .= ' <a target=_blank href=/inf.php?'.$row['owner_current'].'><img border=0 src=http://i.oldbk.com/i/inf.gif></a>';
				$ret .= "<BR><A HREF='klan_arsenal.php?my=1&get=1&sid=".$user['sid']."&item=".$row['id_ars']."&getmy=1'>забрать</A>";
			}
		} elseif($row['chk_arsenal'] == 3) {
			$ret .= '<A HREF="klan_arsenal.php?put=1&sid='.$user['sid'].'&item='.$row['id'].'">сдать в арсенал</A>';
			if ($giftars == 1) {
				$ret .= "<br><br><a  onclick=\"";
				$ret .= "if(confirm('Вы действительно хотите подарить арсеналу предмет безвозвратно?')) { window.location='klan_arsenal.php?put=2&sid=".$user['sid']."&item=".$row['id']."';}";
				$ret .= "\" href='#'>";
				$ret .= "Подарить в арсенал</a>";
			}
		} elseif($row['chk_arsenal'] == 4) {
           		$ret .= '<A HREF="klan_arsenal.php?return=1&sid='.$user['sid'].'&item='.$row['id'].'">вернуть</A>';
		} elseif($row['chk_arsenal'] == 5) {
			if($row['owner'] == 22125) {
				$ret .= "<BR>Не используется.<BR>";
			} else {
				$ret .= "<BR>Используется: <BR>";
				$ret .= "<b>".global_nick($row['owner'])."</b>";
				$ret .= ' <a target=_blank href=/inf.php?'.$row['owner'].'><img border=0 src=http://i.oldbk.com/i/inf.gif></a>';
				if ($klan_ars_back==1) {
				 	// линк на забрать
			         	$ret .= '<br><A HREF="klan_arsenal.php?back=1&item='.$row['id'].'">изъять</A>';
				}
			}
		} elseif($row['chk_arsenal'] == 6) {
           		$ret .= '<A HREF="klan_arsenal.php?mybox=1&sid='.$user['sid'].'&item='.$row['id'].'">забрать из сундука</A>';
		} elseif($row['chk_arsenal']>=3003131 && $row['chk_arsenal']<= 3003135) {
			$ret .= '<A HREF="klan_arsenal.php?usebook='.$row['id'].'">использовать</A>';
		} elseif($row['chk_arsenal'] == 7) {
			$ret .= '<A HREF="klan_arsenal.php?mybox=2&sid='.$user['sid'].'&item='.$row['id'].'">положить в сундук</A>';
		}

	    	$ret .= "<td valign=top>";
	}

	if ($row['nalign']==1) {
		$row['nalign']='1.5';
	}


	$ehtml = str_replace('.gif','',$row['img']);

	$razdel=array(
		1=>"kasteti", 11=>"axe", 12=>"dubini", 13=>"swords", 14=>"bow", 2=>"boots", 21=>"naruchi", 22=>"robi", 23=>"armors",
		24=>"helmet", 3=>"shields",4=>"clips", 41=>"amulets", 42=>"rings", 5=>"mag1", 51=>"mag2", 6=>"amun", 61=>'eda' , 72 =>''
	);

	$row['otdel'] == '' ? $xx = $row['razdel'] : $xx = $row['otdel'];

	if ($row['type']==30)
		{
		$razdel[$xx]="runs/".$ehtml;
		}
	else
	if($razdel[$xx] == '') {
            	$dola = array(5001,5002,5003,5005,5010,5015,5020,5025);

		if (in_array($row['prototype'],$vau4)) {
			$razdel[$xx]='vaucher';
		} elseif (in_array($row['prototype'],$dola)) {
			$razdel[$xx]='earning';
		}
		else {

			$oskol=array(15551,15552,15553,15554,15555,15556,15557,15558,15561,15562,15568,15563,15564,15565,15566,15567);
			if (in_array($row['prototype'],$oskol))
			{
			$razdel[$xx]="amun/".$ehtml;
			}
			else
			{
			$razdel[$xx]='predmeti/'.$ehtml;
			}
		}
	} else {

		$razdel[$xx]=$razdel[$xx]."/".$ehtml;

	}

	if (($row['art_param'] != '') and ($row['type']!=30)) {
		if ($row['arsenal_klan'] != '')	{
			// клановый
			$razdel[$xx]='art_clan';
		} elseif ($row['sowner'] != 0) {
				//личный
			$razdel[$xx]='art_pers';
		}
	}


	$anlim_txt="";
	if (($anlim_show) and (in_array($row['prototype'],$anlim_items))) {
		$anlim_txt = ' <IMG SRC="http://i.oldbk.com/i/noobs.png" WIDTH="14" HEIGHT="8" BORDER=0 TITLE="Эту вещь всегда можно купить в Гос.магазине" alt="Эту вещь всегда можно купить в Гос.магазине"> ';
	}

	$pod = explode(':|:',$row['present']);
	if(count($pod) > 1) {
		$txt = $pod[0];
	} else {
		$txt = $row['present'];
	}

	if ($row['otdel']==72)
	{
	$ret .= "<a href=https://oldbk.com/commerce/index.php?act=perspres target=_blank>".$row['name']."</a>";
	}
	else
	if ($razdel[$xx]=='mag1/036')
	{
	$ret .= "<a href=https://oldbk.com/encicl/prem.html  target=_blank>".$row['name']."</a>";
	}
	elseif ($razdel[$xx]=='mag1/037')
	{
	$ret .= "<a href=https://oldbk.com/encicl/prem.html  target=_blank>".$row['name']."</a>";
	}
	elseif ($razdel[$xx]=='mag1/137')
	{
	$ret .= "<a href=https://oldbk.com/encicl/prem.html  target=_blank>".$row['name']."</a>";
	}
	else
	{
	$ret .= "<a href=https://oldbk.com/encicl/".$razdel[$xx].".html target=_blank>".$row['name']."</a>";
	}



	$eshopadd = "";

	if ((isset($_GET['otdel']) && (in_array($_GET['otdel'],$unikrazdel) ) && ($_SERVER['PHP_SELF'] == "/_eshop.php" || $_SERVER['PHP_SELF'] == "/eshop.php")  ) or ($row['ekr_flag']>0) ) { 

		$addimg = '<img src="http://i.oldbk.com/i/berezka06.gif" title="Предмет из Березки" alt="Предмет из Березки"> ';
	} else {
		$addimg = "";
	}


	if ($row['present']) {
	$ret .= "<img src=http://i.oldbk.com/i/align_".$row['nalign'].".gif> (Масса: ".$row['massa'].") ".$artinfo.$anlim_txt.'  '.$addimg.' <IMG SRC="http://i.oldbk.com/i/podarok.gif" WIDTH="16" HEIGHT="18" BORDER=0 TITLE="Этот предмет вам подарил '.$txt.'. Вы не сможете передать этот предмет кому-либо еще." ALT="Этот предмет вам подарил '.$txt.'. Вы не сможете передать этот предмет кому-либо еще."><BR>';
	} else {
		$ret .= "<img src=http://i.oldbk.com/i/align_".$row['nalign'].".gif> (Масса: ".$row['massa'].") ".$addimg." ".$artinfo.$anlim_txt.' <BR>';
	}

        if($row['sowner'] > 0) {
		if($row['sowner'] != $user['id']) {
			$so = mysql_query_cache('SELECT * from oldbk.users WHERE id = '.$row['sowner'].' AND id_city = 0',false,600);
			if (!count($so)) {
	        		$so = mysql_query_cache('SELECT * from avalon.users WHERE id = '.$row['sowner'].' AND id_city = 1',false,600);
			}
			if (!count($so)) {
	        		$so = mysql_query_cache('SELECT * from angels.users WHERE id = '.$row['sowner'].' AND id_city = 2',false,600);
			}

			if (count($so)) {
				$so = $so[0];
	        		$sowner = s_nick($so['id'],$so['align'],$so['klan'],$so['login'],$so['level']);
			}
        	} else {
	        	$sowner = s_nick($user['id'],$user['align'],$user['klan'],$user['login'],$user['level']);
		}

        	if ($row['type'] == 250) {
        		$ret .= '<font color=red>Данная вещь принадлежит</font> '.$sowner.'<br>';
		} elseif ($row['type'] == 210) {
			$ret .= '<font color=red>Данную вещь может использовать</font> '.$sowner.'<br>';
		} elseif ($row['type'] == 200) {
			$ret .= '<font color=red>Этот подарок может подарить только</font> '.$sowner.'<br>';
		} elseif (($row['prototype'] >=56661 ) and  ($row['prototype'] <=56663 )) {
			$ret .= '<font color=red>После использования на предмет, он станет привязан к персонажу '.$sowner.'</font><br>';
		}
		else {
        		$ret .= '<font color=red>Данную вещь может надеть только</font> '.$sowner.'<br>';
		}
	}

	if ($row['no'] == 1) {
		// nothing
	} elseif ( (($row['repcost'] > 0) and ($row['ecost'] ==0)) or ($_SERVER['PHP_SELF'] == '/cshop.php') ) {
		if($row['setsale'] > 0) {
			$row['cost']=$row['setsale'];
		}

		if($check_price) {
			if($user['repmoney'] < $row['repcost'])	{
				$ret .= "<b>Цена: <font color='red'>".$row['repcost']."</font> реп.</b> &nbsp;";
			} else {
				$ret .= "<b>Цена: ".$row['repcost']." реп.</b> &nbsp;";
			}
		} elseif((int)$row['type'] == 12) {
			$ret .= "<b>Цена: ".$row['cost']." кр.</b>  &nbsp;";
		} else {
			$ret .= "<b>Цена: ".$row['cost']." кр.</b>  &nbsp;";
		}
	} elseif($row['ecost'] > 0 && ($_SERVER['PHP_SELF'] != '/comission.php')) {
		$ret .= "<b>Цена: ".$row['ecost']." екр.</b> &nbsp; &nbsp;";
	} elseif($row['cost'] > 0 && $row['setsale'] > 0 && ($_SERVER['PHP_SELF'] == '/comission.php')) {
		$ret .= "<b>Цена: ".$row['setsale']." кр.</b> &nbsp; (Гос.Цена. ".$row['cost']." кр.)&nbsp;";
	} else {
		$ret .= "<b>Цена: ".$row['cost']." кр.</b> &nbsp; &nbsp;";
	}

	if ($row['no'] == 1) {
		// nothing
	} elseif($row[GetShopCount()]) {
		if ($_SERVER['PHP_SELF'] == '/eshop.php' || $_SERVER['PHP_SELF'] == '/_eshop.php') {
			$ret .= "<small>(количество:<b>&#8734;</b>)";

			if ($user['klan'] == 'radminion') {
				$ret .= "(<b> ".$row[GetShopCount()]."</b>)";
			}

			$ret .= "</small>";
		} else {
			if (($_SERVER['PHP_SELF'] == '/shop.php') AND in_array($row['id'],$anlim_items) AND ($anlim_show)) {
				$ret .= '<small>(количество: <IMG SRC="http://i.oldbk.com/i/noobs.png" WIDTH="14" HEIGHT="8" BORDER=0 TITLE="Эту вещь всегда можно купить в Гос.магазине" alt="Эту вещь всегда можно купить в Гос.магазине">)</small>';
			} else {
				$ret .= "<small>(количество:<b> {$row[GetShopCount()]}</b>)</small>";
			}
		}

		if($user['prem']>0 && ($_SERVER['PHP_SELF'] == '/shop.php') && strpos($_SERVER['QUERY_STRING'],'newsale') === false) {
			$akkname[1]='Silver';
			$akkname[2]='Gold';
			$akkname[3]='Platinum';

			$cost=sprintf("%01.2f", ($row['cost']*0.9));
			$ret .= "<br><b>Цена: ".$cost." кр.</b> (для ".$akkname[$user[prem]]." account)";
		}
	}

	if ((($row['is_owner']==1) and ($row['id']>=56661 ) and ($row['id']<=56663 ) ) and ($check_price && $priv) )
	{
		$ret .= '<br><small><font color=red>После использования на предмет, он станет привязан к персонажу!</font></small>';
	}
	elseif($check_price && $priv) {
		$ret .= '<br><small><font color=red>После покупки вещь будет привязана к персонажу.</font></small>';
	}  elseif ($row['is_owner']==1) {
		$ret .= '<br><small><font color=red>После покупки вещь будет привязана к персонажу.</font></small>';
	} elseif (($_SERVER['PHP_SELF'] == '/cshop.php') AND ($row['id'] == 1000001 || $row['id'] == 1000002 || $row['id'] == 1000003)) {
		$ret .= '<br><small><font color=red>После покупки предмет будет невозможно передать.</font></small>';
	}

	if ($row['type'] == 30) {
		// показываем уровень урны
		$addlvl = "";
		if ($row['ups'] >= $row['add_time']) {
			$addlvl = ' <a href="?edit=1&uprune='.$row['id'].'" style="color:red;"><img src="http://i.oldbk.com/i/up.gif" border="0"></a> ';
		}

		if (isset($row['ups'])) {
			$ret .= "<br><b>Уровень: ".$row['up_level']." </b> Опыт: <b><a href=\"http://oldbk.com/encicl/?/runes_opyt_table.html\" target=\"_blank\">".$row['ups']."</b></a> (".$row['add_time'].") ".$addlvl;
		} else {
			$ret .= "<br><b>Уровень: ".(int)($row[up_level])." </b> ";
		}
	}

	$ret .= "<BR>Долговечность: ".$row['duration']."/".$row['maxdur']."<BR>";

	if (!$row['needident']) {
		// распаковка масива с арт параметрами
		$art_param = explode(',',$row['art_param']);

		if ($row['type'] != 30) {
			 // поле ups - для рун используется для хранения опыта руны для перехода на уровень
			if ($row['ups'] > 0) {
				$ret .= "Подогнано: <b>".$row['ups']." раз</b><BR>";
			}
		}

		if ($row['stbonus'] > 0) {
			$ret .= "Возможных увеличений: <b>".$row['stbonus']."</b><BR>";
		}
		if ($row['mfbonus'] > 0) {
			$ret .= "Возможных увеличений мф: <b>".$row['mfbonus']."</b><BR>";
		}

		if ($magic['chanse']) {
			$ret .= "Вероятность срабатывания: ".$magic['chanse']."%<BR>";
		}
		if ($magic['time']) {
			$ret .= "Продолжительность действия магии: ".get_delay_time($magic['time'])." <BR>";
		}
		if ($row['goden']) {
			$ret .= "Срок годности: {$row['goden']} дн. ";
			if (!$row[GetShopCount()] or $_SERVER['PHP_SELF'] == '/comission.php' or $_SERVER['PHP_SELF'] == '/main.php') {
				$ret .= "(до ".date("d.m.Y H:i",$row['dategoden']).")";
			}
			$ret .= "<BR>";
		}
		if ($row['nsex'] == 1) {
			$ret .= "• Пол: <b>Женский</b><br>";
		}

		if ($row['nsex'] == 2) {
			$ret .= "• Пол: <b>Мужской</b><br>";
		}

		$ret .= "<b>Требуется минимальное:</b><BR>";

				$ret .= "Уровень: <input type=text size=5 name=nlevel value='".(int)($row['nlevel'])."'><br>";
		
				$ret .= "<b>Требования в статах:</b><BR>";
				$ret .= "Сила: <input type=text size=5 name=nsila value='".(int)($row['nsila'])."'><br>";
				$ret .= "Ловкость: <input type=text size=5 name=nlovk value='".(int)($row['nlovk'])."'><br>";
				$ret .= "Интуиция: <input type=text size=5 name=ninta value='".(int)($row['ninta'])."'><br>";
				$ret .= "Выносливость: <input type=text size=5 name=nvinos value='".(int)($row['nvinos'])."'><br>";
				$ret .= "Интеллект: <input type=text size=5 name=nintel value='".(int)($row['nintel'])."'><br>";
				$ret .= "Мудрость: <input type=text size=5 name=nmudra value='".(int)($row['nmudra'])."'><br>";
		
	
				$ret .= "<b>Требования в оружии:</b><BR>";
				$ret .= "Мастерство владения ножами и кастетами: <input type=text size=5 name=nnoj value='".(int)($row['nnoj'])."'><br>";
				$ret .= "Мастерство владения топорами и секирами: <input type=text size=5 name=ntopor value='".(int)($row['ntopor'])."'><br>";
				$ret .= "Мастерство владения дубинами и булавами: <input type=text size=5 name=ndubina value='".(int)($row['ndubina'])."'><br>";
				$ret .= "Мастерство владения мечами: <input type=text size=5 name=nmech value='".(int)($row['nmech'])."'><br>";
			

				$ret .= "<b>Требования в магии:</b><BR>";
				$ret .= "Мастерство владения стихией Огня: <input type=text size=5 name=nfire value='".(int)($row['nfire'])."'><br>";
				$ret .= "Мастерство владения стихией Воды: <input type=text size=5 name=nwater value='".(int)($row['nwater'])."'><br>";
				$ret .= "Мастерство владения стихией Воздуха: <input type=text size=5 name=nair value='".(int)($row['nair'])."'><br>";
				$ret .= "Мастерство владения стихией Земли: <input type=text size=5 name=nearth value='".(int)($row['nearth'])."'><br>";
				$ret .= "Мастерство владения магией Света: <input type=text size=5 name=nlight value='".(int)($row['nlight'])."'><br>";
				$ret .= "Мастерство владения серой магией: <input type=text size=5 name=ngray value='".(int)($row['ngray'])."'><br>";
				$ret .= "Мастерство владения магией Тьмы: <input type=text size=5 name=ndark value='".(int)($row['ndark'])."'><br>";



		$ret .= "<b>Действует на:</b><hr>";
		
		if ($row['type']==3)
		{
		$ret .= "<b>Повреждения.:</b><BR>";
		$ret .= "• Минимальное наносимое повреждение: <input type=text size=5 name=minu value='".(int)($row['minu'])."'><br>";
		$ret .= "• Максимальное наносимое повреждение: <input type=text size=5 name=maxu value='".(int)($row['maxu'])."'><br>";
		}
		
		$ret .= "<b>Статы и жизнь.:</b><BR>";
		$ret .= "• Сила: +<input type=text size=5 name=gsila value='".(int)($row['gsila'])."'><br>";
		$ret .= "• Ловкость: +<input type=text size=5 name=glovk value='".(int)($row['glovk'])."'><br>";
		$ret .= "• Интуиция: +<input type=text size=5 name=ginta value='".(int)($row['ginta'])."'><br>";			
		$ret .= "• Интеллект: +<input type=text size=5 name=gintel value='".(int)($row['gintel'])."'><br>";			
		$ret .= "• Мудрость: +<input type=text size=5 name=gmp value='".(int)($row['gmp'])."'><br>";						
		$ret .= "• Уровень жизни: +<input type=text size=5 name=ghp value='".(int)($row['ghp'])."'><br>";												

		$ret .= "<b>Мф.:</b><BR>";
		$ret .= "• Мф. критических ударов: <input type=text size=5 name=mfkrit value='".(int)($row['mfkrit'])."'><br>";
		$ret .= "• Мф. против крит. ударов: <input type=text size=5 name=mfakrit value='".(int)($row['mfakrit'])."'><br>";			
		$ret .= "• Мф. увертливости: <input type=text size=5 name=mfuvorot value='".(int)($row['mfuvorot'])."'><br>";			
		$ret .= "• Мф. против увертлив.: <input type=text size=5 name=mfauvorot value='".(int)($row['mfauvorot'])."'><br>";									
			
		$ret .= "<b>Мастерство:</b><BR>";
		$ret .= "• Мастерство владения ножами и кастетами: +<input type=text size=5 name=gnoj value='".(int)($row['gnoj'])."'><br>";
		$ret .= "• Мастерство владения топорами и секирами: +<input type=text size=5 name=gtopor value='".(int)($row['gtopor'])."'><br>";
		$ret .= "• Мастерство владения дубинами и булавами: +<input type=text size=5 name=gdubina value='".(int)($row['gdubina'])."'><br>";
		$ret .= "• Мастерство владения мечами: +<input type=text size=5 name=gmech value='".(int)($row['gmech'])."'><br>";
		$ret .= "• Мастерство владения стихией Огня: +<input type=text size=5 name=gfire value='".(int)($row['gfire'])."'><br>";
		$ret .= "• Мастерство владения стихией Воды: +<input type=text size=5 name=gwater value='".(int)($row['gwater'])."'><br>";
		$ret .= "• Мастерство владения стихией Воздуха: +<input type=text size=5 name=gair value='".(int)($row['gair'])."'><br>";
		$ret .= "• Мастерство владения стихией Земли: +<input type=text size=5 name=gearth value='".(int)($row['gearth'])."'><br>";
		$ret .= "• Мастерство владения магией Света: +<input type=text size=5 name=glight value='".(int)($row['glight'])."'><br>";
		$ret .= "• Мастерство владения серой магией: +<input type=text size=5 name=ggray value='".(int)($row['ggray'])."'><br>";
		$ret .= "• Мастерство владения магией Тьмы: +<input type=text size=5 name=gdark value='".(int)($row['gdark'])."'><br>";
		$ret .= "<b>Броня:</b><BR>";
		$ret .= "• Броня головы: <input type=text size=5 name=bron1 value='".(int)($row['bron1'])."'><br>";
		$ret .= "• Броня корпуса: <input type=text size=5 name=bron2 value='".(int)($row['bron2'])."'><br>";
		$ret .= "• Броня пояса: <input type=text size=5 name=bron3 value='".(int)($row['bron3'])."'><br>";
		$ret .= "• Броня ног: <input type=text size=5 name=bron4 value='".(int)($row['bron4'])."'><br>";


		$ret .= "<b>Усиление (Бонусы):</b><br>";
		$ret .= "• максимального мф.:<input type=text size=5 name=ab_mf value='".$row['ab_mf']."'><br>";
		$ret .= "• брони:+<input type=text size=5 name=ab_bron value='".$row['ab_bron']."'><br>";
		$ret .= "• урона:+<input type=text size=5 name=ab_uron value='".$row['ab_uron']."'><br>";

		
		if($row['present'] != '') 
		{
			$prez = explode(':|:',$row['present']);
		}
		
		if($row['gmeshok']) $ret .= "• Увеличивает рюкзак: +".$row['gmeshok']."<BR>";
		
		if($row['letter'] && $user['in_tower'] != 16 && $row['prototype'] != 3006000) $ret .= "Количество символов: ".strlen($row['letter'])."<BR>";
		if($row['present']) $ret .= "Подарок от: <b>".$prez[0]."</b><br>";
		if($row['letter'] && $user['in_tower'] != 16 && $row['prototype'] != 3006000) $ret .= "На бумаге записан текст:<div style='background-color:FAF0E6;'> ".$row['letter']."</div>";
		if($row['prokat_idp']>0) $ret .= "Осталось:".floor(($row['prokat_do']-time())/60/60)." ч. ".round((($row['prokat_do']-time())/60)-(floor(($row['prokat_do']-time())/3600)*60))." мин.<br>";
		if($magic['name'] && $row['type'] != 50) $ret .= "<font color=maroon>Наложены заклятия:</font> ".$magic['name']."<BR>";
		if($magic['img'] && $row['type'] == 12 && $row['labonly']==0 && $row['dategoden'] == 0) $ret .= "<font color=maroon>Свойства:</font> может встраиваться в вещи</font> <BR>";
		if((!$magic['img'] || $row['labonly']==1 || $row['dategoden'] > 0) && $row['type'] == 12 ) $ret .= "<font color=maroon>Свойства:</font> не может встраиваться в вещи</font> <BR>";
		
		if ($row['magic'] == 8888) {
			$ret .=  "<font color=maroon>Свойства:</font> может быть использован только на свой уровень<br>";
		}
		
		if (($row['id'] == 30012) OR ($row['prototype'] == 30012)) {
			$ret .=  "<font color=maroon>Свойства:</font>В три раза увеличивает срок годности букета<br>";
		}
		
		if (($row['id'] == 501) OR ($row['prototype'] == 501) OR ($row['id'] == 502) OR ($row['prototype'] == 502) ) {
			$ret .=  "<font color=maroon>Описание:</font>Для профилактики травм<br>";
		}		
		
		
		if ($magic['name'] && $row['type'] == 50) {
			$ret .= "<font color=maroon>Свойства:</font> ".$magic['name']."<BR>";
		}
		if ($row['text'] && $row[type]==3) {
			$ret .= "На ручке выгравирована надпись:<center>".$row['text']."</center><BR>";
		}
		if ($row['text'] && $row[type]==5) {
			$ret .= "На кольце выгравирована надпись:<center>".$row['text']."</center><BR>";
		}
		if ($row['present_text']) {
			$ret .= "На подарке написан текст:<br />".$row['present_text']."<BR>";
		}
		if ($incmagic['max']) {
			$ret .= "Встроено заклятие <img src=\"http://i.oldbk.com/i/magic/".$incmagic['img']."\" title=\"".$incmagic['name']."\"> ".$incmagic['cur']."/".$incmagic['max']." шт.<BR> ";
			if ($incmagic['nlevel'] > $user['level']) {
				$ret .= "<font color=red>Требуемый уровень: ".$incmagic['nlevel']."</font>";
			} else {
				$ret .= "Требуемый уровень: ".$incmagic['nlevel'];
			}
			$ret .= "<br>";
		}
		if($incmagic['max']) {
			$ret .= "К-во перезарядок: ".$incmagic['uses']."<br>";
		}
		if ($row['labonly']==1) {
			$ret .= "<small><font color=maroon>Предмет пропадет после выхода из Лабиринта</font></small><BR>";
		}
		if(!$row['isrep']) {
			$ret .= "<small><font color=maroon>Предмет не подлежит ремонту</font></small><BR>";
		}
	} else { 
		$ret .= "<font color=maroon><B>Свойства предмета не идентифицированы</B></font><BR>";
	}

	if ($row['type'] == 27)	{
		$ret .= "Особенности:<br>• может одеваться на броню<br>";
	} elseif ($row['type'] == 28) {
		$ret .= "Особенности:<br>• может одеваться под броню<br>";
	}


	if  (($row['ab_mf'] > 0) or ($row['ab_bron'] > 0) or ($row['ab_uron']>0) || ($row['prototype'] >= 55510301 && $row['prototype'] <= 55510401)) {
		
/*
		$ekr_elka = array(55510312,55510313,55510314,55510315,55510316,55510317,55510318,55510319,55510320,55510321,55510322,55510334,55510335,55510336,55510337,55510338,55510339);
		$art_elka = array(55510323,55510324,55510325,55510326,55510327,55510340,55510341,55510342,55510343,55510344);
*/

		$ekr_elka = array(55510350);
		$art_elka = array(55510351);

	
		$elkabonus = 0;						
		if ((($row['prototype'] >= 55510301) AND ($row['prototype'] <= 55510311)) || (($row['prototype'] >= 55510328) AND ($row['prototype'] <= 55510333))) {
			//кредовые елки
			$elkabonus = 1;
		} elseif (in_array($row['prototype'],$ekr_elka)) {
			//екровые елки
			$elkabonus = 2;
		} elseif (in_array($row['prototype'],$art_elka)) {
			//артовые елки
			$elkabonus = 3;
		}
		if ($elkabonus > 0) {
			$ret .= "• рунного опыта:+".$elkabonus."%<br>";
		}

		if ($row['prototype'] == 55510351)
		{
			$ret .= "• опыта:+10%<br>";
		}
		
		
	} elseif (($_SERVER['PHP_SELF'] == '/cshop.php') AND (($row['id']>=6000) AND ($row['id'] <=6017))) {
		$runs_5lvl_param=array(
			"6000" =>array("ab_mf"=>0,"ab_bron"=>0,"ab_uron"=>1),
			"6001" =>array("ab_mf"=>0,"ab_bron"=>3,"ab_uron"=>0),
			"6002" =>array("ab_mf"=>1,"ab_bron"=>0,"ab_uron"=>0),
			"6003" =>array("ab_mf"=>0,"ab_bron"=>0,"ab_uron"=>1),
			"6004" =>array("ab_mf"=>0,"ab_bron"=>3,"ab_uron"=>0),
			"6005" =>array("ab_mf"=>1,"ab_bron"=>0,"ab_uron"=>0),
			"6006" =>array("ab_mf"=>0,"ab_bron"=>0,"ab_uron"=>1),
			"6007" =>array("ab_mf"=>0,"ab_bron"=>3,"ab_uron"=>0),
			"6008" =>array("ab_mf"=>1,"ab_bron"=>0,"ab_uron"=>0),
			"6009" =>array("ab_mf"=>0,"ab_bron"=>0,"ab_uron"=>1),
			"6010" =>array("ab_mf"=>0,"ab_bron"=>3,"ab_uron"=>0),
			"6011" =>array("ab_mf"=>1,"ab_bron"=>0,"ab_uron"=>0),
			"6012" =>array("ab_mf"=>0,"ab_bron"=>0,"ab_uron"=>1),
			"6013" =>array("ab_mf"=>0,"ab_bron"=>3,"ab_uron"=>0),
			"6014" =>array("ab_mf"=>1,"ab_bron"=>0,"ab_uron"=>0),
			"6015" =>array("ab_mf"=>0,"ab_bron"=>0,"ab_uron"=>1),
			"6016" =>array("ab_mf"=>0,"ab_bron"=>3,"ab_uron"=>0),
			"6017" =>array("ab_mf"=>1,"ab_bron"=>0,"ab_uron"=>0),
		);

		//правка отображения рун в cshop
		$ab = $runs_5lvl_param[$row[id]];
		$ret .= "Усиление:<br>";

		if ($ab['ab_mf'] > 0) $ret .= "• максимального мф.:0%<br>";
		if ($ab['ab_bron'] > 0) $ret .= "• брони:0%<br>";
		if ($ab['ab_uron'] > 0) $ret .= "• урона:0%<br>";
	}

	//отображение улучшений на артах
	if (($row['bonus_info']!='') or ($row['charka']!='') )
		{
		$bohtml=array(
		'bron1' => 'Броня головы',
		'bron2' => 'Броня корпуса',
		'bron3' => 'Броня пояса',
		'bron4' => 'Броня ног',
		'ghp' =>'Уровень жизни' ,
		'mfkrit' =>'Мф. критических ударов' ,
		'mfakrit' => 'Мф. против крит. ударов' ,
		'mfuvorot' => 'Мф. увертливости', 
		'mfauvorot' =>'Мф. против увертлив.',
		'gsila' =>'Сила' ,
		'glovk' => 'Ловкость' ,
		'ginta' => 'Интуиция', 
		'gintel' =>'Интеллект',
		'gmp' =>'Мудрость',
		
		'fstat' =>'Возможных увеличений',
		'fmf' =>  'Возможных увеличений мф' ,
		'gw' => 'Мастерство владения оружием' ,
		 'gm' => 'Магическое мастерство cтихий',
		
		'gnoj' =>'Мастерство владения ножами и кастетами' ,
		'gtopor' =>'Мастерство владения топорами и секирами',
		'gdubina' => 'Мастерство владения дубинами, булавами', 
		'gmech' =>'Мастерство владения мечами',
		'gfire' =>'Магическое мастерство cтихия огня',
		'gwater' => 'Магическое мастерство cтихия воды',
		'gair' => 'Магическое мастерство cтихия воздуха',
		'gearth' => 'Магическое мастерство cтихия земли',
		'ab_mf' =>'Усиление максимального мф',
		'ab_bron' => 'Усиление брони',
		'ab_uron' => 'Усиление урона');
		
		$pp=array(
		'mfkrit' =>'%' ,
		'mfakrit' => '%' ,
		'mfuvorot' => '%', 
		'mfauvorot' =>'%',
		'ab_mf' =>'%',
		'ab_bron' => '%',
		'ab_uron' => '%');
		
			if ($row['bonus_info']!='')
			{
				$inputbonus=unserialize($row['bonus_info']); //все данные
			if (is_array($inputbonus))
				{
					$ret .= "<small><b><a onclick=\"showhide('art_{$row['id']}');\" href=\"javascript:Void();\">Список улучшений:</a></small><BR>";
					$ret .= "<div id=art_{$row['id']} style=\"display:none;\"><small><b>";
					ksort($inputbonus);
					foreach($inputbonus as $blevl => $bdata) {
						$outtxt = 'X';
						if ($blevl == 1) $outtxt = 'I';
						if ($blevl == 2) $outtxt = 'II';
						if ($blevl == 3) $outtxt = 'III';
						if ($blevl == 4) $outtxt = 'IV';
						if ($blevl == 5) $outtxt = 'V';
						if ($blevl == 6) $outtxt = 'VI';

						$ret .= "&nbsp;&nbsp;Улучшение {$outtxt} уровня:<br>";
								foreach($bdata as $k => $v)											
									{
									$ret .= "&nbsp;&nbsp;• ".$bohtml[$k].": +{$v}".$pp[$k]."<br>";
									}
						}
					$ret .= "</b></small></div>";						
				}
			}
			if ($row['charka']!='')
			{

			$charka=substr($row['charka'], 2,strlen($row['charka'])-1); //откидываем первые два символа
			$inputbonus=unserialize($charka); //все данные

			if (is_array($inputbonus))
				{
					$ret .= "<small><b><a onclick=\"showhide('art_{$row['id']}');\" href=\"javascript:Void();\">Список чарований:</a></small><BR>";
					$ret .= "<div id=art_{$row['id']} style=\"display:none;\"><small><b>";
					ksort($inputbonus);
					foreach($inputbonus as $blevl => $bdata)			
						{

						$outtxt = 'X';
						if ($blevl == 1) $outtxt = 'I';
						if ($blevl == 2) $outtxt = 'II';
						if ($blevl == 3) $outtxt = 'III';
						if ($blevl == 4) $outtxt = 'IV';
						if ($blevl == 5) $outtxt = 'V';
						if ($blevl == 6) $outtxt = 'VI';

						$ret .= "&nbsp;&nbsp;Чарование {$outtxt} уровня:<br>";
								foreach($bdata as $pk => $pv)											
									{
									foreach($pv as $k => $v) 
										{
										$ret .= "&nbsp;&nbsp;• ".$bohtml[$k].": +{$v}".$pp[$k]."<br>";
										}
									}
						}
					$ret .= "</b></small></div>";						
				}			
			}
			

				
	}


	if (isset($_GET['otdel']) && in_array($_GET['otdel'],$unikrazdel) && ($_SERVER['PHP_SELF'] == "/_eshop.php" || $_SERVER['PHP_SELF'] == "/eshop.php")) {
		$ret .= "<small><b><a onclick=\"showhide('unik_{$row['id']}');return false;\" href=\"#\">Модификация:</a></small><BR>";
		$ret .= "<div id=unik_{$row['id']} style=\"display:none;\"><small><b>";


		if ($row['gsila'] > 0 || $row['glovk'] > 0 || $row['ginta'] > 0 || $row['gintel'] > 0 || $row['gmudra'] > 0) {
			$ret .= "• Статы: +3<br>";
		}
		if ($row['ghp'] > 0) $ret .= "• Уровень жизни: +20<br>";
		if ($row['bron1'] > 0 || $row['bron2'] || $row['bron3'] || $row['bron4']) $ret .= "• Броня: +3<br>";

		$ret .= "</b></small></div>";
	}

	if (($row['idcity']==1) OR (($row['idcity'] == null) AND ($_SERVER["SERVER_NAME"] == 'avaloncity.oldbk.com'))) {
		$ret .= "<small>Сделано в AvalonCity</small>";
	} elseif (($row['idcity']==2) OR (($row['idcity'] == null) AND ($_SERVER["SERVER_NAME"] == 'angelscity.oldbk.com'))) {
		$ret .= "<small>Сделано в AngelsCity</small>";
	} elseif (($row['idcity'] == 0) OR (($row['idcity'] == null) AND ($_SERVER["SERVER_NAME"] == 'capitalcity.oldbk.com'))) {
		$ret .= "<small>Сделано в CapitalCity</small>";
	}
			
	if($row['unik']==1) {
		$ret .= "<br><font color=red><small><b>Вещь с уникальной модификацией.</b></small></font>";
	}
	elseif(($row['unik']==2) and ($row['type']!=200) )  {
		$ret .= "<br><font color=#990099><small><b>Вещь с улучшенной уникальной модификацией.</b></small></font>";
	}
	
	if($row['type'] == 555) {
		$ret .= "<br><small><font color=red>Данное кольцо выведено из пользования и продажи. Обменять кольцо на новое можно в Ремонтной мастерской. Правила обмена можно прочитать на форуме в этом топе - <a target=_blank href=http://oldbk.com/forum.php?topic=228962139>http://oldbk.com/forum.php?topic=228962139</a>. Администрация ОлдБК</red></small>"; 
	}
	
	

		if (($row['bs_owner']==0) and ($user['ruines']==0) and ($row['labonly']==0) and ($row['prototype']!=55510000)  )
		{
		$ups_types=array(1,2,3,4,5,8,9,10,11);
		$ebarr=array(128,17,149,148);
		
		
		if ((strpos($row['name'], '[') == true) AND (in_array($row['prototype'],$ebarr) ) )
		{
		$ret .= "<br><small><font color=red>Внимание! Эта вещь подлежит бесплатному обмену либо деапу в Ремонтной мастерской, иначе она  перестанет надеваться после 00:00 27.09.14.</red></small>";
		}
		else
		if ((strpos($row['name'], '[') == true) AND ($row['art_param']!='') )
		{//на артах личных:
		$ret .= "<br><small><font color=red>Внимание! Эта вещь подлежит бесплатному обмену в Коммерческом отделе, иначе она  перестанет надеваться после 00:00 27.09.14.</red></small>"; 		
		}
		elseif ((strpos($row['name'], '[') == true) AND (($row['type']==28) OR $row['prototype']==100028 OR $row['prototype']==100029 OR $row['prototype']==173173 OR $row['prototype']==2003 OR $row['prototype']==195195) )
		{
		//на вещах которые надо деапать:
		$ret .= "<br><small><font color=red>Внимание! Эту вещь необходимо бесплатно деапнуть в Ремонтной мастерской, иначе она перестанет надеваться после 00:00 27.09.14.</red></small>"; 			
		}
		elseif ( (strpos($row['name'], '[') == true) AND (in_array($row['type'],$ups_types)) AND $row['ab_mf']==0  AND $row['ab_bron']==0  AND $row['ab_uron']==0   ) // не храм арты 
		{
		//на апнутых	
		$ret .= "<br><small><font color=red>Внимание! Эта вещь подлежит бесплатному обмену в ремонтной мастерской, иначе она перестанет надеваться после 00:00 27.09.14.</red></small>"; 
		}		
		}
	
	if($row['type']==556) {
		$ret .= "<br><small><font color=red>Данный предмет выведен из пользования и продажи. Администрация ОлдБК</red></small>"; 
	}		
	$ret .= "</td></TR>";
	if ($retdata > 0) {
		return $ret;
	} else {
		echo $ret;
	}
}

	
	
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<SCRIPT src='i/commoninf.js'></SCRIPT>
<script type="text/javascript" src="/i/globaljs.js"></script>
<SCRIPT>

function showhide(id)
	{
	 if (document.getElementById(id).style.display=="none")
	 	{document.getElementById(id).style.display="block";}
	 else
	 	{document.getElementById(id).style.display="none";}
	}
</SCRIPT>

</HEAD>
<body bgcolor=e2e0e0><div id=hint3 class=ahint></div><div id=hint4 class=ahint></div>
<H3>Редактор предметов</H3>
	
	<?
	          echo '<form name="" action="" method="post"><input name="new_it" type="hidden" value="'.$_POST[new_it].'">';
	          echo "Редактировать вещь (вещь должна быть в инвентаре):";
		  echo '<select size="1" name="new_it">';
	    	    $data=mysql_query(' select * from oldbk.inventory where owner='.$user[id].' AND (type <12 OR type=28 OR type=27) order by name, id;');
	            while($new_it=mysql_fetch_assoc($data))
	            {
	                echo '<option '.($new_it[id]==$_POST[new_it]?'selected':'').' value="'.$new_it[id].'">'.$new_it[name].'['.$new_it[unik].']['.$new_it[id].']</option>';
	            }
	            echo '</select>';
	           echo "&nbsp;<input type=submit value=Редактировать></form>";

		if ($_POST[new_it])
		{
		$ret = "";
		$itm=(int)$_POST[new_it];
			
				if ($_POST[save])
					{
					unset($_POST[save]);
					unset($_POST[new_it]);					

						foreach($_POST as $k => $v) 
							{
							$sqlstr.=mysql_real_escape_string($k)."='".$v."' , ";
							}

						mysql_query('update  oldbk.inventory  set '.$sqlstr.' battle=0, present="Admin-Editor" where owner='.$user[id].' AND (type <12 OR type=28 OR type=27) and id='.$itm.' limit 1;');
						if (mysql_affected_rows()>0) 
							{
							 echo "<b><font color=red>Все параметры успешно сохранены!</font></b>";	
							}
					}
					elseif($_POST[savecopy])
					{
					echo "TEst 1";
					unset($_POST[save]);
					unset($_POST[savecopy]);					
					unset($_POST[new_it]);		
						
						//грузим оригинал
						$citem=mysql_fetch_assoc(mysql_query(' select * from oldbk.inventory where owner='.$user[id].' AND (type <12 OR type=28 OR type=27) and id='.$itm.' '));			

						//накладываем изменения
						foreach($_POST as $k => $v) 
							{
							$citem[$k]=$v;
							}
						//готовим масив 
						
						$citem['present']='Admin-Editor';
						unset($citem['battle']);						
						unset($citem['id']);												
						
						foreach($citem as $k => $v) 
							{
							$sqlstr.="`".mysql_real_escape_string($k)."`='".$v."' , ";
							}

						mysql_query('insert into  oldbk.inventory  set '.$sqlstr.' battle=0  ');
						if (mysql_affected_rows()>0) 
							{
							 echo "<b><font color=red>Все параметры успешно сохранены! Создан новый предмет {$citem['name']} ['".mysql_insert_id()."'] </font></b>";	
							}
							else
							{
							 echo "<b><font color=red>Ошибка!</font></b>";	
							}
					}
		
		
			
			$q=mysql_query(' select * from oldbk.inventory where owner='.$user[id].' AND (type <12 OR type=28 OR type=27) and id='.$itm.' ');

			if (mysql_num_rows($q) > 0) 
			{
				$ret .= "<table border=2  WIDTH=100% CELLSPACING='0' CELLPADDING='2' BGCOLOR='#A5A5A5'>";
				while($row = mysql_fetch_assoc($q)) {
					if ($ff==0) { $ff = 1; $color = '#C7C7C7';} else { $ff = 0; $color = '#D5D5D5'; }
		
					$ret .= showitem_edit($row,0,false,$color,'',0,0,1);
				}
				$ret .= "</table>";
			
			echo "<form method=post>";
			echo "<input name=new_it type=hidden value=".$itm.">";
			echo $ret;
			echo "<input type=submit name=save value='Сохранить'> <br>
			<input type=submit name=savecopy value='Сохранить Копию'></form>";
			} 
		}
		
	
	?>




</BODY>
</HTML>

