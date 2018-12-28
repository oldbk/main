<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 14.12.16
 * Time: 19:42
 */ 

use components\Model\LibraryPage;

?>

<?= $page['body'] ?>
<?php
if ($page['type'] > 0 && !empty($page['var_from']) && !empty($page['var_to'])) {
        require_once $_SERVER['DOCUMENT_ROOT']."/config_ko.php";

	?><br><br><?php
	if (isset(${$page['var_from']}) && isset(${$page['var_to']}) && $page['type'] == LibraryPage::TYPE_ACTION && time() >= ${$page['var_from']} && time() <= ${$page['var_to']}) {
		?>
		<i>Акция действует в настоящий момент c <?=date("d.m.Y H:i:s",${$page['var_from']}) ?> по <?=date("d.m.Y H:i:s",${$page['var_to']}) ?></i>
		<?php
	}
	if (isset(${$page['var_from']}) && isset(${$page['var_to']}) && $page['type'] == LibraryPage::TYPE_EVENT && time() >= ${$page['var_from']} && time() <= ${$page['var_to']}) {
		?>
		<i>Событие действует в настоящий момент c <?=date("d.m.Y H:i:s",${$page['var_from']}) ?> по <?=date("d.m.Y H:i:s",${$page['var_to']}) ?></i>
		<?php
	}
	if (isset(${$page['var_from']}) && isset(${$page['var_to']}) && $page['type'] == LibraryPage::TYPE_QUEST && time() >= ${$page['var_from']} && time() <= ${$page['var_to']}) {
		?>
		<i>Квест действует в настоящий момент c <?=date("d.m.Y H:i:s",${$page['var_from']}) ?> по <?=date("d.m.Y H:i:s",${$page['var_to']}) ?></i>
		<?php
	}
}

?>
