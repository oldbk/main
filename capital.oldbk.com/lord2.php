<?php
	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	function MyDie() {
		Redirect("lord2.php");
	}


	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");

	include('connect.php');
	include('functions.php');

	if ($user['room'] != 90) Redirect("main.php");
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { Redirect("fbattle.php"); }

	if (time() < mktime(0,0,0,11,10,2014)) Redirect("lord.php");

	$cango = true;
	$q = mysql_query('SELECT * FROM `lord_var` WHERE `owner` = '.$user['id'].' AND var = "cango" AND val = '.mktime(0,0,0)) or die();
	if (mysql_num_rows($q) > 0) {
		$cango = false;
	}


	$err = "";


	if (isset($_GET['lord']) && isset($_GET['cup']) && $cango && $user['hp'] > 0) {
		if ($user['level'] >= 6) {
			// ставим кубок 300100x
			// 1 - екры
			// 2ая - репа
			// 3ая чаша - дилерка
			// 4ая - креды
			$_GET['cup'] = intval($_GET['cup']);
			if ($_GET['cup'] < 1 || $_GET['cup'] > 4) die();
			$cupid = 3001000 + $_GET['cup'];
			$batid = 600+$_GET['cup'];
	
			$mycup = mysql_fetch_assoc(mysql_query('SELECT *, IF (dategoden = 0, 2052691200, dategoden) as dategoden2 FROM oldbk.inventory WHERE owner = '.$user['id'].' AND prototype = '.$cupid.' AND (sowner = 0 or sowner = '.$user['id'].') order by dategoden2 LIMIT 1'));
	                                                                               
	/*
			$lord_bots = array(
				6 => array(
					"sum_minu" => 50,
					"sum_maxu" => 100,
					"sum_mfkrit" => 100,
					"sum_mfakrit" => 5000,
					"sum_mfuvorot" => 100,
					"sum_mfauvorot" => 300,
					"sum_bron1" => 50,
					"sum_bron2" => 50,
					"sum_bron3" => 50,
					"sum_bron4" => 50,
				),
				7 => array(
					"sum_minu" => 75,
					"sum_maxu" => 125,
					"sum_mfkrit" => 200,
					"sum_mfakrit" => 5000,
					"sum_mfuvorot" => 200,
					"sum_mfauvorot" => 600,
					"sum_bron1" => 100,
					"sum_bron2" => 100,
					"sum_bron3" => 100,
					"sum_bron4" => 100,
				),
				8 => array(
					"sum_minu" => 100,
					"sum_maxu" => 150,
					"sum_mfkrit" => 300,
					"sum_mfakrit" => 5000,
					"sum_mfuvorot" => 300,
					"sum_mfauvorot" => 800,
					"sum_bron1" => 150,
					"sum_bron2" => 150,
					"sum_bron3" => 150,
					"sum_bron4" => 150,
				),
				9 => array(
					"sum_minu" => 125,
					"sum_maxu" => 175,
					"sum_mfkrit" => 400,
					"sum_mfakrit" => 5000,
					"sum_mfuvorot" => 400,
					"sum_mfauvorot" => 1200,
					"sum_bron1" => 200,
					"sum_bron2" => 200,
					"sum_bron3" => 200,
					"sum_bron4" => 200,
				),
				10 => array(          
					"sum_minu" => 150,
					"sum_maxu" => 200,
					"sum_mfkrit" => 500,
					"sum_mfakrit" => 5000,
					"sum_mfuvorot" => 500,
					"sum_mfauvorot" => 1700,
					"sum_bron1" => 250,
					"sum_bron2" => 250,
					"sum_bron3" => 250,
					"sum_bron4" => 250,
				),
				11 => array(
					"sum_minu" => 175,
					"sum_maxu" => 225,
					"sum_mfkrit" => 600,
					"sum_mfakrit" => 5000,
					"sum_mfuvorot" => 600,
					"sum_mfauvorot" => 2700,
					"sum_bron1" => 350,
					"sum_bron2" => 350,
					"sum_bron3" => 350,
					"sum_bron4" => 350,
				),
				12 => array(
					"sum_minu" => 200,
					"sum_maxu" => 250,
					"sum_mfkrit" => 700,
					"sum_mfakrit" => 5000,
					"sum_mfuvorot" => 700,
					"sum_mfauvorot" => 3200,
					"sum_bron1" => 400,
					"sum_bron2" => 400,
					"sum_bron3" => 400,
					"sum_bron4" => 400,
				),
				13 => array(
					"sum_minu" => 225,
					"sum_maxu" => 275,
					"sum_mfkrit" => 800,
					"sum_mfakrit" => 5000,
					"sum_mfuvorot" => 800,
					"sum_mfauvorot" => 3600,
					"sum_bron1" => 450,
					"sum_bron2" => 450,
					"sum_bron3" => 450,
					"sum_bron4" => 450,
				),
				14 => array(
					"sum_minu" => 250,
					"sum_maxu" => 300,
					"sum_mfkrit" => 900,
					"sum_mfakrit" => 5000,
					"sum_mfuvorot" => 900,
					"sum_mfauvorot" => 4000,
					"sum_bron1" => 500,
					"sum_bron2" => 500,
					"sum_bron3" => 500,
					"sum_bron4" => 500,
				),
			);
	*/

$lord_bots = array(
6 => array(
"sum_minu" => 50,
"sum_maxu" => 100,
"sum_mfkrit" => 100,
"sum_mfakrit" => 5000,
"sum_mfuvorot" => 100,
"sum_mfauvorot" => 300,
"sum_bron1" => 50,
"sum_bron2" => 50,
"sum_bron3" => 50,
"sum_bron4" => 50,
),
7 => array(
"sum_minu" => 75,
"sum_maxu" => 125,
"sum_mfkrit" => 200,
"sum_mfakrit" => 5000,
"sum_mfuvorot" => 200,
"sum_mfauvorot" => 700,
"sum_bron1" => 50,
"sum_bron2" => 50,
"sum_bron3" => 50,
"sum_bron4" => 50,
),
8 => array(
"sum_minu" => 150,
"sum_maxu" => 200,
"sum_mfkrit" => 300,
"sum_mfakrit" => 5000,
"sum_mfuvorot" => 300,
"sum_mfauvorot" => 1200,
"sum_bron1" => 75,
"sum_bron2" => 75,
"sum_bron3" => 75,
"sum_bron4" => 75,
),
9 => array(
"sum_minu" => 200,
"sum_maxu" => 250,
"sum_mfkrit" => 400,
"sum_mfakrit" => 5000,
"sum_mfuvorot" => 400,
"sum_mfauvorot" => 1700,
"sum_bron1" => 75,
"sum_bron2" => 75,
"sum_bron3" => 75,
"sum_bron4" => 75,
),
10 => array(
"sum_minu" => 300,
"sum_maxu" => 350,
"sum_mfkrit" => 500,
"sum_mfakrit" => 5000,
"sum_mfuvorot" => 500,
"sum_mfauvorot" => 2200,
"sum_bron1" => 100,
"sum_bron2" => 100,
"sum_bron3" => 100,
"sum_bron4" => 100,
),
11 => array(
"sum_minu" => 450,
"sum_maxu" => 500,
"sum_mfkrit" => 600,
"sum_mfakrit" => 5000,
"sum_mfuvorot" => 600,
"sum_mfauvorot" => 2700,
"sum_bron1" => 350,
"sum_bron2" => 350,
"sum_bron3" => 350,
"sum_bron4" => 350,
),
12 => array(
"sum_minu" => 400,
"sum_maxu" => 450,
"sum_mfkrit" => 700,
"sum_mfakrit" => 5000,
"sum_mfuvorot" => 700,
"sum_mfauvorot" => 4200,
"sum_bron1" => 375,
"sum_bron2" => 375,
"sum_bron3" => 375,
"sum_bron4" => 375,
),
13 => array(
"sum_minu" => 650,
"sum_maxu" => 700,
"sum_mfkrit" => 800,
"sum_mfakrit" => 6000,
"sum_mfuvorot" => 800,
"sum_mfauvorot" => 5200,
"sum_bron1" => 400,
"sum_bron2" => 400,
"sum_bron3" => 400,
"sum_bron4" => 400,
),
14 => array(
"sum_minu" => 750,
"sum_maxu" => 800,
"sum_mfkrit" => 900,
"sum_mfakrit" => 6000,
"sum_mfuvorot" => 900,
"sum_mfauvorot" => 6200,
"sum_bron1" => 425,
"sum_bron2" => 425,
"sum_bron3" => 425,
"sum_bron4" => 425,
),
);	
			$lbot = array();
	
			if (isset($lord_bots[$user['level']])) {
				$lbot = $lord_bots[$user['level']];
			} else {
				$lbot = max($lord_bots);
			}
	

			if ($mycup) {
				// чаша найдена
				// стартуем бой
				$sql = 'UPDATE oldbk.map_var SET val = val + 1 WHERE owner = '.$user['id'].' AND var = "q32s43"';
				mysql_query_100($sql);

				//логируем прогресс
				mysql_query("INSERT INTO oldbk.users_progress set owner='{$user['id']}', alordcount=1 ON DUPLICATE KEY UPDATE alordcount=alordcount+1");

				mysql_query('UPDATE oldbk.users SET battle = 1 WHERE id = '.$user['id'].' and battle = 0 LIMIT 1');
				if (mysql_affected_rows() > 0) {
					$q = mysql_query('START TRANSACTION') or die();
		
					// для начала создаём клонов
					$botids = "";
					$bothist = "";    	
			
					$BOT = mysql_query_cache('SELECT * FROM oldbk.`users` WHERE `id` = 12',false,24*3600);
					if ($BOT === FALSE || !count($BOT)) die();
					$BOT = $BOT[0];
			
					$BOT['level'] = $user['level'];
		
					$q = mysql_query('INSERT INTO oldbk.`users_clons` SET 
							`login` = "'.$BOT['login'].'",
							`sex` = "'.$BOT['sex'].'",
							`level` = "'.$BOT['level'].'",
							`align` = "'.$BOT['align'].'",
							`klan` = "'.$BOT['klan'].'",
							`sila` = "10",
							`lovk` = "10",
							`inta` = "10",
							`vinos` = "10",
							`intel` = "10",
							`mudra` = "10",
							`duh` = "'.$BOT['duh'].'",
							`bojes` = "'.$BOT['bojes'].'",
							`noj` = "'.((6+($user['level']))*2).'",
							`mec` = "'.((6+($user['level']))*2).'",
							`topor` = "'.((6+($user['level']))*2).'",
							`dubina` = "'.((6+($user['level']))*2).'",
							`maxhp` = "'.$BOT['maxhp'].'",
							`hp` = "'.$BOT['maxhp'].'",
							`maxmana` = "'.$BOT['maxmana'].'",
							`mana` = "'.$BOT['mana'].'",
							`sergi` = "'.$BOT['sergi'].'",
							`kulon` = "'.$BOT['kulon'].'",
							`perchi` = "'.$BOT['perchi'].'",
							`weap` = "'.$BOT['weap'].'",
							`bron` = "'.$BOT['bron'].'",
							`r1` = "'.$BOT['r1'].'",
							`r2` = "'.$BOT['r2'].'",
							`r3` = "'.$BOT['r3'].'",
							`helm` = "'.$BOT['helm'].'",
							`shit` = "'.$BOT['shit'].'",
							`boots` = "'.$BOT['boots'].'",
							`nakidka` = "'.$BOT['nakidka'].'",
							`rubashka` = "'.$BOT['rubashka'].'",													
							`shadow` = "'.$BOT['shadow'].'",
							`battle` = 0,
							`bot` = 1,
							`id_user` = "'.$BOT['id'].'",
							`at_cost` = "1000",
							`kulak1` = 0,
							`sum_minu` = "'.$lbot['sum_minu'].'",
							`sum_maxu` = "'.$lbot['sum_maxu'].'",
							`sum_mfkrit` = "'.$lbot['sum_mfkrit'].'",
							`sum_mfakrit` = "'.$lbot['sum_mfakrit'].'",
							`sum_mfuvorot` = "'.$lbot['sum_mfuvorot'].'",
							`sum_mfauvorot` = "'.$lbot['sum_mfauvorot'].'",
							`sum_bron1` = "'.$lbot['sum_bron1'].'",
							`sum_bron2` = "'.$lbot['sum_bron2'].'",
							`sum_bron3` = "'.$lbot['sum_bron3'].'",
							`sum_bron4` = "'.$lbot['sum_bron4'].'",
							`ups` = "5",
							`injury_possible` = 0, 
							`battle_t` = 0, 
							`bot_online` = 0, 
							`bot_room` = 0
					');
					if ($q === FALSE) die();
		
					$botids = mysql_insert_id();
					$bothist = BNewHist($BOT);
		
					// клонов создали, делаем бой
					$q = mysql_query('UPDATE oldbk.`users` SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` = '.$user['id']);
					if ($q === FALSE) die();
										
					$q = mysql_query('INSERT INTO oldbk.`battle` (`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`,`blood`,`CHAOS`)
						VALUES
						(
							"Бой с Лордом Разрушителем",
							".",
							"5",
							"'.$batid.'",
							"0",
							"'.$user['id'].'",
							"'.$botids.'",
							"'.time().'",
							"'.time().'",
							3,
							"'.mysql_real_escape_string(BNewHist($user)).'",
							"'.mysql_real_escape_string($bothist).'",
							"1","0"
						)
					');
					if ($q === FALSE) die();
				
					$id = mysql_insert_id();
				
					// теперь обновляем себя и противника что мы в бою
					$q = mysql_query('UPDATE oldbk.`users_clons` SET `battle` = '.$id.', `battle_t` = 2  WHERE `id` IN ('.$botids.')');
					if ($q === FALSE) die();
					$q = mysql_query('UPDATE oldbk.`users` SET `battle` = '.$id.', `battle_t` = 1  WHERE `id`= '.$user['id']);
					if ($q === FALSE) die();
	
					addlog($id,"!:S:".time().":".BNewHist($user).":".$bothist."\n");
		
					mysql_query('DELETE FROM oldbk.inventory WHERE id = '.$mycup['id']) or die();
			
					mysql_query('INSERT INTO oldbk.`lord_var` (`owner`,`var`,`val`) 
							VALUES(
								'.$user['id'].',
							"cango",
							'.(mktime(0,0,0)).')
							ON DUPLICATE KEY UPDATE
								`val` = '.(mktime(0,0,0))

					) or die();
	
					// стартуем бой
					$q = mysql_query('COMMIT') or die();
				}
				echo '<script>location.href="fbattle.php";</script>';
				die();
			} else {
				$err = "Подходящая Чаша не найдена в Вашем инвентаре!";
			}
		} else {
			$err = "Удостоиться чести сразиться с Лордом Разрушителем может только персонаж выше 5го уровня. Подрасти немного и приходи снова.";
		}
	}

	if (isset($_GET['exit'])) {
		mysql_query("UPDATE `users` SET `users`.`room` = '200' WHERE  `users`.`id`  = '{$user[id]}' ;");
		header('location: restal.php');
		die();
	}



	if (isset($_GET['history'])) {
		if (isset($_GET['p'])) $_GET['p'] = intval($_GET['p']);
		if ($_GET['p'] < 0) $_GET['p'] = 0;

		$res = array();
		$res['users'] = array();
		mysql_query('SET NAMES utf8');
		$q = mysql_query('SELECT users.login,align,klan,points FROM lord_donate LEFT JOIN users ON lord_donate.owner = users.id WHERE users.id is not null and users.align != 4 and block = 0 ORDER BY points DESC LIMIT '.($_GET['p']*30).',30');
		$i = 1;	

		while($u = mysql_fetch_assoc($q)) {
			$res['users'][$_GET['p']*30+$i] = $u;
			$i++;
		}

		$q = mysql_query('SELECT count(*) as cccount FROM lord_donate LEFT JOIN users ON lord_donate.owner = users.id WHERE users.id is not null and users.align != 4 and block = 0');
		$q = mysql_fetch_assoc($q);
		$res['all'] = ceil($q['cccount'] / 30);
		echo json_encode($res);
		die();
	}



	$mllord = array(
		0 => array(
			0 => "Приветствую тебя, Воин. Я - Страж замка Лорда Разрушителя. Что привело тебя сюда?",
			"d1" => "Расскажи мне о Лорде и этом Замке.",
			"33333" => "Я пожалуй пойду.",
		),
		1 => array(
			"0"  => "Лорд Разрушитель - великий Ангел, издавна наблюдающий за этим Миром. Ходят слухи, что когда-то он разрушил Государственный магазин в столице, а в другом городе - комиссионный магазин. Лорда стоит опасаться, однако, недавно он выстроил себе здесь Замок и раз в сутки позволяет наиболее смелым людям пройти к Нему в Тронный зал. Тот, кто придет к нему с пустыми руками - не получит ничего. Но тот, кто принесет хороший дар, удостоится чести сразиться с Лордом и получить награду за свой героизм..",
			"d2" => "Что нужно сделать, чтоб пройти к Лорду Разрушителю?",
			"d3" => "Говорят, что есть Чаши, которые надо принести Лорду. Расскажи мне о них?",
			"d0"  => "Спасибо за разъяснения.",
		),
		2 => array(
			"0"  => "Пройти к Лорду может персонаж выше 5го уровня и только раз в сутки, если ты не имеешь специального пропуска. Пропуска были лишь однажды даны тем, кто при постройке замка принес больше всего пожертвований. К Лорду Разрушителю не ходят с пустыми руками. Ты можешь принести ему одну из 4х Чаш - Чаша Триумфа, Чаша Крови, Чаша Алчности или Чаша Смерти. Поставив Чашу на Его алтарь ты удостоишься чести сразиться с Лордом.",
			"d1"  => "Спасибо за разъяснения.",
		),
		3 => array(
			"0" => "Ты можешь принести Лорду одну из 4х Чаш:<br>- Чаша Триумфа (ее можно купить в Березке за 2 екр.)<br>- Чаша Крови (ее можно купить в Храмовой Лавке за 500 реп.)<br>- Чаша Алчности (ее можно купить в Березке за 1 екр.)<br>- Чаша Смерти (ее можно купить в Гос.магазине за 5 кред.)<br>Поставив чашу на Алтарь ты удостоишься чести сразиться с Лордом. Исход боя предрешен - ты в любом случае погибнешь, но после смерти ты получишь награду за свой героизм. <br>За этот бой ты получишь двойной опыт, запишешь себе 20 великих побед в личный список и сможешь прокачать свои руны с коэффициентом, который зависит от ценности твоего подарка:<br>за Чашу Триумфа ты получишь 170% РК<br>за Чашу Крови ты получишь 150% РК<br>за Чашу Алчности ты получишь 125% РК<br>за Чашу Смерти ты получишь 100% РК<br>Желаю удачи!",
			"d1"  => "Спасибо за разъяснения.",		
		),
	);

	if (isset($_GET['qaction']) && strlen($_GET['qaction']) || isset($_GET['quest'])) {
		if (!isset($_GET['qaction'])) $_GET['qaction'] = "d0";

		if ($_GET['qaction'] == "qlord") {
			Redirect('lord2.php?lord=1');
		}


		$qa = $_GET['qaction'];
		$num = -1;
		if (!is_numeric($qa[0])) {
			$num = intval(substr($qa,1));
		}
		if ($qa[0] == "d" && isset($mllord[$num])) {
			$mldiag = $mllord[$num];
		} else {
			unset($_GET['quest']);
			unset($_GET['qaction']);
		}

	}


	if (isset($_GET['lord']) && !$cango) {
		$_GET['quest'] = 1;
		$mldiag = array(
			0 => "Ты сегодня уже был удостоен аудиенции у Лорда. Подожди до полуночи, прежде чем снова надоедать Ему.",
			"33333"  => "Спасибо за разъяснения.",
		);
	}


?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="windows-1251">
    <title></title>
    <link type="text/css" rel="stylesheet" href="http://i.oldbk.com/i/main.css" />
    <link type="text/css" rel="stylesheet" href="http://i.oldbk.com/i/lord/lordstyles7.css" />
    <link rel="stylesheet" href="/i/btn.css" type="text/css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="/i/globaljs.js"></script>
</head>
<style>
.n_area-guard:hover {
    background-image: url("http://i.oldbk.com/i/lord/guardian_hover.png");
}
.n_area-guard {
    position: absolute;
    background-image: url("http://i.oldbk.com/i/lord/guardian.png");
    background-repeat: no-repeat;
    height: 280px;
    width: 125px;
    left: 860px;
    top: 180px;
    cursor: pointer;
    z-index: 3;
}

.n_area-cup1:hover {
    background-image: url("http://i.oldbk.com/i/lord/cub5_hover.png");
}
.n_area-cup1 {
    position: absolute;
    background-image: url("http://i.oldbk.com/i/lord/cub5.png");
    background-repeat: no-repeat;
    height: 55px;
    width: 47px;
    left: 728px;
    top: 336px;
    cursor: pointer;
    z-index: 3;
}

.n_area-cup2:hover {
    background-image: url("http://i.oldbk.com/i/lord/cub2_hover.png");
}
.n_area-cup2 {
    position: absolute;
    background-image: url("http://i.oldbk.com/i/lord/cub2.png");
    background-repeat: no-repeat;
    height: 55px;
    width: 47px;
    left: 631px;
    top: 336px;
    cursor: pointer;
    z-index: 3;
}

.n_area-cup3:hover {
    background-image: url("http://i.oldbk.com/i/lord/cub3_hover.png");
}
.n_area-cup3 {
    position: absolute;
    background-image: url("http://i.oldbk.com/i/lord/cub3.png");
    background-repeat: no-repeat;
    height: 55px;
    width: 47px;
    left: 684px;
    top: 336px;
    cursor: pointer;
    z-index: 3;
}

.n_area-cup4:hover {
    background-image: url("http://i.oldbk.com/i/lord/cub4_hover.png");
}
.n_area-cup4 {
    position: absolute;
    background-image: url("http://i.oldbk.com/i/lord/cub4.png");
    background-repeat: no-repeat;
    height: 55px;
    width: 47px;
    left: 576px;
    top: 336px;
    cursor: pointer;
    z-index: 3;
}


</style>
<body>    
<?php
	if (isset($_GET['lord']) && $cango) {
?>
    <div class="btn-control">
        <INPUT class="button-mid btn" TYPE=button value="Вернуться" onClick="location.href='lord2.php';" style="z-index:3; position: absolute; right: 20px; top: 20px;">
    </div>
    <div id="n_lord_castle" style="background-image: url('http://i.oldbk.com/i/lord/bg22_80.jpg');">
        <img src="http://i.oldbk.com/i/lord/t.gif" class="tlong" width="1350" height="510" alt="" usemap="#n_map"/>
        <ul>
            <li class="n_cup1-hover" style="display: none;"></li>
            <li class="n_cup2-hover" style="display: none;"></li>
            <li class="n_cup3-hover" style="display: none;"></li>
            <li class="n_cup4-hover" style="display: none;"></li>
        </ul>
        <a href="?lord=1&cup=1" class="n_hover"><div class="n_area-cup1"></div></a>
        <a href="?lord=1&cup=2" class="n_hover"><div class="n_area-cup2"></div></a>
        <a href="?lord=1&cup=3" class="n_hover"><div class="n_area-cup3"></div></a>
        <a href="?lord=1&cup=4" class="n_hover"><div class="n_area-cup4"></div></a>
    </div>
	<?php
		if (strlen($err)) echo '<script>alert("'.$err.'")</script>';
	?>
<?
	} else {

?>
    <div class="btn-control">
        <INPUT class="button-mid btn" TYPE=button value="Вернуться" onClick="location.href='lord2.php?exit=1';" style="z-index:3; position: absolute; right: 20px; top: 20px;">
    </div>
    <div id="n_lord_castle">
	<?

		$mlquest = "500/200";
		if (isset($_GET['quest']) || isset($_GET['qaction'])) require_once('mlquestlord.php');
	?>
        <img src="http://i.oldbk.com/i/lord/t.gif" class="tlong" width="1350" height="510" alt="" usemap="#n_map"/>
        <ul>
            <li class="n_book-hover" style="display: none;"></li>
            <li class="n_gate-hover" style="display: none;"></li>
            <li class="n_guard-hover" style="display: none;"></li>
        </ul>
        <a href="?quest=1" class="n_hover"><div class="n_area-guard"></div></a>
    </div>
    <map name="n_map" id="n_map">
        <area class="n_area-book" shape="poly" href="#" coords="310,345,368,329,397,365,344,393" />
        <area class="n_area-gate" shape="poly" href="?lord=1" coords="605,348,605,208,631,173,679,153,725,169,753,208,751,351" />
    </map>
<div id="n_book_popup">
    <div id="n_book_wrapper">
        <div id="n_book_close"></div>
        <div id="n_book_body">
            <ul id="n_left" class="n_left">
            </ul>
            <ul id="n_right" class="n_right">
            </ul>
        </div>
        <div id="n_book_pagination">
            <ul id="n_pagination_body">
                <li id="n_p1" class="n_first"></li>
                <li id="n_p2" class="n_second"></li>
                <li id="n_p3" class="n_third"></li>
                <li id="n_p4" class="n_fourth"></li>
                <li id="n_p5" class="n_fifth"></li>
                <li id="n_p6" class="n_sixth"></li>
                <li id="n_p7" class="n_seventh"></li>
                <li id="n_p8" class="n_eighth"></li>
            </ul>
        </div>
	<div id="n_book_bottom" style="font-size:14px;">
	           Вы пожертвовали: <?php $s = mysql_fetch_assoc(mysql_query('SELECT * FROM lord_donate WHERE owner = '.$user['id'])); if (!$s['points']) $s['points'] = 0; echo $s['points']; ?>
        </div>
    </div>
</div>
<div id="n_page-dark"></div>
</body>
<script>
	var currentpage = 1;
	var allpages = 0;

	function ShowPersPages() {
		for (i = 1; i < 9; i++) {
			$("#n_p"+i).html("");
			$("#n_p"+i).unbind('click');
			$("#n_p"+i).removeClass('active');
		}
		if (allpages <= 8) {
			for (i = 1; i < allpages+1; i++) {
				if (i == currentpage) $("#n_p"+i).addClass("active");
				$("#n_p"+i).html(i);
				$("#n_p"+i).bind( "click", {msg:i},function(event) {ChangePersPage(event.data.msg-1)});
			}
		} else {
			if (currentpage <= 5) {
				for (i = 1; i < 7; i++) {
					if (i == currentpage) $("#n_p"+i).addClass("active");
					$("#n_p"+i).html(i);
					$("#n_p"+i).bind( "click", {msg:i},function(event) {ChangePersPage(event.data.msg-1)});
				}
				$("#n_p7").html("...");
				$("#n_p8").html(allpages);
				$("#n_p8").bind( "click", function() {
					ChangePersPage(allpages-1);
				});
			} else if (currentpage+4 >= allpages) {
				$("#n_p1").html("1");
				$("#n_p1").bind( "click", function() {
					ChangePersPage(0);
				});
				$("#n_p2").html("...");


				for (i = 8; i > 2; i--) {
					$("#n_p"+i).html(allpages+i-8);
					$("#n_p"+i).bind( "click", {msg:allpages+i-8},function(event) {ChangePersPage(event.data.msg-1)});
					if (allpages+i-8 == currentpage) $("#n_p"+i).addClass("active");
				}
			} else {
				$("#n_p1").html("1");
				$("#n_p1").bind( "click", function() {
					ChangePersPage(0);
				});
				$("#n_p2").html("...");

				$("#n_p3").html(currentpage-1);
				$("#n_p3").bind( "click", {msg:currentpage-1},function(event) {ChangePersPage(event.data.msg-1)});

				$("#n_p4").html(currentpage);
				$("#n_p4").bind( "click", {msg:currentpage},function(event) {ChangePersPage(event.data.msg-1)});
				$("#n_p4").addClass("active");

				$("#n_p5").html(currentpage+1);
				$("#n_p5").bind( "click", {msg:currentpage+1},function(event) {ChangePersPage(event.data.msg-1)});
				$("#n_p6").html(currentpage+2);
				$("#n_p6").bind( "click", {msg:currentpage+2},function(event) {ChangePersPage(event.data.msg-1)});
				
				$("#n_p7").html("...");
				$("#n_p8").html(allpages);
				$("#n_p8").bind( "click", function() {
					ChangePersPage(allpages-1);
				});

			}
		}		
	}

	function ChangePersPage(i) {
		currentpage = i+1;
		$.getJSON("lord2.php?history=1&p="+i,  
			function(data){
				ShowPersInfo(data);
			}
		);
	}

	function ShowPersInfo(data) {
		allpages = data.all;
		$("#n_left").html("");
		$("#n_right").html("");

		k = 0;
		htmlleft = "";
		htmlright = "";
		$.each(data.users, function(i, item) {
			html = '<li><div class="n_number">'+i+'</div><div class="n_nickname"><img src="http://i.oldbk.com/i/align_'+item.align+'.gif"> ';
			if (item.klan != null && item.klan.length) html += '<img src="http://i.oldbk.com/i/klan/'+item.klan+'.gif">';
			html += item.login+'</div><div class="n_count">'+item.points+'</div></li>';
			if (k < 15) {
				htmlleft += html;
			} else {
				htmlright += html;
			}
			k++;
		});
		$("#n_left").html(htmlleft);
		$("#n_right").html(htmlright);

		ShowPersPages();
	}

    function nBookShow() {
	$.getJSON("lord2.php?history=1",  
		function(data){
			ShowPersInfo(data);
		}
	);

	
        $('#n_page-dark').show();
        $('#n_book_popup').show();
        $('#mlquest').hide();
    }
    function nBookHide() {
        $('#n_page-dark').hide();
        $('#n_book_popup').hide();
    }
    $(document).ready(function () {
        var areaAction = function ($target, action) {
            var areaClass = $.trim($target.attr('class')).replace(/^n_area\-/i, '');
            $('.n_' + areaClass + '-hover')[action]('fast');
        };

        $('map').hover(
                function (e) {
                    areaAction($(e.target), 'fadeIn');
                },
                function (e) {
                    areaAction($(e.target), 'fadeOut');
                }
        );

        $(document.body).on('click', '.n_area-book', function(event){ event.preventDefault(); nBookShow();});
        $(document.body).on('click', '#n_book_popup #n_book_wrapper #n_book_close', function(){nBookHide();});
    });
</script>

<?
}
?>
</html>