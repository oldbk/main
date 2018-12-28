<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 03.09.17
 * Time: 16:53
 */

namespace components\Component\Db;

use Illuminate\Database\Capsule\Manager;
class CapitalDb extends Manager
{
	/**
	 * @param string $connection
	 * @return \Illuminate\Database\Connection
	 */
	public static function connection($connection = 'capital')
	{
		return static::$instance->getConnection($connection);
	}

	/**
	 * @param string $table
	 * @param string $connection
	 * @return \Illuminate\Database\Query\Builder
	 */
	public static function table($table, $connection = 'capital')
	{
		return static::$instance->connection($connection)->table($table);
	}

	/**
	 * Get a schema builder instance.
	 *
	 * @param  string  $connection
	 * @return \Illuminate\Database\Schema\Builder
	 */
	public static function schema($connection = 'capital')
	{
		return static::$instance->connection($connection)->getSchemaBuilder();
	}

	/**
	 * Get a registered connection instance.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Database\Connection
	 */
	public function getConnection($name = 'capital')
	{
		return $this->manager->connection($name);
	}
}