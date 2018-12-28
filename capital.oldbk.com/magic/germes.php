<?php
// зелье гермеса для руин
if (!isset($_SESSION["uid"]) || $_SESSION["uid"] == 0) {
 header("Location: index.php");
 die();
}
if (!$user['battle'] == 0) {	echo "Не в бою..."; }
else
if ($user['ruines'] == 0) {	echo "Можно использовать только в руинах..."; }
else
{
 mysql_query('INSERT INTO `effects` (`owner`,`name`,`time`,`type`) VALUES ("'.$user['id'].'","Зелье гермеса",'.(time()+(60*10)).',605)');
 echo "Вы выпили зелье Гермеса...";
 $bet = 1;
 $sbet = 1;
}
?>