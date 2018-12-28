<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 10/23/18
 * Time: 8:33 PM
 */

namespace components\Component\Slim;


class Request extends \Slim\Http\Request
{
	public function isQueryEmpty()
	{
		return !isset($this->env['QUERY_STRING']) || !$this->env['QUERY_STRING'];
	}
}