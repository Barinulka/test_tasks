<?php

use Barinulka\Parser\Migrations\Migration;

require_once __DIR__ . '/database/connection.php';
require_once __DIR__ . '/vendor/autoload.php';

$migrate = new Migration($connection, $config);

$migrate->migrate();