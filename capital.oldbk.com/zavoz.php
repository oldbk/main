<?
session_start();
include "connect.php";
include "functions.php";

if(!ADMIN)
{
	die('Страница не найдена...');
}

if($_POST[action]>0)
{
/*
action => 349
shop_id => 248
cap_shop_count => 172899
ava_shop_count => 194299
eshop_id => 5204
cap_eshop_count => 9997423
ava_eshop_count => 9999422
cshop_id => 1005204
cap_cshop_count => 14002
ava_cshop_count => 1443 
shopbanner => 0  eshopbanner => 0  cshopbanner => 0 
*/
		if(isset($_POST[shop_id]))
		{
			mysql_query("update oldbk.shop set `count`='".(int)$_POST[cap_shop_count]."',avacount='".(int)$_POST[ava_shop_count]."' , shopbanner='".(int)($_POST[shopbanner])."' WHERE id='".(int)$_POST[shop_id]."' ");
		     	if(mysql_affected_rows()>0) 
		     	{
		     	echo "Данные таблице shop обновлены";	
		     	}
		}
		if(isset($_POST[eshop_id]))
		{
			mysql_query("update oldbk.eshop set `count`='".(int)$_POST[cap_eshop_count]."',avacount='".(int)$_POST[ava_eshop_count]."' , shopbanner='".(int)($_POST[eshopbanner])."'  WHERE id='".(int)$_POST[eshop_id]."' ");
		     	if(mysql_affected_rows()>0) 
		     	{
		     	echo "Данные таблице eshop обновлены";	
		     	}
		}
		if(isset($_POST[cshop_id]))
		{
			mysql_query("update oldbk.cshop set `count`='".(int)$_POST[cap_cshop_count]."',avacount='".(int)$_POST[ava_cshop_count]."' , shopbanner='".(int)($_POST[cshopbanner])."' WHERE id='".(int)$_POST[cshop_id]."' ");
		     	if(mysql_affected_rows()>0) 
		     	{
		     	echo "Данные таблице cshop обновлены";	
		     	}			
		}
		//echo $sql1.'<br>'.$sql2.'<br>'.$sql3;
	
}
?>


