<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 08.06.2018
 * Time: 22:47
 *
 *
 * @var \components\models\UserEventRating[] $items
 * @var \components\models\EventRating[] $Ratings
 * @var boolean $isHorn
 */

use \components\Helper\TimeHelper;
?>
<?php foreach ($items as $item): ?>
    <div class="row item">
        <div class="col-3 text-center">
			<?php if($item['img']): ?>
                <div><img src="<?= $item['img'] ?>" height="30"></div>
			<?php endif; ?>
            <div class="title">
				<?php if($item['link']): ?>
                    <a href="<?= $item['link'] ?>" target="_blank"><?= $item['title'] ?></a>
				<?php else: ?>
					<?= $item['title'] ?>
				<?php endif; ?>
            </div>
        </div>
        <div class="col-6">
			<?= $item['desc']; ?>
        </div>
        <div class="col-3 text-center">
			<?php if($item['enable']): ?>
                <strong style="color: green">Доступно</strong>
			<?php else: ?>
				<?php if(isset($item['end_time'])): ?>
                    <div>Осталось: <?= TimeHelper::prettyTime(null, $item['end_time']) ?></div>
				<?php endif; ?>
				<?php if(isset($item['end_fight'])): ?>
                    <div>Боев: <?= $item['end_fight'] ?></div>
				<?php endif; ?>
			<?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="separate"></div>
        </div>
    </div>
<?php endforeach; ?>
<?php if(empty($items)): ?>
    <div style="text-align: center">
        <i>На данный момент у вас нет никаких задержек.</i>
    </div>
<?php endif; ?>