<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.11.2015
 */

namespace components\Component\Slim;


class Router extends \Slim\Router
{
    /**
     * Get URL for named route
     * @param  string               $name   The name of the route
     * @param  array                $params Associative array of URL parameter names and replacement values
     * @throws \RuntimeException            If named route not found
     * @return string                       The URL for the given route populated with provided replacement values
     */
    public function urlFor($name, $params = array())
    {
        if (!$this->hasNamedRoute($name)) {
            throw new \RuntimeException('Named route not found for name: ' . $name);
        }
        $query = array();
        $pattern = $this->getNamedRoute($name)->getPattern();
        foreach ($params as $key => $value) {
            $regex = '#:' . preg_quote($key, '#') . '\+?(?!\w)#';
            if(preg_match($regex, $this->getNamedRoute($name)->getPattern())) {
                $pattern = preg_replace($regex, $value, $pattern);
            } else {
                $query[$key] = $value;
            }
        }
        
        if(!empty($query)) {
            $pattern .= strpos($pattern, '?') !== false ? '&' : '?';
            $pattern .= http_build_query($query);
        }

        //Remove remnants of unpopulated, trailing optional pattern segments, escaped special characters
        return rtrim(preg_replace('#\(/?:.+\)|\(|\)|\\\\#', '', $pattern), '/');
    }
}