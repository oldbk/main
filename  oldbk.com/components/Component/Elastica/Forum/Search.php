<?php


namespace components\Component\Elastica\Forum;

use components\Component\Elastica\Base;

/**
 * Class Search
 * @package components\Component\Elastica
 */
class Search extends Base
{
    public $offset;
    public $limit;
    public $strict;
    public $result;
    public $phrase;
    public $phraseInOtherLang;
    public $docsNumber;
    public $docsNumberInOtherLang;

    public $suggestSmoothing = 0.4;
    public $highlightPrefix = '<b class="text-danger">';
    public $highlightPostfix = '</b>';
    /** fields for search */
    public $fields = array(
        'text' => array(
            'boost' => 1
        ),
    );
    public $highlightSettings = array(
        'fields' => array(
            'text' => array(
                'fragment_size' => 0,
                'number_of_fragments' => 0,
            ),
        ),
    );

    /**
     * Search constructor.
     * @param $indexName
     * @param $typeName
     * @throws \Exception
     */
    public function __construct($indexName, $typeName)
    {
        parent::__construct();

        if (!$this->client) {
            throw new \Exception('Server down!');
        }

        $this->index = $this->client->getIndex($indexName);
        $this->type = $this->index->getType($typeName);

        $this->highlightSettings['pre_tags'] = array($this->highlightPrefix);
        $this->highlightSettings['post_tags'] = array($this->highlightPostfix);
    }

    /**
     * @param $phrase
     * @param $strict
     * @param $offset
     * @param null $limit
     * @return $this
     */
    function initSettings($phrase, $strict, $offset, $limit = null)
    {
        $this->strict = $strict;
        $this->offset = $offset;
        $this->limit = $limit;

        $this->setPhrase($phrase);

        return $this;
    }

    /**
     * @param $phrase
     */
    protected function setPhrase($phrase)
    {
        $phrase = str_replace('/', '-', $phrase);

        $this->phrase = \Xss::clean($phrase);
    }

    /**
     *
     */
    public function run()
    {
        // to count
        $this->docsNumber = $this->count($this->phrase);

        // search docs
        $this->result = $this->search($this->phrase);
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param $phrase
     * @return \Elastica\Query
     */
    protected function addActualQuery($phrase)
    {
        return $this->strict
            ? $this->addQuery($this->addMatchPhrase($phrase))
            : $this->addQuery($this->addQueryString($phrase));
    }

    /**
     * @param $subQuery
     * @param bool $highlight
     * @return \Elastica\Query
     */
    protected function addQuery($subQuery, $highlight = true)
    {
        $query = new \Elastica\Query();
        $query->setQuery($subQuery);

        if ($highlight) {
            $query->setHighlight($this->highlightSettings);
        }

        return $query;
    }

    /**
     * @param $phrase
     * @return mixed
     */
    protected function count($phrase)
    {
        return $this->_count($this->addActualQuery($phrase));
    }

    /**
     * @param $phrase
     * @return mixed
     */
    protected function search($phrase)
    {
        return $this->_search($this->addActualQuery($phrase), $this->getSearchOptions());
    }

    /**
     * @return array
     */
    protected function getSearchOptions()
    {
        $options = [
            'from' => $this->offset
        ];

        if ($this->limit !== null) {
            $options['size'] = $this->limit;
        }

        return $options;
    }

    /**
     *
     */
    public function runSuggest()
    {
        $suggestPhrases = array();

        foreach ($this->fields as $name => $value) {
            $suggestPhrases[] = $this->addSuggestPhrase($name, $this->phrase);
        }

        $suggest = $this->addSuggest($suggestPhrases);
        $result = $this->_search($suggest, $this->getSearchOptions());
        $this->result = $result->getSuggests();
    }

    /**
     * @param $fieldName
     * @param $phrase
     * @return \Elastica\Suggest\Phrase
     */
    protected function addSuggestPhrase($fieldName, $phrase)
    {
        $suggestPhrase = new \Elastica\Suggest\Phrase($this->getSuggestName($fieldName), $fieldName);

        $suggestPhrase->setText($phrase)
            ->setAnalyzer($this->getAnalyzer())
            ->setHighlight($this->highlightPrefix, $this->highlightPostfix)
            ->setStupidBackoffSmoothing($this->suggestSmoothing)
            ->addCandidateGenerator(new \Elastica\Suggest\CandidateGenerator\DirectGenerator($fieldName));

        return $suggestPhrase;
    }

    /**
     * @param $suggestPhrases
     * @return \Elastica\Suggest
     */
    protected function addSuggest($suggestPhrases)
    {
        $suggest = new \Elastica\Suggest();

        foreach ($suggestPhrases as $suggestPhrase) {
            $suggest->addSuggestion($suggestPhrase);
        }

        return $suggest;
    }

    /**
     * @param $fieldName
     * @return string
     */
    protected function getSuggestName($fieldName)
    {
        return 'suggest' . ucfirst($fieldName);
    }

    /**
     * @param $phrase
     * @return \Elastica\Query\QueryString
     */
    protected function addQueryString($phrase)
    {
        $query = new \Elastica\Query\QueryString($phrase);

        $query/*->setAnalyzer($this->getAnalyzer())*/->setFields($this->getFieldsWithBoost());

        return $query;
    }

    /**
     * @param $phrase
     * @return \Elastica\Query\BoolQuery
     */
    protected function addMatchPhrase($phrase)
    {
        $queries = array();

        foreach ($this->fields as $name => $value) {
            $queries[] = $this->addMatch($name, $phrase);
        }

        return $this->addBool($queries);
    }

    /**
     * @param $name
     * @param $phrase
     * @return \Elastica\Query\MatchPhrase
     */
    protected function addMatch($name, $phrase)
    {
        $query = new \Elastica\Query\MatchPhrase();
        $query->setFieldQuery($name, $phrase);

        return $query;
    }

    /**
     * @param $queries
     * @return \Elastica\Query\BoolQuery
     */
    protected function addBool($queries)
    {
        $boolQuery = new \Elastica\Query\BoolQuery();

        foreach ($queries as $query) {
            $boolQuery->addShould($query);
        }

        return $boolQuery;
    }

    /**
     * @param $query
     * @param null $options
     * @return mixed
     */
    protected function _search($query, $options = null)
    {
        return $this->type->search($query, $options);
    }

    /**
     * @param $query
     * @return mixed
     */
    protected function _count($query)
    {
        return $this->type->count($query);
    }

    /**
     * @return string
     */
    protected function getAnalyzer()
    {
//        return $this->strict ? self::SEARCH_STRICT_ANALYZER : self::SEARCH_ANALYZER;
        return self::SEARCH_ANALYZER;
    }

    /**
     * @param $name
     * @return string
     */
    protected function getFieldWithBoost($name)
    {
        $field = $this->getField($name);

        return $name . '^' . $field['boost'];
    }

    /**
     * @return array
     */
    protected function getFieldsWithBoost()
    {
        $result = array();

        foreach ($this->fields as $name => $value) {
            $result[] = $this->getFieldWithBoost($name);
        }

        return $result;
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getField($name)
    {
        return $this->fields[$name];
    }
}