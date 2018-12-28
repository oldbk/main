<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

$how = 0;
if ($rowm['prototype'] == 633) $how = 1000;
if ($rowm['prototype'] == 634) $how = 5000;
if ($rowm['prototype'] == 635) $how = 10000;
if ($rowm['prototype'] == 636) $how = 50000;

if (!$how) die();


$q = mysql_query('UPDATE users SET money = money + '.$how.' WHERE id = '.$user['id'].' LIMIT 1');

$rec['owner']=$user['id'];
$rec['owner_login']=$user['login'];
$rec['target']=0;
$rec['target_login']='Упаковка';
$rec['owner_balans_do']=$user['money'];
$rec['owner_balans_posle']=$user['money']+$how;
$rec['type']=422;//  
$rec['sum_kr']=$how;
$rec['sum_ekr']=0;
$rec['sum_kom']=0;
$rec['item_id']=get_item_fid($rowm);
$rec['item_name']=$rowm['name'];
$rec['item_count']=1;
$rec['item_type']=$rowm['type'];
$rec['item_cost']=$rowm['cost'];
$rec['item_dur']=$rowm['duration'];
$rec['item_maxdur']=$rowm['maxdur'];
$rec['item_ups']=0;
$rec['item_unic']=0;
$rec['item_incmagic']=$rowm['includemagic'];
$rec['item_incmagic_count']=$rowm['includemagicdex'];
$rec['item_arsenal']='';
$rec['add_info'] = $rowm['name'];
add_to_new_delo($rec);

$bet=1;
$sbet = 1;
echo "Вы открыли мешочек и получили ".$how." кр.";

?>