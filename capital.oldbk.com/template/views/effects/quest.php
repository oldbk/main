<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 04.09.17
 * Time: 20:18
 *
 * @var \components\Component\Slim\View $this
 * @var int $menu
 * @var array $items
 * @var array $unik
 * @var array|null $bonus
 */ ?>
<style>
	#page-wrapper .btn-control {
		text-align: right;
		margin-bottom: 20px;
	}
	#page-wrapper .page-content {
		width: 1500px;
		margin: 0 auto;
	}
	#page-wrapper .header-line {
		background-image: url(http://i.oldbk.com/i/frendlist/head_bgx.jpg);
		padding: 0;
	}
	#page-wrapper .header .header-left, #page-wrapper .header .header-right {
		height: 30px;
		width: 19px;
	}
	#page-wrapper .header .header-left {
		background-image: url(http://i.oldbk.com/i/frendlist/head_left.jpg);
		float: left;
	}
	#page-wrapper .header .header-right {
		background-image: url(http://i.oldbk.com/i/frendlist/head_right.jpg);
		float: right;
	}

	#page-wrapper .header .header-item {
		position: relative;
	}
	#page-wrapper .header .header-title {
		background-image: url(http://i.oldbk.com/i/obrazy/head_category_name_bg.jpg);
		width: 174px;
		height: 30px;
		position: absolute;
		top: 0;
	}
	#page-wrapper .header .header-item a {
		color: #535252;
	}
	#page-wrapper .header .header-item a.active, #page-wrapper .header .header-item a:hover {
		color: #8f0000;
	}
	#page-wrapper .header .header-title span {
		display: inline-block;
		margin-top: 9px;
		font-size: 10px;
		font-weight: bold;
		text-transform: uppercase;
		text-decoration: none;
	}
	#page-wrapper .table-list.sostoyanie {
		font-size: 11px;
	}
	#page-wrapper .table-list.sostoyanie td {
		vertical-align: middle;
	}
	#page-wrapper .table-list.sostoyanie>tbody>tr>td.row-left {
		background: url(http://i.oldbk.com/i/sostojanie/main_bgy_left.jpg) repeat-y;
		color: #383838;
		text-align: center;
		font-weight: bold;
	}
	#page-wrapper .table-list.sostoyanie>tbody>tr>td.row-right {
		background: url(http://i.oldbk.com/i/sostojanie/main_bgy_right.jpg) repeat-y;
		padding: 5px;
		padding-left: 10px;
	}
	#page-wrapper .table-list.sostoyanie>tbody>tr>td.row-center {
		background-color: #f5f4f4;
	}
	#page-wrapper .table-list.sostoyanie > tfoot td {
		padding: 0;
	}
	#page-wrapper .table-list.sostoyanie > tfoot .row-footer {
		background: url(http://i.oldbk.com/i/obrazy/down_bgx.jpg) repeat-x;
	}
	#page-wrapper .table-list.sostoyanie > tfoot .row-footer .row-footer-left {
		background: url(http://i.oldbk.com/i/obrazy/down_left.jpg) no-repeat;
		height: 11px;
	}
	#page-wrapper .table-list.sostoyanie > tfoot .row-footer .row-footer-right {
		background: url(http://i.oldbk.com/i/obrazy/down_right.jpg) no-repeat right;
		height: 11px;
	}
	#page-wrapper .table-list.sostoyanie ul.craft-info li {
		padding: 1px;
	}
	#page-wrapper .table-list.sostoyanie .separate {
		background-image: url(http://i.oldbk.com/i/sostojanie/hr_2.jpg);
		height: 2px;
		background-color: transparent;
	}
	#page-wrapper .table-list.sostoyanie .separate.side {
		width: 250px;
		margin: 0 auto;
	}
	#page-wrapper .table-list.sostoyanie .row-right .separate.side {
		margin-left: -1px;
	}
</style>
<div id="" class="page-content">
	<div class="btn-control">

		<div class="button-mid btn" onclick="location.href='/main.php?edit=1&amp;effects=1282';">Обновить</div>
		<div class="button-mid btn" onclick="location.href='/main.php?back=1566';">Вернуться</div>

	</div>
	<?= $this->renderPartial('common/menu', ['active' => $menu]) ?>
	<table class="table-list sostoyanie" cellpadding="0" cellspacing="0">
		<colgroup>
			<col width="280px">
			<col>
			<col width="280px">
		</colgroup>
		<tbody>
		<tr class="element">
			<td class="row-left"></td>
			<td class="row-center"></td>
			<td class="row-right"></td>
		</tr>
		<?php
		$last = count($items) - 1;
		foreach ($items as $key => $item): ?>
			<tr class="element">
				<td class="row-left">
					<?= $item[0] ?>
				</td>
				<td class="row-center">
					<?= $item[1] ?>
				</td>
				<td class="row-right">
					<?= $item[2] ?>
				</td>
			</tr>
			<?php if($key < $last): ?>
				<tr class="element">
					<td class="row-left">
						<div class="separate side"> </div>
					</td>
					<td class="row-center">
						<div class="separate"> </div>
					</td>
					<td class="row-right">
						<div class="separate side"> </div>
					</td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if($bonus): ?>
			<tr class="element">
				<td class="row-left">
					<img src="<?= $bonus['image'] ?>"><br>
					<small><b>Усиление</b></small>
				</td>
				<td class="row-center">
					<?= sprintf($bonus['text'], $bonus['count']); ?>
				</td>
				<td class="row-right"></td>
			</tr>
		<?php endif; ?>
		</tbody>
		<tfoot>
		<tr class="row-footer">
			<td>
				<div class="row-footer-left"></div>
			</td>
			<td></td>
			<td>
				<div class="row-footer-right"></div>
			</td>
		</tr>
		</tfoot>
	</table>
</div>
