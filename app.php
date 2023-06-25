<?php

use Barinulka\Parser\Parser\Parser;

require_once __DIR__ . '/database/connection.php';
require_once __DIR__ . '/vendor/autoload.php';

$parse = new Parser($connection);

$parse->parse();