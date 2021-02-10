<?php

namespace Gendiff\Differ;

use function Gendiff\Tree\makeLeaf;
use function Gendiff\Tree\makeNode;
use function GenDiff\Parser\getData;
use function Gendiff\Formatters\Stylish\makeOutput;

function makeTree($before, $after)
{
    $before = (array) $before;
    $after = (array) $after;
    $keys = array_keys(array_merge($before, $after));
    sort($keys);

    return array_map(function ($key) use ($before, $after) {
        if (!array_key_exists($key, $before)) {
            return makeLeaf($key, 'added', null, $after[$key]);
        }
        if (!array_key_exists($key, $after)) {
            return makeLeaf($key, 'deleted', $before[$key], null);
        }
        if (is_object($before[$key]) && (is_object($after[$key]))) {
            return makeNode($key, 'nested', makeTree($before[$key], $after[$key]));
        };
        if ($before[$key] !== $after[$key]) {
            return makeLeaf($key, 'changed', $before[$key], $after[$key]);
        }
        return makeLeaf($key, 'notChanged', $before[$key], $after[$key]);
    }, $keys);
}

function gendiff($pathToBefore, $pathToAfter)
{
    [$before, $after] = getData($pathToBefore, $pathToAfter);
    $tree = makeTree($before, $after);
    $output = makeOutput($tree, '');
    $result = implode("\n", $output);

    return "{\n" . $result . "\n}\n";
}
