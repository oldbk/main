<?php

function cors() {

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}
	cors();

	session_start();

	include "connect.php";
	include "functions.php";

	if (!ADMIN) die("NOADMIN");

	$arr = array(32,33,34,35,36,37);


	mysql_query('START TRANSACTION') or die("SQL ERROR");
	mysql_query('TRUNCATE TABLE larci') or die("SQL ERROR");

	while(list($k,$v) = each($arr)) {
		$data = file_get_contents('http://a.oldbk.com/api/gift-box.json?id='.$v);
		if ($data === false || strlen($data) < 10) die("FETCH ERROR");
		$data = json_decode($data);
		if ($data === false) die("JSON ERROR");
		while(list($ka,$va) = each($data)) {
			mysql_query('INSERT INTO larci (listid,prototype,shop_id,allcount,`left`,ekrprice) VALUES("'.$v.'","'.$va->prototype.'","'.$va->shop_id.'","'.$va->count.'","'.$va->count.'","'.$va->ekrprice.'")') or die("INSERT ERROR: ".mysql_error());
		}
	}

	mysql_query('COMMIT') or die("SQL ERROR");
	echo "EXPORT OK";
	
?>