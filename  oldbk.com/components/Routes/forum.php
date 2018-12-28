<?php

if ($ip_ban = $app->getCookie('ip_ban')) {
    \components\Enum\User\IpBlackList::pushIp($ip_ban);
}

$blackList = new \components\Helper\BlackList($app);
$blackList->deny(\components\Enum\User\IpBlackList::$ips)->allow('all');


/***************************************************************************************************
 * Форум
 **************************************************************************************************/
$app->group('/forum', $blackList, function () use ($app) {

    /***************************************************************************************************
     * создание таблицы(один раз запускать)
     **************************************************************************************************/
    $app->get('/create_table/:table', function ($table) use ($app) {
        new \components\SchemaManager\SchemaManager($app, 'create', $table);
    })->name('create_table');

    /***************************************************************************************************
     * Главная
     **************************************************************************************************/
    $app->get('/', function ($action = 'index') use ($app) {
        new \components\Controller\forum\TopController($app, $action);
    })->name('forum');


    /***************************************************************************************************
     * Поиск по форуму
     **************************************************************************************************/
    $app->group('/search', function () use ($app) {

        $app->get('/', function () use ($app) {
            new \components\Controller\forum\SearchController($app, 'search');
        })->name('forum_search');

    });

    /***************************************************************************************************
     * Переадресация со старых топов на новые  КОСТЫЛЯКА ))
     **************************************************************************************************/
    $app->get('/topic/forum.php', function () use ($app) {

        if (!$app->request->get()) {
            return $app->redirectTo('forum');
        }

        if ($conf = $app->request->get('conf')) {
            return $app->redirectTo('forum_conf', [
                'id' => $conf
            ]);
        }

        if ($topic = $app->request->get('topic')) {
            return $app->redirectTo('forum_topic', [
                'id' => $topic
            ]);
        }

    })->name('forum_redirect');

    /***************************************************************************************************
     * Конференции
     **************************************************************************************************/
    $app->group('/conf/:id', function () use ($app) {

        $app->get('/', function ($id) use ($app) {
            new \components\Controller\forum\TopController($app, 'conf', $id);
        })->name('forum_conf');


        /***************************************************************************************************
         * Создать топ
         **************************************************************************************************/
        $app->post('/create_top', function ($id) use ($app) {
            new \components\Controller\forum\TopController($app, 'createTop', $id);
        })->name('topic_create');

    });

    /***************************************************************************************************
     * Топ с постами
     **************************************************************************************************/
    $app->group('/topic/:id', function () use ($app) {


        /***************************************************************************************************
         * Топик
         **************************************************************************************************/
        $app->get('/', function ($id) use ($app) {
            new \components\Controller\forum\TopController($app, 'topic', $id);
        })->name('forum_topic');


        /***************************************************************************************************
         * Перенос топика
         **************************************************************************************************/
        $app->get('/transfer/to/:category', function ($id, $category) use ($app) {
            new \components\Controller\forum\TopController($app, 'transferTop', ['top' => $id, 'category' => $category]);
        })->name('topic_transfer');


        /***************************************************************************************************
         * Открытие закрытие топа
         **************************************************************************************************/
        $app->get('/close', function ($id) use ($app) {
            new \components\Controller\forum\TopController($app, 'closeTop', $id);
        })->name('topic_close');
        $app->get('/open', function ($id) use ($app) {
            new \components\Controller\forum\TopController($app, 'openTop', $id);
        })->name('topic_open');


        /***************************************************************************************************
         * Прикрепить/открепить
         **************************************************************************************************/
        $app->get('/fix', function ($id) use ($app) {
            new \components\Controller\forum\TopController($app, 'fixTop', $id);
        })->name('topic_fix');
        $app->get('/unfix', function ($id) use ($app) {
            new \components\Controller\forum\TopController($app, 'unfixTop', $id);
        })->name('topic_unfix');


        /***************************************************************************************************
         * Удаление/восстановление топика
         **************************************************************************************************/
        $app->get('/delete', function ($id) use ($app) {
            new \components\Controller\forum\TopController($app, 'deleteTop', $id);
        })->name('topic_delete');
        $app->get('/restore', function ($id) use ($app) {
            new \components\Controller\forum\TopController($app, 'restoreTop', $id);
        })->name('topic_restore');


        /***************************************************************************************************
         * Удаление всех постов в топике
         **************************************************************************************************/
        $app->get('/delete_posts', function ($id) use ($app) {
            new \components\Controller\forum\TopController($app, 'deleteTopPosts', $id);
        })->name('topic_delete_posts');


        /***************************************************************************************************
         * Комментарии
         **************************************************************************************************/
        $app->post('/post_comment_add/:enum', function ($id, $enum) use ($app) {
            new \components\Controller\forum\PostController($app, 'addComment', [$id, $enum]);
        })->name('post_comment_add');
        $app->get('/post_comment_remove/:enum', function ($id, $enum) use ($app) {
            new \components\Controller\forum\PostController($app, 'removeComment', [$id, $enum]);
        })->name('post_comment_remove');


        /***************************************************************************************************
         * Создание/удаление/восстановление/редактирование поста
         **************************************************************************************************/
        $app->post('/post_create', function ($id) use ($app) {
            new \components\Controller\forum\PostController($app, 'createPost', $id);
        })->name('post_create');

        $app->map('/post_delete/:enum', function ($id, $enum) use ($app) {
            new \components\Controller\forum\PostController($app, 'deletePost', [$id, $enum]);
        })->via('GET', 'POST')->name('post_delete');

        $app->get('/post_restore/:enum', function ($id, $enum) use ($app) {
            new \components\Controller\forum\PostController($app, 'restorePost', [$id, $enum]);
        })->name('post_restore');

        $app->get('/post_edit/:enum', function ($id, $enum) use ($app) {
            new \components\Controller\forum\PostController($app, 'editPost', [$id, $enum]);
        })->name('post_edit_form');

        $app->post('/post_edit/:enum', function ($id, $enum) use ($app) {
            new \components\Controller\forum\PostController($app, 'editPost', [$id, $enum]);
        })->name('post_edit');


        /***************************************************************************************************
         * Отправка жалобы
         **************************************************************************************************/
        $app->get('/post_appeal/:enum', function ($id, $enum) use ($app) {
            new \components\Controller\forum\AppealController($app, 'appealPost', [$id, $enum]);
        })->name('post_appeal');


        /***************************************************************************************************
         * Лайки
         **************************************************************************************************/
        $app->get('/like/:action', function ($id, $action) use ($app) {
            new \components\Controller\forum\LikeController($app, $action . 'Like', $id);
        })->name('top_like');
    });


    /***************************************************************************************************
     * Управление правами
     **************************************************************************************************/
    $app->group('/manage_permission', function () use ($app) {

        $app->get('/', function () use ($app) {
            new \components\Controller\forum\ManageForumPermissionController($app, 'manage');
        })->name('manage_list');

        $app->get('/:id', function ($id) use ($app) {
            new \components\Controller\forum\ManageForumPermissionController($app, 'manageUser', $id);
        })->name('manage_user');

        $app->get('/:id/delete_permission', function ($id) use ($app) {
            new \components\Controller\forum\ManageForumPermissionController($app, 'manageUserDelete', $id);
        })->name('manage_delete_permission');

        $app->post('/:id/save_permission', function ($id) use ($app) {
            new \components\Controller\forum\ManageForumPermissionController($app, 'manageUserSave', $id);
        })->name('manage_save_permission');

    });

    /***************************************************************************************************
     * Управление жалобами
     **************************************************************************************************/
    $app->group('/manage_appeal', function () use ($app) {

        $app->get('/', function () use ($app) {
            new \components\Controller\forum\ManageForumAppealController($app, 'appeal');
        })->name('manage_appeal_list');

        $app->get('/:id/appeal_approve', function ($id) use ($app) {
            new \components\Controller\forum\ManageForumAppealController($app, 'appealApprove', $id);
        })->name('manage_appeal_approve');

        $app->get('/:id/appeal_unapprove', function ($id) use ($app) {
            new \components\Controller\forum\ManageForumAppealController($app, 'appealUnapprove', $id);
        })->name('manage_appeal_unapprove');

        $app->get('/:id/appeal_delete', function ($id) use ($app) {
            new \components\Controller\forum\ManageForumAppealController($app, 'appealDelete', $id);
        })->name('manage_appeal_delete');

    });

    /***************************************************************************************************
     * Управление категориями
     **************************************************************************************************/
    $app->group('/manage_category', function () use ($app) {

        $app->get('/', function () use ($app) {
            new \components\Controller\forum\ManageForumCategoryController($app, 'category');
        })->name('manage_category_list');

        $app->get('/category_create', function () use ($app) {
            new \components\Controller\forum\ManageForumCategoryController($app, 'categoryManageCreate');
        })->name('manage_category_create');

        $app->post('/category_save', function () use ($app) {
            new \components\Controller\forum\ManageForumCategoryController($app, 'categoryManageSave');
        })->name('manage_category_save');

        $app->get('/:id', function ($id) use ($app) {
            new \components\Controller\forum\ManageForumCategoryController($app, 'categoryManage', $id);
        })->name('manage_category')->conditions(['id' => '\d+']);

        $app->post('/:id/category_edit', function ($id) use ($app) {
            new \components\Controller\forum\ManageForumCategoryController($app, 'categoryManageEdit', $id);
        })->name('manage_category_edit')->conditions(['id' => '\d+']);

        $app->get('/:id/category_delete', function ($id) use ($app) {
            new \components\Controller\forum\ManageForumCategoryController($app, 'categoryManageDelete', $id);
        })->name('manage_category_delete')->conditions(['id' => '\d+']);

    });

});