<?
include "connect.php";
include "new_delo.php";
session_start();
function addchpbook ($text,$who,$room=0,$city=-1) {
	global $user;
	if ($room==0) {
		$room = $user['room'];
	}

	$city=$city+1;


	$txt_to_file=":[".time()."]:[{$who}]:[".($text)."]:[".$room."]";
	$room=-1; // TEST only by Fred
	$q = mysql_query("INSERT INTO `oldbk`.`chat` SET `text`='".mysql_real_escape_string($txt_to_file)."',`city`='".($city)."', `room`={$room} ;");
	if ($q !== FALSE) return true;
	return false;
}

$owner=(int)($_GET['owner']);
$putgold=round(floatval($_GET['putgold']),2);
$getgold=round(floatval($_GET['getgold']),2);


$err[answ]='false';
echo json_encode($err);

try {
	ob_start();
	print_r($_REQUEST);
	$request = ob_get_contents();
	ob_clean();

	ob_start();
	print_r($_SERVER);
	$server = ob_get_contents();
	ob_clean();

	$app->logger->emergency('api_gold', [
		'request' => $request,
		'server' => $server,
	]);
} catch (Exception $ex) {

}
try {
	ob_start();
	print_r($_SESSION);
	$session = ob_get_contents();
	ob_clean();

	$app->logger->emergency('api_gold', [
		'session' => $session,
	]);
} catch (Exception $ex) {

}
exit;
if (($_GET[key]=='q3tyv57uwi4k5uiwk5juntgkswen54gkj34g') AND ($owner>0) and (($putgold>0)OR($getgold>0))) {
	mysql_query('START TRANSACTION') or die();
	$get_usr = mysql_query("select * from oldbk.users where id='{$owner}' FOR UPDATE ");
	if ($get_usr === false) die();
	if (mysql_num_rows($get_usr)) {
		$telo = mysql_fetch_assoc($get_usr);
		if ($telo === false) die();
		if ($putgold > 0)	{
			//ставим
			mysql_query("UPDATE `users` SET gold = gold +' {$putgold}'  WHERE id ='{$telo['id']}' ") or die();

			$rec = array();
			$rec['owner']=$telo['id'];
			$rec['owner_login']=$telo['login'];
			$rec['owner_balans_do']=$telo['gold'];
			$rec['owner_balans_posle']=$telo['gold']+$putgold;
			$rec['target']=1000;
			$rec['target_login']="Букмекер";
			$rec['type']=1351;
			$rec['sum_kr']=$putgold;
			add_to_new_delo($rec) or die();

			$err[answ]='true';
			$err[txt]='send gold ok';
			$err[id]=$telo['id'];
			$err[gold]=$telo['gold']+$putgold;

			addchpbook ('<font color=red>Внимание!</font> Вам одобрен вывод средств из букмекера на сумму '.$putgold.' монет.!','{[]}'.$telo['login'].'{[]}',$telo['room'],$telo['id_city']) or die();
		} elseif (($getgold>0) AND ($telo['gold']>=$getgold)) {
			//забираем
			mysql_query("UPDATE `users` SET gold = gold - '{$getgold}'  WHERE id ='{$telo['id']}' ") or die();
			$rec = array();
			$rec['owner']=$telo['id'];
			$rec['owner_login']=$telo['login'];
			$rec['owner_balans_do']=$telo['gold'];
			$rec['owner_balans_posle']=$telo['gold']-$getgold;
			$rec['target']=1000;
			$rec['target_login']="Букмекер";
			$rec['type']=1352;
			$rec['sum_kr']=$getgold;
			add_to_new_delo($rec) or die();
			$err[answ]='true';
			$err[txt]='get gold ok';
			$err[id]=$telo['id'];
			$err[gold]=$telo['gold']-$getgold;
		} elseif (($getgold>0) AND ($telo['gold']<$getgold)) {
			//ошибка нехваетает денег
			$err[answ]='false';
			$err[txt]='do not have gold';
		} else {
			// другие ошибки		
			$err[answ]='false';
			$err[txt]='other errors';
		}
	} else {
		$err[answ]='false';
		$err[txt]='owner not found';
	}
	mysql_query('COMMIT') or die();
	echo json_encode($err);
} else {
	$err[answ]='false';
	echo json_encode($err);
}
?>