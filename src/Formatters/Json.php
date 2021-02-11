<?php

namespace Differ\Formatters\Json;

function json($tree): string
{
    $str = json_encode($tree, JSON_PRETTY_PRINT);
    return $str;
}
