<div class="row text-center">
    <div class="col">
        <h4>Конференции</h4>
    </div>
</div>

<div class="row py-3">
    <div class="col">
        <a class="btn btn-success" href="<?=$app->urlFor('manage_category_create')?>">
            <img src="http://i.oldbk.com/i/up.gif" alt="" title="">
            Добавить новую
        </a>
    </div>
</div>

<div class="row">
    <div class="col">
        <table class="table table-hover table-responsive">
            <thead>
            <tr>
                <th scope="col">id категории</th>
                <th scope="col">Название</th>
                <th scope="col">Позиция</th>
                <? if ($user->isAdmin() || $user->isPaladin()) : ?>
                <th scope="col"><span class="oi oi-wrench"></span></th>
                <? endif; ?>
            </tr>
            </thead>
            <tbody>

                <? foreach ($cats as $cat): ?>

                    <tr>
                        <td>
                            <?=$cat->id?>
                        </td>
                        <td>
                            <a <?=($cat->is_closed ? 'class="text-danger"' : '')?> href="<?=$app->urlFor('manage_category', ['id' => $cat->id]) ?>"><?=$cat->topic?></a>
                        </td>
                        <td>
                            <?=$cat->fix?>
                        </td>

                        <? if ($user->isAdmin() || $user->isPaladin()) : ?>
                            <td>
                                <a href="<?=$app->urlFor('manage_category_delete', ['id' => $cat->id])?>" onclick="if(confirm('Удалить категорию?')) {return true;} return false;">
                                    <img src="/assets/forum/i/clear.gif" alt="Удалить категорию" width="8" height="8">
                                </a>
                            </td>
                        <? endif; ?>

                    </tr>

                <? endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


