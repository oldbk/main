<?
		session_start();
		if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die();}
		include ("connect.php");
		include "functions.php";

        $user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
		if ($user['room'] != 49) { header("Location: main.php"); die(); }
        if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }

							function buildset($id,$img,$top,$left,$des) {
								$imga = ImageCreateFromPNG("i/city/sub/".$img.".png");
								#Get image width / height
								$x = ImageSX($imga);
								$y = ImageSY($imga);
								unset($imga);

								// echo $_SERVER['HTTP_USER_AGENT'];

								if (strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 6.0"))
								 {
								 echo "<div style=\"position:absolute; cursor: pointer; left:{$left}px; top:{$top}px; width:{$x}; height:${y}; z-index:90; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=100, Style=0);\"
								 ><img src=\"http://i.oldbk.com/i/city/sub/{$img}.png\" width=\"${x}\" height=\"${y}\" alt=\"{$des}\" title=\"{$des}\" class=\"aFilter\" onmouseover=\"this.src='http://i.oldbk.com/i/city/sub/{$img}2.png'\" onmouseout=\"this.src='http://i.oldbk.com/i/city/sub/{$img}.png'\"
								 id=\"{$id}\" onclick=\"solo({$id})\" /></div>";
								 }
								 else
								 {
								 echo "<div style=\"position:absolute; cursor: pointer; left:{$left}px; top:{$top}px; width:{$x}; height:${y}; z-index:90; \"
								 ><img src=\"http://i.oldbk.com/i/city/sub/{$img}.png\" width=\"${x}\" height=\"${y}\" alt=\"{$des}\" title=\"{$des}\" class=\"aFilter2\" onmouseover=\"this.src='http://i.oldbk.com/i/city/sub/{$img}2.png'\" onmouseout=\"this.src='http://i.oldbk.com/i/city/sub/{$img}.png'\"
								 id=\"{$id}\" onclick=\"solo({$id})\" /></div>";
								 }
							}

function nick_histmy ($telo) {
	if ($_SERVER["SERVER_NAME"]=='capitalcity.oldbk.com')  {
		$domen='http://capitalcity.oldbk.com/';
	} else if ($_SERVER["SERVER_NAME"]=='avaloncity.oldbk.com')  {
		$domen='http://avaloncity.oldbk.com/';
	} else {
		$domen='http://capitalcity.oldbk.com/';
	}

	//$mm .= "<img src=\"http://i.oldbk.com/i/align_".($telo['align']>0 ? $telo['align']:"0").".gif\">";
	if ($telo['klan'] == 'pal') {
		$mm .= "<img src=\"http://i.oldbk.com/i/align_".($telo['real_align']>0 ? $telo['real_align']:"0").".gif\">";
	}
	elseif ($telo['klan'] <> '') {
		$mm .= '<img title="'.$telo['klan'].'" src="http://i.oldbk.com/i/klan/'.$telo['klan'].'.gif">'; 
	}
	else {
		$mm .= '<img title="" src="http://i.oldbk.com/i/klan/no_klan.gif">'; 
	}

	if (strlen($telo['login']) >= 18) $shw = substr($telo['login'],0,20)."..."; else $shw = $telo['login'];

	$mm .= "<B>{$shw}</B> <a target=_blank href=\"inf.php?login=".htmlspecialchars($telo['login'],ENT_QUOTES)."\"><img valign=left border=0 src=http://i.oldbk.com/i/inf.gif></a> ";
	
	return $mm;
}



        function check_count_exist_q()
        {
		   global $user;                                                                      //с ID 100 начанаются храмовые квесты
		   $sql = 'SELECT * FROM beginers_quests_step WHERE owner='.$user[id].' AND status=0 AND quest_id>=100';
		//   echo $sql;
		   $return=array();
		   $data = mysql_query($sql);
		   if(mysql_affected_rows()>0)
		   {
		     while($row=mysql_fetch_array($data))
		     {
		     	$return[$row[id]]=$row;
		     }
		   }
           return $return;
        }


        function take_new_quest()
        {
			global $user;
		//проверяем наличие поставленной свечи
		$svechka_test=mysql_fetch_array(mysql_query('SELECT * FROM oldbk.inventory WHERE id='.(int)$_POST[target].' AND owner='.$user[id].' AND `dressed`=0  AND bs_owner = 0 AND labonly=0  AND `setsale`=0 LIMIT 1;'));
		if ($svechka_test['id']>0)
		{
				
			                    $align=$user[align];
			                    if($user[align]>2 && $user[align]<3)
			                    {
			                    	$align=2;
			                    }
			                    if($user[align]>1 && $user[align]<2)
			                    {
			                    	$align=6;
			                    }
			
		                		$cquests=check_count_exist_q();
				        //print_r($cquests);
				        	$ids='';
	                        		$ff=0;
			        		foreach ($cquests as $id=>$value)
			        		{
	
			        			$ids.=$value[quest_id].',';
			        		}
			                        $ids=substr($ids,0,-1);
			                        if($ids=='')
			                        {
			                        	$ids=0;
			                        }
				                    $sql='select bql.*, bq.qstart from beginers_quest_list bql
				                    left join beginers_quests bq
				                    on bq.id=bql.id AND bq.step=1
				                    where bql.can_take=1 AND (bql.qalign= '.$align.' OR bql.qalign=0) AND bql.id not in ('.$ids.');';
	                    //echo $sql;
						$data1=mysql_query($sql);
						if(mysql_affected_rows()>0)
						{
							while($row1=mysql_fetch_array($data1))
							{
								$is_quests[$row1[id]]=$row1;
							}
				        }
				
	        }
	        else
	        {
	        	$is_quests='none';
	        }
	        return $is_quests;
        }






        if((int)$_GET[take]>0 && (int)$_GET[take] != 100 && (int)$_GET[take] != 108 && (int)$_GET[take] != 134 && (int)$_GET[take] != 132 && (int)$_GET[take] != 141 && (int)$_GET[take] != 129 && (int)$_GET[take] != 133  && (int)$_GET[take] != 130 && (int)$_GET[take] != 114 && (int)$_GET[take] != 119 && (int)$_GET[take] != 124 && (int)$_GET[take] != 143 && (int)$_GET[take] != 142)
        {

if ($user['id']==14897)
	{
	echo "ok1";
	}

        	//провермяем кол-во квестов, и наличие заного выбранных
        	$cquests=check_count_exist_q();
        	$txt='';
        	//print_r($cquests);
        	//print_r($cquests);

        	if(count($cquests)<4)
        	{
        		foreach ($cquests as $id=>$value)
        		{
        			if($value[quest_id]==$_GET[take])
        			{
        				$txt.='Вы уже взяли этот квест.';
        			}
        		}
        	}
        	else
        	{
        		$txt.='У вас уже есть 4 квеста...';
			}

/*
		if ($_GET[take] == 142 && $user['repmoney'] >= 600000) {
			$txt .= 'Ай-ай-ай';
		}
*/
        	/////////////////////////////////////////////////////       УДАЛИТЬ 30 после тестов!!!!!!!!!!!!!!!!!!


          //  echo $txt;
            if($txt=='')
            {
				if ((int)$_GET['sv']>0)
				{
				//проверяем наличие поставленной свечи
				$svechka_test=mysql_fetch_array(mysql_query('SELECT * FROM oldbk.inventory WHERE id='.(int)$_GET['sv'].' AND owner='.$user[id].' AND `dressed`=0  AND bs_owner = 0 AND labonly=0  AND `setsale`=0 LIMIT 1;'));
				if ($svechka_test['id']>0)
				{
					
			        	$align=$user[align];
			                    if($user[align]>2 && $user[align]<3)
			                    {
			                    	$align=2;
			                    }
			                    if($user[align]>1 && $user[align]<2)
			                    {
			                    	$align=6;
			                    }

			        	$data1=mysql_query('
				        	select bql.*, bq.* from beginers_quest_list bql
							left join beginers_quests bq
							ON bql.id=bq.id
							where bql.can_take=1 AND (bql.qalign= '.$align.' OR bql.qalign=0) AND bql.id='.$_GET[take].' AND bq.step=1
							LIMIT 1;
			        	');
						if (mysql_num_rows($data1) > 0)
						{
							while($row1=mysql_fetch_array($data1))
							{
				                                $ent_sql='INSERT INTO beginers_quests_step SET
								owner ='.$user[id].',
								quest_id='.$row1[id].', step_f=1, qtype='.$row1[qtype].', qftype='.$row1[q_fight_type].'
								ON DUPLICATE KEY UPDATE count=0, step=1, status=0, step_f=1, qtype='.$row1[qtype].', qftype='.$row1[q_fight_type].'
								;';

								if(mysql_query($ent_sql))
								{
									mysql_query('insert into ancients_templ_log   (`owner`,`type`,`count`,`used`)  VALUES  ('.$user[id].','.$svechka_test[prototype].',"1") ON DUPLICATE KEY UPDATE count=count+1, used=1;  ');
			                                    		mysql_query('delete from oldbk.inventory where owner='.$user[id].' and setsale = 0 AND id ='.$svechka_test[id].' Limit 1;');
									$txt='Вы взяли квест <b>' . $row1[qname].'</b>';
									unset($_SESSION[beginer_quest]);
								}
							}
						}
						else
						{
							$txt.='Нет доступных квестов.';
						}
					

				}
				else
				{
					//$txt.='Сначало надо зажечь свечу...';
					$txt.='Такая свеча не найдена...';
				}
				}
    		}
        }



        /*
		if (($user['room']>=210) and ($user['room']<239))
				{
				header('location: restal210.php');
				die();
				}
				else if (($user['room']>=240) and ($user['room']<269))
					{
					header('location: restal240.php');
					die();
					}
					else if (($user['room']>=270) and ($user['room']<299))
					{
					header('location: restal270.php');
					die();
					}
		elseif ($user['room']!=200) { header("Location: main.php"); die(); }
          */

?>

	<HTML><HEAD>
	<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
    <link rel="stylesheet" href="/i/btn.css" type="text/css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<META Http-Equiv=Cache-Control Content=no-cache>
	<meta http-equiv=PRAGMA content=NO-CACHE>
	<META Http-Equiv=Expires Content=0>
	<META HTTP-EQUIV="imagetoolbar" CONTENT="no">
	<script type="text/javascript" src="/i/globaljs.js"></script>	
	<style>
	     IMG.aFilter { filter:Glow(Color=d7d7d7,Strength=9,Enabled=0); cursor:hand }
	</style>
	<style type="text/css">
	img, div { behavior: url(/i/city/ie/iepngfix.htc) }
	</style>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
	<SCRIPT LANGUAGE="JavaScript">

	 function selecttarget(scrollid)
	{
		var targertinput = document.getElementById('target');
		targertinput.value = scrollid;

		var targetform = document.getElementById('formtarget');
		targetform.submit();
	}

	function show_table(title, script)
	{
		var choicehtml = "<form style='display:none' id='formtarget' action='" + script + "' method=POST><input type='hidden' id='target' name='target'>";
		choicehtml += "</form><table width='100%' cellspacing='1' cellpadding='0' bgcolor='CCC3AA'>";
		choicehtml += "<tr><td align='center'><B>" + title + "</td>";
		choicehtml += "<td width='20' align='right' valign='top' style='cursor: pointer' onclick='closehint3(true);'>";
		choicehtml += "<big><b>x</td></tr><tr><td colspan='2' id='tditemcontainer'><div id='itemcontainer' style='width:100%'>";
		choicehtml += "</div></td></tr></table>";
		return choicehtml;
	}

	function showitemschoice(title, script,al)
	{
	    $.get('itemschoice.php?get=1&svecha=qq&al='+al+'',function(data) {
			var choicehtml=show_table(title, script);
			var el = document.getElementById("hint3");
			el.innerHTML = choicehtml+data;
			el.style.width = 400 + 'px';
			el.style.visibility = "visible";
			el.style.left = 100 + 'px';
			el.style.top = 100 + 'px';
			Hint3Name = "target";
		   });
	}


	function closehint3(clearstored){
		if(clearstored)
		{
			var targetform = document.getElementById('formtarget');
			targetform.action += "&clearstored=1";
			targetform.submit();
		}
		document.getElementById("hint3").style.visibility="hidden";
	    Hint3Name='';
	}

	function solo(n)
	{

			<?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>changeroom=n;
			window.location.href='?got=1&level'+n+'=1';

	}

	function imover(im)
	{
		im.filters.Glow.Enabled=true;
	//	im.style.visibility="hidden";
	}

	function imout(im)
	{
		im.filters.Glow.Enabled=false;
	//	im.style.visibility="visible";
	}

	function returned2(s){
		location.href=''+s+'&tmp='+Math.random();
	}


	function Down() {<?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>CtrlPress = window.event.ctrlKey}

		document.onmousedown = Down;

			</SCRIPT>
     		</HEAD>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#D7D7D7>
        <?
      //  make_quest_div();
        ?>

		<div id=hint3 class=ahint style="z-index: 150;"><?=$quests_text?></div>

	<?
	echo $txt;
	if($_GET['level10']==1 || $_GET['level11']==1)
	{
		$ret_url='?';
	}
	else
	{
		$ret_url='city.php?zp=1';
	}

	if(!$_GET[info])
	{
		?>
		<div style="float: right;">
                <div class="btn-control">
                    <INPUT class="button-dark-mid btn" TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/hram.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
                    <input class="button-mid btn" type="button" name="hram" onclick="returned2('<?=$ret_url?>')" value="Вернуться">
                </div>
			</div>
		<?
	}
?>
<TABLE width=100% height=100% border=0 cellspacing="0" cellpadding="0">
	<TR>
		<TD align=center valign=top>
		<?
		///////////////Сама лока
			if($_GET[info])
 	 		{
			  $_GET[info]=(int)$_GET[info];
			  if ($_GET[info]< 3001 or $_GET[info]>3021)
			  {
			    die('Ошибка ресурса');
			  }
			  
				if (($_GET[info]==3013) OR ($_GET[info]==3014) )
				{
					$itmname=mysql_fetch_array(mysql_query("SELECT name from oldbk.eshop where id=3013 or id=3014  "));
					$strids='3013,3014';
				}
				else
				if (($_GET[info]==3015) OR ($_GET[info]==3016) )
				{
					$itmname=mysql_fetch_array(mysql_query("SELECT name from oldbk.eshop where id=3015 or id=3016  "));
					$strids='3015,3016';
					$af=1;
				}
				else
				if (($_GET[info]==3017) OR ($_GET[info]==3018) )
				{
				$itmname=mysql_fetch_array(mysql_query("SELECT name from oldbk.eshop where id=3017 or id=3018  "));
				$strids='3017,3018';
				$af=2;
				}
				else
				if (($_GET[info]==3019) OR ($_GET[info]==3020) OR ($_GET[info]==3021) )
				{
					$itmname=mysql_fetch_array(mysql_query("SELECT name from oldbk.eshop where id=3019 or id=3020 or id=3021  "));
					$strids='3019,3020,3021';
					$af=3;
				}
				else
				{
					$itmname=mysql_fetch_array(mysql_query("SELECT name from oldbk.eshop where id='".$_GET[info]."'"));
					$strids=(int)($_GET[info]);
				}
				?>

					Топ 10, Материал:<b><?=$itmname[name];?></b></td>
					</tr>
				<?
				
				$put_pref='oldbk.';
				
				
				$tops=mysql_query("select * from {$put_pref}church2 where resid in (".$strids.") ORDER by rescount DESC LIMIT 10;");
				while ($row = mysql_fetch_array($tops))
				{
					$addd='';
				 	if ($row[resid]==3013) {$addd='(Уголь)' ;}
				 	if ($row[resid]==3014) {$addd='(Уголёк)' ;}
				 	if ($row[resid]==3015) {$addd='(Рубины)' ;}
				 	if ($row[resid]==3016) {$addd='(Рубин)' ;}
				 	if ($row[resid]==3017) {$addd='(X)' ;}
				 	if ($row[resid]==3018) {$addd='(Y)' ;}
				 	if ($row[resid]==3019) {$addd='(&alpha;)' ;}
				 	if ($row[resid]==3020) {$addd='(&beta;)' ;}
				 	if ($row[resid]==3021) {$addd='(&gamma;)' ;}

					if ($cc==1)
					 {

						echo "<tr bgcolor=#C0C0C0 >
							<td align=center>&nbsp;".nick33($row[owner])." ".$addd." </td>
							<td align=center>&nbsp;<b>".$row[rescount]."шт.</b></td>
						</tr>";
						$cc=0;
					 }
					 else
					 {
					echo "<tr bgcolor=#C0C0CA >
							<td align=center>&nbsp;".nick33($row[owner])." ".$addd." </td>
							<td align=center>&nbsp;<b>".$row[rescount]."шт.</b></td>
						</tr>";
					$cc=1;
					 }


			    }

				?>
					<tr>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
					</tr>
				<?
				$mm=mysql_fetch_array(mysql_query("select sum(rescount) from {$put_pref}church2 where resid in (".$strids.") and owner='{$user[id]}';"));
				if ($mm[0]>0)
				{
				echo "<tr bgcolor=#C0C0CA >
						<td align=center>&nbsp;".nick33($user[id])."</td>
						<td align=center>&nbsp;<b>".$mm[0]."шт.</b></td>
					</tr>";
				}
				?>
				</table>

				<?
				die();
     		}


				if($_GET['got'] && ($_GET['level10'] || $_GET['level62'] || $_GET['level60'] || $_GET['level63'] || $_GET['level61']) || $_GET['level64'] || $_GET['level65'] || $_GET['level66'] || $_GET['level67'] || $_GET['level68'] || $_GET['level69'] || $_GET['level70'])
				{

					function jschalertmsg($text) {
						echo '<script>alert("'.$text.'")</script>';
					}


					$ualign = intval($user['align']);
					if ($ualign == 1) $ualign = 6;


					if (isset($_GET['level60']) || isset($_GET['level61']) || isset($_GET['level62'])) {
						// ложим череп
						if (!$user['align']) {
							jschalertmsg("У вас нет склонности...");
						}
						$err = false;
						if (isset($_GET['level60']) && ($user['align'] != 6 && $user['klan'] != "pal")) {
							jschalertmsg("Не та склонность...");
							$err = true;
						}
						if (isset($_GET['level61']) && ($user['align'] != 2 && $user['klan'] != "radminion")) {
							jschalertmsg("Не та склонность...");
							$err = true;
						}
						if (isset($_GET['level62']) && $user['align'] != 3) {
							jschalertmsg("Не та склонность...");
							$err = true;
						}

						if (date("j") == 1 && date("H") == 0) {
							$mmin = trim(date("i"));
							if ($mmin >= 0 && $mmin <= 10) {
								jschalertmsg("Подводятся итоги за прошлый месяц. Попробуйте через 5 минут");
								$err = true;
							}

						}


						if ($err == false) {
							// ложим череп
							mysql_query('START TRANSACTION');
							$uids = array();
							$uids2 = array();
							$txt1 = "";
							$txt2 = "";
							$voin = 0;
							$vtable = array(
								0 => 5,							
								1 => 10,
								2 => 10,
								3 => 10,
								4 => 10,
								5 => 10,
								6 => 20,
								7 => 20,
								8 => 40,
								9 => 60,
								10=> 100,
								11=> 200,
								12=> 400,
								13=> 600,
								14=> 800,
							);
							
							$how = $vtable[$user['level']];
							if (!$how) $how = max($vtable);


							$q = mysql_query('SELECT * FROM oldbk.inventory WHERE prototype = 3002500 AND owner = '.$user['id']) or die();
							if (mysql_num_rows($q) > 0) {
								while($i = mysql_fetch_assoc($q)) {
									$uids[] = $i['id'];
								}
								if (strlen($user['klan']) > 0) {
									$addklan = "и клановой ";
									mysql_query('UPDATE oldbk.clans SET voinst = voinst + '.(count($uids)*$how).' WHERE short = "'.$user['klan'].'"') or die();
								}

								mysql_query('UPDATE users SET skulls = skulls + '.count($uids).' WHERE id = '.$user['id']) or die();
								mysql_query('UPDATE users SET voinst = voinst + '.(count($uids)*$how).' WHERE id = '.$user['id']) or die();

								mysql_query('INSERT INTO avalon.`op_score` (`owner`,`score`,`align`) 
										VALUES(
											'.$user['id'].',
											'.count($uids).',
											'.$ualign.'
										) 
										ON DUPLICATE KEY UPDATE
											`score` = `score` + '.count($uids).'
								') or die();

								$txt1 = " ".count($uids)." черепов";
								$voin += (count($uids)*$how);

								mysql_query('DELETE FROM oldbk.inventory WHERE id IN('.implode(",",$uids).')') or die();
							}	


							$q = mysql_query('SELECT * FROM oldbk.inventory WHERE (prototype = 3002501 or prototype = 3002502 or prototype = 3002503) AND owner = '.$user['id']) or die();
							if (mysql_num_rows($q) >= 3) {
								$num = floor(mysql_num_rows($q) / 3)*3;

								while($num-- && $i = mysql_fetch_assoc($q) ) {
									$uids2[] = $i['id'];
								}

								if (strlen($user['klan']) > 0) {
									$addklan = "и клановой ";
									mysql_query('UPDATE oldbk.clans SET voinst = voinst + '.((count($uids2)/3)*$how).' WHERE short = "'.$user['klan'].'"') or die();
								}

								mysql_query('UPDATE users SET skulls = skulls + '.(count($uids2)/3).' WHERE id = '.$user['id']) or die();
								mysql_query('UPDATE users SET voinst = voinst + '.((count($uids2)/3)*$how).' WHERE id = '.$user['id']) or die();

								mysql_query('INSERT INTO avalon.`op_score` (`owner`,`score`,`align`) 
										VALUES(
											'.$user['id'].',
											'.(count($uids2)/3).',
											'.$ualign.'
										) 
										ON DUPLICATE KEY UPDATE
											`score` = `score` + '.(count($uids2)/3).'
								') or die();

								$txt2 = " ".count($uids2)." осколков";
								$voin += ((count($uids2)/3)*$how);

								mysql_query('DELETE FROM oldbk.inventory WHERE id IN('.implode(",",$uids2).')') or die();
							}	
								

							if (count($uids) || count($uids2)) {
								jschalertmsg("Вы сдали".$txt1.$txt2.". Получили ".($voin)." личной ".$addklan. "воинственности");
							} else {
								jschalertmsg("У вас нет черепов и осколков...");
							}
							mysql_query('COMMIT');
						}
					}

					$fon = 'ava_fon_hram5';
					$svecha1='skull1';
					$svecha2='skull2';
					$svecha3='skull3';

					$butt2= 'ava_zaltr';
					$butt1= 'ava_ch_top10';
					$butt3= 'ava_zal_poj';


					echo '<script type="text/javascript" src="http://i.oldbk.com/i/js/cufon-yui.js"></script>';
					echo '<script type="text/javascript" src="http://i.oldbk.com/i/js/Stylo_700.font.js"></script>';

					echo "<table width=1><tr><td><div style=\"position:relative; \" id=\"ione\"><img src=\"http://i.oldbk.com/i/city/".$fon.".jpg\" alt=\"\" border=\"0\"/>";

					$level = 7;
					if ($_GET['level63']) $level = 7;
					if ($_GET['level64']) $level = 8;
					if ($_GET['level65']) $level = 9;
					if ($_GET['level66']) $level = 10;
					if ($_GET['level67']) $level = 11;
					if ($_GET['level68']) $level = 12;
					if ($_GET['level69']) $level = 13;
					if ($_GET['level70']) $level = 14;

				    	buildset(1,$butt2,13,42,"Тронный зал");

				    	buildset(63,$butt1,23,322,"Топ 10 собирателей черепов");
				    	//buildset(63,"hr_new7",63,290,"7ой уровень");
				    	//buildset(64,"hr_new8",63,330,"8ой уровень");
				    	buildset(65,"hr_new9",63,310,"9ый уровень");
				    	buildset(66,"hr_new10",63,350,"10ый уровень");
				    	buildset(67,"hr_new11",63,390,"11ый уровень");
				    	buildset(68,"hr_new12",63,430,"12ый уровень");
				    	buildset(69,"hr_new13",63,470,"13ый уровень");
				    	buildset(70,"hr_new14",63,510,"14ый уровень");

				    	buildset(11,$butt3,18,632,"Зал пожертвований");

					buildset(60,$svecha1,402,178,"Сдать черепа и осколки");
					buildset(61,$svecha2,390,399,"Сдать черепа и осколки");
					buildset(62,$svecha3,403,615,"Сдать черепа и осколки");

					if (isset($_GET['level63']) || isset($_GET['level64']) || isset($_GET['level65']) || isset($_GET['level66']) || isset($_GET['level67']) || isset($_GET['level68']) || isset($_GET['level69']) || isset($_GET['level70'])) {
						$mys = 0;
						$q = mysql_query('SELECT * FROM avalon.op_score WHERE owner = '.$user['id']);
						if (mysql_num_rows($q) > 0) {
							$mys = mysql_fetch_assoc($q);
							$mys = $mys['score'];
						}

						
						//по склонкам алигн=2

						$q = mysql_query("(SELECT login,score,users.align,klan FROM avalon.op_score LEFT JOIN oldbk.users ON oldbk.users.id  = avalon.op_score.owner AND oldbk.users.align  = avalon.op_score.align  WHERE level =  ".$level." and id_city =0 and users.align=2 ORDER BY score DESC LIMIT 10  )
						 UNION 
						( SELECT login,score,users.align,klan FROM avalon.op_score LEFT JOIN avalon.users ON avalon.users.id  = avalon.op_score.owner AND avalon.users.align  = avalon.op_score.align WHERE level =  ".$level." and id_city =1 and users.align=2 ORDER BY score DESC LIMIT 10 )
						ORDER BY score DESC LIMIT 10");

						$tlist = array();
						while($u = mysql_fetch_assoc($q)) {
							$u[real_align]=($u['align']);
							$user_align=(int)($u['align']);
							if ($user_align == 1) {
								$u['align'] = 6;
							} 
							$tlist[$u['align']][] = $u; 
							
						}
												
						//по склонкам алигн=3

						$q = mysql_query("(SELECT login,score,users.align,klan FROM avalon.op_score LEFT JOIN oldbk.users ON oldbk.users.id  = avalon.op_score.owner AND oldbk.users.align  = avalon.op_score.align  WHERE level =  ".$level." and id_city =0 and users.align=3 ORDER BY score DESC LIMIT 10  )
						 UNION 
						( SELECT login,score,users.align,klan FROM avalon.op_score LEFT JOIN avalon.users ON avalon.users.id = avalon.op_score.owner  AND avalon.users.align  = avalon.op_score.align  WHERE level =  ".$level." and id_city =1 and users.align=3 ORDER BY score DESC LIMIT 10 )
						ORDER BY score DESC LIMIT 10");

					
						while($u = mysql_fetch_assoc($q)) {
							$u[real_align]=($u['align']);
							$user_align=(int)($u['align']);
							if ($user_align == 1) {
								$u['align'] = 6;
							} 
							$tlist[$u['align']][] = $u; 
							
						}
													
						// палы или алигн=6

						$q = mysql_query("(SELECT login,score,users.align,klan FROM avalon.op_score LEFT JOIN oldbk.users ON oldbk.users.id  = avalon.op_score.owner WHERE level = ".$level." and id_city =0 and (users.align=6 or klan='pal') ORDER BY score DESC LIMIT 10  )
							 UNION 
							( SELECT login,score,users.align,klan FROM avalon.op_score LEFT JOIN avalon.users ON avalon.users.id  = avalon.op_score.owner AND avalon.users.align  = avalon.op_score.align WHERE level = ".$level." and id_city =1 and (users.align=6 or klan='pal') ORDER BY score DESC LIMIT 10 )
							ORDER BY score DESC LIMIT 10");

					
						while($u = mysql_fetch_assoc($q)) {
							$u[real_align]=($u['align']);
							$user_align=(int)($u['align']);
							if ($user_align == 1) {
								$u['align'] = 6;
							} 
							$tlist[$u['align']][] = $u; 
							
						}
						

						echo '<div style="position:absolute; width:275px; height: 281px; left:25px; top:125px; z-index:92;background-image: url(http://i.oldbk.com/i/city/sub/hram_scroll6.png)">
						<div style="cursor:pointer;text-align:right;margin-top:12px;width:235px;"><img src="http://i.oldbk.com/i/city/sub/ava_op_cross.png" onclick="solo(10);" width="14" height="15"></div>
						<div style="margin-left:38px;margin-top:9px;font-family: Tahoma;font-size: 8pt;">';
						
						$i = 1;          
						while(list($k,$u) = each($tlist[6])) {
							echo '<div style="margin:4px; padding:0px;font-family: Tahoma;font-size: 8pt;">'.nick_histmy($u).'('.$u['score'].')</div>';
							$i++;
						}
						if ($i < 10) {
							for ($x = $i; $x <= 10; $x++) {
								echo '<div style="margin:3px; padding:0px;font-family: Tahoma;font-size: 8pt;">&nbsp;<img src="http://i.oldbk.com/i/align_2.7.gif"></div>';
							}
						}
						if ($ualign == 6) echo '<div style="margin-top:10px;margin-left:60px;color:#593419;font-size:11px;font-weight:bold;">Вы сдали '.$mys.' шт.</div>';
						echo '</div>
						</div>';
						echo '<div style="position:absolute; width:275px; height: 281px; left:290px; top:105px; z-index:92;background-image: url(http://i.oldbk.com/i/city/sub/hram_scroll6.png)">
						<div style="cursor:pointer;text-align:right;margin-top:12px;width:235px;"><img src="http://i.oldbk.com/i/city/sub/ava_op_cross.png" onclick="solo(10);" width="14" height="15"></div>
						<div style="margin-left:38px;margin-top:9px;font-family: Tahoma;font-size: 8pt;">';
						$q = mysql_query('SELECT users.*,avalon.op_score.score FROM avalon.op_score LEFT JOIN oldbk.users ON users.id = avalon.op_score.owner WHERE avalon.op_score.align = 2 ORDER BY avalon.op_score.score DESC LIMIT 10');
						$i = 1;
						while(list($k,$u) = each($tlist[2])) {
							echo '<div style="margin:4px; padding:0px;font-family: Tahoma;font-size: 8pt;">'.nick_histmy($u).'('.$u['score'].')</div>';
							$i++;
						}
						if ($i < 10) {
							for ($x = $i; $x <= 10; $x++) {
								echo '<div style="margin:3px; padding:0px;font-family: Tahoma;font-size: 8pt;">&nbsp;<img src="http://i.oldbk.com/i/align_2.7.gif"></div>';
							}
						}
						if ($ualign == 2) echo '<div style="margin-top:10px;margin-left:60px;color:#593419;font-size:11px;font-weight:bold;">Вы сдали '.$mys.' шт.</div>';
						echo '</div>
						</div>';
						echo '<div style="position:absolute; width:275px; height: 281px; left:555px; top:125px; z-index:92;background-image: url(http://i.oldbk.com/i/city/sub/hram_scroll6.png)">
						<div style="cursor:pointer;text-align:right;margin-top:12px;width:235px;"><img src="http://i.oldbk.com/i/city/sub/ava_op_cross.png" onclick="solo(10);" width="14" height="15"></div>
						<div style="margin-left:38px;margin-top:9px;font-family: Tahoma;font-size: 8pt;">';
						$q = mysql_query('SELECT users.*,avalon.op_score.score FROM avalon.op_score LEFT JOIN oldbk.users ON users.id = avalon.op_score.owner WHERE avalon.op_score.align = 3 ORDER BY avalon.op_score.score DESC LIMIT 10');
						$i = 1;
						while(list($k,$u) = each($tlist[3])) {
							echo '<div style="margin:4px; padding:0px;font-family: Tahoma;font-size: 8pt;">'.nick_histmy($u).'('.$u['score'].')</div>';
							$i++;
						}
						if ($i < 10) {
							for ($x = $i; $x <= 10; $x++) {
								echo '<div style="margin:3px; padding:0px;font-family: Tahoma;font-size: 8pt;">&nbsp;<img src="http://i.oldbk.com/i/align_2.7.gif"></div>';
							}
						}
						if ($ualign == 3) echo '<div style="margin-top:10px;margin-left:60px;color:#593419;font-size:11px;font-weight:bold;">Вы сдали '.$mys.' шт.</div>';
						echo '</div>
						</div>';
					}

					$l1 = 0;
					$l2 = 0;
					$l3 = 0;


					$q = mysql_query('SELECT align, sum( score ) AS `sumscore` FROM avalon.`op_score` GROUP BY align');
					while($sc = mysql_fetch_assoc($q)) {
						switch($sc['align']) {
							case 2:
								$l2 = $sc['sumscore'];
							break;
							case 3:
								$l3 = $sc['sumscore'];
							break;
							case 6:
								$l1 = $sc['sumscore'];
							break;
						}
					}			


					echo '<div style="position:absolute; left:204px; top:325px; z-index:91;"><h6 style="margin:0px;padding:0px;"><font color="#0479b7">'.$l1.'</font></h6></div>';
					echo '<div style="position:absolute; left:420px; top:305px; z-index:91;"><h6 style="margin:0px;padding:0px;"><font color="#545454">'.$l2.'</font></h6></div>';
					echo '<div style="position:absolute; left:638px; top:325px; z-index:91;"><h6 style="margin:0px;padding:0px;"><font color="#fe2000">'.$l3.'</font></h6></div>';

					echo '<script>Cufon.replace("h6");</script>';
				    	echo '</td></tr></table>';

				    	die();
				}
				else
				if($_GET['got'] && $_GET['level11'])
				{

					if ($_GET['doit'])
					{
						$_GET['doit']=(int)$_GET['doit'];
						if ($_GET['doit']>3000 && $_GET['doit']<3030)
						{
							//ищим в сумке и жертвуем храму...
							$check=mysql_fetch_array(mysql_query("select name, count(*) as k from oldbk.inventory where setsale = 0 and owner='".$user[id]."' and prototype='".$_GET['doit']."';"));
							if ($check[k]>0)
							{
							  // echo "ok";
							   //удаляем предмет и суммируем счетчик
								if (mysql_query("DELETE FROM oldbk.inventory where owner='".$user[id]."' and setsale = 0 and prototype='".$_GET['doit']."'; ") )
								{
									
								$put_pref='oldbk.';
								
								
							      	mysql_query("INSERT {$put_pref}`church2` (`resid`,`owner`,`rescount`) values('".$_GET['doit']."','".$user[id]."', '{$check[k]}' ) ON DUPLICATE KEY UPDATE `rescount` =`rescount`+".$check[k].";");
							   //добавляем репутацию
						  	 // тут надо формула для подсчета репыы
						  	 //тест
									   $crep[3001]=4;
									   $crep[3002]=4;
									   $crep[3003]=4;
									   $crep[3004]=4;
									   $crep[3005]=4;
									   $crep[3006]=8;
									   $crep[3007]=8;
									   $crep[3008]=8;
									   $crep[3009]=15;
									   $crep[3010]=15;
									   $crep[3011]=15;
									   $crep[3012]=15;

								      $crep[3013]=6;
								      $crep[3014]=5;

								      $crep[3015]=6;
								      $crep[3016]=7;

								      $crep[3017]=8;
								      $crep[3018]=8;

								      $crep[3019]=15;
								      $crep[3020]=15;
								      $crep[3021]=15;


								   $repa=$check[k]*$crep[$_GET['doit']];


									$ar = 0;
									if ($user['prem']) $ar += 0.1;
									
													//дополнительный бонус
										$eff = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '9101' ;")); 	
										if ($eff['id']>0)
											{
											 $ar +=$eff['add_info'];
											}
									
									$ua = intval($user['align']);
									if ($ua == 1) $ua = 6;
									if (GetOpRs() == $ua) $ar += 0.05; 

									$repa = $repa * (1+$ar);

									$repa=(int)($repa);
								   	$msg="<b>Вы пожертвовали на внутреннюю отделку: ".$check[name]." x".$check[k]." </b><br>И получили $repa репутации.<br> ";
								   	mysql_query("UPDATE `users` SET `rep`=`rep`+'".$repa."', `repmoney` = `repmoney` + '".$repa."' WHERE `id`='".$user[id]."' LIMIT 1; ");

					//new delo
					$rec['owner']=$user[id]; 
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user[money];
					$rec['owner_balans_posle']=$user[money];					
					$rec['owner_rep_do']=$user[repmoney];
					$rec['owner_rep_posle']=($user[repmoney]+$repa);					
					$rec['target']=0;
					$rec['target_login']='Храм';
					$rec['type']=260;
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['sum_rep']=$repa;					
					$rec['item_id']='';
					$rec['item_name']=$check[name];
					$rec['item_count']=$check[k];
					$rec['item_type']=210;
					$rec['item_cost']=0;
					$rec['item_dur']=0;
					$rec['item_maxdur']=0;
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']='';
					add_to_new_delo($rec);
								   	
								   	$user['rep']=$user['rep']+$repa;
								//      		   echo $check[k];
					      		}
					      		else
						      	{
									$msg='<font color=red><b>Ошибка сообщите админам...</b></font><br>';
						      	}
						   }
						   else
						   {
							$msg='<font color=red><b>У Вас нет нужного предмета...</b></font><br>';
						   }
						}
						else
						{
						$msg='<font color=red><b>Неподходящий предмет...</b></font><br>';
						}
					}




					?>
					<div style='color:#8F0000; font-weight:bold; font-size:16px; text-align:center; float:center;'> Да пребудет с тобой сила, <?=($user['login']); ?>, репутация:<?=($user['rep']); ?>
					<?
					//test medals
					//$med=mysql_fetch_array(mysql_query("select * FROM users where id='{$user[id]}' and rep > 20000 and medals not like '%011;%' "));
					$need_med=true;
					$med =str_replace("|",";",$user['medals']); //берем все и открытые и закрытые значки
					$medals = explode(";",$med);
					foreach($medals as $k=>$v)
					{
						if ($v=="011")
						{
						$need_med=false;
						break;
						}
					}
		
					if (($need_med) and ($user[rep]>=20000) )
					   {
					   mysql_query("UPDATE `users` SET `medals` = CONCAT('011;',`medals`) WHERE  `id` = '{$user[id]}' ");
					   echo 'Вы получили: <img src="http://i.oldbk.com/i/medal_hram_011.gif" onMouseOut="HideOpisShmot()" onMouseOver="OpisShmot(event,\'Рыцарь Лабиринта\')"> ';
					   }

					?>

					</div><div style='float:right; padding-right:6px;'>
					</div>

					<center>
					<table border="0" width="100%">
						<tr>
							<td>&nbsp;</td>
							<td width="99%">

					<center>
					<table border="0" cellpadding=0 cellspacing=0 width="710" height="489" background=i/sh/temle/bg.jpg>
						<tr>
							<td width="190" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="24" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="39" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="30" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="187" height="10">&nbsp;</td>
						</tr>
						<?
				if ($_GET[level11])		
						{
						$resitems=mysql_query("select * ,(select sum(rescount) from  church2 where id=resid) as cit from oldbk.eshop where id > 3000 and id < 3030;");
					//temp
					$allneed[1]=15000;
					$allneed[2]=15000;
					$allneed[3]=15000;
					$allneed[4]=15000;
					$allneed[5]=15000;
					$allneed[6]=5000;
					$allneed[7]=7500;
					$allneed[8]=7500;
					$allneed[9]=8000;
					$allneed[10]=8000;
					$allneed[11]=8000;
					$allneed[12]=8000;

					$nn=0;
					while ($varray = mysql_fetch_array($resitems))
					{
					$nn++;
					$it_arry[$nn]=$varray;
					//$it_arry[$nn][need]= ", Собрано: ".(int)$varray[cit]."/".$allneed[$nn]." (".round($varray[cit]*100/$allneed[$nn],2)."%)";
					$it_arry[$nn][need]= ", Собрано: ".(int)($varray[cit]-$allneed[$nn]);
					}						
						
						
						?>
						<tr>
							<td width="190" height="60">&nbsp;</td>
							<td width="60" height="60"><?echo ($it_arry[1][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3001','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[1][img]."' title='".$it_arry[1][name].$it_arry[1][need]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>
							<td width="24" height="60">&nbsp;</td>
							<td width="60" height="60" ><?echo ($it_arry[2][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3002','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[2][img]."' title='".$it_arry[2][name].$it_arry[2][need]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>
							<td width="39" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[3][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3003','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[3][img]."' title='".$it_arry[3][name].$it_arry[3][need]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>
							<td width="30" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[4][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3004','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[4][img]."' title='".$it_arry[4][name].$it_arry[4][need]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>
							<td width="187" height="60">&nbsp;</td>
						</tr>
						<tr>
							<td width="190" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="24" height="10" >&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="39" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="30" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="187" height="10">&nbsp;</td>
						</tr>
						<tr>
							<td width="190" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[5][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3005','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[5][img]."' title='".$it_arry[5][name].$it_arry[5][need]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>

							<td width="24" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[6][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3006','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[6][img]."' title='".$it_arry[6][name].$it_arry[6][need]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>
							<td width="39" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[7][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3007','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[7][img]."' title='".$it_arry[7][name].$it_arry[7][need]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>
							<td width="30" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[8][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3008','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[8][img]."' title='".$it_arry[8][name].$it_arry[8][need]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>
							<td width="187" height="60">&nbsp;</td>
						</tr>
						<tr>
							<td width="190" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="24" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="39" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="30" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="187" height="10">&nbsp;</td>
						</tr>
						<tr>
							<td width="190" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[9][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3009','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[9][img]."' title='".$it_arry[9][name].$it_arry[9][need]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>

							<td width="24" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[10][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3010','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[10][img]."' title='".$it_arry[10][name].$it_arry[10][need]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>

							<td width="39" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[11][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3011','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[11][img]."' title='".$it_arry[11][name].$it_arry[11][need]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>

							<td width="30" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[12][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3012','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[12][img]."' title='".$it_arry[12][name].$it_arry[12][need]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>

							<td width="187" height="60">&nbsp;</td>
						</tr>
						<tr>
							<td width="190">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="24" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="39" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="30" height="10">&nbsp;</td>
							<td width="60" height="10">&nbsp;</td>
							<td width="187">&nbsp;</td>
						</tr>


						<tr>
							<td width="190" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[13][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3013','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[13][img]."' title='".$it_arry[13][name]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>

							<td width="24" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[15][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3015','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[15][img]."' title='".$it_arry[15][name]."'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>

							<td width="39" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[17][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3017','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[17][img]."' title='Портальные минералы X Y'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>

							<td width="30" height="60">&nbsp;</td>
							<td width="60" height="60"  ><?echo ($it_arry[19][img])?"<a href=\"javascript:;\" onclick=\"window.open('?info=3019','','scrollbars=no,toolbar=no,status=no,resizable=yes,width=400,height=400')\"><img src='http://i.oldbk.com/i/sh/".$it_arry[19][img]."' title='Портальные кристаллы &alpha; &beta; &gamma;'></a>":"<img src='i/sh/temle/sroll_null.gif'>";?></td>

							<td width="187" height="60">&nbsp;</td>
						</tr>
					
						<?
						}
						?>
						
						<tr>
							<td width="190">&nbsp;</td>
							<td width="60" height="60" ></td>
							<td width="24" height="60">&nbsp;</td>
							<td width="159" height="173" colspan="3" rowspan="2" align=center><a href="javascript:;" onclick="window.open('http://i.oldbk.com/i/sh/fulll_scroll_ok.jpg','','scrollbars=yes,toolbar=no,status=no,resizable=yes,width=740,height=1340')"><img src='i/sh/temle/paper_shadow.gif' title='<?=$sc_arry[15][user_text];?>'></a> </td>
							<td width="277" height="60" colspan="3" valign=center><br><br> </td>
							
						</tr>
						
					<?

					{
						 //проверяем наличие
						$arr_usr_it=array();
						$get_users_res=mysql_query("select * from oldbk.inventory USE INDEX (owner_9) where ( prototype > 3000 and prototype < 3030)  and owner='".$user['id']."' and setsale = 0 ORDER BY prototype DESC ; ");
						while ($row = mysql_fetch_array($get_users_res))
						{
						$i++;
						$arr_usr_it[$i]=$row;
						}
						echo $msg;						
						if (($i==0) and ($msg==''))
						{
							 echo "<br><b>К сожалению у Вас нет нужных ресурсов...</b>";
						}
						else
						{
							for ($k=1;$k<=$i;$k++)
							 {
							  if ($arr_usr_it[$k][name]==$arr_usr_it[$k+1][name])
							   {
							   $kolit++;
							   }
							   else
								 {
								 echo "<a href=?got=1&level11=1&doit=".$arr_usr_it[$k]['prototype']."> Отдать ".$arr_usr_it[$k][name]." x".($kolit+1)." алтарю.</a><br>";
								 $kolit=0;
							 	}

							 }

						}
					}




					?>

					</td>
						</tr>
						</table>


					</td>
						</tr>
					</table></center>

					<?


				    die();
				}
                else
				{
					$is_quests=take_new_quest();
					/*
					if($user[id]==28453)
					{
						print_r($_GET);
						echo '<br>';
						print_r($_POST);
					}
					*/
					if($_GET[put]==1 && $_POST[target])
					{
						$sql = 'SELECT * FROM oldbk.inventory WHERE id='.(int)$_POST[target].' AND owner='.$user[id].' AND `dressed`=0  AND bs_owner = 0 AND labonly=0  AND `setsale`=0 LIMIT 1;';
						$data = mysql_query($sql);
						if (mysql_num_rows($data) > 0)
						{
			                while($row=mysql_fetch_array($data))
			                {
			                    if($row[nlevel]<=$user[level])
			                    {
			                    		
			                    
//				                    mysql_query('insert into ancients_templ_log   (`owner`,`type`,`count`)  VALUES  ('.$user[id].','.$row[prototype].',"1") ON DUPLICATE KEY UPDATE count=count+1, used=0;  ');
//				                    if(mysql_affected_rows()>0)
				                    if (true)
							{

				/*
					                        //echo 'Вы поставили свечу';
		                                    		mysql_query('delete from oldbk.inventory where owner='.$user[id].' and setsale = 0 AND id ='.$row[id].';');
		                */

				                        	$is_quests=take_new_quest();

	                                //    print_r($is_quests);
					                        ?>
					                        <div id="quest" style="z-index: 300; position: absolute; left: 50px; top: 30px;
												background: #DEDCDD url('http://capitalcity.oldbk.com/i/quest/fp_1.png') no-repeat;
												background-position: top;
												width: 688px;
												border: 1px solid black; display: <?=$sh_div?>;"><br>
											<? echo "<table cellpadding=7 cellspasing=5 style=\"background: url('http://capitalcity.oldbk.com/i/quest/fp_2.jpg') repeat-y;\">";

									            	foreach($is_quests as $q_id => $values)
									            	{

												if ($q_id == 100) continue;
												if ($q_id == 132) continue;
												if ($q_id == 134) continue;
												if ($q_id == 108) continue;
												if ($q_id == 141) continue;
												if ($q_id == 129) continue;
												if ($q_id == 133) continue;
												if ($q_id == 143) continue;
												if ($q_id == 130) continue;
												if ($q_id == 114) continue;
												if ($q_id == 119) continue;
												if ($q_id == 124) continue;
												if ($q_id == 142) continue;
												                                                  /*
												if ($q_id == 142 && $user['repmoney'] >= 600000) {
													$vzt = '<strike><b>взять</b></strike> (у вас больше 600000 реп. покупки)';
												} else {

												}                                                   */
									            		//print_r($values);
												$vzt = '<a href=?take='.$q_id.'&sv='.$row['id'].'>взять</a><td>&nbsp;';
									            		echo "<tr>
									            				<td>&nbsp;</td><td align=left valign=top>&nbsp;&nbsp;<b><font color=#003388>". $values[qname] . "</font></b></td>
									            				<td align=left valign=top>" .$values[qdescription] . "<br><div id='txt_".$q_id."' style=\"display: block;\"><a href=#".$q_id." onclick=\"showhidden('".$q_id."');\">Подробнее...</a></div>
									            				<div id='txt1_".$q_id."' style=\"display: none;\"><a href=#".$q_id." onclick=\"showhidden('".$q_id."');\">Скрыть.</a></div>
									            				<br><div id='id_".$q_id."' style=\"display: none;\">".$values[qstart]."</div></td>
									            				<td align=center valign=middle>".$vzt."</td></td>
									            			</tr>
									            			<tr><td colspan=4><hr></td></tr>";
									            	}
									            		?>
									              </tr>
									    	 </table>

							                 <img src="http://capitalcity.oldbk.com/i/quest/fp_3.png">
											</div>
						                     <?

				                    		}
				        		}
				        		else
				        		{
				        			?>
				        			<SCRIPT>
										alert('Уровень маловат...');
									</SCRIPT>
									<?
				        		}
			                }
						}
						else
						{

						}
					}


			        $quests_types='';
			        if(count($is_quests)>0)
			        {
			        	?>
							<SCRIPT>
							function showhidden(id)
							{
							var st=document.getElementById('id_'+id).style.display;
							if (st == 'none')
								{
									document.getElementById('id_'+id).style.display = 'block';
									document.getElementById('txt_'+id).style.display = 'none';
									document.getElementById('txt1_'+id).style.display = 'block';
								}
								else
								{
									document.getElementById('id_'+id).style.display = 'none';
									document.getElementById('txt_'+id).style.display = 'block';
									document.getElementById('txt1_'+id).style.display = 'none';

								}
							}
				   				$(document).ready(function() {
								 show_table('Квесты','?put=2');
								});
							</SCRIPT>
						<?

			        	foreach($is_quests as $type => $value)
			        	{
			                if($value[used]==0)
				            {
				                $quests_types.='';
				               // echo $type . '<br> QWEQWEQWEQWE ';
				              //  print_r($value);
			                }
			        	}
			        }
					
					
					if ($_GET['got'] && $_GET['level6'] &&  $user[battle]==0 )
					{
						if($user[align]==6 || (int)$user[align]==1 || $user[align]==0)
						{
							?>
							<SCRIPT>
				   				$(document).ready(function() {
								 showitemschoice('Свеча света','?put=1','6');
								});
							</SCRIPT>
							<?
						}
						else
						{
			               			$err=1;
						}

					}
					elseif (($_GET['got'] && $_GET['level2']) &&  ($user[battle]==0))
					{
						if((int)$user[align]==2 || $user[align]==0)
						{
							?>
							<SCRIPT>
				   				$(document).ready(function() {
								 showitemschoice('Свеча нейтралов','?put=1','2');
								});
							</SCRIPT>
							<?
			   			}
						else
						{
				 			$err=1;
						}
					}
			       	elseif (($_GET['got'] && $_GET['level3']) &&  ($user[battle]==0))
					{
						if($user[align]==3 || $user[align]==0)
						{
							?>
							<SCRIPT>
				   				$(document).ready(function() {
								 showitemschoice('Свеча тьмы','?put=1','3');
								});
							</SCRIPT>
							<?
			            		}
						else
						{
			 				$err=1;
						}
					}
				
					if($err==1)
					{
						 ?>
							<SCRIPT>
								alert('Это не ваш алтарь...');
							</SCRIPT>
						<?
					}
							    $fon = 'fon';
								$svecha1='svechas';
								$svecha2='svechan';
								$svecha3='svechad';

							    $butt1= 'newtzals';
							    $butt2= 'newtzalp';

								echo "<table width=1>
									<tr>
										<td>
											<div style=\"position:relative; \" id=\"ione\">
												<img src=\"http://i.oldbk.com/i/city/",$fon,".jpg\" alt=\"\" border=\"0\"/>";

										buildset(10,$butt1,19,39,"Зал противостояния");
									    buildset(11,$butt2,19,548,"Зал пожертвований");

										buildset(6,$svecha1,328,154,"Свеча света");
										buildset(2,$svecha2,318,372,"Свеча нейтралов");
										buildset(3,$svecha3,330,584,"Свеча тьмы");


				}
				?>
				</td>
			</tr>
		</table>
</body>
</html>
