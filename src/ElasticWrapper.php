<?php

namespace elascripts;

use Elasticsearch\ClientBuilder;

class ElasticWrapper
{
    /** @var \ElasticSearch\Client $elacticSearchClient */
    protected $elacticSearchClient;
    /** @var string $index the index to use */
    protected $index;
    /** @var array $hosts elastic hosts */
    protected $hosts = [];
    /** @var array $results */
    protected $results = [];
    /** @var array $queryParams query passed to ES server */
    protected $queryParams;
    /** @var array name of the indices */
    protected $indices = [];


    private function __construct(bool $withoutSystemIndexes = true)
    {
        $this->setHosts();
        $this->elacticSearchClient = ClientBuilder::create()->setHosts($this->hosts())->build();
        $this->setIndices($withoutSystemIndexes);
    }

    public static function create(...$params)
    {
        return new static(...$params);
    }

    protected function setHosts(): void
    {
        $host = [
            'host' => getenv('ELASTIC_HOST'),
            'port' => getenv('ELASTIC_PORT'),
        ];
        if (getenv('ELASTIC_USER') !== null) {
            $host['user'] = getenv('ELASTIC_USER');
        }
        if (getenv('ELASTIC_PASS') !== null) {
            $host['pass'] = getenv('ELASTIC_PASS');
        }
        $this->hosts = [$host];
    }

    public function hosts(): array
    {
        return $this->hosts;
    }

    /** this one is returning all the available indices in elasticSearchHost */
    public function setIndices(bool $withoutSystemIndexes = true): void
    {
        // obtaining indices list
        $indices = $this->elacticSearchClient->cat()->indices(array('index' => '*'));
        // filtering system ones 
        if ($withoutSystemIndexes) {
            $indices = array_filter($indices, function ($index) {
                if (substr($index['index'], 0, 1) == '.') {
                    return false;
                }
                return true;
            });
        }

        // keeping only the index names
        $indices = array_map(function ($item) {
            return $item['index'];
        }, $indices);

        if (count($indices) <= 0) {
            throw new \RuntimeException("There is no index on this server.");
        }
        $this->indices = $indices;
        $this->nbIndices = count($indices);
    }

    public function guessIndexToUse()
    {
        if (count($this->indices()) == 1) {
            $this->setIndexToUse(array_values($this->indices())[0]);
            return true;
        }
        return false;
    }

    public function setIndexToUse(string $index): bool
    {
        if (in_array($index, $this->indices())) {
            $this->index = $index;
            return true;
        }
        return false;
    }

    public function index()
    {
        return $this->index;
    }

    public function indices()
    {
        return $this->indices;
    }

    public function nbIndices()
    {
        return $this->nbIndices;
    }

    public function matchAll()
    {
        if ($this->index() === null) {
            throw new \RuntimeException("Set index to use before usinf search");
        }

        $this->results = $this->elacticSearchClient->search(
            [
                'index' => $this->index(),
                'body'  => [
                    'query' => [
                        'match_all' => [
                            "boost" => 1.0,
                        ]
                    ]
                ]
            ]
        );

        return $this;
    }

    public function nbResults(): int
    {
        return $this->results["hits"]["total"]["value"];
    }

    public function match(string $needle, string $haystack = 'title')
    {
        if ($this->index() === null) {
            throw new \RuntimeException("Set index to use before using search");
        }

        $this->results = $this->elacticSearchClient->search(
            [
                'index' => $this->index(),
                'body'  => [
                    'query' => [
                        'match' => [
                            "$haystack" => $needle,
                        ]
                    ]
                ]
            ]
        );
        return $this;
    }

    public function prefix(string $needle, string $haystack = 'title')
    {
        if ($this->index() === null) {
            throw new \RuntimeException("Set index to use before using search");
        }

        $params = [
            'index' => $this->index(),
            'body'  => [
                'query' => [
                    'prefix' => [
                        "$haystack" => [
                            "value" => strtolower($needle)
                        ],
                    ]
                ]
            ]
        ];

        $this->results = $this->elacticSearchClient->search($params);
        return $this;
    }

    public function column(string $columnName)
    {
        $results = array_map(function ($item) use ($columnName) {
            return $item['_source'][$columnName];
        }, $this->results['hits']['hits']);
        return $results;
    }

    public function getResults(array $columns)
    {

        $results = array_map(
            function ($item) use ($columns) {
                $simplifiedItem = [];
                foreach ($columns as $column) {
                    if (isset($item["_source"][$column])) {
                        $simplifiedItem[$column] = $item["_source"][$column];
                    }
                }
                return $simplifiedItem;
            },
            $this->results['hits']['hits']
        );
        return $results;
    }
}
