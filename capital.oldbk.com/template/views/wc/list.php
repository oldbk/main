<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 08.06.2018
 * Time: 22:47
 *
 *
 * @var \components\models\WcEvent[][] $EventsGroup
 * @var \components\models\WcEventBet[] $UserBets
 * @var boolean $isHorn
 */ ?>
<link rel="stylesheet" href="http://capitalcity.oldbk.com/i/btn.css" type="text/css">
<style>
	.popup-wrapper {
		width: 800px;
		position: absolute;
		top: 10%;
		left: 50%;
		margin-left: -400px;
		z-index: 100;
	}
	.f-lay {
		background-image: url("http://i.oldbk.com/i/newd/pop/bg-y_4.jpg");
		padding: 0 20px;
		padding-bottom: 10px;
	}
	.f-header {
		background-image: url("http://i.oldbk.com/i/newd/pop/up_bg_2_2.jpg");
		height: 17px;
	}
	.f-info {
		background-image: url("http://i.oldbk.com/i/newd/pop/bg-y_4_2.jpg");
		text-align: center;
		padding-bottom: 6px;
	}
	.f-footer {
		background-image: url("http://i.oldbk.com/i/newd/pop/down_bg_2.jpg");
		height: 8px;
	}

	.map-block {
		display: table;
		table-layout: fixed;
		width: 100%;
		height: 59px;
		min-width: 309px;
		border-spacing: 0;
	}
	.map-block .map-block-logo {
		width: 65px;
		display: table-cell;
		border-spacing: 0;
	}
	.map-block .map-block-logo.bashnja {
		background-image: url("http://i.oldbk.com/i/newd/pop/wc_logo.jpg");
	}
	.map-block .map-block-right {
		width: 8px;
		display: table-cell;
		border-spacing: 0;
	}
	.map-block .map-block-right.bashnja {
		background-image: url(http://i.oldbk.com/i/world_map2/5_bashnja_end.jpg);
	}
	.map-block .map-block-mid {
		display: table-cell;
		border-spacing: 0;
		text-align: center;
	}
	.map-block .map-block-mid.bashnja {
		background-image: url(http://i.oldbk.com/i/world_map2/5_bashnja_bgx.jpg);
	}
	.map-block .map-block-mid .map-block-head {
		font-weight: 700;
		font-size: 12px;
		height: 20px;
		margin-top: 4px;
		vertical-align: middle;
		padding-top: 4px;
		position: relative;
	}
	.map-block .map-block-mid .map-block-bottom {
		padding-top: 3px;
		height: 25px;
	}
	.f-list {
		margin: 0;
		list-style: none;
		padding: 0;
	}
    .f-list.hide {
        display: none;
    }
	.f-list li {
        width: 350px;
        float: none;
        display: block;
	}
	.f-list li.f-list-left {
		float: left;
	}
	.f-list li.f-list-right {
		float: right;
		width: 367px;
	}
	.f-list li.f-clear:after {
		display: block;
		content: "";
		clear: both;
	}
	.map-block .map-block-mid .map-block-bottom .f-res {
		display: inline-block;
		font-size: 11px;
	}

	div.disabled, a.disabled {
		pointer-events: none;
		opacity: 0.6;
	}
    #no-item.hide {
        display: none;
    }
    #f-close {
        height: 17px;
        width: 26px;
        position: absolute;
        right: 0px;
        cursor: pointer;
        background-image: url("http://i.oldbk.com/i/newd/pop/close_butt.jpg");
    }
    #f-close:hover {
        background-image: url("http://i.oldbk.com/i/newd/pop/close_butt_hover.jpg");
    }
    .f-lay .btn-wrapper {
        display: inline-block;
        position: relative;
    }
    .f-lay .btn-wrapper .f-loader {
        display: none;
        position: absolute;
        height: 21px;
        width: 21px;
        background-image: url("http://i.oldbk.com/i/newd/pop/spinner.gif");
    }
</style>

<div class="popup-wrapper">
	<div class="f-header">
        <div id="f-close"></div>
    </div>
	<div class="f-info">
        <div style="margin-bottom: 10px" id="no-item" class="<?= $isHorn ? 'hide' : '' ?>">
            У вас нет в инвентаре предмета «Горн». Подробнее в <a href="https://oldbk.com/encicl/wc2018.html" target="_blank">Библиотеке</a>.
        </div>
		<a href="javascript:void(0)" class="tabs-link" data-tab="group">Групповой этап</a> |
		<a href="javascript:void(0)" class="tabs-link" data-tab="1_8">1/8</a> |
		<a href="javascript:void(0)" class="tabs-link" data-tab="1_4">1/4</a> |
		<a href="javascript:void(0)" class="tabs-link" data-tab="1_2">1/2</a> |
		<a href="javascript:void(0)" class="tabs-link" data-tab="3">Матч за 3е место</a> |
		<a href="javascript:void(0)" class="tabs-link" data-tab="1">Финал</a>
	</div>
	<div class="f-lay">
		<div class="f-content">
            <?php foreach ($EventsGroup as $t => $Events): ?>
                <ul class="tab-content f-list tab-<?= $t ?> <?= $t == $tab ? 'show' : 'hide' ?>">
					<?php
					$i = 1;
					$_count = count($Events);
					foreach ($Events as $Event):
						$right = $i % 2 == 0  ? true : false;

						?>
                        <li class="f-list-<?= $right ? 'right' : 'left' ?>">
                            <div class="map-block" data-event-id="<?= $Event->id ?>">
                                <div class="map-block-logo bashnja"></div>
                                <div class="map-block-mid bashnja">
                                    <div class="map-block-head">
										<?= sprintf('%s - %s', $Event->team1, $Event->team2) ?> <img title="<?= date('d.m H:i', $Event->datetime) ?>" alt="<?= date('d.m H:i', $Event->datetime) ?>" src="http://i.oldbk.com/i/world_map2/i_2.jpg">
                                    </div>
                                    <div class="map-block-bottom">
										<?php switch (true) {
											case ($Event->datetime > time() && !isset($UserBets[$Event->id])): ?>
                                                <div class="btn-wrapper">
                                                    <div class="f-loader"></div>
                                                    <div data-type="<?= \components\models\WcEventBet::BET_WIN_1 ?>" class="button-mid btn bet" title="Победа 1ой команды">П1</div>
                                                </div>
                                                <div class="btn-wrapper">
                                                    <div class="f-loader"></div>
                                                    <div data-type="<?= \components\models\WcEventBet::BET_NO_WIN ?>" class="button-mid btn bet" title="Ничья">Х</div>
                                                </div>
                                                <div class="btn-wrapper">
                                                    <div class="f-loader"></div>
                                                    <div data-type="<?= \components\models\WcEventBet::BET_WIN_2 ?>" class="button-mid btn bet" title="Победа 2ой команды">П2</div>
                                                </div>
												<?php break; ?>
											<?php case ($Event->datetime > time() && isset($UserBets[$Event->id])): ?>
                                                <div class="f-res">
                                                    Ставка: <?= $UserBets[$Event->id]->getBetType() ?>
                                                </div>
												<?php break; ?>
											<?php case ($Event->datetime < time() && $Event->who_win == null): ?>
                                                <div class="button-mid btn disabled" title="Победа 1ой команды">П1</div>
                                                <div class="button-mid btn disabled" title="Ничья">Х</div>
                                                <div class="button-mid btn disabled" title="Победа 2ой команды">П2</div>
												<?php break; ?>
											<?php case (isset($UserBets[$Event->id]) && $UserBets[$Event->id]->is_win): ?>
                                                <div class="f-res">
                                                    Ставка <?= $UserBets[$Event->id]->getBetType() ?> выиграла <?= sprintf('(%d:%d)', $Event->team1_res, $Event->team2_res) ?>
                                                </div>
												<?php if(!$UserBets[$Event->id]->is_rewarded): ?>
                                                    <div class="btn-wrapper">
                                                        <div class="f-loader"></div>
                                                        <div class="button-mid btn reward" title="Забрать">Забрать</div>
                                                    </div>
												<?php endif; ?>
												<?php break; ?>
											<?php case (isset($UserBets[$Event->id]) && !$UserBets[$Event->id]->is_win): ?>
                                                <div class="f-res">
                                                    Ставка <?= $UserBets[$Event->id]->getBetType() ?> не сыграла <?= sprintf('(%d:%d)', $Event->team1_res, $Event->team2_res) ?>
                                                </div>
												<?php break; ?>
											<?php case (!isset($UserBets[$Event->id])): ?>
                                                <div class="f-res">
                                                    Счет <?= sprintf('%d:%d', $Event->team1_res, $Event->team2_res) ?> ставки не было
                                                </div>
												<?php break; ?>
											<?php } ?>
                                    </div>
                                </div>
                                <div class="map-block-right bashnja"></div>
                            </div>
                        </li>
						<?php if($right || $i == $_count): ?>
                        <li class="f-clear"></li>
					<?php endif; ?>
						<?php
						$i++;
					endforeach; ?>
                </ul>
            <?php endforeach; ?>
		</div>
	</div>
	<div class="f-footer"></div>
</div>
<script>
    jq111(function(){
        jq111(top.frames['main'].document.body).off('click', '.tabs-link');
        jq111(top.frames['main'].document.body).on('click', '.tabs-link', function() {
            var $self = $(this);
            var tab = $self.data('tab');

            $self.closest('.popup-wrapper').find('.tab-content').hide();
            $self.closest('.popup-wrapper').find('.tab-'+tab).show();
        });

        jq111(top.frames['main'].document.body).off('click', '#f-close');
        jq111(top.frames['main'].document.body).on('click', '#f-close', function() {
            jq111(this).closest('#wc18-wrapper').remove();
        });

        jq111(top.frames['main'].document.body).off('click', '.map-block .bet');
        jq111(top.frames['main'].document.body).on('click', '.map-block .bet', function() {
            var $self = $(this);
            var event_id = $self.closest('.map-block').data('event-id');
            var bet_type = $self.data('type');

            var $loader = $self.closest('.btn-wrapper').find('.f-loader');
            $.ajax({
                url: 'http://capitalcity.oldbk.com/action/wc/bet',
                dataType: 'json',
                type: 'post',
                data: {'event_id': event_id, 'bet': bet_type},
                xhrFields: {
                    withCredentials: true
                },
                beforeSend: function() {
                    $self.closest('.map-block-bottom').find('.btn').addClass('disabled');
                    $loader.show();
                },
                success: function(response) {
                    if(response.status == 1) {
                        $self.closest('[data-event-id='+event_id+']').replaceWith(response.html);
                    } else if(response.status == 0 && response.message !== undefined) {
                        $self.closest('.map-block-bottom').find('.btn').removeClass('disabled');
                        $loader.hide();

                        alert(response.message);
                    }

                    if(response.isHorn !== undefined && response.isHorn == 0) {
                        $('#no-item').show();
                    } else {
                        $('#no-item').hide();
                    }
                }
            });
        });

        jq111(top.frames['main'].document.body).off('click', '.map-block .reward');
        jq111(top.frames['main'].document.body).on('click', '.map-block .reward', function() {
            var $self = $(this);
            var event_id = $self.closest('.map-block').data('event-id');

            var $loader = $self.closest('.btn-wrapper').find('.f-loader');
            $.ajax({
                url: 'http://capitalcity.oldbk.com/action/wc/reward',
                dataType: 'json',
                type: 'post',
                data: {'event_id': event_id},
                xhrFields: {
                    withCredentials: true
                },
                beforeSend: function() {
                    $self.closest('.map-block-bottom').find('.btn').addClass('disabled');
                    $loader.show();
                },
                success: function(response) {
                    if(response.status == 1) {
                        $self.closest('[data-event-id='+event_id+']').replaceWith(response.html);
                    } else if(response.status == 0 && response.message !== undefined) {
                        $self.closest('.map-block-bottom').find('.btn').removeClass('disabled');
                        $loader.hide();

                        alert(response.message);
                    }
                }
            });
        });
    });
</script>