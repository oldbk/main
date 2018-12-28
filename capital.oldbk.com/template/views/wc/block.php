<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 11.06.2018
 * Time: 20:05
 *
 * @var \components\models\WcEvent $Event
 * @var \components\models\WcEventBet $Bet
 * @var boolean $isHorn
 */ ?>


<div class="map-block" data-event-id="<?= $Event->id ?>">
	<div class="map-block-logo bashnja"></div>
	<div class="map-block-mid bashnja">
		<div class="map-block-head">
			<?= sprintf('%s - %s', $Event->team1, $Event->team2) ?> <img title="<?= date('d.m H:i', $Event->datetime) ?>" alt="<?= date('d.m H:i', $Event->datetime) ?>" src="http://i.oldbk.com/i/world_map2/i_2.jpg">
		</div>
		<div class="map-block-bottom">
			<?php switch (true) {
				case ($Event->datetime > time()): ?>
					<div class="f-res">
						Ставка: <?= $Bet->getBetType() ?>
					</div>
					<?php break; ?>
				<?php case ($Bet->is_win): ?>
					<div class="f-res">
						Ставка <?= $Bet->getBetType() ?> выиграла <?= sprintf('(%d:%d)', $Event->team1_res, $Event->team2_res) ?>
					</div>
					<?php if(!$Bet->is_rewarded): ?>
						<div class="btn-wrapper">
							<div class="button-mid btn reward" title="Забрать">Забрать</div>
						</div>
					<?php endif; ?>
					<?php break; ?>
				<?php case (!$Bet->is_win): ?>
					<div class="f-res">
						Ставка <?= $Bet->getBetType() ?> не сыграла <?= sprintf('(%d:%d)', $Event->team1_res, $Event->team2_res) ?>
					</div>
					<?php break; ?>
				<?php } ?>
		</div>
	</div>
	<div class="map-block-right bashnja"></div>
</div>
