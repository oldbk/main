<?php
$ext_meta_id = (int)($_GET['ext_meta_id']);
$price_rur = 35; // — цена в рублях, в Вашем случае она ровняется 20
$hash = md5("'$price_rur'33689"); // — хэш-код, который вычисляется по формуле: md5("'$price_rur'60039")
$target[663] = 517;
$target[685] = 518;
$target[686] = 519;
$target_id = $target[$_REQUEST['pid']];
//	Если target_id указан – статистика по целям будет отображаться в соответствующих колонках "Цель 1", "Цель 2", ... , "Цель N".
//Внимание! При вычислении хэш-кода обратите внимание на расположение и тип кавычек. Стоимость должна находиться в одинарных кавычках: md5("'0.09'60039")
//Задержка обновления статистики составляет около 10 минут.
$linklux = "http://luxup.ru/extmeta/?ext_meta_id={$ext_meta_id}&lx_price={$price_rur}&user_id=37563&lx_price_hash={$hash}&target_id={$target_id}";
$fd = fopen($linklux, "r");
fclose($fd);