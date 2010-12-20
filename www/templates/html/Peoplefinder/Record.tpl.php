<?php
echo "<div class='vcard {$context->eduPersonPrimaryAffiliation}'>\n";
echo '<a class="planetred_profile" href="http://planetred.unl.edu/pg/profile/unl_'.str_replace("-", "_", $context->uid).'" title="Planet Red Profile for '.$context->cn.'"><img class="profile_pic medium photo" src="'.htmlspecialchars($context->getImageURL()).'"  alt="Photo of '.$context->displayName.'" /></a> ';

echo '<a href="'.UNL_Peoplefinder::getURL().'vcards/'.$context->uid.'" title="Download V-Card for '.$context->givenName.' '.$context->sn.'" class="text-vcard">vCard</a> ';
echo '<a title="QR Code vCard" href="http://chart.apis.google.com/chart?chs=400x400&amp;cht=qr&amp;chl=' . urlencode($savvy->render($context, 'templates/vcard/Peoplefinder/Record.tpl.php')) . '&amp;chld=L|1&amp;.png" class="img-qrcode">QR Code</a> ';
echo '<div class="wdn_annotate" id="directory_'.$context->uid.'"></div>';

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
    echo '<span class="cn">'.$context->cn.'</span>'.PHP_EOL;
} else {
    echo '<span class="fn">'.$context->displayName.'</span>'.PHP_EOL;
    if (!empty($context->eduPersonNickname)
        && $context->eduPersonNickname != ' ') {
        echo ' ('.$context->eduPersonNickname.')';
    }
    echo '<a class="permalink" href="'.UNL_Peoplefinder::getURL().'?uid='.$context->uid.'" title="Permalink for '.$context->displayName.'">link</a>';
}
if ($displayEmail) {
    echo "</a>\n";
}

if (isset($context->eduPersonAffiliation)) {
    $affiliations = array_intersect(UNL_Peoplefinder::$displayedAffiliations, $context->eduPersonAffiliation->getArrayCopy());
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
    echo '<span class="title"><span class="grade">'.$class.'</span>, <span class="major">'.$context->formatMajor($context->unlSISMajor).'</span> &ndash; <span class="college">'.$context->formatCollege((string) $context->unlSISCollege).'</span></span>';
}

if (isset($context->unlHROrgUnitNumber)) {
    
    $roles = $parent->context->getRoles($context->dn);
    echo $savvy->render($roles);
    
}

if (isset($context->postalAddress)) {
    $address = $context->formatPostalAddress();

    if (strpos($address['postal-code'], '68588') == 0) {
        $regex = "/([A-Za-z0-9].) ([A-Z0-9\&]{2,4})/" ; //& is for M&N Building

        if (preg_match($regex, $address['street-address'], $matches)) {
            $bldgs = new UNL_Common_Building();

            if ($bldgs->buildingExists($matches[2])) {

                $replace = '${1} <a class="location mapurl" href="http://maps.unl.edu/#${2}">${2}</a>';
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
echo '<a href="" class="dir_correctionRequest">Have a correction?</a>
	<div class="commentProblem">
	<h3>Have a correction?</h3>
	<form method="post" action="http://www1.unl.edu/comments/">
		<input type="hidden" name="page_address" value="" />
		<textarea name="comment" id="comment" rows="" cols=""></textarea>
		<input type="submit" value="Submit" />
	</form>
</div>';
echo '';
echo '</div>'.PHP_EOL.'</div>'.PHP_EOL;