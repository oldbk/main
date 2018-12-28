<?php


?>




<div id="myCarousel" class="carousel slide sh-100" data-ride="carousel">
    <div class="kp-border po-ab">
        <div class="border-line-top po-ab im-25 z-3"></div>
        <div class="border-line-bootom po-ab im-26 z-1"></div>
    </div>
    <ol class="carousel-indicators">

        <? foreach (array_keys($comm_data) as $key) {
            echo '<li class="cp '.($key == 0 ? 'active': '').'" data-target="#myCarousel" data-slide-to="'.$key.'"></li>';
        } ?>

    </ol>
    <div class="carousel-inner z-3" role="listbox">

        <? foreach ($comm_data as $key => $item) : ?>

            <div class="item <? echo $key == 0 ? 'active': ''; ?>">
                <img class="first-slide po-ab" src="<?=$item['img']?>" alt='<?=$item['title']?>' style="height:139px;">
                <div class="carousel-caption2">
                    <span class="cont_box_bot_com_txt1">
                        <? if (isset($item['url'])) :?>
                            <a class="z-10" style="color: white; text-decoration: none;" href="<?=$item['url']?>" role="button" target="_blank"><?=$item['title']?></a>
                        <? else : ?>
                            <?=$item['title'] ?>

                        <? endif; ?>
                    </span>
                    <span class="cont_box_bot_com_txt2"><?=$item['text']?></span>
                </div>
            </div>

        <? endforeach; ?>

    </div>
</div>
<div class="kp-form-data1">
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
            <form action="<?= $app->urlFor('login', $app->request->get())?>" method="post">
                <div class="kpoc-input">
                    <input type="text" name="login" placeholder="Логин" class="br-5 im-10 m-im-100 tr-03">
                    <input type="password" name="psw" placeholder="Пароль" class="br-5 im-10 m-im-100 tr-03">
                </div>
                <div class="kpoc-input-bt1 oa">
                    <input class="bt-en im-42 cp fl" type="submit" value="" title="Enter">
                    <a class="bt-no im-43 cp fl" href="<?= $app->urlFor('reminder') ?>"></a>
                    <a class="bt-reg im-44 cp fl" href="<?= $app->urlFor('registration', $app->request->get())?>"></a>
                </div>
                <div class="kp-soc-bt oa">
                    <h3>Используя социальные сети</h3>
                    <div class="fl">

                        <a rel="nofollow" href="http://capitalcity.oldbk.com/action/oauth/vkontakte/index">
                            <i class="fa fa-vk fl tc br-5 cp tr-03 sh-1" aria-hidden="true"></i>
                        </a>

                        <a rel="nofollow" href="http://capitalcity.oldbk.com/action/oauth/facebook/index">
                            <i class="fa fa-facebook fl tc br-5 cp tr-03 sh-1" aria-hidden="true"></i>
                        </a>

                    </div>
                    <div class="fl">
                        <a rel="nofollow" href="http://capitalcity.oldbk.com/action/oauth/ok/index">
                            <i class="fa fa-odnoklassniki fl tc br-5 cp tr-03 sh-1" aria-hidden="true"></i>
                        </a>

                        <a rel="nofollow" href="http://capitalcity.oldbk.com/action/oauth/mailru/index">
                            <i class="fa fa-envelope-o fl tc br-5 cp tr-03 sh-1" aria-hidden="true"></i>
                        </a>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($wars->isNotEmpty()) : ?>

    <div class="wars po-re sh-10">
        <div class="kp-backgr-news po-ab">
            <div class="kp-bk-full im-10 po-ab"></div>
            <div class="kp-bk-top-wars po-re">
                <div class="kp-bk-top1 im-22 po-ab"></div>
                <div class="kp-bk-top2 im-19 po-ab"></div>
                <div class="kp-bk-top3 im-20 po-ab"></div>
                <div class="kp-bk-top4 im-21 po-ab"><h3>Текущие клановые войны</h3></div>
            </div>
            <div class="kp-bk-bot-wars im-18 m-im-100 po-ab">
                <div class="kp-bk-bot-wars-bbt im-24 cp"></div>
            </div>
        </div>
        <div class="kp-wars-block po-re">
            <ul class="war_list close-wars">

                <?php foreach ($wars as $war) : ?>


                    <?

                    $war['agr_txt']=str_replace('и рекруты', ', ', $war['agr_txt']);
                    $war['def_txt']=str_replace('и рекруты', ', ', $war['def_txt']);

                    $war['agr_txt']=preg_replace('/<a .+?clans.+?\/a>/i', '', $war['agr_txt']);
                    $war['def_txt']=preg_replace('/<a .+?clans.+?\/a>/i', '', $war['def_txt']);

                    $war['agr_txt']=str_replace('<img title', '<img alt="" title', $war['agr_txt']);
                    $war['def_txt']=str_replace('<img title', '<img alt="" title', $war['def_txt']);

                    $war['agr_txt']=str_replace('<img src', '<img alt="" src', $war['agr_txt']);
                    $war['def_txt']=str_replace('<img src', '<img alt="" src', $war['def_txt']);

                    $war['agr_txt']=str_replace('<img border=0 src=http://i.oldbk.com/i/inf.gif>', '<img alt="" src=http://i.oldbk.com/i/inf.gif>', $war['agr_txt']);
                    $war['def_txt']=str_replace('<img border=0 src=http://i.oldbk.com/i/inf.gif>', '<img alt="" src=http://i.oldbk.com/i/inf.gif>', $war['def_txt']);


                    $war['agr_txt'] = str_replace('border=0', '', $war['agr_txt']);
                    $war['agr_txt'] = str_replace('border="0"', '', $war['agr_txt']);

                    $war['def_txt'] = str_replace('border=0', '', $war['def_txt']);
                    $war['def_txt'] = str_replace('border="0"', '', $war['def_txt']);

                    ?>

                    <li class="im-23">
                        <div class="war_left fl tl">
                            <?=$war['agr_txt']?>
                        </div>
                        <div class="war_right fr tr">
                            <?=$war['def_txt']?>
                        </div>
                        <div class="war_icon im-13 m-a-0"></div>
                    </li>

                <?php endforeach; ?>

            </ul>
        </div>
    </div>

<?php endif; ?>


<div class="kp-news-block">

    <?php foreach ($news as $item) : ?>

        <?

        $text = $item['text'];
        $text = str_replace('<img src=','<img alt="" src=',$text);
        $text = str_replace('border=0', '', $text);
        $text = str_replace('border="0"', '', $text);
        $text = preg_replace('~<font color="#850404">(.*)</font>~iU','<span style="color:#850404;margin:0px;padding:0px;">\\1</span>', $text);
//        $text = preg_replace('~(<img[^>]+) style=".*?"~i','$1 class="img-responsive text-center"', $text);
//        $text = preg_replace('~(<img[^>]+)~i','$1 class="img-responsive"', $text);

        ?>

        <div class="kp-news-item box po-re sh-10">
            <div class="kp-backgr-news po-ab">
                <div class="kp-bk-full im-10 po-ab"></div>
                <div class="kp-bk-top im-11 m-im-100 po-ab"></div>
                <div class="kp-bk-bot im-12 m-im-100 po-ab"></div>
            </div>
            <div class="po-re">
                <div class="kp-news-title po-re">
                    <div class="kp-backgr-news-title po-ab">
                        <div class="kp-bk-full-tt im-15 po-ab"></div>
                        <div class="kp-bk-left-tt im-16 po-ab"></div>
                        <div class="kp-bk-right-tt im-17 po-ab"></div>
                    </div>
                    <div class="oh po-re kp-main-tittle">
                        <i class="fa fa-newspaper-o fl po-re" aria-hidden="true"></i>
                        <div class="kp-title-news-name fl"><h3><?=$item['topic']?></h3></div>
                        <div class="kp-title-news-date po-re fr"><h3><?=\Carbon\Carbon::parse($item['cdate'])->format('d-m-Y')?></h3></div>
                    </div>
                </div>
                <div class="kp-news-content">
                    <?= $text;?>
                </div>
            </div>
        </div>

    <?php endforeach; ?>

</div>
