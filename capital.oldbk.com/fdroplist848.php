<?
// drop list for fsystem baff 848
$DROP=true;


// setings:
$MIN_DROP_DM=1; // минимальный урон
$MIN_DROP_EXP=1; // 0 - все ;
$MIN_DROP_LEVEL=1;
$DROP_CHANSE=70; //70% на все итемы



if ($user[id_city]==0)
{
if ($user[level]<6)
	{
	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3001" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3002" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3003" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3004" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3005" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3006" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3007" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	
	
	}
else
{

//list:
$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3001" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3002" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3003" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3004" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3005" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3006" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3007" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3008" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3009" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3010" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3011" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3012" , //3012
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

}

if (( strpos($user[medals],"011;" ) !== FALSE ) )
		{
//для героев
	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3013" , //3021
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3014" , //3021
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);
	
	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3015" , //3021
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	
	
	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3016" , //3021
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	
	
	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3017" , //3021
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	
	
	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3018" , //3021
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3019" , //3021
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3020" , //3021
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "3021" , //3021
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);
	}
}
else if ($user[id_city]==1)
{
if ($user[level]<6)
	{
	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103001" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103002" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103003" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103004" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103005" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103006" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103007" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	
	
	}
else
{
//list:
$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103001" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103002" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103003" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103004" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103005" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103006" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103007" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103008" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103009" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103010" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103011" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103012" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

}

if (( strpos($user[medals],"011;" ) !== FALSE ) )
		{
//для героев
	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103013" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103014" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);
	
	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103015" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	
	
	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103016" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	
	
	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103017" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	
	
	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103018" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103019" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);	

	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103020" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);

	$items[] = array(
		 "shop" => "eshop", // какой магаз
 		 "id" => "103021" , 
		 "maxdur" => "1",
		 "cost"=>0,
 		 "present"=> ""
	);
	}
}

?>
