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
    $result = array_map(function ($node) use ($parentName): array {
        $name = trim(($parentName . '.' . getName($node)), ".");
        $type = getType($node);
        switch ($type) {
            case 'added':
                return createAddedString($name, $node);
            case 'removed':
                return createRemovedString($name);
            case 'updated':
                return createUpdatedString($name, $node);
            case 'nested':
                return createNestedString($name, $node);
            case 'notChanged':
                return [];
        };
    }, $tree);
    return flatten($result);
}

function createAddedString($name, $node): array
{
    $newValue = strFormat(getNewValue($node));
    $added = ["Property '{$name}' was added with value: " . $newValue];
    return $added;
}

function createRemovedString($name): array
{
    $removed = ["Property '{$name}' was removed"];
    return $removed;
}

function createUpdatedString($name, $node): array
{
    $oldValue = strFormat(getOldValue($node));
    $newValue = strFormat(getNewValue($node));
    $updated = ["Property '{$name}' was updated. From {$oldValue} to {$newValue}"];
    return $updated;
}

function createNestedString($name, $node): array
{
    $children = getChildren($node);
    return makeOutput($children, $name);
}

function flatten($arr): array
{
    return array_filter(flattenAll($arr), function ($element) {
        return !empty($element);
    });
}

function plainOutput($tree): string
{

    $output = makeOutput($tree);
    $result = implode("\n", $output);
    return $result;
}
