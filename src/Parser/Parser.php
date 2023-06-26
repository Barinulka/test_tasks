<?php
namespace Barinulka\Parser\Parser;

use PDO;
use DOMDocument;
use Barinulka\Parser\Models\Debtam;
use Barinulka\Parser\Repositories\DebtamRepository\DebtamRepository;
use Exception;
use ZipArchive;

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
        $return = [];
        $parseFiles = [];

        // Ищем выполненные файлы
        $query = $this->connection->query(
            'SELECT name from `parse_info`'
        );

        $data = $query->fetchAll(PDO::FETCH_ASSOC);

        if (empty($data)) {
            $return = $allFiles;
        } 

        if (!empty($data)) {
            // Набираем массив 
            foreach ($data as $val) {
                $parseFiles[] = $dirFiles . $val['name'];
            }

            $return = array_diff($allFiles, $parseFiles);
        }
        
        // Забераем по 10 файлов, задел под cron
        return array_slice($return, 0, 10);
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

            // Удаление файла после парсинга
            // unlink($file);

        }

        echo "*** Парсер закончил работу!" . PHP_EOL;
    }

    public function check() 
    {
        $parseData = $this->domParseTable(12);

        $loadDate = strtotime(trim($parseData[2]));

        // Загрузим из БД запись о последнем загруженном файле
        $query = $this->connection->query(
            'SELECT update_date FROM parse_check ORDER BY update_date DESC LIMIT 1'
        );

        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($loadDate > $result['update_date']) {
            return true;
        } else {
            return false;
        }

    }

    public function load() 
    {
        $parseData = $this->domParseTable(8);

        $loadUrl = trim($parseData[2]);
        $name = preg_match("/data-(.*)$/m", $parseData[2], $matches);
        $fileName = $matches[0];

        $path = dirname(__FILE__, 3) . '/storage/' . $fileName;

        if ($this->isIssetFile($fileName)) {
            echo "Нет доступных фалов для загрузки" . PHP_EOL;
        } else {

            if (!file_exists($path)) {
                echo "Скачивание файла..." . PHP_EOL;
                $this->loadFile($loadUrl, $path);
            }
    
            if (file_exists($path)) {
                echo "Распаковка скаченного файла..." . PHP_EOL;
                $this->unzip($path);
            }
                
            $str = preg_match("/\bdata-[0-9]{0,8}\b/m", $parseData[2], $matches);
            $arr = explode('-', $matches[0]);
    
            $fileTime = strtotime($arr[1]);
           
            // Запись в БД ин-фы о загруженном файле
            $statement = $this->connection->prepare(
                'INSERT INTO parse_check (name, update_date) 
                    VALUES (:name, :update_date)'
            );
    
            $statement->execute([
                ':name' => $fileName, 
                ':update_date' => $fileTime, 
            ]);
    
            echo "Файлы распакованы и готовы к парсингу" . PHP_EOL;
        }

    }

    private function getHtmlContent() :string
    {
        $ch = curl_init('https://www.nalog.gov.ru/opendata/7707329152-debtam/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }

    private function domParseTable(int $row) :array
    {
        $html = $this->getHtmlContent();

        // Забираем контет
		$dom = new DOMDocument;
        libxml_use_internal_errors(true);
		$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        libxml_use_internal_errors(false);
		$node = $dom->getElementsByTagName('tr');

        $tbodies = [];

        // Получим массив из <tr>
        foreach ($node as $item) {
            $tbodies[] = preg_split("/[\n\r]+/", trim($item->textContent));
        }

        // Достаем нужный элемент
        $result = [];
        foreach ($tbodies as $elem) {
            if ($elem[0] == $row) {
                $result = $elem;
            }
        }

        return $result;

    }

    private function loadFile(string $url, string $path)
    {
        try {
            $fp = fopen($path, 'w');
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            $data = curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            echo 'file loaded' . PHP_EOL;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function unzip($path)
    {
        $zip = new ZipArchive();
                
        if ($zip->open($path) === true) {
            
            // Архивы большие, ограничил число файлов для распаковки
            // $zip->numFiles - будет идти по всему файлу
            for($i = 0; $i < 11; $i++) { 
                $filename = $zip->getNameIndex($i);

                // Архив может прилететь с вложенной папкой
                // будем ее игнорить и сразу брать нужные файлы
                if (!strpos($filename, '.xml')) continue;

                $fileinfo = pathinfo($filename);
                $file = dirname(__FILE__, 3) . '/storage/' . $fileinfo['basename'];
                $dir = dirname($file);
                // Пересохраняем файлы в каталог
                $fpr = $zip->getStream($filename);
                $fpw = fopen($file, 'w');
                while($data = fread($fpr, 1024)) {
                    fwrite($fpw, $data);
                }
                fclose($fpr);
                fclose($fpw);

            }                   
            $zip->close();

            echo "Архив распакован!" . PHP_EOL; 

            // Удаление масива после рапаковки
            // unlink($path);                   
        }
    }

    private function isIssetFile($fileName) :bool
    {
        $isIsset = false;

        $statement = $this->connection->prepare(
            'SELECT id FROM parse_check WHERE name = :name'
        );

        $statement->execute([
            ':name' => $fileName, 
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        
        if (!empty($result)) {
            $isIsset = true;
        }

        return $isIsset;
    }
}