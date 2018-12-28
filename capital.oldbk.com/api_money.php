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
$putkr=round(floatval($_GET['putkr']),2);
$getkr=round(floatval($_GET['getkr']),2);

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

	$app->logger->emergency('api_kr', [
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

	$app->logger->emergency('api_kr', [
		'session' => $session,
	]);
} catch (Exception $ex) {

}
exit;
if (($_GET[key]=='q3tyv57uwi4k5uiwk5juntgkswen54gkj34g') AND ($owner>0) and (($putkr>0)OR($getkr>0))) {
	mysql_query('START TRANSACTION') or die();
	$get_usr = mysql_query("select * from oldbk.users where id='{$owner}' FOR UPDATE ");
	if ($get_usr === false) die();
	if (mysql_num_rows($get_usr)) {
		$telo = mysql_fetch_assoc($get_usr); 
		if ($telo === false) die();
		if ($putkr > 0)	{
			//ставим
			mysql_query("UPDATE `users` SET money = money +' {$putkr}'  WHERE id ='{$telo['id']}' ") or die();	
						
			$rec = array();
    			$rec['owner']=$telo['id'];
			$rec['owner_login']=$telo['login'];
			$rec['owner_balans_do']=$telo['money'];
			$rec['owner_balans_posle']=$telo['money']+$putkr;
			$rec['target']=1000;
			$rec['target_login']="Букмекер";
			$rec['type']=1104; 
			$rec['sum_kr']=$putkr;
			add_to_new_delo($rec) or die();

			$err[answ]='true';
			$err[txt]='send money ok';	
			$err[id]=$telo['id'];
			$err[money]=$telo['money']+$putkr;
							
			addchpbook ('<font color=red>Внимание!</font> Вам одобрен вывод средств из букмекера на сумму '.$putkr.' кр.!','{[]}'.$telo['login'].'{[]}',$telo['room'],$telo['id_city']) or die();
		} elseif (($getkr>0) AND ($telo['money']>=$getkr)) {
			//забираем
			mysql_query("UPDATE `users` SET money = money - '{$getkr}'  WHERE id ='{$telo['id']}' ") or die();
			$rec = array();
    			$rec['owner']=$telo['id'];
			$rec['owner_login']=$telo['login'];
			$rec['owner_balans_do']=$telo['money'];
			$rec['owner_balans_posle']=$telo['money']-$getkr;
			$rec['target']=1000;
			$rec['target_login']="Букмекер";
			$rec['type']=1105; 
			$rec['sum_kr']=$getkr;
			add_to_new_delo($rec) or die(); 
			$err[answ]='true';
			$err[txt]='get money ok';	
			$err[id]=$telo['id'];
			$err[money]=$telo['money']-$getkr;
		} elseif (($getkr>0) AND ($telo['money']<$getkr)) {
			//ошибка нехваетает денег
			$err[answ]='false';
			$err[txt]='do not have money';					
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