<?php

namespace components\Enum\Forum;

use components\Enum\Enum;
use components\Enum\PermissionInterface;
use components\Enum\UserAlign;

/**
 * Class ForumPermission
 * @package components\Enum\User
 */
class ForumPermission extends Enum implements PermissionInterface
{

    const FORUM_MODERATOR = 1;  //Может модерировать форум (меню модерирования форума)

    const CAN_POST_DELETE = 2;//Удалять посты
    const CAN_POST_DELETE_HARD = 3;//Удалять посты из базы
    const CAN_POST_RESTORE = 4;//Восстанавливать посты

    const CAN_TOP_CLOSE = 5;//Закрывать топ
    const CAN_TOP_OPEN = 6;//Открывать топ
    const CAN_TOP_DELETE = 7;//Удалять топ
    const CAN_TOP_RESTORE = 8;//восстановить топ
    const CAN_TOP_DELETE_HARD = 9;//Удалять топ из базы
    const CAN_TOP_MOVE = 10;//переносить топ
    const CAN_TOP_DELETE_POSTS = 11;//удалять все посты в топике

    const CAN_COMMENT_WRITE = 12;//может писать коммент
    const CAN_COMMENT_DELETE = 13;//может удалять коммент

    const CAN_TOP_FIX = 14;//приклеплять топ
    const CAN_TOP_UNFIX = 15;//откреплять топ

    const CAN_SEE_DELETED_TOP = 16;//может ли смотреть удаленные(скртые топы)
    const CAN_SEE_DELETED_POST = 17;//может ли смотреть удаленные(скртые посты)

    const CAN_EDIT_POST = 18;//редактирование поста

    const CAN_SEE_WHO_MODERATOR = 19;//просмотр инфы модератора
    const CAN_SEE_INVISIBLE_AUTHOR = 20;//просмотр автора под невидом

    const CAN_BE_INVISIBLE = 21;//коммент от имени невидимки
    const CAN_MANAGE_APPEAL = 22;//управление жалобами
    const CAN_MANAGE_CATEGORY = 23;//управление категориями


    /**
     * @return array
     */
    public static function getPermissionsForId()
    {
        return [
            701762 => [
                static::CAN_SEE_INVISIBLE_AUTHOR,
                static::CAN_SEE_DELETED_POST,
                static::CAN_SEE_WHO_MODERATOR,
            ],
        ];
    }


}
