<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 13.12.16
 * Time: 22:22
 *
 * @var \components\Object\Item $Item
 */

?>

<div class="" style="text-align: center;margin-bottom: 10px;">
    <h1><?= $Item->item->name ?></h1>
    <img src="<?= $Item->img_big ?>" alt="<?= $Item->item->name ?>">
</div>
<table class="table_library_one" cellspacing="0" cellpadding="0">
<?=$this->renderPartial('common/renderitem',array('Item' => $Item));?>
</table>
<div>&nbsp;</div>