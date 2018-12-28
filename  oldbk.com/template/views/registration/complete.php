<div class="row">

    <div class="col-8 mx-auto text-center py-5">
        <h5 class="reg-title text-success">Регистрация завершена. Можете зайти в игру.</h5>
    </div>

</div>

<div class="row">
    <div class="col-md-6 col-sm-8 mx-auto">
        <form id="regf" action="<?= $app->urlFor('login', $app->request->get())?>" method="post">
            <input name="login" type="hidden" value="<?=($login ?? '')?>">
            <input name="psw" type="hidden" value="<?=($psw ??'')?>">

            <div class="pt-1">
                <img src="/i/down__buttBg.jpg" alt="" class="img-fluid mx-auto">
            </div>

            <div class="pt-5 mx-auto">
                <input id="reg_button" type="submit" class="im-61 d-block mx-auto" value="">
            </div>
        </form>
    </div>
</div>

<?
if ($pid && is_string($pid)) {
    include $pid;
    $app->session->delete('reg_id');
}
?>

<script>
    setTimeout(function () {
        $('#regf').submit();
    }, 2000);
</script>
