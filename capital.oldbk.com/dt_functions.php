<?php
function Redirect($path) {
	header("Location: ".$path); 
	die();
} 

function MyDie() {
	Redirect("dt.php");
}

function GetSerFile($path) {
	clearstatcache();
	if (file_exists($path)) {
		$data = file_get_contents($path);
		if ($data !== FALSE && strlen($data) > 0) {
			$data = unserialize($data);
			if ($data !== null && $data !== FALSE) {
				return $data;
			}
		}
	}
	return array();
}

function SaveSerFile($path,$arr) {
	$fp = fopen($path,'w+');
	if ($fp) {
		if (flock($fp, LOCK_EX)) {
			fwrite($fp,serialize($arr));
			flock($fp, LOCK_UN);
		}
		fclose($fp);
	}	
}

function undressallbot($bot) {
	//EchoLog("undressallbot: ".$bot['id']);
	$q = mysql_query('SELECT * FROM oldbk.inventory WHERE dressed = 1 AND owner = '.$bot['id']);
	if ($q === false) return false;

	$udrsl = array();

	while($item = mysql_fetch_assoc($q)) {
		$slot1 = "";
		switch($item['type']) {
			case 1: $slot1 = 'sergi'; break;
			case 2: $slot1 = 'kulon'; break;
			case 3: $slot1 = 'weap'; break;
			case 4: $slot1 = 'bron'; break;
			case 5: $slot1 = 'r1'; break;
			case 6: $slot1 = 'r2'; break;
			case 7: $slot1 = 'r3'; break;
			case 8: $slot1 = 'helm'; break;
			case 9: $slot1 = 'perchi'; break;
			case 10: $slot1 = 'shit'; break;
			case 11: $slot1 = 'boots'; break;
			case 27: $slot1 = 'nakidka'; break;
			case 28: $slot1 = 'rubashka'; break;
		}		
		if (empty($slot1)) continue;

		if ($item['type'] == 5) {
			if ($item['id'] == $bot['r1']) $bot['r1'] = 0;
			if ($item['id'] == $bot['r2']) $bot['r2'] = 0;
			if ($item['id'] == $bot['r3']) $bot['r3'] = 0;
		} else {
			$bot[$slot1] = 0;
		}
		//EchoLog("UnDressing: ".$item['id']);
		$udrsl[] = $item['id'];

		$bot['maxhp'] -= $item['ghp'];
		$bot['sum_minu'] -= $item['minu'];
		$bot['sum_maxu'] -= $item['maxu'];

		$bot['sila'] -= $item['gsila'];
		$bot['lovk'] -= $item['glovk'];
		$bot['inta'] -= $item['ginta'];
		$bot['intel'] -= $item['gintel'];
		$bot['mudra'] -= $item['gmp'];

		$bot['sum_mfuvorot'] -= $item['glovk']*5;
		$bot['sum_mfauvorot'] -= $item['glovk']*5;
		$bot['sum_mfauvorot'] -= $item['ginta']*2;

		$bot['sum_mfkrit'] -= $item['ginta']*5;
		$bot['sum_mfakrit'] -= $item['ginta']*5;
		$bot['sum_mfakrit'] -= $item['glovk']*2;

		$bot['noj'] -= $item['gnoj'];
		$bot['topor'] -= $item['gtopor'];
		$bot['dubina'] -= $item['gdubina'];
		$bot['mec'] -= $item['gmech'];							

		$bot['sum_mfkrit'] -= $item['mfkrit'];
		$bot['sum_mfakrit'] -= $item['mfakrit'];
		$bot['sum_mfuvorot'] -= $item['mfuvorot'];
		$bot['sum_mfauvorot'] -= $item['mfauvorot'];

		$bot['sum_bron1'] -= $item['bron1'];
		$bot['sum_bron2'] -= $item['bron2'];
		$bot['sum_bron3'] -= $item['bron3'];
		$bot['sum_bron4'] -= $item['bron4'];
		$bot['at_cost'] -= $item['cost'];
        }

	if (count($udrsl)) {
		if ($bot['hp'] > $bot['maxhp']) $bot['hp'] = $bot['maxhp'];

		$q = mysql_query('UPDATE users_clons 
			SET
				sila = '.$bot['sila'].',
				lovk = '.$bot['lovk'].',
				inta = '.$bot['inta'].',
				intel = '.$bot['intel'].',
				mudra = '.$bot['mudra'].',
				noj = '.$bot['noj'].',
				mec = '.$bot['mec'].',
				topor = '.$bot['topor'].',
				dubina = '.$bot['dubina'].',
				maxhp = '.$bot['maxhp'].',
				hp = '.$bot['hp'].',
				sergi = '.$bot['sergi'].',
				kulon = '.$bot['kulon'].',
				perchi = '.$bot['perchi'].',
				weap = '.$bot['weap'].',
				bron = '.$bot['bron'].',
				r1 = '.$bot['r1'].',
				r2 = '.$bot['r2'].',
				r3 = '.$bot['r3'].',
				helm = '.$bot['helm'].',
				shit = '.$bot['shit'].',
				boots = '.$bot['boots'].',
				nakidka = '.$bot['nakidka'].',
				at_cost = '.$bot['at_cost'].',
				sum_minu = '.$bot['sum_minu'].',
				sum_maxu = '.$bot['sum_maxu'].',
				sum_mfkrit = '.$bot['sum_mfkrit'].',
				sum_mfakrit = '.$bot['sum_mfakrit'].',
				sum_mfuvorot = '.$bot['sum_mfuvorot'].',
				sum_mfauvorot = '.$bot['sum_mfauvorot'].',
				sum_bron1 = '.$bot['sum_bron1'].',
				sum_bron2 = '.$bot['sum_bron2'].',
				sum_bron3 = '.$bot['sum_bron3'].',
				sum_bron4 = '.$bot['sum_bron4'].',
				rubashka = '.$bot['rubashka'].'
			WHERE id = '.$bot['id']
		);
		if ($q === false) return false;

		$q = mysql_query('UPDATE oldbk.inventory SET dressed = 0 WHERE id IN ('.implode(",",$udrsl).') and owner = '.$bot['id']);
		if ($q === false) return false;	
	}
	return $bot;
}

?>