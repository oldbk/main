<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 26.09.2018
 * Time: 11:53
 */

namespace components\models\dailyFree;


use components\models\DailyFree;

class DailyFreeFontan extends DailyFree
{
	public static function boot()
	{
		parent::boot();

		self::creating(function($model) {
			/** @var DailyFreeFontan $model */
			$model->essence = DailyFreeFontan::ESSENCE_FONTAN;
		});

		static::addGlobalScope(function ($query) {
			$query->where('essence', '=', DailyFreeFontan::ESSENCE_FONTAN);
		});
	}

	/**
	 * @return int
	 */
	public function getNextAddedTimestamp()
	{
		return (new \DateTime())->setTimestamp($this->added_at)->modify('+20 minute')->getTimestamp();
	}

	/**
	 * @return int
	 */
	public function getAvailable()
	{
		return $this->uses;
	}

	/**
	 * @param array $attributes
	 * @return DailyFreeFontan
	 */
	public function firstOrNew($attributes)
	{
		return parent::firstOrNew($attributes, [
			//'essence' => DailyFreeFontan::ESSENCE_FONTAN,
			'uses'				=> 10,
			'limit_uses' 		=> 10,
			'limit_used_total' 	=> 0,
			'added_at' 			=> time(),
		]);
	}
}