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
$putekr=round(floatval($_GET['putekr']),2);
$getekr=round(floatval($_GET['getekr']),2);
$bankid=(int)($_GET['bankid']);

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

	$app->logger->emergency('api_ekr', [
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

	$app->logger->emergency('api_ekr', [
		'session' => $session,
	]);
} catch (Exception $ex) {

}
exit;
if  (($_GET[key]=='q3tyv57uwi4k5uiwk5juntgkswen54gkj34g')  AND ($owner>0) and ($bankid>0) and (($putekr>0)OR($getekr>0))) {
	mysql_query('START TRANSACTION') or die();
	$getbank = mysql_query("select * from oldbk.bank where  id='{$bankid}' and owner='{$owner}'  FOR UPDATE");
	if ($getbank === false) die();
	if (mysql_num_rows($getbank)) {
		$get_bank = mysql_fetch_assoc($getbank);
		if ($get_bank === false) die();
		$get_usr = mysql_query("select * from oldbk.users where id='{$owner}' ");
		if ($get_usr === false) die();

		$telo = mysql_fetch_assoc($get_usr); 
		if ($telo === false) die();
		if ($telo['id']>0) {
			if ($putekr>0) {
				mysql_query("UPDATE `oldbk`.`bank` SET `ekr` = `ekr` + '".$putekr."' WHERE `id`= '".$bankid."' LIMIT 1;") or die();
						
				$rec = array();
	    			$rec['owner']=$telo['id'];
				$rec['owner_login']=$telo['login'];
				$rec['owner_balans_do']=$telo['money'];
				$rec['owner_balans_posle']=$telo['money'];
				$rec['target']=1000;
				$rec['target_login']="Букмекер";
				$rec['type']=1106; 
				$rec['sum_ekr']=$putekr;
				$rec['bank_id']=$bankid;
				$rec['add_info']='Баланс до: '.$get_bank['ekr'].' екр. Баланс после:'.($get_bank['ekr']+=$putekr) ;
				add_to_new_delo($rec) or die(); 
							
				mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','Пополнение счета <b>{$putekr} екр.</b>. <i>(Итого: {$get_bank['cr']} кр., {$get_bank['ekr']} екр.)</i>','{$bankid}');") or die();

				addchpbook ('<font color=red>Внимание!</font> Вам одобрен вывод средств из букмекера на сумму '.$putekr.' екр. на счет №'.$bankid.' !','{[]}'.$telo['login'].'{[]}',$telo['room'],$telo['id_city']) or die();
				$err[answ]='true';
				$err[txt]='send money ok';	
				$err[id]=$bankid;
				$err[owner]=$owner;
				$err[ekr]=$get_bank['ekr'];
				$err[cr]=$get_bank['cr'];
			} elseif (($getekr>0) AND ($get_bank['ekr']>=$getekr)) {
				//забираем
				mysql_query("UPDATE `oldbk`.`bank` SET `ekr` = `ekr` - '".$getekr."' WHERE `id`= '".$bankid."' LIMIT 1;") or die();
						
				$rec = array();
	    			$rec['owner']=$telo['id'];
				$rec['owner_login']=$telo['login'];
				$rec['owner_balans_do']=$telo['money'];
				$rec['owner_balans_posle']=$telo['money'];
				$rec['target']=1000;
				$rec['target_login']="Букмекер";
				$rec['type']=1107; 
				$rec['sum_ekr']=$getekr;
				$rec['bank_id']=$bankid;
				$rec['add_info']='Баланс до: '.$get_bank['ekr'].' екр. Баланс после:'.($get_bank['ekr']-=$getekr) ;
				add_to_new_delo($rec) or die(); 
							
				mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','Оплата со счета <b>{$getekr} екр.</b>. <i>(Итого: {$get_bank['cr']} кр., {$get_bank['ekr']} екр.)</i>','{$bankid}');") or die();

				$err[answ]='true';
				$err[txt]='send money ok';	
				$err[id]=$bankid;
				$err[owner]=$owner;
				$err[ekr]=$get_bank['ekr'];
				$err[cr]=$get_bank['cr'];
			} elseif (($getekr>0) AND ($get_bank['ekr']<$getekr)) {
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
	} else {
		$err[answ]='false';
		$err[txt]='bank not found';			
	}
	mysql_query('COMMIT') or die();
	echo json_encode($err);			
} else {
	$err[answ]='false';
	echo json_encode($err);
}	
?>