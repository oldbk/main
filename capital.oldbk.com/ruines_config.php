<?php       
	require_once('ruines_rooms.php');

	if (isset($_SESSION['uid'])) $frpath = '/www/cache/usertimes/r'.$_SESSION["uid"];
	$frrelpath = '/www/cache/usertimes/r';

	$team_colors = array(1 => "blue", 2 => "red");

	$bot_id = array();
	$bot_id[8] = array("start" => array("501" => 2, "502" => 3), "end" => array("507" => 3, "508" => 3));
	$bot_id[9] = array("start" => array("503" => 2, "504" => 3), "end" => array("509" => 3, "510" => 3));;
	$bot_id[10] = array("start" => array("505" => 2, "506" => 3), "end" => array("511" => 3, "512" => 3));;

	$keyexcluderooms = array(75,77,51,41,42,52,31,32,76,9,10,19,20,29,30);

	$gomoney = 10; // кр для входа в заявку или создание

	$iupgrade = array(
				7 => array(
					"level" => 7,
					"hp" => 6,
					"bron" => 1,
					"stat" => 1,
					"mf" => 5,
					"udar" => 1,
					"nparam" => 1,
					"duration" => 30
				),
				8 => array(
					"level" => 8,
					"hp" => 8,
					"bron" => 1,
					"stat" => 1,
					"mf" => 7,
					"udar" => 2,
					"nparam" => 1,
					"duration" => 40
				),
				9 => array(
					"level" => 9,
					"hp" => 10,
					"bron" => 1,
					"stat" => 1,
					"mf" => 10,
					"udar" => 3,
					"nparam" => 1,
					"duration" => 50
				),
				10 => array(
					"level" => 10,
					"hp" => 12,
					"bron" => 1,
					"stat" => 1,
					"mf" => 12,
					"udar" => 4,
					"nparam" => 1,
					"duration" => 50
				)
	);

	$ritems = array();

	// свитки

	// ловушки
	$ritems[194] = array(0 => array("scroll" => 1, "count" => 6));
	// клоны
	$ritems[119] = array(0 => array("scroll" => 1, "count" => 4));
	// переманы
	$ritems[120] = array(0 => array("scroll" => 1, "count" => 4));
	// путы
	$ritems[121] = array(0 => array("scroll" => 1, "count" => 6));
	// иденты
	$ritems[9] = array(0 => array("scroll" => 1, "count" => 10));

	// хилки 90/120/150/180
	$ritems[246] = array(0 => array("scroll" => 1, "count" => 12));
	$ritems[249] = array(0 => array("scroll" => 1, "count" => 12));

	// зелье гермеса
	$ritems[605605] = array(0 => array("scroll" => 1, "count" => 3));

	// свиток оживления
	$ritems[666666] = array(0 => array("scroll" => 1, "count" => 3));

	// свиток опознания мф
	$ritems[666667] = array(0 => array("scroll" => 1, "count" => 24));

	// молчи
	//$ritems[102] = array(0 => array("scroll" => 1, "count" => 6));

	// кольцо лесного духа
	$ritems[222222231] = array(
		// уник 5п
		0 => array(
			"addname" => " (мф)",
			"glovk" => 3,
			"bron1" => 3,
			"bron2" => 3,
			"bron3" => 3,
			"bron4" => 3,
			"mfuvorot" => 25,
			"directionmf" => "mfuvorot",
			"directionstats" => "glovk",
			"count" => 1
    		)
	);
	
	$ritems[222222233] = array(
		//Кольцо Легендарного Воина
 		// не уник на 5п
    		0 => array(
     			"addname" => " (мф)",
     			"ginta" => 2,
     			"bron1" => 2,
     			"bron2" => 2,
     			"bron3" => 2,
     			"bron4" => 2,
     			"mfauvorot" => 25,
     			"directionmf" => "mfauvorot",
     			"directionstats" => "ginta",
     			"count" => 2
    		)
	);

	$ritems[222222230] = array(
		// Кольцо Глаз Дракона
		// не уник на 4п
    		0 => array(
     			"addname" => " (мф)",
     			"ginta" => 2,
     			"bron1" => 2,
     			"bron2" => 2,
     			"bron3" => 2,
     			"bron4" => 2,
     			"mfkrit" => 15,
     			"directionmf" => "mfkrit",
     			"directionstats" => "ginta",
     			"count" => 3
    		)
	);

	$ritems[222222232] = array(
		//Кольцо Великих Стремлений
		// не уник на 3п
    		0 => array(
     			"addname" => " (мф)",
     			"ginta" => 2,
     			"bron1" => 2,
     			"bron2" => 2,
     			"bron3" => 2,
     			"bron4" => 2,
     			"mfakrit" => 9,
     			"directionmf" => "mfakrit",
     			"directionstats" => "ginta",
     			"count" => 4
		)
	);


 	$ritems[222222237] = array(
		// Кольцо Мести
    		// уник, 5п
    		0 => array(
		     "addname" => " (мф)",
		     "ginta" => 3,
		     "bron1" => 3,
		     "bron2" => 3,
		     "bron3" => 3,
		     "bron4" => 3,
		     "mfakrit" => 25,
		     "directionmf" => "mfakrit",
		     "directionstats" => "ginta",
		     "count" => 2
    		)
	);

	$ritems[222222236] = array(
		//Кольцо Защитника
	 	// не уник, 5п
		0 => array(
		     "addname" => " (мф)",
		     "ginta" => 2,
		     "bron1" => 2,
		     "bron2" => 2,
		     "bron3" => 2,
		     "bron4" => 2,
		     "mfkrit" => 25,
		     "directionmf" => "mfkrit",
		     "directionstats" => "ginta",
		     "count" => 3
		)
	);

	$ritems[222222238] = array(
		//Кольцо Хитрости
		// не уник 4п
		0 => array(
		     "addname" => " (мф)",
		     "glovk" => 2,
		     "bron1" => 2,
		     "bron2" => 2,
		     "bron3" => 2,
		     "bron4" => 2,
		     "mfuvorot" => 15,
		     "directionmf" => "mfuvorot",
		     "directionstats" => "glovk",
		     "count" => 6
    		)
	);

	$ritems[222222239] = array(
		//Кольцо Обмана
		 // не уник на АУ 3п
		0 => array(
		     "addname" => " (мф)",
		     "ginta" => 2,
		     "bron1" => 2,
		     "bron2" => 2,
		     "bron3" => 2,
		     "bron4" => 2,
		     "mfauvorot" => 9,
		     "directionmf" => "mfauvorot",
		     "directionstats" => "ginta",
		     "count" => 9
	    	)
	);

 	// Кольцо Осады
 	$ritems[222222240] = array(
		    // уник на АК, 5п
		0 => array(
		     "addname" => " (мф)",
		     "ghp" => 20,
		     "gsila" => 3,
		     "bron1" => 3,
		     "bron2" => 3,
		     "bron3" => 3,
		     "bron4" => 3,
		     "mfakrit" => 25,
		     "directionmf" => "mfakrit",
		     "directionstats" => "gsila",
		     "count" => 1
		)
	);


	$ritems[222222241] = array(
		//Кольцо Отваги
		 // уник на АУ, 5п
		0 => array(
		     "addname" => " (мф)",
		     "ghp" => 20,
		     "gsila" => 3,
		     "bron1" => 3,
		     "bron2" => 3,
		     "bron3" => 3,
		     "bron4" => 3,
		     "mfauvorot" => 25,
		     "directionmf" => "mfauvorot",
		     "directionstats" => "gsila",
		     "count" => 1
		)
	);
	

	
	// кольцо ужаса
	$ritems[16] = array(
				// уник на АУ, 5п
				0 => array(
					"addname" => " (мф)",
					"gintel" => 3,
					"bron1" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"bron4" => 3,
					"mfauvorot" => 25,
					"directionmf" => "mfauvorot",
					"directionstats" => "ginta",
					"count" => 3
				),

				// не уник на У 5п
				1 => array(
					"addname" => " (мф)",
					"gintel" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"mfkrit" => 25,
					"directionmf" => "mfuvorot",
					"directionstats" => "ginta",
					"count" => 4
				),

				// не уник на АК 4п
				2 => array(
					"addname" => " (мф)",
					"gintel" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"mfakrit" => 15,
					"directionmf" => "mfakrit",
					"directionstats" => "ginta",
					"count" => 9
				),

				// не уник на К 3п
				3 => array(
					"addname" => " (мф)",
					"gintel" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"mfkrit" => 9,
					"directionmf" => "mfkrit",
					"directionstats" => "ginta",
					"count" => 14
				),
	);
	

	// секира сумеречных теней
	$ritems[2000] = array(
				0 => array(
					"addname" => " +5",
					"minu" => 5,
					"maxu" => 5,
					"count" => 3
				),
	);

	// Тяжелый мОЛОТ Расплаты
	$ritems[284] = $ritems[2000];
	$ritems[284][0]["count"] = 2;

	// меч безумной крови
	$ritems[2001] = $ritems[2000];

	// молот ярости титанов
	$ritems[2002] = $ritems[2000];
	
	// молот правосудия
	$ritems[242] = $ritems[2000];

	// молот страха
	$ritems[108] = $ritems[2000];

	// тяжёлый молот света
	$ritems[236] = $ritems[2000];

	// карающий топор
	$ritems[82] = $ritems[2000];

	// меч ярости света 
	$ritems[234] = $ritems[2000];

	// двойной топор света
	$ritems[142] = $ritems[2000];

	// топор равновесия
	$ritems[229] = $ritems[2000];

	// мольба
	$ritems[78] = $ritems[2000];

	// топор жестокости
	$ritems[131] = $ritems[2000];

	// оправдаха
	$ritems[96] = $ritems[2000];

	// Стальная секира Спокойствия
	$ritems[265] = $ritems[2000];
	$ritems[265][0]["count"] = 2;

	// Булатный молот
	$ritems[267] = $ritems[2000];
	$ritems[267][0]["count"] = 2;

	// Меч Превосходства
	$ritems[271] = $ritems[2000];
	$ritems[271][0]["count"] = 2;


	// Доспех Сумеречных Теней - 260
	$ritems[260] = array(
				// уник на У, 5п
				0 => array(
					"addname" => " (мф)",
					"glovk" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"mfuvorot" => 25,
					"directionmf" => "mfuvorot",
					"directionstats" => "glovk",
                                        "ghp" => "20",
					"count" => 2
				)
	);


	//  Тяжелый Доспех Расплаты - 283
	$ritems[283] = array(
				// АУ
				0 => array(
					"addname" => "",
					"directionmf" => "mfauvorot",
					"directionstats" => "gsila",
					"count" => 1
				),
				// АК
				1 => array(
					"addname" => "",
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
					"count" => 1
				)
	);

	// Тяжелые Рыцарские латы
	$ritems[266] = array(
				// уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"gsila" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
                                        "ghp" => "20",
					"count" => 1
				),
				// уник на АУ, 5п
				1 => array(
					"addname" => " (мф)",
					"gsila" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"mfauvorot" => 25,
					"directionmf" => "mfauvorot",
					"directionstats" => "gsila",
                                        "ghp" => "20",
					"count" => 1
				)
	);

	// Латы Безумной Крови - 262
	$ritems[262] = array(
				// уник на К, 5п
				0 => array(
					"addname" => " (мф)",
					"ginta" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"mfkrit" => 25,
					"directionmf" => "mfkrit",
					"directionstats" => "ginta",
                                        "ghp" => "20",
					"count" => 2
				)
	);



	// броня печали
	$ritems[62] = array(
				// уник на К, 5п
				0 => array(
					"addname" => " (мф)",
					"gsila" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"mfkrit" => 25,
					"directionmf" => "mfkrit",
					"directionstats" => "gsila",
                                        "ghp" => "20",
					"count" => 1
				),

				// не уник на АК 5п
				1 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
                                        "ghp" => "20",
					"count" => 2
				),

				// не уник на У 4п
				2 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"mfauvorot" => 15,
					"directionmf" => "mfuvorot",
					"directionstats" => "gsila",
                                        "ghp" => "20",
					"count" => 5
				),

				// не уник на АУ 3п
				3 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"mfauvorot" => 9,
					"directionmf" => "mfauvorot",
					"directionstats" => "gsila",
                                        "ghp" => "20",
					"count" => 7
				),
	);

	// тяжёлая пал броня
	$ritems[63] = array(
				// уник на У, 5п
				0 => array(
					"addname" => " (мф)",
					"gsila" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"mfuvorot" => 25,
					"directionmf" => "mfuvorot",
					"directionstats" => "gsila",
                                        "ghp" => "20",
					"count" => 1
				),

				// не уник на АУ 5п
				1 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"mfauvorot" => 25,
					"directionmf" => "mfauvorot",
					"directionstats" => "gsila",
                                        "ghp" => "20",
					"count" => 1
				),

				// не уник на К 4п
				2 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"mfakrit" => 15,
					"directionmf" => "mfkrit",
					"directionstats" => "gsila",
                                        "ghp" => "20",
					"count" => 1
				),

				// не уник на АК 3п
				3 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"mfakrit" => 9,
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
                                        "ghp" => "20",
					"count" => 2
				),
	);

        // маска мельника
        $ritems[73] = array(
                                // уник на АУ, 5п
                                0 => array(
					"addname" => " (мф)",
					"ginta" => 3,
					"bron1" => 3,
					"mfauvorot" => 25,
					"ghp" => "20",
					"directionmf" => "mfauvorot",
					"directionstats" => "ginta",
					"count" => 1
				),

                                // не уник на К 5п
				1 => array(
					"addname" => " (мф)",
					"ginta" =>2 ,
					"bron1" => 2,
					"mfkrit" => 25,
					"directionmf" => "mfkrit",
					"directionstats" => "ginta",
                                        "ghp" => "20",
					"count" => 1
                                ),

                                // не уник на АУ 4п
				2 => array(
					"addname" => " (мф)",
					"ginta" => 2,
					"bron1" => 2,
					"mfauvorot" => 15,
					"directionmf" => "mfauvorot",
					"directionstats" => "ginta",
                                        "ghp" => "20",
					"count" => 2
                                ),

                                // не уник на К 3п
				3 => array(
					"addname" => " (мф)",
					"ginta" =>2 ,
					"bron1" => 2,
					"mfauvorot" => 9,
					"directionmf" => "mfkrit",
					"directionstats" => "ginta",
                                        "ghp" => "20",
					"count" => 3
				)
	);

	// Маска Темного Дровосека
	$ritems[74] = array(
				// уник на У, 5п
				0 => array(
					"addname" => " (мф)",
					"glovk" => 3,
                                        "bron1" => 3,
					"mfuvorot" => 25,
					"directionmf" => "mfuvorot",
					"directionstats" => "glovk",
                                        "ghp" => "20",
					"count" => 1
				),

				// не уник на АУ 5п
				1 => array(
					"addname" => " (мф)",
					"ginta" => 2,
					"bron1" => 3,
					"ghp" => "20",
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"directionstats" => "ginta",
					"count" => 2
				),

				// не уник на АУ 4п
				2 => array(
					"addname" => " (мф)",
					"ginta" => 2,
					"bron1" => 3,
					"mfauvorot" => 15,
					"directionmf" => "mfauvorot",
					"directionstats" => "ginta",
                                        "ghp" => "20",
					"count" => 2
				),

				// не уник на У 3п
				3 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"bron1" => 3,
					"mfuvorot" => 9,
					"directionmf" => "mfuvorot",
					"directionstats" => "glovk",
                                        "ghp" => "20",
					"count" => 3
				),
	);

	// Шлем Гладиатора 
	$ritems[259] = array(
			
				// уник на АК 5п
				0 => array(
					"addname" => " (мф)",
					"gsila" => 3,
                                        "bron1" => 3,
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
                                        "ghp" => "20",
					"count" => 2
				),
				// уник на АУ 5п
				1 => array(
					"addname" => " (мф)",
					"gsila" => 3,
                                        "bron1" => 3,
					"mfauvorot" => 25,
					"directionmf" => "mfauvorot",
					"directionstats" => "gsila",
                                        "ghp" => "20",
					"count" => 2
				),
	);

	// Позолоченный шлем
	$ritems[235] = array(
			
				// не уник на АК 5п
				0 => array(
					"addname" => " (мф)",
					"ginta" => 2,
                                        "bron1" => 2,
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"directionstats" => "ginta",
                                        "ghp" => "20",
					"count" => 1
				),

				// не уник на У 4п
				1 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"ghp" => "20",
                                        "bron1" => 2,
					"mfuvorot" => 15,
					"directionmf" => "mfuvorot",
					"directionstats" => "glovk",
					"count" => 2
				),

				// не уник на АK 3п
				2=> array(
					"addname" => " (мф)",
					"ginta" => 2,
                                        "bron1" => 2,
					"mfakrit" => 9,
					"directionmf" => "mfakrit",
					"directionstats" => "ginta",
                                        "ghp" => "20",
					"count" => 3
				),
	);

         // Кулон защиты
	$ritems[270] = array(
				// уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"bron1" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"bron4" => 3,
					"gsila" => 3,
					"ghp" => "20",
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
					"count" => 2
				),
	);


         // Кулон призрачного дракона
	$ritems[24] = array(
				// уник на У, 5п
				0 => array(
					"addname" => " (мф)",
					"glovk" => 3,
					"ghp" => "20",
					"mfuvorot" => 25,
					"directionmf" => "mfuvorot",
					"directionstats" => "glovk",
					"count" => 1
				),

				// не уник на АК 5п
				1 => array(
					"addname" => " (мф)",
					"ginta" => 2,
					"ghp" => "20",
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"directionstats" => "ginta",
					"count" => 1
				),

				// не уник на АК 4п
				2 => array(
					"addname" => " (мф)",
					"ginta" => 2,
					"ghp" => "20",
					"mfakrit" => 15,
					"directionmf" => "mfakrit",
					"directionstats" => "ginta",
					"count" => 3
				),

				// не уник на АК 3п
				3 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"ghp" => "20",
					"mfuvorot" => 9,
					"directionmf" => "mfuvorot",
					"directionstats" => "glovk",
					"count" => 5
				),
				
	);
           // Драконы-Близнецы
	$ritems[22] = array(
				// уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"ginta" => 3,
					"ghp" => "20",
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"directionstats" => "ginta",
					"count" => 1
				),

				// не уник на АУ 5п
				1 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					 "ghp" => "20",
					"mfauvorot" => 25,
					"directionmf" => "mfauvorot",
					"directionstats" => "glovk",
					"count" => 2
				),

				// не уник на АК 4п
				2 => array(
					"addname" => " (мф)",
					"ginta" => 2,
					"ghp" => "20",
					"mfakrit" => 15,
					"directionmf" => "mfakrit",
					"directionstats" => "ginta",
					"count" => 3
				),

				// не уник на АУ 3п
				3 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					 "ghp" => "20",
					"mfauvorot" => 9,
					"directionmf" => "mfauvorot",
					"directionstats" => "glovk",
					"count" => 4
				),
	);
           // Cережки растления
	$ritems[32] = array(
				//  не уник на У, 5п
				0 => array(
					"addname" => " (мф)",
                                        "gsila" => 2,
					"ghp" => "20",
					"mfuvorot" => 25,
					"directionmf" => "mfuvorot",
					"directionstats" => "gsila",
					"count" => 1
				),

				// не уник на АУ 4п
				1 => array(
					"addname" => " (мф)",
                                        "ginta" => 2,
					"ghp" => "20",
					"mfаuvorot" => 15,
					"directionmf" => "mfаuvorot",
					"directionstats" => "ginta",
					"count" => 1
				),

				// не уник на АК 3п
				2 => array(
					"addname" => " (мф)",
                                        "ginta" => 2,
					"ghp" => "20",
					"mfakrit" => 9,
					"directionmf" => "mfakrit",
					"directionstats" => "ginta",
					"count" => 3
				),

				// не уник на АК 3п
				3 => array(
					"addname" => " (мф)",
                                        "gsila" => 2,
					"ghp" => "20",
					"mfakrit" => 9,
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
					"count" => 2
				),
	);
        // Изумрудные серьги
	$ritems[28] = array(
				// уник на К, 5п
				0 => array(
					"addname" => " (мф)",
					"ginta" => 3,
					"ghp" => "20",
					"mfkrit" => 25,
					"directionmf" => "mfkrit",
					"directionstats" => "ginta",
					"count" => 1
				),

				// не уник на К 3п
				1 => array(
					"addname" => " (мф)",
					"ginta" => 2,
					"ghp" => "20",
					"mfkrit" => 9,
					"directionmf" => "mfkrit",
					"directionstats" => "ginta",
					"count" => 2
				),

				// не уник на АУ 4п
				3 => array(
					"addname" => " (мф)",
					"ginta" => 2,
					"ghp" => "20",
					"mfauvorot" => 15,
					"directionmf" => "mfauvorot",
					"directionstats" => "ginta",
					"count" => 2
				
				),
	);

	// Алмазные серьги Стойкости
	$ritems[273] = array(
				//  уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"gsila" => 3,
					"ghp" => "20",
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
					"count" => 1
				),

				//  уник на АУ, 5п
				1 => array(
					"addname" => " (мф)",
					"gsila" => 3,
					"ghp" => "20",
					"mfauvorot" => 25,
					"directionmf" => "mfauvorot",
					"directionstats" => "gsila",
					"count" => 1
				),
	);

	// Царские серьги
	$ritems[27] = array(
				//  не уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"ghp" => "20",
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
					"count" => 1
				),

				// не уник на У 4п
				1 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"ghp" => "20",
					"mfuvorot" => 15,
					"directionmf" => "mfuvorot",
					"directionstats" => "gsila",
					"count" => 1
				),

				// не уник на У 3п
				2 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"ghp" => "20",
					"mfuvorot" => 9,
					"directionmf" => "mfuvorot",
					"directionstats" => "gsila",
					"count" => 3
				),

				//   уник на К, 5п
				3 => array(
					"addname" => " (мф)",
					"gsila" => 3,
					"ghp" => "20",
					"mfkrit" => 25,
					"directionmf" => "mfkrit",
					"directionstats" => "gsila",
					"count" => 1
				),
	);

	// Серьги Брони Великана
	$ritems[31] = array(
				//  не уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"ghp" => "20",
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"count" => 1
				),

				// не уник на У 4п
				1 => array(
					"addname" => " (мф)",
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"ghp" => "20",
					"mfuvorot" => 15,
					"directionmf" => "mfuvorot",
					"count" => 1
				),

				// не уник на У 3п
				2 => array(
					"addname" => " (мф)",
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"ghp" => "20",
					"mfuvorot" => 9,
					"directionmf" => "mfuvorot",
					"count" => 3
				),

				//   уник на К, 5п
				3 => array(
					"addname" => " (мф)",
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"ghp" => "20",
					"mfkrit" => 25,
					"directionmf" => "mfkrit",
					"count" => 1
				),
	);
         // Перчатки Паладина
	$ritems[231] = array(
				//  не уник на К, 5п
				0 => array(
					"addname" => " (мф)",
					"mfkrit" => 25,
					"directionmf" => "mfkrit",
					"ghp" => "20",
					"count" => 1
				),

				// не уник на АК 4п
				1 => array(
					"addname" => " (мф)",
					"mfаkrit" => 15,
					"directionmf" => "mfаkrit",
					  "ghp" => "20",
					"count" => 2
				),

				// не уник на АУ 3п
				2 => array(
					"addname" => " (мф)",
					"mfauvorot" => 9,
					"directionmf" => "mfauvorot",
					"ghp" => "20",
					"count" => 2
				)
				
	);

	

	// Стальные перчатки
	$ritems[263] = array(
				// топ на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"ghp" => "20",
					"count" => 2
				),
				// топ  на АУ, 5п
				1 => array(
					"addname" => " (мф)",
					"mfauvorot" => 25,
					"directionmf" => "mfauvorot",
					"ghp" => "20",
					"count" => 2
				)
	);

         // Позолоченные перчатки
	$ritems[228] = array(
				//  не уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"mfаkrit" => 25,
					"directionmf" => "mfаkrit",
					"ghp" => "20",
					"count" => 1
				),
				

				// не уник на К 4п
				1 => array(
					"addname" => " (мф)",
					"mfkrit" => 15,
					"directionmf" => "mfkrit",
					"ghp" => "20",
					"count" => 2
				),

				// не уник на У 3п
				2 => array(
					"addname" => " (мф)",
					"mfuvorot" => 9,
					"directionmf" => "mfuvorot",
					"ghp" => "20",
					"count" => 2

				),
	);

	// Перчатки из чешуи дракона
	$ritems[178] = array(
				//  не уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"mfаuvorot" => 25,
					"directionmf" => "mfаuvorot",
					"ghp" => "20",
					"count" => 1
				),

				// не уник на АК 4п
				1 => array(
					"addname" => " (мф)",
					"mfakrit" => 15,
					"directionmf" => "mfakrit",
					"ghp" => "20",
					"count" => 2
				),

				// не уник на АУ 3п
				2 => array(
					"addname" => " (мф)",
					"mfаuvorot" => 9,
					"directionmf" => "mfаuvorot",
					"ghp" => "20",
					"count" => 2
				),
	);
         // Перчатки Вампира
	$ritems[177] = array(
				//  не уник на У, 5п
				0 => array(
					"addname" => " (мф)",
					"mfuvorot" => 25,
					"directionmf" => "mfuvorot",
					"ghp" => "20",
					"count" => 1
				),

				// не уник на АУ 4п
				1 => array(
					"addname" => " (мф)",
					"mfаuvorot" => 15,
					"directionmf" => "mfаuvorot",
					"ghp" => "20",
					"count" => 2
				),

				// не уник на АК 3п
				2 => array(
					"addname" => " (мф)",
					"mfakrit" => 9,
					"directionmf" => "mfakrit",
					"ghp" => "20",
					"count" => 2
				),
	);

	// Тяжелый Рыцарский щит 
	$ritems[269] = array(
				// уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"gsila" => 3,
					"bron1" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"bron4" => 3,
					"mfakrit" => 25,
					"ghp" => "20",
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
					"count" => 2
				)
	);

	// Щит наемника
	$ritems[172] = array(
				// уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"glovk" => 3,
					"bron1" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"bron4" => 3,
					"mfakrit" => 25,
					"ghp" => "20",
					"directionmf" => "mfakrit",
					"directionstats" => "glovk",
					"count" => 1
				),

				// не уник на АУ 5п
				1 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"mfauvorot" => 25,
                                        "ghp" => "20",
					"directionmf" => "mfauvorot",
					"directionstats" => "glovk",
					"count" => 1
				),

				// не уник на АК 4п
				2 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"mfakrit" => 15,
                                        "ghp" => "20",
					"directionmf" => "mfakrit",
					"directionstats" => "glovk",
					"count" => 1
				),

				// не уник на АУ 3п
				3 => array(
				        "addname" => " (мф)",
					"glovk" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"mfauvorot" => 9,
					"ghp" => "20",
					"directionmf" => "mfauvorot",
					"directionstats" => "glovk",
					"count" => 2
				),
	);

        // Щит пегаса
	$ritems[35] = array(
				// уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"gsila" => 3,
					"bron1" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"bron4" => 3,
					"mfakrit" => 25,
                                        "ghp" => "20",
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
					"count" => 1
				),

				// не уник на АУ 5п
				1 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"mfauvorot" => 25,
					"ghp" => "20",
					"directionmf" => "mfauvorot",
					"directionstats" => "gsila",
					"count" => 1
				),

				// не уник на АК 4п
				2 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"mfakrit" => 15,
                                        "ghp" => "20",
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
					"count" => 1
				),

				// не уник на АУ 3п
				3 => array(
				        "addname" => " (мф)",
					"gsila" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" => 2,
					"mfauvorot" => 9,
					"ghp" => "20",
					"directionmf" => "mfauvorot",
					"directionstats" => "gsila",
					"count" => 2
				),
	);

        // Нормандский Щит
	$ritems[33] = array(
				// уник на АУ, 5п
				0 => array(
					"addname" => " (мф)",
					"gsila" => 3,
					"bron1" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"bron4" =>3,
					"mfauvorot" => 25,
					"directionmf" => "mfauvorot",
					"directionstats" => "ginta",
					"count" => 1
				),

				// не уник на АУ 5п
				1 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" =>2,
					"mfauvorot" => 25,
					"directionmf" => "mfauvorot",
					"directionstats" => "ginta",
					"count" => 1
				),

				// не уник на АУ 4п
				2 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" =>2,
					"mfauvorot" => 15,
					"directionmf" => "mfauvorot",
					"directionstats" => "ginta",
					"count" => 3
				),

				// не уник на АУ 3п
				3 => array(
					"addname" => " (мф)",
					"gsila" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" =>2 ,
					"mfаuvorot" => 9,
					"directionmf" => "mfаuvorot",
					"directionstats" => "ginta",
					"count" => 2
				),
	);

        // Щит Стража
	$ritems[244] = array(
				// уник на АУ, 5п
				0 => array(
					"addname" => " (мф)",
					"ginta" => 3,
					"bron1" => 3,
					"bron2" => 3,
					"bron3" => 3,
					"bron4" =>3,
					"mfauvorot" => 25,
					"ghp" => "20",
					"directionmf" => "mfauvorot",
					"directionstats" => "ginta",
					"count" => 1
				),

				// не уник на АУ 5п
				1 => array(
					"addname" => " (мф)",
					"ginta" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" =>2,
					"mfauvorot" => 25,
					"ghp" => "20",
					"directionmf" => "mfauvorot",
					"directionstats" => "ginta",
					"count" => 1
				),

				// не уник на К 4п
				2 => array(
					"addname" => " (мф)",
					"ginta" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" =>2,
					"mfakrit" => 15,
					"ghp" => "20",
					"directionmf" => "mfakrit",
					"directionstats" => "ginta",
					"count" => 3
				),

				// не уник на У 3п
				3 => array(
					"addname" => " (мф)",
					"ginta" => 2,
					"bron1" => 2,
					"bron2" => 2,
					"bron3" => 2,
					"bron4" =>2,
					"mfuvorot" => 9,
					"ghp" => "20",
					"directionmf" => "mfuvorot",
					"directionstats" => "ginta",
					"count" => 3
				),
	);

        // Сапоги Великана
	$ritems[268] = array(
				// уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"gsila" => 3,
					"bron4" => 3,
					"mfakrit" => 25,
                                        "ghp" => "20",
					"directionmf" => "mfakrit",
					"directionstats" => "gsila",
					"count" => 1
				),
				// уник на АУ, 5п
				1 => array(
					"addname" => " (мф)",
					"gsila" => 3,
					"bron4" => 3,
					"mfauvorot" => 25,
                                        "ghp" => "20",
					"directionmf" => "mfauvorot",
					"directionstats" => "gsila",
					"count" => 1
				)
	);

        // Латные сапоги пустыни
	$ritems[174] = array(
				// уник на АК, 5п
				0 => array(
					"addname" => " (мф)",
					"glovk" => 3,
					"bron4" => 3,
					"mfakrit" => 25,
                                        "ghp" => "20",
					"directionmf" => "mfakrit",
					"directionstats" => "glovk",
					"count" => 1
				),

				// не уник на У 5п
				1 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"ghp" => "20",
					"bron4" => 2,
					"mfuvorot" => 25,
					"directionmf" => "mfuvorot",
					"directionstats" => "glovk",
					"count" => 1
				),

				// не уник на У 4п
				2 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"ghp" => "20",
					"bron4" => 2,
					"mfuvorot" => 15,
					"directionmf" => "mfuvorot",
					"directionstats" => "glovk",
					"count" => 2
				),

				// не уник на АУ 3п
				3 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"ghp" => "20",
					"bron4" => 2,
					"mfakrit" => 9,
					"directionmf" => "mfakrit",
					"directionstats" => "glovk",
					"count" => 3
				),
	);

        // Позолоченные ботинки
	$ritems[233] = array(
				// уник на АУ, 5п
				0 => array(
					"addname" => " (мф)",
					"glovk" => 3,
					"ghp" => "20",
					"bron4" => 3,
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"directionstats" => "glovk",
					"count" => 1
				),

				// не уник на У 5п
				1 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"ghp" => "20",
					"bron4" => 2,
					"mfuvorot" => 25,
					"directionmf" => "mfuvorot",
					"directionstats" => "glovk",
					"count" => 1
				),

				// не уник на У 4п
				2 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"ghp" => "20",
					"bron4" => 2,
					"mfuvorot" => 15,
					"directionmf" => "mfuvorot",
					"directionstats" => "glovk",
					"count" => 2
				),

				// не уник на АУ 3п
				3 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"ghp" => "20",
					"bron4" => 2,
					"mfakrit" => 9,
					"directionmf" => "mfakrit",
					"directionstats" => "glovk",
					"count" => 3
				),
	);

	// Сапоги Крестоносца
	$ritems[245] = array(
				// уник на АУ, 5п
				0 => array(
					"addname" => " (мф)",
					"glovk" => 3,
					"ghp" => "20",
					"bron4" => 3,
					"mfakrit" => 25,
					"directionmf" => "mfakrit",
					"directionstats" => "glovk",
					"count" => 1
				),

				// не уник на У 5п
				1 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"ghp" => "20",
					"bron4" => 2,
					"mfuvorot" => 25,
					"directionmf" => "mfuvorot",
					"directionstats" => "glovk",
					"count" => 1
				),

				// не уник на У 4п
				2 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"ghp" => "20",
					"bron4" => 2,
					"mfuvorot" => 15,
					"directionmf" => "mfuvorot",
					"directionstats" => "glovk",
					"count" => 2
				),

				// не уник на АУ 3п
				3 => array(
					"addname" => " (мф)",
					"glovk" => 2,
					"ghp" => "20",
					"bron4" => 2,
					"mfakrit" => 9,
					"directionmf" => "mfakrit",
					"directionstats" => "glovk",
					"count" => 3
				),
	);

?>