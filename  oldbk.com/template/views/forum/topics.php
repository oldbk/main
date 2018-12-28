<?
/**
 * @var \components\Eloquent\User $user
 * @var \components\Eloquent\Forum $topic
 */


$pagination_string = $this->renderPartial('pagination/pagination', ['paginator' => $topics, 'elements' => $elements], true);

if (isset($user) && $user) {
    echo '<a id="search-btn" href="'.$app->urlFor('forum_search').'" class="btn btn-secondary btn-sm float-right mr-3 my-tooltip text-light" title="Поиск по форуму" data-placement="left"><span class="oi oi-magnifying-glass"></span></a>';
}

?>

<div class="row">
    <div class="col">
        <h3>Конференция "<?= $conf['topic'] ?>"</h3>
        <? if ($conf->text) : ?>
            <div class="small pb-3">
                <?= $conf['text'] ?>
            </div>
        <? endif; ?>
    </div>
</div>

<div class="row py-3">
    <div class="col">
        Страницы: <?=($topics->hasPages() ? $pagination_string : '<b>1</b>')?>
    </div>
</div>


<?
foreach ($topics as $topic) {

    if ( !$topic->isDeleted('top') || ($user && $topic->canSeeDeleted($user, 'top'))) : ?>

        <div class="row py-2 pr-4">

            <div class="col">

                <div class="row">
                    <div class="col pleft">

                        <? if ($topic->isFixed()) : ?>
                            <span class="oi oi-pin my-tooltip" title="Тема закреплена"></span>
                        <? endif; ?>

                        <img height="15" src="//i.oldbk.com/i/icon<?= $topic->icon ?>.gif" width="15" alt="icon">

                        <a href="<?= $app->urlFor('forum_topic', ['id' => $topic->id]) ?>"><?= $topic->topic ?></a>

                        <?= $this->renderPartial('common/renderuser', ['user' => $topic['post_author']]); ?>

                        <? if ($topic['post_author']['is_invisible'] && $topic['post_author']['invisible_info'] && $user && $user->canSeeInvisibleAuthor()) : ?>
                            <small>(<?= $this->renderPartial('common/renderuser', ['user' => $topic['post_author']['invisible_info']]); ?>)</small>
                        <? endif; ?>

                        <? if ($topic->isClosed()) : ?>
                            <span class="oi oi-lock-locked my-tooltip" title="Обсуждение закрыто"></span>
                        <? endif; ?>

                        <? if ($topic->isDeleted('top')) : ?>
                            <span class="oi oi-trash my-tooltip" title="Топик удален"></span>
                        <? endif; ?>

                        <? if ($user) : ?>

                            <? if ($user->isForumModerator()) : ?>

                            <div class="btn-group">
                                <button type="button" class="btn btn-warning bg_f2e5b1 btn-sm border-0 dropdown-toggle my-tooltip" title="Модерация топика" data-toggle="dropdown" id="dropdownMenuModerate" aria-haspopup="true" aria-expanded="false">
                                    <span class="oi oi-cog"></span>
                                </button>

                                <div class="dropdown-menu dropdownMenuModerate" aria-labelledby="dropdownMenuModerate">

                                    <? if (!$topic->isDeleted('top') && $user->canTopDelete()) : ?>
                                        <a class="dropdown-item" href="<?=$app->urlFor('topic_delete', ['id' => $topic->id])?>">удалить(скрыть) топ</a>
                                    <? elseif ($topic->isDeleted('top') && $user->canTopRestore()) : ?>
                                        <a class="dropdown-item" href="<?=$app->urlFor('topic_restore', ['id' => $topic->id])?>">восстановить топ</a>
                                    <? endif; ?>

                                    <? if ($user->canTopDelete('hard')) { ?>
                                        <a class="dropdown-item" href="<?=$app->urlFor('topic_delete', ['id' => $topic->id])?>?hard=1">удалить(из базы) топ</a>
                                    <? } ?>

                                    <? if ($topic->isClosed() && $user->canTopOpen()) : ?>
                                        <a class="dropdown-item" href="<?=$app->urlFor('topic_open', ['id' => $topic->id])?>">открыть топ</a>
                                    <? elseif($user->canTopClose()) : ?>
                                        <a class="dropdown-item" href="<?=$app->urlFor('topic_close', ['id' => $topic->id])?>">закрыть топ</a>
                                    <? endif; ?>

                                    <? if ($topic->isFixed() && $user->canTopUnFix()) : ?>
                                        <a class="dropdown-item" href="<?=$app->urlFor('topic_unfix', ['id' => $topic->id])?>">открепить топ</a>
                                    <? elseif($user->canTopFix()) : ?>
                                        <a class="dropdown-item" href="<?=$app->urlFor('topic_fix', ['id' => $topic->id])?>">закрепить топ</a>
                                    <? endif; ?>

                                    <? if ($user->canTopDeletePosts()) { ?>
                                        <a class="dropdown-item" href="<?=$app->urlFor('topic_delete_posts', ['id' => $topic->id])?>">удалить все посты в теме</a>
                                        <a class="dropdown-item" href="<?=$app->urlFor('topic_delete_posts', ['id' => $topic->id])?>?close=1">удалить все посты и закрыть тему</a>
                                    <? } ?>
                                </div>
                            </div>

                            <? endif; ?>

                            <? if ($user->canSeeWhoModerator()) : ?>

                                <? if ($topic->isDeleted('top')) :
                                    if ($topic->hasDeletedInfo('top')) : ?>
                                        <div>
                                            <small>
                                                <span class="text-danger">Топ удален
                                                    <?
                                                    $moderator_info = \components\Helper\AuthorInfo::buildForModerator($topic->getModeratorInfo('top'),$user);
                                                    echo $this->renderPartial('common/rendermoderator', ['user' => $moderator_info]);
                                                    ?>
                                                </span>
                                            </small>
                                        </div>
                                    <? endif;
                                endif; ?>

                                <? if ($topic->isClosed()) :
                                    if ($topic->hasClosedInfo()) : ?>
                                        <? $moderator_info = \components\Helper\AuthorInfo::buildForModerator($topic->getModeratorInfo('close'),$user); ?>
                                        <div>
                                            <small>
                                                <span class="text-danger">Топ <?=($moderator_info['transferred'] ? 'перенесен' : 'закрыт')?>
                                                    <?
                                                    echo $this->renderPartial('common/rendermoderator', ['user' => $moderator_info]);
                                                    ?>
                                                </span>
                                            </small>
                                        </div>
                                    <? endif; ?>
                                <? endif; ?>

                                <? if ($topic->hasComments()) : ?>

                                    <? $comments = $topic->getComments(); ?>

                                    <div>
                                        <a class="small text-danger" data-toggle="collapse" href="#collapseComments<?=$topic->id?>" role="button" aria-expanded="false" aria-controls="collapseComments<?=$topic->id?>">
                                            <span class="oi oi-caret-bottom"></span>
                                        </a>
                                    </div>
                                    <div class="collapse" id="collapseComments<?=$topic->id?>">
                                        <div class="text-wrap">
                                            <? foreach ($comments as $comment) : ?>
                                                <div>
                                                    <small>
                                                        <span class="text-danger">

                                                            <?
                                                            if ($comment['author'] == false) {
                                                                echo $comment['text'];
                                                            } else {

                                                                $comment = \components\Helper\AuthorInfo::buildForComment($comment, $user);

                                                                echo $this->renderPartial('common/renderuser', [
                                                                        'user' => $comment['author']
                                                                    ]) . ' ' . $comment['text'];
                                                            }

                                                            ?>

                                                        </span>
                                                    </small>
                                                </div>
                                            <? endforeach; ?>
                                        </div>
                                    </div>

                                <? endif; ?>

                            <? endif; ?>

                        <? endif; ?>

                    </div>
                </div>

                <div class="row pb-1">
                    <div class="col">

                        <span class="date"><?= $topic->date ?> </span>
                        <? if (!$topic->isDeleted('post')) : ?>
                            <div class="text-wrap d-inline">
                                <?=\components\Helper\Str::limit(strip_tags($topic->text), 100)?>
                            </div>
                        <? else: ?>
                            ...
                        <? endif; ?>

                    </div>

                </div>

                <div class="row">
                    <div class="col-10">
                        <small>
                            Ответов: <?= $topic->children->count() ?> (<?= \Carbon\Carbon::parse($topic->updated)->format('d-m-Y H:i:s') ?>) ...
                            <?= implode(', ', $topic->last_authors);?>
                        </small>
                    </div>
                    <div class="col-2">
                        <?
                        $like_class = $topic->likes->isNotEmpty() ? ' done' : '';
                        if (!$user)
                            $like_class .= ' not';
                        ?>

                        <div class="like-wrapper float-right">
                            <span data-topic="<?= $topic->id ?>" class="d-inline-block pointer like<?= $like_class ?>"></span>
                            <span class="count"><?= $topic->likes_count ?></span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    <? endif;

}
?>


<div class="row py-3">
    <div class="col">
        Страницы: <?=($topics->hasPages() ? $pagination_string : '<b>1</b>')?>
    </div>
</div>


<? if ($user) { ?>

    <? if ($user->hasForumSilence() && !$conf->canCreateWithSilens($user)) : ?>
        <p style="text-align: center">
            <?
                echo $user->getForumSilence()['name'] . ' ' . \components\Helper\TimeHelper::prettyTime(null,$user->getForumSilence()['time']);
            ?>
        </p>
    <? elseif ($user->hasChaos() && !$conf->canCreateTopWithChaos()) :?>
        <p style="text-align: center">
            <?

            if($chaos = $user->getChaos()){
                echo $chaos['name'] . ' ' . \components\Helper\TimeHelper::prettyTime(null,$chaos['time']);
            }

            ?>
        </p>
    <? elseif ($user['level'] < $conf['min_level']) : ?>
        <p style="text-align: center">
            Персонажам до <?=$conf['min_level']?>-го уровня запрещено писать в этой ветке!
        </p>
    <? elseif ($conf->id != 18) : ?>
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

                        <form action="<?= $app->urlFor('topic_create', ['id' => $conf['id']]) ?>" method="post" name="F1" id="needs-validation" novalidate>
                            <h4>Добавить свой вопрос в форум</h4>

                            <div class="form-group row">
                                <div class="col">
                                    <label for="theme">Тема сообщения</label>
                                    <input type="text" name="title" class="form-control form-control-sm" id="theme" value="<?=$flash['title']?>" required>
                                    <div class="invalid-feedback">
                                        Название темы не должно быть пустым
                                    </div>
                                </div>
                            </div>


                            <? if ($user->isAdmin()) :?>
                                <div class="form-group row">
                                    <div class="col">
                                        <input type="checkbox" name="close" value="1"> закрыть тему
                                        <input type="checkbox" name="close_anonym" value="1"> анонимно <br>
                                        <input type="checkbox" name="fix" value="1"> закрепить тему
                                    </div>
                                </div>
                            <? endif; ?>

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
                                        Сообщение не должно быть пустым и максимальное количество символов не должно превышать 300000
                                    </div>
                                    <div class="" id="count_message"></div>
                                </div>
                            </div>



                            <?php if ($app->session->get('captcha_data')['show']): ?>
                            <div class="form-group row">
                                <div class="col">
                                    <?= \components\Helper\Captcha::render() ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="form-group row">
                                <div class="col-10">

                                    <a data-toggle="collapse" href="#collapseIcons" aria-expanded="false" aria-controls="collapseIcons">
                                        <img src="//i.oldbk.com/i/icon7.gif" height=15 width=15 alt="icon">
                                    </a>
                                    <div class="collapse" id="collapseIcons">

                                        <?

                                        for($i = 14; $i >= 1; $i--){

                                            if ($i == 7) {
                                                echo '<br>';
                                            }
                                            echo '<input type="radio" name="icon" value="'.$i.'" '.($i == 13?'checked':'').'><img src="//i.oldbk.com/i/icon'.$i.'.gif" height=15 width=15 alt=""> &nbsp;';
                                        }

                                        ?>

                                    </div>

                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <input type="hidden" name="_token" value="<?=$_token?>">
                                    <input class="btn btn-sm" id="submitbtn" type="submit" value="Добавить" name="add">
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

        $('#search-btn').hover(

            function () {
                $(this).removeClass('btn-secondary');
                $(this).addClass('btn-success');
            },

            function () {
                $(this).removeClass('btn-success');
                $(this).addClass('btn-secondary');
            }
        );


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

                $('input[name=add]').remove();
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
