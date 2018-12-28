<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 25.11.2015
 *
 * @var array $travma
 */ ?>

<tr class="odd">
    <td>
        <div style="color: red;">
            Вы не можете воспользоваться услугами знахаря имея травму, будучи невидимкой, либо с эффектом магии "Ледяной Интеллект"!
        </div>
        <div>Вы чувствуете слабость... <span class="mhint">(еще <?= \components\Helper\TimeHelper::prettyTime(null, $travma['time'], true) ?>)</span></div>
    </td>
</tr>
