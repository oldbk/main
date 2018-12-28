<?php
echo (isset($user['who']) ? $user['who']. ' ' : '');
?>

<? if (isset($user['canViewInfo']) && $user['canViewInfo']) : ?>
    <a target="_blank" href="http://capitalcity.oldbk.com/inf.php?<?=$user['id']?>"><img src="https://i.oldbk.com/i/inf.gif"></a>
<? endif; ?>