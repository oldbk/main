<?php


	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	function MyDie() {
		Redirect("castles_osada.php");
	}


	function mk_my_item($telo, $proto, $item, $score) {
		$dress=mysql_fetch_array(mysql_query("select * from oldbk.shop where id='{$proto}' ;"));
		$dress['present'] = "Удача"; // все подарком
		$dress['notsell']=1;	
		
		if ($dress['goden']==0) {
			$dress['goden']=90;
		}
	
		if ($dress[id]>0) 
			{
		
			if(mysql_query("INSERT INTO oldbk.`inventory`
				(`ekr_flag`,`getfrom`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`, `img_big` ,`maxdur`,`isrep`,`letter`,`notsell`,
				`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
				`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
				`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`
				)
				VALUES
					(0,0,'{$dress['id']}','{$telo[id]}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}', '{$dress['img_big']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['notsell']}',
					'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
					'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
					'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."',
					'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','','','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}' , '{$dress['present']}'
					) ;"))
				{
					$good = 1;
					$insert_item_id=mysql_insert_id();
					$dress['idcity']=$telo[id_city];
					$dress['id']=$insert_item_id;
		        	} else {
					$good = 0;
				}		
				
	
				if ($good) {
					$rec['owner']=$telo['id'];
					$rec['owner_login']=$telo['login'];
					$rec['target']=0;
					$rec['target_login']='Осада замка';
					$rec['owner_balans_do']=$telo['money'];
					$rec['owner_balans_posle']=$telo['money'];
					$rec['type']=3223;//  
					$rec['sum_kr']=$dress['cost'];
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($dress);
					$rec['item_name']=$dress['name'];
					$rec['item_count']=1;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']=$dress['includemagic'];
					$rec['item_incmagic_count']=$dress['includemagicdex'];
					$rec['item_arsenal']='';
					$rec['add_info'] = $item['name']." [".$item['duration']."/".$item['maxdur']."]";
					add_to_new_delo($rec);
					addchp('<font color=red>Внимание!</font> Вы внесли свою лепту в осаду замка и получили '.declOfNum($score,array("очка","очка","очков")).' в событии! Получена награда: «'.link_for_item($dress).'» 1 шт.','{[]}'.$telo['login'].'{[]}',-1,0);
					return $dress['name'];
				} else {
					return false;
				}
		} else {
			return false;
		}
	}


	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");

	require_once('connect.php');
	require_once('functions.php');
	require_once('castles_config.php');
	require_once('castles_functions.php');

	if ($user['room'] != 72002) Redirect("main.php");
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { Redirect("fbattle.php"); }


	if (isset($_GET['exit'])) {
		mysql_query('UPDATE `users` SET room = 70000 WHERE id = '.$_SESSION['uid']) or die();		
		Redirect("castles.php?level=999");
	}

	$cid = 999;
	$proto = [3004050 => 3,3004051 => 6,3004052 => 10,3004053 => 12];
	$protolimits = [3004050 => 10,3004051 => 20,3004052 => 0,3004053 => 0];


	$c = mysql_query('SELECT * FROM castles_osada WHERE id = '.$cid);
	if ($c === false || mysql_num_rows($c) == 0) die();

	$c = mysql_fetch_assoc($c);

	if (isset($_GET['use'])) {
		if (!(time() >= $c['nextosada'] && time() <= $c['osadaend'])) {
			$retarray = ['redirect' => "1"];
			echo json_encode($retarray);
			mysql_query('COMMIT');
			die();
		}

		if ($user['level'] < 8) {
			$retarray = ['redirect' => "1"];
			echo json_encode($retarray);
			mysql_query('COMMIT');
			die();
		}


		$q = mysql_query('SELECT * FROM castles_osada_uses WHERE owner = '.$user['id'].' and type = '.intval($_GET['use']));
		$use = ['count' => 0];
		if (mysql_num_rows($q)) {
			$use = mysql_fetch_assoc($q);
		}

		$error = "";

		if ($use['count'] < $protolimits[$_GET['use']] || $protolimits[$_GET['use']] == 0) {
			$q = mysql_query('SELECT * FROM inventory WHERE owner = '.$user['id'].' and prototype = '.intval($_GET['use']).' and dressed = 0 and setsale = 0 LIMIT 1');
			if (mysql_num_rows($q) > 0) {
				$item = mysql_fetch_assoc($q);
				if ($item) {
					$q = mysql_query('UPDATE castles_osada SET score = score + '.$proto[$item['prototype']].' WHERE id = '.$cid.' and score < '.$osada_end);
					if (mysql_affected_rows() > 0) {
						mysql_query('INSERT INTO `castles_osada_uses` (`owner`,`type`,`count`) 
								VALUES(
									'.$user['id'].',
									"'.$item['prototype'].'",
									"1"
								) 
								ON DUPLICATE KEY UPDATE `count` = `count` + 1
						');
	
						mysql_query('DELETE FROM inventory WHERE id = '.$item['id']);

						if ($_GET['use'] == 3004050) {
							mk_my_item($user, 3004100,$item,$proto[$item['prototype']]);
						} elseif ($_GET['use'] == 3004051) {
							mk_my_item($user, 3004101,$item,$proto[$item['prototype']]);
						} elseif ($_GET['use'] == 3004052) {
							mk_my_item($user, 3004102,$item,$proto[$item['prototype']]);
						} elseif ($_GET['use'] == 3004053) {
							mk_my_item($user, 3004103,$item,$proto[$item['prototype']]);
						}


						$OsadaRating = new \components\Helper\rating\OsadaRating();
						$OsadaRating->value_add = $proto[$item['prototype']];


						$app->applyHook('event.rating', $user, $OsadaRating);
					} else {
						$retarray = ['redirect' => "1"];
						json_encode($retarray);
						die();
					}
				}		
			} else {
				$error = iconv("windows-1251","UTF-8","Такого предмета нет в наличии!");
			}
		} else {
			$error = iconv("windows-1251","UTF-8","Достигнут лимит использования предмета, попробуйте завтра!");
		}

		
		$q = mysql_query('START TRANSACTION') or MyDie();

		$c = mysql_query('SELECT * FROM castles_osada WHERE id = '.$cid.' FOR UPDATE') or MyDie();
		if ($c === false || mysql_num_rows($c) == 0) die();
	
		$c = mysql_fetch_assoc($c);


		if ($c['score'] >= $osada_end) {
			// осада на замок закончена
			mysql_query('truncate table castles_osada_uses') or MyDie();

			$OsadaRating = (new \components\Helper\rating\OsadaRating(2))->setEndStart();
			$app->applyHook('event.operation', $OsadaRating);

			$retarray = ['redirect' => "1"];
			echo json_encode($retarray);
			mysql_query('COMMIT');
			die();
		}
		mysql_query('COMMIT');
	
		
		$available = [];
		$uses = [];
		foreach ($proto as $k => $v) {
			$available[$k] = 0;
			$uses[$k] = 0;
		}

		$q = mysql_query('SELECT prototype,count(*) as kk FROM inventory WHERE owner = '.$user['id'].' and prototype IN ('.implode(",",array_keys($proto)).') GROUP BY prototype');
		while($r = mysql_fetch_assoc($q)) {
			$available[$r['prototype']] = $r['kk'];
		}


		$q = mysql_query('SELECT * FROM castles_osada_uses WHERE owner = '.$user['id']);

		$myscore = 0;
		while($r = mysql_fetch_assoc($q)) {
			$uses[$r['type']] = $r['count'];
			$myscore += $proto[$r['type']]*$r['count'];
		}




		$retarray = [
			'pr' => $pr = round(100-($c['score']/$osada_end) * 100,2),
			'uses' => $uses,
			'available' => $available,
			'score' => $myscore,
		];

		if (!empty($error)) {
			$retarray['error'] = $error;
		}

		echo json_encode($retarray);
		die();
	}

	if (isset($_GET['showitems'])) {
		echo "<table>";
		$q = mysql_query('SELECT * FROM shop WHERE id IN ('.implode(",",array_keys($proto)).')');
		$protodata = [];
		while($r = mysql_fetch_assoc($q)) {
			$protodata[$r['id']] = $r;
		}

		$available = [];
		$q = mysql_query('SELECT prototype,count(*) as kk FROM inventory WHERE owner = '.$user['id'].' and prototype IN ('.implode(",",array_keys($proto)).') GROUP BY prototype');
		while($r = mysql_fetch_assoc($q)) {
			$available[$r['prototype']] = $r['kk'];
		}
		


		$q = mysql_query('SELECT * FROM castles_osada_uses WHERE owner = '.$user['id']);

		$uses = [];
		$myscore = 0;
		while($r = mysql_fetch_assoc($q)) {
			$uses[$r['type']] = $r['count'];
			$myscore += $proto[$r['type']]*$r['count'];
		}


		echo '<tr><td align=center colspan='.count($proto).'><b>Предметы для осады:</b></td></tr>';
		echo '<tr><td colspan='.count($proto).'>&nbsp;</td></tr>';



		echo '<tr><td align="center" colspan='.count($proto).'><span id="currentscore" style="font-size:8pt;font-weight:bold;">Текущий урон: '.$myscore.'</span></td></tr>';
		echo '<tr><td colspan='.count($proto).'>&nbsp;</td></tr>';

		echo '<tr>';
		foreach($proto as $k => $v) {
			echo '<td align="center">'.$v.' урона</td>';
		}
		echo '</tr>';

		echo '<tr>';
		foreach($proto as $k => $v) {
			if (!isset($available[$k])) {
				echo '<td align="center"><div id="avail'.$k.'" class="invgroupcount2">0</div><a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($protodata[$k],true).'.html"><img src="http://i.oldbk.com/i/sh/'.$protodata[$k]['img'].'"></a></td>';
			} else {
				echo '<td align="center"><div id="avail'.$k.'" class="invgroupcount">'.$available[$k].'</div><a target="_blank" href="http://oldbk.com/encicl/'.link_for_item($protodata[$k],true).'.html"><img title="'.$protodata[$k]['name'].'" alt="'.$protodata[$k]['name'].'" src="http://i.oldbk.com/i/sh/'.$protodata[$k]['img'].'"></a></td>';
			}
		}
		echo '</tr>';


		echo '<tr>';
		foreach($proto as $k => $v) {
			echo '<td align="center">(<span id="uses'.$k.'">'.(!isset($uses[$k]) ? 0 : $uses[$k]).'</span>/'.($protolimits[$k] == 0 ? "&infin;" : $protolimits[$k]).')</td>';
		}
		echo '</tr>';


		echo '<tr>';
		foreach($proto as $k => $v) {
			echo '<td align="center"><div class="button-mid btn" OnClick="useitem('.$k.');">Применить</div></td>';
		}
		echo '</tr>';

		echo "</table>";
		die();
	}
?>

<HTML>
<HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META HTTP-EQUIV=Expires CONTENT=0>
<META HTTP-EQUIV=imagetoolbar CONTENT=no>
<script type="text/javascript" src="/i/globaljs.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>
function showitems() {
	$.get('castles_osada.php?showitems',function( data ) {
		$('#showitemsall').center();
		document.getElementById("showitemsall").style.display = "";
		$('#showitemsalldata').html(data);
	});
	
}
jQuery.fn.center = function () {
	this.css("position","absolute");
	this.css("top", "200px");
	this.css("left", Math.max(0, (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft()) + "px");
	return this;
}

function useitem(proto) {
	$.get('castles_osada.php?use='+proto,function( data ) {
		var ret = JSON.parse(data);
		if (ret.error !== undefined) {
			alert(ret.error);
			return;
		}
		if (ret.redirect !== undefined) {
			location.href = "castles_osada.php";
			return;
		}
		document.getElementById("pr11").style.width = ret.pr+"%";
		document.getElementById("pr22").innerHTML = (ret.pr+"%");
		document.getElementById("currentscore").innerHTML = "Текущий урон: "+ret.score;

		$.each(ret.uses, function(index, value) {
			document.getElementById("uses"+index).innerHTML = value;
		}); 
		$.each(ret.available, function(index, value) {
			if (!value) {
				document.getElementById("avail"+index).className = "invgroupcount2";
				document.getElementById("avail"+index).innerHTML = value;
			} else {
				document.getElementById("avail"+index).className = "invgroupcount";
				document.getElementById("avail"+index).innerHTML = value;
			}
		}); 

	});
}

</script>
<style>
.button-mid {
    padding-top: 4px;
    cursor: pointer;
    width: 75px;
    height: 15px;
    font-size: 10px;
    background: url("http://i.oldbk.com/i/images/buttons/button_mid_Sprite.jpg") no-repeat 0 -19px;
}
.button-mid:hover {
    background: url("http://i.oldbk.com/i/images/buttons/button_mid_Sprite.jpg") no-repeat 0 -38px;
}
.button-mid:active {
    background: url("http://i.oldbk.com/i/images/buttons/button_mid_Sprite.jpg") 0 0;
}
.invgroupcount {
	position: relative;
	top: 59px;
	left: 0px;
	font-weight:bold;
	background-color:green;
	width:58px;
	font-size:8pt;
	color:white;
	filter:alpha(opacity=90);
	-moz-opacity: 0.9;
	opacity: 0.9;
	text-align:center;
}

.invgroupcount2 {
	position: relative;
	top: 59px;
	left: 0px;
	font-weight:bold;
	background-color:red;
	width:58px;
	font-size:8pt;
	color:white;
	filter:alpha(opacity=90);
	-moz-opacity: 0.9;
	opacity: 0.9;
	text-align:center;
}


</style>
</head>
<body onResize="$('#showitemsall').center();" bgcolor=#e2e0e0 leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="margin-left:20px;">

<div id="showitemsall" style="display:none;z-index:50;position:absolute;">

<table border=0 cellspacing=0 cellpadding=0 width=550><tr><td width=550 style="background-repeat-y:no-repeat;" background="http://i.oldbk.com/i/newd/pop/up_bg_2.jpg"><img OnClick="document.getElementById('showitemsall').style.display = 'none';" OnMouseOut="this.src='http://i.oldbk.com/i/newd/pop/close_butt.jpg';" OnMouseOver="this.src='http://i.oldbk.com/i/newd/pop/close_butt_hover.jpg';" src="http://i.oldbk.com/i/newd/pop/close_butt.jpg" align=right></td></tr><tr><td background="http://i.oldbk.com/i/castles/bg-y_4-osada.jpg" align="center">
<table><tr>
<td valign="top" width="100%" align="center">
<div id="showitemsalldata" style="text-align:center;">

</div>
</td>
</tr></table></td></tr><tr><td width=550 height=8 background="http://i.oldbk.com/i/newd/pop/down_bg_2.jpg"></td></tr></table>

</div>

<table width="100%" border=0><tr><td width=55% align="center"><h3 style="text-align:right;">Старый замок</td><td align=right><input type=button value='Обновить' onClick="location.href='castles_osada.php?'+Math.random();"> <INPUT TYPE=button value="Вернуться" onClick="location.href='castles_osada.php?exit=1';"></td></tr></table>
<div id="d1">
	<TABLE width=100% height=90% border=0 cellspacing="0" cellpadding="0"><TR><TD align=center valign=top><table width=1 border=0 cellspacing="0" cellpadding="0"><tr><td valign=top>
		<div style="position:relative;">
		<?php
			$bg = "http://i.oldbk.com/i/castles/bg_close2.jpg";

		?>
		<a href="https://oldbk.com/encicl/osadazamka.html" target="_blank"><img style="cursor:pointer; z-index:3; position: absolute; left: 698px; top: 275px;" src="http://i.oldbk.com/i/castles/btn3.png" alt="Правила" title="Правила" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/castles/btn3_h.png';" onmouseout="this.src='http://i.oldbk.com/i/castles/btn3.png';" /></a>
		<a href="https://top.oldbk.com/rate/event/2" target="_blank"><img id="btnreyting" style="cursor:pointer; z-index:3; position: absolute; left: 112px; top: 119px;" src="http://i.oldbk.com/i/castles/btn4_osada_a.png" alt="Рейтинг" title="Рейтинг" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/castles/btn4_osada_ahover.png';" onmouseout="this.src='http://i.oldbk.com/i/castles/btn4_osada_a.png';" /></a>
		<div id="time" style="font-size:7pt;z-index:3; position: absolute; left: 614px; top: 312px; width: 215px; font-family: Tahoma; color: #653d0a; font-weight: bold; text-align:center;">
		<?php
		foreach($osada[$cid] as $k => $v) {
			echo $v['start'].':00 - '.($v['start']+$v['duration']).':00<br />';
		}
		?>
		</div>


		<?php

			// переключаем время
			$bstatus = "";
			if ($c['score'] < $osada_end) {
				if (time() < $c['nextosada'] || time() > $c['osadaend']) {
					// ожидание осады
					if (time() > $c['osadaend']) {
						$bstatus = "Осада замка недавно закончилась";
					} else {
						$bstatus = "Осада начнется через ".prettyTime(null,$c['nextosada']);
					}
					echo '<img id="btnattack" style="cursor:pointer; z-index:3; position: absolute; left: 100px; top: 235px;" src="http://i.oldbk.com/i/castles/btn2_osada.png" alt="Принять участие" title="Принять участие" class="aFilter2" />';
				} elseif (time() >= $c['nextosada'] && time() <= $c['osadaend']) {
					if ($user['level'] >= 8) {
						// осада
						echo '<img OnClick="showitems();" id="btnattack" style="cursor:pointer; z-index:3; position: absolute; left: 100px; top: 235px;" src="http://i.oldbk.com/i/castles/btn2_osada_a.png" alt="Принять участие" title="Принять участие" class="aFilter2" onmouseover="this.src=\'http://i.oldbk.com/i/castles/btn2_osada_ah.png\';" onmouseout="this.src=\'http://i.oldbk.com/i/castles/btn2_osada_a.png\';" />';
						$bstatus = "Идёт осада замка";
		
						$pr = round(100-($c['score']/$osada_end) * 100,2);
						
		
						?>
						<div style="position: absolute; left:150px; top:42px; width:620px;height:12px;background-color:red;padding:0px;margin:0px;border:solid black 0px;font-size:1px; text-align:left" id="prcont">
							<div id="pr11" style="width:<?=round($pr);?>%;height:100%;padding:0px;margin:0px;background-color:#08F721;" id="barl">
							<div style="text-shadow: 0 0 0.1em black, 0 0 0.1em black, 0 0 0.1em black, 0 0 0.1em black;position: relative; color:white; top: -2px; font-weight: bold; font-size:12px; text-align:center" id="pr22">
							<?=$pr?>%
							</div>
		
							</div>
						</div>
						<?php
					} else {
						$bstatus = "Идёт осада замка";
						echo '<img id="btnattack" style="cursor:pointer; z-index:3; position: absolute; left: 100px; top: 235px;" src="http://i.oldbk.com/i/castles/btn2_osada.png" alt="Принять участие" title="Принять участие" class="aFilter2" />';
					}
				} else {
					$bstatus = "Осада замка недавно закончилась";
					echo '<img id="btnattack" style="cursor:pointer; z-index:3; position: absolute; left: 100px; top: 235px;" src="http://i.oldbk.com/i/castles/btn2_osada.png" alt="Принять участие" title="Принять участие" class="aFilter2" />';
				}
			} else {
				$bstatus = "Замок уже разрушен, приходите позднее";
				echo '<img id="btnattack" style="cursor:pointer; z-index:3; position: absolute; left: 100px; top: 235px;" src="http://i.oldbk.com/i/castles/btn2_osada.png" alt="Принять участие" title="Принять участие" class="aFilter2" />';
			}
			

		?>
		<div id="headstatus" style="z-index:3; position: absolute; left: 325px; top: 74px; width: 215px; font-family: Tahoma; color: #653d0a; font-weight: bold; text-align:center;">Осада Замка</div>
		<div id="bottomstatus" style="z-index:3; position: absolute; left: 35px; top: 484px; width: 800px; font-family: Tahoma; color: #653d0a; font-weight: bold; text-align:center;"><?php echo $bstatus; ?></div>

		<img id="imgareamap" src="<?php echo $bg; ?>" border="0" style="z-index:1;">
	</td></tr></TABLE></td></tr></table>
</div>
<div id="pl_list" style="z-index: 300; position: absolute; left: 50px; top: 30px;
	width: 800px; background-color: #eeeeee; height: 400px;
	border: 1px solid black; display: none; overflow-y: auto;">
</div>

<div id="pl_list2" style="z-index: 350; position: absolute; left: 20px; top: 10px;width: 321px; background-color: #eeeeee;border: 1px solid black; display: none; overflow-y: auto;">
</div>


</body>
</html>