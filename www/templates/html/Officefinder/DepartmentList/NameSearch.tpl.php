<div class="results departments" >
<h2>Departments</h2>
<?php
if (count($context)) {
    echo '<div class="result_head">'.count($context).' result(s) found</div>';
    echo '<ul class="pfResult departments">';
    foreach ($context as $department) {

        $title = '';
//        if (!$department->isOfficialDepartment()) {
//            $sub_dept   = $department;
//            $department = $sub_dept->getOfficialParent();
//            $title      = '<div class="title">'.$sub_dept->name.'</div>';
//        }
        echo '<li>
                <div class="overflow">
                    <a class="planetred_profile" href="?view=department&amp;id='.$department->id.'">
                        <img alt="Generic Icon" src="images/organization40.png" class="profile_pic small photo">
                    </a>
                    <div class="recordDetails">
                        <div class="fn">
                            <a href="'.UNL_Officefinder::getURL().'?view=department&amp;id='.$department->id.'">'.$department->name.'</a>
                        </div>
                        '.$title.'
                    </div>
                    <a class="cInfo"" href="'.UNL_Officefinder::getURL().'?view=department&amp;id='.$department->id.'" onclick="return service_officefinder.of_getUID(\''.$department->id.'\');">More Details</a>
                </div>
             </li>';
    }
    echo '</ul>';
} else {
    echo "No results could be found";
}
?>
</div>