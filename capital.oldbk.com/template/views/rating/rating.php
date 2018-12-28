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
 * @var \components\Component\Slim\View $this
 */
?>
<?php if($items || $Ratings): ?>
    <div class="row">
        <?php foreach ($Ratings as $Rating) {
            echo $this->renderPartial('rating/empty', ['model' => $Rating]);
        } ?>
		<?php foreach ($items as $item) {
			echo $this->renderPartial('rating/process', ['model' => $item]);
        } ?>
    </div>
<?php endif; ?>
<?php if(empty($items)): ?>
    <div class="text-center">
        <i>На данный момент нет активных рейтингов. Попробуйте позже.</i>
    </div>
<?php endif; ?>