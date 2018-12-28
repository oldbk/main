<?
//магазимн абилок
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
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

include "connect.php";
include "functions.php";

	function Redirect($path) {
		header("Location: ".$path);
		die();
	}

// проверка на румы
if ($user['room'] != 28) Redirect("main.php");
if ($user['battle'] != 0) Redirect("fbattle.php");


if ($user[klan]=='radminion') {  echo "Admin-info:<!- GZipper_Stats -> <br>"; }
include "clan_kazna.php";
if ($user[klan]!='')
{
  $clan_id=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`clans` WHERE `short` = '{$user[klan]}' LIMIT 1;"));
   if ($clan_id[id] >0)
    {
    	if ($clan_id[glava]==$user[id])
		{
			$clan_kazna=clan_kazna_have($clan_id[id]);
		}
	}
}

?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
<SCRIPT LANGUAGE="JavaScript">

function callc(kol,magid,ecost)
{

var total=0;

total=(ecost*kol).toFixed(2);

document.getElementById("total"+magid).innerHTML ='Общая стоимость:'+total+' екр. Всего:'+kol+' шт.';
}



</SCRIPT>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#e0e0e0>
<?
 if ($clan_kazna)
 {
?>
<TABLE border=0 width=100% cellspacing="0" cellpadding="0">
<FORM action="city.php" method="GET">
<tr>
	<td width=33% ><h3><a href=?look=1>Магазин</a></h3></td>
	<td width=33% ><h3><a href=?mylook=1>Уже куплено</a></h3></td>	
	<td width=33% align=right>
	<INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/abilshop.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
	<INPUT TYPE="submit" value="Вернуться" name="strah"></td>
</tr>
	</FORM>
</table>

<TABLE border=0 width=100% cellspacing="0" cellpadding="4">
<TR>
	<TD valign=top align=left>
<!--Магазин-->
<form method=post name="f1">
<TABLE border=0 width=100% cellspacing="0" cellpadding="0" bgcolor="#A5A5A5">
<TR>

	<TD align=center><B>Можно купить Клановые реликты</B>
	<?
	$abiid=(int)($_POST[abilid]);
	$allkol=(int)($_POST[allcount]);
	
	  if (($_POST[buy]) and ($abiid>0) and ($allkol>0))
	  {
	      //ищем абилку
	       $get_abil=mysql_fetch_array(mysql_query("select * from oldbk.abil_shop where magic='{$abiid}' and open=1"));
	       if ($get_abil[magic]>0)
	       {
	       //абилка есть
	       $total_to_add=$allkol;//всего абилок
	       $total_cost=$total_to_add*$get_abil[ecost];
	       
	       //проверяем могули я купить
	       $coment="\"".$user['login']."\" купил «клановый реликт: \"".$get_abil['name']."\"»  {$total_to_add} шт.";

		if  (by_from_kazna($clan_id[id],2,$total_cost,$coment))
		{ 
	        $clan_kazna[ekr]-=$total_cost;
	            
	            //для админов пишем счетчик сколько купили
	            mysql_query("UPDATE `oldbk`.`abil_shop` SET `allbuy`=`allbuy`+'{$total_to_add}' WHERE `magic`='{$get_abil[magic]}';");
	            
	            
	            //получаем данные есть ли уже такая магия

           	       //нету или закончились
           	       $fin_date=time(); //врема вокупки
           	       mysql_query("INSERT INTO oldbk.abil_buy_clans SET `klan_id`='{$clan_id[id]}',`klan_name`='{$clan_id[short]}' ,`magic_id`='{$abiid}',`findata`='{$fin_date}' , `all_count`='{$total_to_add}' ON DUPLICATE KEY UPDATE `findata`='{$fin_date}' , `all_count`=`all_count`+'{$total_to_add}' ; ");
           	       
           	       //тут добавить инсерт и апдейт в клан_абилс
           	       // для основной выборки надо с 0 значениями
           	       mysql_query("INSERT IGNORE oldbk.`clans_abil`  (`klan`, `magic`, `count`,`maxcount`) values ('{$clan_id[short]}','{$abiid}',0,'0') ;");
           	       mysql_query("INSERT IGNORE avalon.`clans_abil`  (`klan`, `magic`, `count`,`maxcount`) values ('{$clan_id[short]}','{$abiid}',0,'0') ;");
           	       
           	       	 err("<br><b> Удачно куплен «клановый реликт: \"".$get_abil['name']."\"»  {$total_to_add} шт.</b>");

		}
	       
	       }
	       else
	       {
	        err('Ошибка, такой магии нет или она отключена!');
	       }
	  }
	  else
	  {
	 //print_r($_POST);
	  }
	?>	
</form>
	</TD>
</TR>
<TR><TD>

<TABLE BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">
					<?
					if (isset($_GET[mylook]))
					{
					//запрашиваем купленные абилки
	$data = mysql_query("select buy.*,ash.* from oldbk.abil_buy_clans as buy LEFT JOIN oldbk.abil_shop ash ON buy.magic_id=ash.magic  where klan_id='{$clan_id[id]}' and all_count>0 order by magic ");					
	while($row = mysql_fetch_array($data)) 
	{
		if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5';}
		echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
		echo "</TD>";
		echo "<TD valign=top>";
		echo "<b>".$row[name]."<br>Цена:".$row[ecost]." екр/1 шт.</b><br><br>";
		echo "Осталось :<b>".$row[all_count]." шт.</b> <br>";		
		echo "<form method=post><input type=hidden name=abilid value='{$row[magic]}'>";
		echo " Количество:<input type=text name=allcount value='0'  maxlength=3 size=4 id=allcount".$row[magic]." onChange=\"callc(this.value,".$row[magic].",".$row[ecost].")\"  onkeyup=\"this.value=this.value.replace(/[^\d]/,''); callc(this.value,".$row[magic].",".$row[ecost].")\">";
		echo "<br>";		 
		echo " <b><div id=total".$row[magic].">Общая стоимость:0 екр.</div></b>";
		echo "<input type=submit name=buy value='Купить еще'>";
		echo "</form>";
		echo "</TD></TR>";
	}										
					}
					else
					{
					//запрашиваем из магазина абилок
	$data = mysql_query("SELECT * FROM oldbk.`abil_shop` WHERE open >0  ORDER by `ecost` ASC");					
	while($row = mysql_fetch_array($data)) 
	{
		if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5';}
		echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
		echo "</TD>";
		echo "<TD valign=top>";
		echo "<b>".$row[name]."<br>Цена:".$row[ecost]." екр/1 шт.</b>";
		if (($user[klan]=='Adminion') or ($user[klan]=='radminion')) { echo " Admin-info:Всего продано:".$row[allbuy]."шт."; }
		echo "<br><br>";
		echo "<form method=post><input type=hidden name=abilid value='{$row[magic]}'>";
		echo " Количество:<input type=text name=allcount value='0'  maxlength=3 size=4 id=allcount".$row[magic]." onChange=\"callc(this.value,".$row[magic].",".$row[ecost].")\"  onkeyup=\"this.value=this.value.replace(/[^\d]/,''); callc(this.value,".$row[magic].",".$row[ecost].")\">";
		echo "<br>";
		echo " <b><div id=total".$row[magic].">Общая стоимость:0 екр.</div></b>";
		echo "<input type=submit name=buy value='Купить'>";
		echo "</form>";
		echo "</TD></TR>";
	}					
					}

					

					?>
</TABLE>
</TD></TR>
</TABLE>

	</TD>
	<TD valign=top width=280>

	<CENTER>
	<?
	if ($clan_kazna) { echo '<B>В казне: <FONT COLOR="#339900">'.sprintf("%.2f",$clan_kazna[ekr]).'</FONT> eкр.</B><br>'; }
	?>
    <small><font color=red>
	Еврокредиты можно приобрести у любого дилера .
    </font></small>
	</CENTER>
	<div style="MARGIN-LEFT:15px; MARGIN-TOP: 10px;">
  
	<BR>
</div>
<div id="hint3" class="ahint"></div>
    </TD>

</TR>
</TABLE>	
<?
 }
 else
 {
 err('<div align=center>Вы не являетесь главой клана или у Вас нет клановой казны!</div>');
 ?>
 <TABLE border=0 width=100% cellspacing="0" cellpadding="0">
 <FORM action="city.php" method="GET">
<tr>
	<td width=33% > </td>
	<td width=33% > </td>	
	<td width=33% align=right>
	<INPUT TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/abilshop.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
	<INPUT TYPE="submit" value="Вернуться" name="strah"></td>
</tr>
	</FORM>
</table>
 <?
 
 }
?>



<br><div align=left>
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
d.write('<a href="http://top.mail.ru/jump?from=1765367" target="_top">'+
'<img src="http://df.ce.ba.a1.top.mail.ru/counter?id=1765367;t=49;js='+js+
a+';rand='+Math.random()+'" alt="Рейтинг@Mail.ru" border="0" '+
'height="31" width="88"><\/a>');if(11<js)d.write('<'+'!-- ');//--></script>
<noscript><a target="_top" href="http://top.mail.ru/jump?from=1765367">
<img src="http://df.ce.ba.a1.top.mail.ru/counter?js=na;id=1765367;t=49"
height="31" width="88" border="0" alt="Рейтинг@Mail.ru"></a></noscript>
<script language="javascript" type="text/javascript"><!--
if(11<js)d.write('--'+'>');//--></script><div>

<?
include "end_files.php";
?>
</BODY>
</HTML>
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