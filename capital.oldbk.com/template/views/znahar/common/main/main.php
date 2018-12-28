<?php
use components\Helper\StatsHelper;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 *
 * @var int $need_money_stat
 * @var int $free_stats_have
 * @var int $need_money_all_masters
 * @var \components\Component\Slim\Slim $app
 * @var \components\models\user\UserZnahar $user
 */ ?>

<tr class="odd">
    <td>
        <table class="stats">
            <colgroup>
                <col width="100px">
                <col width="50px">
                <col width="100px">
                <col width="50px">
            </colgroup>
            <tbody>
            <tr>
                <td><?= StatsHelper::$stats['sila'] ?></td>
                <td>
                    <strong><?= $user->sila ?></strong>
                </td>
                <td>
                    <?= StatsHelper::$stats['intel'] ?>
                </td>
                <td>
                    <strong><?= $user->intel ?></strong>
                </td>
                <td></td>
            </tr>
            <tr>
                <td><?= StatsHelper::$stats['lovk'] ?></td>
                <td>
                    <strong><?= $user->lovk ?></strong>
                </td>
                <td>
                    <?= StatsHelper::$stats['mudra'] ?>
                </td>
                <td>
                    <strong><?= $user->mudra ?></strong>
                </td>
                <td></td>
            </tr>
            <tr>
                <td><?= StatsHelper::$stats['inta'] ?></td>
                <td>
                    <strong><?= $user->inta ?></strong>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><?= StatsHelper::$stats['vinos'] ?></td>
                <td>
                    <strong><?= $user->vinos ?></strong>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5">
                    <form id="form-change" action="<?= $app->urlFor('znahar', array('action' => 'move')) ?>" method="post">
                        Перенести
                        <select name="from">
                            <?php foreach(StatsHelper::getStatsIdName() as $id => $title): ?>
                                <option value="<?= $id ?>"><?= $title ?></option>
                            <?php endforeach; ?>
                        </select> в
                        <select name="target">
                            <?php foreach(StatsHelper::getStatsIdName() as $id => $title): ?>
                                <?php
                                    $key = StatsHelper::getKeyById($id);
                                    $add = '';
                                    if($free_stats_have || $need_money_stat == 0)
                                        $add = '0 кр.';
                                    else
                                        $add = $user->getCost($id).' кр.';
                                ?>
                                <option value="<?= $id ?>"><?= $title.' '.$add ?></option>
                            <?php endforeach; ?>
                        </select>
                        <a href="javascript:void(0);" onclick="$('#form-change').submit();" class="button-mid btn" title="Перенести">Перенести</a>
                    </form>
                    <div style="color: red;margin-top: 5px;">
                    <?php
                    switch (true) {
                        case $need_money_stat == 0:
                            echo 'У Вас доступны бесплатные перераспределения!';
                            break;
                        case $free_stats_have != 0:
                            echo sprintf('У Вас доступно %d бесплатных перераспределений!', $free_stats_have);
                            break;
                        default:
                            echo 'Следующее бесплатное перераспределение будет доступно в ближайший понедельник!';
                            break;
                    }
                    ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr class="even">
    <td>
        <div>
            Сбросить все характеристики персонажа: <strong><?= $need_money_all_stat ?> кр.</strong>
            <a href="<?= $app->urlFor('znahar', array('action' => 'dropstat'))?>" class="button-mid btn" title="Сбросить">Сбросить</a>
        </div>
        <div>
            Сбросить все навыки владения оружием и магией: <strong><?= $need_money_all_masters ?> кр.</strong>
            <a href="<?= $app->urlFor('znahar', array('action' => 'dropmaster'))?>" class="button-mid btn" title="Сбросить">Сбросить</a>
        </div>
        <div>
            У вас в наличии: <strong><?= $user->money ?></strong> кр.
        </div>
    </td>
</tr>
