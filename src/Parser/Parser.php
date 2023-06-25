<?php
namespace Barinulka\Parser\Parser;

use Barinulka\Parser\Models\Debtam;
use Barinulka\Parser\Repositories\DebtamRepository\DebtamRepository;
use PDO;
use SimpleXMLElement;

class Parser implements ParserInterface
{
    public function __construct(
        private PDO $connection
    ){        
    }

    public function getAllParseFiles(): array
    {
        // Путь к папке с файлами
        $dirFiles = dirname(__FILE__, 3) . '/storage/';
        // Получаем список всех файлов
        $allFiles = glob($dirFiles . '*.xml');
        $parseFiles = [];

        // Ищем выполненные файлы
        $doneFiles = array();
        $query = $this->connection->query(
            'SELECT name from `parse_info`'
        );

        $data = $query->fetchAll(PDO::FETCH_ASSOC);

        if (empty($data)) {
            return $allFiles;
        }

        // Набираем массив 
        foreach ($data as $val) {
            $parseFiles[] = $dirFiles . $val['name'];
        }
 
        return array_diff($allFiles, $parseFiles);
    }

    public function parse()
    {
        echo "*** Получение списка доступных файлов для парсинга..." . PHP_EOL;

        $files = $this->getAllParseFiles();

        echo "*** Доступно " . count($files) . " файлов..." . PHP_EOL;

        if (empty($files)) {
            echo "***  Нет новых файлов!" . PHP_EOL;
            return;
        }

        echo "*** Начало парсинга..." . PHP_EOL;

        foreach ($files as $file) {
            // Парсим xml файл и сохраняем в БД
            $doc = simplexml_load_file($file);

            // Создаем экземпляр репозитория
            $debtamRepository = new DebtamRepository($this->connection);
            
            foreach ($doc as $key => $value) {
                // Выбираем их файла все инфу из раздела <Документ>...</Документ>
                if ($key == 'Документ') {
                    // Основная ин-фо о каждом налоге лежит в <СведНедоим></СведНедоим>
                    // Проходим по ней циклом и на каждой этерации делаем запись в БД
                    foreach ($value->СведНедоим as $v) {
                        $debtamRepository->save(
                            new Debtam(
                                1,
                                $v['НаимНалог'],
                                $value->СведНП['НаимОрг'],
                                $value->СведНП['ИННЮЛ'],
                                $v['СумНедНалог'],
                                $v['СумПени'],
                                $v['СумШтраф'],
                                $v['ОбщСумНедоим'],
                                time()
                            )
                        );
                    }
                }
            }

            echo "*** Парсинг " . basename($file) . " завершен..." . PHP_EOL;

            // Сохранение истори парсинга файлов
            $query = $this->connection->prepare(
                'INSERT INTO `parse_info` (name) VALUES (:name)'
            );
            $query->execute([
                ':name' => basename($file)
            ]);

        }

        echo "*** Парсер закончил работу!" . PHP_EOL;
    }
}