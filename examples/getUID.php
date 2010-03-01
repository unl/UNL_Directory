<?php
require_once 'config.inc.php';

$pf = new UNL_Peoplefinder($driver);

$uid = $pf->getUID('bbieber2');

echo 'uid:'   . $uid->uid;
echo ' cn:'   . $uid->cn;
echo ' mail:' . $uid->mail;
echo ' department:' . $uid->unlHRPrimaryDepartment;
foreach ($uid->eduPersonAffiliation as $affiliation) {
    echo ' affiliation:' . $affiliation;
}