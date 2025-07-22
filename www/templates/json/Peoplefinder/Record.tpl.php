<?php
unset($context->ou);

$ip = '';
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

/**
 * This comes from UNL_Peoplefinder_Person_Roles
 */
function isDisplayableRole($role) {
    if ($role->unlRoleHROrgUnitNumber == UNL_Peoplefinder::ORG_UNIT_NUMBER_RETIREE ||
        strtolower($role->description) == 'retiree') {
        return false;
    }
    return true;
}

// Get all the roles
$roles = $context->getRoles();
foreach ($roles as $single_role) {
    if (!isDisplayableRole($single_role)) { continue; }
    $context->appointments[] = $single_role;
}

if (empty($ip) || !in_array($ip, UNL_Peoplefinder::$allowed_unluncwid_IPs)) {
    unset($context->unluncwid);
}

echo json_encode($context);
