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
 * @property string $coment
 * @property string $teams
 * @property int $timeout
 * @property int $type
 * @property int $status
 * @property string $t1
 * @property string $t2
 * @property string $t3
 * @property string $date
 * @property int $win
 * @property string $damage
 * @property int $to1
 * @property int $to2
 * @property int $to3
 * @property string $exp
 * @property int $blood
 * @property string $t1hist
 * @property string $t2hist
 * @property string $t3hist
 * @property int $status_flag
 * @property string $t1_dead
 * @property int $fond
 * @property int $price
 * @property int $nomagic
 * @property int $CHAOS
 * @property int $war_id
 * @property int $inf
 *
 */
class Battle extends BaseModal
{
	protected $connection = 'capital';
	protected $table = 'battle';
	protected $primaryKey = 'id';

	const FIGHT_ALIGN 			= 'fight_align';
	const FIGHT_HAOS 			= 'fight_haos';
	const FIGHT_DRAGON 			= 'fight_dragon';
	const FIGHT_AUTO 			= 'fight_auto';
	const FIGHT_KUCHA 			= 'fight_kucha';
	const FIGHT_ELKA 			= 'fight_elka';
	const FIGHT_ARENA 			= 'fight_arena';
	const FIGHT_ZAGA 			= 'fight_zaga';
	const FIGHT_CP 				= 'fight_cp';
	const FIGHT_FLOWER 			= 'fight_flower';
	const FIGHT_FIZ 			= 'fight_fiz';
	const FIGHT_RUINE_SOKRA 	= 'fight_ruine_sokra';
	const FIGHT_LABA 			= 'fight_laba';
	const FIGHT_BS 				= 'fight_bs';
	const FIGHT_RUINE 			= 'fight_ruine';
	const FIGHT_LORD 			= 'fight_lord';
	const FIGHT_CLAN_TOURNAMENT = 'fight_clan_tournament';

	public $is_win;
	public $damage;
	public $user_damage;

	public static function getTypes()
	{
		return array(
			self::FIGHT_ALIGN,
			self::FIGHT_HAOS,
			self::FIGHT_DRAGON,
			self::FIGHT_AUTO,
			self::FIGHT_KUCHA,
			self::FIGHT_ELKA,
			self::FIGHT_ARENA,
			self::FIGHT_ZAGA,
			self::FIGHT_CP,
			self::FIGHT_FLOWER,
			self::FIGHT_FIZ,
			self::FIGHT_RUINE_SOKRA,
			self::FIGHT_LABA,
			self::FIGHT_BS,
			self::FIGHT_RUINE,
			self::FIGHT_LORD,
			self::FIGHT_CLAN_TOURNAMENT,
		);
	}

	public function getFightKey()
	{
		switch (true) {
			case ($this->type == 3 && $this->coment == 'Бой склонностей'):
				return self::FIGHT_ALIGN;
			case (strpos($this->t2hist, 'Исчадие Хаоса') !== false):
				return self::FIGHT_HAOS;
			case ($this->type == 6 && $this->coment == '<b>Бой с Волнами Драконов</b>'):
			case (strpos($this->t2hist, 'Дракон') !== false):
				return self::FIGHT_DRAGON;
			case ($this->coment == '<b>#zlevels</b>'):
				return self::FIGHT_AUTO;
			case ($this->coment == '<b>Куча-мала</b>'):
				return self::FIGHT_KUCHA;
			case ($this->type == 7):
				return self::FIGHT_ELKA;
			case (in_array($this->type, array(60, 61, 62))):
				return self::FIGHT_ARENA;
			case (in_array($this->type, array(13, 14, 15))):
				return self::FIGHT_ZAGA;
			case ($this->coment == '<b>Бой на Центральной площади</b>'):
				return self::FIGHT_CP;
			case ($this->type == 8):
				return self::FIGHT_FLOWER;
			case ($this->type == 1):
				return self::FIGHT_FIZ;
			case ($this->type == 12 && $this->coment == 'Бой в руинах за сокровища'):
				return self::FIGHT_RUINE_SOKRA;
			case ($this->type == 30 && $this->coment == 'Бой в лабиринте Хаоса'):
				return self::FIGHT_LABA;
			case ($this->type == 1010 && $this->coment == 'Бой в Башне Смерти'):
				return self::FIGHT_BS;
			case ($this->type == 11 && $this->coment == 'Бой в руинах'):
				return self::FIGHT_RUINE;
			case ($this->coment == 'Бой с Лордом Разрушителем'):
				return self::FIGHT_LORD;
			case ($this->type == 66):
				return self::FIGHT_CLAN_TOURNAMENT;
		}

		return null;
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function finishTotally()
	{
		/*
		 * remove all from battle_dam_exp by battle_id
		 * remove all from battle_data by battle_id
		 * remove all from battle_fd by battle_id
		 * remove all from battle_runs_exp by battle_id
		 * remove all from battle_vars by battle_id
		 * remove all from battle_user_time by battle_id
		 * remove all from users_clons by battle_id
		 */

		$user_ids = array_merge(explode(';', $this->t1), explode(';', $this->t2));

		BattleDamExp::where('battle', '=', $this->id)->delete();
		BattleData::where('battle', '=', $this->id)->delete();
		BattleFd::where('battle', '=', $this->id)->delete();
		BattleRunsExp::where('battle', '=', $this->id)->delete();
		BattleVars::where('battle', '=', $this->id)->delete();
		BattleUserTime::where('battle', '=', $this->id)->delete();

		UserClon::where('battle', '=', $this->id)
			->where('owner', '=', 0)
			->delete();

		UserClon::where('battle', '=', $this->id)
			->where('owner', '>', 0)
			->update(['hp' => 0, 'battle' => 0, 'fullentime' => time()]);

		User::whereIn('id', $user_ids)
			->where('battle', '=', $this->id)
			->update(['battle' => 0, 'last_battle' => 0]);

		$this->win = 0;

		return $this->save();
	}
}