<?php
if ( ($_GET[loginname]) and ($_GET[pass]) and ($_GET[key]=='3q5v7894ty2q893tuiqk3t4ni3k4'))
{
	include "connect.php";

	$last_ch=mysql_fetch_array(mysql_query("select * from users_pas_ch where login='{$_GET[loginname]}' ;"));
	
	if ($last_ch[0]>0)
	{
	// new pass
	include "alg.php";
	$ff=in_smdp_new($_GET[pass]);

	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$_GET[loginname]}' AND `pass`!='' AND  `pass` = '".$ff."' LIMIT 1;"));

	if ((int)($user[id])>0)
		{
		      if ($user[level]<0) { echo "FALSE_LEVEL" ;}
		       else
		       if ($user[block]==1) { echo "FALSE_block" ;}
		        else 
		         if ($user[align]==4) { echo "FALSE_haos" ;}
		          else 
		          { 
		          echo "TRUE\n"; 
		          echo "ID:".$user[id]."\n";		          
		          echo "LOGIN:".$user[login]."\n";	
		          echo "LEVEL:".$user[level]."\n";
		          echo "ALIGN:".$user[align]."\n";			          
		          echo "KLAN:".$user[klan]."\n";			          		          
		          echo "MAIL:".$user[email]."\n";
		          echo "NAME:".$user[realname]."\n";
		          echo "SEX:".$user[sex]."\n";	
		          echo "BORNDATE:".$user[borndate]."\n";
		          echo "LOZUNG:".$user[lozung]."\n";			          
		          echo "ONLINE:";	
		          if ($user[odate] >= (time-60))
		          	{
		          	echo "YES\n";
		          	}
		          	else
		          	{
		          	echo "NO\n";
		          	}
		          }
		}
		else
		{
		echo "NULL";
		}

	
	}
	else
	{
	echo "FALSE_OLDPASS" ;
	}
		
}
else 
{
	echo "NULL";
}
?>