<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Bank
 * @package components\models
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $updated_at
 * @property int $created_at
 * @property int $is_group
 * @property int $is_enabled
 *
 */
class ConfigKoMain extends Model
{
	protected $table = 'config_ko_main';
	protected $primaryKey = 'id';
}