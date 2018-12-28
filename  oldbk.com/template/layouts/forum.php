<!doctype html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta content="INDEX,FOLLOW" name="robots">
    <meta content="1 days" name="revisit-after">

    <?php if ($page_description): ?>
        <meta name="description" content="<?= $page_description ?>">
    <?php endif; ?>
    <meta name="keywords"
          content="онлайн игры, online игры, браузерные игры, онлайн мморпг, рпг онлайн, бк, combats, браузерные игры бесплатно, онлайн мморпг">

    <?php if ($page_title): ?>
        <title><?= $page_title ?></title>
    <?php endif; ?>

    <link rel="apple-touch-icon" sizes="512x512" href="//i.oldbk.com/i/icon/oldbk_512x512.png">
    <link rel="apple-touch-icon" sizes="144x144" href="//i.oldbk.com/i/icon/oldbk_144x144.png">
    <link rel="apple-touch-icon" sizes="114x114" href="//i.oldbk.com/i/icon/oldbk_114x114.png">
    <link rel="apple-touch-icon" sizes="72x72" href="//i.oldbk.com/i/icon/oldbk_72x72.png">
    <link rel="apple-touch-icon" sizes="58x58" href="//i.oldbk.com/i/icon/oldbk_58x58.png">
    <link rel="apple-touch-icon" sizes="48x48" href="//i.oldbk.com/i/icon/oldbk_48x48.png">
    <link rel="apple-touch-icon" sizes="29x29" href="//i.oldbk.com/i/icon/oldbk_29x29.png">
    <link rel="apple-touch-icon" href="//i.oldbk.com/i/icon/oldbk_57x57.png">


    <?php foreach ($app->clientScript->getCssFiles() as $cssFile): ?>
        <link rel="stylesheet" href="<?= $cssFile ?>" type="text/css">
    <?php endforeach; ?>

    <?php foreach ($app->clientScript->getJsFiles(\components\Component\Slim\Middleware\ClientScript\ClientScript::JS_POSITION_BEGIN) as $jsFile): ?>
        <script src="<?= $jsFile; ?>"></script>
    <?php endforeach; ?>


    <?php
    if ($debugbar) {
        echo $debugbar->getJavascriptRenderer()->renderHead();
    }
    ?>

</head>
<body class="bg_grey">

<nav class="d-md-none navbar navbar-dark bg_grey">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerCatMenu" aria-controls="navbarTogglerCatMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerCatMenu">
        <div class="bg_grey p-4">
            <div class="list-group">
                <? foreach ($categories as $category) : ?>
                    <? if (in_array($category['id'], [30, 40])) : ?>
                        <br>
                    <? endif; ?>
                    <a class="list-group-item list-group-item-action bg_f2e5b1" href="<?= $app->urlFor('forum_conf', ['id' => $category['id']]) ?>"><?= $category['topic'] ?></a>
                <? endforeach; ?>
            </div>
        </div>
    </div>
</nav>

<header>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-1 bg_grey min-height"></div>
            <div class="col-lg-10 col-12 bg_grey min-height" id="header_top">
                <img class="mx-auto d-block img-fluid" src="https://i.oldbk.com/i/log_f1.jpg" alt="">
            </div>
            <div class="col-lg-1 bg_grey min-height"></div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-1 bg_black min-height"></div>
            <div class="col-lg-10 col-12 bg_black min-height" id="header_center">
                <a href="https://oldbk.com">
                    <img class="mx-auto d-block img-fluid " src="https://i.oldbk.com/i/log_f2.jpg" alt="">
                </a>
            </div>
            <div class="col-lg-1 bg_black min-height"></div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-1 bg_grey min-height"></div>
            <div class="col-lg-10 col-12 bg_f2e5b1 min-height" id="header_bottom">
                <img class="mx-auto d-block img-fluid" src="https://i.oldbk.com/i/log_f3.jpg" alt="">
            </div>
            <div class="col-lg-1 bg_grey min-height"></div>
        </div>
    </div>

</header>

<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-1 min-height"></div>
            <div class="col-lg-10 col-12 bg_f2e5b1 bg_img_main min-height">
                <div class="row">
                    <div class="col-xl-2 col-lg-2 col-md-3 d-none d-md-block" id="category_menu">

                        <? foreach ($categories as $category) : ?>

                                <div class="row">
                                    <div class="col">
                                        <? if (in_array($category['id'], [30, 40])) : ?>

                                            <br>

                                        <? endif; ?>

                                        <div class="d-table-cell">
                                            &nbsp;&nbsp;•
                                        </div>
                                        <div class="d-table-cell">
                                            <a href="<?= $app->urlFor('forum_conf', ['id' => $category['id']]) ?>"><?= $category['topic'] ?></a>
                                        </div>

                                    </div>
                                </div>

                            <? endforeach; ?>

                    </div>
                    <div class="col-xl-10 col-lg-10 col-md-9 col-sm-12 col-12" id="content">

                        <? if (isset($user) && $user && ($user->isAdmin() || $user->isAdminion() || $user->isPaladin())) : ?>

                            <div class="text-muted">
                                <?= $this->renderPartial('common/renderuser', ['user' => $user]) ?>

                                <div class="dropdown  d-inline">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="oi oi-cog"></span>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">

                                        <? if ($user->canManagePermissions()) : ?>
                                            <a class="dropdown-item" href="<?= $app->urlFor('manage_list') ?>">
                                                Управление правами
                                            </a>
                                        <? endif; ?>

                                        <? if ($user->canManageAppeals()) : ?>
                                            <a class="dropdown-item" href="<?= $app->urlFor('manage_appeal_list') ?>">
                                                Форумные жалобы
                                            </a>
                                        <? endif; ?>

                                        <? if ($user->canManageCategories()) : ?>
                                            <a class="dropdown-item" href="<?= $app->urlFor('manage_category_list') ?>">
                                                Управление категориями
                                            </a>
                                        <? endif; ?>

                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                        <? endif; ?>

                        <?= $content ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-1 min-height"></div>
        </div>
    </div>
</div>

<footer class="footer bg_grey">
    <div class="container">

        <div class="text-center">
            <?= \components\Helper\Counters::getCounters() ?>
        </div>

        <div class="text-center" style="font-size:10px;">
            <a onMouseOver="this.style.color='black';" onMouseOut="this.style.color='black';" href="https://oldbk.com/"
               target="_blank" style="color:black;">
                © 2010—<?= \Carbon\Carbon::now()->year ?> «Бойцовский Клуб ОлдБК»
            </a>
        </div>

    </div>
</footer>

<?php foreach ($app->clientScript->getJsFiles(\components\Component\Slim\Middleware\ClientScript\ClientScript::JS_POSITION_END) as $jsFile): ?>
    <script src="<?= $jsFile; ?>"></script>
<?php endforeach; ?>

<?
if (isset($flash['noty'])) {
    echo $this->renderPartial('noty/noty', $flash['noty']);
}
?>
<script>
    $(function(){
        $.scrollUp({
            scrollText: '<span class="oi oi-caret-top"></span>'
        });
    });
</script>
</body>
</html>