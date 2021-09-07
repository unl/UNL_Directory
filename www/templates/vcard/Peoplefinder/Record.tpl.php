<?php
//connect, taking in UID
echo "BEGIN:VCARD\n";
echo "VERSION:3.0\n";
echo "N:".$context->sn.";".$context->givenName.";;;\n";
echo "FN:".$context->givenName." ".$context->sn."\n";
if (isset($context->unlHROrgUnitNumber)) {
    foreach ($context->unlHROrgUnitNumber as $orgUnit) {
        if (!$org = UNL_Officefinder_Department::getByorg_unit($orgUnit)) {
            // Couldn't retrieve this org's record from officefinder
            continue;
        }
        echo "ORG:University of Nebraska-Lincoln;".$org->name."\n";
    }
    
}
if (isset($context->mail) && ($context->eduPersonPrimaryAffiliation != 'student')) {
    echo "EMAIL;type=INTERNET;type=WORK;type=pref:".$context->mail."\n";
}
if ($context->eduPersonPrimaryAffiliation != "student") {
    echo "TEL;type=WORK;type=pref:".$context->telephoneNumber."\n";
}
if (isset($context->title)) {
    echo "item3.X-ABRELATEDNAMES;type=pref:".$context->title."\n";
    echo "item3.X-ABLabel:title\n";
}
echo "END:VCARD\n";
