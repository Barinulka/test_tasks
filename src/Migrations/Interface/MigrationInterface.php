<?php
namespace Barinulka\Parser\Migrations\Interface;

use PDO;

interface MigrationInterface
{
    public function getMigrationFiles() :array;

    public function migrate();
}