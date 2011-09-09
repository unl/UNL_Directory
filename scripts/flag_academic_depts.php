<?php

$academic_parent_ids = array();
$academic_parent_ids[] = 55;
$academic_parent_ids[] = 85;
$academic_parent_ids[] = 60;
$academic_parent_ids[] = 144;
$academic_parent_ids[] = 121;
$academic_parent_ids[] = 139;
$academic_parent_ids[] = 75;
$academic_parent_ids[] = 47;
$academic_parent_ids[] = 70;
$academic_parent_ids[] = 164;

$sql = 'UPDATE departments SET academic = 1 WHERE org_unit IS NOT NULL AND (';


foreach ($academic_parent_ids as $id) {
    $sql .= ' parent_id = '.$id.' OR id = '.$id. ' OR ';
}

$sql .= ' id = 0);';

echo $sql;


