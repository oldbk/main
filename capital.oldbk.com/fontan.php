<?php
session_start();
if (!($_SESSION['uid'] >0)) header("Location: index.php");
include "connect.php";
//$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
require "config_ko.php";
include "functions.php";
$smiles = Array("/:flowers:/","/:inv:/","/:hug:/","/:horse:/","/:str:/","/:susel:/","/:smile:/","/:laugh:/","/:fingal:/","/:eek:/","/:smoke:/","/:hi:/","/:bye:/","/:king:/","/:king2:/","/:boks2:/","/:boks:/","/:gent:/","/:lady:/","/:tongue:/","/:smil:/","/:rotate:/","/:ponder:/","/:bow:/","/:angel:/","/:angel2:/","/:hello:/","/:dont:/","/:idea:/", "/:mol:/", "/:super:/","/:beer:/","/:drink:/","/:baby:/","/:tongue2:/", "/:sword:/", "/:agree:/","/:loveya:/","/:kiss:/","/:kiss2:/", "/:kiss3:/", "/:kiss4:/","/:rose:/","/:love:/","/:love2:/", "/:confused:/", "/:yes:/","/:no:/","/:shuffle:/","/:nono:/","/:maniac:/","/:privet:/","/:ok:/","/:ninja:/","/:pif:/", "/:smash:/","/:alien:/","/:pirate:/","/:gun:/","/:trup:/","/:mdr:/", "/:sneeze:/","/:mad:/","/:friday:/","/:cry:/","/:grust:/","/:rupor:/","/:fie:/", "/:nnn:/","/:row:/","/:red:/","/:lick:/","/:help:/","/:wink:/","/:jeer:/","/:tease:/","/:kruger:/","/:girl:/","/:Knight1:/","/:rev:/","/:smile100:/","/:smile118:/","/:smile149:/","/:smile166:/","/:smile237:/","/:smile245:/","/:smile28:/","/:smile289:/","/:smile314:/","/:smile36:/","/:smile39:/","/:smile44:/","/:smile70:/","/:smile87:/","/:smile434:/","/:vamp:/","/:ball_girl:/","/:warning2:/","/:futbol:/","/:s180:/","/:s210:/","/:ball:/","/:radio001:/","/:radio002:/","/:radio003:/","/:wall:/","/:smile26:/","/:showng:/","/:snegur:/","/:dedmoroz:/","/:superng:/","/:snowfight:/","/:doctor:/","/:nye:/");


if(!$_SESSION[fontan_refresh])
{
	$_SESSION[fontan_refresh]=time();
}

if($_SESSION[fontan_refresh]>time())
{
	err('Вы забьете водосток!<br>');
	unset($_GET);
	unset($_POST);
}

$smiles2 = Array("<img style=\"cursor:pointer;\" onclick=S(\"flowers\") src=http://i.oldbk.com/i/smiles/flowers.gif>","<img style=\"cursor:pointer;\" onclick=S(\"inv\") src=http://i.oldbk.com/i/smiles/inv.gif>","<img style=\"cursor:pointer;\" onclick=S(\"hug\") src=http://i.oldbk.com/i/smiles/hug.gif>","<img style=\"cursor:pointer;\" onclick=S(\"horse\") src=http://i.oldbk.com/i/smiles/horse.gif>","<img style=\"cursor:pointer;\" onclick=S(\"str\") src=http://i.oldbk.com/i/smiles/str.gif>","<img style=\"cursor:pointer;\" onclick=S(\"susel\") src=http://i.oldbk.com/i/smiles/susel.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile\") src=http://i.oldbk.com/i/smiles/smile.gif>","<img style=\"cursor:pointer;\" onclick=S(\"laugh\") src=http://i.oldbk.com/i/smiles/laugh.gif>","<img style=\"cursor:pointer;\" onclick=S(\"fingal\") src=http://i.oldbk.com/i/smiles/fingal.gif>","<img style=\"cursor:pointer;\" onclick=S(\"eek\") src=http://i.oldbk.com/i/smiles/eek.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smoke\") src=http://i.oldbk.com/i/smiles/smoke.gif>","<img style=\"cursor:pointer;\" onclick=S(\"hi\") src=http://i.oldbk.com/i/smiles/hi.gif>","<img style=\"cursor:pointer;\" onclick=S(\"bye\") src=http://i.oldbk.com/i/smiles/bye.gif>","<img style=\"cursor:pointer;\" onclick=S(\"king\") src=http://i.oldbk.com/i/smiles/king.gif>","<img style=\"cursor:pointer;\" onclick=S(\"king2\") src=http://i.oldbk.com/i/smiles/king2.gif>","<img style=\"cursor:pointer;\" onclick=S(\"boks2\") src=http://i.oldbk.com/i/smiles/boks2.gif>","<img style=\"cursor:pointer;\" onclick=S(\"boks\") src=http://i.oldbk.com/i/smiles/boks.gif>","<img style=\"cursor:pointer;\" onclick=S(\"gent\") src=http://i.oldbk.com/i/smiles/gent.gif>","<img style=\"cursor:pointer;\" onclick=S(\"lady\") src=http://i.oldbk.com/i/smiles/lady.gif>","<img style=\"cursor:pointer;\" onclick=S(\"tongue\") src=http://i.oldbk.com/i/smiles/tongue.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smil\") src=http://i.oldbk.com/i/smiles/smil.gif>","<img style=\"cursor:pointer;\" onclick=S(\"rotate\") src=http://i.oldbk.com/i/smiles/rotate.gif>","<img style=\"cursor:pointer;\" onclick=S(\"ponder\") src=http://i.oldbk.com/i/smiles/ponder.gif>","<img style=\"cursor:pointer;\" onclick=S(\"bow\") src=http://i.oldbk.com/i/smiles/bow.gif>","<img style=\"cursor:pointer;\" onclick=S(\"angel\") src=http://i.oldbk.com/i/smiles/angel.gif>","<img style=\"cursor:pointer;\" onclick=S(\"angel2\") src=http://i.oldbk.com/i/smiles/angel2.gif>","<img style=\"cursor:pointer;\" onclick=S(\"hello\") src=http://i.oldbk.com/i/smiles/hello.gif>","<img style=\"cursor:pointer;\" onclick=S(\"dont\") src=http://i.oldbk.com/i/smiles/dont.gif>","<img style=\"cursor:pointer;\" onclick=S(\"idea\") src=http://i.oldbk.com/i/smiles/idea.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"mol\") src=http://i.oldbk.com/i/smiles/mol.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"super\") src=http://i.oldbk.com/i/smiles/super.gif>","<img style=\"cursor:pointer;\" onclick=S(\"beer\") src=http://i.oldbk.com/i/smiles/beer.gif>","<img style=\"cursor:pointer;\" onclick=S(\"drink\") src=http://i.oldbk.com/i/smiles/drink.gif>","<img style=\"cursor:pointer;\" onclick=S(\"baby\") src=http://i.oldbk.com/i/smiles/baby.gif>","<img style=\"cursor:pointer;\" onclick=S(\"tongue2\") src=http://i.oldbk.com/i/smiles/tongue2.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"sword\") src=http://i.oldbk.com/i/smiles/sword.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"agree\") src=http://i.oldbk.com/i/smiles/agree.gif>","<img style=\"cursor:pointer;\" onclick=S(\"loveya\") src=http://i.oldbk.com/i/smiles/loveya.gif>","<img style=\"cursor:pointer;\" onclick=S(\"kiss\") src=http://i.oldbk.com/i/smiles/kiss.gif>","<img style=\"cursor:pointer;\" onclick=S(\"kiss2\") src=http://i.oldbk.com/i/smiles/kiss2.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"kiss3\") src=http://i.oldbk.com/i/smiles/kiss3.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"kiss4\") src=http://i.oldbk.com/i/smiles/kiss4.gif>","<img style=\"cursor:pointer;\" onclick=S(\"rose\") src=http://i.oldbk.com/i/smiles/rose.gif>","<img style=\"cursor:pointer;\" onclick=S(\"love\") src=http://i.oldbk.com/i/smiles/love.gif>","<img style=\"cursor:pointer;\" onclick=S(\"love2\") src=http://i.oldbk.com/i/smiles/love2.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"confused\") src=http://i.oldbk.com/i/smiles/confused.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"yes\") src=http://i.oldbk.com/i/smiles/yes.gif>","<img style=\"cursor:pointer;\" onclick=S(\"no\") src=http://i.oldbk.com/i/smiles/no.gif>","<img style=\"cursor:pointer;\" onclick=S(\"shuffle\") src=http://i.oldbk.com/i/smiles/shuffle.gif>","<img style=\"cursor:pointer;\" onclick=S(\"nono\") src=http://i.oldbk.com/i/smiles/nono.gif>","<img style=\"cursor:pointer;\" onclick=S(\"maniac\") src=http://i.oldbk.com/i/smiles/maniac.gif>","<img style=\"cursor:pointer;\" onclick=S(\"privet\") src=http://i.oldbk.com/i/smiles/privet.gif>","<img style=\"cursor:pointer;\" onclick=S(\"ok\") src=http://i.oldbk.com/i/smiles/ok.gif>","<img style=\"cursor:pointer;\" onclick=S(\"ninja\") src=http://i.oldbk.com/i/smiles/ninja.gif>","<img style=\"cursor:pointer;\" onclick=S(\"pif\") src=http://i.oldbk.com/i/smiles/pif.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"smash\") src=http://i.oldbk.com/i/smiles/smash.gif>","<img style=\"cursor:pointer;\" onclick=S(\"alien\") src=http://i.oldbk.com/i/smiles/alien.gif>","<img style=\"cursor:pointer;\" onclick=S(\"pirate\") src=http://i.oldbk.com/i/smiles/pirate.gif>","<img style=\"cursor:pointer;\" onclick=S(\"gun\") src=http://i.oldbk.com/i/smiles/gun.gif>","<img style=\"cursor:pointer;\" onclick=S(\"trup\") src=http://i.oldbk.com/i/smiles/trup.gif>","<img style=\"cursor:pointer;\" onclick=S(\"mdr\") src=http://i.oldbk.com/i/smiles/mdr.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"sneeze\") src=http://i.oldbk.com/i/smiles/sneeze.gif>","<img style=\"cursor:pointer;\" onclick=S(\"mad\") src=http://i.oldbk.com/i/smiles/mad.gif>","<img style=\"cursor:pointer;\" onclick=S(\"friday\") src=http://i.oldbk.com/i/smiles/friday.gif>","<img style=\"cursor:pointer;\" onclick=S(\"cry\") src=http://i.oldbk.com/i/smiles/cry.gif>","<img style=\"cursor:pointer;\" onclick=S(\"grust\") src=http://i.oldbk.com/i/smiles/grust.gif>","<img style=\"cursor:pointer;\" onclick=S(\"rupor\") src=http://i.oldbk.com/i/smiles/rupor.gif>","<img style=\"cursor:pointer;\" onclick=S(\"fie\") src=http://i.oldbk.com/i/smiles/fie.gif>", "<img style=\"cursor:pointer;\" onclick=S(\"nnn\") src=http://i.oldbk.com/i/smiles/nnn.gif>","<img style=\"cursor:pointer;\" onclick=S(\"row\") src=http://i.oldbk.com/i/smiles/row.gif>","<img style=\"cursor:pointer;\" onclick=S(\"red\") src=http://i.oldbk.com/i/smiles/red.gif>","<img style=\"cursor:pointer;\" onclick=S(\"lick\") src=http://i.oldbk.com/i/smiles/lick.gif>","<img style=\"cursor:pointer;\" onclick=S(\"help\") src=http://i.oldbk.com/i/smiles/help.gif>","<img style=\"cursor:pointer;\" onclick=S(\"wink\") src=http://i.oldbk.com/i/smiles/wink.gif>","<img style=\"cursor:pointer;\" onclick=S(\"jeer\") src=http://i.oldbk.com/i/smiles/jeer.gif>","<img style=\"cursor:pointer;\" onclick=S(\"tease\") src=http://i.oldbk.com/i/smiles/tease.gif>","<img style=\"cursor:pointer;\" onclick=S(\"kruger\") src=http://i.oldbk.com/i/smiles/kruger.gif>","<img style=\"cursor:pointer;\" onclick=S(\"girl\") src=http://i.oldbk.com/i/smiles/girl.gif>","<img style=\"cursor:pointer;\" onclick=S(\"Knight1\") src=http://i.oldbk.com/i/smiles/Knight1.gif>","<img style=\"cursor:pointer;\" onclick=S(\"rev\") src=http://i.oldbk.com/i/smiles/rev.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile100\") src=http://i.oldbk.com/i/smiles/smile100.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile118\") src=http://i.oldbk.com/i/smiles/smile118.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile149\") src=http://i.oldbk.com/i/smiles/smile149.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile166\") src=http://i.oldbk.com/i/smiles/smile166.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile237\") src=http://i.oldbk.com/i/smiles/smile237.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile245\") src=http://i.oldbk.com/i/smiles/smile245.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile28\") src=http://i.oldbk.com/i/smiles/smile28.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile289\") src=http://i.oldbk.com/i/smiles/smile289.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile314\") src=http://i.oldbk.com/i/smiles/smile314.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile36\") src=http://i.oldbk.com/i/smiles/smile36.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile39\") src=http://i.oldbk.com/i/smiles/smile39.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile44\") src=http://i.oldbk.com/i/smiles/smile44.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile70\") src=http://i.oldbk.com/i/smiles/smile70.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile87\") src=http://i.oldbk.com/i/smiles/smile87.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile434\") src=http://i.oldbk.com/i/smiles/smile434.gif>","<img style=\"cursor:pointer;\" onclick=S(\"vamp\") src=http://i.oldbk.com/i/smiles/vamp.gif>","<img style=\"cursor:pointer;\" onclick=S(\"ball_girl\") src=http://i.oldbk.com/i/smiles/ball_girl.gif>","<img style=\"cursor:pointer;\" onclick=S(\"warning2\") src=http://i.oldbk.com/i/smiles/warning2.gif>","<img style=\"cursor:pointer;\" onclick=S(\"futbol\") src=http://i.oldbk.com/i/smiles/futbol.gif>","<img style=\"cursor:pointer;\" onclick=S(\"s180\") src=http://i.oldbk.com/i/smiles/s180.gif>","<img style=\"cursor:pointer;\" onclick=S(\"s210\") src=http://i.oldbk.com/i/smiles/s210.gif>","<img style=\"cursor:pointer;\" onclick=S(\"ball\") src=http://i.oldbk.com/i/smiles/ball.gif>","<img style=\"cursor:pointer;\" onclick=S(\"radio001\") src=http://i.oldbk.com/i/smiles/radio001.gif>","<img style=\"cursor:pointer;\" onclick=S(\"radio002\") src=http://i.oldbk.com/i/smiles/radio002.gif>","<img style=\"cursor:pointer;\" onclick=S(\"radio003\") src=http://i.oldbk.com/i/smiles/radio003.gif>","<img style=\"cursor:pointer;\" onclick=S(\"wall\") src=http://i.oldbk.com/i/smiles/wall.gif>","<img style=\"cursor:pointer;\" onclick=S(\"smile26\") src=http://i.oldbk.com/i/smiles/smile26.gif>","<img style=\"cursor:pointer;\" onclick=S(\"showng\") src=http://i.oldbk.com/i/smiles/showng.gif>","<img style=\"cursor:pointer;\" onclick=S(\"snegur\") src=http://i.oldbk.com/i/smiles/snegur.gif>","<img style=\"cursor:pointer;\" onclick=S(\"dedmoroz\") src=http://i.oldbk.com/i/smiles/dedmoroz.gif>","<img style=\"cursor:pointer;\" onclick=S(\"superng\") src=http://i.oldbk.com/i/smiles/superng.gif>","<img style=\"cursor:pointer;\" onclick=S(\"snowfight\") src=http://i.oldbk.com/i/smiles/snowfight.gif>","<img style=\"cursor:pointer;\" onclick=S(\"doctor\") src=http://i.oldbk.com/i/smiles/doctor.gif>","<img style=\"cursor:pointer;\" onclick=S(\"nye\") src=http://i.oldbk.com/i/smiles/nye.gif>");

$begin_day = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
$end_day = mktime(23, 59, 59, date("m")  , date("d"), date("Y"));

if ($user['room'] != 66) { header("Location: main.php");  die(); }
else
	if (($user['battle'] != 0) OR ($user['battle_fin'] != 0)) { header('location: fbattle.php'); die(); }





//   if(time()>$end){header("Location: index.php");}

function buildsetPNG($id,$img,$top,$left,$des) {
	$imga = ImageCreateFromPNG("i/city/sub/".$img.".png");
	#Get image width / height
	$x = ImageSX($imga);
	$y = ImageSY($imga);
	unset($imga);

	if (strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 6.0"))
	{
		echo "<div style=\"position:absolute; cursor: pointer; left:{$left}px; top:{$top}px; width:{$x}; height:${y}; z-index:90; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=100, Style=0);\"
	 ><img src=\"http://i.oldbk.com/i/city/sub/{$img}.png\" width=\"${x}\" height=\"${y}\" alt=\"{$des}\" title=\"{$des}\" class=\"aFilter\" onmouseover=\"this.src='http://i.oldbk.com/i/city/sub/{$img}2.png'\" onmouseout=\"this.src='http://i.oldbk.com/i/city/sub/{$img}.png'\"
	 id=\"{$id}\" onclick=\"solo({$id})\" /></div>";
	}
	else
	{
		echo "<div style=\"position:absolute; cursor: pointer; left:{$left}px; top:{$top}px; width:{$x}; height:${y}; z-index:90; \"
	 ><img src=\"http://i.oldbk.com/i/city/sub/{$img}.png\" width=\"${x}\" height=\"${y}\" alt=\"{$des}\" title=\"{$des}\" class=\"aFilter2\" onmouseover=\"this.src='http://i.oldbk.com/i/city/sub/{$img}2.png'\" onmouseout=\"this.src='http://i.oldbk.com/i/city/sub/{$img}.png'\"
	 id=\"{$id}\" onclick=\"solo({$id})\" /></div>";
	}



}
//check_rights

$access=check_rights($user);
//print_r($access);
//if(!$access[i_angel]==1||!$user[i_pal]>1){die();}

if((int)$_GET[del_post]>0 && $access[can_forum_del]==1)
{
	mysql_query('UPDATE `fontan` SET
    	del_id='.$user[id].',del_align="'.$user[align].'",del_login="'.$user[login].'",del_level='.$user[level].',del_klan="'.$user[klan].'"
    	WHERE id='.$_GET[del_post].';');

}
if((int)$_GET[restore_post]>0 && $access[can_forum_restore]==1)
{
	mysql_query('UPDATE `fontan` SET
    	del_id=0,del_align=0,del_login=0,del_level=0,del_klan=0
    	WHERE id='.$_GET[restore_post].';');
}

//      echo mysql_error();

$mess='';
$f_silent=0;
$ef=mysql_fetch_array(mysql_query('SELECT max(id) as id FROM `effects` WHERE owner = '.$user[id].' AND type = 3 AND time >='.time().' LIMIT 1;'));
if($ef[id]>0)
{
	$f_silent=1;
}


$link='fontan';
if($_POST[add]&&$_POST[message])
{
	$text=$_POST[message];
	$at=explode(' ',$text);
	for($i=0;$i<count($at);$i++)
	{
		if(strlen($at[$i])>43)
		{
			$stop=1;
		}
	}

	if($f_silent==0)
	{
		if($stop==1)
		{
			$mess='Не надо постить слишком длинные слова.. Это не прилично.';
		}
		else
		{
			mysql_query('insert into `fontan` (owner, date, text,login, align,klan,level)
		    	VALUES
		    	('.$user[id].','.time().',"'.mysql_escape_string(strip_tags($_POST[message])).'","'.$user[login].'" ,'.$user[align].',"'.$user[klan].'", '.$user[level].' )');
		}
	}
	else
	{
		$mess = 'На вас наложено заклятия форумного молчания.';
	}
}

$DailyFree = \components\models\dailyFree\DailyFreeFontan::firstOrNew(['user_id' => $user['id']]);
if((int)$_GET[get_gift]>0)
{
	$ok=0;
	$stol=0;
	//Пьем воду раз в сутки
	if($_GET[get_gift]==2)
	{
		$stol=22;
	}
	else
		if($_GET[get_gift]==1)
		{
			$stol=23;
		}

	// проверяем сколкьо чего забрали.
	//22-питье
	//23-vjytns(50 в день)
	$gift_count=mysql_fetch_array(mysql_query("SELECT * FROM `stol` where `stol`=".$stol." and owner=".$user[id].";"));

	//if($fontan_hill_travma > 0) $fontan_hill_travma = 9999999;

	if($_GET[get_gift]==2)
	{    //пьем из фонтана. всего один раз..

		if(!$gift_count || $gift_count['count']<$fontan_hill_travma) // было 1
		{

			if($fontan_hill_travma>0)
			{
				$travma = mysql_query("SELECT * FROM `effects` WHERE `owner` = '".$user['id']."' AND (`type`='11' OR `type`='12' OR `type`='13');");
				while ($owntravma=mysql_fetch_array($travma))
				{
					deltravma($owntravma['id']);
					$ee=' и исцелились от всех травм';
				}
			}





			$ok=1;

			if ($gift_count['count'] < 5) {
				$txt='Вы выпили воду из фонтана и пополнили свою жизнь'.$ee.'.';
			} else {
				$txt='Вы выпили воду из фонтана'.$ee.'.';
			}

			if(time()>mktime(0,0,0,12,14,2018) && time()<mktime(23,59,59,15,11,2018)) {
				$fontan_lab_time = true;
			}

			if ($fontan_lab_time==true)
			{
				//mysql_query("UPDATE `labirint_var` SET `val`=0 WHERE `owner`={$user[id]} AND `var`='labstarttime';");
				//$txt.=' и обнулили время похода в лабиринт!';

				$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='4001' ;"));
				$dress['ecost'] = 0;
				$dress['cost'] = 1;
				$dress['prototype'] = 4001;
				$dress['present'] ='Удача';

				$goden_do = time()+(3*24*3600);
				$goden = 3;

				mysql_query("INSERT INTO oldbk.`inventory`
							(`prototype`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,
							`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
							`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
							`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`, `includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`, `present`, `add_time`,`sowner`,`letter`
							)
							VALUES
							('{$dress['prototype']}','{$user['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
							'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}',
							'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$goden_do."',
							'{$goden}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','{$tonick['id_city']}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}','{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}', '{$dress['present']}',".time().",'{$dress['sowner']}', '{$dress['letter']}'
							) ;");

				if (mysql_affected_rows()>0)
				{
					$dress[id]=mysql_insert_id();
					//new_delo
					$rec=array();
					$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0;
					$rec['target_login']='Фонтан';
					$rec['type']=455;//получил предмет от диллера
					$rec['sum_kr']=1;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($dress);
					$rec['item_name']=$dress[name];
					$rec['item_count']=1;
					$rec['item_type']=$dress['type'];
					$rec['item_cost']=$dress['cost'];
					$rec['item_dur']=$dress['duration'];
					$rec['item_maxdur']=$dress['maxdur'];
					$rec['item_ups']=$dress['ups'];
					$rec['item_unic']=$dress['unic'];
					$rec['item_incmagic']=$dress['includemagicname'];
					$rec['item_incmagic_count']=$dress['includemagicuses'];
					$rec['item_arsenal']='';
					$rec['bank_id']='';
					$rec['item_proto']=$dress['prototype'];
					$rec['item_sowner']=($dress['sowner']>0?1:0);
					$rec['item_incmagic_id']=$dress['includemagic'];
					add_to_new_delo($rec); //юзеру

					telepost_new($user,'<font color=red>Внимание!</font> Вам передан предмет <b>'.$dress[name].'</b>');
					$txt.='<br>Вы получили <b>'.$dress[name].'</b> !';
				}

			}


			if ($gift_count['count'] < 5) {
				mysql_query("update `users` set hp=maxhp where id=".$user[id].";");
			}
			mysql_query("INSERT `stol` (`owner`,`stol`,`count`)
					values
					('".$user[id]."', '".$stol."', '1' )
					ON DUPLICATE KEY UPDATE `count` =`count`+1;");
		}
		else
		{
			$mess='Вы уже пили из фонтана сегодня...';
		}

	}
    elseif($_GET[get_gift]==1)
	{
		$_SESSION[fontan_refresh]=time()+1;
		try {
			if($user['level'] < 4) {
				throw new Exception('Уровень маловат...');
			}
			if($user['money'] < 1) {
				throw new Exception('У вас недостаточно денег...');
			}

			if($DailyFree->getAvailable() == 0 && \components\Component\Config::admins($user['id']) !== true) {
				throw new Exception('Дождитесь восстановления...');
			}

			$DailyFree->uses -= 1;
			$DailyFree->used_total += 1;
			$DailyFree->save();

			mysql_query("INSERT INTO `fontan_st` set owner=".$user[id].";");

			$txt='Вы бросили 1кр. в фонтан, но вам не повезло. ';

			mysql_query("update `users` set money=money-1 where id=".$user[id].";");
			$sql='select * from `oldbk`.new_delo where type=103 AND owner='.$user[id].' AND sdate>='.$begin_day.' AND sdate<='.$end_day.' order by id desc LIMIT 1';
			if($user[id]==28453)
			{
				echo $sql;
			}
			$data=mysql_query($sql);

			if(mysql_affected_rows()>0)
			{
				while($row=mysql_fetch_array($data))
				{
					$rec['id']=$row['id'];
					$rec['owner']=$user['id'];
					$rec['owner_balans_do']=$user['money'];
					$user['money'] -= 1;
					$rec['owner_balans_posle']=$user['money'];
					$rec['sum_kr']=$row['sum_kr']+1;
					$rec['type']=$row['type'];
					update_new_delo($rec);
				}
			}
			else
			{
				$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user[money];
				$user['money'] -= 1;
				$rec['owner_balans_posle']=$user[money];
				$rec['target']=0;
				$rec['target_login']='Фонтан Удачи';
				$rec['type']=103;//монетка в фонтан
				$rec['sum_kr']=1;
				add_to_new_delo($rec);
			}

			/*
			mysql_query("INSERT INTO oldbk.`delo`
				   (`id` , `author` ,`pers`, `text`, `type`, `date`)
				VALUES ('','0','{$_SESSION['uid']}',
				'Бросил 1 кр в фонтан удачи. Баланс ( до ".$user[money]." /после ".($user[money]-1)." )',1,'".time()."');");
			*/

			$user[money]=$user[money]-1;

			$counts=mysql_fetch_array(mysql_query("select count(id) from `fontan_st`;"));
			$j=$counts[0];

			$prc=mt_rand(1,100);
			if($j>=15 && $j<=16)
			{
				$shans=15;
				$give=mt_rand(3,15);
			}
            elseif($j>=17 && $j<=18)
			{
				$shans=25;
				$give=mt_rand(5,18);
			}
            elseif($j>=19 && $j<=20)
			{
				$shans=35;
				$give=mt_rand(7,20);
			}
            elseif($j>=21 && $j<=24)
			{
				$shans=75;
				$give=mt_rand(9,45);
			}
            elseif($j>=25)
			{
				$shans=100;
				$give=mt_rand(14,60);
			}

			$_fontan_is_win = false;
			if($prc<$shans)
			{
				$_fontan_is_win = true;
				mysql_query("delete from `fontan_st`;");
				mysql_query("update `users` set money=money+".$give." where id=".$user[id]." LIMIT 1;");

				$rec['owner']=$user[id];
				$rec['owner_login']=$user[login];
				$rec['owner_balans_do']=$user[money];
				$user['money'] += $give;
				$rec['owner_balans_posle']=$user[money];
				$rec['target']=0;
				$rec['target_login']='Фонтан Удачи';
				$rec['type']=104;//выйгрыщ из фонтана
				$rec['sum_kr']=$give;
				add_to_new_delo($rec);


				/*
				$data=mysql_query('select * from delo where pers='.$user[id].'
						 AND date>='.$begin_day.' AND date<='.$end_day.' AND text
						 like "Получен выйгыш на фонтане удачи%" order by id desc LIMIT 1');
				 if(mysql_affected_rows()>0)
				 {
					 while($row=mysql_fetch_array($data))
					 {
						 $dtxt=explode(' ',$row[text]);
						 $dtxt[5]+=$give; //count
						 $dtxt[10]=$user[money];
						 $dtxt[12]=$user[money]+$give;
						$user[money]-=1;
						 $dtxt=implode(' ',$dtxt);
						 mysql_query('update oldbk.`delo` set text="'.$dtxt.'" where pers='.$user[id].' AND id='.$row[id].';');

					 }
				 }
				 else
				 {
					mysql_query("INSERT INTO oldbk.`delo`
						(`id` , `author` ,`pers`, `text`, `type`, `date`)
						VALUES ('','0','{$_SESSION['uid']}',
						'Получен выйгыш на фонтане удачи ".$give." кр. Баланс( до ".$user[money]."/после ".($user[money]+$give)." )',1,'".time()."');");

				 }
				*/


				$user[money]=$user[money]+$give;
				mysql_query("INSERT INTO `fontan_log` set get_money=".$counts[0].", give_money=".$give.";");
				//A-Tech,radminion,2.4,8,0
				$info=$user[login].','.$user[klan].','.$user[align].','.$user[level].','.$user[hidden];
				mysql_query("INSERT INTO `fontan_winers` set winner=".$user[id].", winner_count=".$give.", winner_info='".$info."';");
				$txt='Вы бросили монетку в фонтан и выиграли '.$give.' кр.';

				if(!$_SESSION['beginer_quest'][none])
				{
					// квест
					$last_q=check_last_quest(30);
					if($last_q)
					{
						quest_check_type_30($last_q,$user[id],8,1);
					}

				}

				unset($give);
				unset($shans);
				unset($prc);
				unset($j);

				if (mt_rand(0,100)<5)
				{
					DropBonusItem(112002,$user,"Удача","Коллекция №2: Ангельская поступь",0,1,20,true); //Карта Удачи выпадает при бросании монеток в Фонтан - шанс 1%
				}

			}

			$User = new \components\models\User($user);
			if($_fontan_is_win && mt_rand(0, 100) < 5) {
				$db = \components\Component\Db\CapitalDb::connection();
				try {
					$Winners = \components\models\FontanWiners::where('winner', '=', $user['id'])
						->where('created_at', '>', (new \DateTime())->setTime(0, 0))
						->where('created_at', '<', (new \DateTime())->setTime(23, 59, 59))
						->where('win_type', '=', \components\models\FontanWiners::TYPE_EKR)
						->first();
					if($Winners) {
						throw new Exception();
					}

					$Winners = new \components\models\FontanWiners();
					$Winners->winner = $user['id'];
					$Winners->winner_info = $user['login'] . ',' . $user['klan'] . ',' . $user['align'] . ',' . $user['level'] . ',' . $user['hidden'];
					$Winners->winner_count = 1;
					$Winners->win_type = \components\models\FontanWiners::TYPE_EKR;
					$Winners->created_at = time();
					if (!$Winners->save()) {
						throw new \Exception;
					}

					$GiveEkr = new \components\Helper\item\ItemEkr($User, 1);
					if (!$GiveEkr->give()) {
						throw new \Exception;
					}

					$_data = [
						'target_login' => 'Фонтан',
						'type' => 115,
						'sum_ekr' => 1,
					];

					if (!$GiveEkr->newDeloGive($_data)) {
						throw new \Exception;
					}

					$db->commit();

					$txt .= '<br>Вам улыбнулась Удача, вы получили 1 екр.';

				} catch (Exception $ex) {
					$db->rollback();
				}
			}

			try {
				$Quest = $app->quest
					->setUser($User)
					->get();
				$Checker = new \components\Component\Quests\check\CheckerEvent();
				$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_FONTAN;
				if(($Item = $Quest->isNeed($Checker)) !== false) {
					$Quest->taskUp($Item);
				}
				unset($Checker);

				if($_fontan_is_win) {
					$Checker = new \components\Component\Quests\check\CheckerEvent();
					$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_FONTAN_WIN;
					if(($Items = $Quest->isNeed($Checker, true)) !== false) {
						$Quest->taskUpMultiple($Items);
					}
					unset($Checker);
                }

			} catch (Exception $ex) {
				$app->logger->addEmergency((string)$ex);
			}

			try {
				$FontanRating = new \components\Helper\rating\FontanRating();
				$FontanRating->value_add = $_fontan_is_win ? 5 : 1;

				$app->applyHook('event.rating', $user, $FontanRating);
			} catch (Exception $ex) {
				$app->logger->addEmergency((string)$ex);
			}

		} catch (Exception $ex) {
			if($ex->getCode() == 0) {
				$txt = $ex->getMessage();
			}
		}
		$ok=1;

	}

	if($ok==1)
	{
		$mess=$txt;
	}
}


?>
<HTML><HEAD>
    <link rel=stylesheet type="text/css" href="i/main.css">
    <link rel="stylesheet" href="/i/btn.css" type="text/css">
    <meta content="text/html; charset=windows-1251" http-equiv=Content-type>
    <META Http-Equiv=Cache-Control Content=no-cache>
    <meta http-equiv=PRAGMA content=NO-CACHE>
    <META Http-Equiv=Expires Content=0>
    <script type="text/javascript" src="/i/globaljs.js"></script>
</HEAD>
<script>
    function solo(n)
    {
        // if vk<?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>
        //if (<?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>CtrlPress) {
        //	window.open('/ch.pl?online=1&n='+n,'onlines','width=400,height=500,toolbar=no,location=no,scrollbars=yes,resizable=yes');
        //} else {
		<?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>changeroom=n;
        window.location.href='fontan.php?get_gift='+n+'';
        //}
    }
</script>
<body leftmargin=5 topmargin=5 marginwidth=5 marginheight=5 bgcolor=#d7d7d7>
<div class="btn-control" id="buttons" style="float: right;">
    <a class="button-dark-mid btn" onclick="window.open('help/fontan.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes'); return false;" title="Подсказка">Подсказка</a>
    <a class="button-mid btn" href="?" title="Обновить">Обновить</a>
    <a class="button-mid btn" href="city.php" title="Вернуться">Вернуться</a>
</div>
<TABLE border=0 width=100% cellspacing="0" cellpadding="0" align="center">
    <tr><td align=center valign=top width=100%>

            <TABLE border=0  width=900 style="background-image: url(http://i.oldbk.com/i/fontan/fontanbg.jpg); background-repeat: no-repeat; width: 900px; height:1000px;background-position-y: 40px;">
                <tr><td valign=top  align=left>
                        <div style="position:relative;">
							<?
							buildsetPNG(1,"fallmoney",65,160,"Бросить монетку");
							buildsetPNG(2,"drnkwater",65,580,"Выпить воды");
							?>
                            <table border=0 width=900>
                                <tr>
                                    <td height=240 colspan=5 align=center valign=top>&nbsp;
										<?php
										$start_fontan = (new DateTime())->getTimestamp();
										$message_fontan = sprintf('У вас есть %s/%s попыток!', $DailyFree->uses, $DailyFree->limit_uses);
										if($DailyFree->uses == 0) {
											$message_fontan .= sprintf(' Следующая попытка восстановится через %s',
												\components\Helper\TimeHelper::prettyTime($start_fontan, $DailyFree->getNextAddedTimestamp()));
										}
										?>
                                        <div style="color: red">
											<?= $message_fontan ?>
                                        </div>
                                        <font color=red><?=$mess?></font>
                                    </td>
                                </tr>
                                <tr>
                                    <td width=80>&nbsp;</td>
                                    <td width=310 align=left valign=top>
										<?
										$sql = 'SELECT sum(winner_count) as winc FROM `fontan_winers`';
										$data=mysql_query_cache($sql,false,10*60);
										while(list($k,$ss) = each($data))
										{
											$bros=$ss[winc];
										}

										echo 'Бросая монетки в фонтан вы можете получить кредиты и, если повезет, 1 екр, а можете все проиграть.<br><br>';
										echo 'Всего выиграно: <b>'.$bros.'</b>кр.<br><br>
							           <b>20</b> последних выигрышей:<br>';

										$sql = 'SELECT * FROM `fontan_winers` order by id desc limit 20 ';
										//echo $sql;
										$data = mysql_query($sql);
										if(mysql_affected_rows()>0)
										{
											while($row=mysql_fetch_array($data))
											{
												$info=return_info($row['winner'],$row['winner_info'],0);
												echo $info . ' - '.$row['winner_count'].($row['win_type'] == \components\models\FontanWiners::TYPE_EKR?'екр.':'кр.').' <br>';
											}
										}

										?>

                                    </td>
                                    <td width=90>&nbsp;</td>
                                    <td width=350 align=left valign=top>

										<?
										//$pages=mysql_fetch_array(mysql_query('select count(id) as page from `fontan`;'));
										if($_GET['page']<0)
										{
											$_GET['page']=0;
										}

										$data = mysql_query("SELECT * FROM `fontan`
			 	ORDER by `id` DESC LIMIT ".((int)$_GET['page']*10).",10;");

										//$pgs = ceil($pages[page]/10);

										?><BR>
                                        <table style="
							white-space: pre-wrap;
							white-space: -moz-pre-wrap;
							white-space: -pre-wrap;
							white-space: -o-pre-wrap;
							word-wrap: break-word;">
											<?
											//function s_nick($id,$align,$klan,$login,$level)

											while($row = mysql_fetch_array($data)) {
												echo '<tr><td style="
							white-space: pre-wrap;
							white-space: -moz-pre-wrap;
							white-space: -pre-wrap;
							white-space: -o-pre-wrap;
							word-wrap: break-word;">';
												//echo '<div style="position:relative; width: 330px; text-align: left;">';
												$inf=s_nick($row[owner],$row[align],$row[klan],$row[login],$row[level]);
												$del='';
												$res='';
												if(($access[i_pal]>0 || $access[i_angel]>0)&& !$row[del_id])
												{
													$del= "<a OnClick=\"if (!confirm('Удалить пост?')) { return false; } \" href='".$link.".php?del_post=".$row[id]."'>&nbsp;<img src='i/clear.gif'></a>";
												}
                                                elseif($access[can_forum_restore]==1)
												{
													$res= "<a OnClick=\"if (!confirm('Восстановить пост?')) { return false; } \" href='".$link.".php?restore_post=".$row[id]."'>&nbsp;<img src=i/icon2.gif></a>";
												}

												echo $inf.':<br>';

												if(!$row[del_id])
												{
													$row['text'] = preg_replace($smiles, $smiles2, $row['text'],3);
													echo ''.$row['text'].' '.$del;
												}
												else
												{
													$inf=s_nick($row[del_id], $row[del_align], $row[del_klan], $row[del_login], $row[del_level]);
													if($access[can_forum_restore]==1)
													{
														$row['text'] = preg_replace($smiles, $smiles2, $row['text'],3);
														echo '<i><font color=grey>'.$row['text'].'</font></i> '.$res;
													}
													echo '&nbsp;<font color=red>Удалено '.($access[i_angel]>0?'ангелом ':'паладином ') .$inf.'</font>';
												}
												echo '<hr></td></tr>';
												//echo '</div>';
											}
											echo '</table><center>';

											//======================
											$sql_pgs="select count(id) as page from `fontan`;" ;
											$pgs =mysql_query_cache($sql_pgs,false,20);

											$pgs=$pgs[0];
											if ($pgs[page]>0)
											{
												$pgs = $pgs[page]/20;

												$pages_str='';
												$page = (int)$_GET['page']>0 ? (((int)$_GET['page']+1)>$pgs ? ($pgs-1):(int)$_GET['page']):0;
												$page=ceil($page);
												if ($pgs>1)
												{
													//$pages_str.=($page>4 ? "...":"");
													for ($i=0;$i<ceil($pgs);$i++)
														if (($i>($page-4))&&($i<=($page+3)))
															$pages_str.=($i==$page ? " <b>".($i+1)."</b>":" <a href='?page=".($i)."'>".($i+1)."</a>");
													$pages_str.=($page<$pgs-4 ? "...":"");
													$pages_str=($page>3 ? "<a href='?&page=".($page-1)."'> < </a>...":"").$pages_str.(($page<($pgs-1) ? "<a href='?&page=".($page+1)."' > ></a>":""));
												}
												$FirstPage=(ceil($pgs)>3 ? $_GET['page']>0 ? "<a href='?&page=0'> Перв. </a>":"":"");
												$LastPage=(ceil($pgs)>3 ? (ceil($pgs)-1)!=$_GET['page'] ? "<a href='?&page=".(ceil($pgs)-1)."'> Посл. </a>":"":"");
												$pages_str=$FirstPage.$pages_str.$LastPage;
												echo $pages_str;
											}
											echo '</center>';

											//==================

											?>
                                            <div class="btn-control">
                                                <form action='<?=$link?>.php' method='post'>
                                                    Оставить сообщение:<br>
                                                    <INPUT TYPE="text" name="message" SIZE="35" VALUE="" maxlength=150><br>
                                                    <input type="submit" class="button-mid btn" name="add" value="Добавить">
                                                </form>
                                            </div>
                                            <div id="hint3" class="ahint"></div>
                                            </td>
                                            <td width=70>&nbsp;</td>
                                            </tr>
                                        </table>
                        </div>
                    </td>
                </tr>
            </table>

        </td>
    </tr></table>
</BODY>
</HTML>
