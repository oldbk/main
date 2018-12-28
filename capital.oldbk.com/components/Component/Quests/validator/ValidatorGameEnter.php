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
use components\models\Iplog;

class ValidatorGameEnter extends BaseValidator
{
    public $first_all;
    public $first_day;

    public function getCheckerTypes()
    {
        return array(
            BaseChecker::ITEM_TYPE_EVENT
        );
    }

    /**
     * @param iChecker|CheckerEvent $Checker
     * @return bool
     */
    public function check($Checker)
    {
        $process = $Checker->getProcess();
        $user = $Checker->getUser();

        if($this->first_all) {
            $count = Iplog::where('owner', '=', $user->id)->count();
            if($count == 1) {
                return true;
            }

            return false;
        }

        if($this->first_day) {
            $datetime = new \DateTime();
            $datetime->setTime(0,0);
            $datetime2 = new \DateTime();
            $datetime2->setTime(23,59,59);
            foreach ($process as $item) {
                if($item == $datetime->getTimestamp()) {
                    return false;
                }
            }

			$count = Iplog::whereRaw('owner = ? and date >= ? and date <= ?', [$user->id, $datetime->getTimestamp(), $datetime2->getTimestamp()])
				->count();
            if($count == 1) {
                $process[] = $datetime->getTimestamp();
                $Checker->setProcess($process);
                return true;
            }

            return false;
        }

        return true;
    }
}