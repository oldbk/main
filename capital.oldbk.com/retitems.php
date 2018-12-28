<?php

	session_start();

	include "connect.php";
	include "functions.php";	

	if ($user['klan'] != "radminion" && $user['klan'] != "Adminion") die();

include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
?>
<html>
<head>
<META content=INDEX,FOLLOW name=robots>
<META content="1 days" name=revisit-after>
<META http-equiv=Content-type content="text/html; charset=windows-1251">
<META http-equiv=Pragma content=no-cache>
<META http-equiv=Cache-control content=private>
<META http-equiv=Expires content=0>
<script type="text/javascript" src="/i/globaljs.js"></script>
<LINK href="i/main.css" type=text/css rel=stylesheet>

</head>
<BODY leftmargin=5 topmargin=5 marginwidth=0 marginheight=0 bgcolor=#e2e0e0>
<form method="POST">
Укажите ID, каждый в новой строке. Вбивать много ИД можно только, если все они утеряны из одного места, то есть только из ломбарда или только из госа.
Перемешивать нельзя!<br>
<textarea name="retitems" cols=30 rows=10 value=""></textarea><br>
<input type="submit" value="Поиск">
</form>

<?php
if (isset($_POST['toretl']) && isset($_POST['toid'])) {
	while(list($k,$v) = each($_POST['toretl'])) {
		$_POST[$k] = intval($v);
	}


	$q = mysql_query('SELECT * FROM oldbk.users WHERE id = '.intval($_POST['toid']));
	if (mysql_num_rows($q) > 0) {
		$usr = mysql_fetch_assoc($q);
		$q = mysql_query('SELECT * FROM oldbk.inventory WHERE id IN ('.implode(",",$_POST['toretl']).') and owner = 446');
		while($i = mysql_fetch_assoc($q)) {
			$q2 = mysql_query('UPDATE oldbk.inventory SET owner = '.intval($_POST['toid']).' WHERE id = '.$i['id']);
			if ($q2) {
				echo 'Успешно возвращена вещь из ломбарда <b>'.$i['name'].'</b> <b>'.$i['id'].'</b> к персонажу: <b>'.$usr['login'].'</b><br>';
			} else {
				echo 'Ошибка возврата вещи из ломбарда <b>'.$i['id'].'</b> к персонажу <b>'.$usr['login'].'</b><br>';
			}
		}
	} else {
		echo 'Персонаж для возврата не найден :(';
	}
}

if (isset($_POST['toret']) && is_array($_POST['toret'])) {
	while(list($k,$v) = each($_POST['toret'])) {
		$_POST[$k] = intval($v);
	}

	$q = mysql_query('SELECT * FROM oldbk.inventory_aftr_del WHERE did IN ('.implode(",",$_POST['toret']).') order BY deltime DESC');
	$flds = array();
	for ($i = 0;;$i++) {
		$n = mysql_field_name($q,$i);
		if (!$n || $n == "") break;
		$flds[] = $n;
	}
	unset($flds[0]);
	unset($flds[1]);

	$qs = 'INSERT INTO oldbk.inventory (';
	$fl = "";
	$pos = 0;
	while(list($k,$v) = each($flds)) {
		$fl .= "`".$v."`,";
		if ($v == "art_param") $pos = $k;
	}
	$fl = substr($fl,0,strlen($fl)-1);
	$qs .= $fl;
	$qs .= ') VALUES (';

	$qq = "";
	while($i = mysql_fetch_assoc($q)) {
		$i2 = 0;
		$qq = $qs;
		while(list($k,$v) = each($i)) {
			$i2++;
			if ($i2 < 3) continue;
			if ($pos > 0 && $pos == $i2-1) {
				if (strlen($v)) {
					$qq .= '"'.mysql_escape_string($v).'",';
				} else {
					$qq .= 'NULL,';
				}
			} else {
				$qq .= '"'.mysql_escape_string($v).'",';
			}
		}
		$qq = substr($qq,0,strlen($qq)-1);
		$qq .= ')';
		//echo $qq;
		$s = mysql_query($qq);
		if ($s) {
			echo 'Успешно возвращена вещь: <b>'.$i['name'].'</b> <b>'.$i['id'].'</b><br>';
			
								$telo = mysql_fetch_assoc(mysql_query('SELECT * FROM oldbk.users WHERE id = '.$i['owner']));
						     		//add to new delo
								$rec['owner']=$telo['id']; 
								$rec['owner_login']=$telo['login'];
								$rec['owner_balans_do']=$telo['money'];
								$rec['owner_balans_posle']=$telo['money'];					
								$rec['target']=0;
								$rec['target_login']='Администрация';
								$rec['type']=6062;
								$rec['item_id']="cap".$i['id'];
								$rec['item_name']=$i['name'];
								$rec['item_count']=1;
								$rec['item_type']=$i['type'];
								$rec['item_cost']=$i['cost'];
								$rec['item_ecost']=$i['ecost'];
								$rec['item_dur']=$i['duration'];
								$rec['item_maxdur']=$i['maxdur'];
								$rec['item_ups']=$i['ups'];
								$rec['item_unic']=$i['unik'];
								$rec['item_incmagic']=$i['includemagicname'];
								$rec['item_incmagic_count']=$i['includemagicuses'];
								$rec['item_sowner']=$i['sowner'];
								$rec['item_proto']=$i['prototype'];
								$rec['item_mfinfo']=$i['mfinfo'];
								$rec['item_level']=$i['nlevel'];
								add_to_new_delo($rec); 
			//удалить 
			
			mysql_query("DELETE FROM oldbk.inventory_aftr_del WHERE id='{$i['id']}' ");
			
			
			
			
		} else {
			echo 'Ошибка возврата вещи: <b>'.mysql_error().'</b><br>';
		}
	}

}


if (isset($_POST['retitems'])) {
	$i = explode("\n",$_POST['retitems']);
	$ids = array();
	while(list($k,$v) = each($i)) {
		$v = trim($v);
		$v = str_replace("cap","",$v);
		$v = str_replace("ava","",$v);
		$ids[] = intval($v);
	}

	if (count($ids)) {
		$q = mysql_query('SELECT * FROM oldbk.inventory_aftr_del WHERE id IN ('.implode(",",$ids).') order BY deltime DESC');
		if (mysql_num_rows($q) > 0) {
			echo '<form method="POST"><table><tr><td></td><td><b>Time</b></td><td><b>ID</b></td><td><b>Name</b></td><td><b>Cost</b></td><td><b>Ecost</b></td><td><b>Owner</b></td></tr>';
			while($i = mysql_fetch_assoc($q)) {
				$uu = mysql_fetch_assoc(mysql_query('SELECT * FROM users WHERE id = '.$i['owner']));
				$uu['hidden'] = 0;
				echo '<tr><td><input type="checkbox" name="toret[]" value="'.$i['did'].'"></td><td>'.date("d/m/Y H:i:s",$i['deltime']).'</td><td>'.$i['id'].'</td><td>'.$i['name'].'</td><td>'.$i['cost'].'</td><td>'.$i['ecost'].'</td><td>'.nick_align_klan($uu).'</td></tr>';
			}
			echo '</table><input type="submit" value="Вернуть"></form>';
		} else {
			$q = mysql_query('SELECT * FROM oldbk.inventory WHERE id IN ('.implode(",",$ids).') and owner = 446 order BY id DESC');
			if (mysql_num_rows($q) > 0) {
				echo '<form method="POST">
				<br><b>ID !</b> персонажа кому вернуть вещь из ломбарда: <input type="text" name="toid"><br>
				<table><tr><td></td><td><b>ID</b></td><td><b>Name</b></td><td><b>Cost</b></td><td><b>Ecost</b></td></tr>';
				while($i = mysql_fetch_assoc($q)) {
					echo '<tr><td><input type="checkbox" name="toretl[]" value="'.$i['id'].'"></td><td>'.$i['id'].'</td><td>'.$i['name'].'</td><td>'.$i['cost'].'</td><td>'.$i['ecost'].'</td></tr>';
				}
				echo '</table><input type="submit" value="Вернуть"></form>';
			} else {
				echo 'Ничего не найдено :(<br>';
			}
		}
	}
}
?>
</body>
</html>