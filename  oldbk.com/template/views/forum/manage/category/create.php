<a href="<?= $app->urlFor('manage_category_list') ?>">
    <img title="" src="/assets/forum/i/undo.png" width="12" height="12">
</a>

<div class="row text-center">
    <div class="col">
        <h4>Новая конференция</h4>
    </div>
</div>


<div class="row">
    <div class="col">

        <form action="<?=$app->urlFor('manage_category_save')?>" method="post">

            <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">id</label>
                <div class="col-sm-2">
                    <select class="form-control" name="id" id="cat_id">
                        <? foreach ($available_cat_ids as $id): ?>
                            <option value="<?=$id?>"><?=$id?></option>
                        <? endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="inputText" class="col-sm-2 col-form-label">Название</label>
                <div class="col-sm-8">
                    <input type="text" name="topic" class="form-control" id="inputText" placeholder="Название">
                </div>
            </div>

            <div class="form-group row">
                <label for="inputDesc" class="col-sm-2 col-form-label">Описание</label>
                <div class="col-sm-8">
                    <input type="text" name="text" class="form-control" id="inputDesc" placeholder="Описание">
                </div>
            </div>

            <div class="form-group row">
                <label for="inputFix" class="col-sm-2 col-form-label">Позиция</label>
                <div class="col-sm-8">
                    <input type="text" name="fix" class="form-control" id="inputFix" placeholder="Позиция">
                </div>
            </div>

            <div class="form-group row">
                <label for="input-min_align" class="col-sm-2 col-form-label">min_align</label>
                <div class="col-sm-8">
                    <input type="text" name="min_align" class="form-control" id="input-min_align" placeholder="min_align" value="0">
                </div>
            </div>

            <div class="form-group row">
                <label for="input-max_align" class="col-sm-2 col-form-label">max_align</label>
                <div class="col-sm-8">
                    <input type="text" name="max_align" class="form-control" id="input-max_align" placeholder="max_align" value="0">
                </div>
            </div>

            <div class="form-group row">
                <label for="input-min_level" class="col-sm-2 col-form-label">min_level</label>
                <div class="col-sm-8">
                    <input type="text" name="min_level" class="form-control" id="input-min_level" placeholder="min_level" value="0">
                </div>
            </div>

            <div class="form-group row">
                <label for="input-max_level" class="col-sm-2 col-form-label">max_level</label>
                <div class="col-sm-8">
                    <input type="text" name="max_level" class="form-control" id="input-max_level" placeholder="max_level" value="0">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2">Только свои топы</div>
                <div class="col-sm-10">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" name="only_own" value="1" type="checkbox"> разрешается видеть только свои топы
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2">Только для тестеров</div>
                <div class="col-sm-10">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" name="only_tester" value="1" type="checkbox"> разрешается видеть только топы для тестеров
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-success">Сохранить</button>
                </div>
            </div>
        </form>

    </div>
</div>
