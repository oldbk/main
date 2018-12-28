<?php


namespace components\Component\Elastica\Forum;

use components\Component\Elastica\Base;

/**
 * Class Indexer
 * @package components\Component\Elastica
 */
class Indexer extends Base
{
    public $batchSize = 100;

    public $indexSettings = [
        'analysis' => [
            'analyzer' => [
                'russian' => [
                    'tokenizer' => 'standard',
                    'filter' => [
                        "lowercase",
                        "russian_stop",
                    ]
                ]
            ],
            'filter' => [
                'russian_stop' => [
                    'type' => 'stop',
                    'stopwords' => '_russian_'
                ],
            ]
        ]
    ];

    public $mappingSettings = array(
        'id' => array(
            'type' => 'integer',
        ),
        'text' => array(
            'type' => 'text',
//            'analyzer'=> self::SEARCH_ANALYZER,
            'boost' => 1
        ),
    );

    /**
     * Indexer constructor.
     * @param $indexName
     * @param $typeName
     * @param bool $create
     * @throws \Exception
     */
    public function __construct($indexName, $typeName, $create = false)
    {
        parent::__construct();

        if (!$this->client) {
            throw new \Exception('Server down!');
        }

        $this->index = $this->client->getIndex($indexName);
        if ($create) {
            //$this->index->create($this->indexSettings, true);
            $this->index->create([], true);
        }

        $this->type = $this->index->getType($typeName);

    }

    /**
     * @param $batchSize
     */
    public function initSettings($batchSize)
    {
        $this->batchSize = $batchSize;

        $this->initMapping();
    }

    /**
     * @param $docsOrClosure - if is array we are just adding it but if is closure we are calling it and add docs from result <br>
     *
     * closure = function($offset, $limit)  return $docs
     */
    public function run($docsOrClosure)
    {
        // array
        if (is_array($docsOrClosure)) {
            $this->addDocsFromArray($docsOrClosure);
        } // closure
        else {
            $this->addDocsByPackages($docsOrClosure);
        }
    }

    /**
     *
     */
    public function delete()
    {
        $this->index->delete();
    }

    /**
     *
     */
    public function refresh()
    {
        $this->index->refresh();
    }

    /**
     * @param $closure
     */
    protected function addDocsByPackages($closure)
    {
        $offset = 0;

        while (true) {
            $docs = $closure($offset, $this->batchSize);
            $docsNumber = count($docs);
            $offset += $docsNumber;

            if ($docsNumber < 1) {
                break;
            }

            $this->addDocsFromArray($docs);
        }
    }

    /**
     * @param $array
     */
    protected function addDocsFromArray($array)
    {
        $docs = array();
        $fields = $this->getFields();

        foreach ($array as $item) {
            $data = array();

            foreach ($fields as $name => $field) {
                $data[$name] = $item[$name];
            }
            $docs[] = $this->type->createDocument($data['id'], $data);
        }

        $this->addDocs($docs);
    }

    /**
     * @param $docs
     */
    protected function addDocs($docs)
    {
        $this->type->addDocuments($docs);
    }

    /**
     *
     */
    protected function initMapping()
    {
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($this->type);
        $mapping->setProperties($this->mappingSettings);

        $this->type->setMapping($mapping);
    }

    /**
     * @return array
     */
    protected function getFields()
    {
        $fields = $this->mappingSettings;
        unset($fields['_all']);

        return $fields;
    }
}