<?php
namespace components\Controller\_base;
use components\Component\Slim\Slim;
use components\Exception\CombatsException;
use components\Helper\Json;
/**
 * Created by PhpStorm.
 */

abstract class BaseController
{
    /** @var string */
    protected $layout = 'main';
    /** @var Slim */
    protected $app;
    protected $actionParams;

    protected $htmlCache = null;
    protected $cache = true;
    protected $http_fix_enable = false;
    public $actionId;

    public function __construct(Slim $container, $action, $actionParams = null)
    {
        $this->app = $container;

        $this->actionParams = $actionParams;

        if ($this->app->getMode() === 'local') {
            $this->cache = false;
        }

        $this->run($action);
    }

    /**
     * @param $service_name
     * @return mixed
     */
    protected function get($service_name)
    {
        return $this->app->container->get($service_name);
    }

    protected function run($action)
    {
        $actionMethod = method_exists($this, $action.'Action') ? $action.'Action' : 'indexAction';
        if(!method_exists($this, $actionMethod))
            throw new \Exception(sprintf('Action not found. Action: %s', $actionMethod));

        $this->actionId = str_replace('Action', '' , $actionMethod);
        if($this->cache) {
            $this->htmlCache = $this->app->cache->get($this->getKeyCache());
        }

        if($this->beforeAction($action)) {
            if($this->cache === false || $this->htmlCache === null) {
                $response = $this->{$actionMethod}($this->actionParams);
                if($response) {
                    echo $response;
                }
            } else {
                echo $this->htmlCache;
            }

            $this->afterAction($action);
        }
    }

    protected function beforeAction($action)
    {
        if($this->app->config('gzip')) {
            $this->startGzip();
        }

        return true;
    }

    protected function afterAction($action)
    {
        if($this->app->config('gzip')) {
            $this->endGzip();
        }
        if($this->http_fix_enable === true) {
        	$this->httpfix();
		}
    }

    public function renderJSON(array $data)
    {
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->write(Json::encode($data));

//        echo Json::encode($data);
//        exit;
    }

    /**
     * @param $_view
     * @param null $_data_
     * @param boolean $_return
     * @return string
     * @throws \Exception
     */
    public function render($_view, $_data_ = null, $_return = false)
    {
        $html = $this->app->view()
            ->setControllerId($this->getControllerId())
            ->setLayout($this->layout)
            ->render($_view, $_data_);

        if($_return) {
            return $html;
        } else
            echo $html;

        return null;
    }

    /**
     * @param $_view
     * @param $_data_
     * @param bool|false $_return
     * @return string
     * @throws \Exception
     */
    public function renderPartial($_view, $_data_ = null, $_return = false)
    {
        $this->app->view()->setControllerId($this->getControllerId());
        $html = $this->app->view()->renderPartial($_view, $_data_);
        if($_return) {
            return $html;
        } else
            echo $html;

        return null;
    }

    protected function redirect($link, $code = 301)
    {
        $this->app->redirect($link, $code);
    }

    public function errorRouteRedirect($route, $params, $status = 302)
    {
        $this->app->redirectTo($route, $params, $status);
    }

    protected function getControllerId()
    {
        $ref = (new \ReflectionClass($this));

        return str_replace('controller', '', strtolower($ref->getShortName()));
    }

    protected function startGzip()
    {
        if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
            $miniBB_gzipper_encoding = 'x-gzip';
        }
        if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            $miniBB_gzipper_encoding = 'gzip';
        }
        if (isset($miniBB_gzipper_encoding)) {
            ob_start();
        }
    }

    protected function endGzip()
    {
        if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
            $miniBB_gzipper_encoding = 'x-gzip';
        }
        if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            $miniBB_gzipper_encoding = 'gzip';
        }

        if (isset($miniBB_gzipper_encoding)) {
            $miniBB_gzipper_in = ob_get_contents();
            $miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
            $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
            $miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
            $miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
            $gzpercent = $miniBB_gzipper_in_strlen ? ($miniBB_gzipper_lenn / $miniBB_gzipper_in_strlen) * 100 : 0;
            $percent = round($gzpercent);
            $miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Compression: '.$percent.'%<hr>', $miniBB_gzipper_in);
            $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
            ob_clean();
            header('Content-Encoding: '.$miniBB_gzipper_encoding);

            echo $miniBB_gzipper_out;
        }
    }

	protected function httpfix()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https") {
			$out = ob_get_contents();
			ob_end_clean();
			$out = str_replace('http://', 'https://', $out);
			$out = str_replace('https://capitalcity.oldbk.com', 'http://capitalcity.oldbk.com', $out);
			$out = str_replace('https://plug.oldbk.com', 'http://plug.oldbk.com', $out);
			$out = str_replace('https://paladins.oldbk.com', 'http://paladins.oldbk.com', $out);
			echo $out;
		}
	}

    protected function getKeyCache()
    {
        return sprintf('html_%s_%s', $this->getControllerId(), $this->actionId);
    }
}