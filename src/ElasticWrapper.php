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
    /** @var array elastic hosts */
    protected $hosts = [];

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
        $this->hosts = [
            getenv('ELASTIC_HOST') . ':' . getenv('ELASTIC_PORT')
        ];
    }

    public function hosts(): array
    {
        return $this->hosts;
    }

    /** this one is returning all the available indexes in elasticSearchHost */
    public function indexes(): Collection
    {
        return collect($this->elacticSearchClient->cat()->indices(array('index' => '*')));
    }

    public function setIndexToUse(string $indexName): bool
    {
        if ($this->indexes()->pluck('index')->contains($indexName)) {
            $this->indexToUse = $indexName;
            return true;
        }
        return false;
    }

    public function documents()
    {
        if ($this->indexToUse === null) {
            throw new \RuntimeException("Set index to use before usinf search");
        }

        $params = [
            'index' => $this->indexToUse,
            'body'  => [
                'query' => [
                    'match_all' => [
                        'boost' => 1.0,
                    ]
                ]
            ]
        ];
        return collect($this->elacticSearchClient->search($params));
    }

    public function search(string $needle, string $haystack)
    {
        if ($this->indexToUse === null) {
            throw new \RuntimeException("Set index to use before usinf search");
        }

        $params = [
            'index' => $this->indexToUse,
            'body'  => [
                'query' => [
                    'match' => [
                        'title' => 'cgr'
                    ]
                ]
            ]
        ];
        return collect($this->elacticSearchClient->search($params));
    }
}
