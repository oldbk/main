<?php

die();
session_start();
//ini_set('display_errors','On');
//dur    ur     ghp
	include "connect.php";
	include "functions.php";

	if(!ADMIN){die('Страница не найдена...');}
	$f=$_GET[f];
	if(!$f){$f=0;}
	echo ' f='.$f.'<br>';
	echo '<a href="?f='.($f+5000).' ">NEXT 5k</a><br>';
 
$lotos=array();
$lotos[0]=array('count'=>3,'uron'=>5,'ghp'=>18);
$lotos[1]=array('count'=>3,'uron'=>5,'ghp'=>18);
$lotos[2]=array('count'=>3,'uron'=>5,'ghp'=>18);
$lotos[3]=array('count'=>3,'uron'=>5,'ghp'=>18);
$lotos[4]=array('count'=>5,'uron'=>7,'ghp'=>30);
$lotos[5]=array('count'=>5,'uron'=>7,'ghp'=>30);
$lotos[6]=array('count'=>7,'uron'=>10,'ghp'=>42);
$lotos[7]=array('count'=>9,'uron'=>15,'ghp'=>54);
$lotos[8]=array('count'=>21,'uron'=>25,'ghp'=>126);
$lotos[9]=array('count'=>21,'uron'=>25,'ghp'=>126);
$lotos[10]=array('count'=>21,'uron'=>25,'ghp'=>126);
$lotos[11]=array('count'=>21,'uron'=>30,'ghp'=>140);    
$lotos[12]=array('count'=>21,'uron'=>35,'ghp'=>180); 
$lotos[13]=array('count'=>21,'uron'=>40,'ghp'=>250);  
$lotos[14]=array('count'=>21,'uron'=>45,'ghp'=>300);  


$i=0;


$data=mysql_query('select * from  oldbk.users where block=0 AND sex=0 AND bot=0  LIMIT '.$f.',5000;');
//$data=mysql_query('select * from  oldbk.users where  id = 648');

while($row=mysql_fetch_assoc($data)) {	
	
	$sql='insert into oldbk.inventory
	(name,						    duration, maxdur,	cost,    owner,  nlevel,nsila,nlovk,ninta,nvinos,nintel,nmudra,nnoj,ntopor,ndubina,nmech,nalign,minu,maxu,				gsila,glovk,ginta,gintel,ghp,		 mfkrit,mfakrit,mfuvorot,mfauvorot,gnoj,gtopor,gdubina,gmech,	img,`text`,			dressed,bron1,bron2,bron3,bron4,  dategoden,magic,`type`,present,	sharped,massa,goden,needident,nfire,nwater,nair,nearth,nlight,ngray,ndark,gfire,gwater,gair,gearth,glight,ggray,gdark,	letter,																		isrep,`update`,			setsale,  prototype,otdel, bs,gmp,includemagic,includemagicdex,includemagicmax,includemagicname,includemagicuses,includemagiccost,includemagicekrcost,gmeshok,tradesale,karman,stbonus,upfree,ups,mfbonus,mffree,type3_updated,bs_owner,nsex,present_text,add_time,  labonly,labflag,prokat_idp,prokat_do,arsenal_klan,arsenal_owner,repcost,up_level,ecost,`group`,ekr_up,unik,add_pick,pick_time,sowner,idcity,battle,t_id,ab_mf,ab_bron,ab_uron)
	VALUES
	("С 8 Марта!",						0,	1,	10, "'.$row[id].'",0,0,0,0,0,0,0,0,0,0,0,0,							0,					0,0,0,0,				   0,0,0,0,0,0,0,0,0,0,						"giftcap8mart99gif.gif","",		0,0,0,0,0,			  	0,   0,    200,"Администрация ОлдБК",0,  0.1,    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,									"  Поздравляем с 8 марта! Желаем много счастья в жизни, радости, приятных подарков, мужского внимания и заботы. Любите и будьте любимыми!",	   0,      "'.date("Y").'-03-07 23:59:59",     	0, 	12318, 		"72",   0,  0,      0,                   0,0,                         "",              0,                0,                  0,      0,     	   0,	  0,	  0,	 0,  0,     0,  0,       0,         0,       0,     NULL,	"'.time().'",0,	0,0,NULL,"",0,0,0,0,0,NULL,0,NULL,NULL,0,0,0,0,0,0,0),
	("Пирожное",						0,	10,	5,  "'.$row[id].'",0,0,0,0,0,0,0,0,0,0,0,0,							0,					0,0,0,0,				   0,0,0,0,0,0,0,0,0,0,						"8food2013.gif","",		0,0,0,0,0,		 	'.(time()+(24*3600)).',2004,    200,"Администрация ОлдБК",0,  1,	 1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,									" Сладкого праздника!",												    				0,      "'.date("Y").'-03-07 23:59:59",  0,	    0,     	"73",   0,  0,      0,                   0,0,                         "",              0,                0,                  0,      0,	  0,	  0,	  0,	 0,  0,     0,  0,       0,         0,       0,     NULL,	"'.time().'",0,	0,0,NULL,"",0,0,0,0,0,NULL,0,NULL,NULL,0,0,0,0,0,0,0),
	("Букет весны ['.$lotos[$row['level']]['count'].']",	0,	10,	5,  "'.$row[id].'",0,0,0,0,0,0,0,0,0,0,0,0,							1,"'.$lotos[$row['level']]['uron'].'",	0,0,0,0,"'.$lotos[$row['level']]['ghp'].'",0,0,0,0,0,0,0,0,						"8mar_buket.gif",      "",	0,0,0,0,0,		 	'.(time()+(24*3600)).',   0,      3,"Администрация ОлдБК",0,  1,	 1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,									" С 8 марта!",												    					0,      "'.date("Y").'-03-07 23:59:59",  0,	55510000,     	"6",    0,  0,      0,                   0,0,                        "",              0,                0,		   	0,      0,	  0,	  0,	  0,	 0,  0,     0,  0,       0,         0,       0,     NULL,	"'.time().'",0,	0,0,NULL,"",0,0,0,0,0,NULL,0,NULL,NULL,0,0,0,0,0,0,0);'; 
	
	mysql_query($sql);
	$last_id=$row[id];
	$i++;
}

echo 'last='. $last_id . ', всего:'.$i;
?>