#!/usr/bin/php
<?php

require 'vendor/autoload.php';

use elascripts\ElasticWrapper;

$indexes = ($esWrapperObj = ElasticWrapper::create())->indexes();

$esWrapperObj->setIndexToUse($indexes->first()['index']);

$response = $esWrapperObj->search('gau','title');
print_r($response);
