<?
if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}

if (!isset($_GET['clearstored']))
{
$baff_type=$rowm['magic'];
$baff_name=$rowm['name'];


$conf_bonus[106601][0]=0.1; // опыт
$conf_bonus[106601][1]=0.1; // репутация
$conf_bonus[106601][2]=0.1; //РК


$conf_bonus[106602][0]=0.2; // опыт
$conf_bonus[106602][1]=0.2; // репутация
$conf_bonus[106602][2]=0.2; //РК

$conf_bonus[106603][0]=0.3; // опыт
$conf_bonus[106603][1]=0.3; // репутация
$conf_bonus[106603][2]=0.3; //РК


$conf_bonus[106604][0]=0.5; // опыт
$conf_bonus[106604][1]=0.5; // репутация
$conf_bonus[106604][2]=0.5; //РК


$abonus=$conf_bonus[$baff_type];

if ($user['battle'] > 0) 
{	
	echo "Нельзя использовать в бою..."; 
}
else 
{

			
		 $get_test_eff=mysql_fetch_array(mysql_query("select * from `effects` where owner='{$user['id']}' and type in (9100,9102,9103) limit 1;"));
		 if ($get_test_eff['id']>0)
		 {
		 	err('На Вас уже есть заклятие похожего действия!');
		 }
		 else
		{
			if ( ($baff_type==106604) and ((int)($_POST['target'])>=1) and ((int)($_POST['target'])<=2) )
				{
				$rmd=(int)($_POST['target']);
				}
				else
				{
				$rmd=mt_rand(1,2);
				
					//re_rand
					 $get_last_rnd=mysql_fetch_array(mysql_query("select * from `stol` where owner='{$user['id']}' and  stol=16600;"));
					 if ( ($get_last_rnd['count']==$rmd) and ($rmd==1) )
					 	{
					 	$rmd=2;
					 	}
				}

		$magictime=time()+($magic['time']*60);
		
					//запоминаем последний рандом 
					
					mysql_query("INSERT INTO `stol` SET `owner`='{$user['id']}',`stol`=16600,`count`='{$rmd}'  ON DUPLICATE KEY UPDATE `count`='{$rmd}'  ");
					
						if ($abonus[0]>0)
						{
						////////////////////
						$addinfo=$abonus[0];
						$effarrayche='Увеличение получаемого опыта на '.($addinfo*100).'%';

												
						mysql_query("INSERT INTO `effects` SET `type`= '9102',`name`='{$rowm['img']}:{$effarrayche}:{$baff_name}',`time`='{$magictime}',`owner`='{$user[id]}', add_info='{$addinfo}' ;");
						if (mysql_affected_rows()>0) 
							{
							mysql_query("UPDATE users set expbonus=expbonus+'{$abonus[0]}' where id='{$user[id]}' ; ");
							echo "Получен эффект «".$effarrayche."»<br>";			
							$sbet = 1;
							$bet=1;		
							$MAGIC_OK=1;		
							}
						}
						
						//////////////////////////
						if (($rmd==1) and ($abonus[1]>0) )
						{
						//репутация
						$effarrayche='Увеличение получаемой репутации';
						$prc=$abonus[1];
						
						mysql_query("INSERT INTO `effects` SET `type`='9100',  add_info='{$rowm['img']}:{$prc}:{$baff_name}' ,`name`='{$effarrayche}',`time`='{$magictime}',`owner`='{$user[id]}';"); 
						if (mysql_affected_rows()>0) 
							{
							mysql_query("UPDATE `users` SET `rep_bonus`=`rep_bonus`+'{$prc}' WHERE `id`='{$user['id']}'  ");
							echo "Получен эффект «".$effarrayche."+".($prc*100)."%».<br>";
							$sbet = 1;
							$bet=1;
							$MAGIC_OK=1;
							}
						}
						else
						if (($rmd==2) and ($abonus[2]>0) )
						{
						//////////////////////
						$addinfo = $abonus[2];						
						$effarrayche = 'Увеличение получаемого рунного опыта на '.($addinfo*100).'%';
							
						mysql_query("INSERT INTO `effects` SET `type`='9103',  add_info='{$addinfo}' ,`name`='{$rowm['img']}:{$effarrayche}:{$baff_name}',`time`='{$magictime}',`owner`='{$user[id]}';"); 
							if (mysql_affected_rows()>0) 
							{
							echo "Получен эффект «".$effarrayche."».<br>";
							$sbet = 1;
							$bet=1;		
							$MAGIC_OK=1;					
							}
						}
					
					
		
		}
		
		


}

}


?>