#!/usr/bin/php
<?php

require 'vendor/autoload.php';

use elascripts\ElasticWrapper;

$indexes = ($esWrapperObj = ElasticWrapper::create())->indexes();

$esWrapperObj->setIndexToUse($indexes->first()['index']);

echo PHP_EOL . "total nb documents : " . $esWrapperObj->documents()->nbDocuments();
var_dump($esWrapperObj->documents()->column('title'));

$nbDocuments = $esWrapperObj->search('Cgr', 'title')->nbDocuments();
echo PHP_EOL . "Index user : " . $esWrapperObj->indexUsed();
echo PHP_EOL . "Query : " . $esWrapperObj->lastQuery();
echo PHP_EOL . "Nb results : " . $nbDocuments;
