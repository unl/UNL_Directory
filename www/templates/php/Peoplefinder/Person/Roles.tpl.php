<?php
$roles = array();
foreach ($context as $role) {
    $roles[] = $role;
}
echo serialize($roles);