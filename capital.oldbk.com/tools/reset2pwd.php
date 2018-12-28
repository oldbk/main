<?php
session_start();
$google = true;
require '../connect.php';

$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
if (!ADMIN) die('Страница не найдена :)');
include_once ROOT_DIR.'/components/Component/Security/check_2fa.php';
$reset = false;
if(isset($_POST['login_data'])) {
    $data = explode(",", $_POST['login_data']);
	foreach($data as $login) {
	    mysql_query("UPDATE oldbk.users SET second_password = '' WHERE login = '".mysql_escape_string(trim($login))."'");
	    mysql_query("UPDATE avalon.users SET second_password = '' WHERE login = '".mysql_escape_string(trim($login))."'");

	}
	$reset = true;
}
?>
<html>
  <head>
    <title>сброс пароля</title>
    <link rel="stylesheet" href="http://i.oldbk.com/i/main.css" type="text/css"/>
    <meta content="text/html; charset=windows-1251" http-equiv="content-type" />
  </head>
<body>
<form method="POST">
<fieldset>
  <legend>Сброс второго пароля</legend>
  <?php
  if($reset) {
  ?>
  <span style="color: red; font-weight: bold;">Второй пароль сброшен</span><br />
  <?php
  }
  ?>
  <input type="text" name="login_data" /><input type="submit" value="Сделать это! :)" />
  <br /><small>(Ники можно указать через ,)</small>
</fieldset>
</form>
</body>
</html>