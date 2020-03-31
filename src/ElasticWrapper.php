<?php

namespace elascripts;

use Elasticsearch\ClientBuilder;
use Illuminate\Support\Collection;

class ElasticWrapper
{
    /** @var ClientBuilder $elacticSearchClient */
    protected $elacticSearchClient;
    /** @var string $indexToUse the index to use */
    protected $indexToUse;
    /** @var array $hosts elastic hosts */
    protected $hosts = [];
    /** @var array $results */
    protected $results = [];
    /** @var array $queryParams query passed to ES server */
    protected $queryParams;


    private function __construct()
    {
        $this->setHosts();
        $this->elacticSearchClient = ClientBuilder::create()->setHosts($this->hosts())->build();
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

    /** this one is returning all the available indexes in elasticSearchHost */
    public function indexes(bool $withoutSystemIndexes = true): Collection
    {
        $indexes = $this->elacticSearchClient->cat()->indices(array('index' => '*'));
        if ($withoutSystemIndexes) {
            $indexes = array_filter($indexes, function ($index) {
                if (substr($index['index'], 0, 1) == '.') {
                    return false;
                }
                return true;
            });
        }
        return collect($indexes);
    }

    public function setIndexToUse(string $indexToUse): bool
    {
        $result = array_filter(
            array_map(function ($item) {
                return $item['index'];
            }, $this->indexes()->toArray()),
            function ($item) use ($indexToUse) {
                if ($item == $indexToUse) {
                    return true;
                }
                return false;
            }
        );

        if (count($result)) {
            $this->indexToUse = $indexToUse;
            return true;
        }
        return false;
    }

    public function indexUsed()
    {
        return $this->indexToUse;
    }

    public function documents()
    {
        if ($this->indexToUse === null) {
            throw new \RuntimeException("Set index to use before usinf search");
        }
        $this->setParams($verb = 'match_all');

        $this->results = collect($this->elacticSearchClient->search($this->params()));

        return $this;
    }

    public function search(string $needle, string $haystack = 'title')
    {
        if ($this->indexToUse === null) {
            throw new \RuntimeException("Set index to use before using search");
        }

        $this->setParams($verb = 'match', $attributes = [$haystack => $needle]);

        $this->results = collect($this->elacticSearchClient->search($this->params()));

        return $this;
    }

    protected function setParams(string $verb, array $attributes = ['boost' => 1.0])
    {
        $this->queryParams = [
            'index' => $this->indexToUse,
            'body'  => [
                'size' => 500,
                'query' => [
                    $verb => $attributes
                ]
            ]
        ];
    }

    protected function params()
    {
        return $this->queryParams;
    }

    protected function query()
    {
        return $this->queryParams["body"]["query"];
    }

    public function nbDocuments(): int
    {
        if (empty($this->results)) {
            return null;
        }
        return $this->results['hits']['total']['value'];
    }

    public function column(string $columnName)
    {
        $results = array_map(function ($item) use ($columnName) {
            return $item['_source'][$columnName];
        }, $this->results['hits']['hits']);
        return $results;
    }

    public function lastQuery(): string
    {
        $result = "";
        foreach ($this->query() as $verb => $queryItem) {
            foreach ($queryItem as $key => $value) {
                $result .= "$verb -- $key = $value" . PHP_EOL;
            }
        }
        return $result;
    }

    public function getResults()
    {
        return $this->results;
    }
}
