<?php

require_once __DIR__ . '/config.php';

try {
    $connection = new PDO("mysql:host=".$config['DB_HOST'].";dbname=".$config['DB_NAME'], $config['DB_USER'], $config['DB_PASSWORD']);
} catch (PDOException $ex) {
    echo $ex->getMessage() . PHP_EOL;
}