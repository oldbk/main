<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
$targ=($_POST['target']);

$sub_name[3]='темному';
$sub_name[2]='нейтральному';
$sub_name[6]='светлому';
$get_align=(int)($user[align]);
if ($get_align==1) {$get_align=6;}

$baff_name='Помощь '.$sub_name[$get_align].' собрату';
$baff_type=723;

if ($user['battle'] >0) {	
	echo "Это не боевая магия..."; 
} elseif ($user['in_tower']) {
	echo "Тут это использовать нельзя..."; 
} else {
	
		$jert = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$targ}' and battle>0  and hp>0   LIMIT 1;"));
	
		if ($jert[id] >0)
		{
			
			$q = mysql_query("SELECT * FROM battle WHERE id = ".$jert['battle']);
			$bd = mysql_fetch_assoc($q);
			if ($bd['type'] == 40 || $bd['type'] == 41 || $bd['type'] == 61 || $bd['type'] == 100 || $bd['type'] == 101) 
			{
				err("Нельзя попасть в этот бой с помощью этой абилки...");
				return;
			}
	
			$get_jert_align=(int)($jert[align]);
			if ($get_jert_align==1) {$get_jert_align=6;}
		
		 	if ($get_align==$get_jert_align)		
			{	
				//проверяем условия по склонкам
				// выбираем в бою цели, хоть одного против команды цели с моей склонкой или хоть одного НЕ моей склонки за команду жертвы
				$get_dbat=mysql_fetch_array(mysql_query("select count(id) as kol  from users where battle='{$jert[battle]}' and ( ( get_align(align)='{$get_align}' and battle_t!='{$jert[battle_t]}') OR ( get_align(align)!='{$get_align}' and battle_t='{$jert[battle_t]}') )"));
				
				if ($get_dbat[0]>0)
				{
					err('Вы не можете попасть в бой этим заклятием!');
				}
				else
				{	
					$zastup=true;
					include('helpbatl.php');
					
					if ($sbet==1)
					{
						//добавляем эффект на три хода - шоб потом выкинуть
						mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}',`time`=1999999999,`owner`='{$user[id]}',`lastup`=3,`battle`='{$jert[battle]}';");
						
						if ($user[hidden]>0 and $user[hiddenlog]=='') { $action = ""; }
						elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user);  if ($fuser[sex]==0) {$action="а"; } else {$action="";} }
						elseif ($user['sex'] == 1) {$action=""; }
						else { $action="а"; }
						
						$bet=1;
						$sbet = 1;
						echo "Все прошло удачно!";
						$MAGIC_OK=1;
					}
				}
				
			}
			else
			{
		     	err('Персонаж  "'.$targ.'"  Вам не собрат!');		
			}	
		}
		else
		{
			err('Персонаж  "'.$targ.'"  не в бою или уже погиб!');
		}


	} 
	


?>
