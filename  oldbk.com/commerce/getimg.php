<?
session_start();
include "/www/oldbk.com/connect.php";

function is_ani ( $filename ) {
if(!( $fh = @ fopen ( $filename , 'rb' )))
return false ;
$count = 0 ;
//an animated gif contains multiple "frames", with each frame having a
//header made up of:
// * a static 4-byte sequence (\x00\x21\xF9\x04)
// * 4 variable bytes
// * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)

// We read through the file til we reach the end of the file, or we've found
// at least 2 frame headers
while(! feof ( $fh ) && $count < 2 ) {
$chunk = fread ( $fh , 1024 * 100 ); //read 100kb at a time
$count += preg_match_all ( '#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s' , $chunk , $matches );
}

fclose ( $fh );
return $count > 1 ;
} 

if ($_SESSION['uid'])
{
echo "<div>";
$menu=(int)($_GET[n]);
$param=(int)($_GET[param]);

	if (( ($menu==11)OR($menu==12)OR($menu==612)OR($menu==13)OR ($menu==14) OR ($menu==603) OR ($menu==602) OR ($menu==600) OR ($menu==601) OR ($menu==613) OR ($menu==614)  OR ($menu==15) OR ($menu==16) OR ($menu==83) ) and ($param >0) )
	{
		//ищим образ
		 if ( ($menu==11) OR  ($menu==83))
		 {
		 $get_sh=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.users_shadows  where owner='{$_SESSION['uid']}' and type=2 and id='{$param}' ;"));  
		 }
		 else if ($menu==12 || $menu == 612)
		 {
		 $glava_test_clear=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.clans WHERE `glava` = '".$_SESSION['uid']."'  ;"));
		 if ($glava_test_clear[id]>0)
		 	{
			 $get_sh=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.users_shadows  where klan='{$glava_test_clear[id]}' and type=1 and id='{$param}' ;"));  
			 $pref[0]='g';
 			 $pref[1]='m';
			 $get_sh[name]=$pref[$get_sh[sex]].$get_sh[name];
			 }
		 }
		 else if ($menu==13)
		 {
		 $get_sh=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.eshop  where owner='{$_SESSION['uid']}' and type=200 and razdel=72 and id='{$param}'  ;"));  
		 }
		  else if ($menu==14)
		 {
			 $glava_test_clear=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.clans WHERE `glava` = '".$_SESSION['uid']."'  ;"));
			 if ($glava_test_clear[id]>0)
		 	{
			 $get_sh=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.eshop  where klan='{$glava_test_clear[short]}' and type=200 and razdel=72 and id='{$param}'  ;"));  
			 }
		 }
		  else if ($menu==15 || $menu==600 || $menu==601)
		 {
			 $test_telo=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.users WHERE `id` = '".$_SESSION['uid']."'  ;"));
				  	 if ($test_telo[id]>0)
				  	 {
				  	 //есть чар
				  	 		if ($test_telo[klan]!='')
				  	 		{
				  	 		//в клане
				  	 		$get_klan=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.clans WHERE `short` = '".$test_telo[klan]."'  ;"));
			  	 			$get_sh=mysql_fetch_array(mysql_query("SELECT * FROM gellery where owner='{$test_telo[id]}' and id='{$param}' and img not in (select img from gellery_prot where klan_owner='{$get_klan[id]}') ;"));  
				  	 		}
				  	 		else
				  	 		{
				  	 		//не в клане
							$get_sh=mysql_fetch_array(mysql_query("SELECT * FROM gellery where owner='{$test_telo[id]}' and id='{$param}' and otdel != 99;")); 
				  	 		}
					}
		 }
 		  else if ($menu==16 || $menu == 602 || $menu == 603)
 		  {
 		  	$test_telo=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.users WHERE `id` = '".$_SESSION['uid']."'  ;"));
				  	 if ($test_telo[id]>0)
				  	 {
				  	 //есть чар
				  	 		if ($test_telo[klan]!='')
				  	 		{
				  	 		//в клане
				  	 		$get_klan=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.clans WHERE `short` = '".$test_telo[klan]."'  ;"));
				  	 		$get_sh=mysql_fetch_array(mysql_query("select * from gellery_prot where klan_owner='{$get_klan[id]}' and id='{$param}'  ;"));  
							}
					}
				   
 		  
 		  }
 		  else if ($menu == 613 || $menu == 614)
 		  {
 		  	$test_telo=mysql_fetch_assoc(mysql_query("SELECT * FROM oldbk.users WHERE `id` = '".$_SESSION['uid']."'  ;"));
				  	 if ($test_telo[id]>0)
				  	 {
				  	 //есть чар
					$get_sh=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.inventory WHERE id='{$param}' AND owner='{$test_telo[id]}'  AND setsale=0 AND bs_owner=0  AND sowner = '{$test_telo[id]}'  AND arsenal_klan='' AND art_param !=''  "));

					}
				   
 		  
 		  }		 
		 
			if ($get_sh[id]>0)
			{
			
				///тут надо проверить анимировано оно лил нет
				if (($menu==11) or ($menu==12) or ($menu == 612) or ($menu==83) )
				{
				if ($menu==83)
					{
					//без проверок разрешаем анимированый
					unset($_SESSION['new_serv_img']);
					$_SESSION[select_array][anim]=true;
					$_SESSION[select_array][size]=80;
					}
				else
					{
					if ($menu == 612) {
						$anim = 1;
					} else {
						$anim=is_ani('https://i.oldbk.com/i/shadow/'.$get_sh[name].'.gif');
					}
					if ($anim>=1)
						{
						unset($_SESSION['new_serv_img']);
						$_SESSION[select_array][anim]=true;
						$_SESSION[select_array][size]=80;
						}
						else
						{
						unset($_SESSION['new_serv_img']);					
						$_SESSION[select_array][anim]=false;
						$_SESSION[select_array][size]=40;					
						}
					}
				echo "<img src=https://i.oldbk.com/i/shadow/{$get_sh[name]}.gif>";				
				}
				else if (($menu==13) or ($menu==14) or ($menu==602) or ($menu==603) or ($menu==601) or ($menu==600) or ($menu==15) or ($menu==16) )
				{
				if ($menu == 600 || $menu == 601 || $menu == 602 || $menu == 603) {
					$anim = 1;
				} else {
					$anim=is_ani('https://i.oldbk.com/i/sh/'.$get_sh[img]);
				}

				if ($anim>=1)
					{
					unset($_SESSION['new_serv_img']);
					$_SESSION[select_array][anim]=true;
					$_SESSION[select_array][size]=40;
					$info_img=getimagesize('https://i.oldbk.com/i/sh/'.$get_sh[img]);
					$_SESSION[select_array][w]=$info_img[0];
					$_SESSION[select_array][h]=$info_img[1];
					
					}
					else
					{
					unset($_SESSION['new_serv_img']);					
					$_SESSION[select_array][anim]=false;
					$_SESSION[select_array][size]=20;	
					$info_img=getimagesize('https://i.oldbk.com/i/sh/'.$get_sh[img]);
					$_SESSION[select_array][w]=$info_img[0];
					$_SESSION[select_array][h]=$info_img[1];									
					}
				echo "<img src=https://i.oldbk.com/i/sh/{$get_sh[img]}>";	
				}
				else if (($menu==613) or ($menu==614) )
				{
					
					if ($menu==613)
					{
					unset($_SESSION['new_serv_img']);
					$_SESSION[select_array][anim]=false;
					$_SESSION[select_array][size]=100;
					$info_img=getimagesize('https://i.oldbk.com/i/sh/'.$get_sh[img]);
					$_SESSION[select_array][w]=$info_img[0];
					$_SESSION[select_array][h]=$info_img[1];
					}
					elseif ($menu==614)
					{
					unset($_SESSION['new_serv_img']);					
					$_SESSION[select_array][anim]=true;
					$_SESSION[select_array][size]=100;	
					$info_img=getimagesize('https://i.oldbk.com/i/sh/'.$get_sh[img]);
					$_SESSION[select_array][w]=$info_img[0];
					$_SESSION[select_array][h]=$info_img[1];									
					}
				echo "<img src=https://i.oldbk.com/i/sh/{$get_sh[img]}>";	
					
					
				
				}
				
			

			}
	
	}
echo "</div>";
}
else
{
 die("<script>location.href='index.php?exit=314';</script>");
}
?>
