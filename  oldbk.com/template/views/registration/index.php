<?php

/**
 * @var Slim\Middleware\Flash $flash
 */
?>


<form id="register-form" role="form"
      action="<?= $app->urlFor('registration', array_merge($app->request->get(), ['action' => 'save'])) ?>"
      method="post" novalidate>

    <div class="row">

        <div class="col-lg-5 col-md-6 col-sm-8 mx-auto">

            <div class="form-group has-feedback pt-3">

                <label for="fnlogin" class="hh2">Имя персонажа</label>
                <input
                        name="login"
                        type="text"
                        class="form-control"
                        id="fnlogin"
                        placeholder="Имя персонажа"
                        data-check-login
                        required
                        value="<?= $flash['login'] ?>"
                        data-toggle="tooltip"
                        data-placement="right"
                        data-trigger="focus"
                        data-html="true"
                        title="<p>Имя может содержать от 4 до 20 символов, состоять только из букв русского либо английского алфавита, цифр, символов '_', '-' и пробела.</p><p>Имя не может начинаться или заканчиваться пробелом или символом '_'.</p><p>Также не должно присутствовать подряд более 1 символа '_' или '-', более 1 пробела и более 3 других одинаковых символов или более 4-х цифр.</p>"
                >
                <button class="badge badge-secondary" type="button" id="checklogin">Проверить это имя</button>

                <div class="form-control-feedback">
                    <div class="help-block with-errors text-wrap"></div>
                </div>

            </div>

            <div class="form-group has-feedback">
                <label for="fnemail" class="hh2">Ваш email</label>
                <input
                        type="email"
                        name="email"
                        class="form-control"
                        id="fnemail"
                        placeholder="Ваш email"
                        data-check-email
                        data-toggle="tooltip"
                        data-placement="right"
                        data-trigger="focus"
                        data-html="true"
                        title="Необходим для напоминания пароля и не отображается в информации о персонаже"
                        value="<?= $flash['email'] ?>"
                        required
                >
                <div class="form-control-feedback">
                    <div class="help-block with-errors text-wrap"></div>
                </div>
            </div>

            <div class="form-group has-feedback">
                <label for="fnsex" class="hh2">Ваш пол</label>
                <select id="fnsex" class="form-control" name="sex" required>
                    <option></option>
                    <option value="1" <?= ($flash['sex'] == '1' ? "selected" : "") ?>>мужской</option>
                    <option value="0" <?= ($flash['sex'] == '0' ? "selected" : "") ?>>женский</option>
                </select>
                <div class="form-control-feedback">
                    <div class="help-block with-errors text-wrap"></div>
                </div>
            </div>

            <div class="form-group has-feedback">
                <label for="fnpass1" class="hh2">Пароль</label>
                <input type="password" name="psw" class="form-control" id="fnpass1" placeholder="Пароль"
                       data-minLength="6" data-maxLength="20" data-error="Пароль должен быть от 6 до 20 символов!"
                       data-toggle="tooltip"
                       data-placement="right"
                       data-trigger="focus"
                       data-html="true"
                       title="Введите пароль и пароль повторно, они должны совпадать.Пароль должен быть от 6 до 20 символов!"
                       required
                >
                <div class="form-control-feedback">
                    <div class="help-block with-errors text-wrap"></div>
                </div>
            </div>

            <div class="form-group has-feedback">
                <label for="fnpass2" class="hh2">Пароль повторно</label>
                <input type="password" name="psw2" class="form-control" id="fnpass2" placeholder="Пароль повторно"
                       data-minLength="6" data-maxLength="20" data-error="Пароль должен быть от 6 до 20 символов!"
                       data-match="#fnpass1" data-match-error="Пароли не совпадают"
                       data-toggle="tooltip"
                       data-placement="right"
                       data-trigger="focus"
                       data-html="true"
                       title="Введите пароль и пароль повторно, они должны совпадать.Пароль должен быть от 6 до 20 символов!"
                       required
                >
                <div class="form-control-feedback">
                    <div class="help-block with-errors text-wrap"></div>
                </div>
            </div>

            <input id="A3" name="Law" type="hidden" checked="" value="1">

            <?php if($sn_data): ?>
                <input type="hidden" name="sid" value="<?= $sn_data['sid'] ?>">
            <?php endif; ?>

            <div class="col-12 text-center">
            <span id="helpBlock" class="help-block">
                Регистрируясь, вы подтверждаете, что ознакомлены с <a target="_blank" href="https://oldbk.com/encicl/?/agreement.html">соглашением о предоставлении сервиса</a> и <a
                        target="_blank" href="https://oldbk.com/encicl/rules.html">правилами</a> игры.
            </span>
            </div>

            <div class="p-3 text-center">
                <?= \components\Helper\Captcha::render() ?>
            </div>

        </div>

    </div>

    <div class="row">
        <div class="col-12 text-center pt-3">
            <img src="/i/down__buttBg.jpg" alt="" class="img-fluid mx-auto">
        </div>

        <div class="col-12 text-center pt-3 mx-auto">
            <input id="reg_button" type="submit" class="im-61 d-block mx-auto" value="">
        </div>
    </div>
</form>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>