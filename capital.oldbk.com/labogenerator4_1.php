<?
if ($gen_map!=true) die();
//3D-1st 
$mobs=2; //% монстров
$lov=1;  //% ловушек
$lov_yama=1;  //% ловушек простых ям
$lov_yama_fire=1;  //% ловушек огненных ям
$lov_yama_ice=1;  //% ловушек ледяных ям
$hils=2;  //% хилок
$ahils=2;  //% хилок
$box=0.7;  //% ящиков пандоры

///////////////////
//Error_Reporting(0);



if ($mapid<0)
	{
	$mapid='1';
	}

///dir to save
$outmap='/www/capitalcity.oldbk.com/labmaps/'.$mapid.'.map';

function NettoieCellules($lab,$x2,$y2,$dim_x,$dim_y,$v1, $v2)
{
global $lab;

$lab[$x2][$y2]= $v1;
 if (($x2 > 0) and ($lab[$x2-1][$y2] == $v2))
	NettoieCellules($lab, $x2-1,$y2,$dim_x,$dim_y, $v1, $v2);

 if (($x2 < $dim_x-1) and ($lab[$x2+1][$y2] == $v2) )
  NettoieCellules($lab, $x2+1, $y2, $dim_x, $dim_y, $v1, $v2);

 if (($y2 > 0) and ($lab[$x2][$y2-1] == $v2))
  NettoieCellules($lab,$x2,$y2-1,$dim_x,$dim_y,$v1, $v2);


 if (($y2 < $dim_y-1) and ($lab[$x2][$y2+1] == $v2))
  NettoieCellules($lab, $x2, $y2+1, $dim_x, $dim_y, $v1,$v2);
}



/////////////////////////////////////////
$lab=array();
$MH=array();
$MV=array();

$dim=12; // размерность
$dim_x=$dim;
$dim_y=$dim;
$dim_Finale=2*$dim+1;

//mob and lov as count;
$k_mob=floor($dim_Finale*$dim_Finale/100*$mobs);
$k_lov=floor($dim_Finale*$dim_Finale/100*$lov);
$k_lov_yama=floor($dim_Finale*$dim_Finale/100*$lov_yama);
$k_lov_yama_fire=floor($dim_Finale*$dim_Finale/100*$lov_yama_fire);
$k_lov_yama_ice=floor($dim_Finale*$dim_Finale/100*$lov_yama_ice);

//$k_sund=floor($dim_Finale*$dim_Finale/100*$sund);
$k_hils=floor($dim_Finale*$dim_Finale/100*$hils);
$k_ahils=floor($dim_Finale*$dim_Finale/100*$hils);
$k_box=floor($dim_Finale*$dim_Finale/100*$box);


$inform="GMobs:$k_mob/Trap:$k_lov/Hils:$k_hils/AHils:$k_ahils/Box:$k_box/Yama:$k_lov_yama/FYama:$k_lov_yama_fire/iYama:$k_lov_yama_fire";

///randomize
srand();

$NbMurs=0;
$x1=0;$x2=0;$y1=0;$y2=0;


 for ($i=0;$i<=$dim_x-1;$i++) 
	for ($j=0;$j<=$dim_y-1;$j++)
	 $lab[$i][$j]=$i * $dim_y + $j;


 for ($i=0;$i<=$dim_x-1;$i++)
	for ($j=0;$j<=$dim_y-2;$j++)
	 $MH[$i][$j]=1;


	for ($i=0;$i<=$dim_x-2;$i++)
	 for ($j=0;$j<=$dim_y-1;$j++)
		$MV[$i][$j]= 1;


 while( $NbMurs <> ($dim_x*$dim_y)-1 )
{
	$continue=0;
	$rand= rand(0,2) + 1;
switch($rand)
{
case 1:
			$x1= rand(0,$dim_x);
			$y1= rand(0,$dim_y-1);
			if ($MH[$x1][$y1] == '1')
			{
			 $continue= 1;
			 $x2=$x1;
			 $y2=$y1+1;
			}
	 break;

case 2: 
			$x1= rand(0,$dim_x-1);
			$y1= rand(0,$dim_y);
			if ($MV[$x1][$y1] == '1')
			{
			 $continue=1;
			 $x2=$x1+1;
			 $y2=$y1;

			}
	break;

}


	if ($continue == 1)
	{
	 $v1= $lab[$x1][$y1];
	 $v2= $lab[$x2][$y2];
	 if ($v1 <> $v2) 
		{
		switch ($rand)
			{
			case 1: $MH[$x1][$y1]= 0; 	 break;
			case 2: $MV[$x1][$y1]= 0;	 break;
			}
 		NettoieCellules($lab, $x2, $y2, $dim_x, $dim_y, $v1, $v2);
		$NbMurs++;
		}
	}
} //while



 for ($i=1;$i<=$dim_Finale;$i++)
	for ($j=1;$j<=$dim_Finale;$j++)
	{
	$rendu[$i][$j]='O';
	}



 for ($i=1;$i<=$dim_Finale;$i++)
	$rendu[1][$i]= 'M';

 for ($i=1;$i<=$dim_Finale;$i++)
	$rendu[$dim_Finale][$i]='M';


 for ($i=1;$i<=$dim_Finale;$i++)
	{
	$rendu[$i][1]='M';
 	$rendu[$i][$dim_Finale]='M';
	}



 for ($i=0;$i<=$dim_x-2;$i++)
  for ($j=0;$j<=$dim_y-1;$j++)
   if ($MV[$i][$j] == 1) 
   {
    $rendu[2*($i+1)+1][$j*2+1]= 'M';
    $rendu[2*($i+1)+1][$j*2+2]= 'M';
    $rendu[2*($i+1)+1][$j*2+3]= 'M';
   }


 for ($i=0;$i<=$dim_x-1;$i++)
  for ($j=0;$j<=$dim_y-2;$j++)
    if ($MH[$i][$j] == 1)
	{
     $rendu[$i*2+1][($j+1)*2+1]= 'M';
     $rendu[$i*2+2][($j+1)*2+1]= 'M';
     $rendu[$i*2+3][($j+1)*2+1]= 'M';
	}


//in and out
$k1=(rand(0,$dim-1)+1)*2;
$k2=(rand(0,$dim-1)+1)*2;

$inlabx=$k1;
$inlaby=1;

//echo "Вход : $k1 / 1 <br>";
//echo "Выход: $k2 / $dim_Finale <br>";

 $rendu[$k1][1]= 'I';
 $rendu[$k2][$dim_Finale]= 'F';

/////////////////////////////////// центр

/////////////////////////////////// центр
	 for ($v=$dim-1;$v<=$dim+3;$v++)
	 	{
	 	//вверх периметра
	 	 $rendu[$dim-2][$v]='O';	 	
	 	 $rendu[$dim-1][$v]='N'; 
	 	//лев
	 	 $rendu[$v][$dim-1]='N'; 
	 	 $rendu[$v][$dim-2]='O'; 
	 	//прав
	 	 $rendu[$v][$dim+3]='N'; 	 	
	 	 $rendu[$v][$dim+4]='O'; 	 		 	 
	 	//низ периметра
	 	 $rendu[$dim+3][$v]='N'; 
	 	 $rendu[$dim+4][$v]='O'; 	 	 
	 	}

 $rendu[$dim][$dim]='M'; $rendu[$dim][$dim+1]='S'; $rendu[$dim][$dim+2]='M';
 $rendu[$dim+1][$dim]='S'; $rendu[$dim+1][$dim+1]='х'; $rendu[$dim+1][$dim+2]='5'; // 5- переход на след.этаж
 $rendu[$dim+2][$dim]='M'; $rendu[$dim+2][$dim+1]='Д'; $rendu[$dim+2][$dim+2]='M';
				 $rendu[$dim+3][$dim+1]='O'; 
$rendu[$dim-2][$dim-2]='O'; 
$rendu[$dim-2][$dim+3]='O'; 
$rendu[$dim+4][$dim-2]='O'; 
$rendu[$dim+4][$dim+4]='O'; 
//"крыльцо"
$rendu[$dim+5][$dim]='O'; 
$rendu[$dim+5][$dim+1]='N'; 
$rendu[$dim+5][$dim+2]='O'; 
$rendu[$dim+6][$dim]='O'; $rendu[$dim+6][$dim+1]='O'; $rendu[$dim+6][$dim+2]='O'; 
/////////////////////////////

$k_sund=0;	
$Z_door=0;
$D_door=0;
$X_door=0;
$C_door=0;
$J_mob=0;
$T_point=0;

 for ($roX=0;$roX<=$dim_Finale;$roX++)
  for ($roY=0;$roY<=$dim_Finale;$roY++)
      {
      if ($rendu[$roX][$roY]=='O')
                {
                  // 1 - Г
                    if ( ($rendu[$roX][$roY-1]=='O') AND ($rendu[$roX+1][$roY-1]=='O') AND ($rendu[$roX+2][$roY-1]=='O') AND
                          ($rendu[$roX-1][$roY]=='M') AND ($rendu[$roX-1][$roY-1]=='M') AND  ($rendu[$roX][$roY-2]=='M') AND
			  ($rendu[$roX+1][$roY-2]=='M') AND ($rendu[$roX+2][$roY-2]=='M') AND ($rendu[$roX+3][$roY-1]=='M') AND
			  ($rendu[$roX+2][$roY]=='M') AND ($rendu[$roX+1][$roY]=='M') )
                        {
                                   //Дверь тут
                                   $rendu[$roX][$roY]='Z';
                          $Z_door++;
                                    // проверим нужен ли охранник
                                    // охранника ставим без условий
                                   // if (($roY-1) >  ($dim_Finale/2)  )
                                    	{
                                    	$rendu[$roX][$roY-1]='J';
                                    	$J_mob++;
                                    	}
                                    
                                   
                                   //бокс тут
                                   $rendu[$roX+2][$roY-1]='S';
                                   $k_sund++;
                                   //чертим крипичную стенку
                                   $rendu[$roX-1][$roY]='N';
                                   $rendu[$roX-1][$roY-1]='N';
                                   $rendu[$roX][$roY-2]='N';
                                   $rendu[$roX+1][$roY-2]='N';
                                   $rendu[$roX+2][$roY-2]='N';
                                   $rendu[$roX+3][$roY-1]='N';
                                   $rendu[$roX+2][$roY]='N';
                                   $rendu[$roX+1][$roY]='N';
                                   //дополнительные кирпичики уголовые
                                   $rendu[$roX+3][$roY]='N';
                                   $rendu[$roX+3][$roY-2]='N'; 
                                   $rendu[$roX-1][$roY-2]='N';                                                                      

                        }
                    else
                    //2
                      if ( ($rendu[$roX][$roY+1]=='O') AND ($rendu[$roX+1][$roY+1]=='O') AND ($rendu[$roX+2][$roY+1]=='O') AND
                           ($rendu[$roX][$roY+2]=='M') AND ($rendu[$roX+1][$roY+2]=='M') AND ($rendu[$roX+2][$roY+2]=='M') AND
			   ($rendu[$roX+3][$roY+1]=='M') AND ($rendu[$roX+2][$roY]=='M') AND  ($rendu[$roX+1][$roY]=='M') AND
       			   ($rendu[$roX-1][$roY]=='M') AND ($rendu[$roX-1][$roY+1]=='M') )
                         {
                                   //Дверь тут
                                   $rendu[$roX][$roY]='X';
                           $X_door++;
                                   //бокс тут
                                   $rendu[$roX+2][$roY+1]='S';
                           $k_sund++;
                                   
                                   // проверим нужен ли охранник
                                    // если комната находится ближе к входу то ставим охранника за дверью
                                 //   if (($roY-1) >  ($dim_Finale/2)  )
                                    	{
                                    	$rendu[$roX][$roY+1]='J';
                                    	$J_mob++;                                    	
                                    	}
                                    	
                                   //чертим каменную стенку
                                   $rendu[$roX][$roY+2]='N';
                                   $rendu[$roX+1][$roY+2]='N';
                                   $rendu[$roX+2][$roY+2]='N';
                                   $rendu[$roX+3][$roY+1]='N';
                                   $rendu[$roX+2][$roY]='N';
                                   $rendu[$roX+1][$roY]='N';
       			   	   $rendu[$roX-1][$roY]='N';
       			   	   $rendu[$roX-1][$roY+1]='N';
       			   	    //дополнительные кирпичики уголовые
       			   	   $rendu[$roX+3][$roY]='N';
       			   	   $rendu[$roX+3][$roY+2]='N';
       			   	   $rendu[$roX-1][$roY+2]='N';       			   	          			   	   
                                   
                                   
                         }
                    else
                    //3
                        if ( ($rendu[$roX][$roY+1]=='O') AND ( $rendu[$roX-1][$roY+1]=='O') AND ($rendu[$roX-2][$roY+1]=='O') AND
                             ( $rendu[$roX-1][$roY]=='M') AND ( $rendu[$roX-2][$roY]=='M' ) AND  ( $rendu[$roX-3][$roY+1]=='M' ) AND
		             ( $rendu[$roX-2][$roY+2]=='M' ) AND ( $rendu[$roX-1][$roY+2]=='M' ) AND  ( $rendu[$roX][$roY+2]=='M' ) AND
  			     ( $rendu[$roX+1][$roY+1]=='M' ) AND  ( $rendu[$roX+1][$roY]=='M')  )
                         {
                                   //Дверь тут
                                   $rendu[$roX][$roY]='C';
                             $C_door++;
                                   //бокс тут
                                   $rendu[$roX-2][$roY+1]='S';
                          $k_sund++;                                   
                                    // проверим нужен ли охранник
                                    // если комната находится ближе к входу то ставим охранника за дверью
                                   // if (($roY-1) >  ($dim_Finale/2)  )
                                    	{
                                    	$rendu[$roX][$roY+1]='J';
                                    	$J_mob++;                                    	
                                    	}
                                   
                                   //стеночка
				$rendu[$roX-1][$roY]='N';
				$rendu[$roX-2][$roY]='N';
				$rendu[$roX-3][$roY+1]='N';
		                $rendu[$roX-2][$roY+2]='N';
		                $rendu[$roX-1][$roY+2]='N';
		                $rendu[$roX][$roY+2]='N';
		                $rendu[$roX+1][$roY+1]='N';
		                $rendu[$roX+1][$roY]='N';
                                //дополнительные кирпичики уголовые
                                   $rendu[$roX-3][$roY]='N';
                                   $rendu[$roX-3][$roY+2]='N';
                                   $rendu[$roX+1][$roY+2]='N';                                                                                                          
                                    
                         }
                    else
                      //4
                       if ( ($rendu[$roX][$roY-1]=='O') AND ($rendu[$roX-1][$roY-1]=='O') AND ($rendu[$roX-2][$roY-1]=='O') AND
                            ($rendu[$roX][$roY-2]=='M') AND  ($rendu[$roX-1][$roY-2]=='M') AND ($rendu[$roX-2][$roY-2]=='M') AND
		            ($rendu[$roX-3][$roY-1]=='M') AND ($rendu[$roX-2][$roY]=='M') AND ($rendu[$roX-1][$roY]=='M') AND
		            ($rendu[$roX+1][$roY]=='M') AND   ($rendu[$roX+1][$roY-1]=='M') )
                        {
                                   //Дверь тут
                                   $rendu[$roX][$roY]='D';
                            $D_door++;
                                   //бокс тут
                                   $rendu[$roX-2][$roY-1]='S';
                          $k_sund++;                                   
                                  // проверим нужен ли охранник
                                    // если комната находится ближе к входу то ставим охранника за дверью
                                 //   if (($roY-1) >  ($dim_Finale/2)  )
                                    	{
                                    	$rendu[$roX][$roY-1]='J';
                                    	$J_mob++;                                    	
                                    	}
                                   
                                   
                                   // стеночка
                                    $rendu[$roX][$roY-2]='N';
                                    $rendu[$roX-1][$roY-2]='N';
                                    $rendu[$roX-2][$roY-2]='N';
                                    $rendu[$roX-3][$roY-1]='N';
                                    $rendu[$roX-2][$roY]='N';
                                    $rendu[$roX-1][$roY]='N';
                                    $rendu[$roX+1][$roY]='N';
                                    $rendu[$roX+1][$roY-1]='N';
                                   // дополнительно
                                   $rendu[$roX+1][$roY-2]='N';
                                   $rendu[$roX-3][$roY]='N';
                                   $rendu[$roX-3][$roY-2]='N';
                                   
                        }
                        else
                        //5 - дальше идут горизонтальные
                          if ( ($rendu[$roX+1][$roY]=='O') AND ($rendu[$roX+1][$roY+1]=='O') AND ($rendu[$roX+1][$roY+2]=='O') AND
                               ($rendu[$roX][$roY-1]=='M') AND ( $rendu[$roX+1][$roY-1]=='M') AND ($rendu[$roX][$roY+1]=='M') AND
			       ($rendu[$roX][$roY+2]=='M') AND ( $rendu[$roX+1][$roY+3]=='M') AND ( $rendu[$roX+2][$roY+2]=='M') AND
			       ($rendu[$roX+2][$roY+1]=='M') AND ($rendu[$roX+2][$roY]=='M') )
                           {
                                   //Дверь тут
                                   $rendu[$roX][$roY]='Z';
                          $Z_door++;
                                   //бокс тут
                                   $rendu[$roX+1][$roY+2]='S';
                          $k_sund++;                                   
                                 // проверим нужен ли охранник
                                    // если комната находится ближе к входу то ставим охранника за дверью
                                   // if (($roY-1) >  ($dim_Finale/2)  )
                                    	{
                                    	$rendu[$roX+1][$roY]='J';
                                    	$J_mob++;                                    	
                                    	}
                                   
                                   // стенка
                                   $rendu[$roX][$roY-1]='N';
                                   $rendu[$roX+1][$roY-1]='N';
                                   $rendu[$roX][$roY+1]='N';
                                   $rendu[$roX][$roY+2]='N';
                                   $rendu[$roX+1][$roY+3]='N';
                                   $rendu[$roX+2][$roY+2]='N';
			           $rendu[$roX+2][$roY+1]='N';
			           $rendu[$roX+2][$roY]='N';
				// уголовые
				 $rendu[$roX][$roY+3]='N';				
				 $rendu[$roX+2][$roY+3]='N';				 
				 $rendu[$roX+2][$roY-1]='N';				 
                           }
                           else
                           //6 
                           if ( ( $rendu[$roX+1][$roY]=='O') AND ($rendu[$roX+1][$roY-1]=='O') AND ($rendu[$roX+1][$roY-2]=='O') AND
			        ( $rendu[$roX][$roY-1]=='M') AND ($rendu[$roX][$roY-2]=='M') AND ( $rendu[$roX][$roY+1]=='M') AND
			        ( $rendu[$roX+1][$roY+1]=='M') AND ($rendu[$roX+2][$roY]=='M') AND ($rendu[$roX+2][$roY-1]=='M') AND
			        ( $rendu[$roX+2][$roY-2]=='M') AND ($rendu[$roX+1][$roY-3]=='M') )
                             {
                                   //Дверь тут
                                   $rendu[$roX][$roY]='X';
                                $X_door++;
                                   //бокс тут
                                   $rendu[$roX+1][$roY-2]='S';
                          $k_sund++;                                   
                                    // проверим нужен ли охранник
                                    // если комната находится ближе к входу то ставим охранника за дверью
                                  //  if (($roY-1) >  ($dim_Finale/2)  )
                                    	{
                                    	$rendu[$roX+1][$roY]='J';
                                    	$J_mob++;                                    	
                                    	}
                                   
                                   
                                   //Cтенка
                                   $rendu[$roX][$roY-1]='N';
                                   $rendu[$roX][$roY-2]='N';
                                   $rendu[$roX][$roY+1]='N';
                                   $rendu[$roX+1][$roY+1]='N';
                                   $rendu[$roX+2][$roY]='N';
                                   $rendu[$roX+2][$roY-1]='N';
                                   $rendu[$roX+2][$roY-2]='N';
                                   $rendu[$roX+1][$roY-3]='N';
                                   // углы
                                   $rendu[$roX][$roY-3]='N';
                                   $rendu[$roX+2][$roY-3]='N';
                                   $rendu[$roX+2][$roY+1]='N';
                                   
                                   
                             }
                             else
                             //7
                              if ( ( $rendu[$roX-1][$roY]=='O') AND ( $rendu[$roX-1][$roY+1]=='O') AND ( $rendu[$roX-1][$roY+2]=='O') AND
                                   ($rendu[$roX][$roY-1]=='M') AND  ($rendu[$roX][$roY+1]=='M') AND  ($rendu[$roX][$roY+2]=='M') AND
			           ($rendu[$roX-1][$roY+3]=='M') AND ($rendu[$roX-1][$roY-1]=='M') AND ($rendu[$roX-2][$roY]=='M') AND
			           ($rendu[$roX-2][$roY+1]=='M') AND ($rendu[$roX-2][$roY+2]=='M')  )
                                {
                                   //Дверь тут
                                   $rendu[$roX][$roY]='C';
                              $C_door++;
                                   //бокс тут
                                   $rendu[$roX-1][$roY+2]='S';
                          $k_sund++;                                   
                              	// проверим нужен ли охранник
                                    // если комната находится ближе к входу то ставим охранника за дверью
                                  //  if (($roY-1) >  ($dim_Finale/2)  )
                                    	{
                                    	$rendu[$roX-1][$roY]='J';
                                    	$J_mob++;                                    	
                                    	}
                                   
                                   
                                   // Стенка
                                   $rendu[$roX][$roY-1]='N';
                                   $rendu[$roX][$roY+1]='N';
                                   $rendu[$roX][$roY+2]='N';
                                   $rendu[$roX-1][$roY+3]='N';
                                   $rendu[$roX-1][$roY-1]='N';
                                   $rendu[$roX-2][$roY]='N';
                                   $rendu[$roX-2][$roY+1]='N';
                                   $rendu[$roX-2][$roY+2]='N';
                                   // Углы
                                   $rendu[$roX-2][$roY-1]='N';
                                   $rendu[$roX-2][$roY+3]='N';
                                   $rendu[$roX][$roY+3]='N';                                                                                                         
                                   
                                   
                                }
                              else
                              //8
                                 if (($rendu[$roX-1][$roY]=='O') AND ($rendu[$roX-1][$roY-1]=='O') AND ($rendu[$roX-1][$roY-2]=='O') AND 
                                     ($rendu[$roX][$roY-1]=='M') AND ($rendu[$roX][$roY-2]=='M') AND ($rendu[$roX][$roY+1]=='M') AND
			             ($rendu[$roX-1][$roY+1]=='M') AND ($rendu[$roX-2][$roY]=='M') AND ($rendu[$roX-2][$roY-1]=='M') AND
			             ($rendu[$roX-2][$roY-2]=='M') AND ($rendu[$roX-1][$roY-3]=='M') )
                                 {
                                   //Дверь тут
                                   $rendu[$roX][$roY]='D';
                              $D_door++;
                                   //бокс тут
                                   $rendu[$roX-1][$roY-2]='S';
                          $k_sund++;                                   
	                              // проверим нужен ли охранник
                                    // если комната находится ближе к входу то ставим охранника за дверью
                                   // if (($roY-1) >  ($dim_Finale/2)  )
                                    	{
                                    	$rendu[$roX-1][$roY]='J';
                                    	$J_mob++;                                    	
                                    	}
                                   
                                   // стенка
                                   $rendu[$roX][$roY-1]='N';
                                   $rendu[$roX][$roY-2]='N';
                                   $rendu[$roX][$roY+1]='N';
			           $rendu[$roX-1][$roY+1]='N';
			           $rendu[$roX-2][$roY]='N';
			           $rendu[$roX-2][$roY-1]='N';
			           $rendu[$roX-2][$roY-2]='N';
			           $rendu[$roX-1][$roY-3]='N';
                                   // Углы
                                   $rendu[$roX][$roY-3]='N';
                                   $rendu[$roX-2][$roY-3]='N';
                                   $rendu[$roX-2][$roY+1]='N';                                                                      
                                   
                                 }

                
                
                }
                
      }

///////////////////////////////
// поиск тупиков П
for ($roX=0;$roX<=$dim_Finale;$roX++)
  for ($roY=0;$roY<=$dim_Finale;$roY++)
      {
      	if ($rendu[$roX][$roY]=='O')
                {
			      	if (
			      	     (($rendu[$roX][$roY-1]=='M') AND 
		 	     	     ($rendu[$roX][$roY+1]=='M') AND
		 	     	     ($rendu[$roX-1][$roY]=='M'))
		 	     	     OR
				     (($rendu[$roX][$roY-1]=='M') AND 
		 	     	     ($rendu[$roX][$roY+1]=='M') AND
		 	     	     ($rendu[$roX+1][$roY]=='M'))
		 	     	     OR
		 	     	     (($rendu[$roX-1][$roY]=='M') AND 
		 	     	     ($rendu[$roX+1][$roY]=='M') AND
		 	     	     ($rendu[$roX][$roY+1]=='M'))
		 	     	     OR
		 	     	     (($rendu[$roX-1][$roY]=='M') AND 
		 	     	     ($rendu[$roX+1][$roY]=='M') AND
		 	     	     ($rendu[$roX][$roY-1]=='M'))
		 	     	     )
		 	     	     {
				     $rendu[$roX][$roY]='T'; //поинт тупика типа П - маркеруем
				     $T_point++;
		 	     	     }		 	     	     
		 	     	     
                }
		//////

      }

////
/*
$repear_room=1; // количество комнат с рехтовкой
// поиск подходящего J-moba для установки в комнату рихтовальщика                
//JOS-моб-пусто-сундук
//перестраховка
$cancel=2500;
 while ($repear_room > 0) // разбрасываем ровно столько сколько дверей типа Z
	{
	$cancel--;	
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ( ($rendu[$rx][$ry]=='J') AND ($rendu[$rx][$ry+1]=='O') AND ($rendu[$rx][$ry+2]=='S') )
		{
		$rendu[$rx][$ry+1]='S';//переносим сундук ближе
	 	$rendu[$rx][$ry+2]='1'; //СИМВОЛ клетки с рихтовкой
	 	//JS1 - моб - сундук-рихтовка
		$repear_room--;
		}
	if ($cancel<0) {$repear_room=0;}
	
	}
///////////////////////////////////////
////
*/

/*
$change_room=1; // Старьёвщика 1 шт.
// поиск подходящего J-moba для установки                
//JOS-моб-пусто-сундук
//перестраховка
$cancel=2500;
 while ($change_room > 0) // разбрасываем 
	{
	$cancel--;	
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ( ($rendu[$rx][$ry]=='J') AND ($rendu[$rx][$ry+1]=='O') AND ($rendu[$rx][$ry+2]=='S') )
		{
	 	$rendu[$rx][$ry+1]='2'; //СИМВОЛ клетки с старьевщика 
		$change_room--;
		}
	if ($cancel<0) {$change_room=0;}
	
	}
	
//////////////////////////////////////////////////////////////////////////////
//перестраховка
$cancel=2500;
 while ($change_room > 0) // разбрасываем 
	{
	$cancel--;	
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ( ($rendu[$rx][$ry]=='S') AND ($rendu[$rx][$ry+1]=='O') AND ($rendu[$rx][$ry+2]=='J') )
		{
	 	$rendu[$rx][$ry+1]='2'; //СИМВОЛ клетки с старьевщика 
		$change_room--;
		}
	if ($cancel<0) {$change_room=0;}
	
	}
//////////////////////////////////////////////////////////////////////////////
*/

//стенка по контуру

 for ($i=1;$i<=$dim_Finale;$i++)
 {
  if ($rendu[1][$i]=='M')
   	{
	$rendu[1][$i]= 'N';
	}
  }

 for ($i=1;$i<=$dim_Finale;$i++)
 	{
 	if ($rendu[$dim_Finale][$i]=='M')
 		{
		$rendu[$dim_Finale][$i]='N';
		}
	}


 for ($i=1;$i<=$dim_Finale;$i++)
	{
	  if ($rendu[$i][1]=='M')
	  	{
		$rendu[$i][1]='N';
		}
	  if ($rendu[$i][$dim_Finale]=='M')
		{
	 	$rendu[$i][$dim_Finale]='N';
	 	}
	}

////////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////
// Хрустальные стены + одно направленные проходы
$Al=3;//кол.проходов  ->
$Er=3;//кол.проходов  <-

for ($roX=0;$roX<=$dim_Finale;$roX++)
  for ($roY=0;$roY<=$dim_Finale;$roY++)
      {
      if ($rendu[$roX][$roY]=='M')
                 {
                 	//если стандартная стеночка
                 	// ищим стенку динной 5 клеток - веретиально
                 	if (($rendu[$roX][$roY-1]=='M')
                 	 AND ($rendu[$roX][$roY-2]=='M')
                 	 AND ($rendu[$roX][$roY+1]=='M')
                 	 AND ($rendu[$roX][$roY+2]=='M')
                 	 AND ($rendu[$roX+1][$roY]=='O')
                 	 AND ($rendu[$roX-1][$roY]=='O') )
                                 {
                                 //ставим хрустльную стенку = зелень
                                 $RLR=mt_rand(0,3);
                                 if (($roX >= 24) and ($Al>0) and ($RLR=1))
                                 	{
                                 	$Al--;
                                 	$rendu[$roX][$roY]='A';//огонь со стороны где нельзя пройти =можно зажечь факел
                                 	}
                                 else
                                 if (($roX < 24) and ($Er>0) and ($RLR=2))                                 	
                                 	{
                                 	$Er--;
                                 	$rendu[$roX][$roY]='E';//вода где нельзя пройти = можно набрать воды
                                 	}
                                   else
                                     {
                                     	
                                     	if (mt_rand(1,2)==2)
                                     	{
	                                $rendu[$roX][$roY]='G'; // для ножниц
        	                        }
        	                        else
        	                        {
	                                 $rendu[$roX][$roY]='ж'; //лед для молотка кирки
	                                 }
                                     }
                                 }
                        else
                        // или горизонтально
		   	 if (($rendu[$roX-1][$roY]=='M')
                 	 AND ($rendu[$roX-2][$roY]=='M')
                 	 AND ($rendu[$roX+1][$roY]=='M')
                 	 AND ($rendu[$roX+2][$roY]=='M')
                 	 AND ($rendu[$roX][$roY-1]=='O')
                 	 AND ($rendu[$roX][$roY+1]=='O') )
                                 {
                                 //ставим стенку
                             	if (mt_rand(1,2)==2)
                                     	{
	                                $rendu[$roX][$roY]='G'; // зелень для ножниц
        	                        }
        	                        else
        	                        {
	                                 $rendu[$roX][$roY]='ж'; //лед для молотка
	                                 }
                                 }
                        
		}
      
      }



$inform.='/SUND:'.$k_sund.'/Z-door:'.$Z_door.'/X-door:'.$X_door.'/D-door:'.$D_door.'/C-door:'.$C_door.'/J-mobs:'.$J_mob.'/T-point:'.$T_point;
////////////////////////////////////
//уменьшаем кол. ключей 

$Z_door=(int)($Z_door*0.67);
$X_door=(int)($X_door*0.67);
$D_door=(int)($D_door*0.67);
$C_door=(int)($C_door*0.67);

// ищим П поинты
for ($roX=0;$roX<=$dim_Finale;$roX++)
  for ($roY=0;$roY<=$dim_Finale;$roY++)
      {
      	if ($rendu[$roX][$roY]=='T')
      		{
      		// разбрасываем сундучки в тупиках
      		$BBOX=array(); //масив из которого будет разброс
      		$bkol=0;
      		// если есть то добавим в масив для разброса т.е. учавствеут в разбросе      		
			if ($Z_door>0) 	{  $BBOX[]='W'; $bkol++; }
			if ($X_door>0)  {  $BBOX[]='Y';	$bkol++; }
			if ($D_door>0)  {  $BBOX[]='L';	$bkol++; }
			if ($C_door>0)  {  $BBOX[]='K';	$bkol++; }

		if ($bkol >0) // если в разбросе есть элементы
			{
			$Brand=mt_rand(0,$bkol-1);
			$rendu[$roX][$roY]=$BBOX[$Brand]; // разбрасываем
			
			//отнимаем от соотвествующего счетчика 
			if ($BBOX[$Brand]=='W') {$Z_door--;}
			elseif ($BBOX[$Brand]=='Y') {$X_door--;}
			elseif ($BBOX[$Brand]=='L') {$D_door--;}
			elseif ($BBOX[$Brand]=='K') {$C_door--;}
			}
			else
			{
			//масив разброса пустой 
			//убираем T = >
			$rendu[$roX][$roY]='O';
			}

      		}
     }
     
/// FIX - разброс остальный сундучков если они остались
/// rand Z-dox-key

 while ($Z_door > 0) // разбрасываем ровно столько сколько дверей типа Z
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ( ($rendu[$rx][$ry]=='O') AND
	     ($rendu[$rx][$ry+1]=='O') AND
	     ($rendu[$rx][$ry-1]=='O') )
		{
	 	$rendu[$rx][$ry]='W'; 
		$Z_door--;
		}
	}

/// rand dox-key
 while ($X_door > 0) // разбрасываем ровно столько сколько дверей типа Z
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ( ($rendu[$rx][$ry]=='O') AND
	     ($rendu[$rx][$ry+1]=='O') AND
	     ($rendu[$rx][$ry-1]=='O') )
		{
	 	$rendu[$rx][$ry]='Y'; 
		$X_door--;
		}
	}	
	
/// rand dox-key
 while ($D_door > 0) // разбрасываем ровно столько сколько дверей типа Z
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;// D-dox-key ключница разбрасывает где угодно
	if ( ($rendu[$rx][$ry]=='O') AND
	     ($rendu[$rx][$ry+1]=='O') AND
	     ($rendu[$rx][$ry-1]=='O') )
		{
	 	$rendu[$rx][$ry]='L'; 
		$D_door--;
		}
	}	

      
/// rand dox-key
 while ($C_door > 0) // разбрасываем ровно столько сколько дверей типа Z
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;// C-dox-key ключница разбрасывает где угодно
	if ( ($rendu[$rx][$ry]=='O') AND
	     ($rendu[$rx][$ry+1]=='O') AND
	     ($rendu[$rx][$ry-1]=='O') )
		{
	 	$rendu[$rx][$ry]='K'; 
		$C_door--;
		}
	}	      
/////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////
/// rand mobs
//echo "Монстров: $k_mob  - RED <br>";
 while ($k_mob > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='R'; 
		$k_mob--;

		}
	}

//////////////
// поиск перекрестков - разброс темных зон
for ($roX=0;$roX<=$dim_Finale;$roX++)
  for ($roY=0;$roY<=$dim_Finale;$roY++)
      {
      	if (($rendu[$roX][$roY]=='O') AND ($rendu[$roX-1][$roY]=='O') AND ($rendu[$roX+1][$roY]=='O') and ($rendu[$roX][$roY-1]=='O') and ($rendu[$roX][$roY+1]=='O') )
                {
		$rendu[$roX][$roY]='р'; 
                }
		//////

      }

/// rand lovs
//echo "Ловушек ям: $k_lov_yama  - Blue <br>";
 while ($k_lov_yama > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='я'; 
		$k_lov_yama--;

		}
	}

 while ($k_lov_yama_fire > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='ф'; 
		$k_lov_yama_fire--;

		}
	}

 while ($k_lov_yama_ice > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='ч'; 
		$k_lov_yama_ice--;

		}
	}

/// rand lovs
//echo "Ловушек: $k_lov  - Blue <br>";
 while ($k_lov > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='B'; 
		$k_lov--;

		}
	}
//////////////////

//////////////////
//echo "Хилки $k_hils  <br>";
 while ($k_hils > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='H'; 
		$k_hils--;

		}
	}
//антидоты
 while ($k_ahils > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='т'; 
		$k_ahils--;

		}
	}
/////////////////////////


//////////////////
//echo "Хилки $k_box  <br>";
 while ($k_box > 0)
	{
	$rx=(rand(0,$dim-1)+1)*2;
	$ry=(rand(0,$dim-1)+1)*2;
	if ($rendu[$rx][$ry]=='O')
		{
	 	$rendu[$rx][$ry]='P'; 
		$k_box--;

		}
	}




$savemap = fopen($outmap,"w");
fwrite($savemap,"//map lab id:".$mapid." gen date:".date("d-m-Y H:m:s")."/".$inform."  \n"); // clear line

//echo "<pre>";
 for ($i=1;$i<=$dim_Finale;$i++)
	{
 	$linte=''; // for visualimage
	$fline=' ';
	for ($j=1;$j<=$dim_Finale;$j++)
		{
		//first write to file
		$fline=$fline.$rendu[$i][$j];

	
	
		if ($rendu[$i][$j]=='I') { $rendu[$i][$j]='<img src=o.gif>'; } // вход
		if ($rendu[$i][$j]=='F') { $rendu[$i][$j]='<img src=o.gif>'; } // Выход
		if ($rendu[$i][$j]=='O') { $rendu[$i][$j]='<img src=o.gif>'; } // проход 
		if ($rendu[$i][$j]=='M') { $rendu[$i][$j]='<img src=m.gif>'; } // стенка
		if ($rendu[$i][$j]=='N') { $rendu[$i][$j]='<img src=n.gif>'; } // стенка-непроходимая
		if ($rendu[$i][$j]=='R') { $rendu[$i][$j]='<img src=r.gif>'; } // mob
		if ($rendu[$i][$j]=='J') { $rendu[$i][$j]='<img src=j.gif>'; } // Сильный - mob
		if ($rendu[$i][$j]=='х') { $rendu[$i][$j]='<img src=j.gif>'; } //  босс
		if ($rendu[$i][$j]=='B') { $rendu[$i][$j]='<img src=b.gif>'; } // ловушка
		if ($rendu[$i][$j]=='S') { $rendu[$i][$j]='<img src=s.gif>'; } // сундук
		if ($rendu[$i][$j]=='р') { $rendu[$i][$j]='<img src=tz.gif>'; } //  темная зона на перекрестке
		
		if ($rendu[$i][$j]=='H') { $rendu[$i][$j]='<img src=h.gif>'; } // хилка
		if ($rendu[$i][$j]=='т') { $rendu[$i][$j]='<img src=ah.gif>'; } // антидот
		if ($rendu[$i][$j]=='P') { $rendu[$i][$j]='<img src=p.gif>'; } // пандоры
		if ($rendu[$i][$j]=='D') { $rendu[$i][$j]='<img src=d.gif>'; } // дверь d - зеленая
		if ($rendu[$i][$j]=='Д') { $rendu[$i][$j]='<img src=d.gif>'; } // дверь d - зеленая		
		if ($rendu[$i][$j]=='Z') { $rendu[$i][$j]='<img src=z.gif>'; } // дверь z - серебристая	
		if ($rendu[$i][$j]=='X') { $rendu[$i][$j]='<img src=x.gif>'; } // дверь x - красненькая
		if ($rendu[$i][$j]=='C') { $rendu[$i][$j]='<img src=c.gif>'; } // дверь c - коричневая
		if ($rendu[$i][$j]=='L') { $rendu[$i][$j]='<img src=l.gif>'; } // сундучек с ключиком d
		if ($rendu[$i][$j]=='W') { $rendu[$i][$j]='<img src=w.gif>'; } // сундучек с ключиком z
		if ($rendu[$i][$j]=='Y') { $rendu[$i][$j]='<img src=y.gif>'; } // сундучек с ключиком x
		if ($rendu[$i][$j]=='K') { $rendu[$i][$j]='<img src=k.gif>'; } // сундучек с ключиком с
		if ($rendu[$i][$j]=='T') { $rendu[$i][$j]='<img src=t.gif>'; } // служебный тупик типа П
		if ($rendu[$i][$j]=='G') { $rendu[$i][$j]='<img src=g.gif>'; } // хрустальная стенка - красненькая
		if ($rendu[$i][$j]=='Q') { $rendu[$i][$j]='<img src=q.gif>'; } // квестовый предмет
		if ($rendu[$i][$j]=='V') { $rendu[$i][$j]='<img src=v.gif>'; } // квестовый монстр
		if ($rendu[$i][$j]=='A') { $rendu[$i][$j]='<img src=a.gif>'; } // проход с лева на право или сверху в низ
		if ($rendu[$i][$j]=='E') { $rendu[$i][$j]='<img src=e.gif>'; } // проход с право на лево или низ вверх

		if ($rendu[$i][$j]=='1') { $rendu[$i][$j]='<img src=1.gif>'; } // рихтовка	
		if ($rendu[$i][$j]=='2') { $rendu[$i][$j]='<img src=2.gif>'; } // обменник
		if ($rendu[$i][$j]=='5') { $rendu[$i][$j]='<img src=5.gif>'; } //  телепорт на др. этаж
		
            $linte=$linte.$rendu[$i][$j];
            }
	fwrite($savemap, $fline."\n"); // save line
	//echo  $linte."<br>\n";
	}

//echo "</pre>";
fclose($savemap);

?>