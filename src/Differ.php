<?php

namespace Differ\Differ;

use function Differ\Tree\makeLeaf;
use function Differ\Tree\makeNode;
use function Differ\Parser\getData;
use function Differ\Formatters\Stylish\stylishOutput;
use function Differ\Formatters\Plain\plainOutput;
use function Differ\Formatters\Json\json;
use function Funct\Collection\sortBy;

function makeTree($anyTypeBefore, $anyTypeAfter): array
{
    $before = (array) $anyTypeBefore;
    $after = (array) $anyTypeAfter;
    $unsortedKeys = array_keys(array_merge($before, $after));
    $keys = array_values(sortBy($unsortedKeys, fn ($value) => $value));

    return array_map(function ($key) use ($before, $after): array {
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

function getSorted($arr): array
{
    $sortedArr = sortBy($arr, fn ($value) => $value);
    return $sortedArr;
}

function genDiff($pathToBefore, $pathToAfter, $format = 'stylish'): string
{
    [$before, $after] = getData($pathToBefore, $pathToAfter);
    $tree = makeTree($before, $after);
    $formatters = [
        'stylish' =>
            fn ($tree): string => stylishOutput($tree),
        'plain' =>
            fn ($tree): string => plainOutput($tree),
        'json' =>
            fn ($tree): string => json($tree)
    ];
    return $formatters[$format]($tree);
}
