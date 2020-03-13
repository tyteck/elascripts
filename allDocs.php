#!/usr/bin/php
<?php

require 'vendor/autoload.php';

use elascripts\Curly;
use elascripts\ElasticResultParser;

$elasticHost = "localhost:9200";

$query = <<<EOD
{
    "query": {
        "match_all": {}
    }
}
EOD;
$url = "$elasticHost/_search?pretty";

$curlResults = Curly::init($url)->setQuery($query)->run()->get();

$esObj = ElasticResultParser::parse($curlResults);

var_dump($esObj->nbHits());

//echo "nb documents in elastic : " . count($results['hits']['total'][]) . PHP_EOL;
