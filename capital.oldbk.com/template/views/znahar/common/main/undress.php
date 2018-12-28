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
        Знахарь приветствует тебя, и просит снять доспехи и оружие перед входом в Хижину.
        <a href="<?= $app->urlFor('znahar', array('action' => 'undress')) ?>" class="button-mid btn" title="Раздеться">Раздеться</a>
    </td>
</tr>
