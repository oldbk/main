<?
if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
	header("Location: index.php");
	die();
}

$skup_id=(int)$_POST['target'];
$test_item = mysql_fetch_array(mysql_query("select * from oldbk.skupka where itemid='{$skup_id}' ")); 
	
	if ($test_item['id']>0)
	{
		// уже есть скупка	
		echo "<font color=red><b>На этот предмет уже использовали свиток скидки: ".$test_item['stavka']."% !</b></font>";
	}
	else
	{
	$it = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE id= '{$skup_id}' and `owner` = '".$_SESSION['uid']."'  AND prototype not in (946,947,948,949,950,951,952,953,954,955,956,957,5101,5102,5103,7001,7002,7003,7005,7006,207,100028,100029,100030,100031)  AND (`type` < 12 OR `type`=27 OR `type`=28  OR `type`=555 OR `type`=556 OR  `type`=30 OR  `type`=33 )  AND dressed=0 AND `setsale` = 0 AND bs_owner =0 AND sowner =0 AND `prokat_idp` = 0 AND present!='Арендная лавка' AND arsenal_klan = '' "));	
	
		if ($it['id']>0)
		{
		//типа все гуд
		
				if ( (($it['prototype']>=222222230) AND ($it['prototype']<=222222235)) AND ($it['massa']=='1.1') )
				{
					echo "<font color=red><b>На подарочное кольцо нельзя использовать свиток скупки!</b></font>";
				}
				elseif ($it['cost']<=(EKR_TO_KR*1) )
				{
					echo "<font color=red><b>Стоимость предмета меньше стоимости свитка</b></font>";
				}
				else
				{
					$stav[181]=80;
					$stav[182]=85;		
					$stav[183]=90;		
					$stav[184]=95;		
					$stav[185]=100;		
					
					$stavka=$stav[$magic['id']];
					
					mysql_query("INSERT INTO `oldbk`.`skupka` SET `itemid`='{$it['id']}',`stavka`='{$stavka}' ");
					if(mysql_affected_rows()>0)
						{
						echo "<font color=red>Удачно использована магия <b>\"{$magic[name]}\"</b></font>";
						$bet=1;
						$sbet = 1;
						}
						else
						{
						echo "<font color=red><b>Ошибка!</b></font>";					
						}
				}
		}
		else
		{
		echo "<font color=red><b>Ошибка предмета!</b></font>";		
		}
	}
?>