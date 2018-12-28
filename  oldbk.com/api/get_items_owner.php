<?
if ($_GET[key]=='w5vm948ygu894w5uyvm84wr') //защита
{
include "/www/oldbk.com/connect.php";

function get_item_info($dress)
 {
 $out=htmlspecialchars(strip_tags($dress['name']))."\n"." ѕрочность ".$dress['duration']."/".$dress['maxdur']."\n".(($dress['ghp']>0)?"”ровень жизни +{$dress['ghp']}\n":"")."".(($dress['bron1']!=0)?"Х Ѕрон€ головы:{$dress['bron1']}\n":"")."".(($dress['bron2']!=0)?"Х Ѕрон€ корпуса:{$dress['bron2']}\n":"")."".(($dress['bron3']!=0)?"Х Ѕрон€ по€са:{$dress['bron3']}\n":"")."".(($dress['bron4']!=0)?"Х Ѕрон€ ног:{$dress['bron4']}\n":"")."".(($dress['text']!=null)?"Ќа одежде вышито: {$dress['text']}\n":"")."";
 
 if ($dress[type]==30)
 	{
 	$out.=" ”ровень: {$dress[up_level]}";
 	}
 
 return  $out;
 }
	$_GET[sergi]=(int)$_GET[sergi];
	$_GET[kulon]=(int)$_GET[kulon];
	$_GET[perchi]=(int)$_GET[perchi];
	$_GET[weap]=(int)$_GET[weap];
	$_GET[bron]=(int)$_GET[bron];
	$_GET[r1]=(int)$_GET[r1];
	$_GET[r2]=(int)$_GET[r2];
	$_GET[r3]=(int)$_GET[r3];
	$_GET[helm]=(int)$_GET[helm];
	$_GET[shit]=(int)$_GET[shit];
	$_GET[boots]=(int)$_GET[boots];
	$_GET[nakidka]=(int)$_GET[nakidka];
	$_GET[rubashka]=(int)$_GET[rubashka];
	$_GET[runa1]=(int)$_GET[runa1];
	$_GET[runa2]=(int)$_GET[runa2];
	$_GET[runa3]=(int)$_GET[runa3];

	$get_all_items=mysql_query("select * from oldbk.inventory where id in ({$_GET[sergi]},{$_GET[kulon]},{$_GET[perchi]},{$_GET[weap]},{$_GET[bron]},{$_GET[r1]},{$_GET[r2]},{$_GET[r3]},{$_GET[helm]},{$_GET[shit]},{$_GET[boots]},{$_GET[nakidka]},{$_GET[rubashka]},{$_GET[runa1]},{$_GET[runa2]},{$_GET[runa3]})" );
	
	while($row = mysql_fetch_array($get_all_items)) 
	{
		$items_array[$row[id]]=$row;
	}

	if ($items_array[$_GET[sergi]]>0) echo "<sergi img=\"".(($items_array[$_GET[sergi]][add_pick]!=null)?$items_array[$_GET[sergi]][add_pick]:$items_array[$_GET[sergi]][img])."\"> ".get_item_info($items_array[$_GET[sergi]])." </sergi>\n";
	if ($items_array[$_GET[kulon]]>0) echo "<kulon img=\"".(($items_array[$_GET[kulon]][add_pick]!=null)?$items_array[$_GET[kulon]][add_pick]:$items_array[$_GET[kulon]][img])."\"> ".get_item_info($items_array[$_GET[kulon]])." </kulon>\n";
	if ($items_array[$_GET[perchi]]>0) echo "<perchi img=\"".(($items_array[$_GET[perchi]][add_pick]!=null)?$items_array[$_GET[perchi]][add_pick]:$items_array[$_GET[perchi]][img])."\"> ".get_item_info($items_array[$_GET[perchi]])." </perchi>\n";	
	if ($items_array[$_GET[weap]]>0) echo "<weap img=\"".(($items_array[$_GET[weap]][add_pick]!=null)?$items_array[$_GET[weap]][add_pick]:$items_array[$_GET[weap]][img])."\"> ".get_item_info($items_array[$_GET[weap]])." </weap>\n";	
	if ($items_array[$_GET[bron]]>0) echo "<bron img=\"".(($items_array[$_GET[bron]][add_pick]!=null)?$items_array[$_GET[bron]][add_pick]:$items_array[$_GET[bron]][img])."\"> ".get_item_info($items_array[$_GET[bron]])." </bron>\n";	
	if ($items_array[$_GET[r1]]>0) echo "<r1 img=\"".(($items_array[$_GET[r1]][add_pick]!=null)?$items_array[$_GET[r1]][add_pick]:$items_array[$_GET[r1]][img])."\"> ".get_item_info($items_array[$_GET[r1]])." </r1>\n";	
	if ($items_array[$_GET[r2]]>0) echo "<r2 img=\"".(($items_array[$_GET[r2]][add_pick]!=null)?$items_array[$_GET[r2]][add_pick]:$items_array[$_GET[r2]][img])."\"> ".get_item_info($items_array[$_GET[r2]])." </r2>\n";			
	if ($items_array[$_GET[r3]]>0) echo "<r3 img=\"".(($items_array[$_GET[r3]][add_pick]!=null)?$items_array[$_GET[r3]][add_pick]:$items_array[$_GET[r3]][img])."\"> ".get_item_info($items_array[$_GET[r3]])." </r3>\n";			
	if ($items_array[$_GET[helm]]>0) echo "<helm img=\"".(($items_array[$_GET[helm]][add_pick]!=null)?$items_array[$_GET[helm]][add_pick]:$items_array[$_GET[helm]][img])."\"> ".get_item_info($items_array[$_GET[helm]])." </helm>\n";			
	if ($items_array[$_GET[shit]]>0) echo "<shit img=\"".(($items_array[$_GET[shit]][add_pick]!=null)?$items_array[$_GET[shit]][add_pick]:$items_array[$_GET[shit]][img])."\"> ".get_item_info($items_array[$_GET[shit]])." </shit>\n";				
	if ($items_array[$_GET[boots]]>0) echo "<boots img=\"".(($items_array[$_GET[boots]][add_pick]!=null)?$items_array[$_GET[boots]][add_pick]:$items_array[$_GET[boots]][img])."\"> ".get_item_info($items_array[$_GET[boots]])." </boots>\n";				
	if ($items_array[$_GET[nakidka]]>0) echo "<nakidka img=\"".(($items_array[$_GET[nakidka]][add_pick]!=null)?$items_array[$_GET[nakidka]][add_pick]:$items_array[$_GET[nakidka]][img])."\"> ".get_item_info($items_array[$_GET[nakidka]])." </nakidka>\n";					
	if ($items_array[$_GET[rubashka]]>0) echo "<rubashka img=\"".(($items_array[$_GET[rubashka]][add_pick]!=null)?$items_array[$_GET[rubashka]][add_pick]:$items_array[$_GET[rubashka]][img])."\"> ".get_item_info($items_array[$_GET[rubashka]])." </rubashka>\n";						
	if ($items_array[$_GET[runa1]]>0) echo "<runa1 img=\"".(($items_array[$_GET[runa1]][add_pick]!=null)?$items_array[$_GET[runa1]][add_pick]:$items_array[$_GET[runa1]][img])."\"> ".get_item_info($items_array[$_GET[runa1]])." </runa1>\n";						
	if ($items_array[$_GET[runa2]]>0) echo "<runa2 img=\"".(($items_array[$_GET[runa2]][add_pick]!=null)?$items_array[$_GET[runa2]][add_pick]:$items_array[$_GET[runa2]][img])."\"> ".get_item_info($items_array[$_GET[runa2]])." </runa2>\n";						
	if ($items_array[$_GET[runa3]]>0) echo "<runa3 img=\"".(($items_array[$_GET[runa3]][add_pick]!=null)?$items_array[$_GET[runa3]][add_pick]:$items_array[$_GET[runa3]][img])."\"> ".get_item_info($items_array[$_GET[runa3]])." </runa3>\n";						
}
?>