<?php
echo (isset($user['who']) ? $user['who']. ' ' : '');
if(isset($user['align']) && $user['align'] != '') { ?>
    <img src="https://i.oldbk.com/i/align_<?=$user['align']?>.gif" alt="">
<?php } ?>

<?php if(isset($user['klan']) && $user['klan'] != '') { ?>
    <img src="https://i.oldbk.com/i/klan/<?=$user['klan']?>.gif" alt="">
<?php } ?>
<b class="login"><?=$user['login']?></b>

<? if (isset($user['level'])) : ?>
    [<?=$user['level']?>]
<? endif; ?>

<? if (!isset($user['canViewInfo']) || (isset($user['canViewInfo']) && $user['canViewInfo'])) : ?>
    <a target="_blank" href="http://capitalcity.oldbk.com/inf.php?<?=$user['id']?>"><img src="https://i.oldbk.com/i/inf.gif" alt=""></a>
<? endif; ?>
