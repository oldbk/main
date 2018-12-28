<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 *
 * @var \components\models\user\UserZnahar $user
 * @var int $drop_klass_have
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
            <div class="head-title">Выбор класса персонажа</div>
            <div class="head-right"></div>
            <a class="spoiler right spoiler-down" href="javascript:void(0);"></a>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr class="even hidden">
        <td colspan="2">
            <strong>Для установки класса нажмите на иконку класса. Выбор класса доступен с 8-го уровня. </strong>
        </td>
    </tr>
    <tr class="odd hidden">
        <td colspan="2" class="sub-title">
            <?php if($drop_klass_have == 0): ?>
                <em class="center" style="color: red">
                    Бесплатных смен класса: <?= $drop_klass_have ?>. Стоимость смены класса <?= $need_money_klass ?>кр.
                </em>
            <?php else: ?>
                Бесплатных смен класса: <?= $drop_klass_have ?>.
            <?php endif; ?>
        </td>
    </tr>
    <tr class="event hidden">
        <td colspan="2">
            <ul class="ability-block">
                <?php $user_class = $app->container->get('class_desc'); ?>
				<?php foreach ($user_class as $key => $game_class): ?>
                    <li class="clearfix">
                        <div class="ability <?= $user->uclass == $key ? 'active' : 'disable' ?>">
                            <?php if($user->uclass != $key): ?>
                                <a onclick="return confirm('Вы уверены, что хотите установить своему персонажу класс <?= $game_class['title'] ?>?\nСтоимость смены класса составит: <?= $drop_klass_have > 0 ? 0 : $need_money_klass ?>кр.')" href="<?= $app->urlFor('znahar', array('action' => 'changeclass', 'klass' => $key)); ?>">
                                    <img width="42" title="<?= $game_class['title'] ?>" src="<?= $game_class['img']; ?>">
                                </a>
                            <?php else: ?>
                                <img width="42" title="<?= $game_class['title'] ?>" src="<?= $game_class['img']; ?>">
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
