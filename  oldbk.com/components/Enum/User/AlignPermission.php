<?php

namespace components\Enum\User;

use components\Enum\Enum;
use components\Enum\PermissionInterface;
use components\Enum\UserAlign;

/**
 * Class AlignPermission
 * @package components\Enum\User
 */
class AlignPermission extends Enum implements PermissionInterface
{
    const SLEEP = 1;   //Молчанка
    const SLEEPF = 2;   //Форумная молчанка
    const UNSLEEP = 3;   //Снятие молчи
    const CHAOS = 4;   //Хаосить
    const UNCHAOS = 5;   //Снятие хаоса
    const BLOCK = 6;   //Блок
    const UNBLOCK = 7;   //Разблок
    const PAL_TAKE = 8;   //Прием в орден палов
    const PAL_KICK = 9;   //Кик из палов
    const CHECK = 10;  //Пал проверку
    const CUI = 11;  //Обезличка
    const UNCUI = 12;  //Снятие обезлички
    const VIEW_LD = 13;  //Просмотр ЛД
    const VIEW_TRANSFERS = 14;  //Просмотр переводов
    const VIEW_PAL_LOG = 15;  //Просмотр логов пала
    const CHAT_BROADCAST = 16;  //Чтение чата приватов
    const UNSLEEPF = 17;  //Снятие форумки
    const BLOCK_0 = 19;  //Блокировка нулевых уровней
    const CHANGE_PAL_STATUS = 20;  //Изменение названия статуса пала
    const EMAIL_USER_CONFIRM = 21;  //Запрос на почту о контроле над персонажем
    const SET_ALIGN = 22;  //Присвоение склонки
    const UNSET_ALIGN = 23;  //Снятие склонки
    const SET_DEALER = 24;  //Назначение дилером
    const VAMPIRE_BITE = 25;  //Укус вампира
    const CURE_HP = 26;  //Восстановление ХП
    const CURE_TRAVMA = 27;  //Лечить травму
    const INVISIBLE = 28;  //Невид
    const WRITE_LD = 29;  //Мжет писать в ЛД
    const VIEW_ACCOUNTS_USER = 30;  //Может смотреть полную инфу перса
    const GIVE_EKR = 31;  //Может давать екр
    const PAYMENT_IN_KO = 32;
    const VIEW_DEALER_LOG = 33;  //Просмотр лога дилеров
    const TAKE_PREMIUM = 34;  //Присваивать аккаунты
    const MARRY = 35;  //Свадьба
    const UNMARRY = 36;  //Развод
    const CLEAN_USER_MESSAGES = 37;  //удаление сообщения
    const BLOCK_RVS = 38;  //Блок РВС
    const GIVE_MEDAL = 39;  //Выдавать медаль
    const TELEGRAPH = 40;  //Отправлять телеграммы
    const VIEW_ITEM_HISTORY = 41;  //Просмотр истории предметов
    const TRANSFER_BAN = 42;  //Блок передач
    const TRANSFER_UNBAN = 43;  //Снятие блока передач

    const CAN_MANAGE_PERMISSION = 44;//доступ к редактированию прав на форуме


    /**
     * @return array
     */
    public static function getPermissionsForId()
    {
        return [
            
        ];
    }


}
