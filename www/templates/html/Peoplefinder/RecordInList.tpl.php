<?php
if ($context->ou == 'org') {
    $class = 'org_Sresult';
    $name = $context->cn;
} else {
    $class = 'ppl_Sresult';
    $name = $context->sn . ',&nbsp;'. $context->givenName;
    if (isset($context->eduPersonNickname)) {
        $name .= ' "'.$context->eduPersonNickname.'"';
    }
}
$class .= ' '.$context->eduPersonPrimaryAffiliation;
echo '<li class="'.$class.'">'.PHP_EOL;
echo '    <div class="overflow">'.PHP_EOL;
$onclick = '';
if (isset($parent->parent->context->options, $parent->parent->context->options['onclick'])) {
    $onclick .= ' onclick="return '.htmlentities($parent->parent->context->options['onclick'], ENT_QUOTES).'(\''.$context->uid.'\');"';
}
echo '    <a class="planetred_profile" href="http://planetred.unl.edu/pg/profile/unl_'.str_replace("-", "_", $context->uid).'" title="Planet Red Profile for '.$context->cn.'"><img class="profile_pic small photo" src="http://planetred.unl.edu/mod/profile/icondirect.php?username=unl_'.str_replace("-", "_", $context->uid).'&amp;size=small"  alt="Photo of '.$context->displayName.'" /></a>'.PHP_EOL;
echo '    <div class="recordDetails">'.PHP_EOL;
echo '        <div class="fn"><a href="'.UNL_Peoplefinder::getURL().'?uid='.$context->uid.'" '.$onclick.'>'.$name.'</a></div>'.PHP_EOL;
if (isset($context->eduPersonPrimaryAffiliation)) {
    echo '        <div class="eppa">('.$context->eduPersonPrimaryAffiliation.')</div>'.PHP_EOL;
}
if (isset($context->unlHRPrimaryDepartment)) {
    echo '        <div class="organization-unit">'.$context->unlHRPrimaryDepartment.'</div>'.PHP_EOL;
}
if (isset($context->title)) {
    echo '        <div class="title">'.$context->title.'</div>'.PHP_EOL;
}
if (isset($context->telephoneNumber)) {
    echo '        <div class="tel">'.$savvy->render($context->telephoneNumber, 'Peoplefinder/Record/TelephoneNumber.tpl.php').'</div>'.PHP_EOL;
}

echo '    </div>'.PHP_EOL;
echo '    <a href="'.UNL_Peoplefinder::getURL().'?uid='.$context->uid.'" class="cInfo" '.$onclick.'>Contact '.$context->givenName.'</a><div class="loading"></div>'.PHP_EOL;
if (isset($parent->context->options['chooser'])) {
    echo '    <div class="pfchooser"><a href="#" onclick="return pfCatchUID(\''.$context->uid.'\');">Choose this person</a></div>'.PHP_EOL;
}
echo '</div></li>';