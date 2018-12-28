<?
session_start();
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content="no-cache, max-age=0, must-revalidate, no-store">
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/steel/steel.css" />
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/jscal2.js"></script>
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>
<script>
function absPosition(obj) {
      var x = y = 0;
      while(obj) {
            x += obj.offsetLeft;
            y += obj.offsetTop;
            obj = obj.offsetParent;
      }
      return {x:x, y:y};
}
function alt(id, name){
		//alert('');


	var ourDiv = document.getElementById(id);

        var xx;
        var yy;
		xx=absPosition(ourDiv).x;
		yy=absPosition(ourDiv).y;

        var ss;
        yy=+yy+20;
        xx=+xx+23;
        ss=' <b>'+name+'</b>';


        document.getElementById("result").style.left=xx;
        document.getElementById("result").style.top=yy;
		showdiv();
		document.getElementById('result').innerHTML = ss;
      }


      function hidediv() {
if (document.getElementById)
	{
	document.getElementById('result').style.visibility = 'hidden';
	}
}

function showdiv() {
	if (document.getElementById)
	{
	document.getElementById('result').style.visibility = 'visible';
	}
}
</script>

<style>
	.row {
		cursor:pointer;
	}
</style>

</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 >
<div id="result" style="border: solid 1px black; background: #fc0; visibility: hidden; z-index: 10;  position: absolute; left: 10px; top: 10px;">Подробности о комнате</div>
<table align=right><tr><td><INPUT TYPE="button" onClick="location.href='main.php';" value="Вернуться" title="Вернуться"></table>

<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

	include "connect.php";
	include "functions.php";
	
$access=check_rights($user);
	

if($user[klan]!='Adminion' && $user[klan]!='radminion' && ($access[pals_delo]!=1 || $access[pals_online]!=1))
{
	die('Страница не найдена...');
}

if (isset($_POST[sit_hist]) || isset($_POST[delo]))
{
	if (isset($_POST[new_delo_date]))
	{
	//29.09.11
		$new_delo_date_all=explode(".",$_POST[new_delo_date]);
		$new_delo_date = sprintf("%02d.%02d.%04d", (int)($new_delo_date_all[0]), (int)($new_delo_date_all[1]), (int)($new_delo_date_all[2]));
	}
	else
	{
		$log_date = date("d.m.Y");
	}
	
	if (isset($_POST[new_delo_fdate]))
	{
	//29.09.11
		$new_delo_fdate_all=explode(".",$_POST[new_delo_fdate]);
		$new_delo_fdate = sprintf("%02d.%02d.%04d", (int)($new_delo_fdate_all[0]), (int)($new_delo_fdate_all[1]), (int)($new_delo_fdate_all[2]));
	}
	else
	{
		$new_delo_fdate = date("d.m.Y");
	}
}
else
{
	$new_delo_date = "01.12.2011";
	$new_delo_fdate = date("d.m.Y");
}
echo '<br><br><br><h3>Просмотр действий паладинов</h3><br><br><br>

<div>
<table><tr><td>&nbsp
<form action="?" method=post>
	<input type=text name=login value="'.$_POST[login].'"> Ник паладина. <br>
	';
	echo "c:<input type=text name='new_delo_date' value='{$new_delo_date}' id=\"delocalendar-inputField1\" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' >";
	echo "<input type=button id=\"delocalendar-trigger1\" value='...'>";
	echo "
		<script>
		Calendar.setup({
		trigger    : \"delocalendar-trigger1\",
		inputField : \"delocalendar-inputField1\",
		dateFormat : \"%d.%m.%Y\",
		onSelect   : function() { this.hide() }
				});
		document.getElementById('delocalendar-trigger1').setAttribute(\"type\",\"BUTTON\");
		</script>";
	echo "по:<input type=text name='new_delo_fdate' value='{$new_delo_fdate}' id=\"delocalendar-inputField2\" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' >";
	echo "<input type=button id=\"delocalendar-trigger2\" value='...'>";
	echo "
		<script>
		Calendar.setup({
		trigger    : \"delocalendar-trigger2\",
		inputField : \"delocalendar-inputField2\",
		dateFormat : \"%d.%m.%Y\",
		onSelect   : function() { this.hide() }
				});
		document.getElementById('delocalendar-trigger2').setAttribute(\"type\",\"BUTTON\");
		</script><br>";
	echo '
	<input type="checkbox" name=check '.(isset($_POST[check])?'checked':'').'> Проверка;<br>
	<input type="checkbox" name=marry '.(isset($_POST[marry])?'checked':'').'> женитьба;<br>
	<input type="checkbox" name=obezlich '.(isset($_POST[obezlich])?'checked':'').'> обезличкаы;<br>
	<input type="checkbox" name=haos '.(isset($_POST[haos])?'checked':'').'> Хаос;<br>
	<input type="checkbox" name=sleep '.(isset($_POST[sleep])?'checked':'').'> молча;<br>
	<input type="checkbox" name=fsleep '.(isset($_POST[fsleep])?'checked':'').'> ф молча;<br>
	<input type="checkbox" name=death '.(isset($_POST[death])?'checked':'').'> закл смерти;<br>
	';
	
/*
		1 проверка
		2 внесение в ЛД
		3 женитьба
		4 развод
		5 выгнать из палов
		6 обезличкаы
		7 обезличка откл
		8 хаос
		9 хаос офф
		10 молча
		11 молча офф
		12 ф молча
		13 ф молча офф
		14 закл смерти
		15 закл смерти офф

*/
	
	
	echo '<input type=submit name="delo" value="Действия">&nbsp;&nbsp;&nbsp;<input type=submit name="sit_hist" value="просмот онлайна">
	</form></div>
';
echo '</td></tr><tr><td>';

$stamp_start=mktime(0, 0, 0, (int)($new_delo_date_all[1]), (int)($new_delo_date_all[0]), (int)($new_delo_date_all[2]));
$stamp_fin=mktime(23, 59, 59,(int)($new_delo_fdate_all[1]), (int)($new_delo_fdate_all[0]), (int)($new_delo_fdate_all[2]));
			
if($_POST[delo] && $access[pals_delo]==1)
{
	
	$sql=array();
	$types=array();
	$name=array();
	if(isset($_POST[check]))
	{
		$sql[1]=1;
		$name[1]='Проверка';
	}
	if(isset($_POST[marry]))
	{
		$sql[3]=3;
		$sql[4]=4;
		$name[3]='Свадьба';
		$name[4]='Развод';
	}
	if(isset($_POST[obezlich]))
	{
		$sql[6]=6;
		$name[6]='Обезл.';
		$sql[7]=7;
		$name[7]='Обезл.Снятие';
		
	}
	if(isset($_POST[haos]))
	{
		$sql[8]=8;
		$name[8]='Хаос';
		$sql[9]=9;
		$name[9]='Хаос';
	}
	if(isset($_POST[sleep]))
	{
		$sql[10]=10;
		$name[10]='Молчанка';
		$sql[11]=11;
		$name[11]='Снятие.Молчанки';
	}
	if(isset($_POST[fsleep]))
	{
		$sql[12]=12;
		$name[12]='Форумка';
		$sql[13]=13;
		$name[13]='Снятие.Форумки';
	}
	if(isset($_POST[death]))
	{
		$sql[14]=14;
		$name[14]='Блок';
		$sql[15]=15;
		$name[15]='Снятие.Блока';
	}
	
	
	$pal=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.users where login = '".$_POST[login]."' limit 1"));
	
	$data=mysql_query("SELECT * FROM oldbk.paldelo where author='".$pal[id]."' AND `date` > '".$stamp_start."' AND `date` < '".$stamp_fin."' ".(count($sql)>0?(' AND m_type in ('.(implode(',',$sql)).')'):'')." ;");
	/*
		типы ЛД:
		1 проверка
		2 внесение в ЛД
		3 женитьба
		4 развод
		5 выгнать из палов
		6 обезличкаы
		7 обезличка откл
		8 хаос
		9 хаос офф
		10 молча
		11 молча офф
		12 ф молча
		13 ф молча офф
		14 закл смерти
		15 закл смерти офф
		
	
	*/
	$counts=array();
	while($row=mysql_fetch_assoc($data))
	{
		$row[text]=preg_replace("/&quot;(.*?)&quot;/i","<a target=_blank href=inf.php?login=\\1>\\1</a>", $row[text]);
		echo  date("d.m.y",$row['date']).' '.$row[text];
		echo '<br>';
		$count[$row[m_type]]+=1;
	}
	
	echo '<hr>Итого: ';
	foreach($count as $k=>$v)
	{
		echo $name[$k].': '.$v.'; ';
	}
}
else
if($_POST[sit_hist] && $access[pals_online]==1)
{

	$moder_room=array(1=>'КН',2=>'КН2',3=>'КН3',4=>'КН4',5=>'ЗВ1',6=>'ЗВ2',7=>'ЗВ3',8=>'ТЗ',9=>'РЗ',10=>'БРМ',
		11=>'КМ',12=>'ЭД',13=>'АЭ',14=>'ОМ',15=>'ЗП',16=>'СББ',17=>'ЗТ',18=>'ЦТ',19=>'Б',20=>'ЦП',
		21=>'СУ',22=>'МАГ',23=>'РМ',24=>'НЁ',25=>'КМ',26=>'ПУ',27=>'ПЧТ',28=>'РК',29=>'БНК',30=>'СУД',
		31=>'БС',32=>'ГЗ',33=>'ЛХ',34=>'ЦМ',35=>'СМ',37=>'ГЗПР',38=>'ГЗАР',39=>'ГЗВД',40=>'ГЗМ',41=>'ГЗКО',
		42=>'ЛС',43=>'КЗ',44=>'44',46=>'46',47=>'47',48=>'48',49=>'49',50=>'ЗПЛ',51=>'БСК',
		52=>'ССК',53=>'МСК',54=>'ЗС',55=>'ЦСВ',56=>'ЦСТ',
		57=>"ЗКВ",58=>"К58",59=>"К59",60=>"АБ",61=>"К61",62=>"К62",63=>"К63",64=>"К64",65=>"К65",66=>'ТУ',
		200=> "РИСТ",401=> "ВА",
		70 => "ЛОМ",
		71 => "АУК");
	

	$room=array(
		1=>'КН',2=>'КН2',3=>'КН3',4=>'КН4',5=>'ЗВ1',6=>'ЗВ2',7=>'ЗВ3',8=>'ТЗ',9=>'РЗ',10=>'БРМ',
		11=>'КМ',12=>'ЭД',13=>'АЭ',14=>'ОМ',15=>'ЗП',16=>'СББ',17=>'ЗТ',18=>'ЦТ',19=>'Б',20=>'ЦП',
		21=>'СУ',22=>'МАГ',23=>'РМ',24=>'НЁ',25=>'КМ',26=>'ПУ',27=>'ПЧТ',28=>'РК',29=>'БНК',30=>'СУД',
		31=>'БС',32=>'ГЗ',33=>'ЛХ',34=>'ЦМ',35=>'СМ',37=>'ГЗПР',38=>'ГЗАР',39=>'ГЗВД',40=>'ГЗМ',41=>'ГЗКО',
		42=>'ЛС',43=>'КЗ',44=>'44',45=>'45',46=>'46',47=>'47',48=>'48',49=>'49',50=>'ЗПЛ',51=>'БСК',
		52=>'ССК',53=>'МСК',54=>'ЗС',55=>'ЦСВ',56=>'ЦСТ',
		57=>"ЗКВ",58=>"К58",59=>"К59",60=>"АБ",61=>"К61",62=>"К62",63=>"К63",64=>"К64",65=>"К65",66=>'ТУ',
		200=> "РИСТ",401=> "ВА",
		70 => "ЛОМ",
		"71" => "АУК",
	//турниры
	
	"197"=>"ОК",
	"198"=>"ОК",
	"199"=>"ОК",
	
	"210"=>"ВОС",
	"211"=> "ОС[1]",
	"212"=> "ОС[2]",
	"213"=> "ОС[3]",
	"214"=> "ОС[4]",
	"215"=> "ОС[5]",
	"216"=> "ОС[6]",
	"217"=> "ОС[7]",
	"218"=> "ОС[8]",
	"219"=> "ОС[9]",
	"220"=> "ОС[10]",
	"221"=> "ОС[11]",
	"222"=> "ОС[12]",
	// Групповое сражение
	"240"=>"ВГС",
	"241"=> "ГС[1]",
	"242"=> "ГС[2]",
	"243"=> "ГС[3]",
	"244"=> "ГС[4]",
	"245"=> "ГС[5]",
	"246"=> "ГС[6]",
	"247"=> "ГС[7]",
	"248"=> "ГС[8]",
	"249"=> "ГС[9]",
	"250"=> "ГС[10]",
	"251"=> "ГС[11]",
	"252"=> "ГС[12]",
	//Сражение отрядов
	"270"=>"ВСО",
	"271"=> "СО[1]",
	"272"=> "СО[2]",
	"273"=> "СО[3]",
	"274"=> "СО[4]",
	"275"=> "СО[5]",
	"276"=> "СО[6]",
	"277"=> "СО[7]",
	"278"=> "СО[8]",
	"279"=> "СО[9]",
	"280"=> "СО[10]",
	"281"=> "СО[11]",
	"282"=> "СО[12]",
	
	// БС
	"501" => "БС",
	"502" => "БС",
	"503" => "БС",
	"504" => "БС",
	"505" => "БС",
	"506" => "БС",
	"507" => "БС",
	"508" => "БС",
	"509" => "БС",
	"510" => "БС",
	"511" => "БС",
	"512" => "БС",
	"513" => "БС",
	"514" => "БС",
	"515" => "БС",
	"516" => "БС",
	"517" => "БС",
	"518" => "БС",
	"519" => "БС",
	"520" => "БС",
	"521" => "БС",
	"522" => "БС",
	"523" => "БС",
	"524" => "БС",
	"525" => "БС",
	"526" => "БС",
	"527" => "БС",
	"528" => "БС",
	"529" => "БС",
	"530" => "БС",
	"531" => "БС",
	"532" => "БС",
	"533" => "БС",
	"534" => "БС",
	"535" => "БС",
	"536" => "БС",
	"537" => "БС",
	"538" => "БС",
	"539" => "БС",
	"540" => "БС",
	"541" => "БС",
	"542" => "БС",
	"543" => "БС",
	"544" => "БС",
	"545" => "БС",
	"546" => "БС",
	"547" => "БС",
	"548" => "БС",
	"549" => "БС",
	"550" => "БС",
	"551" => "БС",
	"552" => "БС",
	"553" => "БС",
	"554" => "БС",
	"555" => "БС",
	"556" => "БС",
	"557" => "БС",
	"558" => "БС",
	"559" => "БС",
	"560" => "БС" ,
	
	"999" => "ВР"
	);


	$pal=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.users where login = '".$_POST[login]."' limit 1"));
	
	
	$pals_date=array();
	$pals_date_off=array();
	$data=mysql_query("SELECT * FROM oldbk.pal_vizits where owner='".$pal[id]."' AND (`date` > ".$stamp_start." AND `date` < ".$stamp_fin.") ;");
	while($row=mysql_fetch_assoc($data))
	{
		$d=date("d.m.y",$row['date']);
		$pals_date[$d][(round_time($row['date'],15))]=$row['room'];
		$pals_date_off[$d][(round_time($row['date'],15))]=$row['chatactive'];
	}

	echo '<table border=0>
		<tr>
			<td> Информация по '.($pal[deal]==-1?'помощнику':'паладину').' <h1>'.$_POST[login].'</h1></td></tr>';
	$min_15=60*15;
	$rez_per=0;
	$off_all = 0;
	foreach($pals_date as $date_date => $val)
	{
		
		$rez_day=0;
		$rez_moder_day=0;
		$rez_off = 0;
		$dd=explode('.',$date_date);
		$day_beg=mktime(0,0,0,$dd[1],$dd[0],$dd[2]);
		$day_end=mktime(23,59,59,$dd[1],$dd[0],$dd[2]);
		
		echo '<tr><td lign=left><b>'.$date_date.'</b></td></tr>';
			
		echo '<tr><td><table border=1><tr>';
		for($i=0;$i<24;$i++)
		{
			
			
			echo '<td align=middle>'.$i.'<br><table border=0 cellpadding=0 cellspacing=0><tr>';
				for($j=0;$j<4;$j++)
				{
					if(!$ch_day)
					{
						$ch_day=$day_beg;
					}
					else
					{
						$ch_day+=$min_15;
					}
				
					if($val[$ch_day])
					{

						if (!$pals_date_off[$date_date][$ch_day]) {
							$rez_off += 15;
						} else {
							echo '<td bgcolor="'.($moder_room[$val[$ch_day]]?'SkyBlue':'yellow').'" id="'.(date('d.m.y H:i',($ch_day-60*15))).'-'.(date('H:i',($ch_day))).'" style="cursor: pointer;" onmouseover="javascript:alt(\''.(date('d.m.y H:i',($ch_day-60*15))).'-'.(date('H:i',($ch_day))).'\',\''.($rooms[$val[$ch_day]]).'\');" onmouseout="javascript:hidediv();" title="'.(date('H:i',($ch_day-60*15))).'-'.(date('H:i',($ch_day))).'">&nbsp'.$room[$val[$ch_day]].'</td>';
							$rez_day+=15;
							if($moder_room[$val[$ch_day]])
							{
								$rez_moder_day+=15;
							}
						}
					}
					else
					{
						echo '<td bgcolor="gray">&nbsp</td>';
					}
					
				}
			
			echo '</tr></table></td>';
			
		}
		unset($ch_day);	
		$off_all += $rez_off;
		$rez_per+=$rez_day;
		$rez_per_moder_day+=$rez_moder_day;	
		echo '</tr></table><b>'.($rez_moder_day>0?$rez_moder_day/60:0).'. <small>('.($rez_day>0?$rez_day/60:0).')</small> <small>('.($rez_off>0?$rez_off/60:0).')</small></b> (в часах)<br><br></td></tr>';
		//echo  . ' ' . $row['date'].' '.($rooms[$row['room']]?$rooms[$row['room']]:$row['room']);
		
	}
	echo '</table>';
	echo '<b>Общее время за выбранный период:'.($rez_per_moder_day>0?$rez_per_moder_day/60:0).'('.($rez_per>0?$rez_per/60:0).') ('.($off_all>0?$off_all/60:0).')</b>(в часах)</b>';
	
}
echo '</td></tr></table>';







?>