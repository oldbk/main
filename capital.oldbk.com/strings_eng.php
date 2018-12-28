<?

function get_log_string($input_str)
{

$str=trim($input_str);
if ($str=='') return ;
$str=explode(":",$str);

 if ($str[0]=='!')
 	{
 	//новый формат
 	//главный свич - тип строки
 	switch($str[1])
			{
			case 'S':
			//старт боя
			$out='The clock showed <span class=date>'.date("Y.m.d H.i",$str[2]).'</span>,  when <b>'.BNewRender($str[3]).'</b> и <b>'.BNewRender($str[4]).'</b>'.($str[5]!=''?"и <b>".BNewRender($str[5])."</b>":"").' challenged each other. ';
			break;			 		
			case 'U':
			//уворот
			$autot="";			
			if ($str[12]==1) { $autot=" <i>(auto blow)</i>"; } 			
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.BNewRenderT($str[3]).' '.get_str_text_fail($str[4]).' '.get_str_text_hark($str[5]).' '.BNewRenderT($str[6]).' '.get_str_text_uvorot($str[7]).' '.get_str_text_wep($str[8]).' '.get_str_text_kuda($str[9]).'.'.$autot;
			break;			
			
			case 'B':
			//блок
			$autot="";			
			if ($str[12]==1) { $autot=" <i>(auto blow)</i>"; } 						
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.BNewRenderT($str[3]).' '.get_str_text_fail($str[4]).' '.get_str_text_hark($str[5]).' '.BNewRenderT($str[6]).' '.get_str_text_block($str[7]).' '.get_str_text_wep($str[8]).' '.get_str_text_kuda($str[9]).'.'.$autot;
			break;			

			case 'K':
			//крит
			$udar=explode("|",$str[11]);
			if ($udar[1]!='') { $udar=$udar[1]; } else { $udar=$udar[0]; }
			$udar='<b><font color=red>-'.$udar.'</font></b>';
			$autot="";
			if ($str[13]==1) { $autot=" <i>(auto blow)</i>"; } 						
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.BNewRenderT($str[6]).' '.get_str_text_ud($str[4]).' '.get_str_text_hark($str[5]).' '.BNewRenderT($str[3]).' '.get_str_text_krit($str[7]).' '.get_str_text_kuda($str[9]).' '.$udar.' '.$str[12].$autot;
			break;			
			
			case 'P':
			//крит через блок
			$udar=explode("|",$str[11]);
			if ($udar[1]!='') { $udar=$udar[1]; } else { $udar=$udar[0]; }
			$udar='<b><font color=red>-'.$udar.'</font></b>';
			$autot="";
			if ($str[13]==1) { $autot=" <i>(auto blow)</i>"; } 			
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.BNewRenderT($str[6]).' '.get_str_text_ud($str[4]).' '.get_str_text_hark($str[5]).' '.BNewRenderT($str[3]).' '.get_str_text_krita($str[7]).' '.get_str_text_kuda($str[9]).' '.$udar.' '.$str[12].$autot;
			break;		
			
			case 'R':
			//простой удар
			$udar=explode("|",$str[11]);
			if ($udar[1]!='') { $udar=$udar[1]; } else { $udar=$udar[0]; }
			$udar='<b>-'.$udar.'</b>';
			$autot="";
			if ($str[13]==1) { $autot=" <i>(auto blow)</i>"; } 
			
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.BNewRenderT($str[6]).' '.get_str_text_ud($str[4]).' '.get_str_text_hark($str[5]).' '.BNewRenderT($str[3]).' '.get_str_text_udar($str[7]).' '.get_str_text_wep($str[8]).' '.get_str_text_kuda($str[9]).' '.$udar.' '.$str[12].$autot;
			break;			

			case 'C':
			//комментатор
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.get_str_comment($str[3]);
			break;			
			
			case 'D':
			//умер
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.BNewRenderT($str[3]).' '.get_str_dead($str[4]);
			break;			
			
			case 'H':
			//хил в бою
			$hill=explode("|",$str[11]);
			if ($hill[1]!='') { $hill=$hill[1]; } else { $hill=$hill[0]; }
			$hill='<b>+'.$hill.'</b>';
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.BNewRenderT($str[3]).' '.get_str_use_mag($str[4]).' '.($str[6]!=''?"на ".BNewRenderT($str[6])." ":"").get_str_dohill($str[5]).' '.$hill.' '.$str[12].'';			
			break;			

			case 'F':			
			$out='<span class=date>'.date("H:i",$str[2]).'</span> Fight ended'.($str[3]==1?" by timeout":"").($str[4]!=''?", the victory for the ".BNewRender($str[4]):". Dead heat.");
			break;			

			case 'T':
			//травма
			$act[0]='';
			$act[1]='';
			
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.($str[3]!=''?BNewRenderT($str[3])." ".$act[$str[4]]." was damaged: <font color=red>".$str[5]."</font>":"... The winner was to cripple the losers...");
			break;			
			
			case 'Q':
			//ремонт
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.($str[3]!=''?"Caution! By \"".BNewRenderT($str[3])."\" item \"".$str[4]."\" ".get_str_remont($str[5]):get_str_remont(0));
			break;	
			
			case 'E':
			//выход
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.BNewRenderT($str[3]).' '.get_str_exit($str[4]);
			break;			

			case 'V':
			//вход
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.BNewRenderT($str[3]).' '.get_str_goin($str[4]);
			break;		
			
			case 'W':
			//вход c кнопкой i и цветом команды
		
			$out='<span class=date>'.date("H:i",$str[2]).'</span> <span class="B'.$str[4].'">'.BNewRender($str[3]).'</span> '.get_str_goin($str[5]);
			break;				
			
			case 'X':
			$str[5]=str_replace('^',':',$str[5]);
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.($str[3]!=''?"".BNewRenderT($str[3]):"")." ".get_str_xtext($str[4])." ".$str[5];
			break;

			case 'M':
			$str[7]=str_replace('^',':',$str[7]);
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.($str[3]!=''?"".BNewRenderT($str[3]):"")." ".get_str_xtext($str[4])." ".get_str_magic($str[5]).($str[6]!=''?" on the character ".BNewRenderT($str[6]):"")." ".$str[7];
			break;
			
			case 'Y':
			//ожог
			$udar=explode("|",$str[11]);
			if ($udar[1]!='') { $udar=$udar[1]; } else { $udar=$udar[0]; }
			$udar='<b><font color=red>-'.$udar.'</font></b>';
			$autot="";
			if ($str[13]==1) { $autot=" <i>(auto blow)</i>"; } 						
			$out='<span class=date>'.date("H:i",$str[2]).'</span> '.BNewRenderT($str[3]).' '.get_str_xtext($str[4]).' burn  '.$udar.' '.$str[12].$autot;

			break;
										
			}

 	return $out; 	
 	}
 	else
 	{
 	//вывод старого формата - без изменений
 	return $input_str;
 	}
return $str;
}

/// масивы для парсинга
function get_str_magic($id)
{
	include "abiltxt_eng.php";
	$text="<a style=\"cursor: pointer\" onMouseOut=\"HideThing(this);\" onMouseOver=\"ShowThing(this,25,25,'{$atext[$id]}');\" >".str_replace('/','',$abn[$id])."</a>";
	
	if ($text !='')
	{
	return $text;
	}
	else
	{
	return '';
	}
}


function get_str_xtext($id)
{
$txt[0]="";

$txt[1] = "<b> wait for the next wave of monsters .... </ b>!";

$txt[100] = "peredala leadership";
$txt[101] = "handed leadership";

$txt[110] = "lost leader";
$txt[111] = "lost the lead!";

$txt[121] = "new leader of the World!";
$txt[122] = "new leader of Darkness";
$txt[123] = "Neutrals new leader!";

$txt[200] = "cried out";
$txt[201] = "cried out";

$txt[220] = "get";
$txt[221] = "get";

$txt[300] = "spawned his clone";
$txt[301] = "spawned his clone";

$txt[400] = "shouted: <b> Oops! Fresh krovushka s human! It's time to eat! </ b>";
$txt[401] = "shouted: <b> Oops! Fresh krovushka s human! It's time to eat! </ b>";

$txt[410] = "attacked and drank all his energy in";
$txt[411] = "attacked and drank all his energy in";

$txt[500] = "use the spell";
$txt[501] = "use the spell";

$txt[600] = "closed the fight against interference!";
$txt[601] = "closed the fight against interference!";

$txt[700] = "cried out";
$txt[701] = "cried out";

$txt[800] = "use first aid kit";
$txt[801] = "use first aid kit";

$txt[900] = "use the source of ancient and restored the level of life";
$txt[901] = "used source of ancient and restored the level of life";

$txt[1000] = "lured clone";
$txt[1001] = "lured clone";

$txt[1010] = "captured";
$txt[1011] = "captured";

$txt[1020] = "encouraged";
$txt[1021] = "called";

$txt[1030] = "set free";
$txt[1031] = "unleashed";

$txt[1040] = "received a bolt of lightning omission standard of living";
$txt[1041] = "got a lightning bolt omission standard of living";
$out=$txt[$id];


return $out;
}


function get_str_remont($id)
{

$txt[0] = '<small> (for publicity) <b> Repair shop OldBK </ b>. We give a second life to old things! </ Small> ';
$txt[1] = 'come into complete disrepair';
$txt[2] = 'needs to be repaired!';
$txt[3] = 'critically ill';
$out=$txt[$id];
if ($out=='') 
	{
	$out='debug: get_str_remont ';
	}

return $out;
}

function get_str_goin($id)
{


$txt[1] = 'intervened in the fight!';
$txt[2] = 'went into battle!';

$txt[101] = 'intervened in the fight!';
$txt[102] = 'went into battle!';

$txt[103] = 'furious intervened in the fight!';


$out=$txt[$id];
if ($out=='') 
	{
	$out='debug: get_str_goin ';
	}
$out='<b>'.$out.'</b>';

return $out;
}


function get_str_exit($id)
{
$txt[1] = 'came from the battle!';
$txt[2] = 'left the battle!';
$txt[3] = 'fled from the battle!';

$txt[101] = 'came from the battle!';
$txt[102] = 'left the battle!';
$txt[103] = 'fled from the battle!';

$out=$txt[$id];
if ($out=='') 
	{
	$out='debug get_str_exit ';
	}
$out='<b>'.$out.'</b>';

return $out;
}


function get_str_dohill($id)
{


$txt[1] = 'and restore the level of life';
$txt[101] = 'and restored the level of life';

$out=$txt[$id];
if ($out=='') 
	{
	$out='debug: get_str_dohill '.$id;
	}
return $out;
}



function get_str_use_mag($id)
{

$txt[1] = 'Use the spell energy recovery';
$txt[101] = 'Use the spell energy recovery';

$out=$txt[$id];
if ($out=='') 
	{
	$out='debug: get_str_use_mag '.$id;
	}
return $out;
}


function get_str_dead($id)
{

$txt[1] = 'died';
$txt[2] = 'lost';
$txt[3] = 'fell in battle!';
$txt[4] = 'left this world!';

$txt[101] = 'died';
$txt[102] = 'lost';
$txt[103] = 'fell in battle!';
$txt[104] = 'passed away';

$out=$txt[$id];
if ($out=='') 
	{
	$out='debug: get_str_dead ';
	}
$out='<b>'.$out.'</b>';

return $out;
}

function get_str_text_udar($id) // текстовка по простому удару
{

$textudar[1] = "a run, slashed ";
$textudar[2] = "desperately pierced";
$textudar[3] = "reluctantly pricked";
$textudar[4] = "without thinking, slashed";
$textudar[5] = "smiling, leaving a gaping stab";
$textudar[6] = "put a blow";
$textudar[7] = "blow";
$textudar[8] = "foolishly hit";

$textudar[101] = "a run, slashed";
$textudar[102] = "desperately pierced";
$textudar[103] = "reluctantly pricked";
$textudar[104] = "without thinking, slashed";
$textudar[105] = "smiling, slammed a shot";
$textudar[106] = "put a blow";
$textudar[107] = "blow";
$textudar[108] = "foolishly hit";


$out=$textudar[$id];
if ($out=='') 
	{
	$out='debug: get_str_text_udar ';
	}

return $out;
}

function get_str_text_krita($id) // текстовка по крит пробив блок
{

$textkrita[1] = "scare everyone, quietly came up behind him hit, breaking the block, cobblestone";
$textkrita[2] = "breaking unit, gently wrung her hands behind her opponent hit";
$textkrita[3] = "breaking unit, scratched his nose opponent, hit";
$textkrita[4] = "breaking unit, stepped on the foot of the enemy, hit";
$textkrita[5] = "breaking unit, was bitten on the nose of the enemy, hit";
$textkrita[6] = "breaking unit had a terrible shot through the navel opponent, hit";

$textkrita[101] = "scare everyone, quietly came up behind him hit the ball block cobblestone";
$textkrita[102] = "breaking unit, gently wrung his hands behind the opponent, hit";
$textkrita[103] = "breaking unit, scratched his nose opponent, hit";
$textkrita[104] = "breaking unit, stepped on the foot of the enemy, hit";
$textkrita[105] = "breaking unit, bitten on the nose of the enemy, hit";
$textkrita[106] = ", breaking the block had a terrible shot through the navel opponent, hit";


$out=$textkrita[$id];
if ($out=='') 
	{
	$out='debug: get_str_text_kritа ';
	}

return $out;
}

function get_str_text_krit($id) // текстовка для грита
{

$textkrit[1] = "scare everyone, quietly came up behind him hit the cobblestones opponent";
$textkrit[2] = ", saying \" BU! \", gently wrung her hands behind her opponent hit";
$textkrit[3] = "relaxing, scratched his nose opponent and hit";
$textkrit[4] = "showing two fingers, stepped on the foot and hit the enemy";
$textkrit[5] = "scaring everyone was bitten on the nose and hit the enemy";
$textkrit[6] = "cursing this site had a terrible shot through the navel opponent, hit";

$textkrit[101] = "scare everyone, quietly came up behind him hit the cobblestones opponent";
$textkrit[102] = ", saying \" BU! \", gently wrung his hands behind the opponent, hit";
$textkrit[103] = "relaxing, scratched his nose opponent and hit";
$textkrit[104] = "showing two fingers, stepped on the foot and hit the enemy";
$textkrit[105] = "scaring everyone was bitten on the nose and hit the enemy";
$textkrit[106] = "cursing this site had a terrible shot through the navel opponent, hit";

$out=$textkrit[$id];
if ($out=='') 
	{
	$out='debug: get_str_text_krit ';
	}

return $out;
}

function get_str_text_kuda($id) 
{
// Where the beat
$udars[11] = 'nose';
$udars[12] = 'eye';
$udars[13] = 'jaw';
$udars[14] = 'on the nose';
$udars[15] = "Adam's apple";
$udars[16] = 'over the head';
$udars[17] = 'right eye';
$udars[18] = 'left eye';
$udars[19] = 'a cheekbone';

$udars[21] = 'a breast';
$udars[22] = 'in housing';
$udars[23] = 'solar plexus';
$udars[24] = 'near';
$udars[25] = 'sideways';
$udars[26] = 'to the blades';
$udars[27] = 'stomach on';
$udars[28] = "left arm";
$udars[29] = 'right hand';

$udars[31] = 'on <censored>';
$udars[32] = 'groin';
$udars[33] = 'crotch';
$udars[34] = 'on the left buttock';
$udars[35] = 'right buttock';

$udars[41] = 'legs';
$udars[42] = 'in the region of the right heel';
$udars[43] = 'to left heel';
$udars[44] = 'on the kneecap';
$udars[45] = 'calves';


// text for horses
$udars[111] = 'nose';
$udars[112] = 'eye';
$udars[113] = 'jaw';
$udars[114] = 'on the nape';
$udars[115] = 'in the face';
$udars[116] = 'too tough';
$udars[117] = 'ears';
$udars[118] = 'neck';
$udars[119] = 'neck';

$udars[121] = 'chest';
$udars[122] = 'in housing';
$udars[123] = 'back';
$udars[124] = "right side";
$udars[125] = 'in the left side';
$udars[126] = 'to the blades';
$udars[127] = 'belly';
$udars[128] = 'belly';
$udars[129] = 'belly';

$udars[131] = 'the body';
$udars[132] = 'on the left buttock';
$udars[133] = 'under the tail';
$udars[134] = 'right buttock';
$udars[135] = 'tail on';

$udars[141] = 'legs';
$udars[142] = 'a right hoof';
$udars[143] = 'in the left hoof';
$udars[144] = 'on the front foot';
$udars[145] = 'on the back foot';

$out=$udars[$id];
if ($out=='') 
	{
	$out='debug: get_str_text_kuda';
	}

return $out;
}

function get_str_text_wep($id)
{

// fist
$textchem[1] = "breast";
$textchem[2] = "rib hands";
$textchem[3] = "forehead";
$textchem[4] = "fist";
$textchem[5] = "footed";
$textchem[6] = "left foot";
$textchem[7] = "right foot";
$textchem[8] = "knee";

// "noj"
$textchem[11] = "knife";
$textchem[12] = "with the back of a knife blade";
$textchem[13] = "handle knife";
$textchem[14] = "knife edge";

// "dubina"
$textchem[21] = "knotty stick";
$textchem[22] = "billet";
$textchem[23] = "heavy cudgel";
$textchem[24] = "club";
$textchem[25] = "hammer handle";

// "topor"
$textchem[31] = "an ax";
$textchem[32] = "ax";
$textchem[33] = "ax blade";
$textchem[34] = "halberd";
$textchem[35] = "heavy derzhak";
$textchem[36] = "long ax";

// "mec"

$textchem[41] = "sheath";
$textchem[42] = "hilt";
$textchem[43] = "sword";
$textchem[44] = "blade of the sword";
$textchem[45] = "hilt";
$textchem[46] = "blunt edge";
$textchem[47] = "sharp side of the sword";
$textchem[48] = "huge sword";

// "buket" =>
$textchem[51] = "bunch of flowers";
$textchem[52] = "broom";
$textchem[53] = "bouquet";
$textchem[54] = "thorns";
$textchem[55] = "sheaf";
$textchem[56] = "stem";
$textchem[57] = "leaves";
$textchem[58] = "bud";

// "luk" =>
$textchem[61] = "arrow";

// "meshok"
$textchem[71] = "bag";

// "Loshad" =>
$textchem[81] = "hoof";
$textchem[82] = "tail";
$textchem[83] = "body";
$textchem[84] = "back foot";
$textchem[85] = "front foot";
$textchem[86] = "hind hooves";
$textchem[87] = "jaws";
$textchem[88] = "scruff";

// "elka" =>
$textchem[91] = "heavy barrel";
$textchem[92] = "crown of trees";
$textchem[93] = "stump trunk";
$textchem[94] = "fir branch";
$textchem[95] = "tree trunk";
$textchem[96] = "big spruce";
$textchem[97] = "green stem";
$textchem[98] = "fluffy spruce";

// "kostil" =>
$textchem[101] = "crutch";
$textchem[102] = "bar crutch";
$textchem[103] = "tube crutch";

// "ins"
$textchem[104]="тяжелым инструментом";
$textchem[105]="рукоятью инструмента";
$textchem[106]="торцом инструмента";
$textchem[107]="основанием инструмента";
$textchem[108]="острым углом инструмента";
$textchem[109]="поцарапанным инструментом";
$textchem[110]="личным инструментом";
$textchem[111]="инструментом";


$out=$textchem[$id];
if ($out=='') 
	{
	$out='by hand';
	}

return $out;
}

function get_str_text_fail($id)
{
//Ж
$textfail[1] = 'thinking about <censored>, whereby';
$textfail[2] = 'tried to attack, but';
$textfail[3] = 'slipped, and';
$textfail[4] = 'tried to carry out a strike, but ';
$textfail[5] = 'coughed, and';
$textfail[6] = 'tried to carry out a strike, but ';
$textfail[7] = 'lost self-control, whereby';
$textfail[8] = 'was not thinking about that, and';
// M
$textfail[101] = 'thinking about <censored>, whereby';
$textfail[102] = 'tried to hit, but ';
$textfail[103] = 'slipped, and';
$textfail[104] = 'tried to carry out a strike, but ';
$textfail[105] = 'coughed, and';
$textfail[106] = 'tried to carry out a strike, but ';
$textfail[107] = 'lost self-control, so that';
$textfail[108] = 'was not thinking about that, and';
$out=$textfail[$id];
if ($out=='') 
	{
	$out='debug: get_str_text_fail ';
	}

return $out;
}

function get_str_text_ud($id) // текстовка по попаданию
{

$textud[1] = 'forgotten, and here';
$textud[2] = 'hesitated, and it';
$textud[3] = 'confused when suddenly';
$textud[4] = 'picking his teeth, and then';
$textud[5] = 'choked but suddenly';
$textud[6] = 'tried to say something but suddenly, unexpectedly';
$textud[7] = 'confused when suddenly';
$textud[8] = 'staring at <censored>, but this time';
$textud[9] = 'blew her nose, and at this time';
$textud[10] = 'was not thinking about that, and';
$textud[11] = 'came to, but at this time';
$textud[12] = 'turned as suddenly';

$textud[101] = 'forgotten, and here';
$textud[102] = 'hesitated, and it';
$textud[103] = 'confused when suddenly';
$textud[104] = 'picking his teeth, and then';
$textud[105] = 'choked but suddenly';
$textud[106] = 'tried to say something but suddenly, unexpectedly';
$textud[107] = 'confused when suddenly';
$textud[108] = 'staring at <censored>, but this time';
$textud[109] = 'blew his nose, and at this time';
$textud[110] = 'was not thinking about that, and';
$textud[111] = 'came to, but at this time';
$textud[112] = 'turned suddenly as';


$out=$textud[$id];
if ($out=='') 
	{
	$out='debug: get_str_text_ud ';
	}

return $out;
}

function get_str_text_hark($id) // характер текстовка для лога
{

$hark[101] = 'insensitive';
$hark[102] = 'frustrated';
$hark[103] = 'brave';
$hark[104] = 'mad';
$hark[105] = 'intrepid';
$hark[106] = 'evil';
$hark[107] = 'cruel';
$hark[108] = 'arrogant';
$hark[109] = 'furious';
$hark[110] = 'Advanced';
$hark[111] = 'cunning';
$hark[112] = 'brave';
$hark[113] = 'brave';
$hark[114] = 'brave';
$hark[115] = 'brave';
$hark[116] = 'brave';

$hark[1] = 'unfeeling';
$hark[2] = 'upset';
$hark[3] = 'brave';
$hark[4] = 'distraught';
$hark[5] = 'undaunted';
$hark[6] = 'evil';
$hark[7] = 'cruel';
$hark[8] = 'arrogant';
$hark[9] = 'angry';
$hark[10] = 'advanced';
$hark[11] = 'tricky';
$hark[12] = 'beautiful';
$hark[13] = 'brave';
$hark[14] = 'brave';
$hark[15] = 'brave';
$hark[16] = 'brave';

$out=$hark[$id];

if ($out=='') 
	{
	$out='debug: get_str_text_hark ';
	}

return $out;
}

function get_str_text_uvorot($id) // текстовка по увороту
{
 
$textuvorot[101] = " <font color=green><B>dodged</B></font> a blow ";
$textuvorot[102] = " <font color=green><B>dodged</B></font> a blow ";
$textuvorot[103] = " <font color=green><B>rebounded from </B></font> a blow ";
$textuvorot[104] = " <font color=green><B>jumped from</B></font> a blow ";

$textuvorot[1] = " <font color=green><B>dodged</B></font> a blow ";
$textuvorot[2] = " <font color=green><B>увернулась</B></font> a blow ";
$textuvorot[3] = " <font color=green><B>rebounded from </B></font> a blow ";
$textuvorot[4] = " <font color=green><B>jumped from</B></font> a blow ";


$out=$textuvorot[$id];

if ($out=='') 
	{
	$out='debug: get_str_text_uvorot ';
	}

return $out;
}

function get_str_comment ($id) // комментатор 
 {
$boycom[1] = 'And you dance better.';
$boycom[2] = 'And what are we, here we play hide and seek?';
$boycom[3] = 'And you did penguins have never seen?';
$boycom[4] = 'And, because once you were beautiful ... And now? Well faces! Horror! ';
$boycom[5] = 'And then I will kick the corpse.';
$boycom[6] = 'And last night I was spying on the neighbors. They just tumbled ';
$boycom[7] = 'But you live people Dubas ...';
$boycom[8] = 'But yesterday I was at the zoo ...';
$boycom[9] = 'And you did not serve in the construction battalion?';
$boycom[10] = 'Have you seen that way on the street doing !?';
$boycom[11] = 'Did you know that the hedgehogs proliferate on the Internet?';
$boycom[12] = 'And to live it as you want ...';
$boycom[13] = 'And because of what you actually fights?';
$boycom[14] = 'And what rzhёte, you still have not seen the others';
$boycom[15] = 'And what will happen if you half to death twice ispugaeshsya ?!';
$boycom[16] = "Do not do. You're not a sadist? ";
$boycom[17] = 'No comment ...';
$boycom[18] = 'It hurts, you know!';
$boycom[19] = 'Quickly monitor you for hiding!';
$boycom[20] = 'Everyone wants to go to heaven, but nobody wants to die!';
$boycom[21] = 'yesterday with a girl he met.';
$boycom[22] = 'Only 5 minutes familiar and fighting like a couple with 20 years of experience ...';
$boycom[23] = 'All. I can not go on. ';
$boycom[24] = 'In the end, someone will win?';
$boycom[25] = 'What are you, a tree fell? ';
$boycom[26] = 'potter how sleepy flies ... let me tell you a better anecdote: ...';
$boycom[27] = 'You see how useful brush your teeth at night?';
$boycom[28] = 'Here you hands Mahal, and for all you have.';
$boycom[29] = 'Here you will be taken prisoner, and there you will long to beat. But you did not tell ... and not because you are so persistent, you just do not know anything ';
$boycom[30] = "You'd better go some training! ";
$boycom[31] = 'Are you still knead? Call when bone meal will knead each other. ';
$boycom[32] = 'You fighters! Have a conscience! ';
$boycom[33] = 'Gassi imbecile!';
$boycom[34] = 'Yes, if I could stop it, it would have received the Nobel Prize "for peace"';
$boycom[35] = 'Yes, where they beat ?!';
$boycom[36] = 'Come quickly! For all of you already formed. ';
$boycom[37] = "Let's do today timeout. Huh? And then I have nightmares will soon be dreaming. ";
$boycom[38] = 'fight like girls!';
$boycom[39] = 'Children, look left ... Oh! .. No, there better not to watch.';
$boycom[40] = 'If this continues, we will soon go to sleep!';
$boycom[41] = 'If I had a rocking chair, I would be in it rocked ...';
$boycom[42] = 'If you say something you want, it is better to be silent :)';
$boycom[43] = 'The cruelty is no vice.';
$boycom[44] = "Life is our club - it's a waste of oxygen !!! ";
$boycom[45] = 'COOL! Davie! Bite! Scrabble! ';
$boycom[46] = 'For such battles have to send in the chaos!';
$boycom[47] = 'Do you know where in a thrift store so many things? This is me after your gulyanok collect and hand over there. Sometimes along with parts of the body, stuck in them. ';
$boycom[48] = 'There are people so close to each other. Otherwise you can not just hit. ';
$boycom[49] = 'And shed blood still throbs ...';
$boycom[50] = 'Disabled divorced ...';
$boycom[51] = 'What fight !!!';
$boycom[52] = "Who !? Who's there ?! ";
$boycom[53] = 'Who taught you this?';
$boycom[54] = 'Grasshopper, damn ...';
$boycom[55] = 'Import Buy turntable.';
$boycom[56] = 'Horse go!';
$boycom[57] = 'better enemy than a friend - the enemy.';
$boycom[58] = "Okay, you're here as long as each other by the hair to drag, and I go to lunch.";
$boycom[59] = 'I am your ballet already tired!';
$boycom[60] = 'Maybe it will still real fight ???';
$boycom[61] = 'Thoughts climb to the head from inside and outside the blows.';
$boycom[62] = 'Well, where is your crown blows? Where scenic fall I ask! ';
$boycom[63] = 'Well, you can not so backhand smash!';
$boycom[64] = 'We should have thought before, now mortally late ...';
$boycom[65] = 'At such a sight you can sell tickets. Uhohochetsya people ';
$boycom[66] = 'No! No need to fight! And ... well fight, still do not know. ';
$boycom[67] = 'No, well, must be a reason, there must be a reason?';
$boycom[68] = 'No, I refuse to comment on it!';
$boycom[69] = 'Do not break off such';
$boycom[70] = 'Well, you drank a glass, well ... well, two liter, well, two ... so why then contrive a fight ?! ';
$boycom[71] = 'And who is behind this mayhem will pay?';
$boycom[72] = 'So you grin. Brass knuckles of your smile can do. ';
$boycom[73] = 'Well, what are you ..? Do not be sad. Above his head, so it is more convenient to get there. ';
$boycom[74] = 'Nothing ... Block also hit.';
$boycom[75] = 'Turn around !!! .... Too late ...';
$boycom[76] = 'Wow! Teach me not to do it. ';
$boycom[77] = 'Warning! Do the hole, not zaplombiruesh! ';
$boycom[78] = 'It you want ??? ';
$boycom[79] = 'The usual thing ... there is something to come unstuck.';
$boycom[80] = "Oh, chattering too much and I'm with you ...";
$boycom[81] = 'He did not promahnёtsya if you do not otoydёsh!';
$boycom[82] = 'In my opinion, someone shines a disability.';
$boycom[83] = 'Throw him a rake that he still did not come.';
$boycom[84] = "cat Leopold was right, let's live together?";
$boycom[85] = 'When you hit in the stomach breaks down the acid-alkaline balance.';
$boycom[86] = 'Check for sticking Do you have a knife from the stomach.';
$boycom[87] = 'Stop me scream!';
$boycom[88] = 'Throw him a rake that he still did not come.';
$boycom[89] = 'Leap here as fleas ... Everything I went for dichlorvos';
$boycom[90] = 'Wake me up when this is over pornography ...';
$boycom[91] = 'The child would hit harder!';
$boycom[92] = 'Nice vmazat!';
$boycom[93] = "It's nice they are having fun";
$boycom[94] = 'Look here for you, and tears welling.';
$boycom[95] = 'Please learn to walk, and then only in the fight climb.';
$boycom[96] = 'So they are to each other to break something.';
$boycom[97] = 'So you give him all the bones broken!';
$boycom[98] = 'In my porch just otmudohali neighbor';
$boycom[99] = 'squalid divorced ...';
$boycom[100] = 'Wow, what a quick';
$boycom[101] = 'Fascist !! It should be there, so it hurts to embed ... ';
$boycom[102] = 'enough to beat him on the corner of my cabin! I also then fix it. ';
$boycom[103] = 'Hooligans, stop immediately!';
$boycom[104] = 'Do you want prompt, where he hit?';
$boycom[105] = 'Well, I have more than sleight of you all, and then only you and me in a wheelchair would be planted.';
$boycom[106] = 'good fight';
$boycom[107] = 'A good blow!';
$boycom[108] = 'Hilyak-gap';
$boycom[109] = 'What do you grabbed him by the hair ?! Let go now! ';
$boycom[110] = "Right now, I will overtake you, that's when we pohohochem";
$boycom[111] = 'It was something unknown to me technique ...';
$boycom[112] = 'This is the enemy, not clay! Enough to crush! ';
$boycom[113] = "This is not a fight, it's humiliating beating.";
$boycom[114] = 'This diminished arrogance';
$boycom[115] = 'That was your plan "B"?';
$boycom[116] = 'It was something unknown to me technique ...';
$boycom[117] = 'I warned - will be hurt.';
$boycom[118] = 'I do not suffer from insanity. I enjoy every minute of it :) ';
$boycom[119] = "I'm beautiful, I'm strong, I'm smart, I'm good. But you? You yourself saw something ?! ";
$boycom[120] = 'I, too, know how to fight, but I will not ...';
$boycom[121] = "(anxiously looking around) I'll tell you a secret ... watching you!";
$boycom[122] = '<censored> after the fight I of <censored> in both <censored> and <censored>';
$boycom[123] = '<censored> karate sucks';

// football
$boycom[201] = 'Well, who so pass it gives something ?!';
$boycom[202] = 'Taaaak. After the game is not consumed. Waiting for everyone in the locker room. ';
$boycom[203] = "We are here with the judges conferred and decided. Added time will be 45 minutes. Wait - do not faint - Well it's a joke. Only 3 minutes of course the same. ";
$boycom[204] = 'Here you feint, feint, and to sense? Still shot down. ';
$boycom[205] = 'Mdaaaaa. Not the team sent from Honduras. ';
$boycom[206] = 'Yes, where are you then bubbled ball. To me they then after you collect necessary. ';
$boycom[207] = 'Here you are all bёtes-bёtes and confusing as the match France - Greece.';
$boycom[208] = 'is not right you Uncle Theodore ball is built up, the needle is necessary to keep the bottom.';
$boycom[209] = "Ehhh. Sorry I'm in Cape Town is not gone, would show you all his impeccable technique. ";
$boycom[210] = 'And I July 11 final judge invited - and I did not go. How can you throw it? ';
$boycom[211] = 'Here it happens: one team attacks and the other misses.';
$boycom[212] = 'I have not seen you in any case, not without cause.';
$boycom[213] = 'In the second half of the game was almost no players in the main stand, arguing with the referee.';
$boycom[214] = 'I did not understand what part of the body, he hit the gate';
$boycom[215] = "... and falls forward in the penalty area! What's that sound? Snoring ?! O_O ";
$boycom[216] = 'Who monitors the state of the field - it is not even a lawn, this swamp!';
$boycom[217] = 'Even simulate and fall in the penalty area to the mind.';
$boycom[218] = "Well, let's assume this episode dangerous moment.";
$boycom[219] = 'Kritovik kick spent ... Do not believe me - crit and G-O-O-O-O-A-A-L';
$boycom[220] = 'The enemy gate we put dodge!';
$boycom[221] = 'Bloch ETI does not open - under penalty not to substitute!';
$boycom[222] = 'Tim all the King - Friendly barked "Ole!"';
$boycom[223] = 'You imagined himself as steep because the goalkeeper and catch all crits?';
$boycom[224] = 'And with such impact do you hope to reach the semifinals?';
$boycom[225] = "women's football team in the next field";
$boycom[226] = "Judge in the m-s-s-s-s-s-l-oh-oh-oh-oh-oh!";
$boycom[227] = 'Dear fans! Please do not throw the corpses on the playing field! Dribllingovat hard. ';
$boycom[228] = '<censored> karate sucks';
$out=$boycom[$id];

if ($out=='') 
	{
	$out='debug:  get_str_comment ';
	}

$out='<i>Commentator: '.$out.'</i>';
return $out;
			
}


function get_str_text_block($id) // текстовка по блоку
{
$textblock[1] = "blocked shots";
$textblock[2] = "stopped the blow";
$textblock[3] = "repulsed blow";

$textblock[101] = "blocked kick";
$textblock[102] = "blow down";
$textblock[103] = "parried a blow";

$out=$textblock[$id];
if ($out=='') 
	{
	$out='debug get_str_text_block ';
	}

return $out;
}

function BNewRenderT($in) {
	if (strpos($in,'|') !== FALSE) 
	{
	$v = explode("|",$in);
	if ($v[2]!='')	{ $nick=$v[2];  }  else { $nick=$v[0]; 	}

	if (strpos($nick,'Невидимка') !== FALSE) { $nick='<i> Invisible </i>'; }
	
	
	
	$out='<span class="B'.$v[1].'">'.$nick.'</span>';
	return $out;
	} else 
	{
	return $row;
	}
}


?>