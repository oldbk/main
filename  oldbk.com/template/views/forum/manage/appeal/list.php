<div class="row">
    <div class="col text-center">
        <h4>Форумные жалобы</h4>
    </div>
</div>

<div class="row">
    <div class="col">
        <form action="">

            <div class="form-group">
                <div class="input-group input-group-sm">
                    <input
                            type="text"
                            name="date"
                            data-provide="datepicker"
                            data-date-format="yyyy-mm-dd"
                            class="form-control form-control-sm col-sm-4 datepicker"
                            value="<?=($app->request->get('date') ? \Carbon\Carbon::parse($app->request->get('date'))->toDateString() : \Carbon\Carbon::now()->toDateString())?>">
                    <span class="input-group-btn">
                    <input type="submit" class="btn btn-primary" value="Ok">
            </span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="trashed" value="1"  <?=$app->request->get('trashed') ? 'checked' : ''?>>+ обработанные
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="only_trashed" value="1"  <?=$app->request->get('only_trashed') ? 'checked' : ''?>>только обработанные
                    </label>
                </div>
            </div>

        </form>
    </div>
</div>



<?php
$pagination_string = $this->renderPartial('pagination/pagination', ['paginator' => $appeals, 'elements' => $elements], true);
?>

<div class="row py-3">
    <div class="col">
        Страницы: <?=($appeals->hasPages() ? $pagination_string : '<b>1</b>')?>
    </div>
</div>


<?

foreach ($appeals as $appeal) : ?>

<div class="row py-1">

    <div class="col" id="appeal-<?=$appeal->id?>">
        <small>жалоба от:
            <?=
            $appeal->reporter
                ? $this->renderPartial('common/renderuser', ['user' => $appeal->reporter])
                : $appeal->user_id;
            ?>
            <span class="date">(<?=$appeal->created_at?>)</span>
        </small>

        <? if ($user->isAdmin() || $user->isAdminion()) :?>
            <a onclick="if(confirm('Удалить жалобу?')) {return true;} return false;" href="<?=$app->urlFor('manage_appeal_delete', ['id' => $appeal->id])?>">
                <img src="/assets/forum/i/clear.gif" alt="Удалить жалобу" width="8" height="8">
            </a>
        <? endif; ?>


        <? if (!$appeal->topic) : ?>
            <p class="text-danger">Топик удален из базы (id: <?=$appeal->top_id?>)</p>
        <? else: ?>

            <p>
                <small>в топике: </small> <a target="_blank" href="<?=$app->urlFor('forum_topic', ['id' => $appeal->topic->id])?>?page=<?=$appeal['post_page']?>#p<?=$appeal['post_id']?>"><?=$appeal->topic->topic?></a>
            </p>

        <? endif; ?>

        <small>
            на пост<span class="oi oi-action-redo"></span>
        </small>

        <? if (!$appeal->post) : ?>
            <p class="text-danger">Пост удален из базы (id: <?=$appeal->post_id?>)</p>
        <? else: ?>

            <div>
                <?
                $violator = \components\Helper\AuthorInfo::buildForPostAuthor($appeal->post->author, $appeal->post->a_info, $user);
                echo $this->renderPartial('common/renderuser', ['user' => $violator]);
                ?>
                <small>(<span class="date"><?=$appeal->post->date?></span>)</small>
                <? if ($violator['is_invisible'] && $violator['invisible_info'] && $user && $user->canSeeInvisibleAuthor()) : ?>
                    <small>(<?= $this->renderPartial('common/renderuser', ['user' => $violator['invisible_info']]); ?>)</small>
                <? endif; ?>

                <div class="text-wrap">
                    <?=$appeal->post->del_info ? '<span style="color:grey">' . $appeal->post->text . '</span>' : $appeal->post->text?>
                </div>

            </div>

        <? endif; ?>


        <? if (!$appeal->moderator_id) : ?>
            <p>
                <a href="<?=$app->urlFor('manage_appeal_approve', ['id' => $appeal->id])?>">
                    <small>Пометить как обработанная</small>
                </a>
            </p>

        <? else: ?>
            <p>
                <small>
                    <a href="<?=$app->urlFor('manage_appeal_unapprove', ['id' => $appeal->id])?>">
                        <img src="/assets/forum/i/undo.png" alt="">
                    </a>
                    <b class="text-success">Жалоба обработана</b>
                    <?
                    if ($appeal->moderator)
                        echo $this->renderPartial('common/renderuser', ['user' => $appeal->moderator]);

                    if ($appeal->post && $appeal->post->del_info) {
                        $del_info = \components\Helper\AuthorInfo::buildForModerator($appeal->post->getModeratorInfo('post'),$user);
                        echo ', пост <u>удален</u> ' . $this->renderPartial('common/rendermoderator', ['user' => $del_info]);
                    }
                    ?>
                </small>
            </p>
        <? endif; ?>
        <hr>
    </div>

</div>

<? endforeach; ?>

<div class="row py-3">
    <div class="col">
        Страницы: <?=($appeals->hasPages() ? $pagination_string : '<b>1</b>')?>
    </div>
</div>
