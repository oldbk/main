
<?
session_start();
include 'connect.php';
include 'functions.php';
?>

	
<?
function w($name,$id,$align,$klan,$level,$slp,$trv,$deal,$battle,$war,$r,$rk,$hh) {
			$fight = '';
			$altext = '';

			if (strlen($align)>0) {
				$altext="";
				if ($align>2 && $align<3) $altext = "Ангел";
				if ($align>1 && $align<2 && $klan !="FallenAngels") $altext = "Паладин";
				if ($align>1 && $align<2 && $klan =="FallenAngels") $altext = "Падший ангел";
				if ( $align == 3 ) $altext ="Тёмный";
				if ( $align == 4 ) $altext ="В хаосе";
				if ( $align == 2 ) $altext ="Нейтрал";
				if ( $align == 5 ) $altext ="Истинный Хаос";
				if ( $align == 6 ) $altext ="Светлый";
				if ( $align == 1 ) $altext ="Светлый";
				if ( $align == "2.4") $altext ="Нейтрал";
				$align='<img src="http://i.oldbk.com/i/align_'.$align.'.gif" title="'.$altext.'" width=12 height=15>';
			}

			if ($battle>0) { $fight = '2';}
			if (strlen($klan)>0) { $klan='<A HREF="http://oldbk.com/encicl/klani/clans.php?clan='.$klan.'" target=_blank><img src="http://i.oldbk.com/i/klan/'.$klan.'.gif" title="'.$klan.'" ></A>';}
			if ($deal==1 && $id!=7363) { $klan.='<img src="http://i.oldbk.com/i/deal.gif" width=15 height=15 title="Дилер">';}

			$color = "";
			if ($r > 0) { if ($r == 1) { $color="blue"; } if ($r == 2) { $color="red";} }
			$colorstart = "<font color=".$color.">";
			$colorend = "</font>";
			if (strlen($color)== 0) {
				$colorstart = "";
				$colorend = "";
			}
			$keyowner = "";
			if ($rk > 0) $keyowner = " <img border=0 src=\"http://i.oldbk.com/i/sh/ruin_k.gif\"> ";
			if ($hh > 0) $keyowner = " <img border=0 src=\"http://i.oldbk.com/i/map/horse_chat.gif\"> ";

			echo $keyowner.'<img OnClick="top.AddToPrivate(\''.$name.'\', top.CtrlPress,event); return false;" src="http://i.oldbk.com/i/lock'.$fight.'.gif" style="cursor:pointer;" title="Приват" width=20 height=15></A>'.$align.$klan.'<span OnClick="top.AddTo(\''.$name.'\',event); return false;" class="ahm" style="cursor:pointer;">'.$colorstart.$name.$colorend.'</span>['.$level.']<a href="inf.php?'.$id.'" target=_blank title="Инф. о '.$name.'">'.'<IMG SRC="http://i.oldbk.com/i/inf.gif" WIDTH=12 HEIGHT=11 BORDER=0 ALT="Инф. о '.$name.'"></a>';
			if ($slp>0) { echo ' <IMG SRC="http://i.oldbk.com/i/sleep2.gif" WIDTH=24 HEIGHT=15 BORDER=0 ALT="Наложено заклятие молчания">'; }
			if ($trv>0) { echo ' <IMG SRC="http://i.oldbk.com/i/travma2.gif" WIDTH=24 HEIGHT=15 BORDER=0 ALT="Инвалидность">'; }
			echo'<BR>';
	}


if  ($_SERVER["SERVER_NAME"]=='capitalcity.oldbk.com') {
	$ci=1; 
} elseif ($_SERVER["SERVER_NAME"]=='avaloncity.oldbk.com') {
	$ci=2;
} elseif ($_SERVER["SERVER_NAME"]=='angelscity.oldbk.com') {
	$ci=3;
}

$_GET['room'] = intval($_GET['room']);
	if (!$_GET['room']) 
	{ 
		$_GET['room'] = $user['room']; 
	}
$room_name = $rooms[$_GET['room']];

if (!(($_GET['room'] >= 0 && $_GET['room'] <= 57 && $user['room'] >= 0 && $user['room'] <= 57) OR ($user['room'] == 75) OR ($_GET['room']==$user['room']))) {
	$_GET['room'] = $user['room'];   
}

$d_sql="select align,u.id,klan,level,login,battle,deal,odate,u.id_grup as id_grup,u.room as room,u.podarokAD as podarokAD, u.ruines as ruines,u.hidden as iluz,slp,hiddenlog,u.naim,
			(SELECT `id` FROM `effects` WHERE (`type` = 11 OR `type` = 12 OR `type` = 13 OR `type` = 14) AND `owner` = u.id LIMIT 1) as trv,deal FROM  `users` as u
		    	WHERE (u.`odate` >= ".(time()-90)." OR u.`in_tower` = 1 OR u.`in_tower` = 2) AND u.`room` = ".intval($_GET['room'])." ORDER by deal DESC, `u`.`login`;";

$data = mysql_query_cache($d_sql,false,10);



?>
<p align="right" id="hidr"></p>

<?	
		    	

echo '	<table border=0 width=100%><tr><td ><center><font style="COLOR:#8f0000;FONT-SIZE:10pt"><B>'. $room_name.' ('.count($data).')'.'</B></font></center></td><td align="right"><a  onClick="closeinfo();" style="cursor: pointer;">Х</a></td></tr><tr><td colspan="2">';
	if(count($data)>0)
	{
		while(list($k,$row) = each($data)) {
			$it++;
			
			if ($row['ruines'] > 0 && !count($ruines_map)) {
				$q = mysql_query('SELECT * FROM `ruines_map` WHERE id = '.$row['ruines']);
				if (mysql_num_rows($q) > 0) $ruines_map = mysql_fetch_assoc($q) or die();
			}
	
	
			if ($row['iluz']==0) 
			{
				if($row['id'] == 83) {
					$ar = mysql_fetch_array(mysql_query("SELECT battle from bots WHERE prototype='83' limit 1;"));
					if($ar[0] > 0) {
						$row['battle'] = $ar[0];
					}
				}
				w($row['login'],$row['id'],$row['align'],$row['klan'],$row['level'],$row['slp'],$row['trv'],(int)$row['deal'],(int)$row['battle'],$xxx,0,0,0);
				
			} 
			elseif (($row['iluz']>0) and ($row['hiddenlog']!='')) 
			{
				//перевоплот
				$fake=explode(",",$row['hiddenlog']);
				$row['id']=$fake[0];
				$row['login']=$fake[1];
				$row['level']=$fake[2];				
				$row['align']=$fake[3];
	
				$row['deal']=0;
	
				//sex
				$row['klan']=$fake[5];
				w($row['login'],$row['id'],$row['align'],$row['klan'],$row['level'],$row['slp'],$row['trv'],(int)$row['deal'],(int)$row['battle'],$xxx,0,0,0);
			}
		}
		//bots render
		
		$bots_data = array();
		if (isset($_GET['scan'],$_GET['room'])) {
			$bots_data=mysql_query("select * from users_clons  where bot_room='".(int)($_GET['room'])."' and bot_online > 0 ORDER by login;");
		} else {
			$bots_data=mysql_query("select * from users_clons  where bot_room='".$user['room']."' and bot_online > 0 ORDER by login;");
		}
	
		while($row=mysql_fetch_array($bots_data)) {
			w($row['login'],$row['id'],$row['align'],$row['klan'],$row['level'],$row['slp'],$row['trv'],(int)$row['deal'],(int)$row['battle'],$xxx,0,0);
		}		
	}
	else
	{
		echo 'Комната пуста.';
	}
?>

</td></tr></table>

