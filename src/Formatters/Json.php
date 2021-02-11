<?php

namespace Differ\Formatters\Json;

function json($tree): string
{
    return (string) json_encode($tree, JSON_PRETTY_PRINT);
}
