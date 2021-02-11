<?php

namespace Differ\Tree;

function makeLeaf($name, $type, $oldValue, $newValue)
{
    return [
        "name" => $name,
        "type" => $type,
        "oldValue" => $oldValue,
        "newValue" => $newValue
    ];
}

function makeNode(string $name, string $type, $children)
{
    return [
        "name" => $name,
        "type" => $type,
        "children" => $children
    ];
}

function getName($node)
{
    return $node['name'];
}

function getType($node)
{
    return $node['type'];
}

function getOldValue($node)
{
    return $node['oldValue'];
}

function getNewValue($node)
{
    return $node['newValue'];
}

function getChildren($node)
{
    return $node['children'];
}
