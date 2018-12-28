<?php

/**
 * @var \components\Component\Slim\View $this
 * @var \DebugBar\SlimDebugBar $debugbar;
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
	<link rel="stylesheet" href="//oldbk.com/eassets/stylesssl.css" type="text/css" media="screen">
	<link rel="apple-touch-icon-precomposed" sizes="512x512" href="//i.oldbk.com/i/icon/oldbk_512x512.png">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="//i.oldbk.com/i/icon/oldbk_144x144.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="//i.oldbk.com/i/icon/oldbk_114x114.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="//i.oldbk.com/i/icon/oldbk_72x72.png">
	<link rel="apple-touch-icon-precomposed" sizes="58x58" href="//i.oldbk.com/i/icon/oldbk_58x58.png">
	<link rel="apple-touch-icon-precomposed" sizes="48x48" href="//i.oldbk.com/i/icon/oldbk_48x48.png">
	<link rel="apple-touch-icon-precomposed" sizes="29x29" href="//i.oldbk.com/i/icon/oldbk_29x29.png">
	<link rel="apple-touch-icon-precomposed" href="//i.oldbk.com/i/icon/oldbk_57x57.png">
	<meta name='yandex-verification' content='60ef46abc2646a77'>
	<title><?= $page_title ?></title>
	<?php if($page_description): ?>
		<META name="description" content="<?= $page_description ?>">
	<?php endif; ?>
	<?php foreach ($app->clientScript->getCssFiles() as $cssFile): ?>
		<link rel="StyleSheet" href="<?= $cssFile ?>" type="text/css">
	<?php endforeach; ?>
	<script type="text/javascript">
		function look(type){
			param=document.getElementById(type);
			if(param.style.display == "none") param.style.display = "block";
			else param.style.display = "none"
		}
	</script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<?php foreach ($app->clientScript->getJsFiles(\components\Component\Slim\Middleware\ClientScript\ClientScript::JS_POSITION_BEGIN) as $jsFile): ?>
		<script src="<?= $jsFile; ?>"></script>
	<?php endforeach; ?>
	<?php
	if($debugbar) {
		echo $debugbar->getJavascriptRenderer()->renderHead();
	}
	?>
	<style>
		table.table_library tr:nth-child(odd) {background: #E6E2CE}
		table.table_library tr:nth-child(even) {background: #F3F1E7}
	</style>
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="1065" valign="top" class="leftY"><table width="100%" style="height:270px;" border="0" cellpadding="0" cellspacing="0" class="headLeft">
				<tr>
					<td height="270">&nbsp;</td>
				</tr>
			</table>
		</td>
		<td width="1018" valign="top" style="background-image:url('https://oldbk.com/eassets/i/main_bg.jpg');">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td height="263" class="header">&nbsp;</td>
				</tr>
				<tr>
					<td height="700" valign="top" class="cont_cracks">
						<table width="973" border="0" align="center" cellpadding="2" cellspacing="3">
							<tr>
								<td valign="top" class="TD1">
									<hr style="border: none;color: #a09c81;background-color: #a09c81;height: 2px;">
									<div align="right"><input type="image" src="https://oldbk.com/i/main/lib_reg2.gif" onclick="location.href='https://oldbk.com/reg.php?reg=1&amp;b=&amp;pid=203&amp;ref='"></div>
									<div class="container">
										<?= $content ?>
									</div>
									<div>&nbsp;</div>
									<div align="right"><input type="image" src="https://oldbk.com/i/main/lib_reg2.gif" onclick="location.href='https://oldbk.com/reg.php?reg=1&amp;b=&amp;pid=203&amp;ref='"></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top" class="rightY">
			<table width="100%" style="height:215px;" border="0" cellpadding="0" cellspacing="0" class="headRight">
				<tbody>
				<tr>
					<td height="270">&nbsp;</td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="footLeft">&nbsp;</td>
		<td width="1018" height="138" class="footer" valign=bottom align=center>
			<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="down_menu">
				<tr>
					<td height="38" valign="bottom">
						<a href="https://oldbk.com/?about=yes" target=_blank class="down_menuL">ОБ ИГРЕ</a> |
						<a href="https://oldbk.com/news.php" target=_blank class="down_menuL">НОВОСТИ</a> |
						<a href="https://oldbk.com/forum.php" target=_blank class="down_menuL">ФОРУМ</a> |
						<a href="https://top.oldbk.com/index.php" target=_blank class="down_menuL">РЕЙТИНГИ</a> |
						<a href="https://oldbk.com/partners/index.php" target=_blank class="down_menuL">ПАРТНЕРАМ</a>
					</td>
				</tr>
			</table>
			<div align="center" style="margin-top:10px;">
				<?= include($_SERVER['DOCUMENT_ROOT'].'/counters/all.php'); ?>
				<br><a href="https://oldbk.com/" style="color:#808080;">Многопользовательская бесплатная онлайн фэнтези рпг - ОлдБК - Старый Бойцовский Клуб</a>
			</div>
		</td>
		<td class="footRight">&nbsp;</td>
	</tr>
</table>
</body>
</html>