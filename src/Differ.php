<?php

namespace Gendiff\Differ;

use function Gendiff\Tree\makeLeaf;
use function Gendiff\Tree\makeNode;
use function Gendiff\Parser\getData;
use function Gendiff\Formatters\Stylish\stylishOutput;
use function Gendiff\Formatters\Plain\plainOutput;

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
            return makeLeaf($key, 'removed', $before[$key], null);
        }
        if (is_object($before[$key]) && (is_object($after[$key]))) {
            return makeNode($key, 'nested', makeTree($before[$key], $after[$key]));
        };
        if ($before[$key] !== $after[$key]) {
            return makeLeaf($key, 'updated', $before[$key], $after[$key]);
        }
        return makeLeaf($key, 'notChanged', $before[$key], $after[$key]);
    }, $keys);
}

function gendiff($pathToBefore, $pathToAfter, $format = 'stylish')
{
    [$before, $after] = getData($pathToBefore, $pathToAfter);
    $tree = makeTree($before, $after);
    $formatters = [
        'stylish' =>
            fn ($tree) => stylishOutput($tree),
        'plain' =>
            fn ($tree) => plainOutput($tree)
    ];
    return $formatters[$format]($tree);
}
