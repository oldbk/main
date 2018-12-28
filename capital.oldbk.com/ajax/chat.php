<?
	session_start();
	include "../connect.php";
	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
	if (!ADMIN) die('Страница не найдена :)');
	include('../functions.php');
		//echo "<script>function p(text) {document.write(text+'<BR>');}";

		$ks = 0;
		$newmaxid = 0;

		if (isset($_SESSION['lid']) && intval($_SESSION['lid']) > 0) {
			$q = mysql_query('SELECT max(id) as maxid FROM oldbk.chat') or die();
			$q = mysql_fetch_assoc($q) or die();
			$where = "(id > ".intval($_SESSION['lid'])." AND id <= ".$q['maxid'].')';
			if (isset($_SESSION['admchatmsglast']) && $_SESSION['admchatmsglast'] > 0) {
				$where .= ' or id = '.$_SESSION['admchatmsglast'];
				$_SESSION['admchatmsglast'] = 0;
			}
			$newmaxid = $q['maxid'];
		} else {
			$where = "UNIX_TIMESTAMP(`cdate`) > ".(time()-120);
		}

		// 1 - общий
		// 2 - приваты личные
		// 3 - клан каналы, межклан каналы и тд
		// 4 - тимприваты и груп-приваты (для загорода, руин, боев)
		// 5 - системки
		// 6 - помошники
		// 7 - общая системка

		$get_chat = mysql_query("SELECT UNIX_TIMESTAMP(`cdate`) as tt,text,id FROM oldbk.chat where (".$where.") order by id ASC");

		while($chatrow = mysql_fetch_array($get_chat)) 	{
			$v = $chatrow[text];

			preg_match("/:\[(.*)\]:\[(.*)\]:\[(.*)]:\[(.*)\]/",$v,$math);

			$all_user_dat = explode(":|:",$math[2]);
			$chat_user_id = $all_user_dat[1]; //узер ид

			// а терь возвращаем все как было
			$math[2] = $all_user_dat[0];
			$math[3] = stripslashes($math[3]);

			$orig_math[2]=$math[2];

			if($user['klan'] != 'pal' AND $user['klan'] != 'radminion' AND $user['klan'] != 'Adminion') {
				if(preg_match("/\Невидимка:([0-9]+)/",$math[2],$neved)) {
					//если невед, то
			     		$chat_user_id = $neved[1]; //в коде на жалобу пишем айди неведа
				}
				$math[2] = preg_replace("~Невидимка:([0-9]+)~iU",'</a><b><i>Невидимка</i></b>',$math[2]); // и в чат не выводи его айди
			}

			$chatrowrid = $chatrow[id];
                        $chatrow[id] = xorit(codein($chatrow[id]));
			$zhaloba = " oncontextmenu=\'return OpenMenu2(event,\"".$chatrow[id]."\")\' ";


			// фикс на яваскрипт
			$addjs = "";
			$pos = strpos($math[3],'<BR>\';');
			if ($pos !== FALSE) {
				$addjs = substr($math[3],$pos+6)." ';";
				$math[3] = substr($math[3],0,$pos);
			}
			$addjs = "";

			if ((@$math[2] == '{[]}'.$user['login'].'{[]}')) {
				echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> <span class=stext id=".$chatrow[id].">".$math[3]."</span>',5);".$addjs;
			} elseif(substr($math[2],0,4) == '{[]}') {
			} elseif ((@$math[2] == '!sys!!')) {
				$chat_id = $chatrowrid;
				continue;
			} elseif (@$math[2] == '!sys2all!!') {
				$chat_id = $chatrowrid;
				continue;
			} elseif ((@$math[2] == '!group!'))  {
				$chat_id = $chatrowrid;
				continue;
			} elseif ((strpos($math[3],"private [pal-" ) !== FALSE)) {
				echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> [<a onclick=\"AddTo(\'{$math[2]}\',event);\"><span oncontextmenu=\'return OpenMenu(event,".$user['level'].")\'>{$math[2]}</span></a>] <span class=stext id=".$chatrow[id].">".$math[3]."</span>',3);".$addjs;
			} elseif (((strpos($math[3],"private [team-blue-") !== FALSE))) {
				echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> [<a onclick=\"AddTo(\'{$math[2]}\',event);\"><span oncontextmenu=\'return OpenMenu(event,".$user['level'].")\'>{$math[2]}</span></a>] <span class=stext id=".$chatrow[id].">".$math[3]."</span>',4);".$addjs;
			} elseif (((strpos($math[3],"private [team-blue-" ) !== FALSE))) {
				echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> [<a onclick=\"AddTo(\'{$math[2]}\',event);\"><span oncontextmenu=\'return OpenMenu(event,".$user['level'].")\'>{$math[2]}</span></a>] <span class=stext id=".$chatrow[id].">".$math[3]."</span>',4);".$addjs;
			} elseif (strpos($math[3],"private [zgroup-") !== FALSE)  {
				echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> [<a onclick=\"AddTo(\'{$math[2]}\',event);\"><span oncontextmenu=\'return OpenMenu(event,".$user['level'].")\'>{$math[2]}</span></a>] <span class=stext id=".$chatrow[id].">".$math[3]."</span>',4);".$addjs;
			} elseif (strpos($math[3],"private [team-1-" ) !== FALSE || strpos($math[3],"private [team-2-" ) !== FALSE) {
				echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> [<a onclick=\"AddTo(\'{$math[2]}\',event);\"><span oncontextmenu=\'return OpenMenu(event,".$user['level'].")\'>{$math[2]}</span></a>] <span class=stext id=".$chatrow[id].">".$math[3]."</span>',4);".$addjs;
			} elseif (strpos($math[3],"private [klan-pal-" ) !== FALSE) {
				echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> [<a onclick=\"AddTo(\'{$math[2]}\',event);\"><span oncontextmenu=\'return OpenMenu(event,".$user['level'].")\'>{$math[2]}</span></a>] <span class=stext id=".$chatrow[id].">".$math[3]."</span>',3);".$addjs;
			} elseif (strpos($math[3],"private [klan-" ) !== FALSE ) {
			    	echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> [<a onclick=\"AddTo(\'{$math[2]}\',event);\"><span oncontextmenu=\'return OpenMenu(event,".$user['level'].")\'>{$math[2]}</span></a>] <span class=stext id=".$chatrow[id].">".$math[3]."</span>',3);".$addjs;
			} elseif  (strpos($math[3],"private [mklan-" ) !== FALSE) {
				echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> [<a onclick=\"AddTo(\'{$math[2]}\',event);\"><span oncontextmenu=\'return OpenMenu(event,".$user['level'].")\'>{$math[2]}</span></a>] <span class=stext id=".$chatrow[id].">".$math[3]."</span>',3);".$addjs;
			} elseif (strpos($math[3],"private [pal]" ) !== FALSE) {
				echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> [<a onclick=\"AddTo(\'{$math[2]}\',event);\"><span oncontextmenu=\'return OpenMenu(event,".$user['level'].")\'>{$math[2]}</span></a>] <span class=stext id=".$chatrow[id].">".$math[3]."</span>',3);".$addjs;
			} elseif (strpos($math[3],"private [helpers]" ) !== FALSE) {
				echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> [<a onclick=\"AddTo(\'{$math[2]}\',event);\"><span oncontextmenu=\'return OpenMenu(event,".$user['level'].")\'>{$math[2]}</span></a>] <span class=stext id=".$chatrow[id].">".$math[3]."</span>',6);".$addjs;
			} elseif (strpos($math[3],"private") !== FALSE && @$math[2] != '!sys!!') {
				echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> [<a onclick=\"AddTo(\'{$math[2]}\',event);\"><span oncontextmenu=\'return OpenMenu(event,".$user['level'].")\'>{$math[2]}</span></a>] <span class=stext id=".$chatrow[id].">".$math[3]."</span>',6);".$addjs;
			} else {
				echo "p('<span class=date2 ".$zhaloba.">".date("H:i",$math[1])."</span> [<a onclick=\"AddTo(\'{$math[2]}\',event);\"><span oncontextmenu=\'return OpenMenu(event,".$user['level'].")\'>{$math[2]}</span></a>] <span class=stext id=".$chatrow[id].">".$math[3]."</span>',6);".$addjs;
			}	

			$chat_id = $chatrowrid;
	}

	$_SESSION['admlastlook'] = time();
	$_SESSION['lid'] = $chat_id;
	//echo '</script>';
		
?>