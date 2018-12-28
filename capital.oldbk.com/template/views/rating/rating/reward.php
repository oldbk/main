<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 15.09.2018
 * Time: 19:44
 */ ?>

<div class="block-title">
    Награда
</div>
<ul id="reward-list">
	<?php foreach ($items as $item): ?>
        <li class="item-render">
            <div class="item-count"><?= $item['count'] ?></div>
            <a href="<?= $item['link'] ?>" target="_blank">
                <img src="<?= $item['img'] ?>">
            </a>
        </li>
	<?php endforeach; ?>
</ul>
<div style="text-align: center">
    <div class="button-mid btn f-close" title="Закрыть">Закрыть</div>
</div>
