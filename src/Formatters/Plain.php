<?php

namespace Gendiff\Formatters\Plain;

use function Gendiff\Tree\getName;
use function Gendiff\Tree\getType;
use function Gendiff\Tree\getOldValue;
use function Gendiff\Tree\getNewValue;
use function Gendiff\Tree\getChildren;
use function Funct\Collection\flattenAll;

function boolToStr($value)
{
    if (is_bool($value)) {
        if ($value === true) {
            return 'true';
        }
        return 'false';
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
    return $result . "\n";
}
