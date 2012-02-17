<?php
if ($context->ou == 'org') {
    $class = 'org_Sresult';
    $name = $context->cn;
} else {
    $class = 'ppl_Sresult';
    $name = $context->sn . ',&nbsp;'. $context->givenName;
    if (!empty($context->eduPersonNickname)
        && $context->eduPersonNickname != ' ') {
        $name .= ' ('.$context->eduPersonNickname.')';
    }
}
$class .= ' '.$context->eduPersonPrimaryAffiliation;
echo '<li class="'.$class.'">'.PHP_EOL;
echo '    <div class="overflow">'.PHP_EOL;
$onclick = '';
if (isset($parent->parent->context->options, $parent->parent->context->options['onclick'])) {
    $onclick .= ' onclick="return '.htmlentities($parent->parent->context->options['onclick'], ENT_QUOTES).'(\''.addslashes($context->uid).'\');"';
}
if ($parent->parent->context->options['view'] != 'alphalisting') {
    echo '    <img class="profile_pic small photo planetred_profile" src="http://planetred.unl.edu/pg/icon/unl_'.str_replace("-", "_", $context->uid).'/small"  alt="Photo of '.$context->displayName.'" />'.PHP_EOL;
}
echo '    <div class="recordDetails">'.PHP_EOL;
echo '        <div class="fn"><a href="'.UNL_Peoplefinder::getURL().'?uid='.$context->uid.'" '.$onclick.'>'.$name.'</a></div>'.PHP_EOL;
if (isset($context->eduPersonPrimaryAffiliation)) {
    echo '        <div class="eppa">('.$context->eduPersonPrimaryAffiliation.')</div>'.PHP_EOL;
}
if (isset($context->unlHROrgUnitNumber)) {
    foreach ($context->unlHROrgUnitNumber as $orgUnit) {
        if ($name = UNL_Officefinder_Department::getNameByOrgUnit($orgUnit)) {
            echo '        <div class="organization-unit">'.$name.'</div>'.PHP_EOL;
        }
    }
}

if (isset($context->title)
    && !(
        isset($orgUnit, $parent->parent, $parent->parent->parent, $parent->parent->parent->parent, $parent->parent->context->options, $parent->parent->context->options['view'])
        && $parent->parent->context->options['view'] == 'department'
        && $orgUnit != $parent->parent->parent->parent->context->org_unit
        )
    && false === strpos(strtolower($context->title), 'retiree') // Let's not share retiree or disabled retiree status
    && false === strpos(strtolower($context->title), 'royalty') // Do not show royalty recipients
    ) {
    echo '        <div class="title">'.$context->title.'</div>'.PHP_EOL;
}
if (isset($context->telephoneNumber)) {
    echo '        <div class="tel">'.$savvy->render($context->telephoneNumber, 'Peoplefinder/Record/TelephoneNumber.tpl.php').'</div>'.PHP_EOL;
}

echo '    </div>'.PHP_EOL;
echo '    <a href="'.UNL_Peoplefinder::getURL().'?uid='.$context->uid.'" class="cInfo" '.$onclick.'>Contact '.$context->givenName.'</a><div class="loading"></div>'.PHP_EOL;
if (isset($parent->parent->context->options['chooser'])) {
    echo '    <div class="pfchooser"><a href="#" onclick="return pfCatchUID(\''.$context->uid.'\');">Choose this person</a></div>'.PHP_EOL;
}
echo '</div></li>';