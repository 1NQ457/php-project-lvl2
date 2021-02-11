<?php

namespace Differ\Formatters\Stylish;

use function Funct\Collection\flattenAll;
use function Differ\Tree\getName;
use function Differ\Tree\getType;
use function Differ\Tree\getOldValue;
use function Differ\Tree\getNewValue;
use function Differ\Tree\getChildren;

function boolToStr($value): string
{
    if (is_bool($value)) {
        if ($value === true) {
            return 'true';
        }
        return 'false';
    }
    return $value;
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
    $result = array_map(function ($node) use ($tab) {
        $name = getName($node);
        $type = getType($node);
        switch ($type) {
            case 'added':
                return createAddedString($tab, $name, $node);
                break;
            case 'removed':
                return $tab . "  - {$name}: " . strFormat(getOldValue($node), $tab . "    ");
                break;
            case 'updated':
                return createUpdatedSting($tab, $name, $node);
                break;
            case 'notChanged':
                return createNotChangedString($tab, $name, $node);
                break;
            case 'nested':
                return createFromeNode($tab, $name, $node);
                break;
        };
    }, $tree);
    return flattenAll($result);
}

function createAddedString($tab, $name, $node)
{
    $added = $tab . "  + {$name}: " . strFormat(getNewValue($node), $tab . "    ");
    return $added;
}

function createRemovedString($tab, $name, $node)
{
    $removed = $tab . "  - {$name}: " . strFormat(getOldValue($node), $tab . "    ");
    return $removed;
}

function createUpdatedSting($tab, $name, $node)
{
    $updated = [$tab . "  - {$name}: " . strFormat(getOldValue($node), $tab . "    "),
        $tab . "  + {$name}: " . strFormat(getNewValue($node), $tab . "    ")
    ];
    return $updated;
}

function createNotChangedString($tab, $name, $node)
{
    $notChanged = $tab . "    {$name}: " . strFormat(getOldValue($node), $tab . "    ");
    return $notChanged;
}

function createFromeNode($tab, $name, $node)
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
