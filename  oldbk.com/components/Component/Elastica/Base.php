<?php


namespace components\Component\Elastica;

/**
 * Class Base
 * @package components\Component\Elastica
 */
class Base
{
//    const INDEX_ANALYZER = 'indexAnalyzer';
//    const SEARCH_STRICT_ANALYZER = 'strictSearchAnalyzer';
    const SEARCH_ANALYZER = 'russian';

    protected $client;
    protected $index;
    protected $type;

    protected $indexName;
    protected $typeName;

    /**
     * Base constructor.
     */
    public function __construct()
    {
        return $this->initServer();
    }

    /**
     * @param null $name
     * @return bool
     */
    public function initServer() {

        try {
            $client = new \Elastica\Client($this->getElasticConnection());
            $client->getStatus()->getResponse();

            $this->client = $client;

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getElasticConnection()
    {
        return \Config::get('elastica.config');
    }

    /**
     * @return mixed
     */
    public function getElasticSearchClient()
    {
        return $this->client;
    }

    /**
     * @return mixed
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @param $indexName
     */
    public function setIndexName($indexName)
    {
        $this->indexName = $indexName;
    }

    /**
     * @return mixed
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * @param $typeName
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;
    }


}