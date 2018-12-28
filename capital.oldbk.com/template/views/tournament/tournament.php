<?php

use \components\models\clanTournament\ClanTournamentMapItems as MItem;
use \components\models\clanTournament\ClanTournamentMapItems;
use \components\Helper\map\HeroMapGenerator;

/**
 * Created by PhpStorm.
 * User: me
 * Date: 14.10.2018
 * Time: 01:41
 *
 * @var \components\Helper\map\HeroMapGenerator $builder
 * @var \components\models\clanTournament\ClanTournamentUser $tUser
 * @var boolean $isFlag
 * @var boolean $isEnemy
 * @var boolean $isTeamUser
 * @var \components\models\clanTournament\ClanTournamentGroup $group
 * @var \components\models\clanTournament\ClanTournamentUser[] $users
 */ ?>
<style>
    .s3x3 {
        width: 900px;
        height: 386px;
    }
    .page-content {
        position: relative;
        width: 1000px;
        /*height: 676px;*/
    }
    .page-content #overlay_map {
        position: absolute;
        left: 50px;
        top: 40px;
    }
    .point {
        height: 48px;
        width: 96px;
        line-height: 32px;
        text-align: center;
        font-size: 10px; font-weight: bold;
        color: rgb(34, 34, 34);
        text-shadow: rgb(238, 238, 238) 0px 0px 5px;
    }
    .point-bg {
        background-image: url('/assets/hero/img/image_241_pas.png');
        background-size: contain;
        height: 48px;
        width: 96px;
        opacity: 1;
    }
    .point.active .point-bg {
        background-image: url('/assets/hero/img/image_241_act.png');
    }
    .point .point-bg.hide {
        background: none;
        background-color: #E2E0E1;
        opacity: 1;
    }
    #overlay_map .point a.mclick {
        color: black;
        display: block;
        height: 48px;
        width: 57px;
        position: absolute;
        left: 19px;
        line-height: 47px;
        font-size: 12px;
        z-index: 100;
        top: 0px;
    }
    .player {
        position: absolute;
        left: 30px;
        top: -22px;
    }
    #overlay_map .point img {
        position: absolute;
        z-index: 1;
    }
    #overlay_map .point img.flag {
        left: 15px;
        top: 5px;
        height: 30px;
        z-index: 3;
    }
    #overlay_map .point img.base {
        left: -10px;
        top: -16px;
    }
    #overlay_map .point img.power, #overlay_map .point img.pit, #overlay_map .point img.mine {
        left: -5px;
        top: -5px;
    }
    #overlay_map .point img.hospital {
        left: 3px;
        top: 10px;
    }
    #overlay_map .point img.wall {
        left: 0px;
        top: 5px;
    }
    #overlay_map .point img.power {
        left: 10px;
        top: -15px;
    }
    #overlay_map .point img.pit {
        top: 10px;
        height: 25px;
        left: 5px;
    }
    #overlay_map .point img.user {
        z-index: 2;
        left: 5px;
        top: -25px;
        height: 60px;
    }
    #overlay_map .point img.mine {
        left: 11px;
        top: -2px;
        height: 35px;
    }
    #overlay_map .point img.own {
        position: absolute;
        left: -2px;
        top: 10px;
    }
    #overlay_map .point.remove {
        opacity: 0;
    }
    #overlay_map .point .wrap-user-login, #overlay_map .point .wrap-user-coords {
        font-size: 9px;
        color: white;
        text-shadow: 0 0 0.1em black, 0 0 0.1em black, 0 0 0.1em black, 0 0 0.1em black;
        width: 96px;
        margin-left: -19px;
        height: 10px;
        line-height: 10px;
        z-index: 3;
        position: absolute;
        margin-top: -40px;
        display: none;
    }
    #overlay_map .point:hover .wrap-user-login {
        display: block;
    }
    #page-wrapper a.btn {
        color: black;
        height: 20px;
        background-repeat: no-repeat;
        line-height: 11px;
    }
    #progress-bar {
        background: url("http://i.oldbk.com/i/laba/ramka_s2.gif") no-repeat;
        width: 160px;
        height: 27px;
        margin-top: 10px;
        position: relative;
    }
    #progress-bar #progress-wrapper {
        width: 131px;
        height: 7px;
        position: absolute;
        top: 10px;
        overflow: hidden;
        margin-left: 28px;
    }
    #progress-bar .progress {
        height: 100%;
        padding: 0;
        margin: 0;
        position: absolute;
    }
    #progress-bar .progress.red {
        background-color: red;
        width: 100%;
    }
    .img-hide {
        position: absolute;
        z-index: 1;
        left: 15px;
        top: 7px;
    }
    #col-wrap {
        min-width: 200px;
    }
    #col-wrap .title {
        color: #8f0000;
        font-weight: bold;
        font-size: 18px;
    }
    .page-content.hide {
        display: none;
    }
    #active-wrap {
        position: absolute;
        top: 12px;
        left: 4px;
        z-index: 1;
    }
    #mana-line {
        margin-bottom: 5px;
    }
    #hp-line, #mana-line {
        height: 12px;
    }
    #hp-line img, #mana-line img {
        margin-top: -5px;
    }
    #hp-line .line, #mana-line .line {
        width: 150px;
        background: url(http://i.oldbk.com/i/1silver.gif);
        height: 10px;
        display: inline-block;
        text-align: center;

        z-index: 2;
        font-weight: bold;
        position: relative;
        line-height: 10px;
        font-size: 9px;
        color: white;
        text-shadow: 0 0 0.1em black, 0 0 0.1em black, 0 0 0.1em black, 0 0 0.1em black;
    }
    #hp-line .line .current, #mana-line .line .current {
        height: 10px;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
    }
    #hp-line .line span, #mana-line .line span {
        z-index: 2;
        position: absolute;
        height: 10px;
        width: 100%;
        text-align: center;
        left: 0;
    }
    #hp-line .line .current {
        background-color: #02851E;
    }
    #mana-line .line .current {
        background-color: #0166CC;
    }
    #overlay_map .point .section-fight {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 5;
    }
    #overlay_map .point .section-fight img.fight {
        position: relative;
        height: 20px;
    }

    .arrow_right, .arrow_down, .arrow_left, .arrow_top {
        width: 25px;
    }
    .arrow_right {
        right: -17px;
        top: -10px;
    }
    .arrow_down {
        right: -17px;
        top: 22px;
    }
    .arrow_left {
        right: 50px;
        top: 22px;
    }
    .arrow_top {
        right: 50px;
        top: -10px;
    }
</style>
<div class="container-fluid" id="tournament-editor">
    <div class="row">
        <div class="col"></div>
        <div class="col"></div>
        <div class="col">
            <div id="buttons">
                <a class="button-mid btn" href="<?= $app->urlFor('clan.tournament', array('action' => 'tournament')) ?>" title="Обновить">Обновить</a>
                <a class="button-mid btn" href="<?= $app->urlFor('clan.tournament', array('action' => 'remove')) ?>" title="Обновить">ВЫЙТИ!!!</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div id="col-wrap">
				<?php foreach ($app->flashData() as $type => $message): ?>
                    <div class="alert alert-<?= $type ?>">
						<?= $message; ?>
                    </div>
				<?php endforeach; ?>
				<?php /*\components\widgets\progress\ProgressBar::widget($tUser->can_moved_at - time());*/ ?>
                <div class="title">
                    Участники
                </div>
				<?php foreach ($users as $team_id => $gUsers): ?>
                    <div>
						<?php if($team_id == $tUser->team_id): ?>
                            <img onclick="top.AddToPrivate('kt-team', top.CtrlPress,event); return false;" src="http://i.oldbk.com/i/lock.gif" style="cursor:pointer;" title="Приват" width="20" height="15">
						<?php endif; ?>
                        <b>Команда <?= $team_id ?>:</b> (Очки: <?= $gUsers['point'] ?>)
                    </div>
                    <ul>
						<?php
						/** @var \components\models\clanTournament\ClanTournamentUser $groupUser */
						foreach ($gUsers['users'] as $groupUser): ?>
                            <li>
								<?php if($groupUser->team_id == $tUser->team_id): ?>
                                    <div id="hp-line">
                                        <img src="http://i.oldbk.com/i/herz.gif" width="10" height="10" alt="Уровень жизни">
                                        <div class="line">
                                            <div class="current" style="width: <?= (int)($groupUser->user->hp * 100 / $groupUser->user->maxhp) ?>%"></div>
                                            <span><?= $groupUser->user->hp ?>/<?= $groupUser->user->maxhp ?></span>
                                        </div>
                                    </div>
                                    <div id="mana-line">
                                        <img src="http://i.oldbk.com/i/Mherz.gif" width="10" height="10" alt="Уровень маны">
                                        <div class="line">
                                            <div class="current" style="width: <?= (int)($groupUser->user->mana * 100 / $groupUser->user->maxmana) ?>%"></div>
                                            <span><?= $groupUser->user->mana ?>/<?= $groupUser->user->maxmana ?></span>
                                        </div>
                                    </div>
								<?php endif; ?>
								<?= $groupUser->user->htmlLogin(); ?>
								<?php
								switch (true) {
									case (($battleId = $groupUser->inFight()) > 0):
										echo '<a href="http://capitalcity.oldbk.com/logs.php?log='.$battleId.'" target="_blank"><img src="http://i.oldbk.com/i/fighttype3.gif"></a>';
										break;
									case $groupUser->haveFlag():
										echo '<img src="/assets/tournament/flag_position.png">';
										break;
									case $groupUser->inPit():
										echo '<img src="'.ClanTournamentMapItems::IMAGE_PIT.'" height="15">';
										break;
								}
								?>
                            </li>
						<?php endforeach;; ?>
                    </ul>
                    <div style="margin-bottom: 10px"></div>
				<?php endforeach;; ?>
            </div>
        </div>
        <div class="col">
            <div class="page-content hide">
                <img src="/assets/hero/img/tournament_map4.png" width="1000" height="480">
                <div id="overlay_map" class="mx-auto s3x3">
					<?php
					$coords = $builder->getMap();
					foreach ($coords as $location_y => $_t): ?>
						<?php
						foreach ($_t as $location_x => $info):
							$left = ($location_x - 1) * 48;
							$top = ($location_y - 1) * 48;
							if($location_x % 2 == 0) {
								$top += 24;
							}

							$isHide = $builder->isHide($location_y, $location_x);
							?>
                            <div data-y="<?= $location_y ?>" data-x="<?= $location_x ?>" class="point" style="position: absolute; top: <?= $top ?>px; left: <?= $left ?>px;">
								<?php if($isHide): ?>
                                    <img src="/assets/hero/img/haze2.png" class="img-hide">
								<?php else: ?>
                                    <div class="point-bg"></div>
                                    <a href="<?= $app->urlFor('clan.tournament', ['action' => 'move', 'y' => $location_y, 'x' => $location_x]) ?>" class="mclick" data-y="<?= $location_y ?>" data-x="<?= $location_x ?>">
										<?php
										/** @var \components\Helper\map\items\iMapItem|\components\Helper\map\items\MapUser $item */
										foreach ($builder->getItems($location_y, $location_x) as $item): if($item->isHidden()) {continue;} ?>
											<?php if($item->getType() == ClanTournamentMapItems::TYPE_USER): ?>
												<?php
												/** @var \components\models\clanTournament\ClanTournamentUser $gUser */
												$gUser = $users[$item->getTeamId()]['users'][$item->getUserId()];
												?>
												<?php if($item->getTeamId() == $tUser->team_id && $item->getUserId() != $tUser->user_id): ?>
                                                    <div class="wrap-user-login"><?= $gUser->user->login; ?> (<?= sprintf('%d:%d', $gUser->location_y, $gUser->location_x) ?>)</div>
												<?php endif; ?>

												<?php if($gUser->user_id == $tUser->user_id): ?>
													<?php
													$right = $builder->getCoordsTo($tUser->location_y, $tUser->location_x, HeroMapGenerator::DIRECTION_RIGHT);
													$down = $builder->getCoordsTo($tUser->location_y, $tUser->location_x, HeroMapGenerator::DIRECTION_BOTTOM);
													$left = $builder->getCoordsTo($tUser->location_y, $tUser->location_x, HeroMapGenerator::DIRECTION_LEFT);
													$top = $builder->getCoordsTo($tUser->location_y, $tUser->location_x, HeroMapGenerator::DIRECTION_TOP);
													?>
													<?php if($builder->canMove($tUser->location_y, $tUser->location_x, $right['y'], $right['x'])): ?>
                                                        <img class="arrow_right" src="/assets/tournament/arrow_right.png">
													<?php endif; ?>
													<?php if($builder->canMove($tUser->location_y, $tUser->location_x, $down['y'], $down['x'])): ?>
                                                        <img class="arrow_down" src="/assets/tournament/arrow_down.png">
													<?php endif; ?>
													<?php if($builder->canMove($tUser->location_y, $tUser->location_x, $left['y'], $left['x'])): ?>
                                                        <img class="arrow_left" src="/assets/tournament/arrow_left.png">
													<?php endif; ?>
													<?php if($builder->canMove($tUser->location_y, $tUser->location_x, $top['y'], $top['x'])): ?>
                                                        <img class="arrow_top" src="/assets/tournament/arrow_top.png">
													<?php endif; ?>
												<?php endif; ?>

												<?php if($gUser->user->battle > 0): ?>
                                                    <div class="section-fight">
                                                        <img  class="fight" src="/assets/tournament/tournament_fight.png">
                                                    </div>
												<?php endif; ?>

                                                <img src="<?= $item->getImage() ?>" class="<?= $item->getType() ?>" title="<?= ClanTournamentMapItems::title($item->getType()) ?>">
												<?php if($item->getUserId() == $tUser->user_id): ?>
                                                    <!--<img class="own" src="/assets/tournament/pers_active.png">-->
                                                    <svg id="active-wrap" xmlns="http://www.w3.org/2000/svg" version="1.1" x="0px" y="0px" viewBox="0 0 100 100">
                                                        <path fill-opacity="0" stroke-width="5" stroke="#FF0000" d="M1,21.5 C1,10.174033149171294 19.5718232044199,1 42.5,1 C65.4281767955801,1 84,10.174033149171294 84,21.5 C84,32.825966850828706 65.4281767955801,42 42.5,42 C19.5718232044199,42 1,32.825966850828706 1,21.5 z"/>
                                                        <path id="pers-active" fill-opacity="0" stroke-width="5" stroke="#FFC300" d="M1,21.5 C1,10.174033149171294 19.5718232044199,1 42.5,1 C65.4281767955801,1 84,10.174033149171294 84,21.5 C84,32.825966850828706 65.4281767955801,42 42.5,42 C19.5718232044199,42 1,32.825966850828706 1,21.5 z"/>
                                                    </svg>
												<?php endif; ?>
											<?php else: ?>
                                                <img src="<?= $item->getImage() ?>" class="<?= $item->getType() ?>" title="<?= ClanTournamentMapItems::title($item->getType()) ?>">
											<?php endif; ?>
										<?php endforeach; ?>
                                        <!--<div><?= sprintf('%d:%d', $location_y, $location_x) ?></div>-->
                                    </a>
								<?php endif; ?>
                            </div>
						<?php endforeach; ?>
					<?php endforeach ?>
                </div>
            </div>
        </div>
        <div class="col"></div>
    </div>
</div>
<style>
    #kt-popup {
        width: 300px;
        position: absolute;
        left: 50%;
        top: 20%;
        z-index: 100;
        margin-left: -150px;
        display: none;

        -webkit-box-shadow: 4px 6px 20px 0px rgba(0,0,0,0.75);
        -moz-box-shadow: 4px 6px 20px 0px rgba(0,0,0,0.75);
        box-shadow: 4px 6px 20px 0px rgba(0,0,0,0.75);
    }
    #kt-popup .popup-header {
        height: 7px;
        background: url("http://capitalcity.oldbk.com/i/quest/fp_1.png");
        background-size: 300px;
    }
    #kt-popup .popup-body {
        background: url(http://capitalcity.oldbk.com/i/quest/fp_2.jpg);
        background-size: 300px;
        padding: 10px;
        min-height: 80px;
    }
    #kt-popup .popup-footer {
        height: 7px;
        background: url("http://capitalcity.oldbk.com/i/quest/fp_3.png");
        background-size: 300px;
    }
    #kt-popup .btn-footer {
        text-align: center;
    }
    #kt-popup .content {
        text-align: center;
        margin: 20px 0;
    }
</style>

<div id="kt-popup">
    <div class="popup-header"></div>
    <div class="popup-body">
        <div style="text-align: center"><b>В секторе находится:</b></div>
        <div class="content">
            <div>
				<?php if($isFlag): ?>
                    <img src="/assets/tournament/tournament_flag.png">
				<?php endif; ?>
				<?php if($isEnemy): ?>
                    <img src="/assets/tournament/tournament_pers2.png">
				<?php endif; ?>
				<?php if($isTeamUser): ?>
                    <img src="/assets/tournament/tournament_pers1.png">
				<?php endif; ?>
            </div>
        </div>
        <div class="btn-footer">
			<?php if($isFlag): ?>
                <a class="button-mid btn" href="<?= $app->urlFor('clan.tournament', array('action' => 'flag')) ?>" title="Поднять">Поднять</a>
			<?php endif; ?>
			<?php if($isEnemy): ?>
                <a class="button-mid btn" href="<?= $app->urlFor('clan.tournament', array('action' => 'attack')) ?>" title="Напасть">Напасть</a>
			<?php endif; ?>
			<?php if($isTeamUser): ?>
                <a class="button-mid btn" href="<?= $app->urlFor('clan.tournament', array('action' => 'help')) ?>" title="Помочь">Помочь</a>
			<?php endif; ?>
            <a class="button-mid btn kt-popup-close" href="javascript:void(0);" title="Обновить">Закрыть</a>
        </div>
    </div>
    <div class="popup-footer"></div>
</div>
<script>
    $('.page-content.hide').show();
	<?php if($isFlag || $isEnemy || $isTeamUser): ?>
    $('#kt-popup').show();
	<?php endif; ?>
    $(function(){
        $(document.body).on('mouseover', '.point a', function() {
            $('.point').removeClass('active');
            $(this).closest('.point').addClass('active');
        });
        $(document.body).on('mouseout', '.point a', function() {
            $(this).closest('.point').removeClass('active');
        });

        $(document.body).on('click', '.kt-popup-close', function() {
            $('#kt-popup').hide();
        });
    });

    if($('#pers-active').length) {
        var bar = new ProgressBar.Path('#pers-active', {
            easing: 'easeInOut',
            duration: <?= ($tUser->can_moved_at - time() + 1) * 1000 ?>
        });

        bar.set(0);
        bar.animate(1.0);  // Number from 0.0 to 1.0
    }
    //setInterval(function(){window.location.reload();}, 20 * 1000);
</script>