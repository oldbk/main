<?php

session_start();
include "connect.php";
include "functions.php";

if (!$user['id']) die();

error_reporting(E_ALL);
ini_set('display_errors',1);

if (isset($_GET['print'])) {
	$q = mysql_query('SELECT * FROM inventory WHERE owner = '.$user['id'].' and prototype = 1237 and id = '.intval($_GET['id']));
	if (mysql_num_rows($q) > 0) {
		$ticket = mysql_fetch_assoc($q);
		preg_match('~Билет №([\d]{1,}) на~iU',$ticket['name'],$m);
		if (isset($m[1])) {
			$ticketnum = $m[1];
			$im = imagecreatefromjpeg("./i/ticket_2017.jpg"); 
			$orange = imagecolorallocate($im, 0, 0, 255);

			if ($ticketnum >= 10) {
				imagestring($im, 5, 390, 20, $ticketnum, $orange); 
				imagestring($im, 5, 460, 20, $ticketnum, $orange); 
			} else {
				imagestring($im, 5, 400, 20, $ticketnum, $orange); 
				imagestring($im, 5, 470, 20, $ticketnum, $orange); 
			}
			$rotate = imagerotate($im, 90, 0);
			header("Content-type: image/png"); 
			imagepng($rotate);
			imagedestroy($im); 
			imagedestroy($rotate); 
		}
	}
	die();
}
?>
<html>
<body>
<?php
echo '<img src="printticket.php?id='.$_GET['id'].'&print=1">';
?>
<script>
window.print();
</script>
</body>
</html>