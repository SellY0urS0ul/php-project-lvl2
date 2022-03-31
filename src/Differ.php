<?php

namespace Differ\Differ;

use function Php\Project\Lvl2\Parser\parse;
use function Php\Project\Lvl2\Render\Formatters\render;
use function Functional\sort;

// Функция, генерирующая форматированное отличие 2-х файлов
function genDiff(string $firstPath, string $secondPath, string $format = "stylish"): string
{
    $firstFileContent = parse($firstPath);
    $secondFileContent = parse($secondPath);
    $diff = findDiff($firstFileContent, $secondFileContent);
    return render($diff, $format);
}

// Функция, осуществляющая поиск отличий между 2-я файлами
function findDiff(array $firstFile, array $secondFile): array
{
    //Список уникальных ключей одного уровня
    $uniqueKeys = array_unique(array_merge(array_keys($firstFile), array_keys($secondFile)));
    $sortedUniqueKeys = sort($uniqueKeys, fn (string $left, string $right) => strcmp($left, $right));

    //Рекурсивное построение дерева отличий в 2-х файлах
    $difference = array_map(function ($key) use ($firstFile, $secondFile) {

//Ключ присутствует в обоих файлах

        if (array_key_exists($key, $firstFile) && array_key_exists($key, $secondFile)) {
            //Ключ - директория
            if (is_array($firstFile[$key]) && is_array($secondFile[$key])) {
                $node = generateNode($key, 'Unchanged', '', findDiff($firstFile[$key], $secondFile[$key]));
            } elseif (!is_array($firstFile[$key]) && !is_array($secondFile[$key])) {
                //Ключ -  файл
                if ($firstFile[$key] === $secondFile[$key]) {
                    $node = generateNode($key, 'Unchanged', $firstFile[$key]);
                } else {
                    $changedItem = generateNode($key, 'Changed', $firstFile[$key]);
                    $addedItem = generateNode($key, 'Added', $secondFile[$key]);
                    $node = ["Changed" => $changedItem, "Added" => $addedItem];
                }
            } elseif (is_array($firstFile[$key]) && !is_array($secondFile[$key])) {
                //Первый ключ - директория, второй - файл
                $changedItem =  generateNode($key, 'Changed', '', normalizeNode($firstFile[$key]));
                $addedItem = generateNode($key, 'Added', $secondFile[$key]);
                $node = ["Changed" => $changedItem, "Added" => $addedItem];
            } else {
                //Первый ключ - файл, второй - директория
                $changedItem =  generateNode($key, 'Changed', $firstFile[$key]);
                $addedItem = generateNode($key, 'Added', '', normalizeNode($secondFile[$key]));
                $node = ["Changed" => $changedItem, "Added" => $addedItem];
            }
        } elseif (array_key_exists($key, $firstFile) && !array_key_exists($key, $secondFile)) {
//Ключ присутствует только в 1-м файле

            //Ключ - директория
            if (is_array($firstFile[$key])) {
                $node = generateNode($key, 'Changed', '', normalizeNode($firstFile[$key]));
            } else {
                //Ключ -  файл
                $node = generateNode($key, 'Changed', $firstFile[$key]);
            }
        } elseif (array_key_exists($key, $secondFile) && !array_key_exists($key, $firstFile)) {
//Ключ присутствует только во 2-м файле

            //Ключ - директория
            if (is_array($secondFile[$key])) {
                $node = generateNode($key, 'Added', '', normalizeNode($secondFile[$key]));
            } else {
                //Ключ -  файл
                $node = generateNode($key, 'Added', $secondFile[$key]);
            }
        } else {
            $node = '';
        }
        return $node;
    }, $sortedUniqueKeys);

    return $difference;
}

//Функция, генерирующая узел в дереве изменений
function generateNode(string $key, string $action, mixed $value, array $children = []): array
{
    $nodeContent = ["action" => $action, "value" => normalizeValue($value), "children" => $children];
    $node = [$key => $nodeContent];
    return $node;
}

//Функция, нормализующая формат неизмененных директорий
function normalizeNode(array $node)
{
    $nodeKeys = sort(array_keys($node), fn (string $left, string $right) => strcmp($left, $right));
    $finalNode = array_map(function ($nodeKey) use ($node) {
        $action = 'Unchanged';
        $value = (!is_array($node[$nodeKey])) ? normalizeValue($node[$nodeKey]) : '';
        $key = $nodeKey;
        $children = (!is_array($node[$nodeKey])) ? [] : normalizeNode($node[$nodeKey]);
        $nodeContent = ["action" => $action, "value" => $value, "children" => $children];
        $normalizedNode = [$key => $nodeContent];
        return $normalizedNode;
    }, $nodeKeys);
    return $finalNode;
}

//Функция, обрабатывающие значения bool и null
function normalizeValue(mixed $value)
{
    if ($value === true) {
        return 'true';
    } elseif ($value === false) {
        return 'false';
    } elseif ($value === null) {
        return 'null';
    } else {
        return $value;
    };
}
