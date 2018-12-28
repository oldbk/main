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
<title>Восстановление клана</title>
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
<h2> Восстановление клана</h2><br><br>
<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
include "connect.php";
include "functions.php";
include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
	if(ADMIN)
	{
	 	if($_POST[res_klan])
	 	{
	 		$kl=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.clans WHERE short='".$_POST[res_klan]."' AND time_to_del>0"));
	 		if($kl[id]>0)
	 		{
				$sql='SELECT kh.*,u.klan,u.align FROM oldbk.users_klandel_hist kh 
				LEFT JOIN oldbk.users u
				on u.id=kh.uid
				WHERE kh.kid="'.$kl[id].'" AND u.align=0 AND u.klan="" ;'; 	
	 			$data=mysql_query($sql);
				$usr=array();	
				
	 			while($row=mysql_fetch_assoc($data))
				{
					
					mysql_query("UPDATE oldbk.users SET klan='".$kl[short]."', align='".$kl[align]."', `status` = 'Боец' WHERE id='".$row[uid]."'; ");
					//mysql_query("UPDATE avalon.users SET klan='".$kl[short]."', align='".$kl[align]."', `status` = 'Боец' WHERE id='".$row[uid]."'; ");
						//вертаем тело назад
					$usr[]=$row['uid'];
				}	
				echo mysql_error();
				echo "Вернули людей! <br>";

				// выставляем главенство
				mysql_query('UPDATE users SET status = "'.mysql_real_escape_string("<font color=#008080><b>Глава клана</b></font>").'" WHERE id = '.$kl['glava'].' LIMIT 1');
				
				$imges=mysql_query("select * from gellery_prot where klan_owner='{$kl[id]}'");
				$dt=array();		
				while($row = mysql_fetch_assoc($imges)) 
						{
						$dt[]=$row;		
						 }
			
				foreach($usr as $i => $id)
					{
						
							foreach($dt as $k => $row)
								{
								mysql_query("INSERT INTO `oldbk`.`gellery` SET `owner`={$id},`img`='{$row['img']}',`exp_date`=1999999999,`dressed`=0,`otdel`={$row['otdel']};");
								}
					}
					
				if (count($dt)>0)
					{
					echo "Вернули клановые образы картинок!<br>";
					}
				
				//убираем таймер на удаление клана, продлеваем оплату на месяц, убираем таймер на расформирование
				mysql_query("UPDATE oldbk.clans SET time_to_del=0, tax_timer=0, tax_date=".(time()+60*60*24*30)."  WHERE id=".$kl[id]."  ");
					echo "Продилли таймер оплаты на 30 денй <br>";

				mysql_query('UPDATE topsites.top SET ban = 0 WHERE klan = "'.$kl['short'].'"');
				echo "Разбанили сайт!<br>";				

	 		}
	 	}
	
		echo '<hr>';
		echo "<form method=post action=\"?\"><h4>Восстановление клана. </h4>
			<table><tr><td>Выберите клан </td><td>

     				<select size='1' name='res_klan'>
     				<option value=0>Клан</option>";
     				 $sql=mysql_query("SELECT * FROM oldbk.clans WHERE time_to_del>0 AND time_to_del>".time()." ORDER by short;");
              //  echo mysql_error();
		                   while($kl=mysql_fetch_array($sql))
		                   {
		  					echo "<option value=".$kl[short]." >".$kl[short]." (".(date("d.m.y",$kl[tax_timer])).")</option>";
		                   }
			echo "</select>
                    </td><td>
		<input type=submit value='Восстановить'>
		</td></tr>";
		echo "</table></form>";
	
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