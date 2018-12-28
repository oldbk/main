<?php
$pagination_string = $news->hasPages()
    ? $this->renderPartial('pagination/pagination', ['paginator' => $news, 'elements' => $elements], true)
    : '<b>1</b>';
?>

<div class="row py-1">
    <div class="col-12 mx-auto text-center">
        Страницы: <?=$pagination_string?>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="kp-news-block">
            <? foreach ($news as $post) : ?>
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
                                <div class="kp-title-news-name fl"><h3><a href="<?= $app->urlFor('news_post_id', ['id' => $post['id']]) ?>"><?=$post['topic']?></a></h3></div>
                                <div class="kp-title-news-date po-re fr"><h3><?=\Carbon\Carbon::parse($post['cdate'])->format('d-m-Y')?></h3></div>
                            </div>
                        </div>
                        <div class="kp-news-content px-3">
                            <?
                            $text = $post['text'];
                            $text = str_replace('<img src=','<img alt="" src=',$text);
                            $text = str_replace('border=0', '', $text);
                            $text = str_replace('border="0"', '', $text);
                            $text = preg_replace('~<font color="#850404">(.*)</font>~iU','<span style="color:#850404;margin:0px;padding:0px;">\\1</span>', $text);
//                            $text = preg_replace('~(<img[^>]+)~i','$1 class="img-fluid"', $text);

                            echo $text;
                            ?>
                        </div>
                        <? if ($news_comments) : ?>
                            <div class="float-right">
                                Комментарии: [<a href="<?= $app->urlFor('news_post_id', ['id' => $post['id']]) ?>#comments"><?=$post['comments_count']?></a>]
                            </div>
                        <? endif; ?>
                    </div>
                </div>
            <? endforeach; ?>
        </div>
    </div>
</div>

<div class="row py-1">
    <div class="col-12 mx-auto text-center">
        Страницы: <?=$pagination_string?>
    </div>
</div>

