<?
if (!($_SESSION['uid'] >0)) header("Location: index.php");

if ($user['battle'] > 0) {
	echo "<font color=red>Не в бою...</font>";
} else	{

if (($user['lab'] ==0 ) OR ($user['lab'] ==1 ))
	{
	echo "<font color=red>Можно использовать только в Героическом Лабиринте...</font>";
	}
	else
	{

		{
		mysql_query("INSERT INTO oldbk.`inventory` (`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`) VALUES ('Познание Лабиринта',0,1,1,'{$user[id]}',7,0,0,0,0,3,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'lmap.gif','',0,0,0,0,0,0,78,12,'',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',0,'',0,50078,'52',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,0,NULL,0,NULL,NULL,0);");
		$id=mysql_insert_id();
		mysql_query("UPDATE oldbk.`inventory` SET `present`='Лабиринт Хаоса',`duration`=1,`letter`='<a target=_blank href=lab2.php?lookmap={$id} >Просмотреть карту</a>',`labonly`=1,`labflag`=1 WHERE `id`={$id} and owner='{$user[id]} and setsale=0 ';");
		echo "<font color=red>Удачно использовано: <i>Познание Лабиринта</i>...</font>";
		if ($user[sex]==1) { $sexi='использовал'; } else { $sexi='использовала'; }
		addch("<img src=i/magic/lmap.gif> {$user[login]} ".$sexi." <i>Познание Лабиринта</i>",$user['room'],$user['id_city']);
		$bet=1;
		$sbet = 1;
		}

	 }	
	  
	
	

}
?>

