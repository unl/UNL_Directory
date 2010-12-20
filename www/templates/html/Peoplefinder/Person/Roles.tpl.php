<?php
foreach ($context as $role) {
    if (!$org = UNL_Officefinder_Department::getByorg_unit($role->unlRoleHROrgUnitNumber)) {
        // Couldn't retrieve this org's record from officefinder
        continue;
    }
    $parent_name = 'University of Nebraska&ndash;Lincoln';
//    if ($context->unlHRPrimaryDepartment == 'Office of the President') {
//        $parent_name = 'University of Nebraska';
//    }
    $dept_url = $org->getURL();
    echo "<span class='org'><span class='title'>{$role->description}</span>\n\t<span class='organization-unit'><a href='{$dept_url}'>{$org->name}</a></span>\n\t<span class='organization-name'>$parent_name</span></span>\n";
}