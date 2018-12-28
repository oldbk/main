<?php
$clan['c1.small_gif'] = "<img src='https://i.oldbk.com/i/klan/".$clan['c1.short'].".gif' border='0'>";
$clan['c2.small_gif'] = "<img src='https://i.oldbk.com/i/klan/".$clan['c2.short'].".gif' border='0'>";

if ($clan['c1.short'] == 'pal') {
	$clan['c1.align']="1.99";
	$clan['c1.short']='Paladins';
	$clan['c1.small_gif']="";
} elseif ($clan['c1.short'] == 'Клан Древних') {
     return;
}

?>

<li><strong><img src='https://i.oldbk.com/i/align_<?=$clan['c1.align']?>.gif' border='0'><?=$clan['c1.small_gif']?><a href='clans.html?clan=<?=$clan['c1.short']?>'> <?=$clan['c1.short']?></a></strong>
<?php
if($clan['c1.rekrut_klan'] > 0) { ?>
 - <strong><img src='https://i.oldbk.com/i/align_<?=$clan['c2.align']?>.gif' border='0'><img src='https://i.oldbk.com/i/klan/<?=$clan['c2.short']?>.gif' border='0'><a href='clans.html?clan=<?=$clan['c2.short']?>'> <?=$clan['c2.short']?></a></strong>
<?php } ?>

</li>
