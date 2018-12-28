<div class="row">
    <div class="col">
        <a href="<?=$app->urlFor('manage_list')?>">
            <span class="oi oi-arrow-left"></span> Список модераторов
        </a>
    </div>
</div>


<div class="row py-3">
    <div class="col">
        <?= $moderator->getTitle() . ' ' . $this->renderPartial('common/renderuser', ['user' => $moderator->toArray()]) ?>

        <?
        if ($moderator->status) {
            echo ' - ' . $moderator->status;
        }

        if ($moderator->isPaladin()) {
            echo '<h6>Обработано жалоб за текущий месяц: <span class="badge badge-success">'.$moderator->moderator_appeals_count.'</span></h6>';
        }
        ?>
    </div>
</div>


<div class="row">

    <div class="col">
        <form action="<?=$app->urlFor('manage_save_permission', ['id' => $moderator->id])?>" method="post" id="permission_form">

            <table class="table table-hover table-responsive">

                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Вкл/Выкл</th>
                    </tr>
                </thead>
                <? foreach ($permissions as $key => $perm) :
                    $key = strtolower($key);
                ?>

                    <tr>
                        <td>
                            <label  for="<?=$key?>"><?= \Lang::get('forum.permissions_forum')[$perm] ?? $key ?></label>
                        </td>
                        <td class="px-5 text-center">
                            <div class="form-check">
                                    <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="<?=$key?>"
                                            name="<?=$key?>"
                                        <?= ($moderator->moderator && is_array($moderator->moderator->permissions) && in_array($key, $moderator->moderator->permissions)) ? 'checked' : ''; ?>
                                    >
                            </div>
                        </td>
                    </tr>

                <? endforeach; ?>

            </table>

            <div class="row py-3">
                <div class="col">
                    <input type="button" class="btn btn-danger" id="perm_uncheck" value="Сбросить">
                    <input type="button" class="btn btn-info" id="perm_check" value="Выбрать все">
                    <input type="submit" class="btn btn-success" value="Сохранить">
                </div>
            </div>

        </form>
    </div>

</div>


<script>
    $('#perm_uncheck').on('click', function (event) {
        event.preventDefault();

        $('input.form-check-input').each(function () {

            $(this).prop('checked', false);

        });
    });

    $('#perm_check').on('click', function (event) {
        event.preventDefault();

        $('input.form-check-input').each(function () {

            $(this).prop('checked', true);

        });
    });
</script>

