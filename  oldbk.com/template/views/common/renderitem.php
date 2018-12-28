<?php
use \components\Helper\TimeHelper;

?>
    <tr>
        <td align="center" valign="top"  width="150">
            <div style="padding: 20px;">
                <img src="https://i.oldbk.com/i/sh/<?= $Item->item->img ?>">
            </div>
        </td>
        <td align="left">
            <h3>
                <?= $Item->item->name; ?> <img src="https://i.oldbk.com/i/align_<?= $Item->item->nalign == 1 ? '1.5' : $Item->item->nalign ?>.gif"> (Вес: <?= $Item->item->massa ?>)
                <?php if($Item->isArt()): ?>
                    <IMG SRC="https://i.oldbk.com/i/artefact.gif" WIDTH="18" HEIGHT="16" BORDER=0 TITLE="Артефакт" alt="Артефакт">
                <?php endif; ?>
                <?php if($Item->isUnlim()): ?>
                    <IMG SRC="https://i.oldbk.com/i/noobs.png" WIDTH="14" HEIGHT="8" BORDER=0 TITLE="Эту вещь всегда можно купить в Гос.магазине" alt="Эту вещь всегда можно купить в Гос.магазине">
                <?php endif; ?>
            </h3>
            <?php if(($gold_price = $Item->getGold()) > 0): ?>
                <b>Цена: <?= $gold_price ?> </b><img src=https://i.oldbk.com/i/icon/coin_icon.png> &nbsp; &nbsp;
            <?php elseif($Item->item->repcost > 0): ?>
                <b>Цена: <?= $Item->item->repcost ?> реп.</b> &nbsp; &nbsp;
            <?php elseif($Item->item->ecost > 0): ?>
                <b>Цена: <?= $Item->item->ecost ?> екр.</b> &nbsp; &nbsp;
            <?php elseif($Item->item->cost > 0): ?>
                <b>Цена: <?= $Item->item->cost ?> кр.</b> &nbsp; &nbsp;
            <?php endif; ?>

	    <?php if ($Item->item->id == 501) { ?>
		<br><span style='background-color:white;'>Для профилактики травм</span>
	    <?php } ?>
	    <?php if (strlen($Item->item->letter) && $Item->item->letter != "0") { ?>
		<br><div style='width:90%;border: 1px solid;padding: 4px;border-style: inset;border-width: 2px;'><?=$Item->item->letter?></div>
	    <?php } ?>


            <div>
                Долговечность : <?= $Item->item->duration ?>/<?= $Item->item->maxdur ?>
            </div>
            <?php if($Item->item->needident): ?>
                <font color=maroon><B>Свойства предмета не идентифицированы</B></font><BR>
            <?php else: ?>
            <?php endif; ?>


	    <?php 
		// действует на
		if ($Item->item->gmeshok || $Item->item->gsila || $Item->item->mfkrit || $Item->item->mfakrit || $Item->item->mfuvorot || $Item->item->mfauvorot || $Item->item->glovk || $Item->item->ghp || $Item->item->ginta || $Item->item->gintel || $Item->item->gnoj || $Item->item->gtopor || $Item->item->gdubina || $Item->item->gmech || $Item->item->gfire || $Item->item->gwater || $Item->item->gair || $Item->item->gearth || $Item->item->gearth || $Item->item->glight || $Item->item->ggray || $Item->item->gdark || $Item->item->minu || $Item->item->maxu || $Item->item->bron1 || $Item->item->bron2 || $Item->item->bron3 || $Item->item->bron4 || $Item->item->craftbonus || $Item->item->craftspeedup || (isset($Item->item->mfchance) && $Item->item->mfchance > 0)) { ?>
		<br><b>Действует на:</b><br>
	    <?php } ?>

	    <?php if ($Item->item->minu) { ?>
		Минимальное наносимое повреждение: <?= $Item->item->minu ?></b><br>
	    <?php } ?>
	    <?php if ($Item->item->maxu) { ?>
		Максимальное наносимое повреждение: <?= $Item->item->maxu ?></b><br>
	    <?php } ?>
	    <?php if ($Item->item->craftbonus) { ?>
		Бонус мастерства: <?= $Item->item->craftbonus ?><br>
	    <?php } ?>
	    <?php if ($Item->item->craftspeedup) { ?>
		Сокращает время производства на: <?= $Item->item->craftspeedup ?>%<br>
	    <?php } ?>

	    <?php if (isset($Item->item->mfchance) && $Item->item->mfchance > 0) { ?>
		Шанс модификации: <?= $Item->item->mfchance ?>%<br>
	    <?php } ?>

	    <?php if ($Item->item->gsila) { ?>
		Сила: <?= $Item->item->gsila ?><br>
	    <?php } ?>
	    <?php if ($Item->item->glovk) { ?>
		Ловкость: <?= $Item->item->glovk ?><br>
	    <?php } ?>
	    <?php if ($Item->item->ginta) { ?>
		Интуиция: <?= $Item->item->ginta ?><br>
	    <?php } ?>
	    <?php if ($Item->item->gintel) { ?>
		Интеллект: <?= $Item->item->gintel ?><br>
	    <?php } ?>
	    <?php if ($Item->item->gmp) { ?>
		Мудрость: <?= $Item->item->gmp ?><br>
	    <?php } ?>
	    <?php if ($Item->item->ghp) { ?>
		Уровень жизни: <?= $Item->item->ghp ?><br>
	    <?php } ?>


	    <?php if ($Item->item->mfkrit) { ?>
		Мф. критических ударов: <?= $Item->item->mfkrit ?>%<br>
	    <?php } ?>
	    <?php if ($Item->item->mfakrit) { ?>
		Мф. против крит. ударов: <?= $Item->item->mfakrit ?>%<br>
	    <?php } ?>
	    <?php if ($Item->item->mfuvorot) { ?>
		Мф. увертливости: <?= $Item->item->mfuvorot ?>%<br>
	    <?php } ?>
	    <?php if ($Item->item->mfauvorot) { ?>
		Мф. против увертливости: <?= $Item->item->mfauvorot ?>%<br>
	    <?php } ?>


	    <?php if ($Item->item->gnoj) { ?>
		Мастерство владения ножами и кастетами: <?= $Item->item->gnoj ?><br>
	    <?php } ?>
	    <?php if ($Item->item->gtopor) { ?>
		Мастерство владения топорами и секирами: <?= $Item->item->gtopor ?><br>
	    <?php } ?>
	    <?php if ($Item->item->gdubina) { ?>
		Мастерство владения дубинами и булавами: <?= $Item->item->gdubina ?><br>
	    <?php } ?>
	    <?php if ($Item->item->gmech) { ?>
		Мастерство владения мечами: <?= $Item->item->gmech ?><br>
	    <?php } ?>


	    <?php if ($Item->item->gfire) { ?>
		Мастерство владения стихией Огня: <?= $Item->item->gfire ?><br>
	    <?php } ?>
	    <?php if ($Item->item->gwater) { ?>
		Мастерство владения стихией Воды: <?= $Item->item->gwater ?><br>
	    <?php } ?>
	    <?php if ($Item->item->gair) { ?>
		Мастерство владения стихией Воздуха: <?= $Item->item->gair ?><br>
	    <?php } ?>
	    <?php if ($Item->item->gearth) { ?>
		Мастерство владения стихией Земли: <?= $Item->item->gearth ?><br>
	    <?php } ?>



	    <?php if ($Item->item->glight) { ?>
		Мастерство владения магией Света: <?= $Item->item->glight ?><br>
	    <?php } ?>
	    <?php if ($Item->item->ggray) { ?>
		Мастерство владения серой магией: <?= $Item->item->ggray ?><br>
	    <?php } ?>
	    <?php if ($Item->item->gdark) { ?>
		 Мастерство владения магией Тьмы: <?= $Item->item->gdark ?><br>
	    <?php } ?>


	    <?php if ($Item->item->bron1) { ?>
		 Броня головы: <?= $Item->item->bron1 ?><br>
	    <?php } ?>
	    <?php if ($Item->item->bron2) { ?>
		 Броня корпуса: <?= $Item->item->bron2 ?><br>
	    <?php } ?>
	    <?php if ($Item->item->bron3) { ?>
		 Броня пояса: <?= $Item->item->bron3 ?><br>
	    <?php } ?>
	    <?php if ($Item->item->bron4) { ?>
		 Броня ног: <?= $Item->item->bron4 ?><br>
	    <?php } ?>


	    <?php if ($Item->item->stbonus) { ?>
		Возможных увеличений: <b><?= $Item->item->stbonus ?></b><br>
	    <?php } ?>

	    <?php if ($Item->item->mfbonus) { ?>
		Возможных увеличений мф: <b><?= $Item->item->mfbonus ?></b><br>
	    <?php } ?>

	    <?php 
		// требуется минимальное
		if ((is_object($Item->incmagic) && $Item->incmagic->nlevel > 0) || $Item->item->nsex || $Item->item->nsila || $Item->item->nlovk || $Item->item->ninta || $Item->item->nvinos || $Item->item->nlevel || $Item->item->nintel || $Item->item->nmudra || $Item->item->nnoj || $Item->item->ntopor || $Item->item->ndubina || $Item->item->nmech || $Item->item->nfire || $Item->item->nwater || $Item->item->nair || $Item->item->nearth || $Item->item->nlight || $Item->item->ngray || $Item->item->ndark || $Item->item->nclass ) { ?>
		<br><b>Требуется минимальное:</b><BR>
	    <?php } ?>


	    <?php if ($Item->item->nclass) { ?>
		Класс персонажа: <b><?
			if ($Item->item->nclass == 1) echo 'Уворотчик';
			if ($Item->item->nclass == 2) echo 'Критовик';
			if ($Item->item->nclass == 3) echo 'Танк';
			if ($Item->item->nclass == 4) echo 'Любой';
		?></b><br>
	    <?php } ?>


	    <?php if ($Item->item->nsila) { ?>
		Сила: <?= $Item->item->nsila ?><br>
	    <?php } ?>
	    <?php if ($Item->item->nlovk) { ?>
		Ловкость: <?= $Item->item->nlovk ?><br>
	    <?php } ?>
	    <?php if ($Item->item->ninta) { ?>
		Интуиция: <?= $Item->item->ninta ?><br>
	    <?php } ?>
	    <?php if ($Item->item->nintel) { ?>
		Интеллект: <?= $Item->item->nintel ?><br>
	    <?php } ?>
	    <?php if ($Item->item->nvinos) { ?>
		Выносливость: <?= $Item->item->nvinos ?><br>
	    <?php } ?>

	    <?php if ($Item->item->nlevel > 0 || (is_object($Item->incmagic) && $Item->incmagic->nlevel > 0)) { ?>
		Уровень: <?php
		$nlevel = $Item->item->nlevel;
		if (is_object($Item->incmagic) && $Item->incmagic->nlevel > $nlevel) {
			$nlevel = $Item->incmagic->nlevel;
		}
		echo $nlevel;
		?><br>
	    <?php } ?>
	    <?php if ($Item->item->nmudra) { ?>
		Мудрость: <?= $Item->item->nmudra ?><br>
	    <?php } ?>


	    <?php if ($Item->item->nnoj) { ?>
		Мастерство владения ножами и кастетами: <?= $Item->item->nnoj ?><br>
	    <?php } ?>
	    <?php if ($Item->item->ntopor) { ?>
		Мастерство владения топорами и секирами: <?= $Item->item->ntopor ?><br>
	    <?php } ?>
	    <?php if ($Item->item->ndubina) { ?>
		Мастерство владения дубинами и булавами: <?= $Item->item->ndubina ?><br>
	    <?php } ?>
	    <?php if ($Item->item->nmech) { ?>
		Мастерство владения мечами: <?= $Item->item->nmech ?><br>
	    <?php } ?>


	    <?php if ($Item->item->nfire) { ?>
		Мастерство владения стихией Огня: <?= $Item->item->nfire ?><br>
	    <?php } ?>
	    <?php if ($Item->item->nwater) { ?>
		Мастерство владения стихией Воды: <?= $Item->item->nwater ?><br>
	    <?php } ?>
	    <?php if ($Item->item->nair) { ?>
		Мастерство владения стихией Воздуха: <?= $Item->item->nair ?><br>
	    <?php } ?>
	    <?php if ($Item->item->nearth) { ?>
		Мастерство владения стихией Земли: <?= $Item->item->nearth ?><br>
	    <?php } ?>



	    <?php if ($Item->item->nlight) { ?>
		Мастерство владения магией Света: <?= $Item->item->nlight ?><br>
	    <?php } ?>
	    <?php if ($Item->item->ngray) { ?>
		Мастерство владения серой магией: <?= $Item->item->ngray ?><br>
	    <?php } ?>
	    <?php if ($Item->item->ndark) { ?>
		 Мастерство владения магией Тьмы: <?= $Item->item->ndark ?><br>
	    <?php } ?>

	    <?php if ($Item->item->nsex == 1) { ?>
		Пол: Женский<br>
	    <?php } ?>
	    <?php if ($Item->item->nsex == 2) { ?>
		Пол: Мужской<br>
	    <?php } ?>

	    <?php if (is_object($Item->magic) && $Item->magic->id == 8888) { ?>
		Светлая, Темная, Нейтральная склонность<br>
	    <?php } ?>

	    <?php
		// ограничения
		if (
			(is_object($Item->magic) && strlen($Item->magic->name) && ($Item->magic->us_type > 0 || $Item->magic->target_type > 0)) ||
			(is_object($Item->magic) && strlen($Item->magic->img) && $Item->item->type == 12 && $Item->item->dategoden == 0) ||
			((is_object($Item->magic) && $Item->item->type == 12) && (!strlen($Item->magic->img) || $Item->item->dategoden > 0)) ||
			!$Item->item->isrep ||
			$Item->item->goden ||
			$Item->item->notsell
		) {
	    ?>
		<br><b>Ограничения:</b><br>
	    <?php 
		  }
            ?>

	    <?php if ($Item->item->goden) { ?>
		Срок годности: <b><?= $Item->item->goden ?> дн.</b><br>
	    <?php } ?>


	    <?php if ($Item->item->id >= 946 && $Item->item->id <= 957) { ?>
		<small><font color=red>Невозможно одновременно надеть более 4-х предметов Ярмарки, в том числе не более одного кольца</font></small><br>
	    <?php } ?>

	    <?php if ((is_object($Item->magic) && $Item->item->type == 12) && (!strlen($Item->magic->img) || $Item->item->dategoden > 0)) { ?>
		Не может встраиваться в вещи<BR>
	    <?php } ?>

	    <?php if (is_object($Item->magic) && $Item->magic->id == 8888) { ?>
		Может быть использован только на свой уровень<br>
	    <?php } ?>

	    <?php if (is_object($Item->magic) && strlen($Item->magic->name) && $Item->magic->us_type == 2) { ?>
		Можно использовать только вне боя<br>
	    <?php } ?>

	    <?php if (is_object($Item->magic) && strlen($Item->magic->name) && $Item->magic->us_type == 1) { ?>
		Можно использовать только в бою<br>
	    <?php } ?>

	    <?php if (is_object($Item->magic) && strlen($Item->magic->name) && $Item->magic->target_type == 1) { ?>
		Можно использовать только на себя<br>
	    <?php } ?>


	    <?php if (!$Item->item->isrep && !($Item->item->id >= 946 && $Item->item->id <= 957)) { ?>
		<font color=maroon>Предмет не подлежит ремонту</font><BR>
	    <?php } ?>

	    <?php if ($Item->item->id >= 946 && $Item->item->id <= 957) { ?>
		<font color=maroon>Предмет не подлежит модификации</font><BR>
		<font color=maroon>Предмет не подлежит чарованию</font><BR>
	    <?php } ?>

	    <?php if ($Item->item->notsell) { ?>
		<font color=maroon>Предмет не подлежит продаже в Гос. магазин</font><BR>
	    <?php } ?>

	    <?php
		// свойства
		if (
			$Item->item->id == 30012 ||
			(is_object($Item->magic) && strlen($Item->magic->name) && $Item->item->type == 50) ||
			$Item->item->rareitem > 0 ||
			$Item->item->type == 27 ||
			$Item->item->type == 28 ||
			(is_object($Item->magic) && strlen($Item->magic->name) && $Item->item->type != 50) ||
			(is_object($Item->magic) && $Item->magic->chanse) ||
                        (is_object($Item->magic) && $Item->magic->time) ||
			is_object($Item->incmagic)
		) {
	    ?>
		<br><b>Свойства:</b><br>
	    <?php 
		  }
            ?>


	    <?php if (is_object($Item->magic) && strlen($Item->magic->name) && $Item->item->type == 50) {
			if (strlen($Item->magic->name >=4) && stripos(substr($Item->magic->name,0,-4),'<br>') !== false) { ?>
				<?=$Item->magic->name ?>
		  <?php } else { ?>
				• <?=$Item->magic->name ?><BR>
		  <?php } ?>
	    <?php } ?>


	    <?php if ($Item->item->id == 30012) { ?>
		В три раза увеличивает срок годности букета<br>
	    <?php } ?>

 
	    <?php if (is_object($Item->magic) && strlen($Item->magic->name) && $Item->item->type != 50) { ?>
		<font color=maroon>Наложены заклятия:</font> <?=$Item->magic->name ?><br>
	    <?php } ?>	    

	    <?php if (is_object($Item->incmagic)) { ?>
		Встроено заклятие <img src="https://i.oldbk.com/i/magic/<?=$Item->incmagic->img?>" title="<?=$Item->incmagic->name?>"> 0/<?=$Item->item->includemagicmax?> шт.<BR>
		Количество перезарядок: <?=$Item->item->includemagicuses?><br>
	    <?php } ?>	

	    <?php if (is_object($Item->magic) && $Item->magic->chanse) { ?>
		Вероятность срабатывания: <?= $Item->magic->chanse ?>%</b><br>
	    <?php } ?>

	    <?php if (is_object($Item->incmagic) && $Item->incmagic->chanse) { ?>
		Вероятность срабатывания: <?= $Item->incmagic->chanse ?>%</b><br>
	    <?php } ?>

	    <?php if (is_object($Item->magic) && $Item->magic->time) { ?>
		Продолжительность действия магии: <b><?= TimeHelper::prettyTime(null,time()+($Item->magic->time*60)) ?></b><br>
	    <?php } ?>
	    <?php if (is_object($Item->incmagic) && $Item->incmagic->time) { ?>
		Продолжительность действия магии: <b><?= TimeHelper::prettyTime(null,time()+($Item->incmagic->time*60)) ?></b><br>
	    <?php } ?>

            <?php if($Item->item->type == 27): ?>
                Может одеваться на броню<br>
            <?php elseif($Item->item->type == 28): ?>
                Может одеваться под броню<br>
            <?php endif; ?>

	    <?php if ($Item->item->rareitem == 10) { ?>
		<font color="#676565"><b>Обычный предмет</b></font><BR>
	    <?php } ?>
	    <?php if ($Item->item->rareitem == 1) { ?>
		<font color="#34a122"><b>Редкий предмет</b></font><BR>
	    <?php } ?>
	    <?php if ($Item->item->rareitem == 2) { ?>
		<font color="#2145ad"><b>Великий предмет</b></font><BR>
	    <?php } ?>
	    <?php if ($Item->item->rareitem == 3) { ?>
		<font color="#760c90"><b>Легендарный предмет</b></font><BR>
	    <?php } ?>



	    <?php if ($Item->item->ab_mf > 0 || $Item->item->ab_bron || $Item->item->ab_uron || ($Item->item->id >= 55510301 && $Item->item->id <= 55510401) || ($Item->item->id >= 410027 && $Item->item->id <= 410028) || $Item->item->type == 30 || (is_object($Item->magic) && $Item->magic->id && $Item->get_rkm_bonus_by_magic($Item->magic->id))) { ?>
		<br><b>Усиление:</b><br>
		    <?php if ($Item->item->ab_mf > 0) { ?>
			Максимального мф.: +<?=$Item->item->ab_mf?>%<br>
		    <?php } ?>
		    <?php if ($Item->item->ab_bron > 0) { ?>
			Брони: +<?=$Item->item->ab_bron?>%<br>
		    <?php } ?>
		    <?php if ($Item->item->ab_uron > 0) { ?>
			Урона: +<?=$Item->item->ab_uron?>%<br>
		    <?php } ?>
		    <?php
			$bonus = $Item->getElkaBuketBonus();
			if ($bonus > 0) { ?>
				Рунного опыта: +<?=$bonus?>%<br>
		  <?php } ?>

		    <?php if ($Item->item->id == 55510351) { ?>
			Опыта: +10%<br>
		    <?php } ?>

		    <?php if ($Item->item->id == 55510352) { ?>
			Опыта: +30%<br>
			Получаемая репутация: +20%<br>
		    <?php } ?>


		    <?php if ($Item->item->id == 410027) { ?>
			Опыта: +10%<br>
			Получаемая репутация: +10%<br>
		    <?php } ?>
		    <?php if ($Item->item->id == 410028) { ?>
			Опыта: +30%<br>
			Получаемая репутация: +20%<br>
		    <?php } ?>

		<?php if ($Item->item->type == 30) { 
				$ab = $Item->getEmptyRune();
				if ($ab['ab_mf'] > 0) {
					echo "Максимального мф.: 0%<br>";
				}
				if ($ab['ab_bron'] > 0) {
					echo "Брони: 0%<br>";
				}
				if ($ab['ab_uron'] > 0) {
					echo "Урона: 0%<br>";
				}
		      } ?>


			<?php
			if (is_object($Item->magic) && $Item->magic->id && $Item->get_rkm_bonus_by_magic($Item->magic->id)) { ?>
				Рунного опыта от маг. урона: +<?=$Item->get_rkm_bonus_by_magic($Item->magic->id)?>%<br>
			<?php			
			}
			?>
	    <?php } ?>
	    <div>&nbsp;</div>
        </td>
    </tr>
