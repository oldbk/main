<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\Model;

use components\Component\Slim\Slim;
use components\Component\VarDumper;
use database\DB;
use database\Query;
use database\Statement;

abstract class AbstractModel implements iModel
{
    /** @var DB */
    protected $_db;
    /** @var Statement */
    private $stmt;
    /** @var Query */
    private $builder;
    /** @var bool  */
    private $single = true;
    /** @var string */
    protected static $alias = 't';
    private static $_models = array();
    private $vars = array();
    protected $is_new = true;

    abstract protected function fieldMap();

    public function __construct()
    {
        $this->afterConstruct();
    }

    protected function afterConstruct()
    {

    }

    /**
     * @param string $className
     * @return $this
     */
    public static function model($className=__CLASS__)
    {
        /*if(isset(self::$_models[$className]))
            return self::$_models[$className];
        else
        {

        }*/

        $model = self::$_models[$className] = new $className(null);
        return $model;
    }

    /**
     * @param array $attributes
     * @return static
     */
    public static function create(array $attributes)
    {
        $obj = new static();
        $fields = $obj->fieldMap();

        foreach ($attributes as $attribute => $value) {
            if(!in_array($attribute, $fields)) {
                continue;
            }

            $obj->vars[$attribute] = $value;
        }

        return $obj;
    }



    public static function createFromArray(array $attributes)
    {
        $obj = new static();

        foreach ($attributes as $attribute => $value) {
            $obj->vars[$attribute] = $value;
        }

        return $obj;
    }

    public function __get($name)
    {
        if(array_key_exists($name, $this->vars))
            return $this->vars[$name];
        if(property_exists($this, $name))
            return $this->{$name};
        if(in_array($name, $this->fieldMap()))
            return null;

        throw new \RuntimeException(sprintf('Property %s not found __get', $name));
    }

    public function __set($name, $value)
    {
        if(in_array($name, $this->fieldMap()))
            $this->vars[$name] = $value;
        else
            throw new \RuntimeException(sprintf('Property %s not found __set', $name));
    }

    public function populate($attributes)
    {
        $this->vars = $attributes;

        return $this;
    }

    public function clear()
    {

    }

    /**
     * @return DB
     */
    public function db()
    {
        throw new \Exception();
    }

    public function setDb($db)
    {
        $this->_db = $db;

        return $this;
    }

    /**
     * @return Statement
     */
    public function getStmt()
    {
        return $this->stmt;
    }

    /**
     * @param Statement $stmt
     *
     * @return $this
     */
    public function setStmt($stmt)
    {
        $this->stmt = $stmt;
        return $this;
    }

    /**
     * @return Query
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @param Query $builder
     *
     * @return $this
     */
    public function setBuilder($builder)
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSingle()
    {
        return $this->single;
    }

    /**
     * @param boolean $single
     *
     * @return $this
     */
    public function setSingle($single)
    {
        $this->single = $single;
        return $this;
    }

    /**
     * @deprecated
     */
    public function reload()
    {
        if($this->getPk()) {
            $data = static::findByPk($this->getPk())->asArray();
            $this->clear();
            $this->populate($data);
            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @param array $fields
     * @return boolean|static
     */
    public static function findByPk($id, $fields = array('*'))
    {
        if(static::pkField() === false) {
            return false;
        }

        $find = static::model();

        $field_list = static::prepareFields($fields, static::$alias);
        $find->builder = $find->db()->select($field_list)
            ->from(static::tableName(). ' ' . '`t`')
            ->where(sprintf('%s.%s = ?', static::$alias, static::pkField()), $id)
            ->limit(1);

        return $find;
    }

    /**
     * @param array $condition
     * @param array $params
     * @param array $fields
     * @return static
     */
    public static function find($condition = null, $params = array(), $fields = array('*'))
    {
        $find = static::model();

        $field_list = $find->prepareFields($fields, static::$alias);
        $find->builder = $find->db()
            ->select($field_list)
            ->from(static::tableName(). ' ' . static::$alias)
            ->limit(1);
        if(!is_array($condition)) {
            $condition = array('condition' => $condition);
        }
        if(isset($condition['condition'])) {
            $find->builder->where($condition['condition'], $params);
        }
        if(isset($condition['order'])) {
            $find->builder->orderBy($condition['order']);
        }

        return $find;
    }

    /**
     * @param array $condition
     * @param array $params
     * @return int
     */
    public static function count($condition = null, $params = array())
    {
        $find = static::model();
        $where = null;

        if(!is_array($condition)) {
            $condition = array('condition' => array($condition));
        }
        if(isset($condition['condition'])) {
            $where = $condition['condition'];
        }

        return $find->db()->count(static::tableName(), $where, $params);
    }

    /**
     * @param null $condition
     * @param array $params
     * @param array $fields
     * @return static
     */
    public static function findAll($condition = null, $params = array(), $fields = array('*'))
    {
        $find = static::model();

        $field_list = $find->prepareFields($fields, static::$alias);
        $find->builder = $find->db()
            ->select($field_list)
            ->from(static::tableName(). ' ' . static::$alias);
        if(!is_array($condition)) {
            $condition = array('condition' => $condition);
        }
        if(isset($condition['condition'])) {
            $find->builder->where($condition['condition'], $params);
        }
        if(isset($condition['limit'])) {
            $offset = isset($condition['offset']) ? $condition['offset'] : 0;
            $find->builder->limit($condition['limit'], $offset);
        }
        if(isset($condition['order'])) {
            $find->builder->orderBy($condition['order']);
        }

        return $find->setSingle(false);
    }

    public static function update($update = array(), $condition, $params = array())
    {
        $find = static::model();
        $where = null;

        if(!is_array($condition)) {
            $condition = array('condition' => array($condition));
        }
        if(isset($condition['condition'])) {
            $where = $condition['condition'];
        }

        return $find->db()->update(static::tableName(), $update, $where, $params);
    }

    public static function delete($condition, $params = array())
    {
        $find = static::model();
        $where = null;

        if(!is_array($condition)) {
            $condition = array('condition' => array($condition));
        }

        if(isset($condition['limit'])) {
            $offset = isset($condition['offset']) ? $condition['offset'] : 0;
            $find->builder->limit($condition['limit'], $offset);
        }

        if(isset($condition['condition'])) {
            $where = $condition['condition'];
        }

        return $find->db()->delete(static::tableName(), $where, $params);
    }

    /**
     * @param $data
     * @return int|false
     */
    public static function insert($data)
    {
        $find = static::model();
        $r = $find->db()->insert(static::tableName(), $data);
        if($r && $find->getPk() === false) {
            return true;
        }

        return $r ? $find->db()->lastInsertId() : false;
    }

    public function save()
    {
        $pkField = static::pkField();
        if($pkField === false) {
            return false;
        }

        $data = $this->vars;
        if (!empty($data[$pkField])) {
            unset($data[$pkField]);

            return static::update($data,  $pkField . " = ?", array($this->vars[$pkField]));
        } else {
            return static::insert($data);
        }
    }

    public function asArray()
    {
        if($this->_cache === true && ($_data = $this->getFromCache()) !== null) {
            return $_data;
        }

        $stmt = $this->builder->execute();
        if($this->single)
            $_data =  $stmt->fetch(\PDO::FETCH_ASSOC);
        else
            $_data =  $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($this->_cache === true) {
            $this->writeToCache($_data);
        }

        return $_data;
    }

    public function asModel()
    {
        $return = null;

        if(!$data = $this->asArray())
            return $this->single == true ? null : array();

        if(!$this->single) {
            foreach ($data as $_data) {
                $model = new static();
                $model->populate($_data);
                $model->is_new = false;
                $return[] = $model;
            }
        } else {
            $return = new static();
            $return->populate($data);
            $return->is_new = false;
        }
        unset($data);

        return $return;
    }

    private $_cache = false;
    private $_cache_time = null;
    public function cache($duration = 0)
    {
        $this->_cache = true;
        $this->_cache_time = $duration;

        return $this;
    }

    protected function getFromCache()
    {
        $key = $this->getSql();
        return Slim::getInstance()->cache->get($key);
    }

    protected function writeToCache($_data)
    {
        $key = $this->getSql();
        return Slim::getInstance()->cache->set($key, $_data, $this->_cache_time);
    }

    protected function getSql()
    {
        $string = $this->builder->getQuery();
        $data = $this->builder->getParams();

        $indexed = $data == array_values($data);

        foreach($data as $k => $v) {
            if(is_string($v)) {
                $v = "'$v'";
            }
            if($indexed) {
                $string = preg_replace('/\?/', $v, $string, 1);
            }
            else $string = str_replace(":$k", $v, $string);
        }
        return $string;
    }

    protected static function prepareFields($fields, $alias)
    {
        if(!is_array($fields)) {
            $fields = array($fields);
        }

        $field_string = '';
        foreach ($fields as $key => $field) {
            if(is_numeric($key)) {
                $field_string .= sprintf('`%s`.%s,', $alias, $field);
            } else {
                $field_string .= sprintf('`%s`.%s as %s,', $alias, $key, $field);
            }
        }

        return trim($field_string, ',');
    }

    public static function getIN(array $params)
    {
        return implode(',', array_fill(0, count($params), '?'));
    }
}