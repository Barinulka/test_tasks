<?php
namespace Barinulka\Parser\Migrations;

use Barinulka\Parser\Migrations\Interface\MigrationInterface;
use PDO;

class Migration implements MigrationInterface
{
    public function __construct(
        private PDO $connection, 
        private array $config
    ){
    }

    public function getMigrationFiles(): array
    {
        // Получаем путь до папки в файлами миграций
        $migrationsFolder = dirname(__FILE__, 3) . '/database/migrations/';
        // Получаем список всех файлов
        $allFiles = glob($migrationsFolder . '*.sql');

        // Проверка на наличие таблицы 'migrations' в базе
        $query = $this->connection->query(
            "SHOW TABLES from `" . $this->config['DB_NAME'] . "` LIKE 'migrations'"
        );

        $data = $query->fetch(PDO::FETCH_NUM);

        $firstMigrate = empty($data) ? false : true;

        if (!$firstMigrate) {
            return $allFiles;
        }

        // Ищем уже выполненные миграции
        $migrationsFiles = array();
        $query = $this->connection->query(
            'SELECT name from `migrations`'
        );

        $data = $query->fetchAll(PDO::FETCH_ASSOC);

        // Набираем массив 
        foreach ($data as $val) {
            $migrationsFiles[] = $migrationsFolder . $val['name'];
        }

        return array_diff($allFiles, $migrationsFiles);
    }

    public function migrate()
    {
        echo "*** Получение списка доступных миграций..." . PHP_EOL;

        // Получаем список файлов на выполнение
        $files = $this->getMigrationFiles();

        echo "*** Доступно " . count($files) . " миграций..." . PHP_EOL;

        if (empty($files)) {
            echo "***  БД в актуальном состоянии!" . PHP_EOL;
            return;
        }

        echo "*** Начало миграции..." . PHP_EOL;

        foreach ($files as $file) {
            $sql = file_get_contents($file);
            $this->connection->exec($sql);

            echo "*** Миграция " . basename($file) . " завершена..." . PHP_EOL;

            $query = $this->connection->prepare(
                'INSERT INTO `migrations` (name) VALUES (:name)'
            );

            $query->execute([
                ':name' => basename($file)
            ]);

        }

        echo "*** Все миграции выполнены!" . PHP_EOL;

    }
}