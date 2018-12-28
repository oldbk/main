<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Bank
 * @package components\Model
 *
 * @method $this|$this[] asModel()
 *
 * @property int $id
 * @property int $step
 * @property string $describe
 * @property string $qname
 * @property int $qtype
 * @property int $q_fight_type
 * @property string $qstart
 * @property int $answer_1
 * @property int $answer_2
 * @property string $qfin
 * @property int $q_have_to_win
 * @property string $qsys_fail
 * @property string $qcondition
 * @property int $q_item
 * @property int $q_item_count
 * @property int $q_item2
 * @property int $q_item2_count
 * @property int $q_item3
 * @property int $q_item3_count
 * @property int $q_item4
 * @property int $q_item4_count
 * @property int $q_bot
 * @property int $q_bot_count
 * @property int $exp
 * @property int $kr
 * @property int $repa
 * @property string $shop_prize
 * @property int $shop_prize_gift
 * @property string $eshop_prize
 * @property int $eshop_prize_gift
 * @property string $effect_prize
 * @property int $repeat
 * @property string $info
 * @property string $nps_img
 *
 */
class BeginersQuest extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'beginers_quests';
	protected $primaryKey = 'id';
}