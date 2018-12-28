<?php

/**
 * @var \components\Component\Slim\View $this
 * @var \DebugBar\SlimDebugBar $debugbar;
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
	<link rel="stylesheet" href="//oldbk.com/eassets/stylesssl.css" type="text/css" media="screen">
	<link rel="apple-touch-icon-precomposed" sizes="512x512" href="//i.oldbk.com/i/icon/oldbk_512x512.png">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="//i.oldbk.com/i/icon/oldbk_144x144.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="//i.oldbk.com/i/icon/oldbk_114x114.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="//i.oldbk.com/i/icon/oldbk_72x72.png">
	<link rel="apple-touch-icon-precomposed" sizes="58x58" href="//i.oldbk.com/i/icon/oldbk_58x58.png">
	<link rel="apple-touch-icon-precomposed" sizes="48x48" href="//i.oldbk.com/i/icon/oldbk_48x48.png">
	<link rel="apple-touch-icon-precomposed" sizes="29x29" href="//i.oldbk.com/i/icon/oldbk_29x29.png">
	<link rel="apple-touch-icon-precomposed" href="//i.oldbk.com/i/icon/oldbk_57x57.png">
	<meta name='yandex-verification' content='60ef46abc2646a77'>
	<title><?= $page_title ?></title>
	<?php if($page_description): ?>
		<META name="description" content="<?= $page_description ?>">
	<?php endif; ?>
	<?php foreach ($app->clientScript->getCssFiles() as $cssFile): ?>
		<link rel="stylesheet" href="<?= $cssFile ?>" type="text/css">
	<?php endforeach; ?>

    <link rel="stylesheet" href="/assets/adaptive/css/img.min.css" type="text/css">
    <link rel="stylesheet" href="/assets/adaptive/css/kp-new-style.min.css" type="text/css">
    <link rel="stylesheet" href="/assets/encicl/css/encicl.css" type="text/css">

	<?php foreach ($app->clientScript->getJsFiles(\components\Component\Slim\Middleware\ClientScript\ClientScript::JS_POSITION_BEGIN) as $jsFile): ?>
		<script src="<?= $jsFile; ?>"></script>
	<?php endforeach; ?>
	<?php
	if($debugbar) {
		echo $debugbar->getJavascriptRenderer()->renderHead();
	}
	?>
</head>
<body class="im-01">

<div id="main">
    <div class="container-fluid" id="header">
        <div class="row">
            <div class="col header"></div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="kp-class-all po-re mx-auto px-4 pb-5 sh-10">
                <div class="kp-border po-ab">
                    <div class="border-line-top po-ab im-60"></div>
                    <div class="border-line-bootom po-ab im-4"></div>
                    <div class="border-line-left po-ab im-5-fix"></div>
                    <div class="border-line-right po-ab im-6-fix"></div>
                </div>
                <div class="col-md-12">
                    <div class="row pt-1">
                        <div class="col-8 mx-auto" id="errors">
                            <?
                            if (isset($flash['errors'])) :
                                foreach ($flash['errors'] as $error) : ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?= $error ?>
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    </div>
                                <? endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 pl-3 pr-4 py-2" id="mainContent">
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

    <footer class="footer-body po-ab im-27">
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <div class="text-muted">
                        © 2010—<?= Carbon\Carbon::now()->year; ?> «Бойцовский Клуб ОлдБК»
                        <br>
                        <a href="https://oldbk.com/" style="color:#808080;">Многопользовательская бесплатная онлайн
                            фэнтези рпг - ОлдБК - Старый Бойцовский Клуб</a>
                    </div>
                </div>
                <div class="col-6">
                    <div class="pull-right pt-3">
                        <!--noindex-->
                        <p>
                            <?= \components\Helper\Counters::getCounters() ?>
                        </p>
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
</body>
</html>