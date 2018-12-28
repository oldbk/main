<?php

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 *
 * @var \components\models\User $user
 */ ?>

<style>
    #page-wrapper #znahar ul.ability-block .ability {
        width: 45px;
        margin-right: 5px;
        margin-left: -50px;
    }
    #page-wrapper #znahar ul.ability-block .ability.disable {
        opacity: 0.4;
        filter: alpha(opacity=40); /* msie */
    }
    #page-wrapper #znahar ul.ability-block .ability > img {
        opacity: 1;
        filter: alpha(opacity=100); /* msie */
    }
</style>
<table class="table" cellspacing="0" cellpadding="0">
    <colgroup>
        <col width="100px">
    </colgroup>
    <thead>
    <tr class="head-line spoiler-block ability-spoiler">
        <th colspan="2">
            <div class="head-left"></div>
            <div class="head-title">Выбор основной магии стихии</div>
            <div class="head-right"></div>
            <a class="spoiler right spoiler-down" href="javascript:void(0);"></a>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr class="even hidden">
        <td colspan="2">
            <strong>Для выбора основной магии стихии нажмите на соответствующую иконку. Выбрать стихию можно только единожды. Сменить стихию можно при помощи свитка <a href="https://oldbk.com/encicl/mag1/greatscroll_smenamagic.html" target="_blank"><strong>«Смена магии стихии»</strong></a>. После выбора основной магии вам станет доступна покупка дополнительной магии стихии. Выбор стихии доступен с 8-го уровня.</strong>
        </td>
    </tr>
    <tr class="event hidden">
        <td colspan="2">
            <ul class="ability-block">
				<?php $user_class = $app->container->get('magic_desc'); ?>
				<?php foreach ($user_class as $key => $game_class): ?>
                    <li class="clearfix">
                        <div class="ability disable">
                            <?php if($user->level >= $app->dbConfig->znahar_min_magic): ?>
                                <a onclick="return confirm('Вы уверены, что хотите установить своему персонажу магию стихий <?= $game_class['title'] ?>?')" href="<?= $app->urlFor('znahar', array('action' => 'magic', 'id' => $key)); ?>">
                                    <img width="42" alt="<?= $game_class['title'] ?>" title="<?= $game_class['title'] ?>" src="<?= $game_class['img']; ?>">
                                </a>
                            <?php else: ?>
                                <img width="42" alt="<?= $game_class['title'] ?>" title="<?= $game_class['title'] ?>" src="<?= $game_class['img']; ?>">
                            <?php endif; ?>
                        </div>
                        <div class="description">
                            <strong><?= $game_class['title']; ?></strong> - <?= $game_class['desc']; ?>
                        </div>
                    </li>
				<?php endforeach; ?>
            </ul>
        </td>
    </tr>
    </tbody>
</table>