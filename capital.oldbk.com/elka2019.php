<?php
	session_start();
	if (!($_SESSION['uid'] >0)) header("Location: index.php");
	include "connect.php";
	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
	include "functions.php";
	include "action_days_config.php";
	include "ny_events.php";

	if (!ADMIN) {
		if ($user['room'] != 20) { 
			header("Location: main.php");  die(); 
		}
			
		if(!(time() > $ny_events['elkacpstart'] && time() < $ny_events['elkacpend']) && !ADMIN) {
			header('location: city.php?tmp='.(mt_rand(111111,999999)));
			die();
		}
	}
	if (ADMIN && isset($_GET['showactive'])) {
		$ny_events['elkacpgiftstart'] = time()-14400;
		$ny_events['elkacpgiftend'] = time()+14400;

		$ny_events['elkacpeatstart'] = time()-14400;
		$ny_events['elkacpeatend'] = time()+14400;
	
		$ny_events['elkacpcarnavalstart'] = time()-14400;
		$ny_events['elkacpcarnavalend'] = time()+14400;
	}

	$mess = '';

	$q = mysql_query('SELECT * FROM ny_quest_var WHERE owner = '.$user['id'].' and var = "q1"');
	$questinfo = mysql_fetch_assoc($q);


	if (isset($_GET['takequest']) && $questinfo === false) {
		if (mysql_query('INSERT INTO ny_quest_var (owner,var,val) VALUES ('.$user['id'].',"q1",0)')) {
			$mess = 'Вы удачно взяли квест &quot;Ёлочное безумие&quot;';
		} else {
			$mess = 'Ошибка получения квеста';
		}
		$questinfo = true;
	}


	$lsml = array();
	$lsml2 = array();
	
	$klansm = "";
	if (strlen($user['klan'])) $klansm = ' or (owner = 0 and klan = "'.$user['klan'].'")';
	$q = mysql_query_cache('SELECT * FROM oldbk.smiles WHERE (klan = "" and (owner = 0 OR owner = '.$user['id'].')) '.$klansm.' ORDER BY id ASC',false,5*60);
			
	while(list($k,$ss) = each($q)) {
		$lsml[] = "/:".$ss['name'].":/";
		if ($ss['owner'] > 0 || $ss['klan'] != "") {
			$lsml2[] = "<img style=\"cursor:pointer;\" onclick=S(\"".$ss['name']."\") width=".$ss['w']." height=".$ss['h']." src=\"http://i.oldbk.com/i/smiles/".$ss['name'].".gif\">";
		} else {
			$lsml2[] = "<img style=\"cursor:pointer;\" width=".$ss['w']." height=".$ss['h']." onclick=S(\"".$ss['name']."\") src=\"http://i.oldbk.com/i/smiles/".$ss['name'].".gif\">";
		}
	}

	$access=check_rights($user);

	if((int)$_GET[del_post]>0 && $access[can_forum_del]==1) {
    		mysql_query('UPDATE oldbk.elka_2011 SET
			del_id='.$user[id].',del_align="'.$user[align].'",del_login="'.$user[login].'",del_level='.$user[level].',del_klan="'.$user[klan].'"
			WHERE id='.$_GET[del_post].';');
	}

	if((int)$_GET[restore_post]>0 && $access[can_forum_restore] == 1) {
    		mysql_query('UPDATE oldbk.elka_2011 SET
		    	del_id=0,del_align=0,del_login=0,del_level=0,del_klan=0
		    	WHERE id='.$_GET[restore_post].';');
	}

    	$f_silent=0;
	$ef=mysql_fetch_array(mysql_query('SELECT max(id) as id FROM effects WHERE owner = '.$user[id].' AND type = 3 AND time >='.time().' LIMIT 1;'));
	if($ef[id]>0) {
		$f_silent=1;
	}


    	$link = 'elka2017';

    	if(strlen($_POST['text']) && $user['level'] > 4) {
		$Quest = null;
		$Item = null;
		$need_for_quest = false;
		try {
			$User = new \components\models\User($user);
			$Quest = $app->quest
				->setUser($User)
				->get();
			$Checker = new \components\Component\Quests\check\CheckerEvent();
			$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_COMMENT_ELKA;

			if(($Item = $Quest->isNeed($Checker)) !== false) {
				$need_for_quest = true;
			}

		} catch (Exception $ex) {

		}

		$_POST['text'] = trim($_POST['text']);

	    	if(($f_silent == 0 || $need_for_quest) && strlen($_POST['text']) > 0) {
		    	if(strlen($_POST['text']) > 500) {
				$_POST['text']=substr($_POST['text'], 0, 500);
			}
					
			$comment = mysql_escape_string(preg_replace($lsml, $lsml2,$_POST[text] , 3));
	
		    	mysql_query('insert into oldbk.elka_2011 (owner, date, text,login, align,klan,level)
			    	VALUES
			    	('.$user[id].','.time().',"'.$comment.'","'.$user[login].'" ,'.$user[align].',"'.$user[klan].'", '.$user[level].' )');

			try {
				if($need_for_quest && $Quest && $Item) {
					$Quest->taskUp($Item);
				}
			} catch (Exception $ex) {

			}
		} elseif (strlen($_POST['text']) == 0) {
	        	$mess = 'Текст не должен быть пустым.';
	        } else {
	        	$mess = 'На вас наложено заклятия форумного молчания.';
	        }
    	}

	$_GET['get_gift']=(int)$_GET['get_gift'];

	$ok = 0;

	if((int)$_GET[get_gift] > 0) {

		// забираем одноразовый подарок
		// проверяем сколкьо чего забрали.

		$gift_count = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`stol` where `stol`='".$_GET[get_gift]."' and owner=".$user[id]." LIMIT 1;"));

		if((int)$_GET[get_gift] == 3) {
			if((time() >= $ny_events['elkacpcarnavalstart'] && time() <= $ny_events['elkacpcarnavalend'])) {		
				if($user[hidden]>0) {
					$mess='Вы и так в невидимости...';
				} else {
					$ng=1;
					ob_start();
					include "magic/hilluz.php";
					$mess = ob_get_clean();
					$mess = strip_tags($mess);
				}
			} else {
				if (time() < $ny_events['elkacpcarnavalstart']) {
					$mess='Еще рано...';
				}
				if (time() > $ny_events['elkacpcarnavalend']) {
					$mess='Уже поздно...';
				}
			}
		}

		if($_GET[get_gift]==4) {    
			if((time() >= $ny_events['elkacpgiftstart'] && time() <= $ny_events['elkacpgiftend'])) {
				if(!$gift_count) {
					$goden = $ny_events_cur_m == 12 ? mktime(23,59,59,1,30,$ny_events_cur_y+1) : mktime(23,59,59,1,30,$ny_events_cur_y);
					if (ADMIN) $goden = time()+3600;

					$sql="insert into oldbk.inventory set
						name = 'Новогодний подарок', duration =0, maxdur=1,cost=0, owner=".$user[id].", img = 'gift_ny2019.gif',
						isrep=0,
						type=200, massa=5, magic=1023, prototype=10000, otdel=71, ecost=0, 
						add_time='".time()."', dategoden='".$goden."', goden='30',
						letter = 'С Новым 2019 Годом!',present = 'Администрация'";
						mysql_query($sql);
						$dress[id]=mysql_insert_id();
						$dress[idcity]=$user[id_city];
						$dressid = get_item_fid($dress);
						
						$rec['owner']=$user[id];
						$rec['owner_login']=$user[login];
						$rec['owner_balans_do']=$user[money];
						$rec['owner_balans_posle']=$user[money];
						$rec['target']=0;
						$rec['target_login']='Елка.';
						$rec['type']=174;//забрали подарок
						$rec['sum_kr']=0;
						$rec['sum_ekr']=0;
						$rec['sum_kom']=0;
						$rec['item_id']=$dressid;
						$rec['item_name']='Новогодний подарок';
						$rec['item_count']=1;
						$rec['item_type']=200;
						$rec['item_cost']=0;
						$rec['item_dur']=0;
						$rec['item_maxdur']=1;
						$rec['item_ups']=0;
						$rec['item_unic']=0;
						$rec['item_incmagic']='';
						$rec['item_incmagic_count']='';
						$rec['add_info']=' с Елки';
						add_to_new_delo($rec);
						$ok=1;
						$txt='Вы забрали новогодний подарок';
				} else {
					$mess='Вы уже забрали новогодний подарок...';
				}
				
			} else {
				if (time() < $ny_events['elkacpgiftstart']) {
					$mess='Еще рано...';
				}
				if (time() > $ny_events['elkacpgiftend']) {
					$mess='Уже поздно...';
				}
			}
		}

		if($_GET[get_gift]==2) {
			if((time() >= $ny_events['elkacpeatstart'] && time() <= $ny_events['elkacpeatend'])) {
				if($gift_count['count'] < 5) {
					$ITname[1]='Шампанское';
					$ITfile[1]='stol_ny_002.gif';
					$ITname[2]='Соленая закуска';
					$ITfile[2]='stol_ny_001.gif';
					$ITname[3]='Рыбка';
					$ITfile[3]='stol_ny_004.gif';
					$ITname[4]='Оливье';
					$ITfile[4]='stol_ny_003.gif';
					$ITname[5]='Икра красная';
					$ITfile[5]='stol_ny_005.gif';
					
					$RND=rand(1,5);
						
					$sql="INSERT INTO oldbk.inventory set
					name = '".$ITname[$RND]."', duration =0, maxdur=5,cost=8, owner=".$user[id].",
					img = '".$ITfile[$RND]."', dategoden='".(time()+60*60*24*3)."',
					magic=8, type=50, massa=1, goden=3, prototype=105, isrep=0, otdel=6, ecost=0, present_text = 'Новогоднее угощение',present = 'Новогоднее угощение';";
						
					$ok=1;
						
					mysql_query($sql);
					$dress[id]=mysql_insert_id();
					$dress[idcity]=$user[id_city];
					$dressid = get_item_fid($dress);
						
					$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user[money];
					$rec['owner_balans_posle']=$user[money];
					$rec['target']=0;
					$rec['target_login']='Елка.';
					$rec['type']=173;//забрали с елки
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=$dressid;
					$rec['item_name']=$ITname[$RND];
					$rec['item_count']=1;
					$rec['item_type']=50;
					$rec['item_cost']=8;
					$rec['item_dur']=0;
					$rec['item_maxdur']=5;
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['add_info']=' с Елки';
					add_to_new_delo($rec);
						
					$txt='Вы забрали новогоднее угощение';
					
				} else {
					$mess='На сегодня лимит угощений исчерпан...';
				}
			} else {
				if (time() < $ny_events['elkacpeatstart']) {
					$mess='Еще рано...';
				}
				if (time() > $ny_events['elkacpeatend']) {
					$mess='Уже поздно...';
				}
			}
		}
	}
		
		
	if($ok == 1) {
		mysql_query("INSERT oldbk.`stol` (`owner`,`stol`,`count`)
		values
		('".$user[id]."', '".$_GET[get_gift]."', '1' )
		ON DUPLICATE KEY UPDATE `count` =`count`+1;");
		$mess=$txt;
	}
	
?>

<!DOCTYPE html>
<html>
<head>
    <title>Новогодняя елка 2019!</title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>
    <link rel=stylesheet type="text/css" href="i/main.css">
    <link rel="stylesheet" href="/i/btn.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="/i/<?php echo $link; ?>/style_elka2019.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script type="text/javascript" src="/i/globaljs.js"></script>
    <script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
    <script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>


    <script>
	function solo(n) {
		top.changeroom=n;
		window.location.href='elka2019.php?get_gift='+n+'';
	}
	function S(name){
		var sData = window.document.getElementById('textarea').value;
		sData=sData +' :'+name+': ';
		window.document.getElementById('textarea').value=sData;
	}
	
	function showhidden(id) {
		var st = document.getElementById(id).style.display;

		if (st == 'none') {
			document.getElementById(id).style.display = 'block';
		} else {
			document.getElementById(id).style.display = 'none';
		}
	}

	function defPosition2(event) {
		var isIE11 = navigator.userAgent.match(/Trident\/7.0.*rv.*11\.0/);
	      var x = y = 0;
	      if (document.attachEvent != null || isIE11) { // Internet Explorer & Opera
	            x = window.event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
	            y = window.event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
				if (window.event.clientY + 10 > document.body.clientHeight) { y-=6 } else { y-=2 }
	      } else if (!document.attachEvent && document.addEventListener) { // Gecko
	            x = event.clientX + window.scrollX;
	            y = event.clientY + window.scrollY;
				if (event.clientY + 10 > document.body.clientHeight) { y-=6 } else { y-=2 }
	      } else {
	            // Do nothing
	      }
	      return {x:x, y:y};
	}

	function smiles(e) {
		sm = document.getElementById("sm");
		if (sm.style.display == "") {
		        sm.style.display = "none";
		} else {		                             	
		        sm.style.top = defPosition2(e).y+30 + "px";
		        sm.style.left = defPosition2(e).x-400 + "px";
		        sm.style.display = "";
		}
	}

	</script>

</head>
<body>
<?

if (strlen($mess)) {
	echo '	
		<script>
			var n = noty({
				text: "'.addslashes($mess).'",
			        layout: "topLeft2",
			        theme: "relax2",
				type: "'.($typet == "e" ? "error" : "success").'",
			});
		</script>
	';
}

?>
<table width="100%" cellspacing="0" cellpadding="0">
<tbody><tr>
<td width="100%" align="right">
<div class="btn-control">
    <input class="button-mid btn" type="button" onclick="location.href='main.php';" value="Вернуться" title="Вернуться">
</div>
</td></tr></tbody></table>
<form method="POST" id="addtext" action='elka2019.php' style="padding:0px;margin:0px;">
<style>
.noty_message { padding: 5px !important;}
textarea:focus {
    outline: 0;
}
.formmess {
	left: 523px;
	top: -19px;
	position: relative;
	width:345px;
}
.formbutt {
	top: -8px;
	position: relative;
	width:152px;
	left: 726px;
}


.formsm {
	top: -49px;
	position: relative;
	width:38px;
	left: 680px;
}
.quest_daily {
	position: absolute;
	top: 376px;
	left: 209px;
}

</style>
<div class="main">

    <div class="content">
  	
        <a class="santa" title="Иллюзия" href="?get_gift=3"><img src="http://i.oldbk.com/i/<?php echo $link; ?>/<?=( (time()>$ny_events['elkacpcarnavalstart'] && time()<$ny_events['elkacpcarnavalend']))?'Mbutt_3':'Mbutt_3_passive'?>.png"/></a>
        <?
        $gift_count=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`stol` where `stol`=4 and owner=".$user[id]." LIMIT 1;"));
        ?>
        <a class="gift" title="Новогодний подарок" href="?get_gift=4"><img src="http://i.oldbk.com/i/<?php echo $link; ?>/<?=( /*(ADMIN && $gift_count['count']<1) ||*/ (time()>$ny_events['elkacpgiftstart'] && time()<$ny_events['elkacpgiftend']) && $gift_count['count']<1)?'Mbutt_2':'Mbutt_2_passive'?>.png"/></a>
        
        <?$gift_count=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`stol` where `stol`=2 and owner=".$user[id]." LIMIT 1;"));?>
        <a class="food" title="Новогоднее угощение" href="?get_gift=2"><img src="http://i.oldbk.com/i/<?php echo $link; ?>/<?=( /*(ADMIN && $gift_count['count']<5) ||*/ (time()>$ny_events['elkacpeatstart'] && time()<$ny_events['elkacpeatend']) && $gift_count['count']<5)?'Mbutt_1':'Mbutt_1_passive'?>.png"/></a>
	<!--
	<a class="quest_daily" style="cursor: default;" title="Зимний Дух" href="javascript:void();"><img src="http://i.oldbk.com/i/action/quest_off_6old.gif"/></a>
	-->
	<div class="formmess" id="formmess">

		<textarea <? if ($user['level'] < 5) echo 'disabled'; ?> id="textarea" name="text" style="padding:6px;width:335px;height:107px;background-color: transparent;color: #5485C9;font-size:10pt;font-family: Verdana;border: none;">
<? if ($user['level'] < 5) echo 'Разрешено только для персонажей 5го уровня и выше</textarea>'; else echo "		</textarea>"; ?>
	</div>

	<div class="formbutt" id="formbutt">
		<img style="cursor: pointer;" OnClick="document.getElementById('addtext').submit();" src="http://i.oldbk.com/i/<?php echo $link; ?>/send.png" OnMouseOut='this.src="http://i.oldbk.com/i/<?php echo $link; ?>/send.png";' OnMouseOver='this.src="http://i.oldbk.com/i/<?php echo $link; ?>/send_h.png";'>
	</div>

	<div class="formsm" id="formsm">
		<img style="cursor: pointer;" OnClick="smiles(event);" src="http://i.oldbk.com/i/<?php echo $link; ?>/b4.png" OnMouseOut='this.src="http://i.oldbk.com/i/<?php echo $link; ?>/b4.png";' OnMouseOver='this.src="http://i.oldbk.com/i/<?php echo $link; ?>/b4_h.png";'>
	</div>
<?php
if ($questinfo === false) {
?>
        <a class="quest" title="Получить квест" OnClick="$('#questdiv').show();return false;" href="#"><img OnMouseOver='this.src="http://i.oldbk.com/i/<?php echo $link; ?>/quest_h.png";' OnMouseOut='this.src="http://i.oldbk.com/i/<?php echo $link; ?>/quest.png";' src="http://i.oldbk.com/i/<?php echo $link; ?>/quest.png"/></a>
<?php
}
?>
        <div class="notes">
        
        
<?
	

$data = mysql_query("SELECT * FROM oldbk.`elka_2011`
	ORDER by `id` DESC LIMIT ".((int)$_GET['page']*10).",10;");


while($row = mysql_fetch_array($data)) {
	//$row['text'] = trim($row['text']);
	//if (empty($row['text'])) continue;

	echo ' <div class="m_block">';
	$inf=s_nick($row[owner],$row[align],$row[klan],$row[login],$row[level]);
	$del='';$res='';
	if(($access[i_pal]>0 || $access[i_angel]>0)&& !$row[del_id]) {
		$del= "<a OnClick=\"if (!confirm('Удалить пост?')) { return false; } \" href='elka2019.php?del_post=".$row[id]."'>&nbsp;<img src='i/clear.gif'></a>";
	} elseif($access[can_forum_restore]==1)	{
		$res= "<a OnClick=\"if (!confirm('Восстановить пост?')) { return false; } \" href='elka2019.php?restore_post=".$row[id]."'>&nbsp;<img src=i/icon2.gif></a>";
	}

	//'<span class=date>'. date("j.n.Y H:i",$row['date']).'</span> '.
	echo '<div class="name">'.$inf.'</div>';
	echo '<div class="message">';
	if(!$row[del_id]) {
		echo '<p align=left style="margin-left:7px;">'.$row['text'].'</p> '.$del;
	} else {
		$inf=s_nick($row[del_id], $row[del_align], $row[del_klan], $row[del_login], $row[del_level]);
		if($access[can_forum_restore]==1) {
			echo '<i><font color=grey>'.$row['text'].'</font></i> '.$res;
		}
			echo '&nbsp;<font color=red style="font-size: 12px;">Удалено '.($access[i_angel]>0?'ангелом ':'паладином ') .$inf.'</font>';
	}
	echo '</div><div class="bottom"></div></div>';
}
	
$pages=mysql_fetch_array(mysql_query('select count(id) as page from oldbk.elka_2011;'));
$pgs = ceil($pages[page]/10);
//  $pgs = $pgs[0]/20;

?>
<div class="pager">
<?

$pages_str='';
$page = (int)$_GET['page']>0 ? (((int)$_GET['page']+1)>$pgs ? ($pgs-1):(int)$_GET['page']):0;
$page=ceil($page);
if ($pgs>1) {
	for ($i=0;$i<ceil($pgs);$i++) {
		if (($i>($page-3))&&($i<=($page+2))) {
			$pages_str.=($i==$page ? "<span class=\"pageactive\"><b>".($i+1)."</b></span>" : "<a href='?page=".($i)."'><span class=\"pageinactive\">".($i+1)."</span></a>");
		}
	}

	//$pages_str.=($page<$pgs-3 ? "...":"");
	$pages_str=($page>2 ? "<a href='?page=".($page-1)."'><span class=\"pageinactive\">&lt;</span></a>":"").$pages_str.(($page<($pgs-1) ? "<a href='?page=".($page+1)."'><span class=\"pageinactive\">&gt;</span></a>":""));
}

$FirstPage=(ceil($pgs)>2 ? $_GET['page']>0 ? "<a href='?page=0'><span class=\"pageinactive\">&lt;&lt;</span></a>":"":"");
$LastPage=(ceil($pgs)>2 ? (ceil($pgs)-1)!=$_GET['page'] ? "<a href='?page=".(ceil($pgs)-1)."'><span class=\"pageinactive\">&gt;&gt;</span></a>":"":"");
$pages_str=$FirstPage.$pages_str.$LastPage;
	
echo $pages_str;
	
?>
            </div>
            
            <div class="clear-left"></div>
        </div>
        <div class="clear-right"></div>
    </div>
</div>
<div id="sm" style="background-color: white; z-index:10; display: none; position: absolute; width:400px; height:150px; border: 2px groove #000; text-align:center;">
<div id="sm2" style="background-color: white; z-index:11; width:400px; height:127px; border-bottom-width: 1px; border-bottom-style: groove;border-bottom-color: #000;overflow-y: scroll; text-align:left;">
<?        
foreach($lsml2 as $k=>$v) {
	echo $v;
}
?>
</div>
<input OnClick="smiles(event);" class="ssm-smile-ok" value="Закрыть" type="button">
</div>      
</form>

<div id="questdiv" style="z-index: 300; position: absolute; left: 500px; top: 200px; background: #DEDCDD url('http://capitalcity.oldbk.com/i/quest/fp_1.png') no-repeat; background-position: top;width: 688px; border: 1px solid black; padding-top:17px; display: none;">
<table width=100% height=100% cellpadding=20 cellspacing=0 style="background: url('http://capitalcity.oldbk.com/i/quest/fp_2.jpg') repeat-y;">
<tr>
<td valign="top">
<?php
if ($user['level'] >= 8) {
?>
Вам необходимо одержать победу в 25-ти <img src="http://i.oldbk.com/i/fighttype7.gif" alt=""><b>елочных хаотичных боях</b> или <b>уровневых елочных автозаявках</b> в полном комплекте (13 вещей, не считая рун). Бой будет засчитан, если вами будет нанесен урон.
<br><br>
<b>Награда</b>: 6000 репутации, Волшебная шляпа.
<br><br>
<center><a href="?takequest">Принять</a> <a style="margin-left:15px;" href="#" OnClick="$('#questdiv').hide();return false;">Закрыть</a></center>
<?php 
} else {
?>
Для взятия квеста необходим 8 уровень!
<br><br>
<a href="#" OnClick="$('#questdiv').hide();return false;">Закрыть</a></center>
<?php
}
?>
</td></tr>
</tr>
</table>
<img src="http://capitalcity.oldbk.com/i/quest/fp_3.png">
</div>


</body>
</html>