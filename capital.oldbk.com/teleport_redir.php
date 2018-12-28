<?
//teleporter redirekt
 	session_start();
	if (!($_SESSION['uid'] >0)) {  header("Location: index.php"); die(); } 	
	include "connect.php";
	$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
	
	echo "<html>";
	echo "<body>";	
	
	if (($_SESSION['TELEPORT_GOOD']=='avaloncity.oldbk.com' ) OR ($_SESSION['TELEPORT_GOOD']=='capitalcity.oldbk.com') OR ($_SESSION['TELEPORT_GOOD']=='angelscity.oldbk.com') )
	 {
	 include "alg.php";
	echo '<FORM action="http://'.$_SESSION['TELEPORT_GOOD'].'/enter.php?" id="login" method="POST" name="frm">';
	unset($_SESSION);
	session_destroy();		
    	setcookie ("PHPSESSID", "", time() - 3600);
	echo '<input name="login" type="hidden" value="'.$user[login].'">
		<input name="psw"   type="hidden" value="'.htmlspecialchars(out_smdp_new($user['pass']),ENT_QUOTES).'">
		</form>
		<script language="JavaScript">
		document.frm.submit();
		</script>';
	}
	
	echo "</body>";	
	echo "</html>";	
?>