<?php

use Barinulka\Parser\Parser\Parser;
use Barinulka\Parser\Repositories\DebtamRepository\DebtamRepository;

require_once __DIR__ . '/database/connection.php';
require_once __DIR__ . '/vendor/autoload.php';

$debtamRepository = new DebtamRepository($connection);

if (isset($argv[1])) {
    switch ($argv[1]) {
        case 'parse' :
            $parse = new Parser($connection);
            $parse->parse();
            break;
        case 'totalDebt' : 
            $totalDebt = $debtamRepository->getTotalDebt();

            foreach ($totalDebt as $debt) {
                echo $debt['inn'] . "; " . $debt['org_name'] . "; " . $debt['total'] . PHP_EOL;
            }

            break;
        case 'totalTaxName' : 
            $totalDebt = $debtamRepository->getTotalDebtByTaxType();

            foreach ($totalDebt as $debt) {
                echo $debt['tax_name'] . "; " . $debt['total'] . PHP_EOL;
            }

            break;
        case 'totalAvg' : 
            $totalDebt = $debtamRepository->getAvgDebtByRegion();

            foreach ($totalDebt as $debt) {
                echo $debt['region'] . "; " . $debt['total'] . PHP_EOL;
            }

            break;
        default :
            echo "parse - старт парсинга\r\n";
    }
} else {
    echo "parse - старт парсинга" . PHP_EOL;
}