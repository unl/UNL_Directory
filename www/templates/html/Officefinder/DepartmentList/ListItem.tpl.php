<?php
$title = '';
if (!$context->isOfficialDepartment()) {
    if ($parent = $context->getParent()) {
        $title = '<div class="title">('.$parent->name.')</div>';
    }
}
echo '<li>
    <div class="overflow">
    <a class="planetred_profile" href="'.$context->getURL().'">
    <img alt="Generic Icon" src="'.UNL_Peoplefinder::getURL().'images/organization40.png" class="profile_pic small photo">
    </a>
    <div class="recordDetails">
    <div class="fn">
    <a href="'.$context->getURL().'">'.$context->name.'</a>
    </div>
    '.$title;
    if (isset($context->phone)) {
        echo '        <div class="tel">'.$savvy->render($context->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php').'</div>'.PHP_EOL;
    }
    echo '      </div>
    <a class="cInfo"" href="'.$context->getURL().'" onclick="return service_officefinder.of_getUID(\''.$context->id.'\');">More Details</a>
    </div>
</li>';