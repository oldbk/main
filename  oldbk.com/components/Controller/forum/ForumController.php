<?php

namespace components\Controller\forum;


use components\Component\Slim\Middleware\ClientScript\ClientScript;
use components\Controller\_base\BaseController;
use components\Eloquent\Forum;
use components\Eloquent\User;
use components\Exceptions\ForumException;
use components\Helper\AuthorInfo;
use components\Helper\Str;
use components\Traits\PaginatorTrait;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

/**
 * Class ForumController
 * @package components\Controller
 */
class ForumController extends BaseController
{

    use PaginatorTrait;

    /**
     * @var string
     */
    protected $layout = 'forum';

    /**
     * @var string
     */
    protected $title = 'Форум браузерной онлайн игры Бойцовский Клуб ОЛДБК';

    /**
     * @var string
     */
    protected $description = 'Форум лучшей бесплатной браузерной онлайн игры Старый Бойцовский Клуб (БК) ОЛДБК. Online общение игроков браузерной RPG.';

    /**
     * @var User $user
     */
    protected $user = null;

    /**
     * @var bool
     */
    protected $cache = false;


    /**
     * ForumController constructor.
     * @param \components\Component\Slim\Slim $app
     * @param $action
     * @param null $actionParams
     */
    public function __construct($app, $action, $actionParams = null)
    {

        if($app->container->mode === 'local') {
//            $app->session->set('uid', 8540);
//            $app->session->set('uid', 368518);
//            $app->session->set('uid', 131154);
//            $app->session::destroy();
        }

        $this->user = \Auth::user();

        $app->view()->appendData([
            'user' => $this->user,
        ]);

        parent::__construct($app, $action, $actionParams);
    }

    /**
     * @param null $conf
     * @return \Illuminate\Database\Eloquent\Collection|Collection|static[]
     */
    protected function getCategories($conf = null)
    {

        $query = Forum::whereType(1);

        if (is_null($this->user)) {
            $query->whereIn('id', [1, 49, 2, 3, 4, 5, 42, 13, 16, 18, 19]);
            $query->where('is_closed', 0);
        } else {

            if (!$this->user->isAdmin() && !$this->user->isAdminion() && !$this->user->isHighPaladin()) {
                $query->where('is_closed', 0);

                if(!$this->user->isTester()) {
					$query->where('is_test', 0);
				}

                if ($this->user['level'] > 7 && !$this->user->isPaladin()) {
                    $query->where('id', '!=', 48);
                }
            }

        }

        /**
         * @var Collection $forum_category
         */
        $forum_category = $query->orderBy('fix', 'asc')->get();

        return is_null($this->user)
            ? $forum_category
            : $forum_category->filter(function (Forum $category) {return $category->hasAccess($this->user);});

    }

    /**
     * @param bool $text
     * @return bool
     */
    protected function spamDetect($text = false)
    {
        $time_flag = false;

        if ($this->app->getCookie('lastMessageTime')) {
            $time_flag = true;

            $this->setLastMessageTime($text);//продлеваем куку на 3 сек.

            if ($text !== false && $this->app->getCookie('lastMessageHash') === md5($text)) {
                $this->app->setCookie('ip_ban', $this->app->request->getIp(), time() + 3600);
            }

        }

        return $time_flag;
    }

    /**
     * Set cookie for last message
     * @param bool $text
     */
    protected function setLastMessageTime($text = false)
    {
        $this->app->setCookie('lastMessageTime', time(), time() + 3);

        if ($text !== false) {
            $this->app->setCookie('lastMessageHash', md5($text), time() + 3);
        }
    }

    /**
     * @param $action
     * @return bool
     */
    protected function beforeAction($action)
    {
        $this->checkUserBlock();
        $this->paginatorResolver();

        return parent::beforeAction($action);
    }

    /**
     * @return bool
     */
    protected function checkUserBlock()
    {
        if ($this->user) {
            if ($this->user->block) {
                $this->app->session->destroy();
                $this->app->redirectTo('forum');
            }
        }

        return true;
    }

    /**
     *
     */
    protected function registerCssAndScripts()
    {
        $this->app->clientScript
            ->registerCssFile('/assets/bootstrap/css/bootstrap.min.css')
            ->registerCssFile('/assets/iconic/css/open-iconic-bootstrap.min.css')
            ->registerCssFile('/assets/forum/css/forum.css')
            ->registerCssFile('/assets/noty/lib/noty.css')
            ->registerCssFile('/assets/animate/animate.min.css')
            ->registerCssFile('/assets/bootstrap-datepicker/dist/css/bootstrap-datepicker.standalone.min.css')
            ->registerCssFile('/assets/forum/css/pill.css');


        $this->app->clientScript
            ->registerJsFile('/assets/jquery/jquery.min.js', ClientScript::JS_POSITION_BEGIN)
            ->registerJsFile('/js/gatracking/gat.js', ClientScript::JS_POSITION_BEGIN)
            ->registerJsFile('/assets/bootstrap/js/bootstrap.bundle.min.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/scrollup/dist/jquery.scrollUp.min.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/noty/lib/noty.min.js', ClientScript::JS_POSITION_END)
            ->registerJsFile('/assets/forum/js/forum.js', ClientScript::JS_POSITION_END);

        if (
            $this->user &&
            (
                $this->user->isAdmin() ||
                $this->user->isPaladin() ||
                $this->user->canCommentWrite() ||
                $this->user->canEditPost()
            )
        ) {
            $this->app->clientScript->registerJsFile('/assets/forum/js/forum_moderator.js', ClientScript::JS_POSITION_END);
        }
    }

    /**
     * @throws \Exception
     */
    protected function createToken()
    {
        $_token = $this->app->session->get('_token');

        if(!$_token) {
            $_token = $this->generateToken();
            $this->app->session->set('_token', $_token);
        }

        $this->app->view()->appendData([
            '_token' => $_token,
        ]);
    }

    /**
     * @throws ForumException
     */
    protected function checkToken()
    {

        if (in_array($this->app->request->getMethod(), array('POST', 'PUT', 'DELETE'))) {
            $userToken = $this->app->request->post('_token');
            $_token = $this->app->session->get('_token', false);


            if ($_token !== $userToken) {
                throw new ForumException('Token mismatch');
            }

            $this->app->session->delete('_token');
        }
    }

    /**
     * @return string
     */
    protected function generateToken()
    {
        return Str::random();
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
        $this->registerCssAndScripts();

        $this->app->view()->appendLayoutData(array(
            'page_title' => $this->title,
            'page_description' => $this->description
        ));

        return parent::render($_view, $_data_, $_return);
    }
}