#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php";
//include "/www/kitezhgrad.oldbk.com/cron/init.php";
if( !lockCreate("cron_eco_job") ) {
    exit("Script already running.");
}
$tt=time();

$data = mysql_query("select sum(money) as sum_kr, level, count(id) as kol  from users where klan !='radminion' and klan !='Adminion' and block=0 GROUP by level;");
while($dd = mysql_fetch_array($data)) 
{
 mysql_query("INSERT INTO `economy` SET `levels`='{$dd[level]}',`kr_by_users`='{$dd[sum_kr]}',`kol`='{$dd[kol]}',`ddate`='{$tt}';");
}

$data = mysql_query("select sum(b.cr) as bank_kr, sum(b.ekr) as bank_ekr, `level`, count(u.id) as kol from bank b LEFT JOIN users u ON b.owner=u.id where  u.klan !='radminion' and u.klan !='Adminion' and block=0 GROUP by level");
while($dd = mysql_fetch_array($data)) 
{
 mysql_query("INSERT INTO `economy` SET `levels`='{$dd[level]}',`kol`='{$dd[kol]}',`bank_kr`='{$dd[bank_kr]}',`bank_ekr`='{$dd[bank_ekr]}',`ddate`='{$tt}';");
}

$data = mysql_query("select sum(i.cost) as item_sum,  `level`, count(u.id) as kol from inventory i LEFT JOIN users u ON (i.owner=u.id)  where i.bs_owner=0 and i.labonly=0 and i.prokat_idp=0 and  u.klan !='radminion' and u.klan !='Adminion' and u.block=0 GROUP by u.level");
while($dd = mysql_fetch_array($data)) 
{
 mysql_query("INSERT INTO `economy` SET `levels`='{$dd[level]}',`kol`='{$dd[kol]}',`kr_by_item`='{$dd[item_sum]}',`ddate`='{$tt}';");
}

lockDestroy("cron_eco_job");
?>