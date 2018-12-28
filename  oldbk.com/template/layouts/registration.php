<?php

/**
 * @var \components\Component\Slim\View $this
 * @var \DebugBar\SlimDebugBar $debugbar;
 */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">

    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="document-state" content="Dynamic" />
    <meta name="resource-type" content="document" />
    <meta name="copyright" lang="ru" content="Oldbk" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />


    <meta name='yandex-verification' content='60ef46abc2646a77'>
    <meta name='yandex-verification' content='72aca2e356914f7e'>


    <!-- start favicons -->
    <link rel="apple-touch-icon" sizes="512x512" href="http://i.oldbk.com/i/icon/oldbk_512x512.png" />
    <link rel="apple-touch-icon" sizes="144x144" href="http://i.oldbk.com/i/icon/oldbk_144x144.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="http://i.oldbk.com/i/icon/oldbk_114x114.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="http://i.oldbk.com/i/icon/oldbk_72x72.png" />
    <link rel="apple-touch-icon" sizes="58x58" href="http://i.oldbk.com/i/icon/oldbk_58x58.png" />
    <link rel="apple-touch-icon" sizes="48x48" href="http://i.oldbk.com/i/icon/oldbk_48x48.png" />
    <link rel="apple-touch-icon" sizes="29x29" href="http://i.oldbk.com/i/icon/oldbk_29x29.png" />
    <link rel="apple-touch-icon" href="http://i.oldbk.com/i/icon/oldbk_57x57.png" />
    <!-- end favicons -->


    <?php if ($page_description): ?>
        <meta name="description" content="<?= $page_description ?>">
    <?php endif; ?>

    <?php if ($page_title): ?>
        <title><?= $page_title ?></title>
    <?php endif; ?>


    <?php foreach ($app->clientScript->getCssFiles() as $cssFile): ?>
        <link rel="stylesheet" href="<?= $cssFile ?>" type="text/css">
    <?php endforeach; ?>

    <?php foreach ($app->clientScript->getJsFiles(\components\Component\Slim\Middleware\ClientScript\ClientScript::JS_POSITION_BEGIN) as $jsFile): ?>
        <script src="<?= $jsFile; ?>"></script>
    <?php endforeach; ?>

</head>
<body class="im-01">


<!--BLOCK PRELOADER-->
<!--<div id="page-preloader"><span class="spinner"></span></div>-->
<!--/BLOCKPRELOADER-->


<div class="kp-container-main container">
    <div class="row kp-class-all po-re sh-100">
        <div class="kp-border po-ab">
            <div class="border-line-top po-ab im-59 z-3"></div>
            <div class="border-line-top po-ab im-60 z-3"></div>
            <div class="border-line-bootom po-ab im-4 z-3"></div>
            <div class="border-line-left po-ab im-5"></div>
            <div class="border-line-right po-ab im-6"></div>
            <div class="border-ugol-left po-ab im-1 z-3"></div>
            <div class="border-ugol-right po-ab im-2 z-3"></div>
            <div class="border-ugol-left-bt po-ab im-7 z-3"></div>
            <div class="border-ugol-right-bt po-ab im-8 z-3"></div>
        </div>

        <div class="col-md-12">

            <div class="row pt-2 p-3">
                <div class="reg-title mx-auto text-center">
                    Мир ОлдБК открыт для искателей приключений! Впиши свое имя в историю ОлдБК!
                </div>
            </div>

            <div class="row d-xs-block d-sm-block d-md-none">
                <div class="col-12">
                    <div id="carousel-big" class="carousel2 slide mx-auto" data-ride="carousel" data-pause="false">
                        <div class="carousel-inner">

                            <?
                            $img = [11,12,13,21,22,23,31,32,33,41,42,43];

                            foreach ($img as $key => $item) { ?>
                                <div class="carousel-item <?= ($key === 0) ? ' active' : ''; ?>">
                                    <img class="d-block w-100" src="/images/<?=$item?>_big.jpg" id="<?=$item?>_big" alt="">
                                </div>
                            <? } ?>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row d-none d-sm-none d-md-block px-2">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-4">
                            <div id="carousel1" class="carousel2 slide2 mx-auto" data-ride="carousel" data-pause="false">
                                <div class="carousel-inner">

                                    <?
                                    $img = [11,21,31,41];

                                    foreach ($img as $key => $item) { ?>
                                        <div class="carousel-item  <?= ($key === 0) ? ' active' : ''; ?>">
                                            <img class="d-block w-100" src="/images/<?=$item?>.jpg" id="<?=$item?>" alt="">
                                        </div>
                                    <? } ?>

                                </div>
                                <h2 class="hh2">> Современная браузерная RPG</h2>
                                <h2 class="hh2">> Интересный сюжет</h2>
                            </div>
                        </div>

                        <div class="col-4">
                            <div id="carousel2" class="carousel2 slide2 mx-auto" data-ride="carousel" data-pause="false">
                                <div class="carousel-inner">

                                    <?
                                    $img = [12,22,32,42];

                                    foreach ($img as $key => $item) { ?>
                                        <div class="carousel-item <?= ($key === 0) ? 'active' : ''; ?>">
                                            <img class="d-block w-100" src="/images/<?=$item?>.jpg" id="<?=$item?>" alt="">
                                        </div>
                                    <? } ?>

                                </div>
                                <h2 class="hh2">> Облегченная система боя</h2>
                                <h2 class="hh2">> Разнообразные квесты</h2>
                            </div>
                        </div>

                        <div class="col-4">
                            <div id="carousel3" class="carousel2 slide2 mx-auto" data-ride="carousel" data-pause="false">
                                <div class="carousel-inner">

                                    <?
                                    $img = [13,23,33,43];

                                    foreach ($img as $key => $item) { ?>
                                        <div class="carousel-item <?= ($key === 0) ? 'active' : ''; ?>">
                                            <img class="d-block w-100" src="/images/<?=$item?>.jpg" id="<?=$item?>" alt="">
                                        </div>
                                    <? } ?>

                                </div>
                                <h2 class="hh2">> Яркий игровой мир</h2>
                                <h2 class="hh2">> Живое общение</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row pt-1" >
                <div class="col-8 mx-auto" id="errors">
                    <?
                    if (isset($flash['errors'])) :
                        foreach ($flash['errors'] as $error) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?=$error?>
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            </div>
                        <?  endforeach;
                    endif;
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?= $content ?>
                </div>
            </div>

        </div>

    </div>
</div>

<footer class="footer po-ab im-27">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <p class="text-muted">© 2010—<?= Carbon\Carbon::now()->year; ?> «Бойцовский Клуб ОлдБК»</p>
            </div>
            <div class="col-md-8">
                <div class="pull-right" style="padding-top: 15px;">
                    <!--noindex-->
                    <?= \components\Helper\Counters::getCounters() ?>
                    <!--/noindex-->
                </div>
            </div>
        </div>
    </div>
</footer>



<?php foreach ($app->clientScript->getJsFiles(\components\Component\Slim\Middleware\ClientScript\ClientScript::JS_POSITION_END) as $jsFile): ?>
    <script src="<?= $jsFile; ?>"></script>
<?php endforeach; ?>

</body>
</html>