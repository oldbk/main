<?php
	session_start();

	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

	include "connect.php";
	include "functions.php";

	if (!ADMIN) die();

include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';

?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content="no-cache, max-age=0, must-revalidate, no-store">
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/steel/steel.css" />
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/jscal2.js"></script>
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 >
<?php

$fromdate = isset($_GET['fromdate']) ? htmlspecialchars($_GET['fromdate'],ENT_QUOTES) : (new DateTime())->modify('-1 day')->format('d.m.Y');
$todate = isset($_GET['todate']) ? htmlspecialchars($_GET['todate'],ENT_QUOTES) : (new DateTime())->format('d.m.Y');

?>

<h4>Просмотр статистики продаж</h4>
<form method="GET">
С: <input type=text name='fromdate' value='<?=$fromdate;?>' id="fromdate" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;'> <input type=button id="fromdatetrigger" value='...'>

<script>
var cal1 = Calendar.setup({
	trigger    : "fromdatetrigger",
	inputField : "fromdate",
	dateFormat : "%d.%m.%Y",
	onSelect   : function() { this.hide(); }

});
document.getElementById('fromdatetrigger').setAttribute("type","BUTTON");

</script>

по:<input type=text name='todate' value='<?=$todate;?>' id="todate" style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' > <input type=button id="todatetrigger" value='...'>
<script>
Calendar.setup({
	trigger    : "todatetrigger",
	inputField : "todate",
	dateFormat : "%d.%m.%Y",
	onSelect   : function() { this.hide() },
});
document.getElementById('todatetrigger').setAttribute("type","BUTTON");
</script>

<select name="shoptype">
<option value="shop" <?php if (isset($_GET['shoptype']) && $_GET['shoptype'] == "shop") echo "selected"; ?> >гос маг</option>
<option value="eshop" <?php if (isset($_GET['shoptype']) && $_GET['shoptype'] == "eshop") echo "selected"; ?> >береза</option>
<option value="cshop" <?php if (isset($_GET['shoptype']) && $_GET['shoptype'] == "cshop") echo "selected"; ?>>храм лавка</option>
<option value="fshop" <?php if (isset($_GET['shoptype']) && $_GET['shoptype'] == "fshop") echo "selected"; ?>>цветочка</option>
<option value="fair" <?php if (isset($_GET['shoptype']) && $_GET['shoptype'] == "fair") echo "selected"; ?>>ярмарка</option>
<option value="comission" <?php if (isset($_GET['shoptype']) && $_GET['shoptype'] == "comission") echo "selected"; ?>>комок</option>
</select>

<input type="submit" value="Просмотреть">
</form>
<br><br>
<?php
if (isset($_GET['fromdate'],$_GET['todate'])) {
	$fromdate = explode(".",$_GET['fromdate']);
	$todate = explode(".",$_GET['todate']);
	$fromdate = mktime(0,0,0,$fromdate[1],$fromdate[0],$fromdate[2]);
	$todate = mktime(23,59,59,$todate[1],$todate[0],$todate[2]);
} else {
	$fromdate = (new DateTime())->modify('-1 day')->setTime(0, 0)->getTimestamp();
	$todate = (new DateTime())->setTime(23, 59, 59)->getTimestamp();
	//$fromdate = mktime(0,0,0,date("m"),date("d")-30);
	//$todate = mktime(23,59,59,date("m"),date("d"));
}

$otels = array();
$otels[1]="Кастеты,ножи";
$otels[11]="Топоры";
$otels[12]="Дубины,булавы";
$otels[13]="Мечи";
$otels[2]="Сапоги";
$otels[21]="Перчатки";
$otels[22]="Легкая броня";
$otels[23]="Тяжелая броня";
$otels[24]="Шлемы";
$otels[3]="Щиты";
$otels[4]="Серьги";
$otels[41]="Ожерелья";
$otels[42]="Кольца";
$otels[5]="Нейтральные";
$otels[51]="Боевые и защитные";
$otels[52]="Руны";
$otels[55]="Лицензии";
$otels[6]="Амуниция";
$otels[61]="Еда";
$otels[60] = "Молитвенные предметы";
$otels[62]="Ресурсы";
$otels[63]="Инструменты";
$otels[64]="Купоны";

if (!isset($_GET['shoptype'])) $_GET['shoptype'] = 'shop';

if ($_GET['shoptype'] == "fshop") {
	$otels = array();
	$otels[12] = "Ингредиенты для букетов";
	$otels[14] = "Готовые букеты";
	$otels[7] = "Открытки";
	$otels[71] = "Подарки";
	$otels[60] = "Боевые букеты";
	$otels[6] = "Боевые букеты";
	$otels[73] = "Сезонные/акционные подарки 1";
	$otels[76] = "Сезонные/акционные подарки 2";
	$otels[77] = "Сезонные/акционные подарки 3";
	$otels[72] = "Уникальные";
	$otels[75] = "Свадебные аксессуары";
}

$shoptype = array(
	'shop' => array('name' => 'гос.маг.', 'type' => 1),
	'eshop' => array('name' => 'Березка', 'type' => 140),
	'cshop' => array('name' => 'храм.лавка.', 'type' => 172),
	'fshop' => array('name' => 'цветочный маг.', 'type' => 401),
	'fair' => array('name' => 'ярмарка', 'type' => 509),
	'comission' => array('name' => 'комок', 'type' => 122),
);

if (!isset($shoptype[$_GET['shoptype']])) die();

$noadmin = array();
$q = mysql_query('SELECT id FROM users WHERE klan = "radminion" or klan = "Adminion"');
while($u = mysql_fetch_assoc($q)) {
	$noadmin[] = $u['id'];
}

echo '<h4>Период с '.date("d/m/Y H:i:s",$fromdate).' по '.date("d/m/Y H:i:s",$todate).'</h4>';
if ($_GET['shoptype'] == "comission") {
	$q = mysql_query('SELECT item_name, sum(item_count) as cc FROM new_delo WHERE type = '.$shoptype[$_GET['shoptype']]['type'].' and sdate >= '.$fromdate.' and sdate <= '.$todate.' AND owner not in ('.implode(",",$noadmin).') GROUP BY item_name');
} else {
	$q = mysql_query('SELECT item_name, sum(item_count) as cc FROM new_delo WHERE type = '.$shoptype[$_GET['shoptype']]['type'].' and target_login = "'.$shoptype[$_GET['shoptype']]['name'].'" and sdate >= '.$fromdate.' and sdate <= '.$todate.' AND owner not in ('.implode(",",$noadmin).') GROUP BY item_name');
}
echo '<table>';
$uarr = array();
$sarr = array();
while($u = mysql_fetch_assoc($q)) {
	$uarr[$u['item_name']] = $u;
	if (strlen($u['item_name']) > 0) $sarr[str_replace(" (мф)","",$u['item_name'])] = -1;
}


if ($fromdate < mktime(0,0,0,12,8,2016)) {
	if ($_GET['shoptype'] == "comission") {
		$q = mysql_query('SELECT item_name, sum(item_count) as cc FROM new_delo_old2 WHERE type = '.$shoptype[$_GET['shoptype']]['type'].' and sdate >= '.$fromdate.' and sdate <= '.$todate.' AND owner not in ('.implode(",",$noadmin).') GROUP BY item_name');
	} else {
		$q = mysql_query('SELECT item_name, sum(item_count) as cc FROM new_delo_old2 WHERE type = '.$shoptype[$_GET['shoptype']]['type'].' and target_login = "'.$shoptype[$_GET['shoptype']]['name'].'" and sdate >= '.$fromdate.' and sdate <= '.$todate.' AND owner not in ('.implode(",",$noadmin).') GROUP BY item_name');
	}

	while($u = mysql_fetch_assoc($q)) {
		if (isset($uarr[$u['item_name']])) {
			$uarr[$u['item_name']]['cc'] += $u['cc'];
		} else {
			$uarr[] = $u;
		}
		if (strlen($u['item_name']) > 0) $sarr[str_replace(" (мф)","",$u['item_name'])] = -1;
	}
}

$q = mysql_query('
	SELECT id,name,razdel FROM shop WHERE name IN ("'.implode('","',array_keys($sarr)).'") and maxdur != 500
	UNION 
	SELECT id,name,razdel FROM cshop WHERE name IN ("'.implode('","',array_keys($sarr)).'") and maxdur != 500
	UNION
	SELECT id,name,razdel FROM eshop WHERE name IN ("'.implode('","',array_keys($sarr)).'") and maxdur != 500
');


while($u = mysql_fetch_assoc($q)) {
	if (isset($sarr[$u['name']]) && $sarr[$u['name']] == -1) {
		$sarr[$u['name']] = $u['razdel'];
	}
}

while(list($k,$u) = each($uarr)) {
	$uarr[$k]['razdel'] = $sarr[str_replace(" (мф)","",$u['item_name'])];
}

function cmpuarr($a, $b) {
	if ($a['razdel'] == $b['razdel']) {
		if ($a['cc'] == $b['cc']) {
			return 0;
		}
		return ($a['cc'] < $b['cc']) ? 1 : -1;
	}
	return ($a['razdel'] < $b['razdel']) ? 1 : -1;
}


uasort($uarr, 'cmpuarr');

reset($uarr);
while(list($k,$u) = each($uarr)) {
	echo '<tr><td><b>'.$otels[$sarr[str_replace(" (мф)","",$u['item_name'])]].'</b></td><td>'.$u['item_name']."</td><td>".$u['cc']."</td></tr>";
}
echo '</table>';


?>
</body>
</html>