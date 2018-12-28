<?php

/**
 * @var \components\Component\Slim\View $this
 * @var \DebugBar\SlimDebugBar $debugbar ;
 * @var $page_title ;
 * @var $page_description ;
 */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">

    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <meta name="document-state" content="Dynamic"/>
    <meta name="resource-type" content="document"/>
    <meta name="copyright" lang="ru" content="Oldbk"/>
    <meta name="viewport" content="initial-scale=1.0, width=device-width">


    <meta name='yandex-verification' content='60ef46abc2646a77'>
    <meta name='yandex-verification' content='72aca2e356914f7e'>

    <meta name="robots" content="index, follow"/>


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

    <!--[if lt IE 9]>
    <script>document.createElement('figure');</script>
    <![endif]-->

    <?php
    if ($debugbar) {
        echo $debugbar->getJavascriptRenderer()->renderHead();
    }
    ?>

</head>

<body class="im-0">
<div class="kp-mtnu-bt sh-100 cp br-100 top-20 im-27">
    <i class="fa fa-bars" aria-hidden="true"></i>
</div>
<!--BLOCK PRELOADER-->
<div id="page-preloader"><span class="spinner"></span></div>
<!--/BLOCKPRELOADER-->
<div class="kp-nav-bar">
    <div class="im-27" style="position:absolute;bottom:0;width:100%;height:84px;">
        <div class="row kp-mobile-menu hide">
            <div class="wars po-re sh-10">
                <div class="kp-backgr-news po-ab">
                    <div class="kp-bk-full im-27 po-ab" style="background-size:100% 110%;"></div>
                    <div class="kp-bk-top-wars po-re">
                        <div class="kp-bk-top1 im-22 po-ab"></div>
                        <div class="kp-bk-top2 im-19 po-ab"></div>
                        <div class="kp-bk-top3 im-20 po-ab"></div>
                        <div class="kp-bk-top4 im-21 po-ab"><h3>МЕНЮ</h3></div>
                    </div>
                    <div class="kp-bk-bot-wars im-18 m-im-100 po-ab">
                        <div class="kp-bk-bot-wars-bbt-m im-24 cp"></div>
                    </div>
                </div>
                <div class="kp-wars-block-mobile po-re">
                    <ul class="kp-nav-block-mobile">
                        <li><a href="/encicl" class="im-28"></a></li>
                        <li><a href="http://top.oldbk.com" class="im-29" target="_blank"></a></li>
                        <li><a href="http://blog.oldbk.com" class="im-30" target="_blank"></a></li>
                        <li><a href="/forum" class="im-31"></a></li>
                        <li><a href="/commerce/" class="im-32"></a></li>
                        <li><a href="<?= $app->urlFor('help') ?>" class="im-33"></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row" style="max-width:1100px;margin:0 auto;">
            <div class="col-md-8 kp-menu-main">
                <div class=""
                     style="background-image:url(/assets/adaptive/img/4.png);background-size:73px 86px;width:74px;height:82px;position:absolute;left:-15px;top:-2px;"></div>
                <ul class="kp-nav-block">
                    <li><a href="/encicl" class="im-28" target="_blank"></a></li>
                    <li><a href="http://top.oldbk.com" class="im-29" target="_blank"></a></li>
                    <li><a href="http://blog.oldbk.com" class="im-30" target="_blank"></a></li>
                    <li><a href="/forum" class="im-31" target="_blank"></a></li>
                    <li><a href="/commerce/" class="im-32" target="_blank"></a></li>
                    <li><a href="<?= $app->urlFor('help') ?>" class="im-33"></a></li>
                </ul>
            </div>
            <div class="col-md-4 kp-nav-block-lf kp-menu-after">
                <div class="kp-nav-block-lf1"></div>
                <div class="kp-nav-block-lf2"></div>
                <a href="<?= $app->urlFor('registration', $app->request->get())?>">НАЧАТЬ ИГРАТЬ</a>
            </div>
        </div>
    </div>
</div>

<div class="kp-container-main container">
    <div class="row kp-class-all po-re sh-100">
        <div class="kp-border po-ab">
            <div class="border-line-top po-ab im-3 z-3"></div>
            <div class="border-line-bootom po-ab im-4 z-3"></div>
            <div class="border-line-left po-ab im-5"></div>
            <div class="border-line-right po-ab im-6"></div>
            <div class="border-ugol-left po-ab im-1 z-3"></div>
            <div class="border-ugol-right po-ab im-2 z-3"></div>
            <div class="border-ugol-left-bt po-ab im-7 z-3"></div>
            <div class="border-ugol-right-bt po-ab im-8 z-3"></div>
        </div>

        <div class="col-md-8 z-1 tr-03">
            <?= $content ?>
            <? if ($all_news) {
                echo "<div style='text-align:center;'><b><a href='".($app->urlFor('news'))."' target='_blank'>[ Читать все новости ]</a></b></div>";
            }
            ?>
        </div>

        <div class="col-md-4 z-1 tr-03">
            <div class="kp-right-widget sh-10">
                <div class="kp-backgr-news po-ab">
                    <div class="kp-bk-full im-10 po-ab"></div>
                    <div class="kp-bk-top im-11 m-im-100 po-ab"></div>
                    <div class="kp-bk-bot im-12 m-im-100 po-ab"></div>
                </div>
                <div class="po-re">
                    <div class="m10a20a play-reg tc">
                        <p>Зарегистрировано игроков: <?= $registered; ?> чел.</p>
                    </div>
                    <div class="m10a20a kp-form-data2">
                        <div class="kp-reg-div po-re">
                            <div class="kp-border po-ab">
                                <div class="kp-reg1 po-ab im-38 z-3 th-10">ВОЙТИ В ИГРУ</div>
                                <div class="kp-reg8 po-ab im-41 z-3"></div>
                                <div class="kp-reg2 po-ab im-37"></div>
                                <div class="kp-reg3 po-ab im-39"></div>
                                <div class="kp-reg4 po-ab im-34 z-3"></div>
                                <div class="kp-reg5 po-ab im-36 z-3"></div>
                                <div class="kp-reg6 po-ab im-35 z-3"></div>
                                <div class="kp-reg7 po-ab im-40 z-3"></div>
                            </div>
                            <div class="kp-form-input po-re">
                                <form action="<?= $app->urlFor('login', $app->request->get())?>" method="post" id="form-enter">
                                    <div class="kpoc-input">
                                        <input type="text" name="login" placeholder="Логин"
                                               class="br-5 im-10 m-im-100 tr-03">
                                        <input type="password" name="psw" placeholder="Пароль"
                                               class="br-5 im-10 m-im-100 tr-03">
                                    </div>
                                    <div class="kpoc-input-bt1 oa">
                                        <input type="submit"class="bt-en im-42 cp fl submit-link" value="">
                                        <a class="bt-no im-43 cp fl" href="<?= $app->urlFor('reminder') ?>"></a>
                                        <a class="bt-reg im-44 cp fl"
                                           href="<?= $app->urlFor('registration', $app->request->get()) ?>"></a>
                                    </div>
                                    <div class="kp-soc-bt oa">
                                        <h3>Используя социальные сети</h3>
                                        <div class="fl">

                                            <a rel="nofollow"
                                               href="http://capitalcity.oldbk.com/action/oauth/vkontakte/index">
                                                <i class="fa fa-vk fl tc br-5 cp tr-03 sh-1" aria-hidden="true"></i>
                                            </a>

                                            <a rel="nofollow"
                                               href="http://capitalcity.oldbk.com/action/oauth/facebook/index">
                                                <i class="fa fa-facebook fl tc br-5 cp tr-03 sh-1"
                                                   aria-hidden="true"></i>
                                            </a>

                                        </div>
                                        <div class="fl">
                                            <a rel="nofollow" href="http://capitalcity.oldbk.com/action/oauth/ok/index">
                                                <i class="fa fa-odnoklassniki fl tc br-5 cp tr-03 sh-1"
                                                   aria-hidden="true"></i>
                                            </a>

                                            <a rel="nofollow"
                                               href="http://capitalcity.oldbk.com/action/oauth/mailru/index">
                                                <i class="fa fa-envelope-o fl tc br-5 cp tr-03 sh-1"
                                                   aria-hidden="true"></i>
                                            </a>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <? if ($ivent) : ?>
                        <div class="m10a20a play-event oh m-im-1">
                            <span class="event_week_box" style="background:url(/img/ivent_<?= $ivent['id'] ?>.jpg) no-repeat;"></span>
                            <? $info = explode("–", $ivent['info']); ?>
                            <span class="event_week_text"><? echo $info[1]; ?></span>
                        </div>
                    <? endif; ?>

                    <div class="m10a20a kp-rang po-re">
                        <h3 class="tc">Рейтинги</h3>
                        <div class="kp-rang-main po-re">
                            <div class="kp-rang-bk po-ab">
                                <div class="kp-rang-bkfull im-57 po-ab"></div>
                                <div class="kp-rang-bk-top im-56 po-ab">
                                    <div class="kp-ico-rang active im-51" data-el="wins"></div>
                                    <div class="kp-ico-rang im-52" data-el="skulls"></div>
                                    <div class="kp-ico-rang im-53" data-el="voink"></div>
                                    <div class="kp-ico-rang im-54" data-el="wars"></div>
                                    <div class="kp-ico-rang im-55" data-el="wingl"></div>
                                    <div class="rang-t title-ct content show"><p>Победы</p></div>
                                    <div class="rang-t level-ct content hide">
                                        <ul>
                                            <li class="level-rang" data-el="7">7</li>
                                            <li class="level-rang" data-el="8">8</li>
                                            <li class="level-rang" data-el="9">9</li>
                                            <li class="level-rang" data-el="10">10</li>
                                            <li class="level-rang" data-el="11">11</li>
                                            <li class="level-rang" data-el="12">12</li>
                                            <li class="level-rang" data-el="13">13</li>
                                            <li class="level-rang" data-el="14">14</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="kp-rang-bk-bot im-58 po-ab"></div>
                            </div>
                            <div class="kp-rang-content po-re">
                                <div class="rang kp-rang-cont-wins oa show">
                                    <div class="kp-wins">

                                        <?php foreach ($rate['wins'] as $rate_win) : ?>

                                            <div class="pl-item">
                                                <div class="fl">
                                                    <img alt="" src="https://i.oldbk.com/i/align_<?= $rate_win['align'] ?>.gif">

                                                    <? if ($rate_win['klan']) : ?>
                                                        <img alt="<?= $rate_win['klan'] ?>"
                                                             title="<?= $rate_win['klan'] ?>"
                                                             src="https://i.oldbk.com/i/klan/<?= $rate_win['klan'] ?>.gif">
                                                    <? endif; ?>

                                                    <?= $rate_win['login'] . ' [' . $rate_win['level'] . ']' ?>
                                                    <a rel="nofollow"
                                                       href="http://capitalcity.oldbk.com/inf.php?<?= $rate_win['id'] ?>"
                                                       target="_blank">
                                                        <img src="https://i.oldbk.com/i/inf.gif" width="12" height="11"
                                                             alt="Инф. о <?= $rate_win['login'] ?>"
                                                             title="Инф. о <?= $rate_win['login'] ?>">
                                                    </a>
                                                </div>
                                                <div class="fr"><?= $rate_win['win'] ?></div>
                                            </div>

                                        <?php endforeach; ?>

                                    </div>
                                </div>
                                <div class="rang kp-rang-cont-skulls oa hide">


                                    <?php foreach (array_keys($rate['skulls']) as $lvl) : ?>

                                        <div class="level-gr kp-rang-sckull-<?= $lvl ?> <?= $lvl == 7 ? '' : 'hide' ?>">
                                            <div class="kp-wins">

                                                <?php foreach ($rate['skulls'][$lvl] as $rate_skull) : ?>

                                                    <div class="pl-item">
                                                        <div class="fl">
                                                            <img alt="" src="https://i.oldbk.com/i/align_<?= $rate_skull['align'] ?>.gif">

                                                            <? if ($rate_skull['klan']) : ?>
                                                                <img alt="<?= $rate_skull['klan'] ?>"
                                                                     title="<?= $rate_skull['klan'] ?>"
                                                                     src="https://i.oldbk.com/i/klan/<?= $rate_skull['klan'] ?>.gif">
                                                            <? endif; ?>

                                                            <?= $rate_skull['login'] . ' [' . $rate_skull['level'] . ']' ?>
                                                            <a rel="nofollow"
                                                               href="http://capitalcity.oldbk.com/inf.php?<?= $rate_skull['id'] ?>"
                                                               target="_blank">
                                                                <img src="https://i.oldbk.com/i/inf.gif" width="12"
                                                                     height="11"
                                                                     alt="Инф. о <?= $rate_skull['login'] ?>"
                                                                     title="Инф. о <?= $rate_skull['login'] ?>">
                                                            </a>
                                                        </div>
                                                        <div class="fr"><?= $rate_skull['skulls'] ?></div>
                                                    </div>

                                                <?php endforeach; ?>

                                            </div>
                                        </div>

                                    <?php endforeach; ?>

                                </div>
                                <div class="rang kp-rang-cont-voink oa hide">
                                    <div class="kp-wins">

                                        <?php foreach ($rate['voins'] as $rate_voin) : ?>

                                            <div class="pl-item">
                                                <div class="fl">
                                                    <img alt="" src="https://i.oldbk.com/i/align_<?= $rate_voin['align'] ?>.gif">

                                                    <? if ($rate_voin['klan']) : ?>
                                                        <img alt="<?= $rate_voin['klan'] ?>"
                                                             title="<?= $rate_voin['klan'] ?>"
                                                             src="https://i.oldbk.com/i/klan/<?= $rate_voin['klan'] ?>.gif">
                                                    <? endif; ?>

                                                    <?= $rate_voin['login'] . ' [' . $rate_voin['level'] . ']' ?>
                                                    <a rel="nofollow"
                                                       href="http://capitalcity.oldbk.com/inf.php?<?= $rate_voin['id'] ?>"
                                                       target="_blank">
                                                        <img src="https://i.oldbk.com/i/inf.gif" width="12" height="11"
                                                             alt="Инф. о <?= $rate_voin['login'] ?>"
                                                             title="Инф. о <?= $rate_voin['login'] ?>">
                                                    </a>
                                                </div>
                                                <div class="fr"><?= $rate_voin['voinst'] ?></div>
                                            </div>

                                        <?php endforeach; ?>

                                    </div>
                                </div>
                                <div class="rang kp-rang-cont-wars oa hide">
                                    <div class="kp-wins">

                                        <?php foreach ($rate['clan_wars'] as $rate_clan_war) : ?>

                                            <? $rate_clan_war = (array)$rate_clan_war ?>

                                            <div class="pl-item">
                                                <div class="fl">
                                                    <img alt=""
                                                         src="https://i.oldbk.com/i/align_<?= $rate_clan_war['align'] ?>.gif">
                                                    <img alt="<?= $rate_clan_war['name'] ?>"
                                                         title="<?= $rate_clan_war['name'] ?>"
                                                         src="https://i.oldbk.com/i/klan/<?= $rate_clan_war['short'] ?>.gif">

                                                    <?= $rate_clan_war['name'] ?>
                                                    <a rel="nofollow"
                                                       href="http://capitalcity.oldbk.com/inf.php?<?= $rate_clan_war['short'] ?>"
                                                       target="_blank">
                                                        <img src="https://i.oldbk.com/i/inf.gif" width="12" height="11"
                                                             alt="Инф. о <?= $rate_clan_war['name'] ?>"
                                                             title="Инф. о <?= $rate_clan_war['name'] ?>">
                                                    </a>
                                                </div>
                                                <div class="fr"><?= $rate_clan_war['kw'] ?></div>
                                            </div>

                                        <?php endforeach; ?>

                                    </div>
                                </div>
                                <div class="rang kp-rang-cont-wingl oa hide">
                                    <div class="kp-wins">

                                        <?php foreach ($rate['grand_battles'] as $rgb) : ?>

                                            <div class="pl-item">
                                                <div class="fl">
                                                    <img alt=""
                                                         src="https://i.oldbk.com/i/align_<?= $rgb['align'] ?>.gif">

                                                    <? if ($rgb['klan']) : ?>
                                                        <img alt="<?= $rgb['klan'] ?>" title="<?= $rgb['klan'] ?>"
                                                             src="https://i.oldbk.com/i/klan/<?= $rgb['klan'] ?>.gif">
                                                    <? endif; ?>

                                                    <?= $rgb['login'] . ' [' . $rgb['level'] . ']' ?>
                                                    <a rel="nofollow"
                                                       href="http://capitalcity.oldbk.com/inf.php?<?= $rgb['id'] ?>"
                                                       target="_blank">
                                                        <img src="https://i.oldbk.com/i/inf.gif" width="12" height="11"
                                                             alt="Инф. о <?= $rgb['login'] ?>"
                                                             title="Инф. о <?= $rgb['login'] ?>">
                                                    </a>
                                                </div>
                                                <div class="fr"><?= $rgb['winstbat'] ?></div>
                                            </div>

                                        <?php endforeach; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="m10a20a right_box">

                        <div class="slideshow">
                            <ul id="simple_slides">
                                <?
                                for ($imgk = 1; $imgk <= 25; $imgk++) {
                                    $close_img = array(2, 6, 9, 12, 13, 18, 19, 22, 23);
                                    if (in_array($imgk, $close_img)) continue;
                                    ?>
                                    <li class="simple_slide <?= $imgk == 1 ? 'showing' : ''; ?>">
                                        <img alt="" src="http://i.oldbk.com/i/slide/sm<?= $imgk; ?>m.jpg"
                                             onclick="location.href='<?= $app->urlFor('screen') ?>'"
                                             style="cursor: pointer;">
                                    </li>
                                    <?
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <div class="m10a20a kp-new-list-item-scroll">
                        <ul>
                            <li class="im-45"></li>
                            <li class="im-46 cp"><a href="/partners/" rel="nofollow"></a></li>
                            <li class="im-47 cp"><a href="<?= $app->urlFor('about') ?>"></a></li>
                            <li class="im-48 cp"><a href="/encicl/tvorchestvo.html" target="_blank"></a></li>
                            <li class="im-49"></li>
                        </ul>
                    </div>

                    <div class="m10a20a kp-lib-know">
                        <div class="kp-lib-tittle im-50 tc">
                            <h3>А знаете ли вы что...</h3>
                        </div>
                        <div class="kp-lib-content" id="qamsg">

                        </div>
                    </div>
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