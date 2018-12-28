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
<link rel="stylesheet" href="http://capitalcity.oldbk.com/assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="http://capitalcity.oldbk.com/i/btn.css" type="text/css">
<style>
    body {
        background-color: #d7d7d7;
    }
    .popup-wrapper {
        width: 800px;
        position: absolute;
        top: 10%;
        left: 50%;
        margin-left: -400px;
        z-index: 100;
    }
    .f-lay {
        background-image: url("http://i.oldbk.com/i/newd/pop/bg-y_4_2.jpg");
        padding: 0 20px;
        padding-bottom: 10px;
        font-size: 11px !important;
    }
    .f-header {
        background-image: url("http://i.oldbk.com/i/newd/pop/up_bg_3_2.jpg");
        height: 17px;
    }
    .f-footer {
        background-image: url("http://i.oldbk.com/i/newd/pop/down_bg_2.jpg");
        height: 8px;
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
    .f-lay .item {
        padding: 5px 0;
    }
    .f-lay .title {
        COLOR: #8f0000;
        font-weight: bold;
    }
    .separate {
        background: url("http://i.oldbk.com/i/sostojanie/hr_2.jpg");
        height: 2px;
    }
    a, a:visited, a:hover {
        FONT-WEIGHT: bold;
        COLOR: #003388;
        text-decoration: none;
    }
    .f-lay .hide {
        display: none;
    }
    .f-clear:after {
        display: block;
        content: "";
        clear: both;
    }
    .f-list {
        margin: 0;
        list-style: none;
        padding: 0;
    }
    .f-list .block-item {
        float: none;
        display: block;
    }
    .block-item .hr {
        height: 40px;
        position: relative;
        background-image: url(http://i.oldbk.com/i/newd/pop/razdelitel1.png);
        background-repeat: no-repeat;
        background-position-x: right;
        background-position-y: -10px;
        text-align: right;
    }
    .block-item {
        height: 95px;
        width: 370px;
        display: inline-block;
        padding-right: 10px;
    }
    .f-lay .block-title {
        color: #8F0000;
        font-weight: bold;
        text-align: center;
        font-size: 13px;
    }
    .block-item .block-content .block-logo {
        float: left;
        width: 65px;
        height: 65px;
        margin-right: 5px;
    }
    .block-item .block-content .block-text {
        padding-left: 65px;
    }
    div.details {
        padding-left: 15px;
        color:black;
        font-weight:normal;
    }
    .f-info {
        margin-bottom: 20px;
    }
    #reward-wrapper ul#reward-list li.item-render {
        position: relative;
        display: inline-block;
    }
    #reward-wrapper ul#reward-list li.item-render .item-count {
        position: absolute;
        background-color: #7F7A79;
        min-width: 20px;
        color: white;
        text-align: center;
        bottom: 0px;
        right: 0px;
        font-size: 10px;
        padding: 0 5px;
        font-weight: bold;
    }
    #reward-wrapper #placeholder {
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .f-clear:after {
        display: block;
        content: "";
        clear: both;
    }
</style>

<div class="popup-wrapper">
    <div class="f-header">
        <div id="f-close" class="f-close"></div>
    </div>
    <div class="f-lay">
        <div class="f-info text-center" style="font-size: 15px">
            <a href="javascript:void(0)" class="tabs-link" data-tab="timer">Таймеры</a> |
            <a href="javascript:void(0)" class="tabs-link" data-tab="rating">Рейтинги</a>
        </div>
        <div class="f-content tab-content" data-content="timer">
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
        </div>
        <div class="f-content tab-content hide" data-content="rating">
			<?php if($items_ratings): ?>
                <div class="row">
					<?php foreach ($items_ratings as $item):
						/** @var \components\models\EventRating $Rating */
						$Rating = $item['rating'];
						$date_num = date('N');
						$StartDate = (new DateTime('2018-09-17 17:00:00'));
						$EndDate = (new DateTime('2018-09-24 17:00:00'));

						if($item['items']):
							foreach ($item['items'] as $userRatingId => $UserRating):
								/** @var \components\models\UserEventRating $UserRating */
								$position = $UserRating->getPosition();
								?>
                                <div class="block-item col-6">
                                    <div class="rating-<?= $UserRating->id ?>">
                                        <div class="block-content">
                                            <div class="block-logo">
                                                <img src="http://i.oldbk.com/i/newd/<?= $Rating->icon ?>">
                                            </div>
                                            <div class="block-text">
                                                <div class="block-title">
													<?php if($Rating->link_encicl): ?>
                                                        <a href="<?= $Rating->link_encicl ?>" target="_blank"><?= $Rating->name; ?></a>
													<?php else: ?>
														<?= $Rating->name; ?>
													<?php endif; ?>
                                                </div>
                                                <div class="details">
                                                    <div><?= $StartDate->format('d/m/Y') ?> - <?= $EndDate->format('d/m/Y') ?></div>
                                                    <br>
                                                    <div><a href="<?= $Rating->link ?>?uid=<?= $UserRating->user_id ?>" target="_blank">Место: <?= $position <= 500 ? $position : '500+' ?></a> / Очки: <?= $UserRating->value ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hr rating-<?= $UserRating->id; ?>">
										<?php if($UserRating->is_end == 0): ?>
                                            <i>Продлится еще <?= TimeHelper::prettyTime(
													time(),
													$EndDate->getTimestamp(),
													false,
													[
														'm' => '<strong>%m</strong> мес.',
														'd' => '<strong>%d</strong> дн.',
														'h' => '<strong>%h</strong> ч.',
														'i' => '<strong>%i</strong> мин.',
													]) ?></i>
										<?php elseif($position <= 500): ?>
                                            <div class="btn-wrapper">
                                                <div class="f-loader"></div>
                                                <div class="button-big btn reward" data-rating-id="<?= $UserRating->id ?>" title="Получить награду">Забрать награду</div>
                                            </div>
										<?php endif; ?>
                                    </div>
                                </div>
							<?php endforeach; ?>
						<?php else: ?>
                            <li class="block-item">
                                <div class="block-content">
                                    <div class="block-logo">
                                        <img src="http://i.oldbk.com/i/newd/<?= $Rating->icon ?>">
                                    </div>
                                    <div class="block-text">
                                        <div class="block-title">
											<?= $Rating->name; ?>
                                        </div>
                                        <div class="details">
                                            <div><?= $StartDate->format('d/m/Y') ?> - <?= $EndDate->format('d/m/Y') ?></div>
                                            <br>
                                            <div><a href="<?= $Rating->link ?>" target="_blank">Место: 500+</a> / Очки: 0</div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="hr">
                                <small><i>Продлится еще <?= TimeHelper::prettyTime(
											time(),
											$EndDate->getTimestamp(),
											false,
											[
												'm' => '<strong>%m</strong> мес.',
												'd' => '<strong>%d</strong> дн.',
												'h' => '<strong>%h</strong> ч.',
												'i' => '<strong>%i</strong> мин.',
											]) ?></i></small>
                            </li>
						<?php endif; ?>
					<?php endforeach; ?>
                </div>
			<?php endif; ?>
			<?php if(empty($items_ratings)): ?>
                <div class="text-center">
                    <i>На данный момент нет активных рейтингов. Попробуйте позже.</i>
                </div>
			<?php endif; ?>
        </div>
        <div id="reward-wrapper" class="hide">
            <div class="block-title">
                Награда
            </div>
            <div id="placeholder">

            </div>
            <div style="text-align: center">
                <div class="button-mid btn f-close" title="Закрыть">Закрыть</div>
            </div>
        </div>
    </div>
    <div class="f-footer"></div>
</div>
<script>
    jq111(function(){
        jq111(top.frames['main'].document.body).off('click', '.f-close');
        jq111(top.frames['main'].document.body).on('click', '.f-close', function() {
            jq111(this).closest('#rating-wrapper').remove();
        });

        jq111(top.frames['main'].document.body).off('click', '.tabs-link');
        jq111(top.frames['main'].document.body).on('click', '.tabs-link', function() {
            var $self = jq111(this);
            var tab = $self.data('tab');

            $self.closest('.f-lay').find('.tab-content').hide();
            $self.closest('.f-lay').find('[data-content="'+tab+'"]').show();
        });

        jq111(top.frames['main'].document.body).off('click', '.f-lay .reward');
        jq111(top.frames['main'].document.body).on('click', '.f-lay .reward', function() {
            var $self = $(this);
            var ratingId = $self.data('rating-id');

            var $loader = $self.closest('.btn-wrapper').find('.f-loader');
            jq111.ajax({
                url: 'http://capitalcity.oldbk.com/action/rating/reward',
                dataType: 'json',
                type: 'post',
                data: {'urid': ratingId},
                xhrFields: {
                    withCredentials: true
                },
                beforeSend: function() {
                    $self.addClass('disabled');
                    $loader.show();
                },
                success: function(response) {
                    if(response.status == 1) {
                        var content_block = $self.closest('.f-content');

                        content_block.find('#reward-wrapper #placeholder').html(response.html);
                        content_block.find('#reward-wrapper').show();
                        $self.closest('.f-lay').find('.tab-content').hide();

                        try {
                            removeRating(response.ratingId);
                        } catch (e) {

                        }
                    } else if(response.status == 0 && response.message !== undefined) {
                        $self.removeClass('disabled');
                        alert(response.message);
                    }
                }
            }).always(function() {
                $loader.hide();
            });
        });
    });
</script>