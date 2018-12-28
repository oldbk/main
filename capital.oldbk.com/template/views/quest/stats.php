<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.01.2016
 */ ?>
<style>
    table td {
        text-align: center;
        vertical-align: middle !important;
    }
</style>
<ul>
    <li>TYPE ID: </li>
    <li>1 - дроп</li>
    <li>3 - бой</li>
    <li>4 - использование магии</li>
    <li>5 - подарок</li>
    <li>6 - коммент</li>
    <li>7 - кастомный</li>
</ul>
<table border="1">
    <thead>
        <tr>
            <th>Квест</th>
            <th>Всего взяли</th>
            <th>Выполнили</th>
            <th>Еще в процессе или не выполнили</th>
            <th>Сколько частей</th>
            <th>Стата по частям</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($stats as $quest): ?>
        <tr>
            <td><?= $quest['quest'] ?> (<?= $quest['start'] ?> - <?= $quest['end'] ?>)</td>
            <td><?= $quest['user_count'] ?></td>
            <td><?= $quest['user_count_finished'] ?></td>
            <td><?= $quest['user_process'] ?></td>
            <td><?= count($quest['part']) ?></td>
            <td>
                <ul>
                    <?php foreach ($quest['part'] as $_part): ?>
                        <li>TYPE ID: (<?= $_part['type_id'] ?>): <?= $_part['count']; ?> (Type count:  <?= $_part['need_count'] ?>)</li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
