<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 */

namespace components\Component\Slim\Middleware\ClientScript;
use components\Component\VarDumper;

/**
 * Class Helper
 * @package components\Component\Slim\Middleware\ClientScript
 */
class ClientScript
{
    const JS_POSITION_BEGIN = 0;
    const JS_POSITION_END   = 1;

    protected $_cssFiles = array();

    protected $_jsFiles = array(
        self::JS_POSITION_BEGIN => array(),
        self::JS_POSITION_END   => array(),
    );

    protected $defaultSettings = array(
        'host'          => null,
        'https'         => false,
        'imageBaseUrl'  => ''
    );
    protected $settings = array();
    public function __construct($settings = array())
    {
        $this->settings = array_merge($this->defaultSettings, $settings);
    }

    /**
     * @param $file
     * @return $this
     */
    public function registerCssFile($file)
    {
        if(!in_array($file, $this->_cssFiles)) {
            $this->_cssFiles[] = $file;
        }

        return $this;
    }

    /**
     * @param $file
     * @param int $position
     * @return $this
     */
    public function registerJsFile($file, $position = self::JS_POSITION_BEGIN)
    {
        if(!in_array($file, $this->_jsFiles[$position])) {
            $this->_jsFiles[$position][] = $file;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCssFiles()
    {
        return $this->_cssFiles;
    }

    /**
     * @param $position
     * @return mixed
     */
    public function getJsFiles($position)
    {
        return isset($this->_jsFiles[$position]) ? $this->_jsFiles[$position] : array();
    }

    public function image($url)
    {
        return sprintf('%s/%s', $this->settings['imageBaseUrl'], ltrim($url, '/'));
    }
}