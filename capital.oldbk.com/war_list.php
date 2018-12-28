<?php
//add by Fred 9 12 2010
//update by Fred 10/05/2014

	session_start();

	if (!($_SESSION['uid'] >0)) header("Location: index.php");
	include "connect.php";
	include "functions.php";

	if ($user['room'] != 57) { header("Location: main.php"); die(); }
	if (($_GET['war']!=1) AND ($_GET['war']!=2)  AND ($_GET['war']!=3))
	{
		$_GET['war']="2";
	}
	
	function make_time_log($data)
	{
		$time=explode(' ',$data);
		$date=explode('-',$time[0]);
		$mm=explode(':',$time[1]);
		$stump='<small><u>'.$date[2].'-'.$date[1].'-'.$date[0] . ' ' . $mm[0].':'.$mm[1].'</u></small>';
		return $stump;
	}
	
		?>
		<HTML><HEAD>
		<link rel=stylesheet type='text/css' href='http://i.oldbk.com/i/main.css'>
        <link rel="stylesheet" href="/i/btn.css" type="text/css">
		<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
		<META Http-Equiv=Cache-Control Content=no-cache>
		<meta http-equiv=PRAGMA content=NO-CACHE>
		<META Http-Equiv=Expires Content=0>
		<script type="text/javascript" src="/i/globaljs.js"></script>
		</HEAD>
		<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#e0e0e0>

			<TABLE border=0 width=100% cellspacing="0" cellpadding="0">
			<FORM action="main.php" method=GET>
			<tr><td align=center><h3>Зал клановых войн</td><td width=370 align=right>
                    <div class="btn-control">
                        <input class="button-mid btn" type="button" onclick="location.href='?war=<?=$_GET['war']?>';" value="Обновить">
                        <input class="button-mid btn" type="button" style="font-weight:bold;" onclick="location.href='zayavka.php';" value="Поединки" name="combats">
                        <INPUT class="button-dark-mid btn" TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/klanwar.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')"><br><br>
                        <input class="button-mid btn" type="submit" value="Карта миров" title="Карта миров" name="setch">
                        <input class="button-big btn" type="button" align="right" onclick="location.href='main.php?goto=plo';" value="Выйти на Центральную площадь"><br>
                    </div>
			</td></tr>
			</FORM>
			</table>

		<br>
		<div style='color: #8F0000; font-weight: bold; font-size: 18px; text-align: center;'>
		<TABLE border=1 width=100% cellspacing="0" cellpadding="3">
			<tr>
				<td width="15%" bgcolor="#A5A5A5" align="middle">
					<?echo "".((int)$_GET['war']!=1 ? "<a href='?war=1'>Подготовки к войнам</a>":"<font color='#8F0000'><B>Подготовки к войнам</B></font>").""; ?>
				</TD>
				<td width="15%" bgcolor="#A5A5A5" align="middle">
					<?echo "".((int)$_GET['war']!=2 ? "<a href='?war=2'>Текущие войны</a>":"<font color='#8F0000'><B>Текущие войны</B></font>").""; ?>
				</TD>
				<td width="15%" bgcolor="#A5A5A5" align="middle">
					<?echo "".((int)$_GET['war']!=3 ? "<a href='?war=3'>История войн</a>":"<font color='#8F0000'><B>История войн</B></font>").""; ?>
				</TD>
			</tr>
		</table>

			</div>
			<br>
<TABLE border=0 align=center cellspacing="0" cellpadding="0" width=100%>
	<tr>
		<td align=left valign=top  >&nbsp;</td>
		<td align=left valign=top width=85%>
			<?
				if ($_GET['war']=="1")
				{
				//подготовка
				$data=mysql_query("Select * from oldbk.clans_war_new where winner=0 and  (ztime<=NOW() AND stime>=NOW() ) ORDER BY id DESC");
				$wrtxttype[1]='Дуэльной войне';
				$wrtxttype[2]='Альянсовой войне';		

				$clntxt[1]='Клан';
				$clntxt[2]='Кланы';						

					while($row=mysql_fetch_array($data))
					{
					if ($user['id']==14897)
						{
						echo $row['id'];
						}
					
			                       echo "<span class=\"date\">".date ("d.m.y H:i:s" ,  strtotime($row['ztime']) )."</span> ".$clntxt[$row['wtype']]." ".$row['agr_txt']." готовятся к ".$wrtxttype[$row['wtype']]." против ".$row['def_txt']."     Время подготовки  истекает: <span class=\"date\">".date ("d.m.y H:i:s" ,  strtotime($row['stime']) ). "</span><hr>";
					}
				}
				else
				if ($_GET['war']=="2")
				{
						$data=mysql_query("Select * from oldbk.clans_war_new where winner=0 and  stime<=NOW() ORDER BY id DESC");
						$wrtxttype[1]='Дуэльная война';
						$wrtxttype[2]='Альянсовая война';		

						$clntxt[1]='Клан';
						$clntxt[2]='Кланы';	
				    	  while($row=mysql_fetch_assoc($data))
				    	  {
						echo "<span class=\"date\">".date ("d.m.y H:i:s" ,  strtotime($row['ztime']) ).'</span> '.$wrtxttype[$row['wtype']].': '.$row['agr_txt']. ' <b> против </b>' .$row['def_txt'].' Окончание: <span class="date">'.date ("d.m.y H:i:s" ,  strtotime($row['ftime']) ).'</span>';
						echo '<a href=towerlog.php?war='.$row['id'].' target=_blank> »» </a>';
						echo '<br><hr>';
				    	  }
				}
				else
				if ($_GET['war']=="3")
				{
					$view = 30; // кол. на страницу
					$limit = "";
					$pages="";
					if (isset($_GET['page'])) 
					{
						$page = intval($_GET['page']);
						$limit .= ' LIMIT '.($page*$view).','.$view.' ';
					} else {
					$page = 0;
					$limit .= ' LIMIT '.$view.' ';
					}
						$wrtxttype[1]='Дуэльная война';
						$wrtxttype[2]='Альянсовая война';		
						$clntxt[1]='Клан';
						$clntxt[2]='Кланы';	

					echo "<center><form method=post>";
					echo "Показать войны только клана: ";
					echo "<div class=\"btn-control\"><select size='1' name='klan_filt'><option value=0>Показать всех</option>";
					$sql_klan=mysql_query("select  co.id,co.short,cr.id as rid,cr.short as rshort  from oldbk.`clans` co  left join oldbk.`clans` cr  on co.rekrut_klan=cr.id where co.base_klan=0 AND (co.short !='Adminion' or co.short!='radminion') AND co.time_to_del=0   order by short ");

					
					while($kl=mysql_fetch_array($sql_klan))
					{

						  if ((strpos($kl['short'], 'tester') == false)  and (strpos($kl['short'], 'adminion') == false) )
						{

							echo "<option value=".$kl['id']." ".($kl['id']== $_POST['klan_filt'] ? "selected" : "")."  >".$kl[short].($kl['rid']>0?' - '.$kl['rshort']:'')."</option>";
						}
					}
					echo "</select> "; 
					echo "<input class='button-mid btn' type=submit name=look_filt value='Показать'></div>";
					echo "</form></center>";
					echo " <hr>";
					
					if ((int)($_POST['klan_filt'])>0)
					{	
						$klan_filt=(int)($_POST['klan_filt']);
						$add_filt=" and (wr.agressor='{$klan_filt}' OR wr.defender='{$klan_filt}' OR wr.agressor='{$klan_filt}' OR wr.id=(select warid from oldbk.clans_war_new_ally where clanid='{$klan_filt}' )  ) ";
						$limit = "";
						$no_page=true;
					}
					else
					{
						$add_filt='';					
					}

				$data=mysql_query("select SQL_CALC_FOUND_ROWS *, (if (wtype=2,(select sum(voin) from oldbk.clans_war_new_voin where war_id=wr.id and stor='agr'),(select sum(if (voin > (select voin from oldbk.clans_war_new_voin where war_id=voa.war_id and stor='def' and level=voa.level ),1,0)) as win_count_agr from oldbk.clans_war_new_voin voa where war_id=wr.id and stor='agr'))) as agr_voin,(if (wtype=2,(select sum(voin) from oldbk.clans_war_new_voin where war_id=wr.id and stor='def'),(select sum(if (voin < (select voin from oldbk.clans_war_new_voin where war_id=voa.war_id and stor='def' and level=voa.level ),1,0)) as win_count_def from oldbk.clans_war_new_voin voa where war_id=wr.id and stor='agr'))) as def_voin from oldbk.clans_war_new wr where winner!=0 ".$add_filt."  ORDER BY id DESC".$limit);

					$allcount = mysql_fetch_assoc(mysql_query('SELECT FOUND_ROWS() AS `allcount`'));
					$allcount = $allcount['allcount'];
					for ($i = 0; $i < ceil($allcount/$view); $i++) 
					{
						if ($page == $i) 
						{
						$pages .= '<b> '.($i+1).'</b> ';
	                                	} else 
	                                	{
						$pages .= ' <a href="?war=3&page='.$i.'">'.($i+1).'</a>';
						}
					}
					
				if (mysql_num_rows($data) >0)						
				{
				    	  while($row=mysql_fetch_assoc($data))
				    	  {
				    	  
				    	  	$pobeda1=($row['winner']==1?"<img src='http://i.oldbk.com/i/flag.gif'></img>":"");
				    	  	$pobeda2=($row['winner']==2?"<img src='http://i.oldbk.com/i/flag.gif'></img>":"");	
				    	  	$podeda3=($row['winner']==3?"<b>Ничья</b>":"");	
				    	  	$podeda4=($row['winner']==4?"<b>Отказ в войне</b>":"");					    	  	
				    	  
						echo "<span class=\"date\">".date ("d.m.y H:i:s" ,  strtotime($row['ztime']) ).'</span> '.$wrtxttype[$row['wtype']].': '.$row['agr_txt'].$pobeda1.'<b> против </b>' .$row['def_txt'].$pobeda2.' Завершена: <span class="date">'.date ("d.m.y H:i:s" ,  strtotime($row['ftime']) ).'</span> ';
						
						if ($podeda4=='') { 
										echo 'со счетом: '.(int)($row['agr_voin']).' / '.(int)($row['def_voin']).' '; 
										if (($row['agr_voin']==$row['def_voin'])   and ($row['winner']<3))
											{
											echo "<small>(победа по кол.выиграных боев)</small>";
											}
										
										}
						
						echo $podeda3.$podeda4;
						if ($podeda4=='')  { echo '<a href=towerlog.php?war='.$row['id'].' target=_blank> »» </a>'; }
						echo '<br><hr>';
				    	  }
				    	 if (!$no_page) { echo "Стр.:".$pages; }
				}
				else
					{
					echo err("Для данного клана нет информаци!");
					}
				    	  
				}
			?>
		    </td>
		    <td align=left valign=top >&nbsp;</td>
		</tr>
	</table>
<br>
<br>
<br>



<!--Rating@Mail.ru counter-->
<script language="javascript" type="text/javascript"><!--
d=document;var a='';a+=';r='+escape(d.referrer);js=10;//--></script>
<script language="javascript1.1" type="text/javascript"><!--
a+=';j='+navigator.javaEnabled();js=11;//--></script>
<script language="javascript1.2" type="text/javascript"><!--
s=screen;a+=';s='+s.width+'*'+s.height;
a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth);js=12;//--></script>
<script language="javascript1.3" type="text/javascript"><!--
js=13;//--></script><script language="javascript" type="text/javascript"><!--
d.write('<a href="http://top.mail.ru/jump?from=1765367" target="_blank">'+
'<img src="http://df.ce.ba.a1.top.mail.ru/counter?id=1765367;t=49;js='+js+
a+';rand='+Math.random()+'" alt="Рейтинг@Mail.ru" border="0" '+
'height="31" width="88"><\/a>');if(11<js)d.write('<'+'!-- ');//--></script>
<noscript><a target="_blank" href="http://top.mail.ru/jump?from=1765367">
<img src="http://df.ce.ba.a1.top.mail.ru/counter?js=na;id=1765367;t=49"
height="31" width="88" border="0" alt="Рейтинг@Mail.ru"></a></noscript>
<script language="javascript" type="text/javascript"><!--
if(11<js)d.write('--'+'>');//--></script>
<!--// Rating@Mail.ru counter-->

</BODY>
</HTML>
