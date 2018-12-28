<?php
namespace components\Controller;
use components\Component\Quests\QuestDialogInteractive;
use \components\Controller\_base\MainController;
use components\models\quest\UserDialog;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 *
 */
class DialogController extends MainController
{
    public function dialogAction()
    {
    	$dialog_id = (int)$this->app->request->post('d');
		$bot_id = (int)$this->app->request->post('b');

    	$DialogObj = new QuestDialogInteractive($bot_id);
		$conversation = $DialogObj->dialog($dialog_id, null);

		$html = $this->renderPartial('common/quest', $conversation, true);

		$this->renderJSON(array(
			'status' => 1,
			'content' => $html,
		));
    }

	public function actionAction()
	{
		$dialog_id = (int)$this->app->request->post('d');
		$action_id = (int)$this->app->request->post('a');
		$bot_id = (int)$this->app->request->post('b');

		$DialogObj = new QuestDialogInteractive($bot_id);
		$conversation = $DialogObj->dialog($dialog_id, $action_id);

		$html = $this->renderPartial('common/quest', $conversation, true);

		$this->renderJSON(array(
			'status' => 1,
			'content' => $html,
		));
	}

	public function offAction()
	{
		$dialog_id = (int)$this->app->request->get('d');
		$quest_id = (int)$this->app->request->get('q');
		UserDialog::updateOrCreate([
			'user_id' => $this->app->webUser->getId(),
			'quest_id' => $quest_id,
			'dialog_id' => $dialog_id,
		], ['state' => 0]);

		$this->renderJSON(array(
			'status' => 1,
		));
	}

	public function onAction()
	{
		$dialog_id = (int)$this->app->request->get('d');
		$quest_id = (int)$this->app->request->get('q');

		UserDialog::updateOrCreate([
			'user_id' => $this->app->webUser->getId(),
			'quest_id' => $quest_id,
			'dialog_id' => $dialog_id,
		], ['state' => 1]);

		$this->renderJSON(array(
			'status' => 1,
		));
	}
}