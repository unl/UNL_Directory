<?php
require_once dirname(__FILE__).'/../www/config.inc.php';
error_reporting(E_ALL | E_STRICT);

$sap_dept = new UNL_Peoplefinder_Department(array('d'=>'University of Nebraska - Lincoln'));

if (!$root = UNL_Officefinder_Department::getByID(1)) {
    throw new Exception('Could not find the root element!');
}

updateOfficialDepartment($sap_dept);

function updateOfficialDepartment(UNL_Peoplefinder_Department $sap_dept, UNL_Officefinder_Department &$parent = null)
{

    if (!($dept = UNL_Officefinder_Department::getByorg_unit($sap_dept->org_unit))) {
        // Uhoh, new department!
        $dept = new UNL_Officefinder_Department();
        updateFields($dept, $sap_dept);
    }

    // Now update all fields with the official data from SAP
    //

    if ($parent) {
        if ($dept->isChildOf($parent)) {
            // All OK!
        } else {
            // This department has moved
            $parent->addChild($dept, true);
        }
    }

    if ($sap_dept->hasChildren()) {
        foreach ($sap_dept->getChildren() as $sap_sub_dept) {
            updateOfficialDepartment($sap_sub_dept, $dept);
        }
    }
}

function updateFields(UNL_Officefinder_Department $old, UNL_Peoplefinder_Department $new)
{
    foreach ($old as $key=>$val) {
        if (isset($new->$key)
            && $key != 'options') {
            $old->$key = $new->$key;
        }
        // Save it
        $old->save();
        if (!empty($new->org_abbr)) {
            $old->addAlias($new->org_abbr);
        }
    }
}