<?php

namespace Differ\Differ;

use function Php\Project\Lvl2\Parser\parse;
use function Php\Project\Lvl2\Render\Formatters\render;

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
    sort($uniqueKeys);
    //Рекурсивное построение дерева отличий в 2-х файлах
    $acc = [];
    $difference = array_map(function ($key) use ($firstFile, $secondFile) {

        //Ключ присутствует в обоих файлах
        if (array_key_exists($key, $firstFile) && array_key_exists($key, $secondFile)) {
            //Ключ - директория
            if (is_array($firstFile[$key]) && is_array($secondFile[$key])) {
                return generateNode($key, 'Unchanged', '', findDiff($firstFile[$key], $secondFile[$key]));
            }
            //Ключ -  файл
            if (!is_array($firstFile[$key]) && !is_array($secondFile[$key])) {
                if ($firstFile[$key] === $secondFile[$key]) {
                    return generateNode($key, 'Unchanged', $firstFile[$key]);
                } else {
                    $changedItem = generateNode($key, 'Changed', $firstFile[$key]);
                    $addedItem = generateNode($key, 'Added', $secondFile[$key]);
                    return ["Changed" => $changedItem, "Added" => $addedItem];
                }
            }


            //Первый ключ - директория, второй - файл
            if (is_array($firstFile[$key]) && !is_array($secondFile[$key])) {
                $changedItem =  generateNode($key, 'Changed', '', normalizeNode($firstFile[$key]));
                $addedItem = generateNode($key, 'Added', $secondFile[$key]);
                return ["Changed" => $changedItem, "Added" => $addedItem];
            }

            //Первый ключ - файл, второй - директория
            if (!is_array($firstFile[$key]) && is_array($secondFile[$key])) {
                $changedItem =  generateNode($key, 'Changed', $firstFile[$key]);
                $addedItem = generateNode($key, 'Added', '', normalizeNode($secondFile[$key]));
                return ["Changed" => $changedItem, "Added" => $addedItem];
            }
        }

        //Ключ присутствует только в 1-м файле
        if (array_key_exists($key, $firstFile) && !array_key_exists($key, $secondFile)) {
            //Ключ - директория
            if (is_array($firstFile[$key])) {
                return generateNode($key, 'Changed', '', normalizeNode($firstFile[$key]));
            }
            //Ключ -  файл
            if (!is_array($firstFile[$key])) {
                return generateNode($key, 'Changed', $firstFile[$key]);
            }
        }

        //Ключ присутствует только во 2-м файле
        if (array_key_exists($key, $secondFile) && !array_key_exists($key, $firstFile)) {
            //Ключ - директория
            if (is_array($secondFile[$key])) {
                return generateNode($key, 'Added', '', normalizeNode($secondFile[$key]));
            }
            //Ключ -  файл
            if (!is_array($secondFile[$key])) {
                return generateNode($key, 'Added', $secondFile[$key]);
            }
        }
    }, $uniqueKeys);
    return $difference;
}

//Функция, генерирующая узел в дереве изменений
function generateNode($key, $action, $value, $children = [])
{
    $nodeContent = ["action" => $action, "value" => normalizeValue($value), "children" => $children];
    $node = [$key => $nodeContent];
    return $node;
}

//Функция, нормализующая формат неизмененных директорий
function normalizeNode($node)
{
    $nodeKeys = array_keys($node);
    sort($nodeKeys);
    $normalizedNode = array_map(function ($nodeKey) use ($node) {
        $action = 'Unchanged';
        $value = (!is_array($node[$nodeKey])) ? normalizeValue($node[$nodeKey]) : '';
        $key = $nodeKey;
        $children = (!is_array($node[$nodeKey])) ? [] : normalizeNode($node[$nodeKey]);
        $nodeContent = ["action" => $action, "value" => $value, "children" => $children];
        $node = [$key => $nodeContent];
        return $node;
    }, $nodeKeys);
    return $normalizedNode;
}

/**
 * Функция, обрабатывающие значения bool и null
 **/
function normalizeValue($value)
{
    if ($value === true) {
        $value = 'true';
    }
    if ($value === false) {
        $value = 'false';
    }
    if ($value === null) {
        $value = 'null';
    }
    return $value;
}
