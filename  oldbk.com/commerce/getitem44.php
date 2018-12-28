<?
session_start();
include "/www/oldbk.com/connect.php";

$nclass_name[0]='Любой';     //неопределен = старые предметы
$nclass_name[1]='Уворотчик';
$nclass_name[2]='Критовик';
$nclass_name[3]='Танк';
$nclass_name[4]='Любой';     //любой только класс = новые предметы


function MK_UP_ART($dress,$nup)
{
//функа для расчета апа
$up=array();
$up[7] = array(	"level" => 7, 	"hp" => 6,	"bron" => 1,	"stat" => 1,	"mf" => 5,	"udar" => 1,	"nparam" => 1,	"duration" => 5,	"destiny" => false,	"nintel" => 0	);
$up[8] = array(	"level" => 8,	"hp" => 8,	"bron" => 1,	"stat" => 1,	"mf" => 7,	"udar" => 2,	"nparam" => 1,	"duration" => 5,	"destiny" => false,	"nintel" => 0	);
$up[9] = array(	"level" => 9,	"hp" => 10,	"bron" => 1,	"stat" => 1,	"mf" => 10,	"udar" => 3,	"nparam" => 1,	"duration" => 10,	"destiny" => false,	"nintel" => 0	);
$up[10] = array(	"level" => 10,	"hp" => 12,	"bron" => 1,	"stat" => 1,	"mf" => 12,	"udar" => 4,	"nparam" => 1,	"duration" => 10,	"destiny" => false,	"nintel" => 0	);
$up[11] = array(	"level" => 11,	"hp" => 15,	"bron" => 1,	"stat" => 1,	"mf" => 15,	"udar" => 1,	"nparam" => 1,	"duration" => 15,	"destiny" => false,	"nintel" => 0	);
$up[12] = array(	"level" => 12,	"hp" => 20,	"bron" => 1,	"stat" => 1,	"mf" => 17,	"udar" => 1,	"nparam" => 1,	"duration" => 15,	"destiny" => false,	"nintel" => 0	);
$up[13] = array(	"level" => 13,	"hp" => 27,	"bron" => 1,	"stat" => 1,	"mf" => 22,	"udar" => 2,	"nparam" => 1,	"duration" => 15,	"destiny" => false,	"nintel" => 0 ) ;
$up[14] = array(	"level" => 14, "hp" => 35,	"bron" => 1,	"stat" => 1,	"mf" => 27,	"udar" => 2,	"nparam" => 1,	"duration" => 15,	"destiny" => false,	"nintel" => 0 );

//суммируем все что должно дать
$upgrade=array();

for($i=$dress['nlevel']+1;$i<=$nup;$i++)
	{
	$upgrade['level']=$up[$i]['level'];
	$upgrade['hp']+=$up[$i]['hp'];	
	$upgrade['bron']+=$up[$i]['bron'];
	$upgrade['stat']+=$up[$i]['stat'];
	$upgrade['mf']+=$up[$i]['mf'];
	$upgrade['udar']+=$up[$i]['udar'];
	$upgrade['nparam']+=$up[$i]['nparam'];
	$upgrade['duration']+=$up[$i]['duration'];	
	}

//новое название
			$sharp=explode("+",$dress['name']);
			$basename=$sharp[0];
			if ((int)($sharp[1])>0) {$is_sharp="+".$sharp[1]; } else {$is_sharp='';}
			$newname = $basename." [".$upgrade['level']."]".$is_sharp;

///новые данные шмотки
$dress['name']= $newname;
$dress['nlevel']=$upgrade['level'];
$dress['up_level']=$upgrade['level'];
$dress['maxdur']+=$upgrade['duration'];

		if ($dress['type'] == 3)
			{
			$dress['minu']+=$upgrade['udar'];
			$dress['maxu']+=$upgrade['udar'];
			}
		else
			{
		  	if ($dress['ghp'] > 0) {  $dress['ghp']+=$upgrade['hp']; }
			if ($dress['bron1'] > 0) { $dress['bron1']+=$upgrade['bron']; }
			if ($dress['bron2'] > 0) { $dress['bron2']+=$upgrade['bron']; }
			if ($dress['bron3'] > 0) { $dress['bron3']+=$upgrade['bron']; }
			if ($dress['bron4'] > 0) { $dress['bron4']+=$upgrade['bron']; }
			if (($dress['gsila'] != 0) OR ($dress['glovk']!=0) OR ($dress['ginta']!=0) OR ($dress['gintel']!=0) OR ($dress['stbonus']!=0)) { $dress['stbonus']+=$upgrade['stat']; }
			if (($dress['mfkrit'] !=0) OR ($dress['mfakrit']!=0) OR ($dress['mfuvorot']!=0) OR ($dress['mfauvorot']!=0) OR ($dress['mfbonus']!=0) ) { $dress['mfbonus']+=$upgrade['mf']; }
			}
		
		if ($dress['nsila'] > 0) { $dress['nsila']+=$upgrade['nparam']; }
		if ($dress['nlovk'] > 0) { $dress['nlovk']+=$upgrade['nparam']; }
		if ($dress['ninta'] > 0) { $dress['ninta']+=$upgrade['nparam']; }
		if ($dress['nvinos'] > 0) { $dress['nvinos']+=$upgrade['nparam']; }
		if ($dress['nnoj'] > 0) { $dress['nnoj']+=$upgrade['nparam']; }
		if ($dress['ntopor'] > 0) { $dress['ntopor']+=$upgrade['nparam']; }
		if ($dress['ndubina'] > 0) { $dress['ndubina']+=$upgrade['nparam']; }
		if ($dress['nmech'] > 0) { $dress['nmech']+=$upgrade['nparam']; }


return $dress;
}

function s_nick($id,$align,$klan,$login,$level)
{

  if($align!=''){
  $align="<img src=https://i.oldbk.com/i/align_".$align.".gif border=0>";
  }
  else
  {
   $align='';
  }
  if($klan!=''){
  $klan="<img src=https://i.oldbk.com/i/klan/".$klan.".gif border=0>";
        }
        else
        {
         $klan='';
        }

  $r_info=$align.$klan."<b>".$login."</b>[".$level."]<a target=_blank href=/inf.php?".$id."><img border=0 src=https://i.oldbk.com/i/inf.gif></a>";
  return $r_info;

}

function check_users_city_data($id)
{
    $user_city=mysql_fetch_array(mysql_query('SELECT * from oldbk.`users` where id="'.$id.'";'));
	if(!$user_city)
	{
		$user_city=FALSE;
	}
	else
    if($user_city['id_city']==1)
	{
		$user_city=mysql_fetch_array(mysql_query('SELECT * from avalon.`users` where id="'.$id.'";'));
	}
    return  $user_city;
}


$unikid = explode("_",$_GET['id']);
if (count($unikid) != 2) die();

function ShowARTForm($oldart,$newart) {
global $nclass_name;
	$us = check_users_city_data($_SESSION['uid']);

	$newart['present']=$oldart['present'];

	echo "<table align=center border=0 cellpadding=10 cellspacing=0 class=\"tbl_inside\" width=\"80%\" >";	
	$co[1]='#f4f2e9';
	$co[2]='#f9f7ee';
	$c=2;
	echo '<tr bgcolor="'.$co[$c].'"><td valign=top width="45%">';
echo "<div align=center><b>Вы отдаете:</b></div>";
	echo "<div id=\"old_img\">";
	echo "<img src=https://i.oldbk.com/i/sh/$oldart[img]><br><br>";
	echo "</div>";

	if ($oldart['nalign']==1) {
		$print_align='1.5';
	} else {
		$print_align=$oldart['nalign'];
	}

	$ehtml=str_replace('.gif','',$oldart['img']);
	$razdel=array(1=>"kasteti", 11=>"axe", 12=>"dubini", 13=>"swords", 14=>"bow", 2=>"boots", 21=>"naruchi", 22=>"robi", 23=>"armors",
	24=>"helmet", 3=>"shields",4=>"clips", 41=>"amulets", 42=>"rings", 5=>"mag1", 51=>"mag2", 6=>"amun");

	$oldart['otdel']==''?$xx=$oldart['razdel']:$xx=$oldart['otdel'];
	if($razdel[$xx]=='') {
		$razdel[$xx]='predmeti';
	} else {
		$razdel[$xx]=$razdel[$xx]."/".$ehtml;
	}

		if ( ($oldart['includemagic']>0) AND ($oldart['includemagicuses']>0)  )
		{
		$incmagic=mysql_fetch_array(mysql_query("select * from oldbk.magic  where id=".$oldart['includemagic']));
		///перенос встроек из старого арта
						$newart['includemagic']=$oldart['includemagic'];
						$newart['includemagicdex']=$oldart['includemagicdex'];
						$newart['includemagicmax']=$oldart['includemagicmax'];
						$newart['includemagicname']=$oldart['includemagicname'];
						$newart['includemagicuses']=$oldart['includemagicuses'];
						$newart['includemagiccost']=$oldart['includemagiccost'];
						$newart['includemagicekrcost']=$oldart['includemagicekrcost'];
						$newart['nintel']=$newart['nintel']<$oldart['nintel']?$oldart['nintel']:$newart['nintel'];
						$newart['nmudra']=$newart['nmudra']<$oldart['nmudra']?$oldart['nmudra']:$newart['nmudra'];
						$newart['nfire']=$newart['nfire']<$oldart['nfire']?$oldart['nfire']:$newart['nfire'];
						$newart['nwater']=$newart['nwater']<$oldart['nwater']?$oldart['nwater']:$newart['nwater'];
						$newart['nair']=$newart['nair']<$oldart['nair']?$oldart['nair']:$newart['nair'];
						$newart['nearth']=$newart['nearth']<$oldart['nearth']?$oldart['nearth']:$newart['nearth'];
						$newart['nlight']=$newart['nlight']<$oldart['nlight']?$oldart['nlight']:$newart['nlight'];
						$newart['ngray']=$newart['ngray']<$oldart['ngray']?$oldart['ngray']:$newart['ngray'];
						$newart['ndark']=$newart['ndark']<$oldart['ndark']?$oldart['ndark']:$newart['ndark'];

		$incmagic['max']=$newart['includemagicmax'];
		$incmagic['name']=$newart['includemagicname'];
		$incmagic['cur']=$newart['includemagicdex'];
		$incmagic['uses']=$newart['includemagicuses'];
		}
	
		if ($oldart['sowner']>0)
			{
			$sowner= '<font color=red>Данную вещь может надеть только</font> '.s_nick($us['id'],$us['align'],$us['klan'],$us['login'],$us['level']);	
			}


	echo "<a href=https://oldbk.com/encicl/".$razdel[$xx].".html target=_blank><b>{$oldart['name']}</b></a>";
	echo "<img src=https://i.oldbk.com/i/align_{$print_align}.gif> (Масса: {$oldart['massa']})".(($oldart['present'])?' <IMG SRC="https://i.oldbk.com/i/podarok.gif" WIDTH="16" HEIGHT="18" BORDER=0 TITLE="Этот предмет вам подарил '.$oldart['present'].'. Вы не сможете передать этот предмет кому-либо еще." ALT="Этот предмет вам подарил '.$oldart['present'].'. Вы не сможете передать этот предмет кому-либо еще.">':"")."<BR>";
	echo $sowner;
	echo "<BR>Долговечность : {$oldart['duration']}/{$oldart['maxdur']}<BR>";
	echo (($oldart['ups']>0)?"Подогнано:<b>{$oldart['ups']} раз</b><BR>":"");
	echo (($oldart['stbonus']>0)?"Возможных увеличений: ".($artun==1?"<font color=red>":"")."<b>{$oldart['stbonus']}</b>".($artun==1?" </font><b class=text2><font color=red>(в свободном распределении после приобретения артефакта)</font></b>":"")."<BR>":"");
	echo (($oldart['mfbonus']>0)?"Возможных увеличений мф: ".($artun==1?"<font color=red>":"")."<b>{$oldart['mfbonus']}</b>".($artun==1?" </font><b class=text2><font color=red>(в свободном распределении после приобретения артефакта)</font></b>":"")."</b><BR>":"");
	echo (($magic['chanse'])?"Вероятность срабатывания: ".$magic['chanse']."%<BR>":"")."
	".(($magic['time'])?"Продолжительность действия магии: ".$magic['time']." мин.<BR>":"")."
	".(($oldart['goden'])?"Срок годности: {$oldart['goden']} дн. ".(((!$oldart[GetShopCount()])or($_SERVER['PHP_SELF']=='/comission.php')or($_SERVER['PHP_SELF']=='/main.php'))?"(до ".date("d.m.Y H:i",$oldart['dategoden']).")":"")."<BR>":"")."
	".(($oldart['nsex']==1)?"• Пол: <b>Женский</b><br>":"")."
	".(($oldart['nsex']==2)?"• Пол: <b>Мужской</b><br>":"");
	
	
	echo (($oldart['nsila'] OR $oldart['nlovk'] OR $oldart['ninta'] OR $oldart['nvinos'] OR $oldart['nlevel'] OR $oldart['nintel'] OR $oldart['nmudra'] OR $oldart['nnoj'] OR $oldart['ntopor'] OR $oldart['ndubina'] OR $oldart['nmech'] OR $oldart['nfire'] OR $oldart['nwater'] OR $oldart['nair'] OR $oldart['nearth'] OR $oldart['nearth'] OR $oldart['nlight'] OR $oldart['ngray'] OR $oldart['ndark'] OR ($oldart['nclass'] >0)  )?"<br>Требуется минимальное:<BR>":"");
	
				if ($oldart['nclass'] >0) 
			{
				if ($nclass_name[$oldart['nclass']]!='')
				{	
				echo "• Класс персонажа: <b>{$nclass_name[$oldart['nclass']]}</b><br>";
				}
			}
	
	echo (($oldart['nsila']>0)?"• Сила: {$oldart['nsila']}</font><BR>":"")."
	".(($oldart['nlovk']>0)?"• Ловкость: {$oldart['nlovk']}</font><BR>":"")."
	".(($oldart['ninta']>0)?"• Интуиция: {$oldart['ninta']}</font><BR>":"")."
	".(($oldart['nvinos']>0)?"• Выносливость: {$oldart['nvinos']}</font><BR>":"")."
	".(($oldart['nlevel']>0)?"• Уровень: {$oldart['nlevel']}</font><BR>":"")."
	".(($oldart['nintel']>0)?"• Интеллект: {$oldart['nintel']}</font><BR>":"")."
	".(($oldart['nmudra']>0)?"• Мудрость: {$oldart['nmudra']}</font><BR>":"")."
	".(($oldart['nnoj']>0)?"• Мастерство владения ножами и кастетами: {$oldart['nnoj']}</font><BR>":"")."
	".(($oldart['ntopor']>0)?"• Мастерство владения топорами и секирами: {$oldart['ntopor']}</font><BR>":"")."
	".(($oldart['ndubina']>0)?"• Мастерство владения дубинами и булавами: {$oldart['ndubina']}</font><BR>":"")."
	".(($oldart['nmech']>0)?"• Мастерство владения мечами: {$oldart['nmech']}</font><BR>":"")."
	".(($oldart['nfire']>0)?"• Мастерство владения стихией Огня: {$oldart['nfire']}</font><BR>":"")."
	".(($oldart['nwater']>0)?"• Мастерство владения стихией Воды: {$oldart['nwater']}</font><BR>":"")."
	".(($oldart['nair']>0)?"• Мастерство владения стихией Воздуха: {$oldart['nair']}</font><BR>":"")."
	".(($oldart['nearth']>0)?"• Мастерство владения стихией Земли: {$oldart['nearth']}</font><BR>":"")."
	".(($oldart['nlight']>0)?"• Мастерство владения магией Света: {$oldart['nlight']}</font><BR>":"")."
	".(($oldart['ngray']>0)?"• Мастерство владения серой магией: {$oldart['ngray']}</font><BR>":"")."
	".(($oldart['ndark']>0)?"• Мастерство владения магией Тьмы: {$oldart['ndark']}</font><BR>":"")."
	".(($oldart['gmeshok'] OR $oldart['gsila'] OR $oldart['mfkrit'] OR $oldart['mfakrit']  OR $oldart['mfuvorot'] OR $oldart['mfauvorot']  OR $oldart['glovk'] OR $oldart['ghp'] OR $oldart['ginta'] OR $oldart['gintel'] OR $oldart['gnoj'] OR $oldart['gtopor'] OR $oldart['gdubina'] OR $oldart['gmech'] OR $oldart['gfire'] OR $oldart['gwater'] OR $oldart['gair'] OR $oldart['gearth'] OR $oldart['gearth'] OR $oldart['glight'] OR $oldart['ggray'] OR $oldart['gdark'] OR $oldart['minu'] OR $oldart['maxu'] OR $oldart['bron1'] OR $oldart['bron2'] OR $oldart['bron3'] OR $oldart['bron4'])?"<br>Действует на:":"")."
	".(($oldart['minu'])?"• Минимальное наносимое повреждение: ".($oldart[type]==3?"":"+")."{$oldart['minu']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$oldart[artminu]})</font></b>":"")."<BR>":"")."
	".(($oldart['maxu'])?"• Максимальное наносимое повреждение: ".($oldart[type]==3?"":"+")."{$oldart['maxu']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$oldart[artmaxu]})</font></b>":"")."<BR>":"");
	echo ((($oldart['gsila']) or ($artun==1) )?"<BR>• Сила: ".(($oldart['gsila']>0 )?"+":"")."{$oldart['gsila']}":"")."
	".(( ($oldart['stbonus']>0) and ( $oldart['gsila']!=0) and ($artun==0) )?"<a><img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":"")."
	".((($oldart['gsila']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_sila id=amc_sila value=1 onclick='Calcst(this.checked);' />  <small>будет доступно увеличение</small>":"")."":"")."
	".((($oldart['glovk']) or ($artun==1))?"<BR>• Ловкость: ".(($oldart['glovk']>0)?"+":"")."{$oldart['glovk']}":"")."
	".(( ($oldart['stbonus']>0) and ( $oldart['glovk']!=0 ) and ($artun==0))?"<a><img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":"")."
	".((($oldart['glovk']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_lovk id=amc_lovk value=1 onclick='Calcst(this.checked);'/>  <small>будет доступно увеличение</small>":"")."":"")."
	".((($oldart['ginta']) or ($artun==1))?"<BR>• Интуиция: ".(($oldart['ginta']>0)?"+":"")."{$oldart['ginta']}":"")."
	".(( ($oldart['stbonus']>0) and ($oldart['ginta']!=0) and ($artun==0))?"<a><img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":"")."
	".((($oldart['ginta']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_inta id=amc_inta value=1 onclick='Calcst(this.checked);'/>  <small>будет доступно увеличение</small>":"")."":"")."
	".((($oldart['gintel']) or ($artun==1))?"<BR>• Интеллект: ".(($oldart['gintel']>0)?"+":"")."{$oldart['gintel']}":"")."
	".(( ($oldart['stbonus']>0) and ($oldart['gintel']!=0) and ($artun==0))?"<a><img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":"")."
	".((($oldart['gintel']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_intel id=amc_intel value=1 onclick='Calcst(this.checked);'/>  <small>будет доступно увеличение</small>":"")."":"")."
	".((($oldart['gmp']) or ($artun==1))?"<BR>• Мудрость: ".(($oldart['gmp']>0)?"+":"")."{$oldart['gmp']}":"")."
	".(( ($oldart['stbonus']>0) and ($oldart['gmp']!=0) and ($artun==0))?"<a><img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":"")."
	".((($oldart['gmp']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_gmp id=amc_gmp value=1 onclick='Calcst(this.checked);'/>  <small>будет доступно увеличение</small>":"")."":"");
	echo ((($oldart['ghp'])OR($artun==1))?"<BR>• Уровень жизни: +{$oldart['ghp']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$oldart[artghp]})</font></b> ".(($oldart[artbron1]>0)?"<a style=\"padding:0;\"><img src='https://i.oldbk.com/i/up.gif'   alt='Увеличить' title='Увеличить'></a>":"")."  ".(($oldart[artghp] > 0)?"<a  style=\"padding:0;\" ><img src='https://i.oldbk.com/i/down.gif' alt='Уменьшить' title='Уменьшить'></a>":"")." (5 HP = 1 брони)  ":"")."":"");	
	echo ((($oldart['mfkrit']) or ($artun==1))?"<BR>• Мф. критических ударов: ".(($oldart['mfkrit']>0)?"+":"")."{$oldart['mfkrit']}%":"")."
	".(( ($oldart['mfbonus']>0) and ( $oldart['mfkrit']!=0 ) and ($artun==0))?"<a><img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":"")."
	".((($oldart['mfkrit']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_mkrit id=amc_mkrit value=1 onclick='Calcmf(this.checked);' />  <small>будет доступно увеличение</small>":"")."":"")."
	".((($oldart['mfakrit']) or ($artun==1))?"<BR>• Мф. против крит. ударов: ".(($oldart['mfakrit']>0)?"+":"")."{$oldart['mfakrit']}%":"")."
	".(( ($oldart['mfbonus']>0) and ( $oldart['mfakrit']!=0 ) and ($artun==0))?"<a><img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":"")."
	".((($oldart['mfakrit']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_makrit id=amc_makrit value=1 onclick='Calcmf(this.checked);' />  <small>будет доступно увеличение</small>":"")."":"")."
	".((($oldart['mfuvorot']) or ($artun==1))?"<BR>• Мф. увертливости: ".(($oldart['mfuvorot']>0)?"+":"")."{$oldart['mfuvorot']}%":"")."
	".(( ($oldart['mfbonus']>0) and ( $oldart['mfuvorot']!=0 ) and ($artun==0) )?"<a><img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":"")."
	".((($oldart['mfuvorot']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_muvorot id=amc_muvorot value=1 onclick='Calcmf(this.checked);' />  <small>будет доступно увеличение</small>":"")."":"")."
	".((($oldart['mfauvorot']) or ($artun==1))?"<BR>• Мф. против увертлив.: ".(($oldart['mfauvorot']>0)?"+":"")."{$oldart['mfauvorot']}%":"")."
	".(( ($oldart['mfbonus']>0) and ( $oldart['mfauvorot']!=0 ) and ($artun==0) )?"<a><img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":"")."
	".((($oldart['mfauvorot']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_mauvorot id=amc_mauvorot value=1 onclick='Calcmf(this.checked);' />  <small>будет доступно увеличение</small>":"")."":"");
	echo "<BR>";
	echo (($oldart['gnoj'])?"• Мастерство владения ножами и кастетами: +{$oldart['gnoj']}<BR>":"")."
	".(($oldart['gtopor'])?"• Мастерство владения топорами и секирами: +{$oldart['gtopor']}<BR>":"")."
	".(($oldart['gdubina'])?"• Мастерство владения дубинами и булавами: +{$oldart['gdubina']}<BR>":"")."
	".(($oldart['gmech'])?"• Мастерство владения мечами: +{$oldart['gmech']}<BR>":"")."
	".(($oldart['gfire'])?"• Мастерство владения стихией Огня: +{$oldart['gfire']}<BR>":"")."
	".(($oldart['gwater'])?"• Мастерство владения стихией Воды: +{$oldart['gwater']}<BR>":"")."
	".(($oldart['gair'])?"• Мастерство владения стихией Воздуха: +{$oldart['gair']}<BR>":"")."
	".(($oldart['gearth'])?"• Мастерство владения стихией Земли: +{$oldart['gearth']}<BR>":"")."
	".(($oldart['glight'])?"• Мастерство владения магией Света: +{$oldart['glight']}<BR>":"")."
	".(($oldart['ggray'])?"• Мастерство владения серой магией: +{$oldart['ggray']}<BR>":"")."
	".(($oldart['gdark'])?"• Мастерство владения магией Тьмы: +{$oldart['gdark']}<BR>":"")."
	".(($oldart['bron1'])?"• Броня головы: {$oldart['bron1']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$oldart[artbron1]})</font></b>":"")." <BR>":"")."
	".(($oldart['bron2'])?"• Броня корпуса: {$oldart['bron2']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$oldart[artbron2]})</font></b>":"")."<BR>":"")."
	".(($oldart['bron3'])?"• Броня пояса: {$oldart['bron3']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$oldart[artbron3]})</font></b>":"")."<BR>":"")."
	".(($oldart['bron4'])?"• Броня ног: {$oldart['bron4']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$oldart[artbron4]})</font></b>":"")."<BR>":"")."
	".(($oldart['gmeshok'])?"• Увеличивает рюкзак: +{$oldart['gmeshok']}<BR>":"")."
	".(($oldart['present'])?"<br>Подарок от: <b>".$oldart['present']."</b><br>":"")."
	".(($oldart['letter'])?"Количество символов: ".strlen($oldart['letter'])."<br>":"")."
	".(($oldart['letter'])?"На бумаге записан текст:<div style='background-color:FAF0E6;'> ".$oldart['letter']."</div>":"")."
	".(($oldart['prokat_idp']>0)?"Осталось:".floor(($oldart['prokat_do']-time())/60/60)." ч. ".round((($oldart['prokat_do']-time())/60)-(floor(($oldart['prokat_do']-time())/3600)*60))." мин.<br>":"")."
	".(($magic['name'] && $oldart['type'] != 50)?"<font color=maroon>Наложены заклятия:</font> ".$magic['name']."<BR>":"")."
	".(($magic['name'] && $oldart['type'] == 50)?"<font color=maroon>Свойства:</font> ".$magic['name']."<BR>":"")."
	".(($oldart['text'])?"На ручке выгравирована надпись:<center>".$oldart['text']."</center><BR>":"")."
	".(($oldart['present_text'])?"На подарке написан текст:<br />".$oldart['present_text']."<BR>":"")."
	".(($incmagic['max'])?"	Встроено заклятие <img src=\"https://i.oldbk.com/i/magic/".$incmagic['img']."\" title=\"".$incmagic['name']."\"> ".$incmagic['cur']." шт.	<BR> "."Требуемый уровень: ".$incmagic['nlevel']." <BR> ":"")."
	".(($incmagic['max'])? "К-во перезарядок: ".$incmagic['uses']."<br/>" : "")."
	".(($oldart['labonly']==1)?"<small><font color=maroon>Предмет пропадет после выхода из Лабиринта</font></small><BR>":"")."
	".((!$oldart['isrep'])?"<small><font color=maroon>Предмет не подлежит ремонту</font></small><BR>":"");

	if ($oldart[type]==27) {
		echo "<br>Особенности:<br>• может одеваться на броню<br>";
	} elseif ($oldart[type]==28) {
		echo "<br>Особенности:<br>• может одеваться под броню<br>";
	}

	echo '</td><td valign=center width="10%"><img src="https://i.oldbk.com/i/greenarrow.png"></td>';
	echo '<td valign=top width="45%">';
if ($newart['id']>0)
{
echo "<div align=center><b>Вы получаете:</b></div>";
	echo "<div id=\"old_img\">";
	echo "<img src=https://i.oldbk.com/i/sh/$newart[img]><br><br>";
	echo "</div>";


	if ($newart['nalign']==1) {
		$print_align='1.5';
	} else {
		$print_align=$newart['nalign'];
	}

	$ehtml=str_replace('.gif','',$newart['img']);
	$razdel=array(1=>"kasteti", 11=>"axe", 12=>"dubini", 13=>"swords", 14=>"bow", 2=>"boots", 21=>"naruchi", 22=>"robi", 23=>"armors",
	24=>"helmet", 3=>"shields",4=>"clips", 41=>"amulets", 42=>"rings", 5=>"mag1", 51=>"mag2", 6=>"amun");

	$newart['otdel']==''?$xx=$newart['razdel']:$xx=$newart['otdel'];
	if($razdel[$xx]=='') {
		$razdel[$xx]='predmeti';
	} else {
		$razdel[$xx]=$razdel[$xx]."/".$ehtml;
	}

	/*
	if ($newart['gsila'] > 0 || $newart['glovk'] || $newart['ginta'] || $newart['gintel'] || $newart['gmudra']) $newart['stbonus'] += 3;
	if ($newart['ghp'] > 0) $newart['ghp'] += 20;

	if ($newart['bron1'] > 0) $newart['bron1'] += 3;
	if ($newart['bron2'] > 0) $newart['bron2'] += 3;
	if ($newart['bron3'] > 0) $newart['bron3'] += 3;
	if ($newart['bron4'] > 0) $newart['bron4'] += 3;
	$newart['name'].= ' (мф)';
	*/
	
					
					
						//перенос гребаного АПА
						if ($oldart['nlevel']>$newart['nlevel'])
						{
						$newart=MK_UP_ART($newart,$oldart['nlevel']);
						}
					
					
					//перенос точки если оба оужие
					if (($oldart['type']==3) AND ($newart['type']==3) )
					{
						//если заточка
						if (strpos($oldart['name'], '+') == true)
						{
						$tempa=explode("+",$oldart['name']);
						$sharp=(int)($tempa[1]);
												
							
							if ($sharp>0)
							{
					 		 $newart['minu']+=$sharp;
					 		 $newart['maxu']+=$sharp;
					 		 $newart['cost']+=30;
					 		 $newart['name']=$newart['name']."+".$sharp;
					 		 $newart['sharped']=1;
					 		 
					 		 $newart['otdel']=$newart['razdel'];//т.к. новый из магаза
					 		 
								 if ($newart['otdel']==1) 
								 		{ 
								 		$newart['nnoj']+=$sharp;
								 		$newart['ninta']+=$sharp;
								 		}
								 else
								 if ($newart['otdel']==11) 
								 		{
					 					$newart['ntopor']+=$sharp;
					 					$newart['nsila']+=$sharp;
								 		}
								 else
								 if ($newart['otdel']==12) 
								 		{
										$newart['ndubina']+=$sharp;
										$newart['nlovk']+=$sharp;
										}
								else
					 			 if ($newart['otdel']==13) 
					 			 		{
									 	$newart['nmech']+=$sharp;
									 	$newart['nvinos']+=$sharp;
									 	}
							}
						} 
					
					}
					elseif (($oldart['type']==3) AND (strpos($oldart['name'], '+') == true) )
					{
					//если старый предмет пуха и точена а новый броня
						$tempa=explode("+",$oldart['name']);
						$add_sharp_scroll=(int)($tempa[1]);
						
						$otdel = $oldart['otdel'];
							
							$z = array(
								1 => array(
									1 => 163,
									2 => 164,
									3 => 165,		
									4 => 166,
									5 => 167,
								),
								11 => array(
									1 => 157157,
									2 => 156156,
									3 => 155,		
									4 => 154,
									5 => 85,
								),
								12 => array(
									1 => 158,
									2 => 159,
									3 => 160,		
									4 => 161,
									5 => 162,
								),
								13 => array(
									1 => 150,
									2 => 151,
									3 => 152,		
									4 => 153,
									5 => 84,
								),
							);
							$zz = array(6 => 9090, 7 => 190190, 8=>190191 , 9=>190192);

							if ($add_sharp_scroll <= 5) {
								$dress = mysql_query('SELECT * FROM oldbk.shop WHERE id = '.$z[$oldart['otdel']][$add_sharp_scroll]) or die("Errr0001");								
							} else {
								$dress = mysql_query('SELECT * FROM oldbk.eshop WHERE id = '.$zz[$add_sharp_scroll]) or die("Errr00011");
							}
							$sharp_scroll = mysql_fetch_assoc($dress);
						
						
						
					}
	

	

	echo "<a href=https://oldbk.com/encicl/".$razdel[$xx].".html target=_blank><b>{$newart['name']}</b></a>";
	echo "<img src=https://i.oldbk.com/i/align_{$print_align}.gif> (Масса: {$newart['massa']})".(($newart['present'])?' <IMG SRC="https://i.oldbk.com/i/podarok.gif" WIDTH="16" HEIGHT="18" BORDER=0 TITLE="Этот предмет вам подарил '.$newart['present'].'. Вы не сможете передать этот предмет кому-либо еще." ALT="Этот предмет вам подарил '.$newart['present'].'. Вы не сможете передать этот предмет кому-либо еще.">':"")."<BR>";
	echo $sowner;
	echo "<BR>Долговечность : {$newart['duration']}/{$newart['maxdur']}<BR>";
	echo (($newart['ups']>0)?"Подогнано:<b>{$newart['ups']} раз</b><BR>":"");
	echo (($newart['stbonus']>0)?"Возможных увеличений: ".($artun==1?"<font color=red>":"")."<b>{$newart['stbonus']}</b>".($artun==1?" </font><b class=text2><font color=red>(в свободном распределении после приобретения артефакта)</font></b>":"")."<BR>":"");
	echo (($newart['mfbonus']>0)?"Возможных увеличений мф: ".($artun==1?"<font color=red>":"")."<b>{$newart['mfbonus']}</b>".($artun==1?" </font><b class=text2><font color=red>(в свободном распределении после приобретения артефакта)</font></b>":"")."</b><BR>":"");
	echo (($magic['chanse'])?"Вероятность срабатывания: ".$magic['chanse']."%<BR>":"")."
	".(($magic['time'])?"Продолжительность действия магии: ".$magic['time']." мин.<BR>":"")."
	".(($newart['nsex']==1)?"• Пол: <b>Женский</b><br>":"")."
	".(($newart['nsex']==2)?"• Пол: <b>Мужской</b><br>":"");
	
			
	echo (($newart['nsila'] OR $newart['nlovk'] OR $newart['ninta'] OR $newart['nvinos'] OR $newart['nlevel'] OR $newart['nintel'] OR $newart['nmudra'] OR $newart['nnoj'] OR $newart['ntopor'] OR $newart['ndubina'] OR $newart['nmech'] OR $newart['nfire'] OR $newart['nwater'] OR $newart['nair'] OR $newart['nearth'] OR $newart['nearth'] OR $newart['nlight'] OR $newart['ngray'] OR $newart['ndark'] OR ($newart['nclass'] >0) )?"<br>Требуется минимальное:<BR>":"");
	
		if ($newart['nclass'] >0) 
			{
				if ($nclass_name[$newart['nclass']]!='')
				{	
				echo "• Класс персонажа: <b>{$nclass_name[$newart['nclass']]}</b><br>";
				}
			}
	
	echo (($newart['nsila']>0)?"• Сила: {$newart['nsila']}</font><BR>":"")."
	".(($newart['nlovk']>0)?"• Ловкость: {$newart['nlovk']}</font><BR>":"")."
	".(($newart['ninta']>0)?"• Интуиция: {$newart['ninta']}</font><BR>":"")."
	".(($newart['nvinos']>0)?"• Выносливость: {$newart['nvinos']}</font><BR>":"")."
	".(($newart['nlevel']>0)?"• Уровень: {$newart['nlevel']}</font><BR>":"")."
	".(($newart['nintel']>0)?"• Интеллект: {$newart['nintel']}</font><BR>":"")."
	".(($newart['nmudra']>0)?"• Мудрость: {$newart['nmudra']}</font><BR>":"")."
	".(($newart['nnoj']>0)?"• Мастерство владения ножами и кастетами: {$newart['nnoj']}</font><BR>":"")."
	".(($newart['ntopor']>0)?"• Мастерство владения топорами и секирами: {$newart['ntopor']}</font><BR>":"")."
	".(($newart['ndubina']>0)?"• Мастерство владения дубинами и булавами: {$newart['ndubina']}</font><BR>":"")."
	".(($newart['nmech']>0)?"• Мастерство владения мечами: {$newart['nmech']}</font><BR>":"")."
	".(($newart['nfire']>0)?"• Мастерство владения стихией Огня: {$newart['nfire']}</font><BR>":"")."
	".(($newart['nwater']>0)?"• Мастерство владения стихией Воды: {$newart['nwater']}</font><BR>":"")."
	".(($newart['nair']>0)?"• Мастерство владения стихией Воздуха: {$newart['nair']}</font><BR>":"")."
	".(($newart['nearth']>0)?"• Мастерство владения стихией Земли: {$newart['nearth']}</font><BR>":"")."
	".(($newart['nlight']>0)?"• Мастерство владения магией Света: {$newart['nlight']}</font><BR>":"")."
	".(($newart['ngray']>0)?"• Мастерство владения серой магией: {$newart['ngray']}</font><BR>":"")."
	".(($newart['ndark']>0)?"• Мастерство владения магией Тьмы: {$newart['ndark']}</font><BR>":"")."
	".(($newart['gmeshok'] OR $newart['gsila'] OR $newart['mfkrit'] OR $newart['mfakrit']  OR $newart['mfuvorot'] OR $newart['mfauvorot']  OR $newart['glovk'] OR $newart['ghp'] OR $newart['ginta'] OR $newart['gintel'] OR $newart['gnoj'] OR $newart['gtopor'] OR $newart['gdubina'] OR $newart['gmech'] OR $newart['gfire'] OR $newart['gwater'] OR $newart['gair'] OR $newart['gearth'] OR $newart['gearth'] OR $newart['glight'] OR $newart['ggray'] OR $newart['gdark'] OR $newart['minu'] OR $newart['maxu'] OR $newart['bron1'] OR $newart['bron2'] OR $newart['bron3'] OR $newart['bron4'])?"<br>Действует на:":"")."
	".(($newart['minu'])?"<BR>• Минимальное наносимое повреждение: ".($newart[type]==3?"":"+")."{$newart['minu']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$newart[artminu]})</font></b>":"")."<BR>":"")."
	".(($newart['maxu'])?"• Максимальное наносимое повреждение: ".($newart[type]==3?"":"+")."{$newart['maxu']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$newart[artmaxu]})</font></b>":"")."<BR>":"");
	echo ((($newart['gsila']) or ($artun==1) )?"<BR>• Сила: ".(($newart['gsila']>0 )?"+":"")."{$newart['gsila']}":"")."
	".(( ($newart['stbonus']>0) and ( $newart['gsila']!=0) and ($artun==0) )?"<img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'>":"")."
	".((($newart['gsila']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_sila id=amc_sila value=1 onclick='Calcst(this.checked);' />  <small>будет доступно увеличение</small>":"")."":"")."
	".((($newart['glovk']) or ($artun==1))?"<BR>• Ловкость: ".(($newart['glovk']>0)?"+":"")."{$newart['glovk']}":"")."
	".(( ($newart['stbonus']>0) and ( $newart['glovk']!=0 ) and ($artun==0))?"<img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'>":"")."
	".((($newart['glovk']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_lovk id=amc_lovk value=1 onclick='Calcst(this.checked);'/>  <small>будет доступно увеличение</small>":"")."":"")."
	".((($newart['ginta']) or ($artun==1))?"<BR>• Интуиция: ".(($newart['ginta']>0)?"+":"")."{$newart['ginta']}":"")."
	".(( ($newart['stbonus']>0) and ($newart['ginta']!=0) and ($artun==0))?"<img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'>":"")."
	".((($newart['ginta']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_inta id=amc_inta value=1 onclick='Calcst(this.checked);'/>  <small>будет доступно увеличение</small>":"")."":"")."
	".((($newart['gintel']) or ($artun==1))?"<BR>• Интеллект: ".(($newart['gintel']>0)?"+":"")."{$newart['gintel']}":"")."
	".(( ($newart['stbonus']>0) and ($newart['gintel']!=0) and ($artun==0))?"<img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'>":"")."
	".((($newart['gintel']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_intel id=amc_intel value=1 onclick='Calcst(this.checked);'/>  <small>будет доступно увеличение</small>":"")."":"")."
	".((($newart['gmp']) or ($artun==1))?"<BR>• Мудрость: ".(($newart['gmp']>0)?"+":"")."{$newart['gmp']}":"")."
	".(( ($newart['stbonus']>0) and ($newart['gmp']!=0) and ($artun==0))?"<a href='?act={$action}&edit=1&gmp=1&setup={$newart['id']}'><img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'></a>":"")."
	".((($newart['gmp']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_gmp id=amc_gmp value=1 onclick='Calcst(this.checked);'/>  <small>будет доступно увеличение</small>":"")."":"");
	echo ((($newart['ghp'])OR($artun==1))?"<BR>• Уровень жизни: +{$newart['ghp']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$newart[artghp]})</font></b> ".(($newart[artbron1]>0)?"<a style=\"padding:0;\" href='?act={$action}&edit=1&arthp=1&setup={$newart['id']}'><img src='https://i.oldbk.com/i/up.gif'   alt='Увеличить' title='Увеличить'></a>":"")."  ".(($newart[artghp] > 0)?"<a  style=\"padding:0;\" href='?act={$action}&edit=1&arthpd=1&setup={$newart['id']}'><img src='https://i.oldbk.com/i/down.gif' alt='Уменьшить' title='Уменьшить'></a>":"")." (5 HP = 1 брони)  ":"")."":"");	
	echo ((($newart['mfkrit']) or ($artun==1))?"<BR>• Мф. критических ударов: ".(($newart['mfkrit']>0)?"+":"")."{$newart['mfkrit']}%":"")."
	".(( ($newart['mfbonus']>0) and ( $newart['mfkrit']!=0 ) and ($artun==0))?"<img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'>":"")."
	".((($newart['mfkrit']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_mkrit id=amc_mkrit value=1 onclick='Calcmf(this.checked);' />  <small>будет доступно увеличение</small>":"")."":"")."
	".((($newart['mfakrit']) or ($artun==1))?"<BR>• Мф. против крит. ударов: ".(($newart['mfakrit']>0)?"+":"")."{$newart['mfakrit']}%":"")."
	".(( ($newart['mfbonus']>0) and ( $newart['mfakrit']!=0 ) and ($artun==0))?"<img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'>":"")."
	".((($newart['mfakrit']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_makrit id=amc_makrit value=1 onclick='Calcmf(this.checked);' />  <small>будет доступно увеличение</small>":"")."":"")."
	".((($newart['mfuvorot']) or ($artun==1))?"<BR>• Мф. увертливости: ".(($newart['mfuvorot']>0)?"+":"")."{$newart['mfuvorot']}%":"")."
	".(( ($newart['mfbonus']>0) and ( $newart['mfuvorot']!=0 ) and ($artun==0) )?"<img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'>":"")."
	".((($newart['mfuvorot']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_muvorot id=amc_muvorot value=1 onclick='Calcmf(this.checked);' />  <small>будет доступно увеличение</small>":"")."":"")."
	".((($newart['mfauvorot']) or ($artun==1))?"<BR>• Мф. против увертлив.: ".(($newart['mfauvorot']>0)?"+":"")."{$newart['mfauvorot']}%":"")."
	".(( ($newart['mfbonus']>0) and ( $newart['mfauvorot']!=0 ) and ($artun==0) )?"<img src='https://i.oldbk.com/i/up.gif' alt='Увеличить' title='Увеличить'>":"")."
	".((($newart['mfauvorot']) or ($artun==1))?"".(($artun==1)?"&nbsp;<input type=checkbox name=amc_mauvorot id=amc_mauvorot value=1 onclick='Calcmf(this.checked);' />  <small>будет доступно увеличение</small>":"")."":"");
	echo "<BR>";
	echo (($newart['gnoj'])?"• Мастерство владения ножами и кастетами: +{$newart['gnoj']}<BR>":"")."
	".(($newart['gtopor'])?"• Мастерство владения топорами и секирами: +{$newart['gtopor']}<BR>":"")."
	".(($newart['gdubina'])?"• Мастерство владения дубинами и булавами: +{$newart['gdubina']}<BR>":"")."
	".(($newart['gmech'])?"• Мастерство владения мечами: +{$newart['gmech']}<BR>":"")."
	".(($newart['gfire'])?"• Мастерство владения стихией Огня: +{$newart['gfire']}<BR>":"")."
	".(($newart['gwater'])?"• Мастерство владения стихией Воды: +{$newart['gwater']}<BR>":"")."
	".(($newart['gair'])?"• Мастерство владения стихией Воздуха: +{$newart['gair']}<BR>":"")."
	".(($newart['gearth'])?"• Мастерство владения стихией Земли: +{$newart['gearth']}<BR>":"")."
	".(($newart['glight'])?"• Мастерство владения магией Света: +{$newart['glight']}<BR>":"")."
	".(($newart['ggray'])?"• Мастерство владения серой магией: +{$newart['ggray']}<BR>":"")."
	".(($newart['gdark'])?"• Мастерство владения магией Тьмы: +{$newart['gdark']}<BR>":"")."
	".(($newart['bron1'])?"• Броня головы: {$newart['bron1']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$newart[artbron1]})</font></b>":"")." <BR>":"")."
	".(($newart['bron2'])?"• Броня корпуса: {$newart['bron2']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$newart[artbron2]})</font></b>":"")."<BR>":"")."
	".(($newart['bron3'])?"• Броня пояса: {$newart['bron3']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$newart[artbron3]})</font></b>":"")."<BR>":"")."
	".(($newart['bron4'])?"• Броня ног: {$newart['bron4']} ".($artun==1?"<b class=text3><font color=red> &nbsp;&nbsp;Добавлено артовых (+{$newart[artbron4]})</font></b>":"")."<BR>":"")."
	".(($newart['gmeshok'])?"• Увеличивает рюкзак: +{$newart['gmeshok']}<BR>":"")."
	".(($newart['letter'])?"Количество символов: ".strlen($newart['letter'])."</div>":"")."
	".(($newart['present'])?"<br>Подарок от: <b>".$newart['present']."</b><br>":"")."
	".(($newart['letter'])?"На бумаге записан текст:<div style='background-color:FAF0E6;'> ".$newart['letter']."</div>":"")."
	".(($newart['prokat_idp']>0)?"Осталось:".floor(($newart['prokat_do']-time())/60/60)." ч. ".round((($newart['prokat_do']-time())/60)-(floor(($newart['prokat_do']-time())/3600)*60))." мин.<br>":"")."
	".(($magic['name'] && $newart['type'] != 50)?"<font color=maroon>Наложены заклятия:</font> ".$magic['name']."<BR>":"")."
	".(($magic['name'] && $newart['type'] == 50)?"<font color=maroon>Свойства:</font> ".$magic['name']."<BR>":"")."
	".(($newart['text'])?"На ручке выгравирована надпись:<center>".$newart['text']."</center><BR>":"")."
	".(($newart['present_text'])?"На подарке написан текст:<br />".$newart['present_text']."<BR>":"")."
	".(($incmagic['max'])?"	Встроено заклятие <img src=\"https://i.oldbk.com/i/magic/".$incmagic['img']."\" title=\"".$incmagic['name']."\"> ".$incmagic['cur']." шт.	<BR> "."Требуемый уровень: ".$incmagic['nlevel']." <BR> ":"")."
	".(($incmagic['max'])? "К-во перезарядок: ".$incmagic['uses']."<br/>" : "")."
	".(($newart['labonly']==1)?"<small><font color=maroon>Предмет пропадет после выхода из Лабиринта</font></small><BR>":"")."
	".((!$newart['isrep'])?"<small><font color=maroon>Предмет не подлежит ремонту</font></small><BR>":"");

	if ($newart[type]==27) {
		echo "<br>Особенности:<br>• может одеваться на броню<br>";
	} elseif ($newart[type]==28) {
		echo "<br>Особенности:<br>• может одеваться под броню<br>";
	}
	
	
	if ($sharp_scroll)
	{
	echo "<br>";
	echo "<b>И получите так же:</b> ";
	echo "<img src='https://i.oldbk.com/i/sh/{$sharp_scroll['img']}' alt='{$sharp_scroll['name']}' title='{$sharp_scroll['name']}'>";
	echo "<br><small>Свиток заточки выдается подарком, после использования предмет будет привязан к персонажу.</small>";
	}
	
	
}
else	
	{
	echo "&nbsp;";
	}
	echo "</td></tr></table><br><br><br><br>";

		
		
	//расчет стоимости
	if ($oldart['type']==$newart['type'])	
		{
		$need_cost=20; //40
		}
		else
		{
		$need_cost=40; //80
		}
		

	$qexists = mysql_query('SELECT * FROM inventory WHERE owner = '.$us['id'].' AND setsale=0 and (prototype = 541) ORDER BY ecost DESC');	

	if ((($oldart['type']==$newart['type']) AND ($oldart['type']==3)) || mysql_num_rows($qexists))		
	{
	
	echo '

	<style>
	#snaptarget { height: 140px; background-color:#f2efe8;width:700px;border: 4px; border-style:double; border-color: #908869;}
	</style>
	<center>
	
	<div id="all2vconstr">
		<div id="snaptarget">
		Переместите сюда «Храмовый ваучер в ком.отдел» или «Сертификат на бесплатный обмен артефакта в КО»
	</div><br>';

	$ids = array();

	if ((($oldart['type']==$newart['type']) AND ($oldart['type']==3))) {
		$addsql = "prototype=100000 or ";
	} else {
	}
		
	$q = mysql_query('SELECT * FROM inventory WHERE owner = '.$us['id'].' AND setsale=0 and ('.$addsql.' prototype = 541) ORDER BY ecost DESC');	
if (mysql_num_rows($q) > 0) {
		while($i = mysql_fetch_assoc($q)) {
			$ids[] = $i['id'];
			echo '<span name="prot'.$i['prototype'].'" style="display:inline-block;" id="drag'.$i['id'].'"> <img alt="'.$i['name'].'" title="'.$i['name'].'" src="https://i.oldbk.com/i/sh/'.$i['img'].'"> </span> ';
		}
		echo '
		<script type="text/javascript">
		var spans = [];
		function CheckF() 
		{
			if (confirm("Вы уверены ? ")) 
			{
				document.getElementById("mkec").submit();
			}
		}
	
		$("#all2vconstr span" ).draggable({ containment: "#all2vconstr", scroll: false});
		
		$("#all2vconstr span" ).bind( "dragstop", function(event, ui) 
		{
			spans.splice(0,spans.length) ;
			$("#all2vconstr span").each(function(index,item){
				m = $("#all2vconstr").offset().top ;
	
				if ( ($(item).offset().top-m) < ($("#snaptarget").height()-$(item).height())) 
				{ 
					spans.push(item);
				}
			});
	

	
			var ids = new Array();
	
			for (key in spans) {
				var n = spans[key];
				itemid = n.id;
				itemid = itemid.substring(4);
				if (isNaN(parseInt(itemid))) continue;
				itemproto = $("#"+n.id).attr("name");
				itemproto = itemproto.substr(4);
				ids.push(itemid);
			
				
				
				
			}                     
			document.getElementById("mkdartids").value = ids.join();

		});
		</script>';
		
	} else {
		echo 'У вас нет необходимых предметов. Оплата только через банковский счет!';
	}	
	
	echo '</div></center></div>';
	}
	else
		{
		echo '
		<script>
		var spans = [];
		function CheckF() {
			

			if (confirm("Вы уверены?")) {
				document.getElementById("mkec").submit();
			}
			
		}
		</script>';
		
		
		}

	echo '<br><br><center>';
	$get_bank=mysql_query("select * from oldbk.bank where owner='{$us['id']}' ");

	$BANKS = "";
	
	if (mysql_num_rows($get_bank) > 0) {
		$BANKS="<select style='width:150px' name=bankid>";
		while($row = mysql_fetch_assoc($get_bank)) {
			$BANKS.="<option>".$row['id']."</option>";
		}
		$BANKS.="</select>";
	}
										
	echo '<form id="mkec" method="POST">
		<input type="hidden" id="new_art_proto" name="new_art_proto" value="'.$newart['id'].'">
		<input type="hidden" id="my_oldart" name="my_oldart" value="'.$oldart['id'].'">	
		<input type="hidden" id="mkdartids" name="mkdartids" value=""><table>
	';

	if (strlen($BANKS)) echo '<tr><td><input type="radio" name="paytype" value="0"></td><td>Банковский счет: '.$BANKS.' пароль <input type=password size=8 name=bankpass></td></tr>';
	echo '<tr><td><input type="radio" name="paytype" value="1"></td><td>Золотыми монетами, у вас в наличии <b>'.$us['gold'].'</b> монет</td></tr></table><br>';
	echo '<input type="button" value="ОПЛАТИТЬ" class="button2" name="ОПЛАТИТЬ" OnClick="CheckF();"></form><br>';				
	echo '</center>';
}

if ($_SESSION['uid']) {
	
	if ($unikid[0] > 0) {
		$id = intval($unikid[0]);
		$shop = intval($unikid[1]);
		
		if (!($id>0)) die();
	
		$get_proto = mysql_fetch_array(mysql_query("select * from oldbk.eshop where id in (2000,2001,2002,260,262,284,283,18210,18229,18247,18527) AND id = ".$id));
	
		if  ($get_proto[id]>0) {
			ShowARTForm($_SESSION['artinfo_old'],$get_proto); 
		} else {
			ShowARTForm($_SESSION['artinfo_old'],null); 
			//echo '<font color=red>Ошибка выбора прототипа, данный прототип недоступен!</font>';	
		}
	}
} else {
	die("<script>location.href='index.php?exit=314';</script>");
}

?>