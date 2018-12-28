<?php


namespace components\Controller\forum;


use Carbon\Carbon;
use components\Eloquent\ForumAppeal;
use components\Exceptions\ForumException;

/**
 * Class ManageForumAppealController
 * @package components\Controller\forum
 */
class ManageForumAppealController extends ForumController
{
    /**
     *
     */
    public function appealAction()
    {

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canManageAppeals()) {
                throw new ForumException('Нет прав');
            }

            $request = $this->app->request;

            try {

                $whereDate = $request->get('date')
                    ? Carbon::parse($request->get('date'))->toDateString()
                    : Carbon::now()->toDateString();

            } catch (\Exception $exception){
                $whereDate = Carbon::now()->toDateString();
            }

            $this->app->view()->appendLayoutData([
                'categories' => $this->getCategories(),
            ]);

            $appeals_query = ForumAppeal::with([
                    'post',
                    'violator',
                    'reporter',
                    'moderator',
                ])
                ->with(['topic' => function($q){
                    $q->with('children');
                }]);

            if ($request->get('trashed')) {
                $appeals_query->withTrashed();
            } elseif ($request->get('only_trashed')) {
                $appeals_query->onlyTrashed();
            }

            $appeals = $appeals_query
                ->whereDate('created_at', '=', $whereDate)
                ->orderBy('id', 'desc')
                ->paginate(10, ['*'], 'page', $request->get('page'));


            $appeals->getCollection()->map(function (ForumAppeal $appeal) {

                if ($appeal->topic) {

                    if (is_null($appeal->topic->children)) {
                        $appeal['post_page'] = 1;
                        return $appeal;
                    }

                    $pkey = 0;
                    foreach ($appeal->topic->children as $key => $post) {
                        if ($post['id'] == $appeal->post_id) {
                            $pkey = $key;
                            break;
                        }
                    }

                    $appeal['post_page'] = floor($pkey / 20) + 1;
                }

                return $appeal;

            });

            $appeals->appends($request->get());

            //рендерим список
            $this->render('forum/manage/appeal/list', [
                'appeals' => $appeals,
                'elements' => $this->makeElements($appeals),
            ]);

        } catch (ForumException $exception) {
            $this->app->redirectTo('forum');
        }

    }

    /**
     * @param $id
     */
    public function appealApproveAction($id)
    {

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canManageAppeals()) {
                throw new ForumException('Нет прав');
            }

            $fa = ForumAppeal::where('id', '=', $id)->first();

            if (!$fa) {
                throw new ForumException('Жалоба не найдена');
            }

            $fa->update([
                'moderator_id' => $this->user->id
            ]);
            $fa->delete();

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Жалоба обработана',
            ]);

            $this->app->redirect($this->app->request->getReferer());

        } catch (ForumException $exception) {

            $this->app->redirectWithError($exception->getMessage());
        }
    }

    /**
     * @param $id
     */
    public function appealUnapproveAction($id)
    {

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canManageAppeals()) {
                throw new ForumException('Нет прав');
            }

            $fa = ForumAppeal::onlyTrashed()->where('id', '=', $id)->first();

            if (!$fa) {
                throw new ForumException('Жалоба не найдена');
            }

            $fa->update([
                'moderator_id' => null
            ]);
            $fa->restore();

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Обработка аннулирована',
            ]);

            $this->app->redirect($this->app->request->getReferer());

        } catch (ForumException $exception) {

            $this->app->redirectWithError($exception->getMessage());
        }
    }

    /**
     * @param $id
     */
    public function appealDeleteAction($id)
    {

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canManageAppeals()) {
                throw new ForumException('Нет прав');
            }


            if ($this->user->isAdmin() || $this->user->isHighPaladin()) {
                $fa = ForumAppeal::where('id', '=', $id)->first();

                if (!$fa) {
                    throw new ForumException('Жалоба не найдена');
                }

                $fa->forceDelete();

                $this->app->flash('noty', [
                    'type' => 'success',
                    'msg' => 'Жалоба удалена',
                ]);

                return $this->app->redirect($this->app->request->getReferer());
            }

            throw new ForumException('Нет прав');

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }
    }
}