<div class="float-right p-2">
    <img src='https://i.oldbk.com/i/klan/<?=$clan1['short']?>_big.gif'>
</div>

<div class="text-center">
    <h1><?=$clan1['name']?></h1>
</div>

<div class="text-dark pb-3">
    <?=str_replace("\n","<br>",$clan1['descr']);?>
</div>

<div class="text-center">
    <? if (isset($site['memberid']) && $site['memberid'] > 0 && $site['ban'] == 1 && !empty($clan1['homepage'])) : ?>

        <div class="pb-2">
            <b>Официальный сайт клана:</b> <i>заблокирован</i>
        </div>

    <? elseif (!empty($clan1['homepage'])) : ?>

        <div class="pb-2">
            <b>Официальный сайт клана:</b> <a href='<?=$clan1['homepage']?>' target="_blank"><?=$clan1['homepage']?></a>
        </div>

    <? endif; ?>

    <? if($clan1['rekrut_klan'] > 0 && count($clan2)) : ?>

        <div class="pb-2">
            <b>Рекрут клан:</b> <a href='?clan=<?=$clan2['short']?>'><?=$clan2['short']?></a>
        </div>

    <? elseif($clan1['base_klan'] > 0 && count($clan2)) : ?>

        <div class="pb-2">
            <b>Основной клан:</b> <a href='?clan=<?=$clan2['short']?>'><?=$clan2['short']?></a>
        </div>

    <? endif; ?>

</div>

<div class="pt-3">

    <?php
    if (count($users)) {
        foreach($users as $user) {
            echo $this->renderPartial("common/renderuser",
                ['user' => $user]
            );
            if ($user['id'] == $clan1['glava']) echo '<b style="color: #008080">(Глава клана)</b>';
            echo '<br>';
        }
    }
    ?>

    <div class="pt-3">
        Всего в клане: <b><?=count($users);?></b> игроков.
    </div>


</div>