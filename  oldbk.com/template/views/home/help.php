<?php
$help = <<<HTML
<br><br>В <b>ОлдБК</b> есть специальные персонажи, которые могут оказать вам <b>помощь по Игре</b> и ответить на вопрос, касающийся игровой тематики.<br />
                    Такие персонажи имеют в информации значок <img class="img-responsive" src="http://i.oldbk.com/i/support/support.gif" border="0" /><strong style="color:#800000;">Помощник по игровым вопросам!</strong>.<br /><br />
                    Чат игры разделен на несколько вкладок, одна из которых, называется <b>"Помощь"</b>:<br /><br />

                    <p style="text-align:center;"><img class="img-responsive"  alt="" src=http://i.oldbk.com/i/images/help11.jpg border=0></p><br /><br />
                    <b>Чат Помощи</b> един для всех локаций и городов, и его можно использовать только для получения помощи по игровому процессу.
                    Вы можете обратиться в этот чат с любой игровой (не технической) проблемой и получить помощь или совет.<br /><br />
                    Вы можете задать в нем вопросы о прокачке персонажа, о назначении локаций, о правилах боев, о работе свитков, и так далее.
                    Например - как сделать модификацию, как подать на проверку, что такое уникальные вещи, с какого уровня можно вступить в клан, "что делать если...".<br /><br />

                    Один из <img src="http://i.oldbk.com/i/support/support.gif" border="0" /> <strong style="color:#800000;">Помощников</strong>, находящихся в онлайне, обязательно ответит вам или подскажет к кому обратиться с вашим вопросом. <br /><br />

                    Список <strong style="color:#800000;">Помощников</strong> онлайн можно посмотреть в списке чата, зайдя во вкладку <img src=http://i.oldbk.com/i/chat/ch6_active.jpg border=0>: <br><br>
                    <p style="text-align:center;"><img class="img-responsive" alt="" src=http://i.oldbk.com/i/images/help2.jpg border=0></p><br><br>

                    С <img src="http://i.oldbk.com/i/support/support.gif" border="0" /> <strong style="color:#800000;">Помощниками</strong>, также, можно общаться в привате, найдя необходимый вам ник в списке чата. Однако, общение <b>в привате</b> возможно только с <strong style="color:#800000;">Помощниками</strong>, находящимися <b>в вашем городе</b>. Помощник в другом городе не получит ваше приватное сообщение.
                    <br><br>
                    По вопросам багов и технических проблем советуем обращаться к клану <img src="http://i.oldbk.com/i/align_2.4.gif"><img alt="radminion" title="radminion" src="http://i.oldbk.com/i/klan/radminion.gif"> <B>radminion</B>

HTML;

//render_news('Помощь по Игре', $help);
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
                <div class="kp-title-news-name fl"><h3>Помощь по Игре</h3></div>
<!--                <div class="kp-title-news-date po-re fr"><h3>--><?//=\Carbon\Carbon::parse($item['cdate'])->toDateString()?><!--</h3></div>-->
            </div>
        </div>
        <div class="kp-news-content">
            <?= $help;?>
        </div>
    </div>
</div>
