<?php

$dept = new stdClass;
$dept->name = $context->name;
$dept->org_unit = $context->org_unit;

$json = json_encode($dept);

if ($context->hasOfficialChildDepartments()) {
    $children = $context->getOfficialChildDepartments();
    $children_json = $savvy->render($children);
    $json = rtrim($json, '}').', "children":['.$children_json.']}';
}

echo $json;
