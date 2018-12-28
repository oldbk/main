<?php
	function Redirect($path) {
		header("Location: ".$path); 
		die();
	} 

	function MyDie() {
		Redirect("castles_inside.php");
	}

	session_start();

	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) Redirect("index.php");

	require_once('connect.php');
	require_once('functions.php');
	require_once('mlfunctions.php');
	require_once('fsystem.php');
	require_once('clan_kazna.php');

	require_once('castles_config.php');
	require_once('castles_functions.php');

	if (!($user['room'] > 71000 && $user['room'] < 72000)) Redirect("main.php");
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { Redirect("fbattle.php"); }

	$cid = $user['room']-71000;
	$q = mysql_query('SELECT * FROM oldbk.castles WHERE id = '.$cid) or die();
	$c = mysql_fetch_assoc($q) or die("no castle");

	$selfclan = false;
	$polno = "";

	$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=2"));
	//2) Неделя замков - в каждом замке вместе со страницей, которая в нем появляется, появляется еще одна рандомная страница (т.е. не обязательно та, что падает в этом замке, рандомно любая другая) 


	// проверяем может ли он тут находится
	if (!empty($user['klan'])) {
		$selfclan = mysql_query('SELECT * FROM oldbk.clans WHERE short = "'.$user['klan'].'"');
		$selfclan = mysql_fetch_assoc($selfclan);
		if ($selfclan) {
			if ($user['klan'] != $c['clanshort']) {
				$second = CGetSecondClan($selfclan);
				if ($second != $c['clanshort']) $_GET['exit'] = 1;
			}
			$polno = unserialize($selfclan['vozm']);
		} else {
			$_GET['exit'] = 1;
		}
	} else {
		$_GET['exit'] = 1;
	}

	if (isset($_GET['exit'])) {
		mysql_query('UPDATE `users` SET room = '.(70000+$cid).' WHERE id = '.$_SESSION['uid']) or die();		
		Redirect("castles_pre.php");
	}


	$mlman = array(
		0 => array(
			"0"  => "Приветствую, Славный Воин! Пока твой клан владеет этим Замком, Дворецкий всегда к твоим услугам. Что желаешь?",
			"d1" => "Узнать больше про возможности Замка.",
			"d2" => "Узнать больше про защиту Замка.",
			"1" => "Пока ничего не надо, свободен.",
		),
		1 => array(
			"0"  => 'Через 24 часа после завоевания, во дворе Замка появится '.$c['pagenum'].'я страница '.$cbookpages[$c['pagecolor']]["name"].' Магической Книги. Любой из владельцев Замка может подобрать ее и она окажется в клановом арсенале в разделе Страницы.<br><br>Рядом со страницей вы найдете монетку стоимостью 0.1 екр. Если вам повезет, монетка может оказаться стоимостью 1, 5 или 10 екр. Подняв ее, вы пополните екровую казну своего клана.<br><br>В следующий раз страница и монетка появятся через 24 часа после поднятия.<br><br>Если твой клан настолько силен, что овладел не только этим Замком, а еще и несколькими другими, ваша отвага заслуживает наград, список которых можно посмотреть <a href="http://oldbk.com/encicl/?/zamkisvitki.html" target="_blank"><b>здесь</b></a>.',
			"d0"  => "Благодарю за информацию.",
		),
		2 => array(
			"0"  => "С момента завоевания Замка он принадлежит твоему клану в течение следующих 7 дней, после чего Замок снова станет свободным. Любой клан (твой или другой) сможет объявить о нападении и принять участие в турнире за право бороться за замок и владеть им следующую неделю.<br><br>",
			"d0"  => "Благодарю за информацию.",
		),
	);


	if (isset($_GET['qaction']) && strlen($_GET['qaction'])  || isset($_GET['quest'])) {
		if (!isset($_GET['qaction'])) $_GET['qaction'] = "d0";

		$qa = $_GET['qaction'];
		$num = -1;
		if (!is_numeric($qa[0])) {
			$num = intval(substr($qa,1));
		}

		if ($qa[0] == "d" && isset($mlman[$num])) {
			$mldiag = $mlman[$num];
		} elseif ($qa[0] == "c") {
			// complete
			if ($num == 1) {
				if ($get_ivent['stat']==1) { $bonus_string=', и еще одну случайную! ' ; }
			
				$mldiag = array(
					0 => "Ты поднял ".$c['pagenum']."ю страницу ".$cbookpages[$c['pagecolor']]["name"]." ".$bonus_string."  Магической Книги. В следующий раз страница появится через 24 часа.",
					11111 => "Спасибо.",
				);
			}
			if ($num == 2) {
				$mldiag = array(
					0 => "Тебе попалась монетка стоимостью ".$_SESSION['castleslastnom']." екр. Деньги поступили в екровую казну твоего клана. Следующая монетка появится через 24 часа.",
					11111 => "Спасибо.",
				);
			}
			if ($num == 3) {
				$mldiag = array(
					0 => "Вы не можете поднять монетку, т.к. у вашего клана нет казны.",
					11111 => "Понял.",
				);
			}
		} else {
			UnsetQA();
		}
	}


	if (isset($_GET['scroll'])) {
		$q = mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('SELECT * FROM oldbk.castles WHERE id = '.$cid.' FOR UPDATE') or mydie();
		$c = mysql_fetch_assoc($q);
		if (!empty($user['klan']) && $c['lastpagegen'] <= time() && $selfclan) {
			$q = mysql_query('UPDATE oldbk.castles SET lastpagegen = '.(time()+24*3600).' WHERE id = '.$cid) or mydie();
			if (PutPageToArs($user,3003100,$c['pagenum'],$c['pagecolor'],$selfclan['id']) === FALSE) mydie();
			if ($get_ivent['stat']==1)
				{
				//bonus
				$rnd_page=mt_rand(1,5);
				$rnd_color=mt_rand(1,5);				
				if (PutPageToArs($user,3003100,$rnd_page,$rnd_color,$selfclan['id']) === FALSE) mydie();				
				}
		}
		$q = mysql_query('COMMIT') or mydie();
		Redirect("castles_inside.php?qaction=c1");
	}
	/*
	if (isset($_GET['coin'])) {
		$q = mysql_query('START TRANSACTION') or mydie();
		$q = mysql_query('SELECT * FROM oldbk.castles WHERE id = '.$cid.' FOR UPDATE') or mydie();
		$c = mysql_fetch_assoc($q);
		if (!empty($user['klan']) && $c['lastcoingen'] <= time()) {
			$nom = 0.1;

			if (get_chanse(13)) {
				$nom = 1;
			} elseif (get_chanse(5)) {
				$nom = 5;
			} elseif (get_chanse(2)) {
				$nom = 10;
			}

			$_SESSION['castleslastnom'] = $nom;

			if ($selfclan) {
	    			$clan_kazna=clan_kazna_have($selfclan['id']);
				if ($clan_kazna) {
					$rec = array();
		    			$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0;
					$rec['target_login']="Замок";
					$rec['type']=312; // пополнил казну монеткой
					$rec['sum_ekr']=$nom;
					$rec['add_info']=$user['klan'].'/'.$cid;
		
					add_to_new_delo($rec) or mydie();
					
					txt_to_kazna_log(1,2,$selfclan['id'],"\"".$user['login']."\" пополнил казну из замка ".$castles_config[$c['num']]['name']." [".$c['nlevel']."] на ".$nom." екр.");

					mysql_query("UPDATE oldbk.clans_kazna set ekr = ekr+".$nom." WHERE `clan_id` = '".$selfclan['id']."'") or mydie();
					mysql_query('UPDATE oldbk.castles SET lastcoingen = '.(time()+24*3600).' WHERE id = '.$cid) or mydie();
				} else {
					$q = mysql_query('COMMIT') or mydie();
					Redirect("castles_inside.php?qaction=c3");
				}
			}

		}
		$q = mysql_query('COMMIT') or mydie();
		Redirect("castles_inside.php?qaction=c2");
	}
	*/
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
</head>
<body bgcolor=#e2e0e0 leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="margin-left:20px;">
<table width="100%" border=0><tr><td width=55% align="center"><h3 style="text-align:right;"><?php echo $castles_config[$c['num']]['name'].' замок ['.$c['nlevel'].']' ?></td><td align=right><input type=button value='Обновить' onClick="location.href='castles_inside.php?'+Math.random();"> <INPUT TYPE=button value="Вернуться" onClick="location.href='castles_inside.php?exit=1';"></td></tr></table>
<div id="d1">
	<TABLE width=100% height=90% border=0 cellspacing="0" cellpadding="0"><TR><TD align=center valign=top><table width=1 border=0 cellspacing="0" cellpadding="0"><tr><td valign=top>
		<div style="position:relative;">
		<img id="imgareamap" src="http://i.oldbk.com/i/castles/bg_in2.jpg" border="0" style="z-index:1;">
		<a href="?quest"><img style="z-index:3; position: absolute; left: 490px; top: 235px;" src="http://i.oldbk.com/i/castles/man.png" alt="Дворецкий" title="Дворецкий" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/castles/man_h.png'" onmouseout="this.src='http://i.oldbk.com/i/castles/man.png'"/></a>

		<?php 
		for ($i = 0; $i < $c['defcount']; $i++) {
		?>
		<img style="cursor: pointer; z-index:3; position: absolute; left: <?php echo 225+($i*82); ?>px; top: 433px;" src="http://i.oldbk.com/i/castles/z_protect.png" alt="Защитник" title="Защитник" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/castles/z_protect_hover.png'" onmouseout="this.src='http://i.oldbk.com/i/castles/z_protect.png'"/></a>
		<?php } ?>
		

		<?php if (!empty($user['klan']) && $c['lastpagegen'] <= time()) 
		{ 
					if ($get_ivent['stat']==1)
					{
					 $bonus_str=' x2 ';
					}
		?>
		<a href="?scroll"><img style="cursor: pointer; z-index:3; position: absolute; left: 250px; top: 350px;" src="http://i.oldbk.com/i/castles/z_scroll.png" alt="Страница <?=$bonus_str;?>" title="Страница <?=$bonus_str;?> " class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/castles/z_scroll_hover.png'" onmouseout="this.src='http://i.oldbk.com/i/castles/z_scroll.png'"/></a>
		<?php 
				} ?>

		<?php /*if (!empty($user['klan']) && $c['lastcoingen'] <= time()) { ?>
		<a href="?coin"><img style="cursor: pointer; z-index:3; position: absolute; left: 170px; top: 390px;" src="http://i.oldbk.com/i/castles/z_coin.png" alt="Монетка" title="Монетка" class="aFilter2" onmouseover="this.src='http://i.oldbk.com/i/castles/z_coin_hover.png'" onmouseout="this.src='http://i.oldbk.com/i/castles/z_coin.png'"/></a>
		<?php } */?>
<?php
if (isset($_GET['quest']) || isset($mldiag) || isset($_GET['qaction'])) {
	$mlquest = "20/110";
	if (isset($_GET['quest']) || isset($_GET['qaction'])) require_once('mlquest.php');		
}	
?>

</td></tr></TABLE></td></tr></table>
</div>
</body>
</html>
