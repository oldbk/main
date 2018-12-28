<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 27.08.16
 * Time: 23:18
 */

namespace components\Component\Quests\validator;


use components\Component\Quests\check\BaseChecker;
use components\Component\Quests\check\CheckerEvent;
use components\Component\Quests\check\iChecker;
use components\Helper\FileHelper;

class ValidatorUser extends BaseValidator
{
    public $gender;
    public $level;
    public $align;

    public function getCheckerTypes()
    {
        return BaseChecker::getAllTypes();
    }

    /**
     * @param iChecker|CheckerEvent $Checker
     * @return bool
     */
    public function check($Checker)
    {
        $return = true;
        $user = $Checker->getUser();

        if(is_numeric($this->gender) && $this->gender != $user->sex) {
            $return = false;
        }
        if(is_numeric($this->level) && $this->level != $user->level) {
            $return = false;
        }
        if(is_numeric($this->align) && $this->align != $user->getGlobalAbility()) {
            $return = false;
        }

        return $return;
    }
}