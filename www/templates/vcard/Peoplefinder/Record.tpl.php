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
//echo "TEL;type=CELL:(402) 555-1111\n";
if (isset($context->unlSISLocalPhone)) {
    echo "TEL;type=HOME:{$context->unlSISLocalPhone}\n";
}
if (isset($context->unlSISLocalAddr1)) {
    echo "item1.ADR;type=WORK;type=pref:;;".$context->unlSISLocalAddr1;
    if (isset($context->unlSISLocalAddr2)) {
        echo "\\n".$context->unlSISLocalAddr2;
    }
    echo ";".$context->unlSISLocalCity.";".$context->unlSISLocalState.";".$context->unlSISLocalZip.";\n";
    echo "item1.X-ABLabel:local\n";
}
if (isset($context->unlSISPermaddr1)) {
    echo "item2.ADR;type=HOME;type=pref:;;".$context->unlSISPermAddr1;
    if (isset($context->unlSISPermAddr2)) {
        echo "\\n".$context->unlSISPermAddr2;
    }
    echo ";".@$context->unlSISPermCity.";".@$context->unlSISPermState.";".@$context->unlSISPermZip.";\n";
    echo "item2.X-ABLabel:permanent\n";
}
//echo "item1.X-ABADR:us\n";
//echo "item2.X-ABADR:us\n";
//echo "URL:http://www.unl.edu/\n";
//echo "LOGO;VALUE=uri:http://www.unl.edu/unlpub/2004sharedgraphics/smcolor_wordmark.gif";
if (isset($context->title)) {
    echo "item3.X-ABRELATEDNAMES;type=pref:".$context->title."\n";
    echo "item3.X-ABLabel:title\n";
}
echo "END:VCARD\n";
