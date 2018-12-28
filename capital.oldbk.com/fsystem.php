<?
// Ѕоевые функции !
// v.9 12/14/2017 + наемники
require_once('memcache.php');

function log_stats_adm($text)
{
/*
	$fp = fopen ("/www/other/ristal.txt","a"); //открытие
	flock ($fp,LOCK_EX); //ЅЋќ »–ќ¬ ј ‘ј…Ћј
	fputs($fp , $text."\n"); //работа с файлом
	fflush ($fp); //ќ„»ў≈Ќ»≈ ‘ј…Ћќ¬ќ√ќ Ѕ”‘≈–ј » «јѕ»—№ ¬ ‘ј…Ћ
	flock ($fp,LOCK_UN); //—Ќя“»≈ ЅЋќ »–ќ¬ »
	fclose ($fp); //закрытие
*/
}

function log_sql_tmp($text)
{

	$fp = fopen ("/www/other/tmp.txt","a"); //открытие
	flock ($fp,LOCK_EX); //ЅЋќ »–ќ¬ ј ‘ј…Ћј
	fputs($fp , $text."\n"); //работа с файлом
	fflush ($fp); //ќ„»ў≈Ќ»≈ ‘ј…Ћќ¬ќ√ќ Ѕ”‘≈–ј » «јѕ»—№ ¬ ‘ј…Ћ
	flock ($fp,LOCK_UN); //—Ќя“»≈ ЅЋќ »–ќ¬ »
	fclose ($fp); //закрытие
}

function log_sql_deb($text)
{

	$fp = fopen ("/www/other/sql.txt","a"); //открытие
	flock ($fp,LOCK_EX); //ЅЋќ »–ќ¬ ј ‘ј…Ћј
	fputs($fp , $text."\n"); //работа с файлом
	fflush ($fp); //ќ„»ў≈Ќ»≈ ‘ј…Ћќ¬ќ√ќ Ѕ”‘≈–ј » «јѕ»—№ ¬ ‘ј…Ћ
	flock ($fp,LOCK_UN); //—Ќя“»≈ ЅЋќ »–ќ¬ »
	fclose ($fp); //закрытие
}

function log_kv_deb($text)
{

	$fp = fopen ("/www/other/sql_kv.txt","a"); //открытие
	flock ($fp,LOCK_EX); //ЅЋќ »–ќ¬ ј ‘ј…Ћј
	fputs($fp , $text."\n"); //работа с файлом
	fflush ($fp); //ќ„»ў≈Ќ»≈ ‘ј…Ћќ¬ќ√ќ Ѕ”‘≈–ј » «јѕ»—№ ¬ ‘ј…Ћ
	flock ($fp,LOCK_UN); //—Ќя“»≈ ЅЋќ »–ќ¬ »
	fclose ($fp); //закрытие
}

function log_dp_deb($text)
{

	$fp = fopen ("/www/other/sql_dp.txt","a"); //открытие
	flock ($fp,LOCK_EX); //ЅЋќ »–ќ¬ ј ‘ј…Ћј
	fputs($fp , $text."\n"); //работа с файлом
	fflush ($fp); //ќ„»ў≈Ќ»≈ ‘ј…Ћќ¬ќ√ќ Ѕ”‘≈–ј » «јѕ»—№ ¬ ‘ј…Ћ
	flock ($fp,LOCK_UN); //—Ќя“»≈ ЅЋќ »–ќ¬ »
	fclose ($fp); //закрытие
}

function write_stat ($text,$battle)
{


	  $dir=(int)($battle/1000);
	  if ($battle>=31010000)
	  	{
	  	$dir="/www_stat3/combats_stat/".$dir."000";
	  	}
	  	else
	  	{
		  $dir="/www_logs/combats_stat/".$dir."000";
	  	}


	  if (!is_dir($dir) )
	  {
	  mkdir($dir);
	  }



	$fp = fopen ($dir."/battle".((int)($battle)).".txt","a"); //открытие
	flock ($fp,LOCK_EX); //ЅЋќ »–ќ¬ ј ‘ј…Ћј
	fputs($fp , $text."\n"); //работа с файлом
	fflush ($fp); //ќ„»ў≈Ќ»≈ ‘ј…Ћќ¬ќ√ќ Ѕ”‘≈–ј » «јѕ»—№ ¬ ‘ј…Ћ
	flock ($fp,LOCK_UN); //—Ќя“»≈ ЅЋќ »–ќ¬ »
	fclose ($fp); //закрытие

}
///////////////////////////////////////////////////

function write_stat_adm ($text,$battle)
{
/*
	$fp = fopen ("/www_logs/statadm/battle".((int)($battle)).".txt","a"); //открытие
	flock ($fp,LOCK_EX); //ЅЋќ »–ќ¬ ј ‘ј…Ћј
	fputs($fp , $text."\n"); //работа с файлом
	fflush ($fp); //ќ„»ў≈Ќ»≈ ‘ј…Ћќ¬ќ√ќ Ѕ”‘≈–ј » «јѕ»—№ ¬ ‘ј…Ћ
	flock ($fp,LOCK_UN); //—Ќя“»≈ ЅЋќ »–ќ¬ »
	fclose ($fp); //закрытие
*/
}
///////////////////////////////////////////////////

// проверка тайма
function get_timeout ($battle_data,$telo)
{

if (($battle_data['win']==3) and ($battle_data['status']==0))
{

				//тайм дл€ 2-х сторонних боев
				if ($battle_data['t3']=='')
				{
					if ($telo['battle_t']=='1')
						{
							if($battle_data['to2'] <= $battle_data['to1'])
							{
								return ((time()-$battle_data['to2']) > $battle_data['timeout']*60);
							} else
							{
								return false;
							}
						}
					else
					if ($telo['battle_t']=='2')
						{
							if($battle_data['to2'] >= $battle_data['to1'])
							{
								return ((time()-$battle_data['to1']) > $battle_data['timeout']*60);
							} else
							{
								return false;
							}
						}
						else
						{
						return false;
						}
				}
				else
				//тай м дл€ 3-хстор
				{
					if ($telo['battle_t']=='1') {

							if (($battle_data['to2'] <= $battle_data['to1']) and ($battle_data['to3'] <= $battle_data['to1']) )
							{
							echo "T1";
							 return (((time()-$battle_data['to2']) > $battle_data['timeout']*60) and  ((time()-$battle_data['to3']) > $battle_data['timeout']*60))  ;
							} else {
							return false;
							}
						}
					else
					if ($telo['battle_t']=='2') {
							if ( ($battle_data['to1'] <= $battle_data['to2']) and ($battle_data['to3'] <= $battle_data['to2']) )
							{
							return ( ((time()-$battle_data['to1']) > $battle_data['timeout']*60) and ((time()-$battle_data['to3']) > $battle_data['timeout']*60)) ;
							} else {
							return false;
							}
					}
					else
					if ($telo['battle_t']=='3') {
							if ( ($battle_data['to1'] <= $battle_data['to3']) and ($battle_data['to2'] <= $battle_data['to3']) )
							{
							return ( ((time()-$battle_data['to2']) > $battle_data['timeout']*60) and ((time()-$battle_data['to1']) > $battle_data['timeout']*60)) ;
							} else {
							return false;
							}
					}
					else
					{
					return false;
					}

				}

}
				else
				{
				return false;
				}


}

//////////////////////////////////////////////////
function get_damexp($battle_id,$user_id)
{
  if($battle_id<1 || $user_id<1) {return array('damage'=>'0', 'exp'=>'0');}
  $r = mysql_fetch_array(mysql_query("SELECT damage, exp ,mag_damage, dflag FROM battle_dam_exp WHERE battle='{$battle_id}' and owner='{$user_id}'"));
  $rout=array();
  $rout['damage']=round($r['damage']);
  $rout['exp']=round($r['exp']);
  $rout['mag_damage']=round($r['mag_damage']);
  $rout['dflag']=$r['dflag'];
  return $rout;
}
/*---------------—лужебные функции:------------------------------------------
 проверка на попадание "куда надо" - да / нет - на выходе
--------------------------------------------------------------------*/

function set_telo_mana($telo,$magic_dem)
{

if ($telo['lab']>0) return true; // если тело в лабе не снимаем ману
if ($telo['room']>240 and $telo['room']<270) return true; // если тело в ристе против ботов  не снимаем ману


$mp=round($magic_dem*0.1); //10% от маг урона

if ($mp<1) { $mp=1; } // мин. снимаемой маны

		if ($telo['mana']>$mp)
			{
			mysql_query("UPDATE users set mana=mana-'{$mp}' where id='{$telo['id']}' LIMIT 1; ");
			}
			else
			{
			mysql_query("UPDATE users set mana=0 where id='{$telo['id']}' LIMIT 1; ");
			}

		if (mysql_affected_rows()>0)
			{
			return true;
			}
return false;
}



function get_block ($att,$def,$type=0)
			 {
		//addchp('<font color=red>¬нимание!</font> ƒебаг get_block: A:'.$att.'  D:'.$def.' Type:'.$type,'{[]}Bred{[]}',-1,0);
					if ($type!=0)
					{
						//  если в руке двуручна€ пушка и нет щита - режим 1 /2 - зона блока только одна
						$blocks[1]=array ("1","1");
						$blocks[2]=array ("2","2");
						$blocks[3]=array ("3","3");
						$blocks[4]=array ("4","4");
					}
					else
					{
						//  по типам блоков
						$blocks[1]=array ("1","2");
						$blocks[2]=array ("2","3");
						$blocks[3]=array ("3","4");
						$blocks[4]=array ("4","1");
					}


						$look=$blocks[$def];
						if (($att==$look[0])OR($att==$look[1]))
						 {
							return true;
						} else {
							return false;
						}

			}
///////////////–ендер тела в бою
// расчет оружи€ + соответствующее мастерство

function showtelo($telo,$wearItems,$magicItems,$telo_eff=null,$my_naem=false)
{
if ($_SESSION['uid'] == $telo['id'])
	{
	$pas=true;
	} else {
	$pas=false;
	} // иденифицируем отпервого лица
echo "<CENTER>";
if (($telo['hidden'] > 0 ) and ($telo['hiddenlog']==''))
{
//невидимка
		?>
		<div class="" style="white-space: nowrap;">
			<A HREF="javascript:top.AddToPrivate('Ќевидимка', top.CtrlPress)" target=refreshed><img src="http://i.oldbk.com/i/lock.gif" width=20 height=15></A><B><i>Ќевидимка</i></B> [??]<a href=inf.php?<?=$telo['hidden']?> target=_blank><IMG SRC='http://i.oldbk.com/i/inf.gif' WIDTH=12 HEIGHT=11 ALT="»нф. о Ќевидимка"></a>
		<?
		if ($my_naem!=false) { 	echo "<span id=mynaem>".$my_naem."</span>"; }
		echo "</div>";
}
else if (strpos($telo['login'],"Ќевидимка (клон" ) !== FALSE )
 {
//невидимка клон
		?>
		<div class="" style="white-space: nowrap;">
			<A HREF="javascript:top.AddToPrivate('<?=$telo['login']?>', top.CtrlPress)" target=refreshed><img src="http://i.oldbk.com/i/lock.gif" width=20 height=15></A><B><i><?=$telo['login']?></i></B> [??]<a href=inf.php?<?=$telo['id']?> target=_blank><IMG SRC='http://i.oldbk.com/i/inf.gif' WIDTH=12 HEIGHT=11 ALT="»нф. о <?=$telo['login']?>"></a>
		</div>
		<?

 }
else
{
//нет
$telo=load_perevopl($telo); //проверка и загрузка ника если перевополот
		?>
		<div class="" style="white-space: nowrap;">
			<A HREF="javascript:top.AddToPrivate('<?=$telo['login']?>', top.CtrlPress)" target=refreshed><img src="http://i.oldbk.com/i/lock.gif" width=20 height=15></A><img src="http://i.oldbk.com/i/align_<?echo ($telo['align']>0 ? $telo['align']:"0");?>.gif"><?php if ($telo['klan'] <> '') { echo '<img title="'.$telo['klan'].'" src="http://i.oldbk.com/i/klan/'.$telo['klan'].'.gif">'; } ?><B><?=$telo['login']?></B> [<?=$telo['level']?>]<a href=inf.php?<?=$telo['id']?> target=_blank><IMG SRC='http://i.oldbk.com/i/inf.gif' WIDTH=12 HEIGHT=11 ALT="»нф. о <?=$telo['login']?>"></a>
		<? if ($my_naem!=false) { 	echo "<span id=mynaem>".$my_naem."</span>"; } ?>
		</div>
		<?
}

		if ($telo['block']) {
					echo "<BR><FONT class=private>ѕерсонаж заблокирован!</font>";
		  		    }
		echo "<br>";


//полоска с жизн€ми + мана
	if (   ( ( ($telo['hidden'] > 0) and ($telo['hiddenlog']=='')  ) and (!$pas)) OR (strpos($telo['login'],"Ќевидимка (клон" ) !== FALSE ))
	{
	//невидимка причем не €
	echo setHP('??','??',1);
	echo setMP('??','??',1);




	echo "<TABLE cellspacing=0 cellpadding=0><TR><TD width=62 valign=top>";
   	echo " <img src='http://i.oldbk.com/i/shadow/mhidden_full.gif' title='Ќевидимка' alt='Ќевидимка'><br /></table> ";
   	echo "<TABLE cellPadding=0 cellSpacing=0 width=100%><TBODY><TR><TD colSpan=2 style=\"padding-left:25px;\">";
	echo "—ила: ??<BR>";
	echo "Ћовкость: ??<BR>";
	echo "»нтуици€: ??<BR>";
	echo "¬ыносливость: ??<BR>";
	echo "»нтеллект: ??<BR>";
	echo "ћудрость: ??<BR>";
 	echo "</td></tr></table>";
	}
	else if ( ($telo['hiddenlog']!='') and (!$pas) )
	{
	//echo "отображение перевоплота дл€ врага";
	//echo "<div align=left>";
	echo setHP($telo['hp'],$telo['maxhp'],1);

	 $fakedata=explode(",",$telo['hiddenlog']);
	 $ftelo['id'] = $fakedata[0];

	$fake_telo=check_users_city_data($ftelo['id']);
//	$fake_telo=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `id` = '{$ftelo['id']}' LIMIT 1;")); // загрузка- перевопловских шмоток - в теле-id уже поддельный ид
//	if ($fake_telo[id_city]==1) { 	$fake_telo=mysql_fetch_array(mysql_query("SELECT * FROM avalon.`users` WHERE `id` = '{$ftelo['id']}' LIMIT 1;")); }




	if ($fake_telo['maxmana']) 	{ echo setMP($fake_telo['mana'],$fake_telo['maxmana'],1); }

/*
	if (($telo['level']>=4)AND($telo['id']<10000000) AND ($telo['stamina']>0) )
					{
			$q = mysql_query('SELECT * FROM effects WHERE owner = '.$telo['id'].' and type = 795');
			if (mysql_num_rows($q) > 0) {
				 echo setST($telo['stamina'],$telo['level']+2);
			} else {
				 echo setST($telo['stamina'],$telo['level']);
			}
					 }
*/

	//echo "</div>";



	$fake_items = load_mass_items_by_id($fake_telo);
	$fake_mag = array();
	render_telo ($fake_telo,$fake_items,$fake_mag,false,$telo_eff); //рисуем врага  если он перевопл

	}
	else
	{
	//echo "<div align=left>";
	echo setHP($telo['hp'],$telo['maxhp'],1);
	if ($telo['maxmana'])
	{
	echo setMP($telo['mana'],$telo['maxmana'],1);
	}

	if ($telo['fullentime'] >0 )
	{
	//если показываем наема и у него есть врем€ регена енергии то рисуем ее
		echo setnaemEN($telo['energy'],($telo['level']*5),1);
	}

/*
	if (($telo['level']>=4)AND($telo['id']<10000000) AND ($telo['stamina']>0) )  {
			$q = mysql_query('SELECT * FROM effects WHERE owner = '.$telo['id'].' and type = 795');
			if (mysql_num_rows($q) > 0) {
				 echo setST($telo['stamina'],$telo['level']+2);
			} else {
				 echo setST($telo['stamina'],$telo['level']);
			}
	}
	*/
	//echo "</div>";
	render_telo ($telo,$wearItems,$magicItems,$pas,$telo_eff); //рисуем врага или себ€, если € в нормальном режиме

	}


}

function render_telo ($telo,$wearItems,$magicItems,$pas,$telo_eff=null)
{
global $nclass_name;
	if ($pas==true)
	{
	//поправки дл€ отображени€
	$r=0;	//показываем подсказку справа на лево
	$x[0][1]=55;   //серьги
	$x[0][2]=55;	// одна брони
	$x[0][3]=55;	// две брони
	$x[0][4]=55;	// три брони
	$x[0][5]=17;	// кольца
	$x[0][6]=17;	// руны
	} else {
	//поправки дл€ отображени€
	$r=1; // показываем подсказку слева на право
	$x[1][1]=35;  //серьги
	$x[1][2]=35;	// одна
	$x[1][3]=145;	// две
	$x[1][4]=245;	// три
	$x[1][5]=47;	// кольца
	$x[1][6]=47;	// руны
	}


	if (isset($telo['id_user']) && $telo['id_user'] == 12) {
		$telo['sila'] = 10000;
		$telo['lovk'] = 10000;
		$telo['intel'] = 10000;
		$telo['inta'] = 10000;
		$telo['mudra'] = 10000;
		$telo['vinos'] = 10000;
	}


	// карнавальные образы
	$is301 = false;
	$is302 = false;

	if (($telo['in_tower']==0) and ($telo['ruines']==0) )
	{
	//работает только в клубе
		if ($telo_eff['301']['add_info'] != "") {
			$is301 = $telo_eff['301']['add_info'];
		}
		if ($telo_eff['302']['add_info'] != "") {
			$is301 = $telo_eff['302']['add_info'];
		}
	}

	/*
	$is301 = false;
	$queryeff = mysql_query("SELECT * FROM `effects` WHERE `owner` = ".$telo['id']." and type = 301");
	if (mysql_num_rows($queryeff) > 0) {
		$r = mysql_fetch_assoc($queryeff);
		$is301 = $r['add_info'];
	}
	*/


	if ($is301 !== false) {
		if ($telo['sergi']) $wearItems[$telo['sergi']]['img'] = $is301."sergi.gif";
		if ($telo['kulon']) $wearItems[$telo['kulon']]['img'] = $is301."kulon.gif";
		if ($telo['perchi']) $wearItems[$telo['perchi']]['img'] = $is301."per4i.gif";
		if ($telo['weap']) $wearItems[$telo['weap']]['img'] = $is301."weapon.gif";
		if ($telo['bron']) $wearItems[$telo['bron']]['img'] = $is301."armor.gif";
		if ($telo['r1']) $wearItems[$telo['r1']]['img'] = $is301."ring1.gif";
		if ($telo['r2']) $wearItems[$telo['r2']]['img'] = $is301."ring2.gif";
		if ($telo['r3']) $wearItems[$telo['r3']]['img'] = $is301."ring3.gif";
		if ($telo['helm']) $wearItems[$telo['helm']]['img'] = $is301."helm.gif";
		if ($telo['shit'])  $wearItems[$telo['shit']]['img'] = $is301."shield.gif";
		if ($telo['boots']) $wearItems[$telo['boots']]['img'] = $is301."boots.gif";
		if ($telo['nakidka']) $wearItems[$telo['nakidka']]['img'] = $is301."armor.gif";
		if ($telo['rubashka']) $wearItems[$telo['rubashka']]['img'] = $is301."armor.gif";
	}

	if ($is302 !== false) {
		if ($telo['sergi']) $wearItems[$telo['sergi']]['img'] = $is302."sergi.gif";
		if ($telo['kulon']) $wearItems[$telo['kulon']]['img'] = $is302."kulon.gif";
		if ($telo['perchi']) $wearItems[$telo['perchi']]['img'] = $is302."per4i.gif";
		if ($telo['weap']) $wearItems[$telo['weap']]['img'] = $is302."weapon.gif";
		if ($telo['bron']) $wearItems[$telo['bron']]['img'] = $is302."armor.gif";
		if ($telo['r1']) $wearItems[$telo['r1']]['img'] = $is302."ring1.gif";
		if ($telo['r2']) $wearItems[$telo['r2']]['img'] = $is302."ring2.gif";
		if ($telo['r3']) $wearItems[$telo['r3']]['img'] = $is302."ring3.gif";
		if ($telo['helm']) $wearItems[$telo['helm']]['img'] = $is302."helm.gif";
		if ($telo['shit'])  $wearItems[$telo['shit']]['img'] = $is302."shield.gif";
		if ($telo['boots']) $wearItems[$telo['boots']]['img'] = $is302."boots.gif";
		if ($telo['nakidka']) $wearItems[$telo['nakidka']]['img'] = $is302."armor.gif";
		if ($telo['rubashka']) $wearItems[$telo['rubashka']]['img'] = $is302."armor.gif";
	}


	if (strpos($telo['shadow'],"chaos_bot") !== false || strpos($telo['shadow'],"ruine_botskelet") !== false) {
		echo "<TABLE cellspacing=0 cellpadding=0><TR><TD valign=top>";
		echo " <img src='http://i.oldbk.com/i/shadow/".$telo['shadow']."' title='".$telo['login']."' alt='".$telo['login']."'><br /></TD></TR></table> ";
	}
	else
	{
///// поехали рисовать
echo "<TABLE cellspacing=0 cellpadding=0><TR><TD valign=top>";
?>
<TABLE width=100% cellspacing=0 cellpadding=0 >
	<TR><TD <?=(((($wearItems[$telo['sergi']]['maxdur']-2)<=$wearItems[$telo['sergi']]['duration'] && $wearItems[$telo['sergi']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>>
<?php
		if ($telo['sergi'] > 0 && !$darkbs) {
			$dress = $wearItems[$telo['sergi']];
			if (($dress['includemagicdex'] > 0) && ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
//				$ehtml=str_replace('.gif','.html',$dress['img']);
				$ehtml=render_img_html($dress);
				if ($pas==true)
				{
				echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][1].',20,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа серьгах выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				}
				else
				{
				echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][1].',20,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа серьгах выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				}
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/w1.gif" width=60 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][1].',20,\'ѕустой слот серьги\','.$r.')">';
		}
	?></a></TD></TR>
	<TR><TD <?=(((($wearItems[$telo['kulon']]['maxdur']-2)<=$wearItems[$telo['kulon']]['duration'] && $wearItems[$telo['kulon']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>><?php
		//onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,15,
		if ($telo['kulon'] > 0 && !$darkbs) {
			$dress = $wearItems[$telo['kulon']];
			if (($dress['includemagicdex'] > 0) && ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				//$ehtml=str_replace('.gif','.html',$dress['img']);
				$ehtml=render_img_html($dress);
				if ($pas==true)
				{
				echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,15,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа ожерелье выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				}
				else
				{
				echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,15,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа ожерелье выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				}
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/w2.gif" width=60 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,15,\'ѕустой слот ожерелье\','.$r.')">';
		}
	?></A></TD></TR>
	<TR><TD <?=(((($wearItems[$telo['weap']]['maxdur']-2)<=$wearItems[$telo['weap']]['duration'] && $wearItems[$telo['weap']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>><?php
		if ($telo['weap'] > 0 && !$darkbs) {
			$dress = $wearItems[$telo['weap']];
			if (($dress['includemagicdex'] > 0) && ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
			$ehtml=render_img_html($dress);
			if ($pas==true)
			 {
			 $elkas='';

			if (((($dress['prototype'] >= 55510301) AND ($dress['prototype'] <= 55510311)) || (($dress['prototype'] >= 55510328) AND ($dress['prototype'] <= 55510333))) or ($dress['prototype']==55510350) or ($dress['prototype']==55510351) or ($dress['prototype']==55510352) or ($dress['prototype']==410027) or ($dress['prototype']==410028)  )
			 	{
			 	$elkas="<br>Ќакопленный урон: <b>".$dress['up_level']."</b>";
			 	}

			 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img  src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=60 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,55,\'<b>'.$dress['name']."</b>".$elkas."<br>ѕрочность ".$dress['duration']."/".$dress['maxdur'].(($dress['minu']>0)?"  <br>”рон {$dress['minu']}-{$dress['maxu']}":"")."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br> Ќа оружии выгравировано: {$dress['text']}":"").'\','.$r.')" >';
			 }
			 else
			 {
			 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img  src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=60 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,55,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur'].(($dress['minu']>0)?"  <br>”рон {$dress['minu']}-{$dress['maxu']}":"")." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br> Ќа оружии выгравировано: {$dress['text']}":"").'\','.$r.')" >';
			 }
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/w3.gif" width=60 height=60 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,55,\'ѕустой слот оружие\','.$r.')">';
		}
	?></A></TD></TR>
	<TR><TD <?=(((($wearItems[$telo['rubashka']]['maxdur']-2)<=$wearItems[$telo['rubashka']]['duration'] && $wearItems[$telo['rubashka']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>
 		<?=(((($wearItems[$telo['bron']]['maxdur']-2)<=$wearItems[$telo['bron']]['duration'] && $wearItems[$telo['bron']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>
		<?=(((($wearItems[$telo['nakidka']]['maxdur']-2)<=$wearItems[$telo['nakidka']]['duration'] && $wearItems[$telo['nakidka']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>

	><?php
	//момент дл€ инфы перса , но не в инвинтаре!!
		if ( ( ($telo['rubashka'] == 0) and ($telo['bron'] == 0) and ($telo['nakidka'] > 0) ) and  (!$darkbs))
		{
		// только накидка (без рубашки)
			$dress = $wearItems[$telo['nakidka']];
			if ($dress['includemagicdex']&& ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				$ehtml=render_img_html($dress);
				if ($pas==true)
				{
				echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img  '.((($dress['maxdur']-2)<=$dress['duration'] && $dress['duration'] > 2 && !$pas)?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":"").' src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][2].',75,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['gmp']!=0)?"<br>Х ћудрость:{$dress['gmp']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'\','.$r.')" >';
				}
				else
				{
				echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img  '.((($dress['maxdur']-2)<=$dress['duration'] && $dress['duration'] > 2 && !$pas)?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":"").' src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][2].',75,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'\','.$r.')" >';
				}
			}
		}
		elseif ((($telo['rubashka'] == 0) and ($telo['bron'] > 0) and ($telo['nakidka'] > 0) ) and  (!$darkbs))
		{
		// бронь и накидка (без рубашки)
			$dress = $wearItems[$telo['nakidka']];
			$dress2 = $wearItems[$telo['bron']];
			if ($dress['includemagicdex']&& ($pas==true))
			{
			//1встройка в накидке
			   if ($dress2['includemagicdex']&& ($pas==true))
			   	{
			   	//3. встройка и в броне
				 showMagicHref3($dress,$dress2, $magicItems[$dress['includemagic']]);
				} else
				{
				//4. встройка только в накидке в броне нету
				showMagicHref4($dress, $dress2,$magicItems[$dress['includemagic']]);
				}
			} else {
			//2нет встройки в накидке
			   if ($dress2['includemagicdex']&& ($pas==true))
			   {
   			   //5. встройка в броне , не в накидке
			   	showMagicHref2($dress, $dress2,$magicItems[$dress2['includemagic']]);
			   }
			   else
			   {

			   //6. нет встройки не в накидке и не в броне
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				  echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][3].',75,\'<table border=0 cellspacing=5 cellpadding=0><tr valign=top><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress['img'].' width=60 height=80><br><b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['gmp']!=0)?"<br>Х ћудрость:{$dress['gmp']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'</span></td><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress2['img'].' width=60 height=80><br><b>'.$dress2['name']."</b><br>ѕрочность ".$dress2['duration']."/".$dress2['maxdur']."<br>ƒействует на: ".(($dress2['ghp']>0)?"<br>”ровень жизни +{$dress2['ghp']}":"")." ".(($dress2['gsila']!=0)?"<br>Х —ила:{$dress2['gsila']}":"")." ".(($dress2['glovk']!=0)?"<br>Х Ћовкость:{$dress2['glovk']}":"")." ".(($dress2['ginta']!=0)?"<br>Х »нтуици€:{$dress2['ginta']}":"")." ".(($dress2['gintel']!=0)?"<br>Х »нтеллект:{$dress2['gintel']}":"")." ".(($dress2['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress2['mfkrit']}%":"")." ".(($dress2['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress2['mfakrit']}%":"")." ".(($dress2['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress2['mfuvorot']}%":"")." ".(($dress2['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress2['mfauvorot']}%":"")." ".(($dress2['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress2['gnoj']}":"")." ".(($dress2['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress2['gtopor']}":"")." ".(($dress2['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress2['gdubina']}":"")." ".(($dress2['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress2['gmech']}":"")." ".(($dress2['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress2['gfire']}":"")." ".(($dress2['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress2['gwater']}":"")." ".(($dress2['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress2['gair']}":"")." ".(($dress2['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress2['gearth']}":"")." ".(($dress2['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress2['glight']}":"")." ".(($dress2['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress2['ggray']}":"")." ".(($dress2['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress2['gdark']}":"")." ".(($dress2['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress2['bron1']}":"")." ".(($dress2['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress2['bron2']}":"")." ".(($dress2['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress2['bron3']}":"")." ".(($dress2['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress2['bron4']}":"")." ".(($dress2['text']!=null)?"<br>  Ќа одежде вышито '{$dress2['text']}'":"").'</span></td></tr></table>\','.$r.')" >';
				  }
				  else
				  {
				   echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][3].',75,\'<table border=0 cellspacing=5 cellpadding=0><tr valign=top><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress['img'].' width=60 height=80><br><b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'</span></td><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress2['img'].' width=60 height=80><br><b>'.$dress2['name']."</b><br>ѕрочность ".$dress2['duration']."/".$dress2['maxdur']." ".(($dress2['ghp']>0)?"<br>”ровень жизни +{$dress2['ghp']}":"")." ".(($dress2['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress2['bron1']}":"")." ".(($dress2['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress2['bron2']}":"")." ".(($dress2['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress2['bron3']}":"")." ".(($dress2['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress2['bron4']}":"")." ".(($dress2['text']!=null)?"<br>  Ќа одежде вышито '{$dress2['text']}'":"").'</span></td></tr></table>\','.$r.')" >';
				  }
				}
			}


		}
		elseif ( (($telo['rubashka'] == 0) and ($telo['bron'] > 0) and ($telo['nakidka']==0)) and  (!$darkbs))
		{
		// только бронь (без рубашки)
			$dress = $wearItems[$telo['bron']];
			if ($dress['includemagicdex']&& ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][2].',75,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['gmp']!=0)?"<br>Х ћудрость:{$dress['gmp']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'\','.$r.')" >';
				 }
				 else
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][2].',75,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'\','.$r.')" >';
				 }
			}
		}
		elseif ( ( ($telo['rubashka'] >0) and ($telo['bron'] == 0) and ($telo['nakidka'] > 0) ) and  (!$darkbs))
		{
		// только накидка + рубашка
			$dress = $wearItems[$telo['nakidka']];
			$dress2 = $wearItems[$telo['rubashka']];
			if ($dress['includemagicdex']&& ($pas==true))
			{

			   if ($dress2['includemagicdex']&& ($pas==true))
			   	{

				 showMagicHref3($dress,$dress2, $magicItems[$dress['includemagic']]);
				} else
				{

				showMagicHref4($dress, $dress2,$magicItems[$dress['includemagic']]);
				}
			} else {

			   if ($dress2['includemagicdex']&& ($pas==true))
			   {

			   	showMagicHref2($dress, $dress2,$magicItems[$dress2['includemagic']]);
			   }
			   else
			   {

			   //6. нет встройки не в накидке и не в броне
				$ehtml=render_img_html($dress);

				if ($pas==true)
				 {
				  echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][3].',75,\'<table border=0 cellspacing=5 cellpadding=0><tr valign=top><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress['img'].' width=60 height=80><br><b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['gmp']!=0)?"<br>Х ћудрость:{$dress['gmp']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'</span></td><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress2['img'].' width=60 height=80><br><b>'.$dress2['name']."</b><br>ѕрочность ".$dress2['duration']."/".$dress2['maxdur']."<br>ƒействует на: ".(($dress2['ghp']>0)?"<br>”ровень жизни +{$dress2['ghp']}":"")." ".(($dress2['gsila']!=0)?"<br>Х —ила:{$dress2['gsila']}":"")." ".(($dress2['glovk']!=0)?"<br>Х Ћовкость:{$dress2['glovk']}":"")." ".(($dress2['ginta']!=0)?"<br>Х »нтуици€:{$dress2['ginta']}":"")." ".(($dress2['gintel']!=0)?"<br>Х »нтеллект:{$dress2['gintel']}":"")." ".(($dress2['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress2['mfkrit']}%":"")." ".(($dress2['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress2['mfakrit']}%":"")." ".(($dress2['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress2['mfuvorot']}%":"")." ".(($dress2['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress2['mfauvorot']}%":"")." ".(($dress2['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress2['gnoj']}":"")." ".(($dress2['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress2['gtopor']}":"")." ".(($dress2['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress2['gdubina']}":"")." ".(($dress2['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress2['gmech']}":"")." ".(($dress2['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress2['gfire']}":"")." ".(($dress2['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress2['gwater']}":"")." ".(($dress2['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress2['gair']}":"")." ".(($dress2['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress2['gearth']}":"")." ".(($dress2['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress2['glight']}":"")." ".(($dress2['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress2['ggray']}":"")." ".(($dress2['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress2['gdark']}":"")." ".(($dress2['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress2['bron1']}":"")." ".(($dress2['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress2['bron2']}":"")." ".(($dress2['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress2['bron3']}":"")." ".(($dress2['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress2['bron4']}":"")." ".(($dress2['text']!=null)?"<br>  Ќа одежде вышито '{$dress2['text']}'":"").'</span></td></tr></table>\','.$r.')" >';
			   	 }
			   	 else
			   	 {
				  echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][3].',75,\'<table border=0 cellspacing=5 cellpadding=0><tr valign=top><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress['img'].' width=60 height=80><br><b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'</span></td><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress2['img'].' width=60 height=80><br><b>'.$dress2['name']."</b><br>ѕрочность ".$dress2['duration']."/".$dress2['maxdur']." ".(($dress2['ghp']>0)?"<br>”ровень жизни +{$dress2['ghp']}":"")." ".(($dress2['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress2['bron1']}":"")." ".(($dress2['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress2['bron2']}":"")." ".(($dress2['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress2['bron3']}":"")." ".(($dress2['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress2['bron4']}":"")." ".(($dress2['text']!=null)?"<br>  Ќа одежде вышито '{$dress2['text']}'":"").'</span></td></tr></table>\','.$r.')" >';
			   	 }
			   }
			}


		}
		elseif ( (($telo['rubashka'] > 0 ) and ($telo['bron'] > 0) and ($telo['nakidka']==0)) and  (!$darkbs))
		{
		// только бронь + рубашка
			$dress = $wearItems[$telo['bron']];
			$dress2 = $wearItems[$telo['rubashka']];
			if ($dress['includemagicdex']&& ($pas==true))
			{

			   if ($dress2['includemagicdex']&& ($pas==true))
			   	{

				 showMagicHref3($dress,$dress2, $magicItems[$dress['includemagic']]);
				} else
				{

				showMagicHref4($dress, $dress2,$magicItems[$dress['includemagic']]);
				}
			} else {

			   if ($dress2['includemagicdex']&& ($pas==true))
			   {

			   	showMagicHref2($dress, $dress2,$magicItems[$dress2['includemagic']]);
			   }
			   else
			   {

			   //6. нет встройки не в накидке и не в броне
				$ehtml=render_img_html($dress);

				if ($pas==true)
				{
				echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][3].',75,\'<table border=0 cellspacing=5 cellpadding=0><tr valign=top><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress['img'].' width=60 height=80><br><b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['gmp']!=0)?"<br>Х ћудрость:{$dress['gmp']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'</span></td><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress2['img'].' width=60 height=80><br><b>'.$dress2['name']."</b><br>ѕрочность ".$dress2['duration']."/".$dress2['maxdur']."<br>ƒействует на: ".(($dress2['ghp']>0)?"<br>”ровень жизни +{$dress2['ghp']}":"")." ".(($dress2['gsila']!=0)?"<br>Х —ила:{$dress2['gsila']}":"")." ".(($dress2['glovk']!=0)?"<br>Х Ћовкость:{$dress2['glovk']}":"")." ".(($dress2['ginta']!=0)?"<br>Х »нтуици€:{$dress2['ginta']}":"")." ".(($dress2['gintel']!=0)?"<br>Х »нтеллект:{$dress2['gintel']}":"")." ".(($dress2['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress2['mfkrit']}%":"")." ".(($dress2['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress2['mfakrit']}%":"")." ".(($dress2['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress2['mfuvorot']}%":"")." ".(($dress2['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress2['mfauvorot']}%":"")." ".(($dress2['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress2['gnoj']}":"")." ".(($dress2['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress2['gtopor']}":"")." ".(($dress2['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress2['gdubina']}":"")." ".(($dress2['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress2['gmech']}":"")." ".(($dress2['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress2['gfire']}":"")." ".(($dress2['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress2['gwater']}":"")." ".(($dress2['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress2['gair']}":"")." ".(($dress2['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress2['gearth']}":"")." ".(($dress2['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress2['glight']}":"")." ".(($dress2['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress2['ggray']}":"")." ".(($dress2['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress2['gdark']}":"")." ".(($dress2['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress2['bron1']}":"")." ".(($dress2['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress2['bron2']}":"")." ".(($dress2['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress2['bron3']}":"")." ".(($dress2['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress2['bron4']}":"")." ".(($dress2['text']!=null)?"<br>  Ќа одежде вышито '{$dress2['text']}'":"").'</span></td></tr></table>\','.$r.')" >';
				}
				else
				{
				echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][3].',75,\'<table border=0 cellspacing=5 cellpadding=0><tr valign=top><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress['img'].' width=60 height=80><br><b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'</span></td><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress2['img'].' width=60 height=80><br><b>'.$dress2['name']."</b><br>ѕрочность ".$dress2['duration']."/".$dress2['maxdur']." ".(($dress2['ghp']>0)?"<br>”ровень жизни +{$dress2['ghp']}":"")." ".(($dress2['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress2['bron1']}":"")." ".(($dress2['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress2['bron2']}":"")." ".(($dress2['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress2['bron3']}":"")." ".(($dress2['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress2['bron4']}":"")." ".(($dress2['text']!=null)?"<br>  Ќа одежде вышито '{$dress2['text']}'":"").'</span></td></tr></table>\','.$r.')" >';
				}
			   }
			}


		}
		elseif ( (($telo['rubashka'] > 0 ) and ($telo['bron'] > 0) and ($telo['nakidka'] > 0)) and  (!$darkbs))
		{
		// только накидка + бронь +рубашка
			$dress = $wearItems[$telo['nakidka']];
			$dress2 = $wearItems[$telo['bron']];
			$dress3 = $wearItems[$telo['rubashka']];

			if ( ( ($dress['includemagicdex']) OR ($dress2['includemagicdex']) OR ($dress3['includemagicdex'])  )  && ($pas==true))
			{
			 showMagicHref21($dress,$dress2,$dress3,$magicItems[$dress['includemagic']],$magicItems[$dress2['includemagic']],$magicItems3[$dress['includemagic']] );
			}
		   	else
			   {
			   //6. нет встройки нихде
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				  {
				  echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][4].',75,\'<table border=0 cellspacing=5 cellpadding=0><tr valign=top><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress['img'].' width=60 height=80><br><b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['gmp']!=0)?"<br>Х ћудрость:{$dress['gmp']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'</span></td><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress2['img'].' width=60 height=80><br><b>'.$dress2['name']."</b><br>ѕрочность ".$dress2['duration']."/".$dress2['maxdur']."<br>ƒействует на: ".(($dress2['ghp']>0)?"<br>”ровень жизни +{$dress2['ghp']}":"")." ".(($dress2['gsila']!=0)?"<br>Х —ила:{$dress2['gsila']}":"")." ".(($dress2['glovk']!=0)?"<br>Х Ћовкость:{$dress2['glovk']}":"")." ".(($dress2['ginta']!=0)?"<br>Х »нтуици€:{$dress2['ginta']}":"")." ".(($dress2['gintel']!=0)?"<br>Х »нтеллект:{$dress2['gintel']}":"")." ".(($dress2['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress2['mfkrit']}%":"")." ".(($dress2['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress2['mfakrit']}%":"")." ".(($dress2['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress2['mfuvorot']}%":"")." ".(($dress2['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress2['mfauvorot']}%":"")." ".(($dress2['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress2['gnoj']}":"")." ".(($dress2['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress2['gtopor']}":"")." ".(($dress2['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress2['gdubina']}":"")." ".(($dress2['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress2['gmech']}":"")." ".(($dress2['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress2['gfire']}":"")." ".(($dress2['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress2['gwater']}":"")." ".(($dress2['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress2['gair']}":"")." ".(($dress2['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress2['gearth']}":"")." ".(($dress2['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress2['glight']}":"")." ".(($dress2['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress2['ggray']}":"")." ".(($dress2['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress2['gdark']}":"")." ".(($dress2['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress2['bron1']}":"")." ".(($dress2['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress2['bron2']}":"")." ".(($dress2['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress2['bron3']}":"")." ".(($dress2['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress2['bron4']}":"")." ".(($dress2['text']!=null)?"<br>  Ќа одежде вышито '{$dress2['text']}'":"").'</span></td><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress3['img'].' width=60 height=80><br><b>'.$dress3['name']."</b><br>ѕрочность ".$dress3['duration']."/".$dress3['maxdur']."<br>ƒействует на: ".(($dress3['ghp']>0)?"<br>”ровень жизни +{$dress3['ghp']}":"")." ".(($dress3['gsila']!=0)?"<br>Х —ила:{$dress3['gsila']}":"")." ".(($dress3['glovk']!=0)?"<br>Х Ћовкость:{$dress3['glovk']}":"")." ".(($dress3['ginta']!=0)?"<br>Х »нтуици€:{$dress3['ginta']}":"")." ".(($dress3['gintel']!=0)?"<br>Х »нтеллект:{$dress3['gintel']}":"")." ".(($dress3['gmp']!=0)?"<br>Х ћудрость:{$dress3['gmp']}":"")." ".(($dress3['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress3['mfkrit']}%":"")." ".(($dress3['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress3['mfakrit']}%":"")." ".(($dress3['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress3['mfuvorot']}%":"")." ".(($dress3['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress3['mfauvorot']}%":"")." ".(($dress3['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress3['gnoj']}":"")." ".(($dress3['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress3['gtopor']}":"")." ".(($dress3['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress3['gdubina']}":"")." ".(($dress3['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress3['gmech']}":"")." ".(($dress3['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress3['gfire']}":"")." ".(($dress3['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress3['gwater']}":"")." ".(($dress3['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress3['gair']}":"")." ".(($dress3['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress3['gearth']}":"")." ".(($dress3['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress3['glight']}":"")." ".(($dress3['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress3['ggray']}":"")." ".(($dress3['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress3['gdark']}":"")." ".(($dress3['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress3['bron1']}":"")." ".(($dress3['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress3['bron2']}":"")." ".(($dress3['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress3['bron3']}":"")." ".(($dress3['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress3['bron4']}":"")." ".(($dress3['text']!=null)?"<br>  Ќа одежде вышито: {$dress3['text']}":"").'</span></td></tr></table>\','.$r.')" >';
				  }
				  else
				  {
				  echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][4].',75,\'<table border=0 cellspacing=5 cellpadding=0><tr valign=top><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress['img'].' width=60 height=80><br><b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'</span></td><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress2['img'].' width=60 height=80><br><b>'.$dress2['name']."</b><br>ѕрочность ".$dress2['duration']."/".$dress2['maxdur']." ".(($dress2['ghp']>0)?"<br>”ровень жизни +{$dress2['ghp']}":"")." ".(($dress2['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress2['bron1']}":"")." ".(($dress2['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress2['bron2']}":"")." ".(($dress2['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress2['bron3']}":"")." ".(($dress2['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress2['bron4']}":"")." ".(($dress2['text']!=null)?"<br>  Ќа одежде вышито '{$dress2['text']}'":"").'</span></td><td><span  style=font-size:9px><img src=http://i.oldbk.com/i/sh/'.$dress3['img'].' width=60 height=80><br><b>'.$dress3['name']."</b><br>ѕрочность ".$dress3['duration']."/".$dress3['maxdur']." ".(($dress3['ghp']>0)?"<br>”ровень жизни +{$dress3['ghp']}":"")." ".(($dress3['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress3['bron1']}":"")." ".(($dress3['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress3['bron2']}":"")." ".(($dress3['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress3['bron3']}":"")." ".(($dress3['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress3['bron4']}":"")." ".(($dress3['text']!=null)?"<br>  Ќа одежде вышито: {$dress3['text']}":"").'</span></td></tr></table>\','.$r.')" >';
				  }
				}

		}
		else
		if ( ( ($telo['rubashka'] > 0) and ($telo['bron'] == 0) and ($telo['nakidka'] == 0) ) and  (!$darkbs))
		{
		// только рубашка
			$dress = $wearItems[$telo['rubashka']];
			if ($dress['includemagicdex']&& ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img  '.((($dress['maxdur']-2)<=$dress['duration'] && $dress['duration'] > 2 && !$pas)?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":"").' src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][2].',75,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['gmp']!=0)?"<br>Х ћудрость:{$dress['gmp']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'\','.$r.')" >';
				 }
				 else
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img  '.((($dress['maxdur']-2)<=$dress['duration'] && $dress['duration'] > 2 && !$pas)?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":"").' src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][2].',75,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа одежде вышито: {$dress['text']}":"").'\','.$r.')" >';
				 }
			}
		}
		else {
			echo '<img src="http://i.oldbk.com/i/w4.gif" width=60 height=80 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][2].',75,\'ѕустой слот брон€\','.$r.')">';
		}
	?></a></TD></TR>
	<TR><TD><TABLE cellspacing=0 cellpadding=0><tr>
		<td <?=(((($wearItems[$telo['r1']]['maxdur']-2)<=$wearItems[$telo['r1']]['duration'] && $wearItems[$telo['r1']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>><?php
		if (($telo['r1'] > 0 && !$darkbs) AND ($telo['r1']!=15)) {
			$dress = $wearItems[$telo['r1']];
			if (($dress['includemagicdex'] > 0) && ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=20 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][5].',17,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа кольце выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
				 else
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=20 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][5].',17,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа кольце выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/w6.gif" width=20 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][5].',17,\'ѕустой слот кольцо\','.$r.')" >';
		}
	?></A></td>
		<td <?=(((($wearItems[$telo['r2']]['maxdur']-2)<=$wearItems[$telo['r2']]['duration'] && $wearItems[$telo['r2']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>><?php
		if ($telo['r2'] > 0 && !$darkbs) {
			$dress = $wearItems[$telo['r2']];
			if (($dress['includemagicdex'] > 0) && ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=20 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][5].',17,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа кольце выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
				 else
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=20 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][5].',17,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа кольце выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/w6.gif" width=20 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][5].',17,\'ѕустой слот кольцо\','.$r.')" >';
		}
	?></A></td>
		<td <?=(((($wearItems[$telo['r3']]['maxdur']-2)<=$wearItems[$telo['r3']]['duration'] && $wearItems[$telo['r3']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>><?php
		if ($telo['r3'] > 0 && !$darkbs) {
			$dress = $wearItems[$telo['r3']];
			if (($dress['includemagicdex'] > 0) && ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=20 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][5].',17,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа кольце выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
				 else
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=20 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][5].',17,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа кольце выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/w6.gif" width=20 height=20 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][5].',17,\'ѕустой слот кольцо\','.$r.')" >';
		}
	?></A></td>

        </tr></table>
</TD></TR>
</TABLE>
<?
//образы на 1 апрел€
$begin=mktime(0,0,1,4,01,2012);
$end=mktime(23, 59, 59,4,01,2012);
if(time()>$begin && time()<$end)
{
 	$telo['shadow']='1a'.($telo['sex']==1?'m':'f').mt_rand(1,10).'.gif';
}

if ($is301 !== false) {
	$telo['shadow'] = $is301."obraz.gif";
}

if ($is302 !== false) {
	$telo['shadow'] = $is302."obraz.gif";
}

?>
	</TD><TD valign=top><img src="http://i.oldbk.com/i/shadow/<?=$telo['shadow']?>" width=76 height=209 alt="<?=$telo['login']?>"></TD><TD width=62 valign=top>
	<!--</TD><TD valign=top><img src="http://i.oldbk.com/i/shadow/1a<? $fuck = array('f','m'); echo $fuck[$telo['sex']]; ?><?=mt_rand(1,10)?>.gif" width=76 height=209 alt="<?=$telo['login']?>"></TD><TD width=62 valign=top>>-->
<TABLE width=100% cellspacing=0 cellpadding=0>
	<TR><TD <?=(((($wearItems[$telo['helm']]['maxdur']-2)<=$wearItems[$telo['helm']]['duration'] && $wearItems[$telo['helm']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>><?php
		if ($telo['helm'] > 0 && !$darkbs) {
			$dress = $wearItems[$telo['helm']];

			if (($dress['includemagicdex'] > 0) && ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
     			$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=60 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,55,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа шлеме выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
				 else
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=60 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,55,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа шлеме выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/w9.gif" width=60 height=60 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,55,\'ѕустой слот шлем\','.$r.')" >';
		}
	?></A></TD></TR>
	<TR><TD <?=(((($wearItems[$telo['perchi']]['maxdur']-2)<=$wearItems[$telo['perchi']]['duration'] && $wearItems[$telo['perchi']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>><?php
		if ($telo['perchi'] > 0 && !$darkbs) {
			$dress = $wearItems[$telo['perchi']];
			if (($dress['includemagicdex'] > 0) && ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=40 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,35,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа перчатках выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
				 else
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=40 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,35,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа перчатках выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/w11.gif" width=60 height=40 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,35,\'ѕустой слот перчатки\','.$r.')" >';
		}
	?></A></TD></TR>
	<TR><TD <?=(((($wearItems[$telo['shit']]['maxdur']-2)<=$wearItems[$telo['shit']]['duration'] && $wearItems[$telo['shit']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>><?php
		if ($telo['shit'] > 0 && !$darkbs) {
			$dress = $wearItems[$telo['shit']];
			if (($dress['includemagicdex'] > 0) && ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=60 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,55,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа щите выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
				 else
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=60 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,55,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа щите выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/w10.gif" width=60 height=60 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,17,17,\'ѕустой слот щит\','.$r.')" >';
		}
	?></A></TD></TR>
	<TR><TD <?=(((($wearItems[$telo['boots']]['maxdur']-2)<=$wearItems[$telo['boots']]['duration'] && $wearItems[$telo['boots']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>><?php
		if ($telo['boots'] > 0 && !$darkbs) {
			$dress = $wearItems[$telo['boots']];
			if (($dress['includemagicdex'] > 0) && ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=40 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,35,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа ботинках выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
				 else
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=60 height=40 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,35,\'<b>'.$dress['name']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа ботинках выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/w12.gif" width=60 height=40 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,55,35,\'ѕустой слот обувь\','.$r.')" >';
		}



	?>
	</A></TD></TR>
	</TABLE>
	</TD></TR>
	</TABLE>
	<?
	}
	?>

<TABLE cellspacing=0 cellpadding=0 border=0 style="background-image: url('http://i.oldbk.com/i/runes_slots.jpg'); background-position: center bottom; background-repeat: no-repeat;"><tr>
		<td width=59 height=48 align=right  <?=(((($wearItems[$telo['runa1']]['maxdur']-2)<=$wearItems[$telo['runa1']]['duration'] && $wearItems[$telo['runa1']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>><?php
		if ($telo['runa1'] > 0 && !$darkbs)
		{
			$dress = $wearItems[$telo['runa1']];

			if (($dress['type'] == 30) and ($pas==true) ) // смотрим на свой предмет
				{
				// показываем  кто руну надо апнуть
					if ($dress['ups'] >= $dress['add_time'])
					{
					$mig=explode(".",$dress['img']);
					$dress['img']=$mig[0]."_up.".$mig[1];
					}
				}

			if (($dress['includemagicdex'] > 0) && ($pas==true))
			{
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=30 height=30 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][6].',17,\'<b>'.$dress['name']."</b><br>”ровень:<b>".$dress['up_level']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа руне выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
				 else
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=30 height=30 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][6].',17,\'<b>'.$dress['name']."</b><br>”ровень:<b>".$dress['up_level']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа руне выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/none.gif" width=30 height=30 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][6].',17,\'ѕустой слот руны\','.$r.')" >';
		}
	?></A></td>
		<td width=74 height=48 align=center <?=(((($wearItems[$telo['runa2']]['maxdur']-2)<=$wearItems[$telo['runa2']]['duration'] && $wearItems[$telo['runa2']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>><?php
		if ($telo['runa2'] > 0 && !$darkbs) {
			$dress = $wearItems[$telo['runa2']];

			if (($dress['type'] == 30) and ($pas==true) ) // смотрим на свой предмет
				{
				// показываем  кто руну надо апнуть
					if ($dress['ups'] >= $dress['add_time'])
					{
					$mig=explode(".",$dress['img']);
					$dress['img']=$mig[0]."_up.".$mig[1];
					}
				}


			if (($dress['includemagicdex'] > 0) && ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=30 height=30 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][6].',17,\'<b>'.$dress['name']."</b><br>”ровень:<b>".$dress['up_level']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа руне выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
				 else
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=30 height=30 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][6].',17,\'<b>'.$dress['name']."</b><br>”ровень:<b>".$dress['up_level']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа руне выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/none.gif" width=30 height=30 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][6].',17,\'ѕустой слот руны\','.$r.')" >';
		}
	?></A></td>
		<td width=57 height=48 align=left  <?=(((($wearItems[$telo['runa3']]['maxdur']-2)<=$wearItems[$telo['runa3']]['duration'] && $wearItems[$telo['runa3']]['duration'] > 2 && $pas==true )?" style='background-image:url(http://i.oldbk.com/i/blink.gif);' ":""))?>><?php
		if ($telo['runa3'] > 0 && !$darkbs) {
			$dress = $wearItems[$telo['runa3']];

			if (($dress['type'] == 30) and ($pas==true) ) // смотрим на свой предмет
				{
				// показываем  кто руну надо апнуть
					if ($dress['ups'] >= $dress['add_time'])
					{
					$mig=explode(".",$dress['img']);
					$dress['img']=$mig[0]."_up.".$mig[1];
					}
				}

			if (($dress['includemagicdex'] > 0) && ($pas==true)) {
				showMagicHref($dress, $magicItems[$dress['includemagic']]);
			} else {
				$ehtml=render_img_html($dress);
				 if ($pas==true)
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=30 height=30 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][6].',17,\'<b>'.$dress['name']."</b><br>”ровень:<b>".$dress['up_level']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']."<br>ƒействует на: ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['gsila']!=0)?"<br>Х —ила:{$dress['gsila']}":"")." ".(($dress['glovk']!=0)?"<br>Х Ћовкость:{$dress['glovk']}":"")." ".(($dress['ginta']!=0)?"<br>Х »нтуици€:{$dress['ginta']}":"")." ".(($dress['gintel']!=0)?"<br>Х »нтеллект:{$dress['gintel']}":"")." ".(($dress['mfkrit']!=0)?"<br>Х ћф. критических ударов:{$dress['mfkrit']}%":"")." ".(($dress['mfakrit']!=0)?"<br>Х ћф. против крит. ударов:{$dress['mfakrit']}%":"")." ".(($dress['mfuvorot']!=0)?"<br>Х ћф. увертливости:{$dress['mfuvorot']}%":"")." ".(($dress['mfauvorot']!=0)?"<br>Х ћф. против увертлив.:{$dress['mfauvorot']}%":"")." ".(($dress['gnoj']!=0)?"<br>Х ¬ладени€ ножами и кастетами:+{$dress['gnoj']}":"")." ".(($dress['gtopor']!=0)?"<br>Х ¬ладени€ топорами и секирами+{$dress['gtopor']}":"")." ".(($dress['gdubina']!=0)?"<br>Х ¬ладени€ дубинами, булавами:+{$dress['gdubina']}":"")." ".(($dress['gmech']!=0)?"<br>Х ¬ладени€ мечами:+{$dress['gmech']}":"")." ".(($dress['gfire']!=0)?"<br>Х ¬ладени€ стихи€ огн€:+{$dress['gfire']}":"")." ".(($dress['gwater']!=0)?"<br>Х ¬ладени€ стихи€ воды:+{$dress['gwater']}":"")." ".(($dress['gair']!=0)?"<br>Х ¬ладени€ стихи€ воздуха:+{$dress['gair']}":"")." ".(($dress['gearth']!=0)?"<br>Х ¬ладени€ стихи€ земли:+{$dress['gearth']}":"")." ".(($dress['glight']!=0)?"<br>Х ¬ладени€ ћаги€ —вета:+{$dress['glight']}":"")." ".(($dress['ggray']!=0)?"<br>Х ¬ладени€ —ера€ ћаги€:+{$dress['ggray']}":"")." ".(($dress['gdark']!=0)?"<br>Х ¬ладени€ ћаги€ “ьмы:+{$dress['gdark']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа руне выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
				 else
				 {
				 echo '<a href="http://oldbk.com/encicl/'.$ehtml.'" target="_blank"><img src="http://i.oldbk.com/i/sh/'.$dress['img'].'" width=30 height=30 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][6].',17,\'<b>'.$dress['name']."</b><br>”ровень:<b>".$dress['up_level']."</b><br>ѕрочность ".$dress['duration']."/".$dress['maxdur']." ".(($dress['ghp']>0)?"<br>”ровень жизни +{$dress['ghp']}":"")." ".(($dress['bron1']!=0)?"<br>Х Ѕрон€ головы:{$dress['bron1']}":"")." ".(($dress['bron2']!=0)?"<br>Х Ѕрон€ корпуса:{$dress['bron2']}":"")." ".(($dress['bron3']!=0)?"<br>Х Ѕрон€ по€са:{$dress['bron3']}":"")." ".(($dress['bron4']!=0)?"<br>Х Ѕрон€ ног:{$dress['bron4']}":"")." ".(($dress['text']!=null)?"<br>  Ќа руне выгравировано: {$dress['text']}":"").'\','.$r.')" >';
				 }
			}
		} else {
			echo '<img src="http://i.oldbk.com/i/none.gif" width=30 height=30 onMouseOut="HideThing(this)" onMouseOver="ShowThing(this,'.$x[$r][6].',17,\'ѕустой слот руны\','.$r.')" >';
		}
	?></A></td>

        </tr></table>
<?

   	echo "<TABLE cellPadding=0 cellSpacing=0 width=100%><TBODY><TR><TD colSpan=2 style=\"padding-left:25px;\">";
	echo "—ила: ".$telo['sila']."<BR>";
	echo "Ћовкость: ".$telo['lovk']."<BR>";
	echo "»нтуици€: ".$telo['inta']."<BR>";
	echo "¬ыносливость: ".$telo['vinos']."<BR>";
	if ($telo['level'] > 3) { echo "»нтеллект: ".$telo['intel']."<BR>"; }
	if ($telo['level'] > 6) { echo "ћудрость: ".$telo['mudra']."<BR>"; }
	if ($telo['uclass']>0)
		{
		echo " ласс: <b>{$nclass_name[$telo['uclass']]}</b><BR>";

		$sub_class='ac'; $sub_class_name='јнтикрит';

		$telo_AK=$wearItems['akrit_mf']+$telo['inta']*5+$telo['lovk'] * 2 ;
		$telo_UV=$wearItems['auvor_mf']+$telo['lovk']*5+$telo['inta'] * 2;

		if ($telo['id']==14897)
		{
		echo $telo_AK."/".$telo_UV;
		}

		$cln='user';
		if ($_SESSION['uid']==$telo['id']) {$cln='my';}

		if ($telo_AK<$telo_UV) {
				$sub_class='ad'; $sub_class_name='јнтиуворот';
		}
		echo '<div id="'.$cln.'-sub-class" data-sub="'.$sub_class.'">ѕодкласс: <b>'.$sub_class_name.'</b></div><BR>' ;
		}
 	echo "</td></tr></table>";

////////////////////
}



// расчет оружи€ + соответствующее мастерство
function load_wep($r_wep,$telo)
{
$tout= array();
// надо вернуть масив . шо за пушка
switch($r_wep['otdel'])
			 	{
			 	case 0:
			 		{
			 	//кулак
				$tout['chem']='kulak';
				$tout['mast']=0;
			 		}
				break;
				########

			 	case 1:
			 		{
			 	//ножи кастеты
				$tout['chem']='noj';
				$tout['mast']=$telo['noj'];
			 		}
				break;

			 	case 63:
			 		{
			 	//ножи кастеты
				$tout['chem']='tool';
				$tout['mast']=0;
			 		}
				break;

				########
		 		case 6:
			 		{
			 		if ( ($r_wep['type']==3) and   ($r_wep['prototype']>=55510301) and ($r_wep['prototype']<=55510401))
			 			{
						$tout['chem']='elka';
						$tout['mast']=$telo['noj'];
						   if ($tout['mast']<$telo['topor']) { $tout['mast']=$telo['topor'];}
						   if ($tout['mast']<$telo['dubina']) { $tout['mast']=$telo['dubina'];}
						   if ($tout['mast']<$telo['mec']) { $tout['mast']=$telo['mec'];}
			 			}
					else
					if ( ($r_wep['type']==3) and (($r_wep['prototype']==169) OR ($r_wep['prototype']==170) or ($r_wep['prototype']==600) or ($r_wep['prototype']==601)))
				 		{
						$tout['chem']='meshok';
						$tout['mast']=0;
						}
			 		else
			 		if ( ($r_wep['type']==33) and (($r_wep['prototype']==501) OR ($r_wep['prototype']==502) ))
				 		{
						$tout['chem']='kostil';
						$tout['mast']=0;
						}
					else
					if ( ($r_wep['type']==3) and (($r_wep['prototype']>=410130 and $r_wep['prototype']<=410135) || ($r_wep['prototype']>=410001 and $r_wep['prototype']<=410008) || ($r_wep['prototype']>=410021 and $r_wep['prototype']<=410028) ) )
			 			{
						$tout['chem']='buket';
						$tout['mast']=$telo['noj'];
						   if ($tout['mast']<$telo['topor']) { $tout['mast']=$telo['topor'];}
						   if ($tout['mast']<$telo['dubina']) { $tout['mast']=$telo['dubina'];}
						   if ($tout['mast']<$telo['mec']) { $tout['mast']=$telo['mec'];}
			 			}
					else
					if ( $r_wep['type']==34 && $r_wep['prototype']==410027 )
					{
					    $tout['chem']='buket';
					    $tout['mast']=$telo['noj'];
					    if ($tout['mast']<$telo['topor']) { $tout['mast']=$telo['topor'];}
					    if ($tout['mast']<$telo['dubina']) { $tout['mast']=$telo['dubina'];}
					    if ($tout['mast']<$telo['mec']) { $tout['mast']=$telo['mec'];}
					}
				 	else
				 		{
						$tout['chem']='buket';
						$tout['mast']=0;
				 		}
			 		}
				break;
				########
		 		case 11:
			 		{
			 	//топоры
				$tout['chem']='topor';
				$tout['mast']=$telo['topor'];
			 		}
				break;
				########
		 		case 12:
			 		{
			 	//дубина
				$tout['chem']='dubina';
				$tout['mast']=$telo['dubina'];

			 		}
				break;
				########
		 		case 13:
			 		{
			 	//мечь
				$tout['chem']='mec';
				$tout['mast']=$telo['mec'];

			 		}
				break;
		 		case 14:
			 		{
			 	//лук
				$tout['chem']='luk';
				$tout['mast']=0; // лук

			 		}
				break;


			 	}

	if($telo['id_user']==88)
	{
	//лошадь
	$tout['chem']='loshad';
	$tout['mast']=0;
	}

return $tout;
}

//////////////////////////////////////////////////////////////////
/// ¬ычисл€лки шансов и модификаторов удара по противнику



/////////////////////////////////////////////////////////////////
//в батле масив/чары тоже в масивах
////////формирователи текстовки логов - которые будут запрашивать механику  попадани€ просчет + урон




function do_attack_in($getbat,$telo,$vrag,$input_att,$def,$telo_items,$vrag_items,$telo_eff=null,$vrag_eff=null,$debug_info=null)
{
global $WEAP_ITYPE; //режим в котором находитс€ юзер

//echo "do_attack_in telo {$telo['login']} vrag {$vrag['login']}<br>";
$fin_pre_step= array();
$a_count=0;

if (!(is_array($input_att)))
	{
	//если не массив то делаем массив с одним элементом
	$tmp=$input_att;
	$input_att=array($tmp);
	}


foreach($input_att as $att)	// если в массиве два элемента знач два раза ударили из двух пушек
	{
$a_count++;
//echo "A_count:".$a_count."<br>";

$pre_step= array();
// ƒл€ сн€ти€ достаточно одного запроса
 if (!($vrag))
 	{
 	addchp ('<font color=red>Fsystem!</font> no vrag, info: '.$debug_info.'  ','{[]}Bred{[]}');
 	}

 if (!($telo))
 	{
 	addchp ('<font color=red>Fsystem!</font> no telo, info: '.$debug_info.'  ','{[]}Bred{[]}');
 	}

if ($att == 0 )
 {
 $type='block';
 $pre_step['dem']=0;
 $pre_step['text']='';
 }
 else
 {

// в.1.1
// получаение по шансу в зависимости от процента

// шанс моего уворота т.к. тут € защищаюсь атакует враг

	if (($telo_eff[721]) and ($telo_eff[721]['lastup']>0))
	{
        $uvorotme_p=0;
	$uvorotme = false;
	}
	else
	{
        $uvorotme_p=get_uvorot_chance($telo,$vrag,$telo_items,$vrag_items,$getbat,$telo_eff,$vrag_eff);
	$uvorotme = get_chanse($uvorotme_p);
	}
// щенс крита врага т.к. тут € защищаюсь атакует враг

	if (($vrag_eff[721]) and ($vrag_eff[721]['lastup']>0))
	{
	//лишает ∆≈–“¬” возможности  –»“јЌ”“№
	$krithe_p=0;
	$krithe = false;
	}
	else
	{
	$krithe_p=get_krit_chance($vrag,$telo,$vrag_items,$telo_items,$getbat,$vrag_eff,$telo_eff);

	if (($telo['id']==14897) OR ($vrag['id']==14897) )
		{
		addchp('<font color=red>¬нимание!</font> ƒебаг do_attack_in '.$vrag['login'].'  VS '.$telo['login'].' : krithe_p='.$krithe_p,'{[]}Bred{[]}',-1,0);
		}

	$krithe = get_chanse($krithe_p);

	if (($telo['id']==14897) OR ($vrag['id']==14897) )
		{
		addchp('<font color=red>¬нимание!</font> ƒебаг do_attack_in '.$vrag['login'].'  VS '.$telo['login'].' : krithe='.$krithe,'{[]}Bred{[]}',-1,0);
		}

	}

	// логирование процентов
	$pre_step['uvorotme_p']=$uvorotme_p;
	$pre_step['uvorotme_rez']=$uvorotme;

	$pre_step['krithe_p']=$krithe_p;
	$pre_step['krithe_rez']=$krithe;


////
if($uvorotme && !$krithe) { $uve = 1; }
else if ($krithe && !$uvorotme) { $uve = 2; }
  elseif($uvorotme && $krithe) 	{  $uve = 1; }
    else { $uve = 0; }
///////////////////////////////
//дл€ отмены уворота по авто удару
if (($def==0) and ($uve==1)) { $uve=0;}

$pre_step['uve']=$uve;


	if ($a_count==2)
	{
	$vrag_items_chem=$vrag_items['chem2'];
	}
	else
	{
	$vrag_items_chem=$vrag_items['chem'];
	}

//echo "<br>------------<br>";

//1. смотрим куда враг бил, попал ли в блок + отрабьотка модификаторов которы получили выше

 if (get_block($att,$def,$WEAP_ITYPE)==true)
  {

	  if($uve == 2)
	   {
	   // крит черезблок
	   $type='krita';
	   $pre_step['dem']=(int)(get_dem_udar_krita($vrag,$telo,$vrag_items,$telo_items,$att,$getbat,$vrag_eff,$telo_eff,$a_count)) ;
	   $pre_step['mstep']="to_block|krit";
	   }
	   else
	   {
	    if ($uvorotme)
	    	{
	    	  $type='uvorot';
		  $pre_step['dem']=0;
		  $pre_step['mstep']="to_block|uvorot";
	    	}
	    	else
	    	{
		   // не крит отработка блока
		   $type='block';
		   $pre_step['dem']=0;
		  $pre_step['mstep']="to_block|block";
		}
	   }


	if ( ($pre_step['dem']>0) and ($getbat['nomagic']==0) and ($telo['ruines']==0) )
	{
		//расчет Ќеукротима€ €рость негативна€ сторона  параметр ¬
		if (($telo_eff[440]) and ($telo_eff[440]['add_info']!=''))
		{
			$pr=explode(":",$telo_eff[440]['add_info']);
			$pr_a=$pr[1];
			$pr_b=$pr[2];
			$pre_step['dem']+=(int)($pre_step['dem']*$pr_b);
		}


		/// расчет каменной кожи дл€ всех ударов если не теле есть эфект 420
		if (($telo_eff[430]) and ($telo_eff[430]['add_info']>0))
		{
		$pre_step['stone']=get_stone($pre_step['dem'],$telo_eff[430]['add_info']);
		}
		elseif (($telo_eff[420]) and ($telo_eff[420]['add_info']>0))
		{
		$pre_step['stone']=get_stone($pre_step['dem'],$telo_eff[420]['add_info']);
		}

	}

  $pre_step['text']=get_text_razmen($vrag,$telo,$type,$vrag_items_chem,$att,$pre_step['dem'],$def,$pre_step['stone']);
 // $pre_step[stat]=nick_in_battle($vrag,$vrag['battle_t'])."|++|".nick_in_battle($telo,$telo['battle_t'])."|++|".$type."|++|".$pre_step['dem']."|++|".$att."|++|".$vrag_items['chem']."|++|".$def;

  }
   elseif ($uve == 1)
  {
  $type='uvorot'; //
  $pre_step['mstep']="no_block|uvorot";
  $pre_step['dem']=0;
  $pre_step['text']=get_text_razmen($vrag,$telo,$type,$vrag_items_chem,$att,$pre_step['dem'],$def);
  //$pre_step[stat]=nick_in_battle($vrag,$vrag['battle_t'])."|++|".nick_in_battle($telo,$telo['battle_t'])."|++|".$type."|++|".$pre_step['dem']."|++|".$att."|++|".$vrag_items['chem']."|++|".$def;
  }
  elseif ($uve == 2)
   {
  $type='krit';
  $pre_step['mstep']="no_block|krit";
  $pre_step['dem']=(int)(get_dem_udar_krit($vrag,$telo,$vrag_items,$telo_items,$att,$getbat,$vrag_eff,$telo_eff,$a_count));

  	if ( ($pre_step['dem']>0) and ($getbat['nomagic']==0) and ($telo['ruines']==0) )
	{
		//расчет Ќеукротима€ €рость негативна€ сторона  параметр ¬
		if (($telo_eff[440]) and ($telo_eff[440]['add_info']!=''))
		{
			$pr=explode(":",$telo_eff[440]['add_info']);
			$pr_a=$pr[1];
			$pr_b=$pr[2];
			$pre_step['dem']+=(int)($pre_step['dem']*$pr_b);
		}

		/// расчет каменной кожи дл€ всех ударов если не теле есть эфект 420
		if (($telo_eff[430]) and ($telo_eff[430]['add_info']>0))
		{
		$pre_step['stone']=get_stone($pre_step['dem'],$telo_eff[430]['add_info']);
		}elseif (($telo_eff[420]) and ($telo_eff[420]['add_info']>0))
		{
		$pre_step['stone']=get_stone($pre_step['dem'],$telo_eff[420]['add_info']);
		}
	}

  $pre_step['text']=get_text_razmen($vrag,$telo,$type,$vrag_items_chem,$att,$pre_step['dem'],$def,$pre_step['stone']);
 // $pre_step[stat]=nick_in_battle($vrag,$vrag['battle_t'])."|++|".nick_in_battle($telo,$telo['battle_t'])."|++|".$type."|++|".$pre_step['dem']."|++|".$att."|++|".$vrag_items['chem']."|++|".$def;
  }
  elseif (get_block($att,$def,$WEAP_ITYPE)==false)
  {
  // обычный удар
  // $pre_step['dem']=0;
  // били мен€ и попали
  // дл€ дебага
  $type='udar';
  $pre_step['mstep']="no_block|udar";
  $pre_step['dem']=(int)(get_dem_udar($vrag,$telo,$vrag_items,$telo_items,$att,$getbat,$vrag_eff,$telo_eff,$a_count)) ;

  	if ( ($pre_step['dem']>0) and ($getbat['nomagic']==0) and ($telo['ruines']==0) )
	{
		//расчет Ќеукротима€ €рость негативна€ сторона  параметр ¬
		if (($telo_eff[440]) and ($telo_eff[440]['add_info']!=''))
		{
			$pr=explode(":",$telo_eff[440]['add_info']);
			$pr_a=$pr[1];
			$pr_b=$pr[2];
			$pre_step['dem']+=(int)($pre_step['dem']*$pr_b);
		}
		/// расчет каменной кожи дл€ всех ударов если не теле есть эфект 420
		if (($telo_eff[430]) and ($telo_eff[430]['add_info']>0))
		{
		$pre_step['stone']=get_stone($pre_step['dem'],$telo_eff[430]['add_info']);
		}
		elseif (($telo_eff[420]) and ($telo_eff[420]['add_info']>0))
		{
		$pre_step['stone']=get_stone($pre_step['dem'],$telo_eff[420]['add_info']);
		}
	}

  $pre_step['text']=get_text_razmen($vrag,$telo,$type,$vrag_items_chem,$att,$pre_step['dem'],$def,$pre_step['stone']);
// $pre_step[stat]=nick_in_battle($vrag,$vrag['battle_t'])."|++|".nick_in_battle($telo,$telo['battle_t'])."|++|".$type."|++|".$pre_step['dem']."|++|".$att."|++|".$vrag_items['chem']."|++|".$def;
  }
   else
   {
     $pre_step['mstep']="no_block|error";
     $KK=4;
   }

$pre_step['type']=$type;
//$pre_step['text'].='(I)'.$KK.'-'.$pre_step['dem'].'<BR>';
}
///
// —н€тие данных ’иллера если он был тут сн€тие если было

$needd_sql='';
if ($telo['hiller'] >0 )
	{
	$needd_sql=" id='{$telo['id']}'  ";
	}
if ($vrag['hiller'] >0 )
	{
	  if ($needd_sql!='') {	$needd_sql.=" OR id='{$vrag['id']}'  "; } else { 	$needd_sql=" id='{$vrag['id']}'  "; }
	}
if ($needd_sql!='')
	{
	$needd="UPDATE users SET hiller=0, khiller=0 where ( {$needd_sql} ) AND battle='{$getbat['id']}' ;";
	//addchp ('<font color=red>¬нимание! тест сработки SQL</font> '.$needd.'  ','{[]}Bred{[]}');
	mysql_query($needd);
	}
//

	if ( ($pre_step['dem']>0) and ($getbat['nomagic']==0) and ($vrag['owner']>0) and ($vrag['id'] > _BOTSEPARATOR_) and ($vrag['skills']!='') )
	{
	//—кил атаки наемника
	/*
	 «еркальный удар
	Ц при размене с наемником (при его попадании) противник получает двойной удар, то есть если наемник ударил на -100, то абилка тоже кусает на -100
	*/
		$skill=unserialize($vrag['skills']);
		if ($skill['10001']['id']==10001) //"«еркальный удар";
		{

			$skrnd=mt_rand(1,100);
			if ($skrnd<=$skill[10001]['chance'])
			{

				$add_dem=round($pre_step['dem']*($skill[10001]['power']*0.01)); //мощь!!
				$pre_step['dem']+=$add_dem;

				$telohp=$telo['hp']-$pre_step['dem'];
				if ($telohp<0) $telohp=0;

				$pre_step['text'].="\n!:M:".time().':'.nick_new_in_battle($vrag).':'.($vrag['sex']+510).":10001:".nick_new_in_battle($telo).":<B>-".$add_dem."</B> [".$telohp."/".$telo['maxhp']."]\n";
				$pre_step['ok_skill']=10001;
				if (($telo['id']==188) OR ($vrag['id']==188) )
				{
				addchp('<font color=red>¬нимание!</font> ƒебаг do_attack_in '.$vrag['login'].'  VS '.$telo['login'].' : vskill=10001','{[]}Bred{[]}',-1,0);
				}

			}

		}


	}

	if (($telo['id']==14897) OR ($vrag['id']==14897) )
		{
		addchp('<font color=red>¬нимание!</font> ƒебаг do_attack_in '.$vrag['login'].'  VS '.$telo['login'].' : DMG='.$pre_step['dem'],'{[]}Bred{[]}',-1,0);
		}

	$from_fights=(($debug_info=='from_fights')?":1":"");


	$fin_pre_step['text'].=($a_count>1?$from_fights."\n":"").$pre_step['text'];

	if (isset($pre_step['ok_skill'])) { $fin_pre_step['ok_skill']=$pre_step['ok_skill'];}

	if (isset($pre_step['stone'])) { $fin_pre_step['stone']+=$pre_step['stone'];}



	$fin_pre_step['dem']+=$pre_step['dem'];

	$telo['hp']-=$pre_step['dem'];

	$fin_pre_step['type']=$pre_step['type']; //будет братьс€ последний тип удара

	//print_r($pre_step);

   }


return $fin_pre_step;
}

function do_attack_out($getbat,$telo,$vrag,$input_att,$def,$telo_items,$vrag_items,$telo_eff=null,$vrag_eff=null,$debug_info=null)
{
if (isset($vrag_items['WEAP_ITYPE']))
	{
	$EN_WEAP_ITYPE=$vrag_items['WEAP_ITYPE']; //режим в котором находитс€ враг
	}
	else
	{
	$EN_WEAP_ITYPE=0;
	}
//echo "do_attack_out<br>";
$fin_pre_step= array();
$a_count=0;
//print_r($input_att);

if (!(is_array($input_att)))
	{
	//echo "Ќе масив";
	//если не массив то делаем массив с одним элементом
	$tmp=$input_att;
	$input_att=array($tmp);
	}
	else
	{
	//echo "двуручка";
	}
//print_r($input_att);

foreach($input_att as $att)	// если в массиве два элемента знач два раза ударили из двух пушек
	{
$a_count++;
$pre_step= array();

 if (!($vrag))
 	{
 	addchp ('<font color=red>Fsystem!</font> no vrag, info: '.$debug_info.'  ','{[]}Bred{[]}');
 	}

 if (!($telo))
 	{
 	addchp ('<font color=red>Fsystem!</font> no telo, info: '.$debug_info.'  ','{[]}Bred{[]}');
 	}

if ($att == 0 )
 {
 $type='block';
 $pre_step['dem']=0;
 $pre_step['text']='';
 }
 else
 {
// в.1.1
// получаение по шансу в зависимости от процента
//шанс уворота врага т.к. тут атакую €

	if (($vrag_eff[721]) and ($vrag_eff[721]['lastup']>0))
	{
	$uvorotme_p=0;
	$uvorotme = false;
	}
	else
	{
	$uvorotme_p=get_uvorot_chance($vrag,$telo,$vrag_items,$telo_items,$getbat,$vrag_eff,$telo_eff);
	$uvorotme = get_chanse($uvorotme_p);
	}
// € атакую тут считаю мой крит

  	if (($telo_eff[721]) and ($telo_eff[721]['lastup']>0))
  	{
	$krithe_p=0;
	$krithe = false;
  	}
  	else
  	{
	$krithe_p=get_krit_chance($telo,$vrag,$telo_items,$vrag_items,$getbat,$telo_eff,$vrag_eff);

	if (($telo['id']==14897) OR ($vrag['id']==14897) )
		{
		addchp('<font color=red>¬нимание!</font> ƒебаг do_attack_out '.$telo['login'].'  VS '.$vrag['login'].' : krithe_p='.$krithe_p,'{[]}Bred{[]}',-1,0);
		}

	$krithe = get_chanse($krithe_p);

	if (($telo['id']==14897) OR ($vrag['id']==14897) )
		{
		addchp('<font color=red>¬нимание!</font> ƒебаг do_attack_out '.$telo['login'].'  VS '.$vrag['login'].' : krithe='.$krithe,'{[]}Bred{[]}',-1,0);
		}

	}

	// логирование процентов
	$pre_step['uvorotme_p']=$uvorotme_p;
	$pre_step['uvorotme_rez']=$uvorotme;

	$pre_step['krithe_p']=$krithe_p;
	$pre_step['krithe_rez']=$krithe;


////
if($uvorotme && !$krithe) { $uve = 1; }
else if ($krithe && !$uvorotme) { $uve = 2; }
  elseif($uvorotme && $krithe) 	{  $uve = 1; }
    else { $uve = 0; }
///////////////////////////////

$pre_step['uve']=$uve;

	if ($a_count==2)
	{
	$telo_items_chem=$telo_items['chem2'];
	}
	else
	{
	$telo_items_chem=$telo_items['chem'];
	}

//1. смотрим куда € бил попал ли € в блок

 if (get_block($att,$def,$EN_WEAP_ITYPE)==true)
  {
//  echo "//не попал"; € бил и не попал

	  if($uve == 2)
	  {
	  $type='krita';
  	  $pre_step['dem']=(int)(get_dem_udar_krita($telo,$vrag,$telo_items,$vrag_items,$att,$getbat,$telo_eff,$vrag_eff,$a_count)) ;
   	  $pre_step['mstep']="to_block|krit";
	  }
	  else
	  {
	  if ($uvorotme)
	    	{
	    	  $type='uvorot';
		  $pre_step['dem']=0;
		  $pre_step['mstep']="to_block|uvorot";
	    	}
	    	else
	    	{
		  $type='block';
		  $pre_step['dem']=0;
		  $pre_step['mstep']="to_block|block";
		 }
	  }

	if ( ($pre_step['dem']>0) and ($getbat['nomagic']==0) and ($telo['ruines']==0) )
	{
		//расчет Ќеукротима€ €рость негативна€ сторона  параметр ¬
		if (($vrag_eff[440]) and ($vrag_eff[440]['add_info']!=''))
		{
			$pr=explode(":",$vrag_eff[440]['add_info']);
			$pr_a=$pr[1];
			$pr_b=$pr[2];
			$pre_step['dem']+=(int)($pre_step['dem']*$pr_b);
		}

		/// расчет каменной кожи дл€ всех ударов если не врагу есть эфект 420
		if (($vrag_eff[430]) and ($vrag_eff[430]['add_info']>0))
		{
		$pre_step['stone']=get_stone($pre_step['dem'],$vrag_eff[430]['add_info']);
		}
		elseif (($vrag_eff[420]) and ($vrag_eff[420]['add_info']>0))
		{
		$pre_step['stone']=get_stone($pre_step['dem'],$vrag_eff[420]['add_info']);
		}
	}

 $pre_step['text']=get_text_razmen($telo,$vrag,$type,$telo_items_chem,$att,$pre_step['dem'],$def,$pre_step['stone']);
 //$pre_step[stat]=nick_in_battle($telo,$telo['battle_t'])."|++|".nick_in_battle($vrag,$vrag['battle_t'])."|++|".$type."|++|".$pre_step['dem']."|++|".$att."|++|".$telo_items['chem']."|++|".$def;
  }
  elseif ($uve == 1)
  {
  $type='uvorot'; //
  $pre_step['mstep']="no_block|uvorot";
  $pre_step['dem']=0;
  $pre_step['text']=get_text_razmen($telo,$vrag,$type,$telo_items_chem,$att,$pre_step['dem'],$def);
//  $pre_step[stat]=nick_in_battle($telo,$telo['battle_t'])."|++|".nick_in_battle($vrag,$vrag['battle_t'])."|++|".$type."|++|".$pre_step['dem']."|++|".$att."|++|".$telo_items['chem']."|++|".$def;
   }
   elseif ($uve == 2)
   {
  $type='krit';
  $pre_step['mstep']="no_block|krit";
  $pre_step['dem']=(int)(get_dem_udar_krit($telo,$vrag,$telo_items,$vrag_items,$att,$getbat,$telo_eff,$vrag_eff,$a_count)) ;

  	if ( ($pre_step['dem']>0) and ($getbat['nomagic']==0) and ($telo['ruines']==0) )
	{
		//расчет Ќеукротима€ €рость негативна€ сторона  параметр ¬
		if (($vrag_eff[440]) and ($vrag_eff[440]['add_info']!=''))
		{
			$pr=explode(":",$vrag_eff[440]['add_info']);
			$pr_a=$pr[1];
			$pr_b=$pr[2];
			$pre_step['dem']+=(int)($pre_step['dem']*$pr_b);
		}

		/// расчет каменной кожи дл€ всех ударов если не врагу есть эфект 420
		if (($vrag_eff[430]) and ($vrag_eff[430]['add_info']>0))
		{
		$pre_step['stone']=get_stone($pre_step['dem'],$vrag_eff[430]['add_info']);
		}
		elseif (($vrag_eff[420]) and ($vrag_eff[420]['add_info']>0))
		{
		$pre_step['stone']=get_stone($pre_step['dem'],$vrag_eff[420]['add_info']);
		}
	}

  $pre_step['text']=get_text_razmen($telo,$vrag,$type,$telo_items_chem,$att,$pre_step['dem'],$def,$pre_step['stone']);
//  $pre_step[stat]=nick_in_battle($telo,$telo['battle_t'])."|++|".nick_in_battle($vrag,$vrag['battle_t'])."|++|".$type."|++|".$pre_step['dem']."|++|".$att."|++|".$telo_items['chem']."|++|".$def;
  }
 elseif (get_block($att,$def,$EN_WEAP_ITYPE)==false)
  {
  # ƒебаг удар по -50 всем пока
 // $pre_step['dem']=50;
  // € бил и попал
   // дл€ дебага
  $pre_step['mstep']="no_block|udar";
  $type='udar';
  $pre_step['dem']=(int)(get_dem_udar($telo,$vrag,$telo_items,$vrag_items,$att,$getbat,$telo_eff,$vrag_eff,$a_count));


  	if ( ($pre_step['dem']>0) and ($getbat['nomagic']==0) and ($telo['ruines']==0) )
	{
		//расчет Ќеукротима€ €рость негативна€ сторона  параметр ¬
		if (($vrag_eff[440]) and ($vrag_eff[440]['add_info']!=''))
		{
			$pr=explode(":",$vrag_eff[440]['add_info']);
			$pr_a=$pr[1];
			$pr_b=$pr[2];
			$pre_step['dem']+=(int)($pre_step['dem']*$pr_b);
		}
		/// расчет каменной кожи дл€ всех ударов если не врагу есть эфект 420
		if (($vrag_eff[430]) and ($vrag_eff[430]['add_info']>0))
		{
		$pre_step['stone']=get_stone($pre_step['dem'],$vrag_eff[430]['add_info']);
		}
		elseif (($vrag_eff[420]) and ($vrag_eff[420]['add_info']>0))
		{
		$pre_step['stone']=get_stone($pre_step['dem'],$vrag_eff[420]['add_info']);
		}
	}


  $pre_step['text']=get_text_razmen($telo,$vrag,$type,$telo_items_chem,$att,$pre_step['dem'],$def,$pre_step['stone']);
//  $pre_step[stat]=nick_in_battle($telo,$telo['battle_t'])."|++|".nick_in_battle($vrag,$vrag['battle_t'])."|++|".$type."|++|".$pre_step['dem']."|++|".$att."|++|".$telo_items['chem']."|++|".$def;
  //echo "//да попал";
  }
  else
  {
  $pre_step['mstep']="no_block|error";
  }

$pre_step['type']=$type;
//$pre_step['text'].='(O)'.$pre_step['dem'].'<BR>';
}


	/*
	if ( ($pre_step['dem']>0) and ($getbat['nomagic']==0) and ($vrag['owner']>0) and ($vrag['id'] > _BOTSEPARATOR_) and ($vrag['skills']!='') )
	{
	//—кил атаки наемника

	// «еркальный удар
	//Ц при размене с наемником (при его попадании) противник получает двойной удар, то есть если наемник ударил на -100, то абилка тоже кусает на -100

		$skill=unserialize($vrag['skills']);
		if ($skill['10001']['id']==10001) //"«еркальный удар";
		{

			$skrnd=mt_rand(1,100);
			if ($skrnd<=$skill[10001]['chance'])
			{

				$add_dem=round($pre_step['dem']*($skill[10001]['power']*0.01)); //мощь!!
				$pre_step['dem']+=$add_dem;

				$telohp=$telo['hp']-$pre_step['dem'];
				if ($telohp<0) $telohp=0;

				$pre_step['text'].="\n!:M:".time().':'.nick_new_in_battle($vrag).':'.($vrag['sex']+510).":10001:".nick_new_in_battle($telo).":<B>-".$add_dem."</B> [".$telohp."/".$telo['maxhp']."]\n";
				$pre_step['ok_skill']=10001;

				if (($telo['id']==188) OR ($vrag['id']==188) )
				{
				addchp('<font color=red>¬нимание!</font> ƒебаг do_attack_out '.$vrag['login'].'  VS '.$telo['login'].' : vskill=10001','{[]}Bred{[]}',-1,0);
				}

			}

		}


	}
	*/

	$from_fights=(($debug_info=='from_fights')?":1":"");

	$fin_pre_step['text'].=($a_count>1?$from_fights."\n":"").$pre_step['text'];

	if (isset($pre_step['ok_skill'])) { $fin_pre_step['ok_skill']=$pre_step['ok_skill'];}

	if (isset($pre_step['stone'])) { $fin_pre_step['stone']+=$pre_step['stone'];}

	$fin_pre_step['dem']+=$pre_step['dem'];

	//fix локально снимаем урон с врага
	$vrag['hp']-=$pre_step['dem'];

	$fin_pre_step['type']=$pre_step['type']; //будет братьс€ последний тип удара

	//print_r($pre_step);

   }

return $fin_pre_step;
}

///////////текстовые модули дл€ формировани€ лога

function get_text_kuda($kuda,$type=0) // int
{
// куда били
   if ($type==88)
        {
        //1-тексты дл€ лошадей
         $udars = array(
	 '1' => array ('в нос','в глаз','в челюсть','по загривку','в морду','по зубам','по ушам','в шею'),
	 '2' => array ('в грудь','в корпус','по спине','в правый бок','в левый бок','в область лопаток','по животу'),
	 '3' => array ('по корпу','по левой €годице','под хвост','по правой €годице','по хвосту'),
	 '4' => array ('по ногам','в правое копыто','в левое копыто','по передней ноге','по задней ноге')
			);
        }
        else
        {
	$udars = array(
	'1' => array ('в нос','в глаз','в челюсть','по переносице','в кадык','по затылку','в правый глаз','в левый глаз','в скулу'),
	'2' => array ('в грудь','в корпус','в солнечное сплетение','в сердце','в бок','в область лопаток','по желудку','по левой руке','по правой руке'),
	'3' => array ('по <вырезано цензурой>','в пах','в промежность','по левой €годице','по правой €годице'),
	'4' => array ('по ногам','в область правой п€тки','в область левой п€тки','по коленной чашечке','по икрам')
		);
	}

$txt = $udars[$kuda][mt_rand(0,count($udars[$kuda])-1)];
if ($txt!='') { return $txt;} else { return "в непон€тное место";}

}

function get_new_text_kuda($kuda,$type=0) // int
{
// куда били
   if ($type==88)
        {
        //1-тексты дл€ лошадей
        $o=100+$kuda*10;
	if ($kuda==1) { $mr=9; }
	elseif ($kuda==2) { $mr=9; }
	elseif ($kuda==3) { $mr=5; }
	elseif ($kuda==4) { $mr=5; }
        $txt=$o+mt_rand(1,$mr);
        }
        else
        {
        $o=$kuda*10;
	if ($kuda==1) { $mr=8; }
	elseif ($kuda==2) { $mr=7; }
	elseif ($kuda==3) { $mr=5; }
	elseif ($kuda==4) { $mr=5; }
        $txt=$o+mt_rand(1,$mr);
	}


return $txt;
}

function get_text_hark($telo) // характер текстовка дл€ лога
{
if ($telo['sex']==1) {
						$hark = array('бесчувственный','расстроенный','храбрый','обезумевший','неустрашимый','злобный','жестокий','наглый',
										'разъ€ренный','продвинутый','хитрый','мужественный','','','','','','');
					}
					else {
						$hark = array('бесчувственна€','расстроенна€','храбра€','обезумевша€','неустрашима€','злобна€','жестока€','нагла€',
										'разъ€ренна€','продвинута€','хитра€','прекрасна€','','','','','','');
					}
$txt=$hark[mt_rand(0,count($hark)-1)];
return $txt;
}

function get_new_text_hark($telo)
{
$xxx=$telo['sex']*100;
$txt=$xxx+mt_rand(1,16);
return $txt;
}

function get_text_block($telo) // текстовка по блоку
{
if ($telo['sex']==1)	{
			$textblock = array (" заблокировал удар "," остановил удар "," отбил удар ");
			}
			else {
			$textblock = array (" заблокировала удар "," остановила удар "," отбила удар ");
			     }
$txt=$textblock[mt_rand(0,count($textblock)-1)];
return $txt;

}

function get_new_text_block($telo) // текстовка по блоку
{
$xxx=$telo['sex']*100;
$txt=$xxx+mt_rand(1,3);
return $txt;
}

function get_text_krit($telo) // текстовка дл€ грита
{
if ($telo['sex']==1) {
	$textkrit = array (", напугав всех, неслышно подойд€ сзади ударил по голове булыжником оппонента.",", сказав \"Ѕ”!\", ласково заломил руку за спину соперника.",", расслабившись, расцарапал нос соперника.",", показав сразу два пальца, наступил на ногу врага.",", напугав всех, укусил в нос противника.",", проклина€ этот сайт, провел ужасный бросок через пупок оппонента.");
		   }
		else {
			$textkrit = array (", напугав всех, неслышно подойд€ сзади ударила по голове булыжником оппонента.",", сказав \"Ѕ”!\", ласково заломила руку за спину соперника.",", расслабившись, расцарапала нос соперника.",", показав сразу два пальца, наступила на ногу врага.",", напугав всех, укусила в нос противника.",", проклина€ этот сайт, провела ужасный бросок через пупок оппонента.");
		     }
$txt=$textkrit[mt_rand(0,count($textkrit)-1)];
return $txt;
}

function get_new_text_krit($telo) // текстовка дл€ грита
{
$xxx=$telo['sex']*100;
$txt=$xxx+mt_rand(1,6);
return $txt;
}

function get_text_krita($telo) // текстовка по крит пробив блок
{
if ($telo['sex']==1) {
		$textkrita = array (", напугав всех, неслышно подойд€ сзади ударил, пробив блок, по голове булыжником оппонента.",", пробив блок, ласково заломил руку за спину соперника.",", пробив блок, расцарапал нос соперника.",", пробив блок, наступил на ногу врага.",", пробив блок, укусил в нос противника.",", пробив блок, провел ужасный бросок через пупок оппонента.");
		}
		else {
			$textkrita = array (", напугав всех, неслышно подойд€ сзади ударила, пробив блок, по голове булыжником оппонента.",",  пробив блок, ласково заломила руку за спину соперника.",", пробив блок, расцарапала нос соперника.",", пробив блок, наступила на ногу врага.",", пробив блок, укусила в нос противника.",", пробив блок, провела ужасный бросок через пупок оппонента.");
		}

$txt=$textkrita[mt_rand(0,count($textkrita)-1)];
return $txt;
}

function get_new_text_krita($telo) // текстовка по крит пробив блок
{
$xxx=$telo['sex']*100;
$txt=$xxx+mt_rand(1,6);
return $txt;
}

function get_text_uvorot($telo) // текстовка по увороту
{
if ($telo['sex']==1) {
		$textuvorot = array (" <font color=green><B>уклонилс€</B></font> от удара "," <font color=green><B>увернулс€</B></font> от удара "," <font color=green><B>отскочил</B></font> от удара ");
			}
		else {
		$textuvorot = array (" <font color=green><B>уклонилась</B></font> от удара "," <font color=green><B>увернулась</B></font> от удара "," <font color=green><B>отскочила</B></font> от удара ");
			}
$txt=$textuvorot[mt_rand(0,count($textuvorot)-1)];
return $txt;
}

function get_new_text_uvorot($telo) // текстовка по увороту
{
$xxx=$telo['sex']*100;
$txt=$xxx+mt_rand(1,4);
return $txt;
}

function get_text_udar($telo) // текстовка по простому удару
{
   if ($telo['sex']==1) {
   				$textudar = array(", разбежавшись, рубанул"," отча€нно проткнул "," нехот€ уколол "," не подумав, рубанул ",", улыба€сь, саданул укол "," приложил удар "," ударил "," сдуру вмазал ");
			}
		else {
			$textudar = array(", разбежавшись, рубанула"," отча€нно проткнула "," нехот€ уколола "," не подумав, рубанула ",", улыба€сь, саданула укол "," приложила удар "," ударила "," сдуру вмазала ");
			}
$txt=$textudar[mt_rand(0,count($textudar)-1)];
return $txt;
}

function get_new_text_udar($telo) // текстовка по простому удару
{
$xxx=$telo['sex']*100;
$txt=$xxx+mt_rand(1,8);
return $txt;
}

function get_text_fail($telo) // текст по промаху
{
if ($telo['sex']==1) {
			$textfail = array ( 'думал о <вырезано цензурой>, вследствие чего',
					'пыталс€ нанести удар, но ',
					'подскользнулс€, и',
					'старалс€ провести удар, но',
					'закашл€лс€, и',
					'пыталс€ провести удар, но',
					'потер€л самоконтроль, вследствие чего',
					'думал не о том, и');
                  } else {
		      $textfail = array ( 'думала о <вырезано цензурой>, вследствие чего',
					'пыталась нанести удар, но ',
					'подскользнулась, и',
					'старалась провести удар, но',
					'закашл€лась, и',
					'пыталась провести удар, но ',
					'потер€ла самоконтроль, вследствие чего',
					'думала не о том, и');
					}
$txt=$textfail[mt_rand(0,count($textfail)-1)];
return $txt;
}

function get_new_text_fail($telo) // текст по промаху
{
$xxx=$telo['sex']*100;
$txt=$xxx+mt_rand(1,8);
return $txt;
}

function get_text_ud($telo) // текстовка по попаданию
{
					if ($telo['sex']==1) {
						$textud = array ('забылс€, и тут',
							'замешкалс€, и за это',
							'растер€лс€, как вдруг',
							'ковыр€лс€ в зубах, и тут',
							'поперхнулс€, но вдруг',
							'пыталс€ что-то сказать но вдруг, неожиданно',
							'растер€лс€, как вдруг',
							'засмотрелс€ на <вырезано цензурой>, а в это врем€',
							'высморкалс€, и в это врем€',
							'думал не о том, и',
							'пришел в себ€, но в это врем€',
							'обернулс€, как внезапно');
					} else {
						$textud = array ('забылась, и тут',
							'замешкалась, и за это ',
							'растер€лась, как вдруг ',
							'ковыр€лась в зубах, и тут ',
							'поперхнулась, но вдруг ',
							'пыталась что-то сказать но вдруг, неожиданно',
							'растер€лась, как вдруг',
							'засмотрелась на <вырезано цензурой>, а в это врем€',
							'высморкалась, и в это врем€',
							'думала не о том, и',
							'пришла в себ€, но в это врем€ ',
							'обернулась, как внезапно');
					}
$txt=$textud[mt_rand(0,count($textud)-1)];
return $txt;
}

function get_new_text_ud($telo) // текстовка по попаданию
{
$xxx=$telo['sex']*100;
$txt=$xxx+mt_rand(1,12);
return $txt;
}

function get_text_wep($chem) //int
{

$textchem = array (
	"kulak" => array("грудью","ребром руки","лбом","кулаком","ногой","левой ногой","правой ногой","коленом"),
	"noj" => array("ножом","тыльной стороной лезви€ ножа","руко€тью ножа","лезвием ножа"),
	"dubina" => array("сучковатой палкой","поленом","т€желой дубиной","дубиной","руко€тью молота"),
	"topor" => array("секирой","топором","лезвием секиры","алебардой","т€желым держаком","длинной секирой"),
	"mec" => array("ножнами","гардой","мечом","лезвием меча","руко€тью меча","тупым лезвием","острой стороной меча","огромным мечом"),
	"buket" => array("охапкой цветов","веником","букетом","колючками","снопом","стеблем","листь€ми","бутоном"),
	"luk" => array ("стрелой"),
	"meshok" => array("мешком"),
	"kostil" => array("костылем"),
	"loshad" => array("копытом","хвостом","корпусом","задней ногой","передней ногой","задними копытами","челюст€ми","загривком"),
	"tool" => array("т€желым инструментом","руко€тью инструмента","торцом инструмента","основанием инструмента","острым углом инструмента","поцарапанным инструментом","личным инструментом","инструментом"),
	"elka" => array("т€желым стволом","макушкой елки","обрубком ствола","еловой веткой","стволом елки","большой елью","зеленым стволом","пушистой елью"),
		);
$textchem = $textchem[$chem];
$txt=$textchem[mt_rand(0,count($textchem)-1)];

if ($txt!='') { return $txt; } else { return "рукой"; }
}


function get_new_text_wep($chem) //int
{

$textchem = array (
	"kulak" => array("1","2","3","4","5","6","7","8"),
	"noj" => array("11","12","13","14"),
	"dubina" => array("21","22","23","24","25"),
	"topor" => array("31","32","33","34","35","36"),
	"mec" => array("41","42","43","44","45","46","47","48"),
	"buket" => array("51","52","53","54","55","56","57","58"),
	"luk" => array ("61"),
	"meshok" => array("71"),
	"loshad" => array("81","82","83","84","85","86","87","88"),
	"kostil" => array("101","102","103"),
	"elka" => array("91","92","93","94","95","96","97","98"),
	"tool" => array("104","105","106","107","108","109","110","111"),
		);
$textchem = $textchem[$chem];
$txt=$textchem[mt_rand(0,count($textchem)-1)];

if ($txt!='') { return $txt; } else { return "1"; }
}

//// гл€вный текстовый модуль - собиратель дл€ типа удара
function get_text_razmen($kto,$pokomy,$type,$chem,$kuda,$uron,$def,$stone='')
{
if (strpos($pokomy['login'],"Ќевидимка (клон" ) !== FALSE ) { $pokomy['hidden']=1; $pokomy['sex']=1;  } //fix for hiddenn clons

if (($kto['hidden'] > 0) and ($kto['hiddenlog'] =='') )   { $kto['sex']=1; $K_hnik='Ќевидимка'; }
if (($kto['hidden'] > 0) and ($kto['hiddenlog'] !='') )    {       $kfake=explode(",",$kto['hiddenlog']);
											$kto['sex'] = $kfake[4];
											$K_hnik=$kfake[1];
										 }

if (($pokomy['hidden'] > 0) and ($pokomy['hiddenlog'] ==''))   { $pokomy['sex']=1;  $P_hnik='Ќевидимка';  }
if (($pokomy['hidden'] > 0) and ($pokomy['hiddenlog'] !='') )
										{       $pfake=explode(",",$pokomy['hiddenlog']);
											$pokomy['sex'] = $pfake[4];
											$P_hnik=$kfake[1];
										 }

////////
switch ($type) {
		// уворот
		case "uvorot":
		return '!:U:'.time().':'.nick_new_in_battle($kto).":".get_new_text_fail($kto).":".get_new_text_hark($pokomy).":".nick_new_in_battle($pokomy).":".get_new_text_uvorot($pokomy).":".get_new_text_wep($chem).":".get_new_text_kuda($kuda,$pokomy['id_user']).":".$def.":";
		break;

		//блок
		case "block":
		return '!:B:'.time().':'.nick_new_in_battle($kto).":".get_new_text_fail($kto).":".get_new_text_hark($pokomy).":".nick_new_in_battle($pokomy).":".get_new_text_block($pokomy).":".get_new_text_wep($chem).":".get_new_text_kuda($kuda,$pokomy['id_user']).":".$def.":";
		break;

		//крит
		case "krit":
		// подготовим хп
			if ($stone!='')
			{
			//если есть поглащение то считаем с ним , но в лог по “« попадает весь урон ,а в скобках хп уже с  поправкой на поглащение
				if (($uron-$stone)<$pokomy['hp']) {$pokomy['hp']-=($uron-$stone);}else {$pokomy['hp']=0;}
			}
			else
			{
				if ($uron<$pokomy['hp']) {$pokomy['hp']-=$uron;}else {$pokomy['hp']=0;}
			}

		$uron_str=$uron;
		//hidden приготовление
		if (($pokomy['hidden'] > 0) and ($pokomy['hiddenlog'] ==''))
 	               {   $txtdm='[??/??]';  $uron_str=$uron."|??|".$stone;   } else  {  $txtdm='['.$pokomy['hp'].'/'.$pokomy['maxhp'].']'; $uron_str=$uron."||".$stone;   }



		return '!:K:'.time().':'.nick_new_in_battle($kto).":".get_new_text_ud($pokomy).":".get_new_text_hark($kto).":".nick_new_in_battle($pokomy).":".get_new_text_krit($kto).":".get_new_text_wep($chem).":".get_new_text_kuda($kuda,$pokomy['id_user']).":".$def.":".$uron_str.":".$txtdm;
		break;

		//крит пробив
		case "krita":
		// подготовим хп
			if ($stone!='')
			{
			//если есть поглащение то считаем с ним , но в лог по “« попадает весь урон ,а в скобках хп уже с  поправкой на поглащение
				if (($uron-$stone)<$pokomy['hp']) {$pokomy['hp']-=($uron-$stone);}else {$pokomy['hp']=0;}
			}
			else
			{
				if ($uron<$pokomy['hp']) {$pokomy['hp']-=$uron;}else {$pokomy['hp']=0;}
			}

		$uron_str=$uron;
		//hidden приготовление
		if (($pokomy['hidden'] > 0) and ($pokomy['hiddenlog'] ==''))
                { $txtdm='[??/??]'; $uron_str=$uron."|??|".$stone; } else { $txtdm='['.$pokomy['hp'].'/'.$pokomy['maxhp'].']'; $uron_str=$uron."||".$stone;   }

		return '!:P:'.time().':'.nick_new_in_battle($kto).":".get_new_text_ud($pokomy).":".get_new_text_hark($kto).":".nick_new_in_battle($pokomy).":".get_new_text_krita($kto).":".get_new_text_wep($chem).":".get_new_text_kuda($kuda,$pokomy['id_user']).":".$def.":".$uron_str.":".$txtdm;
		break;

		// попадание
		case "udar":
		// подготовим хп
			if ($stone!='')
			{
			//если есть поглащение то считаем с ним , но в лог по “« попадает весь урон ,а в скобках хп уже с  поправкой на поглащение
				if (($uron-$stone)<$pokomy['hp']) {$pokomy['hp']-=($uron-$stone);}else {$pokomy['hp']=0;}
			}
			else
			{
				if ($uron<$pokomy['hp']) {$pokomy['hp']-=$uron;}else {$pokomy['hp']=0;}
			}
		$uron_str=$uron;
		//hidden приготовление
		if (($pokomy['hidden'] > 0) and ($pokomy['hiddenlog'] ==''))
                { $txtdm='[??/??]';  $uron_str=$uron."|??|".$stone;  } else { $txtdm='['.$pokomy['hp'].'/'.$pokomy['maxhp'].']'; $uron_str=$uron."||".$stone;   }

		return '!:R:'.time().':'.nick_new_in_battle($kto).":".get_new_text_ud($pokomy).":".get_new_text_hark($kto).":".nick_new_in_battle($pokomy).":".get_new_text_udar($kto).":".get_new_text_wep($chem).":".get_new_text_kuda($kuda,$pokomy['id_user']).":".$def.":".$uron_str.":".$txtdm;
		break;
		}
////////////////////////////////////





}

function get_text_travm($getbat) //NEW
{
$eff=mysql_query("select u.id,u.login,u.sex, u.battle_t ,u.hidden,u.hiddenlog, e.name from users as u LEFT JOIN effects as e ON u.id=e.owner where `type` in (11,12,13,14) and e.battle={$getbat['id']} ");
$out='';
$c=0;
while ($rowit = mysql_fetch_array($eff))
	{
	$c++;
	if ($c==1) { $out="!:T:".time()."\n"; }
	$K_hnik='';

	 if (($rowit['hidden'] > 0) and ($rowit['hiddenlog'] =='') )    { $rowit['sex']=1; $K_hnik='Ќевидимка'; }
	 elseif (($rowit['hidden'] > 0) and ($rowit['hiddenlog'] !='') )    {  $kfake=explode(",",$rowit['hiddenlog']); $rowit['sex'] = $kfake[4];	$K_hnik=$kfake[1]; }
		$out.="!:T:".time().':'.$rowit['login']."|".$rowit['battle_t']."|".$K_hnik.":".$rowit['sex'].":".$rowit['name']."\n";
	}
if ($out=='') {return false; } else { return $out; }
}


function get_text_broken($getbat)
{
$itm=mysql_query("select  i.name, i.duration , i.maxdur, i.dressed , u.id , u.login, u.sex , u.battle_t, u.hidden, u.hiddenlog  from oldbk.inventory as i LEFT JOIN users u ON u.id=i.owner where i.battle={$getbat['id']}  and i.duration+2 >= i.maxdur;");
$out='';
$naem_cache=array();
while ($rowit = mysql_fetch_array($itm))
	{
	$K_hnik='';
	 if (($rowit['hidden'] > 0) and ($rowit['hiddenlog'] =='') )    { $rowit['sex']=1; $K_hnik='Ќевидимка'; }
	 elseif (($rowit['hidden'] > 0) and ($rowit['hiddenlog'] !='') )    {  $kfake=explode(",",$rowit['hiddenlog']); $rowit['sex'] = $kfake[4];	$K_hnik=$kfake[1]; }

	if ($rowit['dressed']==2)
			{
			//шмотка надета на наема

				if (!(isset($naem_cache[$rowit['id']]) ))
					{
					//в кеше наема нет
					$gtnaem=mysql_fetch_array(mysql_query("select * from users_clons where owner='{$rowit['id']}' and last_battle='{$getbat['id']}' limit 1;"));
					if ($gtnaem['id']>0)
							{
							$naem_cache[$rowit['id']]=$gtnaem;
							$rowit['login']=$gtnaem['login'];
							$rowit['sex']=$gtnaem['sex'];
							$K_hnik='';
							}
					}
					else
					{
							$rowit['login']=$naem_cache[$rowit['id']]['login'];
							$rowit['sex']=$naem_cache[$rowit['id']]['sex'];
							$K_hnik='';
					}
			}

	if ($rowit['duration'] >= $rowit['maxdur'])
		{
		$out.="!:Q:".time().":".$rowit['login']."|".$rowit['battle_t']."|".$K_hnik.":".$rowit['name'].":1\n";
		}
		else if (($rowit['maxdur']-$rowit['duration'])==2)
		{
		$out.="!:Q:".time().":".$rowit['login']."|".$rowit['battle_t']."|".$K_hnik.":".$rowit['name'].":2\n";
		}
		else
		{
		$out.="!:Q:".time().":".$rowit['login']."|".$rowit['battle_t']."|".$K_hnik.":".$rowit['name'].":3\n";
		}

	}
if ($out=='') {return false; } else { $out.="!:Q:".time()."\n"; return $out; }
}
///////// комментатор

function get_comment () // комментатор обыычный NEW
{
//!:C:1362499410:12
$out="!:C:".time().":".mt_rand(1,123);
return $out;
}

function get_comment_fifa () // комментатор футбольный NEW
{
//!:C:1362499410:228
$out="!:C:".time().":".mt_rand(201,228);
return $out;
}


// вычисл€лка опыта+начисление
function solve_exp ($battle,$telo,$vrag,$telo_items_cost,$vrag_items_cost,$damage,$aura_ids=null,$BSTAT=3,$mag_damage=0) { // в $damage = общий урон  в $mag_damage - только маг. урон
global  $boec_t1,  $boec_t2,  $boec_t3, $data_battle;

$dflag=0;

	if ($battle['type'] == 11 || $battle['type'] == 12 || $battle['type'] == 1010) { $vrag['level']=$telo['level']; }

				    $damage = round($damage); // Ќанесенный урон
				    $baseexp = array(
									"0" => "5",
									"1" => "10",
									"2" => "20",
									"3" => "30",
									"4" => "60",
									"5" => "120",
									"6" => "180",
									"7" => "300",
									"8" => "450",
									"9" => "600",
									"10" => "1200",
									"11" => "2400",
									"12" => "4800",
									"13" => "6800",
									"14" => "4800",
									"15" => "4800",
									"16" => "4800",
									"17" => "4800",
									"18" => "4800",
									"19" => "4800",
									"20" => "4800",
									"21" => "4800",
							); // ћассив базового опыта
                    // модификаторы опыта
		    // 100% опыта
                    $expmf = 1;
                    // 200% опыта
                    //$expmf = 2;
                    if($telo['align']==4) {
                    	$expmf = $expmf/2;
                    }
                    if((int)$telo['align'] == 1 && $vrag['align'] == 3) {
                        $expmf = $expmf*1.5;
                    }
                    if((int)$vrag['align'] == 1 && $telo['align'] == 3) {
                        $expmf = $expmf*1.5;
                    }
                     if($telo['level'] > 3 && $telo_items_cost==0 && $vrag_items_cost==0) {
                        $expmf = $expmf/2;
                    }
					if($telo['level'] < 4)
					{
						$expmf = $expmf * 2;
					}
                    if (($battle['blood']>0) or ($battle['coment']=='<b> уча-мала</b>') OR ($data_battle['type'] == 313)  )
                    {
                        $expmf = $expmf*2;
                    }

				// 200% опыта  кроме лабы
			    if($battle['type']!=30)
			    {
			    if  ($battle['CHAOS']>0)
			    	{
			      	$expmf = $expmf*1.3;
			      	}
			      	else if ($battle['type']==2)
			      	{
			      	$expmf = $expmf*1.5;
			      	}
			    }
			    else
			    {
		      		$expmf = $expmf*0.8;
			    }

					// 200% опыта Weathered
					//$expmf = $expmf*2;

                    if($telo['sergi']>0 && $telo['kulon']>0 && $telo['perchi']>0 && $telo['weap']>0 && $telo['bron']>0 && $telo['r1']>0 && $telo['r2']>0 && $telo['r3']>0 && $telo['helm']>0 && $telo['shit']>0 && $telo['boots']>0)
                    {
                     // I'm in full
                     $mnoj = '1.5'; // *1.5 if i'm in full and my opp isn't
                     if($vrag['sergi']>0 && $vrag['kulon']>0 && $vrag['perchi']>0 && $vrag['weap']>0 && $vrag['bron']>0 && $vrag['r1']>0 && $vrag['r2']>0 && $vrag['r3']>0 && $vrag['helm']>0 && $vrag['shit']>0 && $vrag['boots']>0) {
                       $mnoj = '2'; // *2 if i'm in full and my opp too..
                     }
                     if(($vrag['id_user']==102) OR ($vrag['id_user']==302))
                     {
                     	$mnoj = '2';
                     }
                    }

                    if($mnoj>0) {$expmf = $expmf*$mnoj; $mnoj = 0;}

                    $standart = array(
                    				"0" => 1,
                    				"1" => 1,
                    				"2" => 15,
                    				"3" => 111,
                    				"4" => 265,
                    				"5" => 526,
                    				"6" => 882,
                    				"7" => 919,
                    				"8" => 919,
                    				"9" => 919,
									"10" => 919,
									"11" => 919,
									"12" => 919,
									"13" => 919,
									"14" => 919,
									"15" => 919,
									"16" => 919,
									"17" => 919,
									"18" => 919,
									"19" => 919,
									"20" => 919,
									"21" => 919
                    );

                    $mfit = ($telo_items_cost/($standart[$telo['level']]/3));
                    if ($mfit < 0.8) { $mfit = 0.8; }
                    if ($mfit > 1.5) { $mfit = 1.5; }

					// повышаем опыт если цена одетых шмоток выше 2000 (т.е. мфнутые)
					if($telo_items_cost>2000) {
						$expmf = round($expmf+($telo_items_cost/6000),2);
					}



          // поправить позже опыт за клонов? хз что это за строка.

if ($telo_items_cost < 1 ) {$telo_items_cost=1;}
if ($vrag_items_cost < 1 ) {$vrag_items_cost=1;}



	   	if ($vrag['id_user']==9)
		  {
		  //тыква
		  $vrag['maxhp']=7000;
		  }
		elseif( (($vrag['id_user']>=101) AND ($vrag['id_user']<=110)) and ($vrag['bot_online']==2) )
	   	{
		/*
		костыль дл€ »’
		103 => 0, //»счадье for 6 lvl 20*180
		104 => 0, //»счадье  for 7 lvl	 20*180
		105 => 0, //»счадье  for 8 lvl	 80*180
		106 => 0, //»счадье  for 9 lvl 120*180
		107 => 0, //»счадье  for 10 lvl 300*180
		108 => 0,  //»счадье for 11 lvl 1600*360
		109 => 0,  //»счадье for 12 lvl 800*360
		110 => 0,  //»счадье for 13 lvl 266*360
		101 => 0,  //»счадье for 14 lvl 100*720
		*/
		$ih_old_hp[103]=5180;
		$ih_old_hp[104]=6180;
		$ih_old_hp[105]=8100;
		$ih_old_hp[106]=9180;
		$ih_old_hp[107]=10518;
		$ih_old_hp[108]=12180;
		$ih_old_hp[109]=13000;
		$ih_old_hp[110]=13000;
		$ih_old_hp[101]=13000;
		//дл€ расчета опыта подставл€ем старые значени€ »’
		$vrag['maxhp']=$ih_old_hp[$vrag['id_user']];
		}elseif( (($vrag['id_user']>=42) AND ($vrag['id_user']<=65)) )
		{
		/*  остыль дл€ драконов*/
		$ih_old_hp[42]=350;
		$ih_old_hp[43]=400;
		$ih_old_hp[44]=300;
		$ih_old_hp[45]=600;
		$ih_old_hp[46]=700;
		$ih_old_hp[47]=800;
		$ih_old_hp[48]=850;
		$ih_old_hp[49]=875;
		$ih_old_hp[50]=900;
		$ih_old_hp[51]=1000;
		$ih_old_hp[52]=1100;
		$ih_old_hp[53]=1200;
		$ih_old_hp[54]=1400;
		$ih_old_hp[55]=1600;
		$ih_old_hp[56]=2100;
		$ih_old_hp[57]=2500;
		$ih_old_hp[58]=3000;
		$ih_old_hp[59]=3500;
		$ih_old_hp[60]=777;
		$ih_old_hp[61]=3500;
		$ih_old_hp[62]=4000;
		$ih_old_hp[63]=3500;
		$ih_old_hp[64]=3500;
		$ih_old_hp[65]=3500;
		$vrag['maxhp']=$ih_old_hp[$vrag['id_user']];
		}







          $ret = round((($baseexp[$vrag['level']])*($vrag_items_cost/(($telo_items_cost+$vrag_items_cost)/2))*($damage/$vrag['maxhp'])*$expmf*$mfit),1);





// в базу
if ( $damage > 0)
{
if (($telo['id'] > _BOTSEPARATOR_) and ($telo['owner'] ==0))
  {
  $damage=0; // урон клона не суммируем хоз€ину
  $ret=$ret*0.3; //30%
  $telo['id']=$telo['id_user']; // подмен€ем ид дл€ добавлени€ хоз€ину клона

  }



		  //результирующий опыт в лабе у меньшить до 20%
		   if($battle['type']==30)
		   		{
  		      		$ret = round($ret*0.2); // 22 12 2010 получаем только 20%
  		      		}

		//опыт уменьшить на 20% в бо€х ристалища против монстров
		if (($battle['type']>=240) and ($battle['type']<269))
				{
				//—ерег пореж опыт в растишках на 10% всем и на 15% 11ым уровн€м
				if ($telo['level']>=11)
					{
					$ret = round($ret*0.7);
					}
					else
					{
					$ret = round($ret*0.75);
					}
				}

		      // лабовские ид из второй лабы
		      // покому били это лабовский бот из второй лабы
		      //результирующий опыт в лабе у меньшить до 80%
		      if  (($vrag['id_user'] > 209 ) and ($vrag['id_user'] < 230 ) and ($vrag['bot_online']==0 ) )
      			{
      			$ret = round($ret*0.8); // 11 04 2011 - уменьшаем до 8%
	      		}

   if (   (($telo['id'] > 29 ) and ($telo['id'] < 120 ) ) OR
          (($telo['id'] > 189 ) and ($telo['id'] < 310 ) )  OR //       (($telo['id'] > 300 ) and ($telo['id'] < 310 ) )  OR
          (($telo['id'] > 500 ) and ($telo['id'] < 600 ) ) OR ($telo['id'] == 12) OR ($telo['id'] == 0) OR ($telo['bot'] > 0) )
   {
   // лабовские ид
   //не записываем
   }
   else
   {

 	//addchp ('<font color=red>Fsystem!</font> EXP info/  Telo-'.$telo['login'].' Vrag-'.$vrag['login'].' :D:'.$damage.' E:'.$ret.'  ','{[]}Bred{[]}');

   $ret=round($ret*$telo['expbonus']);

   if ($telo['hiller']>0)
   	{
   	$komu=$telo['hiller'];
   	$ret=$ret*(0.01*$telo['khiller']);
   	}
   	else
   	{
   	$komu=$telo['id'];
   	}


	if ($battle['type'] == 15) $ret = 0;

	if (($vrag['id_user']==395467) OR ($vrag['id']==395467))
		{
		//завышаем опыт против мефестофил€
		$ret=$ret*10;
		}

	if(time()>mktime(0,0,0,12,11,2018) && time()<mktime(23,59,59,12,12,2018))
	{
	$ret=$ret*2;
	}

	   $_dcount = 1;
	   $_magic_effect_count = mysql_fetch_assoc(mysql_query(sprintf('select count(*) as cnt from effects where type in (930, 130, 150) and owner = %d', $telo['id'])));
	   if(isset($_magic_effect_count['cnt']) && $_magic_effect_count['cnt']) {
		   $_dcount = 2;
	   }
	   $mag_damage=(int)$mag_damage;


	   if (($telo['id'] > _BOTSEPARATOR_) and ($telo['owner']>0))
	   {
	   //опыт наемнику = база ставитс€ в момент по€влени€ и больше не добавл€етс€
	   $ret=0;
	   }



	 if ($vrag['bot']==3)
	 {

		if (($vrag['level']-$telo['level'])=="-1") { $ret=round($ret*0.7); $dflag=1;}
		 elseif (($vrag['level']-$telo['level'])=="-2") {$ret=round($ret*0.5); $dflag=2;}
		  elseif (($vrag['level']-$telo['level'])=="-3") {$ret=round($ret*0.3); $dflag=2;}
		   elseif (($vrag['level']-$telo['level'])=="-4") {$ret=round($ret*0.1); $dflag=2;}
		    elseif (($vrag['level']-$telo['level'])=="-5") {$ret=round($ret*0.1); $dflag=2; }
		     elseif (($vrag['level']-$telo['level'])<"-5") {$ret=0; $dflag=2;}

		/* если разница в уровн€х -1, то коэф. 0.7
		 если разница в уровн€х -2, то коэф. 0.5
		 если разница в уровн€х -3, то коэф. 0.3
		 если разница в уровн€х -4, то коэф. 0.1
		 если разница в уровн€х -5, то коэф. 0
		 если разница в уровн€х -6, то коэф. 0
		 если разница в уровн€х -7, то коэф. 0
		 */
		if (($telo['id']==14897) OR ($vrag['id']==14897) )
		{
		addchp('<font color=red>¬нимание!</font> ƒебаг solve_exp '.$vrag['login'].'  VS '.$telo['login'].' : damage='.$damage.' RET:'.$ret.'  R:'.$r,'{[]}Bred{[]}',-1,0);
		}
	  }


	   mysql_query('INSERT `battle_dam_exp` (`battle`,`owner`,`damage`,`mag_damage`,`exp`,`dcount`,`dflag`) values (\''.$battle['id'].'\',\''.$komu.'\',\''.$damage.'\',\''.$mag_damage.'\',\''.$ret.'\',\''.$_dcount.'\',\''.$dflag.'\' ) ON DUPLICATE KEY UPDATE `damage` =`damage`+'.$damage.' , `mag_damage` =`mag_damage`+'.(int)$mag_damage.' , `exp` =`exp`+'.$ret.', `dcount` = `dcount`+'.$_dcount.' , `dflag`='.$dflag.'  ;');



	if (is_array($aura_ids))
			{
				foreach($aura_ids as $k=>$v)
					{
					if ($battle['type']==7) // елочный бой накопление урона елки и выход ботов только в елочных бо€х
						{
						$ind=explode('|',$v);
						$totaldm=$ind[1]+$damage;
						$adddm=$damage;
						$mk_bot=false;


							if (((($ind[0] >= 55510301) AND ($ind[0] <= 55510311)) || (($ind[0] >= 55510328) AND ($ind[0] <= 55510333))) and ($totaldm>=50000)  )
								{
								// кредовые   редова€ елка копит 50000 урона
								$mk_bot=true;
								$dmlim=50000;
								}
								else if (($ind[0]==55510350) and ($totaldm>=35000) )
								{
								//екрова€ , екрова€ 35000 урона,
								$mk_bot=true;
								$dmlim=35000;
								}
								else if (($ind[0]==55510351) and ($totaldm>=25000) )
								{
								//артова€  артова€ 25000.
								$mk_bot=true;
								$dmlim=25000;
								}
								else if (($ind[0]==55510352) and ($totaldm>=20000) )
								{
								//артова€ нова€ 20000.
								$mk_bot=true;
								$dmlim=20000;
								}


							if (($mk_bot==true) and ($BSTAT==3) ) //выпустить бота и бой идет
							{

									//обнул€ем подсчет урона
									mysql_query("UPDATE `oldbk`.`inventory` SET `up_level`=0  WHERE `id`='{$k}' and up_level>='{$dmlim}'   ");
									if (mysql_affected_rows()>0)
										{
										//выпускаем бота
										if (mk_elka_bot($telo))
											{
											$mk_bot=false;	 // ошибка бот не сделалс€
											}
										}
										else
										{
										$mk_bot=false;	 // ошибка бот не сделалс€
										}

							}

							if ($mk_bot==false)
							{
								//просто складываем урон
								mysql_query("UPDATE `oldbk`.`inventory` SET `up_level`=`up_level`+'{$adddm}'  WHERE `id`='{$k}' ");
							}

						}

					}
			}




    // омандный бой в турнире
    if (($battle['type'] > 240 ) AND ($battle['type'] < 269 ))
    	{
    	/// суммируем коммандный урон
    	// mysql_query("UPDATE tur_grup SET demag=demag+{$damage} WHERE battle={$battle['id']} ;");
    	 mysql_query("UPDATE tur_stat SET demag=demag+{$damage} WHERE battle={$battle['id']} ;");
    	}
    elseif (($battle['type']==304) OR ( $battle['type']==308))
    	{
    	//суммируем урон в новых отр€дах 2
    		 if ($telo['battle_t']==1)
    		 	{
    		 	mysql_query("INSERT INTO `ntur_stat` SET `battle`={$battle['id']},`dm_t1`={$damage} ON DUPLICATE KEY UPDATE dm_t1=dm_t1+{$damage}");
    		 	}
    		 else
    		 if ($telo['battle_t']==2)
    		 	{
    		 	mysql_query("INSERT INTO `ntur_stat` SET `battle`={$battle['id']},`dm_t2`={$damage} ON DUPLICATE KEY UPDATE dm_t2=dm_t2+{$damage}");
    		 	}
    	}

   }
}
	return $ret;
			}

function get_krit_chance($telo,$vrag,$telo_items,$vrag_items,$bat,$telo_eff,$vrag_eff) //
{
//  переменные
global $lvlkof,$kritkof,$PARAMS,$FROM_BOTS,$app;
///////////////////////////
//получаем вход€щие данные согласно старым пропорци€м получаем крит а и антикрит б
$telo_krit=$telo_items['krit_mf']+$telo['inta']*5;
//бонус на крит от статов
$telo_krit=add_bonus_inta($telo,$telo_krit,$telo_krit);//100% значение такое же как получили


if ($telo_krit < 0) {$telo_krit=0;}
$vrag_akrit=$vrag_items['akrit_mf']+$vrag['inta']*5+$vrag['lovk'] * 2 ;
if ($vrag_akrit < 0) {$vrag_akrit=0;}

//////////////////////////////////////////////////////////////////////////
// поправки за разницу в уровн€х дл€ тела

$dif_ko=0.05;
if (($telo['level']>=12) OR ($vrag['level']>=12)) { $dif_ko=0.1;}

$lvldif=$telo['level']-$vrag['level'];
//ƒен- 27/09/2012- откличить
//30,19,2012 ƒен- включить дл€ ботов
//3.10.2012 Deni: штраф на уровни надо вернуть дл€ всех )) заметили таки гады ))
if ($bat['type'] == 11 || $bat['type'] == 12 || $bat['type'] == 1010 ||  $bat['type'] == 304 ||  $bat['type'] == 308   ) $lvldif = 0; // руины - без левельного штрафа

$telo_krit_bonus=$dif_ko*$lvldif; //$dif_ko% за каждую разницу в уровне
$telo_krit=$telo_krit+($telo_krit*$telo_krit_bonus); // если уровни равны будте +0; если тело_левел меньше то - если больше то +
// поправка за разницу в уровн€х дл€ врага

$lvldif_vrag=$vrag['level']-$telo['level'];

if ($bat['type'] == 11 || $bat['type'] == 12 || $bat['type'] == 1010  ||  $bat['type'] == 304 ||  $bat['type'] == 308  ) $lvldif_vrag = 0; // руины - без левельного штрафа

$vrag_akrit_bonus=$dif_ko*$lvldif_vrag; //$dif_ko% за каждую разницу в уровне
$vrag_akrit=$vrag_akrit+($vrag_akrit*$vrag_akrit_bonus);
/////////////////////////////////////////////////////////////////////////
if ($telo_krit < 0) {$telo_krit=0;}
if ($vrag_akrit < 0) {$vrag_akrit=0;}

				 $Dcof=1.35;


 				  if ((($telo['room']>240) and ($telo['room']<270)) OR ($telo['lab']>0))
				  {
				  //если тело в растишке тогда постарой схеме
				  $coeff = ($telo_items['ups'] - $vrag_items['ups'])/10;
				  }
				  else
				  {
				  $coeff=0;
				  }


				 //1 % 793 тип
				if (($vrag_eff[793]) AND ($bat['nomagic']==0) )
				{
					//на теле есть баф
					$vrag_akrit+=$vrag_akrit*0.01;//+5%
				}

				 //ќтпор 703 тип
				if (($vrag_eff[703]) and ($vrag_eff[703]['lastup']>0))
				{
				//на теле есть баф
				$vrag_akrit+=$vrag_akrit*0.05;//+5%
				}

				if (($vrag_eff[713]) and ($vrag_eff[713]['add_info'][0]==2))
				{
				//на теле есть баф
				$vrag_akrit+=$vrag_akrit*0.20;//+20%
				}

				if (($vrag_eff[707]) and ($vrag_eff[707]['lastup']>0))
				{
				//на теле есть баф
				$vrag_akrit+=$vrag_akrit*0.01;//+1%
				}

				if (($vrag_eff[712]) and ($vrag_eff[712]['lastup']>0))
				{
				//на теле есть баф
				$vrag_akrit-=$vrag_akrit*0.10;//-10%
				}

				if (($vrag_eff[713]) and ($vrag_eff[713]['add_info'][1]==2))
				{
				//на теле есть баф
				$vrag_akrit-=$vrag_akrit*0.20;//-20%
				}
				/////////////////////////////////////////////////////////////////////////////////////////
				if (($telo_eff[793]) AND ($bat['nomagic']==0) )
				{
					//на теле есть баф
					$telo_krit+=$telo_krit*0.01;//+1%
				}

				if (($telo_eff[707]) and ($telo_eff[707]['lastup']>0))
				{
				//на теле есть баф
				$telo_krit+=$telo_krit*0.01;//+1%
				}

				if (($telo_eff[713]) and ($telo_eff[713]['add_info'][0]==1))
				{
				//на теле есть баф
				$telo_krit+=$telo_krit*0.20;//+20%
				}

				if (($telo_eff[712]) and ($telo_eff[712]['lastup']>0))
				{
				//на теле есть баф
				$telo_krit-=$telo_krit*0.10;//-10%
				}

				if (($telo_eff[713]) and ($telo_eff[713]['add_info'][1]==1))
				{
				//на теле есть баф
				$telo_krit-=$telo_krit*0.20;//-20%
				}
	if  (!((($telo['room']>240) and ($telo['room']<270)) OR ($telo['lab']>0) OR ($telo['in_tower']>0) OR $telo['ruines']>0))
	{
		//бонусы классов дл€ крита
		if ($telo['uclass']==1)
			{
			//уворотчик
			//”воротчикам = -25%  ћф. критических ударов
			$telo_krit=round($telo_krit*0.01*$app->dbConfig->klass_ratio_uv_krit);
			}
		elseif ($telo['uclass']==3)
			{
			//  танк
			// ћф. критических ударов = -12,5%
			$telo_krit=round($telo_kri*0.01*$app->dbConfig->klass_ratio_tank_krit);
			}
	}
			if ($telo_krit < 0) {$telo_krit=0;}

				if ($FROM_BOTS)
				{
				$sh_krit = round(((1 - ($vrag_akrit + 50)/($telo_krit + 50)) * 100) * ($PARAMS['kritkof']));
				}
				else
				{
				$sh_krit = round(((1 - ($vrag_akrit + 50)/($telo_krit + 50)) * 100) * ($lvlkof + $kritkof));
				}

				if($sh_krit < 1) { $sh_krit = 0; }

			        if ($coeff > 0)
			           {
     				   $sh_krit = round($sh_krit * (1 + 0.1 * $coeff) * $Dcof);
			           }
			           else
			           {
     				   $sh_krit = round($sh_krit * $Dcof);
				   }

				if($sh_krit < 1) { $sh_krit = 0; }
				if($sh_krit > 99) { $sh_krit = 99; } // 80 было

if ($telo['id']==14897)
		{
		addchp('<font color=red>¬нимание!</font> ƒебаг krit_poins '.$telo['login'].' : telo_krit='.$telo_krit.' vrag_akrit='.$vrag_akrit,'{[]}Bred{[]}',-1,0);
		addchp('<font color=red>¬нимание!</font> ƒебаг app klass_ratio_uv_krit='.$app->dbConfig->klass_ratio_uv_krit.' / klass_ratio_uv_krit='.$app->dbConfig->klass_ratio_uv_krit.' ','{[]}Bred{[]}',-1,0);
		}

return $sh_krit;
}

function get_uvorot_chance($telo,$vrag,$telo_items,$vrag_items,$bat,$telo_eff,$vrag_eff) //
{
// переменные
global $lvlkof,$uvorotkof,$PARAMS,$FROM_BOTS,$app;
///////////////////////////
//получаем вход€щие данные согласно старым пропорци€м получаем крит а и антикрит б
$telo_uvar=$telo_items['uvor_mf']+$telo['lovk']*5;
//бонус на уворот от статов
$telo_uvar=add_bonus_lovk($telo,$telo_uvar,$telo_uvar);//100% значение такое же как то что есть!!!
if ($telo_uvar<0) {$telo_uvar=0;}



$vrag_auvar=$vrag_items['auvor_mf']+$vrag['lovk']*5+$vrag['inta']* 2;
if ($vrag_auvar<0) {$vrag_auvar=0;}

//////////////////////////////////////////////////////////////////////////
// поправки за разницу в уровн€х дл€ тела
//
$dif_ko=0.05;
if (($telo['level']>=12) OR ($vrag['level']>=12)) { $dif_ko=0.1;}
/////////////////////////////////////////////////

$lvldif=$telo['level']-$vrag['level'];

if ($bat['type'] == 11 || $bat['type'] == 12 || $bat['type'] == 1010 || $bat['type'] == 304 ||  $bat['type'] == 308  ) $lvldif = 0;


$telo_uvar_bonus=$dif_ko*$lvldif; //$dif_ko % за каждую разницу в уровне

$telo_uvar=$telo_uvar+($telo_uvar*$telo_uvar_bonus); // если уровни равны будте +0; если тело_левел меньше то - если больше то +
// поправка за разницу в уровн€х дл€ врага

$lvldif_vrag=$vrag['level']-$telo['level'];

if ($bat['type'] == 11 || $bat['type'] == 12 || $bat['type'] == 1010 || $bat['type'] == 304 ||  $bat['type'] == 308 ) $lvldif_vrag = 0;


$vrag_auvar_bonus=$dif_ko*$lvldif_vrag; //% за каждую разницу в уровне
$vrag_auvar=$vrag_auvar+($vrag_auvar*$vrag_auvar_bonus);
/////////////////////////////////////////////////////////////////////////
if ($telo_uvar<0) {$telo_uvar=0;}
if ($vrag_auvar<0) {$vrag_auvar=0;}

// бонус v.1
				if ((($telo['room']>240) and ($telo['room']<270)) OR ($telo['lab']>0))
				  {
				  //если тело в растишке тогда постарой схеме
				  $coeff = ($telo_items['ups'] - $vrag_items['ups'])/10;
				  if (($telo_uvar/$vrag_auvar) > 2)
				  { $Dcof=1.28; } else { $Dcof=1.0808; }
				  }
				  else
				  {
				  $coeff = 0;
				  //$Dcof=1.188; [18:22:10]  отька: сереж [18:22:20]  отька: коэф уворота щас стоит 1.18, сделай 1.2
				  //[21:41:15] Deni: верни как было объективно - слишком много рул€ уворотам
				  //[18:01:16] Bred: вопрос какой ставить 12 м? [18:16:08] Deni: такой же как дл€ 11х 20/12/12
				  // 8 по 11
				  	if (($telo['level']==$vrag['level']) and ($telo['level']==8))
				  	{
				  	$Dcof=1.15;//дл€ 8-х
				  	}
				  	else if (($telo['level']==$vrag['level']) and ($telo['level']==9))
				  	{
				  	$Dcof=1.145;//дл€ 9-х
				  	}
				  	else if (($telo['level']==$vrag['level']) and ($telo['level']==10))
				  	{
				  	$Dcof=1.17;//дл€ 10-х
				  	}
				  	else if (($telo['level']==$vrag['level']) and ($telo['level']==11))
				  	{
				  	$Dcof=1.18;//дл€ 11-х
				  	}
				  	else if (($telo['level']==$vrag['level']) and ($telo['level']==12))
				  	{
				  	$Dcof=1.19;//дл€ 12-х
				  	}
					else if (($telo['level']==$vrag['level']) and ($telo['level']==13))
				       {
				       $Dcof=1.195; //дл€ 13-х     1.2
				       }
					else
				  	{
				  	//все остальные случаи
				  	$Dcof=1.181;
				  	}
				  }


				 //793 1% тип
				if (($vrag_eff[793]) AND ($bat['nomagic']==0) )
				 {
					//на теле есть баф
					$vrag_auvar+=$vrag_auvar*0.01;//+1%
				}


				 //ќтпор 703 тип
				if (($vrag_eff[703]) and ($vrag_eff[703]['lastup']>0))
				{
				//на теле есть баф
				$vrag_auvar+=$vrag_auvar*0.05;//+5%
				}

				 //707 тип
				if (($vrag_eff[707]) and ($vrag_eff[707]['lastup']>0))
				{
				//на теле есть баф
				$vrag_auvar+=$vrag_auvar*0.01;//+1%
				}

				if (($vrag_eff[713]) and ($vrag_eff[713]['add_info'][0]==4))
				{
				//на теле есть баф
				$vrag_auvar+=$vrag_auvar*0.20;//+20%
				}

				if (($vrag_eff[712]) and ($vrag_eff[712]['lastup']>0))
				{
				//на теле есть баф
				$vrag_auvar-=$vrag_auvar*0.10;//-10%
				}

				if (($vrag_eff[713]) and ($vrag_eff[713]['add_info'][1]==4))
				{
				//на теле есть баф
				$vrag_auvar-=$vrag_auvar*0.20;//+20%
				}
			//////////////////////////////////////////////////////////////////////////////////////////
				 //707 тип
				if (($telo_eff[793]) AND ($bat['nomagic']==0) )
				{
					//на теле есть баф
					$telo_uvar+=$telo_uvar*0.01;//+1%
				}

				if (($telo_eff[707]) and ($telo_eff[707]['lastup']>0))
				{
				//на теле есть баф
				$telo_uvar+=$telo_uvar*0.01;//+1%
				}

				if (($telo_eff[713]) and ($telo_eff[713]['add_info'][0]==3))
				{
				//на теле есть баф
				$telo_uvar+=$telo_uvar*0.20;//+20%
				}

				if (($telo_eff[712]) and ($telo_eff[712]['lastup']>0))
				{
				//на теле есть баф
				$telo_uvar-=$telo_uvar*0.10;//-10%
				}

				if (($telo_eff[713]) and ($telo_eff[713]['add_info'][1]==3))
				{
				//на теле есть баф
				$telo_uvar-=$telo_uvar*0.20;//+20%
				}

		if  (!((($telo['room']>240) and ($telo['room']<270)) OR ($telo['lab']>0) OR ($telo['in_tower']>0) OR $telo['ruines']>0))
		{
			////////////////////////////
			//бонусы классов дл€ уворота
			if ($telo['uclass']==2)
			{
			//  ритовикам  -25%  ћф. увертливости
			$telo_uvar=round($telo_uvar*0.01*$app->dbConfig->klass_ratio_krit_uv);
			}
			elseif ($telo['uclass']==3)
			{
			//  танк
			// ћф. увертливости -12,5%
			$telo_uvar=round($telo_uvar*0.01*$app->dbConfig->klass_ratio_tank_uv);
			}
		}

			if ($telo_uvar<0) {$telo_uvar=0;}

				if ($FROM_BOTS)
				{
				$sh_uver = round(((1 - ($vrag_auvar + 50)/($telo_uvar + 50)) * 100) * ($PARAMS['uvorkof']));
				}
				else
				{
				$sh_uver = round(((1 - ($vrag_auvar + 50)/($telo_uvar + 50)) * 100) * ($lvlkof + $uvorotkof));
				}



				if($sh_uver < 1) { $sh_uver = 0; }



			 if ($coeff >0)
			    {
				$sh_uver = round($sh_uver * (1 + 0.1 * $coeff) * $Dcof);
			    }
			    else
			    {
				$sh_uver = round($sh_uver * $Dcof);
			    }

				if($sh_uver < 1) { $sh_uver = 0; }

				if (mt_rand(1,2)==2)
					{
					if($sh_uver >= 100) { $sh_uver = 100; }
					}
					else
					{
					if($sh_uver > 99) { $sh_uver = 99; } // было 80
					}



return $sh_uver;
}

function get_dem_udar($telo,$vrag,$telo_items,$vrag_items,$kuda,$getbat=null,$telo_eff,$vrag_eff,$a_count=1) // урон простого удара kuda=int
{
global $lvlkof,$attkof,$min_uron,$rabota_boni,$rabota_boni_vinos,$rabota_boni_delta,$PARAMS,$FROM_BOTS,$app;

///отработка боевых эффектов после просчета

if (($vrag_eff[791]) AND ($getbat['nomagic']==0) and ($telo['ruines']==0) )
{
	//на теле есть баф,
	$vrag_items['bron'.$kuda] += intval($vrag_items['bron'.$kuda]*0.15);
}


//707тип
if (($vrag_eff[707]) and ($vrag_eff[707]['lastup']>0))
	{
	//на теле есть баф
	$vrag_items['bron'.$kuda]+=10;
	}
//722
if (($vrag_eff[722]) and ($vrag_eff[722]['lastup']>0))
	{
	//на теле есть баф
	$vrag_items['bron'.$kuda]+=10;
	}

//709-710тип
if (($vrag_eff[710]) and ($vrag_eff[710]['lastup']>0))
	{
	//на теле есть баф
	$vrag_items['bron'.$kuda]-=(int)($vrag_items['bron'.$kuda]*0.15);//-15%  брони
	}

if (($vrag_eff[712]) and ($vrag_eff[712]['lastup']>0))
	{
	//на теле есть баф
       $vrag_items['bron'.$kuda]-=(int)($vrag_items['bron'.$kuda]*0.10);//-10%  брони
	}


	if ($FROM_BOTS)
		{
		//тестова€
		if ($a_count==2)
		{
		$minudar=$telo_items['min_u2'];
		$maxudar=$telo_items['max_u2'];
		}
		else
		{
		$minudar=$telo_items['min_u'];
		$maxudar=$telo_items['max_u'];
		}
		if ($minudar < 1) { $minudar=1;} //перестраховка
		$bron = $vrag_items['bron'.$kuda];
		$damage = mt_rand($minudar, $maxudar);

		$b_ot=round($bron * $PARAMS['r_bron_udar']);
		$b_to=round($bron * ($PARAMS['r_bron_udar']+$PARAMS['bron_sh']));
		$bb=mt_rand($b_ot,$b_to);

		$udar = round(($damage - $bb) * ($PARAMS['attkof']));

		if($udar < ($minudar*0.2)) {  $udar = round($minudar * (0.2));           }

		}
		else
		{
	//V2: стара€ дл€ всех формула
		if ($a_count==2)
		{
		$minudar=$telo_items['min_u2'];
		$maxudar=$telo_items['max_u2'];
		}
		else
		{
		$minudar=$telo_items['min_u'];
		$maxudar=$telo_items['max_u'];
		}
		if ($minudar < 1) { $minudar=1;} //перестраховка
		$bron = $vrag_items['bron'.$kuda];
		$damage = mt_rand($minudar, $maxudar);

		if ($rabota_boni_vinos==true)
			{
				if (true)// (($vrag['room']==44) OR ($vrag['room']==76))
				{
				$rabota_boni=get_rabota_boni_lvls($vrag);
				}
				else
				{
				$rabota_boni=get_rabota_boni($vrag);
				}
			}

		$b_ot=round($bron * $rabota_boni);
		$b_to=round($bron * ($rabota_boni+$rabota_boni_delta));
		$bb=mt_rand($b_ot,$b_to);

		$udar = round(($damage - $bb) * ($attkof*8));

		$minka=round((mt_rand(10,20)*0.01),2);
		if($udar < ($minudar*$minka)) {  $udar = round($minudar * $minka);           }
		}


if (($telo_eff[792]) AND ($getbat['nomagic']==0) and ($telo['ruines']==0) )
{
	//на теле есть баф
	$udar += intval($udar*0.05); //увеличивает ”–ќЌ на 5%
}

//∆ј∆ƒј  –ќ¬» 701 тип
if (($telo_eff[701]) and ($telo_eff[701]['lastup']>0))
	{
	//на теле есть баф
	$udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

if (($telo_eff[712]) and ($telo_eff[712]['lastup']>0))
	{
	//на теле есть баф
	$udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

if (($telo_eff[722]) and ($telo_eff[722]['lastup']>0))
	{
	//на теле есть баф
	$udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

//707тип
if (($telo_eff[707]) and ($telo_eff[707]['lastup']>0))
	{
	//на теле есть баф
	$udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

//709тип
if (($telo_eff[709]) and ($telo_eff[709]['lastup']>0))
	{
	//на теле есть баф
	$udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

//“®ћЌџ… ў»“ - 702 тип
if (($vrag_eff[702]) and ($vrag_eff[702]['lastup']>0))
	{
	$udar-=10;
	 if ($udar<=0) {$udar=0;}
	}

// 708 тип
if (($vrag_eff[708]) and ($vrag_eff[708]['lastup']>0))
	{
	if ((($vrag['hp']-$udar)<=1) and ($udar>0))
		{
		$udar=$vrag['hp']-1;
		}
	}

// 706 тип
if (($vrag_eff[706]) and ($vrag_eff[706]['lastup']>0))
	{
	if ($udar>1)
		{
		$udar=1;
		}
	}

// 716 тип
if (($vrag_eff[716]) and ($vrag_eff[716]['lastup']>0))
	{
	if ($udar>1)
		{
		$udar=1;
		}
	}

	if ($telo['lab'] == 0 && $telo['in_tower'] == 0) {
		if ($telo['uclass'] == 1) {
			$udar = $udar * $app->dbConfig->ratio_damage_uvorot;
		} elseif ($telo['uclass'] == 2) {
			$udar = $udar * $app->dbConfig->ratio_damage_krit;
		} elseif ($telo['uclass'] == 3) {
			$udar = $udar * $app->dbConfig->ratio_damage_tank;
		} else {
			$udar = $udar * $app->dbConfig->ratio_damage_unk;
		}
	}

	return $udar;
}



function get_dem_udar_krit($telo,$vrag,$telo_items,$vrag_items,$kuda,$getbat=null,$telo_eff,$vrag_eff,$a_count=1) // урон крит удара kuda=int
{
global $lvlkof,$attkritkof,$min_uron,$rabota_boni_krit,$rabota_boni_vinos,$rabota_boni_delta,$PARAMS,$FROM_BOTS,$app;
//-------------расчет крит удара учитыва€ бронь---------------------

///отработка боевых эффектов после просчета
//707тип
if (($vrag_eff[791]) AND ($getbat['nomagic']==0) and ($telo['ruines']==0) )
{
	//на теле есть баф,
	$vrag_items['bron'.$kuda] += intval($vrag_items['bron'.$kuda]*0.15);
}


if (($vrag_eff[707]) and ($vrag_eff[707]['lastup']>0))
	{
	//на теле есть баф
	$vrag_items['bron'.$kuda]+=10;
	}

//722тип
if (($vrag_eff[722]) and ($vrag_eff[722]['lastup']>0))
	{
	//на теле есть баф
	$vrag_items['bron'.$kuda]+=10;
	}


//709-710тип
if (($vrag_eff[710]) and ($vrag_eff[710]['lastup']>0))
	{
	//на теле есть баф
	$vrag_items['bron'.$kuda]-=(int)($vrag_items['bron'.$kuda]*0.15);//-15%  брони
	}

if (($vrag_eff[712]) and ($vrag_eff[712]['lastup']>0))
	{
	//на теле есть баф
       $vrag_items['bron'.$kuda]-=(int)($vrag_items['bron'.$kuda]*0.10);//-10%  брони
	}

	if ($FROM_BOTS)
		{
		// тестово
		if ($a_count==2)
		{
		$minudar=$telo_items['min_u2'];
		$maxudar=$telo_items['max_u2'];
		}
		else
		{
		$minudar=$telo_items['min_u'];
		$maxudar=$telo_items['max_u'];
		}

		if ($minudar < 1) { $minudar=1;} //перестраховка
		$bron = $vrag_items['bron'.$kuda];
		$damage = mt_rand($minudar, $maxudar);

		$b_ot=round($bron * $PARAMS['r_bron_krinudar']);
		$b_to=round($bron * ($PARAMS['r_bron_krinudar']+$PARAMS['bron_sh']));
		$bb=mt_rand($b_ot,$b_to);

		$krit_udar = round(($damage - $bb) * ($PARAMS['attkof_krit']));

		if($krit_udar < ($minudar*0.2)) {  $krit_udar = round($minudar * (0.2));           }

		}
		else
		{
		//V2:
		if ($a_count==2)
		{
		$minudar=$telo_items['min_u2'];
		$maxudar=$telo_items['max_u2'];
		}
		else
		{
		$minudar=$telo_items['min_u'];
		$maxudar=$telo_items['max_u'];
		}

		if ($minudar < 1) { $minudar=1;} //перестраховка
		$bron = $vrag_items['bron'.$kuda];
		$damage = mt_rand($minudar, $maxudar);
		//echo "BDM:$damage / ";

		if ($rabota_boni_vinos==true)
			{

					if (true)//(($vrag['room']==44) OR  ($vrag['room']==76))
						{
						$rabota_boni_krit=get_rabota_boni_lvls($vrag);
						}
						else
						{
						$rabota_boni_krit=get_rabota_boni($vrag);
						}
			}

		$b_ot=round($bron * $rabota_boni_krit);
		$b_to=round($bron * ($rabota_boni_krit+$rabota_boni_delta));

		/*
		занижаем дл€ танка броню, при крите на 20%
		http://tickets.oldbk.com/issue/oldbk-2200
		*/
		if ($vrag['uclass']==3) //'“анк';
			{
			$b_ot=round($b_ot*0.8);
			$b_to=round($b_to*0.8);
			}

		$bb=mt_rand($b_ot,$b_to);

		$krit_udar = round(($damage - $bb) * ($attkritkof*8));

		$minka=round((mt_rand(10,20)*0.01),2);
		if($krit_udar < ($minudar*$minka)) {  $krit_udar = round($minudar * $minka);           }

		}

$krit_udar=$krit_udar*2;


if (($telo_eff[792]) AND ($getbat['nomagic']==0) and ($telo['ruines']==0) )
{
	//на теле есть баф
	$udar += intval($udar*0.05); //увеличивает ”–ќЌ на 5%
}

//∆ј∆ƒј  –ќ¬» 701 тип
if (($telo_eff[701]) and ($telo_eff[701]['lastup']>0))
	{
	//на теле есть баф
	$krit_udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

if (($telo_eff[712]) and ($telo_eff[712]['lastup']>0))
	{
	$krit_udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

if (($telo_eff[722]) and ($telo_eff[722]['lastup']>0))
	{
	//на теле есть баф
	$krit_udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

//707тип
if (($telo_eff[707]) and ($telo_eff[707]['lastup']>0))
	{
	//на теле есть баф
	$krit_udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

//709тип
if (($telo_eff[709]) and ($telo_eff[709]['lastup']>0))
	{
	//на теле есть баф
	$krit_udar+=5; //увеличивает ”–ќЌ на 5 единиц
	}

//“®ћЌџ… ў»“ - 702 тип
if (($vrag_eff[702]) and ($vrag_eff[702]['lastup']>0))
	{
	$krit_udar-=10;
	 if ($krit_udar<=0) {$krit_udar=0;}
	}

// 708 тип
if (($vrag_eff[708]) and ($vrag_eff[708]['lastup']>0))
	{
	if ((($vrag['hp']-$krit_udar)<=1) and ($krit_udar>0))
		{
		$krit_udar=$vrag['hp']-1;
		}
	}

// 706 тип
if (($vrag_eff[706]) and ($vrag_eff[706]['lastup']>0))
	{
	if ($krit_udar>1)
		{
		$krit_udar=1;
		}
	}

// 716 тип
if (($vrag_eff[716]) and ($vrag_eff[716]['lastup']>0))
	{
	if ($krit_udar>1)
		{
		$krit_udar=1;
		}
	}

	if ($telo['lab'] == 0 && $telo['in_tower'] == 0) {
		if ($telo['uclass'] == 1) {
			$krit_udar = $krit_udar * $app->dbConfig->ratio_damage_uvorot;
		} elseif ($telo['uclass'] == 2) {
			$krit_udar = $krit_udar * $app->dbConfig->ratio_damage_krit;
		} elseif ($telo['uclass'] == 3) {
			$krit_udar = $krit_udar * $app->dbConfig->ratio_damage_tank;
		} else {
			$krit_udar = $krit_udar * $app->dbConfig->ratio_damage_unk;
		}
	}


	return $krit_udar;
}

function get_dem_udar_krita($telo,$vrag,$telo_items,$vrag_items,$kuda,$getbat=null,$telo_eff,$vrag_eff,$a_count=1) // урон крит пробив блок удара kuda=int
{
global $lvlkof,$kritblokkof,$min_uron,$rabota_boni_krit_a,$rabota_boni_vinos,$rabota_boni_delta,$PARAMS,$FROM_BOTS,$app;

if (($telo['id']==14897) OR ($vrag['id']==14897) )
		{
		addchp('<font color=red>¬нимание!</font> ƒебаг get_dem_udar_krita T:'.$telo['login'].'  V:'.$vrag['login'].' : a_count='.$a_count,'{[]}Bred{[]}',-1,0);
		}
//-------------расчет ккрит через блок удара учитыва€ бронь---------------------
///отработка боевых эффектов после просчета
//707тип
if (($vrag_eff[791]) AND ($getbat['nomagic']==0) and ($telo['ruines']==0) )
{
	//на теле есть баф,
	$vrag_items['bron'.$kuda] += intval($vrag_items['bron'.$kuda]*0.15);
}


if (($vrag_eff[707]) and ($vrag_eff[707]['lastup']>0))
	{
	//на теле есть баф
	$vrag_items['bron'.$kuda]+=10;
	}

//722тип
if (($vrag_eff[722]) and ($vrag_eff[722]['lastup']>0))
	{
	//на теле есть баф
	$vrag_items['bron'.$kuda]+=10;
	}

//709-710тип
if (($vrag_eff[710]) and ($vrag_eff[710]['lastup']>0))
	{
	//на теле есть баф
	$vrag_items['bron'.$kuda]-=(int)($vrag_items['bron'.$kuda]*0.15);//-15%  брони
	}

if (($vrag_eff[712]) and ($vrag_eff[712]['lastup']>0))
	{
	//на теле есть баф
       $vrag_items['bron'.$kuda]-=(int)($vrag_items['bron'.$kuda]*0.10);//-10%  брони
	}

	if ($FROM_BOTS)
		{

			if ($a_count==2)
			{
			$minudar=$telo_items['min_u2'];
			$maxudar=$telo_items['max_u2'];
			}
			else
			{
			$minudar=$telo_items['min_u'];
			$maxudar=$telo_items['max_u'];
			}

		if ($minudar < 1) { $minudar=1;} //перестраховка
		$bron = $vrag_items['bron'.$kuda];
		$damage = mt_rand($minudar, $maxudar);

		$b_ot=round($bron * $PARAMS['r_bron_krinudar_a']);
		$b_to=round($bron * ($PARAMS['r_bron_krinudar_a']+$PARAMS['bron_sh']));
		$bb=mt_rand($b_ot,$b_to);

		$krit_udar = round(($damage - $bb) * ($PARAMS['attkof_krit_a']));


		if($krit_udar < ($minudar*0.2)) {  $krit_udar = round($minudar * (0.2));           }

		}
		else
		{
		//V2:
			if ($a_count==2)
			{
			$minudar=$telo_items['min_u2'];
			$maxudar=$telo_items['max_u2'];
			}
			else
			{
			$minudar=$telo_items['min_u'];
			$maxudar=$telo_items['max_u'];
			}
		if ($minudar < 1) { $minudar=1;} //перестраховка
		$bron = $vrag_items['bron'.$kuda];
		$damage = mt_rand($minudar, $maxudar);
		//echo "BDM:$damage / ";

		if ($rabota_boni_vinos==true)
			{
						if (true)//(($vrag['room']==44) OR ($vrag['room']==76))
						{
						$rabota_boni_krit_a=get_rabota_boni_lvls($vrag);
						}
						else
						{
						$rabota_boni_krit_a=get_rabota_boni($vrag);
						}
			}

		$b_ot=round($bron * $rabota_boni_krit_a);
		$b_to=round($bron * ($rabota_boni_krit_a+$rabota_boni_delta));

		/*
		занижаем дл€ танка броню, при крите на 20%
		http://tickets.oldbk.com/issue/oldbk-2200
		*/
		if ($vrag['uclass']==3) //'“анк';
			{
			$b_ot=round($b_ot*0.8);
			$b_to=round($b_to*0.8);
			}


		$bb=mt_rand($b_ot,$b_to);

		$krit_udar = round(($damage - $bb) * ($kritblokkof*8));

		$minka=round((mt_rand(10,20)*0.01),2);
		if($krit_udar < ($minudar*$minka)) {  $krit_udar = round($minudar * $minka);           }

		}


if (($telo_eff[792]) AND ($getbat['nomagic']==0) and ($telo['ruines']==0) )
{
	//на теле есть баф
	$udar += intval($udar*0.05); //увеличивает ”–ќЌ на 5%
}

//∆ј∆ƒј  –ќ¬» 701 тип
if (($telo_eff[701]) and ($telo_eff[701]['lastup']>0))
	{
	//на теле есть баф
	$krit_udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

if (($telo_eff[712]) and ($telo_eff[712]['lastup']>0))
	{
	$krit_udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

if (($telo_eff[722]) and ($telo_eff[722]['lastup']>0))
	{
	//на теле есть баф
	$krit_udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

//707тип
if (($telo_eff[707]) and ($telo_eff[707]['lastup']>0))
	{
	//на теле есть баф
	$krit_udar+=10; //увеличивает ”–ќЌ на 10 единиц
	}

//709тип
if (($telo_eff[709]) and ($telo_eff[709]['lastup']>0))
	{
	//на теле есть баф
	$krit_udar+=5; //увеличивает ”–ќЌ на 5 единиц
	}

//“®ћЌџ… ў»“ - 702 тип
if (($vrag_eff[702]) and ($vrag_eff[702]['lastup']>0))
	{
	$krit_udar-=10;
	 if ($krit_udar<=0) {$krit_udar=0;}
	}

// 708 тип
if (($vrag_eff[708]) and ($vrag_eff[708]['lastup']>0))
	{
	if ((($vrag['hp']-$krit_udar)<=1) and ($krit_udar>0))
		{
		$krit_udar=$vrag['hp']-1;
		}
	}

// 706 тип
if (($vrag_eff[706]) and ($vrag_eff[706]['lastup']>0))
	{
	if ($krit_udar>1)
		{
		$krit_udar=1;
		}
	}

// 716 тип
if (($vrag_eff[716]) and ($vrag_eff[716]['lastup']>0))
	{
	if ($krit_udar>1)
		{
		$krit_udar=1;
		}
	}

	if ($telo['lab'] == 0 && $telo['in_tower'] == 0) {
		if ($telo['uclass'] == 1) {
			$krit_udar = $krit_udar * $app->dbConfig->ratio_damage_uvorot;
		} elseif ($telo['uclass'] == 2) {
			$krit_udar = $krit_udar * $app->dbConfig->ratio_damage_krit;
		} elseif ($telo['uclass'] == 3) {
			$krit_udar = $krit_udar * $app->dbConfig->ratio_damage_tank;
		} else {
			$krit_udar = $krit_udar * $app->dbConfig->ratio_damage_unk;
		}
	}


	return $krit_udar;
}


function get_stone($demag,$pr)
{
//расчет сколько поглатили урона от каменной кожи
$d=(int)($demag*$pr);
return $d;
}

function get_chanse ($persent)
{
//echo "in p:".$persent;
//echo "<br>";
$persent=$persent;
   $matrix= array();
    for ($i=1;$i<=100;$i++)
	{
	if ($i <= $persent )
	  {
	  $matrix[$i]='Y';
	  }
	  else
	  {
  	  $matrix[$i]='N';
	  }
	}
	//print_r($matrix);
	shuffle($matrix);//мешаем
	//echo "<hr>";
//	print_r($matrix);
//echo "<br>";
	//mt_srand(make_seed()); // колдуем не нужно на пхп Ќачина€ с 4.2.0, это больше не нужно делать
   $key=mt_rand(1,100); // тыкаем пальцем в небо
   // если в по указателю да то да )
  if ($matrix[$key]=='Y') {  return true; } else {  return false;}
}

function get_win_money_logs($getbat)
{
// получить
}

function get_max_mf($krit,$akrit,$uvor,$auvor)
{
if (($krit>=$akrit) and ($krit>=$uvor) and ($krit>=$auvor))
{
return "krit_mf";
}
else
if (($akrit>=$krit) and ($akrit>=$uvor) and ($akrit>=$auvor))
{
return "akrit_mf";
}
else
if (($uvor>=$krit) and ($uvor>=$akrit) and ($uvor>=$auvor))
{
return "uvor_mf";
}
else
{
return "auvor_mf";
}
}

function load_mass_items_by_id($telo,$telo_eff=null,$prof_data=null)
{

global $FROM_BOTS, $PARAMS, $NEW_TEST_BONUS, $app, $user, $WEAP_ITYPE;

//загрузка пользовательских картинок
$uimg=get_users_gellery($telo);

//загружаем шмотки все кроме магий и подарков и вс€кой херни надо добавить
// загруженный масив буит нужен дл€ расчетов и отображени€

// универсальный запрос работает и дл€ людей и дл€ ботов!!
//   $query_telo_dess = mysql_query("SELECT * FROM oldbk.inventory WHERE  ( id in ({$telo['sergi']},{$telo['kulon']},{$telo['perchi']},{$telo['weap']},{$telo['bron']},{$telo['r1']},{$telo['r2']},{$telo['r3']},{$telo['helm']},{$telo['shit']},{$telo['boots']},{$telo['nakidka']},{$telo['rubashka']},{$telo['runa1']},{$telo['runa2']},{$telo['runa3']} ) ) and id > 0 ");
$items_arr=array();
	if ($telo['sergi']>0)
		{
		$items_arr[]=$telo['sergi'];
		}
	if ($telo['kulon']>0)
		{
		$items_arr[]=$telo['kulon'];
		}
	if ($telo['perchi']>0)
		{
		$items_arr[]=$telo['perchi'];
		}
	if ($telo['weap']>0)
		{
		$items_arr[]=$telo['weap'];
		}
	if ($telo['bron']>0)
		{
		$items_arr[]=$telo['bron'];
		}
	if ($telo['r1']>0)
		{
		$items_arr[]=$telo['r1'];
		}
	if ($telo['r2']>0)
		{
		$items_arr[]=$telo['r2'];
		}
	if ($telo['r3']>0)
		{
		$items_arr[]=$telo['r3'];
		}
	if ($telo['helm']>0)
		{
		$items_arr[]=$telo['helm'];
		}
	if ($telo['shit']>0)
		{
		$items_arr[]=$telo['shit'];
		}
	if ($telo['boots']>0)
		{
		$items_arr[]=$telo['boots'];
		}
	if ($telo['nakidka']>0)
		{
		$items_arr[]=$telo['nakidka'];
		}
	if ($telo['rubashka']>0)
		{
		$items_arr[]=$telo['rubashka'];
		}
	if ($telo['runa1']>0)
		{
		$items_arr[]=$telo['runa1'];
		}
	if ($telo['runa2']>0)
		{
		$items_arr[]=$telo['runa2'];
		}
	if ($telo['runa3']>0)
		{
		$items_arr[]=$telo['runa3'];
		}


	$telo_magicIds   = array();
	$telo_magicIds[] = 0;
	$telo_wearItems  = array();

////////////////////////////////
$totsumm=0;
$telo_wearItems['krit_mf']=0;
$telo_wearItems['akrit_mf']=0;
$telo_wearItems['uvor_mf']=0;
$telo_wearItems['auvor_mf']=0;
$telo_wearItems['bron1']=0;
$telo_wearItems['bron2']=0;
$telo_wearItems['bron3']=0;
$telo_wearItems['bron4']=0;
$telo_wearItems['min_u']=0;
$telo_wearItems['max_u']=0;

$telo_wearItems['min_u2']=0;
$telo_wearItems['max_u2']=0;

$telo_wearItems['allsumm']=0;
$telo_wearItems['ab_mf']=0;
$telo_wearItems['ab_bron']=0;
$telo_wearItems['ab_uron']=0;
$telo_wearItems['ups']=0;

$telo_wep['mast']=0;
$telo_wep['mast2']=0;
$telo_wearItems['chem']='';
$telo_wearItems['chem2']='';

//////////////////////////////////////
			//бонусы валентинок
			if (($telo_eff[900]) AND ($telo_eff[900]['add_info']>0))
			{
			//ћф. увертливости
			$telo_wearItems['uvor_mf']+=(int)($telo_eff[900]['add_info']);
			}
			if (($telo_eff[901]) AND ($telo_eff[901]['add_info']>0))
			{
			//ћф. против увертлив.
			$telo_wearItems['auvor_mf']+=(int)($telo_eff[901]['add_info']);
			}
			if (($telo_eff[902]) AND ($telo_eff[902]['add_info']>0))
			{
			//ћф. критических ударов
			$telo_wearItems['krit_mf']+=(int)($telo_eff[902]['add_info']);
			}
			if (($telo_eff[903]) AND ($telo_eff[903]['add_info']>0))
			{
			//ћф. против крит. ударов
			$telo_wearItems['akrit_mf']+=(int)($telo_eff[903]['add_info']);
			}
			if (($telo_eff[904]) AND ($telo_eff[904]['add_info']>0))
			{
			//бонус на макс мф
			$telo_wearItems['ab_mf']+=(int)($telo_eff[904]['add_info']);
			}
			if (($telo_eff[905]) AND ($telo_eff[905]['add_info']>0))
			{
			//бонус на бронь
			$telo_wearItems['ab_bron']+=(int)($telo_eff[905]['add_info']);
			}
			if (($telo_eff[906]) AND ($telo_eff[906]['add_info']>0))
			{
			//бонус на урон
			$telo_wearItems['ab_uron']+=(int)($telo_eff[906]['add_info']);
			}
			if (($telo_eff[440]) AND ($telo_eff[440]['add_info']!=''))
			{
			//бонус на урон Ќеукротима€ €рость параметр ј
			$pr=explode(":",$telo_eff[440]['add_info']);
			$pr_a=$pr[1];
			$pr_b=$pr[2];
			$telo_wearItems['ab_uron']+=(int)($pr_a*100);
			}

			$telo_wearItems['add_bonus_mf']=0;
			if (($telo_eff[441]) AND ($telo_eff[441]['add_info']!=''))
			{
			//бонус на макс мф
			$mpr=explode(":",$telo_eff[441]['add_info']);
			$mpr_hp=$mpr[1];
			$mpr_mf=$mpr[2];
			$telo_wearItems['add_bonus_mf']+=(int)($mpr_mf);
			}
////////////////////////////////
			//уник-чел
			if (strpos($telo['medals'], 'k202;') !== false)
				{
				$telo_wearItems['unik']=1;
				}
				else
				{
				$telo_wearItems['unik']=0;
				}
			//уник-чел
			if (strpos($telo['medals'], 'k203;') !== false)
				{
				$telo_wearItems['supunik']=1;
				}
				else
				{
				$telo_wearItems['supunik']=0;
				}

//// тут потом можно загружать еще чето что надо
/// а пока тольк огрузим ниже мастерство дл€ профильного оружи€
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//$query_telo_dess = mysql_query_cache("SELECT * FROM oldbk.inventory WHERE  ( id in ({$telo['sergi']},{$telo['kulon']},{$telo['perchi']},{$telo['weap']},{$telo['bron']},{$telo['r1']},{$telo['r2']},{$telo['r3']},{$telo['helm']},{$telo['shit']},{$telo['boots']},{$telo['nakidka']},{$telo['rubashka']},{$telo['runa1']},{$telo['runa2']},{$telo['runa3']} ) ) and id > 0  /*{$user['battle']} {$_SESSION['usemagic_count']} */ ",false,300);
	//	while(list($k,$row) = each($query_telo_dess)) {

	if (count($items_arr)>0)
	{
	//echo "SELECT * FROM oldbk.inventory WHERE  id IN (" . implode(", ", $items_arr) . ")" ;



	$query_telo_dess = mysql_query("SELECT * FROM oldbk.inventory WHERE  id IN (" . implode(", ", $items_arr) . ")");
	while($row = mysql_fetch_assoc($query_telo_dess)) {

		if (((($row['prototype'] >= 55510301) AND ($row['prototype'] <= 55510311)) || (($row['prototype'] >= 55510328) AND ($row['prototype'] <= 55510333))) OR  (($row['prototype']==55510350)||($row['prototype']==55510351)||($row['prototype']==55510352))  )
			{
			//елки 2016
			$telo_wearItems['elka_aura_ids'][$row['id']]=$row['prototype'].'|'.$row['up_level'];// запоминаем ид елки дл€ ауры и текущее значение
			}

		$row['img_url']=$row['img'];

	   /* if($row['add_pick']!=''&& $row['pick_time']>time())
        {
        	$row['img']=$row['add_pick'];
        }
			*/

			if (($row['id']==$telo['r1']) and ($uimg[5][1]!='')) {$row['img']=$uimg[$row['type']][1];}
				elseif (($row['id']==$telo['r2']) and ($uimg[5][2]!='')) {$row['img']=$uimg[$row['type']][2];}
					elseif (($row['id']==$telo['r3']) and ($uimg[5][3]!='')) {$row['img']=$uimg[$row['type']][3];}
						elseif (($row['id']==$telo['shit']) and ($uimg[10][1]!='')) {$row['img']=$uimg[10][1];}
							elseif (($row['id']==$telo['weap']) and ($uimg[3][1]!='')) {$row['img']=$uimg[3][1];}
							elseif (($uimg[$row['type']][1]!='') AND ($row['type']!=5)  AND ($row['type']!=3)  AND ($row['type']!=10))  { $row[img]=$uimg[$row['type']][1]; }


	$telo_wearItems[$row['id']] = $row;
  $totsumm+=$row['cost'];

        	if (($row['id']==$telo['shit']) AND (($row['prototype']==501)||($row['prototype']==502)) )
        	{
        	//костыли
		$telo_wearItems['min_u2']+=$row['minu'];
		$telo_wearItems['max_u2']+=$row['maxu'];
        	$row['minu']=0;
        	$row['maxu']=0;
        	}
        	elseif (($row['id']==$telo['weap']) AND (($row['prototype']==501)||($row['prototype']==502)) )
        	{
		$row['bron1']=0;
		$row['bron2']=0;
		$row['bron3']=0;
		$row['bron4']=0;
		$telo_wearItems['min_u']+=$row['minu'];
		$telo_wearItems['max_u']+=$row['maxu'];
        	$row['minu']=0;
        	$row['maxu']=0;
        	}

	$telo_wearItems['krit_mf']+=$row['mfkrit'];
	$telo_wearItems['akrit_mf']+=$row['mfakrit'];
	$telo_wearItems['uvor_mf']+=$row['mfuvorot'];
	$telo_wearItems['auvor_mf']+=$row['mfauvorot'];
		$telo_wearItems['bron1']+=$row['bron1'];
		$telo_wearItems['bron2']+=$row['bron2'];
		$telo_wearItems['bron3']+=$row['bron3'];
		$telo_wearItems['bron4']+=$row['bron4'];

	if ($row['type']==34)
		{
		$telo_wearItems['min_u2']+=$row['minu'];
		$telo_wearItems['max_u2']+=$row['maxu'];
		}
	elseif ($row['type']==3)
		{
		$telo_wearItems['min_u']+=$row['minu'];
		$telo_wearItems['max_u']+=$row['maxu'];
		}
		else
		{
		$telo_wearItems['min_u2']+=$row['minu'];
		$telo_wearItems['max_u2']+=$row['maxu'];
		$telo_wearItems['min_u']+=$row['minu'];
		$telo_wearItems['max_u']+=$row['maxu'];
		}

		$telo_wearItems['ups']+=$row['ups'];
	$telo_wearItems['ab_mf']+=$row['ab_mf'];

	//дл€ работы старого принципа оставл€ем


	if ( ($telo['uclass']==3) and ($row['ab_bron']>0) )
		{
		//компенсаци€
			$telo_wearItems['ab_bron']+=5;
			$telo_wearItems['ab_uron']+=5;
		}
	else
	{
	$telo_wearItems['ab_bron']+=$row['ab_mf'];
	$telo_wearItems['ab_uron']+=$row['ab_mf'];
	}

	//дл€ работы новго - поправленного
	$telo_wearItems['ab_bron']+=$row['ab_bron'];
	$telo_wearItems['ab_uron']+=$row['ab_uron'];



	    if ($row['unik']==1) { $telo_wearItems['unik']+=$row['unik']; } //подсчет уников
	    elseif ($row['unik']==2) { $telo_wearItems['supunik']++; } //подсчет  супер уников

		if($row['includemagic'] > 0) {
	        $telo_magicIds[] = $row['includemagic'];
		}
		// шо за пушка
		if ($row['id']==$telo['weap'])
		 	{
			$telo_wep=load_wep($row,$telo);
		 	}
		 //шо за пушка во второй руке
		 if (($row['id']==$telo['shit']) and ($row['type']==34))
		 	{
		 	$telo_wep2=load_wep($row,$telo);
		 	}

		if ($telo['id']==$user['id'])
			{
			//обработка дл€ загруженного юзера

				 if (($user['weap']==$row['id']) and ($row['type']==35) and ($user['shit']==0) ) //потом убрать условие на пушку, если щита нет то зона блока 1
				 {
					/*
					 ≈сли 1 пуха двуручна€ (щита нет)
					   1 зоны блока
					   1 зона удара
					*/
				 	$WEAP_ITYPE=1;
				 }
				 elseif (($user['shit']==$row['id']) and ($row['type']==34))
			 	{
			 	/*
				 ≈сли 2 пухи двуручных (пуха вместо щита)
				     1 зоны блока
				     2 зоны удара
			 	*/
			 	$WEAP_ITYPE=2;
			 	}
			}
			else
			{
			//дл€ врага
				if (($telo['weap']==$row['id']) and ($row['type']==35) and ($telo['shit']==0) )
				 {
				/*
				 ≈сли 1 пуха двуручна€ (щита нет)
				   1 зоны блока
				   1 зона удара
				*/
			 	$telo_wearItems['WEAP_ITYPE']=1;
			 	}
			 	elseif (($telo['shit']==$row['id']) and ($row['type']==34) )
				 {
				/*
				 ≈сли 1 пуха двуручна€ (щита нет)
				   1 зоны блока
				   1 зона удара
				*/
			 	$telo_wearItems['WEAP_ITYPE']=2;
			 	}

			}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	} // end of while


			//после всего свер€ем если тело=юзер и слот щита пустой
			if (($telo['id']==$user['id']) and ($telo['shit']==0) )
				{
				$WEAP_ITYPE=1;
				}
			//дл€ врага
			elseif (($telo['shit']==0) and ($telo['id'] < _BOTSEPARATOR_) )
				{
			 	$telo_wearItems['WEAP_ITYPE']=1;
				}

	}


	if ($telo['id'] < _BOTSEPARATOR_)
	{
			// ёвелир (профессиональна€ точность)      //Ѕонус от ювелира: + 20 антиуворота / уровень ремесла
			if ($prof_data['jewelerlevel']>0)
				{
				$telo_wearItems['auvor_mf']+=round(20*$prof_data['jewelerlevel']);
				}

			// ѕортной (удобно подогнанна€ одежда)     //Ѕонус от портного: +20 антикрита / уровень ремесла
			if ($prof_data['tailorlevel']>0)
				{
				$telo_wearItems['akrit_mf']+=round(20*$prof_data['tailorlevel']);
				}

			//ќружейник     ћодификатор урона: +...% (абсолютный, как на артах)      0,25% за каждый уровень мастерства
			if ($prof_data['armorerlevel']>0)
				{
				$telo_wearItems['ab_uron']+=($prof_data['armorerlevel']*0.25);
				}

			// Ѕронник      ”силение брони: +...%      0,5% за каждый уровень мастерства
			if ($prof_data['armorsmithlevel']>0)
			{
				$telo_wearItems['ab_bron']+=($prof_data['armorsmithlevel']*0.5);
			}
	}

// запоминаем 100%-ные значени€ ћ‘
$telo_wearItems_krit_mf=$telo_wearItems['krit_mf'];
$telo_wearItems_akrit_mf=$telo_wearItems['akrit_mf'];
$telo_wearItems_uvor_mf=$telo_wearItems['uvor_mf'];
$telo_wearItems_auvor_mf=$telo_wearItems['auvor_mf'];
//100% значени€ брони
$telo_wearItems_bron1=$telo_wearItems['bron1'];
$telo_wearItems_bron2=$telo_wearItems['bron2'];
$telo_wearItems_bron3=$telo_wearItems['bron3'];
$telo_wearItems_bron4=$telo_wearItems['bron4'];
//////////////////////////////////////////////////////////////////////////////////////////////////////
////
		// ƒобавл€ем бонусы от 441
		$add_to_mf=get_max_mf($telo_wearItems['krit_mf'],$telo_wearItems['akrit_mf'],$telo_wearItems['uvor_mf'],$telo_wearItems['auvor_mf']);
		$telo_wearItems[$add_to_mf]+=(int)$telo_wearItems['add_bonus_mf'];

		//Ѕонусы артов
       		if ($telo_wearItems['ab_mf']>0)
		{
		//если есть бонусы на ћ‘ то
		//≈сли бонус на мф - он добавл€етс€ в максимальный глобальный параметр игрока.
		$add_to_mf=get_max_mf($telo_wearItems['krit_mf'],$telo_wearItems['akrit_mf'],$telo_wearItems['uvor_mf'],$telo_wearItems['auvor_mf']);
		$telo_wearItems[$add_to_mf]+=(int)($telo_wearItems[$add_to_mf]*($telo_wearItems['ab_mf']/100));
		}


		if ($telo_wearItems['ab_bron']>0)
		{
		//дополнительна€ поправка
			$telo_wearItems[ab_bron]+=2;

			$telo_wearItems['bron1']+=(int)($telo_wearItems['bron1']*($telo_wearItems['ab_bron']/100));
			$telo_wearItems['bron2']+=(int)($telo_wearItems['bron2']*($telo_wearItems['ab_bron']/100));
			$telo_wearItems['bron3']+=(int)($telo_wearItems['bron3']*($telo_wearItems['ab_bron']/100));
			$telo_wearItems['bron4']+=(int)($telo_wearItems['bron4']*($telo_wearItems['ab_bron']/100));

		}

		//////////////////////////////////////////////////////////////////////////////////////////////////////
		//добавл€ем бонусы за уники
		//добавл€ем бонусы за  супер уники
		$unik_bonus_data=get_unik_bonus_data($telo_wearItems['unik'],$telo_wearItems['supunik']);
		if (($unik_bonus_data) and ($unik_bonus_data[0]>0) )
			{
			$telo_wearItems['krit_mf']+=round($telo_wearItems_krit_mf*(0.01*$unik_bonus_data[0]) );
			$telo_wearItems['akrit_mf']+=round($telo_wearItems_akrit_mf*(0.01*$unik_bonus_data[0]) );
			$telo_wearItems['uvor_mf']+=round($telo_wearItems_uvor_mf*(0.01*$unik_bonus_data[0]) );
			$telo_wearItems['auvor_mf']+=round($telo_wearItems_auvor_mf*(0.01*$unik_bonus_data[0]) );
			}
		////////////////////////////////////////////////////////////////////////////////////////////////




	$telo_wearItems['allsumm']=$totsumm; // запомним общую стоимость своий шмоток
	//fix если кулак и оружие не было просчитано
		if (($telo['weap']==0) or (!$telo_wep))
		 	{
		 	$kulak['otdel']=0;
		 	$telo_wep=load_wep($kulak,$telo);
		 	}
//////////////////////////////////////////////////////////////////////////////////////////////////////
//$battleinfo=mysql_fetch_array(mysql_query("SELECT * FROM battle where id='{$telo['battle']}' ; "));

if (($FROM_BOTS) and ($PARAMS['udarkof']>0))
	{
	//тестова€
	$telo_wearItems['min_u'] = round((floor($telo['sila']/3) + 1) + $telo['level'] + $telo_wearItems['min_u'] * (1 + $PARAMS['udarkof'] * $telo_wep['mast']));
	$telo_wearItems['max_u'] = round((floor($telo['sila']/3) + 4) + $telo['level'] + $telo_wearItems['max_u'] * (1+ $PARAMS['udarkof'] * $telo_wep['mast']));

	if (isset($telo_wep2))
		{
	$telo_wearItems['min_u2'] = round((floor($telo['sila']/3) + 1) + $telo['level'] + $telo_wearItems['min_u2'] * (1 + $PARAMS['udarkof'] * $telo_wep2['mast']));
	$telo_wearItems['max_u2'] = round((floor($telo['sila']/3) + 4) + $telo['level'] + $telo_wearItems['max_u2'] * (1+ $PARAMS['udarkof'] * $telo_wep2['mast']));
		}
	}
	else
	{
	//обща€
	// формула вычислени€ мин - макс урон дл€ мен€
		if ($telo['in_tower'] == 15) {
			$telo_wearItems['min_u'] = round((floor($telo['sila']/3) + 1) + 8 + $telo_wearItems['min_u'] * (1 + 0.08 * $telo_wep['mast']));
			$telo_wearItems['max_u'] = round((floor($telo['sila']/3) + 4) + 8 + $telo_wearItems['max_u'] * (1 + 0.08 * $telo_wep['mast']));

			if (isset($telo_wep2))
			{
			$telo_wearItems['min_u2'] = round((floor($telo['sila']/3) + 1) + 8 + $telo_wearItems['min_u2'] * (1 + 0.08 * $telo_wep2['mast']));
			$telo_wearItems['max_u2'] = round((floor($telo['sila']/3) + 4) + 8 + $telo_wearItems['max_u2'] * (1 + 0.08 * $telo_wep2['mast']));
			}

		} else {
			$telo_wearItems['min_u'] = round((floor($telo['sila']/3) + 1) + $telo['level'] + $telo_wearItems['min_u'] * (1 + 0.08 * $telo_wep['mast']));
			$telo_wearItems['max_u'] = round((floor($telo['sila']/3) + 4) + $telo['level'] + $telo_wearItems['max_u'] * (1 + 0.08 * $telo_wep['mast']));

			if (isset($telo_wep2))
			{
			$telo_wearItems['min_u2'] = round((floor($telo['sila']/3) + 1) + $telo['level'] + $telo_wearItems['min_u2'] * (1 + 0.08 * $telo_wep2['mast']));
			$telo_wearItems['max_u2'] = round((floor($telo['sila']/3) + 4) + $telo['level'] + $telo_wearItems['max_u2'] * (1 + 0.08 * $telo_wep2['mast']));
			}
		}
	}

	// текстовый указатель на оружие
 	$telo_wearItems['chem']=$telo_wep['chem'];
 	$telo_wearItems['mast']=$telo_wep['mast'];

	if (isset($telo_wep2))
	{
 	$telo_wearItems['chem2']=$telo_wep2['chem'];
 	$telo_wearItems['mast2']=$telo_wep2['mast'];
 	}

	///  fix из старой боевки дл€ младших уровней по урону //////////////////////////////////
	if ($telo['id'] < _BOTSEPARATOR_)
	{

				if ( (int)$telo['level'] < 4) // small level
				{
					$telo_wearItems['min_u'] += 2;
					$telo_wearItems['max_u'] += 4;

					if (isset($telo_wep2))
					{
					$telo_wearItems['min_u2'] += 2;
					$telo_wearItems['max_u2'] += 4;
					}

				}
	}

	////////// бонус в кулачке нейтарлам мастерство + иего уровень	////////////////////////
	if($telo_wearItems['chem'] == 'kulak' && $telo['pasbaf'] ==862 && $telo['in_tower'] == 0)
				{
					$telo_wearItems['min_u'] += $telo['level'];
					$telo_wearItems['max_u'] += $telo['level'];
				}

	if ($telo['id'] < _BOTSEPARATOR_)
	{


		//`smithlevel` -  узнец - Ѕонус урона:  1-2 за каждый уровень мастерства (в минимальный и максимальный урон)
		if ($prof_data['smithlevel']>0)
			{
			$telo_wearItems['min_u'] += (int)($prof_data['smithlevel']*1) ;
			$telo_wearItems['max_u'] +=(int)($prof_data['smithlevel']*2) ;

			if (isset($telo_wep2))
				{
			$telo_wearItems['min_u2'] += (int)($prof_data['smithlevel']*1) ;
			$telo_wearItems['max_u2'] +=(int)($prof_data['smithlevel']*2) ;
				}

			}
	}


	if (is_array($PARAMS))
	{
	        if ( ($telo['sila'] >= 300) )
           			{

				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*$PARAMS['sila300']);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*$PARAMS['sila300']);

				if (isset($telo_wep2))
				{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*$PARAMS['sila300']);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*$PARAMS['sila300']);
				}

           			}
		else
	        if ( ($telo['sila'] >= 275) )
           			{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*$PARAMS['sila275']);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*$PARAMS['sila275']);

				if (isset($telo_wep2))
				{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*$PARAMS['sila275']);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*$PARAMS['sila275']);

				}


           			}
		else
	        if ( ($telo['sila'] >= 250) )
           			{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*$PARAMS['sila250']);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*$PARAMS['sila250']);

				if (isset($telo_wep2))
				{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*$PARAMS['sila250']);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*$PARAMS['sila250']);

				}

           			}
		else
	        if ( ($telo['sila'] >= 225) )
           			{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*$PARAMS['sila225']);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*$PARAMS['sila225']);

				if (isset($telo_wep2))
				{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*$PARAMS['sila225']);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*$PARAMS['sila225']);

				}

           			}
		else
	        if ( ($telo['sila'] >= 200) )
           			{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*$PARAMS['sila200']);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*$PARAMS['sila200']);

				if (isset($telo_wep2))
				{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*$PARAMS['sila200']);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*$PARAMS['sila200']);
				}

           			}
		else
	        if ( ($telo['sila'] >= 175) )
           			{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*$PARAMS['sila175']);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*$PARAMS['sila175']);

				if (isset($telo_wep2))
				{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*$PARAMS['sila175']);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*$PARAMS['sila175']);
				}

           			}
		else
	        if ( ($telo['sila'] >= 150) )
           			{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*$PARAMS['sila150']);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*$PARAMS['sila150']);

				if (isset($telo_wep2))
				{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*$PARAMS['sila150']);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*$PARAMS['sila150']);
				}

           			}
		else
	        if ( ($telo['sila'] >= 125) )
           			{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*$PARAMS['sila125']);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*$PARAMS['sila125']);

				if (isset($telo_wep2))
				{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*$PARAMS['sila125']);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*$PARAMS['sila125']);
				}

           			}
		else
	        if ( ($telo['sila'] >= 100) )
           			{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*$PARAMS['sila100']);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*$PARAMS['sila100']);

				if (isset($telo_wep2))
				{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*$PARAMS['sila100']);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*$PARAMS['sila100']);
				}

           			}
           	else  if ( ($telo['sila'] >= 75))
           			{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*$PARAMS['sila75']);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*$PARAMS['sila75']);

				if (isset($telo_wep2))
				{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*$PARAMS['sila75']);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*$PARAMS['sila75']);
				}

           			}
		else if ( ($telo['sila'] >= 50)  )
				{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*$PARAMS['sila50']);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*$PARAMS['sila50']);

				if (isset($telo_wep2))
				{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*$PARAMS['sila50']);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*$PARAMS['sila50']);
				}

				}
		else if ( ($telo['sila'] >= 25) )
				{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*$PARAMS['sila25']);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*$PARAMS['sila25']);

				if (isset($telo_wep2))
				{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*$PARAMS['sila25']);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*$PARAMS['sila25']);
				}

				}

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($telo['vinos']>=300)
				{
				$telo_wearItems['bron1']+=$telo_wearItems_bron1*($PARAMS['vinos300']-1);
				$telo_wearItems['bron2']+=$telo_wearItems_bron2*($PARAMS['vinos300']-1);
				$telo_wearItems['bron3']+=$telo_wearItems_bron3*($PARAMS['vinos300']-1);
				$telo_wearItems['bron4']+=$telo_wearItems_bron4*($PARAMS['vinos300']-1);
				}
			else if ($telo['vinos']>=275)
				{
				$telo_wearItems['bron1']+=$telo_wearItems_bron1*($PARAMS['vinos275']-1);
				$telo_wearItems['bron2']+=$telo_wearItems_bron2*($PARAMS['vinos275']-1);
				$telo_wearItems['bron3']+=$telo_wearItems_bron3*($PARAMS['vinos275']-1);
				$telo_wearItems['bron4']+=$telo_wearItems_bron4*($PARAMS['vinos275']-1);
				}
			else if ($telo['vinos']>=250)
				{
				$telo_wearItems['bron1']+=$telo_wearItems_bron1*($PARAMS['vinos250']-1);
				$telo_wearItems['bron2']+=$telo_wearItems_bron2*($PARAMS['vinos250']-1);
				$telo_wearItems['bron3']+=$telo_wearItems_bron3*($PARAMS['vinos250']-1);
				$telo_wearItems['bron4']+=$telo_wearItems_bron4*($PARAMS['vinos250']-1);
				}
			else if ($telo['vinos']>=225)
				{
				$telo_wearItems['bron1']+=$telo_wearItems_bron1*($PARAMS['vinos225']-1);
				$telo_wearItems['bron2']+=$telo_wearItems_bron2*($PARAMS['vinos225']-1);
				$telo_wearItems['bron3']+=$telo_wearItems_bron3*($PARAMS['vinos225']-1);
				$telo_wearItems['bron4']+=$telo_wearItems_bron4*($PARAMS['vinos225']-1);
				}
			else if ($telo['vinos']>=200)
				{
				$telo_wearItems['bron1']+=$telo_wearItems_bron1*($PARAMS['vinos200']-1);
				$telo_wearItems['bron2']+=$telo_wearItems_bron2*($PARAMS['vinos200']-1);
				$telo_wearItems['bron3']+=$telo_wearItems_bron3*($PARAMS['vinos200']-1);
				$telo_wearItems['bron4']+=$telo_wearItems_bron4*($PARAMS['vinos200']-1);
				}
			else if ($telo['vinos']>=175)
				{
				$telo_wearItems['bron1']+=$telo_wearItems_bron1*($PARAMS['vinos175']-1);
				$telo_wearItems['bron2']+=$telo_wearItems_bron2*($PARAMS['vinos175']-1);
				$telo_wearItems['bron3']+=$telo_wearItems_bron3*($PARAMS['vinos175']-1);
				$telo_wearItems['bron4']+=$telo_wearItems_bron4*($PARAMS['vinos175']-1);
				}
			else if ($telo['vinos']>=150)
				{
				$telo_wearItems['bron1']+=$telo_wearItems_bron1*($PARAMS['vinos150']-1);
				$telo_wearItems['bron2']+=$telo_wearItems_bron2*($PARAMS['vinos150']-1);
				$telo_wearItems['bron3']+=$telo_wearItems_bron3*($PARAMS['vinos150']-1);
				$telo_wearItems['bron4']+=$telo_wearItems_bron4*($PARAMS['vinos150']-1);
				}
			else if ($telo['vinos']>=125)
				{
				$telo_wearItems['bron1']+=$telo_wearItems_bron1*($PARAMS['vinos125']-1);
				$telo_wearItems['bron2']+=$telo_wearItems_bron2*($PARAMS['vinos125']-1);
				$telo_wearItems['bron3']+=$telo_wearItems_bron3*($PARAMS['vinos125']-1);
				$telo_wearItems['bron4']+=$telo_wearItems_bron4*($PARAMS['vinos125']-1);
				}
			else if ($telo['vinos']>=100)
				{
				$telo_wearItems['bron1']+=$telo_wearItems_bron1*($PARAMS['vinos100']-1);
				$telo_wearItems['bron2']+=$telo_wearItems_bron2*($PARAMS['vinos100']-1);
				$telo_wearItems['bron3']+=$telo_wearItems_bron3*($PARAMS['vinos100']-1);
				$telo_wearItems['bron4']+=$telo_wearItems_bron4*($PARAMS['vinos100']-1);
				}
			else if ($telo['vinos']>=75)
				{
				$telo_wearItems['bron1']+=$telo_wearItems_bron1*($PARAMS['vinos75']-1);
				$telo_wearItems['bron2']+=$telo_wearItems_bron2*($PARAMS['vinos75']-1);
				$telo_wearItems['bron3']+=$telo_wearItems_bron3*($PARAMS['vinos75']-1);
				$telo_wearItems['bron4']+=$telo_wearItems_bron4*($PARAMS['vinos75']-1);
				}
			else if ($telo['vinos']>=50)
				{
				$telo_wearItems['bron1']+=$telo_wearItems_bron1*($PARAMS['vinos50']-1);
				$telo_wearItems['bron2']+=$telo_wearItems_bron2*($PARAMS['vinos50']-1);
				$telo_wearItems['bron3']+=$telo_wearItems_bron3*($PARAMS['vinos50']-1);
				$telo_wearItems['bron4']+=$telo_wearItems_bron4*($PARAMS['vinos50']-1);
				}
			else if ($telo['vinos']>=25)
				{
				$telo_wearItems['bron1']+=$telo_wearItems_bron1*($PARAMS['vinos25']-1);
				$telo_wearItems['bron2']+=$telo_wearItems_bron2*($PARAMS['vinos25']-1);
				$telo_wearItems['bron3']+=$telo_wearItems_bron3*($PARAMS['vinos25']-1);
				$telo_wearItems['bron4']+=$telo_wearItems_bron4*($PARAMS['vinos25']-1);
				}

	}
	else
	{
		//если по какой-то причине нет масива настроек работаем по старой схеме - настройки только на силу
	        if ( ($telo['sila'] >=100) and ($telo['id'] < _BOTSEPARATOR_) )
           			{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*1.30);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*1.30);

				if (isset($telo_wep2))
					{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*1.30);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*1.30);

					}

           			}
		else if ( ($telo['sila'] >= 75) and ($telo['id'] < _BOTSEPARATOR_) )
				{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*1.20);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*1.20);

				if (isset($telo_wep2))
					{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*1.20);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*1.20);
					}

				}
		else if ( ($telo['sila'] >= 50) and ($telo['id'] < _BOTSEPARATOR_) )
				{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*1.10);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*1.10);

				if (isset($telo_wep2))
					{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*1.10);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*1.10);
					}

				}
		else if ( ($telo['sila'] >= 25) and ($telo['id'] < _BOTSEPARATOR_) )
				{
				$telo_wearItems['min_u'] = round($telo_wearItems['min_u']*1.05);
				$telo_wearItems['max_u'] = round($telo_wearItems['max_u']*1.05);

				if (isset($telo_wep2))
					{
				$telo_wearItems['min_u2'] = round($telo_wearItems['min_u2']*1.05);
				$telo_wearItems['max_u2'] = round($telo_wearItems['max_u2']*1.05);

					}

				}
	}




	if ($telo_wearItems['ab_uron']>0)
	{
	$telo_wearItems['ab_uron']+=1;
	$telo_wearItems['min_u']+=(int)($telo_wearItems['min_u']*($telo_wearItems['ab_uron']/100));
	$telo_wearItems['max_u']+=(int)($telo_wearItems['max_u']*($telo_wearItems['ab_uron']/100));

	if (isset($telo_wep2))
		{
	$telo_wearItems['min_u2']+=(int)($telo_wearItems['min_u2']*($telo_wearItems['ab_uron']/100));
	$telo_wearItems['max_u2']+=(int)($telo_wearItems['max_u2']*($telo_wearItems['ab_uron']/100));
		}

	}


///////////////////////////////////////////////////////////////////////////////////
///закрузка названий дл€ магии встроеной
	$query_telo_mag = mysql_query("SELECT * FROM magic WHERE id IN (" . implode(", ", $telo_magicIds) . ")");
	while($row = mysql_fetch_assoc($query_telo_mag)) {
	    $telo_magicItems[$row['id']] = $row;
	}
//////////////////////////////////////////////////////////////////////////////////
	$telo_wearItems['incmagic']=$telo_magicItems;
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////fix for bots
///модификаторы ботов загружаем из параметров из таблицы users_clons
if ($telo['id']>_BOTSEPARATOR_) {

	if ($telo['id_user'] == 84 || $telo['owner'] > 0) {
		// арх или наЄмник
		$telo['sum_minu'] = round((floor($telo['sila']/3) + 1) + 8 + $telo['sum_minu'] * (1 + 0.08 * $telo_wep['mast']/2));
		$telo['sum_maxu'] = round((floor($telo['sila']/3) + 4) + 8 + $telo['sum_maxu'] * (1 + 0.08 * $telo_wep['mast']/2));

	}

 	if ($telo['at_cost']) {
		$totsumm=$telo['at_cost'];
		if ($telo_wearItems['allsumm'] == 0) {
			   $telo_wearItems['allsumm'] = $totsumm;
		}
	}

	/*
	if ($telo['owner'] > 0) {
		// наЄмник
		if ($telo['sum_mfkrit'])   {  $telo_wearItems['krit_mf']=$telo['sum_mfkrit']+($telo['inta']*5);}
		if ($telo['sum_mfakrit'])  {  $telo_wearItems['akrit_mf']=$telo['sum_mfakrit']+($telo['inta']*5)+($telo['lovk']*2);}
		if ($telo['sum_mfuvorot']) {  $telo_wearItems['uvor_mf']=$telo['sum_mfuvorot']+($telo['lova']*5); }
		if ($telo['sum_mfauvorot']){  $telo_wearItems['auvor_mf']=$telo['sum_mfauvorot']+($telo['lova']*5)+($telo['inta']*2);}
		if ($telo['sum_bron1'])    {  $telo_wearItems['bron1']=$telo['sum_bron1'];}
		if ($telo['sum_bron2'])    {  $telo_wearItems['bron2']=$telo['sum_bron2'];}
		if ($telo['sum_bron3'])    {  $telo_wearItems['bron3']=$telo['sum_bron3'];}
		if ($telo['sum_bron4'])    {  $telo_wearItems['bron4']=$telo['sum_bron4'];}
		if ($telo['sum_minu'])     {  $telo_wearItems['min_u']=$telo['sum_minu'];}
		if ($telo['sum_maxu'])     {  $telo_wearItems['max_u']=$telo['sum_maxu']; }
		if ($telo['ups'])          {  $telo_wearItems['ups']=$telo['ups'];}
	} else
	*/
	{

		// просто бот
		if ($telo['sum_mfkrit'])   {  $telo_wearItems['krit_mf']=$telo['sum_mfkrit'];}
		if ($telo['sum_mfakrit'])  {  $telo_wearItems['akrit_mf']=$telo['sum_mfakrit'];}
		if ($telo['sum_mfuvorot']) {  $telo_wearItems['uvor_mf']=$telo['sum_mfuvorot']; }
		if ($telo['sum_mfauvorot']){  $telo_wearItems['auvor_mf']=$telo['sum_mfauvorot'];}
		if ($telo['sum_bron1'])    {  $telo_wearItems['bron1']=$telo['sum_bron1'];}
		if ($telo['sum_bron2'])    {  $telo_wearItems['bron2']=$telo['sum_bron2'];}
		if ($telo['sum_bron3'])    {  $telo_wearItems['bron3']=$telo['sum_bron3'];}
		if ($telo['sum_bron4'])    {  $telo_wearItems['bron4']=$telo['sum_bron4'];}
		if ($telo['sum_minu'])     {  $telo_wearItems['min_u']=$telo['sum_minu'];}
		if ($telo['sum_maxu'])     {  $telo_wearItems['max_u']=$telo['sum_maxu']; }

		if ($telo['sum_minu2'])     {  $telo_wearItems['min_u2']=$telo['sum_minu2'];}
		if ($telo['sum_maxu2'])     {  $telo_wearItems['max_u2']=$telo['sum_maxu2']; }


		if ($telo['ups'])          {  $telo_wearItems['ups']=$telo['ups'];}
	}

}

if ($telo['id']==14897)
	{
	//print_r($telo_wearItems);
	}


return $telo_wearItems;
}

function takeMLitem($to,$item) {
	if ($item['type'] == 1 || $item['type'] == 2) {
		// передали вещь
		mysql_query_100('UPDATE inventory SET owner = '.$to['id'].' WHERE id = '.$item['item']['id']);

		// в дело грабителю
		$rec = array();
    		$rec['owner']=$to['id'];
		$rec['owner_login']=$to['login'];
		$rec['owner_balans_do']=$to['money'];
		$rec['owner_balans_posle']=$to['money'];
		$rec['target']=$item['owner'];
		$rec['target_login']=$item['ownername'];
		$rec['type']=264; // получил предмет за грабЄж

		$rec['item_id']=get_item_fid($item['item']);
		$rec['item_name']=$item['item']['name'];
		$rec['item_count']=1;
		$rec['item_type']=$item['item']['type'];
		if ($item['item']['prototype'] == 3003060) $item['item']['cost'] = 3;
		$rec['item_cost']=$item['item']['cost'];
		$rec['item_dur']=$item['item']['duration'];
		$rec['item_maxdur']=$item['item']['maxdur'];
		$rec['item_ups']=$item['item']['ups'];
		$rec['item_unic']=$item['item']['unik'];
		$rec['item_incmagic']=$item['item']['includemagicname'];
		$rec['item_incmagic_count']=$item['item']['includemagicuses'];
		$rec['item_arsenal']='';
		add_to_new_delo($rec);

		$q = mysql_query('SELECT * FROM users WHERE id = '.$item['item']['owner']);
		if ($q !== FALSE) {
			$u = mysql_fetch_assoc($q);

			// в дело жертве
			$rec['owner']=$u['id'];
			$rec['owner_login']=$u['login'];
			$rec['owner_balans_do']=$u['money'];
			$rec['owner_balans_posle']=$u['money'];
			$rec['target']=$to['id'];
			$rec['target_login']=$to['login'];
			$rec['type']=265; // отдал за грабЄж
			add_to_new_delo($rec);
		}

		// системка хоз€ину
		addchp ('<font color=red>¬нимание!</font> ¬ас ограбили и забрали <b>'.$item['item']['name'].'</b>','{[]}'.$item['ownername'].'{[]}',-1);
	}
	if ($item['type'] == 3) {
		// передаЄм бабки
		mysql_query_100('UPDATE users SET money = money + '.$item['count'].' WHERE id = '.$to['id']);

		// в дело грабителю
		$rec = array();
		$rec['owner']=$to['id'];
		$rec['owner_login']=$to['login'];
		$rec['owner_balans_do']=$to['money'];
		$rec['owner_balans_posle']=$to['money']+$item['count'];
		$rec['target']=$item['owner'];
		$rec['target_login']=$item['ownername'];
		$rec['type']=262; // получил за грабЄж
		$rec['sum_kr']=$item['count'];
		add_to_new_delo($rec);

		$q = mysql_query('SELECT * FROM users WHERE id = '.$item['owner']);
		if ($q !== FALSE) {
			$u = mysql_fetch_assoc($q);

			// в дело жертве
			$rec = array();
			$rec['owner']=$u['id'];
			$rec['owner_login']=$u['login'];
			$rec['owner_balans_do']=$u['money'];
			$rec['owner_balans_posle']=$u['money']-$item['count'];
			$rec['target']=$to['id'];
			$rec['target_login']=$to['login'];
			$rec['type']=263; // отдал за грабЄж
			$rec['sum_kr']=$item['count'];
			add_to_new_delo($rec);
		}

		// забираем
		mysql_query_100('UPDATE users SET money = money - '.$item['count'].' WHERE id = '.$item['owner']);

		// системка хоз€ину
		addchp ('<font color=red>¬нимание!</font> ¬ас ограбили и забрали <b>'.$item['count'].'</b> кр.','{[]}'.$item['ownername'].'{[]}',-1);


	}

	return $item['type'];
}

function finish_battle ($win_t, $bat, $blood , $btype , $bfond , $arch_fin=0)
{
$MY_MONEY='';

	//јкци€ ¬есна - хоть тут не инклюдить
	//$KO_start_time5=mktime(0,0,0,3,15,date("Y"));
	//$KO_fin_time5  =mktime(0,10,0,5,15,date("Y"));

mysql_query("update battle set t1_dead='finbatt' where id={$bat['id']} and t1_dead='finlog' ;");
if (mysql_affected_rows()>0)
{
//если апдейт прошел по запускаем концовку бо€

			if (($bat['type']==601) OR ( $bat['type']==602) OR ( $bat['type']==603) OR ( $bat['type']==604) )
			{
				$win_t = 1; // лорд в t_2
				$bat['win_t'] = 1;
			}

			//ƒелаем прибавку очков опыта рунам на персах победившей команды

			//“аблица коэф- бо€!!!
			if ($win_t==1)
			{
			$winowners=explode(";",$bat['t1']);
			$loserss=explode(";",$bat['t2'].";".$bat['t3']);
			}
			elseif ($win_t==2)
			{
			$winowners=explode(";",$bat['t2']);
			$loserss=explode(";",$bat['t1'].";".$bat['t3']);
			}
			elseif ($win_t==4)
			{
			$winowners=explode(";",$bat['t3']);
			$loserss=explode(";",$bat['t1'].";".$bat['t2']);
			}

			$winowners_bots=array();
			$loserss_bots=array();

			foreach($winowners as $k=>$v)
					{
						if ((empty($v)) OR ($v>_BOTSEPARATOR_) ) //если в масиве пусто или ид больше чем бот_ид
						{
						$winowners_bots[]=$winowners[$k];
						unset($winowners[$k]); //удал€ем из масива
						}
					}


			 	//фильтруем проигравших - все проиравшие
			 		foreach($loserss as $k=>$v)
					{
						if ((empty($v)) OR ($v>_BOTSEPARATOR_) ) //если в масиве пусто или ид больше чем бот_ид
						{
						$loserss_bots[]=$loserss[$k];
						unset($loserss[$k]); //удал€ем из масива
						}
					}

			//бои склонностей плюшки
			if (($bat['type']==3) and ($bat['coment']=='Ѕой склонностей') and ($bat['teams']=='Ѕой склонностей'))
			{

				if ($bat['nomagic']==0)
				{
				//бои с магией:
				if ($winowners)
					{
					//// победители: сортировка по урону, макс урон = 100% череп, 2й- 100% осколок, 3й - 80% осколок, 4й - 65% осколок, 5й - 50% осколок
					$priz=array();
					$priz[1][3002500]=100; //первое место череп 100%
					$priz[2][3002501]=100; //2е место осколок 100%
					$priz[3][3002502]=80; //3е место осколок 80%
					$priz[4][3002503]=65; //4е место осколок 65%
					$priz[5][3002503]=50; //5е место осколок 50%

					$get_info=mysql_query("select u.* from battle_dam_exp be LEFT JOIN users u on u.id=be.owner where be.battle='{$bat['id']}' and be.owner in (".implode(',',$winowners).") and be.damage>0 order by be.damage desc");
					log_dp_deb("Winners:select u.* from battle_dam_exp be LEFT JOIN users u on u.id=be.owner where be.battle='{$bat['id']}' and be.owner in (".implode(',',$winowners).") and be.damage>0 order by be.damage desc");
					$wkh=1;
					while ($gm = mysql_fetch_array($get_info))
						{
							$priza=$priz[$wkh];
							foreach($priza as $itemid=>$rshans)
								{
									if (mt_rand(0,99)<$rshans)
										{
											put_bonus_item($itemid,$gm,'');
											log_dp_deb("ITEM:{$itemid} /  Shans:{$rshans} / owner:{$gm['id']}");
										}
								}

						$wkh++;
						}
					}

				if ($loserss)
					{
					//11) проигравшие: сортировка по урону, макс урон = 100% осколок, 2й - 80% осколок, 3й - 50% осколок, 4й - 10% осколок, 5й - ничего
					$priz=array();
					$priz[1][3002501]=100;
					$priz[2][3002502]=80;
					$priz[3][3002503]=50;
					$priz[4][3002501]=10;
					$priz[5][3002503]=0;

					$get_info=mysql_query("select u.* from battle_dam_exp be LEFT JOIN users u on u.id=be.owner where be.battle='{$bat['id']}' and be.owner in (".implode(',',$loserss).") and be.damage>0 order by be.damage desc");
					log_dp_deb("Loserss:select u.* from battle_dam_exp be LEFT JOIN users u on u.id=be.owner where be.battle='{$bat['id']}' and be.owner in (".implode(',',$loserss).") and be.damage>0 order by be.damage desc");
					$wkh=1;
					while ($gm = mysql_fetch_array($get_info))
						{
							$priza=$priz[$wkh];
							foreach($priza as $itemid=>$rshans)
								{
									if (mt_rand(0,99)<$rshans)
										{
											put_bonus_item($itemid,$gm,'');
											log_dp_deb("ITEM:{$itemid} /  Shans:{$rshans} / owner:{$gm['id']}");
										}
								}

						$wkh++;
						}
					}


				}
				elseif ($bat['nomagic']>0)
				{
				//бои с без магии:

					if ($winowners)
					{
					$priz=array();
					//10) победители: сортировка по урону, макс урон = 50% череп, 2й- 50% осколок, 3й - 40% осколок, 4й - 33% осколок, 5й - 25% осколок
					$priz[1][3002500]=50; //первое место череп 50%
					$priz[2][3002501]=50; //2е место осколок 50%
					$priz[3][3002502]=40; //3е место осколок 40%
					$priz[4][3002503]=33; //4е место осколок 33%
					$priz[5][3002503]=25; //5е место осколок 25%

					$get_info=mysql_query("select u.* from battle_dam_exp be LEFT JOIN users u on u.id=be.owner where be.battle='{$bat['id']}' and be.owner in (".implode(',',$winowners).") and be.damage>0 order by be.damage desc");
					log_dp_deb("select u.* from battle_dam_exp be LEFT JOIN users u on u.id=be.owner where be.battle='{$bat['id']}' and be.owner in (".implode(',',$winowners).") and be.damage>0 order by be.damage desc");

					$wkh=1;
					while ($gm = mysql_fetch_array($get_info))
						{
							$priza=$priz[$wkh];
							foreach($priza as $itemid=>$rshans)
								{
									if (mt_rand(0,99)<$rshans)
										{
											put_bonus_item($itemid,$gm,'');
											log_dp_deb("ITEM:{$itemid} /  Shans:{$rshans} / owner:{$gm['id']}");
										}
								}

						$wkh++;
						}
					}

				if ($loserss)
					{
					$priz=array();
					//11) проигравшие: сортировка по урону, макс урон = 50% осколок, 2й - 40% осколок, 3й -25% осколок, 4й - 5% осколок, 5й - ничего
					$priz[1][3002501]=50;
					$priz[2][3002502]=40;
					$priz[3][3002503]=25;
					$priz[4][3002501]=5;
					$priz[5][3002503]=0;

					$get_info=mysql_query("select u.* from battle_dam_exp be LEFT JOIN users u on u.id=be.owner where be.battle='{$bat['id']}' and be.owner in (".implode(',',$loserss).") and be.damage>0  order by be.damage desc");
					log_dp_deb("select u.* from battle_dam_exp be LEFT JOIN users u on u.id=be.owner where be.battle='{$bat['id']}' and be.owner in (".implode(',',$loserss).") and be.damage>0  order by be.damage desc");
					$wkh=1;
					while ($gm = mysql_fetch_array($get_info))
						{
							$priza=$priz[$wkh];
							foreach($priza as $itemid=>$rshans)
								{
									if (mt_rand(0,99)<$rshans)
										{
											put_bonus_item($itemid,$gm,'');
											log_dp_deb("ITEM:{$itemid} /  Shans:{$rshans} / owner:{$gm['id']}");
										}
								}

						$wkh++;
						}
					}



				}
			}




			 if (
				 		($bat['type']!=30) AND //не бой в ЋјЅј’
				 		($bat['type']!=312) AND
				 		($bat['type']!=314) AND
				 		( $bat['type']!=11) AND //Ќ≈ –уины
				 		( $bat['type']!=10)  AND //Ќ≈ Ѕ—
				 		( $bat['type']!=22)  AND //Ќ≈ “еренировочные
				 		( $bat['type']!=1010)  AND //Ќ≈ Ѕ—
				 		( $bat['type']!=12)  AND //Ќ≈ –уины
				 		( $bat['type']!=15) AND // Ќ≈ бой в загороде против ботов - квестовый бой
				 		(!($bat['type']>=240 AND $bat['type'] <=260)) // Ќ≈ –исталка против ботов
				 	) //бои - где не качаетс€
			{

				$all_kol_p=count($winowners)+count($loserss)-(int)($bat['inf']); //общее кол. людей в бою

				if ($winowners)
				{

				//провер€ем сколько набили урона все команды
				$test_dem=true; //флаг проверки

				if (!(($bat['type']==601) OR ( $bat['type']==602) OR ( $bat['type']==603) OR ( $bat['type']==604) )) // дл€ этих боев несчитаем уроны и игнорил кол.людей
				{
					$get_dem_t=mysql_query("select sum(damage) as dm, u.battle_t as bt , count(u.id) as allpip   from battle_dam_exp bex LEFT JOIN users u ON bex.owner=u.id where u.battle='{$bat['id']}' group by battle_t");
					while ($gdmt = mysql_fetch_array($get_dem_t))
					{


						if  ($gdmt['dm']==0)
						{
						$test_dem=false;
						}
					}
				}

				if ($test_dem)
				    {
				    	$dragon_week=false;
    					 if (($bat['coment'] == "<b>Ѕой с ¬олнами ƒраконов</b>" ) and ($bat['type'] == 6) )
    					 	{
							//недел€ драконов
							$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=14"));
							if ($get_ivent['stat']==1)
							{
						    	$dragon_week=true;
							}
    					 	}

	 			    	//теперь проверка на кол. людей если бой  не 171 замки и не  40,41 - противосто€ние и не лордовские бои
	 			    	if ($bat['teams']=='Ѕой склонностей' )
			         	 {
			         	 $ddrkof=1; //100%
			         	 }
			         	 elseif ($dragon_week==true)
			         	 {
			         	 $ddrkof=1; //100%
			         	 }
				         elseif (($bat['coment'] == "<b> уча-мала</b>" ) OR ($bat['type'] == 313) )
			         	 {
			         	 $ddrkof=1; //100%
		                 	 }
				         elseif (($bat['coment'] == "<b>Ѕой с ѕ€тницо</b>" ) OR ($bat['coment'] == "<b>Ѕой с “ыквой</b>" ) )
			         	 {
			         	 $ddrkof=1; //100%
		        	 	 }
				         elseif ($bat['type'] == 61 ) // //јрена Ѕогов
			         	 {
			         	 $ddrkof=1; //100%
			         	 }
			         	elseif ($bat['status_flag']==4)
		        	 	{
		         		$ddrkof=1; //100%
			         	}
	 			  	else
					      if (($all_kol_p<5) and ( $bat['type']!=171 ) and ( $bat['type']!=40 ) and ( $bat['type']!=41 ) and ( $bat['type']!=601) and ( $bat['type']!=602 ) and ( $bat['type']!=603 ) and ( $bat['type']!=604)  and ($bat['teams']!='AFB') )
					  {
					      $ddrkof=0.1; //10%
      					      mysql_query("UPDATE `battle_runs_exp` SET `battle_flag`=0 WHERE battle='{$bat['id']}' "); //пишем в флаг  0 - говорим что меньше 5 чел
					 }
					 else
					  {
					 $ddrkof=1; //100%
					     //флаг по умолчанию 1;
				      	}

					$RKF=runs_battle_get_kof($bat);
					if ($RKF>0)
					{

							//запрашиваем сколько каждому начилисть на руну - ƒЋя ѕќЅ≈ƒџ
							$get_run_ex=mysql_query("select * from battle_runs_exp where battle='{$bat['id']}'  and owner in (".implode(',',$winowners).") ");
							while ($runex = mysql_fetch_array($get_run_ex))
							{
							$runex['point']=(int)$runex['point'];
								if ($runex['rkf_bonus']>0) {
									$RKFM=round(($RKF+($runex['rkf_bonus']/100)),2);
								} else {
									$RKFM = $RKF;
								}

								$rue[$runex['owner']]=round((($runex['point']*$RKFM)/$runex['runs'])*$ddrkof);

								//если есть – ћ
								if ($runex['rkm_bonus']>0)
									{
									//была баки€ стихий = делаем расчет от маг урона
									 $telo_mag_damage = mysql_fetch_array(mysql_query("SELECT mag_damage FROM battle_dam_exp WHERE battle='{$bat['id']}' and owner='{$runex['owner']}' "));
									 if ($telo_mag_damage['mag_damage']>0)
									 	{
									 	//ƒобавл€ем опыт рун бонус переводим в процент, умножаем маг урон на него и раскидываем на кол. рун
	 									$rue[$runex['owner']]+=round(($telo_mag_damage['mag_damage']*(0.01*$runex['rkm_bonus']))/$runex['runs']);
									 	}
									}
							}

							foreach($rue as $kown=>$vexp)
							{
								if ($vexp>0)
								{
								mysql_query_100("update oldbk.inventory set ups=ups+{$vexp} where owner='{$kown}' and (battle='{$bat['id']}' OR dressed=1) and type=30 and prototype not in (6018,6019,6020) "); //and add_time>ups
								$aff_r=mysql_affected_rows();
								//log_sql_deb("Battle:{$bat['id']}|{$aff_r}|{$ddrkof}|{$all_kol_p}|update oldbk.inventory inv set ups=ups+{$vexp} where owner='{$kown}' and (battle='{$bat['id']}' OR dressed=1) and type=30 ");

								//и пишем в прогресс общий опыт по рунам
								mysql_query("INSERT INTO oldbk.users_progress set owner='{$kown}', druns='".($vexp*$aff_r)."' ON DUPLICATE KEY UPDATE druns=druns+'".($vexp*$aff_r)."'");

								}
							}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
							if ($loserss)
							{
							//теперь дл€ проигравших если есть список
							$rue=array();
							$get_run_ex=mysql_query("select * from battle_runs_exp where battle='{$bat['id']}'  and owner in (".implode(',',$loserss).") ");
							while ($runex = mysql_fetch_array($get_run_ex))
							{
							$runex['point']=(int)$runex['point'];
							//при раздаче рунного опыта во всех бо€х (где даетс€ рунный опыт в соответствиии с таблицей –  http://oldbk.com/encicl/?/rk_table.html даем за проигрыш четверть –  выигрыша
								if ($runex['rkf_bonus']>0)
								{
									$RKFM=round(($RKF+($runex['rkf_bonus']/100)),2);
								} else
								{
									$RKFM = $RKF;
								}

								 if ($bat['teams']=='Ѕой склонностей' )
								 {
									$rue[$runex['owner']]=round(((($runex['point']*$RKFM)/$runex['runs'])*$ddrkof)/1.5);
								 }
								 elseif (($bat['coment'] == "<b> уча-мала</b>" ) OR ($bat['type'] == 313) )
								 {
									$rue[$runex['owner']]=round(((($runex['point']*$RKFM)/$runex['runs'])*$ddrkof)/2);
								 }
								 elseif (($bat['coment'] == "<b>Ѕой с ѕ€тницо</b>" ) OR ($bat['coment'] == "<b>Ѕой с “ыквой</b>" ))
								 {
									$rue[$runex['owner']]=round(((($runex['point']*$RKFM)/$runex['runs'])*$ddrkof));
								 }
								 elseif ($bat['type'] == 61 ) //јрена Ѕогов
								 {
									$rue[$runex['owner']]=round((($runex['point']*$RKFM)/$runex['runs'])*$ddrkof);
								 }
								 elseif ($bat['status_flag'] == 4)
									{
									$rue[$runex['owner']]=round(((($runex['point']*$RKFM)/$runex['runs'])*$ddrkof));
									}
								else
									{
									$rue[$runex['owner']]=round(((($runex['point']*$RKFM)/$runex['runs'])*$ddrkof)/4);
									}


								//если есть – ћ
								if ($runex['rkm_bonus']>0)
									{
									//была баки€ стихий = делаем расчет от маг урона
									 $telo_mag_damage = mysql_fetch_array(mysql_query("SELECT mag_damage FROM battle_dam_exp WHERE battle='{$bat['id']}' and owner='{$runex['owner']}' "));
									 if ($telo_mag_damage['mag_damage']>0)
									 	{
									 	//ƒобавл€ем опыт рун бонус переводим в процент, умножаем маг урон на него и раскидываем на кол. рун

										 	 if (($bat['type']==601) OR ( $bat['type']==602) OR ( $bat['type']==603) OR ( $bat['type']==604) )
										 	 {
												$rue[$runex['owner']]+=round((($telo_mag_damage['mag_damage']*(0.01*$runex['rkm_bonus']))/$runex['runs']));
										 	 }
										 	elseif ($bat['teams']=='Ѕой склонностей' )
											 {
												$rue[$runex['owner']]+=round((($telo_mag_damage['mag_damage']*(0.01*$runex['rkm_bonus']))/$runex['runs'])/1.5);
											 }
											 elseif (($bat['coment'] == "<b> уча-мала</b>" ) OR ($bat['type'] == 313) )
											 {
												$rue[$runex['owner']]+=round((($telo_mag_damage['mag_damage']*(0.01*$runex['rkm_bonus']))/$runex['runs'])/2);
											 }
											 elseif (($bat['coment'] == "<b>Ѕой с ѕ€тницо</b>" ) OR ($bat['coment'] == "<b>Ѕой с “ыквой</b>" ) )
											 {
												$rue[$runex['owner']]+=round((($telo_mag_damage['mag_damage']*(0.01*$runex['rkm_bonus']))/$runex['runs']));
											 }
											 elseif ($bat['type'] == 61 ) //јрена Ѕогов
											 {
												$rue[$runex['owner']]+=round((($telo_mag_damage['mag_damage']*(0.01*$runex['rkm_bonus']))/$runex['runs']));
											 }
											 elseif ($bat['status_flag'] == 4)
												{
												$rue[$runex['owner']]+=round((($telo_mag_damage['mag_damage']*(0.01*$runex['rkm_bonus']))/$runex['runs']));
												}
									 		else
									 		{
										 		// остальные /4
		 										$rue[$runex['owner']]+=round((($telo_mag_damage['mag_damage']*(0.01*$runex['rkm_bonus']))/$runex['runs'])/4);
	 										}
									 	}
									}

							}
							foreach($rue as $kown=>$vexp)
							{
								if ($vexp>0)
								{
								mysql_query_100("update oldbk.inventory set ups=ups+{$vexp} where owner='{$kown}' and (battle='{$bat['id']}' OR dressed=1) and type=30 ");//and add_time>ups
								$aff_r=mysql_affected_rows();
								//log_sql_deb("Battle:{$bat['id']}|{$aff_r}|{$ddrkof}|{$all_kol_p}|update oldbk.inventory inv set ups=ups+{$vexp} where owner='{$kown}' and (battle='{$bat['id']}' OR dressed=1) and type=30 ");
								//и пишем в прогресс общий опыт по рунам
								mysql_query("INSERT INTO oldbk.users_progress set owner='{$kown}', druns='".($vexp*$aff_r)."' ON DUPLICATE KEY UPDATE druns=druns+'".($vexp*$aff_r)."'");
								}
							}
							}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					}
				   }
				    else
				      {
				      			log_sql_deb("Battle:{$bat['id']}| Ќе дало опыт на руны. 0 урона в одной из команд");
				      			//удал€ем данные о рунном опыте - шоб небыло системки в fbattle
				      			 mysql_query("DELETE FROM `battle_runs_exp` WHERE `battle`='{$bat['id']}' ");
				      }

				}

			}

/////////////////////////////////////////////////////////////////////////////////////
///обработка свитков захвата монстров

		if (($bat['type']==30) OR ($bat['type']==31)  OR ($bat['coment'] =='Ѕой с »счадием ’аоса') OR ($bat['coment'] == '<b>Ѕой с ¬олнами ƒраконов</b>'))   //30-лаба 31-тип бо€ дл€ тестов
		//if  ($bat['type']==31)
			{

										$string_conf_log[122121]=1;
										$string_conf_log[122122]=2;
										$string_conf_log[122123]=3;
										$string_conf_log[122124]=4;


				//выгребаем юзы только дл€ живых
				$get_all_uses=mysql_query("select u.*, gb.id as gid, gb.chanse, gb.name_bot, gb.proto_bot, gb.level_bot, gb.used_proto, gb.idbot from get_lock_bots gb LEFT JOIN users u ON u.id=owner where gb.battle='{$bat['id']}' and u.hp>0  order by  gb.chanse desc");



				if (mysql_num_rows($get_all_uses) > 0)
					{
					$locked_bots=array();
					//грузим данные
					while($lobot = mysql_fetch_assoc($get_all_uses))
						{
						$locked_bots[$lobot['idbot']][]=$lobot;
						}


					foreach($locked_bots as $idbot=>$dd)
							{
								foreach($dd as $k=>$dinfo)
									{

										if (get_chanse($dinfo['chanse']))
											{
											// у бираем флаг бо€ из таблика - помеча€ что она выиграла и будет выдача свитка
											mysql_query("UPDATE `get_lock_bots` SET `item_id`=1 WHERE `id`='{$dinfo['gid']}' ");
												if (mysql_affected_rows()>0)
													{
													//echo "Owner: {$dinfo['owner']} захватил бота {$idbot} шанс {$dinfo['chanse']} \n ";
													if ($string_conf_log[$dinfo['used_proto']]!='')
														{
														$outstr=$string_conf_log[$dinfo['used_proto']];
														}
														else
														{
														$outstr=0;
														}
													$sstt="2".$outstr."50";
													$sstt=(int)$sstt;
													$all_bots_namea= "<B>{$dinfo['name_bot']}</B> [{$dinfo['level_bot']}]<a href=inf.php?{$dinfo['proto_bot']} target=_blank><IMG SRC=http://i.oldbk.com/i/inf.gif WIDTH=12 HEIGHT=11 ALT=\"»нф. о {$dinfo['name_bot']}\"></a>";
													$btext=str_replace(':','^',$all_bots_namea);

													addlog($bat['id'],"!:X:".time().':'.nick_new_in_battle($dinfo).':'.($dinfo[sex]+$sstt).":".trim($btext)."\n");
													break; //выход из цикла по боту т.к. он уже разигран
													}
											}
									}
							}
					}
			}



//однодневна€ акци€ - раздача 30 пушек хаоса - дл€ победителей

/*
одноразовый ивент
 if ($bat['id']==12645905)
  {
  do_chaos_items_event($win_t,$bat);
  }
*/

//ага, любой статусный бой, кроме боев против исчади€ -ƒен
// ставим переменну дл€ подсчета статусных и выиграных статусных битв
	  if (($bat['coment'] =='Ѕой с »счадием ’аоса') OR ($bat['coment'] =='<b>Ѕой с ƒухом ћерлина</b>'))
	  {
	  	if ($winowners) {
			$sql = 'UPDATE oldbk.map_var SET val = val + 1 WHERE owner IN ('.implode(",",$winowners).') AND var = "q32s6"';
			mysql_query_100($sql);


			$q = mysql_query('SELECT vr.owner as zz FROM oldbk.map_var vr LEFT JOIN battle_dam_exp be ON vr.owner=be.owner and be.battle='.$bat['id'].'  WHERE vr.owner  IN ('.implode(",",$winowners).')   AND vr.var = "q31" AND vr.val = 11 AND be.damage>0');
			if (mysql_num_rows($q) > 0)
			{
				while($u = mysql_fetch_assoc($q))
				{
					mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`)
								VALUES(
									'.$u['zz'].',
									"q31a",
									"1"
								)
								ON DUPLICATE KEY UPDATE
									`val` = val + 1
					');
				}
			}
		}
	}





  if (($bat['status_flag'] > 0) and ($bat['coment'] !='Ѕой с »счадием ’аоса') and ($bat['coment'] !='<b>Ѕой с ƒухом ћерлина</b>')  )
   {
   $WIN_ST_COUNT=' `stbat`=`stbat`+1 , `winstbat`=`winstbat`+1 , ';
   $LOSE_ST_COUNT=' `stbat`=`stbat`+1 , ';
   }
   else
   {
   $WIN_ST_COUNT='';
   $LOSE_ST_COUNT='';
   }


	if (count($winowners) && ($bat['CHAOS']>0) and ($bat['type']!=5) and ($bat['type']!=4) and ($bat['type']!=308) and ($bat['type']!=304)) {
		$q = mysql_query('SELECT * FROM oldbk.map_var WHERE owner IN ('.implode(",",$winowners).') AND var = "q31" AND val = 11');
		if (mysql_num_rows($q) > 0) {
			while($u = mysql_fetch_assoc($q)) {
				mysql_query('INSERT INTO oldbk.map_var (`owner`,`var`,`val`)
							VALUES(
								'.$u['owner'].',
								"q31v",
								"1"
							)
							ON DUPLICATE KEY UPDATE
								`val` = val + 1
				');
			}
		}
	}


///
$check_quest=array();
//эти данные должны быть одинаковы в fsystem
$EXP_TO_LOSE=0; //1- вкл / 0-выкл

include "ny_events.php";

if(time()>$ny_events['ngloseexpstart'] && time()< $ny_events['ngloseexpend']) {
	$EXP_TO_LOSE=1;
}

if(time()>$ny_events['hbloseexpstart'] && time()< $ny_events['hbloseexpend']) {
	$EXP_TO_LOSE=1;
}



     $date=date("l", time());
    if($date=='Saturday' || $date=='Sunday')
    {
    	$EXP_TO_LOSE=1;
    }

		$add_voinst_win='';
		$add_voinst_lose='';

		/// обрабатываем воинственность в статусных битвах всех! но не клан вар - отключено 11/06/2013
	/*	if (($bat[status_flag] > 0 ) and  ($bat[war_id] == 0 ) )
		{
		$add_voinst_win=' voinst=voinst+ifnull((FLOOR(@EE*0.01)),0) , '; //личные начислени€
		$add_voinst_lose=' voinst=voinst+ifnull((FLOOR(@EE*0.001)),0) , '; //личные начислени€
			if ($win_t==1)
				{
				add_voinst(1,0.01,$bat['id'],0); // 1-€ победила 2% за каждого
				add_voinst(2,0.001,$bat['id'],0); //2-€ проиграла 1%  за каждого
				if ($bat['t3']!='')
					{
					add_voinst(3,0.001,$bat['id'],0); //2-€ проиграла 1%  за каждого
					}
				}
			else
			if ($win_t==2)
				{
				add_voinst(2,0.01,$bat['id'],0); // 2-€ победила 2% за каждого
				add_voinst(1,0.001,$bat['id'],0); //1-€ проиграла 1% за каждого
				if ($bat['t3']!='')
					{
					add_voinst(3,0.001,$bat['id'],0); //1-€ проиграла 1% за каждого
					}
				}
			else
			if ($win_t==4)
				{
				add_voinst(3,0.01,$bat['id'],0); // 3-€ победила 2% за каждого
				add_voinst(1,0.001,$bat['id'],0); //1-€ проиграла 1% за каждого
				add_voinst(2,0.001,$bat['id'],0); //2-€ проиграла 1% за каждого

				}
			 else
			 {
				add_voinst(1,0.001,$bat['id'],0); // ничь€ 1% за каждого
				add_voinst(2,0.001,$bat['id'],0); //ничь€ 1% за каждого
				if ($bat['t3']!='')
					{
					add_voinst(3,0.001,$bat['id'],0); //ничь€ 1% за каждого
					}

			 }
		}
		else */
		 if ((  ($bat['type'] ==140) OR  ($bat['type'] ==141) OR  ($bat['type'] ==150) OR  ($bat['type'] ==151) ) and  ($bat['war_id'] > 0 ) )
		 {
		 //новые клан вары
log_kv_deb("D1_battle:{$bat['id']} type {$bat['type']}  ");
		 		 	 		if (($bat['type'] ==140) OR ($bat['type'] ==150) )  // обычный
						  	{
						 	$win_voi_koef=0.01;
						 	$lose_voi_koef=0.001;
						 	}
						 	else  // клановый бой большой 30 чел и больше
						  	{
						 	$win_voi_koef=0.011;
						 	$lose_voi_koef=0.0011;
						 	$EXP_WIN=1.1; //+10%
		 					}

			$add_voinst_win=' voinst=voinst+150+ifnull((FLOOR(@EE*'.$win_voi_koef.')),0) , '; //личные начислени€
			$add_voinst_lose=' voinst=voinst+ifnull((FLOOR(@EE*'.$lose_voi_koef.')),0) , '; //личные начислени€

	$gsql="select u.id, u.login, u.level,  u.battle_t, u.naim, u.naim_war, d.damage, d.`exp`  , (select if ((base_klan>0),base_klan,id) as id  from oldbk.clans where short=u.klan) as clan_id from users as u LEFT JOIN battle_dam_exp as d ON (owner=u.id and d.battle=u.battle) where u.battle='{$bat['id']}' ";
		 		$get_all_data=mysql_query($gsql);
	log_kv_deb($gsql);

				$insert_array=array();
				$stor_array=array();
				$winner_array=array();
				$rem_level=0;
		 		while ($rowdata = mysql_fetch_array($get_all_data))
		 				{
		 				if ($rem_level==0) { $rem_level=$rowdata['level']; } //запоминаем уровень чара - нужен дл€ вставки если война дуэльна€
		 				//если тело наемник
			 				 if (($rowdata['naim']> 0) AND ($rowdata['naim_war']==$bat['war_id']) )
			 				 {
			 				 //наемник билс€ за клан
			 				 //подставл€ем в его ид = ид клана закоторый билс€ наемник
			 				 $rowdata['clan_id']=$rowdata['naim'];
			 				 }


		 					if ($win_t==$rowdata['battle_t'])
		 						{
		 						//разбираемс€ с победител€ми
		 						if ($insert_array[$rowdata['clan_id']]==0) { $insert_array[$rowdata['clan_id']]=150; $winner_array[$rowdata['clan_id']]=1;  } //клан победител€ если там 0 то добавл€ем бонусные 150 очков
		 						//суммируем воинственность клана взависимости от набитога персом*на коэф.победител€
		 						$insert_array[$rowdata['clan_id']]+=(int)($rowdata['exp']*$win_voi_koef);
		 						}
		 						else
		 						{
		 						//проигравшие или ничь€
		 						$insert_array[$rowdata['clan_id']]+=(int)($rowdata['exp']*$lose_voi_koef);
		 						}

	 					log_kv_deb(print_r($rowdata,true));
		 				}


				//загружаем  данные по войне
				$wardata=mysql_fetch_array(mysql_query("select  * from oldbk.clans_war_new where id='{$bat['war_id']}' "));
				//записываем кланам их стороны
				$stor_array[$wardata['agressor']]='agr';
				$stor_array[$wardata['defender']]='def';

	log_kv_deb(print_r($stor_array,true));

		 		 if (  ($bat['type'] ==150) OR  ($bat['type'] ==151) )
		 		 {
		 		 //јль€нсовые - затираем уровень
		 		 $rem_level=0;
		 		 //дл€ аль€нсовых делаем запрос аль€нсов
		 		 $allydata=mysql_query("select clanid, (if (agressor>0,'agr','def') ) as stor from oldbk.clans_war_new_ally where warid='{$bat['war_id']}' ");
		 		 	while ($rowdata = mysql_fetch_array($allydata))
		 		 		{
						$stor_array[$rowdata['clanid']]=$rowdata['stor'];
		 		 		}
		 		 }

		 		 //собираем инсерт
		 		 $ins='INSERT INTO `oldbk`.`clans_war_new_voin` (clan_id,war_id,stor,level,voin,winned) VALUES ';

	 		 	foreach ($insert_array as $clan_id=>$val)
	 		 			{
	 		 	$ins.="(".$clan_id.",".$bat['war_id'].",'".$stor_array[$clan_id]."',".$rem_level.",".$val.", ".(int)($winner_array[$clan_id])." ),";
	 		 	//попутно делаем апдейты дл€ общего зачета клана
	 		 	mysql_query("update oldbk.clans set voinst = voinst +".$val." WHERE id='{$clan_id}' ");
	 		 			}

		 		$ins = substr($ins,0,strlen($ins)-1);

		 		$ins.='	ON DUPLICATE KEY UPDATE voin=voin+VALUES(voin), winned=winned+VALUES(winned) ';

				log_kv_deb($ins);

		 		mysql_query($ins); // ƒелаем масс инсерт / или апдейт с добавлением воинственности данных о полученой воинственности







		 }
		else
		 if ((  ($bat['type'] ==100) OR  ($bat['type'] ==101) ) and  ($bat['war_id'] > 0 ) )
		 {

		  if ($bat['type'] ==100) // обычный клановый бой
		  	{
		 	$win_voi_koef=0.01;
		 	$lose_voi_koef=0.001;
		 	}
		 	elseif ($bat['type'] ==101)  // клановый бой большой 30 чел и больше
		  	{
		 	$win_voi_koef=0.011;
		 	$lose_voi_koef=0.0011;
		 	$EXP_WIN=1.1; //+10%
		 	}


		   $get_info_war=mysql_fetch_array(mysql_query("select * from oldbk.clans_war_2 where war_id={$bat['war_id']} LIMIT 1; "));
		   if($get_info_war['def_active']==1)
		   	{
		   	//обоюдна€
			$add_voinst_win=' voinst=voinst+150+ifnull((FLOOR(@EE*'.$win_voi_koef.')),0) , '; //личные начислени€
			$add_voinst_lose=' voinst=voinst+ifnull((FLOOR(@EE*'.$lose_voi_koef.')),0) , '; //личные начислени€
		   	// за каждого кланера участвовавшего в кланбитве (при обоюдной войне) при победе в этом бою клан получает 2 очко воинственности, при поражении 1 очко.
		   	if ($win_t==1)
				{
				add_voinst(1,$win_voi_koef,$bat['id'],$bat['war_id'],150); //победа агрессора
				add_voinst(2,$lose_voi_koef,$bat['id'],$bat['war_id']); //поражение дефендера

				if ($bat['t3']!='')
					{
				add_voinst(3,$lose_voi_koef,$bat['id'],$bat['war_id']); //поражение дефендера
					}

				}
			else
			if ($win_t==2)
				{
				add_voinst(2,$win_voi_koef,$bat['id'],$bat['war_id'],150); // победа дефендера
				add_voinst(1,$lose_voi_koef,$bat['id'],$bat['war_id']); //поражение агрессора

				if ($bat['t3']!='')
					{
				add_voinst(3,$lose_voi_koef,$bat['id'],$bat['war_id']); //поражение дефендера
					}

				}
			else
			if ($win_t==4)
				{
				add_voinst(3,$win_voi_koef,$bat['id'],$bat['war_id'],150); // победа дефендера
				add_voinst(1,$lose_voi_koef,$bat['id'],$bat['war_id']); //поражение агрессора
				add_voinst(2,$lose_voi_koef,$bat['id'],$bat['war_id']); //поражение агрессора
				}
			 else
			 {
				add_voinst(1,$lose_voi_koef,$bat['id'],$bat['war_id']); // ничь€
				add_voinst(2,$lose_voi_koef,$bat['id'],$bat['war_id']); //ничь€
				if ($bat['t3']!='')
				{
					add_voinst(3,$lose_voi_koef,$bat['id'],$bat['war_id']); //ничь€
				}

			 }
		   }
		    elseif($get_info_war['def_active']==0)
		       {
		       //односторонн€€
			$add_voinst_win=' voinst=voinst+150+ifnull((FLOOR(@EE*'.$win_voi_koef.')),0) , '; //личные начислени€
		        $add_voinst_lose=''; // нету
		       //   если война односторонн€€ то за поражение клан не получает воинственности

		       if ($win_t==1)
				{
				add_voinst(1,$win_voi_koef,$bat['id'],$bat['war_id'],150); // победил агрессор
				//add_voinst(2,1,$bat['id'],$bat[war_id]); //неполучает
				}
			else
			if ($win_t==2)
				{
				//add_voinst(2,$win_voi_koef,$bat['id'],$bat[war_id],150); // победил  дефендер - тоже не начил€ем , хз
				//add_voinst(1,1,$bat['id'],$bat[war_id]); //неполучаем
				}
			else
			if ($win_t==4)
				{
				add_voinst(3,$win_voi_koef,$bat['id'],$bat['war_id'],150); // победил  дефендер
				//add_voinst(1,1,$bat['id'],$bat[war_id]); //неполучаем
				}
			 else
			 {
				//add_voinst(1,1,$bat['id'],$bat[war_id]); // ничь€ нихто не получает
				//add_voinst(2,1,$bat['id'],$bat[war_id]); //ничь€ нихто не получает

			 }

		       }
		 }





//добавл€ем проверку на наличие квестов боевого типа.
 if (!($EXP_WIN)) //если не установленно то ставим поумолчанию
 {
	$EXP_WIN=1; // 100%  стороне победившей
  }
$WIN_REP=''; // дополнительна€ строка дл€ начислени€ репутаций при определюнных услови€х дл€ победител€
//echo "1<br>";

//правка Bred - запрос тимы проигравших != победившим
if ($win_t == 4)
	{
	$winner_t = 3;
	}
	else
	{
	$winner_t = $win_t;
	}

		//руины
		if ($btype == 12) {

		// бой за сокровищницу
		$ruin_conf='/www/'.CITY_DOMEN.'/ruines_config.php';
		if (file_exists($ruin_conf)) {
			require_once($ruin_conf);
		}


		// узнаЄм номер карты руин
		$q = mysql_query_100('SELECT `ruines`,`battle_t`,`room` FROM `users` WHERE in_tower = 2 AND `battle` = '.$bat['id'].' LIMIT 1') ;
		if (mysql_num_rows($q) > 0) {
			$mapid = mysql_fetch_assoc($q) ;

			$q = mysql_query_100('SELECT * FROM `ruines_map` WHERE id = '.$mapid['ruines']) ;
			if (mysql_num_rows($q) > 0) {
				$map = mysql_fetch_assoc($q) ;

				if ($mapid['battle_t']==3) {$mapid['battle_t']=4;} //хз как там ”марти - узнать

				if ($win_t == $mapid['battle_t']) {
					// победили люди

					// пишем что больше бой не стартуем
					mysql_query_100('UPDATE `ruines_map` SET `sanct` = "-1" WHERE `id` = '.$map['id']) ;

					$log = '<span class=date>'.date("d.m.y H:i").'</span> <b>—тражи —окровищницы повержены</b>.<BR>';
					mysql_query_100('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']);

				} else {
					// люди проиграли - всех на кладбище
					mysql_query_100('UPDATE `ruines_map` SET `sanct` = "0" WHERE `id` = '.$map['id']) ;

					$sql = 'UPDATE `users` SET `room` = '.($map['rooms']+75).' WHERE in_tower = 2 AND battle = '.$bat['id'];
					mysql_query_100($sql) ;

					// увеличиваем счЄтчик проигрышей в карте дл€ юзеров
					$sql = 'SELECT * FROM `users` WHERE in_tower = 2 AND `battle` = '.$bat['id'];
					$q = mysql_query_100($sql) ;
					$ids = "";
					$val = "";
					$uinfo = array();
					while($u = mysql_fetch_assoc($q)) {
						$ids .= $u['id'].',';
						$val .= '("'.$u['id'].'","rdeath","1"),';
						$uinfo[$u['id']] = $u;
					}
					if (strlen($val)) {
						$ids = substr($ids,0,strlen($ids)-1);
						$val = substr($val,0,strlen($val)-1);

						mysql_query_100('INSERT INTO `ruines_var` (`owner`,`var`,`val`)
									VALUES '.$val.'
									ON DUPLICATE KEY UPDATE
										`val` = `val` + 1
						') ;


						// пишем в лог если есть мЄртвые боты

						// вешаем путы взависимости от колва смерти
						$q = mysql_query_100('SELECT * FROM `ruines_var` WHERE `owner` IN ('.$ids.') AND `var` = "rdeath"') ;
						while($var = mysql_fetch_assoc($q)) {
							// лог
							$u = $uinfo[$var['owner']];
							$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$u['id_grup']].'>'.nick_hist($u).'</font> повержен и находитс€ на <b> ладбище</b>.<BR>';
							mysql_query_100('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']);

							mysql_query_100('DELETE FROM `effects` WHERE `type` = 10 AND `owner` = '.$var['owner']);

							if ($var['val'] == 1) {
								// вешаем путы на 10 минут
								mysql_query_100('INSERT INTO `effects` (`owner`,`name`,`time`,`type`) VALUES ("'.$var['owner'].'","ѕуты",'.(time()+(60*10)).',10)');
							} else {
								// вешаем путы на 1000 часов
								mysql_query_100('INSERT INTO `effects` (`owner`,`name`,`time`,`type`) VALUES ("'.$var['owner'].'","ѕуты",'.(time()+(60*60*1000)).',10)');

								// раздеваем
								undressall($var['owner']);

								require_once('memcache.php');

								$qa = mysql_query_100('SELECT * FROM oldbk.inventory WHERE owner = '.$var['owner'].' AND bs_owner = 2 AND type != 200');
								while($di = mysql_fetch_assoc($qa)) {
									$iroom = mt_rand(1,74)+$map['rooms'];
									mysql_query_100('INSERT `ruines_items` (`type`,`iteam_id`, `name`, `img`, `room`, `extra`,`present`,`durability`)
										VALUES (
											"0",
											"'.$di["prototype"].'",
											"'.mysql_real_escape_string($di['name']).'",
											"'.mysql_real_escape_string($di['img']).'",
											"'.$iroom.'",
											"'.$di['bs'].'",
											"'.mysql_real_escape_string($di['present']).'",
											"'.$di['duration'].'"
										)
									') ;

									$id = mysql_insert_id();
									$sql = 'SELECT img FROM ruines_items WHERE id = '.$id;
									$cache = array();
									$cache[0] = array(
										'img' => mysql_real_escape_string($di['img']),
									);
									setCache(md5("mysql_query".$sql),$cache,3*3600);
								}

								// удал€ем шмот из его инвентар€
								mysql_query_100('DELETE FROM oldbk.`inventory` WHERE bs_owner = 2 AND `owner` = '.$var['owner']) ;

							}
						}
					}

				}

			}
		}
	} else if ($btype == 601 || $btype == 602 || $btype == 603 || $btype == 604)
	{
		$u = mysql_fetch_assoc(mysql_query('SELECT * FROM users WHERE battle = '.$bat['id']));
		$u['id']=(int)$u['id'];
		if ($u['id']>0)
		{
		mysql_query('UPDATE oldbk.users SET winstbat = winstbat + 20 WHERE id = '.$u['id']);
		addchp('<font color=red>¬нимание!</font> “ы был достаточно отважен и рискнул сразитьс€ с Ћордом –азрушителем! “во€ героическа€ смерть в битве с великим јнгелом была оценена по достоинству и в твой личный список побед добавлены еще 20 ¬еликих —ражений!','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
		}
	} else if ($btype == 1010) {
		// тут нова€ бс
		if ($win_t == 1) {
			$winlist = explode(";",$bat['t1']);
			$loselist = explode(";",$bat['t2'].";".$bat['t3']);
		} elseif ($win_t == 2) {
			$winlist = explode(";",$bat['t2']);
			$loselist = explode(";",$bat['t1'].";".$bat['t3']);
		} else {
			$winlist = explode(";",$bat['t3']);
			$loselist = explode(";",$bat['t1'].";".$bat['t2']);
		}

		reset($winlist);
		while(list($k,$v) = each($winlist)) {
			if (empty($v)) unset($winlist[$k]);
		}

		reset($loselist);
		while(list($k,$v) = each($loselist)) {
			if (empty($v)) unset($loselist[$k]);
		}


		// узнаЄм карту бс
		$q = mysql_query('SELECT * FROM dt_map WHERE active = 1');
		$map = mysql_fetch_assoc($q);

		$iroom = 0;

		$chkl = array_merge($winlist,$loselist);

		// провер€ем есть ли арх в победившей команде
		$archlist = array();
		$q = mysql_query('SELECT * FROM users_clons WHERE id IN ('.implode(",",$chkl).') and bot_room > 0 AND id_user = 84');
		$isarchwin = 0;
		while($ar = mysql_fetch_assoc($q)) {
			if (!$isarchwin && in_array($ar['id'],$winlist)) {
				if ($ar['hp'] > 0) $isarchwin = $ar['id'];
			}
			if (!$iroom) $iroom = $ar['bot_room'];
			$archlist[$ar['id']] = $ar;
		}

		// узнаем в какой комнате был замес если на нашли у ботов
		$chklusers = array();
		$q = mysql_query('SELECT * FROM users WHERE id IN ('.implode(",",$chkl).')');

		while($uu = mysql_fetch_assoc($q)) {
			$chklusers[$uu['id']] = $uu;
			$iroom = $uu['room'];
		}


		// выкидываем из бо€ ботов
		mysql_query_100('UPDATE users_clons SET battle = 0, fullhptime = '.time().' WHERE battle = '.$bat['id'].' and id_user = 84');

		// выкидываем из бс проигравших или с хп = 0
		$loselistcp = $loselist;
		reset($winlist);
		while(list($k,$v) = each($winlist)) {
			if (isset($chklusers[$v]['hp']) && $chklusers[$v]['hp'] < 1) $loselist[] = $v;
			if (isset($archlist[$v]['hp']) && $archlist[$v]['hp'] < 1) $loselist[] = $v;
		}

		// получаем профил€
		$realprofile = array();
		$q = mysql_query('SELECT * FROM dt_realchars WHERE owner IN ('.implode(",",$loselist).')');
		while($pr = mysql_fetch_assoc($q)) {
			$realprofile[$pr['owner']] = $pr;
		}


		$droplist = array();
		$checklist = array();

		undressallfast($loselist);

		reset($loselist);
		while(list($k,$v) = each($loselist)) {
			if (empty($v)) continue;
			if($v > _BOTSEPARATOR_ && !isset($archlist[$v])) {
				// не арх
				continue;
			}

			$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$v.' AND bs_owner = 15');
			while($itm = mysql_fetch_assoc($q)) {
				if ($itm['prototype'] == 114) $checklist[$itm['id']] = $itm;
				$droplist[$itm['id']] = $itm;
			}

			if ($v > _BOTSEPARATOR_) {
				// умер бот - снимаем весь шмот, удал€ем из базы
				mysql_query_100('DELETE FROM users_clons WHERE id = '.$v);
				$sql = 'UPDATE `dt_log` SET `log` = CONCAT(`log`,\''."<span class=date>".date("d.m.y H:i")."</span> ".s_nick($archlist[$v]['id'],$archlist[$v]['align'],$archlist[$v]['klan'],$archlist[$v]['login'],$archlist[$v]['level'])." повержен и выбывает из турнира<BR>".'\') WHERE `dt_id` = '.$map['id'];
				mysql_query($sql);
			} else {
				// умерло тело - раздеваем, возвращаем статы и вэк из бс

				mysql_query('DELETE FROM effects WHERE (type = 10 or type = 11 or type = 12 or type = 13 or type = 14) AND owner = '.$v);

				// статы возвращаем и выстав€ем рум
				$q2 = mysql_query("select * from effects where owner='{$v}' and type>=1001 and type<=1003");
				$hp_bonus = mysql_fetch_array($q2);
				if ($hp_bonus['id'] > 0) {
					// эффект еще есть
				} else {
					//эфекта такого уже нет!

					$realprofile[$v]['maxhp'] = $realprofile[$v]['maxhp']-$realprofile[$v]['bpbonushp'];
					$realprofile[$v]['bpbonushp'] = 0;

					if ($realprofile[$v]['hp'] > $realprofile[$v]['maxhp']) {
						$realprofile[$v]['hp'] = $realprofile[$v]['maxhp'];
					}
				}

				$hp = $realprofile[$v]['vinos']*6 + ($realprofile[$v]['bpbonushp']);

				mysql_query_100('UPDATE `users` SET
						`sila` = "'.($realprofile[$v]['sila']+$realprofile[$v]['bpbonussila']).'",
						`lovk` = "'.$realprofile[$v]['lovk'].'",
						`inta` = "'.$realprofile[$v]['inta'].'",
						`vinos` = "'.$realprofile[$v]['vinos'].'",
						`intel` = "'.$realprofile[$v]['intel'].'",
						`mudra` = "'.$realprofile[$v]['mudra'].'",
						`stats` = "'.$realprofile[$v]['stats'].'",
						`noj` = "'.$realprofile[$v]['noj'].'",
						`mec` = "'.$realprofile[$v]['mec'].'",
						`topor` = "'.$realprofile[$v]['topor'].'",
						`dubina` = "'.$realprofile[$v]['dubina'].'",
						`mfire` = "'.$realprofile[$v]['mfire'].'",
						`mwater` = "'.$realprofile[$v]['mwater'].'",
						`mair` = "'.$realprofile[$v]['mair'].'",
						`mearth` = "'.$realprofile[$v]['mearth'].'",
						`mlight` = "'.$realprofile[$v]['mlight'].'",
						`mgray` = "'.$realprofile[$v]['mgray'].'",
						`mdark` = "'.$realprofile[$v]['mdark'].'",
						`master` = "'.$realprofile[$v]['master'].'",
						`mana` = "'.$realprofile[$v]['mana'].'",
						`maxmana` = "'.$realprofile[$v]['mana'].'",
						`maxhp` = "'.$hp.'",
						`hp` = "'.$hp.'",
						`bpbonussila` = '.$realprofile[$v]['bpbonussila'].',
						`bpbonushp` = '.$realprofile[$v]['bpbonushp'].', `room` = 10000, in_tower = 0 WHERE `id` = '.$v
				);

				$sql = 'UPDATE `dt_log` SET `log` = CONCAT(`log`,\''."<span class=date>".date("d.m.y H:i")."</span> ".nick3($v)." повержен и выбывает из турнира<BR>".'\') WHERE `dt_id` = '.$map['id'];
				mysql_query($sql);
				$q = mysql_query('SELECT * FROM users WHERE id = '.$v);
				$row = mysql_fetch_assoc($q);
				addchp ('<font color=red>¬нимание!</font> ¬ы выбыли из турнира Ѕашни —мерти.', '{[]}'.$row['login'].'{[]}');

				if (mt_rand(0,100)<15)
					{
						DropBonusItem(112003,$row,"”дача"," оллекци€ є2: јнгельска€ поступь",0,1,20,true); // арта јрхивариуса выдаетс€ за участие в Ѕ— - шанс 15% за участие (в зачет идет “ќЋ№ ќ участие, победа не важна)
					}

			}
		}

		if ($isarchwin) {
			// провер€ем был ли противник в противоположной команде с чеком
			$qchk = mysql_query('SELECT * FROM inventory WHERE owner = '.$isarchwin.' AND prototype = 114 and present != ""');
			if (mysql_num_rows($qchk) > 0) {
				while($chz = mysql_fetch_assoc($qchk)) {
					$q = mysql_query('SELECT * FROM users WHERE login = "'.$chz['present'].'"');
					$usr = mysql_fetch_assoc($q);

					$sh = mt_rand(1,100); // шансы
					if ($sh >= 60 && in_array($usr['id'],$loselistcp)) {
						$pr_count = explode(' ',$chz['name']);
						$pr_count[3] = (int)$pr_count[3];

						$q = mysql_query('SELECT * FROM users_clons WHERE id = '.$isarchwin);
						$bot = mysql_fetch_assoc($q);

						$q = mysql_query("UPDATE `users` SET `money` = `money`+ ".$pr_count[3]." WHERE `id` = ".$usr['id']);
						addchp ('<font color=red>¬нимание!</font> <B>"'.$bot['login'].'"</B> передал вам <B>'.$pr_count[3].'.00 кр</B>.  ','{[]}'.$usr['login'].'{[]}',$usr['room'],$usr['id_city']);

						$rec = array();
						$rec['owner'] = $usr['id'];
						$rec['owner_login']=$usr['login'];
						$rec['owner_balans_do'] = $usr['money'];
						$usr['money'] += $pr_count[3];
						$rec['owner_balans_posle'] = $usr['money'];
						$rec['sum_kr'] = $pr_count[3];
						$rec['target'] = 0;
						$rec['target_login'] = 'Ѕ—';
						$rec['type'] = 102;//ѕолучение выйгрыша
						add_to_new_delo($rec);

						mysql_query('UPDATE dt_log SET `log` = CONCAT(`log`,\''."<span class=date>".date("d.m.y H:i")."</span>  ".mysql_real_escape_string(nick_align_klan($usr))." обналичил чек на <B>".$pr_count[3]." кр.</B><BR>".'\') WHERE `dt_id` = '.$map['id']);
						mysql_query('DELETE FROM oldbk.`inventory` WHERE id = '.$chz['id'].' and owner = '.$isarchwin);
						unset($checklist[$chz['id']]); // удал€ем обналенный чек
					}
				}
			}

			// в победившей команде есть арх - ему весь шмот и кидаем
			if (count($droplist)) {
				mysql_query_100('UPDATE oldbk.inventory SET owner = '.$isarchwin.', dressed = 0 WHERE id IN ('.implode(",",array_keys($droplist)).')');
			}

			if (count($checklist)) {
				require_once('memcache.php');
				reset($checklist);
				while(list($k,$v) = each($checklist)) {
					mysql_query_100('
						INSERT `dt_items` (`type`,`iteam_id`, `name`, `img`, `room`, `extra`,`present`,`durability`)
						VALUES (
							"0",
							"'.$v["prototype"].'",
							"'.mysql_real_escape_string($v['name']).'",
							"'.mysql_real_escape_string($v['img']).'",
							"'.$iroom.'",
							"'.$v['bs'].'",
							"'.mysql_real_escape_string($v['present']).'",
							"'.$v['duration'].'"
							)
					') ;

					// дл€ кеша отображени€ в dt_show.php
					$id = mysql_insert_id();
					$sql = 'SELECT img FROM dt_items WHERE id = '.$id;
					$cache = array();
					$cache[0] = array(
						'img' => mysql_real_escape_string($v['img']),
					);
					setCache(md5("mysql_query".$sql),$cache,3*3600);
				}
				mysql_query_100('DELETE FROM oldbk.inventory WHERE id IN ('.implode(",", array_keys($checklist)).')');
			}
		} elseif ($iroom > 0) {
			// выкидываем шмотки в бс
			require_once('memcache.php');

			reset($droplist);
			while(list($k,$v) = each($droplist)) {
				mysql_query_100('
					INSERT `dt_items` (`type`,`iteam_id`, `name`, `img`, `room`, `extra`,`present`,`durability`)
					VALUES (
						"0",
						"'.$v["prototype"].'",
						"'.mysql_real_escape_string($v['name']).'",
						"'.mysql_real_escape_string($v['img']).'",
						"'.$iroom.'",
						"'.$v['bs'].'",
						"'.mysql_real_escape_string($v['present']).'",
						"'.$v['duration'].'"
						)
				') ;

				// дл€ кеша отображени€ в dt_show.php
				$id = mysql_insert_id();
				$sql = 'SELECT img FROM dt_items WHERE id = '.$id;
				$cache = array();
				$cache[0] = array(
					'img' => mysql_real_escape_string($v['img']),
				);
				setCache(md5("mysql_query".$sql),$cache,3*3600);
			}
			if (count($droplist)) mysql_query_100('DELETE FROM oldbk.inventory WHERE id IN ('.implode(",", array_keys($droplist)).')');
		}
	} else if ($btype == 11) {

		$ruin_conf='/www/'.CITY_DOMEN.'/ruines_config.php';
		if (file_exists($ruin_conf)) {
			require_once($ruin_conf);
		}


		// узнаЄм номер карты руин
		$q = mysql_query_100('SELECT `ruines`,`room` FROM `users` WHERE in_tower = 2 AND `battle` = '.$bat['id'].' LIMIT 1') ;
		if (mysql_num_rows($q) > 0) {
			$mapid = mysql_fetch_assoc($q) ;

			$q = mysql_query_100('SELECT * FROM `ruines_map` WHERE id = '.$mapid['ruines']);
			if ($mapid['ruines'] > 0 && mysql_num_rows($q) > 0) {
				$map = mysql_fetch_assoc($q) ;

				// обновление тайма на выпадение ключа
				$bt1 = explode(";",$bat['t1']);
				$bt2 = explode(";",$bat['t2']);
				if ($map['k1owner'] > 0 && (in_array($map['k1owner'],$bt1) || in_array($map['k1owner'],$bt2))) {
					mysql_query_100('UPDATE `ruines_map` SET k1timeout = '.time().' WHERE id = '.$map['id']);
				}

				if ($map['k2owner'] > 0 && (in_array($map['k2owner'],$bt1) || in_array($map['k2owner'],$bt2))) {
					mysql_query_100('UPDATE `ruines_map` SET k2timeout = '.time().' WHERE id = '.$map['id']);
				}

				// проигравшую команду кидаем на кладбище
				$sql = 'UPDATE `users` SET `room` = '.($map['rooms']+75).' WHERE in_tower = 2 AND battle = '.$bat['id'];
				if ($win_t != 0)
				{
					$sql .= ' AND `battle_t` !='.$winner_t;
				} else {
					// ничь€ - кидаем всех на кладбище
				}
				mysql_query_100($sql) ;

				// увеличиваем счЄтчик проигрышей в карте дл€ юзеров
				$sql = 'SELECT * FROM `users` WHERE in_tower = 2 AND `battle` = '.$bat['id'];
				if ($win_t != 0)
				{
					$sql .= ' AND `battle_t` !='.$winner_t;
				} else {
					// ничь€ = все
				}

				$q = mysql_query_100($sql) ;
				$ids = "";
				$val = "";
				$uinfo = array();
				while($u = mysql_fetch_assoc($q)) {
					$ids .= $u['id'].',';
					$uinfo[$u['id']] = $u;
					$val .= '("'.$u['id'].'","rdeath","1"),';
				}

				// пишем лог дл€ ботов
				$sql = 'SELECT * FROM `users_clons` WHERE `battle` = '.$bat['id'];

				if ($win_t != 0)
				{
					$sql .= ' AND `battle_t` !='.$winner_t;
				} else {
					// ничь€ - все
				}


				$q = mysql_query_100($sql) ;
				while($u = mysql_fetch_assoc($q)) {
					// лог
					if (strpos($u['login'],'(иллюзи€') !== FALSE) {
						$log = '<span class=date>'.date("d.m.y H:i").'</span>  '.nick_hist($u).' повержен.<BR>';
						mysql_query_100('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) ;
					}
				}

				if (strlen($val)) {
					$ids = substr($ids,0,strlen($ids)-1);
					$val = substr($val,0,strlen($val)-1);

					mysql_query_100('INSERT INTO `ruines_var` (`owner`,`var`,`val`)
								VALUES '.$val.'
								ON DUPLICATE KEY UPDATE
									`val` = `val` + 1
					') ;


					// вешаем путы взависимости от колва смерти
					$q = mysql_query_100('SELECT * FROM `ruines_var` WHERE `owner` IN ('.$ids.') AND `var` = "rdeath"') ;
					while($var = mysql_fetch_assoc($q)) {
						// лог
						$u = $uinfo[$var['owner']];
						$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$u['id_grup']].'>'.nick_hist($u).'</font> повержен и находитс€ на <b> ладбище</b>.<BR>';
						mysql_query_100('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']) ;

						mysql_query_100('DELETE FROM `effects` WHERE `type` = 10 AND `owner` = '.$var['owner']);

						if ($var['val'] == 1) {
							// вешаем путы на 10 минут
							mysql_query_100('INSERT INTO `effects` (`owner`,`name`,`time`,`type`) VALUES ("'.$var['owner'].'","ѕуты",'.(time()+(60*10)).',10)');

						} else {
							// вешаем путы на 1000 часов
							mysql_query_100('INSERT INTO `effects` (`owner`,`name`,`time`,`type`) VALUES ("'.$var['owner'].'","ѕуты",'.(time()+(60*60*1000)).',10)');

							// раздеваем
							undressall($var['owner']);

							require_once('memcache.php');

							$qa = mysql_query_100('SELECT * FROM oldbk.inventory WHERE owner = '.$var['owner'].' AND bs_owner = 2 AND type != 200') ;
							while($di = mysql_fetch_assoc($qa)) {
								$iroom = mt_rand(1,74)+$map['rooms'];
								mysql_query_100('INSERT `ruines_items` (`type`,`iteam_id`, `name`, `img`, `room`, `extra`,`present`,`durability`)
									VALUES (
										"0",
										"'.$di["prototype"].'",
										"'.mysql_real_escape_string($di['name']).'",
										"'.mysql_real_escape_string($di['img']).'",
										"'.$iroom.'",
										"'.$di['bs'].'",
										"'.mysql_real_escape_string($di['present']).'",
										"'.$di['duration'].'"
									)
								');

								$id = mysql_insert_id();
								$sql = 'SELECT img FROM ruines_items WHERE id = '.$id;
								$cache = array();
								$cache[0] = array(
									'img' => mysql_real_escape_string($di['img']),
								);
								setCache(md5("mysql_query".$sql),$cache,3*3600);
							}

							// удал€ем шмот из его инвентар€
							mysql_query_100('DELETE FROM oldbk.`inventory` WHERE bs_owner = 2 AND `owner` = '.$var['owner']) ;
						}
					}


					// провер€ем был ли ключеносец и если был - сносим у него ключ и выкидываем. при ничьей тоже выносим ключ
					$sql = 'SELECT * FROM `users` WHERE in_tower = 2 AND `battle` = '.$bat['id'].' AND (`id` = '.$map['k1owner'].' OR `id` = '.$map['k2owner'].')';
					if ($win_t != 0)
					{
						$sql .= ' AND `battle_t` !='.$winner_t;
					} else {
					// ничь€ - все
					}

					$q = mysql_query_100($sql) ;
					if (mysql_num_rows($q) > 0) {
						// ктото сдох с ключом
						while($u = mysql_fetch_assoc($q)) {
							// снимаем ключ
							mysql_query_100('UPDATE `ruines_map` SET k'.$u['id_grup'].'owner = 0 WHERE id = '.$map['id']);

							// ложим рандомно ключ
							$keyroom = 0;
							do {
								$keyroom = mt_rand(1,77);
							} while(in_array($keyroom,$keyexcluderooms));
							mysql_query_100('INSERT INTO `ruines_items` (type,name,img,room,extra) VALUES ("4"," люч","",'.($map['rooms']+$keyroom).',0)') ;

							// кидаем системку
							$q2 = mysql_query_100('SELECT * FROM `users` WHERE `room` BETWEEN '.$map['rooms'].' AND '.($map['rooms']+100).' AND `in_tower` = 2') ;
							$mids = array();
							while($u2 = mysql_fetch_assoc($q2)) {
								$mids[] = $u2['id'];
							}
							if (count($mids)) addch_group('<font color=red>¬нимание!</font> <B><font color="'.$team_colors[$u['id_grup']].'">'.$u['login'].'</font></B> умер в бою и потер€л ключ. »щите его снова.',$mids);

							$log = '<span class=date>'.date("d.m.y H:i").'</span>  <font color='.$team_colors[$u['id_grup']].'>'.nick_hist($u).'</font> потер€л ключ.<BR>';
							mysql_query_100('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']);

						}
					}
				}

				// провер€ем сдох ли бот в проигравшей команде и если был - ложим чек и колодец. при ничьей ничего не ложим.
				$croom = $mapid['room'] - $map['rooms'];
				$q = mysql_query_100('SELECT * FROM `users_clons` WHERE `battle` = '.$bat['id'].' AND bot_online = 5 AND battle_t != '.$winner_t) ;
				if (mysql_num_rows($q) > 0) {
					// есть боты в проигравшей команде
					$bot = mysql_fetch_assoc($q);

					// ставим колодец
					mysql_query_100('INSERT INTO `ruines_items` (type,name,img,room,extra) VALUES ("5"," олодец","",'.($mapid['room']).',0)') ;

					$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=3"));
					//3) Ќедел€ руин - из убитой души падает пропуск в руины с 40% веро€тностью
					if ($get_ivent['stat']==1) {
						if (get_chanse(40)) {
							$q = mysql_query_100('SELECT * FROM `users` WHERE in_tower = 2 AND `battle` = '.$bat['id'].' AND `battle_t` = '.$winner_t.' ORDER BY RAND() LIMIT 1') ;
								if (mysql_num_rows($q) > 0) {
									$u = mysql_fetch_assoc($q) ;
									mysql_query_100("INSERT INTO `oldbk`.`inventory` SET `name`='ћалый свиток Ђѕропуск в –уиныї',`duration`=0,`maxdur`=1,`cost`=300,`owner`='{$u['id']}',`nlevel`=0,`nsila`=0,`nlovk`=0,`ninta`=0,`nvinos`=0,`nintel`=0,`nmudra`=0,`nnoj`=0,`ntopor`=0,`ndubina`=0,`nmech`=0,`nalign`=0,`minu`=0,`maxu`=0,`gsila`=0,`glovk`=0,`ginta`=0,`gintel`=0,`ghp`=0,`mfkrit`=0,`mfakrit`=0,`mfuvorot`=0,`mfauvorot`=0,`gnoj`=0,`gtopor`=0,`gdubina`=0,`gmech`=0,`img`='ruineticket0_2.gif',`text`='',`dressed`=0,`bron1`=0,`bron2`=0,`bron3`=0,`bron4`=0,`dategoden`=".(time()+(15*3600*24)).",`magic`=228,`type`=12,`present`='”дача',`sharped`=0,`massa`=1,`goden`=15,`needident`=0,`nfire`=0,`nwater`=0,`nair`=0,`nearth`=0,`nlight`=0,`ngray`=0,`ndark`=0,`gfire`=0,`gwater`=0,`gair`=0,`gearth`=0,`glight`=0,`ggray`=0,`gdark`=0,`letter`='—тирает врем€ дл€ посещени€ –уин —тарого «амка.',`isrep`=0,`update`=NOW(),`setsale`=0,`prototype`=4017,`otdel`='52',`bs`=0,`gmp`=0,`includemagic`=0,`includemagicdex`=0,`includemagicmax`=0,`includemagicname`='',`includemagicuses`=0,`includemagiccost`=0,`includemagicekrcost`=0,`gmeshok`=0,`tradesale`=0,`karman`=0,`stbonus`=0,`upfree`=0,`ups`=0,`mfbonus`=0,`mffree`=0,`type3_updated`=0,`bs_owner`=0,`nsex`=0,`present_text`='',`add_time`=0,`labonly`=0,`labflag`=0,`prokat_idp`=0,`arsenal_klan`='',`arsenal_owner`=0,`repcost`=0,`up_level`=0,`ecost`=0,`group`=0,`unik`=0,`sowner`=0,`idcity`=0,`battle`=0,`t_id`=0,`ab_mf`=0,`ab_bron`=0,`ab_uron`=0,`img_big` = 'ruineticket0.gif';");
									$ch_id = mysql_insert_id();

									$rec = array();
						    			$rec['owner']=$u['id'];
									$rec['owner_login']=$u['login'];
									$rec['owner_balans_do']=$u['money'];
									$rec['owner_balans_posle']=$u['money'];
									$rec['type']=205;
									$rec['item_id']=get_item_fid(array("idcity" => $u['id_city'], "id" => $ch_id));
									$rec['item_name']='ѕропуск в –уины';
									$rec['item_count']=1;
									$rec['item_type']=12;
									$rec['item_cost']=5;
									$rec['item_dur']=0;
									$rec['item_maxdur']=1;
									add_to_new_delo($rec); //юзеру
									addchp ('<font color=red>¬нимание!</font> «а победу в бою вы получили <b>ѕропуск в –уины</b>.','{[]}'.$u['login'].'{[]}',$u['room'],$u['id_city']);
								}
							}
					}



					// рандомно даЄм 100кр чек 5%
					if (get_chanse(3))  {
						// повезло комуто )
						$q = mysql_query_100('SELECT * FROM `users` WHERE in_tower = 2 AND `battle` = '.$bat['id'].' AND `battle_t` = '.$winner_t.' ORDER BY RAND() LIMIT 1') ;
						if (mysql_num_rows($q) > 0) {
							$u = mysql_fetch_assoc($q) ;
							// даЄм чек и пишем в чат
							mysql_query_100("INSERT INTO oldbk.`inventory` (`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`) VALUES ('„ек на предъ€вител€ 100кр',0,1,100,'{$u['id']}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'lab2_100kr.gif','',0,0,0,0,0,0,0,50,'',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',0,NOW(),0,3205,'52',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,1,NULL,0,NULL,NULL,0);");
							$ch_id = mysql_insert_id();

							$rec = array();
				    			$rec['owner']=$u['id'];
							$rec['owner_login']=$u['login'];
							$rec['owner_balans_do']=$u['money'];
							$rec['owner_balans_posle']=$u['money'];
							$rec['type']=205;
							$rec['item_id']=get_item_fid(array("idcity" => $u['id_city'], "id" => $ch_id));
							$rec['item_name']='„ек на предъ€вител€ 100кр';
							$rec['item_count']=1;
							$rec['item_type']=50;
							$rec['item_cost']=100;
							$rec['item_dur']=0;
							$rec['item_maxdur']=1;
							add_to_new_delo($rec); //юзеру

							addchp ('<font color=red>¬нимание!</font> «а победу в бою вы получили чек на <b>100</b> кр.','{[]}'.$u['login'].'{[]}',$u['room'],$u['id_city']);

							$log = '<span class=date>'.date("d.m.y H:i").'</span>  »з '.nick_hist($bot).' выпал чек на 100 кр. и <font color='.$team_colors[$u['id_grup']].'>'.nick_hist($u).'</font> подобрал его.<BR>';
							mysql_query_100('UPDATE `ruines_log` SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($log).'") WHERE id = '.$map['id']);


							// сколько екр вынесено
							mysql_query_100('INSERT INTO `ruines_var` (`owner`,`var`,`val`)
								VALUES(
									'.$u['id'].',
									"100kr",
									"1"
								)
								ON DUPLICATE KEY UPDATE
									`val` = val + 1
							') ;

						}
					}
				}

			} else {
				// если нет карты и мы проиграли, то значит что нас выкинули из руин
			}
		}
	} else if ($btype == 13 || $btype == 14) {
		// если это бой на карте - значит мен€ем статус обоих групп и всЄ
		$grroom = 0;
		$q = mysql_query('select DISTINCT(id_grup) as `mapgroup`,room FROM users WHERE battle = '.$bat['id'].' AND id_grup != 0');
		$tids = "";
		while($t = mysql_fetch_assoc($q)) {
			$tids .= $t['mapgroup'].",";
			if (!$grroom) $grroom = $t['room'];
		}
		if (strlen($tids)) {
			$tids = substr($tids,0,strlen($tids)-1);
			mysql_query_100('UPDATE `map_groups` SET status = 0 WHERE id IN ('.$tids.')');
		}

		// проигравша€ команда
		$sql = 'SELECT * FROM oldbk.`users` WHERE battle = '.$bat['id'];
		if ($win_t != 0)
					{
						$sql .= ' AND `battle_t` !='.$winner_t;
					} else {
					// ничь€ - все
					}

		$losers = array();
		$q = mysql_query_100($sql) ;

		while($u = mysql_fetch_assoc($q)) {
			$losers[$u['id']] = $u;
		}

		// шанс на выпадени€ лошади
		$drophorsechanse = 30;
		if (count($losers)) {
			reset($losers);
			while(list($k,$v) = each($losers)) {
				if ($v['podarokAD'] && get_chanse($drophorsechanse)) {
					// скидываем с него лошадь
					mysql_query_100('UPDATE oldbk.users SET podarokAD = 0 WHERE id = '.$k);

					// ложим лошадь на карту
					mysql_query('INSERT INTO oldbk.map_items (itemid,type,name,img,room,extra,extra2) VALUES("0","0","Ћошадь","","'.$losers[$k]['room'].'","'.$losers[$k]['id'].'","'.$losers[$k]['injury_possible'].'")');

					$q = mysql_query('START TRANSACTION');
					if ($q !== FALSE) {
						$q = mysql_query('SELECT * FROM oldbk.map_groups WHERE id = '.$losers[$k]['id_grup'].' FOR UPDATE');
						$outmap = mysql_fetch_assoc($q);

						if ($outmap !== FALSE) {
							$cache = unserialize($outmap['team_cache']);
							$losers[$k]['podarokAD'] = 0;
							$cache[$k] = nick_hist_horse($losers[$k]);

							if ($outmap['leader'] == $k) {
								mysql_query_100('UPDATE oldbk.map_groups SET horse = 0, path="", cost = 0, nextcost = 0, team_cache = "'.mysql_real_escape_string(serialize($cache)).'" WHERE id = '.$outmap['id']);
							} else {
								mysql_query_100('UPDATE oldbk.map_groups SET path="", cost = 0, nextcost = 0, team_cache = "'.mysql_real_escape_string(serialize($cache)).'" WHERE id = '.$outmap['id']);
							}
							addchp ('<font color=red>¬нимание!</font> ¬ы упали с лошади','{[]}'.$losers[$k]['login'].'{[]}',-1,$losers[$k]['id_city']);
							addch('<b>'.$losers[$k]['login'].'</b> проиграл бой и упал с лошади',$losers[$k]['room']);
						}

						mysql_query('COMMIT');
					}

				}
			}
		}


		$winners = array();
		$sql = 'SELECT * FROM oldbk.`users` WHERE battle = '.$bat['id'].' AND battle_t = '.$winner_t;
		$q = mysql_query_100($sql);
		while($u = mysql_fetch_assoc($q)) {
			$winners[$u['id']] = $u;
		}

		// легендарный квест - победа в загородных бо€х
		if (count($winners) && $btype == 14 && ($blood>0) ) {
			$sql = 'UPDATE oldbk.map_var SET val = val + 1 WHERE owner IN ('.implode(",",array_keys($winners)).') AND var = "q32s1"';
			$q = mysql_query_100($sql);
		}


		// грабЄж на ресы
		if ($win_t > 0) {
			// есть победители, грабим
			$grablist = array();
			reset($losers);
			// составл€ем список того, чего украли

			// type - 1 монета, id = inventory.id (1-3), owner владелец
			// type - 2 ресурс, id = inventory.id (3-6), owner владелец
			// type - 3 кр. count - 1-4. owner владелец

			while(list($k,$v) = each($losers)) {
				// монеты
				/*
				$m = mt_rand(1,3);
				if ($btype == 14) {
					$m = mt_rand(2,4);
				}
				$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$k.' AND prototype = 3003060 LIMIT '.$m);
				if (mysql_num_rows($q) > 0) {
					while($it = mysql_fetch_assoc($q)) {
						$grablist[] = array('type' => 1, 'ownername' => $v['login'], 'item' => $it);
					}
				} else {
				*/
					// ресурсы с лабы
					$r = mt_rand(3,6);
					if ($btype == 14) {
						$r = mt_rand(5,8);
					}
					$q = mysql_query('SELECT * FROM oldbk.inventory WHERE owner = '.$k.' AND ((`prototype`>3000 AND `prototype` <3022 ) OR (`prototype`>103000 AND `prototype` <103022)) LIMIT '.$r);
					if (mysql_num_rows($q) > 0) {
						while($it = mysql_fetch_assoc($q)) {
							$grablist[] = array('type' => 2, 'ownername' => $v['login'], 'item' => $it);
						}
					} else {
						// грабим кредиты
						$kr = mt_rand(1,4);
						if ($btype == 14) {
							$kr = mt_rand(3,6);
						}
						if ($v['money'] >= $kr) {
							$grablist[] = array('type' => 3, 'count' => $kr, 'owner' => $k, 'ownername' => $v['login']);
						}
					}
				//}
			}

			$loot = array();

			if (count($grablist)) {
				// только не спрашивайте как работает код ниже ))
				if (count($winners)) {
					while(list($k,$v) = each($grablist)) {
						while(list($ka,$va) = each($winners)) {
							takeMLitem($va,$v);
							if ($va['hidden']) $loot[$va['login']]['hidden'] = 1;
							if ($v['type'] == 3) {
								$loot[$va['login']]['type'.$v['type']] += $v['count'];
							} else {
								$loot[$va['login']]['type'.$v['type']]++;
							}
							continue 2;
						}
						reset($winners);

						list($ka,$va) = each($winners);
						takeMLitem($va,$v);
						if ($va['hidden']) $loot[$va['login']]['hidden'] = 1;
						if ($v['type'] == 3) {
							$loot[$va['login']]['type'.$v['type']] += $v['count'];
						} else {
							$loot[$va['login']]['type'.$v['type']]++;
						}
					}

				}

				// системки тем кто своровал
				while(list($k,$v) = each($loot)) {
					$addtxt = "";
					if (isset($loot[$k]['type3'])) {
						$addtxt = " <b>".$loot[$k]['type3']."</b> кр. ";
					}
					if (isset($loot[$k]['type1'])) {
						if ($loot[$k]['type1'] == 1) {
							$addtxt = " <b>".$loot[$k]['type1']."</b> монетку ";
						} else {
							$addtxt = " <b>".$loot[$k]['type1']."</b> монетки ";
						}
					}
					if (isset($loot[$k]['type2'])) {
						if ($loot[$k]['type2'] == 1) {
							$addtxt = " <b>".$loot[$k]['type2']."</b> ресурс ";
						} else {
							$addtxt = " <b>".$loot[$k]['type2']."</b> ресурсов ";
						}
					}

					addchp ('<font color=red>¬нимание!</font> ¬ы ограбили группу и получили '.$addtxt,'{[]}'.$k.'{[]}',-1);
					if (isset($loot[$k]['hidden'])) {
						addch('<img src=i/magic/attack.gif> <b><i>Ќевидимка</i></b> ограбил группу и получил '.$addtxt,$grroom);
					} else {
						addch('<img src=i/magic/attack.gif> <b>'.$k.'</b> ограбил группу и получил '.$addtxt,$grroom);
					}
				}
			}
		}
	} else if ($btype == 170) {
		require_once('castles_functions.php');
		require_once('clan_kazna.php');
		// турнирный бой, дело имеем только с командой проигравших
		$loseteam = 0;
		if ($win_t == 1) $loseteam = 2;
		if ($win_t == 2) $loseteam = 1;
		if ($win_t == 0) {
			$d1 = 0; $d2 = 0;

			$q = mysql_query('SELECT  u.battle_t, sum(damage) as alldmg  from users u LEFT JOIN battle_dam_exp dam on dam.owner=u.id and dam.battle=u.battle where u.battle='.$bat['id'].' group by battle_t');
			while($u = mysql_fetch_assoc($q)) {
				if ($u['battle_t'] == 1) {
					$d1 = $u['alldmg'];
				}
				if ($u['battle_t'] == 2) {
					$d2 = $u['alldmg'];
				}
			}

			if ($d1 > $d2) {
				$loseteam = 2;
			} else if ($d1 == $d2) {
				$loseteam = mt_rand(1,2);
			} else {
				$loseteam = 1;
			}
		}

		$q = mysql_query('SELECT * FROM users WHERE battle = '.$bat['id'].' AND battle_t = '.$loseteam);

		$ua = array();
		$id_tur = 0;
		$klan = "";
		while($u = mysql_fetch_assoc($q)) {
			$ua[$u['id']] = $u;
			$id_tur = $u['id_grup'];
			$klan = $u['klan'];
		}
		if (count($ua) && strlen($klan)) {
			// выпиливаем их из турнира
			mysql_query('START TRANSACTION');
			$q2 = mysql_query('SELECT * FROM castles_tur WHERE id = '.$id_tur.' FOR UPDATE');
			$t = mysql_fetch_assoc($q2);
			$teams = unserialize($t['data']);

			while(list($k,$v) = each($teams)) {
				$second = CGetSecondClan2($v['klan']);
				if ($v['klan'] == $klan || $second == $klan) {
					// возвращаем 45 кр
					$clan = mysql_query('SELECT * FROM oldbk.clans WHERE short = "'.$v['ownerklan'].'"');
					$clan = mysql_fetch_assoc($clan);
		    			$clan_kazna=clan_kazna_have($clan['id']);
					if ($clan_kazna) {
						sell_to_kazna($clan['id'],45,"","¬озврат 45 кр. в казну за проигрыш в турнире");
					}

					unset($teams[$k]);
				}
			}
			$ttemp = array();
			reset($teams);
			while(list($k,$v) = each($teams)) {
				$ttemp[] = $v;
			}

			$teams = $ttemp;

			mysql_query('UPDATE castles_tur SET data = "'.mysql_real_escape_string(serialize($teams)).'" WHERE id = '.$id_tur);
			mysql_query('COMMIT');

			// пишем лог
			$tmp = "";
			while(list($k,$v) = each($ua)) {
				$tmp .= nick_align_klan($v).", ";
				CastleExitDress($v);
			}
			$cc = "(".substr($tmp,0,strlen($tmp)-2).")";

			$txt = '<span class=date>'.date("d.m.y H:i").'</span>  оманда '.$cc.' покидает турнир.<BR>';
			mysql_query('UPDATE castles_tur SET `log` = CONCAT(`log`,"'.mysql_real_escape_string($txt).'") WHERE id = '.$id_tur);
		}


	} else if ($btype == 171) {
		require_once('castles_functions.php');

		$vointoc = array(
			9 => 2000,
			10 => 2000,
			11 => 2000,
			12 => 2000,
			13 => 2000,
			14 => 2000,
			15 => 2000,
			16 => 2000,
		);


		$q = mysql_query('SELECT * FROM oldbk.castles WHERE battle = '.$bat['id']);
		if (mysql_num_rows($q) > 0) {
			$c = mysql_fetch_assoc($q);
			if ($c !== FALSE) {
				$cid = $c['id'];
				mysql_query('DELETE FROM castles_inventory WHERE cid = '.$cid);
				$winclan = "";
				if ($win_t == 1) $winclan = $c['clanashort1'];
				if ($win_t == 2) $winclan = $c['clanashort2'];
				// нашли замок
				if ($winclan != "") {
					mysql_query_100('UPDATE oldbk.clans SET voinst = voinst + '.$vointoc[$c['nlevel']].' WHERE short = "'.$winclan.'"');
					mysql_query_100('UPDATE oldbk.castles SET clanshort = "'.$winclan.'", status = 0, lastpagegen = '.(time()+(24*3600)).', lastcoingen = '.(time()+(24*3600)).', battle = 0, clanashort1 = "", clanashort2 = "", timeouta = 0 WHERE id = '.$cid);
					WriteToCastle($cid,'«амок захвачен кланом '.CGetClan2($winclan).'. Ћог бо€ <a href="logs.php?log='.$bat['id'].'" target="_blank">&gt;&gt</a>');


					// выдаЄм награды
					$q = mysql_query('SELECT * FROM oldbk.`users` WHERE battle = '.$bat['id'].' AND battle_t = '.$winner_t);

					$winners = array();

					while($u = mysql_fetch_assoc($q)) {
						$winners[$u['id']] = $u;
					}

					if (count($winners)) {
						mysql_query_100('UPDATE users SET rep = rep + 1000, repmoney = repmoney + 1000 WHERE id IN ('.implode(",",array_keys($winners)).')');
					}

					$mids1 = array();
					$mids2 = array();
					while(list($k,$v) = each($winners)) {
						$bank = mysql_query('SELECT * FROM bank WHERE owner = '.$k.' and def = 1');
						if (mysql_num_rows($bank)) {
							$bank = mysql_fetch_assoc($bank);
                                                        mysql_query_100('UPDATE bank SET ekr = ekr + 1 WHERE id = '.$bank['id']);
							$bank['ekr']++;
							mysql_query("INSERT INTO `oldbk`.`bankhistory`(`date`, `text` , `bankid`) VALUES ('".time()."','<b>¬ы получили 1 екр за победу в замках.</b>, <i>(»того: {$bank['cr']} кр., {$bank['ekr']} екр.)</i>','{$bank['id']}');");
							$mids1[] = $v['id'];
						} else {
							$mids2[] = $v['id'];
						}

					}

					if (count($mids1)) addch_group('<font color=red>¬нимание!</font> ѕолучено 1 екр и 1000 репутации.',$mids1);
					if (count($mids2)) addch_group('<font color=red>¬нимание!</font> ѕолучено 1000 репутации.',$mids2);

				} else {
					WriteToCastle($cid,'Ѕой за замок между кланами '.CGetClan2($c['clanshort']).' и '.CGetClan2($c['clanashort']).' окончен вничью. «амок никому не принадлежит. Ћог бо€ <a href="logs.php?log='.$bat['id'].'" target="_blank">&gt;&gt</a>');
					mysql_query_100('UPDATE oldbk.castles SET status = 0, battle = 0, clanashort1 = "", clanashort2 = "", timeouta = 0 WHERE id = '.$cid);
				}
			}
		}
		mysql_query('DELETE FROM battle_data WHERE battle = '.$bat['id']);

		// собираем людей которых надо выкинуть из одевалки
		$uall = array();
		$q = mysql_query('SELECT * FROM users WHERE battle = '.$bat['id'].' and in_tower = 16');
		while($u = mysql_fetch_assoc($q)) {
			$uall[] = $u['id'];
			// временно
			mysql_query('DELETE FROM effects WHERE (type = 10 or type = 11 or type = 12 or type = 13 or type = 14) AND owner = '.$u['id']);
		}
		if (count($uall)) UndressCastlesAllNoTrz($uall);
	} else if ($btype == 15) {
		// квестовый бой, узнаЄм что за квест
		// в левой команде (1) всегда юзер (один всегда один), в правой(2) бот/ы

		// бой на карте, всем мен€ем статус
		$q = mysql_query('select DISTINCT(id_grup) as `mapgroup` FROM users WHERE battle = '.$bat['id'].' AND id_grup != 0');
		$tids = "";
		while($t = mysql_fetch_assoc($q)) {
			$tids .= $t['mapgroup'].",";
		}
		if (strlen($tids)) {
			$tids = substr($tids,0,strlen($tids)-1);
			mysql_query_100('UPDATE `map_groups` SET status = 0 WHERE id IN ('.$tids.')');
		}

		// узнаЄм кто в левой команде
		if ($winner_t == 1) {
			$u = $bat['t1'];
			$u = explode(";",$bat['t1']);
			$u = $u[0];
			$u = mysql_fetch_assoc(mysql_query('SELECT * FROM users WHERE id = '.$u));

			$b = explode(";",$bat['t2']);
			$b = $b[0];
			$b = mysql_fetch_assoc(mysql_query('SELECT * FROM users_clons WHERE id = '.$b));

			$q = mysql_query('SELECT * FROM map_quests WHERE owner = '.$u['id']);

			if (mysql_num_rows($q) > 0 && $u !== FALSE && $b !== FALSE) {
				$quest = mysql_fetch_assoc($q);

				require_once('mlfunctions.php');
				$q = mysql_query('START TRANSACTION');

				if ($q !== FALSE && $quest !== FALSE) {
				switch($quest['q_id']) {
					case 1:
						if ($quest['step'] == 1) {
							if ($b['id_user'] == 531) {
								if(PutQItem($u,3003001,"“рактирщик")) {
									addchp ('<font color=red>¬нимание!</font> “рактирщик передал вам <b>—пирт</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}

							if ($b['id_user'] == 532) {
								if (QItemEXistsCount($u,3003002,5)) {
									if (PutQItem($u,3003002," рыса")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b> рысиный хвост</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
						}
					break;
					case 2:
						if ($quest['step'] == 0) {
							$ai = explode("/",$quest['addinfo']);

							if ($b['id_user'] == 534) {
								if ($ai[0] == 1) {
									if (QItemEXistsCount($u,3003005,4)) {
										if (PutQItem($u,3003005,"–удокоп")) {
											addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>ќсколок руды</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
										}
									}
								}
							}
							if ($b['id_user'] == 533) {
								if ($ai[1] == 1) {
									if (!QItemEXists($u,3003007)) {
										$q1 = PutQItem($u,3003007,"ƒракон");
										$q2 = false;
										if ($q1) $q2 = PutQItem($u,3003007,"ƒракон");
										if ($q1 || $q2) {
											addchp ('<font color=red>¬нимание!</font> ¬ы получили немного <b>’вороста</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
										}
									}
								}
							}
						}
					break;
					case 4:
						if ($b['id_user'] == 535) {
							if(PutQItem($u,3003015,"–азбойник")) {
								addchp ('<font color=red>¬нимание!</font> –азбойник передал вам <b>√рамота</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
							}
						}
					break;
					case 5:
						if ($quest['step'] == 0) {
							$ai = explode("/",$quest['addinfo']);

							if ($b['id_user'] == 534) {
								if ($ai[0] == 1) {
									if (QItemEXistsCount($u,3003005,2)) {
										if (PutQItem($u,3003005,"–удокоп")) {
											addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>ќсколок руды</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
										}
									}
								}
							}
						}
					break;
					case 7:
						if ($quest['addinfo'] > 0 && $quest['addinfo'] < 11) {
							if ($b['id_user'] == 536) {
								if (UpdateQuestInfo($u,7,$quest['addinfo']+1)) {
									if ((11-($quest['addinfo']+1)) == 1) {
										addchp ('<font color=red>¬нимание!</font> ¬ы убили волка, еще осталось убить <b>'.(11-($quest['addinfo']+1)).'</b> волка ','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									} else {
										addchp ('<font color=red>¬нимание!</font> ¬ы убили волка, еще осталось убить <b>'.(11-($quest['addinfo']+1)).'</b> волков ','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
						}
						if ($b['id_user'] == 537 && QItemEXistsCount($u,3003024,10)) {
							$z = mt_rand(2,3);
							$yz = false;
							for ($i = 0; $i < $z; $i++) {
								if (PutQItem($u,3003024,"ќрЄл")) {
									$yz = true;
								} else {
									break;
								}
							}
							if ($yz) addchp ('<font color=red>¬нимание!</font> ¬ы получили немного перьев!','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
						}

					break;
					case 8:
						if ($b['id_user'] == 538) {
							// тут стрЄмное место
							$q1 = UpdateQuestInfo($u,8,"3");
							$q2 = false;
							if ($q1) {
								$q2 = PutQItem($u,3003028,"ћаг");
								if ($q2 === FALSE) {
									$q1 = UpdateQuestInfo($u,8,"2");
								}
							}
							if ($q1 && $q2) {
								addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>јмулет</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
							}
						}
					break;
					case 9:
						if ($b['id_user'] == 539) {
							if (PutQItem($u,3003031,"ћедведь")) {
								addchp ('<font color=red>¬нимание!</font> ¬ы получили <b> оготь медвед€</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
							}
						}
						if ($b['id_user'] == 532) {
							if (PutQItem($u,3003034,"—купщик")) {
								addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>ƒрагоценные камни</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
							}
						}
					break;
					case 10:
						if ($quest['step'] == 3) {
							if ($b['id_user'] == 534) {
								if (QItemEXistsCount($u,3003005,5)) {
									if (PutQItem($u,3003005,"–удокоп")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>ќсколок руды</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
						}
						if ($quest['step'] == 8) {
							if ($b['id_user'] == 536) {
								if (QItemEXistsCount($u,3003038,5)) {
									if (PutQItem($u,3003038,"¬олк")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b> апл€ волчьей крови</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
						}
					break;
					case 11:
						if ($b['id_user'] == 531) {
							$ai = explode("/",$quest['addinfo']);
							for ($i = 0; $i < count($ai); $i++) {
								if ($ai[$i] == 1) {
									$ai[$i] = 2;
									UpdateQuestInfo($u,11,implode("/",$ai));
									break;
								}
							}
						}
					break;
					case 12:
						if ($b['id_user'] == 536) {
							if (PutQItem($u,3003042,"¬олк")) {
								addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>¬олчий клык</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
							}
						}
					break;
					case 13:
						$t = explode("/",$quest['addinfo']);
						if ($t[0] > 0 && $t[0] < 11) {
							if ($b['id_user'] == 535) {
								$t[0] = $t[0] + 1;
								if (UpdateQuestInfo($u,13,implode("/",$t))) {
									addchp ('<font color=red>¬нимание!</font> ¬ы убили –азбойника, еще осталось убить <b>'.(11-($t[0])).'</b> –азбойников','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}
						}

						if ($b['id_user'] == 537 && QItemEXistsCount($u,3003045,3)) {
							if (PutQItem($u,3003045,"ќрЄл")) {
								addchp ('<font color=red>¬нимание!</font> ¬ы получили <b> расивое орлиное перо</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
							}
						}
					break;
					case 14:
						$t = explode("/",$quest['addinfo']);

						if ($t[0] == 3 && $t[1] == 1 && $b['id_user'] == 533) {
							if (!QItemExists($u,3003049)) {
								if (PutQItem($u,3003049,"ƒракон")) {
									addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>«уб дракона</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}
						}

						if ($t[0] == 3 && $t[2] == 0 && $b['id_user'] == 531) {
							if(PutQItem($u,3003048,"“рактирщик")) {
								$t[2] = 1;
								UpdateQuestInfo($u,14,implode("/",$t));

								addchp ('<font color=red>¬нимание!</font> “рактирщик передал вам <b>ѕоловина рукописи</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
							}
						}

					break;
					case 15:
						if ($b['id_user'] == 534 && $quest['step'] == 3) {
							if (QItemEXistsCount($u,3003005,10)) {
								if (PutQItem($u,3003005,"–удокоп")) {
									addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>ќсколок руды</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}
						}

					break;
					case 16:
						if ($quest['step'] == 1) {
							// хвосты крыс
							if ($b['id_user'] == 532) {
								if (QItemEXistsCount($u,3003002,5)) {
									if (PutQItem($u,3003002," рыса")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b> рысиный хвост</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
							// крыло летучей мыши
							if ($b['id_user'] == 540) {
								if (QItemEXistsCount($u,3003059,10)) {
									if (PutQItem($u,3003059,"Ћетуча€ мышь")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b> рыло летучей мыши</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
							// 5 капель крови
							if ($b['id_user'] == 536) {
								if (QItemEXistsCount($u,3003038,5)) {
									if (PutQItem($u,3003038,"¬олк")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b> апл€ волчьей крови</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
						}
					break;
					case 17:
						// сердце летучей мыши
						if ($b['id_user'] == 540) {
							if (QItemEXistsCount($u,3003064,1)) {
								if (PutQItem($u,3003064,"Ћетуча€ мышь")) {
									addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>—ердце Ћетучей мыши</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}
						}

						// сердце дракона
						if ($b['id_user'] == 533) {
							if (QItemEXistsCount($u,3003062,1)) {
								if (PutQItem($u,3003062,"ƒракон")) {
									addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>—ердце ƒракона</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}
						}

						// сердце орла
						if ($b['id_user'] == 537) {
							if (QItemEXistsCount($u,3003063,1)) {
								if (PutQItem($u,3003063,"ќрЄл")) {
									addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>—ердце ќрла</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}
						}
					break;
					case 18:
						// сердце дракона
						if ($b['id_user'] == 533) {
							if (QItemEXistsCount($u,3003067,2)) {
								if (PutQItem($u,3003067,"ƒракон")) {
									addchp ('<font color=red>¬нимание!</font> ¬ы получили <b> ровь ƒракона</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}
						}

						if ($b['id_user'] == 534) {
							if (QItemEXistsCount($u,3003005,10)) {
								if (PutQItem($u,3003005,"–удокоп")) {
									addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>ќсколок руды</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}
						}

						if ($b['id_user'] == 535) {
							if (QItemEXistsCount($u,3003068,1)) {
								if (PutQItem($u,3003068,"–азбойник")) {
									addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>ћешок с драгоценными камн€ми</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}
						}
					break;
					case 19:
						$t = explode("/",$quest['addinfo']);

						if ($b['id_user'] == 534 && $t[0] == 1) {
							if (QItemEXistsCount($u,3003005,10)) {
								if (PutQItem($u,3003005,"–удокоп")) {
									addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>ќсколок руды</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}
						}
					break;
					case 21:
						$ai = explode("/",$quest['addinfo']);

						if ($b['id_user'] == 534) {
							if ($ai[0] == 1) {
								if (QItemEXistsCount($u,3003005,2)) {
									if (PutQItem($u,3003005,"–удокоп")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>ќсколок руды</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
						}
					break;
					case 22:
						if ($quest['step'] == 3) {
							if ($b['id_user'] == 537 && QItemEXistsCount($u,3003080,1)) {
								if (PutQItem($u,3003080,"ќрЄл")) {
									addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>ѕеро ќрла</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}
							if ($b['id_user'] == 533) {
								if (QItemEXistsCount($u,3003003,1)) {
									if (PutQItem($u,3003003,"ƒракон")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>ѕомЄт ƒракона</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
							if ($b['id_user'] == 539) {
								if (QItemEXistsCount($u,3003079,3)) {
									if (PutQItem($u,3003079,"ћедведь")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>√лаз медвед€</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}

						}
					break;
					case 23:
						if ($b['id_user'] == 534) {
							if ($quest['step'] == 1) {
								if (QItemEXistsCount($u,3003005,10)) {
									if (PutQItem($u,3003005,"–удокоп")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>ќсколок руды</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
						}
					break;
					case 24:
						$t = explode("/",$quest['addinfo']);

						if ($b['id_user'] == 536 && $t[1] == 1) {
							if (PutQItem($u,3003042,"¬олк")) {
								addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>¬олчий клык</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
							}
						}
					break;
					case 26:
						if ($b['id_user'] == 535) {
							SetQuestStep($u,26,1);
						}
					break;
					case 27:
						if ($quest['step'] == 3 && $b['id_user'] == 541) {
							if (PutQItem($u,3003091,"Ћюдоед")) {
								SetQuestStep($u,27,4);
								addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>√олова людоеда</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
							}
						}
					break;
					case 28:
						$t = explode("/",$quest['addinfo']);

						if ($b['id_user'] == 535 && $t[0] == 1) {
							if (PutQItem($u,3003202,"—купщик")) {
								addchp ('<font color=red>¬нимание!</font> —купщик испугалс€ вашей силы и передал <b>ƒрагоценные бусины</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
							}
						}
						if ($b['id_user'] == 535 && $t[0] == 4) {
							if (PutQItem($u,3003200,"–азбойник")) {
								addchp ('<font color=red>¬нимание!</font> –азбойник испугалс€ вашей силы и передал <b>„етки ручной работы</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								$t[0] = 5;
								UpdateQuestInfo($u,28,implode("/",$t));
							}
						}
						if ($b['id_user'] == 535 && $t[0] == 3) {
							$t[0] = 6;
							UpdateQuestInfo($u,28,implode("/",$t));
						}
						if ($b['id_user'] == 531 && $t[0] == 8) {
							if (PutQItem($u,3003204,"“рактирщик")) {
								addchp ('<font color=red>¬нимание!</font> “рактирщик испугалс€ вашей силы и передал <b>≈да  дл€ –азбойников</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								$t[0] = 9;
								UpdateQuestInfo($u,28,implode("/",$t));
							}
						}
						if ($b['id_user'] == 535 && $t[0] == 9) {
							$qi1 = QItemExistsID($u,3003204,1);
							if ($qi1 !== FALSE) {
								if (PutQItem($u,3003200,"–азбойник",0,$qi1)) {
									addchp ('<font color=red>¬нимание!</font> –азбойник испугалс€ вашей силы и передал <b>„етки ручной работы</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									$t[0] = 10;
									UpdateQuestInfo($u,28,implode("/",$t));
								}
							}
						}
					break;
					case 29:
						if ($quest['step'] == 1) {
							// хвосты крыс
							if ($b['id_user'] == 532) {
								if (QItemEXistsCount($u,3003002,3)) {
									if (PutQItem($u,3003002," рыса")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b> рысиный хвост</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
						}
						// крыло летучей мыши
						if ($b['id_user'] == 540) {
							if (QItemEXistsCount($u,3003059,1)) {
								if (PutQItem($u,3003059,"Ћетуча€ мышь")) {
									addchp ('<font color=red>¬нимание!</font> ¬ы получили <b> рыло летучей мыши</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								}
							}
						}
						if ($b['id_user'] == 542) {
							if (PutQItem($u,3003208,"¬едьма")) {
								addchp ('<font color=red>¬нимание!</font> ¬едьма испугалась вашей силы и вы получили <b>ѕоходный плащ</b>. ¬ам предстоит узнать кому он принадлежит.','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
								SetQuestStep($u,29,6);
							}
						}
					break;
					case 30:
						if ($quest['step'] == 0) {
							if ($b['id_user'] == 536) {
								if (QItemEXistsCount($u,3003211,1)) {
									if (PutQItem($u,3003211,"¬олк")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>—трела из красного дерева</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
						}
						if ($quest['step'] == 7) {
							if ($b['id_user'] == 543) {
								SetQuestStep($u,30,8);
							}
						}
						if ($quest['step'] == 11) {
							if ($b['id_user'] == 533) {
								if (!QItemExists($u,3003216)) {
									if (PutQItem($u,3003216,"ƒракон")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>«уб дракона</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
						}

						if ($quest['step'] == 11) {
							if ($b['id_user'] == 536) {
								if (QItemEXistsCount($u,3003217,5)) {
									if (PutQItem($u,3003217,"¬олк")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b> ровь волка</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
						}

						if ($quest['step'] == 11) {
							if ($b['id_user'] == 544) {
								if (QItemEXistsCount($u,3003218,1)) {
									if (PutQItem($u,3003218,"”топленник")) {
										addchp ('<font color=red>¬нимание!</font> ¬ы получили <b>¬ода из уха утопленника</b>','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
									}
								}
							}
						}

						if ($quest['step'] == 14) {
							if ($b['id_user'] == 543) {
								SetQuestStep($u,30,15);
								PutQItemTo($u,'ќхотник на ¬едьм',QItemExistsId($u,3003222));
								addchp ('<font color=red>¬нимание!</font> ¬ы разрушили ћагическую руну','{[]}'.$u['login'].'{[]}',-1,$u['id_city']);
							}
						}

					break;



				}
				}

				mysql_query('COMMIT');
			}
		}
	} else if ($btype == 40 || $btype == 41  || $btype == 61 ) {
		if ($winner_t == 1 || $winner_t == 2 || $winner_t == 3) {

			///////
			$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=7"));
			//7) Ќедел€ противосто€ни€ - при получении черепа вместе с ним падает с веро€тностью:


			// выдаЄм черепа победившей команде
			$q = mysql_query('SELECT * FROM oldbk.shop WHERE id = 3002500');
			$dress = mysql_fetch_assoc($q);

			if ($dress !== FALSE) {
				$q = mysql_query('SELECT * FROM op_battle_index WHERE battle = '.$bat['id'].' AND team = '.$winner_t);
				while($u = mysql_fetch_assoc($q)) {
					$q2 = mysql_query('SELECT * FROM users WHERE id = '.$u['owner']);
					$usr = mysql_fetch_assoc($q2);

					addchp ('<font color=red>¬нимание!</font> «а победу в бою вы получили черепа в количестве <b>'.$u['value'].'</b> шт.','{[]}'.$usr['login'].'{[]}',-1,$usr['id_city']);



					for ($i = 0; $i < $u['value']; $i++) {

						$q3 = mysql_query('INSERT INTO oldbk.`inventory`
							(`present`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`ecost`,`img`,`duration`,`maxdur`,`isrep`,
								`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,
								`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`
								,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
								`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,
								`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`bs_owner`,`group`, `letter`, `gmp`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`gmeshok`,`tradesale`,`bs`,`idcity`
							)
								VALUES	(
									"ѕротивосто€ние",
									'.$dress['id'].',
									"'.$u['owner'].'",
									"'.mysql_real_escape_string($dress['name']).'",
									'.$dress['type'].',
									"'.$dress['massa'].'",
									"'.$dress['cost'].'",
									"'.$dress['ecost'].'",
									"'.mysql_real_escape_string($dress['img']).'",
									'.$dress['duration'].',
									'.$dress['maxdur'].',
									'.$dress['isrep'].',
									'.$dress['gsila'].',
									'.$dress['glovk'].',
									'.$dress['ginta'].',
									'.$dress['gintel'].',
									'.$dress['ghp'].',
									'.$dress['gnoj'].',
									'.$dress['gtopor'].',
									'.$dress['gdubina'].',
									'.$dress['gmech'].',
									'.$dress['gfire'].',
									'.$dress['gwater'].',
									'.$dress['gair'].',
									'.$dress['gearth'].',
									'.$dress['glight'].',
									'.$dress['ggray'].',
									'.$dress['gdark'].',
									'.$dress['needident'].',
									'.$dress['nsila'].',
									'.$dress['nlovk'].',
									'.$dress['ninta'].',
									'.$dress['nintel'].',
									'.$dress['nmudra'].',
									'.$dress['nvinos'].',
									'.$dress['nnoj'].',
									'.$dress['ntopor'].',
									'.$dress['ndubina'].',
									'.$dress['nmech'].',
									'.$dress['nfire'].',
									'.$dress['nwater'].',
									'.$dress['nair'].',
									'.$dress['nearth'].',
									'.$dress['nlight'].',
									'.$dress['ngray'].',
									'.$dress['ndark'].',
									'.$dress['mfkrit'].',
									'.$dress['mfakrit'].',
									'.$dress['mfuvorot'].',
									'.$dress['mfauvorot'].',
									'.$dress['bron1'].',
									'.$dress['bron2'].',
									'.$dress['bron3'].',
									'.$dress['bron4'].',
									'.$dress['maxu'].',
									'.$dress['minu'].',
									'.$dress['magic'].',
									'.$dress['nlevel'].',
									'.$dress['nalign'].',
										"0",0,
									'.$dress['razdel'].',
									"0",
									'.$dress['group'].',"'.mysql_real_escape_string($dress['letter']).'",0,0,0,0,"",0,0,0,"0",'.$usr['id_city'].'
							)
						');

						if ($get_ivent['stat']==1) //если недел€ противосто€ни€
							{
							$mk_bonus_items=0;
							$rmnt=mt_rand(1,100); // 20% - ничего 40%- хилка 360 40% - аркан противосто€ни€.
								if (($rmnt>=1) and ($rmnt<=80))
									{
									$mk_bonus_items=200273;
									}
								elseif ($rmnt>=80)
									{
									$mk_bonus_items=155155;
									}

							if ($mk_bonus_items>0)
									{
									//выдаем
									put_bonus_item($mk_bonus_items,$usr,'ѕротивосто€ние');
									}
							}
					}
				}
			}
			// выдаЄм осколки черепов проигравшим
			$q = mysql_query('SELECT * FROM op_battle_index WHERE battle = '.$bat['id'].' AND team != '.$winner_t);
			while($u = mysql_fetch_assoc($q)) {
				$q2 = mysql_query('SELECT * FROM users WHERE id = '.$u['owner']);
				$usr = mysql_fetch_assoc($q2);

				addchp ('<font color=red>¬нимание!</font> «а поражение в бою вы получили осколки черепа в количестве <b>'.$u['value'].'</b> шт.','{[]}'.$usr['login'].'{[]}',-1,$usr['id_city']);

				for ($i = 0; $i < $u['value']; $i++) {

					$q4 = mysql_query('SELECT * FROM oldbk.shop WHERE id = '.mt_rand(3002501,3002503));
					$dress = mysql_fetch_assoc($q4);

					if ($dress !== FALSE) {
						$q3 = mysql_query('INSERT INTO oldbk.`inventory`
							(`present`,`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`ecost`,`img`,`duration`,`maxdur`,`isrep`,
								`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,
								`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`
								,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
								`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,
								`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`bs_owner`,`group`, `letter`, `gmp`, `includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`gmeshok`,`tradesale`,`bs`,`idcity`
							)
								VALUES	(
									"ѕротивосто€ние",
									'.$dress['id'].',
									"'.$u['owner'].'",
									"'.mysql_real_escape_string($dress['name']).'",
									'.$dress['type'].',
									"'.$dress['massa'].'",
									"'.$dress['cost'].'",
									"'.$dress['ecost'].'",
									"'.mysql_real_escape_string($dress['img']).'",
									'.$dress['duration'].',
									'.$dress['maxdur'].',
									'.$dress['isrep'].',
									'.$dress['gsila'].',
									'.$dress['glovk'].',
									'.$dress['ginta'].',
									'.$dress['gintel'].',
									'.$dress['ghp'].',
									'.$dress['gnoj'].',
									'.$dress['gtopor'].',
									'.$dress['gdubina'].',
									'.$dress['gmech'].',
									'.$dress['gfire'].',
									'.$dress['gwater'].',
									'.$dress['gair'].',
									'.$dress['gearth'].',
									'.$dress['glight'].',
									'.$dress['ggray'].',
									'.$dress['gdark'].',
									'.$dress['needident'].',
									'.$dress['nsila'].',
									'.$dress['nlovk'].',
									'.$dress['ninta'].',
									'.$dress['nintel'].',
									'.$dress['nmudra'].',
									'.$dress['nvinos'].',
									'.$dress['nnoj'].',
									'.$dress['ntopor'].',
									'.$dress['ndubina'].',
									'.$dress['nmech'].',
									'.$dress['nfire'].',
									'.$dress['nwater'].',
									'.$dress['nair'].',
									'.$dress['nearth'].',
									'.$dress['nlight'].',
									'.$dress['ngray'].',
									'.$dress['ndark'].',
									'.$dress['mfkrit'].',
									'.$dress['mfakrit'].',
									'.$dress['mfuvorot'].',
									'.$dress['mfauvorot'].',
									'.$dress['bron1'].',
									'.$dress['bron2'].',
									'.$dress['bron3'].',
									'.$dress['bron4'].',
									'.$dress['maxu'].',
									'.$dress['minu'].',
									'.$dress['magic'].',
									'.$dress['nlevel'].',
									'.$dress['nalign'].',
										"0",0,
									'.$dress['razdel'].',
									"0",
									'.$dress['group'].',"'.mysql_real_escape_string($dress['letter']).'",0,0,0,0,"",0,0,0,"0",'.$usr['id_city'].'
							)
						');

					if ($get_ivent['stat']==1) //если недел€ противосто€ни€
							{
							$mk_bonus_items=0;
							$rmnt=mt_rand(1,100); //при получении осколка черепа вместе с ним падает с веро€тностью: 20% - ничего 40%- хилка 180 40% - аркан противосто€ни€
								if (($rmnt>=20) and ($rmnt<=60))
									{
									$mk_bonus_items=5205;
									}
								elseif ($rmnt>=60)
									{
									$mk_bonus_items=155155;
									}

									if ($mk_bonus_items>0)
									{
									//выдаем
									put_bonus_item($mk_bonus_items,$usr,'ѕротивосто€ние');
									}
							}

					}
				}
			}



		}
		mysql_query('DELETE FROM op_battle_index WHERE battle = '.$bat['id']);
	}


				else
				//турнирные одиночные бои
				if (($btype >210 )and ( $btype <232 ))
				{
				///турнирные бои
					if ($winner_t==1)
					{
					//кто в тиме два выбыл
					$telo=mysql_fetch_array(mysql_query("select * from users WHERE `battle`={$bat['id']} and `battle_t`!=$winner_t;"));
					exit_dress($telo,210);
					//ID типа
			                $check_quest[]=11;
					//add to log turn
				  	mysql_query_100("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> ".BNewRender($bat['t2hist'])." <b> выбывает из турнира</b><BR>') WHERE  `type`='{$btype}' and active=1;");
					if ($bat['t3hist']!='') { mysql_query_100("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> ".BNewRender($bat['t3hist'])." <b> выбывает из турнира</b><BR>') WHERE  `type`='{$btype}' and active=1;"); }
					}
					else if ($winner_t==2)
					{
					//тот кто в тиме 1 выбыл
					$telo=mysql_fetch_array(mysql_query("select * from users WHERE `battle`={$bat['id']} and `battle_t`!=$winner_t;"));
					exit_dress($telo,210);
					$check_quest[]=11;
					//add to log turn
				  	mysql_query_100("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> ".BNewRender($bat['t1hist'])." <b> выбывает из турнира</b><BR>') WHERE  `type`='{$btype}' and active=1;");
					if ($bat['t3hist']!='') { mysql_query_100("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> ".BNewRender($bat['t3hist'])." <b> выбывает из турнира</b><BR>') WHERE  `type`='{$btype}' and active=1;"); }
					}
					else if ($winner_t==3)
					{
					//тот кто в тиме 1 выбыл
					$telo=mysql_fetch_array(mysql_query("select * from users WHERE `battle`={$bat['id']} and `battle_t`!=$winner_t;"));
					exit_dress($telo,210);
					$check_quest[]=11;
					//add to log turn
				  	mysql_query_100("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> ".BNewRender($bat['t1hist'])." <b> выбывает из турнира</b><BR>') WHERE  `type`='{$btype}' and active=1;");
				  	mysql_query_100("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> ".BNewRender($bat['t2hist'])." <b> выбывает из турнира</b><BR>') WHERE  `type`='{$btype}' and active=1;");
					}
					//вешаем эфект типу победившей команды
				  	$efti=time()+300;
				  	if ($win_t==0)
				  	   {
				  	   mysql_query_100("INSERT INTO `effects` SET `type`=77,`name`='«ащита от нападени€',`time`='{$efti}',`owner`=(select id from users where battle={$bat['id']} and battle_t=1 LIMIT 1)");
				  	   mysql_query_100("INSERT INTO `effects` SET `type`=77,`name`='«ащита от нападени€',`time`='{$efti}',`owner`=(select id from users where battle={$bat['id']} and battle_t=2 LIMIT 1)");
				  	   if ($bat['t3hist']!='')
				  	   	{
				  	   mysql_query_100("INSERT INTO `effects` SET `type`=77,`name`='«ащита от нападени€',`time`='{$efti}',`owner`=(select id from users where battle={$bat['id']} and battle_t=3 LIMIT 1)");
				  	   	}
				  	   }
				  	   else
				  	   {
				  	   mysql_query_100("INSERT INTO `effects` SET `type`=77,`name`='«ащита от нападени€',`time`='{$efti}',`owner`=(select id from users where battle={$bat['id']} and battle_t={$winner_t} LIMIT 1)");
				  	   }
				}
				else
 				if (($btype >240 )and ( $btype <269 ))
 					{
 					//турниры  √рупповое сражение
					//add to log turn
				  	//mysql_query("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> ".BNewRender($bat[t1hist])." <b> погиб </b><BR>') WHERE  `type`='{$btype}' and active=1;");
					//mysql_query("UPDATE tur_grup SET `active`=0, `battle`=0  WHERE `battle`= '".$bat['id']."';");
					mysql_query_100("UPDATE tur_stat  SET `start`=2  WHERE `battle`= '".$bat['id']."';");
					//ставим эфект на следующее посещение
					//добавл€ем эфект 8240


					$usr_eff=mysql_query("select id, login, (SELECT add_info FROM `effects` WHERE `owner` = u.id  AND type=9105) as add_info  from users u where battle={$bat['id']}  and battle_t=1");
					while ($ue = mysql_fetch_array($usr_eff))
					{

						if ($ue['add_info']>0)
						{
						  $need_bat=(int)(10-(10*$ue['add_info'])); //  есть бонус
						}
						else
						{
						  $need_bat=10;
						}


						//mysql_query_100("INSERT INTO `effects` SET `type`=8240,`name`='—ледующее посещение √рупповые сражени€',`time`=".(time()+$def_time)." ,`owner`={$ue['id']} ");
						//mysql_query_100("DELETE FROM `oldbk`.`ristalka` WHERE `owner`={$ue['id']} ");

						mysql_query_100("UPDATE `oldbk`.`ristalka` SET `chaos`=`chaos`-'{$need_bat}' WHERE `owner`={$ue['id']} ");

					}


 					//обновл€ем рум и чистим id_grup
 					mysql_query_100("UPDATE users set room=240, id_grup=0 where battle={$bat['id']} and battle_t=1 ;");

 					}
				else if (($btype >270 )and ( $btype <299 ))
					{
					//—ражение отр€дов
					if (($winner_t==1)OR($winner_t==2) OR ($winner_t==3)) // если бой таки ноормально закончилс€ т.е. не ничью боева€
					{
						$ttt="t".$winner_t;
						$lead_win_id=explode(";",$bat[$ttt]);
						$lead_win_id=$lead_win_id[0];
						//получаеm лидера команды победителей- по нему будем работать с отр€дом
						if ($winner_t==1)
							{
							//выбрасываем тех кто проиграл и пишем лог
							mysql_query_100("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> <b>".BNewRender($bat['t2hist'])." выбывают из турнира</b><BR>') WHERE  `type`='{$btype}' and active=1;");
							if ($bat['t3hist']!='') { mysql_query("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> <b>".BNewRender($bat['t3hist'])." выбывают из турнира</b><BR>') WHERE  `type`='{$btype}' and active=1;"); }
							}
						 else if ($winner_t==2)
							{
							//выбрасываем тех кто проиграл и пишем лог
							mysql_query_100("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> <b>".BNewRender($bat['t1hist'])." выбывают из турнира</b><BR>') WHERE  `type`='{$btype}' and active=1;");
							if ($bat['t3hist']!='') { mysql_query_100("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> <b>".BNewRender($bat['t3hist'])." выбывают из турнира</b><BR>') WHERE  `type`='{$btype}' and active=1;"); }
							}
						else if ($winner_t==3)
							{
							//выбрасываем тех кто проиграл и пишем лог
							mysql_query_100("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> <b>".BNewRender($bat['t1hist'])." выбывают из турнира</b><BR>') WHERE  `type`='{$btype}' and active=1;");
							mysql_query_100("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> <b>".BNewRender($bat['t2hist'])." выбывают из турнира</b><BR>') WHERE  `type`='{$btype}' and active=1;");
							}

					//удал€ем проигравшую сторону
					//по ид бо€ где овнер1 не равен победившей стороне
//					mysql_query("DELETE from tur_grup where battle={$bat['id']} and owner1<>{$lead_win_id} ; ");

					//выводим проигравшую сторону!!!

					$exit1=mysql_query("SELECT * from  users  WHERE  `battle`={$bat['id']} and `battle_t`!={$winner_t};");
					$del_ok=0;
				         while($gorow=mysql_fetch_array($exit1))
					  {
					  exit_dress($gorow,270);

							if ($del_ok!=1)   //удал€ем ту группу из тур_груп где чуваки которых  убили и мы их выгон€ем
							{
							mysql_query_100("DELETE from tur_grup where id={$gorow['id_grup']} ");
							$del_ok=1;
							}
					   }

					}
					//вешаем эфект  по лидеру победившей команды и ид бо€ и убираем ид бо€

				  	$efti=time()+300;
					mysql_query_100("update tur_grup set battle=0, stat={$efti} where battle={$bat['id']}  ; ");
					}
					else if (($btype==304) OR ( $btype==308) or ($btype==1210)) //1210 - спец турнир
					{
					$turnir_id=0;
					$go_out_status=0;
					//бои новые - турниры
							if ($winner_t==1)
							{
								$go_out=2;
							}
							elseif ($winner_t==2)
							{
								$go_out=1;
							}
							else
							{
							//ничь€
							$go_out_status=1;
							$d1 = 0; $d2 = 0;
							$q = mysql_query('SELECT  u.battle_t, sum(damage) as alldmg  from users u LEFT JOIN battle_dam_exp dam on dam.owner=u.id and dam.battle=u.battle where u.battle='.$bat['id'].' group by battle_t');
							while($u = mysql_fetch_assoc($q))
								{
								if ($u['battle_t'] == 1)
									{
									$d1 = $u['alldmg'];
									}
								else
								if ($u['battle_t'] == 2)
									{
									$d2 = $u['alldmg'];
									}
								}

								if ($d1 > $d2)
								{
								$go_out = 2;
								}
								else if ($d1 == $d2)
								{
								$go_out_status=2;
								$go_out = mt_rand(1,2);
								}
								else
								{
								$go_out = 1;
								}
							}
					///////////////////////////////////////////////////
					if  ($btype==1210)
								{
								//данные о шаге спец. турнира
								$tur_windata=mysql_fetch_array(mysql_query("select * from stur_users where battle='{$bat['id']}' "));
								}

					$u=array();
					//грузим всех дл€ работы
					$q = mysql_query("SELECT * FROM users WHERE battle = '{$bat['id']}' ");
					$count_out=0; // счет проигравших
					$count_win=0;//счет победителей


					while($u = mysql_fetch_assoc($q))
					{
						if (($turnir_id==0) AND ($u['id_grup']>0)) { $turnir_id=$u['id_grup']; }

						if ($u['battle_t']==$go_out)
							{
							//масив проигравших
							$count_out++;
							$go_out_arr[$u['id']]=$u;
							$go_out_team[]=$u['id'];

									if  ($btype==1210)
									{
									//тут дл€ спец турнира дл€ проигравшего правим таблицу  турнира
									mysql_query("UPDATE `stur_users` SET `t".$go_out."_owner`=0 , stat=10 , krug=krug-2, battle=0 WHERE  battle = '{$bat['id']}'  and  t".$go_out."_owner='{$u['id']}'    ");
									}

							}
							else
							{
							//масив победителей
							$count_win++;
							$win_arr[$u['id']]=$u;
							$win_team[]=$u['id'];


							}
					}
							//// отправл€ем системку проиграшей тиме
							if  ($btype==1210)
							{
								if ($go_out_status==0)
								{
								addch_group('<font color=red>¬нимание!</font> ¬ы проиграли бой и выбываете из турнира!', $go_out_team);
								}
								elseif ($go_out_status==1)
								{
								addch_group('<font color=red>¬нимание!</font> ¬ы набили меньше урона в бою и выбываете из турнира!', $go_out_team);
								}
								elseif ($go_out_status==2)
								{
								addch_group('<font color=red>¬нимание!</font> ¬ы выбыли из турнира, проиграли в жеребьевке после бо€ при равном уроне и ничьи!', $go_out_team);
								}



							}
						else
						{
									if ($go_out_status==0)
										{
										addch_group('<font color=red>¬нимание!</font> ¬аша команда проиграла бой и выбыла из турнира!', $go_out_team);
										}
									elseif ($go_out_status==1)
										{
										addch_group('<font color=red>¬нимание!</font> ¬аша команда набила меньше урона в бою и выбыла из турнира!', $go_out_team);
										}
									elseif ($go_out_status==2)
										{
										addch_group('<font color=red>¬нимание!</font> ¬аша команда выбыла из турнира, проиграла в жеребьевке после бо€ при равном уроне и ничьи!', $go_out_team);
										}
						}

					////////////////////-/-/-/-/-/-/-/-////////////////////////////////
					//грузим травмы если были получены в бою - что бы отн€ть их из реальных статов
					$get_travm_all=mysql_query("select * from effects where battle='{$bat['id']}' and type in (11,12,13,14)");
					while($travms= mysql_fetch_assoc($get_travm_all))
					{
						$row_travm[$travms['owner']]=$travms;
					}
					////////////////////-/-/-/-/-/-/-/-////////////////////////////////
					// удалить шмот турнира - у всех
					mysql_query_100("delete from oldbk.inventory where bs_owner=3 and owner in (select id from users where battle='{$bat['id']}')");
					////////////////////-/-/-/-/-/-/-/-////////////////////////////////
					//пишем в лог кто выбывает
					$outhist="t".$go_out."hist";
					$logtext="<span class=date2>".date("d.m.y H:i")."</span> <b>¬ыбывают:</b>".BNewRender($bat[$outhist])."<BR>";
					if  ($btype==1210)
					{


						mysql_query_100("UPDATE `stur_logs` SET  `logs`= CONCAT(`logs`,'{$logtext}')  WHERE id='{$turnir_id}' ");


					}
					else
					{
					mysql_query_100("UPDATE `ntur_logs` SET  `logs`= CONCAT(`logs`,'{$logtext}')  WHERE id='{$turnir_id}' ");
					}
					//////////////////////////////////////////////////////////////////////////
					if ($count_win>1)
					{
					//грузим дефалтовые профили -дл€ сброса выигравшей тиме

								//удалить травмы которые были получены в бою - в команде победителей
								mysql_query_100("delete from effects where battle='{$bat['id']}' and type in (11,12,13,14) and owner in (".implode(",",$win_team).") ");

								//загрузить им по новой их дефалтовый профиль
								$get_load_prof=mysql_query("SELECT * FROM `ntur_profile` WHERE `owner` in (".implode(",",$win_team).")   AND `def` = 1") ;
								 while ($prof_row = mysql_fetch_array($get_load_prof))
								 {
								 $owners_prof[$prof_row['owner']]=$prof_row;
								 }

						$mas[304] = 5;
						$mas[308] = 9;
						$mas[1210] = 9;
						//примен€ем профиль
						foreach($win_team as $k=>$v)
						{

								 if  ($owners_prof[$v]['id']>0)
							 	{
							 	// есть профиль
								mysql_query_100('UPDATE `users` SET
								`sila` = "'.$owners_prof[$v]['sila'].'",
								`lovk` = "'.$owners_prof[$v]['lovk'].'",
								`inta` = "'.$owners_prof[$v]['inta'].'",
								`vinos` = "'.$owners_prof[$v]['vinos'].'",
								`intel` = "'.$owners_prof[$v]['intel'].'",
								`mudra` = "'.$owners_prof[$v]['mudra'].'",
								`sergi`=0,`kulon`=0,`perchi`=0,	`weap`=0,`bron`=0,`r1`=0,`r2`=0,`r3`=0,	`helm`=0,`shit`=0,`boots`=0,`m1`=0,`m2`=0,`m3`=0,`m4`=0,`m5`=0,	`m6`=0,`m7`=0,	`m8`=0,	`m9`=0,	`m10`=0,`m11`=0,`m12`=0,`m13`=0,`m14`=0,`m15`=0,`m16`=0,`m17`=0,`m18`=0,`m19`=0,`m20`=0,`nakidka`=0,`rubashka`=0, `runa1`=0 , `runa2`=0 , `runa3`=0  ,`stats` = 0,`noj` = 0,	`mec` = 0,`topor` = 0,	`dubina` = 0,`mfire` = 0,`mwater` = 0,`mair` = 0,`mearth` = 0,`mlight` = 0,`mgray` = 0,	`mdark` = 0,
								`master` = "'.$mas[$btype].'",
								`maxhp` = "'.($owners_prof[$v]['vinos']*6).'",
								`hp` = "'.($owners_prof[$v]['vinos']*6).'",
								`bpbonussila` = 0, `mana` = 0, 	`maxmana` = 0, 	`bpbonushp` = 0 WHERE `id` = '.$v );
							 	}
							 	else
							 	{
							 	//нет профил€ - просто сброс статов
							 	//ставим пустые - взависимости от типа турнира
								$asts[304]=34;
								$avin[304]=7;
								$ahp[304]=42;

								$asts[308]=78;
								$avin[308]=11;
								$ahp[308]=66;

								$asts[1210]=78;
								$avin[1210]=11;
								$ahp[1210]=66;

								$vinos=$avin[$btype];
								$hp=$ahp[$btype];
								$stats=$asts[$btype];

								$master=$mas[$btype];

							mysql_query_100('UPDATE `users` SET
							`sila` = "3",`lovk` = "3",`inta` = "3",	`vinos` = "'.$vinos.'",	`intel` = "0",	`mudra` = "0",	`stats` = "'.$stats.'",	`sergi`=0,`kulon`=0, `perchi`=0,`weap`=0,`bron`=0,`r1`=0,`r2`=0,`r3`=0,	`helm`=0,`shit`=0,`boots`=0,`m1`=0,`m2`=0,`m3`=0,`m4`=0,`m5`=0,	`m6`=0,	`m7`=0,	`m8`=0,	`m9`=0,	`m10`=0,`m11`=0,`m12`=0,`m13`=0,`m14`=0,`m15`=0,`m16`=0,`m17`=0,`m18`=0,`m19`=0,`m20`=0,`nakidka`=0,`rubashka`=0 , `runa1`=0 , `runa2`=0 , `runa3`=0  ,`noj` = 0,`mec` = 0,`topor` = 0,`dubina` = 0,	`mfire` = 0,`mwater` = 0,`mair` = 0,`mearth` = 0,`mlight` = 0,`mgray` = 0,`mdark` = 0,	`master` = "'.$master.'",
							`maxhp` = "'.$hp.'",
							`hp` = "'.$hp.'",
							`bpbonussila` = 0,`mana` = 0,`maxmana` = 0,`bpbonushp` = 0 WHERE `id` = '.$v );

				 				}

						}


								// обновить запись о турнире
								// статус 3 это состо€ние после бо€ - дл€ новой дележки на команду и раздачу шмоток
								$tur_bag_status_fix=true;


					}
					else
					{

					if  ($btype==1210)
						{
									if ($go_out==1) { $wintm=2;} else {$wintm=1;}
									$zzhist="t".$wintm."hist";

									if ($tur_windata['krug']==0)
									{
									// это был финальный бой
									$go_out_team=array_merge($go_out_team,$win_team);
									$logtext="<span class=date2>".date("d.m.y H:i")."</span> <b>“урнир окончен, победил:</b>".BNewRender($bat[$zzhist])."<BR>";
									mysql_query_100("UPDATE `stur_logs` SET `active`='0' , end_time=".time()." ,  `logs`= CONCAT(`logs`,'{$logtext}') , winer='".BNewRender($bat[$zzhist])."'   WHERE id='{$turnir_id}' ");

									//чистимс€
									mysql_query("delete  from stur_users where stur='{$turnir_id}' ");

									}
									else
									{
									//переход победител€ дальше
									$logtext="<span class=date2>".date("d.m.y H:i")."</span> <b> ¬ 1/".$tur_windata['krug']." победил:</b>".BNewRender($bat[$zzhist])."<BR>";

									mysql_query_100("UPDATE `stur_logs` SET  `logs`= CONCAT(`logs`,'{$logtext}')  WHERE id='{$turnir_id}' ");
									}
						}
						else
						{
						//если  в победившей команде 1 тело то выгон€ем его тоже подключив к масиву выход€щих
						$go_out_team=array_merge($go_out_team,$win_team);

								//и в запись о турнире пишем что он закончен
									// статус 4 это состо€ние после бо€ - когда турнир окончен
									mysql_query_100("UPDATE `ntur_users` SET `battle`='0' , stat=4, stat_time=NOW()   WHERE battle='{$bat['id']}' ");

									if ($go_out==1) { $wintm=2;} else {$wintm=1;}
									$zzhist="t".$wintm."hist";
									$logtext="<span class=date2>".date("d.m.y H:i")."</span> <b>“урнир окончен, победил:</b>".BNewRender($bat[$zzhist]);
									mysql_query_100("UPDATE `ntur_logs` SET `active`='0' , end_time=".time()." ,  `logs`= CONCAT(`logs`,'{$logtext}') , winer='".BNewRender($bat[$zzhist])."'   WHERE id='{$turnir_id}' ");
						}
					}

					////////////////////-/-/-/-/-/-/-/-////////////////////////////////
					//грузим реальные профили дл€ выхода
					$get_all_real_stat=mysql_query("select * from ntur_realchars where owner in  (".implode(",",$go_out_team).") ");
					while($real_stat = mysql_fetch_assoc($get_all_real_stat))
					{
						$row_real_st[$real_stat['owner']]=$real_stat;
					}


					$ttco=0;
					$add_kr_sql='';

					$exp_arr[4]=array(1=>500,2=>250,3=>125,4=>125,5=>50);
					$exp_arr[5]=array(1=>1000,2=>500,3=>250,4=>250,5=>100);
					$exp_arr[6]=array(1=>2000,2=>1000,3=>500,4=>500,5=>200);
					$exp_arr[7]=array(1=>4000,2=>2000,3=>1000,4=>1000,5=>400);
					$exp_arr[8]=array(1=>6000,2=>3000,3=>1500,4=>1500,5=>600);
					$exp_arr[9]=array(1=>10000,2=>5000,3=>2500,4=>2500,5=>1000);
					$exp_arr[10]=array(1=>20000,2=>10000,3=>5000,4=>5000,5=>2000);
					$exp_arr[11]=array(1=>40000,2=>20000,3=>10000,4=>10000,5=>4000);
					$exp_arr[12]=array(1=>80000,2=>40000,3=>20000,4=>20000,5=>8000);
					$exp_arr[13]=array(1=>160000,2=>80000,3=>40000,4=>40000,5=>16000);
					$exp_arr[14]=array(1=>240000,2=>100000,3=>60000,4=>60000,5=>32000);


					//провер€ем ивент
					/*$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=4"));
					if ($get_ivent['stat']==1)
					{
					$expkof=1.5;
					$addrepkof=1.5;
					}
					else
					*/

					$expkof=1;
					$addrepkof=1;


					 foreach($go_out_team as $k=>$v)
					   {
					   $ttco++;
					   	//выдаем призы + системка
						if (($count_out==16) and ($count_win==16))
							{

							$add_kr=0; $add_kr_sql="";  $add_rep=0; $add_exp=0;
							$rep_rez=put_item_turn(6,$btype,$go_out_arr[$v],$add_exp,$add_kr,$add_rep); // награда 6е место
							$add_sql_rep[$v]="";
							$add_sql_exp[$v]="";
							}
					   	else
						if (($count_out==8) and ($count_win==8))
							{
							//$add_exp=(int)($exp_arr[$go_out_arr[$v]['level']][5]*$expkof);
							$add_exp=0;
							$add_kr=0;
							$add_rep=0;

							$rep_rez=put_item_turn(5,$btype,$go_out_arr[$v],$add_exp,$add_kr,$add_rep); // награда 5е место
							$add_sql_exp[$v]=""; //$add_sql_exp[$v]=" `exp`=`exp`+{$exp_rez} ,  ";
							$add_sql_rep[$v]=""; //$add_sql_rep[$v]=" rep=rep+{$rep_rez} , repmoney=repmoney+{$rep_rez} ,  ";
							}
					   	else
						if (($count_out==4) and ($count_win==4))
							{
							//$add_exp=(int)($exp_arr[$go_out_arr[$v]['level']][4]*$expkof);
							$add_exp=0;
							$add_kr=0;
							$add_rep=0;

							$rep_rez=put_item_turn(4,$btype,$go_out_arr[$v],$add_exp,$add_kr,$add_rep); // награда 4е место
							$add_sql_exp[$v]=""; //$add_sql_exp[$v]=" `exp`=`exp`+{$exp_rez} ,  ";
							$add_sql_rep[$v]=" rep=rep+{$rep_rez} , repmoney=repmoney+{$rep_rez} ,  ";

							}
					   	else
						if (($count_out==2) and ($count_win>1))
							{
							//$add_exp=(int)($exp_arr[$go_out_arr[$v]['level']][3]*$expkof);
							$add_exp=0;
							$add_kr=0;

							if ($btype==304) { $add_rep=100;  } elseif ($btype==308)  { $add_rep=250;  }
							$add_rep=$add_rep*$addrepkof;

							if ($go_out_arr[$u]['prem']>0) {$add_rep=$add_rep*1.1;}
							$add_rep=(int)($add_rep);

							$rep_rez=put_item_turn(3,$btype,$go_out_arr[$v],$add_exp,$add_kr,$add_rep); // награда 3е место
							$add_sql_exp[$v]=""; //$add_sql_exp[$v]=" `exp`=`exp`+{$exp_rez} ,  ";
							$add_sql_rep[$v]=" rep=rep+{$rep_rez} , repmoney=repmoney+{$rep_rez} ,  ";

							}
							else if (($count_out==1) and ($ttco==1) )
							{
							//$add_exp=(int)($exp_arr[$go_out_arr[$v]['level']][2]*$expkof);
							$add_exp=0;
							$add_kr=0;

							if ($btype==304) { $add_rep=200;  } elseif ($btype==308)  { $add_rep=500;  }
							$add_rep=$add_rep*$addrepkof;

							if ($go_out_arr[$u]['prem']>0) {$add_rep=$add_rep*1.1;}
							$add_rep=(int)($add_rep);

							$rep_rez=put_item_turn(2,$btype,$go_out_arr[$v],$add_exp,$add_kr,$add_rep); // награда 2е место
							$add_sql_exp[$v]=""; //$add_sql_exp[$v]=" `exp`=`exp`+{$exp_rez} ,  ";
							$add_sql_rep[$v]=" rep=rep+{$rep_rez} , repmoney=repmoney+{$rep_rez} ,  ";

							}
							else if (($count_out==1) and ($ttco==2) )
							{
							//$add_exp=(int)($exp_arr[$win_arr[$v]['level']][1]*$expkof);
							$add_exp=0;
							$add_kr=0;

							if ($btype==304) { $add_rep=400;  } elseif ($btype==308)  { $add_rep=1000;  }
							$add_rep=$add_rep*$addrepkof;

							if ($go_out_arr[$u]['prem']>0) {$add_rep=$add_rep*1.1;}
							$add_rep=(int)($add_rep);

							$rep_rez=put_item_turn(1,$btype,$win_arr[$v],$add_exp,$add_kr,$add_rep); // награда 1е место
							$add_sql_exp[$v]=""; //$add_sql_exp[$v]=" `exp`=`exp`+{$exp_rez} ,  ";
							$add_sql_rep[$v]=" rep=rep+{$rep_rez} , repmoney=repmoney+{$rep_rez} ,  ";
							}
							else
							{
							put_item_turn(0,$btype,$go_out_arr[$v],0,0,0); // награда остальным - эфект только
							$add_sql_exp[$v]="";
							$add_sql_rep[$v]="";
							}

					  $out_room=270;
					  if ($btype==1210) {  $out_room=210; }

					   // выгон€ем + ставим реальные статы + если  надо добавл€ем репу
					   	mysql_query_100('UPDATE `users` SET
							`sila` = "'.($row_real_st[$v]['sila']-$row_travm[$v][sila]).'",
							`lovk` = "'.($row_real_st[$v]['lovk']-$row_travm[$v][lovk]).'",
							`inta` = "'.($row_real_st[$v]['inta']-$row_travm[$v][inta]).'",
							`vinos` = "'.$row_real_st[$v]['vinos'].'",
							`intel` = "'.$row_real_st[$v]['intel'].'",
							`mudra` = "'.$row_real_st[$v]['mudra'].'",
							'.$add_sql_exp[$v].'
							`sergi`=0,
							`kulon`=0,
							`perchi`=0,
							`weap`=0,
							`bron`=0,
							`r1`=0,
							`r2`=0,
							`r3`=0,
							`helm`=0,
							`shit`=0,
							`boots`=0,
							'.$add_kr_sql.'
							`m1`=0,
							`m2`=0,
							`m3`=0,
							`m4`=0,
							`m5`=0,
							`m6`=0,
							`m7`=0,
							`m8`=0,
							`m9`=0,
							`m10`=0,
							`m11`=0,
							`m12`=0,
							`m13`=0,
							`m14`=0,
							`m15`=0,
							`m16`=0,
							`m17`=0,
							`m18`=0,
							`m19`=0,
							`m20`=0,
							'.$add_sql_rep[$v].'
							`nakidka`=0,
							`rubashka`=0,
							`runa1`=0 ,
							`runa2`=0 ,
							`runa3`=0 ,
							`stats` = "'.$row_real_st[$v]['stats'].'",
							`noj` = "'.$row_real_st[$v]['noj'].'",
							`mec` = "'.$row_real_st[$v]['mec'].'",
							`topor` ="'.$row_real_st[$v]['topor'].'",
							`dubina` = "'.$row_real_st[$v]['dubina'].'",
							`mfire` = "'.$row_real_st[$v]['mfire'].'",
							`mwater` = "'.$row_real_st[$v]['mwater'].'",
							`mair` = "'.$row_real_st[$v]['mair'].'",
							`mearth` = "'.$row_real_st[$v]['mearth'].'",
							`mlight` = "'.$row_real_st[$v]['mlight'].'",
							`mgray` = "'.$row_real_st[$v]['mgray'].'",
							`mdark` = "'.$row_real_st[$v]['mdark'].'",
							`master` = "'.$row_real_st[$v]['master'].'",
							`maxhp` = "'.(($row_real_st[$v]['vinos']*6)+$row_real_st[$v]['bpbonushp']).'",
							`hp` = "0",
							`bpbonussila` = "'.$row_real_st[$v]['bpbonussila'].'",
							`mana` = "'.($row_real_st[$v]['mudra']*10).'",
							`maxmana` = "'.($row_real_st[$v]['mudra']*10).'",
							`bpbonushp` = "'.$row_real_st[$v]['bpbonushp'].'",
							`id_grup`=0,
							`in_tower`=0,
							`room`="'.$out_room.'"
						WHERE `id` = '.$v.' and battle='.$bat['id'].' LIMIT 1 ') ;

						$text=print_r($row_real_st[$v], true);
						$text.="\n";
						$text.=print_r($row_travm[$v], true);
						$text.="--------------\n";
						 log_stats_adm($text);




					   }

						////апдейт статуса турнира после того как все чары вышли
						if (($tur_bag_status_fix==true) and ($btype==1210))
						{


						}
						elseif (($tur_bag_status_fix==true) and ($btype!=1210))
						{
						mysql_query_100("UPDATE `ntur_users` SET `battle`='0' , stat=3 , stat_time=NOW()   WHERE battle='{$bat['id']}' ");
						}


					}


////////////////////////////////////////////////////////
// проверка на бота в бою
		if ($winner_t>0)
		 {

		 	if ($bat['type']==314)
			{
			if ($winner_t==1)
				{
				//победил человек
				//дополнительный дроп из лабовскихботов	 в локации с призывными ботами
						include "labconfig_4.php";
						$test_bot=mysql_fetch_array(mysql_query("select * from users_clons where battle='{$bat['id']}'  LIMIT 1"));
						if ($test_bot['id']>0)
						{
							$protobot_id=$test_bot['id_user'];
							$protobot_drop=$mdrop[$protobot_id];
							if (is_array($protobot_drop))
								{
									foreach ($protobot_drop as $iditema=>$shans)
									{
									 if ($iditema>0) //убираем 0е значени€
									 	{
											for ($ss=0;$ss<$shans;$ss++)
												{
												$cs++;
												$ar_shans[$cs]=$iditema;
												}
										}
									}
									if (count($ar_shans)>0)
										{
										shuffle($ar_shans);//мешаем
										}
									$rnd=mt_rand(0,count($ar_shans));
									if($ar_shans[$rnd]>0)
									{
									$wintelo= mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE battle='{$bat['id']}'  limit 1" ));
									put_bonus_item($ar_shans[$rnd],$wintelo,'”дача');
									}
								}
						}
				}
			}
			else
			{
				 //победила команда 1
				 //во второй команде был бот который умер?
				 	$test_bot=mysql_fetch_array(mysql_query("select * from users_clons where ((id_user>=101 and id_user<=110) OR (id_user>=302 and id_user<=309) OR id_user=395467) and battle='{$bat['id']}' and battle_t!={$winner_t} "));

				 	if ($test_bot['id']>0 && $test_bot['hp']<=0) //победили люди из команды 1 против бота
			 		{
			 		   do_chaos_items($winner_t,$bat,$test_bot['id_user']);
		           			$check_quest[]=10;
			 		}
					elseif($test_bot['id']>0 && $test_bot['hp']>0) //победил бот
					{
		           			$check_quest[]=10;
					}
			}
		 }



//////////////////////////////////////////////
	// и так
	//в статусных бо€х следующие изменени€:
	//если бой статусный то в конце бо€:
	//победивша€ сторона получает репу в размере 4% от полученного опыта
	//проигравша€ сторона получает репу в размере 0.5% от теоретически полученного опыта
	//проигравша€ сторона получает 10-20-30% опыта в зависимости от статуса бо€

	if ($bat['coment'] == "<b>Ѕой на ÷ентральной площади</b>" )
	{

		if (count($winowners) > 0) {
			$sql = 'UPDATE oldbk.map_var SET val = val + 1 WHERE owner IN ('.implode(",",$winowners).') AND var = "q32s42"';
			$q = mysql_query_100($sql);
		}

		$get_wes=mysql_fetch_array(mysql_query("SELECT * from `battle_war` where battle='{$bat['id']}' "));
		if  ($get_wes['active']==1)
		{
			$exp_sql=""; //проигравшим ничего
		 	//репутаци€ победившей стороне 2.3% от базового опыта @EE
			$WIN_REP="    rep=rep+ (if( (@REP:=round(@EE*0.023*GetKof(level) ))>2000,2000,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>2000,2000,@REP ) * (if(prem=0,1,1.1)) )   ,  ";



			$EXP_WIN=1.5;
		}
	}

	if ((($bat['status_flag'] > 0 ) and ($bat['status_flag'] !=6 )) or ($bat['type'] ==7) or ($bat['type'] ==8) or ($bat['type'] ==311) or ($bat['type'] ==313) )
	{
	//
		if ($bat['status_flag'] != 4) {
			$tmp = array_merge($winowners,$loserss);
			if (count($tmp) > 0) {
				$sql = 'UPDATE oldbk.map_var SET val = val + 1 WHERE owner IN ('.implode(",",$tmp).') AND var = "q32s71"';
				mysql_query_100($sql);
			}
		} else {
			$tmp = array_merge($winowners,$loserss);
			if (count($tmp) > 0) {
				$sql = 'UPDATE oldbk.map_var SET val = val + 1 WHERE owner IN ('.implode(",",$tmp).') AND var = "q32s72"';
				mysql_query_100($sql);
			}
		}

		if ($bat['status_flag']==1) {
		//20% опыта 0,5% отопыта в репу - проигравшему
		//http://tickets.oldbk.com/issue/oldbk-278

			$exp_sql="  `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*0.2),   ";
		 	//репутаци€ победившей стороне 2.3% от базового опыта @EE
			$WIN_REP="  rep=rep+ (if( (@REP:=round(@EE*0.023*GetKof(level) ))>10000,10000,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>10000,10000,@REP ) * (if(prem=0,1,1.1)) )   ,  ";


			$EXP_WIN=2; //¬елика€ битва - 200% опыта
					  }
		else if ($bat['status_flag'] ==2) {
		//40% опыта 0,5% отопыта в репу - проигравшему

			$exp_sql="  `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*0.4),  ";
		 	//репутаци€ победившей стороне 2.3% от базового опыта @EE
			$WIN_REP="  rep=rep+ (if( (@REP:=round(@EE*0.023*GetKof(level) ))>10000,10000,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>10000,10000,@REP ) * (if(prem=0,1,1.1)) )   ,  ";

			$EXP_WIN=2.5; // Ёпохальна€ битва - 250% опыта
						 }
		 else if (($bat['status_flag'] ==3) OR ($bat['type'] ==311)  )  {
			//30% опыта 0,5% отопыта в репу - проигравшему

			$exp_sql="  `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*0.6),   ";
		 	//репутаци€ победившей стороне 2.3% от базового опыта @EE

		 		if (($bat['coment'] == 'Ѕой с »счадием ’аоса' ) OR ($bat['type'] ==311)  )
		 			{
		 			$max_preps=1000;
		 			}
		 			else
		 			{
		 			$max_preps=10000;
		 			}

			$WIN_REP="  rep=rep+ (if( (@REP:=round(@EE*0.023*GetKof(level) ))>".$max_preps.",".$max_preps.",@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>".$max_preps.",".$max_preps.",@REP ) * (if(prem=0,1,1.1)) )   ,  ";

			$EXP_WIN=3; //Ёпическа€ битва - 300% опыта - 400% опыта
		  }
		  else if ($bat['status_flag'] ==4) {
			//300%+100 опыта 2,3% отопыта в репу - проигравшему

			$exp_sql="  `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*3),   ";
		 	//репутаци€ победившей стороне 2.3% от базового опыта @EE
			$WIN_REP="  rep=rep+ (if( (@REP:=round(@EE*0.023*GetKof(level) ))>10000,10000,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>10000,10000,@REP ) * (if(prem=0,1,1.1)) )   , ";


			$EXP_WIN=5; //500 +100
		  }
		else if ($bat['type'] == 313)  {
		//10% опыта 0,5% отопыта в репу - проигравшему
			$exp_sql="  `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*0.1),   ";
		 	//репутаци€ победившей стороне 2.3% от базового опыта @EE
			$WIN_REP="  rep=rep+ (if( (@REP:=round(@EE*0.023*GetKof(level) ))>500,500,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>500,500,@REP ) * (if(prem=0,1,1.1)) )   ,   ";
			$EXP_WIN=1.2;
		  }
		else if (($bat['coment'] == "<b> уча-мала</b>" ) )  {
		//10% опыта 0,5% отопыта в репу - проигравшему
			$exp_sql="  `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*0.1),   ";
		 	//репутаци€ победившей стороне 2.3% от базового опыта @EE
			$WIN_REP="  rep=rep+ (if( (@REP:=round(@EE*0.023*GetKof(level) ))>2000,2000,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>2000,2000,@REP ) * (if(prem=0,1,1.1)) )   ,   ";
			$EXP_WIN=1.2;
		  }
		else if (($bat['type'] ==7) ) {
		//10% опыта 0,5% отопыта в репу - проигравшему - елкаи

			$exp_sql="  `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*0.1),   ";
		 	//репутаци€ победившей стороне 2.3% от базового опыта @EE
		 	// засчитываетс€ +1 елка только при победе
		 	// и если людей больше 50 тел
		 		if (((count($loserss)+count($winowners))>=50) OR ($bat['coment'] == "<b>#zelka</b>" ))
		 		{
				$WIN_REP=" elkbat=elkbat+1 ,   rep=rep+ (if( (@REP:=round(@EE*0.023*GetKof(level) ))>250,250,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>250,250,@REP ) * (if(prem=0,1,1.1)) )   ,  ";
				}
				else
				{
				//без репы
				$WIN_REP=" elkbat=elkbat+1 ,    ";
				}

					$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=11"));
					if ($get_ivent['stat']==1)
					{
					$EXP_WIN=2.5;
					}
					else
					{
					$EXP_WIN=2;
					}
		  }
		else if ($bat['type'] ==8)
		{
		//бои на букетах
				if ($bat['status_flag'] ==10)
				{
				//"безумный" бой набукетах 200 ограничение в репе
				//10% опыта
				$exp_sql="  `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*0.1),   ";
			 	//репутаци€ победившей стороне 2.3% от базового опыта @EE
				$WIN_REP="  rep=rep+ (if( (@REP:=round(@EE*0.023*GetKof(level) ))>200,200,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>200,200,@REP ) * (if(prem=0,1,1.1)) )   ,  ";
				$EXP_WIN=1.2;
				}
				else
				{
				//обычный 100 ограничение в репе
				//10% опыта
				$exp_sql="  `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*0.1),   ";
			 	//репутаци€ победившей стороне 2.3% от базового опыта @EE
				$WIN_REP="  rep=rep+ (if( (@REP:=round(@EE*0.023*GetKof(level) ))>100,100,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>100,100,@REP ) * (if(prem=0,1,1.1)) )   ,   ";

				$EXP_WIN=1.2;
				}

		  }
		else if ($bat['status_flag'] ==10) {
		//10% опыта 0,5% отопыта в репу - проигравшему
		/*
			 убрать репу за поражение в великах-[16:48:57 27-12-2015] ¬редина: великий хаотичный
		*/
			$exp_sql="  `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*0.1),   ";
		 	//репутаци€ победившей стороне 2.3% от базового опыта @EE
			$WIN_REP="    rep=rep+ (if( (@REP:=round(@EE*0.023*GetKof(level) ))>100,100,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>100,100,@REP ) * (if(prem=0,1,1.1)) )   ,   ";
			$EXP_WIN=1.5;
		  }


	}
	else
	/// в групповых турнирах - получение опыта при поражении
	if (($btype >240 )and ( $btype <269 ))
	{

					//провер€ем ивент
					$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=4"));
					if ($get_ivent['stat']==1)
					{
					$addekof=1.5;
					}
					else
					{
					$addekof=1;
					}

		$exp_sql="  `exp`=`exp`+(ifnull(((select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),0)* ".$addekof.") , ";
	}
	else if ($bat['coment'] =='<b>#zlevels</b>')
	{
			if ($EXP_TO_LOSE==1)
			{
		 	$exp_sql="  `exp`=`exp`+ifnull(((select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})*0.1),0) , ";
			}

			$max_rep=100;
			$WIN_REP="  rep=rep+@REP:=round(if ((@RR:=((ifnull(((select `damage` from battle_dam_exp where users.id=owner and battle={$bat['id']})),0)) / maxhp))>=1,({$max_rep} * ( if(prem=0,1,1.1))) ,(@RR*{$max_rep}*( if(prem=0,1,1.1)) )  ) ), repmoney=repmoney+@REP ,  ";

				//если хаот то смотрим есть ли акци€
					$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=11"));
					if ($get_ivent['stat']==1)
					{
					$EXP_WIN=1.5;
					}
					else
					{
					$EXP_WIN=1.3;
					}
	}
	else if ($bat['type'] ==23)
	{
			if ($EXP_TO_LOSE==1)
			{
		 	$exp_sql="  `exp`=`exp`+ifnull(((select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})*0.1),0) , ";
			}
			$max_rep=100;
			$WIN_REP="  rep=rep+@REP:=round(if ((@RR:=((ifnull(((select `damage` from battle_dam_exp where users.id=owner and battle={$bat['id']})),0)) / maxhp))>=1,({$max_rep} * ( if(prem=0,1,1.1))) ,(@RR*{$max_rep}*( if(prem=0,1,1.1)) )  ) ), repmoney=repmoney+@REP ,  ";
			$EXP_WIN=1;
	}
	elseif ($bat['type']==171) {
	//только опыт 30% - при поражении
		$exp_sql="  `exp`=`exp`+(ifnull(((select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),0)*0.3) , ";
	}
	else
	if (($bat['type']==60) OR ($bat['type']==61) OR ($bat['type']==62) )
		{
            		$check_quest[]=12;
			//echo "2<br>";
			$KO_start_time17=1410118710;
			$KO_fin_time17 =1413201600;//Mon, 13 Oct 2014 12:00:00 GMT

			if ((time()>$KO_start_time17-14400) and (time()<$KO_fin_time17-14400))
			{
			$MY_MONEY=" money=money+GetMyMoney(level,@EE) , "; // начисл€ем бабки за бой согласно уровню и базовому опыту - всем !
			//в дело
	                telepost('Bred','<font color=red>¬нимание!</font> Start...: бой'.$bat['id']);
			      $qv=mysql_query("select u.id, u.login, u.money, be.exp, GetMyMoney(u.level,be.exp) as getmoney  from users u LEFT JOIN battle_dam_exp be ON be.owner=u.id and be.battle={$bat['id']} where u.battle={$bat['id']} and be.exp>99");
				$sql_temp="select u.id, u.login, u.money, be.exp, GetMyMoney(u.level,be.exp) as getmoney  from users u LEFT JOIN battle_dam_exp be ON be.owner=u.id and be.battle={$bat['id']} where u.battle={$bat['id']} and be.exp>99" ;
				$kkk=mysql_num_rows($qv);
				if ($kkk>0)
				{
					              while ($roqv = mysql_fetch_array($qv))
				             		{
						               //new_delo
						                $rec['owner']=$roqv['id'];
						                $rec['owner_login']=$roqv['login'];
						                $rec['owner_balans_do']=$roqv['money'];
						                $rec['owner_balans_posle']=($roqv['money']+$roqv['getmoney']);
								$rec['target']=0; $rec['target_login']='јрена Ѕогов';
								$rec['type']=1515;//получил кр в бою
								$rec['sum_kr']=$roqv['getmoney']; $rec['sum_ekr']=0; $rec['sum_kom']=0; $rec['item_id']='';
								$rec['item_name']=''; $rec['item_count']=0; $rec['item_type']=0; $rec['item_cost']=0;
								$rec['item_dur']=0; $rec['item_maxdur']=0;$rec['item_ups']=0; $rec['item_unic']=0;
								$rec['item_incmagic']=''; $rec['item_incmagic_count']=''; $rec['item_arsenal']='';
								$rec['battle']=$bat['id'];
								add_to_new_delo($rec); //юзеру
						 	//addchp ('<font color=red>Fsystem!</font> ADD_MONEY TO : '.$roqv['login'].' kr:'.$roqv['getmoney'].'  ','{[]}Bred{[]}');
				            		 }
				}
				else
				{
			 	addchp ('<font color=red>Fsystem!</font> ADD_MONEY EROR:'.$sql_temp,'{[]}Bred{[]}');
				}
			}
			else
				{
			 	addchp ('<font color=red>Fsystem!</font> ADD_MONEY EROR2:'.$sql_temp,'{[]}Bred{[]}');
				}

			//10) недел€ арены богов. - в бо€х на арене даетс€ полторы репы, полторы опыта и первые 10 (а не 5) получают перезар€д.
			//провер€ем ивент
					$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=10"));
					if ($get_ivent['stat']==1)
					{
					$arena_week=true; //надо настроить!!!!

					//105% от опыта в бою свет тьма - проигравшим и репа 0,75%

					$exp_sql="  `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*1.35),   "; // 1.05
				 	$EXP_WIN=2.25; // 225% опыта стороне победившей
				 	//репутаци€ победившей стороне 2,58% от базового опыта @EE
					$WIN_REP="    rep=rep+ (if( (@REP:=round(@EE*0.0258*GetKof(level) ))>10000,10000,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>10000,10000,@REP ) * (if(prem=0,1,1.1)) )   ,     ";



					}
					else
					{
					//70% от опыта в бою свет тьма - проигравшим и репа 0,5%

					$exp_sql="  `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*0.9),   ";	//0.7
				 	$EXP_WIN=1.5; // 150%  стороне победившей
				 	//репутаци€ победившей стороне 1,75% от базового опыта @EE
					$WIN_REP="    rep=rep+ (if( (@REP:=round(@EE*0.0175*GetKof(level) ))>10000,10000,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>10000,10000,@REP ) * (if(prem=0,1,1.1)) )   ,     ";
					}



		}
		else if (($bat['CHAOS']>0) and ($bat['type']!=4) and ($bat['type']!=2) and $bat['type']<=20  and ($bat['coment'] != 'Ѕой с »счадием ’аоса' ) )
		{

					//если хаот то смотрим есть ли акци€
					$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=11"));
					if ($get_ivent['stat']==1)
					{
					$EXP_WIN=1.5;
					}

		}
		else if (($bat['type']==6) and ($bat['coment'] == '<b>Ѕой с ¬олнами ƒраконов</b>' ) )
		{
					//недел€ драконов
					$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=14"));
					if ($get_ivent['stat']==1)
					{
					$WIN_REP="  rep=rep+ (if( (@REP:=round(@EE*0.023*GetKof(level) ))>2000,2000,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>2000,2000,@REP ) * (if(prem=0,1,1.1)) )   ,   ";
					}

		}
		else if (($bat['type']==4) or ($bat['type']==2) )
		{
		//группы акци€
			/*
			$tmp = array_merge($winowners,$loserss);
			if (count($tmp) > 0) {
				$sql = 'UPDATE oldbk.map_var SET val = val + 1 WHERE owner IN ('.implode(",",$tmp).') AND var = "q32s43"';
				mysql_query_100($sql);
			}
			*/

					$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=12"));
					if ($get_ivent['stat']==1)
					{
					$EXP_WIN=1.5;
					}

		}

		else
		if ($EXP_TO_LOSE==1)
		{

			// echo "3<br>";
		 	$exp_sql="  `exp`=`exp`+ifnull(((select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})*0.1),0) , ";
		}
		else
		{
		 //echo "4<br>";
		 	$exp_sql="";
		}





		 if(count($check_quest)>0)
         {
           	 $check_battle_quest=false;
			 $types=implode(',',$check_quest);
			 $data=mysql_query('select u.id as uid, bqs.* from users u
			 					left join oldbk.beginers_quests_step bqs
			 					on bqs.owner = u.id
			 					WHERE u.battle = '.$bat['id'].' AND (bqs.qtype in ('.$types.'))  AND bqs.status=0 AND step_f=1');

		     //выбрали все прин€тые квесты нужного типа всех участников битвы. запуливаем их в массив
			 if (mysql_num_rows($data))
			 {
			   while($row=mysql_fetch_assoc($data))
			   {
				   $check_battle_quest[$row['qtype']][$row['uid']]=$row;
			   }
			 }

         	if($check_battle_quest)
			{
			  foreach($check_battle_quest as $qtype=>$users_data)
			  {
			    if($qtype==10) //ключ на боевой квест
			    {
				    foreach($users_data as $uid=>$quest_step)
				    {
				    	quest_check_type_10($quest_step,$uid,'OFF');
				    }
			    }
	    		if($qtype==11) //ключ на участие в ристалище квест
			    {
				    foreach($users_data as $uid=>$quest_step)
				    {
				    	quest_check_type_11($quest_step,$uid,'OFF');
				    }
			    }
			    if($qtype==12) //ключ на арену богов
			    {
				    foreach($users_data as $uid=>$quest_step)
				    {
				    	quest_check_type_12($quest_step,$uid,'OFF');
				    }
			    }
			  }
			}
         }
/////////////////400% опыта типу 62-7 на 7 на арене
	     if ($bat['type']==62)
			{
		 		$EXP_WIN=4; // 400%  стороне победившей
				//репутаци€ победившей стороне 4% от базового опыта @EE
				$WIN_REP="    rep=rep+ (if( (@REP:=round(@EE*0.04*GetKof(level) ))>10000,10000,@REP ) *(if(prem=0,1,1.1)) )  , repmoney=repmoney+(if( @REP>10000,10000,@REP ) * (if(prem=0,1,1.1)) )   ,     ";
			}

    {

     	mysql_query("update battle set win='{$win_t}'  where id={$bat['id']};");
//echo "5<br>";
    	  if (($bat['type']==60) OR ($bat['type']==61) OR ($bat['type']==62) ) // бои свет тьма на арене
	   {
	    if ($bat['type']==62)
	      {
  	      do_item_bonus($bat,$winner_t,10); // бонус только победител€м
	      }
	      else
	      {
	  	$def_limit=5;
		     if ($arena_week==true)    // 10) недел€ арены богов. - в бо€х на арене даетс€ полторы репы, полторы опыта и первые 10 (а не 5) получают перезар€д.
	     		{
			$def_limit=10;
	     		}

           	do_item_bonus($bat,1,$def_limit);
  		do_item_bonus($bat,2,$def_limit);
   		do_item_bonus($bat,3,$def_limit);
 	      }
	   		mysql_query("update place_logs set win={$winner_t}, active=0  where battle={$bat['id']};");
	    if ($winner_t==1)
	       {
	        // выставл€ем поле в свет и добавл€ем очко
				mysql_query("UPDATE `place_battle` SET `val`=6 WHERE `var`='master' ; ");
				mysql_query("UPDATE `place_battle` SET `val`=1 WHERE `var`='winers' ; ");
				mysql_query("UPDATE `place_battle` SET `val`=`val`+1 WHERE `var`='light_count' ; ");
				addch2all('<font color=red>¬нимание!</font> Ѕой <a href=http://oldbk.com/encicl/arena.html target=_blank>'.$bat['coment'].'</a>,  ѕобедил <img src=http://i.oldbk.com/i/align_6.gif> —вет!',CITY_ID);
	       }
	       else if ($winner_t==2)
	        {
	            mysql_query("UPDATE `place_battle` SET `val`=3 WHERE `var`='master' ; ");
			    mysql_query("UPDATE `place_battle` SET `val`=2 WHERE `var`='winers' ; ");
			    mysql_query("UPDATE `place_battle` SET `val`=`val`+1 WHERE `var`='darck_count' ; ");
			    addch2all('<font color=red>¬нимание!</font> Ѕой <a href=http://oldbk.com/encicl/arena.html target=_blank>'.$bat['coment'].'</a>,  ѕобедила <img src=http://i.oldbk.com/i/align_3.gif> “ьма!',CITY_ID);
	        }
	       else if ($winner_t==3)
	        {
	            mysql_query("UPDATE `place_battle` SET `val`=2 WHERE `var`='master' ; ");
			    mysql_query("UPDATE `place_battle` SET `val`=3 WHERE `var`='winers' ; ");
			    mysql_query("UPDATE `place_battle` SET `val`=`val`+1 WHERE `var`='neitral_count' ; ");
			    addch2all('<font color=red>¬нимание!</font> Ѕой <a href=http://oldbk.com/encicl/arena.html target=_blank>'.$bat['coment'].'</a>,  ѕобедили <img src=http://i.oldbk.com/i/align_2.gif> Ќейтралы !',CITY_ID);
	        }
	        else
	        {
	            // выставл€ем поле в ничью
			    mysql_query("UPDATE `place_battle` SET `val`=0 WHERE `var`='master' ; ");
			    mysql_query("UPDATE `place_battle` SET `val`=0 WHERE `var`='winers' ; ");
			    addch2all('<font color=red>¬нимание!</font> Ѕой  <a href=http://oldbk.com/encicl/arena.html target=_blank>'.$bat['coment'].'</a>,  Ќичь€!',CITY_ID);
	        }

	   }

	///////////// -20% дл€ тех кто выпустил наема
	//общий - отключено на врем€ тестов
	//mysql_query("update battle_dam_exp set `exp`=`exp`*0.8 where battle={$bat['id']} and owner in (select owner from users_clons where battle={$bat['id']} and owner>0);");



        if ($winner_t==1)
          {
          //echo "6w<br>";
	          if ($bfond > 0)
	             {
	                $kol=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat['id']} and battle_t=1;"));
			    	$fmoney = round(($bfond*0.9)/$kol[0],2);
		    	$qv=mysql_query("select `id`,`money`,`login` FROM `users` where battle={$bat['id']} and battle_t=1;");

	              while ($roqv = mysql_fetch_array($qv))
	             		{
			              $vId=$roqv['id'];
			              $vBalans=$roqv['money'];
			              $vLogin=$roqv['login'];
			              $vBalans2=$vBalans+$fmoney;
			               //new_delo
			                $rec['owner']=$vId; $rec['owner_login']=$vLogin;  $rec['owner_balans_do']=$vBalans; $rec['owner_balans_posle']=$vBalans2;
					$rec['target']=0; $rec['target_login']='';
					$rec['type']=15;//получил кр в бою
					$rec['sum_kr']=$fmoney;	$rec['sum_ekr']=0; $rec['sum_kom']=0; $rec['item_id']='';
					$rec['item_name']=''; $rec['item_count']=0; $rec['item_type']=0; $rec['item_cost']=0;
					$rec['item_dur']=0; $rec['item_maxdur']=0;$rec['item_ups']=0; $rec['item_unic']=0;
					$rec['item_incmagic']=''; $rec['item_incmagic_count']=''; $rec['item_arsenal']='';
					$rec['battle']=$bat['id'];
					add_to_new_delo($rec); //юзеру

	            		 }

				}
                       else
                       {
                        $fmoney=0;
                       }

	if ($fmoney>0)
		{
		$FMONEY_SQL=" money=money+{$fmoney} , ";
		$MY_MONEY=""; //затираем если было
		}
		else
		{
		$FMONEY_SQL="";
		}

              #battle in lab users win
              if ($btype==30)
              {
              $labqv=mysql_query("select `map`,`x`,`y` FROM `labirint_items` where val='{$bat['id']}';");
               while ($rolab = mysql_fetch_array($labqv))
               {
              $labMAP=$rolab['map'];
              $labX=$rolab['x'];
              $labY=$rolab['y'];

			 mysql_query("UPDATE `labirint_items` SET `val`=0  WHERE `map`='{$labMAP}' and (`item`='R' OR `item`='J'  OR `item`='х'  )  and `x`='{$labX}' and `y`='{$labY}' and `active`=1 ; ");
             		mysql_query("UPDATE `labirint_items` SET `count`=0  WHERE `map`='{$labMAP}' and `item`='I' and `x`='{$labX}' and `y`='{$labY}' and `count`=-1;");
		    }
	       }
			else
	       {
	       //echo "7w<br>";

	       			//загружаем данные по 704  эффектам этого бо€
	       			$get_baff_bat=mysql_query("select owner from effects where battle='{$bat['id']}' and type=704");
	       			 while ($baf_owners = mysql_fetch_array($get_baff_bat))
			   	{
			   	$hava_baf[$baf_owners['owner']]=$baf_owners;
			   	}

		       		if (($bat['type']==601) OR ( $bat['type']==602) OR ( $bat['type']==603) OR ( $bat['type']==604) )
		       		{
		       		// у лорда
				$get_owners=mysql_query("select id from users where battle='{$bat['id']}' and battle_t={$winner_t}");
		       		$BROK=100;
		       		}
		       		else
		       		{
		       		$BROK=0;
		       		//остальные
			       // бой не в лабе  т.е. в других команде не боты надо ломать шмотки
				$get_owners=mysql_query("select id from users where battle='{$bat['id']}' and battle_t!={$winner_t}");
				}

			   if (( ($bat['status_flag'] > 0) OR ($bat['type']==171) ) and ($BROK==0) )
			   {
			   //4. ¬о всех статусных бо€х поломка 50%
			   $BROK=75;
//		   	   $BROK=mt_rand(65,70);
			   }
			   elseif ($BROK==0)
			   {
//   			   $BROK=70;
//			   $min_lim_brok=3;
//			   $max_lim_brok=4;
//		   	   $BROK=mt_rand(75,80);
			   $BROK=80;
			   }

			   while ($r_owners = mysql_fetch_array($get_owners))
			   {
			   	if ($hava_baf[$r_owners['id']])
			   	{
			   	//если на овнере был баф 704 то ломаем все шмотки от 1-х до 2
				 mysql_query("update oldbk.inventory set `duration`= `duration`+floor(RAND()*2+1) where owner={$r_owners['id']} and type!=30 and  battle={$bat['id']} ; ");
			   	}
			   	else
			   	{
				  mysql_query("update oldbk.inventory set `duration`= IF (100*RAND()<".$BROK.",`duration`+1,`duration`) where owner={$r_owners['id']} and type!=30 and  battle={$bat['id']}  ");
				 }
			   }
	       }

		 $wc="";
		//if (!((time()>$KO_start_time5) and (time()<$KO_fin_time5)) )
			{
			//јкци€ - ¬есна не включена!!!!!!!!!
			if ( ($bat['CHAOS']>0) AND ($bat['type']!=5))
			 {
			 //суммируем только тем кто не нападал
			 $wc=" wcount=if(((select napal from battle_vars where owner=users.id and battle={$bat['id']})=0),(`wcount`+1),`wcount`) , ";
			 }
			}


	//запрос - победа дл€ людей

	if ($bat['type']==22)
		{
		//тренеровочные бои
		    if (mysql_query_100("update users set  battle=0,  hiller=0, khiller=0 ,  `fullhptime` = '".time()."'  where battle='{$bat['id']}' and battle_t=1; ") )
		         {
	                // all is good
	               //echo mysql_error();
	                }
	                else
	                {
	                telepost('Bred','<font color=red>¬нимание! fsystem.php str 7600</font> ќшибка начислени€ опыта: тренеровочный бой'.$bat['id']);
	                }

		}
		else
		{
		//обычные бои
                    if (mysql_query_100("update users set `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*{$EXP_WIN}), {$WIN_REP}  win=win+1 ,  {$add_voinst_win}  battle=0, {$WIN_ST_COUNT}   hiller=0, khiller=0 , {$wc} {$FMONEY_SQL} {$MY_MONEY} `fullhptime` = '".time()."'  where battle='{$bat['id']}' and battle_t=1; ") )
		         {
	                // all is good
	               //echo mysql_error();
	                }
	                else
	                {
	                telepost('Bred','<font color=red>¬нимание! fsystem.php str 7615</font> ќшибка начислени€ опыта: бой'.$bat['id']);
	                }

		}





	  $wc="";
	// if (!((time()>$KO_start_time5) and (time()<$KO_fin_time5)) ) - нулим всем
	 {
	 //јкци€ - ¬есна не включена!!!!!!!!!
            if ( ($bat['CHAOS']>0) AND ($bat['type']!=5))
               {
                //сбрасываем в 0  только тем кто не нападал
		$wc=" wcount=if(((select napal from battle_vars where owner=users.id and battle={$bat['id']})=0),0,`wcount`) , ";
               }
	}

	if ($bat['type']==22)
	    {
	    //тренеровочные бои - выход проигравших
 	   	mysql_query_100("UPDATE users SET hp=0, battle=0 ,   hiller=0, khiller=0  , `fullmptime`='".time()."' , `fullhptime` = '".time()."' where battle={$bat['id']} and battle_t!=1;");
	    }
	   else
            if ($blood>0)
            {
             		if ($bat['blood']==2)
             		{
             		//функци€ установки травмы дл€ вендетто
             		$travma_func='settravmatv2_new';
             		}
			else if ($bat['status_flag'] > 0)
             		{
             		// кровь дл€ стутусных 5. “равмы в статусных кровавых бо€х: до 40% легка€, 41- 70% средн€€, 71-100% т€жела€
             		$travma_func='settravma3st_new';
             		}
             		else
             		{
             		//обычна€ кровь
             		$travma_func='settravma3_new';
             		}


		mysql_query_100("UPDATE users SET hp=0, {$wc}  lose=lose+1 , ".$exp_sql." battle=0 , {$add_voinst_lose} {$MY_MONEY} hiller=0, {$LOSE_ST_COUNT} khiller=0 , `fullmptime`='".time()."' , `fullhptime` = '".time()."', sila = IF((@RR:=100*RAND())<30, ".$travma_func."(id,'sila',sila,level,{$bat['id']},{$bat['type']},align,trv,pasbaf), sila), lovk = IF(@RR>=30 AND @RR<60, ".$travma_func."(id,'lovk',lovk,level,{$bat['id']},{$bat['type']},align,trv,pasbaf), lovk), inta = IF(@RR>=60, ".$travma_func."(id,'inta',inta,level,{$bat['id']},{$bat['type']},align,trv,pasbaf), inta) where battle={$bat['id']} and battle_t!=1;");
		$err_text=mysql_error();
		if ($err_text!='') {	addchp ('<font color=red>fsystem.php</font> lose team 1a '.$err_text,'{[]}Bred{[]}'); }

            }
            else
            {

		mysql_query_100("update users set hp=0, lose=lose+1 , ".$exp_sql." battle=0 , {$add_voinst_lose} {$MY_MONEY}  hiller=0, {$LOSE_ST_COUNT} khiller=0 , {$wc}  `fullmptime`='".time()."' ,  `fullhptime` = '".time()."' where battle={$bat['id']} and battle_t!=1;");
		$err_text=mysql_error();
 		 if ($err_text!='') {	addchp ('<font color=red>fsystem.php</font> lose team 1b  '.$err_text,'{[]}Bred{[]}'); }

	    }

		       mysql_query("delete from  battle_user_time  where  battle={$bat['id']};");
		       mysql_query("delete from  battle_fd  where  battle={$bat['id']};");

		       mysql_query("delete from users_clons where (battle ={$bat['id']} AND bot_online != 5 and owner=0) OR (battle ={$bat['id']} AND bot_online = 5 AND hp <= 0 and owner=0) ");

		       mysql_query("delete from  users_hill where battle={$bat['id']};");
		  // дл€ ботов в руинах выгон€ем из бо€
		      if ($bat['type']==11)
		      	{
		      	mysql_query("UPDATE users_clons set battle=0  where battle={$bat['id']} and  bot_online=5 ");
		      	}

		//обработка победы дл€ наймов - ботов которые остались в таблике
		//{$WIN_REP}
		//{$add_voinst_win}
		//`fullhptime` = '".time()."'

                if (mysql_query_100("update users_clons set `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where owner=users_clons.id and battle={$bat['id']})),(@EE:=0))),   win=win+1 ,  {$WIN_ST_COUNT} battle=0, hp=maxhp  ,  `fullentime` = '".time()."'    where battle='{$bat['id']}' and battle_t=1 and owner>0; ") ) //*{$EXP_WIN}
                	{
                	//ok
                	if ($bat[type]==8)
                		{
	                	//telepost('Bred','<font color=red>¬нимание! fsystem.php</font> начислени€ опыта наЄмам тима 1 победа: бой (цветочнй)'.$bat['id']."update users_clons set `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where owner=users_clons.id and battle={$bat['id']})),(@EE:=0))),   win=win+1 ,  {$WIN_ST_COUNT} battle=0, hp=maxhp   where battle='{$bat['id']}' and battle_t=1 and owner>0; ");
                		}
                	}
                	else
                	{
                	//telepost('Bred','<font color=red>¬нимание! fsystem.php</font> ќшибка начислени€ опыта наЄмам тима 1 победа: бой'.$bat['id']);
                	}

		//`fullmptime`='".time()."' ,  `fullhptime` = '".time()."'


		$exp_sql_naem=str_replace('users.','users_clons.',$exp_sql); // подмен€ем название


		if (mysql_query_100("update users_clons set hp=maxhp, lose=lose+1 , {$LOSE_ST_COUNT} battle=0  ,  `fullentime` = '".time()."'   where battle={$bat['id']} and battle_t!=1  and owner>0;"))  //".$exp_sql_naem."
			{
			//ok
			}
			else
			{
			telepost('Bred','<font color=red>¬нимание! fsystem.php</font> ќшибка начислени€ опыта наЄмам тима 2 поражение: бой'.$bat['id']);
			}



		// выставл€ем trv = 0 тем у кого есть эффект защиты от травмы на 1 бой
		$qarr = array();
		$qtrv = mysql_query('SELECT * FROM effects WHERE type = 556 and battle = '.$bat['id']);
		while($qtr = mysql_fetch_assoc($qtrv)) {
			$qarr[] = $qtr['owner'];
		}
		if (count($qarr)) {
			mysql_query('UPDATE oldbk.users SET trv = 0 WHERE id IN ('.implode(",",$qarr).')');
		}

		///”даление остатков боевых эффектов
		mysql_query("delete  from effects where battle='{$bat['id']}' and ((`type`>=700 and `type` <=800) or `type`=805 or `type`=556 or `type`=302 or `type`=430)   ");

		//”даление остатков ничейных данных
		mysql_query("delete  from battle_vars  where battle='{$bat['id']}' and owner=0 ;");

	          RETURN "OK1";
          }
          elseif  ($winner_t==2)
          {

		 $wc="";
		//if (!((time()>$KO_start_time5) and (time()<$KO_fin_time5)) )
			{
			//јкци€ - ¬есна не включена!!!!!!!!!
			 if ( ($bat['CHAOS']>0) AND ($bat['type']!=5))
			 {
			 // +1 только тем кто не нападал
			 $wc=" wcount=if(((select napal from battle_vars where owner=users.id and battle={$bat['id']})=0),(`wcount`+1),`wcount`) , ";
			 }
			}


         	 if ($bfond > 0)
             {
                    $kol=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat['id']} and battle_t=2;"));
		     		$fmoney = round(($bfond*0.9)/$kol[0],2);

		    	$qv=mysql_query("select `id`,`money`,`login` FROM `users` where battle={$bat['id']} and battle_t=2;");
     	  	     while ($roqv = mysql_fetch_array($qv))
             	{
             		$vId=$roqv['id'];
	                $vBalans=$roqv['money'];
	                $vLogin=$roqv['login'];
                    $vBalans2=$vBalans+$fmoney;
			 //new_delo
			                $rec['owner']=$vId; $rec['owner_login']=$vLogin;  $rec['owner_balans_do']=$vBalans; $rec['owner_balans_posle']=$vBalans2;
					$rec['target']=0; $rec['target_login']='';
					$rec['type']=15;//получил кр в бою
					$rec['sum_kr']=$fmoney;	$rec['sum_ekr']=0; $rec['sum_kom']=0; $rec['item_id']='';
					$rec['item_name']=''; $rec['item_count']=0; $rec['item_type']=0; $rec['item_cost']=0;
					$rec['item_dur']=0; $rec['item_maxdur']=0;$rec['item_ups']=0; $rec['item_unic']=0;
					$rec['item_incmagic']=''; $rec['item_incmagic_count']=''; $rec['item_arsenal']='';
					$rec['battle']=$bat['id'];
					add_to_new_delo($rec); //юзеру

        		}
	         }
             else
             {
             	$fmoney = 0;
             }

	if ($fmoney>0)
		{
		$FMONEY_SQL=" money=money+{$fmoney} , ";
		$MY_MONEY=""; //затираем если было
		}
		else
		{
		$FMONEY_SQL="";
		}

            if ($btype==30)   // боты в лабе выиграли
			{
	              $labqv=mysql_query("select `map`,`x`,`y` FROM `labirint_items` where val='{$bat['id']}';");
	               while ($rolab = mysql_fetch_array($labqv))
		               {
		              $labMAP=$rolab['map'];
		              $labX=$rolab['x'];
		              $labY=$rolab['y'];

	              mysql_query("DELETE FROM `labirint_items`  WHERE `map`={$labMAP} and (`item`='R' OR `item`='I' OR `item`='J' OR `item`='х' ) and `x`={$labX} and `y`={$labY} ;");
	              mysql_query("UPDATE `labirint_users` SET `x`=0 , `y`=0, `dead`=`dead`+1  WHERE `map`={$labMAP} and `x`={$labX} and `y`={$labY} ;");
			        }
			}
			else // бой не влабе значит можно выполн€ть добавл€ть опыт
			{

			if ($bat['type']==22)
				{
				//тренеровочные бои
				    if (mysql_query_100("update users set  battle=0,  hiller=0, khiller=0 ,  `fullhptime` = '".time()."'  where battle='{$bat['id']}' and battle_t=2; ") )
				         {
			                // all is good
			               //echo mysql_error();
			                }
			                else
			                {
			                telepost('Bred','<font color=red>¬нимание! fsystem.php win team 2</font> ќшибка начислени€ опыта: тренеровочный бой'.$bat['id']);
			                }

				}
				else
				{
			                if ( mysql_query_100("update users set `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*{$EXP_WIN}), {$WIN_REP}  win=win+1 ,  {$wc} battle=0 ,  {$add_voinst_win}  hiller=0, {$WIN_ST_COUNT} khiller=0 , {$FMONEY_SQL}  {$MY_MONEY} `fullhptime` = '".time()."'  where battle={$bat['id']} and battle_t=2;") )
			                {
			                // all is good
			                }
			                else
			                {
			                telepost('Bred','<font color=red>¬нимание! fsystem.php str win team 2</font> ќшибка начислени€ опыта: бой'.$bat['id']);
			                }
			        }
           		}


	       			//загружаем данные по 704  эффектам этого бо€
	       			$get_baff_bat=mysql_query("select owner from effects where battle='{$bat['id']}' and type=704");
	       			 while ($baf_owners = mysql_fetch_array($get_baff_bat))
			   	{
			   	$hava_baf[$baf_owners['owner']]=$baf_owners;
			   	}


		// ламаем шмотки проигравшим командам команте
			$get_owners=mysql_query("select id from users where battle='{$bat['id']}' and battle_t!=2");
			   if (($bat['status_flag'] > 0) OR ($bat['type']==171) )
			   {
			   //4. ¬о всех статусных бо€х поломка 50%
			   $BROK=75;
//				$BROK=mt_rand(65,70);
			   }
			   else
			   {
//				$BROK=mt_rand(75,80);
			   $BROK=80;
//			   $min_lim_brok=3;
//			   $max_lim_brok=4;
			   }
		   while ($r_owners = mysql_fetch_array($get_owners))
		   {
		   		if ($hava_baf[$r_owners['id']])
			   	{
			   	//если на овнере был баф 704 то ломаем все шмотки от 2-х до 4
				 mysql_query("update oldbk.inventory set `duration`= `duration`+floor(RAND()*2+1) where owner={$r_owners['id']} and type!=30 and  battle={$bat['id']} ; ");
			   	}
			   	else
			   	{
		   		   mysql_query("update oldbk.inventory set `duration`= IF (100*RAND()<".$BROK.",`duration`+1,`duration`) where owner={$r_owners['id']} and type!=30 and  battle={$bat['id']} ");
		   		}
		   }


		 $wc="";
		//if (!((time()>$KO_start_time5) and (time()<$KO_fin_time5)) )
			{
			//јкци€ - ¬есна не включена!!!!!!!!!
			if ( ($bat['CHAOS']>0) AND ($bat['type']!=5))
			 {
	                  //сбрасываем в 0  только тем кто не нападал
		          $wc=" wcount=if(((select napal from battle_vars where owner=users.id and battle={$bat['id']})=0),0,`wcount`) , ";
			  }
			  }

			if ($bat['type']==22)
			 {
			 //тренеровочный бой
			 	mysql_query_100("UPDATE users SET hp=0, battle=0 ,  hiller=0, khiller=0 , `fullmptime`='".time()."' ,  `fullhptime` = '".time()."'  where battle={$bat['id']} and battle_t!=2;");
			 }
			 else
			if ($blood>0)
			{
				if ($bat['blood']==2)
	             		{
	             		//функци€ установки травмы дл€ вендетто
	             		$travma_func='settravmatv2_new';
	             		}
				else if ($bat['status_flag'] > 0)
	             		{
	             		// кровь дл€ стутусных 5. “равмы в статусных кровавых бо€х: до 40% легка€, 41- 70% средн€€, 71-100% т€жела€
	             		$travma_func='settravma3st_new';
        	     		}
             			else
	             		{
	             		//обычна€ кровь
        	     		$travma_func='settravma3_new';
             			}


				mysql_query_100("UPDATE users SET hp=0, {$wc} lose=lose+1 , ".$exp_sql." battle=0 , {$add_voinst_lose} {$MY_MONEY}   hiller=0, {$LOSE_ST_COUNT} khiller=0 , `fullmptime`='".time()."' ,  `fullhptime` = '".time()."', sila = IF((@RR:=100*RAND())<30, ".$travma_func."(id,'sila',sila,level,{$bat['id']},{$bat['type']},align,trv,pasbaf), sila), lovk = IF(@RR>=30 AND @RR<60, ".$travma_func."(id,'lovk',lovk,level,{$bat['id']},{$bat['type']},align,trv,pasbaf), lovk), inta = IF(@RR>=60, ".$travma_func."(id,'inta',inta,level,{$bat['id']},{$bat['type']},align,trv,pasbaf), inta) where battle={$bat['id']} and battle_t!=2;");

		 		$err_text=mysql_error();
		 		if ($err_text!='') {	addchp ('<font color=red>fsystem.php</font> lose team 2a  '.$err_text,'{[]}Bred{[]}'); }

			}
			else
			{
				mysql_query_100("update users set hp=0, {$wc} lose=lose+1 , ".$exp_sql." battle=0 , {$add_voinst_lose} {$MY_MONEY}   hiller=0, {$LOSE_ST_COUNT} khiller=0 , `fullmptime`='".time()."' ,  `fullhptime` = '".time()."' where battle={$bat['id']} and battle_t!=2;");
		 		$err_text=mysql_error();
		 		if ($err_text!='') {	addchp ('<font color=red>fsystem.php</font> lose team 2b '.$err_text,'{[]}Bred{[]}'); }
			}

		     mysql_query("delete from battle_user_time  where  battle={$bat['id']};");
		     mysql_query("delete from battle_fd  where  battle={$bat['id']};");
		     mysql_query("delete from users_clons where battle={$bat['id']} and id_user != 84 and owner=0");
		     mysql_query("delete from users_hill where battle={$bat['id']};");



                if (mysql_query_100("update users_clons set `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where owner=users_clons.id and battle={$bat['id']})),(@EE:=0))),   win=win+1 , {$WIN_ST_COUNT} battle=0, hp=maxhp  ,  `fullentime` = '".time()."'   where battle='{$bat['id']}' and battle_t=2 and owner>0; ") ) //(@EE:=0))*{$EXP_WIN}
                	{
                	//ok
	                	if ($bat[type]==8)
	                		{
					//telepost('Bred','<font color=red>¬нимание! fsystem.php</font> начислени€ опыта наЄмам тима 2 победа: бой (цветочнй)'.$bat['id']."update users_clons set `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where owner=users_clons.id and battle={$bat['id']})),(@EE:=0))),   win=win+1 , {$WIN_ST_COUNT} battle=0, hp=maxhp  where battle='{$bat['id']}' and battle_t=2 and owner>0; ");
	                		}
                	}
                	else
                	{
                	//telepost('Bred','<font color=red>¬нимание! fsystem.php</font> ќшибка начислени€ опыта наЄмам тима 2 победа: бой'.$bat['id']);
                	}

		//`fullmptime`='".time()."' ,  `fullhptime` = '".time()."'


		$exp_sql_naem=str_replace('users.','users_clons.',$exp_sql); // подмен€ем название

		if (mysql_query_100("update users_clons set hp=maxhp,  lose=lose+1 , {$LOSE_ST_COUNT} battle=0  ,  `fullentime` = '".time()."'   where battle={$bat['id']} and battle_t!=2  and owner>0;")) //".$exp_sql_naem."
			{
			//ok
			}
			else
			{
			//telepost('Bred','<font color=red>¬нимание! fsystem.php</font> ќшибка начислени€ опыта наЄмам тима 1 поражение: бой'.$bat['id']);
			}


			$qarr = array();
			$qtrv = mysql_query('SELECT * FROM effects WHERE type = 556 and battle = '.$bat['id']);
			while($qtr = mysql_fetch_assoc($qtrv)) {
				$qarr[] = $qtr['owner'];
			}
			if (count($qarr)) {
				mysql_query('UPDATE oldbk.users SET trv = 0 WHERE id IN ('.implode(",",$qarr).')');
			}

		        ///”даление остатков боевых эффектов
			mysql_query("delete  from effects where battle='{$bat['id']}' and ((`type`>=700 and `type` <=800) or `type`=805 or `type`=556 or `type`=302 or `type`=430)   ");


			//”даление остатков ничейных данных
			mysql_query("delete  from battle_vars  where battle='{$bat['id']}' and owner=0 ;");

                     RETURN "OK2";
          }
	elseif  ($winner_t==3)
          {

		 $wc="";
		//if (!((time()>$KO_start_time5) and (time()<$KO_fin_time5)) )
			{
			//јкци€ - ¬есна не включена!!!!!!!!!
			 if ( ($bat['CHAOS']>0) AND ($bat['type']!=5))
			 {
			 // +1 только тем кто не нападал
			 $wc=" wcount=if(((select napal from battle_vars where owner=users.id and battle={$bat['id']})=0),(`wcount`+1),`wcount`) , ";
			 }
			 }



         	 if ($bfond > 0)
             {
                    $kol=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat['id']} and battle_t=3;"));
		     		$fmoney = round(($bfond*0.9)/$kol[0],2);

		    	$qv=mysql_query("select `id`,`money`,`login` FROM `users` where battle={$bat['id']} and battle_t=3;");
     	  	     while ($roqv = mysql_fetch_array($qv))
             	{
             		$vId=$roqv['id'];
	                $vBalans=$roqv['money'];
	                $vLogin=$roqv['login'];
                    $vBalans2=$vBalans+$fmoney;
			 //new_delo
			                $rec['owner']=$vId; $rec['owner_login']=$vLogin;  $rec['owner_balans_do']=$vBalans; $rec['owner_balans_posle']=$vBalans2;
					$rec['target']=0; $rec['target_login']='';
					$rec['type']=15;//получил кр в бою
					$rec['sum_kr']=$fmoney;	$rec['sum_ekr']=0; $rec['sum_kom']=0; $rec['item_id']='';
					$rec['item_name']=''; $rec['item_count']=0; $rec['item_type']=0; $rec['item_cost']=0;
					$rec['item_dur']=0; $rec['item_maxdur']=0;$rec['item_ups']=0; $rec['item_unic']=0;
					$rec['item_incmagic']=''; $rec['item_incmagic_count']=''; $rec['item_arsenal']='';
					$rec['battle']=$bat['id'];
					add_to_new_delo($rec); //юзеру

        		}
	         }
             else
             {
             	$fmoney = 0;
             }

	if ($fmoney>0)
		{
		$FMONEY_SQL=" money=money+{$fmoney} , ";
		$MY_MONEY=""; //затираем если было
		}
		else
		{
		$FMONEY_SQL="";
		}

            if ($btype==30)   // боты в лабе выиграли
			{
	              $labqv=mysql_query("select `map`,`x`,`y` FROM `labirint_items` where val='{$bat['id']}';");
	               while ($rolab = mysql_fetch_array($labqv))
		               {
		              $labMAP=$rolab['map'];
		              $labX=$rolab['x'];
		              $labY=$rolab['y'];

	              mysql_query("DELETE FROM `labirint_items`  WHERE `map`={$labMAP} and (`item`='R' OR `item`='I' OR `item`='J' OR `item`='х' ) and `x`={$labX} and `y`={$labY} ;");
	              mysql_query("UPDATE `labirint_users` SET `x`=0 , `y`=0, `dead`=`dead`+1  WHERE `map`={$labMAP} and `x`={$labX} and `y`={$labY} ;");
			        }
			}
			else // бой не влабе значит можно выполн€ть добавл€ть опыт
			{
	                if ( mysql_query_100("update users set `exp`=`exp`+(ifnull((@EE:=(select `exp` from battle_dam_exp where users.id=owner and battle={$bat['id']})),(@EE:=0))*{$EXP_WIN}), {$WIN_REP}  win=win+1 ,  {$wc} battle=0 ,  {$add_voinst_win}  hiller=0, {$WIN_ST_COUNT} khiller=0 , {$FMONEY_SQL} {$MY_MONEY} `fullhptime` = '".time()."'  where battle={$bat['id']} and battle_t=3;") )
	                {
	                // all is good
	                }
	                else
	                {
	                telepost('Bred','<font color=red>¬нимание! fsystem.php str 5356</font> ќшибка начислени€ опыта: бой'.$bat['id']);
	                }
           		}


	       			//загружаем данные по 704  эффектам этого бо€
	       			$get_baff_bat=mysql_query("select owner from effects where battle='{$bat['id']}' and type=704");
	       			 while ($baf_owners = mysql_fetch_array($get_baff_bat))
			   	{
			   	$hava_baf[$baf_owners['owner']]=$baf_owners;
			   	}


		// ламаем шмотки проигравшим командам команте
			$get_owners=mysql_query("select id from users where battle='{$bat['id']}' and battle_t!=3");
			   if (($bat['status_flag'] > 0) OR ($bat['type']==171) )
			   {
			   //4. ¬о всех статусных бо€х поломка 50%
   			   $BROK=75;
//				$BROK=mt_rand(65,70);
//			   $min_lim_brok=6;
//			   $max_lim_brok=7;
			   }
			   else
			   {
//			   $BROK=mt_rand(75,80);
			   $BROK=80;
//			   $min_lim_brok=3;
//			   $max_lim_brok=4;
			   }
		   while ($r_owners = mysql_fetch_array($get_owners))
		   {
		   		if ($hava_baf[$r_owners['id']])
			   	{
			   	//если на овнере был баф 704 то ломаем все шмотки от 1-х до 2
				 mysql_query("update oldbk.inventory set `duration`= `duration`+floor(RAND()*2+1) where owner={$r_owners['id']} and type!=30 and  battle={$bat['id']} ; ");
			   	}
			   	else
			   	{
		   		 mysql_query("update oldbk.inventory set `duration`= IF (100*RAND()<".$BROK.",`duration`+1,`duration`) where owner={$r_owners['id']} and type!=30 and  battle={$bat['id']} ");
		   		}
		   }

		 $wc="";
		//if (!((time()>$KO_start_time5) and (time()<$KO_fin_time5)) )
			{
			//јкци€ - ¬есна не включена!!!!!!!!!
			if ( ($bat['CHAOS']>0) AND ($bat['type']!=5))
			 {
	                  //сбрасываем в 0  только тем кто не нападал
		          $wc=" wcount=if(((select napal from battle_vars where owner=users.id and battle={$bat['id']})=0),0,`wcount`) , ";
			  }
			 }


			if ($blood>0)
			{
				if ($bat['blood']==2)
	             		{
	             		//функци€ установки травмы дл€ вендетто
	             		$travma_func='settravmatv2_new';
	             		}
				else if ($bat['status_flag'] > 0)
	             		{
	             		// кровь дл€ стутусных 5. “равмы в статусных кровавых бо€х: до 40% легка€, 41- 70% средн€€, 71-100% т€жела€
	             		$travma_func='settravma3st_new';
        	     		}
             			else
	             		{
	             		//обычна€ кровь
        	     		$travma_func='settravma3_new';
             			}

				mysql_query_100("UPDATE users SET hp=0, {$wc} lose=lose+1 , ".$exp_sql." battle=0 , {$add_voinst_lose} {$MY_MONEY}   hiller=0, {$LOSE_ST_COUNT} khiller=0 ,  `fullmptime`='".time()."' ,   `fullhptime` = '".time()."', sila = IF((@RR:=100*RAND())<30, ".$travma_func."(id,'sila',sila,level,{$bat['id']},{$bat['type']},align,trv,pasbaf), sila), lovk = IF(@RR>=30 AND @RR<60, ".$travma_func."(id,'lovk',lovk,level,{$bat['id']},{$bat['type']},align,trv,pasbaf), lovk), inta = IF(@RR>=60, ".$travma_func."(id,'inta',inta,level,{$bat['id']},{$bat['type']},align,trv,pasbaf), inta) where battle={$bat['id']} and battle_t!=3;");
		 		$err_text=mysql_error();
		 		if ($err_text!='') {	addchp ('<font color=red>fsystem.php</font> 5420 '.$err_text,'{[]}Bred{[]}'); }

			}
			else
			{
				mysql_query_100("update users set hp=0, {$wc} lose=lose+1 , ".$exp_sql." battle=0 , {$add_voinst_lose} {$MY_MONEY}  hiller=0, {$LOSE_ST_COUNT} khiller=0  ,  `fullmptime`='".time()."' ,  `fullhptime` = '".time()."' where battle={$bat['id']} and battle_t!=3;");
		 		$err_text=mysql_error();
		 		if ($err_text!='') {	addchp ('<font color=red>fsystem.php</font> 5427 '.$err_text,'{[]}Bred{[]}'); }
			}

		     mysql_query("delete from battle_user_time  where  battle={$bat['id']};");
		     mysql_query("delete from battle_fd  where  battle={$bat['id']};");
		     mysql_query("delete from users_clons where battle={$bat['id']} and id_user != 84 and owner=0");
		     mysql_query("delete from users_hill where battle={$bat['id']};");





			$qarr = array();
			$qtrv = mysql_query('SELECT * FROM effects WHERE type = 556 and battle = '.$bat['id']);
			while($qtr = mysql_fetch_assoc($qtrv)) {
				$qarr[] = $qtr['owner'];
			}
			if (count($qarr)) {
				mysql_query('UPDATE oldbk.users SET trv = 0 WHERE id IN ('.implode(",",$qarr).')');
			}

		        ///”даление остатков боевых эффектов
			mysql_query("delete  from effects where battle='{$bat['id']}' and ((`type`>=700 and `type` <=800) or `type`=805 or `type`=556 or `type`=302 or `type`=430)   ");


			//”даление остатков ничейных данных
			mysql_query("delete  from battle_vars  where battle='{$bat['id']}' and owner=0 ;");

                     RETURN "OK3";
          }
          else
	  if  ($winner_t==0)
		  {
		          if ($bfond > 0)
	                {
	                     $kol=mysql_fetch_array(mysql_query("select count(*) from users where battle={$bat['id']} ;"));
			             $fmoney = round(($bfond*0.9)/$kol[0],2);

			             $qv=mysql_query("select `id`,`money`,`login` FROM `users` where battle={$bat['id']} ;");
		              while ($roqv = mysql_fetch_array($qv))
             			{
	             		$vId=$roqv['id'];
		                $vBalans=$roqv['money'];
		                $vLogin=$roqv['login'];
	                        $vBalans2=$vBalans+$fmoney;
	                         //new_delo
			                $rec['owner']=$vId; $rec['owner_login']=$vLogin;  $rec['owner_balans_do']=$vBalans; $rec['owner_balans_posle']=$vBalans2;
					$rec['target']=0; $rec['target_login']='';
					$rec['type']=16;//получил кр в бою
					$rec['sum_kr']=$fmoney;	$rec['sum_ekr']=0; $rec['sum_kom']=0; $rec['item_id']='';
					$rec['item_name']=''; $rec['item_count']=0; $rec['item_type']=0; $rec['item_cost']=0;
					$rec['item_dur']=0; $rec['item_maxdur']=0;$rec['item_ups']=0; $rec['item_unic']=0;
					$rec['item_incmagic']=''; $rec['item_incmagic_count']=''; $rec['item_arsenal']='';
					$rec['battle']=$bat['id'];
					add_to_new_delo($rec); //юзеру

			              //old_delo
			              if (olddelo==1)
			              {
		                        $ttext=$vLogin.' получил '.$fmoney.' кр за победу в поединке є'.$bat['id'].' (Ѕаланс до:'.$vBalans.'/ после:'.$vBalans2;
		              		mysql_query("INSERT INTO oldbk.`delo` (`pers`, `text`, `type`, `date`, `battle`) VALUES ('{$vId}','{$ttext}',1, '".time()."','{$bat['id']}');");
		              	      }
        			   }

		            }
	            else
	            {
	                $fmoney = 0;
			    }


	if ($fmoney>0)
		{
		$FMONEY_SQL=" money=money+{$fmoney} , ";
		$MY_MONEY=""; //затираем если было
		}
		else
		{
		$FMONEY_SQL="";
		}


               #battle in lab users not win
              if ($btype==30)
			  {
			   $labqv=mysql_query("select `map`,`x`,`y` FROM `labirint_items` where val='{$bat['id']}';");
	             while ($rolab = mysql_fetch_array($labqv))
		               {
		              $labMAP=$rolab['map'];
		              $labX=$rolab['x'];
		              $labY=$rolab['y'];
	                  mysql_query("DELETE FROM `labirint_items`  WHERE `map`={$labMAP} and (`item`='R' OR `item`='I' OR `item`='J' OR `item`='х' ) and `x`={$labX} and `y`={$labY} ;");
        		      mysql_query("UPDATE `labirint_users` SET `x`=0 , `y`=0, `dead`=`dead`+1  WHERE `map`={$labMAP} and `x`={$labX} and `y`={$labY} ;");
				}
			  }


                     mysql_query_100("update users set  battle=0 , {$add_voinst_lose}  hiller=0, khiller=0 ,  {$FMONEY_SQL} {$MY_MONEY} `fullhptime` = '".time()."' where battle={$bat['id']} ;");

		     mysql_query("delete from battle_user_time  where  battle={$bat['id']};");
                     mysql_query("delete from battle_fd  where  battle={$bat['id']};");
                     mysql_query("delete from users_clons where battle={$bat['id']} and id_user != 84 and owner=0 ");
                     mysql_query("delete from users_hill where battle={$bat['id']};");



		//ничь€
		if (mysql_query_100("update users_clons set hp=0, battle=0,  `fullentime` = '".time()."'  where battle={$bat['id']} and owner>0;"))
			{
			//ok
			}
			else
			{
			//telepost('Bred','<font color=red>¬нимание! fsystem.php</font> ќшибка запроса наЄмам ничь€: бой'.$bat['id']);
			}





			$qarr = array();
			$qtrv = mysql_query('SELECT * FROM effects WHERE type = 556 and battle = '.$bat['id']);
			while($qtr = mysql_fetch_assoc($qtrv)) {
				$qarr[] = $qtr['owner'];
			}
			if (count($qarr)) {
				mysql_query('UPDATE oldbk.users SET trv = 0 WHERE id IN ('.implode(",",$qarr).')');
			}

                     ///”даление остатков боевых эффектов
			mysql_query("delete  from effects where battle='{$bat['id']}' and ((`type`>=700 and `type` <=800) or `type`=805 or `type`=556 or `type`=302 or `type`=430)   ");


       			//”даление остатков ничейных данных
			mysql_query("delete  from battle_vars  where battle='{$bat['id']}' and owner=0 ;");

              RETURN "OK0";
            }
            else
            {
            RETURN "ERR1";
            }

 }
		//	else
		//	{
		//	RETURN "ERR2";
		//	}
}
}



function do_item_bonus($bat,$t,$top) // вызываетс€ до проставки battle=0!!
{
if ($bat['id']==0) return false;


$get_all_levels=str_replace('—¬≈“ VS T№ћј [','',$bat['coment']);
$get_all_levels=explode("-",$get_all_levels);

$start_lvl=(int)($get_all_levels[0]);
$fin_lvl=(int)($get_all_levels[1]);

if ($start_lvl==0) {$start_lvl=7;}
if ($fin_lvl==0) {$fin_lvl=14;}


			for($lvl=$start_lvl;$lvl<=$fin_lvl;$lvl++)	// если в массиве два элемента знач два раза ударили из двух пушек
				{
				//1. выбираем  топ и гроков по опыту в уровне
				$get_top_users=mysql_query("select u.id, ex.`exp` from users u LEFT JOIN  battle_dam_exp ex ON u.id=ex.owner and u.battle=ex.battle where u.battle='{$bat['id']}' and u.level='{$lvl}' and u.battle_t='{$t}' order by ex.`exp` DESC LIMIT {$top} ");
				if (mysql_num_rows($get_top_users) > 0)
				{
					$owners=array();
					while ($ru = mysql_fetch_array($get_top_users))
						{
						$owners[]=$ru['id'];
						}

				$flag=1;
				$fin_time=time()+259200; //3-e суток

				//2. выгребаем ид шмоток дл€ бонусов
				$inserts_line=array();

				$get_top=mysql_query("select id, owner from oldbk.inventory where owner in (".implode(",",$owners).") and battle='{$bat['id']}'  and type!=30");
				while ($itt = mysql_fetch_array($get_top))
				{
				$inserts_line[$itt['owner']][]=$itt['id'];
				}

				ksort($inserts_line);
				//3. делаем инсерты
						foreach ($inserts_line as $owner=>$itemid)
						{
						$buf='';
							foreach ($itemid as $n=>$id)
								{
								$buf.=" ('{$id}','{$flag}','{$owner}','{$fin_time}' , '{$bat['id']}'  ) ,";
								}

							if ($buf!='')
									 {
									  $buf=substr($buf, 0, -1); // удал€ем последнюю зап€тую
									mysql_query("INSERT INTO bonus_items (`item_id`,`flag`,`owner`,`finish`, `battle`) VALUES ".$buf." ON DUPLICATE KEY UPDATE `finish`='{$fin_time}' , `battle`='{$bat['id']}' ; ");
									log_sql_tmp("INSERT INTO bonus_items (`item_id`,`flag`,`owner`,`finish`, `battle`) VALUES ".$buf." ON DUPLICATE KEY UPDATE `finish`='{$fin_time}' , `battle`='{$bat['id']}' ; \n");
									 }
						}
				}
				}


return true;
}

function do_chaos_items($win_t,$bat,$bottype=103)// приходит сюда номер победившей команды
{
$ALL_LIM=10;
$ALL_LIM_baff=(int)($ALL_LIM/2);

$array_lvls_bot=array(
					"101" => 14,
					"103" => 6,
					"104" => 7,
					"105" => 8,
					"106" => 9,
					"107" => 10,
					"108" => 11,
					"109" => 12,
					"110" => 13);
$need_ups=$array_lvls_bot[$bottype];


//берем тех у кого есть баф но не больше 50%
$get_candidat1=mysql_query("select DISTINCT id, login, room , id_city  from users where battle='{$bat['id']}' and battle_t='{$win_t}'  and id in (select owner from effects where type=844) ORDER BY RAND() LIMIT {$ALL_LIM_baff} ;");
$num_rows = mysql_num_rows($get_candidat1);

	if ($num_rows>0)
		{
			while ($cand = mysql_fetch_array($get_candidat1))
			{
			get_chaos_item($cand,$need_ups);
			$im_get_item[]=$cand['id'];//запоминаем кому уже раздали
			}
		}

	if ($num_rows<$ALL_LIM)
		{
		$ALL_LIM-=$num_rows;

		$get_candidat2=mysql_query("select DISTINCT id, login, room , id_city  from users where battle='{$bat['id']}' and battle_t='{$win_t}' ".(is_array($im_get_item)?"  and id not in (".implode(", ",$im_get_item).")":"")." ORDER BY RAND() LIMIT {$ALL_LIM};");
		while ($cand = mysql_fetch_array($get_candidat2))
			{
			get_chaos_item($cand,$need_ups);
			}
		}

}

function get_chaos_item($cand,$need_ups=6)
{

	$upgrade[7] = array("hp" => 6,	"bron" => 1,	"stat" => 1,	"mf" => 5,	"udar" => 5,	"nparam" => 1,	"duration" => 5);
	$upgrade[8] = array("hp" => 8,	"bron" => 1,	"stat" => 1,	"mf" => 7,	"udar" => 5,	"nparam" => 1,	"duration" => 5);
	$upgrade[9] = array("hp" => 10,	"bron" => 1,	"stat" => 1,	"mf" => 10,	"udar" => 5,	"nparam" => 1,	"duration" => 10);
	$upgrade[10] = array("hp" => 12,"bron" => 1,	"stat" => 1,	"mf" => 12,	"udar" => 6,	"nparam" => 1,	"duration" => 10);
	$upgrade[11] = array("hp" => 15,"bron" => 1,	"stat" => 1,	"mf" => 15,	"udar" => 7,	"nparam" => 1,	"duration" => 15);
	$upgrade[12] = array("hp" => 20,"bron" => 1,	"stat" => 1,	"mf" => 17,	"udar" => 8,	"nparam" => 1,	"duration" => 15);
	$upgrade[13] = array("hp" => 25,"bron" => 2,	"stat" => 2,	"mf" => 20,	"udar" => 9,	"nparam" => 1,	"duration" => 15);
	$upgrade[14] = array("hp" => 25,"bron" => 2,	"stat" => 2,	"mf" => 20,	"udar" => 9,	"nparam" => 1,	"duration" => 15);

	$upgrr=array();

	for($yy=6;$yy<=$need_ups;$yy++)
	{
	$upgrr['hp']+=$upgrade[$yy]['hp'];
	$upgrr['bron']+=$upgrade[$yy]['bron'];
	$upgrr['stat']+=$upgrade[$yy]['stat'];
	$upgrr['mf']+=$upgrade[$yy]['mf'];
	$upgrr['udar']+=$upgrade[$yy]['udar'];
	$upgrr['nparam']+=$upgrade[$yy]['nparam'];


	$deft=5;

	if (($need_ups>=10) and ($need_ups<=14)) $deft=$need_ups-4;


	}




	$hitem[1]='      ("ћеч ’аоса+'.$deft.'",  "5",  "630","'.$cand['id'].'",  "'.$need_ups.'" , '.$need_ups.' , "'.(25+$upgrr['nparam']).'", "0", "'.(25+$upgrr['nparam']).'", "'.(25+$upgrr['nparam']).'",  "0","0","'.($deft+$upgrr['nparam']).'",  "'.(29+$upgrr['udar']).'","'.(38+$upgrr['udar']).'",    "120", "50",  "0",  "0",   "0",  "2", "0"  ,"chaos_sword.gif",     34,     "1", "5",  "0", "1006233","13","116"         ,2             , 2            ,"ѕризвать ќгненную Ёлементаль",50,15,'.(time()+60*60*3).',1,5,0,2)';
	$hitem_name[1]='"ћеч ’аоса+'.$deft.'"';

	$hitem[2]='      ("“опор ’аоса+'.$deft.'","5",  "675","'.$cand['id'].'",   "'.$need_ups.'", '.$need_ups.' , "'.(25+$upgrr['nparam']).'", "0", "'.(25+$upgrr['nparam']).'", "'.(25+$upgrr['nparam']).'",  "0","'.($deft+$upgrr['nparam']).'","0",  "'.(29+$upgrr['udar']).'","'.(39+$upgrr['udar']).'",    "130",  "50",  "0",  "0",   "2",   "0", "0" ,"chaos_axe.gif",      34,     "1", "5",  "0", "1006232","11","116"         ,2             , 2            ,"ѕризвать ќгненную Ёлементаль",50,15,'.(time()+60*60*3).',1,5,0,2)';
	$hitem_name[2]='"“опор ’аоса+'.$deft.'"';

	$hitem[3]='      ("ћеч ’аоса+'.$deft.'",  "5",  "630","'.$cand['id'].'",  "'.$need_ups.'" , '.$need_ups.' , "'.(25+$upgrr['nparam']).'", "'.(25+$upgrr['nparam']).'", "0", "'.(25+$upgrr['nparam']).'",  "0","0","'.($deft+$upgrr['nparam']).'",  "'.(29+$upgrr['udar']).'","'.(38+$upgrr['udar']).'",    "0",  "0",    "110","50",   "0",  "2" ,"0"   ,"chaos_sword.gif",     34,     "1", "5",  "0", "1006233","13","116"         ,2             , 2            ,"ѕризвать ќгненную Ёлементаль",50,15,'.(time()+60*60*3).',1,5,0,2)';
	$hitem_name[3]='"ћеч ’аоса+'.$deft.'"';

	$hitem[4]='      ("“опор ’аоса+'.$deft.'","5",  "675","'.$cand['id'].'",   "'.$need_ups.'", '.$need_ups.', "'.(25+$upgrr['nparam']).'", "'.(20+$upgrr['nparam']).'", "0", "'.(25+$upgrr['nparam']).'",  "0","'.($deft+$upgrr['nparam']).'","0",  "'.(29+$upgrr['udar']).'","'.(39+$upgrr['udar']).'",     "0", "0",    "120","50",   "2",   "0", "0"  ,"chaos_axe.gif",      34,     "1", "5",  "0", "1006232","11","116"         ,2             , 2            ,"ѕризвать ќгненную Ёлементаль",50,15,'.(time()+60*60*3).',1,5,0,2)';
	$hitem_name[4]='"“опор ’аоса+'.$deft.'"';

	$hitem[5]='      ("ћолот ’аоса+'.$deft.'","5",  "680","'.$cand['id'].'",   "'.$need_ups.'",  '.$need_ups.',"'.(25+$upgrr['nparam']).'", "'.(15+$upgrr['nparam']).'", "'.(15+$upgrr['nparam']).'", "'.(25+$upgrr['nparam']).'",  "'.($deft+$upgrr['nparam']).'","0","0", "'.(32+$upgrr['udar']).'","'.(43+$upgrr['udar']).'",     "0", "120",    "0","50",   "0",   "0", "2"  ,"chaos_hamm.gif",      34,     "1", "5",  "0", "1006234","12","116"         ,2             , 2            ,"ѕризвать ќгненную Ёлементаль",50,15,'.(time()+60*60*3).',1,3,1,2)';
	$hitem_name[5]='"ћолот ’аоса+'.$deft.'"';

	$hitem[6]='      ("ћолот ’аоса+'.$deft.'","5",  "680","'.$cand['id'].'",   "'.$need_ups.'", '.$need_ups.' ,"'.(25+$upgrr['nparam']).'", "'.(15+$upgrr['nparam']).'", "'.(15+$upgrr['nparam']).'", "'.(25+$upgrr['nparam']).'",  "'.($deft+$upgrr['nparam']).'","0","0",  "'.(32+$upgrr['udar']).'","'.(43+$upgrr['udar']).'",    "120", "50",  "0",  "0",   "0",   "0" , "2"  ,"chaos_hamm.gif",      34,     "1", "5",  "0", "1006234","12","116"         ,2             , 2            ,"ѕризвать ќгненную Ёлементаль",50,15,'.(time()+60*60*3).',1,3,1,2)';
	$hitem_name[6]='"ћолот ’аоса+'.$deft.'"';

	$iit=mt_rand(1,6);
	$it=$hitem[$iit];
	$it_name=$hitem_name[$iit];

	mysql_query('insert into oldbk.inventory
         (name,maxdur,cost,  owner, nlevel, `up_level`  ,nsila,nlovk,ninta,nvinos,   ndubina,ntopor,nmech,  minu,maxu,   mfkrit,mfauvorot,mfuvorot,mfakrit,    gtopor,gmech, gdubina,     img,          `type`,  sharped,massa,isrep,prototype,otdel,includemagic,includemagicdex,includemagicmax,includemagicname,includemagicuses,includemagiccost,dategoden,goden,ab_mf,ab_uron,unik)
    	 VALUES  '.$it.' ;');

   	  addchp ('<font color=red>¬нимание!</font> ¬ы получили  <i>'.$it_name.'</i>  ','{[]}'.$cand['login'].'{[]}',$cand['room'],$cand['id_city']);

}


function do_chaos_items_event($win_t,$bat)// приходит сюда номер победившей команды
{

$get_candidat=mysql_query("select * , (select `exp` from battle_dam_exp where owner=users.id and battle=users.battle) as ex from users where battle='{$bat['id']}' and battle_t='{$win_t}' order by ex DESC LIMIT 30;");


	while ($cand = mysql_fetch_array($get_candidat))
	{
	$hitem[1]='      ("ћеч ’аоса+5",  "1",  "630","'.$cand['id'].'",  "7" ,  "25", "0", "25", "25",  "0","0","5",  "24","37",    "120", "50",  "0",  "0",   "0",  "2", "0"  ,"chaos_sword.gif",     3,     "1", "5",  "0", "1006233","13","116"         ,2             , 2            ,"ѕризвать ќгненную Ёлементаль",50,15,'.(time()+60*60*3).',1 ) ';
	$hitem_name[1]='"ћеч ’аоса+5"';

	$hitem[2]='      ("“опор ’аоса+5","1",  "675","'.$cand['id'].'",   "7",  "25", "0", "25", "25",  "0","5","0",  "29","38",    "130",  "50",  "0",  "0",   "2",   "0", "0" ,"chaos_axe.gif",      3,     "1", "5",  "0", "1006232","11","116"         ,2             , 2            ,"ѕризвать ќгненную Ёлементаль",50,15,'.(time()+60*60*3).',1 ) ';
	$hitem_name[2]='"“опор ’аоса+5"';

	$hitem[3]='      ("ћеч ’аоса+5",  "1",  "630","'.$cand['id'].'",  "7" ,  "25", "25", "0", "25",  "0","0","5",  "24","37",    "0",  "0",    "110","50",   "0",  "2" ,"0"   ,"chaos_sword.gif",     3,     "1", "5",  "0", "1006233","13","116"         ,2             , 2            ,"ѕризвать ќгненную Ёлементаль",50,15,'.(time()+60*60*3).',1 ) ';
	$hitem_name[3]='"ћеч ’аоса+5"';

	$hitem[4]='      ("“опор ’аоса+5","1",  "675","'.$cand['id'].'",   "7",  "25", "20", "0", "25",  "0","5","0",  "29","38",     "0", "0",    "120","50",   "2",   "0", "0"  ,"chaos_axe.gif",      3,     "1", "5",  "0", "1006232","11","116"         ,2             , 2            ,"ѕризвать ќгненную Ёлементаль",50,15,'.(time()+60*60*3).',1 ) ';
	$hitem_name[4]='"“опор ’аоса+5"';

	$hitem[5]='      ("ћолот ’аоса+5","1",  "680","'.$cand['id'].'",   "7",  "25", "15", "15", "25",  "5","0","0", "32","43",     "0", "120",    "0","50",   "0",   "0", "2"  ,"chaos_hamm.gif",      3,     "1", "5",  "0", "1006234","12","116"         ,2             , 2            ,"ѕризвать ќгненную Ёлементаль",50,15,'.(time()+60*60*3).',1 ) ';
	$hitem_name[5]='"ћолот ’аоса+5"';

	$hitem[6]='      ("ћолот ’аоса+5","1",  "680","'.$cand['id'].'",   "7",  "25", "15", "15", "25",  "5","0","0",  "32","43",    "120", "50",  "0",  "0",   "0",   "0" , "2"  ,"chaos_hamm.gif",      3,     "1", "5",  "0", "1006234","12","116"         ,2             , 2            ,"ѕризвать ќгненную Ёлементаль",50,15,'.(time()+60*60*3).',1 ) ';
	$hitem_name[6]='"ћолот ’аоса+5"';

	$iit=mt_rand(1,6);
	$it=$hitem[$iit];
	$it_name=$hitem_name[$iit];

	mysql_query('insert into oldbk.inventory
         (name,          maxdur,cost,  owner, nlevel,nsila,nlovk,ninta,nvinos,   ndubina,ntopor,nmech,  minu,maxu,   mfkrit,mfauvorot,mfuvorot,mfakrit,    gtopor,gmech, gdubina,     img,          `type`,  sharped,massa,isrep,prototype,otdel,includemagic,includemagicdex,includemagicmax,includemagicname,includemagicuses,includemagiccost,dategoden,goden)
    	 VALUES  '.$it.' ;');

   	  addchp ('<font color=red>¬нимание!</font> ¬ы получили  <i>'.$it_name.'</i>  ','{[]}'.$cand['login'].'{[]}',$cand['room'],$cand['id_city']);

	}

}

function exit_dress($telo,$goto)
{
$stringtype[270]='ќдиночных —ражений';
$type[210]='—ражение отр€дов';
mysql_query("INSERT INTO `effects` SET `type`=8".$goto.",`name`='—ледующее посещение ".$stringtype[$goto]."',`time`=".(time()+19800).",`owner`='{$telo['id']}';");
///////////////////////////////////////////////////////////////////////////////
		   if ($goto>0)
		     {

		     ///загружаем параметры prof=0 дл€ выхода
			     $telo_real=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users_profile` WHERE  prof=0 and  `owner` = '{$telo['id']}' LIMIT 1;"));
			     if ($telo_real['bpbonushp'] >0)
			     {
			     //если был боныс хп - провер€ем незакончилс€ ли он
			     $hp_bonus=mysql_fetch_array(mysql_query("select * from effects where owner='{$telo['id']}' and type>=1001 and type<=1003"));
			     if ($hp_bonus['id']>0)
			       {
			       //все ок эфект еще висит
			       }
			       else
			       {
			       //эфекта такого уже нет!
			       //снимаем его ручками, т.к. в кроене он не сн€лс€
			       $telo_real['maxhp']=$telo_real['maxhp']-$telo_real['bpbonushp'];
	       		       $telo_real['bpbonushp']=0;
				       if ($telo_real['hp']>$telo_real['maxhp'])
				       		{
				       		$telo_real['hp']=$telo_real['maxhp'];
				       		}
			       }
		     }
		     //идем дальше-стравмы - если есть
		     $eff = mysql_query("SELECT * FROM `effects` WHERE `owner` = '".$telo['id']."' AND (`type` >=11 AND `type` <= 14);");
		     while($effrow=mysql_fetch_array($eff))
				{
				$telo_real['sila']=$telo_real['sila']-$effrow['sila'];
				$telo_real['lovk']=$telo_real['lovk']-$effrow['lovk'];
				$telo_real['inta']=$telo_real['inta']-$effrow['inta'];
				$telo_real['vinos']=$telo_real['vinos']-$effrow['vinos'];
				}


		     //обновл€ем инвентарь
		     //1. удал€ем шаблонные вещи-fix bs_owner
		     mysql_query_100("delete from oldbk.inventory  where owner='{$telo['id']}' and bs_owner=3 ");
		     //2.устанавливаем родные шмотки
		     mysql_query_100("update oldbk.inventory  set dressed=1 where id in ({$telo_real['sergi']},{$telo_real['kulon']},{$telo_real['perchi']},{$telo_real['weap']},{$telo_real['bron']},{$telo_real['r1']},{$telo_real['r2']},{$telo_real['r3']},{$telo_real['helm']},{$telo_real['shit']},{$telo_real['boots']},{$telo_real['nakidka']},{$telo_real['rubashka']},{$telo_real['runa1']},{$telo_real['runa2']},{$telo_real['runa3']}  ) AND owner='{$telo['id']}' and dressed=0 ");
		     //3. обновл€ем чарчика
		     $sk_row=" `sila`='{$telo_real['sila']}',`lovk`='{$telo_real['lovk']}',`inta`='{$telo_real['inta']}',`vinos`='{$telo_real['vinos']}',`intel`='{$telo_real['intel']}',
		`mudra`='{$telo_real['mudra']}',`duh`='{$telo_real['duh']}',`bojes`='{$telo_real['bojes']}',`noj`='{$telo_real['noj']}',`mec`='{$telo_real['mec']}',`topor`='{$telo_real['topor']}',`dubina`='{$telo_real['dubina']}',
		`maxhp`='{$telo_real['maxhp']}',`hp`='{$telo_real['hp']}',`maxmana`='{$telo_real['maxmana']}',`mana`='{$telo_real['mana']}',`sergi`='{$telo_real['sergi']}',`kulon`='{$telo_real['kulon']}',`perchi`='{$telo_real['perchi']}',
		`weap`='{$telo_real['weap']}',`bron`='{$telo_real['bron']}',`r1`='{$telo_real['r1']}',`r2`='{$telo_real['r2']}',`r3`='{$telo_real['r3']}',`helm`='{$telo_real['helm']}',`shit`='{$telo_real['shit']}',`boots`='{$telo_real['boots']}',
		`stats`='{$telo_real['stats']}',`master`='{$telo_real['master']}',`nakidka`='{$telo_real['nakidka']}',`rubashka`='{$telo_real['rubashka']}', `runa1`='{$telo_real['runa1']}',`runa2`='{$telo_real['runa2']}',`runa3`='{$telo_real['runa3']}',   `mfire`='{$telo_real['mfire']}',`mwater`='{$telo_real['mwater']}',`mair`='{$telo_real['mair']}',`mearth`='{$telo_real['mearth']}',
		`mlight`='{$telo_real['mlight']}',`mgray`='{$telo_real['mgray']}',`mdark`='{$telo_real['mdark']}', `bpbonushp`='{$telo_real['bpbonushp']}'  ";
		      mysql_query_100("UPDATE `users` SET ".$sk_row." ,   `users`.`id_grup` = '0' ,  `users`.`room` = '{$goto}' WHERE  `users`.`id` = '{$telo['id']}' ;");
		     // header('location: restal'.$goto.'.php?refresh=3.14&onlvl='.$telo['level']);
		     }
		     else
		     {
		     echo "ќшибка направлени€...";
		     }
/////////////////////////////////////////////////////////////////////////////
}

function add_voinst($kom,$points,$battle_id,$war_id,$addvo=0)
{
///1. начислени€ без учета наемников - ЋёЅџ’, даже если есть наемник и война его клана, а он наемник другого - не учитываем
  if ($war_id >0)
   {
      //бой был клан война надо писать в войну вот както так пишу :)
      $who[1]='agressor';
      $who[2]='defender';
	mysql_query("update (select (select id from oldbk.clans where short=users.klan) as cl, ifnull((sum(e.`exp`)),0) as kol FROM users LEFT JOIN battle_dam_exp e on e.owner=users.id and e.battle=users.battle  WHERE users.battle='{$battle_id}' and users.battle_t='{$kom}' and users.klan!='' and users.naim=0  group by users.klan) A, oldbk.clans_war_2 C1 set C1.win".$kom." = C1.win".$kom." +".$addvo."+(FLOOR(A.kol* ".$points.")) WHERE A.cl = C1.".$who[$kom]." and C1.war_id={$war_id}  ;");
   //выгребаем всех кланеров в команде бо€ добавл€ем клану воинственость из суммы опыта кланеров - дл€ боев в клановых битвах
   mysql_query("update (select users.klan as cl, ifnull((sum(e.`exp`)),0) as kol FROM users LEFT JOIN battle_dam_exp e on e.owner=users.id and e.battle=users.battle  WHERE users.battle='{$battle_id}' and users.battle_t='{$kom}' and users.klan!='' and users.naim=0 group by users.klan) A, oldbk.clans C1 set C1.voinst = C1.voinst +".$addvo."+(FLOOR(A.kol* ".$points.")) WHERE A.cl = C1.short ;");
   }
   else
   {
   //выгребаем всех кланеров в команде бо€ добавл€ем клану воинственость из суммы опыта кланеров - дл€ статусных боев (тут наймы пофиг)
   mysql_query("update (select users.klan as cl, ifnull((sum(e.`exp`)),0) as kol FROM users LEFT JOIN battle_dam_exp e on e.owner=users.id and e.battle=users.battle  WHERE users.battle='{$battle_id}' and users.battle_t='{$kom}' and users.klan!=''  group by users.klan) A, oldbk.clans C1 set C1.voinst = C1.voinst + (FLOOR(A.kol* ".$points.")) WHERE A.cl = C1.short ;");
   }


/////////////////////////////////////////////////////////////
//2. разбор полетов наймов
//2.а выбираем всех наймов расчетной команды и их набитый  опыт
	if ($war_id >0) // только если бой клановый
	{
	$get_naim_exp=mysql_query("SELECT  users.id ,users.login,users.klan,users.naim,users.naim_war,  ifnull((e.`exp`),0) as kol FROM users LEFT JOIN battle_dam_exp e on e.owner=users.id and e.battle=users.battle WHERE users.battle='{$battle_id}' and users.battle_t='{$kom}' and users.naim>0  ; ");
	if (mysql_num_rows($get_naim_exp)>0)
	{
	//есть наймы в бою
		while($naimrow=mysql_fetch_assoc($get_naim_exp))
		{
			//парсим наймов
			if ($naimrow['naim_war']==$war_id)
				{
				//если ид_войны найма равен ид_войны бо€, значит надо добавить воинственность за этот бой клану которому помогает наемник и войне
				$who[1]='agressor';
				$who[2]='defender';
				//клану  в войне
				mysql_query("update oldbk.clans_war_2 C1 set C1.win".$kom." = C1.win".$kom." +".$addvo."+ (FLOOR({$naimrow['kol']}* ".$points.")) WHERE   C1.".$who[$kom]."='{$naimrow['naim']}' and C1.war_id={$war_id}  ;");
				//общий зачет клана
			  	mysql_query("update oldbk.clans C1 set C1.voinst = C1.voinst +".$addvo."+(FLOOR({$naimrow['kol']}* ".$points.")) WHERE C1.id='{$naimrow['naim']}'  ;");

				}
				else
				{
				//если эти данные не равны значит наемник билс€ в своей войне, и добавл€ть воинственность надо клану самого наемника и войне
				$who[1]='agressor';
				$who[2]='defender';
				//клану  в войне
				mysql_query("update oldbk.clans_war_2 C1 set C1.win".$kom." = C1.win".$kom." +".$addvo."+(FLOOR({$naimrow['kol']}* ".$points.")) WHERE   C1.".$who[$kom]."=(select id from oldbk.clans where short='{$naimrow['klan']}') and C1.war_id={$war_id}  ;");
				//общий зачет клана
			  	mysql_query("update oldbk.clans C1 set C1.voinst = C1.voinst +".$addvo."+ (FLOOR({$naimrow['kol']}* ".$points.")) WHERE C1.short='{$naimrow['klan']}' ;");
				}
		}

	}
	}


}



function echohelper($telo,$bat)
{
global $maxdur;
$all_ok=false;
$filt='';




$get_vars=mysql_fetch_array(mysql_query("select * from battle_vars where battle='{$bat['id']}' and owner='{$telo['id']}' ;"));

		if ($get_vars['help_use']==0)
		{
		$all_ok=true;
		}
		elseif ( ($get_vars['help_proto']>0) and ($get_vars['help_use'] < $maxdur[$get_vars['help_proto']]) )
			{
				$all_ok=true;
				$filt=" and prototype='{$get_vars['help_proto']}'  ";
			}


	if ($all_ok)
 	{
				//рисуем вызов если надо
				$get_item_help=mysql_fetch_array(mysql_query("select * from oldbk.inventory where owner='{$telo['id']}' and type=13 and nlevel<='{$telo['level']}' and ngray<='{$telo['mgray']}' and nintel<='{$telo['intel']}'  ".$filt."  and setsale=0 and bs_owner='{$telo['in_tower']}' order by prototype DESC , duration DESC limit 1;"));
				if ($get_item_help['id']>0)
						{


						?>
								<SCRIPT>
								function jshelper(title, id){

								document.all("hint3").innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=20 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
								'<table border=1 width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><form  method=POST><INPUT TYPE=hidden name=sd4 value="6"><td colspan=2 align=center>'+
								'” ¬ас есть шанс выбрать нужную магию:<small><BR></TD></TR><TR><TD width=90% align=center>'+
								'<a onclick="if(confirm(\'»спользовать сейчас?\')) { window.location=\'fbattle.php?h=1&use='+id+'\'; }  ;"; href=#><img src="http://i.oldbk.com/i/sh/cure<? if ($telo['level']>=8) { echo "180"; } else { echo "120";} ?>.gif"> »спользовать</a><br><br>'+
								'<a onclick="if(confirm(\'»спользовать сейчас?\')) { window.location=\'fbattle.php?h=2&use='+id+'&enemy=\'+document.getElementById(\'penemy\').value+\'&defend=\'+document.getElementById(\'txtblockzone\').value; }  ;"; href=#><img src="http://i.oldbk.com/i/sh/mirror.gif"> »спользовать</a><br><br>'+
								'<a onclick="findlogin(\'¬ведите им€ клона\', \'fbattle.php?h=3&use='+id+'\', \'target\'); "; href=#><img src="http://i.oldbk.com/i/sh/antimirror.gif"> »спользовать</a><br><br>'+
								'<a onclick="if(confirm(\'»спользовать сейчас?\')) { window.location=\'fbattle.php?h=4&use='+id+'\'; }  ;"; href=#><img src="http://i.oldbk.com/i/sh/cure<? if ($get_item_help['prototype']==1002223) { echo "90"; } elseif ($get_item_help['prototype']==1002224||$get_item_help['prototype']==1002225) { echo "180"; } else  { echo "360";} ?>mana0_2.gif"> »спользовать</a><br><br>'+
								'</TD></TR></TABLE></td></tr></table></form>';
								document.all("hint3").style.visibility = "visible";
								document.all("hint3").style.left = 100;
								document.all("hint3").style.top = 100;
								document.all(name).focus();
								Hint3Name = name;
								}
								</SCRIPT>
						<?
						//'<a onclick="findlogin(\'¬ведите им€ персонажа\', \'fbattle.php?h=1&use='+id+'\', \'target\'); "; href=#><img src="http://i.oldbk.com/i/sh/cure180.gif"> »спользовать</a><br><br>'+



						echo "<a onclick=\"";
						echo "jshelper('{$get_item_help['name']}', '{$get_item_help['id']}');";
						echo "\" href='#'>";
						echo '<img src="http://i.oldbk.com/i/sh/'.$get_item_help['img'].'" width=40 height=25 title="»спользовать: '.$get_item_help['name'].' ['.$get_item_help['duration'].'/'.$get_item_help['maxdur'].']  (¬озможно '.($get_item_help['maxdur']-$get_vars['help_use']).' раз.)"></a>';
						}
 	}


}

function bet_battle_eff($eff)
{
 if ($eff['lastup']>1)
	{
	//делаем -1
	mysql_query("UPDATE `effects` SET `lastup`=`lastup`-1 WHERE `id`='{$eff['id']}';");
	return 'ok';
	}
	else
	{
	//удал€ем
	mysql_query("DELETE FROM `effects` WHERE `id`='{$eff['id']}';");
	return 'del';
	}
}

function load_battle_eff($telo,$getbat)
{
global $boec_t1, $boec_t2,$user,$real_enemy;
//$telo=$telo||$vrag
	$get_battle_eff=mysql_query("select * from effects where owner='{$telo['id']}' and  (  ( battle='{$getbat['id']}' and ((`type`>=700 and `type` <=800 ) OR (`type`=838) OR (`type`=430) )  ) OR (`type` in (420,440,441,301,302,130,150,5577,900,901,902,903,904,905,906,920,930,10901,10902,10903,10904,557)))  ");

	//838,150- просто загружаем дл€ данных ничего более с ним не делаем
	if (mysql_num_rows($get_battle_eff) > 0)
	{

	while ($row = mysql_fetch_array($get_battle_eff))
		{
		$telo_eff[$row['type']]=$row;

		 if ($row['type']==711)
		 {
		 //если размены прошли между тем кто наложил эфект и между жертвой то снимаем баф
			if  (  (($user['id']==$telo['id']) and ($row['add_info']==$real_enemy['id']) ) OR
				(($real_enemy['id']==$telo['id']) and ($row['add_info']==$user['id']) ) )
				{
				$rez=bet_battle_eff($row);
				}
		 }
		 else
		 if ($row['type']==723)
		 {
		 $rez=bet_battle_eff($row);
			if (($rez=='del') and ($telo['hp']>0))
				{
				//был последний размен выходим из бо€!
				$time = time();
				mysql_query("UPDATE battle SET inf=inf+1  , to1=".$time.", to2=".$time.",  t".$telo['battle_t']."hist=REPLACE(t".$telo['battle_t']."hist,',".BNewRender($telo)."','') WHERE id = ".$telo['battle']." ;");
				mysql_query('UPDATE `users` SET `battle`=0 , `battle_t`=0 , `battle_fin`=0 , `hp`=1,  `fullmptime`='.time().' , `fullhptime` = '.time().'  WHERE `id` = '.$telo['id'].';');
				mysql_query("delete from battle_fd where (razmen_to='{$telo['id']}') or (razmen_from='{$telo['id']}')  or (owner='{$telo['id']}')  ");
				mysql_query("INSERT battle_vars (`battle`, `owner`, `bexit_count`, `bexit_team`) values ('{$telo['battle']}', '{$telo['id']}' , '1', '{$telo['battle_t']}' ) ON DUPLICATE KEY UPDATE `bexit_count`=`bexit_count`+1, `bexit_team`='{$telo['battle_t']}' ; ");
				$telo_eff['STOP']=true;
				}

		 }
		else
		// сразу обрабытываем использование т.к. это 1 размен
		 if ( ($row['type']==701) OR ($row['type']==702) OR ($row['type']==703) OR ($row['type']==705)  OR ($row['type']==706)  OR ($row['type']==712)  OR ($row['type']==714) OR ($row['type']==430)  OR
		  ($row['type']==707) OR ($row['type']==708) OR ($row['type']==709) OR ($row['type']==710) OR ($row['type']==716)  OR ($row['type']==720) OR ($row['type']==721) OR ($row['type']==722) OR ($row['type']==795)  )
		 	{
			$rez=bet_battle_eff($row);

			if (($rez=='del') and ($row['type']==720) )
				{
				//720 баф + последний юз= убиваем чара
				mysql_query("UPDATE `users` SET `hp` =0  WHERE `id` = ".$telo['id']."  ;");

				if (($telo['sex'] == 1)OR($telo['hidden']>0 and $telo['hiddenlog']=='')) { $action = ""; }
				elseif ($telo['hidden']>0 and $telo['hiddenlog']!='') {  $ftelo=load_perevopl($telo);  if ($ftelo['sex']==0) {$action="ла"; } else {$action="";}}
				else { $action="ла"; }

				  $actions[0]='умер'.$action;
				  $actions[1]='погиб'.$action;
				  $rda=mt_rand(0,1);

		//		addlog($getbat['id'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($telo,$telo['battle_t']).' <b>'.$actions[$rda].'</b>!<BR>');
				addlog($getbat['id'],"!:D:".time().":".nick_new_in_battle($telo).":".get_new_dead($telo)."\n");


				// апаем мемори
				 if ($telo['id']==$user['id'])	{$user['hp']=0;}
				 if ($telo['id']==$real_enemy['id']) {$user['hp']=0;}

				if ($telo['battle_t']==1)
				{
				$boec_t1[$telo['id']]['hp']=0 ;
				}
				else
				{
				$boec_t2[$telo['id']]['hp']=0;
				}


				}
		 	}
		}
	}
	else
	{
	return false;
	}
return $telo_eff;
}


function load_battle_eff_pre($telo,$getbat)
{
//просто загрузка ефектов - до разменов
	$get_battle_eff=mysql_query("select * from effects where owner='{$telo['id']}' and  `type` in (440,441,796,900,901,902,903,904,905,906,930,301,302,150,930,920,130) "); // 150,930,920,130 - стихии грузим дл€ отображени€
	if (mysql_num_rows($get_battle_eff) > 0)
	{

	while ($row = mysql_fetch_array($get_battle_eff))
		{
		$telo_eff[$row['type']]=$row;
		}
	}
	else
	{
	return false;
	}

return $telo_eff;
}


function echo_users_babil($telo)
{

include "abiltxt.php";

	$get_babil=mysql_query("select * from oldbk.users_babil ab LEFT JOIN magic m ON ab.magic=m.id where ab.owner='{$telo['id']}' and ab.btype=1");
	if (mysql_num_rows($get_babil)>0)
	{
	while ($abi = mysql_fetch_array($get_babil))
		{
		echo "<a  onclick=\"";
		if($abi['targeted']==1)
				{
					echo "findlogin('¬ведите им€ персонажа', '?abiluse={$abi['id']}', 'target'); ";
				} else
				{
					echo "if(confirm('»спользовать сейчас?')) {
							 window.location='?abiluse=".$abi['id']."&enemy='+document.getElementById('penemy').value+'&defend='+document.getElementById('txtblockzone').value;
					}";
				}
		echo "\" href='#'>";
		echo '<img src="http://i.oldbk.com/i/magic/'.$abi['img'].'" width=40 height=25 onMouseOut="HideThing(this);" onMouseOver="ShowThing(this,25,25,\'»спользовать:'.$atext[$abi['id']].'<br>ƒоступно: '.$abi['dur'].'/'.$abi['maxdur'].'  \');" ></a>';
		}
		echo "<br>";
	}
}

function echo_bat_exit($telo)
{
global $boec_t1,$boec_t2, $boec_t3;

				$ftelo = load_perevopl($telo);
				if (($telo['hidden']>0) and ($telo['hiddenlog']=='')) {$ftelo['sex']=1;}

				//addlog($telo['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle_hist($telo,$telo['battle_t']).' '.$lsex.' из бо€!<BR>');
				addlog($telo['battle'],"!:E:".time().":".nick_new_in_battle($telo).":".(($ftelo['sex']*100)+3)."\n");

				if ($telo['battle_t']==1) {unset($boec_t1[$telo['id']]);}
				elseif ($telo['battle_t']==2)  {unset($boec_t2[$user['id']]);}
				elseif ($telo['battle_t']==3)  {unset($boec_t3[$user['id']]);}

}


function add_bonus_lovk($telo,$mf_100p_uvor,$mf_uvor)
{
global $PARAMS;
 if (is_array($PARAMS))
 	{
if (($telo['id']==14897) OR ($telo['id']==188) or ($telo['id_user']==14897) or ($telo['id_user']==188)) { echo "LOVK_BONUS_IN {$mf_uvor} <br> "; }

			if ($telo['lovk']>=300)
				{
				$mf_uvor+=$mf_100p_uvor*($PARAMS['lovk300']-1);
				}
			else
			if ($telo['lovk']>=275)
				{
				$mf_uvor+=$mf_100p_uvor*($PARAMS['lovk275']-1);
				}
			else
			if ($telo['lovk']>=250)
				{
				$mf_uvor+=$mf_100p_uvor*($PARAMS['lovk250']-1);
				}
			else
			if ($telo['lovk']>=225)
				{
				$mf_uvor+=$mf_100p_uvor*($PARAMS['lovk225']-1);
				}
			else
			if ($telo['lovk']>=200)
				{
				$mf_uvor+=$mf_100p_uvor*($PARAMS['lovk200']-1);
				}
			else
			if ($telo['lovk']>=175)
				{
				$mf_uvor+=$mf_100p_uvor*($PARAMS['lovk175']-1);
				}
			else
			if ($telo['lovk']>=150)
				{
				$mf_uvor+=$mf_100p_uvor*($PARAMS['lovk150']-1);
				}
			else if ($telo['lovk']>=125)
				{
				$mf_uvor+=$mf_100p_uvor*($PARAMS['lovk125']-1);
				}
			else if ($telo['lovk']>=100)
				{
				$mf_uvor+=$mf_100p_uvor*($PARAMS['lovk100']-1);
				}
			else if ($telo['lovk']>=75)
				{
				$mf_uvor+=$mf_100p_uvor*($PARAMS['lovk75']-1);
				}
			else if ($telo['lovk']>=50)
				{
				$mf_uvor+=$mf_100p_uvor*($PARAMS['lovk50']-1);
				}
			else if ($telo['lovk']>=25)
				{
				$mf_uvor+=$mf_100p_uvor*($PARAMS['lovk25']-1);
				}
	}

if (($telo['id']==14897) OR ($telo['id']==188) or ($telo['id_user']==14897) or ($telo['id_user']==188)) { echo "LOVK_BONUS_OUT {$mf_uvor} <br> "; }

return $mf_uvor;
}

function add_bonus_inta($telo,$mf_100p_krit,$mf_krit)
{

global $PARAMS;

 if (is_array($PARAMS))
 	{

if (($telo['id']==14897) OR ($telo['id']==188) or ($telo['id_user']==14897) or ($telo['id_user']==188))
{ echo "INTA_BONUS_IN {$mf_krit} <br> "; }

			if ($telo['inta']>=300)
				{
				$mf_krit+=$mf_100p_krit*($PARAMS['inta300']-1);
				}
			else if ($telo['inta']>=275)
				{
				$mf_krit+=$mf_100p_krit*($PARAMS['inta275']-1);
				}
			else if ($telo['inta']>=250)
				{
				$mf_krit+=$mf_100p_krit*($PARAMS['inta250']-1);
				}
			else if ($telo['inta']>=225)
				{
				$mf_krit+=$mf_100p_krit*($PARAMS['inta225']-1);
				}
			else if ($telo['inta']>=200)
				{
				$mf_krit+=$mf_100p_krit*($PARAMS['inta200']-1);
				}
			else if ($telo['inta']>=175)
				{
				$mf_krit+=$mf_100p_krit*($PARAMS['inta175']-1);
				}
			else if ($telo['inta']>=150)
				{
				$mf_krit+=$mf_100p_krit*($PARAMS['inta150']-1);
				}
			else if ($telo['inta']>=125)
				{
				$mf_krit+=$mf_100p_krit*($PARAMS['inta125']-1);
				}
			else if ($telo['inta']>=100)
				{
				$mf_krit+=$mf_100p_krit*($PARAMS['inta100']-1);
				}
			else if ($telo['inta']>=75)
				{
				$mf_krit+=$mf_100p_krit*($PARAMS['inta75']-1);
				}
			else if ($telo['inta']>=50)
				{
				$mf_krit+=$mf_100p_krit*($PARAMS['inta50']-1);
				}
			else if ($telo['inta']>=25)
				{
				$mf_krit+=$mf_100p_krit*($PARAMS['inta25']-1);
				}
	}
if (($telo['id']==14897) OR ($telo['id']==188) or ($telo['id_user']==14897) or ($telo['id_user']==188)) { echo "INTA_BONUS_OUT {$mf_krit} <br> "; }
return $mf_krit;
}


function put_item_turn($wt,$btype,$telo,$addexp,$addkra,$addrepa)
{
    global $app, $KO_start_time47, $KO_fin_time47;

	if ($_SERVER["SERVER_NAME"]=='capitalcity.oldbk.com')  { $cnis='cap'; }   else if ($_SERVER["SERVER_NAME"]=='avaloncity.oldbk.com')  { $cnis='ava'; }	 else if ($_SERVER["SERVER_NAME"]=='angelscity.oldbk.com')  { $cnis='ang'; }

					$item_ch[3201]=5; // чек 5 кр
					$item_ch[3202]=10; //10 кр
					$item_ch[3204]=50; // 50 кр
					$item_ch[3205]=100; //100 кр

 if ($btype==304)  {

 				$nagr_chek[1]=3204; //50 кр
 				$nagr_chek[2]=3202; //10 кр
 				$nagr_chek[3]=3201; //5 кр
 				//$nagr_chek[4]=3201; //5 кр

 				}
		else {
				$nagr_chek[1]=3205; //100 кр
 				$nagr_chek[2]=3204; //50 кр
 				$nagr_chek[3]=3202; //10 кр
 				//$nagr_chek[4]=3201; //5 кр
			}

$time_out[0]=0;
$time_out[1]=10800;   //1-2 место - 3 часов таймаут
$time_out[2]=7200; //1-2 место - 2 часа таймаут
$time_out[3]=3600;  //3-4 место - 1 час таймаут
$time_out[4]=0; //4-4 - нет таймаута вообще
$time_out[5]=0; //8-8 - нет таймаута вообще
$time_out[6]=0;  //16-16 - нет таймаута вообще




	//запрос бонуса
	$time_out_bonus=0;

	$bect = mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$telo['id']}'  and `type` in  (9105,9101) ");
	while ($bonuseffect = mysql_fetch_array($bect))
	{
		if  ($bonuseffect['type']==9105)
		{
			//бонус на таймаут
			$time_out_bonus=(int)($time_out[$wt]*$bonuseffect['add_info']);
		}

        elseif ($bonuseffect['type']==9101)
		{
			//бонус на репу
			$addrepa+=(int)($addrepa*$bonuseffect['add_info']);
		}

	}


	//вешаем тайм - согласно типу награды
	if ($time_out[$wt]>0)
	{

		$UserBadge = \components\models\UserBadge::whereRaw('rate_unique = ? and user_id = ?', [
			\components\models\UserBadge::TYPE_NTUR_RISTA,
			$telo['id']
		])->first(['stage']);
		$stage = $UserBadge ? $UserBadge->stage : -1;
		$mbonus[0]=0.03;
		$mbonus[1]=0.05;
		$mbonus[2]=0.07;
		$mbonus[3]=0.10;

		$time_out_bonus2=(int)($time_out[$wt]*$mbonus[$stage]);

		$time_out[$wt]-=$time_out_bonus;
		$time_out[$wt]-=$time_out_bonus2;

		mysql_query("INSERT INTO `effects` SET `type`=8270,`name`='—ледующее посещение ќдиночных —ражений',`time`=".(time()+$time_out[$wt]).",`owner`='{$telo['id']}';");
	}

	if($wt == 1) {
		if(!$KO_start_time47 || !$KO_fin_time47) {
            require_once("config_ko.php");
		}

		\components\Helper\FileHelper::writeArray([
			'$KO_start_time47' => $KO_start_time47,
			'$KO_fin_time47' => $KO_fin_time47,
		], 'fsystem_put_item_turn_debug');

		$_curr_time = time();
	    if($KO_start_time47 < $_curr_time && $_curr_time < $KO_fin_time47) {
			try {
				$_item_day_goden = round(($KO_fin_time47-$_curr_time)/60/60/24);
				if ($_item_day_goden < 1) { $_item_day_goden=1; }

				put_bonus_item(591, $telo, '”дача', [], ['goden' => $_item_day_goden, 'dategoden' => $KO_fin_time47]);
			} catch (Exception $ex) {
				\components\Helper\FileHelper::writeException($ex, 'fsystem_put_item_turn');
			}
        }


	}

	if (($wt>=1) and ($wt<=3))
	{
				$vp=array(1=>5,2=>3,3=>1);
				try {
				$NturRating = new \components\Helper\rating\NturRating();
				$NturRating->value_add = $vp[$wt];

				$app->applyHook('event.rating', $telo, $NturRating);
				} catch (Exception $ex) {
				$app->logger->addEmergency((string)$ex);
				}

	}

	if ($wt>0)
	{
				$check=$nagr_chek[$wt]; // нужный ид чека
				$item_counts=0;
				$item_typee=0;

				if ($check>0)
				{
				//если положен чек
			        	mysql_query("INSERT INTO oldbk.`inventory` (`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`,`idcity`)
			        	 VALUES ('„ек на предъ€вител€ ".$item_ch[$check]."кр',0,1,'{$item_ch[$check]}','{$telo['id']}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'lab2_".$item_ch[$check]."kr.gif','',0,0,0,0,0,0,0,50,'–исталище',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',0,'',0,'{$check}','52',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,1,NULL,0,NULL,NULL,0,'{$telo['id_city']}');");
		        		$dressid = $cnis."".mysql_insert_id().",";
		        		$rec['item_name']="\"„ек на предъ€вител€ ".$item_ch[$check]."кр\"";
					$item_counts++;
					$item_typee=50;
					$chat_text='<font color=red>ѕоздравл€ем!</font> ¬ы получили <b>\"„ек на предъ€вител€ '.$item_ch[$check].'кр\"</b>';
				}
				else if ($addkra>0)
				{
				//получаем кредиты вместо чека
				// делаем только системку т.к. креды добав€тс€ в общем запросе после вызова функи
				$chat_text='<font color=red>ѕоздравл€ем!</font> ¬ы получили <b>\"'.$addkra.'кр\"</b> за участие в турнире';
				}

			        	if ($wt==1)
			        	{
			        	mysql_query("INSERT INTO oldbk.`inventory` (`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`,`idcity`)
			        	VALUES ('—ердце –ыцар€',0,1,1,'{$telo['id']}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'heart_of_hero".mt_rand(1,7).".gif','',0,0,0,0,0,0,0,200,'',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',0,'',0,1011001,'72',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,1,NULL,0,NULL,NULL,0,'{$telo['id_city']}');");
		        		$dressid .= $cnis."".mysql_insert_id();
		        		$rec['item_name'].=",\"—ердце –ыцар€\"";
					$item_counts++;
					$item_typee=50;
					$chat_text.=' и —увенир:<b>\"—ердце –ыцар€\"</b> !';

					if ($addexp>0)
					{
			        	addchp ('<font color=red>ѕоздравл€ем!</font> ¬ы получили <b>'.$addexp.'</b> опыта за победу в турнире!','{[]}'.$telo['login'].'{[]}',270,$telo['id_city']);
			        	}

			        	//+1 победа
   	    				 mysql_query("INSERT INTO oldbk.users_progress set owner='{$telo['id']}', ar270_win=1 ON DUPLICATE KEY UPDATE ar270_win=ar270_win+1");



				        	if ($addrepa>0)
				        	{
				        	addchp ('<font color=red>ѕоздравл€ем!</font> ¬ы получили <b>'.$addrepa.'</b> репутации за победу в турнире!','{[]}'.$telo['login'].'{[]}',270,$telo['id_city']);
				        	}

				 		    $last_qe=mysql_fetch_assoc(mysql_query("select * from oldbk.beginers_quests_step where status=0 AND owner ='{$telo['id']}'  AND quest_id=106 and status=0 order by id desc;"));
				 		    if ($last_qe['id']>0)
				 		    	{
				 		    	$last_q[106]=$last_qe;
			       		         	    quest_check_type_20($last_q,$telo['id'],'OFF',71); // квест номер 106
			       		         	 }

							try {
								$UserModel = \components\models\User::find($telo['id']);
								if(!$UserModel) {
									throw new Exception(sprintf('User not found: %d', $telo['id']));
								}
								/** @var \components\Component\Quests\Quest $QuestComponent */
								$QuestComponent = $app->quest->get($UserModel);

								$Checker = new \components\Component\Quests\check\CheckerEvent();
								$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_RIST_WIN;
								if (($Items = $QuestComponent->isNeed($Checker, true))) {
									$QuestComponent->taskUpMultiple($Items);
									unset($Items);
								}

								unset($UserModel);
								unset($QuestComponent);
								unset($Checker);
							} catch (Exception $ex) {
								\components\Helper\FileHelper::writeException($ex, 'fsystem_ntur_reward');
							}
		        		}
		        		else
		        		{

		        			if ($addexp>0)
		        				{
					        	addchp ('<font color=red>ѕоздравл€ем!</font> ¬ы получили <b>'.$addexp.'</b> опыта за участие в турнире!','{[]}'.$telo['login'].'{[]}',270,$telo['id_city']);
				        		}

						if ($addrepa>0)
		        				{
					        	addchp ('<font color=red>ѕоздравл€ем!</font> ¬ы получили <b>'.$addrepa.'</b> репутации за участие в турнире!','{[]}'.$telo['login'].'{[]}',270,$telo['id_city']);
				        		}

		        		}

		        		if ($chat_text!='')
		        			{
						addchp ($chat_text,'{[]}'.$telo['login'].'{[]}',270,$telo['id_city']);
						}

		        		if ( ($item_typee>0) OR ($addexp>0) OR ($addrepa>0) OR ($addkra>0) )
		        		{
					//new delo
					$rec['owner']=$telo['id'];
					$rec['owner_login']=$telo['login'];
					$rec['owner_balans_do']=$telo['money'];
					$rec['owner_balans_posle']=($telo['money']+$addkra);
					$rec['owner_rep_do']=$telo['repmoney'];
					$rec['owner_rep_posle']=($telo['repmoney']+$addrepa);
					$rec['target']=0;
					$rec['target_login']='ристалище';
					$rec['type']=4;
					$rec['sum_kr']=$addkra;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['sum_rep']=$addrepa;
					$rec['item_id']=$dressid;

					$rec['item_count']=$item_counts;
					$rec['item_type']=$item_typee;
					$rec['item_cost']=$item_ch[$check];
					$rec['item_dur']=0;
					$rec['item_maxdur']=0;
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']='';

						if ($addexp>0)
							{
							$rec['add_info']='ѕолучено:'.$addexp.' опыта';
							}
					add_to_new_delo($rec);
					}

					return $addrepa;

	}

}

function get_kv_bonus($telo)
{
//8) Ќедел€ кланваров -  за то что ты убил кого-то (принцип как в противосто€нии) вне зависимости от выигран бой или проигран, даЄтс€  с веро€тностью:
	$get_ivent=mysql_fetch_array(mysql_query("select * from oldbk.ivents where id=8"));
	if ($get_ivent['stat']==1) //если недел€  KV
							{
							$mk_bonus_items=0;
							$rmnt=mt_rand(1,100);
								if (($rmnt>=20) and ($rmnt<=60)) //20% ничего, 40% лечилка любых травм на один юз, 40%  ловушка.
									{
									$mk_bonus_items=125126;
									}
								elseif ($rmnt>=60)
									{
									$mk_bonus_items=40000000;
									}

									if ($mk_bonus_items>0)
									{
									//выдаем
									put_bonus_item($mk_bonus_items,$telo,'клановые войны');
									}
							}
}

function mk_elka_bot($telo)
{
global  $boec_t1,  $boec_t2,  $boec_t3, $data_battle;
//выпускаем бота
//if($telo[hp]<=0) {  return false;  }
if (($telo['room'] >= 210) AND ($telo['room'] <= 300))   { return false;  }
else
	{
//добавить проверку  бо€!
	$bots_config_array=array( 8 =>249, 9 =>250, 10 =>251,11 =>252, 	12 =>253, 13 =>254, 14 =>255, 15=>256 );
	$bots_config=$bots_config_array[$telo['level']];// id  бота

	if ($bots_config>0)
	{
	$clon_count=mysql_fetch_array(mysql_query("select * from battle_vars WHERE battle='".$telo['battle']."' and owner='".$telo['id']."' ORDER BY `bots_use` DESC LIMIT 1; "));
	if (($clon_count[bots_use] < 1000) or ($telo['id']==9))
			        {
				$cb=1;

					//делаем масив дл€ бота
					$bot_data=mysql_fetch_array(mysql_query("select * from users where id={$bots_config} ;"));
					$bot_data[login]=$bot_data[login]." (".($clon_count[bots_use]+$cb).")";
					$bots_items=load_mass_items_by_id($bot_data);
					$bots_items['allsumm']=$bots_items['allsumm']*0.4;//занижаем стоимость шмоток

					mysql_query("INSERT INTO `users_clons` SET `login`='".$bot_data[login]."',`sex`='{$bot_data['sex']}',
								`level`='{$bot_data['level']}',`align`='{$bot_data['align']}',`klan`='{$bot_data['klan']}',`sila`='{$bot_data['sila']}',
								`lovk`='{$bot_data['lovk']}',`inta`='{$bot_data['inta']}',`vinos`='{$bot_data['vinos']}',
								`intel`='{$bot_data['intel']}',`mudra`='{$bot_data['mudra']}',`duh`='{$bot_data['duh']}',`bojes`='{$bot_data['bojes']}',`noj`='{$bot_data['noj']}',
								`mec`='{$bot_data['mec']}',`topor`='{$bot_data['topor']}',`dubina`='{$bot_data['dubina']}',`maxhp`='{$bot_data['maxhp']}',`hp`='{$bot_data['hp']}',
								`maxmana`='{$bot_data['maxmana']}',`mana`='{$bot_data['mana']}',`sergi`='{$bot_data['sergi']}',`kulon`='{$bot_data['kulon']}',`perchi`='{$bot_data['perchi']}',
								`weap`='{$bot_data['weap']}',`bron`='{$bot_data['bron']}',`r1`='{$bot_data['r1']}',`r2`='{$bot_data['r2']}',`r3`='{$bot_data['r3']}',`helm`='{$bot_data['helm']}',
								`shit`='{$bot_data['shit']}',`boots`='{$bot_data['boots']}',`nakidka`='{$bot_data['nakidka']}',`rubashka`='{$bot_data['rubashka']}',`shadow`='{$bot_data['shadow']}',`battle`='{$telo['battle']}',`bot`=1,
								`id_user`='{$bot_data['id']}',`at_cost`='{$bots_items['allsumm']}',`kulak1`=0,`sum_minu`='{$bots_items['min_u']}',
								`sum_maxu`='{$bots_items['max_u']}',`sum_mfkrit`='{$bots_items['krit_mf']}',`sum_mfakrit`='{$bots_items['akrit_mf']}',
								`sum_mfuvorot`='{$bots_items['uvor_mf']}',`sum_mfauvorot`='{$bots_items['auvor_mf']}',`sum_bron1`='{$bots_items['bron1']}',
								`sum_bron2`='{$bots_items['bron2']}',`sum_bron3`='{$bots_items['bron3']}',`sum_bron4`='{$bots_items['bron4']}',`ups`='{$bots_items['ups']}',
								`injury_possible`=0, `battle_t`='{$telo['battle_t']}', `mklevel`='{$telo[level]}' ;");

						$bot_data[id] = mysql_insert_id();
						$time = time();
						$ttt=$telo[battle_t];

							// добавл€ем в мемори данные
						if ($user[battle_t]==1)
						  {
						  $boec_t1[$bot_data[id]]=$bot_data;
						  // обновл€ем данные
				         	  $data_battle[t1].=";".$bot_data[id];
						  }
						  elseif ($user[battle_t]==2)
						  {
						  $boec_t2[$bot_data[id]]=$bot_data;
				  		  // обновл€ем данные
				         	  $data_battle[t2].=";".$bot_data[id];
						  }
						  elseif ($user[battle_t]==3)
						  {
						  $boec_t3[$bot_data[id]]=$bot_data;
				  		  // обновл€ем данные
				         	  $data_battle[t3].=";".$bot_data[id];
						  }

						$temp_bot_name = BNewHist($bot_data);
						$temp_bot_namea = nick_align_klan($bot_data);

						if ($cb==1) {
							if (($cb>=($cb_stop)) and ($cb>1)) { $ptex=" и "; } else { $ptex=""; }
							$all_bots_namea=$ptex.$temp_bot_namea;
							$all_bots_id=$bot_data[id];
							$all_bots_hist=$temp_bot_name;
						} else {
							if (($cb>=($cb_stop)) and ($cb>1)) { $ptex=" и "; } else { $ptex=", "; }
							$all_bots_namea.=$ptex.$temp_bot_namea;
							$all_bots_id.=';'.$bot_data[id];
							$all_bots_hist.= $temp_bot_name;
						}

						mysql_query("INSERT `battle_vars` (`battle`,`owner`,`update_time`,`bots_use`) values('".$telo['battle']."', '".$telo['id']."', '".time()."' , '{$cb}' ) ON DUPLICATE KEY UPDATE `bots_use` =`bots_use`+{$cb};");

						if ($telo[hidden]>0 and $telo[hiddenlog]=='') 	{ $telo[sex]=1;	}
						elseif ($telo[hidden]>0 and $telo[hiddenlog]!='') {  $fuser=load_perevopl($telo); $telo[sex]=$fuser[sex]; }
						$btext=str_replace(':','^',$all_bots_namea);
				       	       addlog($telo['battle'],"!:X:".time().':'.nick_new_in_battle($telo).':'.($telo[sex]+1020).":".trim($btext)."\n");

						if ($data_battle[t3]!='')
						{
						mysql_query('UPDATE battle SET to1='.$time.', to2='.$time.', to3='.$time.', t'.$ttt.'=CONCAT(t'.$ttt.',\';'.$all_bots_id.'\') , t'.$ttt.'hist=CONCAT(t'.$ttt.'hist,\''.$all_bots_hist.'\')    WHERE id = '.$telo['battle'].' ;');
						}
						else
						{
						mysql_query('UPDATE battle SET to1='.$time.', to2='.$time.', t'.$ttt.'=CONCAT(t'.$ttt.',\';'.$all_bots_id.'\') , t'.$ttt.'hist=CONCAT(t'.$ttt.'hist,\''.$all_bots_hist.'\')    WHERE id = '.$telo['battle'].' ;');
						}

						return true;
				}
	}
	}

return false;
}

function get_chance_point($telo,$dmg,$runs)
{
$lim1=10;
$lim2=10;
$k=5; //5%

     //ѕервое условие:  ”рон / ћакс’ѕ= колво очко, лимит 10
    $us1=(int)($dmg['damage']/$telo['maxhp']);
    if ($us1>$lim1) $us1=$lim1;

    //¬торое условие:  отхилл ’ѕ / ћакс’ѕ= колво очков, лимит 10
    $us2=(int)($runs['cure_value_hp']/$telo['maxhp']);
    if ($us2>$lim2) $us2=$lim2;

    //если первое или второе условие <1, то не учитываем персонажа в выдаче награды
    if (($us1<1) OR ($us2<1))
    	{
    	return 0;
    	}

//     если первое условие >=1, то плюсуем очки за оба услови€, где: 1 очко = 5% шанс дропа
//    если второе условие >=1, то плюсуем очки за оба услови€, где: 1 очко = 5% шанс дропа

return round(($us1+$us2)*$k);
}

function RuinesCheckPenalty($mapid) {
	// выгребаем всех участников турнира
	$q = mysql_query('SELECT * FROM ruines_activity_log WHERE mapid = '.$mapid.' and var = "start"');
	$uids = array();
	while($u = mysql_fetch_assoc($q)) {
		$uids[] = $u['owner'];
	}

	// проходимс€ по каждому, по последним 10 турнирам и считаем баллы
	reset($uids);
	while(list($k,$v) = each($uids)) {
		// выгребли 10 последних до этого турниров (включа€ этот)
		$q = mysql_query('SELECT * FROM ruines_activity_log WHERE mapid <= '.$mapid.' AND var = "start" and owner = '.$v.' ORDER BY mapid DESC LIMIT 10');
		$mapids = array();
		while($m = mysql_fetch_assoc($q)) {
			$mapids[$m['mapid']] = array('owner' => $m['owner']);
		}

		// выгребаем всю активность по этим турнирам
		if (count($mapids)>0) {
			$q = mysql_query('SELECT * FROM ruines_activity_log WHERE mapid IN ('.implode(",",array_keys($mapids)).') and owner = '.$v);
			while($m = mysql_fetch_assoc($q)) {
				$mapids[$m['mapid']][$m['var']] = $m['val'];
			}
		}

		// считаем баллы
		$txttosave = "";
		$penalty = 0;
		reset($mapids);
		while(list($ka,$va) = each($mapids)) {
			// ka - айди карты
			// va - массив с данными

			if (!isset($va['give'])) {
				$penalty += 15;
				$txttosave .= "map: ".$ka." give.";
			}
			if (!isset($va['path'])) {
				$penalty += 15;
				$txttosave .= "map: ".$ka." path.";
			}
			if (!isset($va['battle'])) {
				$penalty += 5;
				$txttosave .= "map: ".$ka." battle.";
			}
			if (!isset($va['dress'])) {
				$penalty += 5;
				$txttosave .= "map: ".$ka." dress.";
			}
			if (isset($va['22path'])) {
				$penalty += 15;
				$txttosave .= "map: ".$ka." 22path.";
			}
			if (!isset($va['chaton'])) {
				$penalty += 15;
				$txttosave .= "map: ".$ka." chaton.";
			}
			if (isset($va['15chaton'])) {
				$penalty += 5;
				$txttosave .= "map: ".$ka." 15chaton.";
			}
			if (isset($va['noexitfirst'])) {
				$penalty += 5;
				$txttosave .= "map: ".$ka." noexitfirst.";
			}
		}

		if ($penalty >= 150) {
			$fp = fopen('/www/other/ruinesban.txt','a+');
			flock($fp,LOCK_EX);
			fwrite($fp,time().":".$v.":".$txttosave."\r\n");
			fclose($fp);



			// выставл€ем врем€ до след посещени€ руин - ћ≈—я÷
			mysql_query('INSERT INTO oldbk.`ruines_var` (`owner`,`var`,`val`)  VALUES ('.$v.',"cango",'.(time()+30*24*3600).')  ON DUPLICATE KEY UPDATE  `val` = '.(time()+30*24*3600));

			// чистим активность тела
			mysql_query('DELETE FROM ruines_activity_log WHERE owner = '.$v);


		}
	}
}

?>
