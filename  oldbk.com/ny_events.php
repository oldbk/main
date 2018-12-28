<?php
	$ny_events_cur_m = date("m");
	$ny_events_cur_y = date("Y");

	$ny_events = array(
		// с 1 декабря по 31 января
		'sertstart' => $ny_events_cur_m == 12 ? mktime(0,0,0,12,1,$ny_events_cur_y) : mktime(0,0,0,1,1,$ny_events_cur_y),
		'sertend' => $ny_events_cur_m == 12 ? mktime(23,59,59,12,31,$ny_events_cur_y) : mktime(23,59,59,1,30,$ny_events_cur_y),

		/*
			[2:06:53] Тринити: от 55600008
			до 55600047
			[2:07:14] Тринити: на будущее себе запиши. подарки 1 декабря. Раздел - Зимние подарки
			все подарки тока кредовые
		*/


		/*
			ларцы с 10 декабря до 31 декабря // отключаем автостарт
		*/		
		'larcistart' => mktime(0,0,0,12,10,$ny_events_cur_y-2),
		'larciend' => mktime(23,59,59,12,31,$ny_events_cur_y-2),
	

		/* ёлка на цп с 15 декабря до 30 января 23:59  */
		'elkacpstart' => $ny_events_cur_m == 12 ? mktime(0,0,0,12,15,$ny_events_cur_y) : mktime(0,0,0,1,1,$ny_events_cur_y),
		'elkacpend' => $ny_events_cur_m == 12 ? mktime(23,59,59,12,31,$ny_events_cur_y) : mktime(23,59,59,2,29,$ny_events_cur_y),


		/* подарок на ёлке можно взять с 20 декабря с 1:30 ночи по 29 января 23:59  */
		'elkacpgiftstart' => $ny_events_cur_m == 12 ? mktime(1,30,0,12,20,$ny_events_cur_y) : mktime(0,0,0,1,1,$ny_events_cur_y),
		'elkacpgiftend' => $ny_events_cur_m == 12 ? mktime(23,59,59,12,31,$ny_events_cur_y) : mktime(23,59,59,1,29,$ny_events_cur_y),

		/* еда на ёлке можно взять с 29 декабря по 2 января 23:59  */
		'elkacpeatstart' => $ny_events_cur_m == 12 ? mktime(0,0,0,12,29,$ny_events_cur_y) : mktime(0,0,0,1,1,$ny_events_cur_y),
		'elkacpeatend' => $ny_events_cur_m == 12 ? mktime(23,59,59,1,2,$ny_events_cur_y+1) : mktime(23,59,59,1,2,$ny_events_cur_y),

		/* образ на ёлке можно взять с 20 декабря c 1:30 по 10 января 23:59  */
		'elkacpcarnavalstart' => $ny_events_cur_m == 12 ? mktime(1,30,0,12,20,$ny_events_cur_y) : mktime(0,0,0,1,1,$ny_events_cur_y),
		'elkacpcarnavalend' => $ny_events_cur_m == 12 ? mktime(23,59,59,12,31,$ny_events_cur_y) : mktime(23,59,59,1,10,$ny_events_cur_y),

		/* продажа ёлок и выпадение */
		'elkadropstart' => $ny_events_cur_m == 12 ? mktime(0,0,0,12,1,$ny_events_cur_y) : mktime(0,0,0,1,1,$ny_events_cur_y),
		'elkadropend' => $ny_events_cur_m == 12 ? mktime(23,59,59,2,28,$ny_events_cur_y+1) : mktime(23,59,59,2,28,$ny_events_cur_y),

		/* 10% опыта за поражение с 29 декабря по 2 января 23:59  */
		'ngloseexpstart' => $ny_events_cur_m == 12 ? mktime(0,0,0,12,29,$ny_events_cur_y) : mktime(0,0,0,1,1,$ny_events_cur_y),
		'ngloseexpend' => $ny_events_cur_m == 12 ? mktime(23,59,59,12,31,$ny_events_cur_y) : mktime(23,59,59,1,2,$ny_events_cur_y),

		/* 10% опыта за поражение с 00:00 14 го по 23:59 15 января  */
		'hbloseexpstart' => mktime(0,0,0,1,14,$ny_events_cur_y),
		'hbloseexpend' => mktime(23,59,59,1,15,$ny_events_cur_y),

		/* скупка */
		'skupkastart' => mktime(0,0,0,12,29,$ny_events_cur_y),
		'skupkaend' => mktime(23,59,59,12,30,$ny_events_cur_y),

		/* ногодняя волна хауса */
		'nghaosstart' => $ny_events_cur_m == 12 ? mktime(0,0,0,12,29,$ny_events_cur_y) : mktime(0,0,0,1,1,$ny_events_cur_y),
		'nghaosend' => $ny_events_cur_m == 12 ? mktime(23,59,59,12,31,$ny_events_cur_y) : mktime(23,59,59,1,2,$ny_events_cur_y),

		/* волна на годовщину с 00:00 14 го по 23:59 15 января */
		'hbhaosstart' => mktime(0,0,0,1,14,$ny_events_cur_y),
		'hbhaosend' => mktime(23,59,59,1,15,$ny_events_cur_y),
	);
?>