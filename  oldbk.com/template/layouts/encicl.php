<?php
/**
 * @var \components\Component\Slim\View $this
 * @var \DebugBar\SlimDebugBar $debugbar ;
 */

$cat_menu = '';
foreach ($categories as $category) {
    if ($category['parent'] == -1) {
        $cat_menu .= '<div class="menu_cat">'.$category['title'].'</div>';
    } else {
        $cat_menu .= '<div class="menu"><span style="color:#413321">'.$category['title'].'</span></div>';
    }

    if (isset($pages_by_category[$category['id']])) {
        foreach ($pages_by_category[$category['id']] as $page) {
            $cat_menu .= '<div class="menu">&#9658; <a href="/encicl/'.$page['dir'].'.html">'.$page['page_title'].'</a><br></div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">

    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <meta name="document-state" content="Dynamic"/>
    <meta name="resource-type" content="document"/>
    <meta name="copyright" lang="ru" content="Oldbk"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <link rel="apple-touch-icon" sizes="512x512" href="//i.oldbk.com/i/icon/oldbk_512x512.png">
    <link rel="apple-touch-icon" sizes="144x144" href="//i.oldbk.com/i/icon/oldbk_144x144.png">
    <link rel="apple-touch-icon" sizes="114x114" href="//i.oldbk.com/i/icon/oldbk_114x114.png">
    <link rel="apple-touch-icon" sizes="72x72" href="//i.oldbk.com/i/icon/oldbk_72x72.png">
    <link rel="apple-touch-icon" sizes="58x58" href="//i.oldbk.com/i/icon/oldbk_58x58.png">
    <link rel="apple-touch-icon" sizes="48x48" href="//i.oldbk.com/i/icon/oldbk_48x48.png">
    <link rel="apple-touch-icon" sizes="29x29" href="//i.oldbk.com/i/icon/oldbk_29x29.png">
    <link rel="apple-touch-icon" href="//i.oldbk.com/i/icon/oldbk_57x57.png">
    <meta name='yandex-verification' content='60ef46abc2646a77'>

    <title><?= $page_title ?></title>
    <?php if ($page_description): ?>
        <meta name="description" content="<?= $page_description ?>">
    <?php endif; ?>

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

<body class="im-01">

<div id="sidebar" class="sidebar d-md-none animated">
    <div class="kp-right-widget sh-10 po-re">
        <div class="kp-backgr-news po-ab">
            <div class="kp-bk-left im-5-fix po-ab"></div>
        </div>
        <div class="po-re pl-4">
            <?=$cat_menu?>
        </div>
    </div>
</div>

<div id="wrapper">
    <div class="container-fluid" id="header">
        <div class="row">
            <div class="col header"></div>
        </div>
    </div>

    <div class="container mb-3" id="main">
        <div class="row">
            <div class="kp-class-all po-re mx-auto px-4 pb-5 sh-10">
                <div class="kp-border po-ab">
                    <div class="border-line-top po-ab im-60"></div>
                    <div class="border-line-bootom po-ab im-4"></div>
                    <div class="border-line-left po-ab im-5-fix"></div>
                    <div class="border-line-right po-ab im-6-fix"></div>
                    <img id="sidebarCollapse" class="d-md-none sps sps--abv" data-toggle="tooltip" data-placement="right" title="Меню"
                         src="/assets/adaptive/img/block/left_show_button.png" alt="">
                </div>
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-4 tr-03 p-2 d-none d-md-block">
                            <div class="kp-right-widget sh-10 po-re">
                                <div class="kp-backgr-news po-ab">
                                    <div class="kp-bk-full im-10 po-ab"></div>
                                    <div class="kp-bk-top im-11 m-im-100 po-ab"></div>
                                    <div class="kp-bk-bot im-12 m-im-100 po-ab"></div>
                                </div>
                                <div class="po-re">
                                    <?=$cat_menu?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 p-4" id="mainContent">
                            <div class="row">
                                <div class="col-12">
                                    <div class="float-right">
                                        <input type="image" alt="Регистрация"
                                               src="https://oldbk.com/i/main/lib_reg2.gif"
                                               onclick="location.href='https://oldbk.com/f/reg?reg=1&amp;b=&amp;pid=203&amp;ref='">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <?= $content ?>
                                </div>
                            </div>

                            <div class="row pt-3">
                                <div class="col-12">
                                    <div class="float-right">
                                        <input type="image" alt="Регистрация"
                                               src="https://oldbk.com/i/main/lib_reg2.gif"
                                               onclick="location.href='https://oldbk.com/f/reg?reg=1&amp;b=&amp;pid=203&amp;ref='">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer po-ab im-27">
        <div class="container-fluid d-flex h-100">
            <div class="row align-self-center w-100">
                <div class="col-8">
                    <div class="text-muted m-1">
                        © 2010—<?= Carbon\Carbon::now()->year; ?> «Бойцовский Клуб ОлдБК»
                        <br>
                        <a href="https://oldbk.com/" class="text-muted">Многопользовательская бесплатная онлайн
                            фэнтези рпг - ОлдБК - Старый Бойцовский Клуб</a>
                    </div>
                </div>
                <div class="col-4">
                    <div class="float-right">
                        <!--noindex-->
                        <?= \components\Helper\Counters::getCounters() ?>
                        <!--/noindex-->
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>


<?php foreach ($app->clientScript->getJsFiles(\components\Component\Slim\Middleware\ClientScript\ClientScript::JS_POSITION_END) as $jsFile): ?>
    <script src="<?= $jsFile; ?>"></script>
<?php endforeach; ?>

<script>
    $(document).ready(function () {

        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('toggled');
            $(this).toggleClass('opened');
        });

        $.scrollUp({
            scrollText: '<span class="oi oi-caret-top"></span>'
        });

    });
</script>

</body>
</html>