<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 10.10.16
 * Time: 14:36
 */

namespace components\Component\Quests;

use components\Component\Db\CapitalDb;
use components\Component\VarDumper;
use components\Helper\FileHelper;

/**
 * Class QuestManual
 * @package components\Component\Quests
 */
class QuestManual extends BaseQuest
{
    public function init()
    {
        // TODO: Implement init() method.
    }


    /**
     * @param $quest_id
     * @return bool
     */
    public function manualFinishQuest($quest_id)
    {
        $db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            $Quest = $this->getUserQuestObj()->getQuest($quest_id);
            if(!$Quest) {
                return false;
            }

            if($this->tryFinishQuest($Quest->id) == false) {
                throw new \Exception();
            }

            $db->commit();

            return true;
        } catch (\Exception $ex) {
            $db->rollBack();
            FileHelper::writeException($ex, 'quest_manualFinishQuest');
        }

        return false;
    }

    /**
     * @param $quest_id
     * @return bool
     *
     * Завершеам активную часть
     * Если нет активных частей - ошибка
     */
    public function manualFinishPart($quest_id)
    {
        $Quest = $this->getUserQuestObj()->getQuest($quest_id);
        if(!$Quest) {
            return false;
        }

        $PartNeedEnd = null;
        foreach ($Quest->part as $Part) {
            if(!$Part->isStarted()) {
                continue;
            }

            $PartNeedEnd = $Part;
        }

		$db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            if($PartNeedEnd && ($this->canFinishPart($Quest->id, $PartNeedEnd->id, false) == false || $this->tryFinishPart($Quest->id, $PartNeedEnd->id) == false)) {
                throw new \Exception();
            }

            if($this->canFinishQuest($Quest->id) == true && $this->tryFinishQuest($Quest->id) == false) {
                throw new \Exception();
            }

            $db->commit();

            return true;
        } catch (\Exception $ex) {
            $db->rollBack();
            FileHelper::writeException($ex, 'quest_manualFinishPart');
        }

        return false;
    }

	public function forceFinishPart($quest_id, $part_id)
	{
		$Part = $this->getUserQuestObj()->getPart($quest_id, $part_id);
		if($Part === false) {
			return false;
		}

		if($Part->is_finished || !$Part->is_started) {
			return true;
		}

		try {
			if($this->finishPart($Part) === false) {
				throw new \Exception("Can't finish part");
			}

			return true;
		} catch (\Exception $ex) {
			FileHelper::writeException($ex, 'quest_tryFinishPart');
		}

		return false;
	}

    /**
     * @param $quest_id
     * @return bool
     */
    public function manualStartNextPart($quest_id)
    {
        $Quest = $this->getUserQuestObj()->getQuest($quest_id);
        if(!$Quest) {
            return false;
        }

		$db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            $position = 0;
            foreach ($Quest->part as $Part) {
                if(!$Part->isStarted()) {
                    continue;
                }

                if($this->canFinishPart($Quest->id, $Part->id, false) == false || $this->tryFinishPart($Quest->id, $Part->id) == false) {
                    throw new \Exception('1');
                }

                $position = $Part->part_number + 1;
                break;
            }

            if(!$position) {
                throw new \Exception('2');
            }

            $PartNext = null;
            foreach ($Quest->part as $Part) {
                if($Part->part_number != $position) {
                    continue;
                }

                $PartNext = $Part;
                break;
            }

            if(!$PartNext || $this->startPart($PartNext) == false) {
                throw new \Exception('3');
            }

            $db->commit();
            return true;
        } catch (\Exception $ex) {
            $db->rollBack();
            FileHelper::writeException($ex, 'quest_manualStartNextPart');
        }

        return false;
    }

    /**
     * @param $quest_id
     * @return bool
     *
     * Стартуем следующую часть.
     * Если есть запущенные части - ошибка
     * Если все части уже закончены - ошибка
     */
    public function manualStartPart($quest_id)
    {
        $Quest = $this->getUserQuestObj()->getQuest($quest_id);
        if(!$Quest) {
            return false;
        }

        $parts = array();
        foreach ($Quest->part as $Part) {
            if($Part->isStarted()) {
                return false;
            }

            if($Part->is_finished) {
                continue;
            }

            $parts[$Part->part_number] = $Part;
        }

        if(!$parts) {
            return false;
        }

        ksort($parts);
        $PartNeedStart = array_shift($parts);
        unset($parts);

		$db = CapitalDb::connection();
        $db->beginTransaction();
        try {
            if(!$this->startPart($PartNeedStart)) {
                throw new \Exception();
            }

            $db->commit();

            return true;
        } catch (\Exception $ex) {
            $db->rollBack();
            FileHelper::writeException($ex, 'quest_manualStartPart');
        }

        return false;
    }
}