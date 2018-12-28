<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.01.2016
 */

require_once __DIR__ . '/../bootstrap_cli.php';

$quest_date = new \DateTime();
$quest_date->modify('-1 day')
    ->setTime(01, 00, 00);

$today_date = new \DateTime();
//quest_date это вчерашняя дата. Смотрим чтоб она входила в диапазон начала и конца квеста. И проверяем так же сегодняшнюю дату
//если квест закончился, тогда выбираем его
$QuestList = \components\Model\quest\QuestList::findAll('quest_type = "daily" and is_enabled = 1')->asArray();

$quest_ids = array();
foreach ($QuestList as $Quest) {
    $quest_ids[] = $Quest['id'];
}

$Conditions = \components\Model\quest\QuestCondition::findAll(
    'item_id in ('.\components\Model\quest\QuestCondition::getIN($quest_ids).') and condition_type = "date" and item_type = ?',
    array_merge($quest_ids, array(\components\Model\quest\QuestCondition::ITEM_TYPE_QUEST))
)->asArray();
$condition_list = array();
foreach ($Conditions as $Condition) {
    $condition_list[$Condition['item_id']][$Condition['group']][$Condition['field']] = $Condition['value'];
}

$end_quest_ids = array();
foreach ($condition_list as $quest_id => $conditions) {
    foreach ($conditions as $group => $data) {
        $current = new \DateTime();

        $datestart = new \DateTime($data['date']);
        $datestart->setTime(0,0);

        $dateend = new \DateTime($data['date']);
        $dateend->setTime(23,59,59);

        if(isset($data['date']) && $datestart < $quest_date && $dateend > $quest_date) {
            $end_quest_ids[] = $quest_id;
        }
    }
}

//обновляем все связи квест <=> пользователь, если он его не завершил. Ставим, что он его проебал
if($end_quest_ids) {
    \components\Model\quest\UserQuest::update(array('is_end' => 1), 'quest_id in ("'.implode('","', $end_quest_ids).'") and is_finished = 0');
}