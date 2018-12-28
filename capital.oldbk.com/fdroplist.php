<?
// drop list for fsystem

$DROP=false;

//if ((time() >= mktime(0,0,0,10,13) && time() <= mktime(23,59,59,11,13))) 
if (false) // старый 2016
	{
	$DROP=true;
	// setings:
	$MIN_DROP_DM=1; // минимальный урон
	$MIN_DROP_EXP=1; // 0 - все ;
	$MIN_DROP_LEVEL=4;
	$DROP_CHANSE=98; //50% на все итемы

///Хеоуинские дроп
	//list:
	$items[0] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "304013" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=> round((mktime(23,59,59,11,13)-time())/60/60/24),
			 "dategoden"=> mktime(23,59,59,11,13),
	 		 "present"=> "Halloween"
		);

	$items[1] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "304014" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>round((mktime(23,59,59,11,13)-time())/60/60/24),
			 "dategoden"=> mktime(23,59,59,11,13),
	 		 "present"=> "Halloween"
		);

	$items[2] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "304015" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>round((mktime(23,59,59,11,13)-time())/60/60/24),
			 "dategoden"=> mktime(23,59,59,11,13),
	 		 "present"=> "Halloween"
		);

	$items[3] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "304016" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>round((mktime(23,59,59,11,13)-time())/60/60/24),
			 "dategoden"=> mktime(23,59,59,11,13),
	 		 "present"=> "Halloween"
		);

	$items[4] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "304017" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>round((mktime(23,59,59,11,13)-time())/60/60/24),
			 "dategoden"=> mktime(23,59,59,11,13),
	 		 "present"=> "Halloween"
		);

	$items[5] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "304018" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>round((mktime(23,59,59,11,13)-time())/60/60/24),
			 "dategoden"=> mktime(23,59,59,11,13),
	 		 "present"=> "Halloween"
		);

	$items[6] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "304019" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>round((mktime(23,59,59,11,13)-time())/60/60/24),
			 "dategoden"=> mktime(23,59,59,11,13),
	 		 "present"=> "Halloween"
		);
	
}
else
	if ( ( (time()>mktime(0,0,1,4,1,date("Y"))) and (time()<mktime(23,59,59,4,7,date("Y"))) )   )  
	{
	//list: первоапрельский дроп лист
	
	$DROP=true;
	// setings:
	$MIN_DROP_DM=1; // минимальный урон
	$MIN_DROP_EXP=1; // 0 - все ;
	$MIN_DROP_LEVEL=4;
	$DROP_CHANSE=98; 
	$APRIL=true;
	
	$items[0] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "404001" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>"7",
			 "dategoden"=> mktime(23,59,59,4,7),
	 		 "present"=> ""
		);

	$items[1] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "404002" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>"7",
			 "dategoden"=> mktime(23,59,59,4,7),
	 		 "present"=> ""
		);

	$items[2] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "404003" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>"7",
			 "dategoden"=> mktime(23,59,59,4,7),
	 		 "present"=> ""
		);

	$items[3] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "404004" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>"7",
			 "dategoden"=> mktime(23,59,59,4,7),
	 		 "present"=> ""
		);
	$items[4] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "404005" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>"7",
			 "dategoden"=> mktime(23,59,59,4,7),
	 		 "present"=> ""
		);
	$items[5] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "404006" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>"7",
			 "dategoden"=> mktime(23,59,59,4,7),
	 		 "present"=> ""
		);
	}
	
//print_r($items);
?>
