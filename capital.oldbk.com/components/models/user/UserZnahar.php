<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 */

namespace components\models\user;

use components\Helper\StatsHelper;
use components\models\User;

class UserZnahar extends User
{
    const ZNAHAR_FREE_LEVEL = 3;
    const ZNAHAR_RATIO_STATS        = 1;
    const ZNAHAR_RATIO_STATS_NEWBIE = 0;

    /**
     * @return int
     */
    public function getMoneyStatsForZnahar()
    {
        return (int)(($this->sila + $this->lovk + $this->inta + $this->vinos + $this->intel + $this->mudra - 9 - $this->level + 3) * 4);
    }

    /**
     * @return int
     */
    public function getMoneyMasterForZnahar()
    {
        return (int)(($this->noj + $this->mec + $this->topor + $this->dubina + $this->mfire + $this->mwater + $this->mair + $this->mearth + $this->mlight + $this->mgray + $this->mdark) * 4);
    }

    public function getZnaharRatioStats()
    {
        return $this->level > static::ZNAHAR_FREE_LEVEL ? static::ZNAHAR_RATIO_STATS : static::ZNAHAR_RATIO_STATS_NEWBIE;
    }

    public function checkMinStat($num)
    {
        $add = 0;
        if($this->level == 9) {
            $add = 1;
        } elseif($this->level == 10) {
            $add = 3;
        }

        switch($num) {
            case StatsHelper::STAT_SILA:
                return $this->sila < (4 + $this->bpbonussila);
                break;
            case StatsHelper::STAT_LOVK:
                return $this->lovk < 4;
                break;
            case StatsHelper::STAT_INTA:
                return $this->inta < 4;
                break;
            case StatsHelper::STAT_VINOS:
                return $this->vinos < (4 + $this->level + $add);
                break;
            case StatsHelper::STAT_INTEL:
                return $this->intel < 1;
                break;
            case StatsHelper::STAT_MUDRA:
                return $this->mudra < 1;
                break;
        }

        return false;
    }

    public function canMoveTo($num)
    {
        switch($num) {
            case StatsHelper::STAT_INTEL:
                return $this->level >= 4;
                break;
            case StatsHelper::STAT_MUDRA:
                return $this->level >= 7;
                break;
        }

        return true;
    }

    public function getCost($num)
    {
        $key = StatsHelper::getKeyById($num);

        return $this->{$key} <= 10 ? 5 : $this->{$key} / 2 + 0.5;
    }

    public function takeStat($num, $count = 1)
    {
        $stat = StatsHelper::getKeyById($num);
        $this->takeParam($stat, $count);

        return $this;
    }

    public function addStat($num, $count = 1)
    {
        $stat = StatsHelper::getKeyById($num);
        $value = (int)$this->{$stat};

        $this->{$stat} = $value + (int)$count;

        return $this;
    }
}