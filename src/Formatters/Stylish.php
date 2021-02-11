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
    return array_reduce($tree, function ($result, $node) use ($tab) {
        $name = getName($node);
        $type = getType($node);
        switch ($type) {
            case 'added':
                $result[] = $tab . "  + {$name}: " . strFormat(getNewValue($node), $tab . "    ");
                break;
            case 'removed':
                $result[] = $tab . "  - {$name}: " . strFormat(getOldValue($node), $tab . "    ");
                break;
            case 'updated':
                $result[] = $tab . "  - {$name}: " . strFormat(getOldValue($node), $tab . "    ");
                $result[] = $tab . "  + {$name}: " . strFormat(getNewValue($node), $tab . "    ");
                break;
            case 'notChanged':
                $result[] = $tab . "    {$name}: " . strFormat(getOldValue($node), $tab . "    ");
                break;
            case 'nested':
                $result[] = $tab . "    {$name}: {";
                $result[] = makeOutput(getChildren($node), $tab . "    ");
                $result[] = $tab . '    }';
                break;
        };
        return flattenAll($result);
    }, []);
}

function stylishOutput($tree): string
{
    $output = makeOutput($tree);
    $result = implode("\n", $output);
    return "{\n" . $result . "\n}";
}
