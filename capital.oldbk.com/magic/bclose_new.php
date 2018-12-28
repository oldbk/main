<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
$do_not_close=array(10000,190672,14897,9,10);
//Изолирование боя до 10 человек

if ($user['battle'] == 0) {	
	err("Это боевая магия...");
} elseif($user[hp]<=0) {      
	err('Для Вас бой окончен!');        
} else {
	//проверяем данные о бое

	$badbattle = array(61,40,41,13,14,15,170,171,172);

	$get_battle_data = mysql_fetch_array(mysql_query("select * from battle where id='{$user[battle]}' and win=3 and `status`=0 and t1_dead=''; "));
	$arrt1=explode(";",$get_battle_data['t1']);
	$arrt2=explode(";",$get_battle_data['t2']);
		
	if ($get_battle_data[id]>0) {
		if ($get_battle_data[teams] != '') {
			err("Этот бой уже закрыт от вмешательства!");
		} elseif ($user['in_tower'] > 0 || in_array($get_battle_data['type'],$badbattle) !== FALSE || $get_battle_data['war_id'] > 0 || $user['lab'] > 0 || ($get_battle_data['type'] >= 211 and $get_battle_data['type'] < 240) || ($get_battle_data['type'] > 240 and $get_battle_data['type'] < 270) || ($get_battle_data['type'] > 270 and $get_battle_data['type'] < 290)) {
			err('Нельзя использовать в этом поединке!');
		} else if ( ($get_battle_data['coment'] =='Бой с Исчадием Хаоса') OR ($get_battle_data['coment'] =='<b>Бой с Волнами Драконов</b>') OR ($get_battle_data['coment'] =='<b>Бой с Пятницо</b>')  OR   (search_arr_in_arr($do_not_close,array_merge($arrt1,$arrt2))==true)  ) 
		{
		err('Нельзя использовать в этом поединке!');
		}
		elseif (users_in_battle($get_battle_data[id]) > 10) {
			err('В бою уже более 10 человек, использовать нельзя!');
		} else {
			mysql_query("UPDATE battle set teams='20000:||:{$magic['name']}' WHERE `id` = '{$user[battle]}' and status=0 and win=3  LIMIT 1;");
		
				if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
				elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }

	       	       addlog($user['battle'],"!:M:".time().':'.nick_new_in_battle($user).':'.($user[sex]+500).":719\n");
			
			$bet=1;
			$sbet = 1;
			echo "Все прошло удачно!";
			$MAGIC_OK=1;
		}
	} else {
		err('Этот бой уже окончен!');
	}   
		
}


?>