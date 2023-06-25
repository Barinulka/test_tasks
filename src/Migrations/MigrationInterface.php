<?php
namespace Barinulka\Parser\Migrations;

use PDO;

interface MigrationInterface
{
    public function getMigrationFiles() :array;

    public function migrate();
}