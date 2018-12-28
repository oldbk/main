<?php       
	require_once('dt_rooms.php');

	if (isset($_SESSION['uid'])) $frpath = '/www/cache/usertimes/dt'.$_SESSION["uid"];
	$frrelpath = '/www/cache/usertimes/dt';

	$dt_topweapon = array(
		1006241,1006242,199,206
	);
	$art_items_up = array(
		"1006242" => array(
			"minu" => 29,
		),
		"199" => array(
			"cost" => 950,
		),
		"206" => array(
			"cost" => 940,
		),
		"170" => array(
			"cost" => 20,
		),
		"263" => array(
			"nvinos" => 20,
			"nsila" => 20,
		),
		"109" => array(
			"gdubina" => 2,
		),
		"82" => array(
			"minu" => 12,
			"maxu" => 20,
			
		),
		"5" => array(
			"minu" => 9,
			"maxu" => 19,
		),
		"4" => array(
			"minu" => 9,
			"maxu" => 18,		
			"gmech" => 2,
		),             
		"111" => array(
			"gtopor" => 2,
		),
	);

	$noart_items_up = array(
		"263" => array(
			"nvinos" => 20,
			"nsila" => 20,
		),
		"109" => array(
			"gdubina" => 2,
		),
		"82" => array(
			"minu" => 12,
			"maxu" => 20,
		),
		"5" => array(
			"minu" => 9,
			"maxu" => 19,
		),
		"4" => array(
			"minu" => 9,
			"maxu" => 18,		
			"gmech" => 2,
		),
		"111" => array(
			"gtopor" => 2,
		),
		"170" => array(
			"cost" => 20,
		),
		"75" => array(
			"minu" => 10,
			"maxu" => 16,
			"mfkrit" => 35,
			"mfauvorot" => 20,
		),
		"94" => array(
			"minu" => 10,
			"maxu" => 14,
			"gdubina" => 1,
			"mfauvorot" => 15,
		),
		"95" => array(
			"minu" => 10,
			"maxu" => 15,
			"gdubina" => 1,
			"mfkrit" => 20,
		),
		"91" => array(
			"minu" => 10,
			"maxu" => 17,
			"mfuvorot" => 20,
			"mfauvorot" => 10,
		),
		"110" => array(
			"minu" => 12,
			"maxu" => 18,
			"gtopor" => 1,
			"mfkrit" => 30,
		),
		"76" => array(
			"minu" => 11,
			"maxu" => 16,
		),
		"80" => array(
			"minu" => 11,
			"maxu" => 16,
		),
		"112" => array(
			"minu" => 12,
			"maxu" => 16,
			"mfauvorot" => 46,
			"gnoj" => 1,
		),
		"93" => array(
			"minu" => 10,
			"maxu" => 16,
			"mfakrit" => 40,
			"gnoj" => 1,
		),
		"92" => array(
			"minu" => 13,
			"maxu" => 14,
			"mfkrit" => 10,
			"mfuvorot" => 20,
			"mfauvorot" => 30,
			"gnoj" => 1,
		),
		"1" => array(
			"minu" => 13,
			"maxu" => 14,
			"mfuvorot" => 40,
			"gnoj" => 1,
		),
		"113" => array(
			"minu" => 11,
			"maxu" => 17,
			"mfkrit" => 35,
			"mfauvorot" => 10,
		),
		"79" => array(
			"minu" => 11,
			"maxu" => 16,
			"mfkrit" => 35,
			"mfauvorot" => 10,
		),

		"281" => array(
			"nsila" => 20,
			"nvinos" => 20,
			"nlevel" => 6,
		),
		"62" => array(
			"nsila" => 20,
			"nvinos" => 20,
			"nlevel" => 6,
		),
		"280" => array(
			"nsila" => 20,
			"nvinos" => 20,
			"nlevel" => 6,
		),
		"222222236" => array(
			"nsila" => 20,
			"nvinos" => 20,
			"nlevel" => 6,
		),
		"222222237" => array(
			"nsila" => 20,
			"nvinos" => 20,
			"nlevel" => 6,
		),
	);

?>