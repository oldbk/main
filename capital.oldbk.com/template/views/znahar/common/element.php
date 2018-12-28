<?php
use components\models\effect\Element;
use components\Helper\StringHelper;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 *
 * @var \components\models\User $user
 */ ?>

<table class="table border" cellspacing="0" cellpadding="0">
    <colgroup>
        <col width="20%">
        <col width="20%">
        <col width="20%">
        <col width="20%">
        <col width="20%">
    </colgroup>
    <thead>
    <tr class="head-line spoiler-block">
        <th colspan="5">
            <div class="head-left"></div>
            <div class="head-title">Добавление магии стихий</div>
            <div class="head-right"></div>
            <a class="spoiler right spoiler-down" href="javascript:void(0);"></a>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr class="even hidden">
        <td colspan="5">
            Вы можете добавить к своим возможностям еще одну стихийную магию на определенный срок. Добавить к родной магии можно только одну дополнительную.
            В любой момент можно заменить выбранную дополнительную магию на другую, оплатив этот процесс.
        </td>
    </tr>
    <?php
    $user_element = $user->getMagStih();
    ?>
    <tr class="odd hidden">
        <td colspan="5" class="sub-title center">
            Ваша родная магия - "<?= Element::getTitle($user_element) ?>". Какую магию Вы хотите добавить?
        </td>
    </tr>
    <tr class="even hidden">
        <td colspan="5"></td>
    </tr>
    <tr class="odd hidden">
        <td></td>
        <?php foreach(Element::getPrices() as $day => $price): ?>
            <td class="center">
                На <strong><?= $day ?></strong> <?= StringHelper::dayEnding($day); ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <?php foreach (Element::getTitles() as $element_type => $title): ?>
        <?php if($element_type == $user_element) continue; ?>
        <tr class="odd hidden">
            <td class="center" style="vertical-align: middle">
                <strong><?= $title ?></strong>
            </td>
            <?php foreach(Element::getPrices() as $day => $price): ?>
                <td class="center">
                    <div>
                        <a href="<?= $app->urlFor('znahar', array(
                            'action' => 'buyelement',
                            'element' => $element_type,
                            'day' => $day,
                        )) ?>"><img src="<?= Element::getImage($element_type) ?>"></a>
                    </div>
                    <div class="size11"><strong><?= $price ?></strong> екр.</div>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
