<?php
	session_start();
	//if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
	
	include "connect.php";
	include "functions.php";
	
	if ($user['klan']!='radminion')  die('Страница не найдена :)');

	die();
	
    	if(!$_GET[page]){$_GET[page]=0;}
     	//шаги в 5000 пользователей

	$lastid = 0;


	if($_GET[keyon]==1)
	{
	     $i=0;
	     //
	     $u=mysql_query('SELECT * FROM oldbk.users WHERE block=0 AND bot=0 LIMIT 90000,5000');
	     //$u=mysql_query('SELECT * FROM users WHERE block=0 AND bot=0 AND id=457757');
	     while($row=mysql_fetch_array($u))
	     {
	     	 $sql='insert into oldbk.inventory
	         (name,duration,maxdur,cost,owner,nlevel,nsila,nlovk,ninta,nvinos,nintel,nmudra,nnoj,ntopor,ndubina,nmech,nalign,minu,maxu,gsila,glovk,ginta,gintel,ghp,mfkrit,mfakrit,mfuvorot,mfauvorot,gnoj,gtopor,gdubina,gmech,img,`text`,dressed,bron1,bron2,bron3,bron4,dategoden,magic,`type`,present,sharped,massa,goden,needident,nfire,nwater,nair,nearth,nlight,ngray,ndark,gfire,gwater,gair,gearth,glight,ggray,gdark,letter,isrep,`update`,setsale,prototype,otdel,bs,gmp,includemagic,includemagicdex,includemagicmax,includemagicname,includemagicuses,includemagiccost,includemagicekrcost,gmeshok,tradesale,karman,stbonus,upfree,ups,mfbonus,mffree,type3_updated,bs_owner,nsex,present_text,add_time,labonly,labflag,prokat_idp,prokat_do,arsenal_klan,arsenal_owner,repcost,up_level,ecost,`group`,ekr_up,unik,add_pick,pick_time,sowner,idcity,battle,t_id,ab_mf,ab_bron,ab_uron)
	    	 VALUES
		("День Святого Валентина",0,1,10,"'.$row[id].'",0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,"giftcap14feb98gif.gif","",0,0,0,0,0,0,0,200,"Администрация ОлдБК",0,0.1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,"От всего сердца поздравляем с Днем Святого Валентина. Пусть счастье и романтика всегда сопровождают Вас! Любите и будьте Любимыми!",0,"2014-02-13 00:00:01",0.00,12318,"72",0,0,0,0,0,"",0,0.00,.0000000000000000000000000000000,0,.0000000000000000000000000000000,0,0,0,0,0,0,0,0,0,NULL,"'.time().'",0,0,0,NULL,"",0,0,0,0.00,0,NULL,0,NULL,NULL,0,0,0,0,0,0,0);';

	         mysql_query($sql);
	     //	 echo $sql . '<br>';
	       $i++;               $lastid = $row['id'];	     }                

     }
     echo 'Обработано '.$i.' записей: '.$lastid;

?>


</html>