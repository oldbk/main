<?

function check_rights($user)
{
    $data = mysql_query("SELECT * FROM oldbk.`pal_rights` WHERE pal_id='" . $user['id'] . "' LIMIT 1");
    if (mysql_num_rows($data) > 0) {
        $pal_rights = mysql_fetch_assoc($data);
    }

    $access = [];

    if (($user['align'] > 2 && $user['align'] < 3)) {
        $access['i_angel'] = $user['align'];
    }

    if (
        ($user['align'] > 1 && $user['align'] < 2) ||
        $access['i_angel'] > 0 ||
        in_array($user['align'], [5, 7]) ||
        in_array($user['id'], [546433, 1000, 697032, 5])
    ) {
        //призепить палрайтс.
        $access['i_pal'] = $user['align'];

        $access['can_forum_del'] = (($user['align'] >= '1.5' && $user['align'] < 2) || $user['align'] == 7 || $access['i_angel'] > 0 || $user['id'] == 1000 || $user['id'] == 14897 || $user['id'] == 5) ? 1 : 0;    //удаление постов (скрытие)
        $access['can_forum_restore'] = (($user['align'] >= '1.91' && $user['align'] < 2) || $user['align'] == 7 || $user['id'] == 546433 || $user['id'] == 1000 || $user['id'] == 14897 || $access['i_angel'] > 0 || $user['id'] == 5) ? 1 : 0;
        $access['can_close_top'] = (($user['align'] >= '1.5' && $user['align'] < 2) || $user['align'] == 7 || $access['i_angel'] > 0 || $user['id'] == 5) ? 1 : 0;
        $access['can_open_top'] = (($user['align'] >= '1.5' && $user['align'] < 2) || $user['align'] == 7 || $access['i_angel'] > 0 || $user['id'] == 5) ? 1 : 0;
        $access['can_del_top'] = (($user['align'] >= '1.5' && $user['align'] < 2) || $user['align'] == 7 || $access['i_angel'] > 0 || $user['id'] == 1000 || $user['id'] == 14897) ? 1 : 0;        //удаление топиков(скрытие)
        $access['can_del_top_all'] = (($user['align'] >= '1.91' && $user['align'] < 2) || $access['i_angel'] > 0) ? 1 : 0;
        $access['can_rest_top_all'] = (($user['align'] >= '1.91' && $user['align'] < 2) || $access['i_angel'] > 0) ? 1 : 0;
        $access['can_del_pal_comments'] = (($user['align'] >= '1.91' && $user['align'] < 2) || $user['id'] == 546433 || $user['id'] == 1000 || $user['id'] == 697032 || $access['i_angel'] > 0) ? 1 : 0;
        $access['can_create_votes'] = (($user['align'] >= '1.91' && $user['align'] < 2) || $access['i_angel'] > 0) ? 1 : 0;

        $access['view_ekr'] = ($access['i_angel'] > 0) ? 1 : 0;  //видеть екры в переводах
        $access['can_comment'] = (($pal_rights['red_forum'] == 1) || $user['id'] == 546433 || $user['id'] == 1000 || $user['id'] == 697032 || $access['i_angel'] > 0 || $user['id'] == 5) ? 1 : 0;        //Коментарий к посту
        $access['can_top_move'] = (($pal_rights['top_move'] == 1) || $access['i_angel'] > 0) ? 1 : 0;
        $access['perevodi'] = (($pal_rights['logs'] == 1) || $access['i_angel'] > 0) ? 5 : 0; //простые переводы + анализатор
        $access['item_hist'] = (($pal_rights['ext_logs'] == 1) || $access['i_angel'] > 0) ? 1 : 0; //открывает еще историю вещей
        $access['pal_tel'] = (($pal_rights['pal_tel'] == 1) || $access['i_angel'] > 0) ? 1 : 0;    //пал телеграф


        $access['klans_kazna_view'] = (($pal_rights['klans_kazna_view'] == 1) || $access['i_angel'] > 0) ? 1 : 0; //просмотр казны кланов
        $access['klans_kazna_logs'] = (($pal_rights['klans_kazna_logs'] == 1) || $access['i_angel'] > 0) ? 1 : 0; //просмотр логов казны кланов
        $access['klans_ars_logs'] = (($pal_rights['klans_ars_logs'] == 1) || $access['i_angel'] > 0) ? 1 : 0; //просмотр логов арсеналов кланов

        $access['klans_ars_put'] = (($pal_rights['klans_ars_put'] == 1) || $access['i_angel'] > 0) ? 1 : 0; //изымать вещь из арсенала (привязанную к арсу) и также возможность привязывать вещь к арсеналу.

        $access['pals_delo'] = (($pal_rights['pals_delo'] == 1) || $access['i_angel'] > 0) ? 1 : 0; //просмотр пал дела
        $access['pals_online'] = (($pal_rights['pals_online'] == 1) || $access['i_angel'] > 0) ? 1 : 0; //просмотр палов онлайн

        $access['anonim_hist'] = (($user['align'] >= '1.91' && $user['align'] < 2) || $access['i_angel'] > 0) ? 1 : 0; //смена анонима на ник
        $access['abils'] = $pal_rights['abils'];
    }
    return $access;
}


function show_palcomment($mess, $id, $access = 0)
{

    echo "<BR>";
    $mess = explode('|', $mess);
    $show_comment = '<br><font color=red>';

    for ($jj = 0; $jj < (count($mess) - 1); $jj++)  //строчное разделение разных каментов
    {
        $pl_inf = explode('_;_', $mess[$jj]);
        $mess_autor_txt = array();
        for ($ff = 0; $ff < count($pl_inf); $ff++)   // разделяем части камента
        {
            $mess_autor_txt[$ff] = $pl_inf[$ff];
        }
        $author = return_info($mess_autor_txt[0], $mess_autor_txt[1], 4);
        $show_comment .= $author . ' ' . $mess_autor_txt[2];
        if ($access[can_del_pal_comments] == 1) {
            $show_comment .= "<a OnClick=\"if (!confirm('Удалить комментарий?')) { return false; } \" href='?konftop=" . $_GET['konftop'] . "&topic=" . $_GET['topic'] . "&page=" . $_GET['page'] . "&com=" . $id . "&dc=" . $jj . "'>&nbsp;<img src='http://i.oldbk.com/i/clear.gif'></a>";
        }
        $show_comment .= '<br>';
    }

    $show_comment = substr($show_comment, 0, -4) . '</font>';
    return $show_comment;

}

function return_info($id, $info, $aa = 1)
{
    global $user;
    $inf = explode(',', $info);
    if (
        (($user[id] == 14897) OR
            ($user[id] == 457757) OR
            ($user[id] == 326) OR
            ($user[id] == 8540) OR
            ($user[id] == 6745) OR
            ($user[id] == 182783) OR
            ($user[id] == 684792) OR
            ($user[id] == 102904)) AND ($inf[4] > 0)) {
        $qwe = '. Adm:<a href="http://capitalcity.oldbk.com/inf.php?' . $id . '" target="_blank"><img border="0" alt="" src="http://i.oldbk.com/i/inf.gif"></a>';
    }
    //A-Tech,radminion,2.4,8,0
    if ($aa != 2) {
        if ($aa < 4) {
            if ($inf[2] > 1.1 && $inf[2] < 2) {
                $angel = "паладином";
            }
            if ($inf[2] > 2 && $inf[2] < 3) {
                $angel = "Ангелом";
            }
        } else {
            $angel = '';
        }
        if ($inf[4] > 0 && $aa == 1) {
            $print = '<img alt="" border="0" src="http://i.oldbk.com/i/align_0.gif"><b><i>Невидимкой</i></b>[??]<a href="http://capitalcity.oldbk.com/inf.php?' . $inf[4] . '" target="_blank"><img border="0" alt="" src="http://i.oldbk.com/i/inf.gif"></a>';
        } elseif ($inf[4] > 0 && ($aa == 0 || $aa == 4)) {
            $print = '<img alt="" border="0" src="http://i.oldbk.com/i/align_0.gif"><b><i>Невидимка</i></b>[??]<a href="http://capitalcity.oldbk.com/inf.php?' . $inf[4] . '" target="_blank"><img border="0" alt="" src="http://i.oldbk.com/i/inf.gif"></a>';
        } else {
            $inf1 = s_nick($id, $inf[2], $inf[1], $inf[0], $inf[3]);
            $print = ($aa == 1 ? $angel . ' ' : '') . $inf1;
        }
    } elseif ($aa == 2) {
        if ($inf[4] > 0) {
            $print = '<i>Невидимка</i>';
        } else {
            $print = $inf[0];
        }
    }
    return $print . $qwe;
}


function s_nick($id, $align, $klan, $login, $level)
{

    if ($align != '') {
        $align = "<img alt=\"\" src=\"http://i.oldbk.com/i/align_" . $align . ".gif\" border=\"0\">";
    } else {
        $align = '';
    }
    if ($klan != '') {
        $klan = "<img alt=\"\" src=\"http://i.oldbk.com/i/klan/" . $klan . ".gif\" border=\"0\">";
    } else {
        $klan = '';
    }

    $r_info = $align . $klan . "<b>" . $login . "</b>[" . $level . "]<a target=\"_blank\" href=\"/inf.php?" . $id . "\"><img border=0 alt=\"\" src=\"http://i.oldbk.com/i/inf.gif\"></a>";
    return $r_info;

}


function find_items_timeout($telo)
{
//ищем сроки которые меншье суток осталось
    $query = mysql_query("SELECT * FROM oldbk.`inventory` WHERE owner='{$telo['id']}' and `dategoden` > 0 and `dategoden` <= UNIX_TIMESTAMP()+(24*60*60)");
    $mtext = array();
    while ($it = mysql_fetch_assoc($query)) {
        $mtext[] = ' «' . link_for_item($it) . '» - предмет исчезнет через <b>' . prettyTime(null, $it['dategoden']) . '</b>';
    }

    if (count($mtext) > 0) {
        telepost_new($telo, '<font color=red>Внимание!</font> Заканчивается срок годности предметов:' . implode(",", $mtext));
        return true;
    }
    return false;
}


function do_present_items($telo)
{
    if ($telo['level'] <= 7) {
        if (time() - $telo['ldate'] >= (30 * 24 * 60 * 60)) {
            //30 дней
            $getitems = '';

            $getitems1 = DropBonusItem(105103, $telo, 'Удача', 'Бонус по возвращению', 0, 1, 20, false, true);     //		     Сытный завтрак 1 шт.
            if ($getitems1 != '') {
                $getitems = $getitems1;
            }

            $getitems2 = DropBonusItem(4005, $telo, 'Удача', 'Бонус по возвращению', 3, 1, 20, false, true);     //		     Малый свиток «Пропуск в Лабиринт» 1 шт. (срок годности 3 дня)
            if ($getitems2 != '') {
                $getitems .= " и " . $getitems2;
            }

            if ($getitems != '') {
                telepost_new($telo, '<font color=red>С возвращением!</font> Вы получили в подарок ' . $getitems . ', предметы находятся у вас в Инвентаре. Удачной игры!');
                return true;
            }
        }
    }
    return false;
}

function link_for_item($row, $retpath = false)
{

    $ehtml = str_replace('.gif', '', $row['img']);

    $razdel = array(
        1 => "kasteti", 11 => "axe", 12 => "dubini", 13 => "swords", 14 => "bow", 2 => "boots", 21 => "naruchi", 22 => "robi", 23 => "armors",
        24 => "helmet", 3 => "shields", 4 => "clips", 41 => "amulets", 42 => "rings", 5 => "mag1", 51 => "mag2", 6 => "amun", 61 => 'eda', 72 => ''
    );

    $row['otdel'] == '' ? $xx = $row['razdel'] : $xx = $row['otdel'];

    if ($row['type'] == 30) {
        $razdel[$xx] = "runs/" . $ehtml;
    } elseif ($razdel[$xx] == '') {
        $dola = array(5001, 5002, 5003, 5005, 5010, 5015, 5020, 5025);
        if (in_array($row['prototype'], $vau4)) {
            $razdel[$xx] = 'vaucher';
        } elseif (in_array($row['prototype'], $dola)) {
            $razdel[$xx] = 'earning';
        } else {
            $oskol = array(15551, 15552, 15553, 15554, 15555, 15556, 15557, 15558, 15561, 15562, 15568, 15563, 15564, 15565, 15566, 15567);
            if (in_array($row['prototype'], $oskol)) {
                $razdel[$xx] = "amun/" . $ehtml;
            } else {
                $razdel[$xx] = 'predmeti/' . $ehtml;
            }
        }
    } else {
        $razdel[$xx] = $razdel[$xx] . "/" . $ehtml;

    }

    if (($row['art_param'] != '') and ($row['type'] != 30)) {
        if ($row['arsenal_klan'] != '') {
            // клановый
            $razdel[$xx] = 'art_clan';
        } elseif ($row['sowner'] != 0) {
            //личный
            $razdel[$xx] = 'art_pers';
        }
    }

    if ($retpath) return $razdel[$xx];

    $out = "<a href=http://oldbk.com/encicl/" . $razdel[$xx] . ".html target=_blank>" . $row['name'] . "</a>";
    return $out;
}


function prettyTime($start_timestamp = null, $end_timestamp = null)
{
    $start_datetime = new DateTime();
    if ($start_timestamp !== null) {
        $start_datetime->setTimestamp($start_timestamp);
    }

    $end_datetime = new DateTime();
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


function telepost_new($to_telo, $text)
{
    global $user;

    if ($to_telo[odate] >= (time() - 60)) {
        addchp($text . '  ', '{[]}' . $to_telo[login] . '{[]}', $to_telo[room], $to_telo[id_city]);
    } else {
        // если в офе
        mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('" . $to_telo['id'] . "','','" . '[' . date("d.m.Y H:i") . '] ' . $text . '  ' . "');");
    }


}

function addchp($text, $who, $room = 0, $city = -1)
{
    global $user;
    if ($room == 0) {
        $room = $user['room'];
    }

    $city = $city + 1;

    $txt_to_file = ":[" . time() . "]:[{$who}]:[" . ($text) . "]:[" . $room . "]";
    $room = -1; // TEST only by Fred
    $q = mysql_query("INSERT INTO `oldbk`.`chat` SET `text`='" . mysql_real_escape_string($txt_to_file) . "',`city`='" . ($city) . "', `room`={$room} ;");
    if ($q !== FALSE) return true;
    return false;
}

function DropBonusItem($proto, $telo, $pres, $info, $goden_days = 0, $count = 1, $getfrom = 20, $sysm = false, $notsell = false)
{
    $q = mysql_query("SELECT * FROM oldbk.shop WHERE id = '{$proto}' ");
    $dress = mysql_fetch_assoc($q);
    if ($dress['id'] > 0) {
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

        for ($i = 1; $i <= $count; $i++) {
            if (mysql_query("INSERT INTO oldbk.`inventory`
							(`prototype`,`owner`,`name`,`type`,`massa`,`cost`,`ecost`,`img`,`maxdur`,`isrep`,
								`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
								`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`nsex`,`otdel`,`present`,`labonly`,`labflag`,`group`,`idcity`,`letter`,`ekr_flag`,`img_big`,`rareitem`,`getfrom`,`notsell`
							)
							VALUES
							('{$dress['id']}','{$telo['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},{$dress['ecost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
							'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','" . $dress['dategoden'] . "','{$dress['goden']}','{$dress['nsex']}','{$dress['razdel']}','{$pres}','0','0','{$dress['group']}','{$telo['id_city']}','{$dress['letter']}','{$dress['ekr_flag']}','{$dress['img_big']}','{$dress['rareitem']}','{$dress['getfrom']}','{$dress['notsell']}' ) ;")) {
                $aitems[] = "cap" . mysql_insert_id();
            }
        }


        if (count($aitems) > 0) {
            //add to new delo
            $rec['owner'] = $telo['id'];
            $rec['owner_login'] = $telo['login'];
            $rec['owner_balans_do'] = $telo['money'];
            $rec['owner_balans_posle'] = $telo['money'];
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
            $rec['battle'] = $telo['battle'];
            $rec['add_info'] = $info;
            add_to_new_delo($rec);
        }

        if ($sysm == true) {
            addchp('<font color=red>Внимание!</font> Вы получили: <b>' . $dress['name'] . '</b> ' . $count . 'шт. ', '{[]}' . $telo['login'] . '{[]}', $telo['room'], $telo['id_city']);
        }
        return "<b>«" . link_for_item($dress) . "»</b> " . $count . " шт.";
    }
    return false;
}

function get_pid($telo)
{
    $id = $telo[id];
    $squl = "select * from oldbk.partners_users where id='{$id}' LIMIT 1;";
    $getpaid = mysql_query($squl);
    if (mysql_affected_rows() > 0) {
        $array_pidd = mysql_fetch_array($getpaid);
        return $array_pidd;
    } else {
        return false;
    }
}

function make_record($uid, $pid, $type, $val)
{
    $t = time();
    $squl = "INSERT INTO oldbk.`xml_data` SET `user_id`='{$uid}',`added_at`='" . date('Y-m-d', $t) . "',`param_id`='{$type}',`value`='{$val}',`pid`='{$pid}',`stamp`='{$t}' ON DUPLICATE KEY UPDATE `value`=`value`+1 ;";
    if (mysql_query($squl)) {
        return true;
    } else {
        return true;
    }
}

function addch_group($text, $ids)
{

    if (is_array($ids)) {
        $ids = implode(":|:", $ids);
    }
    $ci = CITY_ID + 1;

    $txt_to_file = ":[" . time() . "]:[!group!:|:" . $ids . "]:[" . ($text) . "]:[]";
    $q = mysql_query("INSERT INTO `oldbk`.`chat` SET `text`='" . mysql_real_escape_string($txt_to_file) . "',`city`='{$ci}' ;");
    if ($q === FALSE) return FALSE;
    return TRUE;
}

function addch($text, $room = 0, $city = -1)
{
    global $user;
    if ($room == 0) {
        $room = $user['room'];
    }

    if ($user) {
        $ci = $user[id_city] + 1;
    } else {

        $ci = $city + 1;

    }

    $txt_to_file = ":[" . time() . "]:[!sys!!]:[" . ($text) . "]:[" . $room . "]";

    $room = -1; // TEST only by Fred
    $q = mysql_query("INSERT INTO `oldbk`.`chat` SET `text`='" . mysql_real_escape_string($txt_to_file) . "',`city`='" . $ci . "' , `room`='{$room}' ;");
    if ($q === FALSE) return FALSE;
    return TRUE;
}

include "new_delo.php";

?>