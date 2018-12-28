<?php

namespace components\Helper;

use Carbon\Carbon;
use components\Eloquent\Inventory;
use components\Eloquent\NewDelo;
use components\Eloquent\NewDeloItIndex;
use components\Eloquent\Shop;
use components\Eloquent\Telegraph;
use components\Eloquent\XmlData;


/**
 * Class Oldbk
 * @package components\Helper
 */
class Oldbk
{

    /**
     * @param $text
     * @param $who
     * @param $user
     * @return mixed
     */
    public static function sendPrivate($text, $who, $user)
    {
        return ToChat::sendPrivate($text, $who, $user);
    }

    /**
     * @param $text
     * @param $user
     * @return mixed
     */
    public static function sendSystems($text, $user)
    {
        return ToChat::sendSys($text, $user);
    }

    /**
     * @param $txt
     * @param $frend_ids
     * @return mixed
     */
    public static function sendGroup($txt, $frend_ids)
    {
        return ToChat::sendGroup($txt, $frend_ids);
    }

    /**
     * @param $user
     * @return bool
     */
    public static function itemsTimeOut($user)
    {

        $items = Inventory::where('owner', $user['id'])
            ->where('dategoden', '>', 0)
            ->where('dategoden', '<=', Carbon::now()->addDay()->timestamp)
            ->get();


        if ($items) {

            $mtext = [];

            foreach ($items as $item) {
                $mtext[] = ' «' . static::linkForItem($item) . '» - предмет исчезнет через <b>' . static::prettyTime(null, $item['dategoden']) . '</b>';
            }

            if (count($mtext) > 0) {
                static::telegraph($user, '<font color=red>Внимание!</font> Заканчивается срок годности предметов:' . implode(",", $mtext));
                return true;
            }

        }

        return false;
    }

    /**
     * @param $item
     * @param bool $retpath
     * @return mixed|string
     */
    public static function linkForItem($item, $retpath = false)
    {
        $ehtml = str_replace('.gif', '', $item['img']);

        $razdel = array(
            1 => "kasteti", 11 => "axe", 12 => "dubini", 13 => "swords", 14 => "bow", 2 => "boots", 21 => "naruchi", 22 => "robi", 23 => "armors",
            24 => "helmet", 3 => "shields", 4 => "clips", 41 => "amulets", 42 => "rings", 5 => "mag1", 51 => "mag2", 6 => "amun", 61 => 'eda', 72 => ''
        );

        $xx = $item['otdel'] == '' ? $item['razdel'] : $item['otdel'];

        if ($item['type'] == 30) {
            $razdel[$xx] = "runs/" . $ehtml;
        } elseif (isset($razdel[$xx]) && $razdel[$xx] == '') {

            $vau4 = [];
            $dola = array(5001, 5002, 5003, 5005, 5010, 5015, 5020, 5025);

            if (in_array($item['prototype'], $vau4)) {
                $razdel[$xx] = 'vaucher';
            } elseif (in_array($item['prototype'], $dola)) {
                $razdel[$xx] = 'earning';
            } else {
                $oskol = array(15551, 15552, 15553, 15554, 15555, 15556, 15557, 15558, 15561, 15562, 15568, 15563, 15564, 15565, 15566, 15567);
                if (in_array($item['prototype'], $oskol)) {
                    $razdel[$xx] = "amun/" . $ehtml;
                } else {
                    $razdel[$xx] = 'predmeti/' . $ehtml;
                }
            }
        } else {
		if (isset($razdel[$xx])) {
	             $razdel[$xx] = $razdel[$xx] . "/" . $ehtml;
		} else {
	             $razdel[$xx] = "/" . $ehtml;
		}
        }

        if (($item['art_param'] != '') and ($item['type'] != 30)) {
            if ($item['arsenal_klan'] != '') {
                // клановый
                $razdel[$xx] = 'art_clan';
            } elseif ($item['sowner'] != 0) {
                //личный
                $razdel[$xx] = 'art_pers';
            }
        }

        if ($retpath) {
            return $razdel[$xx];
        }

        return "<a href=https://oldbk.com/encicl/" . $razdel[$xx] . ".html target=_blank>" . $item['name'] . "</a>";
    }

    /**
     * @param null $start_timestamp
     * @param null $end_timestamp
     * @return null|string
     */
    public static function prettyTime($start_timestamp = null, $end_timestamp = null)
    {
        $start_datetime = new \DateTime();
        if ($start_timestamp !== null) {
            $start_datetime->setTimestamp($start_timestamp);
        }

        $end_datetime = new \DateTime();
        if ($end_timestamp !== null) {
            $end_datetime->setTimestamp($end_timestamp);
        }

        if (($end_datetime->getTimestamp() - $start_datetime->getTimestamp()) <= 60) {
            return 'менее минуты';
        }

        $interval = $end_datetime->diff($start_datetime);

        $time_type = array(
            'm' => '%m мес.',
            'd' => '%d дн.',
            'h' => '%h ч.',
            'i' => '%i мин.',
        );
        $format_arr = array();
        foreach ($time_type as $property => $format) {
            if ($interval->{$property} != 0) {
                $format_arr[] = $format;
            }
        }

        if (empty($format_arr)) {
            return null;
        }

        return $interval->format(implode(' ', $format_arr));
    }

    /**
     * @param $recipient
     * @param $text
     */
    public static function telegraph($recipient, $text)
    {

        if ($recipient['odate'] >= (Carbon::now()->subMinutes(1)->timestamp)) {
            static::sendPrivate($text . '  ', '{[]}' . $recipient['login'] . '{[]}', $recipient);
        } else {
            // если в офе
            Telegraph::create([
                'owner' => $recipient['id'],
                'date' => '',
                'text' => '[' . Carbon::now()->format("d.m.Y H:i") . '] ' . $text . '  ',
            ]);
        }

    }

    /**
     * @param $presents
     * @param $user
     * @return array
     */
    public static function doPresent($presents, $user)
    {
        $getitemsText = [];

        foreach ($presents as $present) {

            /**
             * $present (array)
             * 0 - proto
             * 1 - present author
             * 2 - info(item name)
             * 3 - goden days
             * 4 - count
             * 5 - getform
             * 6 - sys message
             * 7 - not sell
             */
            $getitems = static::dropBonusItems($present[0], $user, $present[1], $present[2], $present[3], $present[4], $present[5], $present[6], $present[7]);

            if ($getitems !== false) {
                $getitemsText[] = $getitems;
            }

        }

        return $getitemsText;
    }

    /**
     * @param $proto
     * @param $user
     * @param $pres
     * @param $info
     * @param int $goden_days
     * @param int $count
     * @param int $getfrom
     * @param bool $sysm
     * @param bool $notsell
     * @return bool|string
     */
    public static function dropBonusItems($proto, $user, $pres, $info, $goden_days = 0, $count = 1, $getfrom = 20, $sysm = false, $notsell = false)
    {
        $dress = Shop::find($proto);

        if ($dress) {

            if ($pres == '') {
                $pres = 'Удача';
            }

            $dress['getfrom'] = $getfrom;
            $dress['cost'] = 0;
            $dress['ecost'] = 0;
            if ($goden_days > 0) {
                $dress['goden'] = $goden_days;
            }
            if ($notsell == true) {
                $dress['notsell'] = 1;
            }

            $dress['dategoden'] = (($dress['goden']) ? ($dress['goden'] * 24 * 60 * 60 + time()) : "");

            $aitems = [];

            for ($i = 1; $i <= $count; $i++) {

                $newItem = Inventory::create([
                    'prototype' => $dress['id'],
                    'owner' => $user['id'],
                    'name' => $dress['name'],
                    'type' => $dress['type'],
                    'massa' => $dress['massa'],
                    'cost' => $dress['cost'],
                    'ecost' => $dress['ecost'],
                    'img' => $dress['img'],
                    'maxdur' => $dress['maxdur'],
                    'isrep' => $dress['isrep'],
                    'gsila' => $dress['gsila'],
                    'glovk' => $dress['glovk'],
                    'ginta' => $dress['ginta'],
                    'gintel' => $dress['gintel'],
                    'ghp' => $dress['ghp'],
                    'gnoj' => $dress['gnoj'],
                    'gtopor' => $dress['gtopor'],
                    'gdubina' => $dress['gdubina'],
                    'gmech' => $dress['gmech'],
                    'gfire' => $dress['gfire'],
                    'gwater' => $dress['gwater'],
                    'gair' => $dress['gair'],
                    'gearth' => $dress['gearth'],
                    'glight' => $dress['glight'],
                    'ggray' => $dress['ggray'],
                    'gdark' => $dress['gdark'],
                    'needident' => $dress['needident'],
                    'nsila' => $dress['nsila'],
                    'nlovk' => $dress['nlovk'],
                    'ninta' => $dress['ninta'],
                    'nintel' => $dress['nintel'],
                    'nmudra' => $dress['nmudra'],
                    'nvinos' => $dress['nvinos'],
                    'nnoj' => $dress['nnoj'],
                    'ntopor' => $dress['ntopor'],
                    'ndubina' => $dress['ndubina'],
                    'nmech' => $dress['nmech'],
                    'nfire' => $dress['nfire'],
                    'nwater' => $dress['nwater'],
                    'nair' => $dress['nair'],
                    'nearth' => $dress['nearth'],
                    'nlight' => $dress['nlight'],
                    'ngray' => $dress['ngray'],
                    'ndark' => $dress['ndark'],
                    'mfkrit' => $dress['mfkrit'],
                    'mfakrit' => $dress['mfakrit'],
                    'mfuvorot' => $dress['mfuvorot'],
                    'mfauvorot' => $dress['mfauvorot'],
                    'bron1' => $dress['bron1'],
                    'bron2' => $dress['bron2'],
                    'bron3' => $dress['bron3'],
                    'bron4' => $dress['bron4'],
                    'maxu' => $dress['maxu'],
                    'minu' => $dress['minu'],
                    'magic' => $dress['magic'],
                    'nlevel' => $dress['nlevel'],
                    'nalign' => $dress['nalign'],
                    'dategoden' => $dress['dategoden'],
                    'goden' => $dress['goden'],
                    'nsex' => $dress['nsex'],
                    'otdel' => $dress['razdel'],
                    'present' => $pres,
                    'labonly' => 0,
                    'labflag' => 0,
                    'group' => $dress['group'],
                    'idcity' => $user['id_city'],
                    'letter' => $dress['letter'],
                    'ekr_flag' => $dress['ekr_flag'],
                    'img_big' => $dress['img_big'],
                    'rareitem' => $dress['rareitem'],
                    'getfrom' => $dress['getfrom'],
                    'notsell' => $dress['notsell']
                ]);

                if ($newItem) {
                    $aitems[] = "cap" . $newItem->id;
                }

            }

            if (count($aitems) > 0) {
                //add to new delo
                $rec['owner'] = $user['id'];
                $rec['owner_login'] = $user['login'];
                $rec['owner_balans_do'] = $user['money'];
                $rec['owner_balans_posle'] = $user['money'];
                $rec['target'] = 0;
                if ($getfrom == 37) {
                    $rec['target_login'] = 'Коллекции';
                } else {
                    $rec['target_login'] = 'Онлайн';
                }
                $rec['type'] = 699;//получил !!!

                if (count($aitems) == 1) {
                    $rec['item_id'] = $aitems[0];
                } else {
                    $rec['aitem_id'] = implode(",", $aitems);
                }
                $rec['item_name'] = $dress['name'];
                $rec['item_count'] = $count;
                $rec['item_type'] = $dress['type'];
                $rec['item_cost'] = $dress['cost'];
                $rec['item_ecost'] = $dress['ecost'];
                $rec['item_dur'] = $dress['duration'];
                $rec['item_maxdur'] = $dress['maxdur'];
                $rec['battle'] = $user['battle'];
                $rec['add_info'] = $info;
                static::addToNewDelo($rec);
            }

            if ($sysm == true) {
                $text = '<font color=red>Внимание!</font> Вы получили: <b>' . $dress['name'] . '</b> ' . $count . 'шт. ';
                static::sendPrivate($text, '{[]}' . $user['login'] . '{[]}', $user);
            }
            return "<b>«" . static::linkForItem($dress) . "»</b> " . $count . " шт.";

        }
        return false;

    }


    /**
     * @param $rec
     * @return bool
     */
    public static function addToNewDelo($rec)
    {
        $new_delo = NewDelo::create($rec + [
                'sdate' => time(),
            ]);

        if (!$new_delo) {
            return false;
        }

        $sert = array(200001, 200002, 200005, 200010, 200025, 200050, 200100, 200250, 200500);

        if (
            (
                (
                    $rec['item_type'] > 0 &&
                    $rec['item_type'] < 12 ||
                    $rec['item_type'] == 28 ||
                    $rec['item_type'] == 555 ||
                    $rec['item_type'] == 27 ||
                    (isset($rec['item_proto']) && in_array($rec['item_proto'], $sert))
                ) &&
                $rec['type'] != 32 &&
                $rec['type'] != 33
            ) OR
            (
                $rec['type'] == 1179 ||
                $rec['type'] == 1180 ||
                $rec['type'] == 1181 ||
                $rec['type'] == 179 ||
                $rec['type'] == 1303
            )
        ) {

            $it = explode(',', $rec['item_id']);

            $insert_data = [];

            for ($j = 0; $j < count($it); $j++) {
                $insert_data[] = [
                    'item_id' => trim($it[$j]),
                    'delo_id' => $new_delo->id,
                ];
            }

            $ndindx = NewDeloItIndex::insert($insert_data);

            if (!$ndindx) return false;

        }

        if (
            ($rec['type'] == 32 || $rec['type'] == 33 || $rec['type'] == 1300) &&
            (isset($rec['aitem_id']) && !empty($rec['aitem_id']))
        ) {

            $it = explode(',', $rec['aitem_id']);

            $insert_data = [];

            for ($j = 0; $j < count($it); $j++) {
                $insert_data[] = [
                    'item_id' => trim($it[$j]),
                    'delo_id' => $new_delo->id,
                ];
            }

            $ndindx = NewDeloItIndex::insert($insert_data);

            if (!$ndindx) return false;

        }

        return true;

    }

    /**
     * @param $uid
     * @param $pid
     * @param $type
     * @param $val
     * @return mixed
     */
    public static function updateXmlData($uid, $pid, $type, $val)
    {
        try {

            $t = Carbon::now();

            $xmlData = XmlData::where('user_id', $uid)
                ->where('added_at', $t->toDateString())
                ->where('param_id', $type)
                ->where('pid', $pid)
//            ->where('value', $val)
                ->first();

            if ($xmlData) {
                $xmlData->increment('value');
            } else {
                XmlData::create([
                    'user_id' => $uid,
                    'added_at' => $t->toDateString(),
                    'param_id' => $type,
                    'pid' => $pid,
                    'value' => $val,
                    'stamp' => $t->timestamp,
                ]);
            }

        } catch (\Exception $exception) {

        }
    }

    /**
     * @param $medal
     * @param $medals
     * @return false|int|string
     */
    public static function checkMedal($medal, $medals)
    {
        $medals = array_filter(explode(";", str_replace('|', '', $medals)));

        return array_search($medal, $medals);
    }
}