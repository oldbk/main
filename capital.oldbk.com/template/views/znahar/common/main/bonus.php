<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 *
 * @var \components\Component\Slim\Slim $app
 */ ?>

<tr class="odd">
    <td>
        Знахарь приветствует тебя, и просит оставить усиления и бонусы за пределами хижины.
        <a href="<?= $app->urlFor('znahar', array('action' => 'delbonus')) ?>" class="button-mid btn" title="Убрать бонусы">Убрать бонусы</a>
    </td>
</tr>
