<?php

namespace Php\Project\Lvl2\Render\Json;

function jsonFormatter(array $diff)
{
    return json_encode($diff, JSON_PRETTY_PRINT);
}
