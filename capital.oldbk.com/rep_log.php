<?php   
session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
include "connect.php";
include "functions.php";

if ($user[klan]!='radminion' && $user[klan]!='Adminion') {  die('Страница не найдена...'); }





$now_n=date("N")+1; // какой день недели - надо понедельник=0;

$glvl=(int)($_GET[lvl]);

if (!($glvl>=7 and $glvl<=12))
	{
	$glvl=7;
	}


if (!(isset($_GET[s_date])))
   {
   $_GET[s_date]=date("d.m.Y", mktime(0, 0, 0, date("m") , date("d")-30 , date("Y") ));
   }

if (!(isset($_GET[f_date])))
  {
  $_GET[f_date]=date("d.m.Y");
  }



$in_dates=explode(".",$_GET[s_date]); //01.09.11
$in_datef=explode(".",$_GET[f_date]); //01.09.11



$sdat=date("Y-m-d", mktime(0, 0, 0, (int)($in_dates[1]) , (int)($in_dates[0]) , (int)($in_dates[2])));

$fdat=date("Y-m-d", mktime(0, 0, 0, (int)($in_datef[1]), (int)($in_datef[0]), (int)($in_datef[2])));




   $res = mysql_query("select lvl, sdate , sum(rep_lab) as rep_lab, sum(rep_zag) as rep_zag, sum(rep_rist270) as rep_rist270, sum(rep_rist240) as rep_rist240, sum(rep_rist210) as rep_rist210 ,sum(rep_zam) as rep_zam, sum(rep_ruin) as rep_ruin, sum(rep_bat) as rep_bat, sum(rep_other) as rep_other, sum(rep_hram) as rep_hram from users_rep_log where lvl='{$glvl}'  and  sdate >= '{$sdat}' and sdate <= '{$fdat}'   group by sdate ");
   
   
echo    "select lvl, sdate , sum(rep_lab) as rep_lab, sum(rep_zag) as rep_zag, sum(rep_rist270) as rep_rist270, sum(rep_rist240) as rep_rist240, sum(rep_rist210) as rep_rist210 ,sum(rep_zam) as rep_zam, sum(rep_ruin) as rep_ruin, sum(rep_bat) as rep_bat, sum(rep_other) as rep_other, sum(rep_hram) as rep_hram from users_rep_log where lvl='{$glvl}'  and  sdate >= '{$sdat}' and sdate <= '{$fdat}'   group by sdate " ;



        while ($ro = mysql_fetch_array($res)) 
	{
	$data[$ro[sdate]]=$ro;
	}

//print_r($data[7]);
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>
     OLDBK.COM - REP GRAFIC
    </title>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="http://i.oldbk.com/i/jscal/css/steel/steel.css" />
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/jscal2.js"></script>
<script type="text/javascript" src="http://i.oldbk.com/i/jscal/js/lang/ru2.js"></script>    
    
    <h2>Статистика по <?=$glvl;?> уровню</h2>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
      function drawVisualization() {
        // Create and populate the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'x');
        data.addColumn('number', 'Репа в Лабе');
        data.addColumn('number', 'Репа в Загороде');        
       data.addColumn('number', 'Ристалище отрядов');        
       data.addColumn('number', 'Ристалище групповое');                
       data.addColumn('number', 'Ристалище одиночное');                       
       data.addColumn('number', 'Репа в Замках');                
       data.addColumn('number', 'Репа в Руинах');                       
       data.addColumn('number', 'Репа в боях');                  
       data.addColumn('number', 'Квесты в храме');                         
       data.addColumn('number', 'Репа полученая в остальных случаях');                                
        
        
        <?


	foreach($data as $k=>$val)
	{
	
//	$d_out=explode("-",$k);
//	$ddout=$d_out[2]."-".$d_out[1];
	echo "data.addRow([\"".$k."\", ".$val[rep_lab].", ".$val[rep_zag].", ".$val[rep_rist270]." , ".$val[rep_rist240]." , ".$val[rep_rist210]." , ".$val[rep_zam]." , ".$val[rep_ruin]." , ".$val[rep_bat]." , ".$val[rep_hram]." , ".$val[rep_other]." ]); \n";	

	
	}
	
        ?>
       
        // Create and draw the visualization.
        new google.visualization.LineChart(document.getElementById('visualization')).
            draw(data, {curveType: "function",pointSize:"5",
                        width: screen.width, height: 500,
                        vAxis: {maxValue: 100}}
                );
      }
      

      google.setOnLoadCallback(drawVisualization);
    </script>
  </head>
  <body style="font-family: Arial;border: 0 none;">
    <div id="visualization" style="width: 100%; height: 500px;"></div>
    <br>
    <center>
    <form>
    Уровень <select name=lvl> <option value="7">[7]</option> <option value="8">[8]</option> <option value="9">[9]</option>    <option value="10">[10]</option>     <option value="11">[11]</option>  <option value="12">[12]</option></select>
    
    с:<input type='text' name='s_date' readonly="true" class='enter0'  value='<? echo $_GET[s_date]; ?>' style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' id="calendar-inputField0">
	<input type=button id="calendar-trigger0" value='...'>
	<script>
	Calendar.setup({
        trigger    : "calendar-trigger0",
        inputField : "calendar-inputField0",
		dateFormat : "%d.%m.%Y",
		onSelect   : function() { this.hide() }
    			});
	document.getElementById('calendar-trigger0').setAttribute("type","BUTTON");
	</script>
 по:<input type='text' name='f_date' readonly="true" class='enter1'  value='<? echo $_GET[f_date]; ?>' style='width: 70px; padding-left: 2px; height:18px; padding-bottom: 0px;' id="calendar-inputField1">
	<input type=button id="calendar-trigger1" value='...'>
	<script>
	Calendar.setup({
        trigger    : "calendar-trigger1",
        inputField : "calendar-inputField1",
		dateFormat : "%d.%m.%Y",
		onSelect   : function() { this.hide() }
    			});
	document.getElementById('calendar-trigger1').setAttribute("type","BUTTON");
	</script>
  <input type=submit value='Вывести'>	
    </form>
    </center>
  </body>
</html>

