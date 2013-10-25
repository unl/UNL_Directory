<?php
$permalink = UNL_Peoplefinder::getURL().'?uid='.$context->uid;

$preferredFirstName = $context->getPreferredFirstName();

echo "<div class='vcard {$context->eduPersonPrimaryAffiliation}'>\n";
echo '<a class="planetred_profile" href="http://planetred.unl.edu/pg/profile/unl_'.str_replace("-", "_", $context->uid).'" title="Planet Red Profile for '.$context->cn.'"><img class="profile_pic medium photo" src="'.htmlspecialchars($context->getImageURL()).'"  alt="Photo of '.$context->displayName.'" /></a> ';
echo '<div class="wdn_vcardTools">';
echo '<a href="'.UNL_Peoplefinder::getURL().'vcards/'.$context->uid.'" title="Download V-Card for '.$preferredFirstName.' '.$context->sn.'" class="text-vcard">vCard</a> ';
echo '<a title="QR Code vCard" href="http://chart.apis.google.com/chart?chs=400x400&amp;cht=qr&amp;chl=' . urlencode($savvy->render($context, 'templates/vcard/Peoplefinder/Record.tpl.php')) . '&amp;chld=L|1&amp;.png" class="img-qrcode">QR Code</a> ';
if (!$parent->context->options['mobile']) {
    echo '<a title="Print this listing" href="'.$permalink.'&amp;print" class="print">Print</a> ';
}
echo '<div class="wdn_annotate" id="directory_'.$context->uid.'"></div>';
echo '</div>'; //Close the tools div.

echo '<div class="vcardInfo">'.PHP_EOL;
$displayEmail = false;
if (isset($context->mail)
    && ($context->eduPersonPrimaryAffiliation != 'student')) {
    $displayEmail = true;
}
if ($displayEmail) {
    echo "<a class='email' href='mailto:{$context->mail}'>";
}
if ($context->ou == 'org') {
    echo '<span class="cn">'.$context->cn.'</span>';
} else {
    echo '<span class="fn">'.$preferredFirstName.' '.$context->sn.'</span>';
    if (!empty($context->eduPersonNickname)
        && $context->eduPersonNickname != ' ') {
        echo ' <span class="givenName">'.$context->givenName.'</span>';
    }
}
if ($displayEmail) {
    echo "</a>\n";
}
if ($context->ou != 'org') {
    echo ' <a class="permalink" href="'.$permalink.'" title="Permalink for '.$context->displayName.'">link</a>';
}

if (isset($context->eduPersonAffiliation)) {
    $affiliations = array_intersect(UNL_Peoplefinder::$displayedAffiliations, $context->getRaw('eduPersonAffiliation')->getArrayCopy());
    echo '<span class="eppa">('.implode(', ', $affiliations).')</span>';
}

if (isset($context->unlSISClassLevel)) {
    switch ($context->unlSISClassLevel) {
        case 'FR':
            $class = 'Freshman';
            break;
        case 'SR':
            $class = 'Senior';
            break;
        case 'SO':
            $class = 'Sophomore';
            break;
        case 'JR':
            $class = 'Junior';
            break;
        case 'GR':
            $class = 'Graduate Student';
            break;
        default:
            $class = $context->unlSISClassLevel;
    }
    echo '<span class="title"><span class="grade">'.$class.'</span> ';
    if (isset($context->unlSISMajor)) {
        foreach ($context->unlSISMajor as $major) {
            echo '<span class="major">'.$context->formatMajor($major).'</span> ';// <span class="college">'.$context->formatCollege((string) $context->unlSISCollege).'</span>';
        }
    }
    if (isset($context->unlSISMinor)) {
        foreach ($context->unlSISMinor as $minor) {
            echo '<span class="minor">'.$context->formatMajor($minor).'</span> ';
        }
    }
    echo '</span>';
}

if (isset($context->unlHROrgUnitNumber)) {
    $roles = $parent->context->getRoles($context->dn);
    
    if (count($roles)) {
        echo $savvy->render($roles);
    }

}

if (isset($context->postalAddress)) {
    $address = $context->formatPostalAddress();

    echo '<div class="adr workAdr">
         <span class="type">Work</span>';
        if (strpos($address['postal-code'], '68588') == 0) {
            if ($code = $context->getUNLBuildingCode()) {
                echo '<span class="street-address">'. str_replace($code, '<a class="location mapurl" href="http://maps.unl.edu/#'.$code.'">'.$code.'</a>', $address['street-address']) . '</span>';
            }
        } else {
            echo '<span class="street-address">'. $address['street-address'] . '</span>';
        }
        echo '
         <span class="locality">' . $address['locality'] . '</span>
         <span class="region">' . $address['region'] . '</span>
         <span class="postal-code">' . $address['postal-code'] . '</span>
         <div class="country-name">USA</div>
        </div>'.PHP_EOL;
}

if (isset($context->telephoneNumber)) {

    echo '<div class="tel workTel">
             <span class="voice">
             <span class="type">Work</span>
             <span class="value">'.$savvy->render($context->telephoneNumber, 'Peoplefinder/Record/TelephoneNumber.tpl.php').'</span>
             </span>
            </div>'.PHP_EOL;
}

if (isset($context->unlSISLocalPhone)) {
    echo '<div class="tel homeTel">
             <span class="voice">
             <span class="type">Phone</span>
             <span class="value">'.$savvy->render($context->unlSISLocalPhone, 'Peoplefinder/Record/TelephoneNumber.tpl.php').'</span>
             </span>
            </div>'.PHP_EOL;
}

if ($displayEmail) {
    echo "<span class='email'><a class='email' href='mailto:{$context->mail}'>{$context->mail}</a></span>\n";
}
echo '</div>'.PHP_EOL.'</div>'.PHP_EOL;