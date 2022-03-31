<?php

namespace Php\Project\Lvl2\Parser;

use Symfony\Component\Yaml\Yaml;

// Функция, принимающая на вход путь к файлу и возвращающая массив с его содержимым

function parse(mixed $path): array
{
    if (substr($path, -4) === "json") {
        $fileContent = json_decode(file_get_contents($path), true);
        return $fileContent;
    } else {
        $fileContent = Yaml::parseFile($path);
        return $fileContent;
    }
}
