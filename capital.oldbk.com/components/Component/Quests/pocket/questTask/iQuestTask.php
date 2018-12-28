<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 04.06.2016
 */

namespace components\Component\Quests\pocket\questTask;


interface iQuestTask
{
    public function check($Checker);
    public function setProcess($process);
    public function getProcess();
    public function getItemType();
    public function getUpCount();
    public function getCount();
}