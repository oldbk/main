<?
session_start();
	if (isset($_GET['curtab']))
		{
		if (((int)$_GET['curtab']>0) and ((int)$_GET['curtab']<=5) )
			{
			$_SESSION['gruppovuha'][8]=(int)$_GET['curtab'];
			}
		}
?>