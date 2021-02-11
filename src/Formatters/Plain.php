<?php

namespace Differ\Formatters\Plain;

use function Differ\Tree\getName;
use function Differ\Tree\getType;
use function Differ\Tree\getOldValue;
use function Differ\Tree\getNewValue;
use function Differ\Tree\getChildren;
use function Funct\Collection\flattenAll;

function boolToStr($value): string
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

function strFormat($value, $tab = ''): string
{
    if (!is_object($value)) {
        if ($value === null) {
            return 'null';
        }
        return boolToStr($value);
    }

    return '[complex value]';
}

function makeOutput($tree, $parentName = ''): array
{
    $result = array_map(function ($node) use ($parentName) {
        $name = trim(($parentName . '.' . getName($node)), ".");
        $type = getType($node);
        switch ($type) {
            case 'added':
                return createAddedString($name, $node);
                break;
            case 'removed':
                return createRemovedString($name);
                break;
            case 'updated':
                return createUpdatedString($name, $node);
                break;
            case 'nested':
                return createNestedString($name, $node);
                break;
        };
    }, $tree);
    return flatten($result);
}

function createAddedString($name, $node)
{
    $newValue = strFormat(getNewValue($node));
    $added = "Property '{$name}' was added with value: " . $newValue;
    return $added;
}

function createRemovedString($name)
{
    $removed = "Property '{$name}' was removed";
    return $removed;
}

function createUpdatedString($name, $node)
{
    $oldValue = strFormat(getOldValue($node));
    $newValue = strFormat(getNewValue($node));
    $updated = "Property '{$name}' was updated. From {$oldValue} to {$newValue}";
    return $updated;
}

function createNestedString($name, $node)
{
    $children = getChildren($node);
    return makeOutput($children, $name);
}

function flatten($arr)
{
    return array_filter(flattenAll($arr), function ($element) {
        return !empty($element);
    });
}

function plainOutput($tree)
{

    $output = makeOutput($tree);
    $result = implode("\n", $output);
    return $result;
}
