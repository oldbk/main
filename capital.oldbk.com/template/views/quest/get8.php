<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 03.03.2016
 */ ?>


<?php
$text = <<<EOT
    <table>
        <colgroup>
            <col width="120px">
            <col>
        </colgroup>
        <tr>
            <td colspan="2" align="center">
                <div style="text-align:center;margin-bottom:10px;">
                    <b>Поздравь девушек с праздником весны!</b>
                </div>
            </td>
        </tr>
        <tr>
            <td align="center">
                <img src="http://i.oldbk.com/i/city/sub/vesna_cap_flowershop.png">
            </td>
            <td>
                Собери свой или купи готовый букет в <b>цветочном магазине</b>, подари его любой <b>девушке</b> в ОлдБК и получи награду.<br>
                Успей сделать подарок до полуночи 8 марта!
            </td>
        </tr>
    </table>
    <br>
    <center><a href="{$app->urlFor('quest', array('action' => 'quest8', 'get' => true))}">Принять</a> <a style="margin-left:15px;" href="#" onclick="$('#questdiv').hide();return false;">Закрыть</a></center>
EOT;
?>

<?php
echo $this->renderPartial('common/cp_popup', array('text' => $text));
?>