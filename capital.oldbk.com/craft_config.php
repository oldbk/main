<?php

$craftconfigcache = 5*60; // время кеша в секундах

$craftlist = array();
$craftlisttype = array();
$craftlistdesc = array();
$craftlistrname = array();
$craftrooms = array();
$craftspeedupprice = 1; // 1 екр за 1 час
$craftspeedupmin = 0.1; // мин 0.1 екр

// профы
$t = mysql_query_cache('SELECT * FROM craft_prof',false,$craftconfigcache);
if (!count($t)) die();
while(list($k,$v) = each($t)) {
	$craftlist[$k] = $v['name'];
	$craftlistrname[$k] = $v['rname'];
	$craftlisttype[$k] = $v['type'];
	$craftlistdesc[$k] = $v['desc'];
}

$craftlistname = array_flip($craftlist);


// конфиг лок
$t = mysql_query_cache('
SELECT * FROM craft_locations 
       LEFT JOIN craft_razdel ON craft_locations.id = craft_razdel.locationid
       LEFT JOIN craft_ins ON craft_razdel.id = craft_ins.razdelid 
',false,$craftconfigcache);

if (!count($t)) die();
while(list($k,$v) = each($t)) {
	if (!isset($craftrooms[$v['locationid']])) $craftrooms[$v['locationid']] = array();
	if (!isset($craftrooms[$v['locationid']]["name"])) $craftrooms[$v['locationid']]["name"] = $v['name'];
	if (!isset($craftrooms[$v['locationid']]["helpname"])) $craftrooms[$v['locationid']]["helpname"] = $v['helpname'];
	if (!isset($craftrooms[$v['locationid']]["razdel"])) $craftrooms[$v['locationid']]["razdel"] = array();
	if (!isset($craftrooms[$v['locationid']]["razdel"][$v['razdel']])) $craftrooms[$v['locationid']]["razdel"][$v['razdel']] = array();
	if (!isset($craftrooms[$v['locationid']]["razdel"][$v['razdel']]['name'])) $craftrooms[$v['locationid']]["razdel"][$v['razdel']]['name'] = $v['rname'];
	if (!isset($craftrooms[$v['locationid']]["razdel"][$v['razdel']]['desc'])) $craftrooms[$v['locationid']]["razdel"][$v['razdel']]['desc'] = $v['desc'];
	if (!isset($craftrooms[$v['locationid']]["razdel"][$v['razdel']]['ins'])) $craftrooms[$v['locationid']]["razdel"][$v['razdel']]['ins'] = array();
	if (intval($v['insprotoid']) > 0) {
		$craftrooms[$v['locationid']]["razdel"][$v['razdel']]['ins'][] = $v['insprotoid'];
	}
}


$craftexptable = require(ROOT_DIR . '/components/config/craft/exp.php');
$craftreqs_params = require(ROOT_DIR . '/components/config/craft/params.php');

?>