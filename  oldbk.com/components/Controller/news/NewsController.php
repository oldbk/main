<?php

namespace components\Controller\news;

use components\Component\Slim\Middleware\ClientScript\ClientScript;
use components\Component\Slim\Slim;
use components\Controller\_base\BaseController;
use components\Eloquent\News;
use components\Exceptions\NewsException;
use components\Traits\PaginatorTrait;


/**
 * Class NewsController
 * @package components\Controller\news
 */
class NewsController extends BaseController
{

    use PaginatorTrait;

    /**
     * @var string
     */
    protected $layout = 'news';

    /**
     * @var string
     */
    protected $title = 'Новости | ОлдБК: Бойцовский Клуб - ролевая бесплатная онлайн мморпг игра | Играть бесплатно в браузерную игру';

    /**
     * @var string
     */
    protected $description = 'Новая бесплатная многопользовательская MMORPG онлайн игра «Старый Бойцовский Клуб - ОлдБК». Стань участником Бойцовского Клуба Комбатс!';

    /**
     * @var null
     */
    protected $user = null;

    /**
     * @var bool|null
     */
    protected $post_id;

    /**
     * @var bool
     */
    protected $news_comments = false;
    
    /**
     * NewsController constructor.
     * @param Slim $app
     * @param $action
     * @param bool $actionParams
     */
    public function __construct(Slim $app, $action, $actionParams = null)
    {
        $this->user = \Auth::user();
        $this->post_id = $actionParams;
        $this->news_comments = \Config::get('news.comments', $this->news_comments);

        $app->view()->appendData([
            'user' => $this->user,
        ]);

        parent::__construct($app, $action, $actionParams);
    }

    /**
     * @param $action
     * @return bool
     */
    protected function beforeAction($action)
    {
        $this->paginatorResolver();

        return parent::beforeAction($action);
    }

    /**
     * @throws \Throwable
     */
    public function newsAction()
    {
		$this->http_fix_enable = true;

        $request = $this->app->request;

        try {

            $news_qury = News::homeNews();

            if ($this->news_comments) {
                $news_qury->withCount('comments');
            }

            $news = $news_qury->paginate(10, ['*'], 'page', $request->get('page'));

            if ($news->isEmpty()) {
                throw new NewsException('news.news_not_found');
            }

            $news->appends($request->get());

            $this->render('news', [
                'news' => $news,
                'news_comments' => $this->news_comments,
                'elements' => $this->makeElements($news),
            ]);

        } catch (NewsException $exception) {
            $this->app->flashNow('errors', [
                $exception->getMessage(),
            ]);

            $this->render('notfound');
        }

    }

    /**
     * @param $id
     * @throws \Throwable
     */
    public function postAction($id)
    {
		$this->http_fix_enable = true;
		
        try {

            $post = News::homeNews()->find($id);

            if (!$post) {
                throw new NewsException('news.post_not_found');
            }

            $this->title = $post['topic'] . ' | ' . $this->title;

            if ($this->news_comments) {
                $comments = $post->comments()
                    ->with('comments')
                    ->orderBy('id', 'asc')
                    ->paginate(10, ['*'], 'p', $this->app->request->get('p'));
            } else {
                $comments = collect();
            }

            $this->render('post', [
                'post' => $post,
                'comments' => $comments,
                'elements' => !$comments->isEmpty() ? $this->makeElements($comments) : [],
            ]);

        } catch (NewsException $exception) {
            $this->app->flashNow('errors', [
                $exception->getMessage(),
            ]);

            $this->render('notfound');
        }

    }

    /**
     * @param $id
     */
    public function deleteCommentAction($id)
    {
        $request = $this->app->request;

        try {

            if (is_null($this->user) || ($this->user && $this->user->isSimpleUser())) {
                throw new NewsException('Нет прав');
            }

            $comment = News::with('comments')->find($id);

            if (!$comment) {
                throw new NewsException('news.post_comment_not_found');
            }

            if ($comment->comments) {
                $comment->comments()->delete();//удаляем все красные комменты к текущему комменту
            }

            $comment->delete();//удаляем сам коммент

            $this->app->flash('noty', [
                'type' => 'success',
                'msg' => \Lang::get('news.post_comment_delete_success'),
            ]);

            $this->app->redirect($request->getReferer());

        } catch (NewsException $exception) {
            $this->app->redirectWithError($exception->getMessage());
        }

    }

    /**
     * @param $_view
     * @param null $_data_
     * @param bool $_return
     * @return string
     * @throws \Exception
     */
    public function render($_view, $_data_ = null, $_return = false)
    {
        $this->loadCssAndScripts();

        $this->app->view()->appendLayoutData(array(
            'page_title' => $this->title,
            'page_description' => $this->description
        ));

        return parent::render($_view, $_data_, $_return);
    }

    /**
     *
     */
    protected function loadCssAndScripts()
    {
        $this->app->clientScript
            ->registerCssFile('/assets/bootstrap/css/bootstrap.min.css')
            ->registerCssFile('/assets/iconic/css/open-iconic-bootstrap.min.css')
            ->registerCssFile('/assets/adaptive/css/font-awesome.min.css')
            ->registerCssFile('/assets/adaptive/css/kp-new-style.css')
            ->registerCssFile('/assets/adaptive/css/img.min.css')
            ->registerCssFile('/assets/pill/css/pill.css')
            ->registerCssFile('/assets/noty/lib/noty.css')
            ->registerCssFile('/assets/news/css/news.css');

        $this->app->clientScript
            ->registerJsFile('/assets/jquery/jquery.min.js', ClientScript::JS_POSITION_BEGIN)
            ->registerJsFile('/assets/news/js/news.js', ClientScript::JS_POSITION_BEGIN)
            ->registerJsFile('/js/gatracking/gat.js', ClientScript::JS_POSITION_BEGIN)
            ->registerJsFile('/assets/scrollup/dist/jquery.scrollUp.min.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/noty/lib/noty.min.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/bootstrap/js/bootstrap.bundle.js', ClientScript::JS_POSITION_END);
    }

}