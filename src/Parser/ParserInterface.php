<?php
namespace Barinulka\Parser\Parser;

interface ParserInterface
{
    public function getAllParseFiles() :array;

    public function parse();
}