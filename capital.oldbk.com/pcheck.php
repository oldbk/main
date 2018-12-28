<?php
	session_start();
	include "connect.php";
	include "functions.php";


	if ($user['id'] != 8540 && $user['id'] != 102904) die();

	error_reporting(E_ALL);
	ini_set("display_errors",1);

	if (isset($_FILES['userfile']) && $_FILES['userfile']['error'] == 0 && $_FILES['userfile']['size'] > 0) {
		$iplist = array();
		$file = file_get_contents($_FILES['userfile']['tmp_name']);
		$file = iconv("UTF-16","cp1251",$file);
		$file = explode("\n",$file);

		$first = false;

		while(list($k,$v) = each($file)) {		
			if (!$first) {
				$first = true; 
				continue;
			}
			$data = explode("\t",$v);

			if (!empty($data[10])) $iplist[] = trim($data[10]);
		}

		include "/www/capitalcity.oldbk.com/alg.php";

		if (count($iplist)) {
			$r = "";
			while(list($k,$v) = each($iplist)) {
				$r .= '"'.$v.'",';
			}

			$r = substr($r,0,strlen($r)-1);	

			$q = mysql_query('SELECT * FROM users WHERE `id` IN ('.$r.')') or die(mysql_error());
			$ulist = array();
			while($u = mysql_fetch_assoc($q)) {
				$ulist[] = $u;
			}
			
			$ulist2 = $ulist;
			while(list($k,$v) = each($ulist)) {
				reset($ulist2);
				while(list($ka,$va) = each($ulist2)) {
					if ($k == $ka) continue;
					if ($v['pass'] == $va['pass']) {
						echo 'Совпадение паролей: '.$v['login'].' = '.$va['login'].' ('.out_smdp_new($v['pass']).') ('.$v['id'].' '.$va['id'].')<br>';
					}
					if ($v['email'] == $va['email']) {
						echo 'Совпадение емейлов: '.$v['login'].' = '.$va['login'].' ('.$v['email'].') ('.$v['id'].' '.$va['id'].')<br>';
					}
					if ($v['borndate'] == $va['borndate']) {
						echo 'Совпадение ДР: '.$v['login'].' = '.$va['login'].' ('.$v['borndate'].') ('.$v['id'].' '.$va['id'].')<br>';
					}
					if (!empty($v['realname']) && $v['realname'] == $va['realname']) {
						echo 'Совпадение имён: '.$v['login'].' = '.$va['login'].' ('.$v['realname'].') ('.$v['id'].' '.$va['id'].')<br>';
					}
					if (!empty($v['ip']) && $v['ip'] == $va['ip']) {
						echo 'Совпадение IP: '.$v['login'].' = '.$va['login'].' ('.$v['ip'].') ('.$v['id'].' '.$va['id'].')<br>';
					}
				}
			}
			echo '<br><br>';
			echo 'Проверено: '.count($ulist);

		}
	}
?>
<HTML>
<HEAD>
<link rel=stylesheet type="text/css" href="i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META HTTP-EQUIV=Expires CONTENT=0>
<META HTTP-EQUIV=imagetoolbar CONTENT=no>
<script type="text/javascript" src="/i/globaljs.js"></script>
</head>
<body leftmargin=2 topmargin=2 marginwidth=2 marginheight=2 bgcolor="#D7D7D7">

<form enctype="multipart/form-data" method="POST">
    Репорт: <input name="userfile" type="file" />
    <input type="submit" value="Отправить" />
</form>

</body>
</html>