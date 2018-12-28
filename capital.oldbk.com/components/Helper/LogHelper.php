<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 10/29/18
 * Time: 1:01 PM
 */

namespace components\Helper;


use components\models\User;

class LogHelper
{
	private $_battleId;

	public function __construct($battleId)
	{
		$this->_battleId = $battleId;
	}

	public function startFight(User $User, User $Enemy)
	{
		$string = sprintf("!:S:%d:%s:%s\n", time(), $User->logRaw(), $Enemy->logRaw());

		$this->addToLog($string);
	}

	public function addUserToFight(User $User)
	{
		$ac = ($User->sex * 100) + mt_rand(1,2);
		$string = sprintf("!:W:%d:%s:%d:%d\n", time(), $User->logRaw(), $User->battle_t, $ac);

		$this->addToLog($string);
	}

	public function addToLog($string)
	{
		$string = trim($string);
		if($string == '') {
			return;
		}

		$string = $string."\n";

		$filename = sprintf('/www/data/combat_logs/%d000/battle%d.txt', $this->_battleId/1000, $this->_battleId);
		$dir = dirname($filename);
		if(!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}

		$fp = fopen ($filename,"a"); //открытие
		flock ($fp,LOCK_EX); //БЛОКИРОВКА ФАЙЛА
		fputs($fp , $string); //работа с файлом
		fflush ($fp); //ОЧИЩЕНИЕ ФАЙЛОВОГО БУФЕРА И ЗАПИСЬ В ФАЙЛ
		flock ($fp,LOCK_UN); //СНЯТИЕ БЛОКИРОВКИ
		fclose ($fp); //закрытие
	}
}