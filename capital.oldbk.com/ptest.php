<?php if (!isset($_GET['start'])) { ?>
<HTML>
<HEAD>
<META http-equiv=Content-type content="text/html; charset=windows-1251">
<META http-equiv=Pragma content=no-cache>
<META http-equiv=Cache-control content=private>
<META http-equiv=Expires content=0><LINK href="i/main.css" type=text/css rel=stylesheet>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
</HEAD>
<BODY>
<?php
}

session_start();
include "connect.php";
include "functions.php";

$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
if (!ADMIN) die('Страница не найдена :)');

//include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
session_write_close();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	while(list($k,$v) = each($_POST['toreset'])) {
		resetuser($v);
	}
	die('</body></html>');
}


function count_kr($current_exp,$exptable) {
    $cl = 0; $money = 0; $stats = 3; $vinos = 3; $master = 1;
    $stats_count=$stats+12;//общая сумма статов на входе
    while($exptable) {
      if($current_exp >= $exptable[$cl][5]) {
        /* 0stat  1umen  2vinos 3kred, 4level, 5up*/
        $cl = $exptable[$cl][5];
        $money = $money+$exptable[$cl][3];
        $stats = $stats+$exptable[$cl][0];
        $master = $master+$exptable[$cl][1];
        $vinos = $vinos+$exptable[$cl][2];
        $stats_count=$stats_count+$exptable[$cl][0]+$exptable[$cl][2];
      }
      else
      {
        $arr = array('money'=>$money,'stats'=>$stats,'master'=>$master,'vinos'=>$vinos,'cl'=>$exptable[$cl][5],'count_stats'=>$stats_count);
      	return $arr;
      }
    }
}

function resetuser($id) {
	global $exptable;
	$user_t = mysql_fetch_array(mysql_query('SELECT * FROM oldbk.users WHERE id = '.$id.';'));
	$user_t=check_users_city_data($user_t[id]);
	if ($user_t['battle'] == 0 && $user_t['in_tower'] == 0 && !(($user_t[room]>=197 and $user_t[room]<=199) or ($user_t[room]>=211 and $user_t[room]<240) or ($user_t[room]>240 and $user_t[room]<270) or ($user_t[room]>270 and $user_t[room]<290))) {
		if ($user_t['id_city'] == 0) {
		 	undressall($id,0);    //раздели

			mysql_query('DELETE from oldbk.users_bonus where owner='.$user_t['id'].';'); //убрали еду
			mysql_query('DELETE from oldbk.effects where owner='.$user_t['id'].' AND type in (11,12,13,14,826);'); //убрали еду
			mysql_query("DELETE FROM oldbk.`labirint_var` WHERE  var='stat_trap'  and  `owner`='".$user_t[id]."' ;");

		 	$must_be=count_kr($user_t['exp'],$exptable);
	
			 //bpbonushp
		    	$user_up = "UPDATE oldbk.users SET intel='0', mudra='0', duh='0', bojes='0', mfire='0', mwater='0', mair='0', mearth='0', mlight='0', mgray='0', mdark='0',
			 		 sila='3', lovk='3', inta='3', vinos='".$must_be[vinos]."', stats='".$must_be[stats]."', maxhp=".($must_be[vinos]*6).",hp=".($must_be[vinos]*6).",
			  	     master='".$must_be[master]."', noj='0', mec='0', topor='0', dubina='0', bpbonushp='0', bpbonussila='0' WHERE id='".$user_t[id]."'";
			
		         if(mysql_query($user_up)) {
	         	 	$telega = "INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$user_t[id]."','','<font color=red>Внимание!</font> На Вашем персонаже были обнаружены лишние/недостающие параметры (статы или умения). Вашему персонажу произведен полный сброс характеристик в соответствии с опытом.');";
	             		mysql_query($telega);
				echo 'Персонаж <b>'.$user_t['login'].'</b> обнулён в Кэпе<br>';
			 }
		} else if ($user_t['id_city'] == 1) {
		 	undressall($id,1);    //раздели

			mysql_query('DELETE from avalon.users_bonus where owner='.$user_t['id'].';'); //убрали еду
			mysql_query('DELETE from avalon.effects where owner='.$user_t['id'].' AND type in (11,12,13,14);'); //убрали еду
			mysql_query("DELETE FROM avalon.`labirint_var` WHERE  var='stat_trap'  and  `owner`='".$user_t[id]."' ;");
	
		 	$must_be=count_kr($user_t['exp'],$exptable);
	
			 //bpbonushp
		    	$user_up = "UPDATE avalon.users SET intel='0', mudra='0', duh='0', bojes='0', mfire='0', mwater='0', mair='0', mearth='0', mlight='0', mgray='0', mdark='0',
			 		 sila='3', lovk='3', inta='3', vinos='".$must_be[vinos]."', stats='".$must_be[stats]."', maxhp=".($must_be[vinos]*6).",hp=".($must_be[vinos]*6).",
			  	     master='".$must_be[master]."', noj='0', mec='0', topor='0', dubina='0', bpbonushp='0', bpbonussila='0' WHERE id='".$user_t[id]."'";
			
		         if(mysql_query($user_up)) {
	         	 	$telega = "INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$user_t[id]."','','<font color=red>Внимание!</font> На Вашем персонаже были обнаружены лишние/недостающие параметры (статы или умения). Вашему персонажу произведен полный сброс характеристик в соответствии с опытом.');";
	             		mysql_query($telega);
				echo 'Персонаж <b>'.$user_t['login'].'</b> обнулён в авалоне<br>';
			 }
		}
	} else {
		echo 'Персонаж <b>'.$user_t['login'].'</b> в бою или в руинах/бс/ристалке<br>';
	}
}

function checkuser($user,&$info) {
	global $exptable;
	$user_t = mysql_fetch_array(mysql_query('SELECT * FROM oldbk.users WHERE id = '.$user['id'].';'));
	$user_t=check_users_city_data($user_t[id]);

	if (($user_t[room]>=197 and $user_t[room]<=199) or ($user_t[room]>=211 and $user_t[room]<240) or ($user_t[room]>240 and $user_t[room]<270) or ($user_t[room]>270 and $user_t[room]<290)) {
		return 0;
	}

 
	$shmots_stats= mysql_fetch_array(mysql_query('select sum(gsila) as gsila, sum(glovk) as glovk, sum(ginta) as ginta, sum(gintel) as gintel, sum(gmp) as gmp,
												sum(ghp) as ghp,
												sum(gnoj) as gnoj, sum(gtopor) as gtopor, sum(gdubina) as gdubina, sum(gmech) as gmech,
												sum(gfire) as gfire, sum(gwater) as gwater, sum(gair) as gair, sum(gearth) as gearth, sum(glight) as glight,
												sum(ggray) as ggray, sum(gdark) as gdark
												from oldbk.inventory where id IN ('.GetDressedItems($user_t,DRESSED_ITEMS).')'));

      	$trsila=0;
      	$tlovka=0;
      	$tvinos=0;

      	$bsila=0;
      	$blovka=0;
      	$binta=0;
      	$bmudra=0;
      	$bmaxhp=0;

	if ($user_t['id_city'] == 0) {
		$bonus=mysql_query('SELECT * FROM oldbk.users_bonus WHERE owner = '.$user_t[id].';');
	} else {
		$bonus=mysql_query('SELECT * FROM avalon.users_bonus WHERE owner = '.$user_t[id].';');
	}
      	if(mysql_affected_rows()>0) {
      	while($tr=mysql_fetch_assoc($bonus))
      	{
      		$bsila+=$tr['sila'];
      		$blovka+=$tr['lovk'];
      		$binta+=$tr['inta'];
      		$bintel+=$tr['intel'];

      		$bmudra+=$tr['mudra'];
      		$bmaxhp+=$tr['maxhp'];
        }
      }
	if ($user_t['id_city'] == 0) {
      		$travma=mysql_query('SELECT * FROM oldbk.effects WHERE type in (11,12,13,14) AND owner = '.$user_t[id].';');
	} else {
      		$travma=mysql_query('SELECT * FROM avalon.effects WHERE type in (11,12,13,14) AND owner = '.$user_t[id].';');
	}
      if(mysql_affected_rows()>0)
      {
      	while($tr=mysql_fetch_assoc($travma))
      	{
      		$trsila+=$tr['sila'];
      		$tlovka+=$tr['lovk'];
      		$tinta+=$tr['inta'];
        }
      }


	$e826 = 0;
	if ($user_t['id_city'] == 0) {
      		$li=mysql_query('SELECT * FROM oldbk.effects WHERE type in (826) AND owner = '.$user_t[id].';');
	} else {
      		$li=mysql_query('SELECT * FROM avalon.effects WHERE type in (826) AND owner = '.$user_t[id].';');
	}


	$e441 = 0;
	if ($user_t['id_city'] == 0) {
      		$li=mysql_query('SELECT * FROM oldbk.effects WHERE type in (441) AND owner = '.$user_t[id].';');
	} else {
      		$li=mysql_query('SELECT * FROM avalon.effects WHERE type in (441) AND owner = '.$user_t[id].';');
	}


      	while($lii=mysql_fetch_assoc($li)) {
		$tmp = explode(":",$lii['add_info']);
		$e441 += $tmp[1];
	}

       $victory_hp=0;
        //    ($trsila+$tlovka+$tinta)-($bsila+$blovka+$binta+$bmudra)
                                //  bpbonushp
       $victory_hp=$user_t['bpbonushp'];
	   $current_stats['sila']=$user_t['sila']-$shmots_stats['gsila']-$bsila+$trsila;
	   $current_stats['lovk']=$user_t['lovk']-$shmots_stats['glovk']-$blovka+$tlovka;
	   $current_stats['inta']=$user_t['inta']-$shmots_stats['ginta']-$binta+$tinta-$e826;
	   $current_stats['intel']=$user_t['intel']-$shmots_stats['gintel']-$bintel;
	   $current_stats['mudra']=$user_t['mudra']-$shmots_stats['gmp']-$bmudra;
	   $current_stats['vinos']=$user_t['vinos'];
	   $current_stats['maxhp']=$user_t['maxhp']-$shmots_stats['ghp']-$bmaxhp-$victory_hp-$e441;
	   $current_stats['maxhp2']=$user_t['vinos']*6;
	   $current_stats['maxmana']=$user_t['mudra']*10;

	   $current_stats['stats']= $user_t['stats'];

	   $current_stats['noj']=$user_t['noj']-$shmots_stats['gnoj'];
	   $current_stats['mec']=$user_t['mec']-$shmots_stats['gmech'];
	   $current_stats['topor']=$user_t['topor']-$shmots_stats['gtopor'];
	   $current_stats['dubina']=$user_t['dubina']-$shmots_stats['gdubina'];

	   $current_stats['mfire']=$user_t['mfire']-$shmots_stats['gfire'];
	   $current_stats['mwater']=$user_t['mwater']-$shmots_stats['gwater'];
	   $current_stats['mair']=$user_t['mair']-$shmots_stats['gair'];
	   $current_stats['mearth']=$user_t['mearth']-$shmots_stats['gearth'];
	   $current_stats['mlight']=$user_t['mlight']-$shmots_stats['glight'];
	   $current_stats['mgray']=$user_t['mgray']-$shmots_stats['ggray'];
	   $current_stats['mdark']=$user_t['mdark']-$shmots_stats['gdark'];
	   $current_stats['master']=$user_t['master'];

	   $current_stats['count_stats']=$current_stats['stats']+$current_stats['sila']+$current_stats['lovk']+
	   $current_stats['inta']+$current_stats['intel']+$current_stats['mudra']+$current_stats['vinos'];


	 //  $current_stats['stats']=$current_stats['count_stats']-$current_stats['vinos'];
	  $current_stats['master']=$current_stats['master']+$current_stats['mdark']+$current_stats['mgray']+$current_stats['mlight']+$current_stats['mearth']+$current_stats['mair']+$current_stats['mwater']+$current_stats['mfire']+$current_stats['noj']+$current_stats['mec']+$current_stats['topor']+$current_stats['dubina'];


 	$must_be=count_kr($user_t['exp'],$exptable);

	if ($user_t['lab'] > 0) {
		if ($user_t['id_city'] == 0) {
			$i_have_st=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`labirint_var` WHERE  var='stat_trap'  and  `owner`='".$user_t[id]."' ;"));
			if ($i_have_st[owner]==$user_t[id]) {
				$must_be['count_stats'] -= 3;
			}
		} else {
			$i_have_st=mysql_fetch_array(mysql_query("SELECT * FROM avalon.`labirint_var` WHERE  var='stat_trap'  and  `owner`='".$user_t[id]."' ;"));
			if ($i_have_st[owner]==$user_t[id]) {
				$must_be['count_stats'] -= 3;
			}
		}
	}


	if(($current_stats['count_stats'])!=$must_be['count_stats']) {
		return 1;
	}
	if($current_stats['master']!=$must_be['master']) {
		return 2;
	}

	if(($current_stats['maxhp'])!=$current_stats['maxhp2']) {
		return 3;
	}

	if(($user_t['mudra']*10) != $user_t['maxmana']) {
		$info['m1'] = $user_t['mudra']*10;
		$info['m2'] = $user_t['maxmana'];
		return 4;
	}

	return 0;
}

if (isset($_GET['start'])) {
	$q = mysql_query('
		SELECT * FROM oldbk.users WHERE (id > 10 AND id != 190672 AND in_tower = 0 AND bot = 0 AND id_city = 0 AND klan != "Adminion" AND klan != "radminion" AND exp < nextup)
		LIMIT '.intval($_GET['start']).',100');
	if (mysql_num_rows($q) == 0) die("DIE");
	$buf = "";
	$lastid = 0;
	while($u = mysql_fetch_assoc($q)) {
		$lastid = $u['id'];
		$info = "";
		$res = checkuser($u,$info);
		if ($res != 0) {
			if ($res == "1") $res = "Invalid stats";
			if ($res == "2") $res = "Invalid masters";
			if ($res == "3") $res = "Invalid heals";
			if ($res == "4") $res = "Invalid mana: ".$info['m1'].':'.$info['m2'];
			if ($u['id_city'] == 0) $city = " capital";
			if ($u['id_city'] == 1) $city = " avalon";
			$lab = "";
			if ($u['lab'] > 0) $lab = "<b>lab</b>";
			$buf .= '<input checked type="checkbox" name="toreset[]" value="'.$u['id'].'"> <b>'.htmlspecialchars($u['login'],ENT_QUOTES)."</b> ".$res." ".$city." ".$lab."<BR>";
		}
	}
	die($lastid."<NEXT>".$buf);
}

?>
<script>

var lmta = 0;
var stop = 0;

function mydone(data) {
	if (data.length == 3) {
	 	$('#res').append("<BR><input type='submit' name='reset' value='reset'> <br>FINISH!");
		$('#sbutton2').hide();
		return;
	} else if (data.length >= 4) {
		pos = data.toString().indexOf("<NEXT>");
		pr = data.substring(0,pos);
		data = data.substring(pos+6);
	 	$('#pres').html("LastId: "+pr);
		if (data.length != 0) {
		 	$('#res').append(data);
		}
		lmta += 100;
		if (stop == 0) {
			setTimeout("startchk();",100);
		} else {
		 	$('#res').append("<BR><input type='submit' name='reset' value='reset'> <br>FINISH!");
			$('#sbutton2').hide();
		}
	}                      
}

function stopchk() {
	stop = 1;
}

function startchk() {
	$.ajax({
		url:'ptest.php?start='+(lmta)}).done(function(data) {
		mydone(data);
	});
}
</script>
<input type="button" id="sbutton" name="start" value="start" OnClick="$('#sbutton').hide();$('#sbutton2').show();startchk();">
<input type="button" id="sbutton2" name="stop" value="stop" style="display:none;" OnClick="stopchk();$('#sbutton2').hide();">
<form METHOD="POST">
<div id="pres">
</div><br>
<div id="res">
</div>
</form>
</BODY>
</HTML>