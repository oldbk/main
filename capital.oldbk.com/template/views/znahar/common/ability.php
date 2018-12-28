<?php
use components\models\magic\Ability;
use \components\Component\Config;
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 *
 * @var \components\models\user\UserZnahar $user
 * @var array $allAbility
 * @var array $userAbility
 * @var array $canAbility
 * @var boolean $free_abil_drop
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
<?php include ROOT_DIR."/abiltxt.php"; ?>
<table class="table" cellspacing="0" cellpadding="0">
    <colgroup>
        <col width="100px">
    </colgroup>
    <thead>
    <tr class="head-line spoiler-block ability-spoiler">
        <th colspan="2">
            <div class="head-left"></div>
            <div class="head-title">Управление абилити склонности</div>
            <div class="head-right"></div>
            <a class="spoiler right spoiler-down" href="javascript:void(0);"></a>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr class="even hidden">
        <td colspan="2">
            <strong>Для выбора абилити нажмите на соответствующую иконку. Выбор абилити доступен с 8-го уровня.</strong>
        </td>
    </tr>
    <?php
    $align = $user->getAlignForAbility();
    foreach (Ability::getAbilityViewList($align) as $ability_type => $ability_list): ?>
        <?php if(empty($ability_list)) continue; ?>
        <tr class="odd hidden">
            <td colspan="2" class="sub-title">
                <?= Ability::getAbilityTitle($ability_type). ' (макс. '.Ability::getAbilityLimit($ability_type).' шт.)' ?>
            </td>
        </tr>
        <tr class="event hidden">
            <td colspan="2">
                <ul class="ability-block">
                    <?php foreach ($ability_list as $id): ?>
                        <?php $_ability = $allAbility[$id]; ?>
                        <li class="clearfix">
                            <div class="ability <?= in_array($id, $userAbility) ? 'active' : 'disable'; ?>">
                                <?php if($user->level < $app->dbConfig->znahar_min_ability || in_array($id, $userAbility) || !$canAbility[$ability_type]): ?>
                                    <img title="<?= $_ability['name'] ?>" src="http://i.oldbk.com/i/magic/<?= $_ability['img']; ?>">
                                <?php else: ?>
                                    <a href="<?= $app->urlFor('znahar', array('action' => Ability::getAbilityKey($ability_type), 'ability' => $id)); ?>">
                                        <img title="<?= $_ability['name'] ?>" src="http://i.oldbk.com/i/magic/<?= $_ability['img']; ?>">
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="description">
                                <?= isset($atext[$id]) ? $atext[$id] : ''; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr class="odd hidden">
        <td colspan="2">
            <em class="center" style="color: red">
                Один раз в 30 дней Вы можете бесплатно сбросить все установленные абилити и выбрать их заново. В остальное время сброс абилити Вам обойдется в <?= $app->dbConfig->znahar_ability_drop_cost ?>кр.
            </em>
            <?php if(!empty($userAbility)): ?>
                <div class="center">
                    <a href="<?= $app->urlFor('znahar', array('action' => 'dropability')) ?>" class="button-big btn" title="Сбросить">Сбросить <?= $free_abil_drop ? 'бесплатно' : ' за <strong>1000</strong> кр' ?></a>
                </div>
            <?php endif; ?>
        </td>
    </tr>
    </tbody>
</table>
