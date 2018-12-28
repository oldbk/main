<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 */

namespace components\Component\Slim\Middleware\Session;

use Countable;
use ArrayAccess;
use ArrayIterator;
use JsonSerializable;
use IteratorAggregate;

/**
 * Class Helper
 * @package components\Component\Slim\Middleware\Session
 */
class Helper implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{

    const DEFAULT_SESSION_NAME = 'def_name';

    /**
     * Set a given key / value pair or pairs
     * if the key doesn't exist already
     *
     * @param array|int|string $keys
     * @param mixed            $value
     */
    public function add($keys, $value = null)
    {
        if (is_array($keys)) {
            foreach ($keys as $key => $value) {
                $this->add($key, $value);
            }
        } elseif (is_null($this->get($keys))) {
            $this->set($keys, $value);
        }
    }

    /**
     * Return all the stored items
     *
     * @return array
     */
    public function all()
    {
        return $_SESSION;
    }

    /**
     * Delete the contents of a given key or keys
     *
     * @param array|int|string|null $keys
     */
    public function clear($keys = null)
    {
        if (is_null($keys)) {
            $_SESSION = [];

            return;
        }

        $keys = (array) $keys;

        foreach ($keys as $key) {
            $this->set($key, []);
        }
    }

    /**
     * Delete the given key or keys
     *
     * @param array|int|string $keys
     */
    public function delete($keys)
    {
        $keys = (array) $keys;

        foreach ($keys as $key) {
            if ($this->exists($_SESSION, $key)) {
                unset($_SESSION[$key]);

                continue;
            }

            $items = &$_SESSION;
            $segments = explode('.', $key);
            $lastSegment = array_pop($segments);

            foreach ($segments as $segment) {
                if (!isset($items[$segment]) || !is_array($items[$segment])) {
                    continue 2;
                }

                $items = &$items[$segment];
            }

            unset($items[$lastSegment]);
        }
    }

    /**
     * Checks if the given key exists in the provided array.
     *
     * @param  array      $array Array to validate
     * @param  int|string $key   The key to look for
     *
     * @return bool
     */
    protected function exists($array, $key)
    {
        return array_key_exists($key, $array);
    }

    /**
     * Return the value of a given key
     *
     * @param  int|string|null $key
     * @param  mixed           $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->all();
        }

        if ($this->exists($_SESSION, $key)) {
            return $_SESSION[$key];
        }

        if (strpos($key, '.') === false) {
            return $default;
        }

        $items = $_SESSION;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($items) || !$this->exists($items, $segment)) {
                return $default;
            }

            $items = &$items[$segment];
        }

        return $items;
    }

    /**
     * Return the given items as an array
     *
     * @param  mixed $items
     * @return array
     */
    protected function getArrayItems($items)
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof self) {
            return $items->all();
        }

        return (array) $items;
    }

    /**
     * Check if a given key or keys exists
     *
     * @param  array|int|string $keys
     * @return bool
     */
    public function has($keys)
    {
        $keys = (array) $keys;

        if (!$_SESSION || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $items = $_SESSION;

            if ($this->exists($items, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (!is_array($items) || !$this->exists($items, $segment)) {
                    return false;
                }

                $items = $items[$segment];
            }
        }

        return true;
    }

    /**
     * Check if a given key or keys are empty
     *
     * @param  array|int|string|null $keys
     * @return bool
     */
    public function isEmpty($keys = null)
    {
        if (is_null($keys)) {
            return empty($_SESSION);
        }

        $keys = (array) $keys;

        foreach ($keys as $key) {
            if (!empty($this->get($key))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the value of a given key and
     * delete the key
     *
     * @param  int|string|null $key
     * @param  mixed           $default
     * @return mixed
     */
    public function pull($key = null, $default = null)
    {
        if (is_null($key)) {
            $value = $this->all();
            $this->clear();

            return $value;
        }

        $value = $this->get($key, $default);
        $this->delete($key);

        return $value;
    }

    /**
     * Set a given key / value pair or pairs
     *
     * @param array|int|string $keys
     * @param mixed            $value
     */
    public function set($keys, $value = null)
    {
        if (is_array($keys)) {
            foreach ($keys as $key => $value) {
                $this->set($key, $value);
            }

            return;
        }

        $items = &$_SESSION;

        foreach (explode('.', $keys) as $key) {
            if (!isset($items[$key]) || !is_array($items[$key])) {
                $items[$key] = [];
            }

            $items = &$items[$key];
        }

        $items = $value;
    }

    /**
     * Replace all items with a given array
     *
     * @param mixed $items
     */
    public function setArray($items)
    {
        $_SESSION = $this->getArrayItems($items);
    }

    /**
     * Replace all items with a given array as a reference
     *
     * @param array $items
     */
    public function setReference(array &$items)
    {
        $_SESSION = &$items;
    }

    /**
     * Return the value of a given key or all the values as JSON
     *
     * @param  mixed  $key
     * @param  int    $options
     * @return string
     */
    public function toJson($key = null, $options = 0)
    {
        if (is_string($key)) {
            return json_encode($this->get($key), $options);
        }

        $options = $key === null ? 0 : $key;

        return json_encode($_SESSION, $options);
    }


    /**
     * Get a session variable.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    /*public function get($key, $default = null)
    {
        return $this->exists($key)
            ? $_SESSION[$key]
            : $default;
    }*/
    /**
     * Set a session variable.
     *
     * @param string $key
     * @param mixed  $value
     */
    /*public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }*/
    /**
     * Delete a session variable.
     *
     * @param string $key
     */
    /*public function delete($key)
    {
        if ($this->exists($key)) {
            unset($_SESSION[$key]);
        }
    }*/
    /**
     * Clear all session variables.
     */
    /*public function clear()
    {
        $_SESSION = array();
    }*/
    /**
     * Check if a session variable is set.
     *
     * @param string $key
     *
     * @return bool
     */
    /*protected function exists($key)
    {
        return array_key_exists($key, $_SESSION);
    }*/
    /**
     * Get or regenerate current session ID.
     *
     * @param bool $new
     *
     * @return string
     */
    public static function id($new = false)
    {
        if ($new && session_id()) {
            session_regenerate_id(true);
        }
        return session_id() ?: '';
    }

    /**
     * Destroy the session.
     */
    public static function destroy()
    {
        if (self::id()) {
            session_unset();
            session_destroy();
            session_write_close();
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 4200,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
        }
    }
    /**
     * Magic method for get.
     *
     * @param string $key
     *
     * @return mixed
     */
    /*public function __get($key)
    {
        return $this->get($key);
    }*/
    /**
     * Magic method for set.
     *
     * @param string $key
     * @param mixed  $value
     */
    /*public function __set($key, $value)
    {
        $this->set($key, $value);
    }*/
    /**
     * Magic method for delete.
     *
     * @param string $key
     */
    /*public function __unset($key)
    {
        $this->delete($key);
    }*/
    /**
     * Magic method for exists.
     *
     * @param string $key
     *
     * @return bool
     */
    /*public function __isset($key)
    {
//        return $this->exists($key);
        return $this->has($key);
    }*/

    /*
     * --------------------------------------------------------------
     * ArrayAccess interface
     * --------------------------------------------------------------
     */

    /**
     * Check if a given key exists
     *
     * @param  int|string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Return the value of a given key
     *
     * @param  int|string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a given value to the given key
     *
     * @param int|string|null $key
     * @param mixed           $value
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $_SESSION[self::DEFAULT_SESSION_NAME] = $value;

            return;
        }

        $this->set($key, $value);
    }

    /**
     * Delete the given key
     *
     * @param int|string $key
     */
    public function offsetUnset($key)
    {
        $this->delete($key);
    }

    /*
     * --------------------------------------------------------------
     * Countable interface
     * --------------------------------------------------------------
     */

    /**
     * Return the number of items in a given key
     *
     * @param  int|string|null $key
     * @return int
     */
    public function count($key = null)
    {
        return count($this->get($key));
    }

    /*
     * --------------------------------------------------------------
     * IteratorAggregate interface
     * --------------------------------------------------------------
     */

    /**
     * Get an iterator for the stored items
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($_SESSION);
    }

    /*
     * --------------------------------------------------------------
     * JsonSerializable interface
     * --------------------------------------------------------------
     */

    /**
     * Return items for JSON serialization
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $_SESSION;
    }
}