<?php
if ($context->ou == 'org') {
    $name = $context->cn;
} else {
    $name = $context->sn . ',&nbsp;'. $context->givenName;
    if (isset($context->eduPersonNickname)) {
        $name .= ' "'.$context->eduPersonNickname.'"';
    }
}
$onclick = '';
if (isset($context->uid_onclick)) {
    $onclick .= ' onclick="return '.$context->uid_onclick.'(\''.$uid.'\');"';
}
echo '<a class="planetred_profile" href="http://planetred.unl.edu/pg/profile/unl_'.str_replace("-", "_", $context->uid).'" title="Planet Red Profile for '.$context->cn.'"><img class="profile_pic small" src="http://planetred.unl.edu/mod/profile/icondirect.php?username=unl_'.str_replace("-", "_", $context->uid).'&amp;size=small"  alt="Photo of '.$context->displayName.'" /></a>';
echo '<div class="recordDetails">';
echo '<div class="fn"><a href="'.UNL_Peoplefinder::getURL().'?uid='.$context->uid.'" '.$onclick.'>'.$name.'</a></div>'.PHP_EOL;
if (isset($context->eduPersonPrimaryAffiliation)) {
    echo '<div class="eppa">('.$context->eduPersonPrimaryAffiliation.')</div>'.PHP_EOL;
}
if (isset($context->unlHRPrimaryDepartment)) {
    echo '<div class="organization-unit">'.$context->unlHRPrimaryDepartment.'</div>'.PHP_EOL;
}
if (isset($context->title)) {
    echo '<div class="title">'.$context->title.'</div>'.PHP_EOL;
}
if (isset($context->telephoneNumber)) {
    $link = '<a href="';
    if (strpos($_SERVER['HTTP_USER_AGENT'], "iPhone") === false) {
        $link .= "wtai://wp/mc;".str_replace(array("(", ")", "-"), "", $phone);
    } else {
        $link .= "tel:".$phone;
    }
    $link .= '">'.$context->telephoneNumber.'</a>';
    echo '<div class="tel">'.$link.'</div>'.PHP_EOL;
}

echo '</div>';
echo '<a href="'.UNL_Peoplefinder::getURL().'?uid='.$context->uid.'" class="cInfo" '.$onclick.'>Contact '.$context->givenName.'</a>';
if ($context->choose_uid) {
    echo '<div class="pfchooser"><a href="#" onclick="return pfCatchUID(\''.$context->uid.'\');">Choose this person</a></div>'.PHP_EOL;
}