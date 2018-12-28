<?
//компресия для логов
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

 if(isset($_GET['page'])) {
	 $page = (int)$_GET['page'];
	 if($page < 0) { die; }
	 $_GET['page'] = $page;
 }

 if(isset($_REQUEST['log'])) {
	 $log = (int)$_REQUEST['log'];
 if($log < 0) { die ;}
	 $_REQUEST['log'] = $log;
 }
 
 if(isset($_GET['flogin']))
 	{
 	if ($_GET['flogin']=='') unset ($_GET['flogin']);
 	}
 
 ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<script type="text/javascript" src="i/showthing.js"></script>
<script type="text/javascript" src="/i/globaljs.js"></script>
</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=e2e0e0>
<H3  style="margin-bottom: 0px;">Бойцовский клуб <a href="http://oldbk.com/">oldbk.com</a></H3>
<FORM METHOD=GET ACTION="logs.php">
<INPUT TYPE=hidden name=page value="<?=$_GET['page']?>">
<INPUT TYPE=hidden name=log value="<?=$_REQUEST['log']?>">
<?
session_start();
include "connect.php";
include "functions.php";
include "strings_eng.php";

if (CITY_ID==0)
	{
	$log4=49383315; //порог новых логов для кепитала
	}
else if (CITY_ID==1)
	{
	$log4=12622595;	//порог новых логов для авалона
	}
else if (CITY_ID==2)
	{
	$log4=100;	//порог новых логов для ангелся
	}	
else
	{
	$log4=50000000000;	//рубеж
	}


 if ($user[klan]=='radminion') {
  echo "Admin-info:<!- GZipper_Stats -> <br>";  
 }

function countliveinbattle($t) {
	$ret = 0;
	$t = explode(";",$t);
	while(list($k,$v) = each($t)) {
		if ($v < _BOTSEPARATOR_) {
			$ret++;
		}
	}
	return $ret;
}

$data = mysql_fetch_array(mysql_query ("SELECT *, UNIX_TIMESTAMP(`date`) as `udate` FROM `battle` WHERE `id` = ".$_REQUEST['log'].""));


if ($data['win'] != 3 && ($user['align'] == "1.99" || ADMIN)) {
	$t1 = explode(";",$data['t1']);
	$t2 = explode(";",$data['t2']);
	$t3 = explode(";",$data['t3']);
	while(list($k,$v) = each($t1)) {
		if (empty($v) || $v > _BOTSEPARATOR_) unset($t1[$k]);
	}
	while(list($k,$v) = each($t2)) {
		if (empty($v) || $v > _BOTSEPARATOR_) unset($t2[$k]);
	}
	while(list($k,$v) = each($t3)) {
		if (empty($v) || $v > _BOTSEPARATOR_) unset($t3[$k]);
	}
	$alllist = array_merge($t1,$t2,$t3);
	$allrender = array();
	$q = mysql_query('SELECT * FROM users WHERE id IN ('.implode(",",$alllist).')');
	while($u = mysql_fetch_assoc($q)) {
		$u['hidden'] = 0;
		$u['hiddenlog'] = "";
		$allrender[$u['id']] = nick_align_klan($u);
	}

	reset($t1);
	reset($t2);
	reset($t3);
	if (count($t1)) {
		echo 'Team 1: ';
		while(list($k,$v) = each($t1)) {
			echo $allrender[$v]." ";
		}
		echo '<br>';
	}
	if (count($t2)) {
		echo 'Team 2: ';
		while(list($k,$v) = each($t2)) {
			echo $allrender[$v]." ";
		}
		echo '<br>';
	}

	if (count($t3)) {
		echo 'Team 3: ';
		while(list($k,$v) = each($t3)) {
			echo $allrender[$v]." ";
		}
		echo '<br>';
	}
	//echo '<br><br>';
}

if (($data[win]==3) and ($data[status_flag]>0) and (!($_SESSION['uid'] >0)) ) {

$cnam[0]='Capital City';
$cnam[1]='Avalon City';
$cnam[2]='Angels City';

//die("<center><b>Лог текущего Статусного боя скрыт. <br>Для просмотра войдите в <a href='http://oldbk.com' target=_blank>игру</a> и переместитесь в {$cnam[CITY_ID]}!</b> </center></body></html>"); 

}
else
if (($data[status_flag] ==4) and (!($_SESSION['uid'] >0)) ) {

$cnam[0]='Capital City';
$cnam[1]='Avalon City';
$cnam[2]='Angels City';

//die("<center><b>Лог Статусного боя скрыт. <br>Для просмотра войдите в <a href='http://oldbk.com' target=_blank>игру</a> и переместитесь в {$cnam[CITY_ID]}!</b> </center></body></html>"); 

}



if ($_GET['stat']!='1')
{

//if ((int)($_REQUEST['log']) > 10312000)
{
$_REQUEST['log']=(int)($_REQUEST['log']);
$logdir=(int)($_REQUEST['log']/1000);




if ((int)($_REQUEST['log'])>=104389000) 
{
$logdir="/www_logs5/combats_log/".$logdir."000";
$NEW_LOG=true;
}
else
{
$logdir="/www_logs4/combats_log/".$logdir."000";
$NEW_LOG=true;
}


$filename=$logdir."/battle".$_REQUEST['log'].".txt";


	if (file_exists($filename)) 
	{
	//если файл есть текстовый то открываем его
	$log = file($filename);	
	
	if ($NEW_LOG)
	{
	//готовим масив
	$out_log=array();
	
		foreach ($log as $line) 
			{ 

			
			if ($FINISHLOG!=true)
			{
			//временно - потом можно убрать для увеличения скорости и уменьшения нагрузок
			if (strpos($line,"<BR>") > 0)
				{
				//в строке старые текстовые логи
					$oldlog = explode("<BR>",$line);
					 foreach ($oldlog as $oldline) 
						{
						if ($oldline!='') $out_log[]=$oldline;				
						}
				}
				else
				{
				$out_log[]=$line;				
				}
	
				/*
				if (strpos($line,'!:F:') !== FALSE) // пришло окончание боя - закрываем вывод
					{
					$FINISHLOG=true;			
					}
				*/
			}
			
			}
			
		$log=$out_log;
	  }
	  else
	  {
	  	$log = explode("<BR>",$log[0]);
	  }
	  
	}
	else
	{
	//если нет текстового файла пробуем открыть зип
	$filename.=".gz";
	if (file_exists($filename)) 
		{
		//читаем зип-лог
		$gzlog = gzfile($filename);
		unset($log);
		//лепим все в одно т.к. зип вернул неправильно поделенное
		foreach ($gzlog as $line) {  $log.=$line; }
		$log = explode("<BR>",$log);	  
		}
		else
		{
		echo "<h4>Лог этого боя не найден!</h4>";
		die("</body></html>");
		}
	} 
	
}

?>
<?
  

			$data_coment=$data['coment'];

			if($data['coment']!='') {$data['coment'] = ", <b>''{$data['coment']}''</b>";}
			
			if ($data['type'] == 20) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype20.gif\" WIDTH=20 HEIGHT=20 ALT=\"Футбольный поединок\"> (футбольный поединок)";
			}elseif ($data['type'] == 10) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\"> (поединок в башне смерти)";
			}elseif ($data['type'] == 1010) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\"> (поединок в башне смерти)";
			}elseif ($data['type'] == 30) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype30.gif\" WIDTH=20 HEIGHT=20 ALT=\"Бой в лабиринте Хаоса\"> (Бой в лабиринте Хаоса)";
			}elseif ($data['type'] == 60) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок Свет-Тьма\"> ".$data['coment']." (Бой на Арене Богов)";
			}			
			elseif ($data['blood'] && ($data['type'] == 5 OR $data['type'] == 4)) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype5.gif\" WIDTH=20 HEIGHT=20 ALT=\"кулачный бой\"><IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\"> (кровавый кулачный поединок{$data['coment']})";
			}
			elseif ($data['blood'] && ($data['type'] == 2 OR $data['type'] == 3 OR $data['type'] == 6)) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\"> (кровавый поединок{$data['coment']})";
			}
			elseif ($data['type'] == 5 OR $data['type'] == 4) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype4.gif\" WIDTH=20 HEIGHT=20 ALT=\"кулачный бой\"> (кулачный поединок{$data['coment']})";
			}
			elseif ($data['blood']>0 && $data['type'] == 7) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Blood fight \"><IMG SRC=\"http://i.oldbk.com/i/fighttype7.gif\" WIDTH=20 HEIGHT=20 ALT=\"Fight on Christmas tree\"> (blood fight on Christmas tree {$data['coment']})";
			}		
			elseif ($data['blood']==0 && $data['type'] == 7) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype7.gif\" WIDTH=20 HEIGHT=20 ALT=\"Бой на елках\"> (Бой на елках {$data['coment']})";
			}			
			elseif (($data['type'] == 3 OR $data['type'] == 2) AND ($data['CHAOS']>0) )  {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype3.gif\" WIDTH=20 HEIGHT=20 ALT=\"хаотический бой\"> (хаотический поединок{$data['coment']})";
			}
			elseif ($data['type'] == 3 OR $data['type'] == 2) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype3.gif\" WIDTH=20 HEIGHT=20 ALT=\"групповой бой\"> (групповой поединок{$data['coment']})";
			}
			elseif ($data['type'] == 1) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype1.gif\" WIDTH=20 HEIGHT=20 ALT=\"физический бой\"> (физический поединок{$data['coment']})";
			}
			elseif ($data['type'] == 100 || $data['type'] == 101 ) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\"> (клановая битва)"; //старая
			}
			elseif ($data['type'] == 140 || $data['type'] == 141 ) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\"> (клановая дуэльная битва)";
			}			
			elseif ($data['type'] == 150 || $data['type'] == 151 ) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\"> (клановая альянсовая битва)";
			}			
			elseif ($data['type'] == 40) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype40.gif\" WIDTH=20 HEIGHT=20 ALT=\"Противостояние\"> (Противостояние) ";
			}
			elseif ($data['type'] == 41) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype41.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавое Противостояние\"> (Кровавое Противостояние) ";
			}
			else
			{
			$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype3.gif\" WIDTH=20 HEIGHT=20 ALT=\"групповой бой\"> (групповой поединок{$data['coment']})";
			}

			if ( ($data['type'] >= 210) AND ($data['type'] <239) )
				{
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype".$data['type'].".gif\" WIDTH=20 HEIGHT=20 TITLE='Кровавый поединок' ALT='Кровавый поединок'>".$data[coment];
				}
			elseif ( ($data['type'] >= 240) AND ($data['type'] <269) )
				{
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype".$data['type'].".gif\" WIDTH=20 HEIGHT=20 TITLE='Кровавый поединок' ALT='Кровавый поединок'>".$data[coment];
				}
			elseif ( ($data['type'] ==11) OR ($data['type'] ==12 ) )
				{
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype".$data['type'].".gif\" WIDTH=20 HEIGHT=20 TITLE='Бой в Руинах' ALT='Бой в Руинах'>".$data[coment];
				}

			
				
			

			if (($data['CHAOS']>1) or ($data['CHAOS']==-1))
					{
					$rr.= "<IMG SRC=\"http://i.oldbk.com/i/achaos.gif\" WIDTH=20 HEIGHT=20 ALT=\"групповой бой с автоударом\">";
					}

			if(($data['win']==3) and ($data['status']!=1) and ($data['id']>1)) 
			{
			// new fsystem 
			//загружаем нужные данные о чарах и клонах - только живых
			$people=mysql_query("select id, login, hp, maxhp,battle_t, hidden, hiddenlog from users where battle='{$data['id']}' and hp >0  UNION select id, login, hp, maxhp, battle_t, hidden, hiddenlog from users_clons where battle='{$data['id']}' and hp>0	");

			$boec_t1=array ();
			$boec_t2=array ();
			$boec_t3=array ();			
			while ($rowa = mysql_fetch_array($people))
			{
			//заполнитель живых в тимы
				if ($rowa[battle_t]==1)
				 {
				 $boec_t1[$rowa[id]]=$rowa;
				 }
				 elseif ($rowa[battle_t]==2)
				 {
				 $boec_t2[$rowa[id]]=$rowa;
				 }
				 elseif ($rowa[battle_t]==3)
				 {
				 $boec_t3[$rowa[id]]=$rowa;
				 }				 
				 
			}
			///////////////////////////////////////////////////////
			// вывод в нужном порядке
			$ffs ='';
			if ($data[t1]!='')
			{
				$i=0;
				$T1=explode(";",$data[t1]);
				foreach ($T1 as $k => $v)
				{
				if ( ($boec_t1[$v]!='')  )
					{
					++$i;
					if ($i > 1) { $cc = ', '; } else { $cc = ''; }
					
					if ( ($data[type]==61) OR ($data[type]==40) OR ($data[type]==41))
					{
					$ffs .= $cc.bat_nick_team_nl($boec_t1[$v],"B11");					
					}
					else
					{
					$ffs .= $cc.bat_nick_team_nl($boec_t1[$v],"B1");
					}
					
					 }
				}
			if ($i>0)
			{
				if ( ($data[type]==61) OR ($data[type]==40) OR ($data[type]==41))
				{
				$kom1_echo="(".countliveinbattle($data['t1']).") <img src='http://i.oldbk.com/i/align_6.gif'> ".$ffs;
				}
				else
				{
				$kom1_echo=$ffs;					
				}
				
			$A=1;
			}
			else
			{
				if ( ($data[type]==61) OR ($data[type]==40) OR ($data[type]==41))
				{
				$kom1_echo="<img src='http://i.oldbk.com/i/align_6.gif'>  <i>(нет живых участников)</i> ";				
				}
			}
			
			$i=0;	
			}
			
			if ($data[t2]!='')
			{
				if ($A) {  $kom2_echo=" против "; }
				$ffs ='';
				$T2=explode(";",$data[t2]);
				foreach ($T2 as $k => $v)
				{
				if ( ($boec_t2[$v][login]!='')  )
					{
					++$i;
					if ($i > 1) { $cc = ', '; } else { $cc = ''; }
					
					if ( ($data[type]==61) OR ($data[type]==40) OR ($data[type]==41))
					{
					$ffs .= $cc.bat_nick_team_nl($boec_t2[$v],"B12");					
					}
					else
					{
					$ffs .= $cc.bat_nick_team_nl($boec_t2[$v],"B2");
					}
				 	}
				}
			if ($i>0)
			{
				if ( ($data[type]==61) OR ($data[type]==40) OR ($data[type]==41))
				{
				$kom2_echo.="(".countliveinbattle($data['t2']).") <img src='http://i.oldbk.com/i/align_3.gif'> ".$ffs;				
				}
				else
				{
				$kom2_echo.=$ffs;
				}
			}
			else
			{
				if ( ($data[type]==61) OR ($data[type]==40) OR ($data[type]==41))
				{
				$kom2_echo.="<img src='http://i.oldbk.com/i/align_3.gif'> <i>(нет живых участников)</i>";				
				}
			}
			
			
			$i=0;
			$B=1;
			}
			
			if ($data[t3]!='')
			{
			if (($A)OR($B)) {  $kom3_echo=" против "; }
			$ffs ='';
			$T3=explode(";",$data[t3]);
			foreach ($T3 as $k => $v)
			{
				if ( ($boec_t3[$v][login]!='')  )
				{
				++$i;
				if ($i > 1) { $cc = ', '; } else { $cc = ''; }
				

				if ( ($data[type]==61) OR ($data[type]==40) OR ($data[type]==41))
				{
				$ffs .= $cc.bat_nick_team_nl($boec_t3[$v],"B13");				
				}
				else
				{
				$ffs .= $cc.bat_nick_team_nl($boec_t3[$v],"B3");
				}
	 			}
			}
			if ($i>0)
			{
				if ( ($data[type]==61) OR ($data[type]==40) OR ($data[type]==41))
				{
				$kom3_echo.="(".countliveinbattle($data['t3']).") <img src='http://i.oldbk.com/i/align_2.gif'> ".$ffs;				
				}
				else
				{
				$kom3_echo.=$ffs;
				}
			}
			else
			{
				if ( ($data[type]==61) OR ($data[type]==40) OR ($data[type]==41))
				{
				$kom3_echo.="<img src='http://i.oldbk.com/i/align_2.gif'> <i>(нет живых участников)</i>";				
				}
			}
			
			
			$i=0;	
			$C=1;
			}

$ffs=$kom1_echo.$kom2_echo.$kom3_echo;



			
			
			
			
			}
			
			if (($data['status_flag'] == 10) and ($data['type'] == 7) ) {
				$battle_status = "<h3>Mad fight on Christmas tree</h3>";
			}
			else
			if (($data[type]==101) and ($data[status_flag]==0))
			{
				$battle_status = "<h3>Великая клановая битва!</h3>";
			}
			elseif (($data[type]==141) and ($data[status_flag]==0))
			{
				$battle_status = "<h3>Великая клановая битва!</h3>";
			}			
			elseif (($data[type]==151) and ($data[status_flag]==0))
			{
				$battle_status = "<h3>Великая клановая битва!</h3>";
			}			
			elseif ($data['status_flag'] == 10) {
				$battle_status = "<h3>Великий Хаотический бой!</h3>";
			}
			elseif ($data['status_flag'] == 3) {
				$battle_status = "<h3>Эпическая битва!</h3>";
			}elseif ($data['status_flag'] == 2) {
				$battle_status = "<h3>Эпохальная битва!</h3>";
			}elseif ($data['status_flag'] == 1) {								
				$battle_status = "<h3>Великая битва!</h3>";
			}elseif ($data['status_flag'] == 4) {								
				$battle_status = "<h3>Судный День!</h3>";
			}elseif ($data['status_flag'] == 6) {		
				$battle_status = "Великая битва! Свет - Тьма! ";

			if  ($data[win]==3)
				{
				$get_info=mysql_query("select * from place_battle where (var='battle' and val=".$data[id].") or var='close' ");
				if (mysql_affected_rows()==2)
				  {
				  $get_closetime=mysql_fetch_array($get_info);
   				  $get_closetime=mysql_fetch_array($get_info);
   				    if ($get_closetime[val]>time())
					{
				        echo "<font color=red><b>Бой закроется через: ".floor(($get_closetime[val]-time())/60/60)." ч. ".round((($get_closetime[val]-time())/60)-(floor(($get_closetime[val]-time())/3600)*60))." мин.</b></font><br>";					  
				        }
				  }
				}
				  
				
				
			}
			
			
			
			if((int)$data['status_flag'] > 1 && (int)$data['type'] == 5) 
			{
				$battle_status = "<h3>Великая хаотическая битва!</h3>";
			}
			
			echo $battle_status;
			

			
			if ($data['type'] !=30) 
		{
			$co=0;
		if ($data['t1']!='')
			{
			$ttt=explode(";",$data['t1']);
			foreach($ttt as $k=>$v)
					{
					if (($v > _BOTSEPARATOR_) and ($v < 1000000000 ))
				 			{
				 			//клоны
				 			}
				 			else
				 			{
							$co++;
							}
					}
			}
	if ($data['t2']!='')
			{
			$ttt=explode(";",$data['t2']);
			foreach($ttt as $k=>$v)
					{
					if (($v > _BOTSEPARATOR_) and ($v < 1000000000 ))
				 			{
				 			//клоны
				 			}
				 			else
				 			{
							$co++;
							}
					}
			}
	if ($data['t3']!='')
			{
			$ttt=explode(";",$data['t3']);
			foreach($ttt as $k=>$v)
					{
					if (($v > _BOTSEPARATOR_) and ($v < 1000000000 ))
				 			{
				 			//клоны
				 			}
				 			else
				 			{
							$co++;
							}
					}
			}
	
echo "<table border=0 width=100% > <tr> <td width=33%>";			
			if ($data['inf']>0)
			{
			$co-=$data['inf'];
			}
			
			if ($co>0)
				{
				echo "In the battle: <b>".$co."</ B> people <br>";
				}
			}

			if($data['type'] == 61) 
			{
			$cc=mysql_fetch_array(mysql_query("select * from place_logs where battle='{$data['id']}'"));
			if ($cc[usrc]>0) echo "Макс. кол-во участников для каждой склонности: <b>{$cc[usrc]}</b> человек<br>";
			}
			
			
			if(($data['type'] != 10) and ($data['type'] != 1010) and ($data['type'] != 11) and ($data['type'] != 12))  {
			$timeo = "Fight goes with timeout {$data['timeout']} min.";
			}
			if(($data['type'] == 11) or  ($data['type'] == 12))
			{
			$timeo .= "Fight in Ruins ";
			}
			
			if($data['fond']>0) 
			{	
			$data['fond'] = round($data['fond']*0.9,2);	
			$timeo .= "Бой на деньги. Призовой фонд: {$data['fond']} кр.";
			$timeo .= "<br>Бой закрыт от вмешательств со стороны. ";
				if($data['nomagic'] == 1) 
					{
					$timeo .= "<br>Fight without magic.";
					}
			}
			else
			{
				if ($data['teams']!='')
					{
					$timeo .= "  Fight closed by interventions...{$data['teams']}";
					}
			}
			
			if ($data['status'] == 0 && $data['win'] == 3 && $data['t1_dead'] == "") {
				if ($data['type'] == 40 || $data['type'] == 41) {
					if ($data['t1'] == "" || $data['t2'] == "" || $data['t3'] == "") {
						if (($data['udate'] + (15*60)) >= time()) {
							$timeo .= '<br><font color=red>Бой будет закрыт для вмешательства третьей склонности через '.floor((($data['udate'] + (15*60))-time())/60).' минут</font><br>';
						}
					}
				}
			}			
			
				echo "{$timeo}</td><td width=33%>";
				
				/////////////// если новый клановый бой - Весы
				if ($data['type'] == 140 ||  $data['type'] == 141 || $data['type'] == 150 || $data['type'] == 151 ||  $data_coment == "<b>Бой на Центральной площади</b>" ) 
				{
				 	$get_wes=mysql_fetch_array(mysql_query("SELECT * from `battle_war` where battle='{$data[id]}' "));
					if  ($get_wes['active']==1)
					{
					  if ( ($get_wes['t1'] >= $get_wes['wmax']) AND ($get_wes['t2'] < $get_wes['t1']) )     {   $vclass1='ves1n';   }   else   {  $vclass1='ves1';   }
					  if ( ($get_wes['t2'] >= $get_wes['wmax']) AND ($get_wes['t1'] < $get_wes['t2']) )     {   $vclass2='ves2n';   }   else   {  $vclass2='ves2';   }					  
					  
					  $ves1=$get_wes['t1'];   $ves2=$get_wes['t2'];  $planka=$get_wes['wmax'];					  
					}
					 else 	 {   $vclass1='ves1';  $vclass2='ves2';  $ves1=0;  $ves2=0;  $planka=0;  }
				 
				echo "<center><table border=0 height=50 width=157 style=\"background-image: url(http://i.oldbk.com/i/ves_jpg.jpg);   background-position: center center; background-repeat: no-repeat;\">
				<tr align=center>
				<td  width=52 class=$vclass1> $ves1 </td>
				<td  width=52 class=vesp> $planka </td>
				<td  width=52 class=$vclass2> $ves2 </td>
				</table>
				</center>";
				}
				////////////////////////////////////////////////////////////////////
				
				echo "</td><td width=33%>";				
			
			
			if ($_GET['stat']!='1')
			{
			echo "<div align=right><form method=get><input type=hidden name='log' value='".(int)$_GET['log']."'><input type=hidden name='stat' value='1'><input type=submit value='Statistics battle'></form></div>";
			}
			
			echo "<div align=right><FORM METHOD=GET ACTION='logs.php'>
			<INPUT TYPE=hidden name='page' value='".$_GET['page']."'>
			<INPUT TYPE=hidden name='log' value='".$_REQUEST['log']."'>
			Filter by nick:<INPUT TYPE='text' name='flogin' value='".$_GET['flogin']."'>			
			<INPUT TYPE=submit name=filt value=\"Show\">
			<br>
			<INPUT TYPE=submit name='analiz2' value=\"Refresh\">
			 </form></div>
			 </td>
			 </tr>
			 </table> Type fight: ";
			echo $rr;


?>
&nbsp;
Pages:
<?


	if ($NEW_LOG) {	$all = count($log); } else  { $all = count($log)-1; }
	$pgs = $all/50;

if (isset($_REQUEST['analiz2']))
 {
$_GET['page']=(int)$pgs;
 }

if (!isset($_GET['flogin']))
{
$dp=0;$op=0;
$pgs=(int)$pgs;
	for ($i=0;$i<=$pgs;++$i) {
	
	   if (($_GET['page']-100) > $i)
	    {
	     if ($op==0)
	     	{
	     	echo ' <a href="?log=',$_GET['log'],'&page=0">1</a> ';
	     	 $op=1;echo "...";
	     	}
	    }
	  else
	   if (($_GET['page']+100) < $i)
	   	{
	   	 if ($dp==0) 
	   	 	{
		   	$dp=1;	echo "...";
		   	echo ' <a href="?log=',$_GET['log'],'&page=',$pgs,'">',($pgs+1),'</a> ';
		   	}
	   	}
	   else
	   {
		if ($_GET['page']==$i) {
			echo ' <a href="?log=',$_GET['log'],'&page=',$i,'"><font color=#8f0000>',($i+1),'</font></a> ';
		}
		else {
			echo ' <a href="?log=',$_GET['log'],'&page=',$i,'">',($i+1),'</a> ';
		}
	    }
	}
}
else
{
echo "1";
}		

?><HR>
<?

include "abiltxt.php";

  foreach($atext as $index => $val)
	{  
	$atext[$index]="<a style=\"cursor: pointer\" onMouseOut=\"HideThing(this);\" onMouseOver=\"ShowThing(this,25,25,'{$val}');\" >".str_replace('/','',$abn[$index])."</a>";
	}

ksort($atext);
ksort($abilname);


	if (isset($_GET['flogin']))
	{
	$start = 0;
	$stop=50*$all;
	}
	else
	{
	$start = 50*$_GET['page'];
	if(50*$_GET['page']+50 <= $all) {
		$stop = 50*$_GET['page']+50;
	} else {
		$stop = 50*$_GET['page']+($all-50*$_GET['page'])-1;
	}
	//echo $stop;
	 if ($_GET['page']>0) { $start++; }
	}
	for($i=$start;$i<=$stop;$i++) 
	{
	
		if ($NEW_LOG)
		{
			
			if (($log[$i][2]=='D') and ($i>$start+2) ) //фикс визуальный
			{
			$strtemp=explode(":",$log[$i]);
			$tmpnik=explode("|",$strtemp[3]);
			$tmpnik[0]; //Булик
			
				$strtemp2=explode(":",$log[$i-2]);				
				$tmpnik2=explode("|",$strtemp2[6]);				
				if (($tmpnik[0]==$tmpnik2[0]) and ($strtemp2[12][1]!=0))
					{
					//делаем правку
					//!:K:1415810561:Джузеппе_Жестко|1|:110:101:Булик|2|:103:33:45:2:124:[22/398]
					$fixhp=explode("/",$strtemp2[12]);				
					$strtemp2[12]="[0/".$fixhp[1];
					$log[$i-2]=implode(":",$strtemp2);
					$logout[$i-2]=get_log_string($log[$i-2]);
					}
			}
			$logout[$i]=get_log_string($log[$i]);
		}
		else
		{
			$logout[$i]=preg_replace($abilname,$atext,$log[$i]);
		}


	}
	
	for($i=$start;$i<=$stop;$i++) 
		{
			if (isset($_GET['flogin']))	
				{
				
					if ((strpos($log[$i],$_GET['flogin'])) > 0)
					{
					$logout[$i]=trim($logout[$i]);
					if ( $logout[$i] !='' ) { echo $logout[$i]."<BR>\n"; }
					}
				}
				else
				{
					$logout[$i]=trim($logout[$i]);
					if ( $logout[$i] !='' ) { echo $logout[$i]."<BR>\n"; }
		
				}		
		}
	
?>
<HR>
<?
	echo "<center>".$ffs."</center><HR>";
?>
<FORM METHOD=GET ACTION="logs.php">
<INPUT TYPE=hidden name=page value="<?=$_GET['page']?>">
<INPUT TYPE=hidden name=log value="<?=$_REQUEST['log']?>">

<INPUT TYPE=submit name=analiz2 value="Обновить">
</form>
&nbsp;
Страницы:
<?
if (!isset($_GET['flogin']))
{
$dp=0;$op=0;
$pgs=(int)$pgs;
	for ($i=0;$i<=$pgs;++$i) {
	
	   if (($_GET['page']-100) > $i)
	    {
	     if ($op==0)
	     	{
	     	echo ' <a href="?log=',$_GET['log'],'&page=0">1</a> ';
	     	 $op=1;echo "...";
	     	}
	    }
	  else
	   if (($_GET['page']+100) < $i)
	   	{
	   	 if ($dp==0) 
	   	 	{
		   	$dp=1;	echo "...";
		   	echo ' <a href="?log=',$_GET['log'],'&page=',$pgs,'">',($pgs+1),'</a> ';
		   	}
	   	}
	   else
	   {
		if ($_GET['page']==$i) {
			echo ' <a href="?log=',$_GET['log'],'&page=',$i,'"><font color=#8f0000>',($i+1),'</font></a> ';
		}
		else {
			echo ' <a href="?log=',$_GET['log'],'&page=',$i,'">',($i+1),'</a> ';
		}
	    }
	}
}
echo "<br><br><form method=get><input type=hidden name='log' value='".(int)$_GET['log']."'><input type=hidden name='stat' value='1'><input type=submit value='Статистика боя'></form>";
}
else { echo "<form method=get><input type=hidden name='log' value='".(int)$_GET['log']."'><input type=submit value='Лог боя'></form>";
//if ((int)($_REQUEST['log'])>=$log4) 
			 {
				include('battle_stat_new.php');
			 } 
			 //else { include('battle_stat.php');  }
echo "<br><form method=get><input type=hidden name='log' value='".(int)$_GET['log']."'><input type=submit value='Лог боя'></form>";
}
?>

</FORM>
<A name=end></A>
<div align=right>
<!--LiveInternet counter--><script type="text/javascript"><!--
			document.write("<a href='http://www.liveinternet.ru/click' "+
			"target=_blank><img style='float:right; ' src='http://counter.yadro.ru/hit?t54.2;r"+
			escape(document.referrer)+((typeof(screen)=="undefined")?"":
			";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
			screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
			";"+Math.random()+
			"' alt='' title='LiveInternet: показано число просмотров и"+
			" посетителей за 24 часа' "+
			"border='0' ><\/a>")
			//--></script><!--/LiveInternet-->

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

</div>



</BODY>
</HTML>
<?php
function bat_nick_team_nl($telo,$st)
{

if ( ($telo[hidden] > 0) and ($telo[hiddenlog] ==''))
   {
	$telo['login']='<b><i>Невидимка</i></b>';
	$telo['hp']='??';
	$telo['maxhp']='??';
   }
   else
	if (strpos($telo[login],"Невидимка (клон" ) !== FALSE )
		{
		$telo['login']='<b><i>'.$telo['login'].'</i></b>';
		$telo['hp']='??';
		$telo['maxhp']='??';
		}
$telo=load_perevopl($telo);
if ($telo[razm]==1)
{
$telo['login']="<u>".$telo['login']."</u>";
}
if($telo[lid]==1)
{
$lid="<img src='http://i.oldbk.com/i/leader.gif' width=16 height=19 style='cursor:pointer' title='Лидер' alt='Лидер'>";
}
else {$lid='';}


if ($telo[bpstor]>0)
	{
	
	
	//телов 3-х стороннем бою делаем классы B11 , b12 ,b13
	 if ($st=='B1') $st='B11';
	 if ($st=='B2') $st='B12';
	 if ($st=='B3') $st='B13';
	}


//меняем класс если должна быть подсветка в боях 60 61
/*
if ($telo[blow]==1)
  {
  $st='B3';
  }
*/

$outstring=$lid."<span class={$st}>".$telo['login']."</span> [".$telo['hp']."/".$telo['maxhp']."]";

return $outstring;
}

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