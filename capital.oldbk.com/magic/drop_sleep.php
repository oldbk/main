<?php
if (!($_SESSION['uid'] >0))  { header("Location: index.php"); die();}
$baff_name='Снять Молчание';
				$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user[id]}' and `type` = '2' and pal=0 LIMIT 1;"));
				if ($effect['time'])
				{
					mysql_query("DELETE FROM`effects` WHERE `owner` = '{$user['id']}' and `type` = '2' and pal=0 ;");
					if (mysql_affected_rows()>0)
						{

							$qtype2 = mysql_query('SELECT * FROM effects WHERE type = 2 and owner = '.$effect['owner']);
							if (mysql_num_rows($qtype2) == 0) {
								mysql_query("UPDATE users set slp=0 where id='{$user[id]}' ; ");
							}


						
							 $mag_gif='<img src=i/magic/sleep_off.gif>';
							 if(($user['hidden'] > 0) and ($user['hiddenlog'] ==''))
							 {
							 $fuser['login']='<i>Невидимка</i>';
							 $sexi='использовал';
							 }
							 else
						 	{
							 $fuser=load_perevopl($user); //проверка и загрузка перевопла если надо
							 if ($fuser['sex'] == 1) {$sexi='использовал';  }	else { $sexi='использовала';}
							 }
				
						addch($mag_gif." <B>{$fuser['login']}</B>, использовал магию &quot;".$baff_name."&quot;",$user['room'],$user['id_city']);
						$bet=1;
						$sbet = 1;
						echo "Все прошло удачно!";
						$MAGIC_OK=1;
						}
						else 
						{
						err('Произошла ошибка!');
						}
					
				}
				else 
				{
					err("На Вас нет заклятия молчания(игрового)");
				}

?>
