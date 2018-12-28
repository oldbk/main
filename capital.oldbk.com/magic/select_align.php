<?php
if (!($_SESSION['uid'] >0)) {
	header("Location: index.php");
	die();
}
$ust=(int)($_POST['target']);
$ual=(int)$user['align'];

$eff_align_type=5001;
$eff_align_time=time()+60*60*24*30*2;

$st[2]='нейтральную';
$st[3]='темную';
$st[6]='светлую';

if ($ust==$ual)
{
	err("Зачем?");
	return;
}
else
if (($ust==2) or ($ust==3) or ($ust==6))
	{
		if ($user['battle'] > 0) {
			err("Не в бою...");
			return;
		} 
		
		
		
		if ($user['klan']!='') {
			err("Нельзя использовать находясь в клане!");
			return;
		}

		if ($user['align']==4) {
			err("Нельзя использовать, находясь в хаосе!");
			return;
		}		

		
		
		
		mysql_query("UPDATE `oldbk`.`users` SET `align`='{$ust}' WHERE `id`='{$user['id']}' ");
		if (mysql_affected_rows()>0)
				{
					
						$cheff=mysql_fetch_array(mysql_query("SELECT * from  effects WHERE type = '".$eff_align_type."' AND owner = '".$user['id']."' LIMIT 1;"));
						if ($cheff['id']>0)
							{
							//удаляем то что есть!
							mysql_query("DELETE from  effects WHERE id='{$cheff['id']}' LIMIT 1; ");
							//какая была в штафе
							//запоминаем старую склонку
							mysql_query("INSERT INTO users_last_align (`owner`,`align`) VALUES      ('".$user['id']."','".$cheff['add_info']."')	ON DUPLICATE KEY UPDATE align='".$cheff['add_info']."';");
							}

							//новый штраф склонки
							$sql="INSERT INTO effects (`type`, `name`, `owner`, `time`, `add_info`)  VALUES  ('".$eff_align_type."','Штраф склонки','".$user['id']."','".$eff_align_time."','".$ust."');";
							mysql_query($sql);
							
						
				
				echo "Все прошло удачно! Вы получили <b>".$st[$ust]." склонность</b>! ";
				$bet=1;
				$sbet = 1;
				}
				else
				{
				echo "что-то пошло не так!";
				}

	}

?>