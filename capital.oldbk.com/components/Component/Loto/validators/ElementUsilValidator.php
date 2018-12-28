<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 12.04.2016
 */

namespace components\Component\Loto\validators;


use components\Component\Loto\items\Item;
use components\Helper\ShopHelper;
use components\models\User;

class ElementUsilValidator extends AbstractValidator
{
    protected $prototypes = array();

    protected function run()
    {
        $this->prototypes = ShopHelper::getPrototypes(array(130135, 920925, 150155, 930935), ShopHelper::TYPE_ESHOP);
    }

    /**
     * @return ItemList
     */
    public function view()
    {
        $response = array();
        foreach ($this->prototypes as $prototype) {
            $response[] = new Item($this->app, $this->loto_id, $this->item, $prototype);
        }
        
        return $response;
    }

    /**
     * @param array $owner
     * @return mixed|null
     */
    public function loto(array $owner)
    {
        $User = new User($owner);


        $_ids = array(
            User::STIH_AIR      => 130135,
            User::STIH_EARTH    => 920925,
            User::STIH_FIRE     => 150155,
            User::STIH_WATER    => 930935,
        );

        $key = $User->smagic > 0 ? $User->smagic : $User->getMagStih();
        $user_element_id = $_ids[$key];
        unset($User);
        
        foreach ($this->prototypes as $prototype) {
            if($prototype['id'] == $user_element_id) {
                return new Item($this->app, $this->loto_id, $this->item, $prototype);
            }
        }

        return null;
    }
}