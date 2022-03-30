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
    $difference = array_reduce($uniqueKeys, function ($acc, $key) use ($firstFile, $secondFile) {

        //Ключ присутствует в обоих файлах
        if (array_key_exists($key, $firstFile) && array_key_exists($key, $secondFile)) {
            //Ключ - директория
            if (is_array($firstFile[$key]) && is_array($secondFile[$key])) {
                $acc[] = generateNode($key, "Old", 'Unchanged', '', findDiff($firstFile[$key], $secondFile[$key]));
            }
            //Ключ -  файл
            if (!is_array($firstFile[$key]) && !is_array($secondFile[$key])) {
                if ($firstFile[$key] === $secondFile[$key]) {
                    $acc[] = generateNode($key, "Old", 'Unchanged', $firstFile[$key]);
                } else {
                    $acc[] = generateNode($key, "Old", 'Changed', $firstFile[$key]);
                    $acc[] = generateNode($key, "Old", 'Added', $secondFile[$key]);
                }
            }


            //Первый ключ - директория, второй - файл
            if (is_array($firstFile[$key]) && !is_array($secondFile[$key])) {
                $acc[] = generateNode($key, "Old", 'Changed', '', normalizeNode($firstFile[$key]));
                $acc[] = generateNode($key, "Old", 'Added', $secondFile[$key]);
            }

            //Первый ключ - файл, второй - директория
            if (!is_array($firstFile[$key]) && is_array($secondFile[$key])) {
                $acc[] = generateNode($key, "Old", 'Added', '', normalizeNode($secondFile[$key]));
                $acc[] = generateNode($key, "Old", 'Changed', $firstFile[$key]);
            }
        }

        //Ключ присутствует только в 1-м файле
        if (array_key_exists($key, $firstFile) && !array_key_exists($key, $secondFile)) {
            //Ключ - директория
            if (is_array($firstFile[$key])) {
                $acc[] = generateNode($key, "New", 'Changed', '', normalizeNode($firstFile[$key]));
            }
            //Ключ -  файл
            if (!is_array($firstFile[$key])) {
                $acc[] = generateNode($key, "New", 'Changed', $firstFile[$key]);
            }
        }

        //Ключ присутствует только во 2-м файле
        if (array_key_exists($key, $secondFile) && !array_key_exists($key, $firstFile)) {
            //Ключ - директория
            if (is_array($secondFile[$key])) {
                $acc[] = generateNode($key, "New", 'Added', '', normalizeNode($secondFile[$key]));
            }
            //Ключ -  файл
            if (!is_array($secondFile[$key])) {
                $acc[] = generateNode($key, "New", 'Added', $secondFile[$key]);
            }
        }
        return $acc;
    });
    return $difference;
}

//Функция, генерирующая узел в дереве изменений
function generateNode($key, $type, $action, $value, $children = [])
{
    $nodeContent = ["type" => $type, "action" => $action, "value" => normalizeValue($value), "children" => $children];
    $node = [$key => $nodeContent];
    return $node;
}

//Функция, нормализующая формат неизмененных директорий
function normalizeNode($node)
{
    $nodeKeys = array_keys($node);
    sort($nodeKeys);
    $normalizedNode = array_map(function ($nodeKey) use ($node) {
            $type = 'Old';
            $action = 'Unchanged';
            $value = (!is_array($node[$nodeKey])) ? normalizeValue($node[$nodeKey]) : '';
            $key = $nodeKey;
            $children = (!is_array($node[$nodeKey])) ? [] : normalizeNode($node[$nodeKey]);
            $nodeContent = ["type" => $type, "action" => $action, "value" => $value, "children" => $children];
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
