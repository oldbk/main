<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 04.09.17
 * Time: 18:04
 *
 * @var array $profs
 * @var \components\models\UsersCraft $UserProfs
 * @var array $config
 * @var \components\Component\Slim\View $this
 * @var int $menu
 */
?>

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
	#page-wrapper .table-list.sostoyanie .row-left .separate {
		position: absolute;
		left: 15px;
		right: 8px;
		bottom: 0;
	}
	#page-wrapper .table-list.sostoyanie .row-right .separate {
		position: absolute;
		left: 8px;
		right: 15px;
		bottom: 0;
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
		<?php $i = 0; while(true):
			if(!isset($profs[$i])) {break;}
			$first = $profs[$i];
			$second = isset($profs[$i+1]) ? $profs[$i+1] : null;

			$flevel = $UserProfs->getLevelByName($first['name']);
			$fexp = $UserProfs->getExpByName($first['name']);

			$slevel = null;
			$sexp = null;
			if($second) {
				$slevel = $UserProfs->getLevelByName($second['name']);
				$sexp = $UserProfs->getExpByName($second['name']);
			}
			?>
			<tr class="element">
				<td class="row-left">
				</td>
				<td class="row-center">
					<table width="100%" cellpadding="0" cellspacing="0">
						<colgroup>
							<col width="60px">
							<col width="40%">
							<col width="60px">
							<col width="*">
						</colgroup>
						<tbody><tr>
							<td style="padding:3px;" valign="top">
								<img src="http://i.oldbk.com/i/craft/prof<?= $first['id'] ?>.png">
							</td>
							<td style="padding:3px;" valign="top">
								<ul class="craft-info">
									<li>
										<b><?= $first['rname'] ?> [<?= $flevel ?>]</b>
									</li>
									<li>
										Опыт <?= $fexp ?>/<?= isset($config['exp'][$flevel+1]) ? $config['exp'][$flevel+1] : '??' ?>
									</li>
									<?php if($bonus = $UserProfs->getBonusById($first['id'], $flevel)): ?>
										<li><?= $bonus ?></li>
									<?php endif; ?>
									<li>
										<?= $first['desc'] ?>
									</li>
								</ul>
							</td>
							<td style="padding:3px;" valign="top">
								<?php if($second): ?>
									<img src="http://i.oldbk.com/i/craft/prof<?= $second['id'] ?>.png">
								<?php endif; ?>
							</td>
							<td style="padding:3px;" valign="top">
								<?php if($second): ?>
									<ul class="craft-info">
										<li>
											<b><?= $second['rname'] ?> [<?= $slevel ?>]</b>
										</li>
										<li>
											Опыт <?= $sexp ?>/<?= isset($config['exp'][$slevel+1]) ? $config['exp'][$slevel+1] : '??' ?>
										</li>
										<?php if($bonus = $UserProfs->getBonusById($second['id'], $slevel)): ?>
											<li><?= $bonus ?></li>
										<?php endif; ?>
										<li>
											<?= $second['desc'] ?>
										</li>
									</ul>
								<?php endif; ?>
							</td>
						</tr>
						</tbody>
					</table>
				</td>
				<td class="row-right">
				</td>
			</tr>
			<?php if(isset($profs[$i + 2])): ?>
				<tr class="element">
					<td class="row-left"></td>
					<td class="row-center">
						<div class="separate"> </div>
					</td>
					<td class="row-right"></td>
				</tr>
			<?php endif; ?>
			<?php $i += 2; endwhile; ?>
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