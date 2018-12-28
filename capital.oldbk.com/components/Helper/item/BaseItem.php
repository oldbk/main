<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

namespace components\Helper\item;

use components\models\User;

abstract class BaseItem implements iItemGiveTake
{
    /** @var User */
    protected $owner;

	/**
	 * BaseItem constructor.
	 * @param User $owner
	 */
    public function __construct($owner)
    {
		$this->owner = User::find($owner->id);
    }

    public function getOwner()
	{
		return $this->owner;
	}
}
