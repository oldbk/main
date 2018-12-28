<?php
namespace components\Repositories;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use components\Exceptions\RepositoryException;

/**
 * Class BaseRepository
 * @package components\Repositories
 */
abstract class BaseRepository
{

    public $model;

    /**
     * BaseRepository constructor.
     * @param bool $boot
     */
    public function __construct($boot = false)
    {
        $this->makeModel();
        $this->boot();
    }

    /**
     * @return mixed
     */
    abstract public function model();

    /**
     * @return Model|mixed
     * @throws RepositoryException
     */
    public function makeModel()
    {
        $model = Container::getInstance()->make($this->model());

        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * @throws RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     *
     */
    public function boot()
    {

    }
}