<?
// API  - первый файл - открывает файл  V2
//header ("content-type: text/xml");
//ob_start("");

$city_pref='oldbk.';
$city_id='0';

//без лимита
$LIM=5000; 

function get_item_info($dress)
 {
 $out=htmlspecialchars(strip_tags($dress['name']))."\n"." Прочность ".$dress['duration']."/".$dress['maxdur']."\n".(($dress['ghp']>0)?"Уровень жизни +{$dress['ghp']}\n":"")."".(($dress['bron1']!=0)?"• Броня головы:{$dress['bron1']}\n":"")."".(($dress['bron2']!=0)?"• Броня корпуса:{$dress['bron2']}\n":"")."".(($dress['bron3']!=0)?"• Броня пояса:{$dress['bron3']}\n":"")."".(($dress['bron4']!=0)?"• Броня ног:{$dress['bron4']}\n":"")."".(($dress['text']!=null)?"На одежде вышито: {$dress['text']}\n":"")."";
 
 if ($dress[type]==30)
 	{
 	$out.=" Уровень: {$dress[up_level]}";
 	}
 
 return  $out;
 }

function get_items($items)
{

if ($items['sergi']>0)
	{
	$ids[]=$items['sergi'];
	}


if ($items['kulon']>0)
	{
	$ids[]=$items['kulon'];
	}
	
if ($items['perchi']>0)
	{
	$ids[]=$items['perchi'];
	}

if ($items['weap']>0)
	{
	$ids[]=$items['weap'];
	}
	
if ($items['bron']>0)
	{
	$ids[]=$items['bron'];
	}	

if ($items['r1']>0)
	{
	$ids[]=$items['r1'];
	}		

if ($items['r2']>0)
	{
	$ids[]=$items['r2'];
	}		

if ($items['r3']>0)
	{
	$ids[]=$items['r3'];
	}			

if ($items['helm']>0)
	{
	$ids[]=$items['helm'];
	}			
if ($items['shit']>0)
	{
	$ids[]=$items['shit'];
	}		
if ($items['boots']>0)
	{
	$ids[]=$items['boots'];
	}			
if ($items['nakidka']>0)
	{
	$ids[]=$items['nakidka'];
	}				
if ($items['rubashka']>0)
	{
	$ids[]=$items['rubashka'];
	}				
if ($items['runa1']>0)
	{
	$ids[]=$items['runa1'];
	}				
if ($items['runa2']>0)
	{
	$ids[]=$items['runa2'];
	}			
if ($items['runa3']>0)
	{
	$ids[]=$items['runa3'];
	}				

if (count($ids)>0)
	{
	$get_all_items=mysql_query("select * from oldbk.inventory where id in (".implode(",",$ids).")" );
	
	while($row = mysql_fetch_array($get_all_items)) 
	{
		$items_array[$row[id]]=$row;
	}
	

	if ($items_array[$items[sergi]]>0) echo "<sergi img=\"".(($items_array[$items[sergi]][add_pick]!=null)?$items_array[$items[sergi]][add_pick]:$items_array[$items[sergi]][img])."\"> ".get_item_info($items_array[$items[sergi]])." </sergi>\n";
	if ($items_array[$items[kulon]]>0) echo "<kulon img=\"".(($items_array[$items[kulon]][add_pick]!=null)?$items_array[$items[kulon]][add_pick]:$items_array[$items[kulon]][img])."\"> ".get_item_info($items_array[$items[kulon]])." </kulon>\n";
	if ($items_array[$items[perchi]]>0) echo "<perchi img=\"".(($items_array[$items[perchi]][add_pick]!=null)?$items_array[$items[perchi]][add_pick]:$items_array[$items[perchi]][img])."\"> ".get_item_info($items_array[$items[perchi]])." </perchi>\n";	
	if ($items_array[$items[weap]]>0) echo "<weap img=\"".(($items_array[$items[weap]][add_pick]!=null)?$items_array[$items[weap]][add_pick]:$items_array[$items[weap]][img])."\"> ".get_item_info($items_array[$items[weap]])." </weap>\n";	
	if ($items_array[$items[bron]]>0) echo "<bron img=\"".(($items_array[$items[bron]][add_pick]!=null)?$items_array[$items[bron]][add_pick]:$items_array[$items[bron]][img])."\"> ".get_item_info($items_array[$items[bron]])." </bron>\n";	
	if ($items_array[$items[r1]]>0) echo "<r1 img=\"".(($items_array[$items[r1]][add_pick]!=null)?$items_array[$items[r1]][add_pick]:$items_array[$items[r1]][img])."\"> ".get_item_info($items_array[$items[r1]])." </r1>\n";	
	if ($items_array[$items[r2]]>0) echo "<r2 img=\"".(($items_array[$items[r2]][add_pick]!=null)?$items_array[$items[r2]][add_pick]:$items_array[$items[r2]][img])."\"> ".get_item_info($items_array[$items[r2]])." </r2>\n";			
	if ($items_array[$items[r3]]>0) echo "<r3 img=\"".(($items_array[$items[r3]][add_pick]!=null)?$items_array[$items[r3]][add_pick]:$items_array[$items[r3]][img])."\"> ".get_item_info($items_array[$items[r3]])." </r3>\n";			
	if ($items_array[$items[helm]]>0) echo "<helm img=\"".(($items_array[$items[helm]][add_pick]!=null)?$items_array[$items[helm]][add_pick]:$items_array[$items[helm]][img])."\"> ".get_item_info($items_array[$items[helm]])." </helm>\n";			
	if ($items_array[$items[shit]]>0) echo "<shit img=\"".(($items_array[$items[shit]][add_pick]!=null)?$items_array[$items[shit]][add_pick]:$items_array[$items[shit]][img])."\"> ".get_item_info($items_array[$items[shit]])." </shit>\n";				
	if ($items_array[$items[boots]]>0) echo "<boots img=\"".(($items_array[$items[boots]][add_pick]!=null)?$items_array[$items[boots]][add_pick]:$items_array[$items[boots]][img])."\"> ".get_item_info($items_array[$items[boots]])." </boots>\n";				
	if ($items_array[$items[nakidka]]>0) echo "<nakidka img=\"".(($items_array[$items[nakidka]][add_pick]!=null)?$items_array[$items[nakidka]][add_pick]:$items_array[$items[nakidka]][img])."\"> ".get_item_info($items_array[$items[nakidka]])." </nakidka>\n";					
	if ($items_array[$items[rubashka]]>0) echo "<rubashka img=\"".(($items_array[$items[rubashka]][add_pick]!=null)?$items_array[$items[rubashka]][add_pick]:$items_array[$items[rubashka]][img])."\"> ".get_item_info($items_array[$items[rubashka]])." </rubashka>\n";						
	if ($items_array[$items[runa1]]>0) echo "<runa1 img=\"".(($items_array[$items[runa1]][add_pick]!=null)?$items_array[$items[runa1]][add_pick]:$items_array[$items[runa1]][img])."\"> ".get_item_info($items_array[$items[runa1]])." </runa1>\n";						
	if ($items_array[$items[runa2]]>0) echo "<runa2 img=\"".(($items_array[$items[runa2]][add_pick]!=null)?$items_array[$items[runa2]][add_pick]:$items_array[$items[runa2]][img])."\"> ".get_item_info($items_array[$items[runa2]])." </runa2>\n";						
	if ($items_array[$items[runa3]]>0) echo "<runa3 img=\"".(($items_array[$items[runa3]][add_pick]!=null)?$items_array[$items[runa3]][add_pick]:$items_array[$items[runa3]][img])."\"> ".get_item_info($items_array[$items[runa3]])." </runa3>\n";	
	}					
}


function get_eff($ownerid)
{

	$get_all_items=mysql_query("select * from oldbk.effects where owner='{$ownerid}' and type in (11,12,13,14)");
	
	$tt[11]='легкая';
	$tt[12]='средняя';
	$tt[13]='тяжелая';		
	$tt[14]='неизлечимая';	
	

	if (mysql_num_rows($get_all_items) >0 ) 
	{
 	echo "<effects>\n";	
	while($row = mysql_fetch_array($get_all_items)) 
	{
	echo "<travma type=\"$row[type]\" finishtime=\"$row[time]\" ";
	
	if ($row[sila]>0)
		{
		echo "  sila=\"-$row[sila]\" ";
		}

	if ($row[lovk]>0)
		{
		echo "  lovk=\"-$row[lovk]\" ";
		}
		
	if ($row[inta]>0)
		{
		echo "  inta=\"-$row[inta]\" ";
		}				
	
	if ($row[battle]>0)
		{
		echo "  battle=\"$row[battle]\" ";
		}	
	
	echo ">".htmlspecialchars(strip_tags($row['name']))."</travma>\n";
	}
 	echo "</effects>\n"; 	
 	}

}


echo "<?xml version=\"1.0\" encoding=\"windows-1251\"?>\n";
include "/www/oldbk.com/connect.php";
$rooms = array ("Секретная Комната","Комната для новичков","Комната для новичков 2","Комната для новичков 3","Комната для новичков 4","Зал Воинов 1","Зал Воинов 2","Зал Воинов 3","Торговый зал",
	"Рыцарский зал","Башня рыцарей-магов","Колдовской мир","Этажи духов","Астральные этажи","Огненный мир","Зал Паладинов","Совет Белого Братства","Зал Тьмы","Царство Тьмы","Будуар",
	"Центральная площадь","Страшилкина улица","Магазин","Ремонтная мастерская","Новогодняя елка","Комиссионный магазин","Парковая улица","Почта","Регистратура кланов","Банк","Суд",
	"Башня смерти","Готический замок","Лабиринт хаоса","Цветочный магазин","Магазин 'Березка'","Зал Стихий","Готический замок - приемная","Готический замок - арсенал","Готический замок - внутренний двор",
	"Готический замок - мастерские","Готический замок - комнаты отдыха","Лотерея Сталкеров","Комната Знахаря","Комната №44","Вход в Лабиринт Хаоса","Прокатная лавка","Арендная лавка","Храмовая лавка","Храм Древних","Замковая площадь",
	"Большая скамейка","Средняя скамейка","Маленькая скамейка","Зал Света","Царство Света","Царство Стихий","Зал клановых войн","Комната №58","Комната №59","Арена Богов","Комната №61","Комната №62","Комната №63","Комната №64","Комната №65","66"=>'Торговая улица',
"200"=> "Ристалище","401"=> "Врата Ада",

//Ломбард
"70" => "Ломбард",
"71" => "Аукцион",
//турниры

"197"=>"Оружейная Комната",
"198"=>"Оружейная Комната",
"199"=>"Оружейная Комната",

"270"=>"Вход в Одиночные сражения",
"271"=> "Одиночные сражения[1]",
"272"=> "Одиночные сражения[2]",
"273"=> "Одиночные сражения[3]",
"274"=> "Одиночные сражения[4]",
"275"=> "Одиночные сражения[5]",
"276"=> "Одиночные сражения[6]",
"277"=> "Одиночные сражения[7]",
"278"=> "Одиночные сражения[8]",
"279"=> "Одиночные сражения[9]",
"280"=> "Одиночные сражения[10]",
"281"=> "Одиночные сражения[11]",
"282"=> "Одиночные сражения[12]",
// Групповое сражение
"240"=>"Вход в Групповое сражения",
"241"=> "Групповое сражение[1]",
"242"=> "Групповое сражение[2]",
"243"=> "Групповое сражение[3]",
"244"=> "Групповое сражение[4]",
"245"=> "Групповое сражение[5]",
"246"=> "Групповое сражение[6]",
"247"=> "Групповое сражение[7]",
"248"=> "Групповое сражение[8]",
"249"=> "Групповое сражение[9]",
"250"=> "Групповое сражение[10]",
"251"=> "Групповое сражение[11]",
"252"=> "Групповое сражение[12]",
"253"=> "Групповое сражение[13]",
//Сражение отрядов
"210"=>"Вход в Сражение отрядов",
"211"=> "Сражение отрядов[1]",
"212"=> "Сражение отрядов[2]",
"213"=> "Сражение отрядов[3]",
"214"=> "Сражение отрядов[4]",
"215"=> "Сражение отрядов[5]",
"216"=> "Сражение отрядов[6]",
"217"=> "Сражение отрядов[7]",
"218"=> "Сражение отрядов[8]",
"219"=> "Сражение отрядов[9]",
"220"=> "Сражение отрядов[10]",
"221"=> "Сражение отрядов[11]",
"222"=> "Сражение отрядов[12]",

// БС
"501" => "Восточная Крыша",
"502" => "Бойница",
"503" => "Келья 3",
"504" => "Келья 2",
"505" => "Западная Крыша 2",
"506" => "Келья 4",
"507" => "Келья 1",
"508" => "Служебная комната",
"509" => "Зал Отдыха 2",
"510" => "Западная Крыша 1",
"511" => "Выход на Крышу",
"512" => "Зал Статуй 2",
"513" => "Храм",
"514" => "Восточная комната",
"515" => "Зал Отдыха 1",
"516" => "Старый Зал 2",
"517" => "Старый Зал 1",
"518" => "Красный Зал 3",
"519" => "Зал Статуй 1",
"520" => "Зал Статуй 3",
"521" => "Трапезная 3",
"522" => "Зал Ожиданий",
"523" => "Оружейная",
"524" => "Красный Зал-Окна",
"525" => "Красный Зал",
"526" => "Гостинная",
"527" => "Трапезная 1",
"528" => "Внутренний Двор",
"529" => "Внутр.Двор-Вход",
"530" => "Желтый Коридор",
"531" => "Мраморный Зал 1",
"532" => "Красный Зал 2",
"533" => "Библиотека 1",
"534" => "Трапезная 2",
"535" => "Проход Внутр. Двора",
"536" => "Комната с Камином",
"537" => "Библиотека 3",
"538" => "Выход из Мрам.Зала",
"539" => "Красный Зал-Коридор",
"540" => "Лестница в Подвал 1",
"541" => "Южный Внутр. Двор",
"542" => "Трапезная 4",
"543" => "Мраморный Зал 3",
"544" => "Мраморный Зал 2",
"545" => "Картинная Галерея 1",
"546" => "Лестница в Подвал 2",
"547" => "Проход Внутр. Двора 2",
"548" => "Внутр.Двор-Выход",
"549" => "Библиотека 2",
"550" => "Картинная Галерея 3",
"551" => "Картинная Галерея 2",
"552" => "Лестница в Подвал 3",
"553" => "Терасса",
"554" => "Оранжерея",
"555" => "Зал Ораторов",
"556" => "Лестница в Подвал 4",
"557" => "Темная Комната",
"558" => "Винный Погреб",
"559" => "Комната в Подвале",
"560" => "Подвал" ,
"999" => "Вход в Руины"

);





$get_all_data=mysql_query("select u.id, u.`login`,u.`realname`,u.`borndate`,u.`sex`,u.`city`,u.`icq`,u.`http`,u.`info`,u.`lozung`,u.`level`,u.`align`,u.`klan`,u.`sila`,u.`lovk`,u.`inta`,u.`vinos`, u.stbat, u.winstbat, u.skulls ,u.`intel`,u.`mudra`,u.`duh`,u.`bojes`,u.`noj`,u.`mec`,u.`topor`,u.`dubina`,u.`win`,u.`lose`,u.`status`,u.`borncity`,u.`borntime`,u.`room`,u.`maxhp`,u.`hp`,u.`maxmana`,u.`mana`,u.`sergi`,u.`kulon`,u.`perchi`,u.`weap`,u.`bron`,u.`r1`,u.`r2`,u.`r3`,u.`helm`,u.`shit`,u.`boots`,u.`shadow`,u.`nakidka`,u.`rubashka`,u.`battle`,u.`battle_t`,u.`block`,u.`palcom`,u.`medals`,u.`lab`,u.`in_tower`,u.`deal`,u.`married`,u.`last_battle`,u.`bpbonussila`,u.`bpbonushp`,u.`hidden`, u.`wcount`,u.`victorina`,u.`prem`,u.`slp`,u.`trv`,u.`odate`,u.`id_city`,u.`ruines`,  u.`id_grup` ,  u.`voinst` , u.`runa1`, u.`runa2`, u.`runa3`    from   ".$city_pref."users as u   where u.hidden=0 and id_city='{$city_id}' and  u.odate >=".(time()-120)." LIMIT {$LIM} ");

$users_array=array();
$items_array=array();

$cod=0;

$all_count=mysql_num_rows($get_all_data);

while($row = mysql_fetch_array($get_all_data)) 
	{
	$cod++;
	//load info array
	if (!(isset($users_array[$row[id]])))
		{
		// грузим параметры чара
			$users_array[$row[id]][login]=$row[login];
			$users_array[$row[id]][realname]=htmlspecialchars(strip_tags($row[realname]));
			$users_array[$row[id]][borndate]=$row[borndate];
			$users_array[$row[id]][sex]=$row[sex];
			$users_array[$row[id]][city]=htmlspecialchars(strip_tags($row[city]));
			$users_array[$row[id]][icq]=htmlspecialchars(strip_tags($row[icq]));
			$users_array[$row[id]][http]= htmlspecialchars(strip_tags($row[http]));
			$users_array[$row[id]][lozung]= htmlspecialchars(strip_tags($row[lozung]));
			$users_array[$row[id]][level]=$row[level];
			$users_array[$row[id]][align]=$row[align];
			$users_array[$row[id]][klan]=$row[klan];
			$users_array[$row[id]][sila]=$row[sila];
			$users_array[$row[id]][lovk]=$row[lovk];
			$users_array[$row[id]][inta]=$row[inta];
			$users_array[$row[id]][vinos]=$row[vinos];
			$users_array[$row[id]][intel]=$row[intel];
			$users_array[$row[id]][mudra]=$row[mudra];
			$users_array[$row[id]][duh]=$row[duh];
			$users_array[$row[id]][bojes]=$row[bojes];
			$users_array[$row[id]][noj]=$row[noj];
			$users_array[$row[id]][mec]=$row[mec];
			$users_array[$row[id]][topor]=$row[topor];
			$users_array[$row[id]][dubina]=$row[dubina];
			$users_array[$row[id]][win]=$row[win];
			$users_array[$row[id]][lose]=$row[lose];
			$users_array[$row[id]][status]= htmlspecialchars(strip_tags($row[status]));
			$users_array[$row[id]][borncity]=$row[borncity];
			$users_array[$row[id]][borntime]=$row[borntime];
			$users_array[$row[id]][room]=$row[room];
			$users_array[$row[id]][maxhp]=$row[maxhp];
			$users_array[$row[id]][hp]=$row[hp];
			$users_array[$row[id]][maxmana]=$row[maxmana];
			$users_array[$row[id]][mana]=$row[mana];
			$users_array[$row[id]][sergi]=$row[sergi];
			$users_array[$row[id]][kulon]=$row[kulon];
			$users_array[$row[id]][perchi]=$row[perchi];
			$users_array[$row[id]][weap]=$row[weap];
			$users_array[$row[id]][bron]=$row[bron];
			$users_array[$row[id]][r1]=$row[r1];
			$users_array[$row[id]][r2]=$row[r2];
			$users_array[$row[id]][r3]=$row[r3];
			$users_array[$row[id]][helm]=$row[helm];
			$users_array[$row[id]][shit]=$row[shit];
			$users_array[$row[id]][boots]=$row[boots];
			$users_array[$row[id]][shadow]=$row[shadow];
			$users_array[$row[id]][nakidka]=$row[nakidka];
			$users_array[$row[id]][rubashka]=$row[rubashka];			
			$users_array[$row[id]][battle]=$row[battle];
			$users_array[$row[id]][battle_t]=$row[battle_t];
			$users_array[$row[id]][block]=$row[block];
			$users_array[$row[id]][palcom]=htmlspecialchars(strip_tags($row[palcom]));
			$users_array[$row[id]][medals]=$row[medals];
			$users_array[$row[id]][lab]=$row[lab];
			$users_array[$row[id]][in_tower]=$row[in_tower];
			$users_array[$row[id]][deal]=$row[deal];
			$users_array[$row[id]][married]=$row[married];
			$users_array[$row[id]][last_battle]=$row[last_battle];
			$users_array[$row[id]][bpbonussila]=$row[bpbonussila];
			$users_array[$row[id]][bpbonushp]=$row[bpbonushp];
			$users_array[$row[id]][hidden]=$row[hidden];
			$users_array[$row[id]][wcount]=$row[wcount];
			$users_array[$row[id]][victorina]=$row[victorina];
			$users_array[$row[id]][prem]=$row[prem];
			$users_array[$row[id]][slp]=$row[slp];
			$users_array[$row[id]][trv]=$row[trv];
			$users_array[$row[id]][odate]=$row[odate];
			$users_array[$row[id]][id_city]=$row[id_city];
			$users_array[$row[id]][ruines]=$row[ruines];
			$users_array[$row[id]][voinst]=$row[voinst];

			$users_array[$row[id]][stbat]=$row[stbat];			
			$users_array[$row[id]][winstbat]=$row[winstbat];			
			$users_array[$row[id]][skulls]=$row[skulls];						
						
			$users_array[$row[id]][id_grup]=$row[id_grup];						
			
			
			$users_array[$row[id]][runa1]=$row[runa1];						
			$users_array[$row[id]][runa2]=$row[runa2];						
			$users_array[$row[id]][runa3]=$row[runa3];												
			
			
			if ($cod>=60) // регулятор задержек
				{
				$cod=0;
			//	sleep(1);
				}
		}
	}
///////////////////////////////
echo "<online refresh=\"".time()."\">";
foreach($users_array as $u_id => $u_data)
	{
	echo "<user id=\"".$u_id."\" ";
	echo "login=\"".$u_data[login]."\" ";
	echo "realname=\"".$u_data[realname]."\" ";
	//echo "borndate=\"".$u_data[borndate]."\" ";
	echo "sex=\"".$u_data[sex]."\" ";
	echo "city=\"".$u_data[city]."\" ";
	echo "icq=\"".$u_data[icq]."\" ";
	echo "http=\"".$u_data[http]."\" ";

	echo "lozung=\"".$u_data[lozung]."\" ";
	echo "level=\"".$u_data[level]."\" ";
	echo "align=\"".$u_data[align]."\" ";
	echo "klan=\"".$u_data[klan]."\" ";
	echo "sila=\"".$u_data[sila]."\" ";
	echo "lovk=\"".$u_data[lovk]."\" ";
	echo "inta=\"".$u_data[inta]."\" ";
	echo "vinos=\"".$u_data[vinos]."\" ";
	echo "intel=\"".$u_data[intel]."\" ";
	echo "mudra=\"".$u_data[mudra]."\" ";
	echo "duh=\"".$u_data[duh]."\" ";
	echo "bojes=\"".$u_data[bojes]."\" ";

	echo "win=\"".$u_data[win]."\" ";
	echo "lose=\"".$u_data[lose]."\" ";
	echo "status=\"".$u_data[status]."\" ";
	echo "borncity=\"".$u_data[borncity]."\" ";
	echo "borntime=\"".$u_data[borntime]."\" ";

	echo "maxhp=\"".$u_data[maxhp]."\" ";
	echo "hp=\"".$u_data[hp]."\" ";
	echo "maxmana=\"".$u_data[maxmana]."\" ";
	echo "mana=\"".$u_data[mana]."\" ";
	
	echo "shadow=\"".$u_data[shadow]."\" ";
	echo "battle=\"".$u_data[battle]."\" ";
	echo "battle_t=\"".$u_data[battle_t]."\" ";
	echo "block=\"".$u_data[block]."\" ";
	echo "palcom=\"".$u_data[palcom]."\" ";
	echo "medals=\"".$u_data[medals]."\" ";

	if ($u_data[lab]>0)
	{
		$lab[1]='Лабиринт Хаоса - обычный';
		$lab[2]='Лабиринт Хаоса - героический';		
		$lab[3]='Лабиринт Хаоса - новичков';				
		echo "loc=\"".$lab[$u_data[lab]]."\" ";			
	}
	else
	  if ($u_data[ruines] > 0)
	   {
	   	echo "loc=\"Руины\" trunir=\"".$u_data[ruines]."\" ";			
	   }
	else 
	  if  ($u_data[in_tower]==10)
	  { 
	  echo "loc=\"Башня смерти\" ";
	  }
	else 
	  if  ($u_data[in_tower]==3)
	  { 
	  echo "loc=\"Турниры:Сражение отрядов\" trunir=\"".$u_data[id_grup]."\" ";
	  }	  
	 else
	 if ($user['room'] >= 49998 && $user['room'] <= 60000)
	 {
	 echo "loc=\"Загород\" ";
	 } 
	else
	 {
	 echo "loc=\"".$rooms[$u_data[room]]."\" ";
	 }
	$city_name[0]="Capitalcity";
	$city_name[1]="AvalonCity";	
	echo "id_city=\"".$city_name[$u_data[id_city]]."\" ";	
		
//	echo "deal=\"".$u_data[deal]."\" ";
	echo "married=\"".$u_data[married]."\" ";
	echo "last_battle=\"".$u_data[last_battle]."\" ";
	echo "bpbonussila=\"".$u_data[bpbonussila]."\" ";
	echo "bpbonushp=\"".$u_data[bpbonushp]."\" ";

	echo "wcount=\"".$u_data[ wcount]."\" ";
	echo "victorina=\"".$u_data[victorina]."\" ";

	echo "slp=\"".$u_data[slp]."\" ";
	//echo "trv=\"".$u_data[trv]."\" ";
	echo "odate=\"".$u_data[odate]."\" ";

	echo "voinst=\"".$u_data[voinst]."\" ";

	echo "stbat=\"".$u_data[stbat]."\" ";
	echo "winstbat=\"".$u_data[winstbat]."\" ";
	echo "skulls=\"".$u_data[skulls]."\" ";		

 	echo ">\n";  	 
 	echo "<items>";


	if (($u_data[in_tower]==3)  AND ($u_data[battle]==0) )
	{
	
	}
	else
	{
	//get items content - танец с бубнов для создания новых конектов
		//$itemsowner = file_get_contents("http://oldbk.com/api/get_items_owner.php?key=aS3Afn41d9&sergi=$u_data[sergi]&kulon=$u_data[kulon]&perchi=$u_data[perchi]&weap=$u_data[weap]&bron=$u_data[bron]&r1=$u_data[r1]&r2=$u_data[r2]&r3=$u_data[r3]&helm=$u_data[helm]&shit=$u_data[shit]&boots=$u_data[boots]&nakidka=$u_data[nakidka]&rubashka=$u_data[rubashka]&runa1=$u_data[runa1]&runa2=$u_data[runa2]&runa3=$u_data[runa3]");
		get_items($u_data);		
	//	echo $itemsowner;
	
	}
				
	echo "</items>\n";
 

		//get-eff
		//$effowner = file_get_contents("http://oldbk.com/api/get_eff_owner.php?key=aS3Afn41d9&teloid=$u_id");
	get_eff($u_id);
		//echo $effowner;

 	
 	
 	echo "</user>\n"; 
	 
	}

echo "</online>";
//ob_end_flush();
?>