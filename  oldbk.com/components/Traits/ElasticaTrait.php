<?php

namespace components\Traits;

use components\Component\Elastica\Forum\Search;
use components\Component\Elastica\Forum\Worker;

/**
 * Trait ElasticaTrait
 * @package components\Traits
 */
trait ElasticaTrait
{
    /**
     * @return mixed
     */
    public function getTypeName()
    {
        return $this->getTable();
    }

    /**
     * @return mixed
     */
    public function getIndexName()
    {
        return \Config::get('elastica.default_index', 'oldbk');
    }

    /**
     * @param $fields
     * @return bool
     */
    public function addToIndex($fields)
    {
        $worker = $this->getWorker();
        if (!$worker) {
            return false;
        }
        $worker->add($this->getIndexDocumentData($fields));
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function deleteFromIndex($ids = [])
    {
        $worker = $this->getWorker();
        if (!$worker) {
            return false;
        }
        $worker->delete($ids ?: [$this->getKey()]);
    }

    /**
     * @param $fields
     * @return bool
     */
    public function updateIndex($fields)
    {
        $worker = $this->getWorker();
        if (!$worker) {
            return false;
        }
        $worker->update($this->getIndexDocumentData($fields));
    }

    /**
     * @param $fields
     * @return array
     */
    public function getIndexDocumentData($fields)
    {
        $data = [];
        $data['id'] = $this->getKey();

        foreach ($fields as $field) {
            $data[$field] = iconv('windows-1251', 'utf-8', $this[$field]);
        }

        return $data;
    }

    /**
     * @param string $term
     * @param bool $strict
     * @param int $limit
     * @return mixed
     */
    public static function elasticSearch($term = '', $strict = false, $limit = 1000)
    {
        $model = new static;

        $el = new Search($model->getIndexName(), $model->getTypeName());
        $el->initSettings(iconv('windows-1251', 'utf-8', $term), $strict, 0, $limit)->run();

        return $el->getResult();
    }

    /**
     * @return bool|Worker
     */
    public function getWorker()
    {
        return new Worker($this->getIndexName(), $this->getTypeName());
    }

}