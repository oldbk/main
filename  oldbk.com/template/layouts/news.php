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
                </div>
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-12 p-4" id="mainContent">

                            <div class="row">
                                <div class="col-12">
                                    <div class="float-right">
                                        <a href="http://oldbk.com/rss.php" target="_blank">
                                            <img src="http://i.oldbk.com/i/iconrss_s.png" alt="RSS-Лента новостей" title="RSS-Лента новостей">
                                        </a>
                                    </div>
                                    <?= $content ?>
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