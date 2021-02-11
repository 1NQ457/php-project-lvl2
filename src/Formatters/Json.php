<?php

namespace Differ\Formatters\Json;

function json($tree): string
{
    return json_encode($tree, JSON_PRETTY_PRINT) ? json_encode($tree, JSON_PRETTY_PRINT) : '';
}
