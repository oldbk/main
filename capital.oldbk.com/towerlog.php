<?php
	//session_start();
	//if ($_SESSION['uid'] == null) header("Location: index.php");
	include "connect.php";	
	//$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
	include "functions.php";
	?>
	<HTML>
	<HEAD>
		<link rel=stylesheet type="text/css" href="i/main.css">
		<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
		<META Http-Equiv=Cache-Control Content=no-cache>
		<meta http-equiv=PRAGMA content=NO-CACHE>
		<META Http-Equiv=Expires Content=0>
		<script type="text/javascript" src="/i/globaljs.js"></script>		
	</HEAD>
	<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=e2e0e0>
	
	<?
	if($_GET['id']>0)
	{	
		$tr = mysql_fetch_array(mysql_query("SELECT * FROM `deztow_turnir` WHERE `id` = '".(int)$_GET['id']."'"));

			$ls = mysql_fetch_array(mysql_query("select count(`id`) from `users` WHERE `id` = '".(int)$_GET['id']."';"));
			$lss = mysql_query("select `id` from `users` WHERE `in_tower` = 1;");
			$i=0;
			while($in = mysql_fetch_array($lss)) {
				$i++;
				if($i>1) { $lors .= ", "; }
				$lors .= nick3($in[0]);
			}
		?>

				
				
					<H3>Башня смерти. Отчет о турнире . <? if($tr['art'] == 1) {echo "<img src='http://i.oldbk.com/i/artefact.gif' alt='Артовая БС' title='Артовая БС'> Артовая БС.";} ?></H3>
					Призовой фонд: <B><?=$tr['coin']?> кр.</B><BR>
					<?=$tr['log']?><BR>
		<?
		
	}
	else
	if($_GET['war']>0)
	{
			if ($_GET['war']>1000)
				{
					 $data=mysql_query('select * from oldbk.`clans_war_new` WHERE id="'.(int)$_GET['war'].'" limit 1');
				}
				else
				{
				 //$data=mysql_query('select * from oldbk.`clans_war_log` WHERE war_id="'.(int)$_GET['war'].'" limit 1');				
				}
	
	
			 if(mysql_num_rows($data)>0)			    
		    	  {
		    	  	?>
		    	  	<H3>Клановые войны. Отчет о войне.</H3>
		    	  	<?
		    	  	$row=mysql_fetch_assoc($data);
			    	$wrtxttype[1]='Дуэльная война';
				$wrtxttype[2]='Альянсовая война';				
		    	  
		    	  
				echo $wrtxttype[$row['wtype']]." между: ".$row['agr_txt'].($row['winner']==1?'<img src="http://i.oldbk.com/i/flag.gif">':''). ' <b> против </b>' .$row['def_txt'].($row['winner']==2?'<img src="http://i.oldbk.com/i/flag.gif">':''). ' Окончание: <span class="date">' . date ("d.m.y H:i:s" ,  strtotime($row['ftime']) ).'</span>'. ($row['winner']==3?' <b>Ничья</b> ':'').($row['winner']==4?' <b>Отказ в войне</b> ':'');
					echo '<hr><h3>Бои в CapitalCity:</h3><br>';
				
					  $data=mysql_query('select * from oldbk.`battle` WHERE war_id = "'.(int)$_GET[war].'";');
				    	  if(mysql_num_rows($data)>0)
				    	  {
				    	  	while($row=mysql_fetch_assoc($data))
				    	  	{
						          $k=$row['date'];   $kk=explode(" ",$k);  $d=explode("-",$kk[0]); $t=explode(":",$kk[1]);
							  $mmk=mktime($t[0], $t[1], $t[2], $d[1], $d[2], $d[0]);
						//	  $mmk=$mmk-3600; //-1 час
							  $row['date']=date("Y-m-d H:i:s", $mmk);
					
							echo "<span class=date>{$row['date']}</span> ";
					
							echo BNewRender($row['t1hist']);
							if ($row['win'] == 1) { echo '<img src=http://i.oldbk.com/i/flag.gif>'; }
							echo " против ";
					
							echo BNewRender($row['t2hist']);
							if ($row['win'] == 2) { echo '<img src=http://i.oldbk.com/i/flag.gif>'; }
					
							if ($row['CHAOS']==2 || $row['CHAOS']==-1)
							{
								echo "<IMG SRC=\"i/achaos.gif\" WIDTH=20 HEIGHT=20 ALT=\"бой с автоударом\">";
							}

								
							$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок (клановая битва)\"> ";
							echo $rr;
							
							echo " <a href='http://capitalcity.oldbk.com/logs.php?log={$row['id']}' target=_blank>»»</a><BR>";
							$i++;
						}
					}
					else
					{
						echo '<CENTER><BR><BR><B>В этот день не было боев, или же, летописец опять потерял свитки...</B><BR><BR><BR></CENTER>';
					}
					/*
					echo '<hr><h3>Бои в AvalonCity:</h3><br>';
				
					  $data=mysql_query('select * from avalon.`battle` WHERE war_id = "'.(int)$_GET[war].'";');
				    	  if(mysql_num_rows($data)>0)
				    	  {
				    	  	while($row=mysql_fetch_assoc($data))
				    	  	{
						          $k=$row['date'];   $kk=explode(" ",$k);  $d=explode("-",$kk[0]); $t=explode(":",$kk[1]);
							  $mmk=mktime($t[0], $t[1], $t[2], $d[1], $d[2], $d[0]);
						//	  $mmk=$mmk-3600; //-1 час
							  $row['date']=date("Y-m-d H:i:s", $mmk);
					
							echo "<span class=date>{$row['date']}</span> ";
					
							echo BNewRender($row['t1hist']);
							if ($row['win'] == 1) { echo '<img src=http://i.oldbk.com/i/flag.gif>'; }
							echo " против ";
					
							echo BNewRender($row['t2hist']);
							if ($row['win'] == 2) { echo '<img src=http://i.oldbk.com/i/flag.gif>'; }
					
							if ($row['CHAOS']==2 || $row['CHAOS']==-1)
							{
								echo "<IMG SRC=\"i/achaos.gif\" WIDTH=20 HEIGHT=20 ALT=\"бой с автоударом\">";
							}

								
							$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок (клановая битва)\"> ";
							echo $rr;
							echo " <a href='http://avaloncity.oldbk.com/logs.php?log={$row['id']}' target=_blank>»»</a><BR>";
							$i++;
						}
					}
					else
					{
						echo '<CENTER><BR><BR><B>В этот день не было боев, или же, летописец опять потерял свитки...</B><BR><BR><BR></CENTER>';
					}
					*/
		    	  }
		    	  else
		    	  {
		    	  	echo 'Ничего не найдено...';
		    	  	die();
		    	  }
			  
			
	}	
		
	?>				
		</BODY>
		</HTML>
	