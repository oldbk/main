<?php

namespace components\Enum;

/**
 * Class PaladinAction
 * @package Combats\Enum
 */
class PaladinAction extends Enum
{
    const FORUM_TOPIC_CLOSED         = 1;
    const FORUM_POST_DELETE          = 2;
    const FORUM_TOPIC_OPEN           = 3;
    const FORUM_TOPIC_HIDDEN         = 4;
    const FORUM_TOPIC_RESTORE        = 5;
    const FORUM_ADD_COMMENT          = 6;
    const FORUM_TOPIC_FIXED          = 7;
    const FORUM_TOPIC_UNFIXED        = 8;
    const FORUM_REMOVE_COMMENT       = 9;
    const FORUM_POST_RESTORE         = 10;
    const PALADIN_CHECK              = 11;
    const USER_BLOCK                 = 12;
    const USER_UNBLOCK               = 13;
    const USER_SLEEP                 = 14;
    const USER_UNSLEEP               = 15;
    const USER_SLEEPF                = 16;
    const USER_UNSLEEPF              = 17;
    const USER_CUI                   = 18;
    const USER_UNCUI                 = 19;
    const USER_CHAOS                 = 20;
    const USER_UNCHAOS               = 21;
    const USER_TAKE_PALADIN          = 22;
    const USER_KICK_PALADIN          = 23;
    const USER_WRITE_LD              = 24;
    const CHAT_BROADCAST             = 25;
    const USER_VIEW_TRANSFERS        = 26;
    const MODERATOR_VIEW_ACTIONS     = 27;
    const FORUM_EDIT_POST_TO_USER    = 28;
    const FORUM_TOPIC_DELETE         = 29;
    const FORUM_TOPIC_TRANSFER       = 30;
    const MARRY                      = 31;
    const UNMARRY                    = 32;
    const REMOVE_RED_MESSAGE         = 33;
    const GIVE_MEDAL_USER            = 34;
    const CHANGE_PAL_STATUS          = 35;
    const USER_TRANSFER_BAN          = 36;
    const USER_TRANSFER_UNBAN        = 37;
}
