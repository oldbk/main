<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 08.06.2018
 * Time: 22:47
 *
 *
 * @var \components\models\WcEvent $events
 */ ?>

<style>
	.popup-wrapper {
		width: 800px;
		position: absolute;
		top: 20px;
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
	.f-list li {
		width: 350px;
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

	div.disabled {
		pointer-events: none;
		opacity: 0.6;
	}
</style>

<div class="popup-wrapper">
	<div class="f-header"></div>
	<div class="f-info">У вас нет в инвентаре предмета «Горна». Подробнее в <a href="">Библиотеке</a>.
		<br><br>
		<a href="">Групповой этап</a> |
		<a href="">1/8</a> |
		<a href="">1/4</a> |
		<a href="">1/2</a> |
		<a href="">Матч за 3е место</a> |
		<a href="">Финал</a>
	</div>
	<div class="f-lay">
		<div class="f-content">
			<ul class="f-list">
				<li class="f-list-left">
					<div class="map-block">
						<div class="map-block-logo bashnja"></div>
						<div class="map-block-mid bashnja">
							<div class="map-block-head">
								Россия - Саудовская Аравия <img src="http://i.oldbk.com/i/world_map2/i_2.jpg">
							</div>
							<div class="map-block-bottom">
								<div class="f-res">
									Ставка Х выиграла (2:2)
								</div>
								<div class="button-mid btn" title="Войти в комнату">Забрать</div>
							</div>
						</div>
						<div class="map-block-right bashnja"></div>
					</div>
				</li>
				<li class="f-list-right">
					<div class="map-block">
						<div class="map-block-logo bashnja"></div>
						<div class="map-block-mid bashnja">
							<div class="map-block-head">
								Россия - Саудовская Аравия <img src="http://i.oldbk.com/i/world_map2/i_2.jpg">
							</div>
							<div class="map-block-bottom">
								<div class="f-res">Счет: 2:2</div>  <small>ставки не было</small>

							</div>
						</div>
						<div class="map-block-right bashnja"></div>
					</div>
				</li>
				<li class="f-clear"></li>
				<li class="f-list-left">
					<div class="map-block">
						<div class="map-block-logo bashnja"></div>
						<div class="map-block-mid bashnja">
							<div class="map-block-head">
								Россия - Саудовская Аравия <img src="http://i.oldbk.com/i/world_map2/i_2.jpg">
							</div>
							<div class="map-block-bottom">
								<div class="f-res">
									Ставка: X
								</div>
							</div>
						</div>
						<div class="map-block-right bashnja"></div>
					</div>
				</li>
				<li class="f-list-right">
					<div class="map-block">
						<div class="map-block-logo bashnja"></div>
						<div class="map-block-mid bashnja">
							<div class="map-block-head">
								Россия - Саудовская Аравия <img src="http://i.oldbk.com/i/world_map2/i_2.jpg">
							</div>
							<div class="map-block-bottom">
								<div class="button-mid btn" title="Войти в комнату">П1</div>
								<div class="button-mid btn" title="Войти в комнату">Х</div>
								<div class="button-mid btn" title="Войти в комнату">П2</div>
							</div>
						</div>
						<div class="map-block-right bashnja"></div>
					</div>
				</li>
				<li class="f-clear"></li>
				<li class="f-list-left">
					<div class="map-block">
						<div class="map-block-logo bashnja"></div>
						<div class="map-block-mid bashnja">
							<div class="map-block-head">
								Россия - Саудовская Аравия <img src="http://i.oldbk.com/i/world_map2/i_2.jpg">
							</div>
							<div class="map-block-bottom">
								<div class="button-mid btn" title="Войти в комнату">П1</div>
								<div class="button-mid btn" title="Войти в комнату">Х</div>
								<div class="button-mid btn" title="Войти в комнату">П2</div>
							</div>
						</div>
						<div class="map-block-right bashnja"></div>
					</div>
				</li>
				<li class="f-list-right">
					<div class="map-block">
						<div class="map-block-logo bashnja"></div>
						<div class="map-block-mid bashnja">
							<div class="map-block-head">
								Россия - Саудовская Аравия <img src="http://i.oldbk.com/i/world_map2/i_2.jpg">
							</div>
							<div class="map-block-bottom">
								<div class="button-mid btn" title="Войти в комнату">П1</div>
								<div class="button-mid btn" title="Войти в комнату">Х</div>
								<div class="button-mid btn" title="Войти в комнату">П2</div>
							</div>
						</div>
						<div class="map-block-right bashnja"></div>
					</div>
				</li>
				<li class="f-clear"></li>
				<li class="f-list-left">
					<div class="map-block">
						<div class="map-block-logo bashnja"></div>
						<div class="map-block-mid bashnja">
							<div class="map-block-head">
								Россия - Саудовская Аравия <img src="http://i.oldbk.com/i/world_map2/i_2.jpg">
							</div>
							<div class="map-block-bottom">
								<div class="button-mid btn" title="Войти в комнату">П1</div>
								<div class="button-mid btn" title="Войти в комнату">Х</div>
								<div class="button-mid btn" title="Войти в комнату">П2</div>
							</div>
						</div>
						<div class="map-block-right bashnja"></div>
					</div>
				</li>
				<li class="f-list-right"></li>
				<li class="f-clear"></li>
			</ul>
		</div>
	</div>
	<div class="f-footer"></div>
</div>