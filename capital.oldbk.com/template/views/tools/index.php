<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 03.12.2015
 *
 * @var \components\Component\Slim\Slim $app
 */ ?>

<div class="" style="text-align: center;">
    <div class="flash">
        <?php foreach ($app->flashData() as $type => $message): ?>
            <div class="alert alert-<?= $type ?>">
                <?= $message; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <form id="invalidate-form" method="post" action="<?= $app->urlFor('tools', array('action' => 'invalidate')) ?>">
        <div>
            <select name="p1" style="width: 414px;padding: 5px;">
                <option value="i.oldbk.com">i.oldbk.com</option>
                <option value="oldbk.com">oldbk.com</option>
                <option value="capitalcity.oldbk.com">capitalcity.oldbk.com</option>
                <option value="chat.oldbk.com">chat.oldbk.com</option>
            </select>
            <input type="text" name="p2" style="width: 400px;padding: 5px;">
        </div>
        <br>
        <div>
            <a href="javascript:void(0);" onclick="$('#invalidate-form').submit();" class="button-big btn" title="Инвалидировать">Инвалидировать</a>
        </div>
    </form>
</div>
