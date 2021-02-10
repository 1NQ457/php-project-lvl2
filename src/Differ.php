<?php

namespace Gendiff\Differ;

use function Gendiff\Tree\makeLeaf;
use function Gendiff\Tree\getName;
use function Gendiff\Tree\getType;
use function Gendiff\Tree\getOldValue;
use function Gendiff\Tree\getNewValue;
use function GenDiff\Parser\getData;

function boolToString($value)
{
    if (is_bool($value)) {
        if ($value === true) {
            return 'true';
        }
        return 'false';
    }
    return $value;
}

function makeTree($before, $after)
{
    $keys = array_keys(array_merge($before, $after));
    sort($keys);

    return array_map(function ($key) use ($before, $after) {
        if (!array_key_exists($key, $before)) {
            return makeLeaf($key, 'added', null, $after[$key]);
        }
        if (!array_key_exists($key, $after)) {
            return makeLeaf($key, 'deleted', $before[$key], null);
        }
        if ($before[$key] !== $after[$key]) {
            return makeLeaf($key, 'changed', $before[$key], $after[$key]);
        }
        return makeLeaf($key, 'notChanged', $before[$key], $after[$key]);
    }, $keys);
}

function makeOutput($tree)
{
    return array_reduce($tree, function ($result, $node) {
        $name = getName($node);
        $type = getType($node);

        switch ($type) {
            case 'added':
                $newValue = getNewValue($node);
                $result[] = "  + {$name}: " . boolToString($newValue);
                break;
            case 'deleted':
                $oldValue = getOldValue($node);
                $result[] = "  - {$name}: " . boolToString($oldValue);
                break;
            case 'changed':
                $oldValue = getOldValue($node);
                $newValue = getNewValue($node);
                $result[] = "  - {$name}: " . boolToString($oldValue);
                $result[] = "  + {$name}: " . boolToString($newValue);
                break;
            case 'notChanged':
                $value = getOldValue($node);
                $result[] = "    {$name}: " . boolToString($value);
                break;
        };
        return $result;
    }, []);
}

function gendiff($pathToBefore, $pathToAfter)
{
    [$before, $after] = getData($pathToBefore, $pathToAfter);
    $tree = makeTree($before, $after);
    $output = makeOutput($tree);
    $result = implode("\n", $output);

    return "{\n" . $result . "\n}\n";
}
