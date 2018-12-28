<?
//установки групп монстов легкие 0 - сложные 10
// mob id - kol

$mt_rand_23=mt_rand(2,3);
$mt_rand_34=mt_rand(3,4);
$mt_rand_45=mt_rand(4,5);


$mob[1]= array(
		"name" => "Медузa",
		"id" => 267,
		"kol" => 1);		
		
$mob[2]= array(
		"name" => "Мимант",
		"id"=>263,
		"kol" => 1);	

$mob[3]= array(
		"name" => "Каллисто",
		"id"=>273,
		"kol" => $mt_rand_34);	
	
$mob[4]= array(
		"name" => "Гектор",
		"id"=>264,
		"kol" => $mt_rand_34);		


$mob[5]= array(
		"name" => "Кронос",
		"id"=>269,
		"kol" => $mt_rand_34);		

$mob[6]= array(
		"name" => "Пaллaнт",
		"id"=>268,
		"kol" => $mt_rand_34);		


//////////
$sjmob[1]= array(
		"name"=> "Голем",
		"id" => 270);


////////////установки мобов для J-ботов

$jmob[1]= array(
		"name"=> "Малый Голем",
		"id" => 275,
		"top"=>25, 
		"left" =>90 );		


$jmob[2]= array(
		"name"=> "Локи",
		"id" => 279,
		"top"=>50, 
		"left" =>135 );			


$jmob[3]= array(
		"name"=> "Зириддар",
		"id" => 278,
		"top"=>60, 
		"left" =>90 );	

$jmob[4]= array(
		"name"=> "Полибот",
		"id" => 274,
		"top"=>50, 
		"left" =>110 );		


$jmob[5]= array(
		"name"=> "Агуара",
		"id" => 277,
		"top"=>43, 
		"left" =>115 );			
		
								
/////////////////////////////////////////////////	
//установки ловушек  простые 0 - сложнее - 10
// один итем с параметром  в секци

$lov[0]=array(
// magic - time min
		"timer_trap" => "16" // путы 
		);

$lov[1]=array(
// magic - time min
		"poison_trap" => "18" // яд 
		);

$lov[2]=array(
// magic - time min
		"stat_trap" => "16" // статы
		);

$lov[3]=array(
// magic - time min
		"poison_trap" => "20" // яд 
		);
		
///////////////////////////////////////////////
//Установка хилов
// один итем с параметром  в секци
$hils[0]=array(
		"H" => 60 // 60%
		);

$hils[1]=array(
		"H" => 75 // 75%
		);

$hils[2]=array(
		"H" => 80 // 80%
		);

$hils[3]=array(
		"H" => 85 // 85%
		);

$hils[4]=array(
		"H" => 90 // 90%
		);

$hils[5]=array(
		"H" => 99 // 99%
		);

/////////////////////////////////////////////////
// установки BOX-ов
//все что в коробках можно выносить из лабы
//лучше чередовать хороший и плохой!

$pbox[0]=array(
		"buter" => 105, 
		"gold" => 3013,
		"pvedro"=>4306
		);

$pbox[1]=array(
		"almaz" => 3006,
		"ugolk" => 3014, 		
		);

$pbox[2]=array(
		"gold" => 3009,
		"pvedro"=>4306,
		"antidot" => 4313		
		);
$pbox[3]=array(
		"rudas" => 3017,
		"antidot" => 4000
		);
$pbox[4]=array(
		"vostanovlenie_HP" => 246
//		"buter" => 105
		);

$pbox[5]=array(
		"items" => 3018, 
		"antidot" => 4313
		);
$pbox[6]=array(
		"granit" => 3012, 
		"gold" => 3021 
		);
$pbox[7]=array(
		"gold" => 3020,
		"items" => 3011
		);

/////////////////////////////////////////////////
	
//установки дропа из сундуков простые 0 - лучше - 10
// labonly - неподлежит выносу из лабы



$sund[0]=array(
		 "shop" => "shop",
		 "labonly" => "1",
		 "maxdur" => "1",
 		 "present"=> "",
		 "id" => "50176"
			);

$sund[1]=array(
		 "shop" => "shop",
		 "labonly" => "1",
		 "maxdur" => "3",
 		 "present"=> "",
		 "id" => "198"
			);
$sund[2]=array(
		 "shop" => "shop",
		 "labonly" => "1",
		 "maxdur" => "3",
 		 "present"=> "",
		 "id" => "203"
			);
			
$sund[3]=array(
		 "shop" => "shop",
		 "labonly" => "1",
		 "maxdur" => "3",
 		 "present"=> "",
		 "id" => "204"
			);			

$sund[4]=array(
		 "shop" => "shop",
		 "labonly" => "1",
		 "maxdur" => "3",
		 "present"=> "",
		 "id" => "196" 
			);
			
$sund[5]=array(
		 "shop" => "shop",
		 "labonly" => "1",
		 "maxdur" => "3",
 		 "present"=> "",
		 "id" => "205"
			);			

////настройки для повторного дропа
// [id] - итема и колво повторов
/// если итема в конфиге нет то безограничения

$reitem[210]=1;
$reitem[209]=1;
$reitem[208]=1;
$reitem[206]=1;
$reitem[205]=1;
$reitem[204]=1;
$reitem[203]=1;
$reitem[202]=1;
$reitem[201]=1; 
$reitem[200]=1;
$reitem[199]=1;
$reitem[198]=1;
$reitem[197]=1;
$reitem[196]=1;
$reitem[195]=1;

include ("labconfig_4.php"); // настройки дропа и ботов 				
?>