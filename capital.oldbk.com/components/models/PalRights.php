<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\models;
use components\models\_base\BaseModal;

/**
 * Class Chat
 * @package components\Model
 *
 * @property int $pal_id
 * @property int $logs
 * @property int $ext_logs
 * @property int $red_forum
 * @property int $top_move
 * @property string $abils
 * @property int $klans_ars_logs
 * @property int $klans_ara_put
 * @property int $klans_kazna_logs
 * @property int $klans_kazna_view
 * @property int $pals_delo
 * @property int $pals_online
 * @property int $pal_tel
 * @property int $zhhistory
 * @property int $loginip
 * @property int $viewmanyips
 *
 */
class PalRights extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'pal_rights';
	protected $primaryKey = 'pal_id';
}