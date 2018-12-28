<?
if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}

if (!(isset($_GET['clearstored'])))
{
$che=(int)($_POST['target']);

	if ($che>0)
	{
	$item = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.inventory WHERE id='{$che}' and owner = '{$_SESSION['uid']}' and (sowner=0 or sowner = '{$user['id']}') AND type in (1,2,3,4,5,8,9,10,11,28) AND unik=0  AND `dressed`= 0 AND bs_owner = '".$user[in_tower]."' AND `setsale`=0 AND `prokat_idp` = 0 AND (arsenal_klan = '' OR arsenal_owner=1 ) AND present!='Арендная лавка' and name LIKE '%(мф)%' and name NOT LIKE '%футболка%' and (gsila > 0 or glovk > 0 or ginta > 0 or gintel > 0 or gmp > 0);"));
		
		if ($item['id']>0)
		{
		
		
				if ($item['nlevel']< $rowm['nlevel'])
				{
						echo "Нельзя использовать на предмет с требованием уровня меньше, чем требование уровня свитка!";
						return;
				}

				if ($item['sowner'] > 0) {
					if ($item['sowner'] != $user['id']) {
						echo "Владелец предмета не совпадает";
						return;
					}
				}
		                $str = "";
				if (strlen($item['mfinfo'])) {
					$mfinfo = unserialize($item['mfinfo']); 
					$mfinfo['stats'] += 1;
					$mfinfo = serialize($mfinfo);
					$str = ', mfinfo = "'.mysql_real_escape_string($mfinfo).'" ';
				}

				$str2 = "";
				if (($rowm['repcost'] > 0 && $rowm['getfrom'] == 43) OR ($rowm['getfrom'] == 131) OR ($rowm['getfrom'] == 132) OR ($rowm['getfrom'] == 31) OR ($rowm['getfrom'] == 35) )  
				{
					$str2 = ', sowner = '.$user['id'];
				}
		
				if (mysql_query("UPDATE oldbk.`inventory` SET `unik` = 1 , stbonus=stbonus+1 ".$str.", `sebescost` = `sebescost` + ".$rowm['cost']." ".$str2." WHERE `id` = {$item['id']} ;")) 
				{

					echo "<font color=red><b>Предмет \"{$item['name']}\" теперь имеет уникальную модификацию.<b></font> ";
					$bet=1;
					$sbet = 1;
					$infoc="Добавлено: стат: +1";
								 				$rec=array();
								 				$rec['owner']=$user['id'];
												$rec['owner_login']=$user['login'];
												$rec['owner_balans_do']=$user['money'];
												$rec['owner_balans_posle']=$user['money'];
								 				$rec['target'] = 0;
												$rec['target_login'] = 'Улучшение';
												$rec['type']=1181;
												$rec['sum_kr']=0;
												$rec['sum_ekr']=0;
												$rec['sum_kom']=0;
												$rec['item_count']=0;
												$rec['item_id']=get_item_fid($item);
												$rec['item_name']=$item['name'];
												$rec['item_count']=1;
												$rec['item_type']=$item['type'];
												$rec['item_cost']=$item['cost'];
												$rec['item_dur']=$item['duration'];
												$rec['item_maxdur']=$item['maxdur'];
												$rec['item_ups']=$item['ups'];
												$rec['item_unic']=$item['unik'];
												$rec['item_incmagic']=$item['includemagicname'];
												$rec['item_incmagic_count']=$item['includemagicuses'];
												$rec['add_info']="(id:".$rowm['id'].") ".$rowm['name']." ".$infoc;

												add_to_new_delo($rec); //юзеру
								 				$rec=array();
					
				}
				else 
				{
				echo "<font color=red><b>Произошла ошибка!<b></font>";
				}
		
		}
	}
}

?>