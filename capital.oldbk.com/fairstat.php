<?php
session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

include "connect.php";
include "functions.php";

if (!ADMIN) die();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="windows-1251">
<title></title>
<link rel="stylesheet" href="newstyle_loc4.css" type="text/css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/steel/steel.css" />
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type="text/javascript" src="/i/bank9.js"></script>	
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/jscal2.js"></script>
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>
<style>
button:focus {
    outline: 0;
}

input:focus {
    outline: 0;
}

SELECT {
	BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
}
TEXTAREA {
	BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
}
INPUT {
	BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
}

.noty_message { padding: 5px !important;}
</style>
</HEAD>
<body id="arenda-body">
<div id="page-wrapper">
<table style="table-layout:auto;width:30%;">
<?php
$s = 0;
$q = mysql_query('SELECT shop.name as name,sum(fair_buy.faircount) as cc,fairprice as price, sum(fair_buy.faircount)*fairprice as dd FROM fair_buy LEFT JOIN shop ON shop.id = fair_buy.fairprototype LEFT JOIN fair_shop ON fair_shop.fairprototype = fair_buy.fairprototype GROUP BY fair_buy.fairprototype');
while($i = mysql_fetch_assoc($q)) {
	echo '<tr><td>'.$i['name'].'</td><td>'.$i['price'].'</td><td>'.$i['cc'].'</td><td>'.$i['dd'].'</td></tr>';	
	$s += $i['dd'];
}
echo '<tr><td colspan="4" align="right"><b>Итого: '.$s.' монет</b></td></tr>';
?>
</table>
</div>
</body>
</html?

