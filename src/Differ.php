<?php

namespace Differ\Differ;

use function Php\Project\Lvl2\Parser\parse;
use function Php\Project\Lvl2\Formatters\render;
use function Functional\sort;

// Функция, генерирующая форматированное отличие 2-х файлов
function genDiff(string $firstPath, string $secondPath, string $format = "stylish"): string
{
    $firstFileContent = parse($firstPath);
    $secondFileContent = parse($secondPath);
    $diff = makeDiff($firstFileContent, $secondFileContent);
    return render($diff, $format);
}

// Функция, осуществляющая поиск отличий между 2-я файлами
function makeDiff(array $firstFile, array $secondFile): array
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
                return generateNode($key, 'Unchanged', '', makeDiff($firstFile[$key], $secondFile[$key]));
            }
            if (!is_array($firstFile[$key]) && !is_array($secondFile[$key])) {
                //Ключ -  файл
                if ($firstFile[$key] === $secondFile[$key]) {
                    return generateNode($key, 'Unchanged', $firstFile[$key]);
                }
                if ($firstFile[$key] !== $secondFile[$key]) {
                    $changedItem = generateNode($key, 'Changed', $firstFile[$key]);
                    $addedItem = generateNode($key, 'Added', $secondFile[$key]);
                    return ["Changed" => $changedItem, "Added" => $addedItem];
                }
            }
            if (is_array($firstFile[$key]) && !is_array($secondFile[$key])) {
                //Первый ключ - директория, второй - файл
                $changedItem =  generateNode($key, 'Changed', '', normalizeNode($firstFile[$key]));
                $addedItem = generateNode($key, 'Added', $secondFile[$key]);
                return ["Changed" => $changedItem, "Added" => $addedItem];
            }
            //Первый ключ - файл, второй - директория
            $changedItem =  generateNode($key, 'Changed', $firstFile[$key]);
            $addedItem = generateNode($key, 'Added', '', normalizeNode($secondFile[$key]));
            return ["Changed" => $changedItem, "Added" => $addedItem];
        }
        if (array_key_exists($key, $firstFile)) {
            //Ключ присутствует только в 1-м файле
            //Ключ - директория
            if (is_array($firstFile[$key])) {
                return generateNode($key, 'Changed', '', normalizeNode($firstFile[$key]));
            }
            //Ключ -  файл
            return generateNode($key, 'Changed', $firstFile[$key]);
        }
        if (array_key_exists($key, $secondFile)) {
            //Ключ присутствует только во 2-м файле
            //Ключ - директория
            if (is_array($secondFile[$key])) {
                return generateNode($key, 'Added', '', normalizeNode($secondFile[$key]));
            }
            //Ключ -  файл
            return generateNode($key, 'Added', $secondFile[$key]);
        }
    }, $sortedUniqueKeys);
    return $difference;
}

//Функция, генерирующая узел в дереве изменений
function generateNode(string $key, string $action, mixed $value, array $children = []): array
{
    $nodeContent = ["action" => $action, "value" => $value, "children" => $children];
    $node = [$key => $nodeContent];
    return $node;
}

//Функция, нормализующая формат неизмененных директорий
function normalizeNode(mixed $node)
{
    $nodeKeys = sort(array_keys($node), fn (string $left, string $right) => strcmp($left, $right));
    $finalNode = array_map(function ($nodeKey) use ($node) {
        $action = 'Unchanged';
        $value = (!is_array($node[$nodeKey])) ? $node[$nodeKey] : '';
        $key = $nodeKey;
        $children = (!is_array($node[$nodeKey])) ? [] : normalizeNode($node[$nodeKey]);
        $nodeContent = ["action" => $action, "value" => $value, "children" => $children];
        $normalizedNode = [$key => $nodeContent];
        return $normalizedNode;
    }, $nodeKeys);
    return $finalNode;
}
