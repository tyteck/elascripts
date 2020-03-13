<?php

namespace elascripts;

use Illuminate\Support\Collection;

class ElasticResultParser
{
    /** @var string $response */
    protected $response;

    /** @var array $responseAry */
    protected $responseAry = [];

    private function __construct(string $elasticResponse)
    {
        $this->response = $elasticResponse;
        $this->responseAry = $this->toArray();
    }

    public static function parse(...$params)
    {
        return new static(...$params);
    }

    public function toArray(): array
    {
        return json_decode($this->response, true);
    }

    public function nbHits(): int
    {
        return $this->responseAry['hits']['total']['value'];
    }
}
