<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 20.11.16
 * Time: 16:22
 *
 * @var string $message
 */ ?>

<div id="actiondiv" class="actiondiv" style="z-index: 99999; position: absolute; left: 50%; top: 10%; margin-left: -275px;">
    <table border="0" cellspacing="0" cellpadding="0" width="505">
        <tbody>
        <tr>
            <td width="505" style="background-repeat-y:no-repeat;" background="http://i.oldbk.com/i/newd/pop/up_bg.jpg">
                <img onclick="$('.actiondiv').remove();" onmouseout="this.src='http://i.oldbk.com/i/newd/pop/close_butt.jpg';" onmouseover="this.src='http://i.oldbk.com/i/newd/pop/close_butt_hover.jpg';" src="http://i.oldbk.com/i/newd/pop/close_butt.jpg" align="right">
            </td>
        </tr>
        <tr>
            <td background="http://i.oldbk.com/i/newd/pop/bg-y.jpg" align="center" style="padding: 10px;">
                <?= $message ?>
            </td>
        </tr>
        <tr>
            <td width="500" height="8" background="http://i.oldbk.com/i/newd/pop/down_bg.jpg"></td>
        </tr>
        </tbody>
    </table>
</div>