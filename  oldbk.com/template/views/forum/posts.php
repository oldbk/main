<?
/**
 * @var \components\Eloquent\User $user
 */


$pagination_string = $this->renderPartial('pagination/pagination', ['paginator' => $children_posts, 'elements' => $elements], true);
$domain = \Config::get('url.oldbk');
?>



<div class="row py-3">
    <div class="col text-center">
        <?if ($prevTopic) : ?>
            <a href="<?=$app->urlFor('forum_topic', ['id' => $prevTopic->id])?>">« <span class="next_prev">
                предыдущая ветвь
            </span></a>
        <? else: ?>
            « <span class="next_prev">предыдущая ветвь</span>
        <? endif; ?>

        | <a href="<?=$app->urlFor('forum_conf', ['id' => $main_post->above->id])?>"><?=$main_post->above->topic?></a> |

        <? if ($nextTopic) : ?>
            <a href="<?=$app->urlFor('forum_topic', ['id' => $nextTopic->id])?>"><span class="next_prev">следующая ветвь</span> »</a>
        <? else: ?>
            <span class="next_prev">следующая ветвь</span> »
        <? endif; ?>
    </div>
</div>

<div class="row py-3">
    <div class="col">
        Страницы: <?=($children_posts->hasPages() ? $pagination_string : '<b>1</b>')?>
    </div>
</div>


<? if ($user && $user->isForumModerator()) : ?>
    <div class="row pb-3">
        <div class="col">
            <div class="btn-group">
                <button type="button" class="btn btn-secondary btn-sm dropdown-toggle my-tooltip" title="Модерация топика" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="oi oi-cog"></span>
                </button>
                <div class="dropdown-menu dropdownMenuModerate">

                    <? if (!$main_post->isDeleted('top') && $user->canTopDelete()) : ?>
                        <a class="dropdown-item" href="<?=$app->urlFor('topic_delete', ['id' => $main_post->id])?>">удалить(скрыть) топ</a>
                    <? elseif ($main_post->isDeleted('top') && $user->canTopRestore()) : ?>
                        <a class="dropdown-item" href="<?=$app->urlFor('topic_restore', ['id' => $main_post->id])?>">восстановить топ</a>
                    <? endif; ?>

                    <? if ($user->canTopDelete('hard')) { ?>
                        <a class="dropdown-item" href="<?=$app->urlFor('topic_delete', ['id' => $main_post->id])?>?hard=1">удалить(из базы) топ</a>
                    <? } ?>

                    <? if ($main_post->isClosed() && $user->canTopOpen()) : ?>
                        <a class="dropdown-item" href="<?=$app->urlFor('topic_open', ['id' => $main_post->id])?>">открыть топ</a>
                    <? elseif($user->canTopClose()) : ?>
                        <a class="dropdown-item" href="<?=$app->urlFor('topic_close', ['id' => $main_post->id])?>">закрыть топ</a>
                    <? endif; ?>

                    <? if ($main_post->isFixed() && $user->canTopUnFix()) : ?>
                        <a class="dropdown-item" href="<?=$app->urlFor('topic_unfix', ['id' => $main_post->id])?>">открепить топ</a>
                    <? elseif($user->canTopFix()) : ?>
                        <a class="dropdown-item" href="<?=$app->urlFor('topic_fix', ['id' => $main_post->id])?>">закрепить топ</a>
                    <? endif; ?>

                    <? if ($user->canTopDeletePosts()) { ?>
                        <a class="dropdown-item" href="<?=$app->urlFor('topic_delete_posts', ['id' => $main_post->id])?>">удалить все посты в теме</a>
                        <a class="dropdown-item" href="<?=$app->urlFor('topic_delete_posts', ['id' => $main_post->id])?>?close=1">удалить все посты и закрыть тему</a>
                    <? } ?>

                </div>
            </div>

            <? if ($user->canTopMove()) : ?>
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle my-tooltip" title="Перенос топика" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="oi oi-transfer"></span>
                    </button>
                    <div class="dropdown-menu scrollable-menu dropdownMenuModerate">
                        <? foreach($categories as $category) : ?>
                            <? if ($category->id != $main_post->parent) : ?>
                                <a class="dropdown-item" href="<?=$app->urlFor('topic_transfer', ['id' => $main_post->id, 'category' => $category->id])?>"><?=$category->topic?></a>
                            <? endif; ?>
                        <? endforeach; ?>
                    </div>
                </div>
            <? endif; ?>
        </div>
    </div>
<? endif; ?>

<div class="row">
    <div class="col">
        <h4>
            <? if ($main_post->isFixed()) : ?>
                <span class="oi oi-pin my-tooltip text-dark" title="Тема закреплена"></span>
            <? endif; ?>

            <img alt="" height="15" src="//i.oldbk.com/i/icon<?=$main_post->icon ?>.gif" width="15" border="0"> <?=$main_post->topic ?>

            <? if ($main_post->isClosed()) : ?>
                <span class="oi oi-lock-locked my-tooltip text-dark" title="Обсуждение закрыто"></span>
            <? endif; ?>

            <? if ($main_post->isDeleted('top')) : ?>
                <span class="oi oi-trash my-tooltip text-dark" title="Топик удален"></span>
            <? endif; ?>
        </h4>
    </div>
</div>


<? foreach($children_posts as $key => $post) : ?>

<div class="one_post pr-md-3" id="p<?=$post->id?>">

    <div class="">
        <span class="post_author">
            <?= $this->renderPartial('common/renderuser', ['user' => $post['post_author']]); ?>
        </span>
        <? if ($post['post_author']['is_invisible'] && $post['post_author']['invisible_info'] && $user && $user->canSeeInvisibleAuthor()) : ?>
            <small>(<?= $this->renderPartial('common/renderuser', ['user' => $post['post_author']['invisible_info']]); ?>)</small>
        <? endif; ?>
        <span class="post_date">
            <small>(<span class="date"><?=$post->date ?></span>)</small>
        </span>
    </div>

    <?if ($post->isDeleted('post')) : ?>
        <div class="post_body text-wrap">
            <? if ($user && $post->canSeeDeleted($user, 'post')) { ?>
                <div class="text-muted"><?=$post->text?></div>
            <? } ?>

            <p class="red_margin_0">
                <b><?= $post['deleted_text']; ?></b>
            </p>
        </div>
    <? else : ?>

        <div class="post_body text-wrap"><?=$post->text?></div>

    <? endif;?>

    <? if ($post->hasComments()) : ?>

        <div class="comments text-wrap">
            <? foreach ($post->getComments() as $i => $comment) : ?>
                <div class="red_margin_0">
                        <?

                        if ($comment['author'] == false) {
                            echo \components\Helper\Str::makeLink($comment['text']);
                        } else {
                            $comment = \components\Helper\AuthorInfo::buildForComment($comment, $user);
                            echo $this->renderPartial('common/renderuser', [
                                    'user' => $comment['author']
                                ]) . ' ' . $comment['text'];
                        }

                        ?>

                    <? if ($user && $user->canCommentDelete()) : ?>
                        <a title="Удалить комментарий" onclick="hardDelete('Удалить комментарий', '<?= $app->urlFor('post_comment_remove', ['id' => $main_post->id, 'enum' => $post->id])?>?comment_number=<?=$i?>')"
                           href="javascript:void(0)">
                            <img src="/assets/forum/i/clear.gif">
                        </a>
                    <? endif; ?>

                </div>
            <? endforeach; ?>
        </div>

    <? endif; ?>

    <div class="float-right">

        <? if ($user) : ?>

        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">

            <? if (!$main_post->isClosed()) : ?>
                <a class="btn btn-warning bg_f2e5b1 btn-sm border-0 my-tooltip" title="Цитата" href="javascript:void(0)" onclick="quoteAuthor(<?=$post->id?>); return false;">
                    <span class="oi oi-double-quote-serif-right"></span>
                </a>

            <? endif; ?>

            <?if (!$post->isDeleted('post')) : ?>
                <a class="btn btn-warning bg_f2e5b1 btn-sm border-0 my-tooltip" title="Сообщить о нарушении" onclick="makeAppeal(<?=$post->id?>, '<?= $app->urlFor('post_appeal', ['id' => $main_post->id, 'enum' => $post->id])?>'); return false;" href="javascript:void(0)">
                    <span class="oi oi-bell"></span>
                </a>
            <? endif; ?>

            <button data-copy="<?=$domain . $children_posts->url($children_posts->currentPage())?>#p<?=$post['id']?>"  class="btn btn-warning bg_f2e5b1 btn-sm border-0 copy-clipboard my-tooltip" title="Скопировать ссылку на пост" type="button">
                <span class="oi oi-clipboard"></span>
            </button>

            <?if ($user->isForumModerator()) : ?>
                <div class="btn-group" role="group">

                    <button id="btnGroupDrop1" type="button" class="btn btn-warning bg_f2e5b1 btn-sm border-0 dropdown-toggle my-tooltip" title="Модерация" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="oi oi-cog"></span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">

                        <? if ($user->canCommentWrite()) : ?>
                            <a class="dropdown-item" href="javascript:void(0)" onclick="writeCommentForPost(
                                    'Добавить комментарий к записи',
                                    '<?=$app->urlFor('post_comment_add', ['id' => $main_post->id, 'enum' => $post->id])?>',
                                    '<?=$post->id?>',
                                    <?= (int)$user->canBeInvisible() ?>,
                                    <?= ((int)$user->isPaladin() || ((int)$user->isAdmin()) || (int)$user->isAdminion()) ?>);" >
                                Комментировать
                            </a>
                        <? endif; ?>

                        <? if ($user->canEditPost()) : ?>
                            <a class="dropdown-item" href="javascript:void(0)" onclick="editPost(
                                    'Редактирование поста',
                                    '<?=$app->urlFor('post_edit', ['id' => $main_post->id, 'enum' => $post->id])?>',
                                    '<?=$post->id?>'
                                    );" >
                                Редактировать
                            </a>
                        <? endif; ?>

                        <? if ($user->canPostDelete() && !$post->isDeleted('post')) : ?>

                            <a class="dropdown-item" href="javascript:void(0)" onclick="deletePost(
                                    'Указать причину удаления? (можно оставить поле пустым и добавить позже)',
                                    '<?=$app->urlFor('post_delete', ['id' => $main_post->id, 'enum' => $post->id])?>',
                                    '<?=$post->id?>',
                                    <?= (int)$user->canBeInvisible() ?>
                                    );" >
                                Удалить пост
                            </a>

                        <? elseif ($user->canPostRestore() && $post->isDeleted('post')) : ?>
                            <a class="dropdown-item" onclick="if (!confirm('Восстановить пост?')) { return false; } " href="<?=$app->urlFor('post_restore', ['id' => $main_post->id, 'enum' => $post->id])?>?<?=http_build_query($app->request->get())?>">
                                Восстановить пост
                            </a>
                        <? endif; ?>

                        <? if ($key != 0 && $user->canPostDelete('hard')) : ?>

                            <a class="dropdown-item" onclick="hardDelete('Удалить пост из базы', '<?= $app->urlFor('post_delete', ['id' => $main_post->id, 'enum' => $post->id])?>?hard=1&<?=http_build_query($app->request->get())?>')" href="javascript:void(0)">
                                Удалить пост из базы
                            </a>

                        <? endif; ?>

                    </div>

                </div>
            <? endif; ?>

        </div>

        <? endif; ?>

    </div>

    <div class="clearfix"></div>

</div>
<hr class="m-0 p-0 pb-2">

<? endforeach; ?>

<div class="row py-3">
    <div class="col">
        Страницы: <?=($children_posts->hasPages() ? $pagination_string : '<b>1</b>')?>
    </div>
</div>


<?
if ($main_post->isClosed()) :

    $who_closed = '';

    if ($main_post->hasClosedInfo()) {
        $moderator_info = \components\Helper\AuthorInfo::buildForModerator($main_post->getModeratorInfo('close'),$user);
        $who_closed = $this->renderPartial('common/rendermoderator', ['user' => $moderator_info]);
    }

    echo '<div class="text-center py-3"><span style="color: red">Обсуждение закрыто '.$who_closed.'</span></div>';

endif;


if ($user && (!$main_post->isClosed() || ($user->isAdmin() || $user->isHighPaladin()))) { ?>



    <? if ($user->hasForumSilence() && !$main_post->above->canCreateWithSilens($user)) : ?>

        <p class="text-center">
            <?=$user->getForumSilence()['name'] . ' ' . \components\Helper\TimeHelper::prettyTime(null,$user->getForumSilence()['time']); ?>
        </p>

    <? elseif ($user->hasChaos() && !$main_post->canCreatePostWithChaos()) :?>
        <p class="text-center">
            <?

            if($chaos = $user->getChaos()){
                echo $chaos['name'] . ' ' . \components\Helper\TimeHelper::prettyTime(null,$chaos['time']);
            }

            ?>
        </p>
    <? elseif ($user['level'] < $main_post->above['min_level']) : ?>
        <p class="text-center">
            Персонажам до <?=$main_post->above['min_level']?>-го уровня запрещено писать в этой ветке!
        </p>
    <? elseif ($main_post->parent != 18) : ?>
        <div class="row">

            <div class="col-xl-8 col-lg-8 col-md-10 col-sm-10 col-12">

                <div class="card bg_f2e5b1 mb-3">
                    <div class="card-body">
                        <? if ((!$user->isAdmin() && !$user->isAdminion() && !$user->isHighPaladin()) && $user->isHidden()) : ?>
                            <div class="alert alert-danger" role="alert">
                                <h4 class="alert-heading">Внимание!</h4>
                                При публикации сообщения иллюзия невидимости не работает.
                                <hr>
                                <?
                                if($hidden = $user->getHiddenEffect()){
                                    echo '<img src="https://i.oldbk.com/i/sh/hidden.gif"> "' . $hidden['name'] . '", еще ' . \components\Helper\TimeHelper::prettyTime(null,$hidden['time']);
                                }
                                ?>
                            </div>
                        <? endif; ?>
                        <form
                                action="<?= $app->urlFor('post_create', ['id' => $main_post['id']]) ?>"
                                method="post"
                                name="F1"
                                id="needs-validation"
                                novalidate>
                            <h4 class="card-title">Написать ответ</h4>

                            <? if (!$main_post->isClosed() && ($user->isAdmin() || $user->isHighPaladin())) { ?>
                                <div class="form-group row">
                                    <div class="col">
                                        и закрыть топик <input type="checkbox" name="andclose" value="1">
                                    </div>
                                </div>
                            <? } ?>

                            <div class="form-group row mb-0">
                                <div class="col">
                                    <div class="btn-group">
                                        <button data-placement="top" title="Жирный шрифт" onclick="addText('text', '<b>', '</b>');return false;" type="button" class="btn btn-warning bg_f2e5b1 border-secondary border-bottom-0 rounded-0 my-tooltip pointer">
                                            <b>Ж</b>
                                        </button>
                                        <button data-placement="top" title="Наклонный шрифт" onclick="addText('text', '<i>', '</i>');return false;" type="button" class="btn btn-warning bg_f2e5b1 border-secondary border-bottom-0 rounded-0 my-tooltip pointer">
                                            <i>К</i>
                                        </button>
                                        <button data-placement="top" title="Подчеркнутый шрифт" onclick="addText('text', '<u>', '</u>');return false;" type="button" class="btn btn-warning bg_f2e5b1 border-secondary border-bottom-0 rounded-0 my-tooltip pointer">
                                            <u>Ч</u>
                                        </button>
                                        <button data-placement="top" title="Текст программы" onclick="addText('text', '<code>', '</code>');return false;" type="button" class="btn btn-warning bg_f2e5b1 border-secondary border-bottom-0 rounded-0 my-tooltip pointer">
                                            <span class="oi oi-code"></span>
                                        </button>
                                        <button data-placement="top" title="Вставка цитаты.&#10;Выделите цитируемый текст и нажмите эту кнопку." onclick="addText('text', '<blockquote>', '</blockquote>');return false;" type="button" class="btn btn-warning bg_f2e5b1 border-secondary border-bottom-0 rounded-0 my-tooltip pointer">
                                            <span class="oi oi-double-quote-serif-right"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <textarea name="text" id="textarea_body" class="form-control rounded-0" maxlength="300000" style="min-height:151px;" required><?=$flash['text']?></textarea>
                                    <div class="invalid-feedback">
                                        Сообщение не должно быть пустым
                                    </div>
                                    <div class="" id="count_message"></div>
                                </div>
                            </div>

                            <?php if ($app->session->get('captcha_data')['show']): ?>
                                <?= \components\Helper\Captcha::render() ?>
                            <?php endif; ?>

                            <div class="form-group row">
                                <div class="col">
                                    <input type="hidden" name="_token" value="<?=$_token?>">
                                    <input class="btn btn-sm" type="submit" value="Добавить" name="add2">
                                    <div class="progress d-none" id="progress">
                                        <div id="progress-bar" class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <small class="form-text text-dark">
                                        Разрешается использование тегов форматирования текста:<br>
                                        <font color=#990000>&lt;b&gt;</font><b>жирный</b>
                                        <font color=#990000>&lt;/b&gt; &lt;i&gt;</font><i>наклонный</i>
                                        <font color=#990000>&lt;/i&gt; &lt;u&gt;</font><u>подчеркнутый</u>
                                        <font color=#990000>&lt;/u&gt;</font>,
                                        <BR>а для выделения текста программ, используйте
                                        <font color=#990000>&lt;code&gt; ... &lt;/code&gt;</font>
                                        <BR>и не забывайте закрывать теги!
                                        <font color=#990000>&lt;/b&gt;&lt;/i&gt;&lt;/u&gt;&lt;/code&gt;</font> :)
                                    </small>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>

        </div>
    <? endif; ?>

<? } ?>

<script>
    $(function () {
        $('.my-tooltip').tooltip();

        function copyToClipboard(text, el) {
            var copyTest = document.queryCommandSupported('copy');
            var elOriginalText = el.attr('data-original-title');

            if (copyTest === true) {
                var copyTextArea = document.createElement("textarea");
                copyTextArea.value = text;
                document.body.appendChild(copyTextArea);
                copyTextArea.select();
                try {
                    var successful = document.execCommand('copy');
                    var msg = successful ? 'Скопировал!' : 'Упс, рука-лицо!';
                    el.attr('data-original-title', msg).tooltip('show');
                } catch (err) {
                    console.log('Oops, unable to copy');
                }
                document.body.removeChild(copyTextArea);
                el.attr('data-original-title', elOriginalText);
            } else {
                // Fallback if browser doesn't support .execCommand('copy')
                window.prompt("Copy to clipboard: Ctrl+C or Command+C, Enter", text);
            }
        }

        $('.copy-clipboard').click(function() {
            var text = $(this).attr('data-copy');
            var el = $(this);
            copyToClipboard(text, el);
        });

        $('#needs-validation').on('submit', function(event){

            if (this.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                $(this).data('requestRunning', false);
            } else {
                if ($(this).data('requestRunning')) {
                    return false;
                }
                $(this).data('requestRunning', true);

                $('input[name=add2]').remove();
                $('#progress').removeClass('d-none');
            }

            this.classList.add('was-validated');

        });

    });

    $('#textarea_body').keyup(function() {
        var text_length = $(this).val().length;
        var max_length = $(this).attr('maxlength');
        var text_remaining = max_length - text_length;

        $('#count_message').html('Доступно  ' + text_remaining + ' символов');
    });

</script>
