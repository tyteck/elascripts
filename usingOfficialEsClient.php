#!/usr/bin/php
<?php

require 'vendor/autoload.php';

use elascripts\ElasticWrapper;

$indexes = ($esWrapperObj = ElasticWrapper::create())->indexes();

// no index => leave
if ($indexes->count() <= 0) {
    die("There is no indexes on this server.");
}

if ($indexes->count() == 1) {
    // only one index use it
    $esWrapperObj->setIndexToUse($indexes->first()['index']);
} else {
    //ask user
    echo "=================================" . PHP_EOL;
    echo "Available indexes" . PHP_EOL;
    $i = 1;
    foreach ($indexes->pluck('index') as $index) {
        echo "$i -- $index : " . PHP_EOL;
        $i++;
    }
    $selectedOne = readline("Select the index to use [1-" . $indexes->count() . "] : ");

    if (!is_numeric($selectedOne)) {
        die("You should type a number.");
    }

    if (!(1 <= $selectedOne && $selectedOne <= $indexes->count())) {
        die("You should type a number between 1 and " . $indexes->count() . ".");
    }

    $indexName = $indexes->values()[$selectedOne - 1]['index'];
}

echo "=================================" . PHP_EOL;
echo "Index used : " . $esWrapperObj->indexUsed() . PHP_EOL;
echo "=================================" . PHP_EOL;


echo "total nb documents : " . $esWrapperObj->documents()->nbDocuments();
//var_dump($esWrapperObj->documents()->column('title'));

$nbDocuments = $esWrapperObj->search('Cgr', 'title')->nbDocuments();
echo PHP_EOL . "Query : " . $esWrapperObj->lastQuery();
echo PHP_EOL . "Nb results : " . $nbDocuments;
