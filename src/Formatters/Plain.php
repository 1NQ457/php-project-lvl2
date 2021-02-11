<?php

namespace Differ\Formatters\Plain;

use function Differ\Tree\getName;
use function Differ\Tree\getType;
use function Differ\Tree\getOldValue;
use function Differ\Tree\getNewValue;
use function Differ\Tree\getChildren;
use function Funct\Collection\flattenAll;

function boolToStr($value)
{
    if (is_bool($value)) {
        if ($value === true) {
            return 'true';
        }
        return 'false';
    }
    if (is_int($value)) {
        return $value;
    }
    return "'{$value}'";
}

function strFormat($value, $tab = '')
{
    if (!is_object($value)) {
        if ($value === null) {
            return 'null';
        }
        return boolToStr($value);
    }

    return '[complex value]';
}

function makeOutput($tree, $parentName = '')
{
    return array_reduce($tree, function ($result, $node) use ($parentName) {
        $name = trim(($parentName . '.' . getName($node)), ".");
        $type = getType($node);

        switch ($type) {
            case 'added':
                $newValue = getNewValue($node);
                $result[] = "Property '{$name}' was added with value: " . strFormat($newValue);
                break;
            case 'removed':
                $result[] = "Property '{$name}' was removed";
                break;
            case 'updated':
                $oldValue = strFormat(getOldValue($node));
                $newValue = strFormat(getNewValue($node));
                $result[] = "Property '{$name}' was updated. From {$oldValue} to {$newValue}";
                break;
            case 'nested':
                $children = getChildren($node);
                $result[] = makeOutput($children, $name);
                break;
        };
        return flattenAll($result);
    }, []);
}

function plainOutput($tree)
{
    $output = makeOutput($tree);
    $result = implode("\n", $output);
    return $result;
}
