<?php

namespace components\Controller\forum;


use components\Eloquent\Forum;
use components\Exceptions\ForumException;

/**
 * Class ManageForumCategoryController
 * @package components\Controller\forum
 */
class ManageForumCategoryController extends ForumController
{
    /**
     *
     */
    public function categoryAction()
    {

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canManageCategories()) {
                throw new ForumException('Нет прав');
            }


            $this->app->view()->appendLayoutData([
                'categories' => $this->getCategories(),
            ]);


            $all_category = Forum::whereType(1)->orderBy('fix', 'asc')->get();

            //рендерим список
            $this->render('forum/manage/category/list', [
                'cats' => $all_category,
            ]);

        } catch (ForumException $exception) {

            $this->app->redirectTo('forum');
        }

    }

    /**
     * @param $id
     */
    public function categoryManageAction($id)
    {
        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canManageCategories()) {
                throw new ForumException('Нет прав');
            }

            $this->app->view()->appendLayoutData([
                'categories' => $this->getCategories(),
            ]);


            $selected_category = Forum::find($id);

            //рендерим список
            $this->render('forum/manage/category/edit', [
                'selected_category' => $selected_category,
            ]);

        } catch (ForumException $exception) {
            $this->app->redirectTo('forum');
        }
    }

    /**
     * @param $id
     */
    public function categoryManageEditAction($id)
    {

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canManageCategories()) {
                throw new ForumException('Нет прав');
            }

            $post = $this->app->request->post();

            Forum::find($id)->update([
                'topic' => $post['topic'],
                'text' => $post['text'],
                'fix' => $post['fix'],
                'min_align' => $post['min_align'],
                'max_align' => $post['max_align'],
                'min_level' => $post['min_level'],
                'max_level' => $post['max_level'],
                'is_closed' => $post['is_closed'] ?? 0,
                'only_own' => $post['only_own'] ?? 0,
				'is_test' => $post['only_tester'] ?? 0
            ]);

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Конференция сохранена',
            ]);

            $this->app->redirect($this->app->request->getReferer());

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }

    }

    /**
     *
     */
    public function categoryManageCreateAction()
    {

        $this->app->view()->appendLayoutData([
            'categories' => $this->getCategories(),
        ]);


        $cat_ids = Forum::whereType(1)->where('id', '<=', 100)->get(['id'])->pluck('id');
        $range_collection = collect(range(1, 100));
        $available_cat_ids = $range_collection->diff($cat_ids)->values();

        $this->render('forum/manage/category/create', [
            'available_cat_ids' => $available_cat_ids
        ]);
    }

    /**
     *
     */
    public function categoryManageSaveAction()
    {

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canManageCategories()) {
                throw new ForumException('Нет прав');
            }

            $post = $this->app->request->post();

            //Валидация входящих данных
            $validator = \Validator::make(
                $post,
                [
                    'topic' => [
                        'required',
                        'max:65',
                    ],
                    'fix' => [
                        'required',
                    ],
                ],
                [
                    'topic.required' => 'Название не может быть пустым',
                    'topic.max' => 'Максимальная длина заголовка 65 символов',
                    'fix.required' => 'Укажите позицию',
                ]
            );

            if ($validator->fails()) {
                throw new ForumException($validator->errors()->first());
            }

            $new_cat = Forum::create([
                'id' => $post['id'],
                'topic' => $post['topic'],
                'type' => 1,
                'text' => $post['text'],
                'fix' => $post['fix'],
                'min_align' => $post['min_align'],
                'max_align' => $post['max_align'],
                'min_level' => $post['min_level'],
                'max_level' => $post['max_level'],
                'only_own' => $post['only_own'] ?? 0,
                'is_test' => $post['only_tester'] ?? 0,
                'is_closed' => 1,
            ]);

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Конференция создана(но еще скрыта от просмотра)',
            ]);

            $this->app->redirectTo('manage_category', ['id' => $new_cat->id]);

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }
    }

    /**
     * @param $id
     */
    public function categoryManageDeleteAction($id)
    {

        try {

            if (!$this->user) {
                throw new ForumException('Нет прав');
            }

            if (!$this->user->canManageCategories()) {
                throw new ForumException('Нет прав');
            }

            $cat = Forum::find($id);
//            $topics = Forum::where('parent', $cat->id);
//            $posts = Forum::whereIn('parent', $topics->pluck('id'));

//            $posts->delete();//удаляем посты
//            $topics->delete();//удаляем топики
            $cat->delete();//удаляем конфу

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => 'Конференция удалена',
            ]);

            $this->app->redirect($this->app->request->getReferer());

        } catch (ForumException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }

    }
}