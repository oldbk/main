<?
session_start();

if (isset($_GET['exit']))
 {
	unset($_SESSION['uid']);
	unset($_SESSION['uid2']);
	unset($_SESSION['sid']);
	unset($_SESSION['view']);
	unset($_SESSION['chpass']);
	unset($_SESSION['oldalg']);
     	unset($_SESSION['ip']);

     	unset($_SESSION['quest']);
     	unset($_SESSION['questdata']);
     	unset($_SESSION['questid']);
     	unset($_SESSION['boxisopen']);     	
     	unset($_SESSION['laastchatid']);
     	unset($_SESSION['lastlook']);
     	//session_regenerate_id();		
	session_unset();
	session_destroy();
	header("Location: http://oldbk.com?e=2");
	die();
 }

if (!($_SESSION['uid'] >0)) 
{
		header("Location: http://oldbk.com?error=123");
 		die; 
 }
	

header("Location: http://oldbk.com?e=1");
?>