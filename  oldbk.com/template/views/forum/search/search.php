<?

$by_login = $app->request->get('search_by') === 'login' ? 'checked' : '';

$by_text = $app->request->get('search_by') === 'text' ? 'checked' : (is_null($app->request->get('search_by')) ? 'checked' : '');

$c = $app->request->get('conf_id');
$s = $app->request->get('strict');

?>

<div class="row">
    <div class="col text-center">
        <h4>Поиск по форуму</h4>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="pr-md-4 pt-4 pb-4">
            <form method='get' name='wform' action='<?= $app->urlFor('forum_search') ?>'>

                <div class="form-row input-group">
                    <input type="text" name="word" id="word" class="form-control " placeholder="что ищем?"
                           aria-label="Что ищем?"
                           value="<?= (filled($word) ? $word : ($flash['word'] ?? '')) ?>" minlength="3" required>
                    <span class="input-group-append pointer" id="sizing-addon1">
                        <span class="btn btn-secondary oi oi-trash"
                              onclick="document.getElementById('word').value = ''"></span>
                    </span>
                    <select class="form-control col-2 input-group rounded-0" style="-webkit-appearance: none;"
                            id="where-search" name="conf_id">
                        <option value="">Везде</option>
                        <? foreach ($categories as $category) : ?>
                            <option value="<?= $category['id'] ?>" <?= ($c == $category['id'] ? 'selected' : '') ?>><?= $category['topic'] ?></option>
                        <? endforeach; ?>
                    </select>
                    <span class="input-group-append">
                        <input class="btn btn-secondary btn-sm" type='submit' value='Мне повезет'>
                    </span>
                </div>


                <div class="form-row pt-2">

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="search_by" id="search_by_text"
                               value="text" <?= $by_text ?>>
                        <label class="form-check-label" for="search_by_text">по тексту</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="search_by" id="search_by_login"
                               value="login" <?= $by_login ?>>
                        <label class="form-check-label" for="search_by_login">по логину</label>
                    </div>

                </div>

                <div class="form-row pt-2">

                    <div class="form-check form-check-inline">
                        <label id="strictMode" class="form-check-label <?= ($by_login ? 'd-none' : '') ?>">
                            <input
                                    type="checkbox"
                                    name="strict"
                                    value="1"
                                    class="form-check-input"
                                <?= ($s ? 'checked' : (blank($by_text) ? 'checked' : '')) ?>>
                            точная фраза
                        </label>
                    </div>

                </div>

            </form>
        </div>
    </div>
</div>


<? if (filled($word)) : ?>

    <?
    $pagination_string = $this->renderPartial('pagination/pagination', ['paginator' => $topics, 'elements' => $elements], true);
    ?>

    <h4 class="pb-4">Результат поиска (<?= $topics->total() ?>)</h4>
    <p>Конференция: <span class="font-weight-bold"><?= !$c ? 'Все' : $categories->find($c)->topic ?></span></p>


    <div class="row py-3">
        <div class="col">
            Страницы: <?= ($topics->hasPages() ? $pagination_string : '<b>1</b>') ?>
        </div>
    </div>


    <?

    foreach ($topics as $topic) {

        if (!$topic->isDeleted('top') || ($user && $user->isForumModerator())) : ?>

            <div class="row">
                <div class="col">

                    <div class="py-2">
                        <img height="15" src="//i.oldbk.com/i/icon<?= $topic->icon ?>.gif" width="15" border="0">


                        <a target="_blank" href="<?= $app->urlFor('forum_topic', [
                            'id' => ($topic->topic != '' ? $topic->id : $topic->parent)
                        ]) ?>"><?= ($topic->topic != '' ? $topic->topic : $topic->mtop) ?></a>
                    </div>

                    <?= $this->renderPartial('common/renderuser', ['user' => $topic['post_author']]); ?>

                    <span class="date">(<?= $topic->date ?>)</span>


                    <? if ($topic->isDeleted('post')) : ?>

                        <? if ($user && $user->canSeeDeletedPost()) { ?>
                            <p class="post_body text-wrap text-muted"><?= $topic->text ?></p>
                        <? } ?>

                        <p class="text-danger">
                            <b>
                                Удалено <?= $this->renderPartial('common/rendermoderator', ['user' => $topic->getModeratorInfo('post')]); ?>
                            </b>
                        </p>

                    <? else : ?>

                        <div class="post_body text-wrap"><?= $topic->text ?></div>

                    <? endif; ?>

                </div>
            </div>
            <hr>
        <? endif;

    }

    ?>

    <div class="row py-3">
        <div class="col">
            Страницы: <?= ($topics->hasPages() ? $pagination_string : '<b>1</b>') ?>
        </div>
    </div>


<? endif; ?>

<script>

    $('.form-check-input').on('change', function (e) {
        var targ;

        if (e.target) { // W3C
            targ = e.target;
        } else if (e.srcElement) { // IE6-8
            targ = e.srcElement;
        }
        if (targ.nodeType === 3) { // Safari
            targ = targ.parentNode;
        }

        targ.value === 'login'
            ? $('#strictMode').addClass('d-none')
            : $('#strictMode').removeClass('d-none');
    });

</script>
