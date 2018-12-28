<?
		if (!($_SESSION['uid'] >0)) header("Location: index.php");

		if ($INinlude!=true)
			{
			$mapid=(int)$_POST["target"];
			}

		echo "Карта:".$mapid;
		echo "<br>";
		$map=file('/www/capitalcity.oldbk.com/labmaps/'.$mapid.'.map');
$GX=strlen($map[1]);
if (($GX > 58) or ($GX ==27) ) {$LAB='4';} 
elseif ($GX > 27) {$LAB='2';} else {$LAB='';}

if ($user['id']==14897)
	{
	echo $GX."/".$LAB."<br>";
	}

		if ($INinlude!=true)
			{
			$mapid=(int)$_POST["target"];
			 if ($LAB=='4')
			 	{
				 	if ($GX ==27)
				 	{
				 	$floor=0;					 	
				 	$get_flr=mysql_fetch_array(mysql_query("select * from labirint_users where map='{$mapid}'  limit 1"));
					 	if ($get_flr['flr']>0)
					 		{
							$floor=$get_flr['flr'];
					 		}
						
						if (($floor>=1) and ($floor<=4))
						{
						include ("labconfig_4_".(int)$floor.".php"); // настройки			 	
						}
						else
						{
						include ("labconfig_s3d.php"); // настройки			 							
						}
				 	}
			 		else
			 		{
					include ("labconfig_s3d.php"); // настройки			 	
					}
			 	}
			 	else
			 	{
				include ("labconfig_s2.php"); // настройки
				}
			}

//////////////////////////////////////////////////////////////////////////		
/// Load J-mobs
/// нумератор - обзыватель - назначатель ид
$stopt=count($map);
$JMOB=array();
$JMOB_id=array();

$SJMOB=array();
$SJMOB_id=array();


$jm_count=0;
$sjm_count=0;
$jmmob=count($jmob); // сколько штук
$sjmmob=count($sjmob); // сколько штук
//load
for ($ii=1;$ii<$stopt;$ii++)
 for ($jj=1;$jj<$stopt;$jj++)
	{
	if ($map[$ii][$jj]=='J')
		{
		$jm_count++;
		if ($jm_count>$jmmob) { $jm_count=1;}
		$JMOB[$ii][$jj]=$jmob[$jm_count][name];
		$JMOB_id[$ii][$jj]=$jmob[$jm_count][id];
		}
		else	if ($map[$ii][$jj]=='х')
		{
		$sjm_count++;
		if ($sjm_count>$sjmmob) { $sjm_count=1;}
		$SJMOB[$ii][$jj]=$sjmob[$sjm_count][name];
		$SJMOB_id[$ii][$jj]=$sjmob[$sjm_count][id];
		}
	}
		

///////////////////LOAD MY TEAM ////////////////////////////////////////////////////////////////////////////
$team=mysql_query("SELECT * from `labirint_users` where `map`='".$mapid."' ;  ");

		$TA=array();
		$Tcount=0;
		while ($reamrow = mysql_fetch_array($team)) 
		{
		$Tcount++;
			$Displ_team.=" ".nick33($reamrow[owner])." X[".$reamrow[x]."]/Y[".$reamrow[y]."] <br>";
			$TA[$reamrow[x]][$reamrow[y]].=nick7($reamrow[owner])." ";
		}

////////////////////LOAD ITEMS POZ ///////////////////////////////////////////////////////////////////////
$items=mysql_query("SELECT * FROM `labirint_items` WHERE  (`owner`=0 OR `owner`='{$user[id]}')  and  `active`=1 and  `map`='".$mapid."'  ;");
// надо грузить весь масив итемов ВСЕх...+
			$Aitems=array();
			$ukaz[1]='п';
			$ukaz[2]='л';
			$ukaz[3]='в';
			$ukaz[4]='н';
			while ($row = mysql_fetch_array($items)) 
			{
			 if ($row[item]=='T')
			 	{
 				$map[$row[x]][$row[y]]='T';
			 	}
			 else
			 if ($row[item]=='9')
			 	{
 				$map[$row[x]][$row[y]]=$ukaz[$row[val]]; //подставляем нужную букву
			 	}			 	
			 	else
			 	{
				$Aitems[$row[x]][$row[y]]=$row[item];
				$Aitems_val[$row[x]][$row[y]]=$row[val];
				$Aitems_count[$row[x]][$row[y]]=$row[count];
				}
			}

?>
<html>
<head>
	<link rel=stylesheet type="text/css" href="i/main.css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<style>
		body {
			background-repeat: no-repeat;
			background-position: top right;
		}
		.INPUT {
			width:50px; height:50px;
			BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
		}
	</style>
	


</head>
<body leftmargin=5 topmargin=0 marginwidth=0 marginheight=0 bgcolor=#e2e0e0>
<?		
		
///Paint map prep
////////////////////////////////////////////////////////////////////////////////
//set user poz to mass
///Организация видимого квадрата карты////////////////
$disp=10-5;
$dispy=10-5;
if ($disp < 1) {$disp=1;}
if ($dispy < 1) {$dispy=1;}
if ($disp < 1) {$disp=1;}
if ($dispy < 1) {$dispy=1;}
if ($dispy > strlen($map[1])-12)  {$dispy=strlen($map[1])-12;} //ограничитель экрана карты по y
if ($disp > count($map)-11) 	{ $disp=count($map)-11;  } //ограничитель экрана карты по х
$dispfin=$disp+10; //квадрат видимый х фин
$dispfiny=$dispy+10; //квадрат видимый y фин
/////////////////////////////////////////////

// for ($i=$disp;$i<=$dispfin;$i++)




 for ($i=1;$i<=$GX;$i++)
	{
 	$linte=""; // for visualimage
//	for ($j=$dispy;$j<=$dispfiny;$j++)
	for ($j=1;$j<=$GX;$j++)
		{


if ($LAB==4)
		{
		// миникарта для 3д лабы

switch($map[$i][$j])
			{

case "I":
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/os'.$LAB.'.gif title="Вход" alt="Вход">';
			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">';
			}
		 } // вход
break;

case "F":
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/of'.$LAB.'.gif title="Выход" alt="Выход">';
			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">';
			}
		 } // вЫход
break;

case "O":
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>';
			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">';
			}
		 } // проход
break;

case "Q":
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>';
			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif  title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">';
			}
		 } // проход 2 для квеста
break;

case 'M' :
		{
		$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/m'.$LAB.'.gif>';
		} // стенка
break;


case 'N' :
		{
		$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/n'.$LAB.'.gif>';
		} // стенка
break;

case 'R' :
		{
		 if ($TA[$i][$j]=='')
			{
			if ($Aitems[$i][$j]!='R')
					{
					  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/r'.$LAB.'.gif title="Опасная зона" alt="Опасная зона">'; // активный монстр
					}
					else
					{
					  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // труп монстра
					}

			}
			else
			{
				if ($Aitems[$i][$j]!='R')
					{
					$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/rt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый тип у монстра
					}
					else
					{
					 if ($Aitems_val[$i][$j]>0)
						{
						$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/rt'.$LAB.'.gif title="'.$TA[$i][$j].' - в бою" alt="'.$TA[$i][$j].' - в бою">'; // левый тип у монстра-в бою
						}
						else
						{
						$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый тип у монстра-трупа
						}


					}
			}
		 } // mob
break;

case 'х' :
		{
		 if ($TA[$i][$j]=='')
			{
			if ($Aitems[$i][$j]!='х')
					{
					  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/j'.$LAB.'.gif title="'.$SJMOB[$i][$j].'" alt="'.$SJMOB[$i][$j].'">'; // активный монстр
					/*echo "FFFFF";
					echo $i;
					echo "/";
					echo $j;
				        echo $Aitems[$i][$j];
       					echo "<br>";	*/
					}
					else
					{
					  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // труп монстра
					}

			}
			else
			{
				if ($Aitems[$i][$j]!='х')
					{
					$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/rt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый тип у монстра
					}
					else
					{
					 if ($Aitems_val[$i][$j]>0)
						{
						$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/rt'.$LAB.'.gif title="'.$TA[$i][$j].' - в бою" alt="'.$TA[$i][$j].' - в бою">'; // левый тип у монстра-в бою
						}
						else
						{
						$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый тип у монстра-трупа
						}


					}
			}
		 } // mob
break;

case 'J' :
		{
		 if ($TA[$i][$j]=='')
			{
			if ($Aitems[$i][$j]!='J')
					{
					  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/j'.$LAB.'.gif title="'.$JMOB[$i][$j].'" alt="'.$JMOB[$i][$j].'">'; // активный монстр
					/*echo "FFFFF";
					echo $i;
					echo "/";
					echo $j;
				        echo $Aitems[$i][$j];
       					echo "<br>";	*/
					}
					else
					{
					  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // труп монстра
					}

			}
			else
			{
				if ($Aitems[$i][$j]!='J')
					{
					$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/rt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый тип у монстра
					}
					else
					{
					 if ($Aitems_val[$i][$j]>0)
						{
						$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/rt'.$LAB.'.gif title="'.$TA[$i][$j].' - в бою" alt="'.$TA[$i][$j].' - в бою">'; // левый тип у монстра-в бою
						}
						else
						{
						$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый тип у монстра-трупа
						}


					}
			}
		 } // mob
break;


case 'B' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='B')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/b'.$LAB.'.gif alt="Ловушка" title="Ловушка" >'; // активная ловушка
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная ловушка
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/bt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в ловушке
			}
		 } // ловушка

break;

case 'я' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='я')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/b'.$LAB.'.gif alt="Яма-Ловушка" title="Яма-Ловушка" >'; // активная ловушка
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная ловушка
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/bt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в ловушке
			}
		 } // ловушка

break;

case 'ф' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='ф')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/fire'.$LAB.'.gif alt="Огненная Яма-Ловушка" title="Огненная Яма-Ловушка" >'; // активная ловушка
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная ловушка
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/bt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в ловушке
			}
		 } // ловушка

break;

case 'ч' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='ч')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ice'.$LAB.'.gif alt="Ледяная Яма-Ловушка" title="Ледяная Яма-Ловушка" >'; // активная ловушка
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная ловушка
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/bt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в ловушке
			}
		 } // ловушка

break;

case 'G' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='G')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/g'.$LAB.'.gif>'; 
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип
			}
		 } //

break;

case 'ж' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='ж')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/gg'.$LAB.'.gif>'; //ледяная
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип
			}
		 } //

break;

case 'в' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='в')
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/'.$ukazkam[$_SESSION['looklab']][3].'.gif alt="Направление вверх" title="Направление вверх" >'; // активная хрустальная стенка
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип
			}
		 } //

break;

case 'н' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='н')
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/'.$ukazkam[$_SESSION['looklab']][4].'.gif alt="Направление вниз" title="Направление вниз" >'; // активная хрустальная стенка
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип
			}
		 } //

break;

case 'л' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='л')
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/'.$ukazkam[$_SESSION['looklab']][2].'.gif alt="Направление налево" title="Направление налево" >'; // активная хрустальная стенка
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип
			}
		 } //

break;

case 'п' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='п')
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/'.$ukazkam[$_SESSION['looklab']][1].'.gif alt="Направление направо" title="Направление направо" >'; // активная хрустальная стенка
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип
			}
		 } //

break;

case '1' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='1')
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/kz.gif alt="Кузня" title="Кузня" >'; // Кузня
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип
			}
		 } //

break;

case '2' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='2')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/2'.$LAB.'.gif alt="Старьевщик" title="Старьевщик" >'; // старьевщик

					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип
			}
		 } //

break;

case '5' : // переход на сл.этаж
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='5')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/5'.$LAB.'.gif alt="Переход на следующий этаж" title="Переход на следующий этаж" >'; // старьевщик					 					 

					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип
			}
		 } //

break;



case 'A' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='A')
					{
					if (($_SESSION['looklab']==90) OR ($_SESSION['looklab']==180) )
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/e'.$LAB.'.gif  >';					
					}
					else
						{
						 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/a'.$LAB.'.gif  >';
						 }
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип
			}
		 } //

break;

case 'E' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='E')
					{
					if (($_SESSION['looklab']==90) OR ($_SESSION['looklab']==180) )
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/a'.$LAB.'.gif  >'; 					
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/e'.$LAB.'.gif  >'; 
					 }
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип
			}
		 } //

break;


case 'D' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='D')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/d'.$LAB.'.gif alt="Дверь" title="Дверь" >'; // активная
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в двери
			}
		 } //

break;

case 'Д' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='Д')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/d'.$LAB.'.gif alt="Дверь" title="Дверь" >'; // активная
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в двери
			}
		 } //

break;

case 'T' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='T')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/t'.$LAB.'.gif alt="Портал X:'.$i.'/Y:'.$j.'" title="Портал X:'.$i.'/Y:'.$j.'" >'; // активная
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в
			}
		 } //

break;

case 'X' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='X')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/x'.$LAB.'.gif alt="Дверь" title="Дверь" >'; // активная
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в двери
			}
		 } //

break;

case 'Z' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='Z')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/z'.$LAB.'.gif alt="Дверь" title="Дверь" >'; // активная
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в двери
			}
		 } //

break;

case 'C' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='C')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/c'.$LAB.'.gif alt="Дверь" title="Дверь" >'; // активная
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в двери
			}
		 } //

break;


case 'P' :
		{
		 if ($TA[$i][$j]=='')
			{
				if (($Aitems[$i][$j]!='P') AND ($Aitems[$i][$j]!='з') )
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/p'.$LAB.'.gif alt="Ящик Пандоры" title="Ящик Пандоры">'; // активная коробка
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная ловушка
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/pt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в ловушке
			}
		 } // BOX P) Пандоры

break;


case 'S' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='S')
							{
							$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/s'.$LAB.'.gif alt="Сундук" title="Сундук">' ; //полный
							}
							else
							{
							$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>' ;  //пустой сундук
							}
			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/st'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый чар копается в сундуке
			}
		 } // сундук

break;

case 'W' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='W')
							{
							$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/w'.$LAB.'.gif alt="Ларец" title="Ларец">' ; //полный
							}
							else
							{
							$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>' ;  //пустой сундук
							}
			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый чар копается в сундуке
			}
		 } // сундук

break;

case 'Y' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='Y')
							{
							$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/y'.$LAB.'.gif alt="Ларец" title="Ларец">' ; //полный
							}
							else
							{
							$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>' ;  //пустой сундук
							}
			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый чар копается в сундуке
			}
		 } // сундук

break;

case 'L' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='L')
							{
							$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/l'.$LAB.'.gif alt="Ларец" title="Ларец">' ; //полный
							}
							else
							{
							$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>' ;  //пустой сундук
							}
			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый чар копается в сундуке
			}
		 } // сундук

break;

case 'K' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='K')
							{
							$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/k'.$LAB.'.gif alt="Ларец" title="Ларец">' ; //полный
							}
							else
							{
							$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>' ;  //пустой сундук
							}
			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый чар копается в сундуке
			}
		 } // сундук

break;

case 'т' :
		{
		 if ($TA[$i][$j]=='')
			{
					if ($Aitems[$i][$j]!='т')
							{
							  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ah'.$LAB.'.gif alt="Антидот" title="Антидот" >';
							}
							else
							{
							  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>';	// пустая лилка
							}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ht'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый чар копается в хилке
			}
		 } 

break;

case 'р' :
		{
		 if ($TA[$i][$j]=='')
			{
					if ($Aitems[$i][$j]!='р')
							{
							  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/tz'.$LAB.'.gif  alt="Темный перекресток" title="Темный перекресток"  >';
							}
							else
							{
							  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>';	// пустая лилка
							}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый чар копается в хилке
			}
		 } 

break;



case 'H' :
		{
		 if ($TA[$i][$j]=='')
			{
					if ($Aitems[$i][$j]!='H')
							{
							  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/h'.$LAB.'.gif alt="Живая Вода" title="Живая Вода" >'; // хилка
							}
							else
							{
							  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>';	// пустая лилка
							}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ht'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый чар копается в хилке
			}
		 } // хилка

break;

case 'U' :
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/u'.$LAB.'.gif title="Я" alt="Я">'; // Я на карте
			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ut'.$LAB.'.gif title="Я ,'.$TA[$i][$j].'" alt="Я, '.$TA[$i][$j].'">'; //я и еще ктото из тимы
			}
		 } // user
break;




			}		
		
		
		}
else
			{	
switch($map[$i][$j])
			{

case "I":
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://i.oldbk.com/llabb/os'.$LAB.'.gif title="Вход" alt="Вход">';
			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">';		
			}
		 } // вход
break;


case "F":
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://i.oldbk.com/llabb/of'.$LAB.'.gif title="Выход" alt="Выход">';
			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">';		
			}
		 } // вЫход
break;

case "O":
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>';
			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">';		
			}
		 } // проход
break;

case 'M' :  
		{
		$linte.='<img src=http://i.oldbk.com/llabb/m'.$LAB.'.gif>';
		} // стенка
break;


case 'N' :  
		{
		$linte.='<img src=http://i.oldbk.com/llabb/n.gif>';
		} // стенка
break;

case 'R' : 	
		{
		 if ($TA[$i][$j]=='')
			{
			if ($Aitems[$i][$j]!='R') 
					{
					  $linte.='<img src=http://i.oldbk.com/llabb/r'.$LAB.'.gif title="Опасная зона" alt="Опасная зона">'; // активный монстр
					}
					else
					{
					  $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // труп монстра
					}
			
			}
			else 
			{
				if ($Aitems[$i][$j]!='R') 
					{
					$linte.='<img src=http://i.oldbk.com/llabb/rt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый тип у монстра 
					}
					else 
					{
					 if ($Aitems_val[$i][$j]>0) 
						{
						$linte.='<img src=http://i.oldbk.com/llabb/rt'.$LAB.'.gif title="'.$TA[$i][$j].' - в бою" alt="'.$TA[$i][$j].' - в бою">'; // левый тип у монстра-в бою
						}
						else
						{
						$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый тип у монстра-трупа		
						}

				
					}
			}
		 } // mob
break;



case 'J' : 	
		{
		 if ($TA[$i][$j]=='')
			{
			if ($Aitems[$i][$j]!='J') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/j'.$LAB.'.gif title="'.$JMOB[$i][$j].'" alt="'.$JMOB[$i][$j].'">'; // активный монстр
					}
					else
					{
					  $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // труп монстра
					}
			
			}
			else 
			{
				if ($Aitems[$i][$j]!='J') 
					{
					$linte.='<img src=http://i.oldbk.com/llabb/rt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый тип у монстра 
					}
					else 
					{
					 if ($Aitems_val[$i][$j]>0) 
						{
						$linte.='<img src=http://i.oldbk.com/llabb/rt'.$LAB.'.gif title="'.$TA[$i][$j].' - в бою" alt="'.$TA[$i][$j].' - в бою">'; // левый тип у монстра-в бою
						}
						else
						{
						$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый тип у монстра-трупа		
						}

				
					}
			}
		 } // mob
break;


case 'B' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='B') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/b'.$LAB.'.gif alt="Ловушка" title="Ловушка" >'; // активная ловушка
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная ловушка
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/bt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в ловушке		
			}
		 } // ловушка

break;

case 'я' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='я')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/b'.$LAB.'.gif alt="Яма-Ловушка" title="Яма-Ловушка" >'; // активная ловушка
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная ловушка
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/bt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в ловушке
			}
		 } // ловушка

break;


case 'ф' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='ф')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/fire'.$LAB.'.gif alt="Огненная Яма-Ловушка" title="Огненная Яма-Ловушка" >'; // активная ловушка
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная ловушка
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/bt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в ловушке
			}
		 } // ловушка

break;

case 'ч' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='ч')
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/ice'.$LAB.'.gif alt="Ледяная Яма-Ловушка" title="Ледяная Яма-Ловушка" >'; // активная ловушка
					}
					else
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/oo'.$LAB.'.gif>'; // отюзаная ловушка
					}

			}
			else
			{
			$linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/bt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в ловушке
			}
		 } // ловушка

break;

case 'G' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='G') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/g.gif  >'; // активная хрустальная стенка
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип 	
			}
		 } // 

break;

case 'ж' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='ж') 
					{
					 $linte.='<img src=http://capitalcity.oldbk.com/i/plab/map/gg4.gif>'; //ледяная
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип 	
			}
		 } // 

break;

case 'в' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='в') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/uk3.gif alt="Направление вверх" title="Направление вверх" >'; // активная хрустальная стенка
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип 	
			}
		 } // 

break;

case 'н' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='н') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/uk4.gif alt="Направление вниз" title="Направление вниз" >'; // активная хрустальная стенка
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип 	
			}
		 } // 

break;

case 'л' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='л') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/uk2.gif alt="Направление влево" title="Направление влево" >'; // активная хрустальная стенка
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип 	
			}
		 } // 

break;

case 'п' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='п') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/uk1.gif alt="Направление вправо" title="Направление вправо" >'; // активная хрустальная стенка
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип 	
			}
		 } // 

break;

case '1' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='1') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/kz.gif alt="Кузня" title="Кузня" >'; // Кузня
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип 	
			}
		 } // 

break;

case '2' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='2') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/2.gif alt="Тайный Вход" title="Тайный Вход" >'; // Кузня
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип 	
			}
		 } // 

break;


case 'A' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='A') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/a'.$LAB.'.gif  >'; // активная хрустальная стенка
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип 	
			}
		 } // 

break;

case 'E' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='E') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/e'.$LAB.'.gif  >'; // активная хрустальная стенка
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип 	
			}
		 } // 

break;


case 'D' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='D') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/d.gif alt="Дверь" title="Дверь" >'; // активная 
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в двери	
			}
		 } // 

break;

case 'T' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='T') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/t'.$LAB.'.gif alt="Портал X:'.$i.'/Y:'.$j.'" title="Портал X:'.$i.'/Y:'.$j.'" >'; // активная 
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в
			}
		 } // 

break;

case 'X' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='X') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/x.gif alt="Дверь" title="Дверь" >'; // активная 
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в двери	
			}
		 } // 

break;

case 'Z' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='Z') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/z.gif alt="Дверь" title="Дверь" >'; // активная 
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в двери	
			}
		 } // 

break;

case 'C' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='C') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/c.gif alt="Дверь" title="Дверь" >'; // активная 
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная 
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в двери	
			}
		 } // 

break;

case 'P' :	
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='P') 
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/p'.$LAB.'.gif alt="Ящик Пандоры" title="Ящик Пандоры">'; // активная коробка
					}
					else
					{
					 $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>'; // отюзаная ловушка
					}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/pt'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый тип копается в ловушке		
			}
		 } // BOX P) Пандоры

break;


case 'S' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='S')  
							{ 
							$linte.='<img src=http://i.oldbk.com/llabb/s'.$LAB.'.gif alt="Сундук" title="Сундук">' ; //полный 
							}
							else 
							{
							$linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>' ;  //пустой сундук	
							}
			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/st'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый чар копается в сундуке		
			}
		 } // сундук

break;

case 'W' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='W')  
							{ 
							$linte.='<img src=http://i.oldbk.com/llabb/w'.$LAB.'.gif alt="Ларец" title="Ларец">' ; //полный 
							}
							else 
							{
							$linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>' ;  //пустой сундук	
							}
			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый чар копается в сундуке		
			}
		 } // сундук

break;

case 'Y' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='Y')  
							{ 
							$linte.='<img src=http://i.oldbk.com/llabb/y'.$LAB.'.gif alt="Ларец" title="Ларец">' ; //полный 
							}
							else 
							{
							$linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>' ;  //пустой сундук	
							}
			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый чар копается в сундуке		
			}
		 } // сундук

break;

case 'L' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='L')  
							{ 
							$linte.='<img src=http://i.oldbk.com/llabb/l'.$LAB.'.gif alt="Ларец" title="Ларец">' ; //полный 
							}
							else 
							{
							$linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>' ;  //пустой сундук	
							}
			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый чар копается в сундуке		
			}
		 } // сундук

break;

case 'K' :
		{
		 if ($TA[$i][$j]=='')
			{
				if ($Aitems[$i][$j]!='K')  
							{ 
							$linte.='<img src=http://i.oldbk.com/llabb/k'.$LAB.'.gif alt="Ларец" title="Ларец">' ; //полный 
							}
							else 
							{
							$linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>' ;  //пустой сундук	
							}
			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ot'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; //левый чар копается в сундуке		
			}
		 } // сундук

break;

case 'H' :	
		{
		 if ($TA[$i][$j]=='')
			{
					if ($Aitems[$i][$j]!='H') 
							{
							  $linte.='<img src=http://i.oldbk.com/llabb/h'.$LAB.'.gif alt="Живая Вода" title="Живая Вода" >'; // хилка
							}
							else 
							{
							  $linte.='<img src=http://i.oldbk.com/llabb/o'.$LAB.'.gif>';	// пустая лилка
							}

			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ht'.$LAB.'.gif title="'.$TA[$i][$j].'" alt="'.$TA[$i][$j].'">'; // левый чар копается в хилке		
			}
		 } // хилка

break;

case 'U' :	
		{
		 if ($TA[$i][$j]=='')
			{
			  $linte.='<img src=http://i.oldbk.com/llabb/u'.$LAB.'.gif title="Я" alt="Я">'; // Я на карте
			}
			else 
			{
			$linte.='<img src=http://i.oldbk.com/llabb/ut'.$LAB.'.gif title="Я ,'.$TA[$i][$j].'" alt="Я, '.$TA[$i][$j].'">'; //я и еще ктото из тимы		
			}
		 } // user
break;




			}
			}

            }
	  $MAP_SCREEN.=$linte."<br>\n";
	}

/// map prep

echo $MAP_SCREEN;
?>

</body>
</html>
