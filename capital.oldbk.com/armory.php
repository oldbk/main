<?
session_start();
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die();}
		include ("connect.php");		
		include "functions.php";
		if (($user['battle']>0) OR ($user['battle_fin'] >0))  { header("Location: fbattle.php"); die(); }
		if (($user['room']!=197) AND ($user['room']!=199))
		    { 
		    header("Location: main.php"); die(); 
		    }
		
//		mysql_query('START TRANSACTION') or die();
    
		 //Загружаем все комплекты кроме 0-го - его нельзя
		 $all_kom=mysql_query("select * from oldbk.users_profile where owner='{$user[id]}' and prof>0 ORDER BY def DESC ") or die(); 
		 $kom_kol=mysql_num_rows($all_kom);
		 $next_id=1;$update=0;$defaltis=0;
		 $arrkom=array();
		 if ($kom_kol>0)
		 {
		 
		 while ($arrkom= mysql_fetch_array($all_kom))
			{
			if ($arrkom['pname']==$_POST[savecomplect]) {$update=1;} //пригодится потом
			if ($arrkom['def'] >0) {$defaltis=$arrkom['prof'];}
			if ($next_id < $arrkom['prof']) {$next_id=$arrkom['prof'];} // запоминаем последнее значение номера профиля
			$arr_kom[$arrkom['prof']]=$arrkom;
			}
		 }
		 

if ($user[level]<=7)
	 	{
	 	$seven_level=' and id not in (31,16,174) ';
	 	}
	 	else
	 	{
	 	$seven_level='';
	 	}		 
		    
/////работа 


function chck_scrolls_prem () {
//echo "RUN TEST";
		global $user, $mysql;
		//Делаем запрос ищим  свитки которые не могут висеть
		//Уравнялка склонки
		$ch_al=$user['align']; if(($user['align']>=1.3) AND ($user['align']<1.4)) { $ch_al=6; }
		if ($user['klan']=='pal') { $ch_al=6; }
		$isql="SELECT * FROM oldbk.inventory WHERE dressed=1 and owner='{$user[id]}' AND type=12 AND
		(
		nsila > {$user[sila]} OR nlovk > {$user[lovk]} OR ninta > {$user[inta]} OR nvinos > {$user[vinos]} OR
		nintel > {$user[intel]} OR nmudra > {$user[mudra]} OR nlevel > {$user[level]} OR
		((nalign != '".((int)$ch_al)."' ) AND (nalign != 0)) OR nnoj > {$user[noj]} OR ntopor > {$user[topor]} OR
		ndubina > {$user[dubina]} OR nmech > {$user[mec]} OR nfire > {$user[mfire]} OR
		nwater > {$user[mwater]}  OR nair > {$user[mair]} OR nearth > {$user[mearth]} OR
		nlight > {$user[mlight]} OR ngray > {$user[mgray]} OR ndark > {$user[mdark]} ) GROUP BY prototype ; ";
//echo $isql;		
 $items= mysql_query($isql) or die();
 $kom_it=mysql_num_rows($items);
		 if ($kom_it>0)
		 {
		 $SVIT='';
		 //есть такой свиток надо его записать
		 while ($arrit= mysql_fetch_array($items))
			{
			//echo 'k<br>';
			if ($arrit[nsila] >  $user[sila]) {$SVIT.='Силы:'.($arrit[nsila]-$user[sila]).',';}
			if ($arrit[nlovk] >  $user[lovk]) {$SVIT.=' Ловкости:'.($arrit[nlovk]-$user[lovk]).',';}
			if ($arrit[ninta] >  $user[inta]) {$SVIT.=' Интуиции:'.($arrit[ninta]-$user[inta]).',';}
			if ($arrit[nvinos] >  $user[vinos]){$SVIT.=' Выносливости:'.($arrit[nvinos]-$user[vinos]).',';}
			if ($arrit[nintel] >  $user[intel]) {$SVIT.=' Интеллект:'.($arrit[nintel]-$user[intel]).',';}
			if ($arrit[nmudra] >  $user[mudra]) {$SVIT.=' Мудрости:'.($arrit[nmudra]-$user[mudra]).',';}
			if ($arrit[nlevel] >  $user[level]){$SVIT.=' Уровня:'.($arrit[nlevel]-$user[level]).',';}
			if (($arrit[nalign] != ((int)$ch_al)) AND ($arrit[nalign] != 0)) {$SVIT.=' Склонность не та';}
			if ($arrit[nnoj] >  $user[noj]) {$SVIT.=' Владений ножами и кастетами:'.($arrit[nnoj]-$user[noj]).', ';}
			if ($arrit[ntopor] >  $user[topor]) {$SVIT.=' Владений топорами и секирами:'.($arrit[ntopor]-$user[topor]).', ';}
			if ($arrit[ndubina] >  $user[dubina]) {$SVIT.=' Владений дубинами, булавами:'.($arrit[ndubina]-$user[dubina]).', ';}
			if ($arrit[nmech] >  $user[mec]) {$SVIT.=' Владений мечами:'.($arrit[nmec]-$user[mec]).', ';}
			if ($arrit[nfire] >  $user[mfire]){$SVIT.=' Владений Стихии огня:'.($arrit[nfire]-$user[mfire]).', ';}
			if ($arrit[nwater] >  $user[mwater]){$SVIT.=' Владений Стихии воды:'.($arrit[nwater]-$user[mwater]).', ';}
			if ($arrit[nair] >  $user[mair]) {$SVIT.=' Владений Стихии воздуха:'.($arrit[nair]-$user[mair]).', ';}
			if ($arrit[nearth] >  $user[mearth]){$SVIT.=' Владений Стихии земли:'.($arrit[nearth]-$user[mearth]).', ';}
			if ($arrit[nlight] >  $user[mlight]) {$SVIT.=' Владений Магии Света:'.($arrit[nlight]-$user[mlight]).', ';}
			if ($arrit[ngray] >  $user[mgray]) {$SVIT.=' Владений Серой магии:'.($arrit[ngray]-$user[mgray]).', ';}
			if ($arrit[ndark] >  $user[mdark]) {$SVIT.=' Владений Магии Тьмы:'.($arrit[ndark]-$user[mdark]).', ';}
			}
		 echo "<b>Внимание!<b> Чтобы удержать надетые свитки Вам не хватает:<br>".$SVIT."<br>"; 
		 return false;			
		 }
		 else
		 {
		 //все вещи свитки норм
		 return true;
		 }
}


function chck_items_prem () {
//echo "RUN TEST";
		global $user, $mysql;

		//Делаем запрос ищим прдедметы которые не могут висеть- кроме свитков
		//Уравнялка склонки
		$ch_al=$user['align']; if(($user['align']>=1.3) AND ($user['align']<1.4)) { $ch_al=6; }
		if ($user['klan']=='pal') { $ch_al=6; }
		$isql="SELECT * FROM oldbk.inventory WHERE dressed=1 and owner='{$user[id]}' AND type!=12 AND
		(
		nsila > {$user[sila]} OR nlovk > {$user[lovk]} OR ninta > {$user[inta]} OR nvinos > {$user[vinos]} OR
		nintel > {$user[intel]} OR nmudra > {$user[mudra]} OR nlevel > {$user[level]} OR
		((nalign != '".((int)$ch_al)."' ) AND (nalign != 0)) OR nnoj > {$user[noj]} OR ntopor > {$user[topor]} OR
		ndubina > {$user[dubina]} OR nmech > {$user[mec]} OR nfire > {$user[mfire]} OR
		nwater > {$user[mwater]}  OR nair > {$user[mair]} OR nearth > {$user[mearth]} OR
		nlight > {$user[mlight]} OR ngray > {$user[mgray]} OR ndark > {$user[mdark]} ) GROUP BY prototype ; ";
//echo $isql;		
 $items= mysql_query($isql) or die();
 $kom_it=mysql_num_rows($items);
		 if ($kom_it>0)
		 {
		 $SVIT='';
		 //есть такой надо его записать
		 while ($arrit= mysql_fetch_array($items))
			{
			//echo 'k<br>';
			if ($arrit[nsila] >  $user[sila]) { if ($dsila<($arrit[nsila]-$user[sila])) { $dsila=($arrit[nsila]-$user[sila]); $N_sila='Силы:'.$dsila.','; } }
			if ($arrit[nlovk] >  $user[lovk]) { if ($dlovk<($arrit[nlovk]-$user[lovk])) { $dlovk=($arrit[nlovk]-$user[lovk]); $N_lovk=' Ловкости:'.$dlovk.',';} }
			if ($arrit[ninta] >  $user[inta]) { if ($dinta<($arrit[ninta]-$user[inta])) { $dinta=($arrit[ninta]-$user[inta]); $N_inta=' Интуиции:'.$dinta.',';} }
			if ($arrit[nvinos] >  $user[vinos]){ if ($dvinos<($arrit[nvinos]-$user[vinos])) { $dvinos=($arrit[nvinos]-$user[vinos]); $N_vinos=' Выносливости:'.$dvinos.',';} }
			if ($arrit[nintel] >  $user[intel]){ if ($dnintel<($arrit[nintel]-$user[intel])) { $dnintel=($arrit[nintel]-$user[intel]); $N_intel=' Интеллект:'.$dnintel.',';}  }
			if ($arrit[nmudra] >  $user[mudra]) {  if ($dmudra<($arrit[nmudra]-$user[mudra])) { $dmudra=($arrit[nmudra]-$user[mudra]);  $N_mudra=' Мудрости:'.$dmudra.',';} }
			if ($arrit[nlevel] >  $user[level]){  if ($dlevel<($arrit[nlevel]-$user[level])) { $dlevel=($arrit[nlevel]-$user[level]);  $N_level=' Уровня:'.$dlevel.',';} }
			if (($arrit[nalign] != (int)($ch_al) ) AND ($arrit[nalign] != 0)) {$SVIT=' Склонность не та';}
			if ($arrit[nnoj] >  $user[noj]) { if ($dnoj<($arrit[nnoj]-$user[noj])) { $dnoj=($arrit[nnoj]-$user[noj]);  $N_noj=' Владений ножами и кастетами:'.$dnoj.', ';} }
			if ($arrit[ntopor] >  $user[topor]) { if ($dtopor<($arrit[ntopor]-$user[topor])) { $dtopor=($arrit[ntopor]-$user[topor]) ;    $N_dtopor=' Владений топорами и секирами:'.$dtopor.', ';} }
			if ($arrit[ndubina] >  $user[dubina]) { if ($ddubina<($arrit[ndubina]-$user[dubina])) { $ddubina=($arrit[ndubina]-$user[dubina]);  $N_dubina=' Владений дубинами, булавами:'.$ddubina.', ';}}
			if ($arrit[nmech] >  $user[mec]) { if ($dmec<($arrit[nmec]-$user[mec])) { $dmec=($arrit[nmec]-$user[mec]);  $N_mec=' Владений мечами:'.$dmec.', ';} }
			if ($arrit[nfire] >  $user[mfire]) { if ($dmfire<($arrit[nfire]-$user[mfire])) { $dmfire=($arrit[nfire]-$user[mfire]);   $N_mfire=' Владений Стихии огня:'.$dmfire.', ';}}
			if ($arrit[nwater] >  $user[mwater]) { if ($dmwater<($arrit[nwater]-$user[mwater])) { $dmwater=($arrit[nwater]-$user[mwater]);   $N_mwater=' Владений Стихии воды:'.$dmwater.', ';}}
			if ($arrit[nair] >  $user[mair]) { if ($dmair<($arrit[nair]-$user[mair])) { $dmair=($arrit[nair]-$user[mair]);   $N_mair=' Владений Стихии воздуха:'.$dmair.', ';}}
			if ($arrit[nearth] >  $user[mearth]) { if ($dmearth<($arrit[nearth]-$user[mearth])) { $dmearth=($arrit[nearth]-$user[mearth]);  $N_mearth=' Владений Стихии земли:'.$dmearth.', ';}}
			if ($arrit[nlight] >  $user[mlight]) { if ($dmlight<($arrit[nlight]-$user[mlight])) { $dmlight=($arrit[nlight]-$user[mlight]);  $N_mlight=' Владений Магии Света:'.$dmlight.', ';}}
			if ($arrit[ngray] >  $user[mgray]) { if ($dmgray<($arrit[ngray]-$user[mgray])) { $dmgray=($arrit[ngray]-$user[mgray]);  $N_mgray=' Владений Серой магии:'.$dmgray.', ';}}
			if ($arrit[ndark] >  $user[mdark]) { if ($dmdark<($arrit[ndark]-$user[mdark])) {  $dmdark=($arrit[ndark]-$user[mdark]);  $N_dmdark=' Владений Магии Тьмы:'.$dmdark.', ';}}
			}
		 $SVIT=$N_sila.$N_lovk.$N_inta.$N_vinos.$N_intel.$N_mudra.$N_level.$N_noj.$N_dtopor.$N_dubina.$N_mec.$N_mfire.$N_mwater.$N_mair.$N_mearth.$N_mlight.$N_mgray.$N_dmdark.$SVIT;	
		 echo "<font color=red><b>Внимание!<b> Чтобы удержать надетые вещи Вам не хватает:<br>".$SVIT."<br></font>"; 
		 return false;			
		 }
		 else
		 {
		 //все вещи свитки норм
			 if ( ($user[sergi]==0) or ($user[kulon]==0) or ($user[perchi]==0) or ($user[weap]==0) or ($user[bron]==0) or ($user[r1]==0)  or ($user[r2]==0)   or ($user[r3]==0) or ($user[helm]==0) or ($user[shit]==0) or ($user[boots]==0) )
			{
			 echo "<font color=red><b>Внимание!<b> Можно сохранять только полный комплект!</b></font><br>"; 
			 return false;			 	
	 		}		 
			else
			 {
			 return true;
			 }
		}
   
}



//обновление удержание предмета

function ref_drop_prem () {
//echo "RUN TEST";
		global $user, $mysql;
		//Делаем запрос ищим прдедметы которые не могут висеть- кроме свитков
		//Уравнялка склонки
		$ch_al=$user['align']; if(($user['align']>=1.3) AND ($user['align']<1.4)) { $ch_al=6; }
		if ($user['klan']=='pal') { $ch_al=6; }
		$isql="SELECT * FROM oldbk.inventory WHERE dressed=1 and owner='{$user[id]}' AND type!=12 AND
		(
		nsila > {$user[sila]} OR nlovk > {$user[lovk]} OR ninta > {$user[inta]} OR nvinos > {$user[vinos]} OR
		nintel > {$user[intel]} OR nmudra > {$user[mudra]} OR nlevel > {$user[level]} OR
		((nalign != '".((int)$ch_al)."' ) AND (nalign != 0)) OR nnoj > {$user[noj]} OR ntopor > {$user[topor]} OR
		ndubina > {$user[dubina]} OR nmech > {$user[mec]} OR nfire > {$user[mfire]} OR
		nwater > {$user[mwater]}  OR nair > {$user[mair]} OR nearth > {$user[mearth]} OR
		nlight > {$user[mlight]} OR ngray > {$user[mgray]} OR ndark > {$user[mdark]} ); ";
	
		//echo $isql;
 $items= mysql_query($isql) or die();
 $kom_it=mysql_num_rows($items);
		 if ($kom_it>0)
		 {
		 //есть такой надо его снять
		 while ($arrit= mysql_fetch_array($items))
			{
			//echo "Надо снять ид".$arrit['prof'];
			 if ($arrit['type']==5)
			 {
			 //если бля тип 5 кольцо - надо понять в каком оно слоту
			 
			  $rr=mysql_query("select id,r1,r2,r3 from users where id='{$user[id]}' and (r1='{$arrit['id']}' OR r2='{$arrit['id']}' OR r3='{$arrit['id']}')") or die("1");
			  $rings=mysql_fetch_array($rr);
			  if ($rings[r1]==$arrit['id'])
			  		{
  					dropitem_prem(5); // первое кольцо
			  		}
			  else
			  if ($rings[r2]==$arrit['id'])
			  		{
  					dropitem_prem(6); // 2-е
			  		}
			  else
			  if ($rings[r3]==$arrit['id'])
			  		{
  					dropitem_prem(7); // 2-е
			  		}
			  
			 }
			 else
			 {
			  dropitem_prem($arrit['type']);
			 }
			$u1=mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;") or die("2");
			$user = mysql_fetch_array($u1);
			}
		 return true;			
		 }
		 else
		 {
		 //все вещи держаться норм
		 return false;
		 }
 return false;
}


//подсчет статов+умелок
 function count_kr($current_exp,$exptable) {
    $cl = 0; $money = 0; $stats = 3; $vinos = 3; $master = 1;
    while($exptable) {
      if($current_exp >= $exptable[$cl][5]) {
        /* 0stat  1umen  2vinos 3kred, 4level, 5up*/
        $cl = $exptable[$cl][5];
        $money = $money+$exptable[$cl][3];
        $stats = $stats+$exptable[$cl][0];
        $master = $master+$exptable[$cl][1];
        $vinos = $vinos+$exptable[$cl][2];
      } else { $arr = array('money'=>$money,'stats'=>$stats,'master'=>$master,'vinos'=>$vinos,'cl'=>$exptable[$cl][5]); return $arr; }
    }
  }		   

//обнуление 
function dropall_prem()
{
	global $user, $mysql, $exptable;
 //1. удаляем шаблонные вещи
 mysql_query_100("delete from oldbk.inventory  where owner='{$user[id]}' and bs_owner=3 and type!=12");
//2. обновляем чарчика
$arr = count_kr($user[exp],$exptable);
$arr[hp]=$arr[vinos]*6;
mysql_query("UPDATE `users` SET 
				`users`.`sila`=3,`users`.`lovk`=3,`users`.`inta`=3,`users`.`vinos`='{$arr[vinos]}',`users`.`intel`=0,`users`.`mudra`=0,
				`users`.`duh`=0,`users`.`bojes`=0,`users`.`noj`=0,`users`.`mec`=0,`users`.`topor`=0,`users`.`dubina`=0,
				`users`.`maxhp`='{$arr[hp]}',`users`.`hp`='{$arr[hp]}',`users`.`maxmana`=0,`users`.`mana`=0,`users`.`sergi`=0,`users`.`kulon`=0,
				`users`.`perchi`=0,`users`.`weap`=0,`users`.`bron`=0,`users`.`r1`=0,`users`.`r2`=0,`users`.`r3`=0,`users`.`helm`=0,
				`users`.`shit`=0,`users`.`boots`=0,`users`.`stats`='{$arr[stats]}',`users`.`master`='{$arr[master]}',`users`.`nakidka`=0,`rubashka`=0,`runa1`=0 ,`runa2`=0 ,`runa3`=0 ,`users`.`mfire`=0,
				`users`.`mwater`=0,`users`.`mair`=0,`users`.`mearth`=0,`users`.`mlight`=0,`users`.`mgray`=0,`users`.`mdark`=0
				 WHERE `id` =  '{$user[id]}' ;") or die("3");
}

//снять предмет со слота
function dropitem_prem($slot) {
	global $user, $mysql;

if ($slot>0)
{
	switch($slot) {
		case 1: $slot1 = 'sergi'; break;
		case 2: $slot1 = 'kulon'; break;
		case 3: $slot1 = 'weap'; break;
		case 4: $slot1 = 'bron'; break;
		case 5: $slot1 = 'r1'; break;
		case 6: $slot1 = 'r2'; break;
		case 7: $slot1 = 'r3'; break;
		case 8: $slot1 = 'helm'; break;
		case 9: $slot1 = 'perchi'; break;
		case 10: $slot1 = 'shit'; break;
		case 11: $slot1 = 'boots'; break;
		case 12: $slot1 = 'm1'; break;
		case 13: $slot1 = 'm2'; break;
		case 14: $slot1 = 'm3'; break;
		case 15: $slot1 = 'm4'; break;
		case 16: $slot1 = 'm5'; break;
		case 17: $slot1 = 'm6'; break;
		case 18: $slot1 = 'm7'; break;
		case 19: $slot1 = 'm8'; break;
		case 20: $slot1 = 'm9'; break;
		case 21: $slot1 = 'm10'; break;
		case 22: $slot1 = 'm11'; break;
		case 23: $slot1 = 'm12'; break;
		case 24: $slot1 = 'm13'; break;
		case 25: $slot1 = 'm14'; break;
		case 26: $slot1 = 'm15'; break;
		case 27: $slot1 = 'nakidka'; break;
		case 28: $slot1 = 'rubashka'; break;
		case 31: $slot1 = 'runa1'; break;			
		case 32: $slot1 = 'runa2'; break;					
		case 33: $slot1 = 'runa3'; break;					
		case 34: $slot1 = 'm16'; break;
		case 35: $slot1 = 'm17'; break;
		case 36: $slot1 = 'm18'; break;
		case 37: $slot1 = 'm19'; break;
		case 38: $slot1 = 'm20'; break;
	}

 if ($user[$slot1]!=0)
           {
	     // запомним ИД снимаемого предмета для его удаления потом
	   $TO_DEL_ID=$user[$slot1]; 
	   //обновляем чара  и снимаем предмет
	   mysql_query_100("UPDATE `users` as u, oldbk.`inventory` as i SET u.{$slot1} = 0, i.dressed = 0,
			u.sila = u.sila - i.gsila,
			u.lovk = u.lovk - i.glovk,
			u.inta = u.inta - i.ginta,
			u.intel = u.intel - i.gintel,
			u.mudra = u.mudra - i.gmp,
			u.maxmana = (u.maxmana-i.gmp*10),
			u.maxhp = u.maxhp - i.ghp,
			u.noj = u.noj - i.gnoj,
			u.topor = u.topor - i.gtopor,
			u.dubina = u.dubina - i.gdubina,
			u.mec = u.mec - i.gmech,
			u.mfire = u.mfire - i.gfire,
			u.mwater = u.mwater - i.gwater,
			u.mair = u.mair - i.gair,
			u.mearth = u.mearth - i.gearth,
			u.mlight = u.mlight - i.glight,
			u.mgray = u.mgray - i.ggray,
			u.mdark = u.mdark - i.gdark
				WHERE i.id = u.{$slot1} AND i.dressed = 1 AND i.owner = {$user['id']} AND u.id = {$user['id']};");
		
	    //Фиксим ХП и маНУ
	    mysql_query("UPDATE `users` SET `hp` = `maxhp`, `fullhptime` = ".time()." WHERE  `hp` > `maxhp` AND `id` = '{$user['id']}' LIMIT 1;") or die("4");
            mysql_query("UPDATE `users` SET `mana` = `maxmana`, `fullmptime`=".time()." WHERE  `mana` > `maxmana` AND `id` = '{$user['id']}' LIMIT 1;") or die("5");
            //Удаляем предмет
	     mysql_query_100("delete from oldbk.inventory  where owner='{$user[id]}' and id='{$TO_DEL_ID}' ;");


		//Обновляем удержание
		
		return 	true;
		
		
	 }
	 else
	 {
	 //слот пустой просто нечего снимать
	 return true;
	 }
		
		
  }
else
	{
	//ощибка номера слота
		return 	false;
	}
}

// одеть предмет
function dressitem_prim ($id,$ot) 
{
	global $mysql, $user, $seven_level;
		//Уравнялка склонки
		$ch_al=$user['align']; if(($user['align']>=1.3) AND ($user['align']<1.4)) { $ch_al=6; }
	 	if ($user['klan']=='pal') { $ch_al=6; }
		
	 
	
	//1. Ищем шмотку в магазе - которая разрешина
	// и может быть одета
	$it=mysql_query("SELECT * FROM oldbk.shop WHERE  `wopen` > 0 AND `razdel` = '{$ot}' AND type!=12 AND
		((nalign = '".((int)$ch_al)."' ) or (nalign = 0)) AND `id` = '{$id}' ".$seven_level."  ; ") or die("6");
		
	$item = mysql_fetch_array($it) ;
        if ($item[id]>0)
	{
	//предмет подходит
	//
	//echo "Одеваем....";
					//fixelka -from shop
					$str=''; $sql=''; if($item[nlevel]>6) {	$str=",`up_level` "; $sql=",'".$item[nlevel]."' ";}
					mysql_query("INSERT INTO oldbk.`inventory`
					( `bs_owner` , `present`, `dressed` , `prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
						`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
						`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,
						`otdel`,`gmp`,`gmeshok`, `group`,`letter` ".$str."
					)
					VALUES
					( '3', 'Armory' , 1,'{$item['id']}','{$user[id]}','{$item['name']}','{$item['type']}',{$item['massa']},{$item['cost']},'{$item['img']}',{$item['maxdur']},{$item['isrep']},'{$item['gsila']}','{$item['glovk']}','{$item['ginta']}','{$item['gintel']}','{$item['ghp']}','{$item['gnoj']}','{$item['gtopor']}','{$item['gdubina']}','{$item['gmech']}','{$item['gfire']}','{$item['gwater']}','{$item['gair']}','{$item['gearth']}','{$item['glight']}','{$item['ggray']}','{$item['gdark']}','0','{$item['nsila']}','{$item['nlovk']}','{$item['ninta']}','{$item['nintel']}','{$item['nmudra']}','{$item['nvinos']}','{$item['nnoj']}','{$item['ntopor']}','{$item['ndubina']}','{$item['nmech']}','{$item['nfire']}','{$item['nwater']}','{$item['nair']}','{$item['nearth']}','{$item['nlight']}','{$item['ngray']}','{$item['ndark']}',
					'{$item['mfkrit']}','{$item['mfakrit']}','{$item['mfuvorot']}','{$item['mfauvorot']}','{$item['bron1']}','{$item['bron2']}','{$item['bron3']}','{$item['bron4']}','{$item['maxu']}','{$item['minu']}','{$item['magic']}','{$item['nlevel']}',
					'{$item['nalign']}','".(($item['goden'])?($item['goden']*24*60*60+time()):"")."','{$item['goden']}'
					,'{$item['razdel']}','{$item['gmp']}','{$item['gmeshok']}','{$item['group']}','{$item['letter']}' ".$sql."
					) ;") or die("7");
					
					$good = 1;
					//подменяем ид прототипа на свеже добавленный ид в сумке
					$id = mysql_insert_id();

						
					
		
	

		if ($good==1)
		{
			switch($item['type']) {
				case 1: $slot1 = 'sergi'; break;
				case 2: $slot1 = 'kulon'; break;
				case 3: $slot1 = 'weap'; break;
				case 4: $slot1 = 'bron'; break;
				case 5: $slot1 = 'r1'; break;
				case 6: $slot1 = 'r2'; break;
				case 7: $slot1 = 'r3'; break;
				case 8: $slot1 = 'helm'; break;
				case 9: $slot1 = 'perchi'; break;
				case 10: $slot1 = 'shit'; break;
				case 11: $slot1 = 'boots'; break;
				case 12: $slot1 = 'm1'; break;
				case 27: $slot1 = 'nakidka'; break;
				case 28: $slot1 = 'rubashka'; break;				
			}

			/*
			if($item['type']==30)
			{
				if(!$user['runa1']) { $slot1 = 'runa1';}
				elseif(!$user['runa2']) { $slot1 = 'runa2';}
				elseif(!$user['runa3']) { $slot1 = 'runa3';}
				else {
					$slot1 = 'runa1';
					dropitem_prem(31);
					$us=mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;") or die("9");
  					$user = mysql_fetch_array($us);
				}
			}
			else */
			if($item['type']==5)
			{
				if(!$user['r1']) { $slot1 = 'r1';}
				elseif(!$user['r2']) { $slot1 = 'r2';}
				elseif(!$user['r3']) { $slot1 = 'r3';}
				else {
				if ($user[id]==14897) 
					{
					echo "T1";
					}
					$slot1 = 'r1';
					dropitem_prem(5);
					$us=mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;") or die("9");
  					$user = mysql_fetch_array($us);
				}
			}
			else
			{
				dropitem_prem($item['type']);
			        $us=mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;") or die("9");
				$user = mysql_fetch_array($us);
			}

			if (!($item['type']==12 && $user['level'] < 4))
				{
				$sql="UPDATE `users` as u SET u.{$slot1} = {$id},
					u.sila = u.sila + '{$item['gsila']}',
					u.lovk = u.lovk + '{$item['glovk']}',
					u.inta = u.inta + '{$item['ginta']}',
					u.intel = u.intel + '{$item['gintel']}', 
					u.mudra = u.mudra + '{$item['gmp']}',  
					u.maxmana = (u.maxmana+'{$item['gmp']}'*10),
					u.maxhp = u.maxhp + '{$item['ghp']}',
					u.noj = u.noj + '{$item['gnoj']}',
					u.topor = u.topor + '{$item['gtopor']}',
					u.dubina = u.dubina + '{$item['gdubina']}',
					u.mec = u.mec + '{$item['gmech']}',
					u.mfire = u.mfire + '{$item['gfire']}',
					u.mwater = u.mwater + '{$item['gwater']}',
					u.mair = u.mair + '{$item['gair']}',
					u.mearth = u.mearth + '{$item['gearth']}',
					u.mlight = u.mlight + '{$item['glight']}',
					u.mgray = u.mgray + '{$item['ggray']}',
					u.mdark = u.mdark + '{$item['gdark']}'
						WHERE
					u.id = {$user['id']};";

					mysql_query($sql) or die("8787") ;
					
					//$user[$slot1] = $item['id'];
					return 	true;
				}
				else
				{
				return 	false;
				}
			}
			else
			{
			return false;
			}
			
		}
		else
		{
			echo '<font color=red>Вы не можете примерить эту вещь...</font>';
			return false;
		}
		
	
}

//print_r($_POST);


		 if (!(isset($_SESSION['aotdel']))) {$_SESSION['aotdel']=11;}
		 if (!(isset($_GET[otdel]))) {$_GET[otdel]=$_SESSION['aotdel'];}
		 $_SESSION['aotdel']=(int)$_GET[otdel];
		 
//////////////////////////////////////////////////////////////////////////////////////
		 if (($_GET[setdef]) AND ((int)$_GET[setdef] >0 ))
		 {
		 //
		 $d=(int)($_GET[setdef]);
		  mysql_query("UPDATE oldbk.`users_profile` set def=0 WHERE `owner`='{$user[id]}' AND `def`=1;") or die("11");
  		  mysql_query("UPDATE oldbk.`users_profile` set def=1 WHERE `owner`='{$user[id]}' AND `prof`='{$d}' ;") or die("12");
		 $arr_kom[$defaltis][def]=0;
 		 $arr_kom[$d][def]=1;
		 }
		else
		 if (($_GET[edit]) AND ($_GET[delcomplect]))
		 {
		 //удалим просто запись для этого комплекта и все
		 $dell=(int)($_GET[delcomplect]);
		 
		  if ($dell>0)
		  	{
			 mysql_query("DELETE FROM oldbk.`users_profile` WHERE `owner`='{$user[id]}' AND `prof`='{$dell}';") or die("13");
			unset($arr_kom[$dell]);
			if ($defaltis==$dell) {$defaltis=0; }
			}
		 
		 }
		 else
		 if (($_GET[edit]) AND (((int)($_GET[prof]))>0) )
		 {
		 
		 $profil=(int)($_GET[prof]);
		 //загрузка комплаета
		// запрашиваем комплект - есть ли такой комплект
		 
		//1. удаляем все что щас надето
		dropall_prem();
		//2. устанавливаем нужные вещи - по прототипу который в базе профилей и с флагом доступности
		//гружаем профиль
		//надо выбрать из магазина по прототипам нужные шмотки и инсертнуть в сумку
		//
		$arrneed=$arr_kom[$profil]; //распаковали нужный заранее загруженый проф
		$slots=array('sergi'=>0, 'kulon'=>0,'weap'=>0,'bron'=>0,'r1'=>0,'r2'=>0,'r3'=>0,'helm'=>0,'perchi'=>0,'shit'=>0,'boots'=>0,'nakidka'=>0,'rubashka'=>0);		
		$rco=0;$slqitem='';
		foreach($arrneed as $kn=>$kv)
		{
		if ( ($kn=='sergi' OR  $kn=='kulon' OR $kn=='weap' OR  $kn=='bron' OR
		$kn=='r1' OR $kn=='r2' OR $kn=='r3' OR	$kn=='helm' OR $kn=='perchi' OR
		$kn=='shit' OR  $kn=='boots' OR $kn=='nakidka' OR $kn=='rubashka' ) AND $kv >0)
		{
		$gsql="select * from oldbk.shop where id={$kv} and  `wopen` > 0 AND type!=12 ".$seven_level." ; ";
		$get_items=mysql_query($gsql) or die("14");
		$it_kol=mysql_num_rows($get_items);
			 if ($it_kol>0)
			 {
			 $arrdress=mysql_fetch_array($get_items);
				//инсертим-с флагом одето
				//fixelka -from shop
					$str=''; $sql=''; if($arrdress[nlevel]>6) {$str=",`up_level` "; $sql=",'".$arrdress[nlevel]."' ";}
					mysql_query("INSERT INTO oldbk.`inventory`
					( `bs_owner` , `present`, `dressed` , `prototype`,`owner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
						`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
						`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,
						`otdel`,`gmp`,`gmeshok`, `group`,`letter` ".$str."
					)
					VALUES
					('3','Armory' , 1,'{$arrdress['id']}','{$user[id]}','{$arrdress['name']}','{$arrdress['type']}',{$arrdress['massa']},{$arrdress['cost']},'{$arrdress['img']}',{$arrdress['maxdur']},{$arrdress['isrep']},'{$arrdress['gsila']}','{$arrdress['glovk']}','{$arrdress['ginta']}','{$arrdress['gintel']}','{$arrdress['ghp']}','{$arrdress['gnoj']}','{$arrdress['gtopor']}','{$arrdress['gdubina']}','{$arrdress['gmech']}','{$arrdress['gfire']}','{$arrdress['gwater']}','{$arrdress['gair']}','{$arrdress['gearth']}','{$arrdress['glight']}','{$arrdress['ggray']}','{$arrdress['gdark']}','0','{$arrdress['nsila']}','{$arrdress['nlovk']}','{$arrdress['ninta']}','{$arrdress['nintel']}','{$arrdress['nmudra']}','{$arrdress['nvinos']}','{$arrdress['nnoj']}','{$arrdress['ntopor']}','{$arrdress['ndubina']}','{$arrdress['nmech']}','{$arrdress['nfire']}','{$arrdress['nwater']}','{$arrdress['nair']}','{$arrdress['nearth']}','{$arrdress['nlight']}','{$arrdress['ngray']}','{$arrdress['ndark']}',
					'{$arrdress['mfkrit']}','{$arrdress['mfakrit']}','{$arrdress['mfuvorot']}','{$arrdress['mfauvorot']}','{$arrdress['bron1']}','{$arrdress['bron2']}','{$arrdress['bron3']}','{$arrdress['bron4']}','{$arrdress['maxu']}','{$arrdress['minu']}','{$arrdress['magic']}','{$arrdress['nlevel']}',
					'{$arrdress['nalign']}','".(($arrdress['goden'])?($arrdress['goden']*24*60*60+time()):"")."','{$arrdress['goden']}'
					,'{$arrdress['razdel']}','{$arrdress['gmp']}','{$arrdress['gmeshok']}','{$arrdress['group']}','{$arrdress['letter']}' ".$sql."
					) ;") or die("20") ;
					
					{
					//echo "INserted";
					$nid = mysql_insert_id();
					
					
/////////////////////////////////////////////////////////////////////////////////////////////									
				//потом определяем что за поле и прописываем чару
				switch($arrdress['type']) 
				{
				case 1: $slots['sergi']=$nid; break;
				case 2: $slots['kulon']=$nid; break;
				case 3: $slots['weap']=$nid; break;
				case 4: $slots['bron']=$nid; break;
				case 5: 	
					{
					
					$rco++;	$tmp='r'.$rco;
					$slots[$tmp]=$nid; break;
					}
				case 8: $slots['helm']=$nid; break;
				case 9: $slots['perchi']=$nid; break;
				case 10: $slots['shit']=$nid; break;
				case 11: $slots['boots']=$nid; break;
				case 27: $slots['nakidka']=$nid; break;
				case 28: $slots['rubashka']=$nid; break;				
					}
					
				}
					
				
			 } 
		  }
		}
			// print_r($slots);
			//собираем поля			
			foreach($slots as $kn=>$kv)
				{
				$slqitem.=" ".$kn."=".$kv." , ";	
				}		 
		/////
		///апдейтим чара
		
		$asql="UPDATE `users` SET 
				`users`.`sila`={$arrneed[sila]},`users`.`lovk`={$arrneed[lovk]},`users`.`inta`={$arrneed[inta]},`users`.`vinos`={$arrneed[vinos]}, `users`.`runa1`=0, `users`.`runa2`=0, `users`.`runa3`=0, 
				`users`.`intel`={$arrneed[intel]},`users`.`mudra`={$arrneed[mudra]},`users`.`duh`={$arrneed[duh]},`users`.`bojes`={$arrneed[bojes]},
				`users`.`noj`={$arrneed[noj]},`users`.`mec`={$arrneed[mec]},`users`.`topor`={$arrneed[topor]},`users`.`dubina`={$arrneed[dubina]},
				`users`.`maxhp`={$arrneed[maxhp]},`users`.`hp`={$arrneed[maxhp]},`users`.`maxmana`={$arrneed[maxmana]},`users`.`mana`={$arrneed[mana]},
				 ".$slqitem."
				`users`.`stats`='{$arrneed[stats]}',`users`.`master`='{$arrneed[master]}',`users`.`mfire`={$arrneed[mfire]},`users`.`mwater`={$arrneed[mwater]},
				`users`.`mair`={$arrneed[mair]},`users`.`mearth`={$arrneed[mearth]},`users`.`mlight`={$arrneed[mlight]},`users`.`mgray`={$arrneed[mgray]},`users`.`mdark`={$arrneed[mdark]}
				 WHERE  `users`.`id` = '{$user[id]}' ;";

		mysql_query($asql) or die("31");
		
				$us=mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;") or die("99");
  				$user = mysql_fetch_array($us);
			if (ref_drop_prem()==true) //проверяем на удержание предметы
				{
				//были предметы- и были сняты - надо перечитать
				$us=mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;") or die("99");
				$user = mysql_fetch_array($us);
				}
		
		
		 }
		else
                  if (($_POST[savecomplect])AND(strlen($_POST[savecomplect])>0))
                  {
                  //запомнить комплект
                  //1. 
                  if ($kom_kol>=10)
                    {
                    echo "<font color=red>У Вас уже есть 10 профилей...</font>";
                    }
                    else
                    {
                    //можем сохранить?
                     if (chck_items_prem()==true)
                       {
                    //1. запрашиваем одетые шмотки
                    $get_dressed=mysql_query("select * from oldbk.inventory where dressed=1 and owner='{$user[id]}' and type!=12") or die("80");
		    $dress_kol=mysql_num_rows($get_dressed);
			 if ($dress_kol>0)
			 {
			 $slqitem='';
			 $rco=0;
			 $slots=array('sergi'=>0, 'kulon'=>0,'weap'=>0,'bron'=>0,'r1'=>0,'r2'=>0,'r3'=>0,'helm'=>0,'perchi'=>0,'shit'=>0,'boots'=>0,'nakidka'=>0,'rubashka'=>0);
			 while ($arrdress=mysql_fetch_array($get_dressed))
				{
				switch($arrdress['type']) {
				case 1: $slots['sergi']=$arrdress['prototype']; break;
				case 2: $slots['kulon']=$arrdress['prototype']; break;
				case 3: $slots['weap']=$arrdress['prototype']; break;
				case 4: $slots['bron']=$arrdress['prototype']; break;
				case 5: 	
					{
					$rco++;	$tmp='r'.$rco;
					$slots[$tmp]=$arrdress['prototype']; break;
					}
				case 8: $slots['helm']=$arrdress['prototype']; break;
				case 9: $slots['perchi']=$arrdress['prototype']; break;
				case 10: $slots['shit']=$arrdress['prototype']; break;
				case 11: $slots['boots']=$arrdress['prototype']; break;
				case 27: $slots['nakidka']=$arrdress['prototype']; break;
				case 28: $slots['rubashka']=$arrdress['prototype']; break;				
					}
				
				}
				foreach($slots as $kn=>$kv)
				{
				$slqitem.=" ".$kn."=".$kv." , ";	
				}
				
			 }
			 else
			 {
			 $slqitem="";
			 }
                    //апаем чара запоминаем комплект в профиль
                   $next_id++;
                   if ($next_id==2) { $adf=' `def` = 1 , '; $dd=1; $defaltis=$next_id; } else { $adf=''; $dd=0;}
                  //echo $slqitem;
                    //поля
		$sk_row=" `sila`='{$user[sila]}',`lovk`='{$user[lovk]}',`inta`='{$user[inta]}',`vinos`='{$user[vinos]}',`intel`='{$user[intel]}',
		`mudra`='{$user[mudra]}',`duh`='{$user[duh]}',`bojes`='{$user[bojes]}',`noj`='{$user[noj]}',`mec`='{$user[mec]}',`topor`='{$user[topor]}',`dubina`='{$user[dubina]}',
		`maxhp`='{$user[maxhp]}',`hp`='{$user[hp]}',`maxmana`='{$user[maxmana]}',`mana`='{$user[mana]}', ".$slqitem."  `stats`='{$user[stats]}',`master`='{$user[master]}',
		`mfire`='{$user[mfire]}',`mwater`='{$user[mwater]}',`mair`='{$user[mair]}',`mearth`='{$user[mearth]}', ".$adf."
		`mlight`='{$user[mlight]}',`mgray`='{$user[mgray]}',`mdark`='{$user[mdark]}', `bpbonushp`='0'  ";
		$asql="INSERT INTO oldbk.`users_profile` SET `owner`='{$user[id]}',`pname`='{$_POST[savecomplect]}' ,`prof`='{$next_id}', ".$sk_row." ON DUPLICATE KEY UPDATE  ".$sk_row."  ; ";
		//echo $asql;
		mysql_query($asql) or die("81");
		if ($update==0) {$arr_kom[$next_id][pname]=$_POST[savecomplect]; $arr_kom[$next_id][prof]=$next_id; $arr_kom[$next_id][def]=$dd; }

		//print_r($arr_kom);
                       }
                       else
                       {
                       echo "Вы не можете сохранить этот комплект!";
                       }
                    
                    
                    }
                  
                  }
		  else
		  if ($_GET[undress])
		  {
		  // снять все+обнулить статы
		  dropall_prem();
		  //перечитка
	  	// mysql_query('COMMIT') or die();
		//mysql_query('START TRANSACTION') or die();		  
  		  $us=mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;") or die("9");
		  $user = mysql_fetch_array($us);
		  }
		  else
 		  if (($_GET[drop]) AND ($_GET[edit]))
		  {
	   	   $drop=(int)($_GET[drop]);
	   	   if ((($drop>0) AND ($drop<12)) OR ($drop==27) OR ($drop==28) )
	   	   	{
	   	   	dropitem_prem($drop);
		  	//делаем перечитку юзера
			$us=mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;") or die("9");
			$user = mysql_fetch_array($us);
						

	   	   	}
	   	   	else
	   	   	{
	   	   	echo "<font color=red>Вы не можете снять этот предмет</font>";
	   	   	}
		  
		  }
		  else
		  if (($_GET[up]) AND ($_GET[edit]))
		  {
		  if ($_GET[up]==1)
		  	{
	        	$stats=array('sila','lovk','inta','vinos','intel','mudra');
	        	$add=array();
	        	for($jjj=0;$jjj<count($stats);$jjj++)
	        	{
	        		$add[$stats[$jjj]]=((int)$_GET[$stats[$jjj]]>0?$_GET[$stats[$jjj]]:0);
	        	}
                //от любителей подделывать строки - функцию добавления стата вызываем по одной, даже если в гете пришло несколько статов...
                     if ($add[sila]>0) { setup_user_stats("sila",$add[sila]);	}
				elseif  ($add[lovk]>0)  { setup_user_stats("lovk",$add[lovk]);}
				elseif  ($add[inta]>0)  { setup_user_stats("inta",$add[inta]);}
				elseif  ($add[vinos]>0)  { setup_user_stats("vinos",$add[vinos]);}
				elseif  ($add[intel]>0)  { setup_user_stats("intel",$add[intel]);}
				elseif  ($add[mudra]>0)  { setup_user_stats("mudra",$add[mudra]);}
		  	}
		  	else
  			if ($_GET[up]>20)
  			{
//21-22-23-24-25-26-27-28-210-211
  			 switch ($_GET['up']) {
			case 21 :
				if ($user['master'] >0 && $user['noj'] < 5 && $user['sid']==$_GET['s4i'])	mysql_query("UPDATE `users` SET `noj` = `noj`+1, `master`=`master`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			break;
			case 22 :
				if ($user['master'] >0 && $user['mec'] < 5 && $user['sid']==$_GET['s4i'])	mysql_query("UPDATE `users` SET `mec` = `mec`+1, `master`=`master`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			break;
			case 23 :
				if ($user['master'] >0 && $user['dubina'] < 5 && $user['sid']==$_GET['s4i'])	mysql_query("UPDATE `users` SET `dubina` = `dubina`+1, `master`=`master`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			break;
			case 24 :
				if ($user['master'] >0 && $user['topor'] < 5 && $user['sid']==$_GET['s4i'])	mysql_query("UPDATE `users` SET `topor` = `topor`+1, `master`=`master`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			break;

			case 25 :
				if ($user['master'] >0 && $user['sid']==$_GET['s4i'])	mysql_query("UPDATE `users` SET `mfire` = `mfire`+1, `master`=`master`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			break;
			case 26 :
				if ($user['master'] >0 && $user['sid']==$_GET['s4i'])	mysql_query("UPDATE `users` SET `mwater` = `mwater`+1, `master`=`master`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			break;
			case 27 :
				if ($user['master'] >0 && $user['sid']==$_GET['s4i'])	mysql_query("UPDATE `users` SET `mair` = `mair`+1, `master`=`master`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			break;
			case 28 :
				if ($user['master'] >0 && $user['sid']==$_GET['s4i'])	mysql_query("UPDATE `users` SET `mearth` = `mearth`+1, `master`=`master`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			break;
			case 29 :
				if ($user['master'] >0 && $user['sid']==$_GET['s4i'])	mysql_query("UPDATE `users` SET `mlight` = `mlight`+1, `master`=`master`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			break;
			case 210 :
				if ($user['master'] >0 && $user['sid']==$_GET['s4i'])	mysql_query("UPDATE `users` SET `mgray` = `mgray`+1, `master`=`master`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			break;
			case 211 :
				if ($user['master'] >0 && $user['sid']==$_GET['s4i'])	mysql_query("UPDATE `users` SET `mdark` = `mdark`+1, `master`=`master`-1 WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;");
			break;
	    		}
  			}
		//делаем перечитку юзера
		$us=mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;") or die("9");
		  $user = mysql_fetch_array($us);
		  }		
		else
		  if (($_GET[otdel]) AND ($_GET[prim]))
		  {
		  //меряем шмотку
		  $ot=(int)($_GET[otdel]);
		  $prim=(int)($_GET[prim]);
		  if (dressitem_prim ($prim,$ot)==true)
		  	{
		  	//echo 'Удачно';
		  	//делаем перечитку юзера
		  	$us=mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;") or die("9");
		        $user = mysql_fetch_array($us);
		  	
		  	}
		  	else
		  	{
		  	//echo 'Не Удачно';		  	
		  	}
		  
		  }
		  else
		   if (($_GET['exit']) and  ($user[battle]==0) )
		   {
		   
		   $napr[197]=210;
		   //$napr[198]=240;		   
   		   $napr[199]=270;
		   $goto=$napr[$user[room]];
		   if ($goto>0)
		     {
		     ///загружаем параметры prof=0 для выхода
		     $user_real=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users_profile` WHERE  prof=0 and  `owner` = '{$user[id]}' LIMIT 1;"));
		     if ($user_real[bpbonushp] >0)
		     {
		     //если был боныс хп - проверяем незакончился ли он
		     $hp_bonus=mysql_fetch_array(mysql_query("select * from effects where owner='{$user[id]}' and (type=1001 or  type=1002 or type=1003)"));
		     if ($hp_bonus[id]>0)
		       {
		       //все ок эфект еще висит
		       
		       }
		       else
		       {
		       //эфекта такого уже нет! 
		       //снимаем его ручками, т.к. в кроене он не снялся
		       $user_real[maxhp]=$user_real[maxhp]-$user_real[bpbonushp];
       		       $user_real[bpbonushp]=0;
			       if ($user_real[hp]>$user_real[maxhp]) 
			       		{
			       		$user_real[hp]=$user_real[maxhp];
			       		}
		       }
		     }
		     //идем дальше
		     //обновляем инвентарь
		     //1. удаляем шаблонные вещи
		     mysql_query_100("delete from oldbk.inventory  where owner='{$user[id]}' and bs_owner=3 and type!=12");
		     //2.устанавливаем родные шмотки
		     mysql_query_100("update oldbk.inventory  set dressed=1 where id in ({$user_real[sergi]},{$user_real[kulon]},{$user_real[perchi]},{$user_real[weap]},{$user_real[bron]},{$user_real[r1]},{$user_real[r2]},{$user_real[r3]},{$user_real[helm]},{$user_real[shit]},{$user_real[boots]},{$user_real[nakidka]},{$user_real[rubashka]},{$user_real[runa1]},{$user_real[runa2]},{$user_real[runa3]}) AND owner='{$user[id]}' and dressed=0 ");
		     //3. обновляем чарчика
		     $sk_row=" `sila`='{$user_real[sila]}',`lovk`='{$user_real[lovk]}',`inta`='{$user_real[inta]}',`vinos`='{$user_real[vinos]}',`intel`='{$user_real[intel]}',
		`mudra`='{$user_real[mudra]}',`duh`='{$user_real[duh]}',`bojes`='{$user_real[bojes]}',`noj`='{$user_real[noj]}',`mec`='{$user_real[mec]}',`topor`='{$user_real[topor]}',`dubina`='{$user_real[dubina]}',
		`maxhp`='{$user_real[maxhp]}',`hp`='{$user_real[hp]}',`maxmana`='{$user_real[maxmana]}',`mana`='{$user_real[mana]}',`sergi`='{$user_real[sergi]}',`kulon`='{$user_real[kulon]}',`perchi`='{$user_real[perchi]}',
		`weap`='{$user_real[weap]}',`bron`='{$user_real[bron]}',`r1`='{$user_real[r1]}',`r2`='{$user_real[r2]}',`r3`='{$user_real[r3]}', `runa1`='{$user_real[runa1]}',`runa2`='{$user_real[runa2]}',`runa3`='{$user_real[runa3]}',  `helm`='{$user_real[helm]}',`shit`='{$user_real[shit]}',`boots`='{$user_real[boots]}',
		`stats`='{$user_real[stats]}',`master`='{$user_real[master]}',`nakidka`='{$user_real[nakidka]}',`rubashka`='{$user_real[rubashka]}',`mfire`='{$user_real[mfire]}',`mwater`='{$user_real[mwater]}',`mair`='{$user_real[mair]}',`mearth`='{$user_real[mearth]}',
		`mlight`='{$user_real[mlight]}',`mgray`='{$user_real[mgray]}',`mdark`='{$user_real[mdark]}', `bpbonushp`='{$user_real[bpbonushp]}'  ";
		      mysql_query_100("UPDATE `users` SET ".$sk_row." , `users`.`room` = '{$goto}'  WHERE `users`.`id`  = '{$user[id]}' ;");
		      //mysql_query('COMMIT') or die();
		      header('location: restal'.$goto.'.php');
		      die();
		     }
		     else
		     {
		     echo "Ошибка направления...";
		     }
		   }
		   
    		   

/////////////////////////
	 //mysql_query('COMMIT') or die();
	 //mysql_query('COMMIT') or die();		 		 
	 //echo "RUN";
	 //мегафикс
	 //проверка после всех махинаций есть ли шмот который надет
	 
	 $user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$user[id]}' ;"));	 
	 $nadeto=0;
	 $idn=array();
	 if ($user['sergi']>0) { $nadeto++; $idn[]=$user['sergi']; }
	 if ($user['kulon']>0) { $nadeto++; $idn[]=$user['kulon']; }
	 if ($user['weap']>0) { $nadeto++; $idn[]=$user['weap']; }
	 if ($user['bron']>0) { $nadeto++; $idn[]=$user['bron']; }
	 if ($user['r1']>0) { $nadeto++; $idn[]=$user['r1']; }
	 if ($user['r2']>0) { $nadeto++; $idn[]=$user['r2']; }
	 if ($user['r3']>0) { $nadeto++; $idn[]=$user['r3']; }
	 if ($user['helm']>0) { $nadeto++; $idn[]=$user['helm']; }
	 if ($user['perchi']>0) { $nadeto++; $idn[]=$user['perchi']; }
	 if ($user['shit']>0) { $nadeto++; $idn[]=$user['shit']; }
	 if ($user['boots']>0) { $nadeto++; $idn[]=$user['boots']; }
	 if ($user['nakidka']>0) { $nadeto++; $idn[]=$user['nakidka']; }
	 if ($user['rubashka']>0) { $nadeto++; $idn[]=$user['rubashka']; }	 
	 
	 if ($nadeto>0)
	 	{
	 	//mysql_query('START TRANSACTION') or die();
		$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' ;"));
	 	$nsql="select count(*) from oldbk.inventory where id in (".implode(",", $idn).") and owner='{$_SESSION['uid']}' and type!=12 and dressed=1 ;";
		$heved=mysql_fetch_array(mysql_query($nsql));
		//echo $heved[0];
		//echo "/";
		//echo "$nadeto";		
		 if ($nadeto!=$heved[0])
		 	{
		 	//баг!!!!!!!!!
		 	dropall_prem();
			//mysql_query('COMMIT') or die();		 	
		 	addchp('<font color=red>Внимание armory fix for:'.$user[login].' </font>'.$nsql,'{[]}Bred{[]}');
			header("Location: armory.php");
			die("");
		 	}
		 	else
		 	{
		 	//все гуд
		 	}
		 	
		}
	 
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<META HTTP-EQUIV="imagetoolbar" CONTENT="no">
<script type="text/javascript" src="/i/globaljs.js"></script>
<style>
    IMG.aFilter { filter:Glow(Color=d7d7d7,Strength=9,Enabled=0); cursor:hand }

    body {
			/* background-image: url('http://capitalcity.oldbk.com/i/restal/r210_1.jpg');*/
			background-repeat: no-repeat;
			background-position: top right;
	   }
</style>
<SCRIPT LANGUAGE="JavaScript">

		function returned2(s){
			location.href='armory.php?'+s+'tmp='+Math.random();
		}

function Down() {<?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>CtrlPress = window.event.ctrlKey}

	document.onmousedown = Down;


function save(f){
   f.submit();
   return true;
}

function save1(id){

	var gg;
	var f = document.f1;
	id='rzd'+id;
	gg=document.getElementById(id).value;
    if(gg==0)
    {document.getElementById(id).value=1;}
    else
    {document.getElementById(id).value=0;}
    f.submit();
    return true;
}

function showhidden(id)
{
var st=document.getElementById('id_'+id).style.display;
if (st == 'none')
	{
		document.getElementById('id_'+id).style.display = 'block';
		document.getElementById('txt_'+id).style.display = 'none';
		document.getElementById('txt1_'+id).style.display = 'block';
	}
	else
	{
		document.getElementById('id_'+id).style.display = 'none';
		document.getElementById('txt_'+id).style.display = 'block';
		document.getElementById('txt1_'+id).style.display = 'none';

	}
}

function AddCount(name, txt, drop, href) {
    var el = document.getElementById("hint3");

	el.innerHTML = '<form method=post style="margin:0px; padding:0px;"><table border=0 width=100% cellspacing=1 cellpadding=0 bgcolor="#CCC3AA"><tr><td align=center><B>Выкинуть неск. штук</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</TD></tr><tr><td colspan=2>'+
	'<table border=0 width=100% cellspacing=0 cellpadding=0 bgcolor="#FFF6DD"><tr>'+
	'<INPUT TYPE="hidden" name="gift" value="'+drop+'"><INPUT TYPE="hidden" name="dur" value="'+href+'"><INPUT TYPE="hidden" name="destruct" value="1"><INPUT TYPE="hidden" name="set" value="'+name+'"><td colspan=2 align=center><B><I>'+txt+'</td></tr><tr><td width=80% align=right>'+
	'Количество (шт.) <INPUT TYPE="text" NAME="count" size=4 ></td><td width=20%>&nbsp;<INPUT TYPE="submit" value=" »» ">'+
	'</TD></TR></TABLE></td></tr></table></form>';
	el.style.visibility = "visible";
	el.style.left = (document.body.scrollLeft - 20) + 100 + 'px';
	el.style.top = (document.body.scrollTop + 5) + 100 + 'px';
	document.getElementById("count").focus();

}
var Hint3Name = '';
// Заголовок, название скрипта, имя поля с логином
function findlogin(title, script, name){
    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><td colspan=2><INPUT TYPE=hidden name=sd4 value="6">'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT TYPE=text id="'+name+'" NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100;
	el.style.top = 100;
	document.getElementById(name).focus();
	Hint3Name = name;
}

function createrequestobject()
{
	var request;
	if (window.XMLHttpRequest)
	{
        try
		{
            request = new XMLHttpRequest();
        }
		catch (e){}
    }
	else if (window.ActiveXObject)
	{
        try
		{
            request = new ActiveXObject('Msxml2.XMLHTTP');
        }
		catch (e)
		{
            try
			{
                request = new ActiveXObject('Microsoft.XMLHTTP');
            }
			catch (e){}
        }
    }

	return request;
}

function getchoice(type)
{
	var container = document.getElementById("itemcontainer");
	var request = createrequestobject();
	if (request)
	{
        request.open("POST", "itemschoice.php?get=1&" + type + "=1", true);
        request.onreadystatechange = function()
		{
			if (request.readyState == 4)
			{
				if (request.status == 200)
				{
					container.innerHTML = request.responseText;
				}
				else
				{
					container.innerHTML = "<font color='red'><B>Произошла ошибка</font>";
				}
			}
		};
        request.send(null);
    }
	else
	{
		container.innerHTML = "<font color='red'><B>Произошла ошибка</font>";
	}
}

function showitemschoice(title, type, script)
{
	var choicehtml = "<form style='display:none' id='formtarget' action='" + script + "' method=POST><input type='hidden' id='target' name='target'>";
	choicehtml += "</form><table width='100%' cellspacing='1' cellpadding='0' bgcolor='CCC3AA'>";
	choicehtml += "<tr><td align='center'><B>" + title + "</td>";
	choicehtml += "<td width='20' align='right' valign='top' style='cursor: pointer' onclick='closehint3(true);'>";
	choicehtml += "<big><b>x</td></tr><tr><td colspan='2' id='tditemcontainer'><div id='itemcontainer' style='width:100%'>";
	choicehtml += "</div></td></tr></table>";

	var el = document.getElementById("hint3");
	el.innerHTML = choicehtml;
	el.style.width = 400 + 'px';
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	Hint3Name = "target";

	getchoice(type);
}

function selecttarget(scrollid)
{
	var targertinput = document.getElementById('target');
	targertinput.value = scrollid;

	var targetform = document.getElementById('formtarget');
	targetform.submit();
}

// Заголовок, название скрипта, имя поля с шмоткой
function okno(title, script, name,coma,errk){
	var errkom=''; var com='';
	var el = document.getElementById("hint3");
	if (errk==1) { errkom='Нельзя использовать символы: /:*?"<>|+%&#\'\\<br>'; com=coma}
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=sd4 value="6"><td colspan=2><font color=red>'+
	errkom+'</font>введите название предмета</TD></TR><TR><TD width=50% align=right><INPUT TYPE=text id="'+name+'" NAME="'+name+'" value="'+com+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 100;
	el.style.top = 100;
	document.getElementById(name).focus();
	Hint3Name = name;
}
// Заголовок, название скрипта, имя поля с пассом
function oknoPass(title, script, name,coma){
    var el = document.getElementById("hint3");
	el.innerHTML = '<form action="'+script+'" method=POST><table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><INPUT TYPE=hidden name=sd4 value="6"><td colspan=2>'+
	'Введите пароль для рюкзака</TD></TR><TR><TD width=50% align=right><INPUT TYPE=password NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></td></tr></table></form>';
	el.style.visibility = "visible";
	el.style.left = 100 + 'px';
	el.style.top = 100 + 'px';
	document.getElementById(name).focus();
	Hint3Name = name;
}
function closehint3(clearstored){
	if(clearstored)
	{
		var targetform = document.getElementById('formtarget');
		targetform.action += "&clearstored=1";
		targetform.submit();
	}
	document.getElementById("hint3").style.visibility="hidden";
    Hint3Name='';
}

function defPosition(event) {
      var x = y = 0;
      if (document.attachEvent != null) { // Internet Explorer & Opera
            x = window.event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
            y = window.event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
			if (window.event.clientY + 72 > document.body.clientHeight) { y-=38 } else { y-=2 }
      } else if (!document.attachEvent && document.addEventListener) { // Gecko
            x = event.clientX + window.scrollX;
            y = event.clientY + window.scrollY;
			if (event.clientY + 72 > document.body.clientHeight) { y-=38 } else { y-=2 }
      } else {
            // Do nothing
      }
      return {x:x, y:y};
}

function OpisShmot(evt,s){
	menu=document.createElement("div");
	menu.style.border='1px solid black';
	menu.innerHTML = s;
	menu.id='ShowInfoShmot';
	menu.style.background='#FFFFE1';
	menu.style.fontsize='8px';
	menu.style.position='absolute';
    menu.style.top = defPosition(evt).y + "px";
    menu.style.left = defPosition(evt).x + "px";

	showSH=setTimeout(function(){
					document.body.appendChild(menu);
			   }, 1000);
}

function HideOpisShmot(){
	try{
		ids=document.getElementById('ShowInfoShmot');
		ids.parentNode.removeChild(ids);
	}
	catch (err){
		clearTimeout(showSH);
	}
}

</SCRIPT>
</HEAD>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 onLoad="top.setHP(<?=$user['hp']?>,<?=$user['maxhp']?>)">
<div id=hint3 class=ahint></div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign=top>
  
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
	        <tr>
		<td valign=top align=left width=250><?showpersinv($user)?><center>
		
		<a href='?undress=all'>Сбросить все</a><BR>
		<a  onclick = "okno('Сохранить профиль','?edit=1','savecomplect','');" href='#'>Запомнить профиль</a>
		</center>
		<BR>
		<?
//		 if ($kom_kol>0)
		 {
		echo ' <table>
		<tr><td>&nbsp;</td>
		<td><small>';
		
		
		
		 //есть комплекты далеем линки
		foreach($arr_kom as $ke=>$va)
		{
		echo "<a onclick=\"if (!confirm('Вы уверены, что хотите удалить профиль?')) { return false; }\" href='?edit=1&delcomplect=$ke'><img src='i/clear.gif'></a>";			
		if ($va[def] > 0) { $defal="<img src='http://i.oldbk.com/i/def.png' title='По умолчанию'>"; echo "&nbsp;&nbsp;&nbsp;&nbsp;"; } else
			 {
			  $defal=""; 
			  echo "<a href=?setdef=$ke><img src='http://i.oldbk.com/i/setdef.png' title='Установить по умолчанию'></a>"; 
			  }
		echo "<a href=?edit=1&prof=$ke>Надеть: ".$va[pname]."</a>".$defal."<BR>";
		}
		echo '<tr><td>&nbsp;</td><td>
		</td></tr>
		</table>';
	
		 }

		?>
		</td>

<TD valign=top >
		<br/>
		Опыт: <?=$user['exp']?> (<?=$user['nextup']?>)<BR>
		Уровень: <?=$user['level']?><BR>
		Побед: <?=$user['win']?><BR>
		Поражений: <?=$user['lose']?><BR>
		Деньги: <b><?=$user['money']?></b> кр. <BR>
		<?php
		if($user['klan']) {
			echo "Клан: {$user['klan']}<BR>";
		}?>
	<HR>
	<!--Параметры-->
	<table border=0><tr><td>

	Сила: <?=$user['sila']?><?=($user['stats'])?"<a onclick='var obj; if (obj = prompt(\"Введите количество СТАТОВ передаваемых в силу\",\"1\")) { window.location=\"?up=1&edit=1&sila=\"+obj+\"\"; }' href='#'><img src='http://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":""?><BR>
	Ловкость: <?=$user['lovk']?><?=($user['stats'])?"<a onclick='var obj; if (obj = prompt(\"Введите количество СТАТОВ передаваемых в ловкость\",\"1\")) { window.location=\"?up=1&edit=1&lovk=\"+obj+\"\"; }' href='#'><img src='http://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":""?><BR>
	Интуиция: <?=$user['inta']?><?=($user['stats'])?"<a onclick='var obj; if (obj = prompt(\"Введите количество СТАТОВ передаваемых в интуицию\",\"1\")) { window.location=\"?up=1&edit=1&inta=\"+obj+\"\"; }' href='#'><img src='http://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":""?><BR>
	Выносливость: <?=$user['vinos']?><?=($user['stats'])?"<a onclick='var obj; if (obj = prompt(\"Введите количество СТАТОВ передаваемых в выносливость\",\"1\")) { window.location=\"?up=1&edit=1&vinos=\"+obj+\"\"; }' href='#'><img src='http://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":""?><BR>
	<?echo($user['level']>3)?"Интеллект: {$user['intel']}":""; echo ($user['stats'] && ($user['level']>3))?"<a onclick='var obj; if (obj = prompt(\"Введите количество СТАТОВ передаваемых в интелект\",\"1\")) { window.location=\"?up=1&edit=1&intel=\"+obj+\"\"; }' href='#'><img src='http://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":"";  if($user['level']>3) echo"<BR>"?>
	<?echo($user['level']>6)?"Мудрость: {$user['mudra']}":""; echo ($user['stats'] && ($user['level']>6))?"<a onclick='var obj; if (obj = prompt(\"Введите количество СТАТОВ передаваемых в мудрость\",\"1\")) { window.location=\"?up=1&edit=1&mudra=\"+obj+\"\"; }' href='#'><img src='http://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":"";  if($user['level']>6) echo"<BR>"?>
	<FONT COLOR="green">Возможных увеличений: <?=$user['stats']?></FONT>
	<!-- Added: 27.04.2010 Auth: Weathered -->
	<hr/>
	<?
		function get_wep_type($idwep)
		{
			if ($idwep == 0 || $idwep == null || $idwep == '') { return "kulak"; }
			$wep = mysql_fetch_array(mysql_query('SELECT `otdel`,`minu` FROM oldbk.`inventory` WHERE `id` = '.$idwep.' LIMIT 1;'));
			if($wep[0] == '1') { return "noj"; }
			elseif($wep[0] == '12') { return "dubina"; }
			elseif($wep[0] == '11') { return "topor"; }
			elseif($wep[0] == '13') {return "mech";	}
			elseif($wep[1] > 0) { return "buket"; } else { return "kulak"; }
		}
		$user_dressed = mysql_fetch_array(mysql_query('SELECT sum(minu),sum(maxu),sum(mfkrit),sum(mfakrit),sum(mfuvorot),sum(mfauvorot),sum(bron1),sum(bron2),sum(bron3),sum(bron4) FROM oldbk.`inventory` WHERE `dressed`=1 AND `owner` = \''.$user['id'].'\' LIMIT 1;'));

		$user_level = $user['level'];

		$master = 0;
		switch(get_wep_type($user['weap']))
		{
			case "noj": $master += $user['noj']; break;
			case "dubina": $master += $user['dubina']; break;
			case "topor": $master += $user['topor']; break;
			case "mech": $master += $user['mec']; break;
		}

		$min_damage = round((floor($user['sila']/3) + 1) + $user_level + $user_dressed[0] * (1 + 0.07 * $master));
		$max_damage =  round((floor($user['sila']/3) + 4) + $user_level + $user_dressed[1] * (1 + 0.07 * $master));

		if($weapon_type == 'kulak' && $user['align'] == '2')
		{
			$min_damage += $user_level;
			$max_damage += $user_level;
		};
?>
		Урон: <? echo $min_damage; ?> - <? echo $max_damage; ?> <br/>
		Модификаторы<br/>
		 &nbsp; уворот: &nbsp;<? echo $user_dressed[4] + $user['lovk'] * 5; ?>%<br/>
		 &nbsp; антиуворот: &nbsp;<? echo $user_dressed[5] + $user['lovk'] * 5 + $user['inta'] * 2; ?>%<br/>
		 &nbsp; крит: &nbsp;<? echo $user_dressed[2] + $user['inta'] * 5; ?>%<br/>
		 &nbsp; антикрит: &nbsp;<? echo $user_dressed[3] + $user['inta'] * 5 + $user['lovk'] * 2; ?>%<br/>
		Броня<br/>
		 &nbsp; головы: &nbsp;<? echo $user_dressed[6]; ?><br/>
		 &nbsp; корпуса: &nbsp;<? echo $user_dressed[7]; ?><br/>
		 &nbsp; пояса: &nbsp;<? echo $user_dressed[8]; ?><br/>
		 &nbsp; ног: &nbsp;<? echo $user_dressed[9]; ?><br/>
		<!-- </> -->
		<HR>
		Мастерство владения:<BR>
		 &nbsp; ножами и кастетами: <?=$user['noj']?><?=($user['master'])?"<a href='?up=21&s4i=".($user['sid'])."&edit=1'><img src=i/up.gif></a>":""?><BR>
		 &nbsp; мечами: <?=$user['mec']?><?=($user['master'])?"<a href='?up=22&s4i=".($user['sid'])."&edit=1'><img src=i/up.gif></a>":""?><BR>
		 &nbsp; дубинами, булавами: <?=$user['dubina']?><?=($user['master'])?"<a href='?up=23&s4i=".($user['sid'])."&edit=1'><img src=i/up.gif></a>":""?><BR>
		 &nbsp; топорами и секирами: <?=$user['topor']?><?=($user['master'])?"<a href='?up=24&s4i=".($user['sid'])."&edit=1'><img src=i/up.gif></a>":""?><BR>
		<?if ($user['level'] > 3) {?>
		Магическое мастерство:<BR>
		 &nbsp; Стихия огня: <?=$user['mfire']?><?=($user['master'])?"<a href='?up=25&s4i=".($user['sid'])."&edit=1'><img src=i/up.gif></a>":""?><BR>
		 &nbsp; Стихия воды: <?=$user['mwater']?><?=($user['master'])?"<a href='?up=26&s4i=".($user['sid'])."&edit=1'><img src=i/up.gif></a>":""?><BR>
		 &nbsp; Стихия воздуха: <?=$user['mair']?><?=($user['master'])?"<a href='?up=27&s4i=".($user['sid'])."&edit=1'><img src=i/up.gif></a>":""?><BR>
		 &nbsp; Стихия земли: <?=$user['mearth']?><?=($user['master'])?"<a href='?up=28&s4i=".($user['sid'])."&edit=1'><img src=i/up.gif></a>":""?><BR>
		 &nbsp; Магия Света: <?=$user['mlight']?><?=($user['master'])?"<a href='?up=29&s4i=".($user['sid'])."&edit=1'><img src=i/up.gif></a>":""?><BR>
		 &nbsp; Серая магия: <?=$user['mgray']?><?=($user['master'])?"<a href='?up=210&s4i=".($user['sid'])."&edit=1'><img src=i/up.gif></a>":""?><BR>
		 &nbsp; Магия Тьмы: <?=$user['mdark']?><?=($user['master'])?"<a href='?up=211&s4i=".($user['sid'])."&edit=1'><img src=i/up.gif></a>":""?><BR> <?
		 }?>
		<FONT COLOR="#333399">Возможных увеличений: <?=$user['master']?></font>
		</td></tr></table>

	</TD>


		  </tr>
		  </table>
	
	</td>
    <td valign=top align=right>
    <h3>Оружейная Комната</h3>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td align=left>
    <? /* <font color=red><b>Внимание!</b> Рассчитывайте профиль так, чтобы удержать Ваши свитки!<br> */ ?>
    <?
    if ($defaltis==0) { echo "<font color=red><b>Внимание!</b> У Вас не установлен профиль <b>по умолчанию</b>  - Вы не сможете пройти на турнир!<br>";}
    chck_scrolls_prem();
    chck_items_prem ();
    echo "</td><td align=right>";
    echo "<form method=GET > <input type=button value='Обновить' onClick=\"returned2('refresh=3.14&');\">
		<INPUT TYPE=button value=\"Выйти\" onClick=\"returned2('exit=true&');\"><br>
		</form>
		</td></tr></table>";
    ?>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign=top>
    <TABLE BORDER=0 WIDTH=100% CELLSPACING="1" CELLPADDING="2" BGCOLOR="#A5A5A5">
    <?
	$data = mysql_query("SELECT * FROM oldbk.`shop` WHERE `wopen` > 0 AND `razdel` = '{$_GET['otdel']}' ".$seven_level." ORDER by `cost` ASC");
	while($row = mysql_fetch_array($data))
	 {
	$row[GetShopCount()]="1";
	$row[needident]="0";

		if ($i==0) { $i = 1; $color = '#C7C7C7';} else { $i = 0; $color = '#D5D5D5';}

		echo "<TR bgcolor={$color}><TD align=center style='width:150px'><IMG SRC=\"http://i.oldbk.com/i/sh/{$row['img']}\" BORDER=0>";
		?>
		<BR><A HREF="?otdel=<?=$_GET['otdel']?>&prim=<?=$row['id']?>">примерить</A>
		</TD>
		<?php
		echo "<TD valign=top>";
		showitem ($row);
		echo "</TD></TR>";
	 }

	?>
	</TABLE>
    </td>
  <td valign=top>
<div style="MARGIN-LEFT:15px; MARGIN-TOP: 10px;">
<div style="background-color:#d2d0d0;padding:1"><center><font color="#oooo"><B>Ассортимент</B></center></div>
<A HREF="armory.php?otdel=11&sid=&0.337606814894404">Оружие: топоры</A><BR>
<A HREF="armory.php?otdel=12&sid=&0.286790872806733">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;дубины,булавы</A><BR>
<A HREF="armory.php?otdel=13&sid=&0.0943516060419363">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;мечи</A><BR>
<A HREF="armory.php?otdel=2&sid=&0.76205958316951">Одежда: сапоги</A><BR>
<A HREF="armory.php?otdel=21&sid=&0.648260824682342">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;перчатки</A><BR>
<A HREF="armory.php?otdel=22&sid=&0.520447517792988">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;легкая броня</A><BR>
<A HREF="armory.php?otdel=23&sid=&0.99133839275569">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;тяжелая броня</A><BR>
<A HREF="armory.php?otdel=24&sid=&0.567932791291376">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;шлемы</A><BR>
<A HREF="armory.php?otdel=3&sid=&0.725667864710179">Щиты</A><BR>
<A HREF="armory.php?otdel=4&sid=&0.321709306035984">Ювелирные товары: серьги</A><BR>
<A HREF="armory.php?otdel=41&sid=&0.902093651333512">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ожерелья</A><BR>
<A HREF="armory.php?otdel=42&sid=&0.510210803380268">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;кольца</A><BR>
</div>
    </td>
  </tr>
</table>  
   
    
    </td>
  </tr>
</table>

	
</body>
</html>