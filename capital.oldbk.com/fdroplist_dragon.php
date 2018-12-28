<?
// drop list for fsystem
// for dragons!!!!!!!!!!!!!!!
//$DROP=true;
$DROP=false;

if ($DROP==true)
{
// setings:
$MIN_DROP_DM=1;
$MIN_DROP_EXP=1; 
$WIN_ONLY=true; //true- только победители, false(default) - все
$MIN_DROP_LEVEL=7;
$DROP_CHANSE=75;
$DRAGONS=true;

/*
Дроп
 можно получить только в случае победы в боях с Драконами
 шанс дропа одного любого предмета 75%, если было нанесено как минимум 1 урона
 максимум в одном бою можно получить 3 предмета

*/

//list:
/*
$items[0] = array(
	"shop" => "eshop", // 25% 16022 - Украденный манускрипт
	"id" => "16022" , // ид из магаза - обязательно разные
	"maxdur" => "1",
	"cost"=>0,
);
*/
$items[0] = array(
	"shop" => "shop", 
	"id" => "505501", //Крыло Дракона
	"maxdur" => "1",
	"cost"=>0
);

$items[1] = array(
	"shop" => "shop", 
	"id" => "505502", //Чешуя Дракона
	"maxdur" => "1",
	"cost"=>0
);

$items[2] = array(
	"shop" => "shop", 
	"id" => "505503", //	Череп Дракона
	"maxdur" => "1",
	"cost"=>0
);

$items[3] = array(
	"shop" => "shop", 
	"id" => "505504", //Глаз Дракона
	"maxdur" => "1",
	"cost"=>0
);

$items[4] = array(
	"shop" => "shop", 
	"id" => "505505", //Печень Дракона
	"maxdur" => "1",
	"cost"=>0
);
}
?>
