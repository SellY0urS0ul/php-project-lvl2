#!/usr/bin/env php
<?php

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

$doc = <<<DOC

Generate diff

Usage:
  ./bin/gendiff.php (-h | --help)
  ./bin/gendiff.php (-v | --version)
  ./bin/gendiff.php [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help     Show this screen.
  -v --version      Show version.
  --format <fmt>      Report format [default: stylish]
DOC;

$args = Docopt::handle($doc, array('version'=>'Gendiff 1.0'));
foreach ($args as $k=>$v)
    echo $k.': '.json_encode($v).PHP_EOL;