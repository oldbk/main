<?php

namespace components\Component\Slim;

/**
 * Slim HTTP Request
 *
 * This class provides a human-friendly interface to the Slim environment variables;
 * environment variables are passed by reference and will be modified directly.
 *
 * @package Slim
 * @author  Josh Lockhart
 * @since   1.0.0
 */
class Request extends \Slim\Http\Request
{

    /**
     * Get IP
     * @return string
     */
    public function getIp()
    {
        $keys = array('X_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR', 'CLIENT_IP', 'HTTP_CLIENT_IP', 'HTTP_CF_CONNECTING_IP', 'REMOTE_ADDR');
        foreach ($keys as $key) {
            if (isset($this->env[$key])) {
                return $this->env[$key];
            }
        }

        return $this->env['REMOTE_ADDR'];
    }

    /**
     * @return array
     */
    public function getIps()
    {
        $ips = [];

        $keys = array('X_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR', 'CLIENT_IP', 'HTTP_CLIENT_IP', 'HTTP_CF_CONNECTING_IP', 'REMOTE_ADDR');
        foreach ($keys as $key) {
            if (isset($this->env[$key])) {
                $ips[] = $this->env[$key];
            }
        }

        return $ips ?: [$this->env['REMOTE_ADDR']];
    }
}
