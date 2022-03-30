<?php

namespace Php\Project\Lvl2\Render\Plain;

function plainFormatter(array $diff, $path = '')
{
    $formatedDiff = array_reduce($diff, function ($acc, $element) use ($path, $diff) {

        //Получение информации об узле
        $key = array_key_first($element);
        $children = $element[$key]["children"];
        $value = ($children === []) ? valueFormatter($element[$key]["value"]) : '[complex value]';
        $type = $element[$key]["type"];
        $action = $element[$key]["action"];

        //Добавление нового элемента
        if ($type === "New" && $action === "Added") {
            $acc = $acc . "Property '{$path}{$key}' was added with value: {$value}\n";
        }

        //Удаление элемента
        if ($type === "New" && $action === "Changed") {
            $acc = $acc . "Property '{$path}{$key}' was removed\n";
        }

        //Обновление существующего элемента
        if ($type === "Old" && $action === "Changed") {
            $newValue = array_reduce($diff, function ($carry, $element) use ($key) {
                $newKey = array_key_first($element);
                if ($newKey === $key && $element[$newKey]['action'] === 'Added') {
                    $newValue = valueFormatter($element[$newKey]['value']);
                    $carry = ($element[$newKey]['children'] !== []) ? '[complex value]' : $newValue;
                }
                return $carry;
            });
            $acc = $acc . "Property '{$path}{$key}' was updated. From {$value} to {$newValue}\n";
        }

        //Рекурсивная обработка директорий
        if ($children !== []) {
            $path = "{$path}{$key}.";
            $acc = $acc . plainFormatter($children, $path);
        }
        return $acc;
    });
    return $formatedDiff;
}

function valueFormatter($value)
{
    if ($value !== 'false' && $value !== 'true' && $value !== 'null') {
        $value = "'{$value}'";
    }
    return $value;
}