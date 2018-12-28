<?

//компресия для инфы
///////////////////////////
    if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
    $miniBB_gzipper_encoding = 'x-gzip';
    }
    if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
    $miniBB_gzipper_encoding = 'gzip';
    }
    if (isset($miniBB_gzipper_encoding)) {
    ob_start();
    }
    function percent($a, $b) {
    $c = $b/$a*100;
    return $c;
    }
//////////////////////////////
 	session_start();
	if (!($_SESSION['uid'] >0)) {  header("Location: index.php"); die(); }
	include 'connect.php';
	
	if (isset($_GET['page']))
	{
	$_GET['page']=(int)$_GET['page'];
	}
	
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
</HEAD>
<body bgcolor=e2e0e0><div id=hint3 class=ahint></div><div id=hint4 class=ahint></div>
<TABLE border=0 width=100% cellspacing="10" cellpadding="10">
<tr>
<td><h3>Победители Пасхальной лотереи:</td>
<td align=right>
<FORM action="main.php" method=GET>
<INPUT TYPE="submit" value="Вернуться" name="ex">
</FORM>
<FORM method=GET>
<INPUT TYPE="hidden" value="<?=$_GET['page'];?>" name="page">
<INPUT TYPE="submit" value="Обновить" name="refresh">
</FORM>
</td></tr>

<tr>
<td valign=top>
<?
					$view = 50; // кол. на страницу
					if (isset($_GET['page'])) 
					{
						$page = intval($_GET['page']);
						$limit .= ' LIMIT '.($page*$view).','.$view.' ';
					} else {
					$page = 0;
					$limit .= ' LIMIT '.$view.' ';
					}

$get_winners=mysql_query("SELECT SQL_CALC_FOUND_ROWS * from oldbk.event_loto_win order by id ".$limit);
$k=$page*$view;
	if (mysql_num_rows($get_winners)>0 )
		{
				$q2 = mysql_query('SELECT FOUND_ROWS() AS `allcount`') or die();
				$allcount = mysql_fetch_assoc($q2);
				$allcount = $allcount['allcount'];
				$pages = "";
				for ($i = 0; $i < ceil($allcount/$view); $i++) 
				{
					if ($page == $i) {
						$pages .= '<b> '.($i+1).'</b> ';
	                                } else {
						$pages .= ' <a href="?page='.$i.'">'.($i+1).'</a>';
					}
				}
				
			echo ' <TABLE BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">';
			while ($re = mysql_fetch_assoc($get_winners))
			{
			$k++;
			$color = $i % 2 == 0 ? '#C7C7C7' : '#D5D5D5';
			echo '<tr bgcolor='.$color.'>';
			echo "<td>$k</td><td>";
			echo $re['inf'];
			echo "</td>";
			echo "</tr>";
			}
			echo "</table>";
			
			if ($pages)
				{
				echo "Страницы:".$pages;
				}
			
		}
		else
		{
		echo "<b>Пока нет данных!</b>";
		}


?>
</td>
    <td>&nbsp;</td>
  </tr>
</table>
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