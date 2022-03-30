#!/usr/bin/env php

<?php

// Вызовы функций из других неймспейсов
use function Php\Project\Lvl2\Differ\genDiff;

// Автозагрузка всех зависимостей

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath1)) {
  require_once $autoloadPath1;
} else {
  require_once $autoloadPath2;
}
// Работа терминального интерфейса docopt
$doc = <<<DOC
Generate Difference
Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>
Options:
  -h --help     Show this screen
  -v --version  Show version
  --format <fmt> Report format [default: stylish]
DOC;

$args = Docopt::handle($doc, array('version' => '1.0.0'));

// Запись формата и путей до файлов, полученных из терминала

$firstPath = $args['<firstFile>'];
$secondPath = $args['<secondFile>'];
$format = $args['--format'];

echo (genDiff($firstPath, $secondPath, $format));