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
                return $tab . "  + {$name}: " . strFormat(getNewValue($node), $tab . "    ");
                break;
            case 'removed':
                return $tab . "  - {$name}: " . strFormat(getOldValue($node), $tab . "    ");
                break;
            case 'updated':
                $updeted = [];
                $updated[] = $tab . "  - {$name}: " . strFormat(getOldValue($node), $tab . "    ");
                $updated[] = $tab . "  + {$name}: " . strFormat(getNewValue($node), $tab . "    ");
                return $updated;
                break;
            case 'notChanged':
                return $tab . "    {$name}: " . strFormat(getOldValue($node), $tab . "    ");
                break;
            case 'nested':
                $nested = [];
                $nested[] = $tab . "    {$name}: {";
                $nested[] = makeOutput(getChildren($node), $tab . "    ");
                $nested[] = $tab . '    }';
                return $nested;
                break;
        };
    }, $tree);
    return flattenAll($result);
}

function stylishOutput($tree): string
{
    $output = makeOutput($tree);
    $result = implode("\n", $output);
    return "{\n" . $result . "\n}";
}
