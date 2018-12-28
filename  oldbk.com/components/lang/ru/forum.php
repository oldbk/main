<?php

use components\Enum\Forum\ForumPermission;

return [
	"conf_access_deny"		=> "Конференция не найдена",
	"topic_access_deny"		=> "Тема не найдена",
	"topic_not_main"		=> "Тема не найдена",
	"topic_deleted"		    => "Тема удалена с форума, либо её не существует",

    'permissions_forum' => [
        ForumPermission::FORUM_MODERATOR            => 'Меню модерирования',
        ForumPermission::CAN_POST_DELETE            => 'Удаление поста',
        ForumPermission::CAN_POST_DELETE_HARD       => 'Удаление поста из базы',
        ForumPermission::CAN_POST_RESTORE           => 'Восстановление поста',
        ForumPermission::CAN_EDIT_POST              => 'Редактирование поста',
        ForumPermission::CAN_TOP_CLOSE              => 'Закрытие топа',
        ForumPermission::CAN_TOP_OPEN               => 'Открытие топа',
        ForumPermission::CAN_TOP_DELETE             => 'Удаление топа',
        ForumPermission::CAN_TOP_DELETE_HARD        => 'Удаление топа из базы ',
        ForumPermission::CAN_TOP_RESTORE            => 'Восстановление топа',
        ForumPermission::CAN_TOP_MOVE               => 'Перенос топа',
        ForumPermission::CAN_TOP_DELETE_POSTS       => 'Удаление всех постов в топе',
        ForumPermission::CAN_COMMENT_WRITE          => 'Писать комментарии',
        ForumPermission::CAN_COMMENT_DELETE         => 'Удалять комментарии',
        ForumPermission::CAN_TOP_FIX                => 'Прикреплять топ',
        ForumPermission::CAN_TOP_UNFIX              => 'Откреплять топ',

        ForumPermission::CAN_SEE_DELETED_TOP        => 'Просмотр удаленных топов',
        ForumPermission::CAN_SEE_DELETED_POST       => 'Просмотр удаленных постов',
        ForumPermission::CAN_EDIT_POST              => 'Редактирование постов',
        ForumPermission::CAN_SEE_WHO_MODERATOR      => 'Просмотр инфы модератора',
        ForumPermission::CAN_SEE_INVISIBLE_AUTHOR   => 'Просмотр автора под невидимкой',
        ForumPermission::CAN_BE_INVISIBLE           => 'Писать коммент от имени Невидимки',
        ForumPermission::CAN_MANAGE_APPEAL          => 'История жалоб',
        ForumPermission::CAN_MANAGE_CATEGORY        => 'Управление категориями форума',
    ],
];
