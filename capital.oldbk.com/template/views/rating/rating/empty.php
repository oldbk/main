<?php
use components\Helper\TimeHelper;

/**
 * Created by PhpStorm.
 * User: me
 * Date: 12.10.2018
 * Time: 20:03
 *
 * @var \components\models\EventRating $model
 */ ?>

<div class="block-item col-6">
	<div>
		<div class="block-content">
			<div class="block-logo">
				<img src="http://i.oldbk.com/i/newd/<?= $model->icon ?>">
			</div>
			<div class="block-text">
				<div class="block-title">
					<?php if($model->link_encicl): ?>
                        <a href="<?= $model->link_encicl ?>" target="_blank"><?= $model->name; ?></a>
					<?php else: ?>
						<?= $model->name; ?>
					<?php endif; ?>
				</div>
				<div class="details">
					<?php if($model->isActive): ?>
                        <div style="color: green;font-weight: bold">Доступен</div>
					<?php endif; ?>
					<br>
					<div><a href="<?= $model->link ?>" target="_blank">Место: 500+</a> / Очки: 0</div>
				</div>
			</div>
		</div>
	</div>
	<div class="hr">
		<small>
            <?php if($model->dateend && $model->isActive): ?>
                <i>Продлится еще <?= TimeHelper::prettyTime(
                        time(),
                        $model->dateend->getTimestamp(),
                        false,
                        [
                            'm' => '<strong>%m</strong> мес.',
                            'd' => '<strong>%d</strong> дн.',
                            'h' => '<strong>%h</strong> ч.',
                            'i' => '<strong>%i</strong> мин.',
                        ]) ?></i>
            <?php elseif($model->datestart && !$model->isActive): ?>
                <i>
                    Будет доступен через: <?= TimeHelper::prettyTime(
						time(),
						$model->datestart->getTimestamp(),
						false,
						[
							'm' => '<strong>%m</strong> мес.',
							'd' => '<strong>%d</strong> дн.',
							'h' => '<strong>%h</strong> ч.',
							'i' => '<strong>%i</strong> мин.',
						]) ?>
                </i>
            <?php endif; ?>
        </small>
	</div>
</div>
