<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 23.10.2018
 * Time: 16:26
 *
 * @var \components\models\Castles[] $Castles
 */ ?>
<style>
	#page-content {
        position: absolute;
        top: 0;
        margin-top: 30px;
        left: 50%;
        margin-left: -470px;
	}
    #page-content .tournament, #page-content .tournament-h {
        width: 140px;
        height: 80px;
        cursor: pointer;
    }
    #page-content .tournament {
        background: url(/assets/street/clan/tournament_build2.png) no-repeat;
        position: absolute;
        left: 35px;
        top: 261px;
        z-index: 2;
    }
    #page-content .tournament-h {
        background: url(/assets/street/clan/tournament_build2_h.png) no-repeat;
        z-index: 1;
        display: none;
    }
    #page-content .tournament:hover .tournament-h {
        display: block;
    }

    #page-content .castle, #page-content .castle-h {
        width: 195px;
        height: 95px;
    }
    #page-content .castle {
        background: url(/assets/street/clan/clanbuild_castle.png) no-repeat;
        position: absolute;
        left: 250px;
        top: 201px;
        cursor: pointer;
        z-index: 2;
    }
    #page-content .castle-h {
        background: url(/assets/street/clan/clanbuild_castle_h.png) no-repeat;
        z-index: 1;
        display: none;
    }
    #page-content .castle:hover .castle-h {
        display: block;
    }


    #page-content .castles-menu li {
        background: url("http://i.oldbk.com/i/map/passive_bg.jpg") no-repeat;
        cursor: pointer;
        float: left;
        text-align: center;
        width: 115px;
    }
    #page-content .castles-menu {
        width: 920px;
        height: 28px;
        margin: 0 auto;
    }
    #page-content .castles-menu li a {
        color: #a4a4a4;
        font-size: 10pt;
    }
    #page-content .castles-menu li:hover, #page-content .castles-menu li.active {
        background: url("http://i.oldbk.com/i/map/active_bg.jpg") no-repeat;
    }
    #page-content .castles-menu li:hover a, #page-content .castles-menu li.active a {
        font-weight: bold;
        color: rgb(70, 70, 70);
    }
    #page-content .castle-icon {
        display: inline-block;
    }
    #page-content .exit-icon {
        background: url("http://i.oldbk.com/i/castles/back_button_hover2.png") no-repeat bottom;
        width: 19px;
        height: 13px;
        background-size: 12px;
    }
    #page-content .tour-icon {
        background: url("http://i.oldbk.com/i/castles/castle_icon.png") no-repeat bottom;
        width: 19px;
        height: 14px;
        background-size: 14px;
    }
    #page-content .bg-image {
        margin-top: -6px;
    }
</style>
<div class="" id="street-clan">
	<div id="page-content">
		<div>
            <ul class="castles-menu">
                <?php foreach ($castles as $castleLevel): ?>
                    <li>
                        <a href="<?= $app->urlFor('street.clan', ['action' => 'castle', 'level' => $castleLevel]) ?>">Уровень <?= $castleLevel ?></a>
                    </li>
                <?php endforeach; ?>
                <li>
                    <a href="<?= $app->urlFor('street.clan', ['action' => 'castle', 'tur' => '']) ?>">Турниры <i class="castle-icon tour-icon"></i></a>
                </li>
                <li>
                    <a href="<?= $app->urlFor('street.clan', ['action' => 'castle', 'level' => 999]) ?>">Осада замка</a>
                </li>
                <li class="active">
                    <a href="<?= $app->urlFor('street.clan', ['action' => 'index']) ?>">Клан замок</a>
                </li>
                <li>
                    <a href="<?= $app->urlFor('street.clan', ['action' => 'castle', 'exit' => '']) ?>">Вернуться <i class="castle-icon exit-icon"></i></a>
                </li>
            </ul>
        </div>
        <?php
		$day = "http://i.oldbk.com/i/castles/cday2.jpg";
		$night = "http://i.oldbk.com/i/castles/cnight3.jpg";
		if((int)date("H") > 5 && (int)date("H") < 22) {
			$bg = $day;
		} else{
			$bg = $night;
		}
        ?>
        <img src="<?= $bg ?>" class="bg-image">
        <div class="tournament">
            <div class="tournament-h"></div>
        </div>

        <div class="castle">
            <div class="castle-h"></div>
        </div>
	</div>
</div>

<script>
    $(function() {
        $(document.body).on('click', '.tournament', function() {
            location.href = '<?= $app->urlFor('street.clan', ['action' => 'tournament']) ?>';
        });

        $(document.body).on('click', '.castle', function() {
            alert('Строительство замка пока закрыто');
        });
    });
</script>