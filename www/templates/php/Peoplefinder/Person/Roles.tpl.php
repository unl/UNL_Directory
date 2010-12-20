<?php
$roles = array();
foreach ($context->getRawObject() as $role) {
    $roles[] = $role;
}
echo serialize($roles);