<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 02.11.2015
 */ ?>
<style>
    .block {
        -webkit-box-shadow: 0px 0px 15px 3px rgba(0,0,0,0.75);
        -moz-box-shadow: 0px 0px 15px 3px rgba(0,0,0,0.75);
        box-shadow: 0px 0px 15px 3px rgba(0,0,0,0.75);
        top: 20%;
        position: absolute;
        left: 50%;
        margin-left: -250px;
        min-height: 50px;
        width: 500px;
        padding: 10px;
    }
    .block-bottom {
        font-style: italic;
    }
</style>
<script>
$(function(){
    function timer(){
        var val = $('#time').html();
        val--;

        if(val==0)
            window.close();
        else
            setTimeout(timer, 1000);
        $('#time').html(val);
    }
    setTimeout(timer, 1000);

});
</script>
<div class="block">
    <div class="block-description">
        <table width="100%">
            <colgroup>
                <col>
                <col width="100px">
            </colgroup>
            <tr>
                <td valign="top">
                    <?= $message ?>
                </td>
                <td valign="top">
                    <img src="/images/sn/<?= $sn_type ?>.png">
                </td>
            </tr>
        </table>
    </div>
    <div class="block-bottom">Вкладка закроется через: <span id="time">15</span>сек. <a href="javascript:void(0);" onclick="window.close();">Закрыть сейчас</a></div>
</div>
