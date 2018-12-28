<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 22.08.17
 * Time: 18:35
 *
 * @var \components\Component\Slim\Slim $app
 */

require_once ROOT_DIR.'/components/config/routes.php';

if(isset($_SESSION['uid'])) {
	try {
		$qdialog_info = $app->cache->get(sprintf('dialog_%d', $_SESSION['uid']));
		$qdialog_info = [
			'main' => true,
		];
		if(!$qdialog_info) {
			throw new Exception(null);
		}
		$BotDialog = new \components\Component\Quests\QuestDialogInteractive(\components\Helper\BotHelper::BOT_ALISA);
		if($qdialog_info['main'] == true) {
			$dialogs = $BotDialog->getMainDialog();

			if($dialogs) {
				$html = $app->view()->renderPartial('common/dialogs', array('dialogs' => $dialogs, 'app' => $app));
				echo $html;
			}
			//\components\Component\VarDumper::d($dialogs, false);
		}


	} catch (Exception $ex) {
		if($ex->getMessage() != null) {
			\components\Helper\FileHelper::writeException($ex, 'dialog_checker');
		}
	}
}