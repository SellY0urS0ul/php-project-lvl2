<?php

namespace Php\Project\Lvl2\Differ;

use function Php\Project\Lvl2\Parser\parse;
use function Php\Project\Lvl2\Render\Render\render;

// Общий обработчик, объединяющий работу функций
function genDiff($firstPath, $secondPath, $format = "stylish")
{
    $firstFile = parse($firstPath);
    $secondFile = parse($secondPath);
    $diff = findDiff($firstFile, $secondFile);
    return render($diff, $format);
}

// Функция, производящая поиск отличий между 2-я файлами
function findDiff(array $firstFile, array $secondFile)
{
    //Список уникальных ключей одного уровня
    $uniqueKeys = array_unique(array_merge(array_keys($firstFile), array_keys($secondFile)));
    $difference = array_reduce($uniqueKeys, function ($acc, $key) use ($firstFile, $secondFile) {
        //Ключ присутствует в обоих файлах
        if (array_key_exists($key, $firstFile) && array_key_exists($key, $secondFile)) {
            //Директории
            if (is_array($firstFile[$key]) && is_array($secondFile[$key])) {
                $acc[] = generateNode($firstFile[$key], $key, 'array', findDiff($firstFile[$key], $secondFile[$key]));
                return $acc;
            }
            //Файлы
            if (!is_array($firstFile[$key]) & !is_array($secondFile[$key])) {
                if ($firstFile[$key] == $secondFile[$key]) {
                    $acc[] = generateNode($firstFile[$key], $key, ' ');
                    return $acc;
                } else {
                    $acc[] = generateNode($firstFile[$key], $key, '-');
                    $acc[] = generateNode($secondFile[$key], $key, '+');
                    return $acc;
                }
            }
        }

        //Ключ присутствует только в 1-м файле
        if (array_key_exists($key, $firstFile)) {
            //Директории
            //Файлы
            if (!is_array($firstFile[$key])) {
                $acc[] = generateNode($firstFile[$key], $key, '-');
                return $acc;
            }
        }

        //Ключ присутствует только во 2-м файле
        if (array_key_exists($key, $secondFile)) {
            //Директории
            //Файлы
            if (!is_array($secondFile[$key])) {
                $acc[] = generateNode($secondFile[$key], $key, '+');
                return $acc;
            }
        }
        //Файлы
    }, []);
    return $difference;
}

function generateNode($name, $key, $type, $children = [])
{
    $value = ["type" => $type, "name" => $name, "children" => $children];
    $node = [$key => $value];
    return $node;
}