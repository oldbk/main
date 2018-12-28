<?php


namespace components\Component\Elastica\Forum;

use components\Component\Elastica\Base;

/**
 * Class Worker
 * @package components\Component\Elastica
 */
class Worker extends  Base
{

    /**
     * Worker constructor.
     * @param $indexName
     * @param $typeName
     * @throws \Exception
     */
    public function __construct($indexName, $typeName)
    {
        parent::__construct();

        if (!$this->client) {
            return false;
        }

        $this->index = $this->client->getIndex($indexName);
        $this->type = $this->index->getType($typeName);
    }

    /**
     * @param $array
     * @return bool
     */
    public function add($array)
    {
        if (!$this->client) {
            return false;
        }
        $this->type->addDocument($this->type->createDocument($array['id'], $array));
    }

    /**
     * @param $ids
     * @return bool
     */
    public function delete($ids)
    {
        if (!$this->client) {
            return false;
        }
        $this->type->deleteIds($ids);
    }

    /**
     * @param $array
     * @return bool
     */
    public function update($array)
    {
        if (!$this->client) {
            return false;
        }
        $this->type->updateDocument($this->type->createDocument($array['id'], $array));

    }

}