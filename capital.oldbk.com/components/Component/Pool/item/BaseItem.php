<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.03.2016
 */

namespace components\Component\Pool\item;

abstract class BaseItem implements iItem
{
    const ITEM_TYPE_ITEM            = 'item';
    const ITEM_TYPE_CUSTOM_ITEM     = 'custom_item';
    const ITEM_TYPE_ABILITY_OWN     = 'ability';
    const ITEM_TYPE_EXP             = 'exp';
    const ITEM_TYPE_REPA            = 'repa';
    const ITEM_TYPE_KR              = 'kr';
    const ITEM_TYPE_EKR             = 'ekr';
    const ITEM_TYPE_MEDAL           = 'medal';
    const ITEM_TYPE_WEIGHT          = 'weight';
    const ITEM_TYPE_PROF_EXP        = 'prof_exp';

    public $name;
	/** @var int */
    public $give_count = 1;

    protected $pool_id;
    protected $pool_pocket_id;
    protected $pool_pocket_item_id;

    /**
     * @param $type
     * @return iItem
     */
    public static function getItemInfo($type)
    {
        $type = str_replace(' ', '', ucwords(str_replace('_', ' ', $type)));
        $className = sprintf('components\Component\Pool\item\Pool%s', ucfirst($type));

		return new $className();
    }

    public function populate(array $attributes)
    {
        foreach ($attributes as $field => $value) {
            if(property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

	/**
	 * @param $give_count
	 * @return $this
	 */
    public function setGiveCount($give_count)
	{
		$this->give_count = (int)$give_count;
		return $this;
	}

	/**
	 * @param mixed $pool_id
	 * @return $this
	 */
	public function setPoolId($pool_id)
	{
		$this->pool_id = $pool_id;
		return $this;
	}

	/**
	 * @param mixed $pool_pocket_id
	 * @return $this
	 */
	public function setPoolPocketId($pool_pocket_id)
	{
		$this->pool_pocket_id = $pool_pocket_id;
		return $this;
	}

	/**
	 * @param mixed $pool_pocket_item_id
	 * @return $this
	 */
	public function setPoolPocketItemId($pool_pocket_item_id)
	{
		$this->pool_pocket_item_id = $pool_pocket_item_id;
		return $this;
	}
}