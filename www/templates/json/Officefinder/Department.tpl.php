<?php

$dept = new stdClass;
$dept->id = $context->id;
$dept->name = $context->name;
$dept->org_unit = $context->org_unit;
$dept->building = $context->building;
$dept->room = $context->room;
$dept->city = $context->city;
$dept->state = $context->state;
$dept->postal_code = $context->postal_code;
$dept->address = $context->address;
$dept->phone = $context->phone;
$dept->fax = $context->fax;
$dept->email = $context->email;
$dept->website = $context->website;
$dept->academic = $context->academic;

$json = json_encode($dept);

if ($context->hasOfficialChildDepartments()) {
    $children = $context->getOfficialChildDepartments();
    $children_json = $savvy->render($children);
    $json = rtrim($json, '}').', "children":['.$children_json.']}';
}

if ($context->hasUnofficialChildDepartments()) {
    $children = $context->getUnofficialChildDepartments();
    $children_json = $savvy->render($children);
    $json = rtrim($json, '}').', "unofficial_children":['.$children_json.']}';
}

echo $json;
