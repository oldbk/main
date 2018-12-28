<?php
ob_start("ob_gzhandler");
	session_start();
	
	$city_name[0]='CapitalCity';
	$city_name[1]='AvalonCity';
	
	if (!($_SESSION['uid'] >0)) header("Location: index.php");
	include "connect.php";
	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
	include "functions.php";
	
	if ($user[id_city]==0) { $db_other_city='avalon.';  $db_my_city='oldbk.'; $id_other_city=1; }
	else if ($user[id_city]==1) { $db_other_city='oldbk.' ; $db_my_city='avalon.'; $id_other_city=0; }
	else { $db_other_city='' ; $id_other_city=0;}
	$nw=time()-61;
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<link rel="stylesheet" href="/i/btn.css" type="text/css">
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
<script type="text/javascript">
  function show(ele) {
      var srcElement = document.getElementById(ele);
      if(srcElement != null) {
          if(srcElement.style.display == "block") {
            srcElement.style.display= 'none';
          }
          else {
            srcElement.style.display='block';
          }
      }
  }

var Hint3Name = '';
// Заголовок, название скрипта, имя поля с логином
function findlogin(title, script, name){
    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=sd4 value="6"><td colspan=2>'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT id="'+name+'" TYPE=text NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	document.getElementById(name).focus();
	Hint3Name = name;
}
function returned2(s){
	if (top.oldlocation != '') { top.frames['main'].location=top.oldlocation+'?'+s+'tmp='+Math.random(); top.oldlocation=''; }
	else { top.frames['main'].location='main.php?edit='+Math.random() }
}
function closehint3(){
	document.getElementById("hint3").style.visibility="hidden";
    Hint3Name='';
}
</script>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#e2e0e0><div id=hint3 class=ahint></div>
<table width=100%>
<tr>
	<td align=right>
        <div class="btn-control">
		    <INPUT class="button-mid btn" TYPE="button" onclick="location.href='main.php';" value="Вернуться" title="Вернуться">
        </div>
	</td>
</tr>
<tr>
	<td valign=top>
		<center>
			<h3><A HREF="#" OnClick="top.AddToPrivate('pal', top.CtrlPress);"><img src="i/lock.gif" width=20 height=15></A> Орден Света
			<BR><?
				$cha = mysql_fetch_array(mysql_query("SELECT `name` FROM `chanels` WHERE `klan`='pal' AND `user` = '".$user['id']."';"));
				
				$cha = explode(",",$cha['name']);
				if($cha[0]) {
					if($user[id]==28453 || $user[id]==326)
					{
						foreach ($cha as $v)
						{
								?><A href="#" OnClick="top.AddToPrivate('klan-pal-<?=$v?>', top.CtrlPress);"><img src="i/lock.gif" width=20 height=15></A> klan-pal-<?=$v?> <?							
						}	
							
					}
					echo '<br>';
					foreach ($cha as $v)
					{
							?><A href="#" OnClick="javascript:top.AddToPrivate('pal-<?=$v?>', top.CtrlPress);"><img src="i/lock.gif" width=20 height=15></A> pal-<?=$v?> <?							
					}
					
				}
				
				
				
			?>
			</h3>

			<?php
			if ($user['id'] == 648 || $user['id'] == 102904) {
				if (isset($_GET['showhide'])) {
					echo '<a href="?">Убрать скрытых</a><br><br>';
				} else {
					echo '<a href="?showhide=1">Показать скрытых</a><br><br>';
				}
			}

			?>
		<table>
			<tr>
			<td>
				<?
					$pals=array();
					$data=mysql_query("SELECT * FROM `users` u 
							    WHERE `align` > 1 and `align` < 2 order by  align desc, login asc ;");
					while ($row = mysql_fetch_array($data)) 
					{

						if($row[id_city]==$user[id_city])
						{
							$pals_my.=$row[id].',';
						}
						else
						if($row[id_city]==$id_other_city)
						{
							$pals_other.=$row[id].',';
						}
					}		    
					$pals_my=substr($pals_my,0,-1);
					$pals_other=substr($pals_other,0,-1);
					
					echo "<h3>".$city_name[$user[id_city]]."</h3>";
					if ($pals_my!='')
					{
					$data=mysql_query("SELECT `id`, `login`, `status`, `level`, `room`, `align`, `lab`, `ldate`>= ".(time()-60)." as `online` FROM ".$db_my_city."`users` u 
							    WHERE `align` > 1 and `align` < 2 AND u.id in (".$pals_my.") order by  align desc, login asc ;");
					while ($row = mysql_fetch_array($data)) 
					{
						if ($user['id'] == 648 || $user['id'] == 102904) {
							if ($row['align'] == "1.92" && !isset($_GET['showhide'])) continue;
						}

						if ($row['online']>0) {
							echo '<A href="#" OnClick="javascript:top.AddToPrivate(\'',$row['login'],'\', top.CtrlPress);"><img src="i/lock.gif" width=20 height=15></A>';
							nick2($row['id']);
							  if($row['room'] > 500 && $row['room'] < 561) { $rrm = 'Башня смерти, участвует в турнире';}
							  elseif ($row['lab'] > 0) { $rrm = 'Лабиринт Хаоса'; }
							  elseif ($row['in_tower'] ==3) { $rrm = 'Турниры:Одиночные сражения'; }								  
							  elseif ($row['ruines'] > 0) { $rrm = 'Руины'; }
							  elseif ($row['room'] >= 70000 && $row['room'] <= 72000) { $rrm =  'Замки'; }
							  elseif ($row['room'] >= 49998 && $row['room'] <= 60000) {
								include "map_config.php";
								reset($map_locations);
								$bfound = false;
								while(list($k,$v) = each($map_locations)) {
									if ($row['room'] == $v['room']) {
									    	$rrm = $v['name'];
										$bfound = true;
									}
								}
								if (!$bfound) $rrm = 'Загород'; 
							} else { $rrm = $rooms[$row['room']]; }


							echo ' - ',$row['status'],' - <i>',$rrm,'</i><BR>';
						}
					}
					

					$data=mysql_query("SELECT `id`, `login`, `status`, `level`, `room`, `align`, `ldate`>= ".(time()-60)." as `online` FROM ".$db_my_city."`users` u 
					WHERE `align` > 1 and `align` < 2 AND u.id in (".$pals_my.") order by  align desc, login asc ;");
					while ($row = mysql_fetch_array($data)) {

						if ($user['id'] == 648 || $user['id'] == 102904) {
							if ($row['align'] == "1.92" && !isset($_GET['showhide'])) continue;
						}


						if ($row['online']<1) {
							echo '<img src="i/lock1.gif" width=20 height=15>';
							nick2($row['id']);
							echo ' - ',$row['status'],' - <i><small><font color=gray>персонаж не в клубе</font></small></i><BR>';
						}
					}
				}
					
					
					echo "<h3>".$city_name[$id_other_city]."</h3>";
					
				if  ($pals_other!='')
				{
					$data=mysql_query("SELECT `id`, `login`, `status`, `level`, `room`, `align`, `lab`, `ldate`>= ".(time()-60)." as `online` FROM ".$db_other_city."`users` u 
							    WHERE `align` > 1 and `align` < 2 AND u.id in (".$pals_other.") order by  align desc, login asc ;");
					while ($row = mysql_fetch_array($data)) 
					{

						if ($user['id'] == 648 || $user['id'] == 102904) {
							if ($row['align'] == "1.92" && !isset($_GET['showhide'])) continue;
						}

						if ($row['online']>0) {
							echo '<A href="#" OnClick="javascript:top.AddToPrivate(\'',$row['login'],'\', top.CtrlPress);"><img src="i/lock.gif" width=20 height=15></A>';
							nick2($row['id']);
							  if($row['room'] > 500 && $row['room'] < 561) { $rrm = 'Башня смерти, участвует в турнире';}
							  elseif ($row['lab'] > 0) { $rrm = 'Лабиринт Хаоса'; }
							  elseif ($row['in_tower'] ==3) { $rrm = 'Турниры:Одиночные сражения'; }								  
							  elseif ($row['ruines'] > 0) { $rrm = 'Руины'; }
							  elseif ($row['room'] >= 70000 && $row['room'] <= 72000) { $rrm =  'Замки'; }
							  elseif ($row['room'] >= 49998 && $row['room'] <= 60000) {
								include "map_config.php";
								reset($map_locations);
								$bfound = false;
								while(list($k,$v) = each($map_locations)) {
									if ($row['room'] == $v['room']) {
									    	$rrm = $v['name'];
										$bfound = true;
									}
								}
								if (!$bfound) $rrm = 'Загород'; 
							} else { $rrm = $rooms[$row['room']]; }
							echo ' - ',$row['status'],' - <i>',$rrm,'</i><BR>';
						}
					}
				
					
					$data=mysql_query("SELECT `id`, `login`, `status`, `level`, `room`, `align`, `ldate`>= ".(time()-60)." as `online` FROM ".$db_other_city."`users` u 
					WHERE `align` > 1 and `align` < 2 AND u.id in (".$pals_other.") order by  align desc, login asc ;");
					while ($row = mysql_fetch_array($data)) {
						if ($user['id'] == 648 || $user['id'] == 102904) {
							if ($row['align'] == "1.92" && !isset($_GET['showhide'])) continue;
						}


						if ($row['online']<1) {
							echo '<img src="i/lock1.gif" width=20 height=15>';
							nick2($row['id']);
							echo ' - ',$row['status'],' - <i><small><font color=gray>персонаж не в клубе</font></small></i><BR>';
						}
					}
				}	
					
					
				?>
			</td>
			</tr>
		</table>
		</center>
	</td>

</tr>
</table>
</body>
</html>