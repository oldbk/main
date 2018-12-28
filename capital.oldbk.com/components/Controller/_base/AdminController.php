<?php
namespace components\Controller\_base;

/**
 * Created by PhpStorm.
 */

abstract class AdminController extends MainController
{
    public function beforeAction($action)
    {
        $r = parent::beforeAction($action);

        if(!$this->user || !in_array($this->user->klan, array('Adminion', 'radminion'))) {
            $this->errorUser();
        }

        return $r;
    }
}