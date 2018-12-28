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

session_write_close();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	while(list($k,$v) = each($_POST['toreset'])) {
		resetuser($v);
	}
	die('</body></html>');
}



function resetuser($id) {
	$rr = 0;
	$user_t = mysql_fetch_assoc(mysql_query('SELECT * FROM users WHERE id = '.$id));
	$res = checkuser($user_t,$rr);
	if ($res == "1") {
		mysql_query('UPDATE users SET rep_bonus = "'.$rr.'" WHERE id = '.$id);
		echo 'Персонаж <b>'.$user_t['login'].'</b> исправлен<br>';
	}
}

function checkuser($user,&$rr) {
	$expstart = 1;
	$q = mysql_query('SELECT * FROM oldbk.effects WHERE (type = 9100 or type = 20006) and owner = '.$user['id']);
	while($e = mysql_fetch_assoc($q)) {
		if ($e['type'] == 9100) {
			$mb=explode(":",$e['add_info']);
			$mb=$mb[1];

			$expstart += $mb;
		}		
		if ($e['type'] == 20006) {
			if ($e['add_info']!='cooldown') {
				$eff_tmp=explode(":",$e['add_info']);
				$expstart += $eff_tmp[0];
			}
		}
		
	}

	if ($user['weap'] > 0) {
		$q = mysql_query('SELECT * FROM inventory WHERE id = '.$user['weap'].' and owner = '.$user['id'].' and dressed = 1 and prototype IN (55510352,410027,410028)');
		if (mysql_num_rows($q) > 0) {
			$q = mysql_fetch_assoc($q);
			if ($q['prototype'] == 55510352) {
				$expstart += 0.2;
			}
			if ($q['prototype'] == 410027) {
				$expstart += 0.1;
			}
			if ($q['prototype'] == 410028) {
				$expstart += 0.2;
			}
		}
	}

	if ($user['shit'] > 0) {
		$q = mysql_query('SELECT * FROM inventory WHERE id = '.$user['shit'].' and owner = '.$user['id'].' and dressed = 1 and prototype IN (55510352,410027,410028)');
		if (mysql_num_rows($q) > 0) {
			$q = mysql_fetch_assoc($q);
			if ($q['prototype'] == 55510352) {
				$expstart += 0.2;
			}
			if ($q['prototype'] == 410027) {
				$expstart += 0.1;
			}
			if ($q['prototype'] == 410028) {
				$expstart += 0.2;
			}
		}
	}


	if (round($user['rep_bonus'],2) == round($expstart,2)) return 0;
	$rr = $expstart;
	return 1;
}

if (isset($_GET['start'])) {
	$q = mysql_query('
		SELECT * FROM oldbk.users WHERE (id > 10 AND bot = 0 AND id_city = 0 AND klan != "Adminion" AND klan != "pal" AND klan != "radminion")
		LIMIT '.intval($_GET['start']).',100') or die(mysql_error());
	if (mysql_num_rows($q) == 0) die("DIE");
	$buf = "";
	$lastid = 0;
	while($u = mysql_fetch_assoc($q)) {
		$lastid = $u['id'];
		$rr = 0;
		$res = checkuser($u,$rr);
		if ($res != 0) {
			if ($res == "1") {
				$res = "Invalid rep_bonus ".$u['rep_bonus'].':'.$rr;
			}
			if ($u['id_city'] == 0) $city = " capital";
			if ($u['id_city'] == 1) $city = " avalon";
			$lab = "";
			if ($u['lab'] > 0) $lab = "<b>lab</b>";
			//$buf .= $u['id'].",";
			$buf .= '<input type="checkbox" checked name="toreset[]" value="'.$u['id'].'"> <b>'.htmlspecialchars($u['login'],ENT_QUOTES)."</b> ".$res." ".$city." ".$lab."<BR>";
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
		url:'ptest3.php?start='+(lmta)}).done(function(data) {
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