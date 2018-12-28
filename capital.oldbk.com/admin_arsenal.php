<?
session_start();
/////////////////////////////////////////////////////
    if (isset($miniBB_gzipper_encoding)) {
    $miniBB_gzipper_in = ob_get_contents();
    $miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    $miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
    $miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
    $gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
    $percent = round($gzpercent);
    $miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    ob_clean();
    header('Content-Encoding: '.$miniBB_gzipper_encoding);
    echo $miniBB_gzipper_out;
    }
/////////////////////////////////////////////////////
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<title>Просмотреть/изъять/отвязать/положить/привязать из клан арсенала</title>
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content="no-cache, max-age=0, must-revalidate, no-store">
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
<style>
	.row {
		cursor:pointer;
	}
</style>
<SCRIPT>
function showhide(id) {
	if (document.getElementById(id).style.display=="none")
	{document.getElementById(id).style.display="block";}
	else
	{document.getElementById(id).style.display="none";}
}
</SCRIPT>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 ><br>
<h2>Просмотреть/изъять/отвязать/положить/привязать из клан арсенала</h2><br><br>
<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
include "connect.php";
include "functions.php";
include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
function print_klans_ars_put()
{
global $user;

echo "<td valign=top><form method=post><b>Просмотреть / изъять-отвязать / положить-привязать  из клан арсенала</b><br>";
echo "<select size='1' name='klan_ars_put'>
	<option value=0>Выберите клан</option>";
	$sql_klan=mysql_query("SELECT * FROM oldbk.clans  order by short;");
	while($kl=mysql_fetch_array($sql_klan))
	{
		echo "<option value=".$kl[id]." ".($kl[id]== $_POST[klan_ars_put] ? "selected" : "")."  >".$kl[short]."</option>";
	}
	echo "</select>";
echo "<input type=submit name=arslook_put value='Просмотр'><br>";
echo "</from>";
	$_POST[klan_ars_put]=(int)($_POST[klan_ars_put]);
	
	$_POST[klan_ars_put_item]=(int)$_POST[klan_ars_put_item];
	
	if (($_POST[klan_ars_put_item]>0)and ($_POST[arslook_put_item]))
		{
		echo '<br><br>';
		$itklan=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.clans where  id='{$_POST[klan_ars_put]}' ;"));
		if ($itklan[id]>0) 
			{
			mysql_query("UPDATE oldbk.inventory  SET owner='{$user[id]}' , arsenal_owner='0', arsenal_klan='' where id='{$_POST[klan_ars_put_item]}' and owner=22125 and dressed=0 ;");
			if (mysql_affected_rows()>0) 
				{
				mysql_query("DELETE FROM oldbk.clans_arsenal where id_inventory='{$_POST[klan_ars_put_item]}'");
				//пишем в лог арсенала об изъятии ?
				$item=mysql_fetch_array(mysql_query("SELECT * from oldbk.inventory where id='{$_POST[klan_ars_put_item]}' ; "));
				$log_text = '"'.$user[login].'" изъял из арсенала "'.$item[name].'" ['.$item[duration].'/'.$item[maxdur].'] [ups:'.$item['ups'].'/unik:'.$item[unik].'/inc:'.$item['includemagicname'].']';
				mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$itklan[short]}','{$user[id]}','{$log_text}','".time()."')");
				err('Предмет удачно отвязан из арсенала и находится у вас в инвентаре!<br>');
				}
			}
			else
			{
			err('Клан не найден!');
			}
		
		}
		elseif (($_POST[item_toars]>0)and ($_POST[item_toars_put]))
		{
		$itklan=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.clans where  id='{$_POST[klan_ars_put]}' ;"));
		if ($itklan[id]>0) 
			{
			mysql_query("UPDATE oldbk.inventory  SET owner='22125' , arsenal_owner='1', arsenal_klan='{$itklan[short]}' where id='{$_POST[item_toars]}' and owner='{$user[id]}' and dressed=0 ;");
			if (mysql_affected_rows()>0) 
				{
				mysql_query("INSERT INTO `oldbk`.`clans_arsenal` SET `id_inventory`='{$_POST[item_toars]}',`klan_name`='{$itklan[short]}',`owner_original`=1,`owner_current`=0,`gift`=0,`all_access`=1;");				
				//пишем в лог ?
				$item=mysql_fetch_array(mysql_query("SELECT * from oldbk.inventory where id='{$_POST[item_toars]}' ; "));
				$log_text = '"'.$user[login].'" передал в арсенал "'.$item[name].'" ['.$item[duration].'/'.$item[maxdur].'] [ups:'.$item['ups'].'/unik:'.$item[unik].'/inc:'.$item['includemagicname'].']';
				mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$itklan[short]}','{$user[id]}','{$log_text}','".time()."')");
				err("Предмет удачно передан в арсенал клана {$itklan[short]} !<br>");
				}
				else
				{
				err("Предмет не найден или одет!");
				}
			
			}
			else
			{
			err('Клан не найден!');
			}
		}
	
	
	if ($_POST[klan_ars_put]>0) 
	{
	echo "<form method=post>";
	echo "<select size='1' name='klan_ars_put_item'>
	<option value=0>Выберите предмет</option>";
	$sql_klans_items=mysql_query("select * from oldbk.inventory where arsenal_klan=(select short from oldbk.clans where id='{$_POST[klan_ars_put]}')  and arsenal_owner=1 and owner=22125 and dressed=0  order by type;");
	while($kitem=mysql_fetch_array($sql_klans_items))
	{
		echo "<option value=".$kitem[id]." ".($kitem[id]== $_POST[klan_ars_put_item] ? "selected" : "")."  >(id:".$kitem[id].") ".$kitem[name]."</option>";
	}
	echo "</select>";
	echo "<input type=submit name=arslook_put_item value='Изъять из арсенала'><br><br>";
	

	echo "ID предмета (должен находиться у вас в инвентаре):<input type=text name=item_toars>";
	echo "<input type=submit name=item_toars_put value='Передать в арсенал'><br>";
	echo "</form>";
	}
	
//print_r($_POST);

echo "</td>";

}


if(($user[align]>1&&$user[align]<2)||($user[align]>2&&$user[align]<3) || $user[align]==5 || $user[align]==7)
	{
		$access=check_rights($user);
		//print_r($access);
	}

        if ($access[klans_ars_put])
        {
        	$put_ars_klans=true;   //изымать вещь из арсенала (привязанную к арсу) и также возможность привязывать вещь к арсеналу.
			echo '<table><tr>';
			print_klans_ars_put();
			echo '</table>';
	}
	else
	{
	echo "Доступ закрыт!";
	}
?>	
</body>
</html>
<?
/////////////////////////////////////////////////////
    if (isset($miniBB_gzipper_encoding)) {
    $miniBB_gzipper_in = ob_get_contents();
    $miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    $miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
    $miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
    $gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
    $percent = round($gzpercent);
    $miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    ob_clean();
    header('Content-Encoding: '.$miniBB_gzipper_encoding);
    echo $miniBB_gzipper_out;
    }
/////////////////////////////////////////////////////
?>