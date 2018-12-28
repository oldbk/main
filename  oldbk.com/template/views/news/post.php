<div class="row text-center pb-2">
    <div class="col-12">
        <a href="<?=$app->urlFor('news')?>">Вернуться к новостям</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
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
                        <div class="kp-title-news-name fl">
                            <h3>
                                <?=$post['topic']?>
                            </h3>
                        </div>
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
//                    $text = preg_replace('~(<img[^>]+)~i','$1 class="img-fluid"', $text);

                    echo $text;
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


<? if (!$comments->isEmpty()) : ?>

    <?php
    $pagination_string = $comments->hasPages()
        ? $this->renderPartial('pagination/pagination', ['paginator' => $comments, 'elements' => $elements], true)
        : '<b>1</b>';
    ?>

    <div class="row p-3" id="comments">
        <div class="col-12">
            <? foreach ($comments as $comment) : ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <?=$comment['author']?> (<span class="date small"><?=$comment['date']?></span>)

                        <? if ($user && $user->isAdmin()) : ?>
                            <div class="float-right">
                                <a onclick="return confirm('Уверенны?')" href="<?=$app->urlFor('news_post_delete_comment', ['id' => $comment['id']])?>" class="text-danger">
                                    <span class="oi oi-trash"></span>
                                </a>
                            </div>
                        <? endif; ?>

                    </div>
                    <div class="card-body">
                        <div class="p-1">
                            <?=$comment['text']?>
                        </div>

                        <? if ($comment->comments) : ?>

                            <? foreach ($comment->comments as $c) : ?>
                                <div class="small text-danger p-1 font-italic">
                                    <?=$c['text']?>
                                    <? if ($user && $user->isAdmin()) : ?>
                                        <a onclick="return confirm('Уверенны?')" href="<?=$app->urlFor('news_post_delete_comment', ['id' => $c['id']])?>" class="text-danger">
                                            <span class="oi oi-trash"></span>
                                        </a>
                                    <? endif; ?>
                                </div>
                            <? endforeach; ?>

                        <? endif; ?>
                    </div>
                </div>
            <? endforeach; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mx-auto text-center">
            Страницы: <?=$pagination_string?>
        </div>
    </div>

<? endif; ?>