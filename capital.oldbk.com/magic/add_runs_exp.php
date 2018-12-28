<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

//print_r($_POST);

$vexpa[84]=5000;
$vexpa[85]=10000;
$vexpa[86]=15000;

if ($rowm[magic]==87)
	{
	//мелкие свитки - у всех мелких свитков ид магии 87
	//значения сколько добавить опыта на руну берем из прототипа
		$vexpa[571]=100;
		$vexpa[572]=200;		
		$vexpa[573]=300;		
		$vexpa[574]=400;		
		$vexpa[575]=500;		
		$vexpa[576]=600;		
		$vexpa[577]=700;		
		$vexpa[578]=800;		
		$vexpa[579]=900;		
		$vexpa[580]=1000;		
	$vexp=$vexpa[$rowm['prototype']];		
	}
	else
	{
	$vexp=$vexpa[$rowm['magic']];
	}

$runa_id=(int)($_POST[target]);

	$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.inventory WHERE id='{$runa_id}' and owner = '{$_SESSION['uid']}' AND sowner = '{$_SESSION['uid']}' AND dressed = 0 AND present!='Арендная лавка' AND dressed = 0 AND setsale = 0 AND type =30 AND `prokat_idp`=0 and add_time>ups;"));
	if ($dress[id]>0)
	{
	
		if ($vexp>0)
		{
			mysql_query("update oldbk.inventory inv set ups=ups+{$vexp} where  id='{$dress[id]}' LIMIT 1;");
	
			if(mysql_affected_rows()>0)
			{
				$bet=1;
				$sbet = 1;
				echo "<font color=red><b>{$dress[name]} удачно получила {$vexp} опыта.<b></font>";
			}
			else
			{
			echo "Что-то не так... :(";
			}
		}
		else
		{
		echo "Ошибка свитка!";
		}
	
	}
	else
	{
	echo "Руна не найдена!";	
	}


?>