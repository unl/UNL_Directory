<div class="results departments" >
<h2>Departments</h2>
<?php
if (count($context)) {
    echo '<div class="result_head">'.count($context).' result(s) found</div>';
    echo '<ul class="pfResult departments">';
    foreach ($context as $department) {

        $title = '';
        if (!$department->isOfficialDepartment()) {
            if ($parent = $department->getParent()) {
                $title = '<div class="title">('.$parent->name.')</div>';
            }
        }
        echo '<li>
                <div class="overflow">
                    <a class="planetred_profile" href="'.$department->getURL().'">
                        <img alt="Generic Icon" src="'.UNL_Peoplefinder::getURL().'images/organization40.png" class="profile_pic small photo">
                    </a>
                    <div class="recordDetails">
                        <div class="fn">
                            <a href="'.$department->getURL().'">'.$department->name.'</a>
                        </div>
                        '.$title;
        if (isset($department->phone)) {
            echo '        <div class="tel">'.$savvy->render($department->phone, 'Peoplefinder/Record/TelephoneNumber.tpl.php').'</div>'.PHP_EOL;
        }
        echo '      </div>
                    <a class="cInfo"" href="'.$department->getURL().'" onclick="return service_officefinder.of_getUID(\''.$department->id.'\');">More Details</a>
                </div>
             </li>';
    }
    echo '</ul>';
} else {
    echo "No results could be found";
}
?>
</div>