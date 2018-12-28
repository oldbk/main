<?
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die();}

if ($user['id']==14897)
	{
	echo "GET:";	
	print_r($_GET);
	echo "POST:";
	print_r($_POST);	
	}

if ($user[battle]>0)
{
	
	
 if (($user[level]>=6) and ($user[in_tower]==0 || $user[in_tower]==16) and ($user[ruines]==0) )
 {
 if   (!((($user[room]>=211) and ($user[room]<=222)) OR (($user[room]>=271) and ($user[room]<=282)) ))
 {
 if ($_GET['h']==1)
  {
 $USE_HELPER=1;
 $CHAOS=1;
 	
 	if ($rowm['prototype']==1002223)
 		{
 		$cure_value = 180;
		$self_only = true;
 		}
 	else 	if ($rowm['prototype']==1002224)
 		{
 		$cure_value = 360;
		$self_only = true;
 		} 	
 	else 	if ($rowm['prototype']==1002225)
 		{
 		$cure_value = 360;
		$self_only = true;
 		} 		
 	else 	if ($rowm['prototype']==1002226)
 		{
 		$cure_value = 720;
		$self_only = true;
 		} 
 	else
 		{		 		
		 //старые
		   if (($user[level]==6) or ($user[level]==7))
		   	{ 
		   	$cure_value = 120;
			$self_only = true;
		   	}
		    else 
		   	{ 
		   	$cure_value = 180;
			$self_only = true;
		   	}
		}
  include "cure_base.php"   ;
    
  }
  elseif ($_GET['h']==2)
  {
 $USE_HELPER=2;
 $CHAOS=1;
 include "clone.php";
  
 }
 elseif ($_GET['h']==3)
 {
 $USE_HELPER=3;
 $CHAOS=1;
 $haos_unclone[kol]=10;
 include "unclone.php";

 }
  elseif ($_GET['h']==4)
 {
 $USE_HELPER=4;
 $CHAOS=1;

 	if (($rowm['prototype']==1002223) or  ($rowm['prototype']==1002222) )
 		{
 		$cure_value = 90;
		$self_only = true;
 		}
 	else 	if ($rowm['prototype']==1002224)
 		{
 		$cure_value = 180;
		$self_only = true;
 		} 	
 	else 	if ($rowm['prototype']==1002225)
 		{
 		$cure_value = 180;
		$self_only = true;
 		} 		
 	else 	if ($rowm['prototype']==1002226)
 		{
 		$cure_value = 360;
		$self_only = true;
 		} 
 		

include "cure_mana_base.php";

 }
 
 if ($MAGIC_OK==1)
 	{

  	//пишем в лог если надо
			if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
			elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
  	 $sexi[0]='использовала';
	 $sexi[1]='использовал';
  	//addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle_hist($user,$user[battle_t]).'  '.$sexi[$user[sex]].'  аптечку!<BR>');
	addlog($user['battle'],"!:X:".time().':'.nick_new_in_battle($user).':'.($user[sex]+800)."\n");  	
 	
	// записываем в юзы в бой
	mysql_query("INSERT `battle_vars` (`battle`,`owner`,`update_time`,`help_use`,`help_proto`) values('".$user['battle']."', '".$user['id']."', '".time()."' , '1' ,'".$rowm['prototype']."') ON DUPLICATE KEY UPDATE `help_use` =`help_use`+1 , `help_proto`=(if(`help_proto`=0,'".$rowm['prototype']."',`help_proto`))  ;"); 	
 	
 	}
    }
   }
  
 }
?>