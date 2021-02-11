<?php

namespace Differ\Formatters\Stylish;

use function Funct\Collection\flattenAll;
use function Differ\Tree\getName;
use function Differ\Tree\getType;
use function Differ\Tree\getValue;
use function Differ\Tree\getChildren;

function boolToStr($value): string
{
    if (is_bool($value)) {
        if ($value === true) {
            return 'true';
        }
        return 'false';
    }
    return (string) $value;
}

function strFormat($value, $tab = ''): string
{
    if (!is_object($value)) {
        if ($value === null) {
            return 'null';
        }
        return boolToStr($value);
    }
    $arr = (array) ($value);
    $result = implode('', array_map(function ($key, $value) use ($tab): string {
        return "\n" . $tab . "    {$key}: " . strFormat($value, $tab . '    ');
    }, array_keys($arr), $arr));
    return '{' . $result . "\n" . $tab . '}';
}

function makeOutput($tree, $tab = ''): array
{
    $result = array_map(function ($node) use ($tab): array {
        $name = getName($node);
        $type = getType($node);
        switch ($type) {
            case 'added':
                return createAddedString($tab, $name, $node);
            case 'removed':
                return createRemovedString($tab, $name, $node);
            case 'updated':
                return createUpdatedSting($tab, $name, $node);
            case 'notChanged':
                return createNotChangedString($tab, $name, $node);
            case 'nested':
                return createFromeNode($tab, $name, $node);
            default:
                return [''];
        };
    }, $tree);
    return flattenAll($result);
}

function createAddedString($tab, $name, $node): array
{
    $added = [$tab . "  + {$name}: " . strFormat(getValue($node)['new'], $tab . "    ")];
    return $added;
}

function createRemovedString($tab, $name, $node): array
{
    $removed = [$tab . "  - {$name}: " . strFormat(getValue($node)['old'], $tab . "    ")];
    return $removed;
}

function createUpdatedSting($tab, $name, $node): array
{
    $updated = [$tab . "  - {$name}: " . strFormat(getValue($node)['old'], $tab . "    "),
        $tab . "  + {$name}: " . strFormat(getValue($node)['new'], $tab . "    ")
    ];
    return $updated;
}

function createNotChangedString($tab, $name, $node): array
{
    $notChanged = [$tab . "    {$name}: " . strFormat(getValue($node)['old'], $tab . "    ")];
    return $notChanged;
}

function createFromeNode($tab, $name, $node): array
{
    $nested = [
        $tab . "    {$name}: {",
        makeOutput(getChildren($node), $tab . "    "),
        $tab . '    }'
    ];
    return $nested;
}

function stylishOutput($tree): string
{
    $output = makeOutput($tree);
    $result = implode("\n", $output);
    return "{\n" . $result . "\n}";
}
