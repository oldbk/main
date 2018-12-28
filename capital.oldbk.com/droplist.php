<?
// drop list for fsystem
//$DROP=true;

$DROP=false;

if ((time() >= mktime(0,0,0,4,1) && time() <= mktime(23,59,59,4,1))) {
	$DROP=true;

	// setings:
	$MIN_DROP_DM=1; // минимальный урон
	$MIN_DROP_EXP=1; // 0 - все ;
	$MIN_DROP_LEVEL=4;
	$DROP_CHANSE=75; //50% на все итемы

	//list:
	$items[0] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "300407" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>"1",
			 "dategoden"=> mktime(0,0,0,4,2),
	 		 "present"=> ""
		);

	$items[1] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "300408" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>"1",
			 "dategoden"=> mktime(0,0,0,4,2),
	 		 "present"=> ""
		);

	$items[2] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "300409" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>"1",
			 "dategoden"=> mktime(0,0,0,4,2),
	 		 "present"=> ""
		);

	$items[3] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "300410" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>"1",
			 "dategoden"=> mktime(0,0,0,4,2),
	 		 "present"=> ""
		);
	$items[4] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "300411" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>"1",
			 "dategoden"=> mktime(0,0,0,4,2),
	 		 "present"=> ""
		);
	$items[5] = array(
			 "shop" => "eshop", // какой магаз
	 		 "id" => "300412" , // ид из магаза - обязательно разные
			 "maxdur" => "1",
			 "cost"=>0,
			 "goden"=>"1",
			 "dategoden"=> mktime(0,0,0,4,2),
	 		 "present"=> ""
		);

}

?>
