<?php
use components\Helper\TimeHelper;

/**
 * Created by PhpStorm.
 * User: me
 * Date: 12.10.2018
 * Time: 20:03
 *
 * @var \components\models\UserEventRating $model
 */

$position = $model->getPosition();
?>

<div class="block-item col-6">
    <div class="rating-<?= $model->id ?>">
        <div class="block-content">
            <div class="block-logo">
                <img src="http://i.oldbk.com/i/newd/<?= $model->rating->icon ?>">
            </div>
            <div class="block-text">
                <div class="block-title">
					<?php if($model->rating->link_encicl): ?>
                        <a href="<?= $model->rating->link_encicl ?>" target="_blank"><?= $model->rating->name; ?></a>
					<?php else: ?>
						<?= $model->rating->name; ?>
					<?php endif; ?>
                </div>
                <div class="details">
                    <?php if($model->rating->isActive && $model->is_end == 0): ?>
                        <div style="color: green;font-weight: bold">Доступен</div>
                    <?php elseif($model->rating->datestart && $model->is_end == 0): ?>
						<i>Будет доступен через: <?= TimeHelper::prettyTime(
							time(),
							$model->rating->datestart->getTimestamp(),
							false,
							[
								'm' => '<strong>%m</strong> мес.',
								'd' => '<strong>%d</strong> дн.',
								'h' => '<strong>%h</strong> ч.',
								'i' => '<strong>%i</strong> мин.',
                            ]) ?></i>
                    <?php elseif($model->is_end == 1): ?>
                        <div style="color: red;font-weight: bold">Завершен</div>
                    <?php endif; ?>
                    <br>
                    <div><a href="<?= $model->rating->link ?>?uid=<?= $model->user_id ?>" target="_blank">Место: <?= $position <= 500 ? $position : '500+' ?></a> / Очки: <?= $model->value ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="hr rating-<?= $model->id; ?>">
        <?php if($model->is_end == 0 && $model->rating->dateend && $model->rating->isActive): ?>
            <i>Продлится еще <?= TimeHelper::prettyTime(
					time(),
					$model->rating->dateend->getTimestamp(),
					false,
					[
						'm' => '<strong>%m</strong> мес.',
						'd' => '<strong>%d</strong> дн.',
						'h' => '<strong>%h</strong> ч.',
						'i' => '<strong>%i</strong> мин.',
					]) ?></i>
        <?php elseif($model->is_end == 1 && $position <= 500): ?>
            <div class="btn-wrapper">
                <div class="f-loader"></div>
                <div class="button-big btn reward" data-rating-id="<?= $model->id ?>" title="Получить награду">Забрать награду</div>
            </div>
        <?php endif; ?>
    </div>
</div>
