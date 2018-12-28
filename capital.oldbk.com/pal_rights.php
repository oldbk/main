<?php
	session_start();
	if (!($_SESSION['uid'] >0)) header("Location: index.php");
	include "connect.php";
	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
	include "functions.php";
	$al = mysql_fetch_assoc(mysql_query("SELECT * FROM `aligns` WHERE `align` = '{$user['align']}' LIMIT 1;"));
	header("Cache-Control: no-cache");
   //проверка доступа к управлению
	if ($user['align'] == '1.99' || $user['klan'] == 'Adminion' || $user['klan'] == 'radminion' )
	{		$access = true;	}
	else
	{
			 header("Location: index.php");
			 die();
	}


		$action=isset($_REQUEST['action'])?strip_tags($_REQUEST['action']):0;
		$status=isset($_REQUEST['status'])?strip_tags($_REQUEST['status']):0;
		$id=isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
		$log=isset($_REQUEST['log'])?(int)$_REQUEST['log']:0;
		$extlog=isset($_REQUEST['extlog'])?(int)$_REQUEST['extlog']:0;
//вывод инфы о всех палах.

if(!$action)
{
	?>
	<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="i/main.css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content="no-cache, max-age=0, must-revalidate, no-store">
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<script type="text/javascript" src="/i/globaljs.js"></script>
	<style>
		.row {
			cursor:pointer;
		}
		.green{			background: #F0FFF0;		}
		.red{
			background: #FFCC99;
		}
	</style>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
	<SCRIPT type="text/javascript">
var chk, id, dataString, log, stclass, r0, r1, r2;
	function statusck(f){
	chk=$('#st_'+f).attr('value');
 	dataString='action=status&id='+f+'&status='+chk;
             $.ajax({
			      type: "POST",
			      url: "pal_rights.php",
			      data: dataString,
			      success: function(ans) {
			         //$('#'+f).html( ans );
			       //  alert(ans);
					  return false;
	      			}
	   		  });
	}

	function chanels(f){
	chk=$('#ch_'+f).attr('value');
 	dataString='action=chanels&id='+f+'&status='+chk;
             $.ajax({
			      type: "POST",
			      url: "pal_rights.php",
			      data: dataString,
			      success: function(ans) {
			         //$('#'+f).html( ans );
					  return false;
	      			}
	   		  });
	}

	function prlog_pal(f,l){	  if($('#prlog_'+f).attr('checked')){log = 1; oclass='red'; stclass='green'}else{log=0; oclass='green'; stclass='red'}      if($('#prexlog_'+f).attr('checked')){extlog = 1;}else{extlog=0;}

 		dataString='action=log_pal&id='+f+'&log='+log+'&extlog='+extlog;
             $.ajax({
			      type: "POST",
			      url: "pal_rights.php",
			      data: dataString,
			      success: function(ans) {
			         //$('#'+f).html( ans );
			         //alert(ans);
					  return false;
	      			}
	   		  });
	   	$('#td_'+f).removeClass( oclass ).addClass( stclass );
	}



	function klanrights(f,i,t){	  var dd='';
      var rrr = new Array('vin_','tus_','ars_'); //клановые права. и их формирование  (при добавлении также добавиьт в function.php, в дальнейшем перевезти в базу)
      var rr = new Array();
      ln = rrr.length;
	      for(i=0;i<ln;i++)
	      	{	      	  if($('#'+rrr[i]+f).attr('checked')){rr[i]=1;}else{rr[i]=0;}
	      	  dd=dd+'&rr'+[i]+'='+rr[i];
	      	}
		  if($('#'+t+f).attr('checked')){log = 1; oclass='red'; stclass='green'}else{log=0; oclass='green'; stclass='red';}
	 	  dataString='action=klrights&id='+f+'&rid='+i+dd;
             $.ajax({
			      type: "POST",
			      url: "pal_rights.php",
			      data: dataString,
			      success: function(ans) {
			         //$('#'+f).html( ans );
			     //    alert(ans);
					  return false;
	      			}
	   		  });
	   	$('#t'+t+f).removeClass( oclass ).addClass( stclass );
	}
	</script>
 <form name=fff>
	<table border=1>
		<tr>
			<td>
				<b>Ник</b>
			</td>
			<td>
				<b>Статус</b>
			</td>
			<td>
				<b>Доступ к <br> переводам<br>прост.||расшир.</b>
			</td>
			<td>
				<b>Принимать/выгонять<br> членов клана</b>
			</td>
			<td>
				<b>Менять<br>статус</b>
			</td>
			<td>
				<b>Доступ к<br>арсеналу</b>
			</td>
			<td>
				<b>Каналы<br>PAL</b>
			</td>
		</tr>
	<?
		$data=mysql_query("SELECT `id`, `login`, `status`, `level`, `align`, `klan` FROM `users` WHERE `align` > 1 and `align` < 2 order by  align desc, login asc ;");
		while ($row = mysql_fetch_array($data))
		{				$inf=nick_pal($row['id'],$row['align'], $row['klan'], $row['login'],$row['level'],$row['status'],$row['pal_rights']);
				echo $inf;
		}
	?>

	</table>
</form>
	<?
}

//Часть JQUERRY (обработчики запросов от скриптов)
if($action=='log_pal')
{
    $str= 'INSERT INTO `pal_rights` (`pal_id`,`logs`,`ext_logs`)
    VALUES(
    "'.$id.'","'.$log.'", "'.$extlog.'")
    ON DUPLICATE KEY UPDATE `logs` = "'.$log.'", `ext_logs` = "'.$extlog.'";';

  	  if (!mysql_query($str))
      {
      die('Error: ' . mysql_error());
      }
   // echo $str;
    }

if($action=='status')
{

    //$patterns = ;
     $status=str_replace(array('&lt;','&gt;'),array('<','>'),$status);
     $status1=$status;
     $status=iconv('utf-8', 'cp1251', $status);
    $str= 'UPDATE users SET status = "'.$status.'" WHERE id = "'.$id.'";';

  	  if (!mysql_query($str))
      {
      die('Error: ' . mysql_error());
      }
      //echo $status . $status1;
    }

if($action=='chanels')
{
    $status=iconv('utf-8', 'cp1251', $status);
mysql_query('INSERT `chanels` (`klan`,`name`,`user`)values(\'pal\',\''.$status.'\','.$id.')
				 				ON DUPLICATE KEY UPDATE `name` =\''.$status.'\';');

  	  if (!mysql_query($str))
      {
      die('Error: ' . mysql_error());
      }
    }


if($action=='klrights')
{
	$klan = mysql_fetch_array(mysql_query("SELECT * FROM `clans` WHERE `short` = '{$user['klan']}' LIMIT 1;"));
	$polno = array();
	$polno = unserialize($klan['vozm']);
	//print_r($polno);

    $rights=Array('vin_','tus_','ars_');
    for($i=0;$i<count($rights);$i++)
    {
   	  $rr[$i]=isset($_REQUEST['rr'.$i])?(int)$_REQUEST['rr'.$i]:0;
  	  $polno[$id][$i]=$rr[$i];
    }
      $polno = serialize($polno);
      $str='UPDATE `clans` SET `vozm` = "'.$polno.'" WHERE `id` = "'.$klan['id'].'";';
  	 if (!mysql_query($str))
      {
        die('Error: ' . mysql_error());
      }
    }
?>