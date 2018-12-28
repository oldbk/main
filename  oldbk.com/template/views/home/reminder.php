<?
$msg = '';

if (isset($flash['errors'])) {
    foreach ($flash['errors'] as $error) {
        $msg .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
    }
}

if (isset($flash['success'])) {
    foreach ($flash['success'] as $success) {
        $msg .= '<div class="alert alert-success" role="alert">'.$success.'</div>';
    }
}

?>

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
                <div class="kp-title-news-name fl"><h3>Восстановление пароля</h3></div>
            </div>
        </div>
        <div class="kp-news-content">
                <div class="col-md-12">

                    <?=$msg?>

                    <p>Для восстановления пароля, введите свой login и нажмите кнопку <b>"отправить письмо"</b>.
                    Письмо с паролем будет выслано на Ваш <b>e-mail адрес, указанный Вами при регистрации</b>.
                    Данную услугу можно использовать только раз в сутки.</p>

                    <form name="sendmailid" method="post">
                        <div class="form-group col-lg-6 col-lg-offset-3">
                            <label for="loginid">Введите Ваш логин в игре</label>
                            <input type="text" class="form-control" id="loginid" name="loginid">
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group">
                            <?=\components\Helper\Captcha::render()?>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" onClick="sendmailpassw()" class="btn btn-primary mx-auto">Отправить письмо</button>
                        </div>
                    </form>

                </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<script>
    function sendmailpassw() {
        var loginP = document.getElementById("loginid").value;
        if (loginP == "" || loginP.length > 50) {
            alert("Введен некоректный login");
            return false;
        }
        else document.sendmailid.submit();
    }
</script>