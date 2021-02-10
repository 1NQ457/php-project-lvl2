<?php

namespace Gendiff\Tree;

function makeLeaf($name, $type, $oldValue, $newValue)
{
    return [
        "name" => $name,
        "type" => $type,
        "oldValue" => $oldValue,
        "newValue" => $newValue
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
