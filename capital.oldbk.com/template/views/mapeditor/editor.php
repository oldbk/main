<?php

use \components\models\clanTournament\ClanTournamentMapItems as MItem;

/**
 * Created by PhpStorm.
 * User: me
 * Date: 14.10.2018
 * Time: 01:41
 */ ?>
<style>
    .s3x3 {
        width: 1100px;
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
        background-image: url('/assets/hero/img/infliction_layer_blue_1.png');
        background-size: contain;
        height: 48px;
        width: 96px;
        opacity: 0.3;
    }
    .point.active .point-bg {
        background-image: url('/assets/hero/img/infliction_layer_red.png');
    }
    #overlay_monsters .point a {
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
    #overlay_monsters .point img {
        position: absolute;
        z-index: 1;
    }
    #overlay_monsters .point img.flag {
        left: 5px;
        top: -20px;
    }
    #overlay_monsters .point img.base {
        left: -5px;
        top: -24px;
    }
    #overlay_monsters .point img.hospital, #overlay_monsters .point img.wall, #overlay_monsters .point img.power, #overlay_monsters .point img.pit, #overlay_monsters .point img.mine {
        left: -5px;
        top: -5px;
    }
    #overlay_monsters .point img.power {
        left: 10px;
    }
    #overlay_monsters .point img.pit {
        top: 0px;
    }
    #overlay_monsters .point img.user {
        z-index: 2;
        left: 11px;
        top: -13px;
    }
    #overlay_monsters .point.remove {
        opacity: 0.1;
    }
    #page-wrapper a.btn {
        color: black;
        height: 20px;
        background-repeat: no-repeat;
        line-height: 11px;
    }
</style>
<div class="container-fluid" id="tournament-editor">
    <div class="title">
        <div class="h3">
            Клановый турнир
        </div>
        <div id="buttons">
            <a class="button-mid btn" href="<?= $app->urlFor('znahar', array('action' => 'index')) ?>" title="Обновить">Обновить</a>
            <a class="button-mid btn" href="/city.php?bps=1" title="Вернуться">Вернуться</a>
        </div>
    </div>
    <div>
        <strong>Запахи трав наполняют помещение, непонятные и путающие предметы скрываются в пляшущих тенях...</strong><br>
        <strong>Говорят, здесь можно изменить свою судьбу. Стать кем-то иным... кем раньше был лишь в мечтах...</strong><br>
        <div class="mhint" style="font-size: 13px;margin-top: 5px;">Все имеет цену. Но не все можно купить. Помните - некоторые шансы даются лишь раз в жизни...</div>
    </div>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8 text-center">
            <div id="overlay_monsters" class="mx-auto s3x3" style="z-index: 3;position: relative;">
				<?php
				$Builder = \components\Helper\HeroMapGenerator::generate(20, 10, [1 => [123], 2 => []]);
				$coords = $Builder->getMap();
				foreach ($coords as $location_y => $_t): ?>
					<?php
                    foreach ($_t as $location_x => $info):
						$left = ($location_x - 1) * 48;
						$top = ($location_y - 1) * 50;
                        if($location_x % 2 == 0) {
							$top += 25;
                        }

                        ?>
                        <div data-y="<?= $location_y ?>" data-x="<?= $location_x ?>" class="point" style="position: absolute; top: <?= $top ?>px; left: <?= $left ?>px;">
                            <div class="point-bg"></div>
                            <a href="javascript:void(0);" class="" data-y="<?= $location_y ?>" data-x="<?= $location_x ?>">
                                <?php foreach ($info['items'] as $item): ?>
                                    <img src="<?= $item['image'] ?>" class="<?= $item['type'] ?>">
                                <?php endforeach; ?>
                            </a>
                        </div>
					<?php endforeach; ?>
				<?php endforeach ?>
            </div>
        </div>
        <div class="col-2"></div>
    </div>
</div>
<script>
    $(function(){
        $(document.body).on('mouseover', '.point a', function() {
            $('.point').removeClass('active');
            $(this).closest('.point').addClass('active');
        });
        $(document.body).on('mouseout', '.point a', function() {
            $(this).closest('.point').removeClass('active');
        });


        $('.point a').click(function() {
            alert('click');
        });
    });
</script>