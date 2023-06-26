<?php

use Barinulka\Parser\Parser\Parser;
use Barinulka\Parser\Repositories\DebtamRepository\DebtamRepository;

require_once __DIR__ . '/database/connection.php';
require_once __DIR__ . '/vendor/autoload.php';

$parse = new Parser($connection);

if (isset($argv[1])) {
    switch ($argv[1]) {
        case 'check' :
            if ($parse->check()) {
                echo "Доступна новая версия" . PHP_EOL;
            } else {
                echo "Нет новых файлов" . PHP_EOL;
            }
            break;
        case 'load' : 
            $parse->load();
            break;
        default :
            echo "parse - Cтарт парсинга" . PHP_EOL;
            echo "totalDebt - Компании с наибольшей суммарной задолженностью" . PHP_EOL;
            echo "totalTaxName - Общая задолженность всех компаний по каждому виду налога" . PHP_EOL;
            echo "totalAvg - Средняя задолженность по регионам" . PHP_EOL;
    }
}



