<?php
die(); 
session_start();

set_time_limit(0);
ignore_user_abort(true);

include "connect.php";
include "functions.php";


if(!ADMIN) die();

$f=$_GET[f];
if(!$f){$f=0;}
echo ' f='.$f.'<br>';
echo '<a href="?f='.($f+5000).' ">NEXT 5k</a><br>';

//die();

$data = mysql_query('select * from oldbk.users where block=0 AND sex=1 and bot = 0 LIMIT '.$f.',5000');

//$data = mysql_query('select * from oldbk.users where block=0 AND bot=0 and id = 698171 LIMIT 1');


$lotos=array();
$lotos[0]=array('count'=>0,'uron'=>5,'ghp'=>18);
$lotos[1]=array('count'=>1,'uron'=>5,'ghp'=>18);
$lotos[2]=array('count'=>2,'uron'=>5,'ghp'=>18);
$lotos[3]=array('count'=>3,'uron'=>5,'ghp'=>18);
$lotos[4]=array('count'=>4,'uron'=>7,'ghp'=>30);
$lotos[5]=array('count'=>5,'uron'=>7,'ghp'=>30);
$lotos[6]=array('count'=>6,'uron'=>10,'ghp'=>42);
$lotos[7]=array('count'=>7,'uron'=>15,'ghp'=>54);
$lotos[8]=array('count'=>8,'uron'=>25,'ghp'=>126);
$lotos[9]=array('count'=>9,'uron'=>25,'ghp'=>126);
$lotos[10]=array('count'=>10,'uron'=>25,'ghp'=>126);
$lotos[11]=array('count'=>11,'uron'=>30,'ghp'=>140);    
$lotos[12]=array('count'=>12,'uron'=>35,'ghp'=>180); 
$lotos[13]=array('count'=>13,'uron'=>40,'ghp'=>250); 
$lotos[14]=array('count'=>14,'uron'=>45,'ghp'=>350);

while($row=mysql_fetch_assoc($data)) {	
	
	$sql='insert into oldbk.inventory
	(name,						    duration, maxdur,	cost,    owner,  nlevel,nsila,nlovk,ninta,nvinos,nintel,nmudra,nnoj,ntopor,ndubina,nmech,nalign,minu,maxu,				gsila,glovk,ginta,gintel,ghp,		 mfkrit,mfakrit,mfuvorot,mfauvorot,gnoj,gtopor,gdubina,gmech,	img,`text`,			dressed,bron1,bron2,bron3,bron4,  dategoden,magic,`type`,present,	sharped,massa,goden,needident,nfire,nwater,nair,nearth,nlight,ngray,ndark,gfire,gwater,gair,gearth,glight,ggray,gdark,	letter,																isrep,`update`,			setsale,  prototype,otdel, bs,gmp,includemagic,includemagicdex,includemagicmax,includemagicname,includemagicuses,includemagiccost,includemagicekrcost,gmeshok,tradesale,karman,stbonus,upfree,ups,mfbonus,mffree,type3_updated,bs_owner,nsex,present_text,add_time,  labonly,labflag,prokat_idp,prokat_do,arsenal_klan,arsenal_owner,repcost,up_level,ecost,`group`,ekr_up,unik,add_pick,pick_time,sowner,idcity,battle,t_id,ab_mf,ab_bron,ab_uron)
	VALUES
	("С 23м Февраля!",					0,	1,	10, "'.$row[id].'",0,0,0,0,0,0,0,0,0,0,0,0,							0,					0,0,0,0,				   0,0,0,0,0,0,0,0,0,0,						"giftcap23feb99gif.gif","",		0,0,0,0,0,			  	0,   0,    200,"Администрация ОлдБК",0,  0.1,    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,									"Поздравляем с 23 февраля! Желаем удачи и красивых побед во всех мирах! Мира и благополучия Вам и Вашим близким!",	   	0,      "'.date("Y").'-02-22 23:59:59",     	0, 	12318, 		"72",   0,  0,      0,                   0,0,                         "",              0,                0,                  0,      0,     	   0,	  0,	  0,	 0,  0,     0,  0,       0,         0,       0,     NULL,	"'.time().'",0,	0,0,NULL,"",0,0,0,0,0,NULL,0,NULL,NULL,0,0,0,0,0,0,0),
	("100 грамм",						0,	10,	5,  "'.$row[id].'",0,0,0,0,0,0,0,0,0,0,0,0,							0,					0,0,0,0,				   0,0,0,0,0,0,0,0,0,0,						"stakan_2013.gif","",		0,0,0,0,0,		 	'.(time()+(24*3600)).',2004,    200,"Администрация ОлдБК",0,  1,	 1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,									"",												    				0,      "'.date("Y").'-02-22 23:59:59",  0,	    0,     	"73",   0,  0,      0,                   0,0,                         "",              0,                0,                  0,      0,	  0,	  0,	  0,	 0,  0,     0,  0,       0,         0,       0,     NULL,	"'.time().'",0,	0,0,NULL,"",0,0,0,0,0,NULL,0,NULL,NULL,0,0,0,0,0,0,0),
	("Букет гвоздик ['.$lotos[$row['level']]['count'].']",	0,	10,	5,  "'.$row[id].'",0,0,0,0,0,0,0,0,0,0,0,0,							1,"'.$lotos[$row['level']]['uron'].'",	0,0,0,0,"'.$lotos[$row['level']]['ghp'].'",0,0,0,0,0,0,0,0,						"gvozd23fev_1.gif",      "",	0,0,0,0,0,		 	'.(time()+(24*3600)).',   0,      3,"Администрация ОлдБК",0,  1,	 1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,									"",												    				0,      "'.date("Y").'-02-22 23:59:59",  0,	55510000,     	"6",    0,  0,      0,                   0,0,                        "",              0,                0,		   	0,      0,	  0,	  0,	  0,	 0,  0,     0,  0,       0,         0,       0,     NULL,	"'.time().'",0,	0,0,NULL,"",0,0,0,0,0,NULL,0,NULL,NULL,0,0,0,0,0,0,0);'; 
	
	mysql_query($sql);
	$last_id=$row[id];
	$i++;
}

echo 'last='. $last_id . ', всего:'.$i;
?>
