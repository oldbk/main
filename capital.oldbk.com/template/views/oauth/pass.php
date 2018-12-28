<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 03.11.2015
 *
 * @var array() $user_list
 */ ?>
<style>
    .red {
        color: red;
    }
</style>
<table border="1">
    <thead>
    <tr>
        <th>Логин</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($user_list as $user_id => $user): ?>
            <tr>
                <td valign="top">
                    <?= $user['login'] ?> (<?= $user_id ?>)
                </td>
                <td valign="top">
                    <?= implode('<br>', $user['text']) ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
