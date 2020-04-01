<?php

use Dotenv\Dotenv;
use NunoMaduro\Collision\Provider as CollisionProvider;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['ELASTIC_HOST', 'ELASTIC_PORT']);

(new CollisionProvider)->register();