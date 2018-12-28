<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 04.09.17
 * Time: 20:11
 *
 * @var int $active
 */ ?>

<table class="header" cellpadding="0" cellspacing="0">
	<thead>
	<tr class="header-line spoiler-click">
		<th>
			<div class="header-left"></div>
		</th>
		<th class="header-item">
			<a href="<?= $app->urlFor('effects', ['action' => 'craft']) ?>" class="<?= $active == 1 ? 'active' : '' ?>">
				<div class="header-title">
					<span>ремесла</span>
				</div>
			</a>
		</th>
		<th class="header-item">
			<a href="<?= $app->urlFor('effects', ['action' => 'attainment']) ?>" class="<?= $active == 2 ? 'active' : '' ?>">
				<div class="header-title"><span>достижения</span></div>
			</a>
		</th>
		<th class="header-item">
			<a href="<?= $app->urlFor('effects', ['action' => 'opportunity']) ?>" class="<?= $active == 3 ? 'active' : '' ?>">
				<div class="header-title"><span>возможности</span></div>
			</a>
		</th>
		<th class="header-item">
			<a href="<?= $app->urlFor('effects', ['action' => 'effect']) ?>" class="<?= $active == 4 ? 'active' : '' ?>">
				<div class="header-title"><span>эффекты</span></div>
			</a>
		</th>
		<th class="header-item">
			<a href="<?= $app->urlFor('effects', ['action' => 'quest']) ?>" class="<?= $active == 5 ? 'active' : '' ?>">
				<div class="header-title"><span>квесты</span></div>
			</a>
		</th>
		<th class="header-item">
			<a href="<?= $app->urlFor('effects', ['action' => 'medal']) ?>" class="<?= $active == 6 ? 'active' : '' ?>">
				<div class="header-title"><span>медали</span></div>
			</a>
		</th>
		<th>
			<div class="header-right"></div>
		</th>
	</tr>
	</thead>
</table>
