<?
//меняем тип в бою на тип для теста боя
$btid=(int)$_POST['target'];

$btl=mysql_fetch_array(mysql_query("SELECT * FROM `battle` WHERE `id` = '{$btid}' and status=0 and win=3  LIMIT 1;"));

if ($btl[id]>0)
{
		mysql_query("UPDATE battle set teams='AFB' WHERE `id` = '{$btid}' ;");
		echo "Вы закрыли бой от вмешательства! и включили тестовые формулы :)";
}
else
{
echo "Бой не найден или окончен!";
}


?>