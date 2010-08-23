<?php
echo "<div class='vcard {$context->eduPersonPrimaryAffiliation}'>\n";
echo '<a class="planetred_profile" href="http://planetred.unl.edu/pg/profile/unl_'.str_replace("-", "_", $context->uid).'" title="Planet Red Profile for '.$context->cn.'"><img class="profile_pic medium photo" src="'.htmlspecialchars($context->getImageURL()).'"  alt="Photo of '.$context->displayName.'" /></a>';

echo '<a href="'.UNL_Peoplefinder::getURL().'vcards/'.$context->uid.'" title="Download V-Card for '.$context->givenName.' '.$context->sn.'" class="text-vcard">vCard</a>';

echo '<div class="vcardInfo">'.PHP_EOL;

if (isset($context->mail)
    && ($context->eduPersonPrimaryAffiliation != 'student')) {
    $displayEmail = true;
} else {
    $displayEmail = false;
}
if ($displayEmail && isset($context->mail)) echo "<a class='email' href='mailto:{$context->mail}'>";
if ($context->ou == 'org') {
    echo '<span class="cn">'.$context->cn.'</span>'.PHP_EOL;
} else {
    echo '<span class="fn">'.$context->displayName.'</span>'.PHP_EOL;
    if (isset($context->eduPersonNickname)) echo '<span class="nickname">'.$context->eduPersonNickname.'</span>'.PHP_EOL;
}
if ($displayEmail && isset($context->unlEmailAlias)) echo "</a>\n";

if (isset($context->eduPersonAffiliation)) {
    echo '<span class="eppa">('.implode(', ', $context->eduPersonAffiliation->getArrayCopy()).')</span>';
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
    echo '<span class="title"><span class="grade">'.$class.'</span>, <span class="major">'.$context->formatMajor($context->unlSISMajor).'</span> &ndash; <span class="college">'.$context->formatCollege((string) $context->unlSISCollege).'</span></span>';
}

if (isset($context->title)) {
    echo "<span class='title'>{$context->title}</span>\n";
}

if (isset($context->unlHRPrimaryDepartment)) {
    $org_name = 'University of Nebraska&ndash;Lincoln';
    if ($context->unlHRPrimaryDepartment == 'Office of the President') {
        $org_name = 'University of Nebraska';
    }
    $dept_url = UNL_Officefinder::getURL().'?d='.urlencode($context->unlHRPrimaryDepartment);
    echo "<span class='org'>\n\t<span class='organization-unit'><a href='{$dept_url}'>{$context->unlHRPrimaryDepartment}</a></span>\n\t<span class='organization-name'>$org_name</span></span>\n";
}

if (isset($context->postalAddress)) {
    $address = $context->formatPostalAddress();

    if (strpos($address['postal-code'], '68588') == 0) {
        $regex = "/([A-Za-z0-9].) ([A-Z0-9\&]{2,4})/" ; //& is for M&N Building

        if (preg_match($regex, $address['street-address'], $matches)) {
            $bldgs = new UNL_Common_Building();

            if ($bldgs->buildingExists($matches[2])) {

                $replace = '${1} <a class="location mapurl" href="http://www1.unl.edu/tour/${2}">${2}</a>';
                $address['street-address'] = preg_replace($regex, $replace, $address['street-address']);
            }
        }
    }

    echo '<div class="adr workAdr">
         <span class="type">Work</span>
         <span class="street-address">'. $address['street-address'] . '</span>
         <span class="locality">' . $address['locality'] . '</span>
         <span class="region">' . $address['region'] . '</span>
         <span class="postal-code">' . $address['postal-code'] . '</span>
         <div class="country-name">USA</div>
        </div>'.PHP_EOL;
}

if (isset($context->telephoneNumber)) {
    
    echo '<div class="tel workTel">
             <span class="type">Work</span>
             <span class="value">'.$savvy->render($context->telephoneNumber, 'Peoplefinder/Record/TelephoneNumber.tpl.php').'</span>
            </div>'.PHP_EOL;
}

if (isset($context->unlSISLocalPhone)) {
    echo '<div class="tel homeTel">
             <span class="type">Phone</span>
             <span class="value">'.$savvy->render($context->unlSISLocalPhone, 'Peoplefinder/Record/TelephoneNumber.tpl.php').'</span>
            </div>'.PHP_EOL;
}

if ($displayEmail) {
    echo "<span class='email'><a class='email' href='mailto:{$context->mail}'>{$context->mail}</a></span>\n";
}

echo '';
echo '</div>'.PHP_EOL.'</div>'.PHP_EOL;