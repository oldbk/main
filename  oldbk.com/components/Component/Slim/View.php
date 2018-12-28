<?php
namespace components\Component\Slim;
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 */
class View extends \Slim\View
{
    /** @var string */
    protected $layout;

    /** @var array */
    protected $layoutData = array();

    /** @var null */
    protected $controller_id = null;

    /**
     * @param $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function setLayoutData($data)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Cannot append view data. Expected array argument.');
        }

        $this->layoutData = $data;
    }

    /**
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function appendLayoutData($data)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Cannot append view data. Expected array argument.');
        }

        $this->layoutData = array_merge($this->layoutData, $data);
    }

    public function renderPartial($_view, $_data_ = null)
    {
        $this->setLayout(null);
        return $this->render($_view, $_data_);
    }

    /**
     * @param string $template
     * @param null $data
     * @return string
     */
    public function render($template, $data = null)
    {
        $app = Slim::getInstance();
        $this->appendData(array('app' => $app));
        if ($this->layout !== null) {
            $layout = $this->layout;
            $content = parent::render($this->getViewPath($template), $data);

            $this->appendLayoutData(array(
                'content'   => $content,
                'debugbar'  => $app->container->get('debugbar'),
                'config'    => $app->container->get('config'),
            ));

            return parent::render($this->getLayoutPath($layout), $this->layoutData);
        } else {
            return parent::render($this->getViewPath($template), $data);
        }
    }

    /**
     * @param string $template
     * @param null $data
     * @deprecated
     */
    public function display($template, $data = null)
    {
        echo $this->fetch($template, $data);
    }

    /**
     * @param string $template
     * @param null $data
     * @return string
     * @deprecated
     */
    public function fetch($template, $data = null)
    {
        return $this->render($template, $data);
    }

    /**
     * @return null
     */
    public function getControllerId()
    {
        return $this->controller_id;
    }

    /**
     * @param null $controller_id
     *
     * @return $this
     */
    public function setControllerId($controller_id)
    {
        $this->controller_id = $controller_id;
        return $this;
    }

    private function getViewPath($template)
    {
        $t = sprintf('views/%s/%s.php', $this->getControllerId(), $template);
        if(file_exists($this->getTemplatePathname($t))) {
            return $t;
        }

        return sprintf('views/%s.php', $template);
    }

    private function getLayoutPath($layout)
    {
        return sprintf('layouts/%s.php', $layout);
    }
}