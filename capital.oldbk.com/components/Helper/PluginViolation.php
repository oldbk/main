<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 20.11.16
 * Time: 15:46
 */

namespace components\Helper;

use components\models\effect\Travma;
use components\models\NewDelo;
use components\models\User;

class PluginViolation
{
    /** @var User */
    protected $user;
    protected $message;
    protected $message1 = 'Вы используете ПО (плагин), запрещенное законами ОлдБК. Пожалуйста, удалите ваш текущий плагин и установите официальную версию со страницы плагина в библиотеке игры http://oldbk.com/encicl/?/plug.html';
    protected $message2 = 'Вы всё еще используете плагин, запрещенный законами ОлдБК. Пожалуйста, удалите ваш текущий плагин и установите официальную версию со страницы плагина в библиотеке игры http://oldbk.com/encicl/?/plug.html. В случае повторного нарушения ваш персонаж получит штраф в виде неизлечимой травмы.';
    protected $message3 = 'За использование плагина, запрещенного законами ОлдБК, ваш персонаж получает штраф в виде неизлечимой травмы на 1 час. В случае повторного нарушения срок травмы будет увеличен до 1 года. Установить официальную версию плагина можно со страницы плагина в библиотеке игры http://oldbk.com/encicl/?/plug.html';
    protected $message4 = 'За регулярное использование плагина, запрещенного законами ОлдБК, ваш персонаж получает штраф в виде неизлечимой травмы, 1 год. Для снятия штрафа обращайтесь в Коммерческий отдел проекта http://oldbk.com/commerce/index.php?act=sendmess';

    private $_debug = false;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function make($num)
    {
        switch ($num) {
            case 1:
                return $this->violation1();
                break;
            case 2:
                return $this->violation2();
                break;
            case 3:
                return $this->violation3();
                break;
            case 4:
                return $this->violation4();
                break;
            default:
                return $this->violationDefault();
                break;
        }
    }

    protected function violation1()
    {
        $this->message = $this->message1;

        $_data = array(
            'owner'                 => $this->user->id,
            'owner_login'           => $this->user->login,
            'owner_balans_do'       => $this->user->money,
            'owner_balans_posle'    => $this->user->money,
            'item_count'            => 1,
            'add_info'              => 'Получено предупреждение о запрете использования сторонних плагинов',
            'sdate'                 => time(),
            'type'                  => NewDelo::TYPE_PLUGIN_VIOLATION,
        );

        if($this->_debug === false && !NewDelo::addNew($_data)) {
            return false;
        }

        return true;
    }

    protected function violation2()
    {
        $this->message = $this->message2;

        $_data = array(
            'owner'                 => $this->user->id,
            'owner_login'           => $this->user->login,
            'owner_balans_do'       => $this->user->money,
            'owner_balans_posle'    => $this->user->money,
            'item_count'            => 2,
            'add_info'              => 'Получено повторное предупреждение о запрете использования сторонних плагинов',
            'sdate'                 => time(),
            'type'                  => NewDelo::TYPE_PLUGIN_VIOLATION,
        );

        if($this->_debug === false && NewDelo::addNew($_data) === false) {
            return false;
        }

        return true;
    }

    protected function violation3()
    {
        $this->message = $this->message3;

        $_data = array(
            'owner'                 => $this->user->id,
            'owner_login'           => $this->user->login,
            'owner_balans_do'       => $this->user->money,
            'owner_balans_posle'    => $this->user->money,
            'item_count'            => 3,
            'add_info'              => 'Получено третье предупреждение о запрете использования сторонних плагинов, наложен штраф в виде неизлечимой травмы, на 1 час',
            'sdate'                 => time(),
            'type'                  => NewDelo::TYPE_PLUGIN_VIOLATION,
        );

        if($this->_debug === false && NewDelo::addNew($_data) === false) {
            return false;
        }

        $finishTime = new \DateTime();
        $finishTime->modify('+1 hour');
        if($this->_debug === false && Travma::nelech($this->user->id, $finishTime, array('sila' => 70)) === false) {
            return false;
        }

        return true;
    }

    protected function violation4()
    {
        $this->message = $this->message4;

        $_data = array(
            'owner'                 => $this->user->id,
            'owner_login'           => $this->user->login,
            'owner_balans_do'       => $this->user->money,
            'owner_balans_posle'    => $this->user->money,
            'item_count'            => 4,
            'add_info'              => 'Получено четвертое предупреждение о запрете использования сторонних плагинов, наложен штраф в виде неизлечимой травмы, 1 год',
            'sdate'                 => time(),
            'type'                  => NewDelo::TYPE_PLUGIN_VIOLATION,
        );

        if($this->_debug === false && NewDelo::addNew($_data) === false) {
            return false;
        }

        $finishTime = new \DateTime();
        $finishTime->modify('+1 year');
        if($this->_debug === false && Travma::nelech($this->user->id, $finishTime, array('sila' => 70)) === false) {
            return false;
        }

        return true;
    }

    protected function violationDefault()
    {
        $this->message = $this->message4;

        return true;
    }

    public function getMessage()
    {
        return $this->message;
    }
}