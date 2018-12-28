<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 11.05.2016
 */

namespace components\Component\Loto;

use components\models\ItemLoto;
use components\models\User;

class LotoView extends BaseLoto
{
    public function run()
    {
        $this->buildItemList();

        foreach ($this->item_list as $loto_item_id => $item) {
            foreach ($item->getViewItems() as $item_prototype) {
                $item_prototype['no'] = 1;
                $item_prototype['count'] = 1;
                $item_prototype['avacount'] = 1;
                $item_prototype['angcount'] = 1;
                $item_prototype['getfrom'] = 0;
                $this->item_view[$item->getCategory()][] = $item_prototype;
            }
        }
    }

    public function getLastWin()
    {
        //@TODO exclude admins
		$WinList = ItemLoto::where('loto', '=', $this->loto_id - 1)
			->whereNotIn('owner', [14897])
			->orderBy('cost_ekr', 'desc')
			->orderBy('item_name', 'desc')
			->limit(100)
			->get(['id', 'owner', 'item_name'])
			->toArray();

        $user_ids = array();
        foreach ($WinList as $item) {
            if(!in_array($item['owner'], $user_ids)) {
                $user_ids[] = $item['owner'];
            }
        }

        if(empty($user_ids)) {
            return array();
        }

        $user_login_list = array();
        /** @var User[] $UserList */
		$UserList = User::whereIn('id', $user_ids)->get();
        foreach ($UserList as $User) {
            $user_login_list[$User->id] = $User;
        }

        $returned = array();
        foreach ($WinList as $item) {
            $returned[] = array(
                'user'      => $user_login_list[$item['owner']],
                'ticket_id' => $item['id'],
                'item_name' => $item['item_name'],
            );
        }

        return $returned;
    }
}